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
	$id_product=intval(Tools::getValue('id_product',0));
	$quantity=intval(Tools::getValue('quantity',0));
	$value=Tools::getValue('value');
	$id_discount_quantity=intval(Tools::getValue('gr_id',0));

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="inserted"){

		$type=2;
		$value=trim($value);
		if (strpos($value,'%')!==false)
		{
			$type=1;
			$value=trim($value,'%');
		}
		$sql = "INSERT INTO "._DB_PREFIX_."discount_quantity (id_discount_type,id_product,quantity,value,id_product_attribute) VALUES (".intval($type).",".intval($id_product).",".intval($quantity).",".floatval($value).",0)";
		Db::getInstance()->Execute($sql);
		$newId = Db::getInstance()->Insert_ID();
		$action = "insert";

	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		
		$fields=array('quantity','value');
		$todo=array();
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				addToHistory('discount_quantity','modification',$field,intval($id_product),$id_lang,_DB_PREFIX_."discount_quantity",psql(Tools::getValue($field)));
				if ($field=='value')
				{
					$value=trim(Tools::getValue($field));
					$value=str_replace(',','.',$value);
					if (strpos($value,'%')!==false)
					{
						$type=1;
						$value=trim($value,'%');
					}else{
						$type=2;
					}
					$todo[]=$field."='".psql(html_entity_decode( $value))."'";
					$todo[]="id_discount_type='".$type."'";
				}else{
					$todo[]=$field."='".psql(html_entity_decode( Tools::getValue($field)))."'";
				}
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."discount_quantity SET ".join(' , ',$todo)." WHERE id_discount_quantity=".intval($id_discount_quantity);
			Db::getInstance()->Execute($sql);
		}
		$newId = $_POST["gr_id"];
		$action = "update";
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){

		$sql = "DELETE FROM "._DB_PREFIX_."discount_quantity WHERE id_discount_quantity=".intval($id_discount_quantity);
		Db::getInstance()->Execute($sql);
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