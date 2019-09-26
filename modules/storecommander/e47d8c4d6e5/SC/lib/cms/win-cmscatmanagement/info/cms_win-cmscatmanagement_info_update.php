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
	$id_lang=(int)Tools::getValue('id_lang');
	$gr_id=(Tools::getValue('gr_id',0));
	$action = Tools::getValue('action',"");
	$field = Tools::getValue('field',"");
	$value = Tools::getValue('value',"");

	if(!empty($action) && $action=="update")
	{
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) {
			list($id_cms_category, $id_lang, $id_shop) = explode("_", $gr_id);
		} else {
			list($id_cms_category, $id_lang) = explode("_", $gr_id);
		}

		$todo = array();
		if($field=="name")
		{
			if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
			{
				$sql = "SELECT name FROM "._DB_PREFIX_."category_lang WHERE id_category='".(int)$id_category."' AND id_lang='".(int)$id_lang."'";
				$actual = Db::getInstance()->ExecuteS($sql);
				if(!empty($actual[0]["name"]) && preg_match('/^[0-9]+\./', $actual[0]["name"])>0)
				{
					$exp = explode(".",$actual[0]["name"]);
					$value = $exp[0].".".$value;
				}
			}
			if (_s('CMS_SEO_CAT_NAME_TO_URL'))
			{
				$todo[]="`link_rewrite`='".pSQL(link_rewrite($value))."'";
			}
		}
		$todo[]=$field."='".pSQL( $value )."'";

		if (count($todo)) {
			$sql = "UPDATE " . _DB_PREFIX_ . "cms_category_lang SET " . join(' , ',
					$todo) . " WHERE id_cms_category=" . (int)$id_cms_category . " AND id_lang=" . (int)$id_lang . " ";
			if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) {
				$sql .= " AND id_shop=" . (int)$id_shop;
			}
			Db::getInstance()->Execute($sql);
		}
	}
