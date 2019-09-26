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
	$id_product=intval(Tools::getValue('id_product'));
	$selection=Tools::getValue('selection','');
	$selectionArr=explode(',',$selection);
	foreach($selectionArr AS $k => $sel)
	{
		if (substr($sel,0,3)=='NEW')
			unset($selectionArr[$k]);
	}
	$selection=join(',',$selectionArr);
	
	function getRowsFromDB(){
		global $id_lang,$id_product,$selection,$selectionArr;

		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$sql = '
			SELECT i.*
			FROM `'._DB_PREFIX_.'image` i
				INNER JOIN `'._DB_PREFIX_.'product` p ON (i.`id_product`=p.`id_product`)
				'.(SCMS?'INNER JOIN `'._DB_PREFIX_.'image_shop` ims ON (ims.id_image=i.id_image AND ims.id_shop='.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')':"").'
			WHERE i.`id_product` = '.intval($id_product).'
			ORDER BY i.position';
		}else{
			$sql = '
			SELECT i.*, il.legend
			FROM `'._DB_PREFIX_.'image` i
			LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (il.id_image=i.id_image AND il.id_lang='.$id_lang.')
			WHERE i.`id_product` = '.intval($id_product).'
			ORDER BY i.position';
		}
		$res=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($res AS $image)
		{
			$sql = '
			SELECT COUNT(*) AS nb
			FROM `'._DB_PREFIX_.'product_attribute_image` pai
			WHERE pai.`id_image` = '.intval($image['id_image']).
			($selection!=''?' AND pai.id_product_attribute IN ('.$selection.')':'').
			' GROUP BY pai.`id_image`';
			$used=Db::getInstance()->getRow($sql);
			$xml.=("<row id='".$image['id_image']."'>");
				$xml.=("<cell style=\"background-color:".($used['nb']==count($selectionArr)?'#7777AA':($used['nb'] < count($selectionArr) && $used['nb'] > 0?'#777777':'#DDDDDD'))."\">".($used['nb']==count($selectionArr)?1:0)."</cell>");
				$xml.=("<cell".($image['cover']?' style="background-color:#7777AA"':'')."><![CDATA[<img src='".SC_PS_PATH_REL."img/p/".getImgPath(intval($id_product),intval($image['id_image']),_s('CAT_PROD_GRID_IMAGE_SIZE'))."'/>".(_s('CAT_PROD_IMG_SAVE_FILENAME') && _s('CAT_PROD_IMG_DISPLAY_FILENAME')?'<br/>'.$image['sc_path']:'')."]]></cell>");
				if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
					$xml.=("<cell><![CDATA[".$image['legend']."]]></cell>");
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

	

	$xml=getRowsFromDB();
?>
<rows id="0">
<head>
<column id="used" width="50" type="ch" align="center" sort="na"><?php echo _l('Used')?></column>
<column id="image" width="120" type="ro" align="center" sort="na"><?php echo _l('Image')?></column>
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) { ?>
<column id="legend" width="300" type="ro" align="left" sort="na"><?php echo _l('Legend')?></column>
<?php } ?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_combi_image').'</userdata>'."\n";
	echo $xml;
?>
</rows>
