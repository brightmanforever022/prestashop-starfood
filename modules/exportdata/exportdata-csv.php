<?php
/**
 * Export Data Module
 *
 * @version  1.4.4
 * @date  29-10-2014
 *
 *
 * @author    azelab
 * @copyright All rights by azelab
 * @license   Commercial license
 * Support by mail: support@azelab.com
 * Skype: eibrahimov
 */

include_once(dirname(__FILE__) . '../../../config/config.inc.php');
include_once(dirname(__FILE__) . '../../../config/settings.inc.php');
include_once(dirname(__FILE__) . '../../../classes/Cookie.php');
include_once(dirname(__FILE__) . '../../../init.php');
include_once(dirname(__FILE__) . '../../../classes/AdminTab.php');
error_reporting(E_ALL ^ E_NOTICE);
$token = Tools::getAdminTokenLite('AdminModules');
if (!isset($_REQUEST['token']) && $_REQUEST['token'] !== $token)
    exit;
if (Tools::getIsset($_POST['export'])) {
    $delimiter = $_REQUEST['delimiter'];
    $multi_delimiter = $_REQUEST['multi_delimiter'];
    $image_url = ((!empty($_REQUEST['image_url'])) ? $_REQUEST['image_url']
        : 'http://' . $_SERVER['HTTP_HOST'] . _THEME_PROD_DIR_);
    $lang = (int)$_REQUEST['lang'];
    $entity = $_REQUEST['entity'];
    $entities = array(
        array(
            'name' => 'categories',
            'fieldsName' => array(
                'ID',
                'Active (0/1)',
                'Name *',
                'Parent category',
                'Root category (0/1)',
                'Description',
                'Meta title',
                'Meta keywords',
                'Meta description',
                'URL rewritten',
                'Image URL',
                'ID / Name of shop',
            ),
            'sql' => 'SELECT
								c.id_category AS \'ID\',
								c.active AS \'Active\',
								cl.name AS \'Name\',
								c.id_parent AS \'Parent category\',
								(select 0) AS \'Root category\',
								cl.description AS \'Description\',
								cl.meta_title AS \'Meta title\',
								cl.meta_keywords AS \'Meta keywords\',
								cl.meta_description AS \'Meta description\',
								cl.link_rewrite AS \'URL rewritten\',
								(select \'\') AS \'Image URL\',
								(select \'\') AS \'ID / Name of shop\'
							FROM
								' . _DB_PREFIX_ . 'category AS c
									INNER JOIN
								' . _DB_PREFIX_ . 'category_lang AS cl ON c.id_category = cl.id_category AND cl.id_lang = ' . $lang . '
								ORDER BY c.level_depth ASC;',
            'function' => 'generateCategory'
        ),
        array(
            'name' => 'products',
            'fieldsName' => array(
                'ID',
                'Active (0/1)',
                'Name *',
                'Categories (x,y,z...)',
                'Price tax excluded or Price tax included',
                'Tax rules ID',
                'Wholesale price',
                'On sale (0/1)',
                'Discount amount',
                'Discount percent',
                'Discount from (yyyy-mm-dd)',
                'Discount to (yyyy-mm-dd)',
                'Reference #',
                'Supplier reference #',
                'Supplier',
                'Manufacturer',
                'EAN13',
                'UPC',
                'Ecotax',
                'Weight',
                'Quantity',
                'Short description',
                'Description',
                'Tags (x,y,z...)',
                'Meta title',
                'Meta keywords',
                'Meta description',
                'URL rewritten',
                'Text when in stock',
                'Text when backorder allowed',
                'Available for order (0 = No, 1 = Yes)',
                'Product creation date',
                'Show price (0 = No, 1 = Yes)',
                'Image URLs (x,y,z...)',
                'Delete existing images (0 = No, 1 = Yes)',
                'Feature(Name:Value:Position)',
                'Available online only (0 = No, 1 = Yes)',
                'Condition',
                'ID / Name of shop',
            ),
            'sql' => "SELECT
							product.id_product AS 'ID',
							product.active AS 'Active',
							lang.name AS 'Name',
							(SELECT
									group_concat(cat.id_category
											SEPARATOR '" . $multi_delimiter . "')
								FROM
									" . _DB_PREFIX_ . "category_product AS cat
										INNER JOIN
									" . _DB_PREFIX_ . "category_lang AS catlang
								WHERE
									catlang.id_category = cat.id_category
										AND id_product = product.id_product
										AND catlang.id_lang = $lang) AS 'Categories (x,y,z...)',
							product.price AS 'Price tax excluded or Price tax included',
							product.id_tax_rules_group AS 'Tax rules ID',
							product.wholesale_price AS 'Wholesale price',
							product.on_sale AS 'On sale (0/1)',
							specprice.price AS 'Discount amount',
							specprice.reduction AS 'Discount percent',
							specprice.from AS 'Discount from (yyyy-mm-dd)',
							specprice.to AS 'Discount to (yyyy-mm-dd)',
							product.reference AS 'Reference #',
							product.supplier_reference AS 'Supplier reference #',
							supplier.name AS 'Supplier',
							manufacturer.name AS 'Manufacturer',
							product.ean13 AS 'EAN13',
							product.upc AS 'UPC',
							product.ecotax AS 'Ecotax',
							product.weight AS 'Weight',
							product.quantity AS 'Quantity',
							lang.description_short AS 'Short description',
							lang.description AS 'Description',
							(SELECT
									group_concat(tag.name
											SEPARATOR '" . $multi_delimiter . "')
								FROM
									" . _DB_PREFIX_ . "tag AS tag
										INNER JOIN
									" . _DB_PREFIX_ . "product_tag AS protag
								WHERE
									protag.id_product = product.id_product
										AND tag.id_tag = protag.id_tag
										AND tag.id_lang = $lang) AS 'Tags (x,y,z...)',
							lang.meta_title AS 'Meta title',
							lang.meta_keywords AS 'Meta keywords',
							lang.meta_description AS 'Meta description',
							lang.link_rewrite AS 'URL rewritten',
							lang.available_now AS 'Text when in stock',
							lang.available_later AS 'Text when backorder allowed',
							product.available_for_order AS 'Available for order (0 = No, 1 = Yes)',
							product.date_add AS 'Product creation date',
							product.show_price AS 'Show price (0 = No, 1 = Yes)',
							(select '') AS 'Image URLs (x,y,z...)',
							(SELECT '1') AS 'Delete existing images (0 = No, 1 = Yes)',
							(SELECT '1') AS 'Feature(Name:Value:Position)',
							product.online_only AS 'Available online only (0 = No, 1 = Yes)',
							product.condition AS 'Condition',
							(select '') AS 'ID / Name of shop'
						FROM
							" . _DB_PREFIX_ . "product AS product
								LEFT JOIN
							" . _DB_PREFIX_ . "product_lang AS lang ON product.id_product = lang.id_product
								AND lang.id_lang = $lang
								LEFT JOIN
							" . _DB_PREFIX_ . "supplier AS supplier ON product.id_supplier = supplier.id_supplier
								LEFT JOIN
							" . _DB_PREFIX_ . "manufacturer AS manufacturer ON product.id_manufacturer = manufacturer.id_manufacturer
								LEFT JOIN
							" . _DB_PREFIX_ . "category_product AS cat ON product.id_product = cat.id_product
								LEFT JOIN
							" . _DB_PREFIX_ . "specific_price AS specprice ON specprice.id_product = product.id_product
						GROUP BY product.id_product
						ORDER BY product.id_product ASC;",
            'function' => 'generateProduct'
        ),
        array(
            'name' => 'combinations',
            'fieldsName' => array(
                'Product ID*',
                'Attribute (Name:Type:Position)*',
                'Value (Value:Position)*',
                'Supplier reference',
                'Reference',
                'EAN13',
                'UPC',
                'Wholesale price',
                'Impact on price',
                'Ecotax',
                'Quantity',
                'Minimal quantity',
                'Impact on weight',
                'Default (0 = No, 1 = Yes)',
                'Image position',
                'Image URL',
                'Delete existing images (0 = No, 1 = Yes)',
                'ID / Name of shop',
            ),
            'sql' => "SELECT
									pa.id_product AS 'Product ID',
									pa.id_product_attribute AS 'Product Attribute ID',
									group_concat(CONCAT(agl.name,
												':',
												IF(ag.is_color_group = 1,
													'color',
													'select'),
												':',
												'1')
										SEPARATOR '" . $multi_delimiter . "') AS 'Attribute (Name:Type:Position)',
									group_concat(CONCAT(al.name,
												':',
												'1')
										SEPARATOR '" . $multi_delimiter . "') AS 'Value (Value:Position)',
									pa.supplier_reference AS 'Supplier reference',
									pa.reference AS 'Reference',
									pa.ean13 AS 'EAN13',
									pa.upc AS 'UPC',
									pa.wholesale_price AS 'Wholesale price',
									pa.price AS 'Impact on price',
									pa.ecotax AS 'Ecotax',
									pa.quantity AS 'Quantity',
									pa.minimal_quantity AS 'Minimal quantity',
									pa.weight AS 'Impact on weight',
									pa.default_on AS 'Default (0 = No, 1 = Yes)',
									(SELECT '') AS 'Image position',
									(SELECT '') AS 'Image URL',
									(SELECT '0') AS 'Delete existing images (0 = No, 1 = Yes)',
									(SELECT '') AS 'ID / Name of shop'
								FROM
									" . _DB_PREFIX_ . "product_attribute AS pa
										LEFT JOIN
									" . _DB_PREFIX_ . "product_attribute_combination pac ON pac.id_product_attribute = pa.id_product_attribute
										LEFT JOIN
									" . _DB_PREFIX_ . "attribute a ON a.id_attribute = pac.id_attribute
										LEFT JOIN
									" . _DB_PREFIX_ . "attribute_group ag ON ag.id_attribute_group = a.id_attribute_group
										LEFT JOIN
									" . _DB_PREFIX_ . "attribute_lang al ON (a.id_attribute = al.id_attribute
										AND al.id_lang = $lang)
										LEFT JOIN
									" . _DB_PREFIX_ . "attribute_group_lang agl ON (ag.id_attribute_group = agl.id_attribute_group
										AND agl.id_lang = $lang)
								GROUP BY pa.id_product_attribute
								ORDER BY pa.id_product, agl.name;",
            'function' => 'generateCombination'
        ),
        array(
            'name' => 'customers',
            'fieldsName' => array(
                'ID',
                'Active (0/1)',
                'Titles ID (Mr = 1, Ms = 2, else 0)',
                'Email *',
                'Password *',
                'Birthday (yyyy-mm-dd)',
                'Last Name *',
                'First Name *',
                'Newsletter (0/1)',
                'Opt-in (0/1)',
                'ID / Name of shop',
            ),
            'sql' => "SELECT
							c.id_customer AS 'ID',
							c.active AS 'Active',
							c.id_gender AS 'Titles',
							c.email AS 'Email',
							c.passwd AS 'Password',
							c.birthday AS 'Birthday',
							c.lastname AS 'Last Name',
							c.firstname AS 'First Name',
							c.newsletter AS 'Newsletter',
							c.optin AS 'Opt-in',
							(select '') AS 'ID / Name of shop'
						FROM
							" . _DB_PREFIX_ . "customer AS c
						ORDER BY c.id_customer ASC;"
        ),
        array(
            'name' => 'addresses',
            'fieldsName' => array(
                'ID',
                'Alias',
                'Active (0/1)',
                'Customer email',
                'Customer ID',
                'Manufacturer',
                'Supplier',
                'Company',
                'Last Name',
                'First Name',
                'Address 1',
                'Address 2',
                'Postal code / Zipcode',
                'City',
                'Country',
                'State',
                'Other',
                'Phone',
                'Mobile Phone',
                'VAT number',
            ),
            'sql' => "SELECT
								a.id_address AS 'ID',
								a.alias AS 'Alias',
								a.active AS 'Active',
								c.email AS 'Customer email',
								a.id_customer AS 'Customer ID',
								a.id_manufacturer AS 'Manufacturer',
								a.id_supplier AS 'Supplier',
								a.company AS 'Company',
								a.lastname AS 'Last Name',
								a.firstname AS 'First Name',
								a.address1 AS 'Address 1',
								a.address2 AS 'Address 2',
								a.postcode AS 'Postal code / Zipcode',
								a.city AS 'City',
								a.id_country AS 'Country',
								a.id_state AS 'State',
								a.other AS 'Other',
								a.phone AS 'Phone',
								a.phone_mobile AS 'Mobile Phone',
								a.vat_number AS 'VAT number'
							FROM
								" . _DB_PREFIX_ . "address AS a
							INNER JOIN " . _DB_PREFIX_ . "customer AS c ON a.id_customer=c.id_customer
							ORDER BY a.id_address ASC;"
        ),
        array(
            'name' => 'manufacturers',
            'fieldsName' => array(
                'ID',
                'Active (0/1)',
                'Name *',
                'Description',
                'Short description',
                'Meta title',
                'Meta keywords',
                'Meta description',
                'ID / Name of group shop',
            ),
            'sql' => "SELECT
								m.id_manufacturer AS 'ID',
								m.active AS 'Active',
								m.name AS 'Name',
								ml.description AS 'Description',
								ml.meta_title AS 'Meta title',
								ml.meta_keywords AS 'Meta keywords',
								ml.meta_description AS 'Meta description',
								(select '') AS 'ID / Name of shop'
							FROM
								" . _DB_PREFIX_ . "manufacturer AS m
									INNER JOIN
								" . _DB_PREFIX_ . "manufacturer_lang AS ml ON m.id_manufacturer = ml.id_manufacturer
									AND ml.id_lang = $lang
							ORDER BY m.id_manufacturer ASC;"
        ),
        array(
            'name' => 'suppliers',
            'fieldsName' => array(
                'ID',
                'Active (0/1)',
                'Name *',
                'Description',
                'Short description',
                'Meta title',
                'Meta keywords',
                'Meta description',
                'ID / Name of group shop',
            ),
            'sql' => "SELECT
							s.id_supplier AS 'ID',
							s.active AS 'Active',
							s.name AS 'Name',
							sl.description AS 'Description',
							sl.meta_title AS 'Meta title',
							sl.meta_keywords AS 'Meta keywords',
							sl.meta_description AS 'Meta description',
							(select '') AS 'ID / Name of shop'
						FROM
							" . _DB_PREFIX_ . "supplier AS s
								INNER JOIN
							" . _DB_PREFIX_ . "supplier_lang AS sl ON s.id_supplier = sl.id_supplier
								AND sl.id_lang = $lang
						ORDER BY s.id_supplier ASC;"
        ),
    );
    $file_name = Tools::strtolower($entities[$entity]['name']) . '_' . LanguageCore::getIsoById($lang) . '.csv';
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header('Content-Description: File Transfer');
    //        header("Content-type: text/csv");
    header("content-type:application/csv;charset=UTF-8");
    header("Content-Disposition: attachment; filename={$file_name}");
    header("Expires: 0");
    header("Pragma: public");
    header('Content-Encoding: UTF-8');
    echo "\xEF\xBB\xBF"; // UTF-8 BOM
    $f = @fopen('php://output', 'w');
    fwrite($f, implode($delimiter, $entities[$entity]['fieldsName']) . "\r\n");
    switch ($entities[$entity]['function']) {
        case "generateCombination":
            generateCombination($entities[$entity], $f, $image_url);
            break;
        case "generateProduct":
            generateProduct($entities[$entity], $f, $image_url);
            break;
        case "generateCategory":
            generateCategory($entities[$entity], $f, $delimiter);
            break;
        default:
            $list = Db::getInstance()->ExecuteS($entities[$entity]['sql']);
            foreach ($list as $fields) {
                fputcsv($f, $fields, $delimiter, '"');
            }
    }
    fclose($f);
}

function generateCategory($entity, $f, $delimiter)
{
    $list = Db::getInstance()->ExecuteS($entity['sql']);
    $sql = 'SELECT MAX(id_category) FROM ' . _DB_PREFIX_ . 'category';
    $maxID = Db::getInstance()->getValue($sql);

    $link = new Link();
    foreach ($list as $fieldsValue) {

        if ($fieldsValue["ID"] == "1") {
            continue;
        } elseif ($fieldsValue["ID"] == "2") {
            $fieldsValue["ID"] = (int)$maxID + 1;
        }

        if ($fieldsValue["Parent category"] == "2")
            $fieldsValue["Parent category"] = (int)$maxID + 1;

        //load category Image
        $category = new Category($fieldsValue["ID"], (int)$_REQUEST['lang']);

        if ($category->id_image) {
            if (version_compare(_PS_VERSION_, '1.5', '>=')){
	            $img_path = $link->getCatImageLink($category->link_rewrite, (int)$category->id_image);
	            $imageUrl = ((strpos($img_path, 'http')) !== false)?$img_path:'http://'.$img_path;
	            $fieldsValue['Image URL'] = $imageUrl;
            }
            else{
	            $img_path = _PS_BASE_URL_ . $link->getCatImageLink($category->link_rewrite, (int)$category->id_image);
	            $imageUrl = ((strpos($img_path, 'http')) !== false)?$img_path:'http://'.$img_path;
	            $fieldsValue['Image URL'] = $imageUrl;
            }

        }

        fputcsv($f, $fieldsValue, $delimiter, '"');
    }
}

function generateProduct($entity, $f, $image_url)
{
	$sql = 'SELECT MAX(id_category) FROM ' . _DB_PREFIX_ . 'category';
	$maxID = Db::getInstance()->getValue($sql);
    $rows = Db::getInstance()->ExecuteS($entity['sql']);
    foreach ($rows as $row) {

	   if (strpos($row["Categories (x,y,z...)"], '2'))
	   {

		    $row["Categories (x,y,z...)"] = str_replace('2', (int)$maxID + 1, $row["Categories (x,y,z...)"]);// (int)$maxID + 1;
	   }

	    if($row['Discount amount'] == -1)
		    $row['Discount amount'] = null;

        $img_sql
            = 'SELECT id_image
						FROM ' . _DB_PREFIX_ . 'image
						WHERE id_product=' . $row["ID"] . ';';
        $images = Db::getInstance()->ExecuteS($img_sql);
        foreach ($images as $image) {
            $link = new Link();
			$sp = (empty($row['Image URLs (x,y,z...)'])) ? "" : ", ";
	        $img_path = $link->getImageLink($row['URL rewritten'], (int)$row['ID'].'-'.(int)$image['id_image']);
	        $ab = ((strpos($img_path, 'http')) !== false)?$img_path:'http://'.$img_path;
			$row['Image URLs (x,y,z...)'] .= $sp . $ab;
        }
        fputcsv($f, $row, ';', '"');
    }
}

function generateCombination($entity, $f, $image_url)
{
    $rows = Db::getInstance()->ExecuteS($entity['sql']);
    foreach ($rows as $row) {
        $img_sql
            = 'SELECT *
						FROM fy_product_attribute_image AS pai
						LEFT JOIN fy_image AS i ON pai.id_image=i.id_image
						WHERE pai.id_product_attribute=' . $row["Product Attribute ID"] . ';';
        $images = Db::getInstance()->ExecuteS($img_sql);


        foreach ($images as $image) {
            $row['Image position'] .= $image['position'] . ',';
            $row['Image URL'] .= $image_url . $image['id_product'] . '-' . $image['id_image'] . '.jpg' . ',';

        }
        unset($row["Product Attribute ID"]);
        fputcsv($f, $row, ';', '"');
    }
}

?>