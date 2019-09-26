<?php
  // Wheelronix Ltd. development team
  // site: http://www.wheelronix.com
  // mail: info@wheelronix.com
  //


class SPVBase extends ObjectModel
{
    /** @var string Name */
    public $supref_digits;
    public $supplier_id;
 
    protected $fieldsRequired = array('supref_digits');
    protected $fieldsSize = array('supref_digits' => 20);
    protected $fieldsValidate = array('supref_digits' => 'isGenericName');
    protected $table = 'spv_base';
    protected $identifier = 'id';
 
    public function getFields()
    {
        parent::validateFields();
        $fields['supref_digits'] = pSQL($this->supref_digits);
        $fields['supplier_id'] = pSQL($this->supplier_id);
        return $fields;
    }


    /**
     * Reads list of products that connected with given product
     * @param $productId id of product for that read list of connected products,
     * or array of productIds
     * @param $active flag if true only active product are selected
     * @param $moreThenOne if true, then only groups with more then 1 product in group is returned
     * @returns array(productId=>array(array('product_id', 'link_rewrite',
     * 'id_image', category_id, name -- name of product), ..)) or empty array
     */
    static function &getOtherSameProducts($productId, $langId, $active=true, $moreThenOne=true)
    {
        if (!is_array($productId))
        {
            $productId = array($productId);
        }

        $sql = 'select a.base_id, a.product_id as mainProductId, b.product_id, pl.link_rewrite, id_image, id_category_default as category_id,pl.name,cl.link_rewrite as category_link from '.
            _DB_PREFIX_.'spv_variant a inner join '.
            _DB_PREFIX_.'spv_variant b on a.base_id=b.base_id inner join '._DB_PREFIX_.'product p on b.product_id=p.id_product inner join '._DB_PREFIX_.
            'image i on i.id_product=p.id_product and cover=1 left join '._DB_PREFIX_.'product_lang pl on p.id_product=pl.id_product and pl.id_lang='.$langId.
            ' left join '._DB_PREFIX_.'category_lang cl on cl.id_category=p.id_category_default and cl.id_lang='.$langId.
            ' where a.product_id in ('.implode(',', $productId).')';;
        // additional condition so we select only active products
        if ($active)
        {
            $sql .= '  and p.active=1';
        }
        $products = Db::getInstance()->ExecuteS($sql);

        // generate result
        $result = array();
        $curProductId = 0;
        foreach($products as $product)
        {
            if ($product['mainProductId']!=$curProductId)
            {
                // remove groups with one product
                if ($curProductId>0 && $moreThenOne)
                {
                    if (count($result[$curProductId])<2)
                    {
                        unset($result[$curProductId]);
                    }
                }

                $curProductId = $product['mainProductId'];
                $result[$curProductId] = array($product);
            }
            else
            {
                $result[$product['mainProductId']] []= $product;
            }
        }

        // remove groups with one product
        if ($curProductId>0 && $moreThenOne)
        {
            if (count($result[$curProductId])<2)
            {
                unset($result[$curProductId]);
            }
        }

        return $result;
    }

    
    /**
     * Creates matching tuples for all products in database, adds new matches
     * only, doesn't delete anything
     */
    static function autoMatchAll()
    {
        // read all products
        $products = Db::getInstance()->ExecuteS('select id_product, supplier_reference, id_supplier from '._DB_PREFIX_.'product');

        // calculate common part for all products
        foreach($products as &$product)
        {
            $product['suprefCommon'] = self::extractSupRefCommon($product['supplier_reference']);
        }

        while(count($products))
        {
            $curProduct = array_shift($products);
            
            if (empty($curProduct['suprefCommon']))
            {
                // we can't match this product
                continue;
            }

            // extract product with same common part of supplier reference
            $baseTuple = array($curProduct['id_product']);
            foreach($products as $key=>$product)
            {
                if ($curProduct['id_supplier']==$product['id_supplier'] && strcmp($curProduct['suprefCommon'], $product['suprefCommon'])==0)
                {
                    $baseTuple []= $product['id_product'];
                    unset($products[$key]);
                }
            }

            
            // save tuple, only if it has more then 1 product
            if (count($baseTuple)>1)
            {
                self::insertBaseTuple($curProduct['suprefCommon'], $baseTuple, $curProduct['id_supplier']);
            }
        }
    }


    /**
     * Extracts common part (digits), used to glue products, from supplier
     * reference
     * @param $supReference supplier reference
     * @returns extracted common part or false in case if it is not possible
     */
    static function extractSupRefCommon($supReference)
    {
        if (!preg_match('/(\d{3,})/', $supReference, $matches))
        {
            return false;
        }
        return $matches[1];
    }
    

    /**
     * Adds all possible matches for given product in db, existing matches are
     * not deleted
     * @param $product product object
     */
    static function autoMatch($product)
    {
        $db = Db::getInstance();

        // check if product already in some tupple
        if (self::getProductTuppleId($product->id))
        {
            // if so do nothing
            return;
        }
        
        // extract digits from supplier reference
        $suprefDigits = self::extractSupRefCommon($product->supplier_reference);
        if (!$suprefDigits)
        {
            return;
        }

        // try to add in existing tuple, search for it
        $exsitingTupleId = $db->getValue('select id from '._DB_PREFIX_.'spv_base where supref_digits=\''.addslashes($suprefDigits).'\' and supplier_id='.$product->id_supplier);

        if ($exsitingTupleId)
        {
            // insert product in found tuple
            $db->Execute('insert into '._DB_PREFIX_.'spv_variant (base_id, product_id) values('.$exsitingTupleId.', '.$product->id.')');
            return;
        }
        
        // reading matching products
        $sql = 'select id_product from '._DB_PREFIX_.'product p left join '._DB_PREFIX_.'spv_variant v on v.product_id=p.id_product where v.id is null and '.
            'supplier_reference RLIKE \'[^0-9]'.addslashes($suprefDigits).'[^0-9]|^'.
            addslashes($suprefDigits).'[^0-9]|[^0-9]'.addslashes($suprefDigits).'$\' and p.id_product<>'.$product->id.' and p.id_supplier='.intval($product->id_supplier);
        $query = $db->ExecuteS($sql, false);

        $matchingProductIds = array();
        while($productRec = $db->nextRow($query))
        {
            $matchingProductIds []= $productRec['id_product'];
        }

        if (count($matchingProductIds))
        {
            $matchingProductIds []= $product->id;
            self::insertBaseTuple($suprefDigits, $matchingProductIds, $product->id_supplier);
        }
    }


    /**
     * @param $productId id of product from which we search for tupple id
     * @return tupple id in which product included or false if product doesn't
     * belong to any tupple
     */
    static function getProductTuppleId($productId)
    {
        return Db::getInstance()->getValue('select base_id from '._DB_PREFIX_.'spv_variant where product_id='.$productId);
    }

    
    /**
     * Saves set of connected products in database, so they are all in 1
     * base/tuple. Searches for exsiting base and adds in it, doesn't removes
     * any connections. If necessary creates new base
     * @param $supRefDigits same digits cut from sup ref
     * @param $productIds array of product ids that need to be connected
     * @param $manual flag that tells that product variant was updated manual
     * and thus should not be edited automaticaly further
     */
    static function insertBaseTuple($supRefDigits, $productIds, $supplierId, $manual=false)
    {
        $db = Db::getInstance();
        
        // determine if base exists
        $baseId = $db->getValue('select id from '._DB_PREFIX_.'spv_base where supref_digits=\''.addslashes($supRefDigits).'\' and supplier_id='.$supplierId);

        if (!$baseId)
        {
            // create base
//            echo 'insert into '._DB_PREFIX_.'spv_base(supref_digits, supplier_id) values(\''.addslashes($supRefDigits).'\', '.$supplierId.')';
            $db->Execute('insert into '._DB_PREFIX_.'spv_base(supref_digits, supplier_id) values(\''.addslashes($supRefDigits).'\', '.$supplierId.')');
            $baseId = $db->insert_ID();
        }

        if ($baseId)
        {
            // inserting connections
            $sql = 'replace '._DB_PREFIX_.'spv_variant (base_id, product_id'.($manual?', manual_update':'').') values ';
            foreach($productIds as $productId)
            {
                $sql .= '('.$baseId.', '.$productId.($manual?', 1':'').'),';
            }

            // cut last comma
            $sql = substr($sql, 0, strlen($sql)-1);
            $db->Execute($sql);
        }
        else
        {
            Logger::addLog('spv: base can\'t be inserted: commom part: '.$supRefDigits.' supplier: '.$supplierId, 4);
        }
    }


    /**
     * Updates manually updated tuples, check if it is allowed to do should be done
     * in caller function
     * Updates tuple deleting existing instance first
     */ 
    static function updateBaseTuple($baseId, $supRefDigits, $productIds, $manual=false)
    {
        Db::getInstance()->Execute('update '._DB_PREFIX_.'spv_base set supref_digits=\''.addslashes($supRefDigits).'\' where id='.$baseId);
        Db::getInstance()->Execute('delete from '._DB_PREFIX_.'spv_variant where base_id='.$baseId);

        $sql = 'insert into '._DB_PREFIX_.'spv_variant(base_id, product_id'.($manual?', manual_update':'').') values ';
        foreach($productIds as $productId)
        {
            $sql .= '('.$baseId.', '.$productId.($manual?', 1':'').'),';
        }

        // cut last comma
        $sql = substr($sql, 0, strlen($sql)-1);

        Db::getInstance()->Execute($sql);
    }


    /**
     * Deletes base/tuple and all its variants
     */
    function delete($id=null)
    {
        if ($id)
        {
            $baseId = $id;
        }
        else
        {
            $baseId = $this->id;
        }
        
        Db::getInstance()->Execute('delete from '._DB_PREFIX_.'spv_variant where base_id='.$baseId);
        Db::getInstance()->Execute('delete from '._DB_PREFIX_.'spv_base where id='.$baseId);

        return true;
    }

    function deleteProductVariant($productId)
    {
        // check if we should delete base too
        $numberOfProdsInTupple = Db::getInstance()->getRow('select count(v2.id) as num, v1.base_id from '._DB_PREFIX_.'spv_variant v1, '._DB_PREFIX_.
                                                           'spv_variant v2 where v1.base_id = v2.base_id and v1.product_id='.$productId.' group by v1.base_id');
        if ($numberOfProdsInTupple['num']==1)
        {
            // if only one variant remains we should delete tupple
            Db::getInstance()->Execute('delete from '._DB_PREFIX_.'spv_base where id='.$numberOfProdsInTupple['base_id']);
        }

        Db::getInstance()->Execute('delete from '._DB_PREFIX_.'spv_variant where product_id='.$productId);
    }
}

