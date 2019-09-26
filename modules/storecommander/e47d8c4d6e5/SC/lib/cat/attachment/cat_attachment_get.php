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

	$product_list=Tools::getValue('product_list');
	$id_lang=Tools::getValue('id_lang');
	$id_product=Tools::getValue('id_product');
	$id_attachment=Tools::getValue('id_attachment');
	$id_category=Tools::getValue('id_category');
	$attachmentFilter=Tools::getValue('attachmentFilter');
	$cols='';
	$filters='';
	foreach($languages AS $lang)
	{
		$cols.='<column id="name¤'.$lang['iso_code'].'" width="150" type="edtxt" align="left" sort="str">'._l('Name').' '.strtoupper($lang['iso_code']).'</column>
				<column id="description¤'.$lang['iso_code'].'" width="150" type="txt" align="left" sort="str">'._l('Description').' '.strtoupper($lang['iso_code']).'</column>';
		$filters.='#text_filter,#text_filter,';
	}
	
	function getAttachments()
	{
		global $product_list,$id_lang,$id_product,$attachmentFilter,$id_category,$languages;
		if (intval($attachmentFilter)){					
			$sql="	SELECT a.id_attachment,al.id_lang,al.name,al.description".(version_compare(_PS_VERSION_,"1.4.0.3",">=")?",a.file_name":"")."".(version_compare(_PS_VERSION_,"1.6.0.0",">=")?",a.file_size":"")."
					FROM "._DB_PREFIX_."product_attachment pa
					LEFT JOIN "._DB_PREFIX_."attachment a ON (pa.id_attachment=a.id_attachment)
					LEFT JOIN "._DB_PREFIX_."attachment_lang al ON (a.id_attachment=al.id_attachment)
					WHERE pa.id_product IN (SELECT cp.id_product FROM "._DB_PREFIX_."category_product cp WHERE cp.id_category=".intval($id_category).")
					AND a.id_attachment>0
					ORDER BY al.name";					
		}else{
			$sql="	SELECT a.id_attachment,al.id_lang,al.name,al.description".(version_compare(_PS_VERSION_,"1.4.0.3",">=")?",a.file_name":"")."".(version_compare(_PS_VERSION_,"1.6.0.0",">=")?",a.file_size":"")."
					FROM "._DB_PREFIX_."attachment a
					LEFT JOIN "._DB_PREFIX_."attachment_lang al ON (a.id_attachment=al.id_attachment)
					ORDER BY al.name";
		}		
		$res=Db::getInstance()->ExecuteS($sql);
		$names=array();
		foreach($res AS $row)
		{
			if (version_compare(_PS_VERSION_,'1.4.0.3','>=')){
				$names[$row['id_attachment']]['file_name']=$row['file_name'];
				$names[$row['id_attachment']][$row['id_lang']]['name']=$row['name'];
				$names[$row['id_attachment']][$row['id_lang']]['description']=$row['description'];
			}else{
				$names[$row['id_attachment']][$row['id_lang']]['name']=$row['name'];
				$names[$row['id_attachment']][$row['id_lang']]['description']=$row['description'];
			}
			if (version_compare(_PS_VERSION_,'1.6.0.0','>='))
				$names[$row['id_attachment']]['file_size']=$row['file_size'];
		}
		foreach($names AS $k => $val)
		{
			echo "<row id=\"".$k."\">";
			echo 	"<cell>".$k."</cell>";
			echo 	"<cell>0</cell>";
			if (version_compare(_PS_VERSION_,'1.6.0.0','>='))
				echo 	"<cell><![CDATA[".number_format($val['file_size']/1024)." Ko]]></cell>";
			if (version_compare(_PS_VERSION_,'1.4.0.3','>='))
				echo 	"<cell><![CDATA[".$val['file_name']."]]></cell>";
			foreach($languages as $lang)
			{
				echo "<cell><![CDATA[".(sc_array_key_exists($lang['id_lang'],$names[$k])?$names[$k][$lang['id_lang']]['name']:'')."]]></cell>";
				echo "<cell><![CDATA[".(sc_array_key_exists($lang['id_lang'],$names[$k])?$names[$k][$lang['id_lang']]['description']:'')."]]></cell>";
			}
			echo "</row>";
		}
	}	
	
	//XML HEADER
	//include XML Header (as response will be in xml format)
	
	if (stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	 	header("Content-type: application/xhtml+xml");
	}else{
	 	header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<rows parent="0">';
	echo '<head>';
	echo '<beforeInit>';
	if (version_compare(_PS_VERSION_,'1.4.0.3','>=')){
		echo '<call command="attachHeader"><param><![CDATA[#text_filter, ,#text_filter,#text_filter,'.$filters.']]></param></call>';
	}else{
		echo '<call command="attachHeader"><param><![CDATA[#text_filter, ,'.$filters.']]></param></call>';
	}
	echo '</beforeInit>';
	echo '<column id="id_attachment" width="50" type="ro" align="right" sort="int">'._l('ID').'</column>';
	echo '<column id="used" width="50" type="ch" align="center" sort="str">'._l('Used').'</column>';
	if (version_compare(_PS_VERSION_,'1.6.0.0','>=')){
		echo '<column id="file_size" width="80" type="ro" align="right" sort="str">'._l('File size').'</column>';
	}	
	if (version_compare(_PS_VERSION_,'1.4.0.3','>=')){
		echo '<column id="file_name" width="150" type="edtxt" align="left" sort="str">'._l('File name').'</column>';
	}	
	echo $cols;
	echo '</head>';
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_attachment').'</userdata>'."\n";
	getAttachments();
//	echo "<debug>".$sql."</debug>";
	echo '</rows>';
