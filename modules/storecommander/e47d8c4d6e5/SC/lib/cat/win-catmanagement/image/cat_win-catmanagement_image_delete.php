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
$ids=(Tools::getValue('ids', 0));
$idlist = explode(",",$ids);

require_once(_PS_ROOT_DIR_.'/images.inc.php');

foreach ($idlist AS $id_category)
{
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		$category = new Category($id_category);
		$category->deleteImage(true);
	}
	else
		deleteImage($id_category);
}