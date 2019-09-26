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

$files = array_diff( scandir( SC_DIR.'data/cat_import/' ), array_merge( Array( ".", "..", "index.php", ".htaccess")) );
$iso='EN';
$flag=false;
if ($user_lang_iso=='fr') $iso='FR';
foreach($files AS $file)
{
	if (substr($file,0,2)==$iso)
	{
		@unlink(SC_CSV_IMPORT_DIR.$file);
		copy(SC_DIR.'data/cat_import/'.$file,SC_CSV_IMPORT_DIR.$file);
		if (file_exists(SC_CSV_IMPORT_DIR.$file))
			$flag=true;
	}
}
if ($flag)
	die(_l('The CSV files have been installed. You can use them in the Import CSV tool.'));
die(_l('The CSV files have not been installed. Check write permissions on ').SC_CSV_IMPORT_DIR);
