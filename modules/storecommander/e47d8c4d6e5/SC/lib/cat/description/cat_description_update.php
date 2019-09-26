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

if(Tools::getValue('act','')=='cat_description_update')
{
	$id_product=Tools::getValue('id_product','0');
	$id_lang=Tools::getValue('id_lang','0');
	$description_short=Tools::getValue('description_short','');
	if (Tools::strlen(strip_tags($description_short))>_s('CAT_SHORT_DESC_SIZE'))
	{
		die('ERR|description_short_size');
	}
	if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
	{
		if (!Validate::isCleanHtml($description_short, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
				die('ERR|description_short_with_iframe');
			else
				die('ERR|description_short_invalid');
		}
	}
	elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if (!Validate::isString($description_short))
			die('ERR|description_short_invalid');
	}
	
	$description=Tools::getValue('description','');
	if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
	{
		if (!Validate::isCleanHtml($description, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
				die('ERR|description_with_iframe');
			else
				die('ERR|description_invalid');
		}
	}
	elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if (!Validate::isString($description))
			die('ERR|description_invalid');
	}
	
	$sql = "SELECT description_short, description FROM "._DB_PREFIX_."product_lang WHERE id_product='".intval($id_product)."' AND id_lang='".intval($id_lang)."'";
	$oldvalues=Db::getInstance()->getRow($sql);
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$sql = "UPDATE "._DB_PREFIX_."product_shop SET date_upd=NOW(),indexed=0 WHERE id_product=".intval($id_product)." AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
	}elseif (version_compare(_PS_VERSION_, '1.2.0.1', '>='))
	{
		$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW(),indexed=0 WHERE id_product='".intval($id_product)."'";
	}else{
		$sql = "UPDATE "._DB_PREFIX_."product SET date_upd=NOW() WHERE id_product='".intval($id_product)."'";
	}
	Db::getInstance()->Execute($sql);
	$sql = "UPDATE "._DB_PREFIX_."product_lang SET description_short='".psql($description_short,true)."',description='".psql($description,true)."' WHERE id_product='".intval($id_product)."' AND id_lang='".intval($id_lang)."'";
	if (SCMS)
		$sql.=" AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
	
	Db::getInstance()->Execute($sql);
	addToHistory('cat_prop','modification','description_short',$id_product,intval($id_lang),_DB_PREFIX_."product_lang",$description_short,$oldvalues['description_short']);
	addToHistory('cat_prop','modification','description',$id_product,intval($id_lang),_DB_PREFIX_."product_lang",$description,$oldvalues['description']);

	// PM Cache
	if(!empty($id_product))
		ExtensionPMCM::clearFromIdsProduct($id_product);
}
die('OK');