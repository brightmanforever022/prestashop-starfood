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
	$importConfig=array();

	function getFiles()
	{
		global $importConfig;
		$files = array_diff( scandir( SC_CSV_IMPORT_DIR."customers/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) ); 
		readCusImportConfigXML($files);
		foreach ($files AS $file)
		{
			if (strtolower(substr($file,strlen($file)-4,4))=='.csv' && strpos($file,'&')===false)
			{
				echo "<row id=\"".$file."\">";
				echo 		"<cell></cell>";
				echo 		"<cell>".$file."</cell>";
				echo 		"<cell>".date('d/m/Y',filectime(SC_CSV_IMPORT_DIR."customers/".$file))."</cell>";
				echo 		"<cell>".sizeFormat(filesize(SC_CSV_IMPORT_DIR."customers/".$file))."</cell>";
				echo 		"<cell>".str_replace('.map.xml','',$importConfig[$file]['mapping'])."</cell>";
				if(SCMS)
					echo 	"<cell>".$importConfig[$file]['id_shop']."</cell>";
				echo 		"<cell>".$importConfig[$file]['fieldsep']."</cell>";
				echo 		"<cell>".$importConfig[$file]['valuesep']."</cell>";
				echo 		"<cell>".$importConfig[$file]['utf8']."</cell>";
				echo 		"<cell>".$importConfig[$file]['idby']."</cell>";
				echo 		"<cell>".$importConfig[$file]['iffoundindb']."</cell>";
				echo 		"<cell>".$importConfig[$file]['firstlinecontent']."</cell>";
				echo 		"<cell>".$importConfig[$file]['importlimit']."</cell>";
				echo "</row>";
			}
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
?>
<rows>
<head>
<column id="markedfile" width="20" type="ch" align="center" sort="na"> </column>
<column id="filename" width="160" type="ro" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="date" width="60" type="ro" align="right" sort="sort_dateFR"><?php echo _l('File date')?></column>
<column id="size" width="60" type="ro" align="right" sort="na"><?php echo _l('File size')?></column>
<column id="mapping" width="80" type="coro" align="left" sort="na"><?php echo _l('Mapping')?>
	<option value=""></option>
<?php
	$files = array_diff( scandir( SC_CSV_IMPORT_DIR."customers/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
	$content='';
	foreach ($files AS $file)
	{
		if (substr($file,strlen($file)-8,8)=='.map.xml')
		{
			$file=str_replace('.map.xml','',$file);
			$content.="<option value=\"".$file."\">".$file."</option>";
		}
	}
	echo $content;
?>
</column>
<?php if(SCMS) { ?>
<column id="id_shop" width="80" type="coro" align="left" sort="na"><?php echo _l('Shop')?>
	<option value=""></option>
<?php
	$shops = Shop::getShops(false);
	$content='';
	foreach ($shops AS $shop)
	{
		$content.="<option value=\"".$shop["id_shop"]."\">".$shop["name"]."</option>";
	}
	echo $content;
?>
</column>
<?php } ?>
<column id="fieldsep" width="60" type="coro" align="right" sort="na"><?php echo _l('Field separator')?>
	<option value="dcomma">;</option>
	<option value="dcommamac">; Apple MAC</option>
	<option value=",">,</option>
	<option value="|">| pipe</option>
	<option value="tab">Tabulation</option>
</column>
<column id="valuesep" width="60" type="coro" align="right" sort="na"><?php echo _l('Value separator')?>
	<option value=",">,</option>
	<option value="|">| pipe</option>
	<option value="tab">Tabulation</option>
</column>
<column id="utf8" width="50" type="ch" align="center" sort="na"><?php echo _l('Force UTF8')?></column>
<column id="idby" width="115" type="coro" align="left" sort="na"><?php echo _l('Customers are identified by')?>
	<option value="idcustomer"><?php echo _l('id_customer')?></option>
	<option value="email"><?php echo _l('Email')?></option>
	<option value="idcustomeradresse"><?php echo _l('id_customer + address title')?></option>
	<option value="emailadresse"><?php echo _l('email + address title')?></option>
	<option value="idcustomeridadresse"><?php echo _l('id_customer + id_address')?></option>
	<option value="emailidadresse"><?php echo _l('email + id_address')?></option>
<?php
	/*if (SC_TOOLS && file_exists(SC_TOOLS_DIR.'import_csv_conf.xml'))
	 echo '<option value="specialIdentifier">'._l('specialIdentifier').'</option>';*/
?>
</column>
<column id="iffoundindb" width="120" type="coro" align="left" sort="na"><?php echo _l('If customer with same identifier found in database')?>
	<option value="skip"><?php echo _l('Skip')?></option>
	<option value="replace"><?php echo _l('Modify and create customers/addresses')?></option>
	<option value="replaceonly"><?php echo _l('Modify customers/addresses')?></option>
	<?php /*?><option value="create"><?php echo _l('Create new customer')?></option>*/ ?>
</column>
<column id="firstlinecontent" width="120" type="edtxt" align="left" sort="na"><?php echo _l('First line content')?></column>
<column id="importlimit" width="60" type="edtxt" align="right" sort="na"><?php echo _l('Lines to import')?></column>
</head>
<?php
	getFiles();
	echo '</rows>';
?>