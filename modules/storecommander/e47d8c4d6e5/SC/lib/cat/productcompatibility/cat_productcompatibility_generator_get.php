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

function getCompatibilitiesFilter()
{
    global $id_lang;
	if($id_lang)
	{
		$sql = "SELECT fl.*, f.position
		        FROM "._DB_PREFIX_."ukoocompat_filter_lang fl
		        LEFT JOIN "._DB_PREFIX_."ukoocompat_filter f ON fl.id_ukoocompat_filter = f.id_ukoocompat_filter
		        WHERE fl.id_lang = ".(int)$id_lang."
		        ORDER BY f.position";
		return Db::getInstance()->ExecuteS($sql);
	}
}

function getAttachHeader()
{
    $return = '';
    foreach(getCompatibilitiesFilter() as $filter) {
        if(!empty($return)) {
            $return .= ',#text_filter';
        } else {
            $return .= '#text_filter';
        }
    }
    return $return;
}

if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
    header("Content-type: application/xhtml+xml");
}else{
    header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows>
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[<?php echo getAttachHeader(); ?>]]></param></call>
        </beforeInit>
        <?php foreach(getCompatibilitiesFilter() as $filter) { ?>
            <column id="filter_<?php echo $filter['id_ukoocompat_filter']; ?>" width="200" type="ro" align="left" sort="str"><?php echo $filter['name']; ?></column>
        <?php } ?>
    </head>
</rows>
