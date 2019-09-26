<?php

class AdminCartsController extends AdminCartsControllerCore
{
    protected $addSkuInNameFlag = false;
    
    public function ajaxProcessUpdateQty()
    {
        if ($this->tabAccess['edit'] === '1') {
            $errors = array();
            if (!$this->context->cart->id) {
                return;
            }
            if ($this->context->cart->OrderExists()) {
                $errors[] = Tools::displayError('An order has already been placed with this cart.');
            } elseif (!($id_product = (int)Tools::getValue('id_product')) || !($product = new Product((int)$id_product, true, $this->context->language->id))) {
                $errors[] = Tools::displayError('Invalid product');
            } elseif (!($qty = Tools::getValue('qty')) || $qty == 0) {
                $errors[] = Tools::displayError('Invalid quantity');
            }

            // Don't try to use a product if not instanciated before due to errors
            if (isset($product) && $product->id) {
                if (($id_product_attribute = Tools::getValue('id_product_attribute')) != 0) {
                    if (!Product::isAvailableWhenOutOfStock($product->out_of_stock) && !Attribute::checkAttributeQty((int)$id_product_attribute, (int)$qty)) {
                        $errors[] = Tools::displayError('There is not enough product in stock.');
                    }
                } elseif (!$product->checkQty((int)$qty)) {
                    $errors[] = Tools::displayError('There is not enough product in stock.');
                }
                if (!($id_customization = (int)Tools::getValue('id_customization', 0)) && !$product->hasAllRequiredCustomizableFields()) {
                    $errors[] = Tools::displayError('Please fill in all the required fields.');
                }
                $this->context->cart->save();
            } else {
                $errors[] = Tools::displayError('This product cannot be added to the cart.');
            }

            if (!count($errors)) {
                if ((int)$qty < 0) {
                    $qty = str_replace('-', '', $qty);
                    $operator = 'down';
                } else {
                    $operator = 'up';
                }

                if (!($qty_upd = $this->context->cart->updateQty($qty, $id_product, (int)$id_product_attribute, (int)$id_customization, $operator))) {
                    $errors[] = Tools::displayError('You already have the maximum quantity available for this product.');
                } elseif ($qty_upd < 0) {
                    $minimal_qty = $id_product_attribute ? Attribute::getAttributeMinimalQty((int)$id_product_attribute) : $product->minimal_quantity;
                    $errors[] = sprintf(Tools::displayError('You must add a minimum quantity of %d', false), $minimal_qty);
                }
            }
            
            $this->addSkuInNameFlag = true;
            echo Tools::jsonEncode(array_merge($this->ajaxReturnVars(), array('errors' => $errors)));
        }
    }
    
    
    protected function getCartSummary()
    {
        $summary = $this->context->cart->getSummaryDetails(null, true);
        $currency = Context::getContext()->currency;
        if (count($summary['products'])) {
            foreach ($summary['products'] as &$product) {
                $product['numeric_price'] = $product['price'];
                $product['numeric_total'] = $product['total'];
                $product['price'] = str_replace($currency->sign, '', Tools::displayPrice($product['price'], $currency));
                $product['total'] = str_replace($currency->sign, '', Tools::displayPrice($product['total'], $currency));
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
                $product['customized_datas'] = Product::getAllCustomizedDatas((int)$this->context->cart->id, null, true);
                //if ($this->addSkuInNameFlag)
                //{
                //    $product['name'] = $product['supplier_reference'].' - '.$product['name'];
                //}
            }
        }
        if (count($summary['discounts'])) {
            foreach ($summary['discounts'] as &$voucher) {
                $voucher['value_real'] = Tools::displayPrice($voucher['value_real'], $currency);
            }
        }

        if (isset($summary['gift_products']) && count($summary['gift_products'])) {
            foreach ($summary['gift_products'] as &$product) {
                $product['image_link'] = $this->context->link->getImageLink($product['link_rewrite'], $product['id_image'], 'small_default');
                if (!isset($product['attributes_small'])) {
                    $product['attributes_small'] = '';
                }
            }
        }


        return $summary;
    }
}