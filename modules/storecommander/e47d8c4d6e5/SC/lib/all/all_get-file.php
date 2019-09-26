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
	$path = Tools::getValue('path');
	if (!file_exists($path.$filename))
		die('File not found');
}

/* Detect mime content type */
$mime_type = false;
if (function_exists('finfo_open'))
{
	$finfo = @finfo_open(FILEINFO_MIME);
	$mime_type = @finfo_file($finfo, $path.$filename);
	@finfo_close($finfo);
}
elseif (function_exists('mime_content_type'))
	$mime_type = @mime_content_type($path.$filename);
elseif (function_exists('exec'))
	$mime_type = trim(@exec('file -bi '.escapeshellarg($path.$filename)));

/* Set headers for download */
header('Content-Transfer-Encoding: binary');
if ($mime_type)
	header('Content-Type: '.$mime_type);
header('Content-Length: '.filesize($path.$filename));
header('Content-Disposition: attachment; filename="'.$filename.'"');
readfile($path.$filename);
exit;