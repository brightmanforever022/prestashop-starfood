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
$id_category=intval(Tools::getValue('id_category'),0);

$sourceGridFormat=SCI::getGridViews("productsort");
$sql_gridFormat = $sourceGridFormat;
sc_ext::readCustomProductsortGridConfigXML('gridConfig');
$gridFormat=$sourceGridFormat;
$cols=explode(',',$gridFormat);
$all_cols = explode(',',$gridFormat);

$colSettings=array();
$colSettings=SCI::getGridFields("productsort");
sc_ext::readCustomProductsortGridConfigXML('colSettings');

$xml='';
if(!empty($id_category))
{
	$defaultimg='lib/img/i.gif';
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if (file_exists(SC_PS_PATH_DIR."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE')."_default.jpg"))
			$defaultimg=SC_PS_PATH_REL."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE')."_default.jpg";
	}else{
		if (file_exists(SC_PS_PATH_DIR."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE').".jpg"))
			$defaultimg=SC_PS_PATH_REL."img/p/".$user_lang_iso."-default-"._s('CAT_PROD_GRID_IMAGE_SIZE').".jpg";
	}
	
	$sql="SELECT p.*, pl.* ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" , prs.* ":"").", cp.position ".(sc_in_array("image", $cols,"catProductSort_cols")?" ,i.id_image ":'');
	sc_ext::readCustomProductsortGridConfigXML('SQLSelectDataSelect');
	$sql.="	 FROM "._DB_PREFIX_."product p
				INNER JOIN "._DB_PREFIX_."category_product cp ON (cp.id_product= p.id_product AND cp.id_category=".intval($id_category).") ".
				(SCMS? " INNER JOIN "._DB_PREFIX_."product_shop prs ON (prs.id_product=p.id_product AND prs.id_shop = (".(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').")) ":'').
				((!SCMS && version_compare(_PS_VERSION_, '1.5.0.0', '>='))?" INNER JOIN "._DB_PREFIX_."product_shop prs ON (prs.id_product=p.id_product AND prs.id_shop = p.id_shop_default) ":'')."
				LEFT JOIN "._DB_PREFIX_."product_lang pl ON (pl.id_product= p.id_product AND pl.id_lang=".intval($id_lang)." ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" AND pl.id_shop=prs.id_shop ":"").") 
				".(sc_in_array("image", $cols,"catProductSort_cols")?" LEFT JOIN "._DB_PREFIX_."image i ON (i.id_product= p.id_product AND i.cover=1)":'');
	
	sc_ext::readCustomProductsortGridConfigXML('SQLSelectDataLeftJoin');
	$sql.="GROUP BY p.id_product ORDER BY cp.position ASC";
	$res=Db::getInstance()->ExecuteS($sql);
	$pos = 0;
	foreach($res as $row)
	{
		$xml.=("<row id='".$row['id_product']."'>");
		foreach($cols AS $id => $col)
		{
			if($col=="position")
				$xml.=("<cell>".$pos."</cell>");
			elseif($col=="image")
			{
				if ($row['id_image']=='')
				{
					$xml.="<cell><![CDATA[<img src='".$defaultimg."'/>]]></cell>";
				}else{
					$xml.="<cell><![CDATA[<img src='".SC_PS_PATH_REL."img/p/".getImgPath(intval($row['id_product']),intval($row['id_image']),_s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>]]></cell>";
				}
			}
			else
				$xml.=("<cell><![CDATA[".$row[$col]."]]></cell>");
		}
		$xml.=("</row>\n");
		$pos++;
	}
}


//XML HEADER
if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	header("Content-type: application/xhtml+xml"); } else {
		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	?>
<rows id="0">
<head>
<?php 
foreach($cols AS $id => $col)
{
	echo '<column id="'.$col.'"'.(is_array($colSettings[$col]) && sc_array_key_exists('format',$colSettings[$col])?' format="'.$colSettings[$col]['format'].'"':'').' width="'.$colSettings[$col]['width'].'" align="'.$colSettings[$col]['align'].'" type="'.$colSettings[$col]['type'].'" sort="'.$colSettings[$col]['sort'].'" color="'.$colSettings[$col]['color'].'"><![CDATA['.$colSettings[$col]['text'].']]>';
	if (is_array($colSettings[$col]) && sc_array_key_exists('options',$colSettings[$col]))
	{
		foreach($colSettings[$col]['options'] AS $k => $v)
		{
			echo '<option value="'.str_replace('"','\'',$k).'"><![CDATA['.$v.']]></option>';
		}
	}
	echo '</column>'."\n";
}
?>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_productsort').'</userdata>'."\n";
	sc_ext::readCustomProductsortGridConfigXML('gridUserData');
	echo $xml;
?>
</rows>