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
	$idlist=(Tools::getValue('idlist',0));

	function getRowsFromDB(){
		global $id_lang,$idlist;
		
		$array_langs = array();
		$langs = Language::getLanguages(false);
		foreach($langs as $lang)
			$array_langs[$lang["id_lang"]] = strtoupper($lang["iso_code"]);
		$sql = 'SELECT *
				FROM '._DB_PREFIX_.'group_lang
				WHERE id_group IN ('.psql($idlist).')
				ORDER BY id_group, id_lang';
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $row)
		{
			$xml.=("<row id='".$row['id_group']."_".$row['id_lang']."'>");
			$xml.=	("<userdata name=\"id_language\">".$row['id_lang']."</userdata>");
			$xml.=	("<cell>".$row['id_group']."</cell>");
			$xml.=	("<cell><![CDATA[".$row['name']."]]></cell>");
			$xml.=	("<cell>".$array_langs[$row['id_lang']]."</cell>");
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

	$xml = "";
	if(!empty($idlist))
		$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id_group" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="name" width="120" type="ed" align="left" sort="str"><?php echo _l('Name')?></column>
<column id="id_lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang')?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
//  format="%Y-%m-%d 00:00:00"
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_group_prop_seo_grid').'</userdata>'."\n";
	echo $xml;
?>
</rows>
