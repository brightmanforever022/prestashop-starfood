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
$id_actual_shop = SCI::getSelectedShop();
$value=Tools::getValue('value','0');

if($value=="true")
	$value = 1;
elseif($value=="false")
	$value = 0;

$multiple = false;
if(strpos($idlist, ",") !== false)
	$multiple = true;

$ids = explode(",", $idlist);

$noRefreshProduct = false;

if($action!='' && !empty($id_group) && !empty($idlist))
{
	switch($action)
	{
		case 'present':
			foreach($ids as $id)
			{
				$in_group = Db::getInstance()->getRow('
						SELECT `id_group`
						FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_group = "'.(int)$id_group.'"
							AND id_customer = "'.(int)$id.'"');
				if(empty($in_group["id_group"]) && $value=="1")
				{
					Db::getInstance()->execute('
						INSERT INTO `'._DB_PREFIX_.'customer_group` (id_group, id_customer)
						VALUES ("'.(int)$id_group.'","'.(int)$id.'")');
				}
				elseif(!empty($in_group["id_group"]) && empty($value))
				{
					Db::getInstance()->execute('
						DELETE FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_group = "'.(int)$id_group.'"
							AND id_customer = "'.(int)$id.'"');
				}
			}
			break;
		case 'default':
			foreach($ids as $id)
			{
				$customer = new Customer($id);

				if($value=="1")
				{
					$customer->id_default_group = $id_group;
					$customer->save();
				}
			}
			break;
		case 'mass_present':
			$groups  = explode(",", $id_group);
			foreach($groups as $id_group)
			{
				foreach($ids as $id)
				{
					$in_group = Db::getInstance()->getRow('
							SELECT `id_group`
							FROM `'._DB_PREFIX_.'customer_group`
							WHERE id_group = "'.(int)$id_group.'"
								AND id_customer = "'.(int)$id.'"');
					if(empty($in_group["id_group"]) && $value=="1")
					{
						Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'customer_group` (id_group, id_customer)
							VALUES ("'.(int)$id_group.'","'.(int)$id.'")');
					}
					elseif(!empty($in_group["id_group"]) && empty($value))
					{
						Db::getInstance()->execute('
							DELETE FROM `'._DB_PREFIX_.'customer_group`
							WHERE id_group = "'.(int)$id_group.'"
								AND id_customer = "'.(int)$id.'"');
					}
				}
			}
			break;
	}
}

if($noRefreshProduct)
	echo "noRefreshProduct";