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

	$id_lang=intval(Tools::getValue('id_lang'));
	$id_feature_value=intval(Tools::getValue('gr_id',0));
	$id_feature=intval(Tools::getValue('id_feature'));
	$action = Tools::getValue('action');
	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){
		$newFV=new FeatureValue();
		$newFV->id_feature=$id_feature;
		if (version_compare(_PS_VERSION_, '1.5.0', '>='))
			foreach($languages AS $lang)
				$newFV->value[$lang['id_lang']]='new';
		$newFV->save();
		$newId = $newFV->id;
		$action = "insert";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="duplicated"){
		
		$features=explode(',',Tools::getValue('features',''));
		$updated_features = array();
		foreach($features AS $id_feature)
		{
			$sql="INSERT INTO "._DB_PREFIX_."feature () VALUES ()";
			Db::getInstance()->Execute($sql);
			$newFeatureID=Db::getInstance()->Insert_ID();
			$updated_features[$newFeatureID]=$newFeatureID;
			if (version_compare(_PS_VERSION_, '1.5.0', '>='))
			{
				$shops = SCI::getSelectedShopActionList();
				foreach($shops as $shop)
					$sql="INSERT INTO "._DB_PREFIX_."feature_shop (id_feature,id_shop) VALUES (".intval($newFeatureID).",".(int)$shop.")";
				Db::getInstance()->Execute($sql);
			}
			$sql="
			SELECT * FROM "._DB_PREFIX_."feature f
			LEFT JOIN "._DB_PREFIX_."feature_lang fl ON (fl.id_feature=f.id_feature)
			WHERE f.id_feature=".intval($id_feature);
			$featurelang=Db::getInstance()->ExecuteS($sql);
			foreach($featurelang AS $f)
			{
				$sql="INSERT INTO "._DB_PREFIX_."feature_lang (id_feature,id_lang,name) VALUES (".intval($newFeatureID).",".intval($f['id_lang']).",'".psql($f['name'])."')";
				Db::getInstance()->Execute($sql);
			}
			$sql="
			SELECT * FROM "._DB_PREFIX_."feature_value fv
			LEFT JOIN "._DB_PREFIX_."feature_value_lang fvl ON (fvl.id_feature_value=fv.id_feature_value)
			WHERE fv.id_feature=".intval($id_feature)." AND fv.custom=0";
			$featurevalues=Db::getInstance()->ExecuteS($sql);
			$inserted=array();
			foreach($featurevalues AS $fv)
			{
				if(!in_array($fv['id_feature_value'],$inserted))
				{
					$sql="INSERT INTO "._DB_PREFIX_."feature_value (id_feature) VALUES (".$newFeatureID.")";
					Db::getInstance()->Execute($sql);
					$newFVID=Db::getInstance()->Insert_ID();
					$inserted[]=$fv['id_feature_value'];
				}
				$sql="INSERT INTO "._DB_PREFIX_."feature_value_lang (id_feature_value,id_lang,value) VALUES (".$newFVID.",'".$fv['id_lang']."','".psql($fv['value'])."')";
				Db::getInstance()->Execute($sql);
			}
		}

		// PM Cache
		if(!empty($updated_features))
			ExtensionPMCM::clearFromIdsFeature($updated_features);

		$newId = 0;
		$_POST["gr_id"] = 0;
		$action = "duplicate";
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array();
		$fields_lang=array();
		$idlangByISO=array();
		$todo=array();
		$todo_lang=array();
		foreach($languages AS $lang)
		{
			$fields_lang[]='value¤'.$lang['iso_code'];
			$idlangByISO[$lang['iso_code']]=$lang['id_lang'];
		}
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$todo[]='`'.$field."`='".psql(html_entity_decode( Tools::getValue($field)))."'";
				addToHistory('feature_value','modification',$field,intval($id_feature_value),$id_lang,_DB_PREFIX_."feature_value",psql(Tools::getValue($field)));
			}
		}
		foreach($fields_lang AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$tmp=explode('¤',$field);
				$fname=$tmp[0];
				$flang=$tmp[1];
				$todo_lang[]=array('`'.$fname."`='".psql(trim(html_entity_decode( Tools::getValue($field))), true)."'",$idlangByISO[$flang],psql(html_entity_decode( Tools::getValue($field))));
				addToHistory('feature_value','modification',$fname,intval($id_feature_value),$idlangByISO[$flang],_DB_PREFIX_."feature_value_lang",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."feature_value SET ".join(' , ',$todo)." WHERE id_feature_value=".intval($id_feature_value);
			Db::getInstance()->Execute($sql);
		}
		if (count($todo_lang))
		{
			foreach($todo_lang AS $tlang)
			{
				$sqltest="SELECT * FROM "._DB_PREFIX_."feature_value_lang WHERE id_feature_value=".intval($id_feature_value)." AND id_lang=".intval($tlang[1]);
				$test=Db::getInstance()->ExecuteS($sqltest);
				if (count($test)==0){
					$sqlinsert="INSERT INTO "._DB_PREFIX_."feature_value_lang VALUES ('".intval($id_feature_value)."','".intval($tlang[1])."','".$tlang[2]."')";
					Db::getInstance()->Execute($sqlinsert);
				}else{
					$sql2 = "UPDATE "._DB_PREFIX_."feature_value_lang SET ".$tlang[0]." WHERE id_feature_value=".intval($id_feature_value)." AND id_lang=".intval($tlang[1]);
					Db::getInstance()->Execute($sql2);
				}
			}
		}

		// PM Cache
		if(!empty($id_feature_value))
			ExtensionPMCM::clearFromIdsFeatureValue($id_feature_value);

		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){
		$FV=new FeatureValue($id_feature_value,$id_lang);
		$FV->delete();
		if (version_compare(_PS_VERSION_, '1.2.0.8', '<='))
		{
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE `id_feature_value` = '.intval($id_feature_value));
		}

		// PM Cache
		if(!empty($id_feature_value))
			ExtensionPMCM::clearFromIdsFeatureValue($id_feature_value);

		$newId = $_POST["gr_id"];
		$action = "delete";
	}elseif( !empty($action) && trim($action)=="merge"){
		$featlist=explode(',',Tools::getValue('featlist',0));
		sort($featlist);
		$id_feature=array_shift($featlist);
		foreach($featlist AS $id)
			{
				$sql = "UPDATE "._DB_PREFIX_."feature_product SET id_feature_value=".intval($id_feature)." WHERE id_feature_value=".intval($id);
				Db::getInstance()->Execute($sql);
				$sql = "DELETE FROM "._DB_PREFIX_."feature_value_lang WHERE id_feature_value=".intval($id);
				Db::getInstance()->Execute($sql);
				$sql = "DELETE FROM "._DB_PREFIX_."feature_value WHERE id_feature_value=".intval($id);
				Db::getInstance()->Execute($sql);
			}
 	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	echo '</data>';
?>