<?php

exit;

define('_DMC_ACCESSIBLE',true);
define('_VALID_XTC',true);
define('VALID_DMC',true);

// configurationen
include('conf/definitions.inc.php');
	if (is_file('../includes/application_top_export.php')) require('../includes/application_top_export.php');
	else if (is_file('../../includes/application_top_export.php')) require('../../includes/application_top_export.php');
	else {
		if (is_file('./conf/configure_shop_veyton.php')) require('./conf/configure_shop_veyton.php');
	}
		
include('./functions/dmc_db_functions.php');
include('./functions/dmc_functions.php');

// echo DB_SERVER.", ".DB_SERVER_USERNAME.", ".DB_SERVER_PASSWORD.", ".DB_DATABASE.", ".DB_PORT;
echo '"sku","store_view_code","attribute_set_code","product_type","categories","product_websites","name","description","short_description","weight","product_online","tax_class_name","visibility","price","special_price","special_price_from_date","special_price_to_date","meta_title","meta_keywords","meta_description","base_image","base_image_label","small_image","small_image_label","thumbnail_image","thumbnail_image_label","swatch_image","swatch_image_label","created_at","updated_at","new_from_date","new_to_date","display_product_options_in","additional_attributes","qty","website_id","related_skus","related_position","crosssell_skus","crosssell_position","upsell_skus","upsell_position","additional_images","configurable_variations","configurable_variation_labels","associated_skus","ean","url_key","manufacturer"'."\n";	

$attribute_set_code='Default';
$store_view_code='';
$product_websites='base';
$website_id=1;
$tax_class_name='Vollbesteuerte Artikel';
$tax_class_name_2='';
$visibility="Katalog, Suche";

$query = "SELECT p.products_model AS sku, '$store_view_code' AS store_view_code, '$attribute_set_code' AS attribute_set_code, CASE WHEN p.products_master_flag=0 THEN 'simple' ELSE 'configurable' END AS product_type, LEFT(seo.url_text, CHAR_LENGTH(seo.url_text) - LOCATE('/', REVERSE(seo.url_text))) AS categories, '$product_websites' AS product_websites, pd.products_name AS name, pd.products_description AS description, pd.products_short_description AS short_description,p.products_weight AS weight, p.products_status AS product_online, CASE WHEN p.products_tax_class_id=1 THEN '$tax_class_name' ELSE '$tax_class_name2' END AS tax_class_name, '$visibility' AS visibility, p.products_price AS price, special.specials_price AS special_price, special.date_available AS special_price_from_date, special.date_expired AS special_price_to_date, seo.meta_title AS meta_title, seo.meta_keywords AS meta_keywords, seo.meta_description AS meta_description, p.products_image AS base_image, concat('Bild ',pd.products_name) AS base_image_label, p.products_image AS small_image, concat('Small ',pd.products_name) AS small_image_label, p.products_image AS thumbnail_image, concat('thumbnail ',pd.products_name) AS thumbnail_image_label, CASE WHEN p.products_master_flag=0 THEN p.products_image ELSE '' END AS swatch_image, CASE WHEN p.products_master_flag=0 THEN concat('swatch ',pd.products_name) ELSE '' END AS swatch_image_label, p.date_added AS created_at, p.last_modified AS updated_at, '' AS new_from_date, '' AS new_to_date, (select CONCAT(pa2.attributes_model,'=\"\"',pa.attributes_model,'\"\"')  from `xt_plg_products_to_attributes` pta INNER JOIN xt_plg_products_attributes pa ON pta.attributes_id=pa.attributes_id INNER JOIN xt_plg_products_attributes pa2 ON pta.attributes_parent_id=pa2.attributes_id where pta.products_id=p.products_id) AS additional_attributes, p.products_quantity AS qty, '$website_id' AS website_id, '' AS related_skus, '' AS related_position, '' AS crosssell_skus, '' AS crosssell_position, '' AS upsell_skus, '' AS upsell_position, (SELECT m.file from `xt_media_link` ml INNER JOIN xt_media m ON m.id=ml.m_id WHERE ml.class='product' AND ml.type='images' AND ml.sort_order=1 AND ml.link_id=p.products_id) AS additional_images, (SELECT m.file from `xt_media_link` ml INNER JOIN xt_media m ON m.id=ml.m_id WHERE ml.class='product' AND ml.type='images' AND ml.sort_order=2 AND ml.link_id=p.products_id) AS additional_images2, (SELECT m.file from `xt_media_link` ml INNER JOIN xt_media m ON m.id=ml.m_id WHERE ml.class='product' AND ml.type='images' AND ml.sort_order=3 AND ml.link_id=p.products_id) AS additional_images3, '' AS configurable_variations, '' AS configurable_variation_labels, '' AS associated_skus, p.products_ean AS ean, seo.url_text AS url_path, (select manufacturers_name from `xt_manufacturers` where manufacturers_id=p.manufacturers_id) AS manufacturer FROM  `xt_products` AS p INNER JOIN xt_products_description AS pd ON (p.products_id=pd.products_id AND pd.language_code='de' AND pd.products_store_id=1) INNER JOIN `xt_seo_url` AS seo ON (seo.link_id=p.products_id AND seo.link_type=1 AND seo.language_code='DE' AND seo.store_id=1) LEFT OUTER JOIN `xt_products_price_special` special ON (special.status = 1 AND special.date_expired > now() AND special.products_id=p.products_id) WHERE p.products_status=1 AND (p.products_master_flag=0 OR p.products_master_flag=1)";

			
			$sql_query = dmc_db_query($query);
				while ($r = dmc_db_fetch_array($sql_query)) {	

				
					$string = str_replace("de/","Kölner Jagdhütte/",$r['categories']);
					$r['name']=clean_string($r['name']);
					$r['description']=clean_string($r['description']);
					$r['short_description']=clean_string($r['short_description']);
					$r['meta_description']=clean_string($r['meta_description']);
					$r['meta_title']=clean_string($r['meta_title']);
					$r['meta_description']=clean_string($r['meta_description']);
					if ($r['additional_images2']!="")
						$r['additional_images'] .= ",".$r['additional_images2'];
					if ($r['additional_images3']!="")
						$r['additional_images'] .= ",".$r['additional_images3'];
					
						
					echo '"'.$r['sku'].'","'.$r['store_view_code'].'","'.$r['attribute_set_code'].'","'.$r['product_type'].'","'.$r['categories'].'","'.$r['product_websites'].'","'.$r['name'].'","'.$r['description'].'","'.$r['short_description'].'","'.$r['weight'].'","'.$r['product_online'].'","'.$r['tax_class_name'].'","'.$r['visibility'].'","'.$r['price'].'","'.$r['special_price'].'","'.$r['special_price_from_date'].'","'.$r['special_price_to_date'].'","'.$r['meta_title'].'","'.$r['meta_keywords'].'","'.$r['meta_description'].'","'.$r['base_image'].'","'.$r['base_image_label'].'","'.$r['small_image'].'","'.$r['small_image_label'].'","'.$r['thumbnail_image'].'","'.$r['thumbnail_image_label'].'","'.$r['swatch_image'].'","'.$r['swatch_image_label'].'","'.$r['created_at'].'","'.$r['updated_at'].'","'.$r['new_from_date'].'","'.$r['new_to_date'].'","'.$r['display_product_options_in'].'","'.$r['additional_attributes'].'","'.$r['qty'].'","'.$r['website_id'].'","'.$r['related_skus'].'","'.$r['related_position'].'","'.$r['crosssell_skus'].'","'.$r['crosssell_position'].'","'.$r['upsell_skus'].'","'.$r['upsell_position'].'","'.$r['additional_images'].'","'.$r['configurable_variations'].'","'.$r['configurable_variation_labels'].'","'.$r['associated_skus'].'","'.$r['ean'].'","'.$r['url_path'].'","'.$r['manufacturer'].'"'."\n";
				}


				function clean_string($string) {
					if (strpos($string,'<!--')!==false){
						$string = substr($string,0,strpos($string,'<!--'));
						
					}
					$string = str_replace("\n","",$string);
					$string = str_replace('"','""',$string);
					 
					return $string;
				}
	exit;


?>