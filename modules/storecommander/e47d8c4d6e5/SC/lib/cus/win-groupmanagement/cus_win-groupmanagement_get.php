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

	$attributegroups=array();
	$id_lang=intval(Tools::getValue('id_lang'));

	$xml='';

	$sql="SELECT g.*, gl.name 
			FROM "._DB_PREFIX_."group g
			LEFT JOIN "._DB_PREFIX_."group_lang gl ON gl.id_group = g.id_group AND gl.id_lang = ".(int)$id_lang;
	$groups=Db::getInstance()->ExecuteS($sql);

	foreach($groups AS $row)
	{
		$xml.=("<row id='".$row['id_group']."'>");
			$xml.=("<cell>".$row['id_group']."</cell>");
			$xml.=("<cell><![CDATA[".$row['name']."]]></cell>");
			$xml.=("<cell>".$row['reduction']."</cell>");
			$xml.=("<cell>".$row['price_display_method']."</cell>");
			$xml.=("<cell>".$row['show_prices']."</cell>");
			$xml.=("<cell>".$row['date_add']."</cell>");
			$xml.=("<cell>".$row['date_upd']."</cell>");
		$xml.=("</row>");
	}

	//XML HEADER

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter,#numeric_filter,#numeric_filter,#numeric_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_group" width="40" type="ro" align="left" sort="int"><?php echo _l('ID')?></column>
<column id="name" width="100" type="edtxt" align="left" sort="str"><?php echo _l('Name') ?></column>';
<column id="reduction" width="70" type="edn" align="center" sort="int"><?php echo _l('Reduction in %')?></column>
<column id="price_display_method" width="70" type="coro" align="center" sort="int" options="<?php array(0=>_l('Tax incl.'),1=>_l('Tax excl.')) ?>"><?php echo _l('Price display method')?>
	<option value="0"><![CDATA[<?php echo _l('Tax incl.')?>]]></option>
	<option value="1"><![CDATA[<?php echo _l('Tax excl.')?>]]></option>
</column>
<column id="show_prices" width="70" type="coro" align="center" sort="int" options="<?php array(0=>_l('No'),1=>_l('Yes')) ?>"><?php echo _l('Show prices')?>
	<option value="0"><![CDATA[<?php echo _l('No')?>]]></option>
	<option value="1"><![CDATA[<?php echo _l('Yes')?>]]></option>
</column>
<column id="date_add" width="100" type="ro" align="left" sort="int"><?php echo _l('Date add')?></column>
<column id="date_upd" width="100" type="ro" align="left" sort="int"><?php echo _l('Modified date')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_win-groupmanagement').'</userdata>'."\n";
	echo 	$xml;
?>
</rows>
