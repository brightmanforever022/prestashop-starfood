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
	$selectedProducts=Tools::getValue('id_product');
	$id_category=intval(Tools::getValue('id_category'));
	$filter=intval(Tools::getValue('filter',0));

	$featuresSelection=array();
	$intersectFeatures=array();
	$productList=array();
	if ($filter)
	{
		$sql = '
		SELECT id_product
		FROM `'._DB_PREFIX_.'category_product`
		WHERE id_category='.intval($id_category);
		$res=Db::getInstance()->ExecuteS($sql);
		foreach ($res AS $val)
		{
			$productList[]=intval($val['id_product']);
		}
		$sql = '
		SELECT fp.id_feature
		FROM `'._DB_PREFIX_.'feature_product` fp
			'.((SCMS && SCI::getSelectedShop()>0)?'INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fs.id_feature = fp.id_feature AND fs.id_shop = '.SCI::getSelectedShop().')':"").'
		WHERE fp.id_product IN ('.join(',',$productList).')';
		$res=Db::getInstance()->ExecuteS($sql);
		foreach ($res AS $val)
		{
			$featuresSelection[intval($val['id_feature'])]=intval($val['id_feature']);
		}
	}
	$plist=explode(',',$selectedProducts);
	if (count($plist)>1)
	{
		$sql = '
		SELECT fp.id_feature_value, fp.id_product
		FROM `'._DB_PREFIX_.'feature_product` fp
			'.((SCMS && SCI::getSelectedShop()>0)?'INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fs.id_feature = fp.id_feature AND fs.id_shop = '.SCI::getSelectedShop().')':"").'
		WHERE fp.id_product IN ('.join(',',$plist).')';
		$res=Db::getInstance()->ExecuteS($sql);
		foreach ($res AS $val)
		{
			if (!sc_array_key_exists($val['id_feature_value'],$intersectFeatures))
			{
				$intersectFeatures[$val['id_feature_value']]=1;
			}else{
				$intersectFeatures[$val['id_feature_value']]++;
			}
		}
	}
	$id_product=$plist[0];

	$sql = '
	SELECT f.id_feature,fl.name '.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?',f.position':'').'
	FROM `'._DB_PREFIX_.'feature` f
		LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.id_feature = fl.id_feature AND fl.id_lang = '.intval($id_lang).')
		'.((SCMS && SCI::getSelectedShop()>0)?'INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fs.id_feature = f.id_feature AND fs.id_shop = '.SCI::getSelectedShop().')':"").'
	ORDER BY fl.name';
	$res=Db::getInstance()->ExecuteS($sql);
	$featureValues=array();
	foreach ($res AS $val)
	{
		$featureValues[$val['id_feature']]['name']=$val['name'];
		$featureValues[$val['id_feature']]['id']=$val['id_feature'];
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$featureValues[$val['id_feature']]['position']=$val['position'];
		}
	}

	$sql = '
	SELECT fv.id_feature,fv.custom,fl.name,fvl.id_feature_value,fvl.value AS fvname'.((version_compare(_PS_VERSION_, '1.5.0.0', '>='))?',f.position':'').'
	FROM `'._DB_PREFIX_.'feature_value` fv
	'.((version_compare(_PS_VERSION_, '1.5.0.0', '>='))?'LEFT JOIN `'._DB_PREFIX_.'feature` f ON (f.`id_feature` = fv.`id_feature`)':'').'
	LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (fv.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.intval($id_lang).')
	LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.`id_feature_value` = fvl.`id_feature_value` AND fvl.`id_lang` = '.intval($id_lang).') 
	'.((SCMS && SCI::getSelectedShop()>0)?'INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fs.id_feature = fv.id_feature AND fs.id_shop = '.SCI::getSelectedShop().')':"").'
	'/*.(count($featuresSelection)?' WHERE fv.id_feature IN('.join(',',$featuresSelection).') ':'')*/.'
	WHERE fv.custom = 0 OR fv.custom IS NULL
	ORDER BY '.((version_compare(_PS_VERSION_, '1.5.0.0', '>='))?'f.position ASC,':'').'fl.name ASC,fvl.value ASC';
	$res=Db::getInstance()->ExecuteS($sql);
	foreach ($res AS $val)
	{
		if(empty($featuresSelection) || !empty($featuresSelection[$val['id_feature']]))
		{
			$featureValues[$val['id_feature']]['values'][]=array($val['id_feature_value'],$val['fvname']);
			$featureValues[$val['id_feature']]['name']=$val['name'];
			$featureValues[$val['id_feature']]['id']=$val['id_feature'];
			if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{

				$featureValues[$val['id_feature']]['position']=$val['position'];
			}
		}
	}
	if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		function sortByPos($a, $b)
		{
			if ($a["position"] == $b["position"]) {
				return 0;
			}
			return ($a["position"] < $b["position"]) ? -1 : 1;
		}		
		usort($featureValues, "sortByPos");
	}

	function getRowsFromDB(){
		global $id_lang,$id_product,$featureValues,$languages,$intersectFeatures,$plist,$sql,$featuresSelection;

		// on récupère les valeurs du produit sélectionné
		$sql = '
		SELECT fp.id_feature,fp.id_feature_value,fv.custom,fvl.value,fvl.id_lang'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?',f.position':'').'
		FROM `'._DB_PREFIX_.'feature_product` fp
		LEFT JOIN `'._DB_PREFIX_.'feature_value` fv ON (fv.`id_feature_value` = fp.`id_feature_value`)
		LEFT JOIN `'._DB_PREFIX_.'feature_value_lang` fvl ON (fv.`id_feature_value` = fvl.`id_feature_value`)
		LEFT JOIN `'._DB_PREFIX_.'feature` f ON (f.`id_feature` = fp.`id_feature`)
		'.((SCMS && SCI::getSelectedShop()>0)?'
			INNER JOIN `'._DB_PREFIX_.'product` p ON (fp.`id_product` = p.`id_product`)
			INNER JOIN `'._DB_PREFIX_.'feature_shop` fs ON (fs.id_feature = fp.id_feature AND fs.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')':"").'
		WHERE fp.`id_product` = '.intval($id_product); //  AND fvl.`id_lang` = '.intval($id_lang).')
		$productFeatures=Db::getInstance()->ExecuteS($sql);
		$xml='';
		foreach ($featureValues AS $Fval)
		{
			$Fkey = $Fval['id'];
			if (count($featuresSelection) && !sc_in_array($Fkey,$featuresSelection,"catFeaturePdtGet_featuresSelection"))
				continue;
			$options='<option value="-1">--</option><option value="-2">'._l('Custom').'</option>';
			if (sc_in_array('values',array_keys($Fval),"catFeaturePdtGet_values".$Fkey))
				foreach ($Fval['values'] AS $FVval)
				{
					$options.='<option value="'.$FVval[0].'"><![CDATA['.$FVval[1].']]></option>';
				}
			$productF=array('id_feature_value' => '','custom' => '','value' => '','id_lang' => '');
			if(!empty($productFeatures))
				foreach ($productFeatures AS $productFeaturesValues)
				{
					if ($productFeaturesValues['id_feature']==$Fkey)
					{
						$productF['id_feature_value']=$productFeaturesValues['id_feature_value'];
						$productF['custom']=$productFeaturesValues['custom'];
						$productF['value']=$productFeaturesValues['value'];
						$productF['id_lang']=$productFeaturesValues['id_lang'];
						if ($productF['custom'])
							$productF['custom_'.$productFeaturesValues['id_lang']]=$productFeaturesValues['value'];
					}
				}
			$xml.=("<row id='".$Fkey."'>");
				$xml.=("<cell style=\"color:#999999\">".$Fkey."</cell>");
				$xml.=("<cell><![CDATA[".$Fval['name']."]]></cell>");
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
					$xml.=("<cell><![CDATA[".$Fval['position']."]]></cell>");
				}
				$xml.=("<cell xmlcontent=\"1\" editable=\"0\"><![CDATA[".($productF['custom']?'-2':($productF['id_feature_value']==''?'-1':((sc_array_key_exists($productF['id_feature_value'],$intersectFeatures) && $intersectFeatures[$productF['id_feature_value']]!=count($plist)) || count($plist)>1) ? '-1' : $productF['id_feature_value'] ))."]]>".$options."</cell>");
				foreach($languages AS $lang){
					$xml.=("<cell".($productF['custom'] ? '' : ' type="ro"')."><![CDATA[".(sc_array_key_exists('custom_'.$lang['id_lang'],$productF) ? $productF['custom_'.$lang['id_lang']] : '')."]]></cell>");
				}

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
	<beforeInit>
		<call command="attachHeader"><param><![CDATA[#numeric_filter,#text_filter<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>,#numeric_filter<?php } ?>,#text_filter<?php foreach($languages AS $lang) { ?>,#text_filter<?php } ?>]]></param></call>
	</beforeInit>
<column id="id_feature" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<column id="feature" width="100" type="ro" align="left" sort="str"><?php echo _l('Feature')?></column>
<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
	<column id="position" width="100" type="ro" align="left" sort="int"><?php echo _l('Position')?></column>
<?php } ?>
<column id="id_feature_value" width="100" type="coro" align="left" sort="str"><?php echo _l('Value')?></column>
<?php
	foreach($languages AS $lang){
		echo '<column id="custom_'.$lang['iso_code'].'" width="100" type="edtxt" align="left" sort="str">'. _l('Custom').'_'.$lang['iso_code'].'</column>';
	}
?>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_feature_productfeature').'</userdata>'."\n";
	echo $xml;
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
?>
</rows>
