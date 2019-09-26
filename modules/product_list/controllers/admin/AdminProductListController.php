<?php

/**
 * Admin tab controller
 */
class AdminProductListController extends ModuleAdminController
{

    public function __construct()
    {
        $this->module = 'product_list';
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->explicitSelect = true;
        $this->_where = 'and a.active=1';
        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'product_supplier_reference';
        
        parent::__construct();

        // configure list 
        $this->className = 'Product';
        $this->table = 'product';
        
        $this->_select = 'sa.quantity as total_qty, group_concat(concat_ws(\'=\', al.name, sa1.quantity, ps2.product_supplier_reference) order by al.name separator \',\')'
                . ' as stock, id_image, s.name as supplier, ps.product_supplier_reference';
        $this->_join = ' left join ' . _DB_PREFIX_ . 'product_attribute pa on pa.id_product=a.id_product left join ' . _DB_PREFIX_ .
                'stock_available sa on sa.id_product=a.id_product and sa.id_product_attribute=0 and sa.id_shop=' . $this->context->shop->id .
                ' left join ' . _DB_PREFIX_ . 'stock_available sa1 on sa1.id_product=a.id_product and sa1.id_product_attribute=' .
                'pa.id_product_attribute and sa1.id_shop=' . $this->context->shop->id.' left join ' . _DB_PREFIX_ .
                'product_attribute_combination pac on pa.id_product_attribute=pac.id_product_attribute left join ' . _DB_PREFIX_ .
                'attribute_lang al on al.id_attribute=pac.id_attribute and al.id_lang=' . $this->context->language->id.
                ' left join ' . _DB_PREFIX_ . 'image i on i.id_product=a.id_product and cover=1 left join ' . _DB_PREFIX_ . 
                'product_supplier ps on a.id_product=ps.id_product and ps.id_product_attribute=0 left join ' . _DB_PREFIX_ . 
                'product_supplier ps2 on a.id_product=ps2.id_product and ps2.id_product_attribute=pa.id_product_attribute left join ' . _DB_PREFIX_ .
                'supplier s on s.id_supplier=ps.id_supplier';
        $this->_group = 'group by a.id_product';

        $this->fields_list = array();
        $this->fields_list['id_product'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        $this->fields_list['id_image'] = array(
            'title' => $this->l('Image'),
            'align' => 'center',
            'callback' => 'showProductImage',
            //'image' => 'p',
            'orderby' => false,
            'filter' => false,
            'search' => false
        );
        $this->fields_list['product_supplier_reference'] = array(
            'title' => $this->l('Sku'),
            'align' => 'left',
            'filter_key' => 'ps!product_supplier_reference',
        );
        // reading suppliers
        $suppliers = Supplier::getSuppliers(false, 0 , false);
        $supplierList = array();
        foreach ($suppliers as $supplier)
        {
            $supplierList[$supplier['id_supplier']] = $supplier['name'];
        }
        $this->fields_list['supplier'] = array(
            'title' => $this->l('Supplier'),
            'type' => 'select',
            'list' => $supplierList,
            'filter_key' => 'ps!id_supplier',
            'filter_type' => 'int',
            'order_key' => 'supplier',
            'align' => 'left',
        );
        
        $this->fields_list['stock'] = array(
            'title' => $this->l('Stock'),
            'align' => 'left',
            'filter_key' => 'sa!quantity',
            'filter_type' => 'int',
            'callback' => 'showStock',
        );
        $this->fields_list['price'] = array(
            'title' => $this->l('Price tax excl.'),
            'type' => 'price',
            'align' => 'text-right',
            'filter_key' => 'a!price'
        );
        
        // reading all sizes used in products
        $sizes = Db::getInstance()->s('select distinct al.id_attribute, al.name from '._DB_PREFIX_.'product_attribute_combination pac '
                . 'inner join '._DB_PREFIX_ .'attribute_lang al on al.id_attribute=pac.id_attribute and al.id_lang=' . 
                $this->context->language->id.' order by name');
        
        $sizesList = [];
        foreach($sizes as $size)
        {
            $sizesList [$size['id_attribute']]= $size['name'];
        }
        
        $this->context->smarty->assign('plmSizesList', $sizesList);
    }

    
    public function processFilter()
    {
        parent::processFilter();
        
        // add sizes list filter
        $sizesFilter = [];
        if (isset($_REQUEST['plmSizesFilter']) && is_array($_REQUEST['plmSizesFilter']))
        {
            $this->context->cookie->plmSizesFilter = serialize($sizesFilter = $_REQUEST['plmSizesFilter']);
        }
        elseif(isset($this->context->cookie->plmSizesFilter))
        {
            $sizesFilter = unserialize($this->context->cookie->plmSizesFilter);
        }
        
        // assign filter in view
        $this->context->smarty->assign('plmSelectedSizes', $sizesFilter);
        
        if (count($sizesFilter))
        {
            $size_list = implode(',', $sizesFilter);
            $sizes_products = Db::getInstance()->s('SELECT ps.`id_product` 
								FROM `'._DB_PREFIX_.'product_attribute` ps
								LEFT JOIN `'._DB_PREFIX_.'product_attribute_combination` pac1
								ON (ps.`id_product_attribute` = pac1.`id_product_attribute`)
								LEFT JOIN `'._DB_PREFIX_.'attribute_lang` pas1
								ON (pas1.`id_attribute` = pac1.`id_attribute`)
								LEFT JOIN `'._DB_PREFIX_.'stock_available` past
								ON (ps.`id_product_attribute` = past.`id_product_attribute`)
								WHERE pas1.id_lang='. $this->context->language->id .' AND pas1.id_attribute IN('.$size_list.') AND past.quantity > 0 GROUP BY ps.id_product ');

            $pr_sizes_active = array();
            foreach($sizes_products as $k=>$prod) {
                $pr_sizes_active[] = $prod['id_product'];
            }

            $pr_sizes_active = implode(',', $pr_sizes_active);

            $this->_filter .= ' and ( 0 ';
           // $this->_filter .= ' or ps.`id_product IN(exs.lst) and sa1.quantity>0';

            $this->_filter .= ' or  sa1.quantity>0 and ps.id_product IN('.$pr_sizes_active.')';
           /* foreach($sizesFilter as $sizeId)
            {
                $this->_filter .= ' or pac.id_attribute='.$sizeId.' and sa1.quantity>0';
            }*/
            $this->_filter .= ')';
        }
    }
    
    
    function processResetFilters($list_id = null)
    {
        unset($this->context->cookie->plmSizesFilter);
        parent::processResetFilters($list_id);
    }
    
    function showStock($field, $row)
    {
        $return = '<table class="table"><tr>';
        $stocks = [];
        if (!empty($field))
        {
            // parse field
            $sizes = explode(',', $field);
            foreach ($sizes as $size)
            {
                $stock = explode('=', $size);
                $return .= '<th>' . $stock[0] . '</th>';
                $stocks [] = ['qty'=>$stock[1], 'sku'=>$stock[2]];
            }
        }
        $return .= '<th>'.$this->l('total').'</th>';
        $return .= '</tr><tr>';
        foreach($stocks as $stock)
        {
            // https://www.vipdress.de/admin123
            // https://dmitri.wheel/vipdress.de1/admin123
            $return .= '<td><a href="#" class="supplierOrdersLink" rel="https://www.vipdress.de/admin123/index_service.php/supplier_orders/show_supplier_orders_by_sku/'.
                    rawurlencode($stock['sku']).'">'.$stock['qty'].'</a></td>';
        }
        $return .= '<td>'.$row['total_qty'].'</td></tr></table>';
        return $return;
    }
    
    
    function showProductImage($field, $row)
    {
        //class="plmProductImage"
        return '<a class="plmProductImg" href="#" rel="#img-'.$field.'"><img src='.
                $this->context->link->getImageLink('aaa', $field, 'medium_default').'  rel="'.
                $this->context->link->getImageLink('aaa', $field, 'large_default').'" /></a>'.
                '<div id="img-'.$field.'" class="hidden"><img src="'.$this->context->link->getImageLink('aaa', $field, 'large_default').'" /></div>';
    }
    
    
    public function setMedia()
    {
        parent::setMedia();
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/cluetip/jquery.cluetip.js');
        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/plugins/cluetip/jquery.cluetip.css');
    }
    
    
    public function renderList()
    {
        //, waitImage: \'../img/loader.gif\'
        return '<script type="text/javascript">
        //<![CDATA[
        $(function(){
            $(\'a.plmProductImg\').cluetip({local:true, cursor: \'pointer\', showTitle: false, width: \'591px\'});
            $(\'a.supplierOrdersLink\').cluetip({cluetipClass: \'tbltip\', dropShadow: true, width: \'200px\', showTitle: false});
        });
        //]]>
      </script>'.parent::renderList();
    }
    
    function displayErrors()
    {
        
    }
}
