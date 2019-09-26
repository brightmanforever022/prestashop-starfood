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

	$idlist = Tools::getValue('idlist','0');
	$id_lang = intval(Tools::getValue('id_lang'));
	$cntProducts = count(explode(',',$idlist));
	$used = array();

	$sql = "SELECT DISTINCT a.id_product_2 
			FROM "._DB_PREFIX_."accessory a
			WHERE a.id_product_1 IN (".psql($idlist).")";
	$res = Db::getInstance()->ExecuteS($sql);

	foreach($res as $row){
		$used[]=$row['id_product_2'];
	}
	echo join(',',$used);