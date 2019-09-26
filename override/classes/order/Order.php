<?php

class Order extends OrderCore
{
    var $payment_status_id;
    var $invoice_txt;
    
    function __construct($id = null, $id_lang = null)
    {
        self::$definition['fields']['payment_status_id'] = array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId');
        self::$definition['fields']['invoice_txt'] = array('type' => self::TYPE_HTML, 'validate' => 'isString');
        parent::__construct($id, $id_lang);
    }


    public function setInvoice($use_existing_payment = false)
    {
        if (!$this->hasInvoice()) {
            if ($id = (int)$this->hasDelivery()) {
                $order_invoice = new OrderInvoice($id);
            } 
            else 
            {
                // automatically sets default template id
                $order_invoice = new OrderInvoice(null, $this->id_lang);
            }
            $order_invoice->id_order = $this->id;
            if (!$id) {
                $order_invoice->number = 0;
            }

            // Save Order invoice

            $this->setInvoiceDetails($order_invoice);

            if (Configuration::get('PS_INVOICE')) {
                $this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);
            }



            // Update order_carrier
            $id_order_carrier = Db::getInstance()->getValue('
				SELECT `id_order_carrier`
				FROM `'._DB_PREFIX_.'order_carrier`
				WHERE `id_order` = '.(int)$order_invoice->id_order.'
				AND (`id_order_invoice` IS NULL OR `id_order_invoice` = 0)');

            if ($id_order_carrier) {
                $order_carrier = new OrderCarrier($id_order_carrier);
                $order_carrier->id_order_invoice = (int)$order_invoice->id;
                $order_carrier->update();
            }

            // Update order detail
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_detail`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

            // Update order payment
            if ($use_existing_payment) {
                $id_order_payments = Db::getInstance()->executeS('
					SELECT DISTINCT op.id_order_payment
					FROM `'._DB_PREFIX_.'order_payment` op
					INNER JOIN `'._DB_PREFIX_.'orders` o ON (o.reference = op.order_reference)
					LEFT JOIN `'._DB_PREFIX_.'order_invoice_payment` oip ON (oip.id_order_payment = op.id_order_payment)
					WHERE (oip.id_order != '.(int)$order_invoice->id_order.' OR oip.id_order IS NULL) AND o.id_order = '.(int)$order_invoice->id_order);

                if (count($id_order_payments)) {
                    foreach ($id_order_payments as $order_payment) {
                        Db::getInstance()->execute('
							INSERT INTO `'._DB_PREFIX_.'order_invoice_payment`
							SET
								`id_order_invoice` = '.(int)$order_invoice->id.',
								`id_order_payment` = '.(int)$order_payment['id_order_payment'].',
								`id_order` = '.(int)$order_invoice->id_order);
                    }
                    // Clear cache
                    Cache::clean('order_invoice_paid_*');
                }
            }

            // Update order cart rule
            Db::getInstance()->execute('
				UPDATE `'._DB_PREFIX_.'order_cart_rule`
				SET `id_order_invoice` = '.(int)$order_invoice->id.'
				WHERE `id_order` = '.(int)$order_invoice->id_order);

            // Keep it for backward compatibility, to remove on 1.6 version
            $this->invoice_date = $order_invoice->date_add;

            if (Configuration::get('PS_INVOICE')) {
                $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                $invoice_number = Hook::exec('actionSetInvoice', array(
                    get_class($this) => $this,
                    get_class($order_invoice) => $order_invoice,
                    'use_existing_payment' => (bool)$use_existing_payment
                ));

                if (is_numeric($invoice_number)) {
                    $this->invoice_number = (int)$invoice_number;
                } else {
                    $this->invoice_number = $this->getInvoiceNumber($order_invoice->id);
                }
            }

            $this->update();
        }
    }
    
    
    /**
     * Creates new invoice based on order
     * @param type $delivery
     * @param type $templateId
     * @param array $productIds array with order detail ids
     */
    function addInvoice($delivery, $templateId, array $productIds = [])
    {
        $order_invoice = new OrderInvoice();
        $order_invoice->id_order = $this->id;
        $order_invoice->template_id = $templateId;
        $order_invoice->setProductIds($productIds);
        $order_invoice->number = 0;
        $order_invoice->id_employee = Context::getContext()->employee->id;

        // Save Order invoice

        // leave this values as they are
        $address = new Address((int)$this->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
        $carrier = new Carrier((int)$this->id_carrier);
        $tax_calculator = $carrier->getTaxCalculator($address);
        $order_invoice->total_discount_tax_excl = $this->total_discounts_tax_excl;
        $order_invoice->total_discount_tax_incl = $this->total_discounts_tax_incl;
        $order_invoice->total_shipping_tax_excl = $this->total_shipping_tax_excl;
        $order_invoice->total_shipping_tax_incl = $this->total_shipping_tax_incl;
        $order_invoice->shipping_tax_computation_method = $tax_calculator->computation_method;
        $order_invoice->total_wrapping_tax_excl = $this->total_wrapping_tax_excl;
        $order_invoice->total_wrapping_tax_incl = $this->total_wrapping_tax_incl;

        // read config params from invoice template 
        $invoiceTemplate = Db::getInstance()->getRow('select sum_to_pay_percent, no_tax, auto_set_paid, due_date_plus from '._DB_PREFIX_.
                'ba_prestashop_invoice where id='.$templateId);
        
        // recalculate totals according with selected products
        $order_invoice->total_products = 0;
        $order_invoice->total_products_wt = 0;
        $orderDetails = $this->getOrderDetailList();
        foreach($orderDetails as $orderDetail)
        {
            if (in_array($orderDetail['id_order_detail'], $productIds))
            {
                $order_invoice->total_products += $orderDetail['total_price_tax_excl'];
                $order_invoice->total_products_wt += $invoiceTemplate['no_tax']?$orderDetail['total_price_tax_excl']:
                    $orderDetail['total_price_tax_incl'];
            }
        }
        $order_invoice->total_paid_tax_excl = $this->total_paid_tax_excl-($this->total_products-$order_invoice->total_products);
        if ($invoiceTemplate['no_tax'])
        {
            $order_invoice->total_paid_tax_incl = $order_invoice->total_paid_tax_excl;
        }
        else
        {
            $order_invoice->total_paid_tax_incl = $this->total_paid_tax_incl-($this->total_products_wt-$order_invoice->total_products_wt);
        }
        
        // set auto set some values
        if ($delivery)
        {
            $order_invoice->paid = intval( $invoiceTemplate['auto_set_paid'] );
        }
        else
        {
            $order_invoice->paid = intval( $invoiceTemplate['auto_set_paid'] );
            $order_invoice->due_date = date('Y-m-d', time()+$invoiceTemplate['due_date_plus']*24*3600);
        }
        
        $order_invoice->percent_to_pay = $invoiceTemplate['sum_to_pay_percent'];
                
        $sumToPayPercent = $invoiceTemplate['sum_to_pay_percent']==0?100:$invoiceTemplate['sum_to_pay_percent'];
        $order_invoice->sum_to_pay = Tools::ps_round($order_invoice->total_paid_tax_incl*$invoiceTemplate['sum_to_pay_percent']/100, 2);
        $order_invoice->save();

        if(!$delivery){
            $orderDetails = $this->getOrderDetailList();
            foreach($orderDetails as $orderDetail){
                if (in_array($orderDetail['id_order_detail'], $productIds)){
                    Db::getInstance()->update('order_detail',
                        array('id_order_invoice' => intval($order_invoice->id)),
                        'id_order_detail = '. $orderDetail['id_order_detail']);
                }
            }
        }
        
        if (Configuration::get('PS_ATCP_SHIPWRAP')) {
            $wrapping_tax_calculator = Adapter_ServiceLocator::get('AverageTaxOfProductsTaxCalculator')->setIdOrder($this->id);
        } else {
            $wrapping_tax_manager = TaxManagerFactory::getManager($address, (int)Configuration::get('PS_GIFT_WRAPPING_TAX_RULES_GROUP'));
            $wrapping_tax_calculator = $wrapping_tax_manager->getTaxCalculator();
        }

        $order_invoice->saveCarrierTaxCalculator(
            $tax_calculator->getTaxesAmount(
                $order_invoice->total_shipping_tax_excl,
                $order_invoice->total_shipping_tax_incl,
                _PS_PRICE_COMPUTE_PRECISION_,
                $this->round_mode
            )
        );
        $order_invoice->saveWrappingTaxCalculator(
            $wrapping_tax_calculator->getTaxesAmount(
                $order_invoice->total_wrapping_tax_excl,
                $order_invoice->total_wrapping_tax_incl,
                _PS_PRICE_COMPUTE_PRECISION_,
                $this->round_mode
            )
        );

        
        
        
        // set delivery number if it is delivery slip
        if ($delivery)
        {
            $order_invoice->delivery_number = 0;
            $order_invoice->delivery_date = date('Y-m-d H:i:s');
            // Update Order Invoice
            $order_invoice->update();
            $this->setDeliveryNumber($order_invoice->id, $this->id_shop);
            $this->delivery_number = $this->getDeliveryNumber($order_invoice->id);
            $this->update();
            $template = PDF::TEMPLATE_DELIVERY_SLIP;
        }
        else{
            // update invoice number of this invoice
            if (Configuration::get('PS_INVOICE'))
            {
                $this->setLastInvoiceNumber($order_invoice->id, $this->id_shop);
            }
            $template = PDF::TEMPLATE_INVOICE;
        }
        
        // reload it
        $order_invoice = new OrderInvoice($order_invoice->id);
        
        // create invoice file, recreste invoice because
        $pdf = new PDF(new OrderInvoice($order_invoice->id), $template, Context::getContext()->smarty);
        $pdfFileContent = $pdf->render('S');
        // save file
        file_put_contents($order_invoice->getInvoiceFilePath(), $pdfFileContent);
    }

    /*
     * Invoice is distinguished from delivery slip by inoice number and delivery number fields. Invoice should have only invoice
     * number field greater then 0, deliver slip should have only delivery number field greater then 0. If db record has both invoice number
     * and delivery number fields greater then 0 -- it is error. More likely that prestashop automatically assigned delivery number to
     * existing invoice.
     */
    public function getDocuments()
    {
        // reading invoices and employee names
        $invoices = [];
        $results = Db::getInstance()->executeS('select oi.*, concat(e.firstname, \' \',e.lastname) as employee_name from '._DB_PREFIX_.
                    'order_invoice oi left join '._DB_PREFIX_.'employee e on e.id_employee=oi.id_employee where oi.id_order='.$this->id);
        foreach($results as $result)
        {
            $invoice = new OrderInvoice();
            $invoice->hydrate($result);
            $invoice->employee_name = $result['employee_name'];
            $invoices []= $invoice;
        }
        
        foreach ($invoices as $key => $invoice) {
            if (!$invoice->number) {
                unset($invoices[$key]);
            }
        }
        $delivery_slips = $this->getDeliverySlipsCollection()->getResults();
        // @TODO review
        foreach ($delivery_slips as $key => $delivery) {
            $delivery->is_delivery = true;
            $delivery->date_add = $delivery->delivery_date;
            if (!$delivery->delivery_number) {
                unset($delivery_slips[$key]);
            }
        }
        $order_slips = $this->getOrderSlipsCollection()->getResults();

        $documents = array_merge($invoices, $order_slips, $delivery_slips);
        usort($documents, array('Order', 'sortDocuments'));

        return $documents;
    }
    
    
    public function getInvoicesCollection()
    {
        $order_invoices = new PrestaShopCollection('OrderInvoice');
        $order_invoices->where('id_order', '=', $this->id);
        return $order_invoices;
    }
    
    public function deleteProduct($order, $order_detail, $quantity, $ignoreVoucher=false)
    {
        if (!(int)$this->getCurrentState() || !validate::isLoadedObject($order_detail)) {
            return false;
        }

        if ($this->hasBeenDelivered()) {
            if (!Configuration::get('PS_ORDER_RETURN', null, null, $this->id_shop)) {
                throw new PrestaShopException('PS_ORDER_RETURN is not defined in table configuration');
            }
            $order_detail->product_quantity_return += (int)$quantity;
            return $order_detail->update();
        } elseif ($this->hasBeenPaid()) {
            $order_detail->product_quantity_refunded += (int)$quantity;
            return $order_detail->update();
        }
        return $this->_deleteProduct($order_detail, (int)$quantity, $ignoreVoucher);
    }
    
    
    /**
     * DOES delete the product
     *
     * @param OrderDetail $order_detail
     * @param int $quantity
     * @return bool
     * @throws PrestaShopException
     */
    protected function _deleteProduct($order_detail, $quantity, $ignoreVoucher=false)
    {
        $product_price_tax_excl = $order_detail->unit_price_tax_excl * $quantity;
        $product_price_tax_incl = $order_detail->unit_price_tax_incl * $quantity;
        
        $voucherPartTe = 0;
        $voucherPartTi = 0;
        if (!$ignoreVoucher)
        {
            // decrese on part of voucher value
            $voucherPartTe = $this->total_discounts_tax_excl*$product_price_tax_excl/$this->total_products;
            $voucherPartTi = $this->total_discounts_tax_incl*$product_price_tax_incl/$this->total_products_wt;
            
            //$product_price_tax_excl -= $voucherPartTe;
            //$product_price_tax_incl -= $voucherPartTi;
            $this->total_discounts_tax_excl = $this->total_discounts_tax_excl - $voucherPartTe;
            $this->total_discounts_tax_incl = $this->total_discounts_tax_incl - $voucherPartTi;
        }

        /* Update cart */
        $cart = new Cart($this->id_cart);
        $cart->updateQty($quantity, $order_detail->product_id, $order_detail->product_attribute_id, false, 'down'); // customization are deleted in deleteCustomization
        $cart->update();

        /* Update order */
        $shipping_diff_tax_incl = $this->total_shipping_tax_incl - $cart->getPackageShippingCost($this->id_carrier, true, null, $this->getCartProducts());
        $shipping_diff_tax_excl = $this->total_shipping_tax_excl - $cart->getPackageShippingCost($this->id_carrier, false, null, $this->getCartProducts());
        $this->total_shipping -= $shipping_diff_tax_incl;
        $this->total_shipping_tax_excl -= $shipping_diff_tax_excl;
        $this->total_shipping_tax_incl -= $shipping_diff_tax_incl;
        $this->total_products -= $product_price_tax_excl;
        $this->total_products_wt -= $product_price_tax_incl;
        $this->total_paid -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_incl -= $product_price_tax_incl + $shipping_diff_tax_incl;
        $this->total_paid_tax_excl -= $product_price_tax_excl + $shipping_diff_tax_excl;
        $this->total_paid_real -= $product_price_tax_incl + $shipping_diff_tax_incl;

        $fields = array(
            'total_shipping',
            'total_shipping_tax_excl',
            'total_shipping_tax_incl',
            'total_products',
            'total_products_wt',
            'total_paid',
            'total_paid_tax_incl',
            'total_paid_tax_excl',
            'total_paid_real',
            'total_discounts_tax_incl',
            'total_discounts_tax_excl'
        );

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            if ($this->{$field} < 0) {
                $this->{$field} = 0;
            }
        }

        /* Prevent from floating precision issues */
        foreach ($fields as $field) {
            $this->{$field} = number_format($this->{$field}, _PS_PRICE_COMPUTE_PRECISION_, '.', '');
        }

        /* Update order detail */
        $order_detail->product_quantity -= (int) $quantity;
        // due order_detail is not yet saved
        $order_detail->total_price_tax_incl = Tools::ps_round($order_detail->total_price_tax_incl - ($product_price_tax_incl+$voucherPartTi), 9);
        $order_detail->total_price_tax_excl = Tools::ps_round($order_detail->total_price_tax_excl - ($product_price_tax_excl+$voucherPartTe), 9);
        $order_detail->total_shipping_price_tax_incl -= $shipping_diff_tax_incl;
        $order_detail->total_shipping_price_tax_excl -= $shipping_diff_tax_excl;
        
        $fields = ['total_price_tax_incl', 'total_price_tax_excl', 'total_shipping_price_tax_incl', 'total_shipping_price_tax_excl'];
        foreach ($fields as $field) {
            if ($order_detail->{$field} < 0) {
                $order_detail->{$field} = 0;
            }
        }
        $order_detail->update();
        if ($order_detail->product_quantity == 0 && $this->getProductsCount() == 0)
        {
            /*
              if (!$order_detail->delete()) {
              return false;
              }
             * 
             */
            $history = new OrderHistory();
            $history->id_order = (int) $this->id;
            $history->changeIdOrderState(Configuration::get('PS_OS_CANCELED'), $this);
            $history->addWithemail();
        }

        return $this->update();
    }
    
    
    function getProductsCount()
    {
        return Db::getInstance()->getValue('select sum(product_quantity) from '._DB_PREFIX_.'order_detail where id_order='.$this->id);
    }
    
    
    /**
     * By default this function was made for invoice, to compute tax amounts and balance delta (because of computation made on round values).
     * If you provide $limitToOrderDetails, only these item will be taken into account. This option is usefull for order slip for example,
     * where only sublist of the order is refunded.
     *
     * @param $limitToOrderDetails Optional array of OrderDetails to take into account. False by default to take all OrderDetails from the current Order.
     * @return array A list of tax rows applied to the given OrderDetails (or all OrderDetails linked to the current Order).
     */
    public function getProductTaxesDetails($limitToOrderDetails = false)
    {
        $round_type = $this->round_type;
        if ($round_type == 0) {
            // if this is 0, it means the field did not exist
            // at the time the order was made.
            // Set it to old type, which was closest to line.
            $round_type = Order::ROUND_LINE;
        }

        // compute products discount
        $order_discount_tax_excl = $this->total_discounts_tax_excl;

        $free_shipping_tax = 0;
        $product_specific_discounts = array();

        // total products in order slip already contain prices with discounts
        $expected_total_base = $this->total_products;// - $this->total_discounts_tax_excl;

        foreach ($this->getCartRules() as $order_cart_rule) {
            if ($order_cart_rule['free_shipping'] && $free_shipping_tax === 0) {
                $free_shipping_tax = $this->total_shipping_tax_incl - $this->total_shipping_tax_excl;
                $order_discount_tax_excl -= $this->total_shipping_tax_excl;
                $expected_total_base += $this->total_shipping_tax_excl;
            }

            $cart_rule = new CartRule($order_cart_rule['id_cart_rule']);
            if ($cart_rule->reduction_product > 0) {
                if (empty($product_specific_discounts[$cart_rule->reduction_product])) {
                    $product_specific_discounts[$cart_rule->reduction_product] = 0;
                }

                $product_specific_discounts[$cart_rule->reduction_product] += $order_cart_rule['value_tax_excl'];
                $order_discount_tax_excl -= $order_cart_rule['value_tax_excl'];
            }
        }

        $products_tax    = $this->total_products_wt - $this->total_products;
        $discounts_tax    = $this->total_discounts_tax_incl - $this->total_discounts_tax_excl;

        // We add $free_shipping_tax because when there is free shipping, the tax that would
        // be paid if there wasn't is included in $discounts_tax.
        $expected_total_tax = $products_tax + $free_shipping_tax;
        $actual_total_tax = 0;
        $actual_total_base = 0;

        $order_detail_tax_rows = array();

        $breakdown = array();

        // Get order_details
        $order_details = $limitToOrderDetails ? $limitToOrderDetails : $this->getOrderDetailList();

        $order_ecotax_tax = 0;

        $tax_rates = array();

        // total number of products in order, returns are not taken into account because cart rule contains initial voucher value
        //$productsNum = 
        foreach ($order_details as $order_detail) {
            $id_order_detail = $order_detail['id_order_detail'];
            $tax_calculator = OrderDetail::getTaxCalculatorStatic($id_order_detail);

            // TODO: probably need to make an ecotax tax breakdown here instead,
            // but it seems unlikely there will be different tax rates applied to the
            // ecotax in the same order in the real world
            $unit_ecotax_tax = $order_detail['ecotax'] * $order_detail['ecotax_tax_rate'] / 100.0;
            $order_ecotax_tax += $order_detail['product_quantity'] * $unit_ecotax_tax;

            //$discount_ratio = 0;
/*
            if ($this->total_products > 0) {
                $discount_ratio = ($order_detail['unit_price_tax_excl'] + $order_detail['ecotax']) / $this->total_products;
            }
*/
            // share of global discount, price in order slip detail already includes discount
            $discounted_price_tax_excl = $order_detail['unit_price_tax_excl'];// - $discount_ratio * $order_discount_tax_excl;
            // specific discount
            if (!empty($product_specific_discounts[$order_detail['product_id']])) {
                $discounted_price_tax_excl -= $product_specific_discounts[$order_detail['product_id']];
            }

            $quantity = $order_detail['product_quantity'];

            foreach ($tax_calculator->taxes as $tax) {
                $tax_rates[$tax->id] = $tax->rate;
            }

            foreach ($tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $unit_amount) {
                $total_tax_base = 0;
                switch ($round_type) {
                    case Order::ROUND_ITEM:
                        $total_tax_base = $quantity * Tools::ps_round($discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
                        $total_amount = $quantity * Tools::ps_round($unit_amount, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
                        break;
                    case Order::ROUND_LINE:
                        $total_tax_base = Tools::ps_round($quantity * $discounted_price_tax_excl, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
                        $total_amount = Tools::ps_round($quantity * $unit_amount, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
                        break;
                    case Order::ROUND_TOTAL:
                        $total_tax_base = $quantity * $discounted_price_tax_excl;
                        $total_amount = $quantity * $unit_amount;
                        break;
                }

                if (!isset($breakdown[$id_tax])) {
                    $breakdown[$id_tax] = array('tax_base' => 0, 'tax_amount' => 0);
                }

                $breakdown[$id_tax]['tax_base'] += $total_tax_base;
                $breakdown[$id_tax]['tax_amount'] += $total_amount;

                $order_detail_tax_rows[] = array(
                    'id_order_detail' => $id_order_detail,
                    'id_tax' => $id_tax,
                    'tax_rate' => $tax_rates[$id_tax],
                    'unit_tax_base' => $discounted_price_tax_excl,
                    'total_tax_base' => $total_tax_base,
                    'unit_amount' => $unit_amount,
                    'total_amount' => $total_amount
                );
            }
        }

        if (!empty($order_detail_tax_rows)) {
            foreach ($breakdown as $data) {
                $actual_total_tax += Tools::ps_round($data['tax_amount'], _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
                $actual_total_base += Tools::ps_round($data['tax_base'], _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);
            }

            $order_ecotax_tax = Tools::ps_round($order_ecotax_tax, _PS_PRICE_COMPUTE_PRECISION_, $this->round_mode);

            $tax_rounding_error = $expected_total_tax - $actual_total_tax - $order_ecotax_tax;
            if ($tax_rounding_error !== 0) {
                Tools::spreadAmount($tax_rounding_error, _PS_PRICE_COMPUTE_PRECISION_, $order_detail_tax_rows, 'total_amount');
            }

            $base_rounding_error = $expected_total_base - $actual_total_base;
            if ($base_rounding_error !== 0) {
                Tools::spreadAmount($base_rounding_error, _PS_PRICE_COMPUTE_PRECISION_, $order_detail_tax_rows, 'total_tax_base');
            }
        }

        return $order_detail_tax_rows;
    }
}
