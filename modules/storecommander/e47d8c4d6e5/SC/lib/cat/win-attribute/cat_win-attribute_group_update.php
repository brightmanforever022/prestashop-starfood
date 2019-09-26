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

	$id_attribute_group=intval(Tools::getValue('gr_id'));
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
				$todo[]="UPDATE "._DB_PREFIX_."attribute_group SET position=".$pos[1]." WHERE id_attribute_group=".intval($pos[0])."";
			}
		}
		foreach($todo AS $task)
		{
			Db::getInstance()->Execute($task);
		}
	}
	elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){
		$newgroup=new AttributeGroup();
		if (version_compare(_PS_VERSION_, '1.5.0', '>='))
		{
			foreach($languages AS $lang)
			{
				$newgroup->name[$lang['id_lang']]='new';
				$newgroup->public_name[$lang['id_lang']]='new';
			}
			$newgroup->group_type='select';
            $newgroup->position=AttributeGroup::getHigherPosition()+1;
		}
		$newgroup->add();
        SCI::addToShops('attribute_group', array((int)$newgroup->id));
		$newId = $newgroup->id;
		$action = "insert";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array('is_color_group','group_type');
		$fields_lang=array();
		$idlangByISO=array();
		$todo=array();
		$todo_lang=array();
		foreach($languages AS $lang)
		{
			$fields_lang[]='name¤'.$lang['iso_code'];
			$fields_lang[]='public_name¤'.$lang['iso_code'];
			$idlangByISO[$lang['iso_code']]=$lang['id_lang'];
		}
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$todo[]='`'.$field."`='".psql(html_entity_decode( Tools::getValue($field)))."'";
				addToHistory('attribute_group','modification',$field,intval($id_attribute_group),$id_lang,_DB_PREFIX_."attribute_group",psql(Tools::getValue($field)));
			}
		}
		foreach($fields_lang AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$tmp=explode('¤',$field);
				$fname=$tmp[0];
				$flang=$tmp[1];
				$todo_lang[]=array('`'.$fname."`='".psql(html_entity_decode( Tools::getValue($field)), true)."'",$idlangByISO[$flang],psql(html_entity_decode( Tools::getValue($field))));
				addToHistory('attribute_group','modification',$fname,intval($id_attribute_group),$idlangByISO[$flang],_DB_PREFIX_."attribute_group_lang",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."attribute_group SET ".join(' , ',$todo)." WHERE id_attribute_group=".intval($id_attribute_group);
			Db::getInstance()->Execute($sql);
		}
		if (count($todo_lang))
		{
			foreach($todo_lang AS $tlang)
			{
				$sqltest="SELECT * FROM "._DB_PREFIX_."attribute_group_lang WHERE id_attribute_group=".intval($id_attribute_group)." AND id_lang=".intval($tlang[1]);
				$test=Db::getInstance()->ExecuteS($sqltest);
				if (count($test)==0){
					$sqlinsert="INSERT INTO "._DB_PREFIX_."attribute_group_lang VALUES ('".intval($id_attribute_group)."','".intval($tlang[1])."','".$tlang[2]."','".$tlang[2]."')";
					Db::getInstance()->Execute($sqlinsert);
				}else{
					$sql2 = "UPDATE "._DB_PREFIX_."attribute_group_lang SET ".$tlang[0]." WHERE id_attribute_group=".intval($id_attribute_group)." AND id_lang=".intval($tlang[1]);
					Db::getInstance()->Execute($sql2);
				}
			}
		}
		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){
		$group=new AttributeGroup($id_attribute_group,$id_lang);
		$group->delete();
		
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			/* Select children in order to find linked combinations */
			$attribute_ids = Db::getInstance()->executeS('
				SELECT `id_attribute`
				FROM `'._DB_PREFIX_.'attribute`
				WHERE `id_attribute_group` = '.(int)$id_attribute_group
			);
			if ($attribute_ids !== false)
			{
				/* Removing attributes to the found combinations */
				$to_remove = array();
				foreach ($attribute_ids as $attribute)
					$to_remove[] = (int)$attribute['id_attribute'];
				if (!empty($to_remove))
				{
					Db::getInstance()->execute('
					DELETE FROM `'._DB_PREFIX_.'product_attribute_combination`
					WHERE `id_attribute`
						IN ('.implode(', ', $to_remove).')');
				}
			}
			/* Remove combinations if they do not possess attributes anymore */
			AttributeGroup::cleanDeadCombinations();
			
			/* Also delete related attributes */
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_lang`
				WHERE `id_attribute`
					IN (SELECT id_attribute FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$id_attribute_group.')');
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_shop`
				WHERE `id_attribute`
					IN (SELECT id_attribute FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$id_attribute_group.')');
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'attribute_impact`
				WHERE `id_attribute`
					IN (SELECT id_attribute FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$id_attribute_group.')');
			Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'attribute` WHERE `id_attribute_group` = '.(int)$id_attribute_group);
			$group->cleanPositions();
			
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_group WHERE id_attribute_group=".intval($id_attribute_group);
			Db::getInstance()->Execute($sql2);
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_group_lang WHERE id_attribute_group=".intval($id_attribute_group);
			Db::getInstance()->Execute($sql2);
			$sql2 = "DELETE FROM "._DB_PREFIX_."attribute_group_shop WHERE id_attribute_group=".intval($id_attribute_group);
			Db::getInstance()->Execute($sql2);
		}
		
		$newId = $_POST["gr_id"];
		$action = "delete";
	}

	// PM Cache
	if(!empty($id_attribute_group))
		ExtensionPMCM::clearFromIdsAttributeGroup($id_attribute_group);
	
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
