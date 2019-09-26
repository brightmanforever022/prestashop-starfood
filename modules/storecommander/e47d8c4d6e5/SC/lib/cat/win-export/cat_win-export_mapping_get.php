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
	$exp_mapping_file=Tools::getValue('exp_mapping_file').'.map.xml';

	$xml='';
	if ($exp_mapping_file!='' && $feed = @simplexml_load_file(SC_TOOLS_DIR.'cat_export/'.$exp_mapping_file))
	{
		$feed = ExportConvert::convert($exp_mapping_file, $feed);
		
		foreach($feed->field AS $field)
		{
			$xml.="<row id='".$field->id."'>";
				$xml.="<cell>".$field->used."</cell>";
				$xml.="<cell>".(string)$field->name."</cell>";
				$xml.="<cell><![CDATA[".$field->lang."]]></cell>";
				$xml.="<cell><![CDATA[".$field->options."]]></cell>";
				//$xml.="<cell><![CDATA[".$field->filters."]]></cell>";
				$xml.="<cell><![CDATA[".$field->modifications."]]></cell>";
				$xml.="<cell><![CDATA[".$field->column_name."]]></cell>";
			$xml.="</row>";
		}
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
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="use" width="30" type="ch" align="center" sort="na"><?php echo _l('Use')?></column>
<column id="name" width="160" type="coro"  editable="false" align="left" sort="str" xmlcontent="1"><?php echo _l('Database field')?>
<?php
	$array=getExportCSVFields();
	foreach($array AS $k => $v)
		echo '<option value="'.$v.'">'.stripslashes($k).'</option>';
?>
</column>
<column id="lang" width="50" type="coro" align="left" sort="na"><?php echo _l('Lang')?></column>
<column id="options" width="100" type="coro" align="left" sort="na"><?php echo _l('Options')?>
	<option value="supplier_none"><?php echo _l('Default values display products/combinations grids')?></option>
	<?php if(SCAS) { ?>
	<option value="warehouse_none"><?php echo _l('No warehouse')?></option>
	<?php
	$warehouses=Warehouse::getWarehouses(true);
	foreach($warehouses AS $warehouse)
		echo '<option value="warehouse_'.($warehouse['id_warehouse']).'">'._l('Warehouse').' '.htmlspecialchars($warehouse['name']).'</option>';
	?>
	<?php } ?>
</column>
<?php /*?><column id="filters" width="100" type="ro" align="left" sort="na"><?php echo _l('Filters')?></column>*/ ?>
<column id="modifications" width="100" type="edtxt" align="left" sort="na"><?php echo _l('Modifications')?></column>
<column id="column_name" width="100" type="edtxt" align="left" sort="na"><?php echo _l('Column name')?></column>
</head>
<?php
	echo 	$xml;
?>
</rows>
