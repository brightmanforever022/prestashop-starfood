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

@error_reporting(E_ERROR | E_PARSE);
@ini_set("display_errors", "ON");

$id_lang = Tools::getValue('id_lang','0');
$action = Tools::getValue('action','');

$return = "ERROR: Try again later";

// FUNCTIONS
$return_datas = array();
$updated_products = array();

// Récupération de toutes les modifications à effectuer
if(!empty($_POST["rows"]) || $action=="insert")
{
	if($action!="insert")
	{
		if(_PS_MAGIC_QUOTES_GPC_)
			$_POST["rows"] = stripslashes($_POST["rows"]);
		$rows = json_decode($_POST["rows"]);
	}
	else
	{
		$rows = array();
		$rows[0] = new stdClass();
		$rows[0]->name = Tools::getValue('act','');
		$rows[0]->action = Tools::getValue('action','');
		$rows[0]->row = Tools::getValue('gr_id','');
		$rows[0]->callback = Tools::getValue('callback','');
		$rows[0]->params = $_POST;
	}
	
	if(is_array($rows) && count($rows)>0)
	{
		$callbacks = '';
		
		// Première boucle pour remplir la table sc_queue_log 
		// avec toutes ces modifications
		$log_ids = array();
		$date = date("Y-m-d H:i:s");
		foreach($rows as $num => $row)
		{
			$id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params)?$row->params:array()), (!empty($row->callback)?$row->callback:null), $date);
			$log_ids[$num] = $id;
		}
		
		// Deuxième boucle pour effectuer les 
		// actions les une après les autres
		foreach($rows as $num => $row)
		{
			if(!empty($log_ids[$num]))
			{
				$gr_id = intval($row->row);
				$id=$row->row;
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}

				if(!empty($action) && $action=="update" && !empty($gr_id))
				{
					list($id_product, $id_shop) = explode("_", $id);
					if(!empty($id_product) && !empty($id_shop))
					{
						$updated_products[$id_product]=$id_product;
						$list_lang_fields = "name,link_rewrite,available_now,available_later";
						$list_shop_fields = "active,visibility,on_sale,online_only,show_price,minimal_quantity,ecotax,id_tax_rules_group,price,wholesale_price,unity,unit_price_ratio,additional_shipping_cost,available_for_order,available_date,condition,available_date,online_only";
                        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                            $list_shop_fields .= ",show_condition";
						$ecotaxrate=SCI::getEcotaxTaxRate();
						
						// LANG
						$fields=explode(",",$list_lang_fields);
						$todo=array();
						$link_rewrite = "";
						foreach($fields AS $field)
						{
							if (isset($_POST[$field]))
							{
								$val=Tools::getValue($field);
								$todo[]="`".$field."`='".psql(html_entity_decode( $val ))."'";
						
								if ($field=='name' && _s('CAT_SEO_NAME_TO_URL'))
								{
									$link_rewrite="`link_rewrite`='".link_rewrite($val)."'";
								}
							}
						}
						
						if(!empty($link_rewrite))
							$todo[]=$link_rewrite;
						
						if (count($todo))
						{
							$sql = "UPDATE "._DB_PREFIX_."product_lang SET ".join(' , ',$todo)." WHERE id_product='".intval($id_product)."' AND id_shop='".intval($id_shop)."' AND id_lang='".intval($id_lang)."'";
							Db::getInstance()->Execute($sql);
						}
						
						// SHOP
						$fields=explode(",",$list_shop_fields);
						$todo=array();
						foreach($fields AS $field)
						{
							if (isset($_POST[$field]))
							{
								$val=Tools::getValue($field);
						
								if($field == "ecotax" && !empty($val))
								{
									$val=$val/$ecotaxrate;
								}
						
								$todo[]="`".$field."`='".psql(html_entity_decode( $val ))."'";
							}
						}
						if (count($todo))
						{
							$sql = "UPDATE "._DB_PREFIX_."product_shop SET ".join(' , ',$todo)." WHERE id_product='".intval($id_product)."' AND id_shop='".intval($id_shop)."'";
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
								$sql_supplier = "SELECT * FROM "._DB_PREFIX_."product_supplier WHERE id_product='".intval($id_product)."' AND id_supplier='".intval($product->id_supplier)."'";
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
							VALUES('".intval($id_product)."','0','".$product->id_supplier."','".psql(html_entity_decode( $val ))."')";
									Db::getInstance()->Execute($sql);
								}
							}
						}
						if(isset($_POST["ecotax"]))
						{
							$ecotax=Tools::getValue("ecotax", 0)/$ecotaxrate;
							$todo[]="`ecotax`='".psql(html_entity_decode( $ecotax ))."'";
						}
						if(isset($_POST["ean13"]))
						{
							$ean13=Tools::getValue("ean13");
							$todo[]="`ean13`='".psql(( $ean13 ))."'";
						}
                        if(isset($_POST["upc"]))
                        {
                            $upc=Tools::getValue("upc");
                            $todo[]="`upc`='".psql(( $upc ))."'";
                        }
                        if(isset($_POST["isbn"]))
                        {
                            $isbn=Tools::getValue("isbn");
                            $todo[]="`isbn`='".psql(( $isbn ))."'";
                        }
						if(isset($_POST["out_of_stock"]))
						{
							$out_of_stock=Tools::getValue("out_of_stock");
							$todo[]="`out_of_stock`='".psql(( $out_of_stock ))."'";
						}
						if(isset($_POST["location"]))
						{
							$location=Tools::getValue("location");
							$todo[]="`location`='".psql(( $location ))."'";
						}
						if (count($todo))
						{
							$sql = "UPDATE "._DB_PREFIX_."product SET ".join(' , ',$todo)." WHERE id_product='".intval($id_product)."'";
							Db::getInstance()->Execute($sql);
						}

						//update date_upd
						$sql = "UPDATE "._DB_PREFIX_."product SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product=".(int)$id_product.";";
						if(SCMS) {
							$sql .= "UPDATE "._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product=".(int)$id_product." AND id_shop = ".(int)$id_shop;
						}
						Db::getInstance()->Execute($sql);
						
						sc_ext::readCustomMsProductGridConfigXML('onAfterUpdateSQL');
					}
				}
				
				QueueLog::delete(($log_ids[$num]));
			}
		}

		// PM Cache
		if(!empty($updated_products))
			ExtensionPMCM::clearFromIdsProduct($updated_products);
		
		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}
echo $return;