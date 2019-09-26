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
	echo '<h2>'._l('Store Commander translations update',1).'</h2>';

	if (!file_exists('lang'))
		@mkdir('lang');
		
	// check rights
	$notWritableFiles=array();
	$writePermissions=octdec('0'.substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3));
	$writePermissionsOCT=substr(decoct(fileperms(realpath(SC_PS_PATH_DIR.'img/p'))),-3);
	dirCheckWritable(SC_DIR.'lang/',$notWritableFiles);
	if (count($notWritableFiles))
	{
		$dirStrSize=strlen(SC_PS_PATH_ADMIN_DIR);
		echo _l('Some files are not writable, please change the permission of these files:').' ('.$writePermissionsOCT.')'.'<br/><br/>';
		foreach($notWritableFiles AS $k => $file){
			echo substr($file,$dirStrSize).'<br/>';
			if ($k > 20)
			{
				echo '...';
				exit;
			}
		}
		exit;
	}

	include(SC_DIR.'lib/php/pclzip.lib.php');

	$tmp_folder=SC_DIR.'sc_update_tmp';

	if (!is_dir($tmp_folder)) 
	{
		$old = umask(0);
		mkdir($tmp_folder,$writePermissions);
		umask($old);
	}

	echo _l('Updating...').'<br/><br/>';
	echo _l('Downloading pack').' SCLanguages.zip...<br/>';
	$url = 'http://www.storecommander.com/files/SCLanguages.zip';
	$data=sc_file_get_contents($url);
	if (Tools::getValue('DEBUG',0)==1)
		echo $url.'<br/>';
	echo ' ('.(strlen($data)/1000).'K)<br/>';
	file_put_contents($tmp_folder.'/SCLanguages.zip',$data);
	if (filesize($tmp_folder.'/SCLanguages.zip') == 0)
	{
		echo _l('Error with archive (filesize = 0 Ko)').'<br/>';
	}else{
		echo _l('Opening zip archive...').'<br/>';
		$archive = new PclZip($tmp_folder.'/SCLanguages.zip');
		echo _l('Extracting zip archive...').'<br/>';
		$old = umask(0);
		$archive->extract(PCLZIP_OPT_PATH,SC_DIR.'lang/',PCLZIP_OPT_SET_CHMOD,$writePermissions);
		umask($old);
		echo _l('End of extraction').'<br/><br/>';
	}

	if (!isset($_GET['updatekeepzipfile'])) // for debug purpose
		dirRemove($tmp_folder);

	echo _l('Update finished!').' '.'<a href="index.php" target="_top">'._l('Click here to refresh the application').'</a>';
