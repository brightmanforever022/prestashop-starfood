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

	if (!is_numeric($user_id) || $user_id == 0)
		die('id_user not found!');
	$Ini = new Ini($user_id);
	$Ini->lire();

	// ecriture
	if(isset($_GET["frequence"]))
	{
		$date = new DateTime();
		$Ini->content['mode'] = (int)$_GET["frequence"];
		$Ini->ajouter_array($Ini->content);
		$Ini->ecrire(true);
	}

	$alreadyRead = explode(',',$Ini->content['tip']);
	$tipCount = count($alreadyRead);

	// Appel du template
	require_once (PATH_TEMPLATE."header.tpl");
	require_once (PATH_TEMPLATE."preference.tpl");
	require_once (PATH_TEMPLATE."footer.tpl");
