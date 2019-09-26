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
$id_objet=intval(Tools::getValue('gr_id',0));

if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
	$fields=array('firstname','lastname','alias','address1','address2','postcode','city','id_state','id_country','phone','phone_mobile');
	$todo=array();
	foreach($fields AS $field)
	{
		if (isset($_POST[$field]))
		{
			$todo[]=$field."='".psql($_POST[$field])."'";
			addToHistory('address','modification',$field,intval($id_objet),0,_DB_PREFIX_."address",psql(Tools::getValue($field)));
		}
	}
	if (count($todo))
	{
		$sql = "UPDATE "._DB_PREFIX_."address SET ".join(' , ',$todo)." WHERE id_address=".intval($id_objet);
		Db::getInstance()->Execute($sql);
	}
	$newId = $_POST["gr_id"];
	$action = "update";
}

if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	header("Content-type: application/xhtml+xml"); } else {
	header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
echo '<data>';
echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
echo '</data>';
