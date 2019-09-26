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
	/**
	 * delete selected files
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	if (!empty($_GET['path']) && file_exists($_GET['path']) && is_file($_GET['path']) && isUnderRoot($_GET['path']))
	{
			
			$path = $_GET['path'];
			//check if the file size
			$fileSize = @filesize($path);
			
			if ($fileSize > getMemoryLimit())
			{//larger then the php memory limit, redirect to the file
				
				header('Location: ' . $path);
				exit;				 
			}else 
			{//open it up and send out with php 
				downloadFile($path);	
				 			
			}
	}else 
	{
		die(ERR_DOWNLOAD_FILE_NOT_FOUND);
	}
