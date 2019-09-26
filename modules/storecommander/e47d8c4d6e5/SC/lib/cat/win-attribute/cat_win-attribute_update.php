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
	$id_attribute=intval(Tools::getValue('gr_id',0));
	$id_attribute_group=intval(Tools::getValue('id_attribute_group'));
	$iscolor=intval(Tools::getValue('iscolor'));
	$action=Tools::getValue('action',0);

	if(!empty($action) && $action=="position"){
		$todo=array();
		$row=explode(';',Tools::getValue('positions'));
		foreach($row AS $v)
		{
			if ($v!='')
			{
				$pos=explode(',',$v);
				$todo[]="UPDATE "._DB_PREFIX_."attribute SET position=".$pos[1]." WHERE id_attribute=".intval($pos[0])."";
			}
		}
		foreach($todo AS $task)
		{
			Db::getInstance()->Execute($task);
		}
	}
	elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){
		$newattr=new Attribute();
		$newattr->id_attribute_group=$id_attribute_group;
		$newattr->color='#000000';
		if (version_compare(_PS_VERSION_, '1.5.0', '>='))
			foreach($languages AS $lang)
				$newattr->name[$lang['id_lang']]='new';
		$newattr->save();
		SCI::addToShops('attribute', array((int)$newattr->id));
		$newId = $newattr->id;
		$action = "insert";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="duplicated"){
		
		$groups=explode(',',Tools::getValue('groups',''));
		foreach($groups AS $id_group)
		{
			$sql="INSERT INTO "._DB_PREFIX_."attribute_group (is_color_group) VALUES (0)";
			Db::getInstance()->Execute($sql);
			$newGroupID=Db::getInstance()->Insert_ID();
			$sql="
			SELECT * FROM "._DB_PREFIX_."attribute_group ag
			LEFT JOIN "._DB_PREFIX_."attribute_group_lang agl ON (agl.id_attribute_group=ag.id_attribute_group)
			WHERE ag.id_attribute_group=".intval($id_group);
			$grouplang=Db::getInstance()->ExecuteS($sql);
			$k=0;
			foreach($grouplang AS $g)
			{
				if ($k==0)
				{
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						$sql="UPDATE "._DB_PREFIX_."attribute_group SET is_color_group=".intval($g['is_color_group']).", group_type=".intval($g['group_type'])." WHERE id_attribute_group=".intval($newGroupID);
					else
						$sql="UPDATE "._DB_PREFIX_."attribute_group SET is_color_group=".intval($g['is_color_group'])." WHERE id_attribute_group=".intval($newGroupID);
					Db::getInstance()->Execute($sql);
				}
				$sql="INSERT INTO "._DB_PREFIX_."attribute_group_lang (id_attribute_group,id_lang,name,public_name) VALUES (".intval($newGroupID).",".intval($g['id_lang']).",'".psql($g['name'])."','".psql($g['public_name'])."')";
				Db::getInstance()->Execute($sql);
				$k++;
			}
			if (version_compare(_PS_VERSION_, '1.5.0', '>='))
			{
				$sql="INSERT INTO "._DB_PREFIX_."attribute_group_shop (id_attribute_group,id_shop) VALUES (".intval($newGroupID).",".SCI::getSelectedShopActionList(true).")";
				Db::getInstance()->Execute($sql);
			}
			$sql="
			SELECT * FROM "._DB_PREFIX_."attribute a
			LEFT JOIN "._DB_PREFIX_."attribute_lang al ON (al.id_attribute=a.id_attribute)
			WHERE a.id_attribute_group=".intval($id_group);
			$attributes=Db::getInstance()->ExecuteS($sql);
			$inserted=array();
			foreach($attributes AS $a)
			{
				if(!in_array($a['id_attribute'],$inserted))
				{
					$sql="INSERT INTO "._DB_PREFIX_."attribute (id_attribute_group,color) VALUES (".$newGroupID.",'".$a['color']."')";
					Db::getInstance()->Execute($sql);
					$newAttributeID=Db::getInstance()->Insert_ID();
					if (version_compare(_PS_VERSION_, '1.5.0', '>='))
					{
						$sql="INSERT INTO "._DB_PREFIX_."attribute_shop (id_attribute,id_shop) VALUES (".intval($newAttributeID).",".SCI::getSelectedShopActionList(true).")";
						Db::getInstance()->Execute($sql);
					}
					$inserted[]=$a['id_attribute'];
					if (file_exists(_PS_COL_IMG_DIR_.$a['id_attribute'].'.jpg'))
						@copy(_PS_COL_IMG_DIR_.$a['id_attribute'].'.jpg',_PS_COL_IMG_DIR_.$newAttributeID.'.jpg');
				}
				$sql="INSERT INTO "._DB_PREFIX_."attribute_lang (id_attribute,id_lang,name) VALUES (".$newAttributeID.",'".$a['id_lang']."','".psql($a['name'])."')";
				Db::getInstance()->Execute($sql);
			}
		}
		$action='duplicate';
		$newId=0;
		$_POST['gr_id']=0;
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array('color','color2','sc_active');
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
				if ($field=='color2') 
				{
					$todo[]="color='".psql(html_entity_decode( Tools::getValue('color2')))."'";
					addToHistory('attribute','modification','color',intval($id_attribute),$id_lang,_DB_PREFIX_."attribute",psql(Tools::getValue('color2')));
				}else{
					$todo[]=$field."='".psql(html_entity_decode( Tools::getValue($field)))."'";
					addToHistory('attribute','modification',$field,intval($id_attribute),$id_lang,_DB_PREFIX_."attribute",psql(Tools::getValue($field)));
				}
			}
		}
		foreach($fields_lang AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$tmp=explode('¤',$field);
				$fname=$tmp[0];
				$flang=$tmp[1];
				$todo_lang[]=array(''.$fname."='".psql(Tools::htmlentitiesDecodeUTF8( Tools::getValue($field)), true)."'",$idlangByISO[$flang],psql(html_entity_decode( Tools::getValue($field))));
				addToHistory('attribute','modification',$fname,intval($id_attribute),$idlangByISO[$flang],_DB_PREFIX_."attribute_lang",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."attribute SET ".join(' , ',$todo)." WHERE id_attribute=".intval($id_attribute);
			Db::getInstance()->Execute($sql);
		}
		if (count($todo_lang))
		{
			foreach($todo_lang AS $tlang)
			{
				$sqltest="SELECT * FROM "._DB_PREFIX_."attribute_lang WHERE id_attribute=".intval($id_attribute)." AND id_lang=".intval($tlang[1]);
				$test=Db::getInstance()->ExecuteS($sqltest);
				if (count($test)==0){
					$sqlinsert="INSERT INTO "._DB_PREFIX_."attribute_lang VALUES ('".intval($id_attribute)."','".intval($tlang[1])."','".$tlang[2]."')";
					Db::getInstance()->Execute($sqlinsert);
				}else{
					$sql2 = "UPDATE "._DB_PREFIX_."attribute_lang SET ".$tlang[0]." WHERE id_attribute=".intval($id_attribute)." AND id_lang=".intval($tlang[1]);
					Db::getInstance()->Execute($sql2);
				}
			}
		}
		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){
		$attribute=new Attribute($id_attribute,$id_lang);
		$attribute->delete();
		
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$result = Db::getInstance()->executeS('SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute_combination WHERE id_attribute = '.(int)$id_attribute);
			foreach ($result as $row)
			{
				$combination = new Combination($row['id_product_attribute']);
				$combination->delete();
			}
			
			// Delete associated restrictions on cart rules
			CartRule::cleanProductRuleIntegrity('attributes', $id_attribute);
			
			/* Reinitializing position */
			$attribute->cleanPositions((int)$attribute->id_attribute_group);
			
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute WHERE id_attribute=".intval($id_attribute);
			Db::getInstance()->Execute($sql2);
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_impact WHERE id_attribute=".intval($id_attribute);
			Db::getInstance()->Execute($sql2);
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_lang WHERE id_attribute=".intval($id_attribute);
			Db::getInstance()->Execute($sql2);
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_shop WHERE id_attribute=".intval($id_attribute);
			Db::getInstance()->Execute($sql2);
		}
		
		if(file_exists(_PS_COL_IMG_DIR_.$id_attribute.'.jpg'))
			@unlink(_PS_COL_IMG_DIR_.$id_attribute.'.jpg');
		$newId = $_POST["gr_id"];
		$action = "delete";
	}elseif(isset($_GET["action"]) && trim($_GET["action"])=="merge"){
		$attrlist=explode(',',Tools::getValue('attrlist',0));
		sort($attrlist);
		$id_attribute=array_shift($attrlist);
		foreach($attrlist AS $id)
		{
			$sql = "UPDATE "._DB_PREFIX_."product_attribute_combination SET id_attribute=".$id_attribute." WHERE id_attribute=".intval($id);
			Db::getInstance()->Execute($sql);
			$sql = "DELETE FROM "._DB_PREFIX_."attribute_impact WHERE id_attribute=".intval($id);
			Db::getInstance()->Execute($sql);
			$sql = "DELETE FROM "._DB_PREFIX_."attribute_lang WHERE id_attribute=".intval($id);
			Db::getInstance()->Execute($sql);
			$sql = "DELETE FROM "._DB_PREFIX_."attribute WHERE id_attribute=".intval($id);
			Db::getInstance()->Execute($sql);
		}
		echo 'OK:'.$id_attribute;
		exit;
	}

	// PM Cache
	if(!empty($updated_products))
		ExtensionPMCM::clearFromIdsAttribute($id_attribute);
	
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
