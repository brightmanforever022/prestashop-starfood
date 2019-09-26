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

if(Tools::getValue('act','')=='man_description_update')
{
	$id_manufacturer=Tools::getValue('id_manufacturer','0');
	$id_lang=Tools::getValue('id_lang','0');

	#### SHORT DESCRIPTION
	$short_description=Tools::getValue('short_description','');
	if (Tools::strlen(strip_tags($short_description))>_s('MAN_SHORT_DESC_SIZE'))
	{
		die('ERR|short_description_size');
	}
	if(version_compare(_PS_VERSION_, '1.6.0.0', '>='))
	{
		if (!Validate::isCleanHtml($short_description, (int)Configuration::get('PS_ALLOW_HTML_IFRAME')))
		{
			if (!Configuration::get('PS_ALLOW_HTML_IFRAME'))
				die('ERR|short_description_with_iframe');
			else
				die('ERR|short_description_invalid');
		}
	}
	elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if (!Validate::isString($short_description))
			die('ERR|short_description_invalid');
	}

	#### DESCRIPTION
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

	$sql = "SELECT short_description, description FROM "._DB_PREFIX_."manufacturer_lang WHERE id_manufacturer='".intval($id_manufacturer)."' AND id_lang='".intval($id_lang)."'";
	$oldvalues=Db::getInstance()->getRow($sql);
	$sql = "UPDATE "._DB_PREFIX_."manufacturer SET date_upd=NOW() WHERE id_manufacturer='".intval($id_manufacturer)."'";
	Db::getInstance()->Execute($sql);
	$sql = "UPDATE "._DB_PREFIX_."manufacturer_lang SET short_description='".psql($short_description,true)."',description='".psql($description,true)."' WHERE id_manufacturer='".intval($id_manufacturer)."' AND id_lang='".intval($id_lang)."'";
	Db::getInstance()->Execute($sql);
	addToHistory('man_prop','modification','short_description',$id_manufacturer,intval($id_lang),_DB_PREFIX_."manufacturer_lang",$short_description,$oldvalues['short_description']);
	addToHistory('man_prop','modification','description',$id_manufacturer,intval($id_lang),_DB_PREFIX_."manufacturer_lang",$description,$oldvalues['description']);

	// PM Cache
	if(!empty($id_manufacturer))
		ExtensionPMCM::clearFromIdsProduct($id_manufacturer);
}
die('OK');
