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


function forceCategoryPathFormat($path)
{
	$tmp=explode('>',$path);
	$tmp=array_map('trim',$tmp);
	return join(' > ',$tmp);
}

$id_cat_root = Configuration::get('PS_ROOT_CATEGORY');

function getCategoryPath($id_shop, $id_category,$path='')
{
	global $categoryNameByID,$categoriesProperties,$id_cat_root,$cachePath;
	if ($id_category!=$id_cat_root)
	{
		if (sc_array_key_exists($id_category,$categoriesProperties)) {
			if (!empty($cachePath[$categoriesProperties[$id_category]['id_parent']])) {
				return $cachePath[$categoriesProperties[$id_category]['id_parent']] . ' > ' . $categoryNameByID[$id_category][$id_shop] . $path;
			} else {
				return getCategoryPath($id_shop, $categoriesProperties[$id_category]['id_parent'], ' > ' . $categoryNameByID[$id_category][$id_shop] . $path);
			}
		} else {
			return trim($path, ' > ');
		}
	}else{
		return trim($path,' > ');
	}
}

$cachePath = array();
$categories=array();
$categoriesProperties=array();
$categoryNameByID=array();
$categoryIDByPath=array();
$categoriesFirstLevel=array();
$categoryLang = array();
function getCats()
{
	global $languages,$categories,$categoriesProperties,$categoryNameByID,$categoryIDByPath,$categoriesFirstLevel,$cachePath;

	$baseurl = _PS_BASE_URL_.__PS_BASE_URI__;

	// Customer group
	$groupsArray = array();
	$sql_groups="SELECT cg.id_category, g.name
						FROM `"._DB_PREFIX_."category_group` cg
							INNER JOIN `"._DB_PREFIX_."group_lang` g ON (cg.id_group=g.id_group AND g.id_lang=".(int)Configuration::get('PS_LANG_DEFAULT').")";
	$res_groups=Db::getInstance()->ExecuteS($sql_groups);
	foreach( $res_groups as $k => $res_group) {
		if(!empty($res_group["name"])) {
			$groupsArray[$res_group['id_category']][] = "," . $res_group["name"];
		}
	}

	// Category data
	$sql="SELECT c.*, cl.* ".(SCMS?",cs.id_shop":"")."
							FROM "._DB_PREFIX_."category c
							".(SCMS?" LEFT JOIN "._DB_PREFIX_."category_shop cs ON (cs.id_category=c.id_category) ":"")."
								LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".(int)Configuration::get('PS_LANG_DEFAULT').(SCMS?" AND cl.id_shop=cs.id_shop ":"").")
							WHERE
								c.id_category != '".(int)Configuration::get('PS_ROOT_CATEGORY')."'
							GROUP BY c.id_category ".(SCMS?",cs.id_shop":"")."
							ORDER BY c.id_category ASC ".(SCMS?",cs.id_shop ASC":"");
	$res=Db::getInstance()->ExecuteS($sql);

	// Category lang
	$categLangSql = 'SELECT *
				FROM '._DB_PREFIX_.'category_lang
				GROUP BY id_category, id_shop, id_lang';
	$categLangRes = Db::getInstance()->ExecuteS($categLangSql);
	foreach ($categLangRes as $category) {
		$categoryLang[$category['id_category']][$category['id_shop']]['name'][$category['id_lang']] = $category['name'];
		$categoryLang[$category['id_category']][$category['id_shop']]['description'][$category['id_lang']] = $category['description'];
		$categoryLang[$category['id_category']][$category['id_shop']]['link_rewrite'][$category['id_lang']] = $category['link_rewrite'];
		$categoryLang[$category['id_category']][$category['id_shop']]['meta_title'][$category['id_lang']] = $category['meta_title'];
		$categoryLang[$category['id_category']][$category['id_shop']]['meta_description'][$category['id_lang']] = $category['meta_description'];
		$categoryLang[$category['id_category']][$category['id_shop']]['meta_keywords'][$category['id_lang']] = $category['meta_keywords'];
	}

	foreach($res AS $categ)
	{
		$categories[trim(hideCategoryPosition($categ['name']))]=array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
		$categoryNameByID[$categ['id_category']][$categ['id_shop']]=hideCategoryPosition($categ['name']);
		$categoriesProperties[$categ['id_category']]=array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent'], 'id_shop' => $categ['id_shop']);

	}

	foreach($res AS $categ) {
		if ($categ['id_category'] == $categ['id_parent']) {
			die(_l('A category cannot be parent of itself, you must fix this error for category ID') . ' ' . $categ['id_category'] . ' - ' . trim(hideCategoryPosition($categ['name'])));
		}

		$path = forceCategoryPathFormat(getCategoryPath($categ['id_shop'], $categ['id_category']));
		$cachePath[$categ['id_category']] = $path;
		if ($categ['level_depth'] == 1) {
			$categoriesFirstLevel[] = hideCategoryPosition($categ['name']);
		}

		if(empty($categ['id_shop']))
			$categ['id_shop'] = 0;

		$image_name = "";
		$image_path = _PS_CAT_IMG_DIR_.(int)$categ['id_category'].'.jpg';
		if(file_exists($image_path))
			$image_name = $baseurl.'img/c/'.(int)$categ['id_category'].'.jpg';

		$groups = "";
		foreach ($groupsArray[$categ['id_category']] as $oneGroup) {
			$groups .= $oneGroup;
		}

		echo "<row id=\"".$categ['id_category']."_".$categ['id_shop']."\">";
		echo 		"<cell><![CDATA[".$categ['id_category']."]]></cell>";
		if(SCMS) {
			echo 		"<cell><![CDATA[".$categ['id_shop']."]]></cell>";
			echo 		"<cell><![CDATA[".$categ['id_shop_default']."]]></cell>";
		}
		echo 		"<cell><![CDATA[".$path."]]></cell>";
		echo 		"<cell><![CDATA[".$categ['active']."]]></cell>";
		echo 		"<cell><![CDATA[".trim($groups,",")."]]></cell>";
		echo 		"<cell><![CDATA[".$image_name."]]></cell>";
		foreach($languages AS $lang) {
			echo 		"<cell><![CDATA[".$categoryLang[$categ['id_category']][$categ['id_shop']]['name'][$lang['id_lang']]."]]></cell>";
			echo 		"<cell><![CDATA[".str_replace("\t","",str_replace("\n","",str_replace("\r","",$categoryLang[$categ['id_category']][$categ['id_shop']]['description'][$lang['id_lang']])))."]]></cell>";
			echo 		"<cell><![CDATA[".$categoryLang[$categ['id_category']][$categ['id_shop']]['link_rewrite'][$lang['id_lang']]."]]></cell>";
			echo 		"<cell><![CDATA[".$categoryLang[$categ['id_category']][$categ['id_shop']]['meta_title'][$lang['id_lang']]."]]></cell>";
			echo 		"<cell><![CDATA[".$categoryLang[$categ['id_category']][$categ['id_shop']]['meta_description'][$lang['id_lang']]."]]></cell>";
			echo 		"<cell><![CDATA[".$categoryLang[$categ['id_category']][$categ['id_shop']]['meta_keywords'][$lang['id_lang']]."]]></cell>";
		}
		echo "</row>";
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
	<beforeInit>
		<call command="attachHeader"><param><![CDATA[#numeric_filter<?php if(SCMS) { ?>,#select_filter,#select_filter<?php } ?>,#text_filter,#select_filter,#text_filter,#text_filter<?php foreach($languages AS $lang) { ?>,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter<?php } ?>]]></param></call>
	</beforeInit>
	<column id="id_category" width="60" type="ro" align="left" sort="na"><?php echo _l('id_category')?></column>
	<?php if(SCMS) { ?>
		<column id="id_shop" width="60" type="ro" align="left" sort="na"><?php echo _l('id_shop')?></column>
		<column id="id_shop_default" width="60" type="ro" align="left" sort="na"><?php echo _l('id_shop_default')?></column>
	<?php } ?>
	<column id="path" width="200" type="ro" align="left" sort="na"><?php echo _l('complete path')?></column>
	<column id="active" width="40" type="ro" align="left" sort="na"><?php echo _l('active')?></column>
	<column id="customergroups" width="120" type="ro" align="left" sort="na"><?php echo _l('customer groups')?></column>
	<column id="imageURL" width="120" type="ro" align="left" sort="na"><?php echo _l('imageURL')?></column>

	<?php foreach($languages AS $lang) { ?>
		<column id="name" width="120" type="ro" align="left" sort="na"><?php echo _l('name')." ".$lang["iso_code"]; ?></column>
		<column id="description" width="120" type="ro" align="left" sort="na"><?php echo _l('description')." ".$lang["iso_code"]; ?></column>
		<column id="link_rewrite" width="120" type="ro" align="left" sort="na"><?php echo _l('link_rewrite')." ".$lang["iso_code"]; ?></column>
		<column id="meta_title" width="120" type="ro" align="left" sort="na"><?php echo _l('meta_title')." ".$lang["iso_code"]; ?></column>
		<column id="meta_description" width="120" type="ro" align="left" sort="na"><?php echo _l('meta_description')." ".$lang["iso_code"]; ?></column>
		<column id="meta_keywords" width="120" type="ro" align="left" sort="na"><?php echo _l('meta_keywords')." ".$lang["iso_code"]; ?></column>
	<?php } ?>
</head>
<?php
	getCats();
	echo '</rows>';
?>
