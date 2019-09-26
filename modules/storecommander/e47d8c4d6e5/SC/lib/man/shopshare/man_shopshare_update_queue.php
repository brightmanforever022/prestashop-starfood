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
$updated_manufacturers = array();
function removeInShop($manufacturer_id, $shop_id)
{
	if(!empty($manufacturer_id) && !empty($shop_id))
	{
		$sql = "DELETE FROM `"._DB_PREFIX_."manufacturer_shop` WHERE `id_manufacturer` = '".psql($manufacturer_id)."' AND id_shop = '".psql($shop_id)."'";
		Db::getInstance()->Execute($sql);
	}
}

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
				if(empty($gr_id))
					$gr_id=Tools::getValue('gr_id','0');

				if(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$idlist=$gr_id;
					$action_upd=Tools::getValue('action_upd','');
					$id_lang=Tools::getValue('id_lang','0');
					$id_shop=Tools::getValue('id_shop','0');
					$id_actual_shop = SCI::getSelectedShop();
					$value=Tools::getValue('value','0');
					$auto_share_imgs=intval(Tools::getValue('auto_share_imgs','0'));
					
					if($value=="true")
						$value = 1;
					else
						$value = 0;
					
					$ids = explode(",", $idlist);
					
					if($action_upd!='' && !empty($id_shop) && !empty($idlist)/* && !empty($id_actual_shop)*/)
					{
						if(!empty($ids) && count($ids)==1)
							$updated_manufacturers[$ids]=$ids;
						elseif(!empty($ids) && count($ids)>1)
							$updated_manufacturers = array_merge($updated_manufacturers,$ids);
						switch($action_upd)
						{
							case 'present':
								foreach($ids as $id)
								{
									$manufacturer = new Manufacturer($id, false, null, $id_actual_shop);
					
									if(!$manufacturer->isAssociatedToShop($id_shop) && $value=="1")
									{
										$manufacturer->id_shop_list=array($id_shop);
										$manufacturer->save();
									}
									elseif($manufacturer->isAssociatedToShop($id_shop) && empty($value))
									{
										removeInShop($id, $id_shop);
									}
								}
								break;
								// Modification de present
								// pour un ou plusieurs shops passés en params
								// pour un ou plusieurs manufacturers passés en params
							case 'mass_present':
								$shops  = explode(",", $id_shop);
								foreach($shops as $id_shop)
								{
									foreach($ids as $id)
									{										
										$manufacturer = new Manufacturer($id, false, null, $id_actual_shop);

										if(!$manufacturer->isAssociatedToShop($id_shop) && $value=="1")
										{
											$manufacturer->id_shop_list=array($id_shop);
											$manufacturer->save();
										}
										elseif($manufacturer->isAssociatedToShop($id_shop) && empty($value))
										{
											removeInShop($id, $id_shop);
										}
									}
								}
								break;
						}
					}
				}
				
				QueueLog::delete(($log_ids[$num]));
			}
		}

		// PM Cache
		if(!empty($updated_manufacturers))
			ExtensionPMCM::clearFromIdsmanufacturer($updated_manufacturers);
		
		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}
echo $return;
