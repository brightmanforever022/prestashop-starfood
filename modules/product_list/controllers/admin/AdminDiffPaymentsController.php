<?php

/**
 * Admin tab controller
 */
require_once _PS_MODULE_DIR_.'ba_prestashop_invoice/includes/BaTemplateCategory.php';

class AdminDiffPaymentsController extends ModuleAdminController
{
    var $reminderStates;
    
    public function __construct()
    {
        $this->reminderStates = [OrderInvoice::ReminderNotSent=>$this->l('Not sent'), OrderInvoice::Reminder1Sent=>$this->l('1. Reminder'), 
            OrderInvoice::Reminder2Sent=>$this->l('2. Reminder'), OrderInvoice::Reminder3Sent=>$this->l('3. Reminder'), 
            OrderInvoice::ReminderInkasso=>$this->l('Inkasso')];
        
        $this->module = 'product_list';
        $this->bootstrap = true;
        $this->list_no_link = true;
        $this->explicitSelect = true;
        $this->_defaultOrderBy = 'id_order_invoice';
        $this->_defaultOrderWay = 'desc';
        $this->_where = 'and a.number>0';
        $this->identifier = 'id_order_invoice';
        /*
        $this->_where = 'and a.active=1';
        $this->imageType = 'jpg';
        $this->_defaultOrderBy = 'product_supplier_reference';
        */
        parent::__construct();

        // configure list 
        $this->className = 'OrderInvoice';
        $this->table = 'order_invoice';
        
        $this->_select = 'a.id_order_invoice, \'\' as reminder_files, a.id_order, number, reminder_state, a.paid, sum_to_pay, '
                . 'due_date, payment_date, '.
                'reminder_date, c.firstname, c.lastname, c.company, a.date_add, a.total_paid_tax_incl, o.current_state, osl.name as osname, '
                . 'os.color, o.id_currency, cl.name as countryname,
            it.name AS invoice_tpl_name
        ';
        //, cl.name as countryname
        $this->_join = ' 
            left join '._DB_PREFIX_.'orders o on o.id_order=a.id_order 
                left join '._DB_PREFIX_.'customer c on c.id_customer=o.id_customer 
                LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (o.`current_state` = osl.`id_order_state` '
                . 'AND osl.`id_lang` = '.(int)$this->context->language->id.') 
                LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = o.`current_state`) 
                right join (SELECT * from '._DB_PREFIX_.'address group by id_customer) ad ON c.id_customer=ad.id_customer left join '._DB_PREFIX_.'country_lang cl ON (ad.id_country=cl.id_country AND cl.id_lang='.(int)$this->context->language->id.')
            LEFT JOIN '._DB_PREFIX_.'ba_prestashop_invoice it
                ON it.id = a.template_id
        ';

        $this->fields_list = array();
        
        $this->fields_list['id_order_invoice'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
        );
        $this->fields_list['date_add'] = array(
            'title' => $this->l('Inv.Date'),
            'align' => 'text-right',
            'type' => 'date',
            'filter_key' => 'a!date_add'
        );
        
        $this->fields_list['number'] = array(
            'title' => $this->l('Number'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'callback' => 'showInvoiceNumber',
            'type' => 'int'
        );
        
        $invTplsQuery = '
            select t.id, t.name
            from '._DB_PREFIX_.'ba_prestashop_invoice t
            order by t.name
        ';
        $invTpls = Db::getInstance()->executeS($invTplsQuery);
        $invTplsIdToName = array();
        foreach($invTpls as $invTpl){
            $invTplsIdToName[ $invTpl['id'] ] = $invTpl['name'];
        }
        $this->fields_list['invoice_tpl_name'] = array(
            'title' => $this->l('Invoice type'),
            'type' => 'select',
            //'color' => 'color',
            'list' => $invTplsIdToName,
            'filter_key' => 'it!id',
            'filter_type' => 'int',
            //'order_key' => 'osname'
        );
        $this->fields_list['id_order'] = array(
            'title' => $this->l('Order'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int',
            'callback' => 'showOrderId'
        );
        $this->fields_list['a!printed'] = array(
            'title' => $this->l('Printed'),
            'filter_key' => 'a!printed',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false,
            'active' => 'status',
            //'callback' => 'showPrintedColumn'
        );
        
        $this->fields_list['a!paid'] = array(
            'title' => $this->l('Paid'),
            'filter_key' => 'a!paid',
            'align' => 'text-center',
            'type' => 'bool',
            'class' => 'fixed-width-sm',
            'orderby' => false,
            'callback' => 'showPaidColumn'
        );
        $this->fields_list['payment_date'] = array(
            'title' => $this->l('Payment Date'),
            'align' => 'text-right',
            'type' => 'date',
            'filter_key' => 'payment_date'
        );
        
        $this->fields_list['cl!name'] = array(
            'title' => $this->l('Country'),
            'align' => 'left',
            'filter_key' => 'cl!name',
            'type' => 'text',
        );
        $this->fields_list['company'] = array(
            'title' => $this->l('Company'),
            'align' => 'left',
        );
        $this->fields_list['lastname'] = array(
            'title' => $this->l('Customer'),
            'align' => 'left',
        );
        $this->fields_list['due_date'] = array(
                'title' => $this->l('Due Date'),
                'align' => 'text-right',
                'type' => 'date',
                'filter_key' => 'due_date',
            'class' => 'dueDate'
            );
        $this->fields_list['total_paid_tax_incl'] = array(
            'title' => $this->l('Inv.Total'),
            'align' => 'text-right',
            'type' => 'price',
            'currency' => true,
            'callback' => 'setOrderCurrency',
            'badge_success' => true
        );
        
        // add bulk actions
        $this->bulk_actions = array(
            'setInvoicesPaid' => array('text' => $this->l('Set invoices paid'), 'icon' => 'icon-check'),
            'setInvoicesNotPaid' => array('text' => $this->l('Set invoices not paid'), 'icon' => 'icon-remove'),
        );
        
        $this->addRowAction('edit');
    }

    public function renderForm()
    {
        $id_order_invoice = intval(Tools::getValue('id_order_invoice'));
        $orderInvoice = new OrderInvoice($id_order_invoice);
        
        $invoiceProductsId = explode(',', $orderInvoice->products);
        $invoiceProductsId = array_filter($invoiceProductsId, 'intval');
        $invoiceProductsQuery = '
            SELECT od.`product_id` AS id_product, od.product_name, od.product_quantity, od.product_price, 
                od.product_supplier_reference, od.id_tax_rules_group, od.unit_price_tax_excl, 
                od.unit_price_tax_incl, od.product_attribute_id, od.id_order_detail
            FROM `'. _DB_PREFIX_ .'order_detail` od  
            INNER JOIN `'. _DB_PREFIX_ .'order_invoice` oi 
                ON oi.id_order = od.id_order
            WHERE 
                oi.id_order_invoice = '. intval(Tools::getValue('id_order_invoice')) .'
                '. ( is_array($invoiceProductsId) && count($invoiceProductsId) ? '
                AND od.id_order_detail IN('. implode(',', $invoiceProductsId) .')' : '' ) .'
            
        ';
        $invoiceProducts = Db::getInstance()->executeS($invoiceProductsQuery);
        //var_dump($this->module->getModuleDir());die;
        $rootCat = Category::getRootCategory();
        $tree = new HelperTreeCategories('categories-tree', 'Categories');
        $tree
            ->setAttribute('is_category_filter', true)
            ->setInputName('id-category')
            ->setRootCategory($rootCat->id)
            ->setUseSearch(false)
            ->setNoJS(true)
            //->setFullTree(true)
            ->setTemplateDirectory( $this->module->getModuleDir().'views/templates/admin/diff_payments/helpers/tree' )
        ;
        
        $order = $orderInvoice->getOrder();
        $customer = new Customer( $orderInvoice->getOrder()->id_customer );
        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);
        
        // Retrieve addresses information
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int)$addressInvoice->id_state);
        }
        
        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int)($addressDelivery->id_state));
            }
        }
        
        
        $this->context->smarty->assign(array(
            'invoice_products' => $invoiceProducts,
            'invoice' => $orderInvoice,
            'customer' => $customer,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null
            ),
            
            'gender' => $gender,
            'customerStats' => $customer->getStats(),
            'currency' => new Currency($orderInvoice->getOrder()->id_currency),
            'order_currency_json' => json_encode(new Currency($orderInvoice->getOrder()->id_currency)),
            'order' => $order,
            //'order_products' => $orderInvoice->getOrder()->getProductsDetail(),
            //'order_products_json' => json_encode($orderInvoice->getOrder()->getProductsDetail()),
            'orders_url' => $this->context->link->getAdminLink('AdminOrders'),
            'invoice_templates' => BaTemplateCategory::getTemplatesGroupedByCategory(),
            'form_url' => $this->context->link->getAdminLink('AdminDiffPayments')
                .'&amp;'. http_build_query(array('id_order_invoice'=>$id_order_invoice)),
            'cancel_url' => $this->context->link->getAdminLink('AdminDiffPayments') ,
            'is_category_filter' => true,
            'category_tree' => $tree->render(),
            'change_customer_form_url' => $this->context->link->getAdminLink('AdminDiffPayments')
                .'&'. http_build_query(array('id_order_invoice'=>$id_order_invoice,'action'=>'change_customer')),
            
        ));
        
        //var_dump($orderInvoice->getOrder()->getProductsDetail());
        
        $content = parent::renderForm();
        //$content .= $invoiceProductsQuery;
        return $content . $this->context->smarty->fetch($this->module->getTemplatePath('views/templates/admin/diff_payments/form.tpl'));
    }

    public function processChangeCustomer()
    {
        //var_dump($_POST);
        $customerId = intval( Tools::getValue('customer_id') );
        $orderId = intval( Tools::getValue('order_id') );
        
        $order = new Order($orderId);
        
        $customerNew = new Customer($customerId);
        
        $customerNewAddresses = $customerNew->getAddresses($customerNew->id_lang);
        //var_dump($customerNew,$customerNewAddresses);
        if( !count($customerNewAddresses) ){
            throw new Exception('Customer does not have addresses');
        }
        
        $customerNewAddress = $customerNewAddresses[0];
        $customerAddressObject = new Address($customerNewAddress['id_address']);
        
        // Create new cart
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $customerNew->id;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $customerNewAddress['id_address'];
        $cart->id_address_invoice = $customerNewAddress['id_address'];
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $customerNew->id_lang;
        $cart->secure_key = $customerNew->secure_key;
        
        // Save new cart
        $cart->add();
        
        // Save context (in order to apply cart rule)
        $this->context->cart = $cart;
        $this->context->customer = $customerNew;
        /*
        foreach( OrderDetail::getList($order->id) as $orderDetail ){
            //$order_detail = new OrderDetail($orderDetail['id_order_detail']);
            $this->deleteOrderProductLine($orderDetail['id_order_detail'], $order->id);
            $cart->updateQty($orderDetail['product_quantity'], $orderDetail['product_id'],
                $orderDetail['product_attribute_id'], false, 'up', $customerNewAddress['id_address'], new Shop($cart->id_shop));
        }
        
        $order_detail = new OrderDetail();
        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), 0, true);
        */
        $order->id_customer = $customerNew->id;
        $order->secure_key = $customerNew->secure_key;
        $order->id_cart = $cart->id;
        $order->id_address_delivery = $customerNewAddress['id_address'];
        $order->id_address_invoice = $customerNewAddress['id_address'];
        
        $order->update();
        
        $order->updateOrderDetailTax();
        
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }
        
        $addressStringFormatted = AddressFormat::generateAddress($customerAddressObject);
        $orderInvoices = $order->getInvoicesCollection();
        
        foreach( $orderInvoices as $orderInvoice ){
            Db::getInstance()->update('order_invoice', 
                array(
                    'invoice_address' => pSQL($addressStringFormatted, true), 
                    'delivery_address' => pSQL($addressStringFormatted, true)
                ),
                'id_order_invoice = '. intval($orderInvoice->id)
            );
        }

        Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
    }
    
    function processResetFilters($list_id = null)
    {
        unset($this->context->cookie->plmSizesFilter);
        parent::processResetFilters($list_id);
    }
    
       
    function showInvoiceNumber($field, $row)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminPdf').'&submitAction=generateInvoicePDF&id_order_invoice='.
                $row['id_order_invoice'].'" target="_blank">'.
                sprintf('%1$s%2$06d', Configuration::get('PS_INVOICE_PREFIX', $this->context->language->id, null, $this->context->shop->id), 
                $field).'</a>';
    }
  
    
    function showOrderId($field, $row)
    {
        return '<a href="'.$this->context->link->getAdminLink('AdminOrders').'&id_order='.$field.'&vieworder" target="_blank">'.$field.'</a>';
    }
    
    
    function showReminderState($field, $row)
    {
        return $this->reminderStates[$field];
    }
    
    
    function showComment($field, $row)
    {
        return nl2br($field);
    }
    
    
    function showPaidColumn($field, $row)
    {
        $result = '<a href="#" class="documentPaidChangeLink list-action-enable ';
        if ($field)
        {
            $result .= 'action-enabled"><i class="icon-check"></i>';
        }
        else 
        {
            $result .= 'action-disabled"><i class="icon-remove"></i>';
        }
        $result .= '</a>';
        return $result;
    }
    
    function showPrintedColumn($field, $row)
    {
        $result = '<a href="#" class=" ';
        if ($field)
        {
            $result .= 'action-enabled"><i class="icon-check"></i>';
        }
        else
        {
            $result .= 'action-disabled"><i class="icon-remove"></i>';
        }
        $result .= '</a>';
        return $result;
    }
    
    function showReminderFiles($field, $row)
    {
        $result = '';
        $languages = [Language::getIdByIso('de'), Language::getIdByIso('en')];
        for($i=1; $i<4; $i++)
        {
            foreach ($languages as $lang)
            {
                if ($url = OrderInvoice::getReminderFileUrl($row['id_order_invoice'], $i, $lang, true))
                {
                    if ($result)
                    {
                        $result .= ' | ';
                    }
                    $result .= '<a href="' . $this->context->link->getAdminLink('AdminDiffPayments') . '&getPdfReminder&invoice_id=' .
                            $row['id_order_invoice'] . '&num=' . $i . '&id_lang='.$lang.'">' . basename($url) . '</a>';
                }
            }
        }
        
        return $result;
    }
    
    
    public static function setOrderCurrency($echo, $tr)
    {
        return Tools::displayPrice($echo, (int)$tr['id_currency']);
    }
    
     
    function postProcess()
    {
        if (Tools::isSubmit('submitBulksetInvoicesPaidorder_invoice') || Tools::isSubmit('submitBulksetInvoicesNotPaidorder_invoice'))
        {
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                OrderInvoice::setPaidState($invoiceIds, Tools::isSubmit('submitBulksetInvoicesPaidorder_invoice')?1:0);
            }
        }
        elseif(Tools::isSubmit('genReminder1de'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(1, $invoiceIds, Language::getIdByIso('de'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('genReminder1en'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(1, $invoiceIds, Language::getIdByIso('en'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('submitBulksendReminder1deorder_invoice') || Tools::isSubmit('submitBulksendReminder1enorder_invoice'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                                  
                // set new status
                OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder1Sent);
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
        }
        elseif(Tools::isSubmit('genReminder2de'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(2, $invoiceIds, Language::getIdByIso('de'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('genReminder2en'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(2, $invoiceIds, Language::getIdByIso('en'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('submitBulksendReminder2deorder_invoice') || Tools::isSubmit('submitBulksendReminder2enorder_invoice'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                                  
                // set new status
                OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder2Sent);
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
        }
        elseif(Tools::isSubmit('genReminder3de'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(3, $invoiceIds, Language::getIdByIso('de'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('genReminder3en'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                
                $this->generatePdfReminder(3, $invoiceIds, Language::getIdByIso('en'));
            }
            else
            {
                echo $this->l('No invoices selected');
                exit;
            }
        }
        elseif(Tools::isSubmit('submitBulksendReminder3deorder_invoice') || Tools::isSubmit('submitBulksendReminder3enorder_invoice'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                                  
                // set new status
                OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder3Sent);
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
        }
        elseif(Tools::isSubmit('submitBulksetInkassoorder_invoice'))
        {
            // prepare ids
            $invoiceIds = Tools::getValue('order_invoiceBox', []);
            if (count($invoiceIds))
            {
                array_walk($invoiceIds, function(&$item, $key){ $item = intval($item); });
                                  
                // set new status
                OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::ReminderInkasso);
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
        }
        elseif(Tools::isSubmit('getPdfReminder'))
        {
            $filePath = OrderInvoice::getReminderFilePath(intval(Tools::getValue('invoice_id')), intval(Tools::getValue('num')), 
                    intval(Tools::getValue('id_lang')));
            
            if (!empty($filePath))
            {
                header("Content-type: application/pdf");
                header("Content-Disposition: attachment; filename=".basename($filePath));

                readfile($filePath);
            }
            else
            {
                echo $this->l('Error: file "'.$filePath.'" not found');
            }
            exit;
        }
        elseif( Tools::isSubmit('submit_new_invoice') ){
            //var_dump($_POST);
            $invoiceId = intval( Tools::getValue('id_order_invoice') );
            $templateId = intval( Tools::getValue('invoice_template_id') );
            $paid = intval(Tools::getValue('paid', 0));
            $printed = intval(Tools::getValue('printed', 0));
            $comment = Tools::getValue('comment');
            $comment = trim(strip_tags($comment));
            
            $products = Tools::getValue('products');
            $productQuantities = Tools::getValue('product_quantity');
            $productPrices = Tools::getValue('product_price_te');
            //var_dump($products, $productQuantities, $productPrices);die;

            $order_invoice = new OrderInvoice($invoiceId);
            $order = $order_invoice->getOrder();
            //$order_invoice->id_order = $this->id;
            $order_invoice->template_id = $templateId;
            
            //$order_invoice->number = 0;
            $order_invoice->id_employee = Context::getContext()->employee->id;
            
            $invoiceTemplate = Db::getInstance()->getRow('select sum_to_pay_percent, no_tax, auto_set_paid, due_date_plus from '._DB_PREFIX_.
                'ba_prestashop_invoice where id='.$templateId);
            
            // Total method
            $total_method = Cart::BOTH_WITHOUT_SHIPPING;
            
            // Create new cart
            $cart = new Cart();
            $cart->id_shop_group = $order->id_shop_group;
            $cart->id_shop = $order->id_shop;
            $cart->id_customer = $order->id_customer;
            $cart->id_carrier = $order->id_carrier;
            $cart->id_address_delivery = $order->id_address_delivery;
            $cart->id_address_invoice = $order->id_address_invoice;
            $cart->id_currency = $order->id_currency;
            $cart->id_lang = $order->id_lang;
            $cart->secure_key = $order->secure_key;
            
            // Save new cart
            $cart->add();
            
            // Save context (in order to apply cart rule)
            $this->context->cart = $cart;
            $this->context->customer = new Customer($order->id_customer);
            
            // always add taxes even if there are not displayed to the customer
            $use_taxes = true;
            
            $deleteOrderDetailIds = array();
            $previousOrderDetails = OrderDetail::getList($order->id);
            foreach($previousOrderDetails as $previousOrderDetail){
                if( intval($previousOrderDetail['id_order_invoice']) == $invoiceId ){
                    $deleteOrderDetailIds[] = intval($previousOrderDetail['id_order_detail']);
                }
            }
            //var_dump($deleteOrderDetailIds, $products);die;
            $specificPrices = array();
            
            $orderProductsTotalTaxExcl = 0;
            $orderProductsTotalTaxIncl = 0;
            
            foreach($products as $prodAttrIds){
                
                $prodIds = explode('_', $prodAttrIds);
                $orderDetailId = intval($prodIds[0]);
                $productId = intval($prodIds[1]);
                $attributeId = intval($prodIds[2]);
                
                
                $productPrice = floatval($productPrices[$prodAttrIds]);
                $productQuantity = intval($productQuantities[$prodAttrIds]);
                
                if($orderDetailId){
                    //echo 'PR '.$productPrice;
                    
                    if( ($ordDetIdKey = array_search($orderDetailId, $deleteOrderDetailIds)) !== false ){
                        unset($deleteOrderDetailIds[$ordDetIdKey]);
                    }
                    
                    $taxRate = Tax::getProductTaxRate($productId);
                    $order_detail = new OrderDetail($orderDetailId);
                    $old_quantity = $order_detail->product_quantity;
                    $productPriceTI = $productPrice + ($productPrice / 100 * $taxRate);
                    
                    $totalProductsTEPrev = $order_detail->total_price_tax_excl;
                    $totalProductsTIPrev = $order_detail->total_price_tax_incl;
                    
                    $totalProductsTENew = $productPrice * $productQuantity;
                    $totalProductsTINew = $productPriceTI * $productQuantity;
                    
                    $totalProductsDiffTE = $totalProductsTENew - $totalProductsTEPrev;
                    $totalProductsDiffTI = $totalProductsTINew - $totalProductsTIPrev;
                    
                    $order_detail->unit_price_tax_excl = $productPrice;
                    $order_detail->unit_price_tax_incl = $productPriceTI;
                    
                    $order_detail->product_quantity = $productQuantity;
                    //var_dump($totalProductsDiffTE);
                    $order_detail->total_price_tax_excl += $totalProductsDiffTE;
                    $order_detail->total_price_tax_incl += $totalProductsDiffTI;

                    $order_detail->update();
                    
                    StockAvailable::updateQuantity(
                        $order_detail->product_id, $order_detail->product_attribute_id,
                        ($old_quantity - $order_detail->product_quantity), $order->id_shop
                    );
                    
                }
                else{
                    $product = new Product($productId, false, $order->id_lang);
                    if (!Validate::isLoadedObject($product)){
                        continue;
                    }
                    
                    if( $attributeId ){
                        $combination = new Combination($attributeId);
                        if (!Validate::isLoadedObject($combination)){
                            continue;
                        }
                    }
                    $initial_product_price_tax_excl = Product::getPriceStatic($product->id, false, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
                        false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
                    
                    //$productPrices[$prodAttrIds] = floatval($productPrices[$prodAttrIds]);
                    // Creating specific price if needed
                    if ($productPrice != $initial_product_price_tax_excl) {
                        $specific_price = new SpecificPrice();
                        $specific_price->id_shop = 0;
                        $specific_price->id_shop_group = 0;
                        $specific_price->id_currency = 0;
                        $specific_price->id_country = 0;
                        $specific_price->id_group = 0;
                        $specific_price->id_customer = $order->id_customer;
                        $specific_price->id_product = $product->id;
                        if (isset($combination)) {
                            $specific_price->id_product_attribute = $combination->id;
                        } else {
                            $specific_price->id_product_attribute = 0;
                        }
                        $specific_price->price = $productPrice;
                        $specific_price->from_quantity = 1;
                        $specific_price->reduction = 0;
                        $specific_price->reduction_type = 'amount';
                        $specific_price->reduction_tax = 0;
                        $specific_price->from = '0000-00-00 00:00:00';
                        $specific_price->to = '0000-00-00 00:00:00';
                        $specific_price->add();
                    
                        $specificPrices[] = $specific_price->id;
                    }
                    
                    $update_quantity = $cart->updateQty($productQuantity, $product->id,
                        isset($combination) ? $combination->id : null, false, 'up', 0, new Shop($cart->id_shop));
                    
                    $initial_product_price_tax_incl = Product::getPriceStatic(
                        $product->id, !$invoiceTemplate['no_tax'], 
                        isset($combination) ? $combination->id : null, 2, null, false, true, 1,
                        false, $order->id_customer, $cart->id, 
                        $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)}
                    );
                }
            }
            
            foreach($deleteOrderDetailIds as $deleteOrderDetailId){
                $this->deleteOrderProductLine($deleteOrderDetailId, $order->id);
            }
            
            //echo 'total';
            //var_dump($orderProductsTotalTaxExcl, $orderProductsTotalTaxIncl);
            
            // Add new products to order
            $order_detail = new OrderDetail();
            $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes);
            
            $orderProductsTotalTaxExcl = 0;
            $orderProductsTotalTaxIncl = 0;
            
            // recalculated invoice tottals            
            $order_invoice->total_products = 0;
            $order_invoice->total_products_wt = 0;
            $orderDetails = $order->getOrderDetailList();
            foreach($orderDetails as $orderDetail){
                
                $orderProductsTotalTaxExcl += $orderDetail['total_price_tax_excl'];
                $orderProductsTotalTaxIncl += $orderDetail['total_price_tax_incl'];
                
                if (intval($orderDetail['id_order_invoice']) == $order_invoice->id){
                    $order_invoice->total_products += $orderDetail['total_price_tax_excl'];
                    $order_invoice->total_products_wt += $invoiceTemplate['no_tax'] ? 
                        $orderDetail['total_price_tax_excl'] : $orderDetail['total_price_tax_incl'];
                }
            }
            $order_invoice->total_paid_tax_excl = $order->total_paid_tax_excl-($order->total_products-$order_invoice->total_products);
            if ($invoiceTemplate['no_tax'])
            {
                $order_invoice->total_paid_tax_incl = $order_invoice->total_paid_tax_excl;
            }
            else
            {
                $order_invoice->total_paid_tax_incl = $order->total_paid_tax_incl-($order->total_products_wt-$order_invoice->total_products_wt);
            }
            
            $order_invoice->update();
            //var_dump($orderProductsTotalTaxExcl, $orderProductsTotalTaxIncl);die;
            // update totals amount of order
            $order->total_products = $orderProductsTotalTaxExcl;
            $order->total_products_wt = $orderProductsTotalTaxIncl;

            $order->total_paid = $orderProductsTotalTaxIncl;
            $order->total_paid_tax_excl = $orderProductsTotalTaxExcl;
            $order->total_paid_tax_incl = $orderProductsTotalTaxIncl;
            
            //$order->total_paid += Tools::ps_round((float)($cart->getOrderTotal(true, $total_method)), 2);
            //$order->total_paid_tax_excl += Tools::ps_round((float)($cart->getOrderTotal(false, $total_method)), 2);
            //$order->total_paid_tax_incl += Tools::ps_round((float)($cart->getOrderTotal($use_taxes, $total_method)), 2);
            
            /*
            $order->total_shipping = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $order_invoice->total_shipping_tax_excl;
            */
            /*/ discount
            $order->total_discounts += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            $order->total_discounts_tax_excl += (float)abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
            $order->total_discounts_tax_incl += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
            */
            
            
            // Save changes of order
            $order->update();
            
            $order->updateOrderDetailTax();
            
            $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
            if (Validate::isLoadedObject($order_carrier)) {
                $order_carrier->weight = (float)$order->getTotalWeight();
                if ($order_carrier->update()) {
                    $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                }
            }
            
            
            // Delete specific price if exists
            if (count($specificPrices)) {
                foreach($specificPrices as $specPriceId){
                    $specific_price = new SpecificPrice($specPriceId);
                    $specific_price->delete();
                }
            }
            
            $orderDetailIds = array();
            $currentOrderDetails = OrderDetail::getList($order->id);
            foreach( $currentOrderDetails as $currentOrderDetail ){
                foreach( $previousOrderDetails as $previousOrderDetail ){
                    if( ($currentOrderDetail['id_order_invoice'] == $order_invoice->id)
                        /*&& ($currentOrderDetail['id_order_detail'] != $previousOrderDetail['id_order_detail'])*/
                    ){
                        $orderDetailIds[] = $currentOrderDetail['id_order_detail'];
                    }
                }
            }
            
            $order_invoice->paid = $paid;
            $order_invoice->printed = $printed;
            $order_invoice->comment = $comment;
            $order_invoice->setProductIds($orderDetailIds);
            //$order_invoice->percent_to_pay = $percentToPay;// Tools::ps_round($order_invoice->total_paid_tax_incl*$invoiceTemplate['sum_to_pay_percent']/100, 2);
            //$order_invoice->sum_to_pay = Tools::ps_round($order_invoice->total_paid_tax_incl / 100 * $percentToPay, 2);
            $order_invoice->save();
            
            $template = PDF::TEMPLATE_INVOICE;
            
            $pdf = new PDF(new OrderInvoice($order_invoice->id), $template, Context::getContext()->smarty);
            $pdfFileContent = $pdf->render('S');
            // save file
            file_put_contents($order_invoice->getInvoiceFilePath(), $pdfFileContent);
            
            Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
        }
        
        parent::postProcess();
        
        
    }
    
    public function deleteOrderProductLine($order_detail_id, $order_id)
    {
        $resultData = array(
            'success' => false,
            'messages' => array()
        );
    
        $order_detail = new OrderDetail(intval($order_detail_id));
        if (!Validate::isLoadedObject($order_detail)){
            $resultData['messages'][] = Tools::displayError('Order detail ID invalid:'. intval($order_detail_id));
            return $resultData;
        }
        $order = new Order(intval($order_id));
        if (!Validate::isLoadedObject($order)){
            $resultData['messages'][] = Tools::displayError('Order ID invalid:'. intval($order_id));
            return $resultData;
        }
        if ($order_detail->id_order != $order->id){
            $resultData['messages'][] = Tools::displayError('Order detail ID does not match Order ID');
            return $resultData;
        }
    
        // Update OrderInvoice of this OrderDetail
        if ($order_detail->id_order_invoice != 0) {
            $order_invoice = new OrderInvoice($order_detail->id_order_invoice);
            $order_invoice->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
            $order_invoice->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
            $order_invoice->total_products -= $order_detail->total_price_tax_excl;
            $order_invoice->total_products_wt -= $order_detail->total_price_tax_incl;
            try{
                $order_invoice->update();
            }
            catch(Exception $e){
                $resultData['messages'][] = $e->getMessage();
                return $resultData;
            }
        }
    
        // Update Order
        $order->total_paid -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
        $order->total_products -= $order_detail->total_price_tax_excl;
        $order->total_products_wt -= $order_detail->total_price_tax_incl;
    
        try{
            $order->update();
        }
        catch(Exception $e){
            $resultData['messages'][] = $e->getMessage();
            return $resultData;
            
        }
    
        // Reinject quantity in stock
        $qntReinjRes = $this->reinjectQuantity($order_detail, $order_detail->product_quantity, true);
        if( is_bool($qntReinjRes) && !$qntReinjRes ){
            $resultData['messages'][] = Tools::displayError('Error reinjecting quantity');
            //return $resultData;
        }
        elseif( is_string($qntReinjRes) ){
            $resultData['messages'][] = $qntReinjRes;
        }
    
        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }
    
        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();
    
        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        $resultData['success'] = true;
        
        return $resultData;
    }
    
    
    /**
     * @param OrderDetail $order_detail
     * @param int $qty_cancel_product
     * @param bool $delete
     */
    protected function reinjectQuantity($order_detail, $qty_cancel_product, $delete = false)
    {
        // Reinject product
        $reinjectable_quantity = (int)$order_detail->product_quantity - (int)$order_detail->product_quantity_reinjected;
        $quantity_to_reinject = $qty_cancel_product > $reinjectable_quantity ? $reinjectable_quantity : $qty_cancel_product;
        // @since 1.5.0 : Advanced Stock Management
        $product_to_inject = new Product($order_detail->product_id, false, (int)$this->context->language->id, (int)$order_detail->id_shop);
    
        $product = new Product($order_detail->product_id, false, (int)$this->context->language->id, (int)$order_detail->id_shop);
    
        if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management && $order_detail->id_warehouse != 0) {
            $manager = StockManagerFactory::getManager();
            $movements = StockMvt::getNegativeStockMvts(
                $order_detail->id_order,
                $order_detail->product_id,
                $order_detail->product_attribute_id,
                $quantity_to_reinject
            );
            $left_to_reinject = $quantity_to_reinject;
            foreach ($movements as $movement) {
                if ($left_to_reinject > $movement['physical_quantity']) {
                    $quantity_to_reinject = $movement['physical_quantity'];
                }
    
                $left_to_reinject -= $quantity_to_reinject;
                if (Pack::isPack((int)$product->id)) {
                    // Gets items
                    if ($product->pack_stock_type == 1 || $product->pack_stock_type == 2 || ($product->pack_stock_type == 3 && Configuration::get('PS_PACK_STOCK_TYPE') > 0)) {
                        $products_pack = Pack::getItems((int)$product->id, (int)Configuration::get('PS_LANG_DEFAULT'));
                        // Foreach item
                        foreach ($products_pack as $product_pack) {
                            if ($product_pack->advanced_stock_management == 1) {
                                $manager->addProduct(
                                    $product_pack->id,
                                    $product_pack->id_pack_product_attribute,
                                    new Warehouse($movement['id_warehouse']),
                                    $product_pack->pack_quantity * $quantity_to_reinject,
                                    null,
                                    $movement['price_te'],
                                    true
                                    );
                            }
                        }
                    }
                    if ($product->pack_stock_type == 0 || $product->pack_stock_type == 2 ||
                        ($product->pack_stock_type == 3 && (Configuration::get('PS_PACK_STOCK_TYPE') == 0 || Configuration::get('PS_PACK_STOCK_TYPE') == 2))) {
                            $manager->addProduct(
                                $order_detail->product_id,
                                $order_detail->product_attribute_id,
                                new Warehouse($movement['id_warehouse']),
                                $quantity_to_reinject,
                                null,
                                $movement['price_te'],
                                true
                                );
                        }
                } else {
                    $manager->addProduct(
                        $order_detail->product_id,
                        $order_detail->product_attribute_id,
                        new Warehouse($movement['id_warehouse']),
                        $quantity_to_reinject,
                        null,
                        $movement['price_te'],
                        true
                        );
                }
            }
    
            $id_product = $order_detail->product_id;
            if ($delete) {
                $order_detail->delete();
            }
            StockAvailable::synchronize($id_product);
        } elseif ($order_detail->id_warehouse == 0) {
            StockAvailable::updateQuantity(
                $order_detail->product_id,
                $order_detail->product_attribute_id,
                $quantity_to_reinject,
                $order_detail->id_shop
            );
    
            if ($delete) {
                $order_detail->delete();
            }
        } else {
            return Tools::displayError('This product cannot be re-stocked.');
        }
    }
    
    
    function ajaxProcessSaveInvoiceComment()
    {
        if (!OrderInvoice::saveComment(intval(Tools::getValue('id')), Tools::getValue('comment')))
        {
                die(json_encode(['error'=>$this->l('Error occured, comment not saved')]));
        }
        die(Tools::jsonEncode(array(
                'error' => 0,
            )));
    }
    
    
    function generatePdfReminder($reminderNum, $invoiceIds, $langId)
    {
        // reading data
        $invoices = Db::getInstance()->executeS('select id_order_invoice, sum_to_pay, number, c.firstname, c.lastname, c.address1, c.company, ' .
                'c.address2, c.city, c.postcode, o.id_lang, oi.date_add as invoice_date, gl.name as salutation, cl.name as countryName, ' .
                'o.id_currency from ' . _DB_PREFIX_ .
                'order_invoice oi left join ' . _DB_PREFIX_ . 'orders o on o.id_order=oi.id_order left join ' . _DB_PREFIX_ . 'customer c ' .
                'on c.id_customer=o.id_customer left join ' . _DB_PREFIX_ . 'gender_lang gl on c.id_gender=gl.id_gender and gl.id_lang=' .
                'o.id_lang left join ' . _DB_PREFIX_ . 'address a on o.id_address_invoice=a.id_address left join ' .
                _DB_PREFIX_ . 'country_lang cl on cl.id_country=a.id_country and cl.id_lang=' .
                $langId . ' where id_order_invoice in (' . implode(',', $invoiceIds) . ')');

        $existingFilePaths = [];
        if (count($invoices))
        {
            // generate pdfs
            foreach ($invoices as $invoice)
            {
                $existingFilePaths [] = OrderInvoice::generateReminder($invoice, $reminderNum, $langId);
            }
        }

        // generate zip with pdf files
        $saveFileNameZip = tempnam(OrderInvoice::InvoiceReminderFolderPath, 'bulk_');
        if (!$saveFileNameZip)
        {
            exit('Can\'t create zip file');
        }
        $zip = new ZipArchive();
        if ($zip->open($saveFileNameZip, ZIPARCHIVE::CREATE)!==TRUE) {
            exit('Can\'t open zip file');
        }
        foreach ($existingFilePaths as $file)
        {
            $zip->addFile($file, basename($file));
        }
        
        $zip->close();
                
        // send to user
        header("Content-type: application/zip");
        header("Content-Disposition: attachment; filename=reminders.zip");

        header('Pragma: no-cache',true);
        header('Expires: 0',true);
        readfile($saveFileNameZip);
        
        unlink($saveFileNameZip);
        exit;
    }
    
    
    /*
    * module: ba_prestashop_invoice
    * date: 2016-12-15 08:45:59
    * version: 1.1.16
    */
    public function displayDate($date, $id_lang = null)
    {
        if (!$date || !($time = strtotime($date))) {
            return $date;
        }
        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }
        if (!Validate::isDate($date)) {
            return $date;
        }
        if ($id_lang == null) {
            $id_lang = $this->order->id_lang;
        }
        $context = Context::getContext();
        $lang = empty($id_lang) ? $context->language : new Language($id_lang);
        $date_format = $lang->date_format_lite;
        return date($date_format, $time);
    }
    
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false);
    
        // calculate total
        $sqlTail = stristr(stristr(stristr($this->_listsql, 'from'), 'limit', true), 'order by', true);
        $sumToPayTotal = Db::getInstance()->getValue('select sum(sum_to_pay) '.$sqlTail.' and a.paid=0');
        $this->context->smarty->assign('sumToPayTotal', $sumToPayTotal?$sumToPayTotal:0);
    }
    
    
    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'admin/order_invoices.js');
        $this->addJqueryPlugin('typewatch');
    }
    
    
    public function renderList()
    {
        $this->tpl_list_vars['reminder1Days'] = Configuration::get('dpReminder1Days');
        $this->tpl_list_vars['reminder2Days'] = Configuration::get('dpReminder2Days');
        $this->tpl_list_vars['reminder3Days'] = Configuration::get('dpReminder3Days');
        return '<script type="text/javascript">
        //<![CDATA[
        var admin_order_tab_link = '.json_encode($this->context->link->getAdminLink('AdminOrders')).';
        var admin_diff_payments_link = '.json_encode($this->context->link->getAdminLink('AdminDiffPayments')).';
        var id_lang = '.$this->context->language->id.';
        var id_currency = "";
        var id_address = "";
        var id_customer = "";
        var currency_format = '.$this->context->currency->format.';
        var currency_sign = \''.$this->context->currency->sign.'\';
        var currency_blank = '.json_encode($this->context->currency->blank).';
        var priceDisplayPrecision = '._PS_PRICE_DISPLAY_PRECISION_.';
        var textSave = '.json_encode('Save').';
        var textCancel = '.json_encode('Cancel').';
        //]]>
      </script>'.parent::renderList();
    }
    
    
    function displayErrors()
    {
        
    }
    
    
    function ajaxProcessSaveRemindersCfg()
    {
        $error = '';
        if (!Validate::isInt($_POST['reminder1Days']))
        {
            $error .= $this->l('Days till 1st reminder must be integer');
        }
        
        if (!Validate::isInt($_POST['reminder2Days']))
        {
            $error .= '<br/>'.$this->l('Days till 2nd reminder must be integer');
        }
        
        if (!Validate::isInt($_POST['reminder3Days']))
        {
            $error .= '<br/>'.$this->l('Days till 3rd reminder must be integer');
        }
        
        if ($error)
        {
            die(json_encode(['error'=>$error]));
        }
        
        Configuration::updateValue('dpReminder1Days', intval($_POST['reminder1Days']));
        Configuration::updateValue('dpReminder2Days', intval($_POST['reminder2Days']));
        Configuration::updateValue('dpReminder3Days', intval($_POST['reminder3Days']));
        
        die(json_encode(['error'=>'']));
    }
}
