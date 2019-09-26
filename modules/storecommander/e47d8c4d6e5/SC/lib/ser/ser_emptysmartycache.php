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

	if (!defined('SC_DIR')) exit;

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		dirEmpty(_PS_CACHE_DIR_.'smarty/cache/',_PS_CACHE_DIR_.'smarty/cache/',array('index.php'));
		dirEmpty(_PS_CACHE_DIR_.'smarty/compile/',_PS_CACHE_DIR_.'smarty/compile/',array('index.php'));
	}else{
		dirEmpty(_PS_SMARTY_DIR_.'cache',_PS_SMARTY_DIR_.'cache',array('index.php'));
		dirEmpty(_PS_SMARTY_DIR_.'compile',_PS_SMARTY_DIR_.'compile',array('index.php'));
	}

	echo 'Ok';
