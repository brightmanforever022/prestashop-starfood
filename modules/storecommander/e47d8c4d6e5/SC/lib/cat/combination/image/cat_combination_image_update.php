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

	$ids=Tools::getValue('ids');
	$images  = explode(",", $ids);
	$selection=Tools::getValue('selection','');
	$selectionArr=preg_split('/,/',$selection);
	$state=Tools::getValue('state','');
	
	if ($state=='true')
	{
		foreach($images as $id_image) {
			foreach($selectionArr AS $id_combi)
			{
				$sql = '
			SELECT COUNT(*) as nb FROM '._DB_PREFIX_.'product_attribute_image
			WHERE id_image = '.intval($id_image).' AND id_product_attribute='.intval($id_combi).' GROUP BY id_image';
				$res=Db::getInstance()->getRow($sql);
				if (empty($res['nb']))
				{
					$sql = '
				INSERT INTO '._DB_PREFIX_.'product_attribute_image (id_product_attribute,id_image)
				VALUES ('.intval($id_combi).','.intval($id_image).')';
					Db::getInstance()->Execute($sql);
				}
			}
		}
	}elseif ($state=='false'){
			if ($selection!='')
			{
				foreach($images as $id_image) {
					$sql = '
					DELETE FROM ' . _DB_PREFIX_ . 'product_attribute_image
					WHERE id_image = ' . intval($id_image) . ' AND id_product_attribute IN (' . pSQL($selection) . ')';
					Db::getInstance()->Execute($sql);
				}
			}
	}
