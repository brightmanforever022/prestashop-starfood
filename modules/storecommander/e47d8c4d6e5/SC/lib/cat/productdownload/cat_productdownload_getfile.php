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
if (Tools::getValue('file'))
{
	/* Admin can directly access to file */
	$filename = Tools::getValue('file');
	if (!file_exists(_PS_DOWNLOAD_DIR_.$filename))
		die('File not found');
}

$name = Tools::getValue('name',$filename);

/* Set headers for download */
header('Content-Transfer-Encoding: binary');
header('Content-Length: '.filesize(_PS_DOWNLOAD_DIR_.$filename));
header('Content-Disposition: attachment; filename="'.$name.'"');
readfile(_PS_DOWNLOAD_DIR_.$filename);
exit;