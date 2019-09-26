<?php
 
class AdminProductsController extends AdminProductsControllerCore
{
 
    public function processExport($text_delimiter = '"')
    {
        $this->_select .= ', a.`reference`, ';
        $this->fields_list['reference']['title'] = 'Reference #';

        
        $this->fields_list['image'] = array(
            'title' => $this->l('Image URLs (x,y,z...)'),
            'callback' => 'exportAllImagesLink'
        );
        
        $this->fields_list['name_category'] = array(
            'title' => $this->l('Categories (x,y,z...)'),
            'callback' => 'exportAllProductCategories'
        );
        
        $this->_select .= 'NULL AS features, ';
        $this->fields_list['features'] = array(
            'title' => $this->l('Feature (Name:Value:Position:Customized)'),
            'callback' => 'exportFeatures'
        );
        
        $this->_select .= 'NULL AS tags, ';
        $this->fields_list['tags'] = array(
            'title' => $this->l('Tags (x,y,z...)'),
            'callback' => 'exportTags'
        );
 
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'product_supplier` product_supplier ON (a.`id_product` = product_supplier.`id_product` AND product_supplier.`id_product_attribute` = 0)';
         
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'supplier` supplier ON (product_supplier.`id_supplier` = supplier.`id_supplier`)';
        $this->_select .= 'supplier.`name` AS supplier_name, ';
        $this->fields_list['supplier_name'] = array('title' => $this->l('Supplier'));
         
        $this->_select .= 'product_supplier.`product_supplier_reference` AS supplier_reference, ';
        $this->fields_list['supplier_reference'] = array('title' => $this->l('Supplier reference #'));
         
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'manufacturer` manufacturer ON (a.`id_manufacturer` = manufacturer.`id_manufacturer`)';
        $this->_select .= 'manufacturer.`name` AS manufacturer_name, ';
        $this->fields_list['manufacturer_name'] = array('title' => $this->l('Manufacturer'));
 
 
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'specific_price` specific_price ON (a.`id_product` = specific_price.`id_product` AND specific_price.`id_product_attribute` = 0)';
         
        $this->_select .= '(CASE WHEN specific_price.`reduction_type` = "amount" THEN specific_price.`reduction` END) AS discount_amount, ';
        $this->fields_list['discount_amount'] = array('title' => $this->l('Discount amount'));
         
        $this->_select .= '(CASE WHEN specific_price.`reduction_type` = "percentage" THEN specific_price.`reduction` * 100 END) AS discount_percent, ';
        $this->fields_list['discount_percent'] = array('title' => $this->l('Discount percent'));
         
        $this->_select .= 'IF(DATE(specific_price.`from`) = DATE("0000-00-00"), NULL, DATE(specific_price.`from`)) AS discount_from, ';
        $this->fields_list['discount_from'] = array('title' => $this->l('Discount from (yyyy-mm-dd)'));
         
        $this->_select .= 'IF(DATE(specific_price.`to`) = DATE("0000-00-00"), NULL, DATE(specific_price.`to`)) AS discount_to, ';
        $this->fields_list['discount_to'] = array('title' => $this->l('Discount to (yyyy-mm-dd)'));
 
        $this->_select .= 'IF(DATE(a.`available_date`) = DATE("0000-00-00"), NULL, DATE(a.`available_date`)) AS available_date, ';
        $this->fields_list['available_date'] = array('title' => $this->l('Product availability date'));
         
        $this->_select .= 'IF(TIMEDIFF(a.`date_add`, "0000-00-00 00:00:00"), a.`date_add`, NULL) AS date_add, ';
        $this->fields_list['date_add'] = array('title' => $this->l('Product creation date'));
 
 
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'stock_available` stock_available ON (a.`id_product` = stock_available.`id_product` AND stock_available.`id_product_attribute` = 0)';
        $this->_select .= 'stock_available.`depends_on_stock`, ';
        $this->fields_list['depends_on_stock'] = array('title' => $this->l('Depends on stock'));
         
        $this->_join .= ' LEFT JOIN `' . _DB_PREFIX_ . 'warehouse_product_location` wpl ON (a.`id_product` = wpl.`id_product` AND wpl.`id_product_attribute` = 0)';
        $this->_select .= 'wpl.`id_warehouse`, ';
        $this->fields_list['id_warehouse'] = array('title' => $this->l('Warehouse'));
 
 
        $this->_select .= 'b.`description_short` AS short_description, ';
        $this->fields_list['short_description'] = array(
            'title' => $this->l('Short description'),
            'callback' => 'replaceQuote'
        );
         
        $this->_select .= 'b.`description`, ';
        $this->fields_list['description'] = array(
            'title' => $this->l('Description'),
            'callback' => 'replaceQuote'
        );
         
        $this->_select .= 'b.`meta_title`, ';
        $this->fields_list['meta_title'] = array('title' => $this->l('Meta title'));
         
        $this->_select .= 'b.`meta_keywords`, ';
        $this->fields_list['meta_keywords'] = array('title' => $this->l('Meta keywords'));
         
        $this->_select .= 'b.`meta_description`, ';
        $this->fields_list['meta_description'] = array('title' => $this->l('Meta description'));
         
        $this->_select .= 'b.`link_rewrite`, ';
        $this->fields_list['link_rewrite'] = array('title' => $this->l('URL rewritten'));
         
        $this->_select .= 'b.`available_now`, ';
        $this->fields_list['available_now'] = array('title' => $this->l('Text when in stock'));
         
        $this->_select .= 'b.`available_later`, ';
        $this->fields_list['available_later'] = array('title' => $this->l('Text when backorder allowed'));
        
        
        $this->_select .= 'a.`id_tax_rules_group`, ';
        $this->fields_list['id_tax_rules_group'] = array('title' => $this->l('Tax rules ID'));
         
        $this->_select .= 'a.`wholesale_price`, ';
        $this->fields_list['wholesale_price'] = array('title' => $this->l('Wholesale price'));
         
        $this->_select .= 'a.`on_sale`, ';
        $this->fields_list['on_sale'] = array('title' => $this->l('On sale'));
         
        $this->_select .= 'a.`ean13`, ';
        $this->fields_list['ean13'] = array('title' => $this->l('EAN13'));
         
        $this->_select .= 'a.`upc`, ';
        $this->fields_list['upc'] = array('title' => $this->l('UPC'));
         
        $this->_select .= 'a.`ecotax`, ';
        $this->fields_list['ecotax'] = array('title' => $this->l('Ecotax'));
         
        $this->_select .= 'a.`width`, ';
        $this->fields_list['width'] = array('title' => $this->l('Width'));
         
        $this->_select .= 'a.`height`, ';
        $this->fields_list['height'] = array('title' => $this->l('Height'));
         
        $this->_select .= 'a.`depth`, ';
        $this->fields_list['depth'] = array('title' => $this->l('Depth'));
         
        $this->_select .= 'a.`weight`, ';
        $this->fields_list['weight'] = array('title' => $this->l('Weight'));
         
        $this->_select .= 'a.`minimal_quantity`, ';
        $this->fields_list['minimal_quantity'] = array('title' => $this->l('Minimal quantity'));
         
        $this->_select .= 'a.`visibility`, ';
        $this->fields_list['visibility'] = array('title' => $this->l('Visibility'));
         
        $this->_select .= 'a.`additional_shipping_cost`, ';
        $this->fields_list['additional_shipping_cost'] = array('title' => $this->l('Additional shipping cost'));
         
        $this->_select .= 'a.`unity`, ';
        $this->fields_list['unity'] = array('title' => $this->l('Unit for the unit price'));
         
        $this->_select .= '(a.`price` / a.`unit_price_ratio`) AS unit_price, ';
        $this->fields_list['unit_price'] = array('title' => $this->l('Unit price'));
         
        $this->_select .= 'a.`available_for_order`, ';
        $this->fields_list['available_for_order'] = array('title' => $this->l('Available for order (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`show_price`, ';
        $this->fields_list['show_price'] = array('title' => $this->l('Show price (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`online_only`, ';
        $this->fields_list['online_only'] = array('title' => $this->l('Available online only (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`condition`, ';
        $this->fields_list['condition'] = array('title' => $this->l('Condition'));
         
        $this->_select .= 'a.`customizable`, ';
        $this->fields_list['customizable'] = array('title' => $this->l('Customizable (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`uploadable_files`, ';
        $this->fields_list['uploadable_files'] = array('title' => $this->l('Uploadable files (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`text_fields`, ';
        $this->fields_list['text_fields'] = array('title' => $this->l('Text fields (0 = No, 1 = Yes)'));
         
        $this->_select .= 'a.`out_of_stock`, ';
        $this->fields_list['out_of_stock'] = array('title' => $this->l('Action when out of stock'));
         
        $this->_select .= 'a.`id_shop_default`, ';
        $this->fields_list['id_shop_default'] = array('title' => $this->l('ID / Name of shop'));
         
        $this->_select .= 'a.`advanced_stock_management`, ';
        $this->fields_list['advanced_stock_management'] = array('title' => $this->l('Advanced Stock Management'));
 
        $this->_select .= '0 AS delete_images, ';
        $this->fields_list['delete_images'] = array('title' => $this->l('Delete existing images (0 = No, 1 = Yes)'));
 
 
        $this->fields_list['active']['title'] = 'Active (0/1)';
         
        $this->fields_list['price']['title'] = 'Price tax excluded';
         
        $this->fields_list['price_final']['title'] = 'Price tax included';
        
        static::sortCSVfields($this->fields_list);
 
        parent::processExport($text_delimiter);
    }
    
    public static function replaceQuote($html, $row)
    {
        if (empty($row) || empty($row['id_product'])) {
            return;
        }
    
        return str_replace('"', "'", $html);
    }
    
    
    public static function sortCSVfields(&$fields)
    {
        ksort($fields);
        $positions = array(2, 27, 53, 40, 39, 38, 37, 47, 48, 41, 44, 54, 22, 31, 9, 11, 10, 12, 17, 19, 45, 21, 1, 52, 6, 55, 43, 36, 16, 35, 34, 33, 25, 3, 4, 8, 46, 51, 5, 56, 13, 24, 30, 42, 15, 14, 32, 50, 29, 28, 18, 49, 26, 23, 7, 20);
        array_multisort($positions, $fields);
    }
    
    
    public static function exportTags($tag, $row, $delimiter = ',')
    {
        if (empty($row) || empty($row['id_product'])) {
            return;
        }
     
        $id_product = (int) $row['id_product'];
        $id_lang = Context::getContext()->language->id;
     
        $query = new DbQuery();
        $query->select('tag.name')->from('tag', 'tag');
        $query->innerJoin('product_tag', 'pt', 'tag.id_tag = pt.id_tag AND pt.id_product = ' . $id_product);
        $query->where('tag.id_lang = ' . $id_lang);
     
        $tags = array();
        foreach (Db::getInstance()->executeS($query) as $tag) {
            $tags[] = $tag['name'];
        }
     
        return implode($delimiter, $tags);
    }
    
	
    public static function exportFeatures($feature, $row, $delimiter = ',')
    {
        if (empty($row) || empty($row['id_product'])) {
            return;
        }
     
        $id_product = (int) $row['id_product'];
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
     
        $query = new DbQuery();
        $query->select('IF(LENGTH(feature_value_lang.value), CONCAT_WS(":", feature_lang.name, feature_value_lang.value, feature.position, feature_value.custom), NULL) AS feature')->from('feature', 'feature');
        $query->leftJoin('feature_lang', 'feature_lang', 'feature.id_feature = feature_lang.id_feature AND feature_lang.id_lang = ' . $id_lang);
        $query->leftJoin('feature_shop', 'feature_shop', 'feature.id_feature = feature_shop.id_feature AND feature_shop.id_shop = ' . $id_shop);
        $query->leftJoin('feature_product', 'feature_product', 'feature_product.id_feature = feature_product.id_feature AND feature_product.id_product = ' . $id_product);
        $query->leftJoin('feature_value', 'feature_value', 'feature.id_feature = feature_value.id_feature AND feature_product.id_feature_value = feature_value.id_feature_value');
        $query->leftJoin('feature_value_lang', 'feature_value_lang', 'feature_value.id_feature_value = feature_value_lang.id_feature_value AND feature_value_lang.id_lang = ' . $id_lang);
     
        $features = array();
        foreach (Db::getInstance()->executeS($query) as $feature) {
            if ($feature['feature']) {
                $features[] = $feature['feature'];
            }
        }
     
        return implode($delimiter, $features);
    }
    
    public static function exportAllImagesLink($cover, $row, $delimiter = ',')
    {
        if (empty($row) || empty($row['id_product']) || empty($row['id_image'])) {
            return;
        }
     
        $id_product = (int) $row['id_product'];
        $id_shop = Context::getContext()->shop->id;
        $links = array($cover); // the first link is the cover image
     
        $query = new DbQuery();
        $query->select('i.id_image')->from('image', 'i');
        $query->leftJoin('image_shop', 'is', 'i.id_image = is.id_image AND is.id_shop = ' . $id_shop);
        $query->where('i.id_product = ' . $id_product . ' AND (i.cover IS NULL OR i.cover = 0)');
        $images = Db::getInstance()->executeS($query);
     
        foreach ($images as $image) {
            if (Configuration::get('PS_LEGACY_IMAGES')) {
                $links[] = Tools::getShopDomain(true) . _THEME_PROD_DIR_ . $id_product . '-' . $image['id_image'] . '.jpg';
            } else {
                $links[] = Tools::getShopDomain(true) . _THEME_PROD_DIR_ . Image::getImgFolderStatic($image['id_image']) . $image['id_image'] . '.jpg';
            }
        }
     
        return implode($delimiter, $links);
    }
    
    public static function exportAllProductCategories($defaultCategory, $row, $delimiter = ',')
    {
        if (empty($row) || empty($row['id_product'])) {
            return;
        }
    
        $id_product = (int) $row['id_product'];
        $id_lang = Context::getContext()->language->id;
        $id_shop = Context::getContext()->shop->id;
    
        $query = new DbQuery();
        $query->select('cl.name')->from('category_lang', 'cl');
        $query->leftJoin('category_shop', 'cs', 'cl.id_category = cs.id_category AND cs.id_shop = ' . $id_shop);
        $query->leftJoin('category_product', 'cp', 'cl.id_category = cp.id_category AND cp.id_product = ' . $id_product);
        $query->leftJoin('product', 'p', 'cp.id_product = p.id_product');
        $query->where('cl.id_lang = ' . $id_lang . ' AND p.id_category_default != cl.id_category');
    
        $categories = array($defaultCategory); // the first category is the default one
        foreach (Db::getInstance()->executeS($query) as $category) {
            $categories[] = $category['name'];
        }
    
        return implode($delimiter, $categories);
    }
 
    public static function exportAllProductCategoriesId($defaultCategory, $row, $delimiter = ',')
    {
        if (empty($row) || empty($row['id_product'])) {
            return;
        }
     
        $id_product = (int) $row['id_product'];
        $id_shop = Context::getContext()->shop->id;
     
        $query = new DbQuery();
        $query->select('c.id_category, p.id_category_default')->from('category', 'c');
        $query->leftJoin('category_shop', 'cs', 'c.id_category = cs.id_category AND cs.id_shop = ' . $id_shop);
        $query->leftJoin('category_product', 'cp', 'c.id_category = cp.id_category AND cp.id_product = ' . $id_product);
        $query->leftJoin('product', 'p', 'cp.id_product = p.id_product');
        $query->where('p.id_category_default != c.id_category');
     
        $categories = array();
        foreach (Db::getInstance()->executeS($query) as $category) {
            if (!count($categories)) {
                $categories[] = $category['id_category_default']; // the first category is the default one
            }
     
            $categories[] = $category['id_category'];
        }
     
        return implode($delimiter, $categories);
    }

    protected function _displaySpecificPriceModificationForm($defaultCurrency, $shops, $currencies, $countries, $groups)
    {
        /** @var Product $obj */
        if (!($obj = $this->loadObject())) {
            return;
        }
    
        $page = (int)Tools::getValue('page');
        $content = '';
        $specific_prices = SpecificPrice::getByProductId((int)$obj->id);
        $specific_price_priorities = SpecificPrice::getPriority((int)$obj->id);
    
        $tmp = array();
        foreach ($shops as $shop) {
            $tmp[$shop['id_shop']] = $shop;
        }
        $shops = $tmp;
        $tmp = array();
        foreach ($currencies as $currency) {
            $tmp[$currency['id_currency']] = $currency;
        }
        $currencies = $tmp;
    
        $tmp = array();
        foreach ($countries as $country) {
            $tmp[$country['id_country']] = $country;
        }
        $countries = $tmp;
    
        $tmp = array();
        foreach ($groups as $group) {
            $tmp[$group['id_group']] = $group;
        }
        $groups = $tmp;
    
        $address = Address::initialize();
        $tax_manager = TaxManagerFactory::getManager($address, $obj->id_tax_rules_group);
        $tax_calculator = $tax_manager->getTaxCalculator();
        
        $length_before = strlen($content);
        if (is_array($specific_prices) && count($specific_prices)) {
            $i = 0;
            foreach ($specific_prices as $specific_price) {
                $id_currency = $specific_price['id_currency'] ? $specific_price['id_currency'] : $defaultCurrency->id;
                if (!isset($currencies[$id_currency])) {
                    continue;
                }
    
                $current_specific_currency = $currencies[$id_currency];
                if ($specific_price['reduction_type'] == 'percentage') {
                    $impact = '- '.($specific_price['reduction'] * 100).' %';
                } elseif ($specific_price['reduction'] > 0) {
                    $impact = '- '.Tools::displayPrice(Tools::ps_round($specific_price['reduction'], 2), $current_specific_currency).' ';
                    if ($specific_price['reduction_tax']) {
                        $impact .= '('.$this->l('Tax incl.').')';
                    } else {
                        $impact .= '('.$this->l('Tax excl.').')';
                    }
                } else {
                    $impact = '--';
                }
    
                if ($specific_price['from'] == '0000-00-00 00:00:00' && $specific_price['to'] == '0000-00-00 00:00:00') {
                    $period = $this->l('Unlimited');
                } else {
                    $period = $this->l('From').' '.($specific_price['from'] != '0000-00-00 00:00:00' ? $specific_price['from'] : '0000-00-00 00:00:00').'<br />'.$this->l('To').' '.($specific_price['to'] != '0000-00-00 00:00:00' ? $specific_price['to'] : '0000-00-00 00:00:00');
                }
                if ($specific_price['id_product_attribute']) {
                    $combination = new Combination((int)$specific_price['id_product_attribute']);
                    $attributes = $combination->getAttributesName((int)$this->context->language->id);
                    $attributes_name = '';
                    foreach ($attributes as $attribute) {
                        $attributes_name .= $attribute['name'].' - ';
                    }
                    $attributes_name = rtrim($attributes_name, ' - ');
                } else {
                    $attributes_name = $this->l('All combinations');
                }
    
                $rule = new SpecificPriceRule((int)$specific_price['id_specific_price_rule']);
                $rule_name = ($rule->id ? $rule->name : $specific_price['comment']);
    
                if ($specific_price['id_customer']) {
                    $customer = new Customer((int)$specific_price['id_customer']);
                    if (Validate::isLoadedObject($customer)) {
                        $customer_full_name = $customer->firstname.' '.$customer->lastname;
                    }
                    unset($customer);
                }
    
                if (!$specific_price['id_shop'] || in_array($specific_price['id_shop'], Shop::getContextListShopID())) {
                    $content .= '
					<tr '.($i % 2 ? 'class="alt_row"' : '').'>
						<td>'.$rule_name.'</td>
						<td>'.$attributes_name.'</td>';
    
                    $can_delete_specific_prices = true;
                    if (Shop::isFeatureActive()) {
                        $id_shop_sp = $specific_price['id_shop'];
                        $can_delete_specific_prices = (count($this->context->employee->getAssociatedShops()) > 1 && !$id_shop_sp) || $id_shop_sp;
                        $content .= '
						<td>'.($id_shop_sp ? $shops[$id_shop_sp]['name'] : $this->l('All shops')).'</td>';
                    }
                    $price = Tools::ps_round($specific_price['price'], 2);
                    $fixed_price = ($price == Tools::ps_round($obj->price, 2) || $specific_price['price'] == -1) ? '--' : Tools::displayPrice($price, $current_specific_currency);
    
                    $fixed_price_wt = Tools::displayPrice($tax_calculator->addTaxes($price), $current_specific_currency) ;
    
                    $content .= '
						<td>'.($specific_price['id_currency'] ? $currencies[$specific_price['id_currency']]['name'] : $this->l('All currencies')).'</td>
						<td>'.($specific_price['id_country'] ? $countries[$specific_price['id_country']]['name'] : $this->l('All countries')).'</td>
						<td>'.($specific_price['id_group'] ? $groups[$specific_price['id_group']]['name'] : $this->l('All groups')).'</td>
						<td title="'.$this->l('ID:').' '.$specific_price['id_customer'].'">'.(isset($customer_full_name) ? $customer_full_name : $this->l('All customers')).'</td>
						<td>'.$fixed_price.' / '. $fixed_price_wt .'</td>
						<td>'.$impact.'</td>
						<td>'.$period.'</td>
						<td>'.$specific_price['from_quantity'].'</th>
						<td>'.((!$rule->id && $can_delete_specific_prices) ? '<a class="btn btn-default" name="delete_link" href="'.self::$currentIndex.'&id_product='.(int)Tools::getValue('id_product').'&action=deleteSpecificPrice&id_specific_price='.(int)($specific_price['id_specific_price']).'&token='.Tools::getValue('token').'"><i class="icon-trash"></i></a>': '').'</td>
					</tr>';
                    $i++;
                    unset($customer_full_name);
                }
            }
        }
    
        if ($length_before === strlen($content)) {
            $content .= '
				<tr>
					<td class="text-center" colspan="13"><i class="icon-warning-sign"></i>&nbsp;'.$this->l('No specific prices.').'</td>
				</tr>';
        }
    
        $content .= '
				</tbody>
			</table>
			</div>
			<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>';
    
        $content .= '
		<script type="text/javascript">
			var currencies = new Array();
			currencies[0] = new Array();
			currencies[0]["sign"] = "'.$defaultCurrency->sign.'";
			currencies[0]["format"] = '.intval($defaultCurrency->format).';
			';
        foreach ($currencies as $currency) {
            $content .= '
				currencies['.$currency['id_currency'].'] = new Array();
				currencies['.$currency['id_currency'].']["sign"] = "'.$currency['sign'].'";
				currencies['.$currency['id_currency'].']["format"] = '.intval($currency['format']).';
				';
        }
        $content .= '
		</script>
		';
    
        // Not use id_customer
        if ($specific_price_priorities[0] == 'id_customer') {
            unset($specific_price_priorities[0]);
        }
        // Reindex array starting from 0
        $specific_price_priorities = array_values($specific_price_priorities);
    
        $content .= '<div class="panel">
		<h3>'.$this->l('Priority management').'</h3>
		<div class="alert alert-info">
				'.$this->l('Sometimes one customer can fit into multiple price rules. Priorities allow you to define which rule applies to the customer.').'
		</div>';
    
        $content .= '
		<div class="form-group">
			<label class="control-label col-lg-3" for="specificPricePriority1">'.$this->l('Priorities').'</label>
			<div class="input-group col-lg-9">
				<select id="specificPricePriority1" name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[0] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[0] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[0] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[0] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[1] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[1] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[1] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[1] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[2] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[2] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[2] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[2] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
				<span class="input-group-addon"><i class="icon-chevron-right"></i></span>
				<select name="specificPricePriority[]">
					<option value="id_shop"'.($specific_price_priorities[3] == 'id_shop' ? ' selected="selected"' : '').'>'.$this->l('Shop').'</option>
					<option value="id_currency"'.($specific_price_priorities[3] == 'id_currency' ? ' selected="selected"' : '').'>'.$this->l('Currency').'</option>
					<option value="id_country"'.($specific_price_priorities[3] == 'id_country' ? ' selected="selected"' : '').'>'.$this->l('Country').'</option>
					<option value="id_group"'.($specific_price_priorities[3] == 'id_group' ? ' selected="selected"' : '').'>'.$this->l('Group').'</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<div class="col-lg-9 col-lg-offset-3">
				<p class="checkbox">
					<label for="specificPricePriorityToAll"><input type="checkbox" name="specificPricePriorityToAll" id="specificPricePriorityToAll" />'.$this->l('Apply to all products').'</label>
				</p>
			</div>
		</div>
		<div class="panel-footer">
				<a href="'.$this->context->link->getAdminLink('AdminProducts').($page > 1 ? '&submitFilter'.$this->table.'='.(int)$page : '').'" class="btn btn-default"><i class="process-icon-cancel"></i> '.$this->l('Cancel').'</a>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproduct" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save') .'</button>
				<button id="product_form_submit_btn"  type="submit" name="submitAddproductAndStay" class="btn btn-default pull-right" disabled="disabled"><i class="process-icon-loading"></i> '.$this->l('Save and stay') .'</button>
			</div>
		</div>
		';
        return $content;
    }
    
    public function renderForm()
    {
    
        if($this->tab_display == 'Prices'){
            $currencies = Currency::getCurrencies(false, false);
            
            $this->context->smarty->assign(array(
                'currencies' => $currencies
            ));
        }
        return parent::renderForm();
    }
}