<?php
/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2016 silbersaiten
 * @version   1.3.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

require_once(_PS_MODULE_DIR_.'orderedit/classes/OrderEditOrderDetail.php');

class OrderEdit extends Module
{
    private static $module_tab = array(
        'class_name' => 'AdminOrderEdit',
        'name' => 'Order Editor',
        'module' => false,
        'id_parent' => false
    );

    private static $module_languages;
    private static $round_type;
    private static $module_tpl_path;
    private static $module_ajax_messages = array(
        'success' => array(),
        'warning' => array(),
        'error' => array()
    );

    const ONLY_NEW = 0;
    const ONLY_EXISTING = 1;
    const ONLY_NON_DELETED = 2;
    const ONLY_DELETED = 3;
    const PRODUCT_IN_CART_UPDATED = 1;
    const PRODUCT_IN_CART_UPDATE_ERROR = 2;
    const PRODUCT_IN_CART_DELETED = 3;
    const PRODUCT_IN_CART_DELETE_ERROR = 4;
    const PRODUCT_IN_CART_ADDED = 5;
    const PRODUCT_IN_CART_ADD_ERROR = 6;
    const PRODUCT_IN_CART_INVALID_QTY = 7;
    public $vt = 't17';

    public function __construct()
    {
        $this->name = 'orderedit';
        $this->version = '1.3.0';
        $this->tab = 'quick_bulk_update';
        $this->author = 'Silbersaiten';
        $this->module_key = '6b872e4b6176bf9d6ec905e489a84ada';

        parent::__construct();

        $this->displayName = $this->l('Order Editor');
        $this->description = $this->l('Order editor for Prestashop v.1.6.');

        if (version_compare(_PS_VERSION_, '1.6.0.10', '<')) {
            self::$round_type = 3;
        } else {
            self::$round_type = Configuration::get('PS_ROUND_TYPE');
        }

        if (version_compare('1.7.0.0', _PS_VERSION_)) {
            $this->vt = 't16';
        }

        self::$module_languages = Language::getLanguages();
        self::$module_tpl_path = dirname(__FILE__).'/views/templates/admin/_configure/order_edit/helpers/';
    }

    public static function getTplPath()
    {
        return self::$module_tpl_path;
    }

    public function install()
    {
        if (self::tabExists() || $this->createTab()) {
            return (parent::install() && $this->registerHook('displayBackOfficeHeader'));
        }

        return false;
    }

    public function uninstall()
    {
        if (!self::tabExists() || $this->deleteTab()) {
            return parent::uninstall();
        }

        return false;
    }

    public function hookDisplayBackOfficeHeader($params)
    {
        unset($params);
        if ($this->context->controller instanceof AdminOrdersController
            || $this->context->controller instanceof AdminOrderEdit
            || $this->context->controller instanceof AdminOrderEditController) {
            $this->context->controller->addJquery();
            $this->context->controller->addJS($this->_path.'/views/js/list.js');
            return '
            <script type="text/javascript">
                var orderedit_ajax = "'.$this->_path.'ajax.php",
                    iem = '.(int)$this->context->cookie->id_employee.',
                    iemp = "'.$this->context->cookie->passwd.'", orderedit_id_shop="'.
                    Configuration::get('PS_SHOP_DEFAULT').'",
                    PS_ROUND_TYPE = ' . self::$round_type .',
                    confirm_delete_invoice = "'.
                    $this->l('Do you really want to delete this document? This may affect the order payment data.').
                    '";
            </script>';
        }
    }

    public static function getLanguages()
    {
        if (!is_array(self::$module_languages)) {
            self::$module_languages = Language::getLanguages();
        }

        return self::$module_languages;
    }

    public function createTab()
    {

        $tab = new Tab();
        $tab->active = 0;
        $tab->class_name = "AdminOrderEdit";
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = "Order Editor";
        }
        $tab->id_parent = (int)Tab::getIdFromClassName('AdminParentOrders');
        $tab->module = $this->name;
        return $tab->add();
    }

    private static function getTabId()
    {
        return Tab::getIdFromClassName(self::$module_tab['class_name']);
    }

    private function deleteTab()
    {
        $id_tab = (int)Tab::getIdFromClassName('AdminOrderEdit');
        $tab = new Tab($id_tab);
        return $tab->delete();
    }

    private static function tabExists()
    {
        return self::getTabId();
    }

    public function getMultilangField($string)
    {
        $languages = self::getLanguages();
        $prepared = array();

        foreach ($languages as $language) {
            $prepared[$language['id_lang']] = $string;
        }

        return $prepared;
    }

    /*
     * Deletes in invoice with a provided ID.
     *
     * @param int $id_invoice
     *
     * @return bool
     */
    private function deleteOrderInvoice($id_invoice)
    {
        if (!Validate::isUnsignedId($id_invoice)) {
            self::setAjaxError($this->l('Invalid invoice ID'), '_documents');

            return false;
        }

        $invoice = new OrderInvoice($id_invoice);

        if (Validate::isLoadedObject($invoice)) {
            // Delete invoice data in other tables (see purgeApparentInvoiceInfos method)
            $this->purgeApparentInvoiceInfos($id_invoice);

            // $invoice_payments = $this->getInvoicePayments($id_invoice);
            // if ($invoice_payments) {
            //     Db::getInstance()->delete('order_payment',
            // '`id_order_payment` IN('.(implode(',', $invoice_payments)).')');
            // }

            if (Db::getInstance()->autoExecute(
                _DB_PREFIX_.'order_detail',
                array('id_order_invoice' => 0),
                'UPDATE',
                '`id_order_invoice` = '.(int)$id_invoice
            ) && Db::getInstance()->update(
                'orders',
                array('invoice_number' => 0, 'delivery_number' => 0),
                '`id_order` = '.(int)$invoice->id_order
            )) {
                self::setAjaxSuccess($this->l('An invoice has been successfully deleted'), '_documents');

                return true;
            } else {
                self::setAjaxError($this->l('Database error occured when trying to delete an invoice'), '_documents');
            }
        }

        return false;
    }


    /*
     * Deletes invoice information from the database, like payment, tax details,
     * etc.
     *
     * @param int $id_invoice
     *
     * @return bool
     */
    private function purgeApparentInvoiceInfos($id_invoice)
    {
        if (!Validate::isUnsignedId($id_invoice)) {
            self::setAjaxError($this->l('Invalid invoice ID'), '_documents');

            return false;
        }

        $tables = array(
            'order_invoice',
            'order_invoice_payment',
            'order_invoice_tax'
        );

        foreach ($tables as $table) {
            Db::getInstance()->delete($table, '`id_order_invoice` = '.(int)$id_invoice);
        }

        return true;
    }


    /*
     * Get payments, associated with an invoice
     *
     * @param int $id_invoice
     *
     * @return mixed
     */
    private function getInvoicePayments($id_invoice)
    {
        if (!Validate::isUnsignedId($id_invoice)) {
            self::setAjaxError($this->l('Invalid invoice ID'), '_documents');

            return false;
        }

        $prepared = array();
        $result = Db::getInstance()->ExecuteS(
            'SELECT
                `id_order_payment`
            FROM
                `'._DB_PREFIX_.'order_invoice_payment`
            WHERE
                `id_order_invoice` = '.(int)$id_invoice
        );

        if ($result && count($result)) {
            foreach ($result as $payment) {
                array_push($prepared, (int)$payment['id_order_payment']);
            }
        }

        return count($prepared) ? $prepared : false;
    }


    /*
     * Delete order payments from "order_payment" table
     *
     * @param array $payment_ids
     *
     * @return bool
     */
    private static function deleteOrderPayments(array $payment_ids)
    {
        return Db::getInstance()->Execute(
            'DELETE FROM
                `'._DB_PREFIX_.'order_payment`
            WHERE
                `id_order_payment` IN ('.(implode(',', $payment_ids)).')'
        );
    }


    /*
     * Gets an instance of Order class using order id passed through _POST (via
     * ajax). Order id is passed on each ajax request.
     *
     * @return mixed (will return boolean false if the instance could not be
     *               created)
     */
    private static function getOrderObj()
    {
        $id_order = Tools::getValue('id_order', false);

        if ($id_order) {
            $id_lang = Db::getInstance()->getValue(
                'SELECT `id_lang` FROM `'._DB_PREFIX_.'orders` WHERE `id_order` = '.(int)$id_order
            );
        }

        if (version_compare(_PS_VERSION_, '1.6.0.11', '<')) {
            $order = new Order((int)$id_order);
            $order->id_lang = $id_lang;
            if (!$id_order || !Validate::isLoadedObject($order)) {
                return false;
            }
        } else {
            $id_lang = null;

            if (!$id_order || !Validate::isLoadedObject($order = new Order((int)$id_order, $id_lang))) {
                return false;
            }
        }

        return $order;
    }

    public function getEditLink($id_order)
    {
        if (!Validate::isUnsignedId($id_order) || !Validate::isLoadedObject($order = new Order((int)$id_order))) {
            return 'false';
        }

        return 'controller=AdminOrderEdit&id_order='.$order->id.
            '&updateorder&token='.Tools::getAdminTokenLite('AdminOrderEdit');
    }


    /*
     * Deletes a document (invoice, slip, etc.) from an order. Does not accept
     * document id or class as a parameter, because they are passed via _POST.
     *
     * @param stdClass $std_rq - A set of standard objects that are often
     *                           required, like an order instance, context, etc.
     *                           See "getExecutionPrerequisites" method.
     */
    public function executeDeleteDocument(stdClass $std_rq)
    {
        $document_type = Tools::getValue('document_class', false);
        $document_id = Tools::getValue('id_document', false);

        if (!class_exists($document_type) || !Validate::isUnsignedId($document_id)) {
            return;
        }

        switch ($document_type) {
            case 'OrderInvoice':
                $this->deleteOrderInvoice((int)$document_id);
                break;
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_documents.tpl', '_payment.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Get a list of products in an order to display them as a list when
     * editing. It also tries to create an image thubmnail for each product.
     *
     * @param object $order - An instance of order
     */
    protected function getProducts(Order $order)
    {
        $products = $order->getProducts();

        foreach ($products as &$product) {
            if ($product['image'] != null) {
                $name = 'product_mini_'.$product['image']->id.(int)$product['product_id'].
                    (isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(
                    _PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg',
                    $name,
                    45,
                    'jpg'
                );

                if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }

        return $products;
    }


    /*
    * Gets default smarty variables for order. Order editor works via ajax, and
    * doesn't update all page every time, but as we can't really know which
    * of those variables will be used in which template, we just fetch them all.
    *
    * @param object $order   - An instance of order
    * @param object $context - An instance of Context
    *
    * @return array
    */
    private function getDefaultOrderDataForSmarty(Order $order, Context $context)
    {
        $payment_methods = array();

        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);

            if (Validate::isLoadedObject($module)) {
                $payment_methods[] = $module->displayName;
            }
        }

        $customer = new Customer($order->id_customer);
        $carrier = new Carrier($order->id_carrier);
        $products = $this->getProducts($order);
        $currency = new Currency((int)$order->id_currency);

        // Carrier module call
        $carrier_module_call = null;

        if ($carrier->is_module) {
            $module = Module::getInstanceByName($carrier->external_module_name);

            if (method_exists($module, 'displayInfoByCart')) {
                $carrier_module_call = call_user_func(array($module, 'displayInfoByCart'), $order->id_cart);
            }
        }

        // Retrieve addresses information
        $address_invoice = new Address($order->id_address_invoice, $this->context->language->id);

        if (Validate::isLoadedObject($address_invoice) && $address_invoice->id_state) {
            $invoice_state = new State((int)$address_invoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $address_delivery = $address_invoice;

            if (isset($invoice_state)) {
                $delivery_state = $invoice_state;
            }
        } else {
            $address_delivery = new Address($order->id_address_delivery, $this->context->language->id);

            if (Validate::isLoadedObject($address_delivery) && $address_delivery->id_state) {
                $delivery_state = new State((int)$address_delivery->id_state);
            }
        }

        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();

        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && $product->advanced_stock_management) {
                $warehouses = Warehouse::getWarehousesByProductId(
                    $order_detail['product_id'],
                    $order_detail['product_attribute_id']
                );

                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();

        if ($current_order_state->delivery != 1 && $current_order_state->shipped != 1) {
            $display_out_of_stock_warning = true;
        }

        // products current stock (from stock_available)
        foreach ($products as &$product) {
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int)$customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct(
                $product['product_id'],
                $product['product_attribute_id'],
                $product['id_shop']
            );

            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] == 0 && $display_out_of_stock_warning) {
                $this->displayError($this->l('This product is out of stock: ').' '.$product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int)$product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation(
                    $product['product_id'],
                    $product['product_attribute_id'],
                    $product['id_warehouse']
                );
                if (!empty($warehouse_location)) {
                    $product['warehouse_location'] = $warehouse_location;
                } else {
                    $product['warehouse_location'] = false;
                }
            } else {
                $product['warehouse_name'] = '--';
                $product['warehouse_location'] = false;
            }
        }

        return (array(
            'orderedit_tpl_dir' => dirname(__FILE__).'/views/templates/admin/_configure/order_edit/',
            'can_edit' => true,
            'link' => new Link(),
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'customer_addresses' => $customer->getAddresses($context->language->id),
            'addresses' => array(
                'delivery' => $address_delivery,
                'deliveryState' => isset($delivery_state) ? $delivery_state : null,
                'invoice' => $address_invoice,
                'invoiceState' => isset($invoice_state) ? $invoice_state : null
            ),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'discounts' => $order->getCartRules(),
            // Get the sum of total_paid_tax_incl of the order with similar reference
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(),
            'total_paid' => $order->getTotalPaid(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, 0),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $order->getHistory($context->language->id, false, true),
            'states' => OrderState::getOrderStates($context->language->id),
            'warehouse_list' => $warehouse_list,
            'sources' => ConnectionsSource::getOrderSources($order->id),
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrencies(),
            'previousOrder' => $order->getPreviousOrderId(),
            'nextOrder' => $order->getNextOrderId(),
            'token' => Tools::getValue('token'),
            'current_index' => Tools::getValue('current_index'),
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $context->language->iso_code,
            'id_lang' => $context->language->id,
            'current_id_lang' => $context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE')
        ));
    }


    /*
     * Translates field names that we get from ajax to preoper names required by
     * corresponding objects
     *
     * @param reference $ajaxFields   - A reference to fields array passed via Ajax
     * @param array     $translations - Translation pairs "ajax_field" => "order_field"
     */
    private static function translateFields(array &$ajax_fields, array $translations)
    {
        foreach ($ajax_fields as $k => $v) {
            if (array_key_exists($k, $translations)) {
                $ajax_fields[$translations[$k]] = $v;

                unset($ajax_fields[$k]);
            }
        }
    }

    /*
     * Translates product fields to Prestashop's conventional names. Not really
     * a necessary step, but it makes things easier
     *
     * @param array $product - A reference to product array
     */
    private static function translateProductFields(&$product)
    {
        $field_trans = array(
            'productId' => 'product_id',
            'productAttributeId' => 'product_attribute_id',
            'idOrderDetail' => 'id_order_detail',
            'productNameEdit' => 'name',
            'productReferenceEdit' => 'reference',
            'productSupplierReferenceEdit' => 'supplier_reference',
            'productWeightEdit' => 'product_weight',
            'productPriceEdit' => 'price',
            'productPriceWtEdit' => 'price_wt',
            'productQtyEdit' => 'quantity',
            'productCustomAllQtyEdit' => 'quantityCustom',
            'editProductInvoice' => 'id_product_invoice',
            'productWarehouseId' => 'id_warehouse',
            'productReductionPerEdit' => 'reduction_percent'
        );

        self::translateFields($product, $field_trans);
    }


    /*
     * Translates discount fields to Prestashop's conventional names. Not really
     * a necessary step, but it makes things easier
     *
     * @param array $discount - A reference to discount data array
     */
    private static function translateDiscountFields(&$discount)
    {
        $field_trans = array(
            'orderDiscountId' => 'id_order_cart_rule',
            'discountNameEdit' => 'name',
            'discountPriceEdit' => 'value_tax_excl',
            'discountPriceWtEdit' => 'value'
        );

        self::translateFields($discount, $field_trans);
    }


    /*
     * Translates shipping fields to Prestashop's conventional names. Not really
     * a necessary step, but it makes things easier
     *
     * @param array $carrier - A reference to shipping data array
     */
    private static function translateShippingFields(&$carrier)
    {
        $field_trans = array(
            'orderShippingCarrier' => 'id_order_carrier',
            'shippingCarrierId' => 'id_carrier',
            'shippingDate' => 'date_add',
            'shippingPriceEdit' => 'shipping_cost_tax_excl',
            'shippingPriceWtEdit' => 'shipping_cost_tax_incl',
            'shippingTaxRateEdit' => 'shipping_cost_tax_rate',
            'shippingTrackingNumberEdit' => 'tracking_number',
            'shippingWeightEdit' => 'weight'
        );

        self::translateFields($carrier, $field_trans);
    }

    /*
     * Translates payment fields to Prestashop's conventional names. Not really
     * a necessary step, but it makes things easier
     *
     * @param array $payment - A reference to payment data array
     */
    private static function translatePaymentFields(&$payment)
    {
        $field_trans = array(
            'orderPayment' => 'id_order_payment',
            'paymentDate' => 'date_add',
            'paymentName' => 'payment_method',
            'paymentTransaction' => 'transaction_id',
            'paymentAmountEdit' => 'amount',
            'cardNumber' => 'card_number',
            'cardBrand' => 'card_brand',
            'cardExpiration' => 'card_expiration',
            'cardHolder' => 'card_holder'
        );

        self::translateFields($payment, $field_trans);
    }


    /*
     * Filters a list of products passed via ajax when saving an order to
     * collect only new products, for example
     *
     * @param array $products - A list of products
     *
     * @param integer $type - any of the following: ONLY_NEW, ONLY_EXISTING,
     *                        ONLY_NON_DELETED, ONLY_DELETED
     *
     * @return array
     */
    private static function filterProductList($products, $type = OrderEdit::ONLY_NEW)
    {
        $result = array();

        if (is_array($products) && count($products)) {
            foreach ($products as $product) {
                if (($type == OrderEdit::ONLY_NEW && (int)$product['id_order_detail'] == 0)
                    || ($type == OrderEdit::ONLY_EXISTING && (int)$product['id_order_detail'] > 0)
                    || ($type == OrderEdit::ONLY_NON_DELETED && (int)$product['isDeleted'] == 0)
                    || ($type == OrderEdit::ONLY_DELETED && (int)$product['isDeleted'] == 1)) {
                    array_push($result, $product);
                }
            }
        }

        return $result;
    }


    /*
     * Collects a total price of all products in the passed array
     *
     * @param array $products - A list of products
     *
     * @return array
     */
    private static function getTotalsFromProductList(array $products)
    {
        $result = array(
            'weight' => 0,
            'total' => 0,
            'total_wt' => 0,
            'tax_amount' => 0,
        );

        foreach ($products as $product) {
            if ((int)$product['isDeleted'] == 0) {
                $result['weight'] += (float)$product['product_weight'];
                $result['total'] += (float)$product['price'] * (int)$product['quantity'];
                $result['total_wt'] += (float)$product['price_wt'] * (int)$product['quantity'];
                $result['tax_amount'] += $result['total_wt'] - $result['total'];
            }
        }

        return $result;
    }


    /*
     * Collects a total price of all discounts in the passed array
     *
     * @param array $discounts - A list of discounts
     *
     * @return array
     */
    private static function getTotalsFromDiscountsList($discounts)
    {
        $result = array(
            'total' => 0,
            'total_wt' => 0,
            'tax_amount' => 0,
        );

        if (is_array($discounts) && count($discounts)) {
            foreach ($discounts as $discount) {
                $result['total'] += (float)$discount['value_tax_excl'];
                $result['total_wt'] += (float)$discount['value'];
                $result['tax_amount'] += (float)$discount['value'] - (float)$discount['value_tax_excl'];
            }
        }

        return $result;
    }


    /*
     * Collects a total price of all carriers in the passed array
     *
     * @param array $carriers - A list of carriers
     *
     * @return array
     */
    private static function getTotalsFromShippingList($carriers)
    {
        $result = array(
            'total' => 0,
            'total_wt' => 0,
            'tax_amount' => 0,
        );

        if (is_array($carriers) && count($carriers)) {
            foreach ($carriers as $carrier) {
                $tax_excluded = $carrier['shipping_cost_tax_rate'] > 0 ?
                    $carrier['shipping_cost_tax_incl'] / (1 + (float)$carrier['shipping_cost_tax_rate'] / 100) :
                    $carrier['shipping_cost_tax_incl'];

                $result['total'] += Tools::ps_round((float)$tax_excluded, 2);
                $result['total_wt'] += Tools::ps_round((float)$carrier['shipping_cost_tax_incl'], 2);
                $result['tax_amount'] += Tools::ps_round($carrier['shipping_cost_tax_incl'] - (float)$tax_excluded, 2);
            }
        }

        return $result;
    }


    /*
     * Sets wrapping info for an order, this has moved to a separate method in
     * order to make the "executeSaveOrder" method more readable
     *
     * @param object $order - A reference to an order instance
     *
     * @return array
     */
    private static function setWrappingData(&$order)
    {
        $wrapping = Tools::getValue('wrapping');

        if ($wrapping && is_array($wrapping)) {
            $is_gift = array_key_exists('is_gift', $wrapping) ? (int)$wrapping['is_gift'] == 1 : false;

            $order->total_wrapping_tax_excl = 0;
            $order->total_wrapping_tax_incl = 0;
            $order->total_wrapping = 0;
            $order->gift = (int)$is_gift;
            $order->gift_message = null;

            if ($order->gift) {
                $tax = array_key_exists('tax_rate', $wrapping) ? (float)$wrapping['tax_rate'] : 0;

                $order->total_wrapping_tax_incl = array_key_exists('price_wt', $wrapping)
                    ? (float)$wrapping['price_wt']
                    : 0;
                $order->total_wrapping_tax_excl = $order->total_wrapping_tax_incl / (1 + $tax / 100);

                $order->total_wrapping_tax_incl = Tools::ps_round($order->total_wrapping_tax_incl, 2);
                $order->total_wrapping_tax_excl = Tools::ps_round($order->total_wrapping_tax_excl, 2);
                $order->total_wrapping = $order->total_wrapping_tax_incl;
                $order->gift_message = array_key_exists('gift_message', $wrapping) ? $wrapping['gift_message'] : null;
            }
        }
    }

    private static function setPaymentData($payment_collection)
    {
        $payment_errors = array();

        foreach ($payment_collection as $payment_id => $payment) {
            if (Validate::isLoadedObject($obj = new OrderPayment((int)$payment_id))) {
                self::translatePaymentFields($payment);

                foreach ($payment as $field => $data) {
                    if (property_exists($obj, $field)) {
                        $obj->{$field} = $data;
                    }
                }

                $errors = $obj->validateController();

                if (count($errors)) {
                    $payment_errors = array_merge($payment_errors, $errors);
                } else {
                    $obj->save();
                }
            }
        }

        return count($payment_errors) ? $payment_errors : false;
    }


    /*
     * Updates the quantity of a certain product in the cart.
     *
     * @param object $cart - Cart instance
     *
     * @param integer $quantity - Product quantity
     *
     * @param integer $id_product - Product id
     *
     * @param integer $id_product_attribute - Product combination id
     *
     * @param integer $id_address_delivery - Delivery address id
     *
     * @param object Shop - an instance of Shop
     *
     * @return integer
     */
    private function cartQtyUpdate(Cart $cart, $quantity, $id_product, $id_product_attribute, $id_address_delivery, Shop $shop)
    {
        $quantity = (int)$quantity;
        $quantity = abs($quantity);
        $id_product = (int)$id_product;
        $id_product_attribute = (int)$id_product_attribute;
        $in_cart = $cart->containsProduct($id_product, $id_product_attribute, 0, (int)$id_address_delivery);

        if ($in_cart) {
            if ($quantity <= 0) {
                if ($cart->deleteProduct((int)$id_product, (int)$id_product_attribute)) {
                    return OrderEdit::PRODUCT_IN_CART_DELETED;
                }

                return OrderEdit::PRODUCT_IN_CART_DELETE_ERROR;
            } else {
                if (Db::getInstance()->execute(
                    'UPDATE
                        `'._DB_PREFIX_.'cart_product`
                    SET
                        `quantity` = '.$quantity.', `date_add` = NOW()
                    WHERE
                        `id_product` = '.(int)$id_product.(!empty($id_product_attribute) ? '
                        AND `id_product_attribute` = '.(int)$id_product_attribute : '').'
                    AND
                        `id_cart` = '.(int)$cart->id.(Configuration::get('PS_ALLOW_MULTISHIPPING')
                            && $cart->isMultiAddressDelivery()?'
                        AND `id_address_delivery` = '.(int)$id_address_delivery : '').'
                    LIMIT 1'
                )) {
                    return OrderEdit::PRODUCT_IN_CART_UPDATED;
                } else {
                    return OrderEdit::PRODUCT_IN_CART_UPDATE_ERROR;
                }
            }
        } elseif ($quantity > 0) {
            if (Db::getInstance()->insert('cart_product', array(
                'id_product' => (int)$id_product,
                'id_product_attribute' => (int)$id_product_attribute,
                'id_cart' => (int)$cart->id,
                'id_address_delivery' => (int)$id_address_delivery,
                'id_shop' => $shop->id,
                'quantity' => (int)$quantity,
                'date_add' => date('Y-m-d H:i:s')
            ))) {
                return OrderEdit::PRODUCT_IN_CART_ADDED;
            } else {
                return OrderEdit::PRODUCT_IN_CART_ADD_ERROR;
            }
        } else {
            return OrderEdit::PRODUCT_IN_CART_INVALID_QTY;
        }
    }


    /*
     * Tests whether a product needs to be deleted.
     *
     * @param array $product - an array of data for order detail that we get
     *                         from Order Editor after hitting "Save" button
     *
     * @return bool
     */
    private static function productNeedsToBeDeleted(array $product)
    {
        return (array_key_exists('isDeleted', $product) && (int)$product['isDeleted'] == 1);
    }


    /*
     * Tests whether a product is new (is not stored in Order yet)
     *
     * @param array $product - an array of data for order detail that we get
     *                         from Order Editor after hitting "Save" button
     *
     * @return bool
     */
    private static function productIsNew(array $product)
    {
        return (!array_key_exists('id_order_detail', $product) || (int)$product['id_order_detail'] == 0);
    }


    /*
     * Fills order detail parameters using a data array ($product).
     *
     * @param object $detail - an instance of OrderDetail class
     *
     * @param stdClass $std_rq - A set of standard objects that are often
     *                           required, like an order instance, context, etc.
     *                           See "getExecutionPrerequisites" method.
     *
     * @param array $product - an array of data for order detail that we get
     *                         from Order Editor after hitting "Save" button
     *
     * @param object $shop - an Instance of Shop class
     *
     * @return void
     */
    private static function fillOrderDetail(OrderDetail &$detail, stdClass $std_rq, array $product, $shop)
    {
        $null = null;

        $detail->id_order = (int)$std_rq->order->id;

        // if (array_key_exists('id_product_invoice', $product)) {
        //     $detail->id_order_invoice = (int)$product['id_product_invoice'];
        // }

        $id_invoice = 0;
        if ((int)$std_rq->order->invoice_number != 0) {
            $invoice = OrderInvoice::getInvoiceByNumber((int)$std_rq->order->invoice_number);
            $id_invoice = (int)$invoice->id;
        }
        $detail->id_order_invoice = $id_invoice;
        $q = (array_key_exists('quantity', $product) ? (int)$product['quantity'] : (int)$product['quantityCustom']);
        $detail->product_id = (int)$product['product_id'];
        $detail->id_shop = (int)$shop->id;
        $detail->product_attribute_id = (int)$product['product_attribute_id'];
        $detail->product_name = $product['name'];
        $detail->product_quantity = $q;
        $detail->product_quantity_in_stock = (int)$product['quantity_in_stock'] - $q;
        $detail->tax_rate = (float)$product['tax_rate'];

        if ($detail->product_quantity_in_stock < 0) {
            $detail->product_quantity_in_stock = 0;
        }

        $original_product_price_wt = Product::getPriceStatic(
            $product['product_id'],
            true,
            (int)$product['product_attribute_id'],
            6,
            null,
            false,
            true,
            1,
            false,
            null,
            null,
            null,
            $null,
            true,
            true,
            $std_rq->context
        );

        if ($detail->tax_rate == 0) {
            $pri = $product['price'];
        } elseif (Tools::ps_round(
            $product['price_wt'],
            defined('_PS_PRICE_COMPUTE_PRECISION_') ? _PS_PRICE_COMPUTE_PRECISION_ : _PS_PRICE_DISPLAY_PRECISION_
        ) == Tools::ps_round(
            $original_product_price_wt,
            defined('_PS_PRICE_COMPUTE_PRECISION_') ? _PS_PRICE_COMPUTE_PRECISION_ : _PS_PRICE_DISPLAY_PRECISION_
        )) {
            $pri = $original_product_price_wt;
        } else {
            $pri = $product['price_wt'];
        }

        switch (self::$round_type)
        {
            case 3:
                $detail->product_price = Tools::ps_round($product['price'] * $q, 9);
                $detail->total_price_tax_incl = $pri * $q;
                break;
            case 2:
                $detail->product_price = Tools::ps_round(
                    $product['price'] * $q,
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );
                $detail->total_price_tax_incl = Tools::ps_round(
                    $pri * $q,
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );
                break;

            case 1:
            default:
                $detail->product_price = Tools::ps_round(
                    $product['price'],
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                ) * $q;
                $detail->total_price_tax_incl = Tools::ps_round(
                    $pri,
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                ) * $q;
                break;
        }

        $detail->original_product_price = Product::getPriceStatic(
            $product['product_id'],
            false,
            (int)$product['product_attribute_id'],
            6,
            null,
            false,
            false,
            1,
            false,
            null,
            null,
            null,
            $null,
            true,
            true,
            $std_rq->context
        );
        $detail->unit_price_tax_incl = Tools::ps_round($pri, 6);
        $detail->unit_price_tax_excl = Tools::ps_round($product['price'], 6);

        $detail->total_price_tax_excl = $detail->product_price;
        $detail->product_reference = $product['reference'];
        $detail->product_supplier_reference = $product['supplier_reference'];
                /*
                $product['product_attribute_id']? 
                Db::getInstance()->getValue('select product_supplier_reference')
                : $product['supplier_reference'];*/
        $detail->product_weight = (float)$product['product_weight'];
        $detail->reduction_percent = (float)$product['reduction_percent'];
        $detail->id_warehouse = (int)$product['id_warehouse'];
    }


    /*
     * Deletes and order detail (via invoking native "delete" method). If failed,
     * tries to restore the detail from backup.
     *
     * @param object $detail - an instance of OrderDetail class
     *
     * @param object $detail - an instance of OrderDetail class in it's initial
     *                         state, before any changes has been made by
     *                         Order Editor
     *
     * @return bool
     */
    private function deleteOrderDetail(OrderDetail $detail, OrderDetail $backup_detail)
    {
        if (!Validate::isLoadedObject($detail)) {
            self::setAjaxError(
                $this->l('Unable to delete an order detail, no ID provided'),
                'message_placeholders/_product_list_errors'
            );

            return false;
        }

        if (!$detail->delete()) {
            $this->restoreOrderDetail($detail, $backup_detail);
        } else {
            self::deleteOrderDetailTax($detail);
        }
    }


    /*
     * Deletes order detail tax information from "order_detail_tax" table
     *
     * @param object $detail - an instance of OrderDetail class
     *
     * @return bool
     */
    private static function deleteOrderDetailTax(OrderDetail $detail)
    {
        return Db::getInstance()->Execute(
            'DELETE FROM
                `'._DB_PREFIX_.'order_detail_tax`
            WHERE
                `id_order_detail` = '.(int)$detail->id
        );
    }

    private static function updateDocumentsData($documents, stdClass $std_rq)
    {
        if (is_array($documents)) {
            foreach ($documents as $document) {
                $doc = new OrderInvoiceCore((int)$document['documentId']);
                
                if (array_key_exists('documentDateadd', $document)) {
                    $doc->date_add = $document['documentDateadd'];
                } elseif (array_key_exists('documentDatedelivery', $document)) {
                    $doc->delivery_date = $document['documentDatedelivery'];
                }

                $doc->save();
            }
        }
    }

    private static function updateOrderCarrierData(array $shipping_data, stdClass $std_rq, $products)
    {
        if (!count($shipping_data)) {
            return false;
        }

        foreach ($shipping_data as $carrier) {
            $carrier_obj = new OrderCarrier((int)$carrier['id_order_carrier']);

            if (!Validate::isLoadedObject($carrier_obj)) {
                $carrier_obj = new OrderCarrier();
            }

            if ((int)$carrier['id_carrier'] == 0) {
                if ($carrier_obj->id_order_carrier) {
                    $carrier_obj->delete();
                }

                continue;
            } else {
                $carrier_obj->id_carrier = (int)$carrier['id_carrier'];
                $carrier_obj->id_order = (int)$std_rq->order->id;
                $carrier_obj->shipping_cost_tax_excl = (float)$carrier['shipping_cost_tax_excl'];
                $carrier_obj->shipping_cost_tax_incl = (float)$carrier['shipping_cost_tax_incl'];
                $carrier_obj->date_add = Validate::isDate($carrier['date_add'])
                    ? $carrier['date_add']
                    : date('Y-m-d H:i:s');
                $carrier_obj->tracking_number = $carrier['tracking_number'];
                if ($carrier_obj->weight != $carrier['weight']) {
                    $carrier_obj->weight = $carrier['weight'];
                } else {
                    $weight = 0;
                    foreach ($products as $product) {
                        $weight += (array_key_exists('quantity', $product)
                            ? (int)$product['quantity']
                            : (int)$product['quantityCustom']) * $product['product_weight'];
                    }
                    $carrier_obj->weight = $weight;
                }

                $carrier_obj->save();
            }
        }
    }

    private function updateOrderDiscountData(array $discounts, stdClass $std_rq)
    {
        if (!count($discounts)) {
            return false;
        }

        foreach ($discounts as $discount) {
            if ((float)$discount['value'] != (float)$discount['orderDiscountTaxInclOriginal']) {
                $obj = new OrderCartRule((int)$discount['id_order_cart_rule']);

                if (!Validate::isLoadedObject($obj)) {
                    continue;
                }

                $obj->value_tax_excl = Tools::ps_round(
                    (float)$discount['value'] / (1 + ($std_rq->order->getTaxesAverageUsed() / 100)),
                    2
                );
                $obj->value = (float)$discount['value'];

                if ($obj->save()) {
                    if (Validate::isLoadedObject($invoice = new OrderInvoice((int)$discount['orderDiscountInvoiceId']))) {
                        $this->updateDiscountForInvoice(
                            $invoice,
                            $discount['value'],
                            $discount['value_tax_excl'],
                            $discount['orderDiscountTaxInclOriginal'],
                            $discount['orderDiscountTaxExclOriginal']
                        );
                    }
                }
            }
        }
    }


    /*
     * As of Prestashop 1.5, taxes applied to each product in an order are now
     * stored in a separate table - "order_detail_tax". This method takes an
     * array of instances of OrderDetail class, calculates and stores taxes for
     * each one of them in "order_detail_tax" table.
     *
     * @param object $details - an array of OrderDetail class instances
     *
     * @param stdClass $std_rq - A set of standard objects that are often
     *                           required, like an order instance, context, etc.
     *                           See "getExecutionPrerequisites" method.
     *
     * @return void
     */
    private static function updateOrderDetailsTax(array $details, stdClass $std_rq)
    {
        if (!count($details)) {
            return false;
        }

        $address = new Address((int)$std_rq->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});

        foreach ($details as $detail) {
            $tax_manager = TaxManagerFactory::getManager(
                $address,
                (int)Product::getIdTaxRulesGroupByIdProduct((int)$detail->product_id, $std_rq->context)
            );
            $tax_calculator = $tax_manager->getTaxCalculator();

            if (count($tax_calculator->taxes) > 0 && $std_rq->order->total_products > 0) {
                if ((float)$detail->tax_rate == 0) {
                    Db::getInstance()->execute(
                        'DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$detail->id
                    );

                    continue;
                }

                $ratio = $detail->unit_price_tax_excl / $std_rq->order->total_products;
                $order_reduction_amount = $std_rq->order->total_discounts_tax_excl * $ratio;
                $discounted_price_tax_excl = $detail->unit_price_tax_excl - $order_reduction_amount;

                $values = '';

                foreach ($tax_calculator->getTaxesAmount($discounted_price_tax_excl) as $id_tax => $amount) {
                    $unit_amount = (float)Tools::ps_round($amount, 2);
                    $total_amount = $unit_amount * $detail->product_quantity;
                    $values .= '('.(implode(',', array((int)$detail->id, (float)$id_tax, $unit_amount, (float)$total_amount))).'),';
                }

                $values = rtrim($values, ',');

                Db::getInstance()->execute(
                    'DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$detail->id
                );

                Db::getInstance()->execute(
                    'INSERT INTO
                        `'._DB_PREFIX_.'order_detail_tax`
                    (id_order_detail, id_tax, unit_amount, total_amount)
                    VALUES
                        '.$values
                );
            }
        }
    }


    /*
     * Saves an order detail
     *
     * @param object $detail - an instance of current (modified) order detail
     *
     * @param mixed $backup_detail - an instance of an original order detail,
     *                               note that this parameter will be set to
     *                               boolean false for new products in the order,
     *                               because they do not have an "original" order
     *                               detail.
     *
     * @return mixed
     */
    private function saveOrderDetail(OrderDetail $detail, $backup_detail, $detail_id_tax, $backup_detail_id_tax)
    {
        if (!$detail->save() && !Validate::isLoadedObject($detail)) {
            if (!$backup_detail) {
                self::setAjaxError(
                    $this->l('Unable to save the new product in order'),
                    'message_placeholders/_product_list_errors'
                );

                return false;
            }

            if ($detail = $this->restoreOrderDetail($detail, $backup_detail, $detail_id_tax, $backup_detail_id_tax)) {
                self::setAjaxError(
                    $this->l('Unable to save product detail, data has been restored to original'),
                    'message_placeholders/_product_list_errors'
                );
            } else {
                self::setAjaxError(
                    $this->l('Unable to save product detail, attemt to restore the original has failed'),
                    'message_placeholders/_product_list_errors'
                );

                return false;
            }
        } else {
            Db::getInstance()->execute(
                'DELETE FROM `'._DB_PREFIX_.'order_detail_tax` WHERE id_order_detail='.(int)$detail->id
            );
            if ((int)$detail_id_tax > 0) {
                $unit_amount = $detail->unit_price_tax_incl - $detail->unit_price_tax_excl;
                $total_amount = $detail->total_price_tax_incl - $detail->total_price_tax_excl;

                $sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax`
                    (id_order_detail, id_tax, unit_amount, total_amount)
                    VALUES (\''.pSQL((int)$detail->id).'\', \''.pSQL((int)$detail_id_tax).'\',
                    \''.$unit_amount.'\', \''.$total_amount.'\')';
                Db::getInstance()->execute($sql);
            }
        }
        return $detail;
    }


    /*
     * Gets initial product quantities in a cart, returns an array where keys are
     * %product_id%_%product_attribute_id%.
     *
     * @param object $cart - an instance of cart
     *
     * @return mixed
     */
    private static function getInitialProductQuantities(Cart $cart)
    {
        $qties = array();
        $packages = $cart->getPackageList();

        if (is_array($packages) && count($packages)) {
            foreach ($packages as $packs) {
                foreach ($packs as $package_data) {
                    foreach ($package_data['product_list'] as $product) {
                        $k = $product['id_product'].'_'.$product['id_product_attribute'];

                        if (!array_key_exists($k, $qties)) {
                            $qties[$k] = array(
                                'id_product' => $product['id_product'],
                                'id_product_attribute' => $product['id_product_attribute'],
                                'stock_quantity' => $product['stock_quantity'],
                                'cart_quantity' => $product['cart_quantity'],
                            );
                        }
                    }
                }
            }
        }

        return count($qties) ? $qties : false;
    }


    /*
     * Tries to restore an order detail to it's previous state, if it is
     * possible
     *
     * @param object $detail - an instance of current (modified) order detail
     *
     * @param object $backup_detail - an instance of an original order detail
     *
     * @return mixed
     */
    private function restoreOrderDetail(OrderDetail $detail, OrderDetail $backup_detail, $detail_id_tax, $backup_detail_id_tax)
    {
        $detail = $backup_detail;

        if ($detail->save()) {
            if ((int)$backup_detail_id_tax > 0) {
                Db::getInstance()->execute(
                    'DELETE FROM `'._DB_PREFIX_.'order_detail_tax`
                    WHERE id_order_detail='.(int)$detail->id_order_detail
                );

                $unit_amount = $detail->unit_price_tax_incl - $detail->unit_price_tax_excl;
                $total_amount = $detail->total_price_tax_incl - $detail->total_price_tax_excl;

                $sql = 'INSERT INTO `'._DB_PREFIX_.'order_detail_tax`
                    (id_order_detail, id_tax, unit_amount, total_amount)
                    VALUES (\''.pSQL((int)$detail->id_order_detail).'\', \''.pSQL((int)$backup_detail_id_tax).'\',
                    \''.(float)$unit_amount.'\', \''.(float)$total_amount.'\')';
                Db::getInstance()->execute($sql);
            }

            self::setAjaxError(
                $this->l('Unable to save product detail, data has been restored to original'),
                'message_placeholders/_product_list_errors'
            );

            return $detail;
        } else {
            self::setAjaxError(
                $this->l('Unable to save product detail, attemt to restore the original has failed'),
                'message_placeholders/_product_list_errors'
            );

            return false;
        }
    }

    private static function getOrderCarrierForInvoice(OrderInvoice $order_invoice)
    {
        $id_order_carrier = Db::getInstance()->getValue(
            'SELECT
                `id_order_carrier`
            FROM
                `'._DB_PREFIX_.'order_carrier`
            WHERE
                `id_order_invoice` = '.(int)$order_invoice->id
        );

        if ((int)$id_order_carrier != 0 && Validate::isLoadedObject($order_carrier = new OrderCarrier((int)$id_order_carrier))) {
            return $order_carrier;
        }

        return false;
    }


    /*
     * Sorts the invoices using order details: when a user adds a new product to
     * an order, he can select an invoice that he wants to "attach" this product
     * to, if an order has more than one invoice. This method runs after all the
     * products were added/modified, and collects the invoice id data from them.
     * Then it updates or creates invoices for those products.
     *
     * @param array $details - an array of OrderDetail instances of all the
     *                         products that are currently present in an order.
     *
     * @param object $std_rq
     *
     * @return void
     */
    private static function setDetailInvoices(array $details, stdClass $std_rq)
    {
        if (!count($details) || !Validate::isLoadedObject($cart = new Cart($std_rq->order->id_cart))) {
            return false;
        }

        //$wrapping_fees = $cart->getGiftWrappingPrice(false);
        //$wrapping_fees_wt = $cart->getGiftWrappingPrice(true);

        //print_r($std_rq); exit;

        $ordered_detail_list = array();

        foreach ($details as $detail) {
            if (!array_key_exists($detail->id_order_invoice, $ordered_detail_list)) {
                $ordered_detail_list[$detail->id_order_invoice] = array();
            }

            array_push($ordered_detail_list[$detail->id_order_invoice], $detail);
        }

        if (count($ordered_detail_list)) {
            if ($std_rq->order->hasInvoice()) {
                foreach ($ordered_detail_list as $details) {
                    $total_products_tax_excl = 0;
                    $total_products_tax_incl = 0;

                    foreach ($details as $detail) {
                        $total_products_tax_excl += (float)$detail->total_price_tax_excl;
                        $total_products_tax_incl += (float)$detail->total_price_tax_incl;
                    }

                    $order_invoice = new OrderInvoice($detail->id_order_invoice);

                    $order_carrier = self::getOrderCarrierForInvoice($order_invoice);

                    // Create new invoice
                    if ($order_invoice->id == 0) {
                        $order_invoice->id_order = $std_rq->order->id;

                        if ($order_invoice->number) {
                            Configuration::updateValue('PS_INVOICE_START_NUMBER', false);
                        } else {
                            $order_invoice->number = Order::getLastInvoiceNumber() + 1;
                        }

                        $invoice_address = new Address((int)$std_rq->order->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
                        $carrier = new Carrier((int)$std_rq->order->id_carrier);
                        $tax_calculator = $carrier->getTaxCalculator($invoice_address);

                        $order_invoice->total_shipping_tax_excl = $order_carrier
                            ? $order_carrier->shipping_cost_tax_excl
                            : 0;
                        $order_invoice->total_shipping_tax_incl = $order_carrier
                            ? $order_carrier->shipping_cost_tax_incl
                            : 0;
                        $order_invoice->total_wrapping_tax_excl = abs($std_rq->order->total_wrapping_tax_excl);
                        $order_invoice->total_wrapping_tax_incl = abs($std_rq->order->total_wrapping_tax_incl);
                        $order_invoice->total_products = Tools::ps_round($total_products_tax_excl, 2);
                        $order_invoice->total_products_wt = Tools::ps_round($total_products_tax_incl, 2);
                        $order_invoice->total_paid_tax_excl = Tools::ps_round(
                            $order_invoice->total_products + $order_invoice->total_shipping_tax_excl
                            + $order_invoice->total_wrapping_tax_excl,
                            2
                        );
                        $order_invoice->total_paid_tax_incl = Tools::ps_round(
                            $order_invoice->total_products_wt + $order_invoice->total_shipping_tax_incl
                            + $order_invoice->total_wrapping_tax_incl,
                            2
                        );

                        $order_invoice->shipping_tax_computation_method = (int)$tax_calculator->computation_method;

                        // Update current order field, only shipping because other field is updated later
                        $std_rq->order->total_shipping += $order_invoice->total_shipping_tax_incl;
                        $std_rq->order->total_shipping_tax_excl += $order_invoice->total_shipping_tax_excl;
                        $std_rq->order->total_shipping_tax_incl += $order_invoice->total_shipping_tax_incl;

                        $std_rq->order->total_wrapping += abs($cart->getOrderTotal(true, Cart::ONLY_WRAPPING));
                        $std_rq->order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
                        $std_rq->order->total_wrapping_tax_incl += $std_rq->order->total_wrapping;

                        $order_invoice->add();

                        $order_invoice->saveCarrierTaxCalculator(
                            $tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl)
                        );

                        $order_carrier = new OrderCarrier();
                        $order_carrier->id_order = (int)$std_rq->order->id;
                        $order_carrier->id_carrier = (int)$std_rq->order->id_carrier;
                        $order_carrier->id_order_invoice = (int)$order_invoice->id;
                        $order_carrier->weight = (float)$cart->getTotalWeight();
                        $order_carrier->shipping_cost_tax_excl = (float)$order_invoice->total_shipping_tax_excl;
                        $order_carrier->shipping_cost_tax_incl = (float)$order_invoice->total_shipping_tax_incl;
                        $order_carrier->add();
                    } else {
                        $order_invoice->total_shipping_tax_excl = $order_carrier
                            ? $order_carrier->shipping_cost_tax_excl
                            : 0;
                        $order_invoice->total_shipping_tax_incl = $order_carrier
                            ? $order_carrier->shipping_cost_tax_incl
                            : 0;

                        $order_invoice->total_wrapping_tax_excl = abs($std_rq->order->total_wrapping_tax_excl);
                        $order_invoice->total_wrapping_tax_incl = abs($std_rq->order->total_wrapping_tax_incl);

                        $order_invoice->total_products = Tools::ps_round($total_products_tax_excl, 2);
                        $order_invoice->total_products_wt = Tools::ps_round($total_products_tax_incl, 2);
                        $order_invoice->total_paid_tax_excl = Tools::ps_round(
                            $order_invoice->total_products + $order_invoice->total_shipping_tax_excl
                            + $order_invoice->total_wrapping_tax_excl - $order_invoice->total_discount_tax_excl,
                            2
                        );
                        $order_invoice->total_paid_tax_incl = Tools::ps_round(
                            $order_invoice->total_products_wt + $order_invoice->total_shipping_tax_incl
                            + $order_invoice->total_wrapping_tax_incl - $order_invoice->total_discount_tax_incl,
                            2
                        );
                        $order_invoice->update();
                    }
                }
            }
        }
    }


    /*
     * Updates stock information about a product in an order.
     *
     * @param array $initial_qties - an array of initial product amounts in this
     *                               order
     *
     * @param object $detail - an Instance of OrderDetail class
     *
     * @return void
     */
    private static function updateStockInfo($initial_qties, OrderDetail $detail, $del = false)
    {
        $k = $detail->product_id.'_'.$detail->product_attribute_id;

        $stock_new_amount = 0;

        if (array_key_exists($k, $initial_qties)) {
            /*$stock_amount = $initial_qties[$k]['stock_quantity'];*/
            $amount_in_order = $initial_qties[$k]['cart_quantity'];

            if ($del) {
                $new_amount = 0;
            } else {
                $new_amount = $detail->product_quantity;
            }

            if ($new_amount <= 0) {
                $stock_new_amount = $amount_in_order;
            } elseif ($new_amount != $amount_in_order) {
                $stock_new_amount = $amount_in_order - $new_amount;
            }
        } else {
            $stock_new_amount = ($detail->product_quantity * (-1));
        }

        if (!StockAvailable::dependsOnStock($detail->product_id)) {
            StockAvailable::updateQuantity($detail->product_id, $detail->product_attribute_id, $stock_new_amount);
        }
    }


    /*
     *  Saves order modifications (adds new products, updates the old ones, etc.)
     * ****
     * @todo: to make this function work good it is necessary to check all errors first and only then save something.
     * currently part of things are saved in db and other part is not due errors (not critical) in previous stages
     */
    public function executeSaveOrder(stdClass $std_rq)
    {
        $cart = new Cart($std_rq->order->id_cart);
        $shop = new Shop($cart->id_shop);
        $notify_customer = Tools::getValue('notify_customer', false);
        $id_address_delivery = (int)$cart->id_address_delivery;
        $products = Tools::getValue('products', false);
        $documents = Tools::getValue('documents', false);
        $discounts = Tools::getValue('discounts', false);
        $shipping_data = Tools::getValue('shipping', false);
        $payment_data = Tools::getValue('payment_data', false);
        $order_data_modified = Tools::getValue('order_data_modified', false);
        $is_recyclable = Tools::getValue('is_recyclable');
        $wrapping = Tools::getValue('wrapping');
        $critical_error = false;
        $initial_qties = self::getInitialProductQuantities($cart);
        //$stock_updates = array();
        $return = array(
            'index_order_detail' => array()
        );

        // reading all current order products. Necessary to send notifications and prevent save of duplicate order details
        $orderDetails = Db::getInstance()->executeS('select product_id, product_attribute_id, id_order_detail, product_quantity as quantity'.
                ' from ' . _DB_PREFIX_ .'order_detail where id_order=' . $std_rq->order->id);
        $orderDetailsMapBefore = [];
        foreach ($orderDetails as $orderDetail)
        {
            $orderDetailsMapBefore[$orderDetail['product_id'] . '_' . $orderDetail['product_attribute_id']] = $orderDetail;
        }
        
        // find and remove duplicate of products that already exist in order
        // create new order detail map contaning only products that are marked as deleted
        $orderDetails = $orderDetailsMapBefore;
        foreach ($products as $product)
        {
            if ($product['isDeleted'])
            {
                unset($orderDetails[$product['productId'].'_'.$product['productAttributeId']]);
            }
        }
        foreach ($products as $key=>$product)
        {
            // check if product is duplicate
            if (isset($orderDetails[$product['productId'] . '_' . $product['productAttributeId']]) &&
                 $orderDetails[$product['productId'].'_'.$product['productAttributeId']]['id_order_detail'] != $product['idOrderDetail'])
            {
                // skip product
                self::setAjaxError($this->l('Product is already in cart:') . ' "' . $product['productName'] . '". It is recommeded to reload page.', 
                        'message_placeholders/_product_list_errors');
                unset($products[$key]);
            }
        }

        $wrapping_tax_incl = 0;
        $wrapping_tax_excl = 0;

        if (is_array($wrapping) && $wrapping['is_gift']) {
            $wrapping_tax_incl = $wrapping['price_wt'];
            $wrapping_tax_excl = $wrapping['price'];
        }

        if (!Validate::isLoadedObject($cart)) {
            self::setAjaxError(
                $this->l('Unable to load an original cart for this order'),
                'message_placeholders/_product_list_errors'
            );
            $critical_error = true;
        }

        if (!$products
            || !is_array($products)
            || !count($products)
            || count(self::filterProductList($products, OrderEdit::ONLY_NON_DELETED)) == 0) {
            self::setAjaxError(
                $this->l('The product list can not be empty'),
                'message_placeholders/_product_list_errors'
            );
            $critical_error = true;
        }

        //$order_total_weight = 0;
        //$order_total_price = 0;
        //$order_total_price_wt = 0;

        foreach ($products as &$pr) {
            self::translateProductFields($pr);

            $tax_data = explode(':', $pr['productTaxEdit']);
            unset($pr['productTaxEdit']);
            $pr['id_tax'] = $tax_data[0];
            $pr['tax_rate'] = $tax_data[1];

            $pr['quantity_in_stock'] = (int)Product::getQuantity($pr['product_id'], $pr['product_attribute_id']);
        }

        if (is_array($discounts) && count($discounts)) {
            foreach ($discounts as &$discount) {
                self::translateDiscountFields($discount);
            }
        }

        if (is_array($shipping_data) && count($shipping_data)) {
            foreach ($shipping_data as &$carrier) {
                self::translateShippingFields($carrier);

                if ($carrier['id_carrier'] == 0) {
                    $carrier['shipping_cost_tax_excl'] = $carrier['shipping_cost_tax_incl'] = 0;
                }
            }
        }

        //$product_totals = self::getTotalsFromProductList($products);
        $discount_totals = self::getTotalsFromDiscountsList($discounts);
        $shipping_totals = self::getTotalsFromShippingList($shipping_data);

        //$new_products = self::filterProductList($products, OrderEdit::ONLY_NEW);
        //$existing_products = self::filterProductList($products, OrderEdit::ONLY_EXISTING);
        //$products_to_delete = self::filterProductList($products, OrderEdit::ONLY_DELETED);
        $saved_details = array();

        $order_data = array(
            'total_discounts' => $discount_totals['total_wt'],
            'total_discounts_tax_incl' => $discount_totals['total_wt'],
            'total_discounts_tax_excl' => $discount_totals['total'],
            'total_paid' => 0,
            'total_paid_tax_incl' => 0,
            'total_paid_tax_excl' => 0,
            'total_products' => 0,
            'total_products_wt' => 0,
            'total_shipping' => $shipping_totals['total_wt'],
            'total_shipping_tax_incl' => $shipping_totals['total_wt'],
            'total_shipping_tax_excl' => $shipping_totals['total'],
            'carrier_tax_rate' => (is_array($shipping_data) && count($shipping_data) == 1 && $shipping_data_el = reset($shipping_data))?
                    (float)$shipping_data_el['shipping_cost_tax_rate']:
                    ((float)$shipping_totals['total'] <= 0?0:
                        Tools::ps_round((($shipping_totals['total_wt'] - $shipping_totals['total']) / $shipping_totals['total']) * 100, 3)),
            'total_wrapping' => 0,
            'total_wrapping_tax_incl' => $wrapping_tax_incl,
            'total_wrapping_tax_excl' => $wrapping_tax_excl
        );

        $order_data['recyclable'] = (int)$is_recyclable;

        if (isset($order_data_modified['date_add'])) {
            $order_data['date_add'] = $order_data_modified['date_add'];
        }

        if (!$critical_error) {
            $cart_amounts = array();

            foreach ($products as $product) {
                if (self::productNeedsToBeDeleted($product)) {
                    if (!self::productIsNew($product)) {
                        $product['quantity'] = 0;
                    } else {
                        continue;
                    }
                }

                if (!array_key_exists((int)$product['product_id'].'_'.(int)$product['product_attribute_id'], $cart_amounts)) {
                    $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']] = array(
                        'product_id' => (int)$product['product_id'],
                        'product_attribute_id' => (int)$product['product_attribute_id'],
                        'quantity' => 0
                    );
                }

                if (array_key_exists('quantity', $product)) {
                    $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']]['quantity'] += $product['quantity'];
                } elseif (array_key_exists('quantityCustom', $product)) {
                    $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']]['quantity'] += $product['quantityCustom'];
                }
            }

            foreach ($products as $product) 
            {
                $skip = false;

                if (array_key_exists((int)$product['product_id'].'_'.(int)$product['product_attribute_id'], $cart_amounts)) {
                    $cart_result = $this->cartQtyUpdate(
                        $cart,
                        $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']]['quantity'],
                        $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']]['product_id'],
                        $cart_amounts[(int)$product['product_id'].'_'.(int)$product['product_attribute_id']]['product_attribute_id'],
                        $id_address_delivery,
                        $shop
                    );
                }

                switch ($cart_result) {
                    case OrderEdit::PRODUCT_IN_CART_ADD_ERROR:
                        self::setAjaxError(
                            $this->l('Unable to add this product to cart:').' "'.$product['name'].'"',
                            'message_placeholders/_product_list_errors'
                        );
                        $skip = true;
                        break;
                    case OrderEdit::PRODUCT_IN_CART_DELETE_ERROR:
                        self::setAjaxError(
                            $this->l('Unable to delete this product from cart:').' "'.$product['name'].'"',
                            'message_placeholders/_product_list_errors'
                        );
                        $skip = true;
                        break;
                    case OrderEdit::PRODUCT_IN_CART_UPDATE_ERROR:
                        self::setAjaxError(
                            $this->l('Unable to update this product in cart:').' "'.$product['name'].'"',
                            'message_placeholders/_product_list_errors'
                        );
                        $skip = true;
                        break;
                    case OrderEdit::PRODUCT_IN_CART_INVALID_QTY:
                        self::setAjaxError(
                            $this->l('Invalid quantity for a product:').' "'.$product['name'].'"',
                            'message_placeholders/_product_list_errors'
                        );
                        $skip = true;
                        break;
                }

                if (!$skip) {
                    if ($cart_result != OrderEdit::PRODUCT_IN_CART_DELETED) {
                        $detail_backup = false;
                        $detail_backup_id_tax = 0;

                        if (!self::productIsNew($product)) {
                            $detail = new OrderEditOrderDetail((int)$product['id_order_detail']);

                            if (!Validate::isLoadedObject($detail)) {
                                self::setAjaxError(
                                    $this->l('Invalid order detail id for product:').' "'.$product['name'].'" '.
                                    $this->l('It is recommended to reload page'), 'message_placeholders/_product_list_errors'
                                );

                                continue;
                            } else {
                                $detail_backup = $detail;
                                $detail_backup_id_tax = Db::getInstance()->getValue(
                                    'SELECT id_tax
                                    FROM `'._DB_PREFIX_. 'order_detail_tax`
                                    WHERE `id_order_detail` = '.(int)$product['id_order_detail']
                                );
                            }

                            if (self::productNeedsToBeDeleted($product)) {
                                self::updateStockInfo($initial_qties, $detail, true);
                                $this->deleteOrderDetail($detail, $detail_backup);

                                continue;
                            }
                        } else {
                            $detail = new OrderEditOrderDetail();
                        }

                        // OrderDetail class has a "create" method, but we can't use it,
                        // as it takes prices from the database, ignoring user input,
                        // which is why we set class properties ourselves
                        
                        // var_dump($detail->product_weight);

                        self::fillOrderDetail($detail, $std_rq, $product, $shop);
                        // var_dump($detail->total_price_tax_incl);
                        //ini_set('display_errors', 1);
                        //var_dump($detail->def);
                        // var_dump($detail->product_weight);
                        // exit();
                        $detail_errors = $detail->validateController();

                        if ($detail_errors && count($detail_errors)) {
                            foreach ($detail_errors as &$error) {
                                self::setAjaxError(
                                    $this->l('A problem occured when processing product').
                                    ' "'.$product['name'].'": '.$error,
                                    'message_placeholders/_product_list_errors'
                                );
                            }
                        } else {
                            if ($detail = $this->saveOrderDetail($detail, $detail_backup, $product['id_tax'], $detail_backup_id_tax)) {

                                if (array_key_exists('customdataEdit', $product)) {
                                    foreach ($product['customdataEdit'] as $id_cus => $cus) {
                                        foreach ($cus as $key => $value) {
                                            $sql = 'UPDATE '._DB_PREFIX_.'customized_data
                                                SET `value` = "'.$value.'"
                                                WHERE `id_customization` = '.(int)$id_cus.'
                                                AND `index` = '.(int)$key;
                                            Db::getInstance()->execute($sql);
                                        }
                                    }
                                }

                                if (array_key_exists('productCustomQtyEdit', $product)) {
                                    foreach ($product['productCustomQtyEdit'] as $id_cus => $value) {
                                        $sql = 'UPDATE '._DB_PREFIX_.'customization
                                            SET `quantity` = "'.$value.'"
                                            WHERE `id_customization` = '.(int)$id_cus;
                                        Db::getInstance()->execute($sql);
                                    }
                                }
                                
                                $order_data['total_products'] += $detail->total_price_tax_excl;
                                $order_data['total_products_wt'] += $detail->total_price_tax_incl;

                                $return['index_order_detail'][$product['productIndex']] = $detail->id;

                                array_push($saved_details, $detail);

                                self::updateStockInfo($initial_qties, $detail);
                            }
                        }
                    } else {
                        $detail = new OrderDetail((int)$product['id_order_detail']);

                        if (!self::productIsNew($product) && Validate::isLoadedObject($detail)) {
                            self::updateStockInfo($initial_qties, $detail, true);
                            $detail->delete();
                        }
                    }
                }
            }

            if (!$critical_error) //!self::checkIfErrorsExist('message_placeholders/_product_list_errors')) 
            {
                $payment_prepared = array();
                $first_payment = false;
                if (is_array($payment_data) && count($payment_data)) {
                    $i = 0;
                    foreach ($payment_data as $payment) {
                        if ($i == 0) {
                            $first_payment = $payment;
                        }

                        $i++;

                        if (!array_key_exists($payment['orderPayment'], $payment_prepared)) {
                            $payment_prepared[$payment['orderPayment']] = array();
                        }

                        $payment_prepared[$payment['orderPayment']] = array_merge(
                            $payment_prepared[$payment['orderPayment']],
                            $payment
                        );
                    }
                }

                $order_data['total_products'] = Tools::ps_round(
                    $order_data['total_products'],
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );
                $order_data['total_products_wt'] = Tools::ps_round(
                    $order_data['total_products_wt'],
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );
                $order_data['total_paid'] = Tools::ps_round(
                    $order_data['total_products_wt'] + $order_data['total_shipping_tax_incl']
                    + $order_data['total_wrapping_tax_incl'] - $order_data['total_discounts_tax_incl'],
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );
                $order_data['total_paid_tax_excl'] = Tools::ps_round(
                    $order_data['total_products'] + $order_data['total_shipping_tax_excl']
                    + $order_data['total_wrapping_tax_excl'] - $order_data['total_discounts_tax_excl'],
                    defined('_PS_PRICE_COMPUTE_PRECISION_')
                    ? _PS_PRICE_COMPUTE_PRECISION_
                    : _PS_PRICE_DISPLAY_PRECISION_
                );

                $order_data['total_paid'] = ($order_data['total_paid'] == 0)
                    ? (string)$order_data['total_paid']
                    : $order_data['total_paid'];
                $order_data['total_products'] = ($order_data['total_products'] == 0)
                    ? (string)$order_data['total_products']
                    : $order_data['total_products'];
                $order_data['total_products_wt'] = ($order_data['total_products_wt'] == 0)
                    ? (string)$order_data['total_products_wt']
                    : $order_data['total_products_wt'];
                $order_data['total_paid_tax_excl'] = ($order_data['total_paid_tax_excl'] == 0)
                    ? (string)$order_data['total_paid_tax_excl']
                    : $order_data['total_paid_tax_excl'];

                $order_data['total_paid_tax_incl'] = $order_data['total_paid'];

                // write first payment name in order (displaying in order list) - but we don't change payment module
                if ($first_payment !== false) {
                    $order_data['payment'] = $first_payment['paymentName'];
                }

                foreach ($order_data as $order_property => $value) {
                    if (property_exists($std_rq->order, $order_property)) {
                        $std_rq->order->{$order_property} = $value;
                    }
                }

                if ($std_rq->order->id_currency != Tools::getValue('order_currency', false)) {
                    $std_rq->order->id_currency = Tools::getValue('order_currency', false);
                }

                if ($std_rq->order->id_lang != Tools::getValue('id_lang', false)) {
                    $std_rq->order->id_lang = Tools::getValue('id_lang', false);
                }

                self::setWrappingData($std_rq->order);

                if (count($payment_prepared)) {
                    $payment_errors = self::setPaymentData($payment_prepared);

                    if (is_array($payment_errors) && count($payment_errors)) {
                        foreach ($payment_errors as $error) {
                            self::setAjaxError($error, 'message_placeholders/_product_list_errors');
                        }
                    }
                }

                $last_carrier = end($shipping_data);
                reset($shipping_data);

                $std_rq->order->id_carrier = (int)$last_carrier['id_carrier'];

                $order_errors = $std_rq->order->validateController();

                if ($order_errors && count($order_errors)) {
                    foreach ($order_errors as $error) {
                        self::setAjaxError($error, 'message_placeholders/_product_list_errors');
                    }
                } else {
                    if (!$std_rq->order->save()) {
                        self::setAjaxError(
                            $this->l('Unable to save the order'),
                            'message_placeholders/_product_list_errors'
                        );
                    }
                }
            }

            if (!self::checkIfErrorsExist('message_placeholders/_product_list_errors')) {
                $return['success'] = true;

                //self::updateOrderDetailsTax($saved_details, $std_rq);
                self::updateOrderCarrierData($shipping_data, $std_rq, $products);

                self::updateDocumentsData($documents, $std_rq);

                if (is_array($discounts)) {
                    $this->updateOrderDiscountData($discounts, $std_rq);
                }

                //self::setDetailInvoices($saved_details, $std_rq);

                if ($notify_customer) {
                    $this->notifyCustomerByMail($std_rq->order);
                }

                self::setAjaxSuccess(
                    $this->l('An order has been successfully modified'),
                    'message_placeholders/_product_list_errors'
                );
            }
        }

        // check if we need to send notification to server about product qty update
        // create new order details map
        $orderDetails = Db::getInstance()->executeS('select product_id, product_attribute_id, id_order_detail, product_quantity as quantity'.
                ' from ' . _DB_PREFIX_ .'order_detail where id_order=' . $std_rq->order->id);
        $orderDetailsMapAfter = [];
        foreach ($orderDetails as $orderDetail)
        {
            $orderDetailsMapAfter[$orderDetail['product_id'] . '_' . $orderDetail['product_attribute_id']] = $orderDetail;
        }
        // compare maps and send notifications
        $msss_client = ModuleCore::getInstanceByName('msss_client');
        foreach($orderDetailsMapBefore as $key=>$beforeOd)
        {
            if (isset($orderDetailsMapAfter[$key]))
            {
                if($beforeOd['quantity']!=$orderDetailsMapAfter[$key]['quantity'])
                {
                    // quantity updated
                    $msss_client->scheduleStockUpdateById($beforeOd['product_id'], $beforeOd['product_attribute_id'], 
                            $beforeOd['quantity']-$orderDetailsMapAfter[$key]['quantity']);
                }
                unset($orderDetailsMapAfter[$key]);
            }
            else
            {
                // product was deleted
                $msss_client->scheduleStockUpdateById($beforeOd['product_id'], $beforeOd['product_attribute_id'], 
                            $beforeOd['quantity']);
            }
        }
        
        // remaining products in after map were added
        foreach ($orderDetailsMapAfter as $afterOd)
        {
            $msss_client->scheduleStockUpdateById($afterOd['product_id'], $afterOd['product_attribute_id'], 
                            -$afterOd['quantity']);
        }
        
        $return['tpls'] = $this->updateTemplates(
            array('message_placeholders/_product_list_errors.tpl', '_documents.tpl'),
            $std_rq
        );

        self::ajaxReturn($return);
    }

    private function notifyCustomerByMail(Order $order)
    {
        $customer = new Customer($order->id_customer);
        $invoice_address = new Address($order->id_address_invoice);
        $delivery_address = new Address($order->id_address_delivery);
        $currency = new Currency($order->id_currency);
        $products_order = $order->getProducts();
        $discounts = $order->getCartRules();

        $products_by_invoices = array();
        foreach ($products_order as $product) {
            if (!array_key_exists((int)$product['id_order_invoice'], $products_by_invoices)) {
                $products_by_invoices[(int)$product['id_order_invoice']] = array();
            }

            array_push($products_by_invoices[$product['id_order_invoice']], $product);
        }

        $virtual_product = true;
        $carrier_obj = new Carrier($order->id_carrier);

        if (count($products_by_invoices)) {
            $customization_quantities = Customization::countQuantityByCart($order->id_cart);

            $products_list = '';
            $cart_rules_list = '';

            /* $invoice_id =>*/
            foreach ($products_by_invoices as $products) {
                /*
                $invoice = false;
                $carrier = false;

                if ($invoice_id > 0)
                {
                    $invoice = new OrderInvoice($invoice_id);
                    $carrier = self::getOrderCarrierForInvoice($invoice);
                }
                */

                foreach ($products as $key => $product) {
                    $customized_datas = Product::getAllCustomizedDatas((int)$order->id_cart);
                    $customization_quantity = 0;

                    if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']])) {
                        if (array_key_exists($product['product_id'], $customization_quantities)
                            && array_key_exists($product['product_attribute_id'], $customization_quantities[$product['product_id']])) {
                                $customization_quantity = (int)$customization_quantities[$product['product_id']][$product['product_attribute_id']];
                        }

                        $customization_text = '';
                        foreach ($customized_datas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization) {
                            if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD])) {
                                foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text) {
                                    $customization_text .= $text['name'].': '.$text['value'].'<br />';
                                }
                            }

                            if (isset($customization['datas'][Product::CUSTOMIZE_FILE])) {
                                $customization_text .= sprintf(Tools::displayError('%d image(s)'), count($customization['datas'][Product::CUSTOMIZE_FILE])).'<br />';
                            }

                            $customization_text .= '---<br />';
                        }

                        $customization_text = rtrim($customization_text, '---<br />');

                        $products_list .= '<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
                            <td style="padding: 0.6em 0.4em;width: 15%;">'.$product['product_reference'].'</td>
                            <td style="padding: 0.6em 0.4em;width: 30%;"><strong>'.$product['product_name'].' - '.Tools::displayError('Customized').(!empty($customization_text) ? ' - '.$customization_text : '').'</strong></td>
                            <td style="padding: 0.6em 0.4em; width: 20%;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ?Tools::ps_round($product['unit_price_tax_excl'], 2) : $product['unit_price_tax_incl'], $currency, false).'</td>
                            <td style="padding: 0.6em 0.4em; width: 15%;">'.$customization_quantity.'</td>
                            <td style="padding: 0.6em 0.4em; width: 20%;">'.Tools::displayPrice($customization_quantity * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($product['unit_price_tax_excl'], 2) : $product['unit_price_tax_incl']), $currency, false).'</td>
                        </tr>';
                    }

                    if (!$customization_quantity || (int)$product['product_quantity'] > $customization_quantity) {
                        $products_list .= '<tr style="background-color: '.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
                            <td style="padding: 0.6em 0.4em;width: 15%;">'.$product['product_reference'].'</td>
                            <td style="padding: 0.6em 0.4em;width: 30%;"><strong>'.$product['product_name'].'</strong></td>
                            <td style="padding: 0.6em 0.4em; width: 20%;">'.Tools::displayPrice(Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($product['unit_price_tax_excl'], 2) : $product['unit_price_tax_incl'], $currency, false).'</td>
                            <td style="padding: 0.6em 0.4em; width: 15%;">'.((int)$product['product_quantity'] - $customization_quantity).'</td>
                            <td style="padding: 0.6em 0.4em; width: 20%;">'.Tools::displayPrice(((int)$product['product_quantity'] - $customization_quantity) * (Product::getTaxCalculationMethod() == PS_TAX_EXC ? Tools::ps_round($product['unit_price_tax_excl'], 2) : $product['unit_price_tax_incl']), $currency, false).'</td>
                        </tr>';
                    }

                    if ($product['download_hash'] == '') {
                        $virtual_product &= false;
                    }
                }
            }

            if ($discounts && count($discounts)) {
                foreach ($discounts as $cart_rule) {
                    $cart_rules_list .= '
                        <tr>
                            <td colspan="4" style="padding:0.6em 0.4em;text-align:right">'
                            .Tools::displayError('Voucher name:').' '.$cart_rule['name'].'</td>
                            <td style="padding:0.6em 0.4em;text-align:right">'.
                            ($cart_rule['value'] != 0.00 ? '-' : '').
                            Tools::displayPrice($cart_rule['value'], $currency, false).'</td>
                        </tr>';
                }
            }
        }

        $data = array(
            '{firstname}' => $customer->firstname,
            '{lastname}' => $customer->lastname,
            '{email}' => $customer->email,
            '{delivery_block_txt}' => AddressFormat::generateAddress(
                $delivery_address,
                array('avoid' => array()),
                "\n",
                ' '
            ),
            '{invoice_block_txt}' => AddressFormat::generateAddress(
                $invoice_address,
                array('avoid' => array()),
                "\n",
                ' '
            ),
            '{delivery_block_html}' => AddressFormat::generateAddress(
                $delivery_address,
                array('avoid' => array()),
                "\n",
                ' ',
                array(
                    'firstname' => '<span style="font-weight:bold;">%s</span>',
                    'lastname' => '<span style="font-weight:bold;">%s</span>'
                )
            ),
            '{invoice_block_html}' => AddressFormat::generateAddress(
                $invoice_address,
                array('avoid' => array()),
                "\n",
                ' ',
                array(
                    'firstname' => '<span style="font-weight:bold;">%s</span>',
                    'lastname' => '<span style="font-weight:bold;">%s</span>'
                )
            ),
            '{delivery_company}' => $delivery_address->company,
            '{delivery_firstname}' => $delivery_address->firstname,
            '{delivery_lastname}' => $delivery_address->lastname,
            '{delivery_address1}' => $delivery_address->address1,
            '{delivery_address2}' => $delivery_address->address2,
            '{delivery_city}' => $delivery_address->city,
            '{delivery_postal_code}' => $delivery_address->postcode,
            '{delivery_country}' => $delivery_address->country,
            '{delivery_state}' => $delivery_address->id_state ?'' : '',
            '{delivery_phone}' => ($delivery_address->phone)
                ? $delivery_address->phone
                : $delivery_address->phone_mobile,
            '{delivery_other}' => $delivery_address->other,
            '{invoice_company}' => $invoice_address->company,
            '{invoice_vat_number}' => $invoice_address->vat_number,
            '{invoice_firstname}' => $invoice_address->firstname,
            '{invoice_lastname}' => $invoice_address->lastname,
            '{invoice_address2}' => $invoice_address->address2,
            '{invoice_address1}' => $invoice_address->address1,
            '{invoice_city}' => $invoice_address->city,
            '{invoice_postal_code}' => $invoice_address->postcode,
            '{invoice_country}' => $invoice_address->country,
            '{invoice_state}' => $invoice_address->id_state ? /*$invoice_state->name*/'' : '',
            '{invoice_phone}' => ($invoice_address->phone) ? $invoice_address->phone : $invoice_address->phone_mobile,
            '{invoice_other}' => $invoice_address->other,
            '{order_name}' => $order->getUniqReference(),
            '{date}' => Tools::displayDate(date('Y-m-d H:i:s'), null, 1),
            '{carrier}' => $virtual_product ? Tools::displayError('No carrier') : $carrier_obj->name,
            '{payment}' => Tools::substr($order->payment, 0, 32),
            '{products}' => $products_list,
            '{discounts}' => $cart_rules_list,
            '{total_paid}' => Tools::displayPrice($order->total_paid, $currency, false),
            '{total_products}' => Tools::displayPrice(
                $order->total_paid - $order->total_shipping - $order->total_wrapping + $order->total_discounts,
                $currency,
                false
            ),
            '{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency, false),
            '{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency, false),
            '{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency, false),
            '{total_tax_paid}' => Tools::displayPrice(
                ($order->total_products_wt - $order->total_products)
                + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl),
                $this->context->currency,
                false
            )
        );

        if (Validate::isEmail($customer->email)) {
            Mail::Send(
                (int)$order->id_lang,
                'm_order_changed',
                $this->l('Order changed'),
                $data,
                $customer->email,
                $customer->firstname.' '.$customer->lastname,
                null,
                null,
                null,
                null,
                _PS_MODULE_DIR_.'orderedit/mails/',
                false,
                (int)$order->id_shop
            );
        }
    }


    /*
     * Adds an invoice to an order.
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeAddInvoice(stdClass $std_rq)
    {
        if ($std_rq->order->hasInvoice()) {
            return false;
        }

        $std_rq->order->setInvoice(true);

        self::setAjaxSuccess($this->l('New invoice has been generated'), '_documents');

        $return = array(
            'tpls' => $this->updateTemplates(array('_documents.tpl', '_payment.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Adds a payment to an order. Payment info, such as total amount and
     * payments method are passed via ajax, and can be received using
     * Tools::getValue
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeAddPayment(stdClass $std_rq)
    {
        $amount = str_replace(',', '.', Tools::getValue('payment_amount'));
        $currency = new Currency(Tools::getValue('payment_currency'));
        $order_has_invoice = $std_rq->order->hasInvoice();

        if ($order_has_invoice) {
            $order_invoice = new OrderInvoice(Tools::getValue('payment_invoice'));
        } else {
            $order_invoice = null;
        }

        if (!Validate::isFloat($amount)) {
            self::setAjaxError($this->l('Please enter a valid payment amount'), '_payment');
        } elseif (($std_rq->order->total_paid_real + $amount) < 0) {
            self::setAjaxError($this->l('The total amount can not be less than zero'), '_payment');
        } else {
            if (!$std_rq->order->addOrderPayment(
                $amount,
                Tools::getValue('payment_method'),
                Tools::getValue('payment_transaction_id'),
                $currency,
                Tools::getValue('payment_date'),
                $order_invoice
            )) {
                self::setAjaxError($this->l('Unable to add payment'), '_payment');
            } else {
                self::setAjaxSuccess($this->l('New payment has been added successfully'), '_payment');
            }
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_documents.tpl', '_payment.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Deletes a document (invoice, order slip, etc.) from an order.
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeDeletePayment(stdClass $std_rq)
    {
        $id_payment = Tools::getValue('id_payment', false);

        if (!$id_payment || !Validate::isUnsignedId($id_payment)) {
            self::setAjaxError($this->l('Unable to locate selected payment ID'), '_payment');
        }

        if (!self::checkIfErrorsExist('_payment')) {
            // Clear related tables as well
            $amount = Db::getInstance()->getValue(
                'SELECT `amount` FROM `'._DB_PREFIX_.'order_payment` WHERE `id_order_payment` = '.(int)$id_payment
            );

            if (Db::getInstance()->Execute(
                'DELETE FROM `'._DB_PREFIX_.'order_invoice_payment` WHERE `id_order_payment` = '.(int)$id_payment
            )
                && Db::getInstance()->Execute(
                    'DELETE FROM `'._DB_PREFIX_.'order_payment` WHERE `id_order_payment` = '.(int)$id_payment
                )) {
                Db::getInstance()->Execute(
                    'UPDATE `'._DB_PREFIX_.'orders` SET `total_paid_real`=(`total_paid_real`-'.$amount.')
                    WHERE `id_order` = '.(int)$std_rq->order->id
                );
            }

            self::setAjaxSuccess($this->l('Document has been successfully deleted'), '_payment');
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_documents.tpl', '_payment.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }

    public function executechangeAddressShipping(stdClass $std_rq)
    {
        $new_address = Tools::getValue('id_address_shipping');
        $res = true;
        if ($std_rq->order->hasInvoice()) {
            $order_invoice = OrderInvoice::getInvoiceByNumber($std_rq->order->invoice_number);
            // $order_invoice->invoice_address = '';
            $order_invoice->delivery_address = '';
            $res &= $order_invoice->update();
        }

        $std_rq->order->id_address_delivery = $new_address;
        $res &= $std_rq->order->update();

        if (!$res) {
            self::setAjaxError($this->l('Error changing addresses.'), '_address');
        } else {
            self::setAjaxSuccess($this->l('Delivery address changed'), '_address');
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_address.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }

    public function executechangeAddressInvoice(stdClass $std_rq)
    {
        $new_address = Tools::getValue('id_address_invoice');
        $res = true;
        
        if ($std_rq->order->hasInvoice()) {
            $order_invoice = OrderInvoice::getInvoiceByNumber($std_rq->order->invoice_number);
            $order_invoice->invoice_address = '';
            // $order_invoice->delivery_address = '';
            $res &= $order_invoice->update();
        }

        $std_rq->order->id_address_invoice = $new_address;
        $res &= $std_rq->order->update();

        if (!$res) {
            self::setAjaxError($this->l('Error changing addresses.'), '_address');
        } else {
            self::setAjaxSuccess($this->l('Invoice address changed'), '_address');
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_address.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }

    /*
     * Adds a new order status to an order (such as "delivered", "cancelled",
     * etc.)
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeAddOrderStatus(stdClass $std_rq)
    {
        //$link = $std_rq->context->link;
        $order_state = new OrderState(Tools::getValue('id_order_state'));
        $id_employee = (int)Tools::getValue('iem', false);

        if (!Validate::isLoadedObject($order_state)) {
            self::setAjaxError($this->l('Invalid order state selected, make sure it exists'), '_status');
        } else {
            $current_order_state = $std_rq->order->getCurrentOrderState();

            if ($current_order_state->id != $order_state->id) {
                // Create new OrderHistory
                $history = new OrderHistory();
                $history->id_order = $std_rq->order->id;
                $history->id_employee = $id_employee;

                $use_existings_payment = false;

                if (!$std_rq->order->hasInvoice()) {
                    $use_existings_payment = true;
                }

                $history->changeIdOrderState((int)$order_state->id, $std_rq->order, $use_existings_payment);

                $carrier = new Carrier($std_rq->order->id_carrier, $std_rq->order->id_lang);

                $template_vars = array();

                if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $std_rq->order->shipping_number) {
                    $template_vars = array('{followup}' => str_replace('@', $std_rq->order->shipping_number, $carrier->url));
                }

                if ($history->addWithemail(true, $template_vars)) {
                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')) {
                        foreach ($std_rq->order->getProducts() as $product) {
                            if (StockAvailable::dependsOnStock($product['product_id'])) {
                                StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
                            }
                        }
                    }

                    self::setAjaxSuccess($this->l('Order history has been successfully changed'), '_status');
                } else {
                    self::setAjaxError(
                        $this->l(
                            'An error occurred while changing the status or was unable to send e-mail to the customer.'
                        ),
                        '_status'
                    );
                }
            } else {
                self::setAjaxError(
                    $this->l('The order state you have selected is already a current state for this order'),
                    '_status'
                );
            }
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_status.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Deletes an order status from order's status history
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeDeleteOrderStatus(stdClass $std_rq)
    {
        $id_order_history = Tools::getValue('id_order_history');

        if (!Validate::isUnsignedId($id_order_history)) {
            self::setAjaxError($this->l('Invalid order history ID'), '_status');
        } elseif (self::checkOrderStatusAmount($std_rq->order->id) == 1) {
            self::setAjaxError(
                $this->l('You can\'t delete all order statuses, please add another status prior deleting this one'),
                '_status'
            );
        }

        if (!self::checkIfErrorsExist('_status')) {
            if (Db::getInstance()->delete('order_history', '`id_order_history` = '.(int)$id_order_history)) {

                $id_order_state = Db::getInstance()->getValue(
                    'SELECT `id_order_state`
                    FROM `'._DB_PREFIX_.'order_history`
                    WHERE `id_order` = '.(int)$std_rq->order->id.'
                    ORDER BY `date_add` DESC, `id_order_history` DESC'
                );

                Db::getInstance()->update(
                    'orders',
                    array('current_state' => (int)$id_order_state),
                    '`id_order` = '.(int)$std_rq->order->id
                );

                self::setAjaxSuccess(
                    $this->l('Order history has been successfully changed'),
                    '_status'
                );
            } else {
                self::setAjaxError(
                    $this->l('A database error occured when trying to delete order history item'),
                    '_status'
                );
            }
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_status.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Adds a discount to an order.
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeAddDiscount(stdClass $std_rq)
    {
        if (!Tools::getValue('discount_name')) {
            self::setAjaxError($this->l('You must specify a name in order to create a new discount'), '_discounts');
        } else {
            if ($std_rq->order->hasInvoice()) {
                // If the discount is for only one invoice
                if (!Tools::isSubmit('discount_all_invoices')) {
                    $order_invoice = new OrderInvoice(Tools::getValue('discount_invoice'));

                    if (!Validate::isLoadedObject($order_invoice)) {
                        self::setAjaxError($this->l('Can\'t load Order Invoice object'), '_discounts');
                    }
                }
            }

            $cart_rules = array();

            switch (Tools::getValue('discount_type')) {
                // Percent type
                case 1:
                    if (Tools::getValue('discount_value') < 100) {
                        if (isset($order_invoice)) {
                            $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                $order_invoice->total_paid_tax_incl * Tools::getValue('discount_value') / 100,
                                2
                            );
                            $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                $order_invoice->total_paid_tax_excl * Tools::getValue('discount_value') / 100,
                                2
                            );

                            // Update OrderInvoice
                            $this->applyDiscountOnInvoice(
                                $order_invoice,
                                $cart_rules[$order_invoice->id]['value_tax_incl'],
                                $cart_rules[$order_invoice->id]['value_tax_excl']
                            );
                        } elseif ($std_rq->order->hasInvoice()) {
                            $order_invoices_collection = $std_rq->order->getInvoicesCollection();

                            foreach ($order_invoices_collection as $order_invoice) {
                                $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                    $order_invoice->total_paid_tax_incl * Tools::getValue('discount_value') / 100,
                                    2
                                );
                                $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                    $order_invoice->total_paid_tax_excl * Tools::getValue('discount_value') / 100,
                                    2
                                );

                                // Update OrderInvoice
                                $this->applyDiscountOnInvoice(
                                    $order_invoice,
                                    $cart_rules[$order_invoice->id]['value_tax_incl'],
                                    $cart_rules[$order_invoice->id]['value_tax_excl']
                                );
                            }
                        } else {
                            $cart_rules[0]['value_tax_incl'] = Tools::ps_round($std_rq->order->total_paid_tax_incl * Tools::getValue('discount_value') / 100, 2);
                            $cart_rules[0]['value_tax_excl'] = Tools::ps_round($std_rq->order->total_paid_tax_excl * Tools::getValue('discount_value') / 100, 2);
                        }
                    } else {
                        self::setAjaxError($this->l('Discount value is invalid'), '_discounts');
                    }
                    break;
                // Amount type
                case 2:
                    if (isset($order_invoice)) {
                        if (Tools::getValue('discount_value') > $order_invoice->total_paid_tax_incl) {
                            self::setAjaxError($this->l('Discount value is greater than the order invoice total'), '_discounts');
                        } else {
                            $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                Tools::getValue('discount_value'),
                                2
                            );
                            $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                Tools::getValue('discount_value')/(1+($std_rq->order->getTaxesAverageUsed()/100)),
                                2
                            );

                            // Update OrderInvoice
                            $this->applyDiscountOnInvoice(
                                $order_invoice,
                                $cart_rules[$order_invoice->id]['value_tax_incl'],
                                $cart_rules[$order_invoice->id]['value_tax_excl']
                            );
                        }
                    } elseif ($std_rq->order->hasInvoice()) {
                        $order_invoices_collection = $std_rq->order->getInvoicesCollection();

                        foreach ($order_invoices_collection as $order_invoice) {
                            if (Tools::getValue('discount_value') > $order_invoice->total_paid_tax_incl) {
                                self::setAjaxError(
                                    $this->l('Discount value is greater than the order invoice total (Invoice:').
                                    $order_invoice->getInvoiceNumberFormatted($std_rq->context->language->id).$this->l(')'),
                                    '_discounts'
                                );
                            } else {
                                $cart_rules[$order_invoice->id]['value_tax_incl'] = Tools::ps_round(
                                    Tools::getValue('discount_value'),
                                    2
                                );
                                $cart_rules[$order_invoice->id]['value_tax_excl'] = Tools::ps_round(
                                    Tools::getValue('discount_value')/(1+($std_rq->order->getTaxesAverageUsed()/100)),
                                    2
                                );

                                // Update OrderInvoice
                                $this->applyDiscountOnInvoice(
                                    $order_invoice,
                                    $cart_rules[$order_invoice->id]['value_tax_incl'],
                                    $cart_rules[$order_invoice->id]['value_tax_excl']
                                );
                            }
                        }
                    } else {
                        if (Tools::getValue('discount_value') > $std_rq->order->total_paid_tax_incl) {
                            self::setAjaxError($this->l('Discount value is greater than the order invoice total'), '_discounts');
                        } else {
                            $cart_rules[0]['value_tax_incl'] = Tools::ps_round(Tools::getValue('discount_value'), 2);
                            $cart_rules[0]['value_tax_excl'] = Tools::ps_round(
                                Tools::getValue('discount_value')/(1+($std_rq->order->getTaxesAverageUsed()/100)),
                                2
                            );
                        }
                    }
                    break;
                // Free shipping type
                case 3:
                    if (isset($order_invoice)) {
                        if ($order_invoice->total_shipping_tax_incl > 0) {
                            $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                            $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                            // Update OrderInvoice
                            $this->applyDiscountOnInvoice(
                                $order_invoice,
                                $cart_rules[$order_invoice->id]['value_tax_incl'],
                                $cart_rules[$order_invoice->id]['value_tax_excl']
                            );
                        }
                    } elseif ($std_rq->order->hasInvoice()) {
                        $order_invoices_collection = $std_rq->order->getInvoicesCollection();
                        foreach ($order_invoices_collection as $order_invoice) {
                            if ($order_invoice->total_shipping_tax_incl <= 0) {
                                continue;
                            }
                            $cart_rules[$order_invoice->id]['value_tax_incl'] = $order_invoice->total_shipping_tax_incl;
                            $cart_rules[$order_invoice->id]['value_tax_excl'] = $order_invoice->total_shipping_tax_excl;

                            // Update OrderInvoice
                            $this->applyDiscountOnInvoice(
                                $order_invoice,
                                $cart_rules[$order_invoice->id]['value_tax_incl'],
                                $cart_rules[$order_invoice->id]['value_tax_excl']
                            );
                        }
                    } else {
                        $cart_rules[0]['value_tax_incl'] = $std_rq->order->total_shipping_tax_incl;
                        $cart_rules[0]['value_tax_excl'] = $std_rq->order->total_shipping_tax_excl;
                    }
                    break;
                default:
                    self::setAjaxError($this->l('Discount type is invalid'), '_discounts');
            }

            $res = true;
            foreach ($cart_rules as &$cart_rule) {
                $cart_rule_obj = new CartRule();
                $cart_rule_obj->date_from = date('Y-m-d H:i:s', strtotime('-1 hour', strtotime($std_rq->order->date_add)));
                $cart_rule_obj->date_to = date('Y-m-d H:i:s', strtotime('+1 hour'));
                $cart_rule_obj->name[Configuration::get('PS_LANG_DEFAULT')] = Tools::getValue('discount_name');
                $cart_rule_obj->quantity = 0;
                $cart_rule_obj->quantity_per_user = 1;

                if (Tools::getValue('discount_type') == 1) {
                    $cart_rule_obj->reduction_percent = Tools::getValue('discount_value');
                } elseif (Tools::getValue('discount_type') == 2) {
                    $cart_rule_obj->reduction_amount = $cart_rule['value_tax_excl'];
                } elseif (Tools::getValue('discount_type') == 3) {
                    $cart_rule_obj->free_shipping = 1;
                }

                $cart_rule_obj->active = 0;

                if ($res = $cart_rule_obj->add()) {
                    $cart_rule['id'] = $cart_rule_obj->id;
                } else {
                    break;
                }
            }

            if ($res) {
                foreach ($cart_rules as $id_order_invoice => $cart_rule) {
                    // Create OrderCartRule
                    $order_cart_rule = new OrderCartRule();
                    $order_cart_rule->id_order = $std_rq->order->id;
                    $order_cart_rule->id_cart_rule = $cart_rule['id'];
                    $order_cart_rule->id_order_invoice = $id_order_invoice;
                    $order_cart_rule->name = Tools::getValue('discount_name');
                    $order_cart_rule->value = $cart_rule['value_tax_incl'];
                    $order_cart_rule->value_tax_excl = $cart_rule['value_tax_excl'];
                    $res &= $order_cart_rule->add();

                    $std_rq->order->total_discounts += $order_cart_rule->value;
                    $std_rq->order->total_discounts_tax_incl += $order_cart_rule->value;
                    $std_rq->order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
                    $std_rq->order->total_paid -= $order_cart_rule->value;
                    $std_rq->order->total_paid_tax_incl -= $order_cart_rule->value;
                    $std_rq->order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
                }

                // Update Order
                $res &= $std_rq->order->update();
            }

            if ($res) {
                self::setAjaxSuccess($this->l('A discount has been successfully added to this order'), '_discounts');
            } else {
                self::setAjaxError($this->l('An error occurred on OrderCartRule creation'), '_discounts');
            }
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_discounts.tpl', '_totals.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Deletes an existing discount from an order
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeDeleteDiscount(stdClass $std_rq)
    {
        $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));

        if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $std_rq->order->id) {
            if ($order_cart_rule->id_order_invoice) {
                $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);

                if (!Validate::isLoadedObject($order_invoice)) {
                    self::setAjaxError($this->l('Can\'t load Order Invoice object'), '_discounts');
                }

                $order_invoice->total_discount_tax_excl -= $order_cart_rule->value_tax_excl;
                $order_invoice->total_discount_tax_incl -= $order_cart_rule->value;

                $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                $order_invoice->total_paid_tax_incl += $order_cart_rule->value;

                $order_invoice->update();
            }

            $std_rq->order->total_discounts -= $order_cart_rule->value;
            $std_rq->order->total_discounts_tax_incl -= $order_cart_rule->value;
            $std_rq->order->total_discounts_tax_excl -= $order_cart_rule->value_tax_excl;

            $std_rq->order->total_paid += $order_cart_rule->value;
            $std_rq->order->total_paid_tax_incl += $order_cart_rule->value;
            $std_rq->order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;

            $order_cart_rule->delete();
            $std_rq->order->update();

            self::setAjaxError($this->l('A discount has been successfully deleted'), '_discounts');
        } else {
            self::setAjaxError($this->l('Cannot edit this Order Cart Rule'), '_discounts');
        }

        $return = array(
            'tpls' => $this->updateTemplates(array('_discounts.tpl', '_totals.tpl'), $std_rq)
        );

        self::ajaxReturn($return);
    }


    /*
     * Get a thumbnail image for the product in order to display it in the list
     * of ordered products. This method is being called after a user clicks
     * "add product".
     *
     * @params array $product - a reference to product data array
     *
     * @return void
     */
    private static function setProductImage(array &$product)
    {
        if (isset($product['product_attribute_id']) && $product['product_attribute_id']) {
            $id_image = Db::getInstance()->getValue(
                'SELECT image_shop.id_image
                FROM '._DB_PREFIX_.'product_attribute_image pai'.Shop::addSqlAssociation('image', 'pai', true).'
                WHERE id_product_attribute = '.(int)$product['product_attribute_id']
            );
        }

        if (!isset($id_image) || !$id_image) {
            $id_image = Db::getInstance()->getValue(
                'SELECT image_shop.id_image
                FROM '._DB_PREFIX_.'image i'.
                Shop::addSqlAssociation('image', 'i', true, 'image_shop.cover=1').'
                WHERE i.id_product = '.(int)$product['product_id']
            );
        }

        $product['image'] = null;
        $product['image_size'] = null;

        if ($id_image) {
            $product['image'] = new Image($id_image);
        }
    }


    /*
     * Adds a product to an order. The added product is not saved in an order in
     * any way, products are only saved in executeSaveOrder method, this method
     * just collects information needed to display a new product in the list of
     * products in order.
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeAddProduct(stdClass $std_rq)
    {
        $product = array(
            'index' => (int)Tools::getValue('index'),
            'product_id' => (int)Tools::getValue('product_id'),
            'product_attribute_id' => (int)Tools::getValue('product_attribute_id'),
            'product_name' => Tools::getValue('product_name'),
            'product_reference' => '',
            'product_supplier_reference' => '',
            'product_price' => (float)Tools::getValue('unit_price_tax_incl'),
            'unit_price_tax_excl' => (float)Tools::getValue('unit_price_tax_excl'),
            'unit_price_tax_incl' => (float)Tools::getValue('unit_price_tax_incl'),
            'tax_rate' => (float)Tools::getValue('tax_rate'),
            'product_quantity' => (int)Tools::getValue('product_quantity'),
            'customizationQuantityTotal' => 0,
            'product_quantity_return' => 0,
            'product_quantity_refunded' => 0,
            'id_order_detail' => 0,
            'quantity_refundable' => (int)Tools::getValue('product_quantity'),
            'amount_refundable' => (float)Tools::getValue('unit_price_tax_incl') * (int)Tools::getValue('product_quantity'),
           // 'invoice_selected' => (int)Tools::getValue('product_invoice'),
            'id_warehouse' => (int)Tools::getValue('id_warehouse'),
            'ecotax' => 0,
            'product_weight' => 0,
            'reference' => '',
            'supplier_reference' => '',
            'reduction_percent' => 0,
            'customized_product_quantity' => 0
        );

        $other_product_details = Db::getInstance()->getRow(
            'SELECT
                p.`ecotax`,
                p.`reference` as `product_reference`,
                p.`supplier_reference` as `product_supplier_reference`,
                p.`id_tax_rules_group`,
                p.`weight` as `product_weight`,
                p.`reference`
            FROM `'._DB_PREFIX_.'product` p
            WHERE p.`id_product` = '.(int)$product['product_id']
        );

        // reading right sku
        $other_product_details['product_supplier_reference'] = Db::getInstance()->getValue('select product_supplier_reference from '.
                _DB_PREFIX_.'product_supplier where id_product='.$product['product_id'].' and id_product_attribute='.
                $product['product_attribute_id']);
        
        if ($other_product_details && count($other_product_details)) {
            if ((int)$product['product_attribute_id'] > 0) {
                $combination_details = Db::getInstance()->getRow(
                    'SELECT
                        `reference`,
                        `ecotax`,
                        `weight` as `product_weight`
                    FROM
                        `'._DB_PREFIX_.'product_attribute`
                    WHERE
                        `id_product` = '.(int)$product['product_id'].'
                    AND
                        `id_product_attribute` = '.(int)$product['product_attribute_id']
                );

                if ($combination_details && count($combination_details)) {
                    if (!Tools::isEmpty($combination_details['reference'])) {
                        $other_product_details['reference'] = $combination_details['reference'];
                    }

                    if (!Tools::isEmpty($combination_details['ecotax']) && (float)$combination_details['ecotax'] > 0) {
                        $other_product_details['ecotax'] = (float)$combination_details['ecotax'];
                    }

                    if (!Tools::isEmpty($combination_details['product_weight']) && (float)$combination_details['product_weight'] > 0) {
                        $other_product_details['product_weight'] = (float)$combination_details['product_weight'];
                    }
                }
            }

            $address = Address::initialize();
            $tax_manager = TaxManagerFactory::getManager($address, $other_product_details['id_tax_rules_group']);
            $tax_calculator = $tax_manager->getTaxCalculator();

            if (isset($tax_calculator->taxes[0]->id)) {
                $other_product_details['id_tax'] = $tax_calculator->taxes[0]->id;
            } else {
                $other_product_details['id_tax'] = 0;
            }

            $product = array_merge($product, $other_product_details);
        }

        self::setProductImage($product);

        if ($product['image'] != null) {
            $name = 'product_mini_'.$product['image']->id.(int)$product['product_id'].(isset($product['product_attribute_id']) ?
                    '_'.(int)$product['product_attribute_id'] : '').'.jpg';

            $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg');

            if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
            } else {
                $product['image_size'] = false;
            }
        }

        $std_rq->context->smarty->assign(array(
            'currency' => new Currency($std_rq->order->id_currency),
            'index' => $product['index'],
            'product' => $product,
            'order' => $std_rq->order,
            //'invoices_collection' => array(),
            'can_edit' => true,
            'unsaved' => true,
            'taxes' => Tax::getTaxes($std_rq->context->language->id, true)
        ));

        $tpllink = self::$module_tpl_path.($this->vt == 't17' ? 't17/' : '').'_product_line.tpl';
        $return = array('product_line' => $std_rq->context->smarty->fetch($tpllink));

        self::ajaxReturn($return);
    }


    /*
     * Calculates the wrapping price for an order
     *
     * @params object $std_rq
     *
     * @return void
     */
    public function executeWrappingCalculate($std_rq)
    {
        if (!Validate::isLoadedObject($std_rq->context->cart)) {
            self::setAjaxError($this->l('Unable to get the cart instance'), '_discounts');
        } else {
            $price = $std_rq->context->cart->getGiftWrappingPrice(false);
            $price_wt = $std_rq->context->cart->getGiftWrappingPrice(true);
            $tax = 0;

            if ($price_wt > 0) {
                $tax = (($price_wt - $price) / $price) * 100;
            }

            $return = array(
                'wrapping_price' => $price,
                'wrapping_price_wt' => $price_wt,
                'tax_rate' => $tax
            );

            self::ajaxReturn($return);
        }
    }


    /*
     * Get the data that will be necessary to manipulate the order, such as
     * an instance of an order itself, context, etc.
     *
     * @return object
     */
    public function getExecutionPrerequisites()
    {
        if (!$order = self::getOrderObj()) {
            return false;
        }

        $context = Context::getContext();

        $context->employee = new Employee((int)Tools::getValue('iem', false));
        $context->customer = new Customer((int)$order->id_customer);
        $context->cart = new Cart((int)$order->id_cart);
        $context->shop = new Shop((int)$context->cart->id_shop);

        if (!Validate::isLoadedObject($context->employee)) {
            return false;
        }

        $smarty_vars = $this->getDefaultOrderDataForSmarty($order, $context);

        $obj = new stdClass();

        $obj->order = $order;
        $obj->context = $context;
        $obj->smarty_vars = $smarty_vars;

        return $obj;
    }

    private static function checkOrderStatusAmount($id_order)
    {
        if (!Validate::isUnsignedId($id_order)) {
            return false;
        }

        return (int)Db::getInstance()->getValue(
            'SELECT
                COUNT(*)
            FROM
                `'._DB_PREFIX_.'order_history`
            WHERE
                `id_order` = '.(int)$id_order
        );
    }


    /*
     * A shorthand method to add an ajax error
     *
     * @param string $text - error text
     *
     * @param mixed $template - an optional template name, if provided, an error
     * will be displayed in that template, otherwise it will be displayed at the
     * top
     *
     * @return void
     */
    public static function setAjaxError($text, $template = false)
    {
        self::setAjaxMessage($text, 'error', $template);
    }


    /*
     * A shorthand method to add an ajax warning
     *
     * @param string $text - message text
     *
     * @param mixed $template - an optional template name, if provided, a
     * message will be displayed in that template, otherwise it will be
     * displayed at the top
     *
     * @return void
     */
    private static function setAjaxWarning($text, $template = false)
    {
        self::setAjaxMessage($text, 'warning', $template);
    }


    /*
     * A shorthand method to add an ajax success message
     *
     * @param string $text - message text
     *
     * @param mixed $template - an optional template name, if provided, a
     * message will be displayed in that template, otherwise it will be
     * displayed at the top
     *
     * @return void
     */
    private static function setAjaxSuccess($text, $template = false)
    {
        self::setAjaxMessage($text, 'success', $template);
    }


    /*
     * Adds data to messages array, from which we then form messages when
     * updating templates
     *
     * @param string $text - message text
     *
     * @param string $type - message type, either
     *                       "success", "warning" or "error"
     *
     * @param mixed $template - an optional template name, if provided, a
     * message will be displayed in that template, otherwise it will be
     * displayed at the top
     *
     * @return void
     */
    private static function setAjaxMessage($text, $type = 'error', $template = false)
    {
        if (Tools::strlen(trim($text)) > 0) {
            if (!$type || !array_key_exists($type, self::$module_ajax_messages)) {
                $type = 'error';
            }

            if ($template) {
                if (!array_key_exists($template, self::$module_ajax_messages[$type])) {
                    self::$module_ajax_messages[$type][$template] = array();
                }

                array_push(self::$module_ajax_messages[$type][$template], $text);
            } else {
                array_push(self::$module_ajax_messages[$type], $text);
            }
        }
    }


    /*
     * Clears messages from messages array.

     * @param string $type - message type, either
     *                       "success", "warning" or "error"
     *
     * @param mixed $template - an optional template name, if provided, only the
     *                          messages hooked to this template will be purged,
     *                          otherwise - all messages, regardless of template
     *                          they are hooked to
     *
     * @return void
     */
    private static function purgeAjaxMessages($type = 'error', $template = false)
    {
        if (!$type || !array_key_exists($type, self::$module_ajax_messages)) {
            $type = 'error';
        }

        if (!$template) {
            self::$module_ajax_messages[$type] = array();
        } elseif (array_key_exists($template, self::$module_ajax_messages[$type])) {
            unset(self::$module_ajax_messages[$type][$template]);
        }
    }


    /*
     * Check if a template has errors hooked to it
     *
     * @param mixed $template - template name to check
     *
     * @return bool
     */
    private static function checkIfErrorsExist($template = false)
    {
        if (!$template) {
            return count(self::$module_ajax_messages['error'] > 0);
        } else {
            return array_key_exists($template, self::$module_ajax_messages['error']);
        }
    }


    /*
     * Updates the templates displayed to the user when he is editing an order.
     * Basically, when a user does some action, like deleting an invoice,
     * the result he is getting (a success message, etc.) is formed by this
     * method.
     *
     * @param array $templates_array - an array of template names to update
     *
     * @params object $std_rq
     *
     * @return mixed
     */
    private function updateTemplates(array $templates_array, stdClass $std_rq)
    {
        if (!is_array($templates_array)) {
            return false;
        }

        // Refresh the data, otherwise we're always one action behind the actual state
        $smarty_data = $this->getDefaultOrderDataForSmarty($std_rq->order, $std_rq->context);

        $std_rq->context->smarty->assign($smarty_data);

        $result = array();

        if (!in_array('message_placeholders/_global_message.tpl', $templates_array)) {
            array_push($templates_array, 'message_placeholders/_global_message.tpl');
        }

        foreach ($templates_array as $template_name) {
            if ($fetched_tpl = $this->updateTemplate($template_name)) {
                array_push($result, $fetched_tpl);
            }
        }

        return count($result) ? $result : false;
    }


    /*
     * Updates a single template. Used in updateTemplates method, which is what
     * you should use, even if you need to only update one template.
     *
     * @param array $template_name - template name to update
     *
     * @return array
     */
    private function updateTemplate($template_name)
    {
        $context = Context::getContext();

        if (!file_exists(self::$module_tpl_path.$template_name)) {
            return false;
        }

        $wrapper_name = $template_name;

        if (strpos($wrapper_name, '/') !== false) {
            $wrapper_name = Tools::substr(strrchr($wrapper_name, '/'), 1);
        }

        $wrapper_name = Tools::substr($wrapper_name, 0, -4).'_wrapper';

        if (Tools::substr($wrapper_name, 0, 1) == '_') {
            $wrapper_name = Tools::substr($wrapper_name, 1);
        }

        $content = '';

        if (count(self::$module_ajax_messages['error'])
            || count(self::$module_ajax_messages['success'])
            || count(self::$module_ajax_messages['warning'])) {
            $message_tpl = Tools::substr($template_name, 0, -4);

            foreach (self::$module_ajax_messages as $message_type => $messages) {
                if (array_key_exists($message_tpl, $messages)) {
                    $content .= '<div class="orderedit_msg ';

                    switch ($message_type) {
                        case 'error':
                            $content .= 'alert alert-danger';
                            break;
                        case 'warning':
                            $content .= 'alert alert-warning';
                            break;
                        case 'success':
                            $content .= 'alert alert-success';
                            break;
                    }

                    $content .= '">
                        <ul>';

                    foreach ($messages[$message_tpl] as $template_message) {
                        $content .= '<li>'.$template_message.'</li>';
                    }

                    $content .= '
                        </ul>
                    </div>';
                }
            }
        }

        $content .= $context->smarty->fetch(self::$module_tpl_path.$template_name);

        return array(
            'template_wrapper' => $wrapper_name,
            'template_content' => $content
        );
    }

    private function updateDiscountForInvoice(OrderInvoice $order_invoice, $value_tax_incl, $value_tax_excl, $value_tax_incl_old, $value_tax_excl_old)
    {
        // Note that despite of it's name, the "isNegativePrice" method actually
        // checks positive price as well.
        if (!Validate::isNegativePrice($value_tax_incl) || !Validate::isNegativePrice($value_tax_excl)) {
            self::setAjaxError($this->l('Invalid value passed to "applyDiscountOnInvoice" method'), '_discounts');

            return false;
        }

        $order_invoice->total_discount_tax_incl -= $value_tax_incl_old;
        $order_invoice->total_discount_tax_excl -= $value_tax_excl_old;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl_old;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl_old;
        $order_invoice->total_discount_tax_incl += $value_tax_incl;
        $order_invoice->total_discount_tax_excl += $value_tax_excl;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl;
        $order_invoice->update();
    }


    /*
     * Applies a discount to an invoice. Note that an invoice discount and order
     * discount are not the same thing: an order discount is the sum of invoice
     * discounts.
     *
     * @param object $order_invoice - an instance or OrderInvoice class
     *
     * @param float $value_tax_incl
     *
     * @param float $value_tax_excl
     *
     * @return void
     */
    private function applyDiscountOnInvoice(OrderInvoice $order_invoice, $value_tax_incl, $value_tax_excl)
    {
        // Note that despite of it's name, the "isNegativePrice" method actually
        // checks positive price as well.
        if (!Validate::isNegativePrice($value_tax_incl) || !Validate::isNegativePrice($value_tax_excl)) {
            self::setAjaxError($this->l('Invalid value passed to "applyDiscountOnInvoice" method'), '_discounts');

            return false;
        }

        $order_invoice->total_discount_tax_incl += $value_tax_incl;
        $order_invoice->total_discount_tax_excl += $value_tax_excl;
        $order_invoice->total_paid_tax_incl -= $value_tax_incl;
        $order_invoice->total_paid_tax_excl -= $value_tax_excl;
        $order_invoice->update();
    }

    private static function ajaxReturn(array $result_array)
    {
        die(Tools::jsonEncode($result_array));
    }
}
