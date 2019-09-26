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
//  Function:   runMaintenance
//  Purpose:		Do maintenance of tables and files
//  Arguments:	none
//
// ----------------------------------------------------------------------------
	function runMaintenance(){

		if(!file_exists(SC_CSV_IMPORT_DIR."category/"))
		{
			mkdir(SC_CSV_IMPORT_DIR."category/", 0775);
		}
		if(!file_exists(SC_CSV_IMPORT_DIR."category/".'images/'))
		{
			mkdir(SC_CSV_IMPORT_DIR."category/".'images/', 0775);
		}

		// purge history if more than APP_CHANGE_HISTORY_MAX items
		$sql="SELECT id_history FROM "._DB_PREFIX_."storecom_history ORDER BY id_history DESC LIMIT ".intval(_s('APP_CHANGE_HISTORY_MAX')).",1";
		$res=Db::getInstance()->ExecuteS($sql);
		if (count($res)!=0)
		{
			$sql="DELETE FROM "._DB_PREFIX_."storecom_history WHERE id_history <= ".intval($res[0]['id_history'])."";
			Db::getInstance()->Execute($sql);
			$sql="OPTIMIZE TABLE "._DB_PREFIX_."storecom_history";
			Db::getInstance()->Execute($sql);
		}
		
		// créer le champs dans declinaison : id_sc_available_later
		// créer table sc_available_later (id, id_lang, available_later)
		// maintenance pour supprimer message dans sc_available_later non utilisé
		if(SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
		{
			if(SCI::getConfigurationValue("SC_DELIVERYDATE_SC_INSTALLED")=="0")
			{
				if(!isField("id_sc_available_later", "product_attribute"))
				{
					$sql="ALTER TABLE `"._DB_PREFIX_."product_attribute` ADD `id_sc_available_later` INT NOT NULL DEFAULT '0' AFTER `available_date` ";
					Db::getInstance()->Execute($sql);
				}
				
				$sql='CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'sc_available_later` (
				  `id_sc_available_later` int(10) unsigned NOT NULL auto_increment,
			  	  `id_lang` int(10) unsigned NOT NULL,
				  `available_later` char(255) default NULL,
				  PRIMARY KEY (`id_sc_available_later`)
				) ENGINE=MyISAM DEFAULT CHARSET=utf8;';
				Db::getInstance()->Execute($sql);
				
				if (SC_TOOLS && file_exists(SC_TOOLS_DIR.'grids_combinations_conf.xml'))
				{
					$grids_combinations_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinations_conf.xml');
					if(!empty($grids_combinations_conf->grids->grid->value))
					{
						$sourceGridFormat=(string) $grids_combinations_conf->grids->grid->value;

						if(!empty($sourceGridFormat))
							SC_Ext::addNewField("combinations", "available_later");
					}
				}

				SCI::updateConfigurationValue("SC_DELIVERYDATE_SC_INSTALLED", 1);
			}
			
			$sql="DELETE FROM `"._DB_PREFIX_."sc_available_later` WHERE id_sc_available_later NOT IN (SELECT DISTINCT(id_sc_available_later) FROM `"._DB_PREFIX_."product_attribute` WHERE id_sc_available_later!=0) ";
			Db::getInstance()->Execute($sql);
		}
	}
