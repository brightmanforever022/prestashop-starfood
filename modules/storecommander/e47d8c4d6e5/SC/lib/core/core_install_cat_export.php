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

if (!file_exists(SC_CSV_EXPORT_DIR))
{
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	@mkdir(SC_CSV_EXPORT_DIR,$writePermissions);
	if (!file_exists(SC_CSV_EXPORT_DIR))
		die(SC_CSV_EXPORT_DIR._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
}
if (!file_exists(SC_TOOLS_DIR))
{
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	@mkdir(SC_TOOLS_DIR,$writePermissions);
	if (!file_exists(SC_TOOLS_DIR))
		die(SC_TOOLS_DIR._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
}
if (!file_exists(SC_TOOLS_DIR.'cat_export/'))
{
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	@mkdir(SC_TOOLS_DIR.'cat_export/',$writePermissions);
	if (!file_exists(SC_TOOLS_DIR.'cat_export/'))
		die(SC_TOOLS_DIR.'cat_export/'._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
}
if (!file_exists(SC_TOOLS_DIR.'cat_categories_sel/'))
{
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	@mkdir(SC_TOOLS_DIR.'cat_categories_sel/',$writePermissions);
	if (!file_exists(SC_TOOLS_DIR.'cat_categories_sel/'))
		die(SC_TOOLS_DIR.'cat_categories_sel/'._l(':').' '._l('This folder cannot be created by Store Commander, you need to create it by FTP with writing permission.'));
}

$files = array_diff( scandir( SC_DIR.'data/cat_export/' ), array_merge( Array( ".", "..", "index.php", ".htaccess")) );

foreach($files AS $file)
	if (!file_exists(SC_TOOLS_DIR.'cat_export/'.$file))
		copy(SC_DIR.'data/cat_export/'.$file,SC_TOOLS_DIR.'cat_export/'.$file);

if (file_exists(SC_DIR.'data/cat_export/'.$file))
	die(_l('The CSV files have been installed. You can use them in the Export CSV tool.'));
die(_l('The CSV files have not been installed. Check write permissions on ').SC_TOOLS_DIR);
