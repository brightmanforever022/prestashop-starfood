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

$id_product=Tools::getValue('id_product',1);
$id_product_attribute=Tools::getValue('id_product_attribute',1);
$name=Tools::getValue('name',1);
$reference=Tools::getValue('reference',1);
$supplier_reference=Tools::getValue('supplier_reference',1);
$supplier_reference_all=Tools::getValue('supplier_reference_all',1);
$ean=Tools::getValue('ean',1);
$upc=0;
if (version_compare(_PS_VERSION_, '1.4.0.2', '>='))
	$upc=Tools::getValue('upc',1);
$short_desc = Tools::getValue('short_desc',0);
$desc = Tools::getValue('desc',0);
$limit=25*$nblanguages;
$res='';

$shop_where = "";
if(SCMS)
{
	if(SCI::getSelectedShop()>0)
		$shop_where = " '".(int)SCI::getSelectedShop()."' ";
	else
		$shop_where = " p.id_shop_default ";
}

if(is_numeric($_GET['q'])){
	$sql = "SELECT p.id_product,p.id_category_default,pl.name as pname,cl.name as cname,pl2.name as pname2,pa.id_product_attribute
			".(SCMS ? " ,ps.id_category_default,pas.default_on ":"")."
			FROM `"._DB_PREFIX_."product` p
			LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON (p.id_product=pl.id_product ".(SCMS?"AND pl.id_shop=".$shop_where:"").")
			LEFT JOIN `"._DB_PREFIX_."product_lang` pl2 ON (p.id_product=pl2.id_product AND pl2.id_lang=".intval($sc_agent->id_lang)." ".(SCMS?"AND pl2.id_shop=".$shop_where:"").")
			".(SCMS?" LEFT JOIN `"._DB_PREFIX_."product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop=".$shop_where.") ":"")."
			LEFT JOIN `"._DB_PREFIX_."product_attribute` pa ON (p.id_product=pa.id_product)
			".(SCMS?" LEFT JOIN `"._DB_PREFIX_."product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop=".$shop_where.") ":"")."
			LEFT JOIN `"._DB_PREFIX_."category_lang` cl ON (cl.id_category=".(SCMS?"ps":"p").".id_category_default AND cl.id_lang=".intval($sc_agent->id_lang).")
			".(version_compare(_PS_VERSION_,"1.5.0.0",">=") && $supplier_reference_all==1?" LEFT JOIN `"._DB_PREFIX_."product_supplier` psup ON (psup.id_product=p.id_product) ":"")."
			WHERE (0
				".(($id_product==1)?" OR p.id_product = '".(float)$_GET['q']."'":"")."
				".(($id_product_attribute==1)?" OR pa.id_product_attribute = '".(float)$_GET['q']."'":"")."
				".(($ean==1)?" OR p.ean13 LIKE '%".psql($_GET['q'])."%'":"")."
				".(($ean==1)?" OR pa.ean13 LIKE '%".psql($_GET['q'])."%'":"")."
				".(($reference==1)?" OR p.reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference==1)?" OR p.supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($reference==1)?" OR pa.reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference==1)?" OR pa.supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(version_compare(_PS_VERSION_,"1.4.0.2",">=")&&($upc==1)?" OR p.upc LIKE '%".psql($_GET['q'])."%'":"")."
				".(version_compare(_PS_VERSION_,"1.4.0.2",">=")&&($upc==1)?" OR pa.upc LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference_all==1 && version_compare(_PS_VERSION_,"1.5.0.0",">="))?" OR psup.product_supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				)
				".(SCMS?" AND ps.id_shop=".$shop_where:"")."
			GROUP BY p.id_product
			ORDER BY pl.name ASC,".(SCMS?"pas":"pa").".default_on DESC
			LIMIT ".(int)$limit;
	$res = Db::getInstance()->ExecuteS($sql);
}else{
	$sql = "SELECT p.id_product,p.id_category_default,pl.name as pname,cl.name as cname,pl2.name as pname2,pa.id_product_attribute
			".(SCMS ? " ,ps.id_category_default ":"")."
			FROM `"._DB_PREFIX_."product` p
			LEFT JOIN `"._DB_PREFIX_."product_lang` pl ON (p.id_product=pl.id_product ".(SCMS?"AND pl.id_shop=".$shop_where:"").")
			LEFT JOIN `"._DB_PREFIX_."product_lang` pl2 ON (p.id_product=pl2.id_product AND pl2.id_lang=".intval($sc_agent->id_lang)." ".(SCMS?"AND pl2.id_shop=".$shop_where:"").")
			".(SCMS?" LEFT JOIN `"._DB_PREFIX_."product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop=".$shop_where.") ":"")."
			LEFT JOIN `"._DB_PREFIX_."product_attribute` pa ON (p.id_product=pa.id_product)
			".(SCMS?" LEFT JOIN `"._DB_PREFIX_."product_attribute_shop` pas ON (pas.id_product_attribute = pa.id_product_attribute AND pas.id_shop=".$shop_where.") ":"")."
			LEFT JOIN `"._DB_PREFIX_."category_lang` cl ON (cl.id_category=".(SCMS?"ps":"p").".id_category_default AND cl.id_lang=".intval($sc_agent->id_lang).")
			".(version_compare(_PS_VERSION_,"1.5.0.0",">=") && $supplier_reference_all==1?" LEFT JOIN `"._DB_PREFIX_."product_supplier` psup ON (psup.id_product=p.id_product) ":"")."
			WHERE (0
				".(($reference==1)?" OR p.reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference==1)?" OR p.supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($name==1)?" OR pl.name LIKE '%".psql($_GET['q'])."%'":"")."
				".(($reference==1)?" OR pa.reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference==1)?" OR pa.supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($supplier_reference_all==1 && version_compare(_PS_VERSION_,"1.5.0.0",">="))?" OR psup.product_supplier_reference LIKE '%".psql($_GET['q'])."%'":"")."
				".(($short_desc==1)?" OR pl.description_short LIKE '%".psql($_GET['q'])."%'":"")."
				".(($desc==1)?" OR pl.description LIKE '%".psql($_GET['q'])."%'":"")."
				)
				".(SCMS?" AND ps.id_shop=".$shop_where:"")."
			GROUP BY p.id_product
			ORDER BY pl.name ASC,".(SCMS?"pas":"pa").".default_on DESC
			LIMIT ".(int)$limit;
	$res = Db::getInstance()->ExecuteS($sql);
}

if ($res!='')
{
	$content='';
	$plist=array();
	echo '[';
	foreach($res as $row)
	{
		if (!in_array($row['id_product'],$plist))
		{
			$content.='{"id_category":"'.$row['id_category_default'].'","id_product":"'.$row['id_product'].'","id_product_attribute":"'.(int)$row['id_product_attribute'].'","pname":"'.str_replace("\'",'',addslashes($row['pname2'])).'","cname":"'.str_replace("\'",'',addslashes(hideCategoryPosition($row['cname']))).'"},';
			$plist[]=$row['id_product'];
		}
		if (count($plist)>25) break;
	}
	$content=trim($content,',');
	echo $content;
	echo ']';
}
