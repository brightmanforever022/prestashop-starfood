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

@error_reporting(E_ERROR | E_PARSE);
@ini_set("display_errors", "ON");

$id_lang = Tools::getValue('id_lang','0');
$action = Tools::getValue('action','');
$field=Tools::getValue('field','');
$todo=Tools::getValue('todo','');

$return = "ERROR: Try again later";


// FUNCTIONS
$debug=false;
$extraVars='';
$updated_products = array();
$return_datas = array();

// Récupération de toutes les modifications à effectuer
if(!empty($_POST["rows"]) || $action=="insert")
{
    if($action!="insert")
    {
        if(_PS_MAGIC_QUOTES_GPC_)
            $_POST["rows"] = stripslashes($_POST["rows"]);
        $rows = json_decode($_POST["rows"]);
    }
    else
    {
        $rows = array();
        $rows[0] = new stdClass();
        $rows[0]->name = Tools::getValue('act','');
        $rows[0]->action = Tools::getValue('action','');
        $rows[0]->row = Tools::getValue('gr_id','');
        $rows[0]->callback = Tools::getValue('callback','');
        $rows[0]->params = $_POST;
    }

    if(is_array($rows) && count($rows)>0)
    {
        $callbacks = '';

        // Première boucle pour remplir la table sc_queue_log
        // avec toutes ces modifications
        $log_ids = array();
        $date = date("Y-m-d H:i:s");

        foreach($rows as $num => $row)
        {
            $id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params)?$row->params:array()), (!empty($row->callback)?$row->callback:null), $date);
            $log_ids[$num] = $id;
        }

        // Deuxième boucle pour effectuer les
        // actions les une après les autres

        foreach($rows as $num => $row)
        {

            if(!empty($log_ids[$num])) {
                $gr_id = intval($row->row);
                $id_product=$row->row;
                $updated_products[$id_product]=$id_product;
                $action = $row->action;

                if(!empty($row->callback))
                    $callbacks .= $row->callback.";";

                if($action!="insert")
                {
                    $_POST=array();
                    $_POST = (array) json_decode($row->params);
                }

                $todo = Tools::getValue('todo');
                $field = Tools::getValue('field');

                if ($todo!='') {
                    $needUpdateAttributeHook = false;
                    $needUpdateProductHook = false;
                    switch($field)
                    {
                        case'price':
                            $needUpdateProductHook=true;
                            if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
                            $todo=str_replace(',','.',$todo);
                            if (strpos($todo,'%')===false)
                            {
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=price'.psql($todo).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product);
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=price'.psql($todo).', indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                            }else{
                                $todo=str_replace('%','',$todo);
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=(price+price*('.psql($todo).'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product.'');
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=(price+price*('.psql($todo).'/100)), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product.' AND id_shop IN ('.SCI::getSelectedShopActionList(true).')');
                            }
                            break;
                        case'pricetax':
                            $needUpdateProductHook=true;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop=str_replace(',','.',$todo);

                            if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																														FROM '._DB_PREFIX_.'product p 
																														LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																												        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																														WHERE p.id_product = '.(int)$id_product);
                            } else {
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																														FROM '._DB_PREFIX_.'product p 
																														LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																														WHERE p.id_product = '.(int)$id_product);
                            }

                            if (strpos($todo,'%')===false)
                            {
                                foreach($productwithtaxrate as $p)
                                {
                                    if ($p['prate']==0) $p['prate']=1;
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval(($p['price']*$p['prate']+$todo)/$p['prate'])."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."'");
                                }
                            }else{
                                $todo=str_replace('%','',$todo);
                                foreach($productwithtaxrate as $p)
                                {
                                    if ($p['prate']==0) $p['prate']=1;
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET price=('.floatval((($p['price']*$p['prate'])+($p['price']*$p['prate']*($todo/100)))/$p['prate'])."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."'");
                                }
                            }

                            // pricetax pour product_shop
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                $todo = $todo_shop;
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price
																FROM '._DB_PREFIX_.'product p
																INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																WHERE p.id_product = '.(int)$id_product);

                                if (strpos($todo,'%')===false)
                                {
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=('.floatval(($p['price']*$p['prate']+$todo)/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."' AND id_shop IN (".SCI::getSelectedShopActionList(true, (int)$p['id_product']).")");
                                    }
                                }else{
                                    $todo=str_replace('%','',$todo);
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET price=('.floatval((($p['price']*$p['prate'])+($p['price']*$p['prate']*($todo/100)))/$p['prate'])."), indexed='0', date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."' AND id_shop IN (".SCI::getSelectedShopActionList(true, (int)$p['id_product']).")");
                                    }
                                }
                            }
                            break;
                        case'wholesaleprice':
                            $needUpdateProductHook=true;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop=str_replace(',','.',$todo);

                            if (strpos($todo,'%')===false)
                            {
                                $first_carac = $todo[0];
                                if(is_numeric($first_carac))
                                    $todo = "+".$todo;
                                Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product SET wholesale_price=(wholesale_price".$todo."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."'");

                            }else{
                                $todo=str_replace('%','',$todo);

                                $first_carac = $todo[0];
                                if(is_numeric($first_carac))
                                    $todo = "+".$todo;
                                Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product SET wholesale_price=(wholesale_price*(100".$todo.")/100), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."'");
                            }

                            // wholesale_price pour product_shop
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                $todo = $todo_shop;
                                if (strpos($todo,'%')===false)
                                {
                                    $first_carac = $todo[0];
                                    if(is_numeric($first_carac))
                                        $todo = "+".$todo;
                                    Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_shop SET wholesale_price=(wholesale_price".$todo."), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."' AND id_shop IN (".SCI::getSelectedShopActionList(true, (int)$id_product).")");

                                }else{
                                    $todo=str_replace('%','',$todo);

                                    $first_carac = $todo[0];
                                    if(is_numeric($first_carac))
                                        $todo = "+".$todo;
                                    Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_shop SET wholesale_price=(wholesale_price*(100".$todo.")/100), indexed=0, date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."' AND id_shop IN (".SCI::getSelectedShopActionList(true, (int)$id_product).")");
                                }

                                $sql = "SELECT id_supplier, wholesale_price FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
                                $row = Db::getInstance()->getRow($sql);
                                $id_supplier=(int)$row['id_supplier'];
                                $wholesale_price = $row["wholesale_price"];
                                //	public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
                                if ($id_supplier > 0)
                                {
                                    $id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, (int)0, (int)$id_supplier);

                                    if (!$id_product_supplier)
                                    {
                                        //create new record
                                        $product_supplier_entity = new ProductSupplier();
                                        $product_supplier_entity->id_product = (int)$id_product;
                                        $product_supplier_entity->id_product_attribute = (int)0;
                                        $product_supplier_entity->id_supplier = (int)$id_supplier;
                                        $product_supplier_entity->product_supplier_price_te = psql($wholesale_price);
                                        $product_supplier_entity->id_currency = 0;
                                        $product_supplier_entity->save();
                                    }
                                    else
                                    {
                                        $product_supplier = new ProductSupplier((int)$id_product_supplier);
                                        $product_supplier->product_supplier_price_te = psql($wholesale_price);
                                        $product_supplier->update();
                                    }
                                }
                            }
                            break;
                        case'quantity':
                            $needUpdateProductHook=true;
                            if (strpos($todo,'+')!==false) $todo=str_replace("+","",$todo);
                            $todo=str_replace(',','.',$todo);

                            if (SCAS) {
                                if (
                                    !SCI::usesAdvancedStockManagement($id_product)
                                    ||
                                    (SCI::usesAdvancedStockManagement($id_product) && !StockAvailable::dependsOnStock((int)$id_product,
                                            (int)SCI::getSelectedShop()))
                                ) {
                                    continue;
                                } else {
                                    break;
                                }
                            }

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                if (strpos($todo,'%')===false)
                                {
                                    $product = new Product($id_product, false, $id_lang);
                                    if(!$product->hasAttributes())
                                    {
                                        $shops = SCI::getSelectedShopActionList(false, $id_product);
                                        foreach($shops as $shop_id)
                                        {
                                            $id_stock_available = StockAvailable::getStockAvailableIdByProductId($id_product, null, $shop_id);
                                            if(!empty($id_stock_available))
                                                $stock_available = SCI::updateQuantity($id_product, null, $todo, $shop_id);
                                            else
                                                $stock_available = SCI::setQuantity($id_product, null, $todo, $shop_id);
                                        }
                                        SCI::hookExec('actionUpdateQuantity',
                                            array(
                                                'id_product' => $id_product,
                                                'id_product_attribute' => 0,
                                                'quantity' => $todo
                                            )
                                        );
                                    }
                                }else{
                                    $todo=str_replace('%','',$todo);
                                    $product = new Product($id_product, false, $id_lang);
                                    if(!$product->hasAttributes())
                                    {
                                        $shops = SCI::getSelectedShopActionList(false, $id_product);
                                        foreach($shops as $shop_id)
                                        {
                                            $id_stock_available = StockAvailable::getStockAvailableIdByProductId($id_product, null, $shop_id);
                                            if(!empty($id_stock_available))
                                            {
                                                $actual_qty = StockAvailable::getQuantityAvailableByProduct($id_product, null, $shop_id);

                                                if($todo>0)
                                                    $qty = -1*($actual_qty*abs($todo)/100);
                                                else
                                                    $qty = ($actual_qty*($todo)/100);

                                                $stock_available = SCI::updateQuantity($id_product, null, $qty, $shop_id);
                                            }
                                        }

                                        SCI::hookExec('actionUpdateQuantity',
                                            array(
                                                'id_product' => $id_product,
                                                'id_product_attribute' => 0,
                                                'quantity' => $qty
                                            )
                                        );
                                    }
                                }
                            } else {
                                if (strpos($todo, '%') === false) {
                                    Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product SET quantity=quantity' . $todo . ', indexed=0, date_upd="' . pSQL(date("Y-m-d H:i:s")) . '" WHERE id_product = ' . (int)$id_product);
                                    if (_s("CAT_ACTIVE_HOOK_UPDATE_QUANTITY") == "1") {
                                        $newQuantity = Db::getInstance()->ExecuteS('SELECT quantity FROM ' . _DB_PREFIX_ . 'product WHERE id_product = "' . (int)$id_product . '"');
                                        if (isset($newQuantity[0]["quantity"])) {
                                            SCI::hookExec('actionUpdateQuantity',
                                                array(
                                                    'id_product' => $id_product,
                                                    'id_product_attribute' => 0,
                                                    'quantity' => $newQuantity[0]["quantity"]
                                                )
                                            );
                                        }
                                    }
                                } else {
                                    $todo = str_replace('%', '', $todo);
                                    Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product SET quantity=FLOOR(quantity+quantity*(' . $todo . '/100)), indexed=0, date_upd="' . pSQL(date("Y-m-d H:i:s")) . '" WHERE id_product = ' . (int)$id_product);
                                }
                            }

                            break;
                        case'margin':
                            $needUpdateProductHook=true;
                            $method=_s('CAT_PROD_GRID_MARGIN_OPERATION');
                            switch($method)
                            {
                                case 0:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // update pour product
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price+'.floatval($todo).' 
													WHERE p.id_product = '.(int)$id_product.'
													AND p.wholesale_price>0
													AND NOT EXISTS (
														SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
													)');
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute
														SET price=wholesale_price+'.(floatval($todo)-$pet)."
														WHERE id_product ='".(int)$p['id_product']."'
														AND wholesale_price>0");
                                        }

                                        // update pour product_shop
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=ps.wholesale_price+'.floatval($todo).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product = '.(int)$id_product.'
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');

                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price+'.(floatval($todo)-$pet)."
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price+'.floatval($todo).' , indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product = '.(int)$id_product.' 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																															WHERE p.id_product = '.(int)$id_product.'
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																													    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																															WHERE p.id_product = '.(int)$id_product.'
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            $p['prate']=floatval($p['prate']);
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.$p['prate'].'+'.(floatval($todo)*$p['prate']-$pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."' 
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price+'.(floatval($todo)-$pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'  
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 1:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price*'.floatval($todo).')/100+p.wholesale_price), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
													WHERE p.id_product = '.(int)$id_product.'
													AND p.wholesale_price>0
													AND NOT EXISTS (
														SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
													)');

                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".(int)$p['id_product']."' 
														AND wholesale_price>0");
                                        }

                                        // update pour product_shop
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=((ps.wholesale_price*'.floatval($todo).')/100+ps.wholesale_price), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product = '.(int)$id_product.'
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');

                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price*'.floatval($todo).')/100+p.wholesale_price), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product = '.(int)$id_product.'
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 2:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.floatval($todo).' , indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product = '.(int)$id_product.'
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																				)');

                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".(int)$p['id_product']."' 
														AND wholesale_price>0");
                                        }

                                        // update pour product_shop
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=ps.wholesale_price*'.floatval($todo).' , indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product = '.(int)$id_product.'
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');

                                        // product attribute shop
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price*'.(floatval($todo)).'-'.($pet)."
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.floatval($todo).' , indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product = '.(int)$id_product.' 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.($p['prate']*floatval($todo)).'-'.($pit).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 3:
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product);
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product);
                                    }
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).' , indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.(int)$p['id_product'].'
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.floatval($pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.(int)$p['id_product'].'
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");

                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                                // product_shop
                                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price 
                                                                                FROM '._DB_PREFIX_.'product p 
                                                                                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
                                                                                        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                                            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                                WHERE p.id_product = '.(int)$id_product);

                                                foreach($productwithtaxrate as $p)
                                                {
                                                    if ($p['prate']==0) $p['prate']=1;
                                                    $pit=$p['price']*$p['prate'];
                                                    $pet=$p['price'];
                                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop p SET p.price=p.wholesale_price*'.(floatval($todo)/$p['prate']).', indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
                                                                WHERE p.id_product = '.(int)$p['id_product'].'
                                                                AND p.wholesale_price>0
                                                                AND p.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).')
                                                                AND NOT EXISTS (
                                                                    SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
                                                                )');

                                                }

                                                // product_attribute_shop
                                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
                                                                                FROM '._DB_PREFIX_.'product p
                                                                                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
                                                                                        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                                            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
                                                                                WHERE p.id_product = '.(int)$id_product);

                                                foreach($productwithtaxrate as $p)
                                                {
                                                    if ($p['prate']==0) $p['prate']=1;
                                                    $pit=$p['price']*$p['prate'];
                                                    $pet=$p['price'];
                                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
                                                                SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)."
                                                                WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
                                                                AND p.id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
                                                                AND wholesale_price>0");

                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 4:
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product);
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product);
                                    }
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.(int)$id_product.' 
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																						WHERE p.id_product = '.(int)$id_product.' 
																						AND p.wholesale_price>0
																						AND NOT EXISTS (
																							SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																							)');
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                            // product_shop
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price 
																			FROM '._DB_PREFIX_.'product p 
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																			WHERE p.id_product = '.(int)$id_product);

                                            foreach($productwithtaxrate as $p)
                                            {
                                                if ($p['prate']==0) $p['prate']=1;
                                                $pit=$p['price']*$p['prate'];
                                                $pet=$p['price'];
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop p SET p.price=((p.wholesale_price+(p.wholesale_price*'.floatval($todo).')/100)/'.$p['prate'].'),  date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
															WHERE p.id_product = '.(int)$p['id_product'].'
															AND p.wholesale_price>0
															AND p.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).')
															AND NOT EXISTS (
																SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
															)');

                                            }

                                            // product_attribute_shop
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
																			FROM '._DB_PREFIX_.'product p
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																				INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																			WHERE p.id_product = '.(int)$id_product);
                                            foreach($productwithtaxrate as $p)
                                            {
                                                if ($p['prate']==0) $p['prate']=1;
                                                $pit=$p['price']*$p['prate'];
                                                $pet=$p['price'];
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' 
//															AND p.id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");

                                            }
                                        }
                                    }
                                    break;
                                case 5:
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product p SET p.price=((100*p.wholesale_price)/(100-'.floatval($todo).')), indexed="0", date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
																				WHERE p.id_product = '.(int)$id_product.' 
																				AND p.wholesale_price>0
																				AND NOT EXISTS (
																					SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																					)');
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.' 
																		AND NOT EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
                                    } elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND NOT EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND NOT EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                    }
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                            // update pour product_shop
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop ps SET ps.price=((100*ps.wholesale_price)/(100-'.floatval($todo).')), indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
														WHERE ps.id_product = '.(int)$id_product.'
														AND ps.id_shop IN ('.pSQL(SCI::getSelectedShopActionList(true)).')
														AND ps.wholesale_price>0
														AND NOT EXISTS (
															SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=ps.id_product
														)');

                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price,pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                            foreach($productwithtaxrate as $p)
                                            {
                                                $pet=$p['price'];
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                            }
                                        }

                                    }
                                break;
                            }
                            break;
                        case'margin_combi':
                            $needUpdateProductHook=true;
                            $method=_s('CAT_PROD_GRID_MARGIN_OPERATION');
                            switch($method)
                            {
                                case 0:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute
														SET price=wholesale_price+'.(floatval($todo)-$pet)."
														WHERE id_product ='".(int)$p['id_product']."'
														AND wholesale_price>0");
                                        }

                                        // product attribute shop
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price+'.(floatval($todo)-$pet)."
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																															WHERE p.id_product = '.(int)$id_product.'
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																															FROM '._DB_PREFIX_.'product p 
																															LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																													    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																															WHERE p.id_product = '.(int)$id_product.'
																															AND EXISTS (
																																	SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																	)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            $p['prate']=floatval($p['prate']);
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.$p['prate'].'+'.(floatval($todo)*$p['prate']-$pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."' 
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price+'.(floatval($todo)-$pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'  
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 1:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".(int)$p['id_product']."' 
														AND wholesale_price>0");
                                        }

                                        // product attribute shop
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price*'.(floatval($todo)/100).'+wholesale_price)-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 2:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.'
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
														SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
														WHERE id_product ='".(int)$p['id_product']."' 
														AND wholesale_price>0");
                                        }

                                        // product attribute shop
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                        foreach($productwithtaxrate as $p)
                                        {
                                            $pet=$p['price'];
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=wholesale_price*'.(floatval($todo)).'-'.($pet)."
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                        }
                                    } else {
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }else{
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                        foreach($productwithtaxrate as $p)
                                        {
                                            if ($p['prate']==0) $p['prate']=1;
                                            $pit=$p['price']*$p['prate'];
                                            $pet=$p['price'];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                            {
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.($p['prate']*floatval($todo)).'-'.($pit).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }else{
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.($pet).", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                            }
                                        }
                                    }
                                    break;
                                case 3:
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product);
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product);
                                    }
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)).'-'.floatval($pit)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");

                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {

                                                // product_attribute_shop
                                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
                                                                                FROM '._DB_PREFIX_.'product p
                                                                                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
                                                                                        LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                                            LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
                                                                                WHERE p.id_product = '.(int)$id_product);

                                                foreach($productwithtaxrate as $p)
                                                {
                                                    if ($p['prate']==0) $p['prate']=1;
                                                    $pit=$p['price']*$p['prate'];
                                                    $pet=$p['price'];
                                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
                                                                SET price=wholesale_price*'.(floatval($todo)*$p['prate']).'-'.floatval($pet)."
                                                                WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
                                                                AND p.id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
                                                                AND wholesale_price>0");

                                                }
                                            }
                                        }
                                    }
                                    break;
                                case 4:
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product);
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product);
                                    }

                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                            // product_attribute_shop
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute
																			FROM '._DB_PREFIX_.'product p
																				INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																					LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																			   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																				INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																			WHERE p.id_product = '.(int)$id_product);
                                            foreach($productwithtaxrate as $p)
                                            {
                                                if ($p['prate']==0) $p['prate']=1;
                                                $pit=$p['price']*$p['prate'];
                                                $pet=$p['price'];
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=((wholesale_price+((wholesale_price*'.floatval($todo).')/100))-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' 
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");

                                            }
                                        }
                                    }
                                    break;
                                case 5:
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        // product attribute
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.' 
																		AND NOT EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
                                        if(empty($productwithtaxrate)) {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price
																		FROM '._DB_PREFIX_.'product p 
																		WHERE p.id_product = '.(int)$id_product.' 
																		AND EXISTS (
																				SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																		)');
                                        }
                                    } elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND NOT EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        if(empty($productwithtaxrate)) {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
																														    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																																WHERE p.id_product = '.(int)$id_product.'
																																AND EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                    }else{
                                        $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM '._DB_PREFIX_.'product p 
																																LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = '.(int)$id_product.'
																																AND NOT EXISTS (
																																		SELECT pa.id_product FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        if(empty($productwithtaxrate)) {
                                            $productwithtaxrate = Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price 
																																FROM ' . _DB_PREFIX_ . 'product p 
																																LEFT JOIN ' . _DB_PREFIX_ . 'tax t ON (p.id_tax=t.id_tax) 
																																WHERE p.id_product = ' . (int)$id_product . '
																																AND NOT EXISTS (
																																		SELECT pa.id_product FROM ' . _DB_PREFIX_ . 'product_attribute pa WHERE pa.id_product=p.id_product
																																		)');
                                        }
                                    }
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $pit=$p['price']*$p['prate'];
                                        $pet=$p['price'];
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                        {
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))*'.$p['prate'].'-'.$pit."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }else{
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
																						SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet."), date_upd='".pSQL(date("Y-m-d H:i:s"))."'
																						WHERE id_product ='".(int)$p['id_product']."' 
																						AND wholesale_price>0");
                                        }

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                            $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price,pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (pa.id_product=p.id_product)
																	WHERE p.id_product = '.(int)$id_product);

                                            foreach($productwithtaxrate as $p)
                                            {
                                                $pet=$p['price'];
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
															SET price=(((100*wholesale_price)/(100-'.(floatval($todo)).'))-'.$pet.")
															WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
															AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true, (int)$p['id_product'])).")
															AND wholesale_price>0");
                                            }
                                        }

                                    }
                                break;
                            }
                            break;
                        case'combi_price':
                            $needUpdateAttributeHook=true;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop = $todo;

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,p.price,pa.id_product_attribute,pa.price as price_pa
																FROM '._DB_PREFIX_.'product p 
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																WHERE p.id_product = '.(int)$id_product);
                            } elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) {
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
                                                                FROM '._DB_PREFIX_.'product p 
                                                                LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                    LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
                                                                WHERE p.id_product = '.(int)$id_product);
                            }else{
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
                                                                FROM '._DB_PREFIX_.'product p 
                                                                    LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
                                                                WHERE p.id_product = '.(int)$id_product);
                            }

                            if (strpos($todo,'%')===false)
                            {
                                foreach($productwithtaxrate as $p)
                                {
                                    if ($p['prate']==0) $p['prate']=1;
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $todotax=$todo*$p['prate'];
                                    }else{
                                        $todotax=$todo;
                                    }
                                    if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.pSQL($todotax).", date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."' AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
                                    } else {
                                        Db::getInstance()->Execute('UPDATE ' . _DB_PREFIX_ . 'product_attribute SET price=price' . psql($todotax) . ", date_upd='" . pSQL(date("Y-m-d H:i:s")) . "' WHERE id_product ='" . (int)$p['id_product'] . "'");
                                    }
                                }
                            }else{
                                $todo=str_replace('%','',$todo);
                                foreach($productwithtaxrate as $p)
                                {
                                    $add = ($p["price"]+$p["price_pa"])*($todo/100);
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
                                                                        SET price=price+'.$add.",  date_upd='".pSQL(date("Y-m-d H:i:s"))."'
                                                                        WHERE id_product ='".(int)$p['id_product']."'");
                                    }else{
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
                                                                        SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
                                                                        WHERE id_product ='".(int)$p['id_product']."'
                                                                            AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
                                    }
                                }
                            }

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                // product attribute shop
                                $todo = $todo_shop;
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT p.id_product,ps.price, pa.id_product_attribute,pas.price as price_pa
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND ps.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																			INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product = '.(int)$id_product);
                                if (strpos($todo,'%')===false)
                                {
                                    foreach($productwithtaxrate as $p)
                                    {
                                        $todotax=$todo;
                                        if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET price=price'.pSQL($todotax)." WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
                                    }
                                }else{
                                    $todo=str_replace('%','',$todo);
                                    foreach($productwithtaxrate as $p)
                                    {
                                        $add = ($p["price"]+$p["price_pa"])*($todo/100);
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
													SET price=price+'.$add."
													WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
														AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
                                    }
                                }
                            }
                            break;
                        case'combi_pricetax':
                            $needUpdateAttributeHook=true;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop = $todo;

                            if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                            {
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																	LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax) 
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product = '.(int)$id_product);
                            }else{
                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT 1+(t.rate/100) AS prate,p.id_product,p.price ,pa.id_product_attribute,pa.price as price_pa
																	FROM '._DB_PREFIX_.'product p 
																	LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
															   			LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
																	INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																	WHERE p.id_product = '.(int)$id_product);
                            }

                            if (strpos($todo,'%')===false)
                            {
                                foreach($productwithtaxrate as $p)
                                {
                                    if ($p['prate']==0) $p['prate']=1;
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
                                    {
                                        $todotax=$todo;
                                    }else{
                                        $todotax=$todo/$p['prate'];
                                    }
                                    if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.pSQL($todotax).", date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."'");
                                    } else {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET price=price'.psql($todotax)." , date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$p['id_product']."' AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
                                    }
                                }
                            }else{
                                $todo=str_replace('%','',$todo);
                                foreach($productwithtaxrate as $p)
                                {
                                    if ($p['prate']==0) $p['prate']=1;
                                    $add = ($p["price"]+$p["price_pa"])*($todo/100);
                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
                                                                        SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
                                                                        WHERE id_product ='".(int)$p['id_product']."'");
                                    }else{
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute 
                                                                        SET price=price+'.$add.", date_upd='".pSQL(date("Y-m-d H:i:s"))."'
                                                                        WHERE id_product ='".(int)$p['id_product']."'
                                                                            AND id_product_attribute ='".(int)$p['id_product_attribute']."'");
                                    }
                                }
                            }

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                // product attribute shop
                                $todo = $todo_shop;

                                $productwithtaxrate=Db::getInstance()->ExecuteS('SELECT DISTINCT 1+(t.rate/100) AS prate,p.id_product,ps.price,pa.id_product_attribute,pas.price as price_pa
																	FROM '._DB_PREFIX_.'product p
                                                                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product AND id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
                                                                    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                    RIGHT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
                                                                    INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product = '.(int)$id_product);
                                if (strpos($todo,'%')===false)
                                {
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $todotax=$todo/$p['prate'];
                                        if (strpos($todotax,'-')===false && strpos($todotax,'+')===false) $todotax='+'.$todotax;
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET price=price'.pSQL($todotax)." WHERE id_product_attribute ='".(int)$p['id_product_attribute']."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");
                                    }
                                }else{
                                    $todo=str_replace('%','',$todo);
                                    foreach($productwithtaxrate as $p)
                                    {
                                        if ($p['prate']==0) $p['prate']=1;
                                        $add = (($p["price"])+($p["price_pa"]))*($todo/100);
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop
													SET price=price+'.$add."
													WHERE id_product_attribute ='".(int)$p['id_product_attribute']."'
														AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")");

                                    }
                                }
                            }


                            break;
                        case'combi_wholesaleprice':
                            $needUpdateAttributeHook=true;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop = $todo;
                            if (strpos($todo,'%')===false)
                            {
                                $first_carac = $todo[0];
                                if(is_numeric($first_carac))
                                    $todo = "+".$todo;
                                Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_attribute SET wholesale_price=(wholesale_price".$todo."), date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."'");

                            }else{
                                $todo=str_replace('%','',$todo);

                                $first_carac = $todo[0];
                                if(is_numeric($first_carac))
                                    $todo = "+".$todo;
                                Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_attribute SET wholesale_price=(wholesale_price*(100".$todo.")/100), date_upd='".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product ='".(int)$id_product."'");
                            }

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                // product attribute shop
                                $todo = $todo_shop;
                                if (strpos($todo,'%')===false)
                                {
                                    $first_carac = $todo[0];
                                    if(is_numeric($first_carac))
                                        $todo = "+".$todo;
                                    Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_attribute_shop pas
                                    INNER JOIN "._DB_PREFIX_."product_attribute pa ON (pas.id_product_attribute=pa.id_product_attribute AND pa.id_product ='".(int)$id_product."')
                                    SET pas.wholesale_price=(pas.wholesale_price".$todo.")
                                    WHERE pas.id_shop IN (".SCI::getSelectedShopActionList(true, (int)$id_product).")");

                                }else{
                                    $todo=str_replace('%','',$todo);

                                    $first_carac = $todo[0];
                                    if(is_numeric($first_carac))
                                        $todo = "+".$todo;
                                    Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_attribute_shop pas
                                    INNER JOIN "._DB_PREFIX_."product_attribute pa ON (pas.id_product_attribute=pa.id_product_attribute AND pa.id_product ='".(int)$id_product."')
                                    SET pas.wholesale_price=(pas.wholesale_price*(100".$todo.")/100)
                                    WHERE pas.id_shop IN (".SCI::getSelectedShopActionList(true, (int)$id_product).")");
                                }

                                $sql = "SELECT id_supplier FROM "._DB_PREFIX_."product WHERE id_product=".intval($id_product);
                                $row = Db::getInstance()->getRow($sql);
                                $id_supplier=(int)$row['id_supplier'];
                                //	public function addSupplierReference($id_supplier, $id_product_attribute, $supplier_reference = null, $price = null, $id_currency = null)
                                if ($id_supplier > 0)
                                {
                                    $sql = "SELECT id_product_attribute, wholesale_price FROM "._DB_PREFIX_."product_attribute WHERE id_product=".intval($id_product);
                                    $product_attributes = Db::getInstance()->executeS($sql);
                                    foreach ($product_attributes as $product_attribute)
                                    {
                                        $id_product_attribute = $product_attribute["id_product_attribute"];
                                        $wholesale_price = $product_attribute["wholesale_price"];

                                        $id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$id_product, (int)$id_product_attribute, (int)$id_supplier);

                                        if (!$id_product_supplier)
                                        {
                                            //create new record
                                            $product_supplier_entity = new ProductSupplier();
                                            $product_supplier_entity->id_product = (int)$id_product;
                                            $product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
                                            $product_supplier_entity->id_supplier = (int)$id_supplier;
                                            $product_supplier_entity->product_supplier_price_te = psql($wholesale_price);
                                            $product_supplier_entity->id_currency = 0;
                                            $product_supplier_entity->save();
                                        }
                                        else
                                        {
                                            $product_supplier = new ProductSupplier((int)$id_product_supplier);
                                            $product_supplier->product_supplier_price_te = psql($wholesale_price);
                                            $product_supplier->update();
                                        }
                                    }
                                }
                            }
                            break;
                        case'combi_quantity':
                            $needUpdateAttributeHook=true;
                            if (strpos($todo,'-')===false && strpos($todo,'+')===false) $todo='+'.$todo;
                            $todo=str_replace(',','.',$todo);
                            $todo_shop = $todo;

                            if (strpos($todo,'%')===false)
                            {
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=quantity'.$todo.', date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product);
                            }else{
                                $todo=str_replace('%','',$todo);
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET quantity=FLOOR(quantity+quantity*('.$todo.'/100)), date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product);
                            }

                            Db::getInstance()->Execute('
                                    UPDATE `'._DB_PREFIX_.'product`
                                    SET `quantity` =
                                        (
                                        SELECT SUM(`quantity`)
                                        FROM `'._DB_PREFIX_.'product_attribute`
                                        WHERE `id_product` = '.(int)$id_product.'
                                        ), date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
                                    WHERE `id_product` = '.(int)$id_product);

                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                // product attribute shop
                                $todo = $todo_shop;
                                if (strpos($todo,'+')!==false) $todo=str_replace("+","",$todo);
                                $product_attrs=Db::getInstance()->ExecuteS('SELECT p.id_product,pa.id_product_attribute
																	FROM '._DB_PREFIX_.'product p
																		INNER JOIN '._DB_PREFIX_.'product_attribute pa ON (p.id_product = pa.id_product)
																			INNER JOIN '._DB_PREFIX_.'product_attribute_shop pas ON (pa.id_product_attribute = pas.id_product_attribute AND pas.id_shop = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():'p.id_shop_default').')
																	WHERE p.id_product = '.(int)$id_product);

                                if(SCAS)
                                {
                                    $product_attrs_temp = array();
                                    foreach($productliststr_temp as $product_attr)
                                    {
                                        $c = new Combination((int)$product_attr);
                                        if(
                                            !SCI::usesAdvancedStockManagement($c->id_product)
                                            ||
                                            (SCI::usesAdvancedStockManagement($c->id_product) && !StockAvailable::dependsOnStock((int)$c->id_product, (int)SCI::getSelectedShop())))
                                        {
                                            $product_attrs_temp[] = $product_attr;
                                        }
                                    }
                                    $productliststr_temp = $productliststr_temp;
                                }

                                if (strpos($todo,'%')===false)
                                {
                                    foreach($product_attrs as $product_attr)
                                    {
                                        $shops = SCI::getSelectedShopActionList(false, $product_attr["id_product"]);
                                        foreach($shops as $shop_id)
                                        {
                                            $id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);
                                            if(!empty($id_stock_available))
                                                $stock_available = SCI::updateQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $todo, $shop_id);
                                            else
                                                $stock_available = SCI::setQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $todo, $shop_id);
                                        }
                                    }
                                }else{
                                    $todo=str_replace('%','',$todo);
                                    foreach($product_attrs as $product_attr)
                                    {
                                        $shops = SCI::getSelectedShopActionList(false, $product_attr["id_product"]);
                                        foreach($shops as $shop_id)
                                        {
                                            $id_stock_available = StockAvailable::getStockAvailableIdByProductId($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);
                                            if(!empty($id_stock_available))
                                            {
                                                $actual_qty = StockAvailable::getQuantityAvailableByProduct($product_attr["id_product"], $product_attr["id_product_attribute"], $shop_id);

                                                if($todo>0)
                                                    $qty = -1*($actual_qty*abs($todo)/100);
                                                else
                                                    $qty = ($actual_qty*($todo)/100);

                                                $stock_available = SCI::updateQuantity($product_attr["id_product"], $product_attr["id_product_attribute"], $qty, $shop_id);
                                            }
                                        }
                                    }
                                }

                                SCI::qtySumStockAvailable($id_product);
                            }

                            break;
                        case'defaultcombination':
                            $needUpdateAttributeHook=true;
                            $todo=Tools::getValue('todo','');
                            switch($todo)
                            {
                                case'cheapest':
                                    if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        $shops = SCI::getSelectedShopActionList(false, $id_product);
                                        foreach($shops as $shop_id)
                                        {
                                            if(version_compare(_PS_VERSION_, '1.6.1', '>='))
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=NULL WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$id_product.')');
                                            else
                                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=0 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$id_product.')');
                                            $ida=Db::getInstance()->getValue('SELECT pas2.id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop pas2 WHERE pas2.id_shop = "'.(int)$shop_id.'" AND pas2.id_product_attribute IN (SELECT pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product = '.(int)$id_product.') ORDER BY pas2.price ASC');
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=1 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute = '.(int)$ida);
                                        }
                                    } else {
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = '.(int)$id_product);
                                        $res=Db::getInstance()->ExecuteS('SELECT pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
                                        WHERE pa.id_product = '.(int)$id_product.' AND pa.id_product_attribute=(
                                                SELECT pa2.id_product_attribute FROM  `'._DB_PREFIX_.'product_attribute` pa2
                                                WHERE pa.id_product=pa2.id_product ORDER BY price LIMIT 1)');
                                        $list=array();
                                        foreach($res as $r)
                                        {
                                            $list[]=$r['id_product_attribute'];
                                        }

                                        if (count($list))
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=1, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
                                                                                        WHERE id_product_attribute IN ('.join(',',$list).')');
                                    }
                                case'instockandcheapest':
                                    if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        $shops = SCI::getSelectedShopActionList(false, $id_product);
                                        foreach($shops as $shop_id)
                                        {
                                            $res = Db::getInstance()->ExecuteS('SELECT pa.id_product,pas2.id_product_attribute FROM '._DB_PREFIX_.'product_attribute_shop pas2
																										LEFT JOIN '._DB_PREFIX_.'product_attribute pa ON (pas2.id_product_attribute = pa.id_product_attribute)
																										WHERE id_shop = "'.(int)$shop_id.'"
																											 AND pas2.id_product_attribute IN (SELECT pa.id_product_attribute FROM '._DB_PREFIX_.'product_attribute pa WHERE pa.id_product = '.(int)$id_product.') ORDER BY pas2.price ASC');
                                            foreach($res as $row)
                                            {
                                                $qty = SCI::_getProductAttributeQty($row['id_product'],$row['id_product_attribute']);
                                                if ($qty > 0)
                                                {
                                                    if(version_compare(_PS_VERSION_, '1.6.1', '>='))
                                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=NULL WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$row['id_product'].')');
                                                    else
                                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=0 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute IN (SELECT id_product_attribute FROM '._DB_PREFIX_.'product_attribute WHERE id_product = '.(int)$row['id_product'].')');
                                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on=1 WHERE id_shop = "'.(int)$shop_id.'" AND id_product_attribute = '.(int)$row['id_product_attribute']);
                                                    break;
                                                }
                                            }
                                        }
                                    } else {
                                        $res=Db::getInstance()->ExecuteS('SELECT pa.id_product, pa.id_product_attribute FROM `'._DB_PREFIX_.'product_attribute` pa
                                        WHERE pa.id_product = '.(int)$id_product.' AND pa.id_product_attribute=(
                                                SELECT pa2.id_product_attribute FROM  `'._DB_PREFIX_.'product_attribute` pa2
                                                WHERE pa.id_product=pa2.id_product AND pa2.quantity > 0 ORDER BY price LIMIT 1)');
                                        $list=array();
                                        $listProducts=array();
                                        foreach($res as $r)
                                        {
                                            $list[]=$r['id_product_attribute'];
                                            $listProducts[]=$r['id_product'];
                                        }
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product IN ('.psql(join(',',$listProducts)).')');

                                        if (count($list))
                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET default_on=1, date_upd="'.pSQL(date("Y-m-d H:i:s")).'"
                                                                                        WHERE id_product_attribute IN ('.join(',',$list).')');
                                    }
                                    break;
                            }
                            break;
                        case'mass_round':
                            $needUpdateProductHook=true;
                            $todo=Tools::getValue('todo','0');
                            $column=Tools::getValue('column','');

                            if(!empty($todo) && !empty($column))
                            {
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                    $old_price = array(
                                        "product"=>0,
                                        "shops" => array()
                                    );
                                    $tax_rates = array(
                                        "product"=>0,
                                        "shops" => array()
                                    );
                                    $ecotaxes = array(
                                        "product"=>0,
                                        "shops" => array()
                                    );
                                } else {
                                    $old_price = array(
                                        "product"=>0
                                    );
                                    $tax_rates = array(
                                        "product"=>0
                                    );
                                    $ecotaxes = array(
                                        "product"=>0
                                    );
                                }


                                // Récupération du prix à modifier
                                if($column=="price" || $column=="wholesale_price")
                                {
                                    $sql = 'SELECT '.$column.' FROM '._DB_PREFIX_.'product WHERE id_product = "'.(int)$id_product.'"';
                                    $rslt = Db::getInstance()->ExecuteS($sql);
                                    if(isset($rslt[0][$column]))
                                        $old_price["product"]=$rslt[0][$column];

                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
                                        $shops = SCI::getSelectedShopActionList(false, (int)$id_product);
                                        foreach($shops as $shop)
                                        {
                                            $sql = 'SELECT '.$column.' FROM '._DB_PREFIX_.'product_shop WHERE id_product = "'.(int)$id_product.'" AND id_shop = "'.(int)$shop.'"';
                                            $rslt = Db::getInstance()->ExecuteS($sql);
                                            if(isset($rslt[0][$column]))
                                                $old_price["shops"][$shop]=$rslt[0][$column];
                                        }
                                    }
                                }
                                elseif($column=="price_inc_tax")
                                {
                                    if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                    {
                                        $sql = 'SELECT 1+(t.rate/100) AS prate,p.price,p.ecotax
                                            FROM '._DB_PREFIX_.'product p
                                            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                            WHERE p.id_product = "'.(int)$id_product.'"';
                                    }elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
                                    {
                                        $sql = 'SELECT 1+(t.rate/100) AS prate,p.price,p.ecotax
                                            FROM '._DB_PREFIX_.'product p
                                            LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax)
                                            WHERE p.id_product = "'.(int)$id_product.'"';
                                    }else{
                                        $sql = 'SELECT 1+(t.rate/100) AS prate,p.price, "0" AS ecotax
                                            FROM '._DB_PREFIX_.'product p
                                            LEFT JOIN '._DB_PREFIX_.'tax t ON (p.id_tax=t.id_tax)
                                            WHERE p.id_product = "'.(int)$id_product.'"';
                                    }
                                    $rslt = Db::getInstance()->ExecuteS($sql);
                                    $ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
                                    $ecotaxes["product"]=$ecotax;
                                    if(empty($rslt[0]["prate"]))
                                        $rslt[0]["prate"] = 1;
                                    if(isset($rslt[0]["price"]) && isset($rslt[0]["prate"]))
                                        $old_price["product"]=($rslt[0]["price"]*$rslt[0]["prate"])+$ecotax;
                                    if(isset($rslt[0]["prate"]))
                                        $tax_rates["product"]=$rslt[0]["prate"];

                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    {
                                        $shops = SCI::getSelectedShopActionList(false, (int)$id_product);
                                        foreach($shops as $shop)
                                        {
                                            $sql = 'SELECT 1+(t.rate/100) AS prate,ps.price, ps.ecotax
                                                                    FROM '._DB_PREFIX_.'product p
                                                                    INNER JOIN '._DB_PREFIX_.'product_shop ps ON (p.id_product = ps.id_product)
                                                                    LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (ps.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                                        LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                                                    WHERE ps.id_product = "'.(int)$id_product.'" AND ps.id_shop = "'.(int)$shop.'"';
                                            $rslt = Db::getInstance()->ExecuteS($sql);
                                            if(empty($rslt[0]["prate"]))
                                                $rslt[0]["prate"] = 1;
                                            if(isset($rslt[0]["price"]) && isset($rslt[0]["prate"]))
                                            {
                                                $ecotax = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
                                                $old_price["shops"][$shop]=($rslt[0]["price"]*$rslt[0]["prate"])+$ecotax;
                                                $tax_rates["shops"][$shop]=$rslt[0]["prate"];
                                                $ecotaxes["shops"][$shop]=$ecotax;
                                            }
                                        }
                                    }
                                }

                                // Arrondir le prix
                                $new_price = SCI::roundPrice($old_price["product"], $todo);
                                $update_column = $column;
                                if($column=="price_inc_tax")
                                {
                                    $update_column = "price";
                                    if(!empty($tax_rates["product"]))
                                        $new_price = floatval(($new_price-$ecotaxes["product"])/$tax_rates["product"]);
                                    else
                                        $new_price = floatval($new_price-$ecotaxes["product"]);
                                }
                                Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product SET '.$update_column.'="'.pSQL($new_price).'", indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = "'.(int)$id_product.'"');

                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    foreach($old_price["shops"] as $shop=>$price)
                                    {
                                        $new_price = SCI::roundPrice($price, $todo);
                                        if($column=="price_inc_tax")
                                        {
                                            if(!empty($tax_rates["shops"][$shop]))
                                                $new_price = floatval(($new_price-$ecotaxes["shops"][$shop])/$tax_rates["shops"][$shop]);
                                            else
                                                $new_price = floatval($new_price-$ecotaxes["shops"][$shop]);
                                        }
                                        Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_shop SET '.$update_column.'="'.pSQL($new_price).'", indexed=0, date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product = "'.(int)$id_product.'" AND id_shop = "'.(int)$shop.'"');
                                    }
                                }
                            }
                            break;
                        case'mass_round_combi':
                            $needUpdateAttributeHook=true;
                            $todo=Tools::getValue('todo','0');
                            $column=Tools::getValue('column','');

                            if($column=="price")
                                $column = "priceextax";
                            elseif($column=="price_inc_tax")
                                $column = "price";

                            if(!empty($todo) && !empty($column))
                            {
                                // PRODUCT
                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $sql='SELECT t.rate,p.price,p.ecotax,p.id_shop_default
                                            FROM `'._DB_PREFIX_.'product` p
                                            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                            WHERE p.id_product='.(int)$id_product;
                                }
                                elseif (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                {
                                    $sql='SELECT t.rate,p.price,p.ecotax
                                            FROM `'._DB_PREFIX_.'product` p
                                            LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (p.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
                                                LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
                                            WHERE p.id_product='.(int)$id_product;
                                }
                                elseif (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
                                {
                                    $sql='SELECT t.rate,p.price,p.ecotax 
                                            FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t 
                                            WHERE 
                                                p.id_product='.(int)$id_product.' 
                                                AND t.id_tax=p.id_tax';
                                }
                                else
                                {
                                    $sql='SELECT t.rate,p.price, "0" AS ecotax 
                                            FROM `'._DB_PREFIX_.'product` p, `'._DB_PREFIX_.'tax` t 
                                            WHERE 
                                                p.id_product='.(int)$id_product.'
                                                AND t.id_tax=p.id_tax';
                                }

                                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                {
                                    $p_shops=Db::getInstance()->executeS($sql);
                                    $pprice_shops = array();
                                    $taxrate_shops = array();
                                    $ecotax_shops = array();
                                    foreach($p_shops as $p_shop)
                                    {
                                        if(empty($p_shop['rate']))
                                            $p_shop['rate'] = 0;
                                        $pprice_shops[$p_shop["id_shop"]] = $p_shop['price'];
                                        $taxrate_shops[$p_shop["id_shop"]] = $p_shop['rate'];
                                        $ecotax_shops[$p_shop["id_shop"]] = $p_shop['ecotax'];
                                    }
                                } else {
                                    $p=Db::getInstance()->getRow($sql);
                                    if(empty($p['rate']))
                                        $p['rate'] = 0;
                                    $taxrate=$p['rate'];
                                    $pprice = $p['price'];
                                    $pecotax = $p['ecotax'];
                                }


                                // COMBINATIONS
                                if(SCMS && SCI::getSelectedShop()!=0)
                                {
                                    $sql='SELECT pa.id_product_attribute
                                            FROM `'._DB_PREFIX_.'product_attribute` pa
                                            LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` pas ON (pas.`id_product_attribute` = pa.`id_product_attribute` AND pas.`id_shop` = '.(SCI::getSelectedShop()>0?(int)SCI::getSelectedShop():$p['id_shop_default']).')
                                            WHERE pa.id_product='.(int)$id_product;
                                } else {
                                    $sql='SELECT pa.id_product_attribute
                                            FROM `'._DB_PREFIX_.'product_attribute` pa
                                            WHERE pa.id_product='.(int)$id_product;
                                }
                                $combilist=Db::getInstance()->executeS($sql);
                                foreach($combilist AS $id_product_attribute)
                                {
                                    if(is_array($id_product_attribute))
                                        $id_product_attribute = $id_product_attribute["id_product_attribute"];

                                    $old_price = array(
                                        "product"=>0,
                                        "shops" => array()
                                    );
                                    $eco_price = array(
                                        "product"=>0,
                                        "shops" => array()
                                    );

                                    // Récupération du prix à modifier
                                    if($column=="priceextax")
                                    {
                                        $select_column = "price";

                                        $sql = 'SELECT '.$select_column.'
                                                    FROM '._DB_PREFIX_.'product_attribute
                                                    WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
                                        $rslt = Db::getInstance()->ExecuteS($sql);
                                        if(isset($rslt[0][$select_column]))
                                        {
                                            $price=$rslt[0][$select_column];
                                            if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                            {
                                                $old_price["product"] = number_format($price + $pprice, 6, '.', '');
                                            } else {
                                                if(!empty($taxrate))
                                                    $old_price["product"]=number_format($price/($taxrate/100+1)+$pprice, 6, '.', '');
                                                else
                                                    $old_price["product"]=number_format($price+$pprice, 6, '.', '');
                                            }

                                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            {
                                                $shops = SCI::getSelectedShopActionList(false, (int)$id_product);
                                                foreach($shops as $shop)
                                                {
                                                    $sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
                                                    $rslt = Db::getInstance()->ExecuteS($sql);
                                                    if(!empty($rslt[0][$select_column]))
                                                    {
                                                        $price=$rslt[0][$select_column];
                                                        $old_price["shops"][$shop]=number_format($price+$pprice_shops[$shop], 6, '.', '');
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    elseif($column=="wholesale_price")
                                    {
                                        $select_column = $column;
                                        $sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
                                        $rslt = Db::getInstance()->ExecuteS($sql);
                                        if(!empty($rslt[0][$select_column]))
                                            $old_price["product"]=$rslt[0][$select_column];

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $shops = SCI::getSelectedShopActionList(false, (int)$id_product);
                                            foreach($shops as $shop)
                                            {
                                                $sql = 'SELECT '.$select_column.' FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
                                                $rslt = Db::getInstance()->ExecuteS($sql);
                                                if(!empty($rslt[0][$select_column]))
                                                    $old_price["shops"][$shop]=$rslt[0][$select_column];
                                            }
                                        }
                                    }
                                    elseif($column=="price")
                                    {
                                        $select_column = "price";

                                        if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
                                            $sql = 'SELECT '.$select_column.',ecotax
                                                    FROM '._DB_PREFIX_.'product_attribute
                                                    WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
                                        else
                                            $sql = 'SELECT '.$select_column.', "0" AS ecotax
                                                    FROM '._DB_PREFIX_.'product_attribute
                                                    WHERE id_product_attribute = "'.(int)$id_product_attribute.'"';
                                        $rslt = Db::getInstance()->ExecuteS($sql);
                                        if(!empty($rslt[0][$select_column]))
                                        {
                                            $price=$rslt[0][$select_column];

                                            $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
                                            if(($ecotax_temp*1) == 0)
                                                $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $pecotax*SCI::getEcotaxTaxRate() : 0);

                                            if(!empty($taxrate))
                                            {
                                                if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                                    $old_price["product"]=number_format($price*($taxrate/100+1)+$pprice*($taxrate/100+1) + $ecotax_temp, 6, '.', '');
                                                else
                                                    $old_price["product"]=number_format($price+$pprice*($taxrate/100+1) + $ecotax_temp, 6, '.', '');
                                            }
                                            else
                                            {
                                                $old_price["product"]=number_format($price+$pprice + $ecotax_temp, 6, '.', '');
                                            }
                                            if(_s('CAT_PROD_ECOTAXINCLUDED'))
                                                $eco_price["product"]=$ecotax_temp;
                                        }

                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                        {
                                            $shops = SCI::getSelectedShopActionList(false, (int)$id_product);
                                            foreach($shops as $shop)
                                            {
                                                $sql = 'SELECT '.$select_column.',ecotax FROM '._DB_PREFIX_.'product_attribute_shop WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"';
                                                $rslt = Db::getInstance()->ExecuteS($sql);
                                                if(!empty($rslt[0][$select_column]))
                                                {
                                                    $price=$rslt[0][$select_column];

                                                    $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $rslt[0]['ecotax']*SCI::getEcotaxTaxRate() : 0);
                                                    if(($ecotax_temp*1) == 0)
                                                        $ecotax_temp = (_s('CAT_PROD_ECOTAXINCLUDED') ? $ecotax_shops[$shop]*SCI::getEcotaxTaxRate() : 0);

                                                    if(!empty($taxrate_shops[$shop]))
                                                        $old_price["shops"][$shop]=number_format($price*($taxrate_shops[$shop]/100+1)+$pprice_shops[$shop]*($taxrate_shops[$shop]/100+1) + $ecotax_temp, 6, '.', '');
                                                    else
                                                        $old_price["shops"][$shop]=number_format($price+$pprice_shops[$shop] + $ecotax_temp, 6, '.', '');

                                                    if(_s('CAT_PROD_ECOTAXINCLUDED'))
                                                        $eco_price["shops"][$shop]= $ecotax_temp;
                                                }
                                            }
                                        }
                                    }

                                    // Arrondir le prix
                                    $new_price = SCI::roundPrice($old_price["product"], $todo);
                                    $update_column = $column;
                                    if($column=="price") // TTC
                                    {
                                        $update_column = "price";
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                        {
                                            if(!empty($taxrate))
                                                $new_price = floatval( ( floatval($new_price - $eco_price["product"]) / ($taxrate/100+1) ) - ($pprice)  );
                                            else
                                                $new_price = floatval( ( floatval($new_price - $eco_price["product"]) ) - ($pprice)  );
                                        }
                                        else
                                        {
                                            if(!empty($taxrate))
                                                $new_price = floatval( ( ( floatval($new_price - $eco_price["product"]) ) / ($taxrate/100+1) ) - ( ($pprice) / ($taxrate/100+1) ) );
                                            else
                                                $new_price = floatval( ( floatval($new_price - $eco_price["product"]) ) - ($pprice)  );
                                        }
                                    }
                                    elseif($column=="priceextax") // HT
                                    {
                                        $update_column = "price";
                                        if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
                                            $new_price = floatval($new_price)-floatval($pprice);
                                        else
                                        {
                                            if(!empty($taxrate))
                                                $new_price = ((floatval($new_price)-(floatval($pprice)))*($taxrate/100+1));
                                            else
                                                $new_price = ((floatval($new_price)-(floatval($pprice))));
                                        }
                                    }
                                    Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute SET '.$update_column.'="'.pSQL($new_price).'", date_upd="'.pSQL(date("Y-m-d H:i:s")).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'"');

                                    if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                    {
                                        foreach($old_price["shops"] as $shop=>$price)
                                        {
                                            $new_price = SCI::roundPrice($price, $todo);
                                            if($column=="price") // TTC
                                            {
                                                if(!empty($taxrate_shops[$shop]))
                                                    $new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) / ($taxrate_shops[$shop]/100+1) ) - ($pprice_shops[$shop])) );
                                                else
                                                    $new_price = floatval( ( ( floatval($new_price - $eco_price["shops"][$shop]) ) - ($pprice_shops[$shop])) );
                                            }
                                            elseif($column=="priceextax") // HT
                                                $new_price = ((floatval($new_price)-(floatval($pprice_shops[$shop]))));

                                            Db::getInstance()->Execute('UPDATE '._DB_PREFIX_.'product_attribute_shop SET '.$update_column.'="'.pSQL($new_price).'" WHERE id_product_attribute = "'.(int)$id_product_attribute.'" AND id_shop = "'.(int)$shop.'"');
                                        }
                                    }
                                }
                            }
                        break;
                    }
                }

            }

            $return_callback = "";
            foreach($return_datas as $key=>$val)
            {
                if(!empty($key))
                {
                    if(!empty($return_callback))
                        $return_callback .= ",";
                    $return_callback .= $key.":'".str_replace("'","\'", $val)."'";
                }
            }
            if(!empty($extraVars))
            {
                if(!empty($return_callback))
                    $return_callback .= ",";
                $return_callback .= $extraVars;
            }
            $return_callback = "{".$return_callback."}";
            $callbacks = str_replace("{data}", $return_callback, $callbacks) ;

            QueueLog::delete(($log_ids[$num]));
        }

        // PM Cache
        if(!empty($updated_products))
            ExtensionPMCM::clearFromIdsProduct($updated_products);

        // RETURN
        $return = json_encode(array("callback"=>$callbacks));
    }
}



echo $return;
