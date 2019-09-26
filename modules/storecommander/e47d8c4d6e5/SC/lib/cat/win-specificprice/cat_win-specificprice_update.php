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

$id_lang = (int)Tools::getValue('id_lang','0');

$return = "ERROR: Try again later";

// FUNCTIONS
$updated_products = array();


// Récupération de toutes les modifications à effectuer
if(!empty($_POST["rows"]))
{
	if(_PS_MAGIC_QUOTES_GPC_)
		$_POST["rows"] = stripslashes($_POST["rows"]);
	$rows = json_decode($_POST["rows"]);

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
				$action = $row->action;

				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				$_POST=array();
				$_POST = (array) json_decode($row->params);

				if(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$id_specific_price=$gr_id;
					$id_specific_prices = explode(",", $id_specific_price);
					foreach($id_specific_prices AS $id_specific_price)
					{
						if(!empty($id_specific_price))
						{
							$specificPrice = new SpecificPrice((int)($id_specific_price));
							$updated_products[$specificPrice->id_product]=(int)$specificPrice->id_product;
							$specificPrice->delete();
						}
					}
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$id_specific_price=$gr_id;
					$id_product = (Tools::getValue('id_product',0));
					$id_shop = Tools::getValue('id_shop',0);
					$id_shop_selected = Tools::getValue('id_shop_selected',0);
					$id_shop_group = Tools::getValue('id_shop_group',0);
					$id_currency = Tools::getValue('id_currency',0);
					$id_country = Tools::getValue('id_country',0);
					$id_group = Tools::getValue('id_group',0);
					$id_customer = Tools::getValue('id_customer',0); // TODO: grid management
					$reduction_tax = Tools::getValue('reduction_tax');
					$from_quantity = Tools::getValue('from_quantity');
					
					$price = str_replace(',','.',trim(Tools::getValue('price')));
					$reduction_price = str_replace(',','.',Tools::getValue('reduction_price'));
					$reduction_percent = str_replace(',','.',Tools::getValue('reduction_percent'));
					
					$from = Tools::getValue('from');
					$to = Tools::getValue('to');
					
					$fields=array('price','from_quantity','id_shop','id_shop_group','id_group','id_country','id_currency','reduction_price','reduction_percent','from','to','reduction_tax');
					
					$id_specific_prices = explode(",", $id_specific_price);
					foreach($id_specific_prices as $id_specific_price)
					{
						$specificPrice = new SpecificPrice((int)($id_specific_price));
						$updated_products[$specificPrice->id_product]=(int)$specificPrice->id_product;
						foreach($fields as $field)
						{
							if (isset($_POST[$field]))
							{
								if($field=="reduction_price")
								{
									$specificPrice->reduction = (float)${$field};
									$specificPrice->reduction_type = "amount";
								}
								elseif($field=="reduction_percent")
								{
									$specificPrice->reduction = (float)(${$field}/100);
									$specificPrice->reduction_type = "percentage";
								}
								elseif($field=="from")
									$specificPrice->from = !$from ? '0000-00-00 00:00:00' : $from;
								elseif($field=="to")
									$specificPrice->to = !$to ? '0000-00-00 00:00:00' : $to;
								elseif($field=="price")
								{
									$specificPrice->price = (float)${$field};
								}
								else
									$specificPrice->$field = (int)${$field};
							}
						}
						$specificPrice->update();

                        sc_ext::readCustomWinSpePriceGridConfigXML('onAfterUpdateSQL');
					}
					
					if(isset($_POST["on_sale"]))
					{
						if(SCMS && !empty($id_shop_selected))
						{
							$product = new Product((int)$id_product, false, null, (int)$id_shop_selected);
						}
						else
							$product = new Product((int)$id_product, null);

						//echo $id_product.", ".false.", ".$id_lang.", ".$id_shop_selected." -> ".$product->link_rewrite;die();
						$product->on_sale = (int) $_POST["on_sale"];
						$product->save();
						SCI::hookExec('updateProduct', array('product' => $product));
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