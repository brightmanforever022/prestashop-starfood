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
				$id_specific_price=$row->row;
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}



				if(!empty($action) && $action=="insert")
				{
					$id_shop = Tools::getValue('id_shop',0);
					$id_shop_group = Tools::getValue('id_shop_group',0);
					$id_currency = Tools::getValue('id_currency',0);
					$id_country = Tools::getValue('id_country',0);
					$id_group = Tools::getValue('id_group',0);
					$price = str_replace(',','.',trim(Tools::getValue('price')));
					$from_quantity = Tools::getValue('from_quantity');
					$reduction_tax = Tools::getValue('reduction_tax');
					$reduction = str_replace(',','.',Tools::getValue('reduction'));
					$reduction_type=(strpos(trim($reduction),'%')!==false?'percentage' : 'amount');
					$reduction = str_replace('%','',$reduction);
					$reduction=str_replace(',','.',$reduction);
					$from = Tools::getValue('from');
					$to = Tools::getValue('to');

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

					$id_product = (Tools::getValue('id_product',0));
					$id_products = explode(",", $id_product);
					if(!empty($id_products) && count($id_products)==1)
						$updated_products[$id_products]=$id_products;
					elseif(!empty($id_products) && count($id_products)>1)
						$updated_products = array_merge($updated_products,$id_products);
					
					$spe_id = "";
					$id_products = explode(",", $id_product);
					
					foreach($id_products as $id_product)
					{
						if ((int)$id_product > 0)
						{
							$specificPrice = new SpecificPrice();
							$specificPrice->id_product = $id_product;
							$specificPrice->id_product_attribute = 0;
							$specificPrice->id_shop = 0;//(count(SCI::getSelectedShopActionList()) > 1 || SCI::getSelectedShop() == 0 ? 0 : (int)SCI::getSelectedShop() );
							$specificPrice->id_shop_group = 0;
							$specificPrice->id_currency = (int)($id_currency);
							$specificPrice->id_country = (int)($id_country);
							$specificPrice->id_group = (int)($id_group);
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$specificPrice->id_customer = (int)($id_customer);
							if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
								$specificPrice->reduction_tax = (int)$reduction_tax;
							$specificPrice->price = (float)($price);
							$specificPrice->from_quantity = 1;
							$specificPrice->reduction = (float)($reduction_type == 'percentage' ? (floatval($reduction) / 100) : $reduction);
							$specificPrice->reduction_type = $reduction_type;
							$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
							$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
							$specificPrice->save();

							if(!empty($spe_id))
								$spe_id .= ",";
							$spe_id .= $specificPrice->id;
						}
					}
					$newId = $spe_id;
					
					if(!empty($newId))
					{
						$callbacks = str_replace("{newid}", $newId, $callbacks) ;
					}
				}
				elseif(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$id_specific_prices = explode(",", $id_specific_price);
					foreach($id_specific_prices as $id_specific_price)
					{
						$specificPrice = new SpecificPrice((int)($id_specific_price));
						$updated_products[$specificPrice->id_product]=$specificPrice->id_product;
						$specificPrice->delete();
					}
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$fields=array('price','from_quantity','id_shop','id_shop_group','id_group','id_country','id_currency','reduction','reduction_type','from','to','reduction_tax','id_customer');
					
					$reduction = str_replace(',','.',Tools::getValue('reduction'));
					$reduction_type=(strpos(trim($reduction),'%')!==false?'percentage' : 'amount');
					$reduction = str_replace('%','',$reduction);
					$reduction=str_replace(',','.',$reduction);
					$from = Tools::getValue('from');
					$to = Tools::getValue('to');

                    $updated_products = array();

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

					$id_specific_prices = explode(",", $id_specific_price);
					foreach($id_specific_prices as $id_specific_price)
					{
						$specificPrice = new SpecificPrice((int)($id_specific_price));
						$updated_products[$specificPrice->id_product]=$specificPrice->id_product;
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
								elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $field=="id_customer")
									$specificPrice->id_customer = $id_customer;
								elseif($field=="price")
									$specificPrice->price = (float)$_POST[$field];
								elseif($field=="from_quantity")
								{
									$specificPrice->from_quantity = (_s("APP_COMPAT_MODULE_PPE")?floatval($_POST[$field]):(int)$_POST[$field]);
								}
								else
									$specificPrice->$field = (int)$_POST[$field];
							}
						}
						$specificPrice->update();
					}

					if(!empty($updated_products))
                    {
                        foreach ($updated_products as $id_product)
                        {
                            $sql = "UPDATE "._DB_PREFIX_."product SET date_upd='".date("Y-m-d H:i:s")."'
							        WHERE id_product=".intval($id_product)."";
                            Db::getInstance()->Execute($sql);
                            if(SCMS)
                            {
                                $sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd='".date("Y-m-d H:i:s")."'
							        WHERE id_product=".intval($id_product)."";
                                Db::getInstance()->Execute($sql);
                            }
                        }
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