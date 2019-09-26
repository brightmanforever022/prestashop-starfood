<?php

class Search extends SearchCore
{
    public static function indexation($full = false, $id_product = false)
    {
        $db = Db::getInstance();

        if ($id_product) {
            $full = false;
        }

        if ($full && Context::getContext()->shop->getContext() == Shop::CONTEXT_SHOP) {
            $db->execute('DELETE si, sw FROM `'._DB_PREFIX_.'search_index` si
				INNER JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = si.id_product)
				'.Shop::addSqlAssociation('product', 'p').'
				INNER JOIN `'._DB_PREFIX_.'search_word` sw ON (sw.id_word = si.id_word AND product_shop.id_shop = sw.id_shop)
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1');
            $db->execute('UPDATE `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				SET p.`indexed` = 0, product_shop.`indexed` = 0
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				');
        } elseif ($full) {
            $db->execute('TRUNCATE '._DB_PREFIX_.'search_index');
            $db->execute('TRUNCATE '._DB_PREFIX_.'search_word');
            ObjectModel::updateMultishopTable('Product', array('indexed' => 0));
        } else {
            $db->execute('DELETE si FROM `'._DB_PREFIX_.'search_index` si
				INNER JOIN `'._DB_PREFIX_.'product` p ON (p.id_product = si.id_product)
				'.Shop::addSqlAssociation('product', 'p').'
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				AND '.($id_product ? 'p.`id_product` = '.(int)$id_product : 'product_shop.`indexed` = 0'));

            $db->execute('UPDATE `'._DB_PREFIX_.'product` p
				'.Shop::addSqlAssociation('product', 'p').'
				SET p.`indexed` = 0, product_shop.`indexed` = 0
				WHERE product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				AND '.($id_product ? 'p.`id_product` = '.(int)$id_product : 'product_shop.`indexed` = 0'));
        }

        // Every fields are weighted according to the configuration in the backend
        $weight_array = array(
            'pname' => Configuration::get('PS_SEARCH_WEIGHT_PNAME'),
            'reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_supplier_reference' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_ean13' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'pa_upc' => Configuration::get('PS_SEARCH_WEIGHT_REF'),
            'description_short' => Configuration::get('PS_SEARCH_WEIGHT_SHORTDESC'),
            'description' => Configuration::get('PS_SEARCH_WEIGHT_DESC'),
            'cname' => Configuration::get('PS_SEARCH_WEIGHT_CNAME'),
            'mname' => Configuration::get('PS_SEARCH_WEIGHT_MNAME'),
            'tags' => Configuration::get('PS_SEARCH_WEIGHT_TAG'),
            'attributes' => Configuration::get('PS_SEARCH_WEIGHT_ATTRIBUTE'),
            'features' => Configuration::get('PS_SEARCH_WEIGHT_FEATURE')
        );

        // Those are kind of global variables required to save the processed data in the database every X occurrences, in order to avoid overloading MySQL
        $count_words = 0;
        $query_array3 = array();

        // Retrieve the number of languages
        $total_languages = count(Language::getIDs(false));

        $sql_attribute = Search::getSQLProductAttributeFields($weight_array);
        // Products are processed 50 by 50 in order to avoid overloading MySQL
        while (($products = Search::getProductsToIndex($total_languages, $id_product, 50, $weight_array)) && (count($products) > 0)) {
            $products_array = array();
            // Now each non-indexed product is processed one by one, langage by langage
            foreach ($products as $product) {
                if ((int)$weight_array['tags']) {
                    $product['tags'] = Search::getTags($db, (int)$product['id_product'], (int)$product['id_lang']);
                }
                if ((int)$weight_array['attributes']) {
                    $product['attributes'] = Search::getAttributes($db, (int)$product['id_product'], (int)$product['id_lang']);
                }
                if ((int)$weight_array['features']) {
                    $product['features'] = Search::getFeatures($db, (int)$product['id_product'], (int)$product['id_lang']);
                }
                if ($sql_attribute) {
                    $attribute_fields = Search::getAttributesFields($db, (int)$product['id_product'], $sql_attribute);
                    if ($attribute_fields) {
                        $product['attributes_fields'] = $attribute_fields;
                    }
                }

                // Data must be cleaned of html, bad characters, spaces and anything, then if the resulting words are long enough, they're added to the array
                $product_array = array();
                foreach ($product as $key => $value) {
                    if ($key == 'attributes_fields') {
                        foreach ($value as $pa_array) {
                            foreach ($pa_array as $pa_key => $pa_value) {
                                Search::fillProductArray($product_array, $weight_array, $pa_key, $pa_value, $product['id_lang'], $product['iso_code']);
                            }
                        }
                    } else {
                        Search::fillProductArray($product_array, $weight_array, $key, $value, $product['id_lang'], $product['iso_code']);
                    }
                }

                // products id as word in search index
                $product_array[$product['id_product']] = 100;
                
                // If we find words that need to be indexed, they're added to the word table in the database
                //if (is_array($product_array) && !empty($product_array)) {
                    $query_array = $query_array2 = array();
                    foreach ($product_array as $word => $weight) {
                        if ($weight) {
                            $query_array[$word] = '('.(int)$product['id_lang'].', '.(int)$product['id_shop'].', \''.pSQL($word).'\')';
                            $query_array2[] = '\''.pSQL($word).'\'';
                        }
                    }

                    if (is_array($query_array) && !empty($query_array)) {
                        // The words are inserted...
                        $db->execute('
						INSERT IGNORE INTO '._DB_PREFIX_.'search_word (id_lang, id_shop, word)
						VALUES '.implode(',', $query_array), false);
                    }
                    $word_ids_by_word = array();
                    if (is_array($query_array2) && !empty($query_array2)) {
                        // ...then their IDs are retrieved
                        $added_words = $db->executeS('
						SELECT sw.id_word, sw.word
						FROM '._DB_PREFIX_.'search_word sw
						WHERE sw.word IN ('.implode(',', $query_array2).')
						AND sw.id_lang = '.(int)$product['id_lang'].'
						AND sw.id_shop = '.(int)$product['id_shop'], true, false);
                        foreach ($added_words as $word_id) {
                            $word_ids_by_word['_'.$word_id['word']] = (int)$word_id['id_word'];
                        }
                    }
                //}

                foreach ($product_array as $word => $weight) {
                    if (!$weight) {
                        continue;
                    }
                    if (!isset($word_ids_by_word['_'.$word])) {
                        continue;
                    }
                    $id_word = $word_ids_by_word['_'.$word];
                    if (!$id_word) {
                        continue;
                    }
                    $query_array3[] = '('.(int)$product['id_product'].','.
                        (int)$id_word.','.(int)$weight.')';
                    // Force save every 200 words in order to avoid overloading MySQL
                    if (++$count_words % 200 == 0) {
                        Search::saveIndex($query_array3);
                    }
                }

                $products_array[] = (int)$product['id_product'];
            }
            $products_array = array_unique($products_array);
            Search::setProductsAsIndexed($products_array);

            // One last save is done at the end in order to save what's left
            Search::saveIndex($query_array3);
        }
        return true;
    }
    
    protected static function getProductsToIndex($total_languages, $id_product = false, $limit = 50, $weight_array = array())
    {
        $ids = null;
        if (!$id_product) {
            // Limit products for each step but be sure that each attribute is taken into account
            $sql = 'SELECT p.id_product FROM '._DB_PREFIX_.'product p
				'.Shop::addSqlAssociation('product', 'p', true, null, true).'
				WHERE product_shop.`indexed` = 0
				AND product_shop.`visibility` IN ("both", "search")
				AND product_shop.`active` = 1
				ORDER BY product_shop.`id_product` ASC
				LIMIT '.(int)$limit;
            
            
            $res = Db::getInstance()->executeS($sql, false);
            while ($row = Db::getInstance()->nextRow($res)) {
                $ids[] = $row['id_product'];
            }
        }
        
        // Now get every attribute in every language
        $sql = 'SELECT p.id_product, pl.id_lang, pl.id_shop, l.iso_code';
        
        if (is_array($weight_array)) {
            foreach ($weight_array as $key => $weight) {
                if ((int)$weight) {
                    switch ($key) {
                        case 'pname':
                            $sql .= ', pl.name pname';
                            break;
                        case 'reference':
                            $sql .= ', p.reference';
                            break;
                        case 'supplier_reference':
                            $sql .= ', p.supplier_reference';
                            break;
                        case 'ean13':
                            $sql .= ', p.ean13';
                            break;
                        case 'upc':
                            $sql .= ', p.upc';
                            break;
                        case 'description_short':
                            $sql .= ', pl.description_short';
                            break;
                        case 'description':
                            $sql .= ', pl.description';
                            break;
                        case 'cname':
                            $sql .= ', cl.name cname';
                            break;
                        case 'mname':
                            $sql .= ', m.name mname';
                            break;
                    }
                }
            }
        }
        
        $sql .= ' FROM '._DB_PREFIX_.'product p
			LEFT JOIN '._DB_PREFIX_.'product_lang pl
				ON p.id_product = pl.id_product
			'.Shop::addSqlAssociation('product', 'p', true, null, true).'
			LEFT JOIN '._DB_PREFIX_.'category_lang cl
				ON (cl.id_category = product_shop.id_category_default AND pl.id_lang = cl.id_lang AND cl.id_shop = product_shop.id_shop)
			LEFT JOIN '._DB_PREFIX_.'manufacturer m
				ON m.id_manufacturer = p.id_manufacturer
			LEFT JOIN '._DB_PREFIX_.'lang l
				ON l.id_lang = pl.id_lang
			WHERE product_shop.indexed = 0
			AND product_shop.visibility IN ("both", "search")
			'.($id_product ? 'AND p.id_product = '.(int)$id_product : '').'
			'.($ids ? 'AND p.id_product IN ('.implode(',', array_map('intval', $ids)).')' : '').'
			AND pl.`id_shop` = product_shop.`id_shop`
        ';
        
        $productsToIndex = Db::getInstance()->executeS($sql, true, false);
        $productsGroupped = array();
        
        // collect words from all languages of product
        foreach($productsToIndex as $prodIndex){
            if( !isset($productsGroupped[ $prodIndex['id_product'] ]) ){
                $productsGroupped[ $prodIndex['id_product'] ] = array();
            }

            foreach($prodIndex as $field => $value){
                if( (strncmp($field, 'id_', 3) == 0) || (strncmp($field, 'iso_', 4) == 0) ){
                    continue;
                }
                
                if( !isset($productsGroupped[ $prodIndex['id_product'] ][ $field ]) ){
                    $productsGroupped[ $prodIndex['id_product'] ][ $field ] = '';
                }
                $productsGroupped[ $prodIndex['id_product'] ][ $field ] .= $value .' ';
            }
        }

        // populate all searchable fields with all languages words
        foreach($productsGroupped as $prodGroupId => $prodGroupWords){
            for($pi = 0; $pi < count($productsToIndex); $pi++){
                if( $productsToIndex[$pi]['id_product'] == $prodGroupId ){
                    foreach( $prodGroupWords as $field => $words ){
                        $productsToIndex[$pi][ $field ] = trim($words);
                    }
                }
            }
        }
        
        return $productsToIndex;
        
    }
    
}