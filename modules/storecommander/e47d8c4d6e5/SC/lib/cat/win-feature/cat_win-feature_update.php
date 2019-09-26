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

	$id_feature=intval(Tools::getValue('gr_id'));
	$id_lang=intval(Tools::getValue('id_lang'));
	$action=Tools::getValue('action',0);

	if(!empty($action) && $action=="position"){
		$todo=array();
		$row=explode(';',Tools::getValue('positions'));
		foreach($row AS $v)
		{
			if ($v!='')
			{
				$pos=explode(',',$v);
				$todo[]="UPDATE "._DB_PREFIX_."feature SET position=".$pos[1]." WHERE id_feature=".intval($pos[0])."";
			}
		}
		foreach($todo AS $task)
		{
			Db::getInstance()->Execute($task);
		}
	}
	elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){
		$newFeature=new Feature();
		if (version_compare(_PS_VERSION_, '1.5.0', '>='))
			foreach($languages AS $lang)
				$newFeature->name[$lang['id_lang']]='new';
		$newFeature->save();
		SCI::addToShops('feature', array((int)$newFeature->id));
		$newId = $newFeature->id;
		$action = "insert";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array();
		$fields_lang=array();
		$idlangByISO=array();
		$todo=array();
		$todo_lang=array();
		foreach($languages AS $lang)
		{
			$fields_lang[]='name¤'.$lang['iso_code'];
			$idlangByISO[$lang['iso_code']]=$lang['id_lang'];
		}
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$todo[]='`'.$field."`='".psql(html_entity_decode( Tools::getValue($field)))."'";
				addToHistory('feature','modification',$field,intval($id_feature),$id_lang,_DB_PREFIX_."feature",psql(Tools::getValue($field)));
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
				addToHistory('feature','modification',$fname,intval($id_feature),$idlangByISO[$flang],_DB_PREFIX_."feature_lang",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."feature SET ".join(' , ',$todo)." WHERE id_feature=".intval($id_feature);
			Db::getInstance()->Execute($sql);
		}
		if (count($todo_lang))
		{
			foreach($todo_lang AS $tlang)
			{
				$sqltest="SELECT * FROM "._DB_PREFIX_."feature_lang WHERE id_feature=".intval($id_feature)." AND id_lang=".intval($tlang[1]);
				$test=Db::getInstance()->ExecuteS($sqltest);
				if (count($test)==0){
					$sqlinsert="INSERT INTO "._DB_PREFIX_."feature_lang VALUES ('".intval($id_feature)."','".intval($tlang[1])."','".$tlang[2]."','".$tlang[2]."')";
					Db::getInstance()->Execute($sqlinsert);
				}else{
					$sql2 = "UPDATE "._DB_PREFIX_."feature_lang SET ".$tlang[0]." WHERE id_feature=".intval($id_feature)." AND id_lang=".intval($tlang[1]);
					Db::getInstance()->Execute($sql2);
				}
			}
		}

		// PM Cache
		if(!empty($id_feature))
			ExtensionPMCM::clearFromIdsFeature($id_feature);

		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){
		$feature=new Feature($id_feature,$id_lang);
		$feature->delete();
		
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature` WHERE `id_feature` = '.intval($id_feature));
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature_lang` WHERE `id_feature` = '.intval($id_feature));
		Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature_shop` WHERE `id_feature` = '.intval($id_feature));
		
		if (version_compare(_PS_VERSION_, '1.2.0.8', '<='))
		{
			Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE `id_feature` = '.intval($id_feature));
		}

		// PM Cache
		if(!empty($id_feature))
			ExtensionPMCM::clearFromIdsFeature($id_feature);

		$newId = $_POST["gr_id"];
		$action = "delete";
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