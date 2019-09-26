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
	$imp_opt_file=Tools::getValue('imp_opt_file');

	$getIDlangByISO=array();
	foreach($languages AS $lang)
	{      
		$getIDlangByISO[$lang['iso_code']]=$lang['id_lang'];
	}
	$files = array_diff( scandir( SC_CSV_IMPORT_DIR."customers/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) ); 
	readCusImportConfigXML($files);
	$lineSep="\n";
	if ($importConfig[$imp_opt_file]['fieldsep']=='dcomma') $importConfig[$imp_opt_file]['fieldsep']=';';
	if ($importConfig[$imp_opt_file]['fieldsep']=='dcommamac')
	{
		$importConfig[$imp_opt_file]['fieldsep']=';';
		$lineSep="\r";
	}

/*	@$handle = fopen(SC_CSV_IMPORT_DIR.$imp_opt_file, 'r');
	if (!$handle)
		die('Cannot read the csv file');
	$line = fgetcsv($handle, MAX_LINE_SIZE, $importConfig[$imp_opt_file]['fieldsep'], $importConfig[$imp_opt_file]['valuesep']);
*/
	if ($importConfig[$imp_opt_file]['firstlinecontent']!='')
	{
		$firstLineData=explode($importConfig[$imp_opt_file]['fieldsep'],$importConfig[$imp_opt_file]['firstlinecontent']);
	}else{
		$DATAFILE=file_get_contents(SC_CSV_IMPORT_DIR."customers/".$imp_opt_file);
		$DATA = preg_split("/(?:\r\n|\r|\n)/", $DATAFILE); 
		$firstLineData=explode($importConfig[$imp_opt_file]['fieldsep'],$DATA[0]);
	}
	$firstLineData=array_map('cleanQuotes',$firstLineData);

	$k=0;
	$xml='';
	foreach($firstLineData AS $col)
	{
		escapeCharForPS($col);
		if ($importConfig[$imp_opt_file]['utf8']) $col=utf8_encode($col);
		$xml.=("<row id='".$k."'>");
			$xml.=("<cell></cell>");
			$xml.=("<cell style=\"color:#555555\"><![CDATA[".stripslashes(cleanQuotes($col))."]]></cell>");
			$xml.=("<cell><![CDATA["."]]></cell>");
			$xml.=("<cell><![CDATA["."]]></cell>");
		$xml.=("</row>");
		$k++;
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
<column id="use" width="30" type="ch" align="center" sort="na"><?php echo _l('Use')?></column>
<column id="file_field" width="100" type="ro" align="left" sort="na"><?php echo _l('File field')?></column>
<column id="db_field" width="100" type="coro" align="left" sort="na"><?php echo _l('Database field')?></column>
<column id="options" width="100" type="coro" align="left" sort="na"><?php echo _l('Options')?></column>
</head>
<?php
	echo 	$xml;
?>
</rows>
