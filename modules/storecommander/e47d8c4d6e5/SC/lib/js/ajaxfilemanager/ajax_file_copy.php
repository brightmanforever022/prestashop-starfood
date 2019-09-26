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
	 * copy file
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$error = "";
	$info = '';
	if (CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_COPY)
	{
		$error = SYS_DISABLED;
	}
	elseif (!isset($_POST['selectedDoc']) || !is_array($_POST['selectedDoc']) || sizeof($_POST['selectedDoc']) < 1)
	{
		$error = ERR_NOT_DOC_SELECTED_FOR_COPY;
	}
	elseif (empty($_POST['currentFolderPath']) || !isUnderRoot($_POST['currentFolderPath']))
	{
		$error = ERR_FOLDER_PATH_NOT_ALLOWED;
	}else 
	{		
		require_once(CLASS_SESSION_ACTION);
		$sessionAction = new SessionAction();
		$sessionAction->setAction($_POST['action_value']);
		$sessionAction->setFolder($_POST['currentFolderPath']);
		$sessionAction->set($_POST['selectedDoc']);
		$info = ',num:' . sizeof($_POST['selectedDoc']);
	}
	echo "{error:'" . $error .  "'\n" . $info . "}";
?>