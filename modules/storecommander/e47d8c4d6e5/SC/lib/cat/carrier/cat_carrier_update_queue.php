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
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}

				if(!empty($action) && $action=="update")
				{
					$idlist=Tools::getValue('idlist','');
					$sub_action=Tools::getValue('sub_action','');
					$value=(Tools::getValue('value'));
					
					if($value=="true")
						$value = 1;
					else
						$value = 0;
					
					if(!empty($idlist))
					{
						$products=(explode(',',$idlist));
						if($sub_action=="present")
						{
							$id_carrier = (int)$gr_id;
							if(!empty($id_carrier))
							{
								if($value == 1)
								{
									if(SCMS)
									{
										$shops = SCI::getSelectedShopActionList();
										foreach($shops as $shop_id)
										{
											foreach($products as $id_product)
											{
												$sql = 'SELECT *
												FROM `'._DB_PREFIX_.'product_carrier`
												WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
													AND `id_product` = '.(int)$id_product.'
													AND id_shop = "'.(int)$shop_id.'"';
												$tmp_used = Db::getInstance()->executeS($sql);
												if(empty($tmp_used))
												{
													$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
													VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)$shop_id.'")';
													Db::getInstance()->execute($sql);
												}
											}
										}
									}
									else
									{
										foreach($products as $id_product)
										{
											$sql = 'SELECT *
											FROM `'._DB_PREFIX_.'product_carrier`
											WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
												AND `id_product` = '.(int)$id_product.'';
											$tmp_used = Db::getInstance()->executeS($sql);
											if(empty($tmp_used))
											{
												$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
												VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","1")';
												Db::getInstance()->execute($sql);
											}
										}
									}
								}
								else
								{
									if(SCMS)
									{
										$shops = SCI::getSelectedShopActionList();
										foreach($shops as $shop_id)
										{
											foreach($products as $id_product)
											{
												$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
												WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
													AND `id_product` = '.(int)$id_product.'
													AND id_shop = "'.(int)$shop_id.'"';
												Db::getInstance()->execute($sql);
											}
										}
									}
									else
									{
										foreach($products as $id_product)
										{
											$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
											WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
											AND `id_product` = '.(int)$id_product.'';
											Db::getInstance()->execute($sql);
										}
									}
								}
							}
						}
						elseif($sub_action=="mass_add")
						{
							$carriers=Tools::getValue('carriers','');
							$carriers=(explode(',',$carriers));
							$shops = SCI::getSelectedShopActionList();
							foreach($carriers as $id_carrier)
							{
								if(SCMS)
								{
									foreach($shops as $shop_id)
									{
										foreach($products as $id_product)
										{
											$sql = 'SELECT *
											FROM `'._DB_PREFIX_.'product_carrier`
											WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
												AND `id_product` = '.(int)$id_product.'
												AND id_shop = "'.(int)$shop_id.'"';
											$tmp_used = Db::getInstance()->executeS($sql);
											if(empty($tmp_used))
											{
												$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
												VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","'.(int)$shop_id.'")';
												Db::getInstance()->execute($sql);
											}
										}
									}
								}
								else
								{
									foreach($products as $id_product)
									{
										$sql = 'SELECT *
										FROM `'._DB_PREFIX_.'product_carrier`
										WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
											AND `id_product` = '.(int)$id_product.'';
										$tmp_used = Db::getInstance()->executeS($sql);
										if(empty($tmp_used))
										{
											$sql = 'INSERT INTO `'._DB_PREFIX_.'product_carrier` (id_product, id_carrier_reference, id_shop)
												VALUES ("'.(int)$id_product.'","'.(int)$id_carrier.'","1")';
											Db::getInstance()->execute($sql);
										}
									}
								}
							}
						}
						elseif($sub_action=="mass_delete")
						{
							$carriers=Tools::getValue('carriers','');
							$carriers=(explode(',',$carriers));
							$shops = SCI::getSelectedShopActionList();
							foreach($carriers as $id_carrier)
							{
								if(SCMS)
								{
									$shops = SCI::getSelectedShopActionList();
									foreach($shops as $shop_id)
									{
										foreach($products as $id_product)
										{
											$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
											WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
												AND `id_product` = '.(int)$id_product.'
												AND id_shop = "'.(int)$shop_id.'"';
											Db::getInstance()->execute($sql);
										}
									}
								}
								else
								{
									foreach($products as $id_product)
									{
										$sql = 'DELETE FROM `'._DB_PREFIX_.'product_carrier`
										WHERE `id_carrier_reference` = "'.(int)$id_carrier.'"
										AND `id_product` = '.(int)$id_product.'';
										Db::getInstance()->execute($sql);
									}
								}
							}
						}
					}

					//update date_upd
					$sql = "UPDATE "._DB_PREFIX_."product SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product = (".(int)$idlist.");";
					if(SCMS) {
						$sql .= "UPDATE "._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product = (".(int)$idlist.") AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
					}
					Db::getInstance()->Execute($sql);
				}
				
				QueueLog::delete(($log_ids[$num]));
			}
		}
		
		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}
echo $return;