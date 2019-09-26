<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/
	$id_lang=intval(Tools::getValue('id_lang'));
	$id_specific_price=(Tools::getValue('gr_id',0));
	$id_product_attribute=(Tools::getValue('id_product_attribute',0));
	$id_product = intval(Tools::getValue('id_product',0));
	$id_shop = Tools::getValue('id_shop',0);
	$id_shop_group = Tools::getValue('id_shop_group',0);
	$id_currency = Tools::getValue('id_currency',0);
	$id_country = Tools::getValue('id_country',0);
	$id_group = Tools::getValue('id_group',0);
	$id_customer = Tools::getValue('id_customer',0); // TODO: grid management
	$price = str_replace(',','.',trim(Tools::getValue('price')));
	$from_quantity = Tools::getValue('from_quantity');
	$reduction_tax = intval(Tools::getValue('reduction_tax', '1'));
	$reduction = str_replace(',','.',Tools::getValue('reduction'));
	$reduction_type=(strpos(trim($reduction),'%')!==false?'percentage' : 'amount');
	$reduction = str_replace('%','',$reduction);
	$reduction=str_replace(',','.',$reduction);
	$from = Tools::getValue('from');
	$to = Tools::getValue('to');
	$debug = false;

    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
        $id_customer = Tools::getValue('id_customer');
        if (is_numeric($id_customer)) {
            $sql = 'SELECT COUNT(id_customer) as nbCus FROM ' . _DB_PREFIX_ . 'customer WHERE id_customer = ' . (int)$id_customer;
            $res = Db::getInstance()->getRow($sql);
            if ($res['nbCus'] == 0) {
                $id_customer = 0;
            }
        } else {
            $id_customer = 0;
        }
    }

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){
		$spe_id = "";
		$id_product_attributes = explode(",", $id_product_attribute);
		
		foreach($id_product_attributes as $id_product_attribute)
		{
			$specificPrice = new SpecificPrice();
			$specificPrice->id_product = $id_product;
			$specificPrice->id_product_attribute = $id_product_attribute;
			$specificPrice->id_shop = 0;//(count(SCI::getSelectedShopActionList()) > 1 || SCI::getSelectedShop() == 0 ? 0 : (int)SCI::getSelectedShop() );
			$specificPrice->id_currency = (int)($id_currency);
			$specificPrice->id_country = (int)($id_country);
			$specificPrice->id_group = (int)($id_group);
			if (version_compare(_PS_VERSION_, '1.5.0', '>='))
				$specificPrice->id_customer = (int)($id_customer);
			$specificPrice->price = (float)($price);
			$specificPrice->from_quantity = 1;
			if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
				$specificPrice->reduction_tax = (int)$reduction_tax;
			$specificPrice->reduction = (float)($reduction_type == 'percentage' ? (floatval($reduction) / 100) : $reduction);
			$specificPrice->reduction_type = $reduction_type;
			$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
			$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $specificPrice->id_customer = (int)($id_customer);
			$specificPrice->add();
			
			if(!empty($spe_id))
				$spe_id .= ",";
			$spe_id .= $specificPrice->id;
		}
		$newId = $spe_id;
		$action = "insert";

	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array('price','from_quantity','id_shop','id_shop_group','id_group','id_country','id_currency','reduction','reduction_type','from','to','reduction_tax','id_customer');
		
		$id_specific_prices = explode(",", $id_specific_price);
		foreach($id_specific_prices as $id_specific_price)
		{
			$specificPrice = new SpecificPrice((int)($id_specific_price));
			foreach($fields as $field)
			{
				if (isset($_POST[$field]))
				{
					if($field=="reduction")
					{
						$specificPrice->reduction = (float)($reduction_type == 'percentage' ? ($reduction / 100) : $reduction);
						$specificPrice->reduction_type = $reduction_type;
					}
					elseif($field=="reduction_type")
						$specificPrice->reduction_type = $reduction_type;
					elseif($field=="from")
						$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
					elseif($field=="to")
						$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
                    elseif($field=="id_customer")
                        $specificPrice->id_customer = $id_customer;
					elseif($field=="price")
						$specificPrice->price = (float)$price;
					else
						$specificPrice->$field = (int)${$field};
				}
			}
			$specificPrice->update();
		}
//		addToHistory('discount_quantity','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."discount_quantity",psql(Tools::getValue($field)));

		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){

		$specificPrice = new SpecificPrice((int)($id_specific_price));
		$specificPrice->delete();
		$newId = $_POST["gr_id"];
		$action = "delete";
		
	}

	if(!empty($id_product))
		ExtensionPMCM::clearFromIdsProduct($id_product);
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo ($debug ? '<sql><![CDATA['.$debug.']]></sql>':'');
	echo '</data>';
