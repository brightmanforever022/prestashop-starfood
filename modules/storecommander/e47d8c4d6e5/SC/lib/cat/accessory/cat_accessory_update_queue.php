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
				$id_accessory=$row->row;
				$updated_products[$id_accessory]=$id_accessory;
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}

				if(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = '".psql($id_accessory)."'";
					Db::getInstance()->Execute($sql);
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$idlist=Tools::getValue('idlist','');
					$sub_action=Tools::getValue('sub_action','');
					$id_category=Tools::getValue('id_category','0');

					$tmp = explode(",",$idlist);
					if(!empty($tmp) && count($tmp)==1)
						$updated_products[$tmp[0]]=$tmp[0];
					elseif(!empty($tmp) && count($tmp)>1)
						$updated_products = array_merge($updated_products,$tmp);

					if($sub_action!='')
					{
						switch($sub_action){
							case '1':
								$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = '".psql($id_accessory)."' AND `id_product_1` IN (".psql($idlist).")";
								Db::getInstance()->Execute($sql);
								$id_product_acc=explode(',',$id_accessory);
								$id_product_src=explode(',',$idlist);
								$sql='';
								foreach($id_product_acc AS $acc)
								{
									foreach($id_product_src AS $src)
									{
										if ($acc!=$src && $src!=0 && $acc!=0)
											$sql.='('.$src.','.$acc.'),';
									}
								}
								if ($sql!='')
								{
									$sql = trim($sql,',');
									$sql = "INSERT INTO `"._DB_PREFIX_."accessory` (id_product_1,id_product_2) VALUES ".psql($sql);
									Db::getInstance()->Execute($sql);
								}
								break;
							case '0':
								$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = '".psql($id_accessory)."' AND `id_product_1` IN (".psql($idlist).")";
								Db::getInstance()->Execute($sql);
								break;
							case 'addSel':
								$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = 0 OR `id_product_1` = 0";
								Db::getInstance()->Execute($sql);
								$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = '".psql($id_accessory)."' AND `id_product_1` IN (".psql($idlist).")";
								Db::getInstance()->Execute($sql);
								$id_product_acc=explode(',',$id_accessory);
								$id_product_src=explode(',',$idlist);
								$sql='';
								foreach($id_product_acc AS $acc)
								{
									foreach($id_product_src AS $src)
									{
										if ($acc!=$src && $src!=0 && $acc!=0)
											$sql.='('.$src.','.$acc.'),';
									}
								}
								if ($sql!='')
								{
									$sql = trim($sql,',');
									$sql = "INSERT INTO `"._DB_PREFIX_."accessory` (id_product_1,id_product_2) VALUES ".psql($sql);
									Db::getInstance()->Execute($sql);
								}
								break;
							case 'delSel':
								$sql = "DELETE FROM `"._DB_PREFIX_."accessory` WHERE `id_product_2` = '".psql($id_accessory)."' AND `id_product_1` IN (".psql($idlist).")";
								Db::getInstance()->Execute($sql);
								break;
							case 'active_accessory':
								if(!empty($id_accessory))
								{
									$value=Tools::getValue('value','0');
									if(SCMS && SCI::getSelectedShop()>0)
										$product = new Product((int)$id_accessory, false, null, (int)SCI::getSelectedShop());
									elseif(SCMS)
									$product = new Product((int)$id_accessory, false);
									else
										$product = new Product((int)$id_accessory);
					
									$product->active = (int)$value;
									$product->save();
								}
								break;
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