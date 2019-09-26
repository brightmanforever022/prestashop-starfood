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
$exportConfig=array();

function getFiles()
{
$dir = '../../export/';

$open_dir = opendir($dir) or die('Erreur');

	while($filename = @readdir($open_dir)) {
		if(!is_dir($dir.'/'.$filename) && $filename != '.' && $filename != '..' && $filename != 'index.php') {

			echo "<row id='".$filename."'>";
			echo 		"<cell><![CDATA[<a href=\"".(isset($websiteURL) ? $websiteURL:'').$dir.$filename."\" target=\"_blank\" style=\"color: #000000;\">".$filename."</a>]]></cell>";
			echo 		"<cell><![CDATA[".number_format(filesize($dir.$filename)/1024,2)."]]></cell>";
			echo 		"<cell><![CDATA[".(date ("Y-m-d H:i:s", filemtime($dir.$filename)))."]]></cell>";
			echo "</row>";
		}
	}
	closedir($open_dir);
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
		<column id="filename" width="100" type="ro" align="left" sort="str"> <?php echo _l('Filename')?></column>
		<column id="filesize" width="100" type="ro" align="right" sort="int"><?php echo _l('Filesize')?> (Ko)</column>
		<column id="date" width="120" type="ro" align="right" sort="str"><?php echo _l('Date')?></column>
	</head>
	<?php
	getFiles();
	?>
</rows>

