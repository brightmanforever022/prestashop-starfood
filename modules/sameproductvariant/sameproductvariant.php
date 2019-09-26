<?php
/*
* 2007-2011 PrestaShop 
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 8783 $
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_'))
	exit;

class SameProductVariant extends Module
{
	function __construct()
	{
		$this->name = 'sameproductvariant';
		$this->version = 1.0;
		$this->author = 'Wheelronix';
        $this->tab = 'front_office_features';
		$this->need_instance = 0;

		parent::__construct();

		$this->displayName = $this->l('Same product variant');
		$this->description = $this->l('Module shows other variants (colors) of product to customer.');
	}
	
	public function install()
    {
        // create tables
        $sql ='CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'spv_base
  (
    id             int           auto_increment,
    supref_digits  varchar(20)   not null,
    `supplier_id` int(10) unsigned NOT NULL,

    primary key(id),
    index(supref_digits),
    index(supplier_id)
  )
ENGINE = MyISAM;';
        $result = Db::getInstance()->Execute($sql);
        
        
        $sql='CREATE TABLE IF NOT EXISTS '._DB_PREFIX_.'spv_variant
  (
    `id` int(11) NOT NULL AUTO_INCREMENT,
  `base_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `manual_update` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `base_id_2` (`base_id`,`product_id`),
  KEY `base_id` (`base_id`),
  KEY `product_id` (`product_id`),
  KEY `manual_update` (`manual_update`)
  )
 ENGINE = MyISAM;';
        $result = $result && Db::getInstance()->Execute($sql);
        // check if hook is created
        if (!Db::getInstance()->getValue('select count(*) from '._DB_PREFIX_.'hook where name=\'productVariants\''))
        {
            // create hook
           $result = $result && Db::getInstance()->Execute('INSERT INTO `'._DB_PREFIX_.'hook` (`id_hook`, `name`, `title`, `description`, `position`, `live_edit`)'.
                                       'VALUES (NULL , \'productVariants\', \'Hook used to show variants of product in product details page and in category page\', NULL, 1, 0)');
        }
        $result &= parent::install();

		return $result AND $this->registerHook('addProduct') and $this->registerHook('updateProduct') and $this->registerHook('deleteProduct')
            and $this->installModuleTab('AdminSameProductVariant', 'Same product variants') and $this->registerHook('productVariants');
	}

    	
	/*
	 * Creates a "subtab" in "Catalog" tab.
	 *
	 * @access private
	 * @param string $class    - Class name, like "AdminCatalog"
	 * @param string $name     - Tab title
	 *
	 * @return boolean
	 */
    private function installModuleTab($class, $name)
    {
		$sql = '
		SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `class_name` = "AdminCatalog"';
		
		$tabParent = (int)(Db::getInstance()->getValue($sql));
		
        if ( ! is_array($name))
            $name = self::getMultilangField($name);
        /*     
        if (self::fileExistsInModulesDir('logo.gif') && is_writeable(_PS_IMG_DIR_ . 't/'))
            $this->copyLogo($class);
        */
                
        $tab = new Tab();
        $tab->name       = $name;
        $tab->class_name = $class;
        $tab->module     = $this->name;
        $tab->id_parent  = $tabParent;
        
        return $tab->save();
    }

    
    /*
	 * Turns a string into an array with language IDs as keys. This array can
	 * be used to create multilingual fields for prestashop
	 *
	 * @access private
	 * @scope static
	 * @param mixed $field    - A field to turn into multilingual
	 *
	 * @return array
	 */
    private static function getMultilangField($field)
    {
        $languages = Language::getLanguages();
        $res = array();
        
        foreach ($languages as $lang)
            $res[$lang['id_lang']] = $field;
            
        return $res;
    }

    public function uninstall()
	{
		$sql = '
		SELECT `id_tab` FROM `' . _DB_PREFIX_ . 'tab` WHERE `module` = "' . pSQL($this->name) . '"';
		
		$result = Db::getInstance()->ExecuteS($sql);
		
		if ($result && sizeof($result))
		{
			foreach ($result as $tabData)
			{
				$tab = new Tab($tabData['id_tab']);
				
				if (Validate::isLoadedObject($tab))
					$tab->delete();
			}
		}

        return parent::uninstall();
    }
    
    
    /**
     * shows connections list
     */
    public function getContent()
    {
        global $currentIndex, $cookie;
        
        $tab = 'AdminSameProductVariant';
        
        Tools::redirectAdmin(str_replace(strrchr($currentIndex, '?'), '', $currentIndex) . '?tab=' . $tab . '&token=' . Tools::getAdminTokenLite($tab));
    }
    
    
    public function hookAddProduct($params)
    {
        require_once(dirname(__FILE__) . '/SPVBase.php');
        SPVBase::autoMatch($params['product']);
    }

    public function hookUpdateProduct($params)
    {
        require_once(dirname(__FILE__) . '/SPVBase.php');
        
        // check if product can be present in current tupple after edit
        // (supplier condition)
        $base = Db::getInstance()->getRow('select b.id, b.supplier_id, v.manual_update, b.supref_digits from '._DB_PREFIX_.'spv_base b inner join '._DB_PREFIX_.
                                            'spv_variant v on b.id=v.base_id where product_id='.$params['product']->id);

        if($base['id'] && $base['manual_update']==0 && ($params['product']->id_supplier!=$base['supplier_id'] or
                                                        strpos($params['product']->supplier_reference, $base['supref_digits'])===false))
        {
            // product should be deleted from tupple
            SPVBase::deleteProductVariant($params['product']->id);
            $base['id'] = 0;
        }

        if ($base['id']==0)
        {
            // product not in tupple, try to add it
            SPVBase::autoMatch($params['product']);
        }

    }

    public function hookDeleteProduct($params)
    {
        require_once(dirname(__FILE__) . '/SPVBase.php');
        SPVBase::deleteProductVariant($params['product']->id);
    }


    /**
     * Generates variants portion for product details page
     */
    function hookProductVariants($params)
    {
        if ($this->active)
        {
            require_once(dirname(__FILE__) . '/SPVBase.php');

            $productId = $params['productId'];
            
            // reading variants
            $productVariants = SPVBase::getOtherSameProducts($productId, Context::getContext()->cookie->id_lang);
            if (isset($productVariants[$productId]) && count($productVariants[$productId])>1)
            {
            	// assign preselected size (if it is set)
            	$preselectedSizeAdd = '';
            	foreach ($_GET as $key=>$value)
            	{
            		if (strpos($key, 'layered_id_attribute_group')!==false)
            		{
            			list($optionId, $groupId) = explode('_', $value);
            			$preselectedSizeAdd = '?group_'.$groupId.'='.$optionId;
            			break;
            		}
            		elseif (preg_match('/^group_\d$/', $key))
            		{
            			$preselectedSizeAdd = "?$key=$value";
            			break;
            		}
            	}
                Context::getContext()->smarty->assign(array('productVariants'=>$productVariants[$productId], 
                	'currentProductId'=>$productId, 'link'=>Context::getContext()->link, 'preselectedSizeAdd' => $preselectedSizeAdd,
                                      'variantImageSize'=>Image::getSize('pr_details_thumb')));
                if (isset($params['productDetails']))
                {
                	return $this->display(__FILE__, 'product_details.tpl');
                }
                return $this->display(__FILE__, 'category_product.tpl');
            }
        }
    }


    function getProductVariants($productsIds)
    {
        global $cookie;

        if ($this->active)
        {
            require_once(dirname(__FILE__) . '/SPVBase.php');
            return SPVBase::getOtherSameProducts($productsIds, $cookie->id_lang);
        }
    }
    
    /**
    function deleteVariant()
    {
    }
    */
}


/**
 * Query to find groups with active products
 *
 SELECT base_id, count( product_id ) AS activeNum
FROM ps_spv_variant a
INNER JOIN ps_product p ON a.product_id = p.id_product
WHERE p.active =1
GROUP BY base_id
HAVING activeNum >1
*/