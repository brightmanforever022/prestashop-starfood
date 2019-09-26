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
$id_lang=(int)Tools::getValue('id_lang');
$idlist=(Tools::getValue('idlist',0));

function getRowsFromDB(){
	global $id_lang,$idlist;
	
	$array_langs = array();
	$langs = Language::getLanguages(false);
	foreach($langs as $lang)
		$array_langs[$lang["id_lang"]] = strtoupper($lang["iso_code"]);
	
	if(SCMS)
	{
		$array_shops = array();
		$shops = Shop::getShops(false);
		foreach($shops as $shop)
		{
			$shop['name'] = str_replace("&", _l('and'), $shop['name']);
			$array_shops[$shop["id_shop"]] = $shop["name"];
		}
	}
	
	$sql = '
		SELECT cl.*'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?", cs.id_shop":'').'
		FROM '._DB_PREFIX_.'cms_lang cl
			'.((!_s("CMS_PAGE_LANGUAGE_ALL"))?" INNER JOIN "._DB_PREFIX_."lang l ON (cl.id_lang = l.id_lang AND l.active = 1)":"").'
			'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" INNER JOIN "._DB_PREFIX_."cms_shop cs ON (cs.id_cms = cl.id_cms) ":'').'
		WHERE cl.id_cms IN ('.pSQL($idlist).')
		ORDER BY cl.id_cms, cl.id_lang';
	if(SCMS)
		$sql .= ",cs.id_shop";
	$res=Db::getInstance()->ExecuteS($sql);
	$xml='';
	foreach ($res AS $row)
	{
		$url = getUrl($row['id_cms'],$row['id_lang'],(SCMS?$row['id_shop']:"0"));
		
		$xml.=("<row id='".$row['id_cms']."_".$row['id_lang'].(SCMS?"_".$row['id_shop']:"")."'>");
			$xml.='<userdata name="url"><![CDATA['.$url.']]></userdata>';
			$xml.=("<cell>".$row['id_cms']."</cell>");
			if (SCMS)
			{
				$xml.=("<cell>".$array_shops[$row['id_shop']]."</cell>");
			}
			$xml.=("<cell>".$array_langs[$row['id_lang']]."</cell>");
			$xml.=("<cell><![CDATA[".$row['link_rewrite']."]]></cell>");
			if (strlen($row['meta_title']) >= _s("CMS_SEO_META_TITLE_COLOR")){
				$xml.=("<cell style='background-color: #FE9730'><![CDATA[".$row['meta_title']."]]></cell>");
				}
			else {
				$xml.=("<cell><![CDATA[".$row['meta_title']."]]></cell>");}
			$xml.=("<cell><![CDATA[".strlen($row['meta_title'])."]]></cell>");
			$xml.=("<cell><![CDATA[".$row['meta_description']."]]></cell>");
			$xml.=("<cell><![CDATA[".strlen($row['meta_description'])."]]></cell>");
			$xml.=("<cell><![CDATA[".$row['meta_keywords']."]]></cell>");
			$xml.=("<cell><![CDATA[".strlen($row['meta_keywords'])."]]></cell>");
		$xml.=("</row>");
	}
	return $xml;
}

$cache_cms = array();
$link = new Link();
function getUrl($id_cms, $id_lang, $id_shop=0)
{
	global $cache_cms,$link;
	
	$url = "";
	if(empty($cache_cms[$id."_".$id_shop]))
	{
		if(SCMS)
			$cache_cms[$id] = new CMS((int)$id_cms, null, (int)$id_shop);
		else
			$cache_cms[$id] = new CMS((int)$id_cms, null);
	}
	$cms = $cache_cms[$id];
	
	$alias = $cms->link_rewrite[$id_lang];

	if (SCMS) {
		$url=$link->getCMSLink($cms, $alias, null, $id_lang, (int)$id_shop);
	} else {
		if (!defined('_PS_BASE_URL_'))
		{
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				define('_PS_BASE_URL_', Tools::getShopDomain(true));
			}elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
			{
				define('_PS_BASE_URL_', Tools::getHttpHost(true));
			}
		}
		if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$url=$link->getCMSLink($cms, $alias, null, $id_lang, (int)$id_shop);
		else
			$url=$link->getCMSLink($cms, $alias, null, $id_lang);
	}
	return $url;
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
<call command="attachHeader"><param><![CDATA[#text_filter<?php if(SCMS){ ?>,#select_filter<?php } ?>,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter]]></param></call>
</beforeInit>
<column id="id_cms" width="40" type="ro" align="right" sort="int"><?php echo _l('ID')?></column>
<?php if(SCMS){ ?>
<column id="shop" width="100" type="ro" align="left" sort="int"><?php echo _l('Shop')?></column>
<?php } ?>
<column id="lang" width="60" type="ro" align="center" sort="str"><?php echo _l('Lang')?></column>
	<column id="link_rewrite" width="120" type="ed" align="left" sort="str"><?php echo _l('Link rewrite')?></column>
	<column id="meta_title" width="120" type="ed" align="left" sort="str"><?php echo _l('META title')?></column>
<column id="meta_title_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META title length')?></column>
<column id="meta_description" width="200" type="ed" align="left" sort="str"><?php echo _l('META description')?></column>
<column id="meta_description_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META description length')?></column>
<column id="meta_keywords" width="120" type="ed" align="left" sort="str"><?php echo _l('META keywords')?></column>
<column id="meta_keywords_width" width="40" type="ro" align="right" sort="str"><?php echo _l('META keywords length')?></column>
<afterInit>
<call command="enableMultiselect"><param>1</param></call>
</afterInit>
</head>
<?php
//  format="%Y-%m-%d 00:00:00"
	echo '<userdata name="uisettings">'.uisettings::getSetting('cms_CmsSeo').'</userdata>'."\n";
	echo $xml;
?>
</rows>
