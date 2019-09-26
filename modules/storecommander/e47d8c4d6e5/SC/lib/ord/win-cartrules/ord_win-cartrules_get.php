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
//$active=(int)Tools::getValue('active','1');

function getRowsFromDB(){
    global $id_lang/*,$active*/;
    $sql = '
		SELECT cr.*,crl.name,crs.id_shop
		FROM '._DB_PREFIX_.'cart_rule cr
		    LEFT JOIN '._DB_PREFIX_.'cart_rule_lang crl ON (cr.id_cart_rule=crl.id_cart_rule AND crl.id_lang='.(int)$id_lang.')
		    LEFT JOIN '._DB_PREFIX_.'cart_rule_shop crs ON (cr.id_cart_rule=crs.id_cart_rule)
		WHERE 1 './*(!empty($active)?' AND cr.active=1 ':'').*/'
		GROUP BY cr.id_cart_rule
		ORDER BY cr.active ASC, cr.date_add DESC';
    $res=Db::getInstance()->ExecuteS($sql);
    $xml='';
    foreach ($res AS $row)
    {
        $cellColor = ($row['active'] == 0 || (date("Y-m-d H:i:s") > $row['date_to']) ? 'bgColor="#D7D7D7"' : '');
        $xml.=("<row id='".$row['id_cart_rule']."'>");
        $xml.=("<cell style=\"color:#999999\">".$row['id_cart_rule']."</cell>");
        $xml.=("<cell ".$cellColor."><![CDATA[".$row['name']."]]></cell>");
        $xml.=("<cell ".$cellColor."><![CDATA[".$row['code']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['active']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['date_from']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['date_to']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['minimum_amount']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['quantity']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['quantity_per_user']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['reduction_percent']."]]></cell>");
        $xml.=("<cell><![CDATA[".$row['reduction_amount']."]]></cell>");
        $xml.=("<cell><![CDATA[".(!empty($row['id_shop'])?_l('Yes'):_l('No'))."]]></cell>");
        $xml.=("</row>");
    }
    return $xml;
}

//XML HEADER
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
    header("Content-type: application/xhtml+xml"); } else {
    header("Content-type: text/xml");
}
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

$xml=getRowsFromDB();
?>
<rows id="0">
    <head>
        <beforeInit>
            <call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#text_filter,#select_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#numeric_filter,#select_filter]]></param></call>
        </beforeInit>
        <column id="id_cart_rule" width="45" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
        <column id="name" width="160" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
        <column id="code" width="100" type="ro" align="left" sort="str"><?php echo _l('Code')?></column>
        <column id="active" width="45" type="coro" align="left" sort="int"><?php echo _l('Active')?>
            <option value="1"><?php echo _l('Yes')?></option>
            <option value="0"><?php echo _l('No')?></option>
        </column>
        <column id="date_from" width="120" type="ed" align="left" sort="str"><?php echo _l('Date from')?></column>
        <column id="date_to" width="120" type="ed" align="left" sort="str"><?php echo _l('Date to')?></column>
        <column id="minimum_amount" width="80" type="ro" align="right" sort="int"><?php echo _l('Min. amount')?></column>
        <column id="quantity" width="80" type="ed" align="right" sort="int"><?php echo _l('Quantity')?></column>
        <column id="quantity_per_user" width="80" type="ed" align="right" sort="int"><?php echo _l('Quantity per user')?></column>
        <column id="reduction_percent" width="80" type="ro" align="right" sort="int"><?php echo _l('Gross percent')?></column>
        <column id="reduction_amount" width="80" type="ro" align="right" sort="int"><?php echo _l('Gross amount')?></column>
        <column id="filter_shop" width="60" type="ro" align="left" sort="str"><?php echo _l('Filter by shop')?></column>
    </head>
    <?php
    echo '<userdata name="uisettings">'.uisettings::getSetting('cartrules_grid').'</userdata>'."\n";
    echo $xml;
    ?>
</rows>
