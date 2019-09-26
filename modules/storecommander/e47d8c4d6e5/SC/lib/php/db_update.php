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

// ----------------------------------------------------------------------------
//
//  Function:   isTable
//  Purpose:		Check if table exists in database
//  Arguments:	name of the table to check
//
// ----------------------------------------------------------------------------
	function isTable($name)
	{
		global $sc_tables;
		if (!is_array($sc_tables))
		{
			$sc_tables=array();
			$sql="SHOW TABLES";
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $val)
			{
				$tmp=array_values($val);
				$sc_tables[]=$tmp[0];
			}
		}
		if (sc_in_array(_DB_PREFIX_.$name,$sc_tables,"DBUpdate_sc_tables"))
			return true;
		return false;
	}
	
// ----------------------------------------------------------------------------
//
//  Function:   isField
//  Purpose:		Check if field exists in table
//  Arguments:	name of the field to check, name of the table without prefix
//
// ----------------------------------------------------------------------------
	function isField($name,$table)
	{
		global $sc_fields;
		if (!is_array($sc_fields))
			$sc_fields=array();
		if (!sc_array_key_exists($table,$sc_fields))
		{
			$fields=array();
			$sql="SHOW COLUMNS FROM "._DB_PREFIX_.psql($table);
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $val)
			{
				$fields[]=$val['Field'];
				
				if($table=="product_attribute" && $val['Field']=="date_upd")
				{
					if(empty($val['Default']))
					{
						Db::getInstance()->Execute("ALTER TABLE `"._DB_PREFIX_."product_attribute` CHANGE `date_upd` `date_upd` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00'");
						
						$nulls=Db::getInstance()->ExecuteS('SELECT id_product_attribute,date_upd FROM `'._DB_PREFIX_.'product_attribute` WHERE date_upd IS NULL');
						foreach($nulls as $null)
						{
							if(empty($null["date_upd"]) && $null["date_upd"]!="0000-00-00 00:00:00")
							{
								Db::getInstance()->Execute("UPDATE `"._DB_PREFIX_."product_attribute` SET `date_upd`='0000-00-00 00:00:00' WHERE id_product_attribute='".(int)$null["id_product_attribute"]."'");
							}
						}
					}
				}
			}
			$sc_fields[$table]=$fields;
		}
		if (sc_in_array($name,$sc_fields[$table],"DBUpdate_table".$table))
			return true;
		return false;
	}


// ----------------------------------------------------------------------------
//
//  Function:   checkDB
//  Purpose:		Check and update DB
//  Arguments:	none
//
// ----------------------------------------------------------------------------
	function checkDB(){
		global $sc_tables, $sc_alerts;
		// History
		if (!isTable('storecom_history'))
		{
			$sql="
				CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."storecom_history` (
					`id_history` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
					`id_employee` INT NOT NULL, 
					`section` VARCHAR(32) NOT NULL, 
					`action` VARCHAR(32) NOT NULL, 
					`object` VARCHAR(32) NOT NULL, 
					`object_id` INT NOT NULL, 
					`lang_id` INT NOT NULL, 
					`dbtable` VARCHAR(32) NOT NULL, 
					`date_add` DATETIME NOT NULL, 
					`oldvalue` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
					`newvalue` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL, 
					`flag` TINYINT(1) NOT NULL DEFAULT '0') ENGINE = MyISAM DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
				";
			Db::getInstance()->Execute($sql);
			$sc_tables=0;
			if (!isTable('storecom_history'))
				die(_l('Store Commander cannot create the table %s, please contact your hosting support and ask: Can you please confirm that the MySQL user has the necessary permission to execute these commands: SHOW and CREATE TABLE. Upon confirmation, you can restart Store Commander.',0,_DB_PREFIX_.'storecom_history'));
		}
		$field=Db::getInstance()->ExecuteS('SHOW COLUMNS FROM `'._DB_PREFIX_.'storecom_history` LIKE \'id_employee\'');
		if (!count($field))
			Db::getInstance()->Execute('ALTER TABLE `'._DB_PREFIX_.'storecom_history` ADD `id_employee` INT NOT NULL');
		// Queue log
		if (!isTable('sc_queue_log'))
		{
			$sql="
				CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sc_queue_log` (
				  `id_sc_queue_log` int(11) NOT NULL AUTO_INCREMENT,
				  `name` varchar(255) NOT NULL,
				  `row` varchar(255) NOT NULL,
				  `action` varchar(255) NOT NULL,
				  `params` text,
				  `callback` text,
				  `id_employee` int(11) NOT NULL,
				  `date_add` datetime NOT NULL,
				  PRIMARY KEY (`id_sc_queue_log`)
				) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
				";
			Db::getInstance()->Execute($sql);
			$sc_tables=0;
			if (!isTable('sc_queue_log'))
				die(_l('Store Commander cannot create the table %s, please contact your hosting support and ask: Can you please confirm that the MySQL user has the necessary permission to execute these commands: SHOW and CREATE TABLE. Upon confirmation, you can restart Store Commander.',0,_DB_PREFIX_.'sc_queue_log'));
		}
		// image filename field in ps_image
		if (_s('CAT_PROD_IMG_SAVE_FILENAME') && !isField('sc_path','image'))
		{
			$sql="ALTER TABLE `"._DB_PREFIX_."image` ADD `sc_path` VARCHAR( 150 ) NOT NULL DEFAULT ''";
			Db::getInstance()->Execute($sql);
		}
		// date_upd field in ps_product_attribute
		if (!isField('date_upd','product_attribute'))
		{
			$sql="ALTER TABLE `"._DB_PREFIX_."product_attribute` ADD `date_upd` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'";
			Db::getInstance()->Execute($sql);
		}
		if(!isTable('sc_export'))
		{ 
			$sql="
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sc_export` (
			  `id_sc_export` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(255) NOT NULL,
			  `last_export` datetime DEFAULT NULL,
			  `exporting` tinyint(1) NOT NULL DEFAULT '0',
			  `id_next` int(11) NOT NULL DEFAULT '0',
			  `id_combination_next` int(11) NOT NULL DEFAULT '0',
			  `total_lines` int(11) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id_sc_export`),
			  UNIQUE KEY `name` (`name`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			Db::getInstance()->Execute($sql);
		}
		if(!isTable('sc_export_product'))
		{
			$sql="
			CREATE TABLE IF NOT EXISTS `"._DB_PREFIX_."sc_export_product` (
			  `id_sc_export_product` int(11) NOT NULL AUTO_INCREMENT,
			  `id_sc_export` int(11) NOT NULL,
			  `id_product` int(11) NOT NULL,
			  `id_product_attribute` int(11) NOT NULL DEFAULT '0',
			  `handled` tinyint(1) NOT NULL DEFAULT '0',
			  `exported` tinyint(1) NOT NULL DEFAULT '0',
			  PRIMARY KEY (`id_sc_export_product`)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			Db::getInstance()->Execute($sql);
		}
		if (!isField('handled','sc_export_product'))
		{
			$sql="ALTER TABLE `"._DB_PREFIX_."sc_export_product` ADD `handled`  tinyint(1) NOT NULL DEFAULT '0'";
			Db::getInstance()->Execute($sql);
		}
		if (!isField('exported','sc_export_product'))
		{
			$sql="ALTER TABLE `"._DB_PREFIX_."sc_export_product` ADD `exported`  tinyint(1) NOT NULL DEFAULT '0'";
			Db::getInstance()->Execute($sql);
		}
		
		// Corrige un probl�me d'index unique dans Prestashop Table ps_specific_price
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$sql="SHOW INDEX FROM "._DB_PREFIX_."specific_price WHERE column_name = 'id_product_attribute' AND non_unique = 0";
			$res=Db::getInstance()->ExecuteS($sql);
			if (count($res) > 0)
			{
				$key_name = $res[0]['Key_name'];
				$sql="ALTER TABLE `"._DB_PREFIX_."specific_price` DROP INDEX  `".pSQL($key_name)."` , ADD INDEX `".pSQL($key_name)."` (  `id_product` ,  `id_shop` ,  `id_shop_group` ,  `id_currency` ,  `id_country` ,  `id_group` ,  `id_customer` ,  `id_product_attribute` , `from_quantity` ,  `from` ,  `to` )";
				Db::getInstance()->Execute($sql);
			}
		}

		// champs cache déclinaison pour commande
		if (!isField('sc_attr_infos_v1','order_detail'))
		{
			$sql="ALTER TABLE `"._DB_PREFIX_."order_detail` ADD `sc_attr_infos_v1` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL";
			Db::getInstance()->Execute($sql);
		}


		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if (!isField('shops','storecom_history')) {
				$sql = "ALTER TABLE `" . _DB_PREFIX_ . "storecom_history` ADD `shops` varchar(255) NOT NULL DEFAULT '0'";
				Db::getInstance()->Execute($sql);
			}
		}

		// Check si doublon		
		$field=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'configuration` WHERE name = \'SC_VERSIONS\'');
		if (count($field) > 1)
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE name = \'SC_VERSIONS\'');
		
		// Check disable_functions
		$disabled_functions = ini_get('disable_functions');
		if ($disabled_functions!='')
		{
			$arr = explode(',', $disabled_functions);
			$err = array();

			if(in_array("parse_ini_file", $arr) && !in_array("parse_ini_file",$err))
				$err[] = "parse_ini_file";

			if(in_array(" curl_exec", $arr) && !in_array("curl_exec",$err))
				$err[] = "curl_exec";
			if(in_array("curl_exec", $arr) && !in_array("curl_exec",$err))
				$err[] = "curl_exec";

			if(!empty($err) && count($err)>0)
				$sc_alerts[] = _l("These functions are necessary for Store Commander but disabled in PHP configuration:")." ".implode(", ", $err);
		}

        if (isTable('sc_ff_project') && !isField('nb_product','sc_ff_project'))
        {
            $sql="ALTER TABLE `"._DB_PREFIX_."sc_ff_project` ADD `nb_product` INT NOT NULL DEFAULT '0' AFTER `params`;";
            Db::getInstance()->Execute($sql);

            $ff_projects=Db::getInstance()->ExecuteS('SELECT * FROM `'._DB_PREFIX_.'sc_ff_project` WHERE status NOT IN ("created","configured","pay")');
            foreach ($ff_projects as $ff_project)
            {
                $cat = new Category((int)$ff_project["id_category"]);
                $nb = $cat->getProducts(null,1,1,null,null,true,false);
                if(!empty($nb))
                {
                    Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'sc_ff_project` SET nb_product = "'.(int)$nb.'" WHERE id_project = "'.(int)$ff_project["id_project"].'"');
                }
            }
        }
	}
