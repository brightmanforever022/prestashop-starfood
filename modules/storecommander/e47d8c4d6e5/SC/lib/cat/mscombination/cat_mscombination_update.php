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
	$id=Tools::getValue('gr_id',"_");
	list($id_product, $id_product_attribute, $id_shop) = explode("_", $id);
	
	$list_shop_fields = "minimal_quantity,ecotax,wholesale_price,available_date";
	
	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated" && !empty($id_product) && !empty($id_product_attribute) && !empty($id_shop))
	{
		$ecotaxrate=SCI::getEcotaxTaxRate();
		
		// SHOP
		$fields=explode(",",$list_shop_fields);
		$todo=array();
		foreach($fields AS $field)
		{
			if (isset($_POST[$field]) || isset($_POST[$field]))
			{
				$val=Tools::getValue($field);
				
				if($field == "ecotax" && !empty($val))
				{
					$val=$val/$ecotaxrate;
				}
				
				$todo[]=$field."='".psql(html_entity_decode( $val ))."'";
			}
		}
		
		if (isset($_POST['priceextax']))
		{	
			$todo[]="`price`='".((floatval($_POST["priceextax"])-(floatval($_POST["ppriceextax"]))))."'";
		}
		
		if (isset($_POST['weight']))
		{	
			$product = new Product($id_product);
			
			$todo[]="`weight`='".((floatval($_POST["weight"])-(floatval($product->weight))))."'";
		}
		
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."product_attribute_shop SET ".join(' , ',$todo)." WHERE id_product_attribute='".intval($id_product_attribute)."' AND id_shop='".intval($id_shop)."'";
			Db::getInstance()->Execute($sql);
		}
		
		// REF
		$todo=array();
		if(isset($_POST["reference"]))
		{
			$val=Tools::getValue("reference");
			$todo[]="`reference`='".psql(html_entity_decode( $val ))."'";
		}
		if(isset($_POST["supplier_reference"]))
		{
			$val=Tools::getValue("supplier_reference");
			$todo[]="`supplier_reference`='".psql(html_entity_decode( $val ))."'";

			$product = new Product($id_product);
			if(!empty($product->id_supplier))
			{
				$sql_supplier = "SELECT * FROM "._DB_PREFIX_."product_supplier WHERE id_product='".intval($id_product)."' AND id_product_attribute='".intval($id_product_attribute)."' AND id_supplier='".intval($product->id_supplier)."'";
				$actual_product_supplier = Db::getInstance()->getRow($sql_supplier);
				if(!empty($actual_product_supplier["id_product_supplier"]))
				{
					$sql = "UPDATE "._DB_PREFIX_."product_supplier SET `product_supplier_reference`='".psql(html_entity_decode( $val ))."' WHERE id_product_supplier='".intval($actual_product_supplier["id_product_supplier"])."'";
					Db::getInstance()->Execute($sql);
				}
				else
				{
					$sql = "INSERT INTO "._DB_PREFIX_."product_supplier
							(id_product, id_product_attribute, id_supplier, product_supplier_reference)
							VALUES('".intval($id_product)."','".intval($id_product_attribute)."','".$product->id_supplier."','".psql(html_entity_decode( $val ))."')";
					Db::getInstance()->Execute($sql);
				}
			}
		}
		if(isset($_POST["ecotax"]))
		{
			$ecotax=Tools::getValue("ecotax", 0)/$ecotaxrate;
			$todo[]="`ecotax`='".psql(html_entity_decode( $ecotax ))."'";
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."product_attribute SET ".join(' , ',$todo)." WHERE id_product_attribute='".intval($id_product_attribute)."'";
			Db::getInstance()->Execute($sql);
		}
		
		if(isset($_POST["quantity"]))
		{
			SCI::setQuantity($id_product, $id_product_attribute, intval($_POST["quantity"]), $id_shop);
		}
		
		$newId = $_POST["gr_id"];
		$action = "update";
		
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	echo '</data>';
