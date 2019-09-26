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

$idlist=Tools::getValue('idlist','');
$action=Tools::getValue('action','');
$id_lang=Tools::getValue('id_lang','0');
$id_group=Tools::getValue('id_group','0');
$value=Tools::getValue('value','0');

if($value=="true")
	$value = 1;
else
	$value = 0;

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

$noRefreshCategory = false;

if($action!='' && !empty($id_group) && !empty($idlist))
{
	switch($action)
	{
		// Modification de present pour le group passé en params
		// pour un ou plusieurs categories passés en params
		case 'present':
			foreach($ids as $id)
			{
				$sql2 ="SELECT id_category
					FROM "._DB_PREFIX_."category_group
					WHERE id_category = '".intval($id)."'
						AND id_group = '".(int)$id_group."'";
				$res2 = Db::getInstance()->ExecuteS($sql2);
				
				if(empty($res2[0]["id_category"]) && $value=="1")
				{
					$sql3 ="INSERT INTO "._DB_PREFIX_."category_group (id_group, id_category)
					VALUES ('".intval($id_group)."','".intval($id)."')";
					Db::getInstance()->execute($sql3);
				}
				elseif(!empty($res2[0]["id_category"]) && empty($value))
				{
					$sql3 ="DELETE FROM "._DB_PREFIX_."category_group
					WHERE id_category = '".intval($id)."' AND id_group = '".intval($id_group)."'";
					Db::getInstance()->execute($sql3);
				}
			}
		break;
		// Modification de present 
		// pour un ou plusieurs groups passés en params
		// pour un ou plusieurs categories passés en params
		case 'mass_present':
			$groups  = explode(",", $id_group);
			foreach($groups as $id_group)
			{
				foreach($ids as $id)
				{
					$sql2 ="SELECT id_category
						FROM "._DB_PREFIX_."category_group
						WHERE id_category = '".intval($id)."'
							AND id_group = '".(int)$id_group."'";
					$res2 = Db::getInstance()->ExecuteS($sql2);
					
					if(empty($res2[0]["id_category"]) && $value=="1")
					{
						$sql3 ="INSERT INTO "._DB_PREFIX_."category_group (id_group, id_category)
						VALUES ('".intval($id_group)."','".intval($id)."')";
						Db::getInstance()->execute($sql3);
					}
					elseif(!empty($res2[0]["id_category"]) && empty($value))
					{
						$sql3 ="DELETE FROM "._DB_PREFIX_."category_group
						WHERE id_category = '".intval($id)."' AND id_group = '".intval($id_group)."'";
						Db::getInstance()->execute($sql3);
					}
				}
			}
		break;
	}
}

if($noRefreshCategory)
	echo "noRefreshCategory";