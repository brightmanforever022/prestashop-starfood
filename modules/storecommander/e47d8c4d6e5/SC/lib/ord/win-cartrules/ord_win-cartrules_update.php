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

$id_lang=intval(Tools::getValue('id_lang',0));
$id_cart_rule=intval(Tools::getValue('gr_id',0));
$action=Tools::getValue('action');

if(!empty($action) && $action=="delete")
{
    $ids_array=explode(',',Tools::getValue('ids'));
    if (!empty($ids_array))
    {
        foreach ($ids_array as $id_cartrules)
        {
            if(!empty($id_cartrules))
            {
                $cartrule = new CartRule ((int)$id_cartrules);
                $cartrule->delete();
            }
        }
    }    
}
elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){

    $fields=array('active','date_from','date_to','quantity','quantity_per_user');
    $todo=array();
    foreach($fields AS $field)
    {
        if (isset($_GET[$field]) || isset($_POST[$field]))
        {
            $todo[]=$field."='".psql(Tools::getValue($field))."'";
        }
    }
    if (count($todo))
    {
        $sql = "UPDATE "._DB_PREFIX_."cart_rule SET ".join(' , ',$todo)." WHERE id_cart_rule=".intval($id_cart_rule);
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
echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
echo '</data>';
?>