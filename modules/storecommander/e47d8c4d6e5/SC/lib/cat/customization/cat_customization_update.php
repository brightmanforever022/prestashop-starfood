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

$action = Tools::getValue('action','0');
$value = Tools::getValue('value','0');
$customization_list = Tools::getValue('customization_list','0');
$name = Tools::getValue('name','0');
$lang = Tools::getValue('lang','0');
$id_products = explode(',',Tools::getValue('id_product','0'));
$fields_lang = array();
$idlangByISO = array();
$todo = array();
$todo_lang = array();

if(Tools::getValue('act','')=='cat_customization_update')
{
	if ($action=='delete')
	{
		$sql = "DELETE FROM `"._DB_PREFIX_."customization_field` WHERE `id_customization_field` IN (".psql($customization_list).")";
		Db::getInstance()->Execute($sql);
		$sql = "DELETE FROM `"._DB_PREFIX_."customization_field_lang` WHERE `id_customization_field` IN (".psql($customization_list).")";
		Db::getInstance()->Execute($sql);
		$sql = "UPDATE `"._DB_PREFIX_."product` p SET customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																									text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																									uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
		Db::getInstance()->Execute($sql);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$sql = "UPDATE `"._DB_PREFIX_."product_shop` p SET  customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																													text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																													uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
			Db::getInstance()->Execute($sql);
		}
	}

	if ($action=='insert')
	{
		foreach($id_products AS $id_product)
		{
			$sql = "INSERT INTO `"._DB_PREFIX_."customization_field` (id_product,type,required) VALUES (".(int)$id_product.",0,0)";
			Db::getInstance()->Execute($sql);
			$id_customization_field=Db::getInstance()->Insert_ID();
			foreach($languages AS $lang)
			{
				if(version_compare(_PS_VERSION_, '1.6.0.12', '>='))
				{
					$shops = SCI::getSelectedShopActionList();
					foreach($shops as $shop)
					{
						$sqlinsert = "INSERT INTO "._DB_PREFIX_."customization_field_lang (`id_customization_field`,`id_lang`,name,`id_shop`) VALUES ('".intval($id_customization_field)."','".(int)$lang['id_lang']."','','".intval($shop)."')";
						Db::getInstance()->Execute($sqlinsert);
					}
				}
				else
				{
					$sql = "INSERT INTO `"._DB_PREFIX_."customization_field_lang` (id_customization_field,id_lang,name) VALUES (".(int)$id_customization_field.",".(int)$lang['id_lang'].",'')";
					Db::getInstance()->Execute($sql);
				}
			}
		}
		$sql = "UPDATE `"._DB_PREFIX_."product` p SET customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																									text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																									uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
		Db::getInstance()->Execute($sql);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$sql = "UPDATE `"._DB_PREFIX_."product_shop` p SET customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																										text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																										uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
			Db::getInstance()->Execute($sql);
		}
	}

	if ($action=='updateType')
	{
		addToHistory('customization_field','modification','type',intval($customization_list),0,_DB_PREFIX_."customization_field",psql($value));
		$sql = "UPDATE "._DB_PREFIX_."customization_field SET `type`='".psql($value)."' WHERE id_customization_field=".intval($customization_list);
		Db::getInstance()->Execute($sql);
		$sql = "UPDATE `"._DB_PREFIX_."product` p SET customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																									text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																									uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
		Db::getInstance()->Execute($sql);
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$sql = "UPDATE `"._DB_PREFIX_."product_shop` p SET  customizable=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product), 
																													text_fields=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1), 
																										 			uploadable_files=(SELECT count(id_customization_field) FROM `"._DB_PREFIX_."customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)";
			Db::getInstance()->Execute($sql);
		}

	}
	
	if ($action=='updateRequired')
	{
		addToHistory('customization_field','modification','required',intval($customization_list),0,_DB_PREFIX_."customization_field",psql($value));
		$sql = "UPDATE "._DB_PREFIX_."customization_field SET `required`='".psql($value)."' WHERE id_customization_field=".intval($customization_list);
		Db::getInstance()->Execute($sql);
	}
	
	if ($action=='updateName')
	{
		foreach($languages AS $lang)
		{
			$fields_lang[]='name¤'.$lang['iso_code'];
			$idlangByISO[$lang['iso_code']]=$lang['id_lang'];
		}
		foreach($fields_lang AS $field)
		{		
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$tmp=explode('¤',$field);
				$fname=$tmp[0];
				$flang=$tmp[1];
				$todo_lang[]=array(
							'`'.$fname."`='".psql(html_entity_decode( Tools::getValue($field)))."'",
							$idlangByISO[$flang],
							psql(html_entity_decode( Tools::getValue($field))),
							$fname
							);
				addToHistory('customization_field_lang','modification',$fname,intval($customization_list),$idlangByISO[$flang],_DB_PREFIX_."customization_field_lang",psql(Tools::getValue($field)));
			}
		}
		if (count($todo_lang))
		{
			foreach($todo_lang AS $tlang)
			{
				if(version_compare(_PS_VERSION_, '1.6.0.12', '>='))
				{
					$shops = SCI::getSelectedShopActionList();
					foreach($shops as $shop)
					{
						$sqltest = "SELECT * FROM "._DB_PREFIX_."customization_field_lang WHERE id_customization_field=".intval($customization_list)." AND id_lang=".intval($tlang[1])." AND id_shop = '".intval($shop)."'";
						$test = Db::getInstance()->ExecuteS($sqltest);
						if (count($test)==0){
							$sqlinsert = "INSERT INTO "._DB_PREFIX_."customization_field_lang (`id_customization_field`,`id_lang`,`".psql($tlang[3])."`,`id_shop`) VALUES ('".intval($customization_list)."','".intval($tlang[1])."','".psql($tlang[2])."','".intval($shop)."')";
							Db::getInstance()->Execute($sqlinsert);
						}else{
							$sql2 = "UPDATE "._DB_PREFIX_."customization_field_lang SET ".$tlang[0]." WHERE id_customization_field=".intval($customization_list)." AND id_lang=".intval($tlang[1])." AND id_shop = '".intval($shop)."'";
							Db::getInstance()->Execute($sql2);
						}
					}
				}
				else
				{
					$sqltest = "SELECT * FROM "._DB_PREFIX_."customization_field_lang WHERE id_customization_field=".intval($customization_list)." AND id_lang=".intval($tlang[1]);
					$test = Db::getInstance()->ExecuteS($sqltest);
					if (count($test)==0){
						$sqlinsert = "INSERT INTO "._DB_PREFIX_."customization_field_lang (`id_customization_field`,`id_lang`,`".psql($tlang[3])."`) VALUES ('".intval($customization_list)."','".intval($tlang[1])."','".psql($tlang[2])."')";
						Db::getInstance()->Execute($sqlinsert);
					}else{
						$sql2 = "UPDATE "._DB_PREFIX_."customization_field_lang SET ".$tlang[0]." WHERE id_customization_field=".intval($customization_list)." AND id_lang=".intval($tlang[1]);
						Db::getInstance()->Execute($sql2);
					}
				}
				
			}
		}
	}

	// PM Cache
	if(!empty($id_products))
		ExtensionPMCM::clearFromIdsProduct($id_products);
}