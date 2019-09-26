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

	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

	if (!isTable('pagenotfound'))
	{
		$xml='<rows id="0"><head><column id="error" width="300" type="ro" align="left" sort="str">Error</column></head>
<row id="0"><cell><![CDATA['._l('You must install the pagenotfound module to use this tool.').']]></cell></row>
</rows>';
		die($xml);
	}

	$xml='';
	$result = Db::getInstance()->ExecuteS('
			SELECT http_referer, request_uri, id_shop, COUNT(*) as nb
			FROM `'._DB_PREFIX_.'pagenotfound`
			GROUP BY http_referer, request_uri
			ORDER BY nb DESC');
	$i=1;
	foreach($result AS $f)
	{
		$xml.="<row id='".$i."'>";
		$xml.="<cell><![CDATA[".$f['request_uri']."]]></cell>";
		$xml.="<cell><![CDATA[".$f['http_referer']."]]></cell>";
		$xml.="<cell><![CDATA[".$f['id_shop']."]]></cell>";
		$xml.="<cell><![CDATA[".$f['nb']."]]></cell>";
		$xml.="</row>";
		$i++;
	}

?>
<rows id="0">
<head>
<column id="request_uri" width="300" type="ro" align="left" sort="str">request_uri</column>
<column id="http_referer" width="300" type="ro" align="left" sort="str">http_referer</column>
<column id="id_shop" width="60" type="ro" align="right" sort="int">id_shop</column>
<column id="count" width="50" type="ro" align="right" sort="int">Total</column>
</head>
<?php
	echo 	$xml;
?>
</rows>
