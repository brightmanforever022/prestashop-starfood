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
<call command="attachHeader"><param><![CDATA[,#text_filter]]></param></call>
<call command="enableColumnMove"><param>0</param></call>
</beforeInit>
<column id="color" width="60" type="ro" align="center" sort="str"><?php echo _l('Color')?></column>
<column id="help" width="*" type="ro" align="left" sort="str"><?php echo _l('Help')?></column>
</head>
<row id="D7D7D7">
	<cell bgColor="#D7D7D7" ></cell>
	<cell><![CDATA[<?php echo _l("Product with combination(s), whatever the Advanced Stock Mgmt mode is."); ?>]]></cell>
</row>
<row id="e7ab70">
	<cell bgColor="e7ab70"></cell>
	<cell><![CDATA[<?php echo _l("Product not associated to any warehouse"); ?>]]></cell>
</row>
<row id="f7e4bf">
	<cell bgColor="f7e4bf"></cell>
	<cell><![CDATA[<?php echo _l("Product with at least one warehouse, but not the warehouse selected on the left col"); ?>]]></cell>
</row>
<row id="d7f7bf">
	<cell bgColor="d7f7bf"></cell>
	<cell><![CDATA[<?php echo _l("Product associated to at least one warehouse, including the warehouse selected on the left col"); ?>]]></cell>
</row>
</rows>