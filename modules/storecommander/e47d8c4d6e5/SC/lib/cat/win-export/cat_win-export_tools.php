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

	function getExportCSVFields()
	{
		global $array;
		$array=array();
		$array[_l('active',1)]="active";
		$array[_l('quantity',1)]="quantity";
		$array[_l('name',1)]="name";
		$array[_l('name_with_attributes',1)]="name_with_attributes";
		$array[_l('description',1)]="description";
		$array[_l('description_short',1)]="description_short";
		$array[_l('meta_title',1)]="meta_title";
		$array[_l('meta_description',1)]="meta_description";
		$array[_l('meta_keywords',1)]="meta_keywords";
		$array[_l('link_rewrite',1)]="link_rewrite";
		$array[_l('available_now',1)]="available_now";
		$array[_l('available_later',1)]="available_later";
		$array[_l('out_of_stock',1)]="out_of_stock";
		$array[_l('reference',1)]="reference";
		$array[_l('supplier_reference',1)]="supplier_reference";
		$array[_l('supplier - default',1)]="supplier";
		$array[_l('manufacturer',1)]="manufacturer";
		$array[_l('wholesale_price',1)]="wholesale_price";
		$array[_l('ecotax',1)]="ecotax";
		$array[_l('ecotax tax incl.',1)]="ecotax_taxincl";
		$array[_l('priceinctax_with_reduction',1)]="priceinctax";
		$array[_l('priceexctax_with_reduction',1)]="priceexctax";
		$array[_l('priceinctax',1)]="price_inctax_without_reduction";
		$array[_l('priceexctax',1)]="price_exctax_without_reduction";
		$array[_l('vat',1)]="vat";
		$array[_l('ean13',1)]="ean13";
		$array[_l('weight',1)]="weight";
		$array[_l('on_sale',1)]="on_sale";
		$array[_l('reduction_price',1)]="reduction_price";
		$array[_l('reduction_percent',1)]="reduction_percent";
		$array[_l('reduction_from',1)]="reduction_from";
		$array[_l('reduction_to',1)]="reduction_to";
		$array[_l('location',1)]="location";
		$array[_l('category by default',1)]="category_default";
		$array[_l('category by default (full path)',1)]="category_default_full_path";
		$array[_l('categories (full path)',1)]="category_full_path";
		$array[_l('categories',1)]="categories";
		$array[_l('tags',1)]="tags";
		$array[_l('id_manufacturer',1)]="id_manufacturer";
		$array[_l('id_supplier',1)]="id_supplier";
		$array[_l('id_category_default',1)]="id_category_default";
		$array[_l('id_category(s)',1)]="id_category(s)";
		$array[_l('id_product',1)]="id_product";
		$array[_l('id_product-id_attribute',1)]="id_product-id_attribute";
		$array[_l('id_product_attribute',1)]="id_product_attribute";
		$array[_l('feature',1)]="feature";
		$array[_l('accessories',1)]="accessories";
	//	$array[_l('id_product_attribute',1)]="id_product_attribute','id_product_attribute');";
		$array[_l('attribute of combination',1)]="attribute";
	//	$array[_l('attribute of combination - color value',1)]="attribute_color";
	//	$array[_l('attribute of combination - texture',1)]="attribute_texture";
		$array[_l('link_to_product',1)]="link_to_product";
		$array[_l('link_to_cover_image',1)]="link_to_cover_image";
		/*$array[_l('link_to_image01',1)]="link_to_image01";
		$array[_l('link_to_image02',1)]="link_to_image02";
		$array[_l('link_to_image03',1)]="link_to_image03";
		$array[_l('link_to_image04',1)]="link_to_image04";
		$array[_l('link_to_image05',1)]="link_to_image05";
		$array[_l('link_to_image06',1)]="link_to_image06";
		$array[_l('link_to_image07',1)]="link_to_image07";
		$array[_l('link_to_image08',1)]="link_to_image08";
		$array[_l('link_to_image09',1)]="link_to_image09";
		$array[_l('link_to_image10',1)]="link_to_image10";*/
		$array[_l('image_link (1 image)',1)]="image_link";
		$array[_l('image_url (1 image)',1)]="image_url";
		$array[_l('image_legend (1 image)',1)]="image_legend";
        $array[_l('image_id (1 image)',1)]="image_id";
        $array[_l('image_default_id',1)]="image_default_id";
		$array[_l('images : links_to_all',1)]="links_to_all_images";
		$array[_l('images : urls_to_all',1)]="urls_to_all_images";
		$array[_l('images : id_to_all',1)]="image_id_all";
		$array[_l('images : links_to_all_for_product',1)]="links_to_all_images_for_product";
		$array[_l('images : urls_to_all_for_product',1)]="urls_to_all_images_for_product";
		$array[_l('images : id_to_all_for_product',1)]="image_id_all_for_product";
		$array[_l('_fixed_value',1)]="_fixed_value";
        $array[_l('stock_value',1)]="stock_value";
        $array[_l('stock_value with reduction',1)]="stock_value_with_reduction";
		$array[_l('stock_value_wholesale',1)]="stock_value_wholesale";
		$array[_l('availability_message',1)]="availability_message";
		$array[_l('price impact of combination',1)]="price_impact";
		$array[_l('weight impact of combination',1)]="weight_impact";
		$array[_l('date_add',1)]="date_add";
		$array[_l('date_upd',1)]="date_upd";
		$array[_l('attribute of combination - default combination',1)]="default_on";
        $array[_l('attribute of combination - color value',1)]="attribute_color";
        $array[_l('attribute of combination - texture',1)]="attribute_texture";
		$array[_l('attachments',1)]="attachments";
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			$array[_l('priceinctaxwithshipping',1)]="priceinctaxwithshipping";
			$array[_l('productshippingcost',1)]="productshippingcost";
			$array[_l('upc',1)]="upc";
			$array[_l('minimum quantity',1)]="minimal_quantity";
			$array[_l('available for order',1)]="available_for_order";
			$array[_l('show price',1)]="show_price";
			$array[_l('online only (not sold in store)',1)]="online_only";
			$array[_l('condition',1)]="condition";
			$array[_l('unit (for unit price)',1)]="unity";
			$array[_l('unit price',1)]="unit_price";
			$array[_l('unit_price_impact',1)]="unit_price_impact";
			$array[_l('width',1)]="width";
			$array[_l('height',1)]="height";
			$array[_l('depth',1)]="depth";
		}
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$array[_l('redirect_type',1)]="redirect_type";
			$array[_l('redirect_id_product',1)]="id_product_redirected";
			$array[_l('available date',1)]="available_date";
			$array[_l('carriers',1)]="carriers";
			$array[_l('visibility',1)]="visibility";
            $array[_l('suppliers',1)]="suppliers";
		}
		if (SCMS)
		{
			$array['id_shop_default']="id_shop_default";
			$array['id_shop_list']="id_shop_list";
		}
		if (SCAS)
		{
			$array[_l('stock - advanced stock mgmt.',1)]="advanced_stock_management";
			$array[_l('physical stock',1)]="quantity_physical";
			$array[_l('available stock',1)]="quantity_usable";
			$array[_l('live stock',1)]="quantity_real";
			$array[_l('total physical stock',1)]="quantity_total_physical";
			$array[_l('total available stock',1)]="quantity_total_usable";
		}
        if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
        {
            $array[_l('reduction_tax', 1)] = "reduction_tax";
        }
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
        {
            $array[_l('show condition', 1)] = "show_condition";
            $array[_l('isbn', 1)] = "isbn";
        }
		$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);
		if(!empty($sc_active))
			$array[_l('combination - used',1)]="sc_active";
		$array[_l('margin',1)]="margin";
		$array[_l('last order',1)]="last_order";
		sc_ext::readExportCSVConfigXML('definition');
		ksort($array);
		return $array;
	}
	function getExportCSVLimitedFields()
	{
		$excluded = array();
		$excluded["id_category_default"]="id_category_default";
		$excluded["id_category(s)"]="id_category(s)";
		$excluded["category_default"]="category_default";
		$excluded["categories"]="categories";
		$excluded["date_add"]="date_add";
		$excluded["date_upd"]="date_upd";
		$excluded["last_order"]="last_order";
		$excluded["availability_message"]="availability_message";
		$excluded["priceinctaxwithshipping"]="priceinctaxwithshipping";
		$excluded["productshippingcost"]="productshippingcost";
		$excluded["id_manufacturer"]="id_manufacturer";
		$excluded["id_supplier"]="id_supplier";
		$excluded["id_product"]="id_product";
		$excluded["id_product-id_attribute"]="id_product-id_attribute";
		$excluded["id_product_attribute"]="id_product_attribute";
		$excluded["image_link"]="image_link";
		$excluded["image_legend"]="image_legend";
		$excluded["image_id"]="image_id";
		$excluded["link_to_product"]="link_to_product";
		$excluded["link_to_cover_image"]="link_to_cover_image";
		$excluded["margin"]="margin";
		$excluded["name_with_attributes"]="name_with_attributes";
		$excluded["priceinctax"]="priceinctax";
		$excluded["priceexctax"]="priceexctax";
		$excluded["quantity_physical"]="quantity_physical";
		$excluded["quantity_usable"]="quantity_usable";
		$excluded["quantity_real"]="quantity_real";
		$excluded["stock_value"]="stock_value";
		$excluded["stock_value_wholesale"]="stock_value_wholesale";
        $excluded["image_default_id"]="image_default_id";
        $excluded["image_url"]="image_url";
        $excluded["id_to_all"]="id_to_all";
        $excluded["id_to_all_for_product"]="id_to_all_for_product";
        $excluded["links_to_all_images"]="links_to_all_images";
        $excluded["urls_to_all_images"]="urls_to_all_images";
        $excluded["urls_to_all_images_for_product"]="urls_to_all_images_for_product";
        $excluded["quantity_total_physical"]="quantity_total_physical";
        $excluded["quantity_total_usable"]="quantity_total_usable";

		$array_temp = getExportCSVFields();
		$array = array();
		foreach($array_temp as $name=>$id)
		{
			if(empty($excluded[$id]))
				$array[$name]=$id;
		}
		return $array;
	}


	function readExportConfigXML($scriptfile)
	{
		global $exportConfig;
		// read config
		if ($feed = @simplexml_load_file(SC_TOOLS_DIR.'cat_export/'.$scriptfile))
		{
			$file=$feed->config;
			$exportConfig=array(
                'mapping' => (string) $file->mapping,
                'shops' => (string) $file->shops,
                'categoriessel' => (string) $file->categoriessel,
                'exportfilename' => (string) $file->exportfilename,
                'supplier' => (!empty($file->supplier)?(string) $file->supplier:""),
                'exportdisabledproducts' => (string) $file->exportdisabledproducts,
                'exportcombinations' => (string) $file->exportcombinations,
                'exportoutofstock' => (string) $file->exportoutofstock,
                'exportbydefaultcategory' => (string) $file->exportbydefaultcategory,
                'shippingfee' => (string) $file->shippingfee,
                'shippingfeefreefrom' => (string) $file->shippingfeefreefrom,
                'fieldsep' => (string) $file->fieldsep,
                'valuesep' => (string) $file->valuesep,
                'categorysep' => (string) $file->categorysep,
                'enclosedby' => (string) $file->enclosedby,
                'iso' => (string) $file->iso,
                'firstlinecontent' => (string) $file->firstlinecontent,
                'lastexportdate' => (string) $file->lastexportdate
            );
		}else{
			// config by default
				$exportConfig=array(
                    'shops' => '',
                    'mapping' => '',
                    'categoriessel' => '',
                    'exportfilename' => '',
                    'supplier' => '',
                    'exportdisabledproducts' => '0',
                    'exportcombinations' => '1',
                    'exportoutofstock' => '1',
                    'exportbydefaultcategory' => '1',
                    'shippingfee' => 0.00,
                    'shippingfeefreefrom' => '',
                    'fieldsep' => 'dcomma',
                    'valuesep' => ',',
                    'categorysep' => ',',
                    'enclosedby' => '"',
                    'iso' => '0',
                    'firstlinecontent' => '',
                    'lastexportdate' => '000-00-00 00:00:00'
                );
		}
	}

	
	function writeExportConfigXML($scriptfile)
	{
		global $exportConfig;
		$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content.='<exportscript>'."\n";
		$conf=$exportConfig;
				$content.='<config>'."\n";
				$content.='<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
				$content.='<shops><![CDATA['.$conf['shops'].']]></shops>';
				$content.='<categoriessel><![CDATA['.$conf['categoriessel'].']]></categoriessel>';
                $content.='<exportfilename><![CDATA['.$conf['exportfilename'].']]></exportfilename>';
                $content.='<supplier><![CDATA['.$conf['supplier'].']]></supplier>';
				$content.='<exportdisabledproducts><![CDATA['.$conf['exportdisabledproducts'].']]></exportdisabledproducts>';
				$content.='<exportcombinations><![CDATA['.$conf['exportcombinations'].']]></exportcombinations>';
				$content.='<exportoutofstock><![CDATA['.$conf['exportoutofstock'].']]></exportoutofstock>';
				$content.='<exportbydefaultcategory><![CDATA['.$conf['exportbydefaultcategory'].']]></exportbydefaultcategory>';
				$content.='<shippingfee><![CDATA['.$conf['shippingfee'].']]></shippingfee>';
				$content.='<shippingfeefreefrom><![CDATA['.$conf['shippingfeefreefrom'].']]></shippingfeefreefrom>';
				$content.='<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
				$content.='<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
				$content.='<categorysep><![CDATA['.$conf['categorysep'].']]></categorysep>';
				$content.='<enclosedby><![CDATA['.$conf['enclosedby'].']]></enclosedby>';
				$content.='<iso><![CDATA['.$conf['iso'].']]></iso>';
				$content.='<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
				$content.='<lastexportdate><![CDATA['.$conf['lastexportdate'].']]></lastexportdate>';
				$content.='</config>'."\n";
		$content.='</exportscript>';
		return file_put_contents(SC_TOOLS_DIR.'cat_export/'.$scriptfile, $content);
	}
	

	function ps_round($value,$precision)
	{
		if (is_callable('tools::ps_round'))
		{
			return Tools::ps_round($value,$precision);
		}
		return round($value,$precision);
	}
	
	function isCarrierInRange($id_carrier, $id_zone, $weight, $price)
	{
		$carrier = new Carrier((int)$id_carrier, Configuration::get('PS_LANG_DEFAULT'));
		$shippingMethod = $carrier->getShippingMethod();
	
		if ($shippingMethod == Carrier::SHIPPING_METHOD_FREE)
			return true;
		if ($shippingMethod == Carrier::SHIPPING_METHOD_WEIGHT
				AND (Carrier::checkDeliveryPriceByWeight((int)$id_carrier, $weight, $id_zone)))
			return true;
		if ($shippingMethod == Carrier::SHIPPING_METHOD_PRICE
				AND (Carrier::checkDeliveryPriceByPrice((int)$id_carrier, $price, $id_zone, Configuration::get('PS_CURRENCY_DEFAULT'))))
			return true;
	
		return false;
	}
	
	function getOrderShippingCost($id_carrier = NULL, $useTax = true, $additional_shipping_cost=0, $weight=0, $price=0)
	{
		global $defaultCountry;
		$_carriers = NULL;
	
		// Checking discounts in cart
		//		$products = $this->getProducts();
	
		// Start with shipping cost at 0
		$shipping_cost = 0;
	
		if (!Validate::isLoadedObject($defaultCountry))
			$defaultCountry = new Country(Configuration::get('PS_COUNTRY_DEFAULT'), Configuration::get('PS_LANG_DEFAULT'));
		$id_zone = (int)$defaultCountry->id_zone;
	
		// If no carrier, select default one
		if ($id_carrier && !isCarrierInRange($id_carrier, $id_zone, $weight, $price))
			$id_carrier = '';
	
		if (empty($id_carrier) && isCarrierInRange(Configuration::get('PS_CARRIER_DEFAULT'), $id_zone, $weight, $price))
			$id_carrier = (int)(Configuration::get('PS_CARRIER_DEFAULT'));
	
		if (empty($id_carrier))
		{
			$result = Carrier::getCarriers((int)(Configuration::get('PS_LANG_DEFAULT')), true, false, (int)($id_zone));
			$resultsArray = array();
			foreach ($result AS $k => $row)
			{
				if ($row['id_carrier'] == Configuration::get('PS_CARRIER_DEFAULT'))
					continue;
	
				if (!isset($_carriers[$row['id_carrier']]))
					$_carriers[$row['id_carrier']] = new Carrier((int)($row['id_carrier']));
	
				$carrier = $_carriers[$row['id_carrier']];
	
				// Get only carriers that are compliant with shipping method
				if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND $carrier->getMaxDeliveryPriceByWeight($id_zone) === false)
						OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND $carrier->getMaxDeliveryPriceByPrice($id_zone) === false))
				{
					unset($result[$k]);
					continue ;
				}
	
				// If out-of-range behavior carrier is set on "Desactivate carrier"
				if ($row['range_behavior'])
				{
					// Get only carriers that have a range compatible with cart
					if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($row['id_carrier'], $weight, $id_zone)))
							OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($row['id_carrier'], $price, $id_zone, Configuration::get('PS_CURRENCY_DEFAULT')))))
					{
						unset($result[$k]);
						continue ;
					}
				}
	
				if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
				{
					$shipping = $carrier->getDeliveryPriceByWeight($weight, $id_zone);
	
					if (!isset($tmp))
						$tmp = $shipping;
	
					if ($shipping <= $tmp)
						$id_carrier = (int)($row['id_carrier']);
				}
				else // by price
				{
					$shipping = $carrier->getDeliveryPriceByPrice($price, $id_zone, Configuration::get('PS_CURRENCY_DEFAULT'));
	
					if (!isset($tmp))
						$tmp = $shipping;
	
					if ($shipping <= $tmp)
						$id_carrier = (int)($row['id_carrier']);
				}
			}
		}
	
		if (empty($id_carrier))
			$id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
	
		if (!isset($_carriers[$id_carrier]))
			$_carriers[$id_carrier] = new Carrier((int)($id_carrier), Configuration::get('PS_LANG_DEFAULT'));
		$carrier = $_carriers[$id_carrier];
		if (!Validate::isLoadedObject($carrier))
			die(Tools::displayError('Fatal error: "no default carrier"'));
		if (!$carrier->active)
			return $shipping_cost;
	
		// Free fees if free carrier
		if ($carrier->is_free == 1)
			return 0;
	
		// Select carrier tax
		if ($useTax AND !Tax::excludeTaxeOption())
			$carrierTax = Tax::getCarrierTaxRate((int)$carrier->id);
	
		$configuration = Configuration::getMultiple(array('PS_SHIPPING_FREE_PRICE', 'PS_SHIPPING_HANDLING', 'PS_SHIPPING_METHOD', 'PS_SHIPPING_FREE_WEIGHT'));
		// Free fees
		$free_fees_price = 0;
		if (isset($configuration['PS_SHIPPING_FREE_PRICE']))
			$free_fees_price = Tools::convertPrice((float)($configuration['PS_SHIPPING_FREE_PRICE']), Currency::getCurrencyInstance((int)(Configuration::get('PS_CURRENCY_DEFAULT'))));
		$orderTotalwithDiscounts = $price;
		if ($orderTotalwithDiscounts >= (float)($free_fees_price) AND (float)($free_fees_price) > 0)
			return $shipping_cost;
		if (isset($configuration['PS_SHIPPING_FREE_WEIGHT']) AND $weight >= (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) AND (float)($configuration['PS_SHIPPING_FREE_WEIGHT']) > 0)
			return $shipping_cost;
	
		// Get shipping cost using correct method
		if ($carrier->range_behavior)
		{
			$id_zone = (int)$defaultCountry->id_zone;
			if (($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT AND (!Carrier::checkDeliveryPriceByWeight($carrier->id, $weight, $id_zone)))
					OR ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_PRICE AND (!Carrier::checkDeliveryPriceByPrice($carrier->id, $price, $id_zone, (int)(Configuration::get('PS_CURRENCY_DEFAULT'))))))
				$shipping_cost += 0;
			else {
				if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
					$shipping_cost += $carrier->getDeliveryPriceByWeight($weight, $id_zone);
				else // by price
					$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)(Configuration::get('PS_CURRENCY_DEFAULT')));
			}
		}
		else
		{
			if ($carrier->getShippingMethod() == Carrier::SHIPPING_METHOD_WEIGHT)
				$shipping_cost += $carrier->getDeliveryPriceByWeight($weight, $id_zone);
			else
				$shipping_cost += $carrier->getDeliveryPriceByPrice($order_total, $id_zone, (int)(Configuration::get('PS_CURRENCY_DEFAULT')));
	
		}
		// Adding handling charges
		if (isset($configuration['PS_SHIPPING_HANDLING']) AND $carrier->shipping_handling)
			$shipping_cost += (float)($configuration['PS_SHIPPING_HANDLING']);
	
		$shipping_cost = Tools::convertPrice($shipping_cost, Currency::getCurrencyInstance((int)(Configuration::get('PS_CURRENCY_DEFAULT'))));
	
		// Additional Shipping Cost per product
		//		foreach($products AS $product)
			//			$shipping_cost += $product['additional_shipping_cost'] * $product['cart_quantity'];
	
		//get external shipping cost from module
		/*if ($carrier->shipping_external)
			{
		$moduleName = $carrier->external_module_name;
		$module = Module::getInstanceByName($moduleName);
		if (key_exists('id_carrier', $module))
			$module->id_carrier = $carrier->id;
		if($carrier->need_range)
			$shipping_cost = $module->getOrderShippingCost($this, $shipping_cost);
		else
			$shipping_cost = $module->getOrderShippingCostExternal($this);
	
		// Check if carrier is available
		if ($shipping_cost === false)
			return false;
		}*/
	
		// Apply tax
		if (isset($carrierTax))
			$shipping_cost *= 1 + ($carrierTax / 100);
	
		return (float)(ps_round((float)($shipping_cost+$additional_shipping_cost), 2));
	}
	
	function getHttpHost($http = false, $entities = false)
	{
		$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
		if ($entities)
			$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
		if ($http)
			$host = (Configuration::get('PS_SSL_ENABLED') ? 'https://' : 'http://').$host;
		return $host;
	}
	

	function createCategoryCache($id_lang)
	{
		global $categoryNameByID,$categoriesProperties,$cacheCategory;
		if ((int)$id_lang < 1)
			die(_l('You have to set the language in the mapping for the field:').' '._l('category'));
		if (!sc_array_key_exists($id_lang,$cacheCategory))
		{
			$sql="SELECT c.id_category,c.id_parent,cl.name,c.level_depth
						FROM "._DB_PREFIX_."category c
						LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($id_lang).")
						GROUP BY c.id_category
						ORDER BY c.level_depth ASC";
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res AS $categ)
			{
				if ($categ['id_category']==$categ['id_parent']) die(_l('A category cannot be parent of itself, you must fix this error for category ID').' '.$categ['id_category'].' - '.trim(hideCategoryPosition($categ['name'])));
				$categoryNameByID[$id_lang][$categ['id_category']]=trim(hideCategoryPosition($categ['name']));
				$categoriesProperties[$id_lang][$categ['id_category']]=array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
			}
			$cacheCategory[$id_lang]=1;
		}
	}


    function createCarriersCache()
    {
        global $cacheCarriers;
        if (empty($cacheCarriers) && count($cacheCarriers)==0)
        {
            $sql="SELECT c.* FROM "._DB_PREFIX_."carrier c WHERE c.deleted=0 ORDER BY id_carrier ASC";
            $res=Db::getInstance()->ExecuteS($sql);
            foreach($res AS $carrier)
            {
                if(empty($carrier["name"]))
                    $carrier["name"] = Configuration::get('PS_SHOP_NAME');
                $cacheCarriers[$carrier["id_reference"]] = $carrier["name"];
            }
        }
    }


    function createSuppliersCache()
    {
        global $cacheSuppliers;
        if (empty($cacheSuppliers) && count($cacheSuppliers)==0)
        {
            $sql="SELECT c.* FROM "._DB_PREFIX_."supplier c ORDER BY id_supplier ASC";
            $res=Db::getInstance()->ExecuteS($sql);
            foreach($res AS $supplier)
            {
                if(empty($supplier["name"]))
                    $supplier["name"] = Configuration::get('PS_SHOP_NAME');
                $cacheSuppliers[$supplier["id_supplier"]] = $supplier["name"];
            }
        }
    }

	function createQueriesCache($sqlQuery)
	{
		global $cacheQueries;
		if (!sc_array_key_exists($sqlQuery,$cacheQueries)) {
			$resSQL = Db::getInstance()->ExecuteS($sqlQuery);
			$cacheQueries[$sqlQuery] = $resSQL;
		}

		return $cacheQueries[$sqlQuery];
	}

	$id_cat_root = Configuration::get('PS_ROOT_CATEGORY');
	function getCategoryPath($id_category,$path='',$id_categ_origin, $id_lang)
	{
		global $categoryNameByID,$categoriesProperties,$cacheCategoryPath,$id_cat_root;
		if (sc_array_key_exists($id_lang,$cacheCategoryPath) && sc_array_key_exists($id_categ_origin,$cacheCategoryPath[$id_lang]))
			return $cacheCategoryPath[$id_lang][$id_categ_origin];
		
		$todo = false;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			/*$root_cat = SCI::getConfigurationValue("PS_ROOT_CATEGORY");
			$home_cat = SCI::getConfigurationValue("PS_HOME_CATEGORY");
			if(!empty($id_category)
				&& $id_category != $root_cat
				&& (_s('CAT_EXPORT_ROOT_CATEGORY') || (!_s('CAT_EXPORT_ROOT_CATEGORY') && $id_category != $home_cat))
			)
				$todo = true;*/
			if ($id_category!=$id_cat_root && $id_category > 0)
				$todo = true;
		}
		else
		{
			if ($id_category > 1 || (_s('CAT_EXPORT_ROOT_CATEGORY') && $id_category > 0))
				$todo = true;
		}
		if (!empty($todo))
		{
			if (!sc_array_key_exists($id_category,$categoriesProperties[$id_lang]))
				die(_l('You should use the tool "check and fix the level_depth field" from the Catalog > Tools menu to fix your categories.').' (id_category:'.$id_category.')');

            return getCategoryPath($categoriesProperties[$id_lang][$id_category]['id_parent'],' > '.$categoryNameByID[$id_lang][$id_category].$path,$id_categ_origin,$id_lang);
		}else{
			$path=trim($path,' > ');
			$cacheCategoryPath[$id_lang][$id_categ_origin]=$path;
			return $path;
		}
	}
	
	function forceCategoryPathFormat($path)
	{
		$tmp=explode('>',$path);
		$tmp=array_map('trim',$tmp);
		return join(' > ',$tmp);
	}
	
	function createMultiLangField($field)
	{
		$languages = Language::getLanguages();
		$res = array();
		foreach ($languages AS $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}
	
	function getCombinationImages($id_product)
	{
		global $selected_shops_id;
		if (!$result = Db::getInstance()->ExecuteS('
			SELECT pai.`id_image`,pai.`id_product_attribute`
			FROM `'._DB_PREFIX_.'product_attribute_image` pai
			WHERE pai.`id_product_attribute` IN (
				SELECT pa.`id_product_attribute`
				FROM `'._DB_PREFIX_.'product_attribute` pa
				'.((SCMS && $selected_shops_id>0)?'	INNER JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = "'.(int)$selected_shops_id.'")':"").'
				WHERE pa.`id_product` = '.(int)($id_product).'
			)
			GROUP BY pai.`id_product_attribute`'))
			return array();
		$images = array();
		foreach ($result AS $row)
			$images[$row['id_product_attribute']] = (int)$row['id_image'];
		return $images;
	}

	function showHeaders()
	{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
	<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<script type="text/javascript" src="'.SC_JQUERY.'"></script>
		<script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
		<script type="text/javascript" src="'.SC_JSFUNCTIONS.'"></script>
	</head>
	<body>';
	}
	
	function getBoolean($value)
	{
		if (sc_in_array(Tools::strtoupper($value),array('1','YES','TRUE','VRAI','OUI'),"catWinExportTools_boolean")) return true;
		return false;
	}
