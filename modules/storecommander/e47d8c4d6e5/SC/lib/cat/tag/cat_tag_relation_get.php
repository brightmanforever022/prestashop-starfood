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

	$product_list=Tools::getValue('product_list');
	$id_lang=intval(Tools::getValue('id_lang'));
	$used=array();

	$sql="	SELECT DISTINCT id_tag
			FROM "._DB_PREFIX_."product_tag
			WHERE id_product IN (".$product_list.")";
	$res=Db::getInstance()->ExecuteS($sql);
	foreach($res as $row){
		$used[]=$row['id_tag'];
	}
	echo join(',',$used);