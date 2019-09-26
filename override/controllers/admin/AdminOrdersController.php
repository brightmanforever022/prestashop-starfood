<?php

class AdminOrdersController extends AdminOrdersControllerCore
{
    function __construct()
    {
        $this->bootstrap = true;
        $this->table = 'order';
        $this->className = 'Order';
        $this->lang = false;
        $this->addRowAction('view');
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->deleted = false;
        $this->context = Context::getContext();

        $this->_select = '
		a.id_currency, a.id_customer, a.current_state,
		a.id_order AS id_pdf,
		CONCAT(LEFT(c.`firstname`, 1), \'. \', c.`lastname`) AS `customer`,
		osl.`name` AS `osname`,
		os.`color`, ops.name as opsname, ops.color as opscolor,
		IF((SELECT so.id_order FROM `'._DB_PREFIX_.'orders` so WHERE so.id_customer = a.id_customer AND so.id_order < a.id_order LIMIT 1) > 0, 0, 1) as new,
		country_lang.name as cname,
		IF(a.valid, 1, 0) badge_success, a.current_state<>'.Configuration::get('PS_OS_CANCELED').' and (select count(od.id_order_detail)>0 from '._DB_PREFIX_.'order_detail od inner join '.
                _DB_PREFIX_.'stock_available sa on od.product_id=sa.id_product and sa.id_product_attribute=od.product_attribute_id'
                . ' where od.shipped=0 and (od.product_quantity-od.product_quantity_refunded-od.product_quantity_return)>0 and sa.quantity>=0 and od.id_order=a.id_order) as may_be_shipped';

        $this->_join = '
		LEFT JOIN `'._DB_PREFIX_.'customer` c ON (c.`id_customer` = a.`id_customer`)
		INNER JOIN `'._DB_PREFIX_.'address` address ON address.id_address = a.id_address_delivery
		INNER JOIN `'._DB_PREFIX_.'country` country ON address.id_country = country.id_country
		INNER JOIN `'._DB_PREFIX_.'country_lang` country_lang ON (country.`id_country` = country_lang.`id_country` AND country_lang.`id_lang` = '.(int)$this->context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'order_state` os ON (os.`id_order_state` = a.`current_state`)
                LEFT JOIN `'._DB_PREFIX_.'order_payment_status` ops ON (ops.`id_order_payment_status` = a.`payment_status_id`)
		LEFT JOIN `'._DB_PREFIX_.'order_state_lang` osl ON (os.`id_order_state` = osl.`id_order_state` AND osl.`id_lang` = '.(int)$this->context->language->id.')';
        $this->_orderBy = 'id_order';
        $this->_orderWay = 'DESC';
        $this->_use_found_rows = true;

        $statuses = OrderState::getOrderStates((int)$this->context->language->id);
        foreach ($statuses as $status) {
            $this->statuses_array[$status['id_order_state']] = $status['name'];
        }
        
        $statuses = OrderPaymentStatus::getStatuses();
        $paumentStatuses = [];
        foreach ($statuses as $status) 
        {
            $paymentStatuses[$status['id_order_payment_status']] = $status['name'];
        }

        $this->fields_list = array(
            'id_order' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'reference' => array(
                'title' => $this->l('Reference')
            ),
            'may_be_shipped' => array(
                'title' => $this->l('May be shipped'),
                'align' => 'text-center',
                'callback' => 'showMayBeShipped',
                'type' => 'bool',
                'orderby' => false,
                //'search' => false,
                'remove_onclick' => true,
                'havingFilter' => true
            ),
            'new' => array(
                'title' => $this->l('New client'),
                'align' => 'text-center',
                'type' => 'bool',
                'tmpTableFilter' => true,
                'orderby' => false,
                'callback' => 'printNewCustomer'
            ),
            'customer' => array(
                'title' => $this->l('Customer'),
                'havingFilter' => true,
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->l('Company'),
                    'filter_key' => 'c!company'
                ),
            ));
        }

        $this->fields_list = array_merge($this->fields_list, array(
            'total_paid_tax_incl' => array(
                'title' => $this->l('Total'),
                'align' => 'text-right',
                'type' => 'price',
                'currency' => true,
                'callback' => 'setOrderCurrency',
                'badge_success' => true
            ),
            'payment' => array(
                'title' => $this->l('Payment')
            ),
            'osname' => array(
                'title' => $this->l('Status'),
                'type' => 'select',
                'color' => 'color',
                'list' => $this->statuses_array,
                'filter_key' => 'os!id_order_state',
                'filter_type' => 'int',
                'order_key' => 'osname'
            ),
            'opsname' => array(
                'title' => $this->l('Payment status'),
                'type' => 'select',
                'color' => 'opscolor',
                'list' => $paymentStatuses,
                'filter_key' => 'ops!id_order_payment_status',
                'filter_type' => 'int',
                'order_key' => 'opsname'
            ),
            'date_add' => array(
                'title' => $this->l('Date'),
                'align' => 'text-right',
                'type' => 'datetime',
                'filter_key' => 'a!date_add'
            ),
        ));

        if (Country::isCurrentlyUsed('country', true)) {
            $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
			SELECT DISTINCT c.id_country, cl.`name`
			FROM `'._DB_PREFIX_.'orders` o
			'.Shop::addSqlAssociation('orders', 'o').'
			INNER JOIN `'._DB_PREFIX_.'address` a ON a.id_address = o.id_address_delivery
			INNER JOIN `'._DB_PREFIX_.'country` c ON a.id_country = c.id_country
			INNER JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$this->context->language->id.')
			ORDER BY cl.name ASC');

            $country_array = array();
            foreach ($result as $row) {
                $country_array[$row['id_country']] = $row['name'];
            }

            $part1 = array_slice($this->fields_list, 0, 3);
            $part2 = array_slice($this->fields_list, 3);
            $part1['cname'] = array(
                'title' => $this->l('Delivery'),
                'type' => 'select',
                'list' => $country_array,
                'filter_key' => 'country!id_country',
                'filter_type' => 'int',
                'order_key' => 'cname'
            );
            $this->fields_list = array_merge($part1, $part2);
        }

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_ORDER;

        if (Tools::isSubmit('id_order')) {
            // Save context (in order to apply cart rule)
            $order = new Order((int)Tools::getValue('id_order'));
            $this->context->cart = new Cart($order->id_cart);
            $this->context->customer = new Customer($order->id_customer);
        }

        // add bulk actions
        $this->bulk_actions = array(
            'updateOrderStatus' => array('text' => $this->l('Change Order Status'), 'icon' => 'icon-refresh')
        );

        AdminController::__construct();
        
        $this->bulk_actions['orderdedProductsReport'] = array('text' => $this->l('Generate ordered products list'), 'icon' => 'icon-road', 
            'targetBlank'=>1);
        $this->bulk_actions['htmlShippingInfo'] = array('text' => $this->l('Export pdf shipping info'), 'icon' => 'icon-road');
        
        $this->fields_list['reference']['filter_key'] = 'a!reference';
        $this->fields_list['total_paid_tax_incl']['filter_key'] = 'a!total_paid_tax_incl';
        //$this->_select = ' distinct ';
        $this->_group = ' group by a.id_order';
    }
    
    
    
    public function renderForm()
    {
        $rootCat = Category::getRootCategory();
        $tree = new HelperTreeCategories('categories-tree', 'Categories');
        $tree
            ->setAttribute('is_category_filter', true)
            ->setInputName('id-category')
            ->setRootCategory($rootCat->id)
            ->setUseSearch(false)
            ->setNoJS(true)
            ->setFullTree(true)
        ;
        
        $searchLetters = array();
        for( $c = ord('A'); $c < ord('Z'); $c++ ){
            $searchLetters[] = chr($c);
        }
        
        $this->context->smarty->assign(array(
            'is_category_filter' => true,
            'category_tree' => $tree->render(),
            'searchbar_letters' => $searchLetters
        ));
        
        
        
        parent::renderForm();
    }
    
    
    
    public function renderView()
    {
        $order = new Order(Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            $this->errors[] = Tools::displayError('The order cannot be found within your database.');
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
        $addressInvoice = new Address($order->id_address_invoice, $this->context->language->id);
        if (Validate::isLoadedObject($addressInvoice) && $addressInvoice->id_state) {
            $invoiceState = new State((int)$addressInvoice->id_state);
        }

        if ($order->id_address_invoice == $order->id_address_delivery) {
            $addressDelivery = $addressInvoice;
            if (isset($invoiceState)) {
                $deliveryState = $invoiceState;
            }
        } else {
            $addressDelivery = new Address($order->id_address_delivery, $this->context->language->id);
            if (Validate::isLoadedObject($addressDelivery) && $addressDelivery->id_state) {
                $deliveryState = new State((int)($addressDelivery->id_state));
            }
        }

        $this->toolbar_title = sprintf($this->l('Order #%1$d (%2$s) - %3$s %4$s'), $order->id, $order->reference, $customer->firstname, $customer->lastname);
        if (Shop::isFeatureActive()) {
            $shop = new Shop((int)$order->id_shop);
            $this->toolbar_title .= ' - '.sprintf($this->l('Shop: %s'), $shop->name);
        }

        // gets warehouses to ship products, if and only if advanced stock management is activated
        $warehouse_list = null;

        $order_details = $order->getOrderDetailList();
        foreach ($order_details as $order_detail) {
            $product = new Product($order_detail['product_id']);

            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT')
                && $product->advanced_stock_management) {
                $warehouses = Warehouse::getWarehousesByProductId($order_detail['product_id'], $order_detail['product_attribute_id']);
                foreach ($warehouses as $warehouse) {
                    if (!isset($warehouse_list[$warehouse['id_warehouse']])) {
                        $warehouse_list[$warehouse['id_warehouse']] = $warehouse;
                    }
                }
            }
        }

        $payment_methods = array();
        foreach (PaymentModule::getInstalledPaymentModules() as $payment) {
            $module = Module::getInstanceByName($payment['name']);
            if (Validate::isLoadedObject($module) && $module->active) {
                $payment_methods[] = $module->displayName;
            }
        }

        // display warning if there are products out of stock
        $display_out_of_stock_warning = false;
        $current_order_state = $order->getCurrentOrderState();
        if (Configuration::get('PS_STOCK_MANAGEMENT') && (!Validate::isLoadedObject($current_order_state) || ($current_order_state->delivery != 1 && $current_order_state->shipped != 1))) {
            $display_out_of_stock_warning = true;
        }

        // products current stock (from stock_available)
        foreach ($products as &$product) {
            // Get total customized quantity for current product
            $customized_product_quantity = 0;

            if (is_array($product['customizedDatas'])) {
                foreach ($product['customizedDatas'] as $customizationPerAddress) {
                    foreach ($customizationPerAddress as $customizationId => $customization) {
                        $customized_product_quantity += (int)$customization['quantity'];
                    }
                }
            }

            $product['customized_product_quantity'] = $customized_product_quantity;
            $product['current_stock'] = StockAvailable::getQuantityAvailableByProduct($product['product_id'], $product['product_attribute_id'], $product['id_shop']);
            $resume = OrderSlip::getProductSlipResume($product['id_order_detail']);
            $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
            $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
            $product['amount_refundable_tax_incl'] = $product['total_price_tax_incl'] - $resume['amount_tax_incl'];
            $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl'], $currency);
            $product['refund_history'] = OrderSlip::getProductSlipDetail($product['id_order_detail']);
            $product['return_history'] = OrderReturn::getProductReturnDetail($product['id_order_detail']);

            // if the current stock requires a warning
            if ($product['current_stock'] == 0 && $display_out_of_stock_warning) {
                $this->displayWarning($this->l('This product is out of stock: ').' '.$product['product_name']);
            }
            if ($product['id_warehouse'] != 0) {
                $warehouse = new Warehouse((int)$product['id_warehouse']);
                $product['warehouse_name'] = $warehouse->name;
                $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
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

        $gender = new Gender((int)$customer->id_gender, $this->context->language->id);

        $history = $order->getHistory($this->context->language->id);

        foreach ($history as &$order_state) {
            $order_state['text-color'] = Tools::getBrightness($order_state['color']) < 128 ? 'white' : 'black';
        }

        // Smarty assign
        require_once _PS_MODULE_DIR_.'ba_prestashop_invoice/includes/BaTemplateCategory.php';
        
        
        $rootCat = Category::getRootCategory();
        $tree = new HelperTreeCategories('categories-tree', 'Categories');
        $tree
            ->setAttribute('is_category_filter', true)
            ->setInputName('id-category')
            ->setRootCategory($rootCat->id)
            ->setUseSearch(false)
            ->setNoJS(true)
            ->setFullTree(true)
        ;
        
        $this->tpl_view_vars = array(
            'invoiceTemplates' => BaTemplateCategory::getTemplatesGroupedByCategory(),
            'order' => $order,
            'cart' => new Cart($order->id_cart),
            'customer' => $customer,
            'gender' => $gender,
            'customer_addresses' => $customer->getAddresses($this->context->language->id),
            'addresses' => array(
                'delivery' => $addressDelivery,
                'deliveryState' => isset($deliveryState) ? $deliveryState : null,
                'invoice' => $addressInvoice,
                'invoiceState' => isset($invoiceState) ? $invoiceState : null
            ),
            'customerStats' => $customer->getStats(),
            'products' => $products,
            'discounts' => $order->getCartRules(),
            'orders_total_paid_tax_incl' => $order->getOrdersTotalPaid(), // Get the sum of total_paid_tax_incl of the order with similar reference
            'total_paid' => $order->getTotalPaid(),
            'returns' => OrderReturn::getOrdersReturn($order->id_customer, $order->id),
            'customer_thread_message' => CustomerThread::getCustomerMessages($order->id_customer, null, $order->id),
            'orderMessages' => OrderMessage::getOrderMessages($order->id_lang),
            'messages' => Message::getMessagesByOrderId($order->id, true),
            'carrier' => new Carrier($order->id_carrier),
            'history' => $history,
            'states' => OrderState::getOrderStates($this->context->language->id),
            'paymentStatuses' => OrderPaymentStatus::getStatuses(),
            'warehouse_list' => $warehouse_list,
            'sources' => ConnectionsSource::getOrderSources($order->id),
            'currentState' => $order->getCurrentOrderState(),
            'currency' => new Currency($order->id_currency),
            'currencies' => Currency::getCurrenciesByIdShop($order->id_shop),
            'previousOrder' => $order->getPreviousOrderId(),
            'nextOrder' => $order->getNextOrderId(),
            'current_index' => self::$currentIndex,
            'carrierModuleCall' => $carrier_module_call,
            'iso_code_lang' => $this->context->language->iso_code,
            'id_lang' => $this->context->language->id,
            'can_edit' => ($this->tabAccess['edit'] == 1),
            'current_id_lang' => $this->context->language->id,
            'invoices_collection' => $order->getInvoicesCollection(),
            'not_paid_invoices_collection' => $order->getNotPaidInvoicesCollection(),
            'payment_methods' => $payment_methods,
            'invoice_management_active' => Configuration::get('PS_INVOICE', null, null, $order->id_shop),
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'HOOK_CONTENT_ORDER' => Hook::exec('displayAdminOrderContentOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_CONTENT_SHIP' => Hook::exec('displayAdminOrderContentShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_ORDER' => Hook::exec('displayAdminOrderTabOrder', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'HOOK_TAB_SHIP' => Hook::exec('displayAdminOrderTabShip', array(
                'order' => $order,
                'products' => $products,
                'customer' => $customer)
            ),
            'is_category_filter' => true,
            'category_tree' => $tree->render(),
            
        );

        return AdminController::renderView();
    }
    
    
    function postProcess()
    {
        // process additional actions
        if (isset($_REQUEST['submitResetorder']))
        {
            unset($_REQUEST['orders_search']);
        }
        
        // If id_order is sent, we instanciate a new Order object
        if (Tools::isSubmit('id_order') && Tools::getValue('id_order') > 0) {
            $order = new Order(Tools::getValue('id_order'));
            if (!Validate::isLoadedObject($order)) {
                $this->errors[] = Tools::displayError('The order cannot be found within your database.');
            }
            ShopUrl::cacheMainDomainForShop((int)$order->id_shop);
        }
        
        if (Tools::isSubmit('submitBulkorderdedProductsReportorder'))
        {
            $orderIds = Tools::getValue('orderBox', []);
            if (count($orderIds))
            {
                array_walk($orderIds, function(&$item, $key){ $item = intval($item); });
                $products = Db::getInstance()->executeS('select id_image, sum(product_quantity) as quantity, product_name, '
                        . 'product_supplier_reference from ' . _DB_PREFIX_ . 'order_detail od left join ' . _DB_PREFIX_ . 'image_shop i on '
                        . 'od.product_id=i.id_product and od.id_shop=i.id_shop and cover=1 where id_order in (' . implode(',', $orderIds) .
                        ') group by product_id, product_attribute_id order by product_supplier_reference');
                $link = $this->context->link;
                array_walk($products, function(&$item, $key) use($link)
                {
                    $item['imageLink'] = $link->getImageLink($item['product_name'], $item['id_image'], 'cart_default');
                    $item['size'] = trim(substr($item['product_name'], strrpos($item['product_name'], ':')+1));
                });
            }
            else
            {
                $products = [];
            }
            $this->context->smarty->assign('products', $products);
            echo $this->createTemplate('supplier_order.tpl')->fetch();
            exit;
        }
        elseif (Tools::isSubmit('submitBulkhtmlShippingInfoorder'))
        {
            $orderIds = Tools::getValue('orderBox', []);
            if (count($orderIds))
            {
                array_walk($orderIds, function(&$item, $key){ $item = intval($item); });
                ShippingExporter::exportPdfInfo($orderIds);
            }
            else
            {
                echo Tools::displayError('No orders selected');
            }
            exit;
        }
        elseif (Tools::isSubmit('submitAddInvoice'))
        {
            // prepare values
            $productIds = explode(',', $_REQUEST['product_ids']);
            array_walk($productIds, function (&$value){ $value = intval($value); });
            $templateId = $_REQUEST['template_type']==1?intval($_REQUEST['invoice_template_id']):intval($_REQUEST['delivery_template_id']);
            $order->addInvoice(intval($_REQUEST['template_type'])==2, $templateId, $productIds);
            Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token.'#documents');
        }
        elseif (Tools::isSubmit('submitSetInvoiceText'))
        {
            $order->invoice_txt = trim(Tools::getValue('invoice_txt'));
            $order->save();
            Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=4&token='.$this->token.'#documents');
        }
        elseif(Tools::isSubmit('submitDeleteInvoice'))
        {
            $orderInvoice = new OrderInvoice(intval($_REQUEST['order_invoice_id']));
            $orderInvoice->delete();
            Tools::redirectAdmin(self::$currentIndex.'&id_order='.$_REQUEST['id_order'].'&vieworder&conf=4&token='.$this->token.'#documents');
        }
        elseif(Tools::isSubmit('submitDeleteSlip'))
        {
            $orderSlip = new OrderSlip(intval($_REQUEST['order_slip_id']));
            $orderSlip->delete();
            Tools::redirectAdmin(self::$currentIndex.'&id_order='.$_REQUEST['id_order'].'&vieworder&conf=4&token='.$this->token.'#documents');
        }
        elseif (Tools::isSubmit('submitDeleteVoucher') && isset($order))
        {
            if ($this->tabAccess['edit'] === '1')
            {
                $order_cart_rule = new OrderCartRule(Tools::getValue('id_order_cart_rule'));
                if (Validate::isLoadedObject($order_cart_rule) && $order_cart_rule->id_order == $order->id)
                {
                    if ($order_cart_rule->id_order_invoice)
                    {
                        $order_invoice = new OrderInvoice($order_cart_rule->id_order_invoice);
                        if (Validate::isLoadedObject($order_invoice))
                        {
                            // due we added delete of order invoices, order invoice may not exist, so we don't generate exception
                            // Update amounts of Order Invoice
                            $order_invoice->total_discount_tax_excl -= $order_cart_rule->value_tax_excl;
                            $order_invoice->total_discount_tax_incl -= $order_cart_rule->value;

                            $order_invoice->total_paid_tax_excl += $order_cart_rule->value_tax_excl;
                            $order_invoice->total_paid_tax_incl += $order_cart_rule->value;

                            // Update Order Invoice
                            $order_invoice->update();
                        }
                    }

                    // Update amounts of order
                    $order->total_discounts -= $order_cart_rule->value;
                    $order->total_discounts_tax_incl -= $order_cart_rule->value;
                    $order->total_discounts_tax_excl -= $order_cart_rule->value_tax_excl;

                    $order->total_paid += $order_cart_rule->value;
                    $order->total_paid_tax_incl += $order_cart_rule->value;
                    $order->total_paid_tax_excl += $order_cart_rule->value_tax_excl;

                    // Delete Order Cart Rule and update Order
                    $order_cart_rule->delete();
                    $order->update();
                    Tools::redirectAdmin(self::$currentIndex . '&id_order=' . $order->id . '&vieworder&conf=4&token=' . $this->token);
                }
                else
                {
                    $this->errors[] = Tools::displayError('You cannot edit this cart rule.');
                }
            }
            else
            {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
            AdminController::postProcess();
            return;
        }
        elseif(Tools::isSubmit('submitPaymentStatus') && isset($order))
        {
            if ($this->tabAccess['edit'] === '1') 
            {
                $order->payment_status_id = Tools::getValue('id_order_payment_status');
                if ($order->update())
                {
                    Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&token='.$this->token);
                }
                $this->errors[] = Tools::displayError('An error occurred while changing order payment status.');
            }
            else 
            {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            }
        }
        /* Cancel product from order */
        elseif (Tools::isSubmit('cancelProduct') && isset($order))
        {
            if ($this->tabAccess['delete'] === '1')
            {
                if (!Tools::isSubmit('id_order_detail') && !Tools::isSubmit('id_customization'))
                {
                    $this->errors[] = Tools::displayError('You must select a product.');
                }
                elseif (!Tools::isSubmit('cancelQuantity') && !Tools::isSubmit('cancelCustomizationQuantity'))
                {
                    $this->errors[] = Tools::displayError('You must enter a quantity.');
                }
                else
                {
                    $productList = Tools::getValue('id_order_detail');
                    if ($productList)
                    {
                        $productList = array_map('intval', $productList);
                    }

                    $customizationList = Tools::getValue('id_customization');
                    if ($customizationList)
                    {
                        $customizationList = array_map('intval', $customizationList);
                    }

                    $qtyList = Tools::getValue('cancelQuantity');
                    if ($qtyList)
                    {
                        $qtyList = array_map('intval', $qtyList);
                    }

                    $customizationQtyList = Tools::getValue('cancelCustomizationQuantity');
                    if ($customizationQtyList)
                    {
                        $customizationQtyList = array_map('intval', $customizationQtyList);
                    }

                    $full_product_list = $productList;
                    $full_quantity_list = $qtyList;

                    if ($customizationList)
                    {
                        foreach ($customizationList as $key => $id_order_detail)
                        {
                            $full_product_list[(int) $id_order_detail] = $id_order_detail;
                            if (isset($customizationQtyList[$key]))
                            {
                                $full_quantity_list[(int) $id_order_detail] += $customizationQtyList[$key];
                            }
                        }
                    }

                    if ($productList || $customizationList)
                    {
                        if ($productList)
                        {
                            $id_cart = Cart::getCartIdByOrderId($order->id);
                            $customization_quantities = Customization::countQuantityByCart($id_cart);

                            foreach ($productList as $key => $id_order_detail)
                            {
                                $qtyCancelProduct = abs($qtyList[$key]);
                                if (!$qtyCancelProduct)
                                {
                                    $this->errors[] = Tools::displayError('No quantity has been selected for this product.');
                                }

                                $order_detail = new OrderDetail($id_order_detail);
                                $customization_quantity = 0;
                                if (array_key_exists($order_detail->product_id, $customization_quantities) && array_key_exists($order_detail->product_attribute_id, $customization_quantities[$order_detail->product_id]))
                                {
                                    $customization_quantity = (int) $customization_quantities[$order_detail->product_id][$order_detail->product_attribute_id];
                                }

                                if (($order_detail->product_quantity - $customization_quantity - $order_detail->product_quantity_refunded - $order_detail->product_quantity_return) < $qtyCancelProduct)
                                {
                                    $this->errors[] = Tools::displayError('An invalid quantity was selected for this product.');
                                }
                            }
                        }
                        if ($customizationList)
                        {
                            $customization_quantities = Customization::retrieveQuantitiesFromIds(array_keys($customizationList));

                            foreach ($customizationList as $id_customization => $id_order_detail)
                            {
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                $customization_quantity = $customization_quantities[$id_customization];

                                if (!$qtyCancelProduct)
                                {
                                    $this->errors[] = Tools::displayError('No quantity has been selected for this product.');
                                }

                                if ($qtyCancelProduct > ($customization_quantity['quantity'] - ($customization_quantity['quantity_refunded'] + $customization_quantity['quantity_returned'])))
                                {
                                    $this->errors[] = Tools::displayError('An invalid quantity was selected for this product.');
                                }
                            }
                        }

                        if (!count($this->errors) && $productList)
                        {
                            $ignoreVoucher = false;
                            if ((int) Tools::getValue('refund_total_voucher_off') == 0)
                            {
                                $ignoreVoucher = true;
                            }
                            // Generate credit slip
                            if (Tools::isSubmit('generateCreditSlip') && !count($this->errors))
                            {
                                $product_list = array();
                                $amount = $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail];

                                $totalReturnAmount = 0;
                                $choosen = false;
                                if ((int) Tools::getValue('refund_total_voucher_off') == 2)
                                {
                                    $choosen = true;
                                    $totalReturnAmount = (float) Tools::getValue('refund_total_voucher_choose');
                                    $amount = $totalReturnAmount/count($productList);
                                }
                                foreach ($productList as $key => $id_order_detail)
                                {
                                    $order_detail = new OrderDetail((int) $id_order_detail);
                                    if ((int) Tools::getValue('refund_total_voucher_off') == 1)
                                    {
                                        $voucherPartTe = $order->total_discounts_tax_excl*$order_detail->unit_price_tax_excl*$qtyList[$key]
                                                /$order->total_paid_tax_excl;
                                        $voucherPartTi = $order->total_discounts_tax_incl*$order_detail->unit_price_tax_incl*$qtyList[$key]
                                                /$order->total_paid_tax_incl;
                                        $amount = Tools::ps_round($order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail] - $voucherPartTi, 2);
                                        $unitPrice = $order_detail->unit_price_tax_excl - $order->total_discounts_tax_excl*$order_detail->unit_price_tax_excl/
                                                $order->total_products;
                                        $returnVoucherTotal += $voucherPartTe;
                                    }
                                    elseif(!$choosen)
                                    {
                                        $amount = $order_detail->unit_price_tax_incl * $full_quantity_list[$id_order_detail];
                                    }
                                    
                                    $product_list[$id_order_detail] = array(
                                        'id_order_detail' => $id_order_detail,
                                        'quantity' => $qtyList[$key],
                                        'unit_price' => isset($unitPrice)? $unitPrice : $order_detail->unit_price_tax_excl,
                                        'amount' => $amount,
                                    );
                                    if(!$choosen)
                                    {
                                        $totalReturnAmount += $amount;
                                    }
                                }
                                
                                $shipping = Tools::isSubmit('shippingBack') ? null : false;

                                if (!OrderSlip::create($order, $product_list, $shipping, $totalReturnAmount, $choosen))
                                {
                                    $this->errors[] = Tools::displayError('A credit slip cannot be generated. ');
                                }
                                else
                                {
                                    Hook::exec('actionOrderSlipAdd', array('order' => $order, 'productList' => $full_product_list, 'qtyList' => $full_quantity_list), null, false, true, false, $order->id_shop);
                                }
                            }
                            foreach ($productList as $key => $id_order_detail)
                            {
                                $qty_cancel_product = abs($qtyList[$key]);
                                $order_detail = new OrderDetail((int) ($id_order_detail));

                                if (!$order->hasBeenDelivered() || ($order->hasBeenDelivered() && Tools::isSubmit('reinjectQuantities')) && $qty_cancel_product > 0)
                                {
                                    $this->reinjectQuantity($order_detail, $qty_cancel_product);
                                }

                                // Delete product
                                $order_detail = new OrderDetail((int) $id_order_detail);
                                if (!$order->deleteProduct($order, $order_detail, $qty_cancel_product, $ignoreVoucher))
                                {
                                    $this->errors[] = Tools::displayError('An error occurred while attempting to delete the product.') . ' <span class="bold">' . $order_detail->product_name . '</span>';
                                }

                                // Update weight SUM
                                $order_carrier = new OrderCarrier((int) $order->getIdOrderCarrier());
                                if (Validate::isLoadedObject($order_carrier))
                                {
                                    $order_carrier->weight = (float) $order->getTotalWeight();
                                    if ($order_carrier->update())
                                    {
                                        $order->weight = sprintf("%.3f " . Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
                                    }
                                }

                                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && StockAvailable::dependsOnStock($order_detail->product_id))
                                {
                                    StockAvailable::synchronize($order_detail->product_id);
                                }
                                Hook::exec('actionProductCancel', array('cancelQty'=>$qty_cancel_product, 'sku'=>$order_detail->product_supplier_reference,
                                    'order' => $order, 'id_order_detail' => (int) $id_order_detail), null, false, true, false, $order->id_shop);
                            }
                        }
                        if (!count($this->errors) && $customizationList)
                        {
                            foreach ($customizationList as $id_customization => $id_order_detail)
                            {
                                $order_detail = new OrderDetail((int) ($id_order_detail));
                                $qtyCancelProduct = abs($customizationQtyList[$id_customization]);
                                if (!$order->deleteCustomization($id_customization, $qtyCancelProduct, $order_detail))
                                {
                                    $this->errors[] = Tools::displayError('An error occurred while attempting to delete product customization.') . ' ' . $id_customization;
                                }
                            }
                        }
                        // E-mail params
                        if ((Tools::isSubmit('generateCreditSlip') || Tools::isSubmit('generateDiscount')) && !count($this->errors))
                        {
                            $customer = new Customer((int) ($order->id_customer));
                            $params['{lastname}'] = $customer->lastname;
                            $params['{firstname}'] = $customer->firstname;
                            $params['{id_order}'] = $order->id;
                            $params['{order_name}'] = $order->getUniqReference();
                            @Mail::Send((int) $order->id_lang, 'credit_slip', Mail::l('New credit slip regarding your order', 
                                    (int) $order->id_lang), $params, $customer->email, $customer->firstname . ' ' . $customer->lastname, 
                                    null, null, null, null, _PS_MAIL_DIR_, true, (int) $order->id_shop);
                        }

                        // Generate voucher
                        if (Tools::isSubmit('generateDiscount') && !count($this->errors))
                        {
                            $cartrule = new CartRule();
                            $language_ids = Language::getIDs((bool) $order);
                            $cartrule->description = sprintf($this->l('Credit card slip for order #%d'), $order->id);
                            foreach ($language_ids as $id_lang)
                            {
                                // Define a temporary name
                                $cartrule->name[$id_lang] = 'V0C' . (int) ($order->id_customer) . 'O' . (int) ($order->id);
                            }
                            // Define a temporary code
                            $cartrule->code = 'V0C' . (int) ($order->id_customer) . 'O' . (int) ($order->id);

                            $cartrule->quantity = 1;
                            $cartrule->quantity_per_user = 1;
                            // Specific to the customer
                            $cartrule->id_customer = $order->id_customer;
                            $now = time();
                            $cartrule->date_from = date('Y-m-d H:i:s', $now);
                            $cartrule->date_to = date('Y-m-d H:i:s', $now + (3600 * 24 * 365.25)); // 1 year 
                            $cartrule->active = 1;

                            $products = $order->getProducts(false, $full_product_list, $full_quantity_list);

                            $total = 0;
                            foreach ($products as $product)
                            {
                                $total += $product['unit_price_tax_incl'] * $product['product_quantity'];
                            }

                            if (Tools::isSubmit('shippingBack'))
                            {
                                $total += $order->total_shipping;
                            }

                            if ((int) Tools::getValue('refund_total_voucher_off') == 1)
                            {
                                $total -= (float) Tools::getValue('order_discount_price');
                            }
                            elseif ((int) Tools::getValue('refund_total_voucher_off') == 2)
                            {
                                $total = (float) Tools::getValue('refund_total_voucher_choose');
                            }

                            $cartrule->reduction_amount = $total;
                            $cartrule->reduction_tax = true;
                            $cartrule->minimum_amount_currency = $order->id_currency;
                            $cartrule->reduction_currency = $order->id_currency;

                            if (!$cartrule->add())
                            {
                                $this->errors[] = Tools::displayError('You cannot generate a voucher.');
                            }
                            else
                            {
                                // Update the voucher code and name
                                foreach ($language_ids as $id_lang)
                                {
                                    $cartrule->name[$id_lang] = 'V' . (int) ($cartrule->id) . 'C' . (int) ($order->id_customer) . 'O' . $order->id;
                                }
                                $cartrule->code = 'V' . (int) ($cartrule->id) . 'C' . (int) ($order->id_customer) . 'O' . $order->id;
                                if (!$cartrule->update())
                                {
                                    $this->errors[] = Tools::displayError('You cannot generate a voucher.');
                                }
                                else
                                {
                                    $currency = $this->context->currency;
                                    $params['{voucher_amount}'] = Tools::displayPrice($cartrule->reduction_amount, $currency, false);
                                    $params['{voucher_num}'] = $cartrule->code;
                                    @Mail::Send((int) $order->id_lang, 'voucher', sprintf(Mail::l('New voucher for your order #%s', (int) $order->id_lang), $order->reference), $params, $customer->email, $customer->firstname . ' ' . $customer->lastname, null, null, null, null, _PS_MAIL_DIR_, true, (int) $order->id_shop);
                                }
                            }
                        }
                    } else {
                        $this->errors[] = Tools::displayError('No product or quantity has been selected.');
                    }

                    // Redirect if no errors
                    if (!count($this->errors)) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=31&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
            return AdminController::postProcess();
        }
        /* Add a new message for the current order and send an e-mail to the customer if needed */
        elseif (Tools::isSubmit('submitMessage') && isset($order)) {
            if ($this->tabAccess['edit'] === '1') {
                $customer = new Customer(Tools::getValue('id_customer'));
                if (!Validate::isLoadedObject($customer)) {
                    $this->errors[] = Tools::displayError('The customer is invalid.');
                } elseif (!Tools::getValue('message')) {
                    $this->errors[] = Tools::displayError('The message cannot be blank.');
                } else {
                    /* Get message rules and and check fields validity */
                    $rules = call_user_func(array('Message', 'getValidationRules'), 'Message');
                    foreach ($rules['required'] as $field) {
                        if (($value = Tools::getValue($field)) == false && (string)$value != '0') {
                            if (!Tools::getValue('id_'.$this->table) || $field != 'passwd') {
                                $this->errors[] = sprintf(Tools::displayError('field %s is required.'), $field);
                            }
                        }
                    }
                    foreach ($rules['size'] as $field => $maxLength) {
                        if (Tools::getValue($field) && Tools::strlen(Tools::getValue($field)) > $maxLength) {
                            $this->errors[] = sprintf(Tools::displayError('field %1$s is too long (%2$d chars max).'), $field, $maxLength);
                        }
                    }
                    foreach ($rules['validate'] as $field => $function) {
                        if (Tools::getValue($field)) {
                            if (!Validate::$function(htmlentities(Tools::getValue($field), ENT_COMPAT, 'UTF-8'))) {
                                $this->errors[] = sprintf(Tools::displayError('field %s is invalid.'), $field);
                            }
                        }
                    }

                    if (!count($this->errors)) {
                        //check if a thread already exist
                        $id_customer_thread = CustomerThread::getIdCustomerThreadByEmailAndIdOrder($customer->email, $order->id);
                        if (!$id_customer_thread) {
                            $customer_thread = new CustomerThread();
                            $customer_thread->id_contact = 0;
                            $customer_thread->id_customer = (int)$order->id_customer;
                            $customer_thread->id_shop = (int)$this->context->shop->id;
                            $customer_thread->id_order = (int)$order->id;
                            $customer_thread->id_lang = (int)$this->context->language->id;
                            $customer_thread->email = $customer->email;
                            $customer_thread->status = 'open';
                            $customer_thread->token = Tools::passwdGen(12);
                            $customer_thread->add();
                        } else {
                            $customer_thread = new CustomerThread((int)$id_customer_thread);
                        }

                        $customer_message = new CustomerMessage();
                        $customer_message->id_customer_thread = $customer_thread->id;
                        $customer_message->id_employee = (int)$this->context->employee->id;
                        $customer_message->message = Tools::getValue('message');
                        $customer_message->private = Tools::getValue('visibility');

                        if (!$customer_message->add()) {
                            $this->errors[] = Tools::displayError('An error occurred while saving the message.');
                        } elseif ($customer_message->private) {
                            Tools::redirectAdmin(self::$currentIndex.'&id_order='.(int)$order->id.'&vieworder&conf=11&token='.$this->token);
                        } else {
                            $message = $customer_message->message;
                            if (Configuration::get('PS_MAIL_TYPE', null, null, $order->id_shop) != Mail::TYPE_TEXT) {
                                $message = Tools::nl2br($customer_message->message);
                            }

                            $varsTpl = array(
                                '{lastname}' => $customer->lastname,
                                '{firstname}' => $customer->firstname,
                                '{id_order}' => $order->id,
                                '{order_name}' => $order->getUniqReference(),
                                '{message}' => $message
                            );
                            // add subject adn file attachment
                            $subject = Tools::getValue('subject');
                            $fileAttachment = null;
                            if (!empty($_REQUEST['attach_invoice_id']))
                            {
                                $invoice = new OrderInvoice($_REQUEST['attach_invoice_id'], $order->id_lang);
                                if (Validate::isLoadedObject($invoice))
                                {
                                    if (file_exists($invoice->getInvoiceFilePath()))
                                    {
                                        $pdfFileContent = file_get_contents($invoice->getInvoiceFilePath());
                                    }
                                    else
                                    {
                                        $pdf = new PDF($invoice, $invoice->delivery_number?PDF::TEMPLATE_DELIVERY_SLIP:PDF::TEMPLATE_INVOICE, 
                                                Context::getContext()->smarty);
                                        $pdfFileContent = $pdf->render('S');
                                        // save file
                                        file_put_contents($invoice->getInvoiceFilePath(), $pdfFileContent);
                                    }
                                    $fileAttachment = ['mime'=>'application/pdf', 'name'=>$invoice->getInvoiceFileName(), 
                                        'content'=>$pdfFileContent];
                                }
                            }
                            if (!empty($_REQUEST['to']))
                            {
                                $to = explode(',', $_REQUEST['to']);
                            }
                            else
                            {
                                $to = $customer->email;
                            }
                            if (@Mail::Send((int)$order->id_lang, 'order_merchant_comment',
                                !empty($subject)?$subject:Mail::l('New message regarding your order', (int)$order->id_lang), $varsTpl, $to,
                                $customer->firstname.' '.$customer->lastname, null, null, $fileAttachment, null, _PS_MAIL_DIR_, true, (int)$order->id_shop)) {
                                Tools::redirectAdmin(self::$currentIndex.'&id_order='.$order->id.'&vieworder&conf=11'.'&token='.$this->token);
                            }
                        }
                        $this->errors[] = Tools::displayError('An error occurred while sending an email to the customer.');
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to delete this.');
            }
            return AdminController::postProcess();
        }
        elseif (Tools::isSubmit('submitAddOrder') && ($id_cart = Tools::getValue('id_cart')) &&
            ($module_name = Tools::getValue('payment_module_name')) &&
            ($id_order_state = Tools::getValue('id_order_state')) && Validate::isModuleName($module_name)) {
            if ($this->tabAccess['edit'] === '1') {
                if (!Configuration::get('PS_CATALOG_MODE')) {
                    $payment_module = Module::getInstanceByName($module_name);
                } else {
                    $payment_module = new BoOrder();
                }

                $cart = new Cart((int)$id_cart);
                Context::getContext()->currency = new Currency((int)$cart->id_currency);
                Context::getContext()->customer = new Customer((int)$cart->id_customer);
                $cart->id_lang = Context::getContext()->customer->id_lang;
                $cart->update();

                $bad_delivery = false;
                if (($bad_delivery = (bool)!Address::isCountryActiveById((int)$cart->id_address_delivery))
                    || !Address::isCountryActiveById((int)$cart->id_address_invoice)) {
                    if ($bad_delivery) {
                        $this->errors[] = Tools::displayError('This delivery address country is not active.');
                    } else {
                        $this->errors[] = Tools::displayError('This invoice address country is not active.');
                    }
                } else {
                    $employee = new Employee((int)Context::getContext()->cookie->id_employee);
                    $payment_module->validateOrder(
                        (int)$cart->id, (int)$id_order_state,
                        $cart->getOrderTotal(true, Cart::BOTH), $payment_module->displayName, $this->l('Manual order -- Employee:').' '.
                        substr($employee->firstname, 0, 1).'. '.$employee->lastname, array(), null, false, $cart->secure_key
                    );
                    if ($payment_module->currentOrder) {
                        Tools::redirectAdmin(self::$currentIndex.'&id_order='.$payment_module->currentOrder.'&vieworder'.'&token='.$this->token);
                    }
                }
            } else {
                $this->errors[] = Tools::displayError('You do not have permission to add this.');
            }
        }
         
        parent::postProcess();
    }
    
    
    function ajaxProcessToggleStockState()
    {
        $orderDetail = new OrderDetail((int)Tools::getValue('id'));
        if (!Validate::isLoadedObject($orderDetail)) {
            die(Tools::jsonEncode(array(
                'error' => Tools::displayError('The order detail object cannot be loaded.')
            )));
        }
        
        $orderDetail->in_stock = $orderDetail->in_stock?0:1;
        $orderDetail->update();
        die(Tools::jsonEncode(array(
                'error' => 0,
                'inStock' => $orderDetail->in_stock
            )));
    }
    
    
    function ajaxProcessToggleInvoicePaidState()
    {
        $invoice = new OrderInvoice((int)Tools::getValue('id'));
        if (!Validate::isLoadedObject($invoice)) {
            die(Tools::jsonEncode(array(
                'error' => Tools::displayError('The invoice object cannot be loaded.')
            )));
        }
        
        die(Tools::jsonEncode(array(
                'error' => 0,
                'paid' => $invoice->toggleInvoicePaidState()
            )));
    }
    
    
    function ajaxProcessSetInvoiceSumToPay()
    {
        $invoice = new OrderInvoice((int)Tools::getValue('id'));
        if (!Validate::isLoadedObject($invoice)) {
            die(Tools::jsonEncode(array(
                'error' => Tools::displayError('The invoice object cannot be loaded.')
            )));
        }
        
        $invoice->sum_to_pay = floatval(Tools::getValue('amount'));
        $invoice->update();
        die(Tools::jsonEncode(array(
                'error' => 0,
            )));
    }
    
    
    function ajaxProcessSetInvoiceDueDate()
    {
        $invoice = new OrderInvoice((int)Tools::getValue('id'));
        if (!Validate::isLoadedObject($invoice)) {
            die(Tools::jsonEncode(array(
                'error' => Tools::displayError('The invoice object cannot be loaded.')
            )));
        }
        
        $invoice->due_date = date('Y-m-d', strtotime(trim(Tools::getValue('dueDate'))));
        $invoice->update();
        die(Tools::jsonEncode(array(
                'error' => 0,
            )));
    }
    
    function ajaxProcessToggleShippedState()
    {
        $orderDetail = new OrderDetail((int)Tools::getValue('id'));
        if (!Validate::isLoadedObject($orderDetail)) {
            die(Tools::jsonEncode(array(
                'error' => Tools::displayError('The order detail object cannot be loaded.')
            )));
        }
        
        $orderDetail->shipped = $orderDetail->shipped?0:1;
        $orderDetail->shipped_employee_id = $this->context->employee->id;
        $orderDetail->shipped_date = date('Y-m-d H:i:s');
        $orderDetail->update();
        die(Tools::jsonEncode(array(
                'error' => 0,
                'shipped' => $orderDetail->shipped
            )));
    }
    
    
    public function ajaxProcessSearchProducts()
    {
        Context::getContext()->customer = new Customer((int)Tools::getValue('id_customer'));
        $currency = new Currency((int)Tools::getValue('id_currency'));
        if ($products = Product::searchByName((int)$this->context->language->id, pSQL(Tools::getValue('product_search')))) {
            foreach ($products as &$product) {
                // Formatted price
                $product['formatted_price'] = Tools::displayPrice(Tools::convertPrice($product['price_tax_incl'], $currency), $currency);
                // Concret price
                $product['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_incl'], $currency), 2);
                $product['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($product['price_tax_excl'], $currency), 2);
                $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
                //$product['name'] = $productObj->supplier_reference.' - '.$product['name'];
                $product['supplier_reference'] = ProductSupplier::getProductSupplierReference($productObj->id, 0, $productObj->id_supplier);
                $combinations = array();
                $attributes = $productObj->getAttributesGroups((int)$this->context->language->id, $productObj->id_supplier);

                // Tax rate for this customer
                if (Tools::isSubmit('id_address')) {
                    $product['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
                }

                $product['warehouse_list'] = array();

                foreach ($attributes as $attribute) {
                    if (!isset($combinations[$attribute['id_product_attribute']]['attributes'])) {
                        $combinations[$attribute['id_product_attribute']]['attributes'] = '';
                    }
                    $combinations[$attribute['id_product_attribute']]['supplier_reference'] = $attribute['product_supplier_reference'];
                    $combinations[$attribute['id_product_attribute']]['attributes'] .= $attribute['attribute_name'].' - ';
                    $combinations[$attribute['id_product_attribute']]['id_product_attribute'] = $attribute['id_product_attribute'];
                    $combinations[$attribute['id_product_attribute']]['default_on'] = $attribute['default_on'];
                    if (!isset($combinations[$attribute['id_product_attribute']]['price'])) {
                        $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
                        $price_tax_excl = Product::getPriceStatic((int)$product['id_product'], false, $attribute['id_product_attribute']);
                        $combinations[$attribute['id_product_attribute']]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                        $combinations[$attribute['id_product_attribute']]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                    }
                    if (!isset($combinations[$attribute['id_product_attribute']]['qty_in_stock'])) {
                        $combinations[$attribute['id_product_attribute']]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->id);
                    }

                    if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
                    } else {
                        $product['warehouse_list'][$attribute['id_product_attribute']] = array();
                    }

                    $product['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
                }

                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                    $product['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
                } else {
                    $product['warehouse_list'][0] = array();
                }

                $product['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], 0, (int)$this->context->shop->id);

                foreach ($combinations as &$combination) {
                    $combination['attributes'] = rtrim($combination['attributes'], ' - ');
                }
                $product['combinations'] = $combinations;

                if ($product['customizable']) {
                    $product_instance = new Product((int)$product['id_product']);
                    $product['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
                }
            }

            $to_return = array(
                'products' => $products,
                'found' => true
            );
        } else {
            $to_return = array('found' => false);
        }

        $this->content = Tools::jsonEncode($to_return);
    }
    
    
    function processFilter()
    {
        parent::processFilter();
        
        if(!empty($_REQUEST['orders_search']))
        {
            $scfg = isset($_REQUEST['scfg']) && is_array($_REQUEST['scfg'])?$_REQUEST['scfg']:array();
            $search = trim($_REQUEST['orders_search']);
            $this->_join .= ' left join '._DB_PREFIX_.
                    'address ia on a.id_address_invoice=ia.id_address left join '._DB_PREFIX_.'order_slip oslp on a.id_order=oslp.id_order left join '._DB_PREFIX_.
                    'order_detail od on a.id_order=od.id_order';
            
            if (!empty($scfg['supplier_name']))
            {
               $this->_join .= ' left join '._DB_PREFIX_.'product p on p.id_product=od.product_id left join '._DB_PREFIX_.
                    'supplier sup on p.id_supplier=sup.id_supplier';
            }

            if (!empty($scfg['invoice_id']))
            {
                $this->_join .= ' left join '._DB_PREFIX_.'order_invoice oi on a.id_order=oi.id_order';
            }
            
            if (preg_match('/^#?[A-Z]{2}(\d{6})$/i', $search, $matches))
            {
                $possibleInvoice = $matches[1];
            }
            else
            {
                $possibleInvoice = '';
            }

            // check if integer is searched
            if (preg_match('/^\d+$/', $search))
            {
                $search = pSQL($search);
                $this->_filter = ' and (0 ' . (!empty($scfg['customer_name']) ? " or c.firstname like '%$search%' or c.lastname like '%$search%' or address.lastname like " .
                                "'%$search%' or address.firstname like '%$search%' or ia.lastname like '%$search%' or ia.firstname like '%$search%'" : '') .
                        (!empty($scfg['customer_email']) ? " or c.email like '%$search%'" : '') .
                        (!empty($scfg['customer_address']) ? " or address.address1 like '%$search%' or address.address2 like '%$search%' or address.city like '%$search%' " .
                                "or address.postcode like '%$search%' or ia.address1 like '%$search%'" .
                                " or ia.address2 like '%$search%' or ia.city like '%$search%' or ia.postcode like '%$search%'" : '') .
                        (!empty($scfg['customer_phone']) ? " or address.phone like '%$search%' or address.phone_mobile like '%$search%' or ia.phone like '%$search%' " .
                                "or ia.phone_mobile like '%$search%'" : '') .
                        (!empty($scfg['order_id']) ? " or a.id_order='$search'" : '') .
                        (!empty($scfg['product_name']) || !empty($scfg['supplier_reference']) || !empty($scfg['product_id']) ?
                                " or a.id_order in (select id_order from " . _DB_PREFIX_ . "order_detail where 0" .
                                (!empty($scfg['product_name']) ? " or product_name like '%$search%'" : '') .
                                (!empty($scfg['supplier_reference']) ? " or product_supplier_reference like '%$search%' " : '') .
                                (!empty($scfg['product_id']) ? " or product_id='$search'" : '') . ')' : '') .
                       // (!empty($scfg['tracking_number']) ? " or tn.number like '%$search%'" : '') .
                        (!empty($scfg['invoice_id']) ? " or a.invoice_number=$search or oslp.id_order_slip=$search or oi.number=$search"
                        . " or oi.delivery_number=$search" : '') . ')';
            }
            else
            {
                $search = pSQL($search);
                $this->_join .= ' left join '._DB_PREFIX_ . 'country_lang aic on aic.id_country=ia.id_country and aic.id_lang=' . 
                        $this->context->cookie->id_lang;

                $this->_filter = " and (0 " .
                        (!empty($scfg['customer_name']) ? " or c.firstname like '%$search%' or c.lastname like '%$search%' or address.lastname like '%$search%' or address.firstname" .
                                " like '%$search%' or ia.lastname like '%$search%' or ia.firstname like '%$search%'" : '') .
                        (!empty($scfg['customer_email']) ? " or c.email like '%$search%'" : '') .
                        (!empty($scfg['customer_address']) ? " or address.address1 like '%$search%' or address.address2 like '%$search%' or address.city like '%$search%' " .
                                "or address.postcode like '%$search%' or ia.address1 like '%$search%'" .
                                " or ia.address2 like '%$search%' or ia.city like '%$search%' or ia.postcode like '%$search%'" : '') .
// (!empty($scfg['customer_phone'])?" or address.phone like '%$search%' or address.phone_mobile like '%$search%' ".
//  "or ia.phone like '%$search%' or ia.phone_mobile like '%$search%'":'').
                        
                        (!empty($scfg['product_name']) || !empty($scfg['supplier_reference']) ? " or a.id_order in (select id_order" .
                                " from " . _DB_PREFIX_ . "order_detail where 0 " .
                                (!empty($scfg['product_name']) ? " or product_name like '%$search%'" : '') .
                                (!empty($scfg['supplier_reference']) ? " or product_supplier_reference like '%$search%' " : '') . ')' : '') .
                        (!empty($scfg['supplier_name']) ? " or sup.name like '%$search%'" : '') .
                        //(!empty($scfg['tracking_number']) ? " or tn.number like '%$search%'" : '') .
                        (!empty($scfg['country_name']) ? " or country_lang.name like '%$search%' or aic.name like '%$search%'" : '') .
                        ($possibleInvoice && !empty($scfg['invoice_id']) ? " or a.invoice_number=$possibleInvoice or oslp.id_order_slip="
                        . "$possibleInvoice or oi.number=$possibleInvoice  or oi.delivery_number=$possibleInvoice" : '') . ')'
                    .(!empty($scfg['company_name']) ? 
                        " OR c.company LIKE '%$search%' OR address.company LIKE '%$search%' OR ia.company LIKE '%$search%'" 
                        : '')
                ;
            }
            //echo $this->_join.'<br>'.$this->_filter;
        }

        $searchChar = null;
        if(!empty($this->context->cookie->orders_search_char)){
            $searchChar = $this->context->cookie->orders_search_char;
        }
        
        if( !empty($_POST['search_char']) ){
            if( $_POST['search_char'] == '-' ){
                $searchChar = null;
                unset($this->context->cookie->orders_search_char);
            }
            else{
                $searchChar = $_POST['search_char'];
            }
            
        }
        if( !empty($searchChar) ){
            $this->_filter .= ' AND c.company LIKE "'. pSQL($searchChar) .'%" ';
            $this->context->cookie->orders_search_char = $searchChar;
            $this->context->smarty->assign(array(
                'search_char_selected' => $searchChar
            ));
        }
        
    }

    public function processResetFilters($list_id = null)
    {
        unset($this->context->cookie->customers_search_char);
        return parent::processResetFilters($list_id);
    }
    
    
    /**
     * @param Order $order
     * @return array
     */
    protected function getProducts($order)
    {
        $products = $order->getProducts();
        
        // preapre employees
        $employeesList = Employee::getEmployees(false);
        $employees = [];
        foreach ($employeesList as $employee)
        {
            $employees[$employee['id_employee']] = $employee['firstname'].' '.$employee['lastname'].' ('.$employee['id_employee'].')';
        }

        foreach ($products as &$product) {
            // add information about who set shipped status
            if ($product['shipped_employee_id'])
            {
                $product['who_shipped'] = $employees[$product['shipped_employee_id']].', '.Tools::displayDate($product['shipped_date'], null, true);
            }
            
            if ($product['image'] != null) {
                $name = 'product_mini_'.$product['image']->id.(int)$product['product_id'].(isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
                // generate image cache, only for back office
                $product['image_tag'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 45, 'jpg');
                if (file_exists(_PS_TMP_IMG_DIR_.$name)) {
                    $product['image_size'] = getimagesize(_PS_TMP_IMG_DIR_.$name);
                } else {
                    $product['image_size'] = false;
                }
            }
        }

        ksort($products);

        return $products;
    }
    
    
    protected function reinjectQuantity($order_detail, $qty_cancel_product, $delete = false)
    {
        parent::reinjectQuantity($order_detail, $qty_cancel_product, $delete);
        
        if (count($this->errors)==0)
        {
            // add message to msss client
            $msss_client = ModuleCore::getInstanceByName('msss_client');
            // report stock update 
            $msss_client->scheduleStockUpdateById($order_detail->product_id, $order_detail->product_attribute_id,
                        $qty_cancel_product);
        }
    }
    
    
    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'admin/order_invoices.js');
        $this->context->controller->addJS(__PS_BASE_URI__.'js/jquery/plugins/cluetip/jquery.cluetip.js');
        $this->context->controller->addCss(__PS_BASE_URI__.'js/jquery/plugins/cluetip/jquery.cluetip.css');
    }
    
    
    public function showMayBeShipped($mayBeShipped, $row)
    {
        if ($mayBeShipped)
        {
            return '<i class="icon-truck"></i>';
        }
    }
    
    
    public function ajaxProcessAddProductOnOrder()
    {
        // Load object
        $order = new Order((int)Tools::getValue('id_order'));
        if (!Validate::isLoadedObject($order)) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The order object cannot be loaded.')
            )));
        }

        $old_cart_rules = Context::getContext()->cart->getCartRules();

        if ($order->hasBeenShipped()) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('You cannot add products to delivered orders. ')
            )));
        }

        $product_informations = $_POST['add_product'];
        if (isset($_POST['add_invoice'])) {
            $invoice_informations = $_POST['add_invoice'];
        } else {
            $invoice_informations = array();
        }
        $product = new Product($product_informations['product_id'], false, $order->id_lang);
        if (!Validate::isLoadedObject($product)) {
            die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The product object cannot be loaded.')
            )));
        }

        if (isset($product_informations['product_attribute_id']) && $product_informations['product_attribute_id']) {
            $combination = new Combination($product_informations['product_attribute_id']);
            if (!Validate::isLoadedObject($combination)) {
                die(Tools::jsonEncode(array(
                'result' => false,
                'error' => Tools::displayError('The combination object cannot be loaded.')
            )));
            }
        }

        // Total method
        $total_method = Cart::BOTH_WITHOUT_SHIPPING;

        // Create new cart
        $cart = new Cart();
        $cart->id_shop_group = $order->id_shop_group;
        $cart->id_shop = $order->id_shop;
        $cart->id_customer = $order->id_customer;
        $cart->id_carrier = $order->id_carrier;
        $cart->id_address_delivery = $order->id_address_delivery;
        $cart->id_address_invoice = $order->id_address_invoice;
        $cart->id_currency = $order->id_currency;
        $cart->id_lang = $order->id_lang;
        $cart->secure_key = $order->secure_key;

        // Save new cart
        $cart->add();

        // Save context (in order to apply cart rule)
        $this->context->cart = $cart;
        $this->context->customer = new Customer($order->id_customer);

        // always add taxes even if there are not displayed to the customer
        $use_taxes = true;

        $initial_product_price_tax_incl = Product::getPriceStatic($product->id, $use_taxes, isset($combination) ? $combination->id : null, 2, null, false, true, 1,
            false, $order->id_customer, $cart->id, $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});

        // Creating specific price if needed
        if ($product_informations['product_price_tax_incl'] != $initial_product_price_tax_incl) {
            $specific_price = new SpecificPrice();
            $specific_price->id_shop = 0;
            $specific_price->id_shop_group = 0;
            $specific_price->id_currency = 0;
            $specific_price->id_country = 0;
            $specific_price->id_group = 0;
            $specific_price->id_customer = $order->id_customer;
            $specific_price->id_product = $product->id;
            if (isset($combination)) {
                $specific_price->id_product_attribute = $combination->id;
            } else {
                $specific_price->id_product_attribute = 0;
            }
            $specific_price->price = $product_informations['product_price_tax_excl'];
            $specific_price->from_quantity = 1;
            $specific_price->reduction = 0;
            $specific_price->reduction_type = 'amount';
            $specific_price->reduction_tax = 0;
            $specific_price->from = '0000-00-00 00:00:00';
            $specific_price->to = '0000-00-00 00:00:00';
            $specific_price->add();
        }

        // Add product to cart
        $update_quantity = $cart->updateQty($product_informations['product_quantity'], $product->id, isset($product_informations['product_attribute_id']) ? $product_informations['product_attribute_id'] : null,
            isset($combination) ? $combination->id : null, 'up', 0, new Shop($cart->id_shop));

        if ($update_quantity < 0) {
            // If product has attribute, minimal quantity is set with minimal quantity of attribute
            $minimal_quantity = ($product_informations['product_attribute_id']) ? Attribute::getAttributeMinimalQty($product_informations['product_attribute_id']) : $product->minimal_quantity;
            die(Tools::jsonEncode(array('error' => sprintf(Tools::displayError('You must add %d minimum quantity', false), $minimal_quantity))));
        } elseif (!$update_quantity) {
            die(Tools::jsonEncode(array('error' => Tools::displayError('You already have the maximum quantity available for this product.', false))));
        }

        /*
        if ($product_informations['invoice'])
        {
            // create a new invoice
            $order_invoice = new OrderInvoice();
            // If we create a new invoice, we calculate shipping cost
            $total_method = Cart::BOTH;
            // Create Cart rule in order to make free shipping
            if (isset($invoice_informations['free_shipping']) && $invoice_informations['free_shipping'])
            {
                $cart_rule = new CartRule();
                $cart_rule->id_customer = $order->id_customer;
                $cart_rule->name = array(
                    Configuration::get('PS_LANG_DEFAULT') => $this->l('[Generated] CartRule for Free Shipping')
                );
                $cart_rule->date_from = date('Y-m-d H:i:s', time());
                $cart_rule->date_to = date('Y-m-d H:i:s', time() + 24 * 3600);
                $cart_rule->quantity = 1;
                $cart_rule->quantity_per_user = 1;
                $cart_rule->minimum_amount_currency = $order->id_currency;
                $cart_rule->reduction_currency = $order->id_currency;
                $cart_rule->free_shipping = true;
                $cart_rule->active = 1;
                $cart_rule->add();

                // Add cart rule to cart and in order
                $cart->addCartRule($cart_rule->id);
                $values = array(
                    'tax_incl' => $cart_rule->getContextualValue(true),
                    'tax_excl' => $cart_rule->getContextualValue(false)
                );
                $order->addCartRule($cart_rule->id, $cart_rule->name[Configuration::get('PS_LANG_DEFAULT')], $values);
            }

            $order_invoice->id_order = $order->id;
            if ($order_invoice->number)
            {
                Configuration::updateValue('PS_INVOICE_START_NUMBER', false, false, null, $order->id_shop);
            }
            else
            {
                $order_invoice->number = Order::getLastInvoiceNumber() + 1;
            }

            $invoice_address = new Address((int) $order->{Configuration::get('PS_TAX_ADDRESS_TYPE', null, null, $order->id_shop)});
            $carrier = new Carrier((int) $order->id_carrier);
            $tax_calculator = $carrier->getTaxCalculator($invoice_address);

            $order_invoice->total_paid_tax_excl = Tools::ps_round((float) $cart->getOrderTotal(false, $total_method), 2);
            $order_invoice->total_paid_tax_incl = Tools::ps_round((float) $cart->getOrderTotal($use_taxes, $total_method), 2);
            $order_invoice->total_products = (float) $cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
            $order_invoice->total_products_wt = (float) $cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);
            $order_invoice->total_shipping_tax_excl = (float) $cart->getTotalShippingCost(null, false);
            $order_invoice->total_shipping_tax_incl = (float) $cart->getTotalShippingCost();

            $order_invoice->total_wrapping_tax_excl = abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
            $order_invoice->total_wrapping_tax_incl = abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
            $order_invoice->shipping_tax_computation_method = (int) $tax_calculator->computation_method;

            // Update current order field, only shipping because other field is updated later
            $order->total_shipping += $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl += $order_invoice->total_shipping_tax_excl;
            $order->total_shipping_tax_incl += ($use_taxes) ? $order_invoice->total_shipping_tax_incl : $order_invoice->total_shipping_tax_excl;

            $order->total_wrapping += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
            $order->total_wrapping_tax_excl += abs($cart->getOrderTotal(false, Cart::ONLY_WRAPPING));
            $order->total_wrapping_tax_incl += abs($cart->getOrderTotal($use_taxes, Cart::ONLY_WRAPPING));
            $order_invoice->add();

            $order_invoice->saveCarrierTaxCalculator($tax_calculator->getTaxesAmount($order_invoice->total_shipping_tax_excl));

            $order_carrier = new OrderCarrier();
            $order_carrier->id_order = (int) $order->id;
            $order_carrier->id_carrier = (int) $order->id_carrier;
            $order_carrier->id_order_invoice = (int) $order_invoice->id;
            $order_carrier->weight = (float) $cart->getTotalWeight();
            $order_carrier->shipping_cost_tax_excl = (float) $order_invoice->total_shipping_tax_excl;
            $order_carrier->shipping_cost_tax_incl = ($use_taxes) ? (float) $order_invoice->total_shipping_tax_incl : (float) $order_invoice->total_shipping_tax_excl;
            $order_carrier->add();
        }
         * 
         */

        // Create Order detail information
        $order_detail = new OrderDetail();
        $order_detail->createList($order, $cart, $order->getCurrentOrderState(), $cart->getProducts(), (isset($order_invoice) ? $order_invoice->id : 0), $use_taxes, (int)Tools::getValue('add_product_warehouse'));

        // update totals amount of order
        $order->total_products += (float)$cart->getOrderTotal(false, Cart::ONLY_PRODUCTS);
        $order->total_products_wt += (float)$cart->getOrderTotal($use_taxes, Cart::ONLY_PRODUCTS);

        $order->total_paid += Tools::ps_round((float)($cart->getOrderTotal(true, $total_method)), 2);
        $order->total_paid_tax_excl += Tools::ps_round((float)($cart->getOrderTotal(false, $total_method)), 2);
        $order->total_paid_tax_incl += Tools::ps_round((float)($cart->getOrderTotal($use_taxes, $total_method)), 2);

        if (isset($order_invoice) && Validate::isLoadedObject($order_invoice)) {
            $order->total_shipping = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_incl = $order_invoice->total_shipping_tax_incl;
            $order->total_shipping_tax_excl = $order_invoice->total_shipping_tax_excl;
        }
        // discount
        $order->total_discounts += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_excl += (float)abs($cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $order->total_discounts_tax_incl += (float)abs($cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));

        // Save changes of order
        $order->update();

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            if ($order_carrier->update()) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        // Update Tax lines
        $order_detail->updateTaxAmount($order);

        // Delete specific price if exists
        if (isset($specific_price)) {
            $specific_price->delete();
        }

        $products = $this->getProducts($order);

        // Get the last product
        $product = end($products);
        $resume = OrderSlip::getProductSlipResume((int)$product['id_order_detail']);
        $product['quantity_refundable'] = $product['product_quantity'] - $resume['product_quantity'];
        $product['amount_refundable'] = $product['total_price_tax_excl'] - $resume['amount_tax_excl'];
        $product['amount_refund'] = Tools::displayPrice($resume['amount_tax_incl']);
        $product['return_history'] = OrderReturn::getProductReturnDetail((int)$product['id_order_detail']);
        $product['refund_history'] = OrderSlip::getProductSlipDetail((int)$product['id_order_detail']);
        if ($product['id_warehouse'] != 0) {
            $warehouse = new Warehouse((int)$product['id_warehouse']);
            $product['warehouse_name'] = $warehouse->name;
            $warehouse_location = WarehouseProductLocation::getProductLocation($product['product_id'], $product['product_attribute_id'], $product['id_warehouse']);
            if (!empty($warehouse_location)) {
                $product['warehouse_location'] = $warehouse_location;
            } else {
                $product['warehouse_location'] = false;
            }
        } else {
            $product['warehouse_name'] = '--';
            $product['warehouse_location'] = false;
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'product' => $product,
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'can_edit' => $this->tabAccess['edit'],
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'display_warehouse' => (int)Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT'),
            'orderDocumentsOnlyTable' => true
        ));

        $this->sendChangedNotification($order);
        $new_cart_rules = Context::getContext()->cart->getCartRules();
        sort($old_cart_rules);
        sort($new_cart_rules);
        $result = array_diff($new_cart_rules, $old_cart_rules);
        $refresh = false;

        $res = true;
        foreach ($result as $cart_rule) {
            $refresh = true;
            // Create OrderCartRule
            $rule = new CartRule($cart_rule['id_cart_rule']);
            $values = array(
                    'tax_incl' => $rule->getContextualValue(true),
                    'tax_excl' => $rule->getContextualValue(false)
                    );
            $order_cart_rule = new OrderCartRule();
            $order_cart_rule->id_order = $order->id;
            $order_cart_rule->id_cart_rule = $cart_rule['id_cart_rule'];
            $order_cart_rule->id_order_invoice = $order_invoice->id;
            $order_cart_rule->name = $cart_rule['name'];
            $order_cart_rule->value = $values['tax_incl'];
            $order_cart_rule->value_tax_excl = $values['tax_excl'];
            $res &= $order_cart_rule->add();

            $order->total_discounts += $order_cart_rule->value;
            $order->total_discounts_tax_incl += $order_cart_rule->value;
            $order->total_discounts_tax_excl += $order_cart_rule->value_tax_excl;
            $order->total_paid -= $order_cart_rule->value;
            $order->total_paid_tax_incl -= $order_cart_rule->value;
            $order->total_paid_tax_excl -= $order_cart_rule->value_tax_excl;
        }

        // Update Order
        $res &= $order->update();

        die(Tools::jsonEncode(array(
            'result' => true,
            'view' => $this->createTemplate('_product_line.tpl')->fetch(),
            'can_edit' => $this->tabAccess['add'],
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch(),
            'discount_form_html' => $this->createTemplate('_discount_form.tpl')->fetch(),
            'refresh' => $refresh
        )));
    }
    
    
    public function ajaxProcessDeleteProductLine()
    {
        $res = true;

        $order_detail = new OrderDetail((int)Tools::getValue('id_order_detail'));
        $order = new Order((int)Tools::getValue('id_order'));

        $this->doDeleteProductLineValidation($order_detail, $order);

        // Update Order
        $order->total_paid -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_incl -= $order_detail->total_price_tax_incl;
        $order->total_paid_tax_excl -= $order_detail->total_price_tax_excl;
        $order->total_products -= $order_detail->total_price_tax_excl;
        $order->total_products_wt -= $order_detail->total_price_tax_incl;

        $res &= $order->update();

        // Reinject quantity in stock
        $this->reinjectQuantity($order_detail, $order_detail->product_quantity, true);

        // Update weight SUM
        $order_carrier = new OrderCarrier((int)$order->getIdOrderCarrier());
        if (Validate::isLoadedObject($order_carrier)) {
            $order_carrier->weight = (float)$order->getTotalWeight();
            $res &= $order_carrier->update();
            if ($res) {
                $order->weight = sprintf("%.3f ".Configuration::get('PS_WEIGHT_UNIT'), $order_carrier->weight);
            }
        }

        if (!$res) {
            die(Tools::jsonEncode(array(
                'result' => $res,
                'error' => Tools::displayError('An error occurred while attempting to delete the product line.')
            )));
        }

        // Get invoices collection
        $invoice_collection = $order->getInvoicesCollection();

        $invoice_array = array();
        foreach ($invoice_collection as $invoice) {
            /** @var OrderInvoice $invoice */
            $invoice->name = $invoice->getInvoiceNumberFormatted(Context::getContext()->language->id, (int)$order->id_shop);
            $invoice_array[] = $invoice;
        }

        // Assign to smarty informations in order to show the new product line
        $this->context->smarty->assign(array(
            'order' => $order,
            'currency' => new Currency($order->id_currency),
            'invoices_collection' => $invoice_collection,
            'current_id_lang' => Context::getContext()->language->id,
            'link' => Context::getContext()->link,
            'current_index' => self::$currentIndex,
            'orderDocumentsOnlyTable' => true
        ));

        $this->sendChangedNotification($order);

        die(Tools::jsonEncode(array(
            'result' => $res,
            'order' => $order,
            'invoices' => $invoice_array,
            'documents_html' => $this->createTemplate('_documents.tpl')->fetch(),
            'shipping_html' => $this->createTemplate('_shipping.tpl')->fetch()
        )));
    }
    
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        // reading list
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, $id_lang_shop);
        
        // reading list of customers with not paid invoices and reminders sent
        $db = Db::getInstance();
        $sqlRes = $db->executeS('select distinct id_customer from '._DB_PREFIX_.'order_invoice oi inner join '.
                _DB_PREFIX_.'orders o on o.id_order=oi.id_order where oi.paid=0 and reminder_state>0', false);
        while($row = $db->nextRow($sqlRes))
        {
            $reminderedCustomerIds[$row['id_customer']] = 1;
        }
        $shippedStatusId = Configuration::get('PS_OS_SHIPPING');
        foreach($this->_list as &$order)
        {
            if ($order['current_state'] != $shippedStatusId && isset($reminderedCustomerIds[$order['id_customer']]))
            {
                if (empty($order['class']))
                {
                    $order['class'] = 'notPaidInvoice';
                }
                else
                {
                    $order['class'] .= ' notPaidInvoice';
                }
            }
        }
//        echo '<br>'.$shippedStatusId.' ';
//        print_r($this->_list);
//        exit;
        //$this->tpl_list_vars['reminderedCustomerIds'] = $reminderedCustomerIds;
        
    }

    public function ajaxProcessSearchProductsByCategory()
    {
        Context::getContext()->customer = new Customer((int)Tools::getValue('id_customer'));
        $currency = new Currency((int)Tools::getValue('id_currency'));
        $to_return = array(
            'products' => null,
            'found' => false
        );
        $id_category = Tools::getValue('id_category');
        //$category = new Category($id_category);
    
        $sql = new DbQuery();
        $sql->select('p.`id_product`, pl.`name`, p.`ean13`, p.`upc`, p.`active`, p.`reference`,
            m.`name` AS manufacturer_name, stock.`quantity`, product_shop.advanced_stock_management,
            p.`customizable`, p.`supplier_reference`
        ');
        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$this->context->language->id.Shop::addSqlRestrictionOnLang('pl')
            );
        $sql->innerJoin('category_product', 'cp', 'cp.id_product = p.id_product');
    
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');
    
    
        $sql->orderBy('pl.`name` ASC');
    
        //$where = '';
        $sql->where('cp.id_category = '. $id_category);
        $sql->join(Product::sqlStock('p', 0));
    
        $products = Db::getInstance()->executeS($sql);
    
        foreach ($products as $pi => $product) {
            $products[$pi]['price_tax_incl'] = Product::getPriceStatic($product['id_product'], true, null, 2);
            $products[$pi]['price_tax_excl'] = Product::getPriceStatic($product['id_product'], false, null, 2);
            $products[$pi]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($products[$pi]['price_tax_incl'], $currency), $currency);
    
            $productObj = new Product((int)$product['id_product'], false, (int)$this->context->language->id);
            $combinations = array();
            $attributes = $productObj->getAttributesGroups((int)$this->context->language->id, true);
    
            // Tax rate for this customer
            if (Tools::isSubmit('id_address')) {
                $products[$pi]['tax_rate'] = $productObj->getTaxesRate(new Address(Tools::getValue('id_address')));
            }
    
            $products[$pi]['warehouse_list'] = array();
    
            foreach ($attributes as $ai => $attribute) {
                
                if (!isset($combinations[$ai]['attributes'])) {
                    $combinations[$ai]['attributes'] = '';
                }
                $combinations[$ai]['attributes'] .= $attribute['attribute_name'].' - ';
                $combinations[$ai]['id_product_attribute'] = $attribute['id_product_attribute'];
                $combinations[$ai]['default_on'] = $attribute['default_on'];
                if (!isset($combinations[$ai]['price'])) {
                    $price_tax_incl = Product::getPriceStatic((int)$product['id_product'], true, $attribute['id_product_attribute']);
                    $price_tax_excl = Product::getPriceStatic((int)$product['id_product'], false, $attribute['id_product_attribute']);
                    $combinations[$ai]['price_tax_incl'] = Tools::ps_round(Tools::convertPrice($price_tax_incl, $currency), 2);
                    $combinations[$ai]['price_tax_excl'] = Tools::ps_round(Tools::convertPrice($price_tax_excl, $currency), 2);
                    $combinations[$ai]['formatted_price'] = Tools::displayPrice(Tools::convertPrice($price_tax_excl, $currency), $currency);
                }
                if (!isset($combinations[$ai]['qty_in_stock'])) {
                    $combinations[$ai]['qty_in_stock'] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], $attribute['id_product_attribute'], (int)$this->context->shop->id);
                }
                $combinations[$ai]['supplier_reference'] = $attribute['product_supplier_reference'];
    
                if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                    $products[$pi]['warehouse_list'][$attribute['id_product_attribute']] = Warehouse::getProductWarehouseList($product['id_product'], $attribute['id_product_attribute']);
                } else {
                    $products[$pi]['warehouse_list'][$attribute['id_product_attribute']] = array();
                }
    
                $products[$pi]['stock'][$attribute['id_product_attribute']] = Product::getRealQuantity($product['id_product'], $attribute['id_product_attribute']);
            }
    
            if (Configuration::get('PS_ADVANCED_STOCK_MANAGEMENT') && (int)$product['advanced_stock_management'] == 1) {
                $products[$pi]['warehouse_list'][0] = Warehouse::getProductWarehouseList($product['id_product']);
            } else {
                $products[$pi]['warehouse_list'][0] = array();
            }
    
            $products[$pi]['stock'][0] = StockAvailable::getQuantityAvailableByProduct((int)$product['id_product'], 0, (int)$this->context->shop->id);
    
            foreach ($combinations as &$combination) {
                $combination['attributes'] = rtrim($combination['attributes'], ' - ');
            }
            $products[$pi]['combinations'] = $combinations;
    
            if ($product['customizable']) {
                $product_instance = new Product((int)$product['id_product']);
                $products[$pi]['customization_fields'] = $product_instance->getCustomizationFields($this->context->language->id);
            }
        }
    
        if(count($products)){
            $to_return['products'] = $products;
            $to_return['found'] = true;
        }
    
        $this->content = Tools::jsonEncode($to_return);
    }
    
    public function ajaxProcessSearchCustomersByFirstChar()
    {
        $this->ajax = true;
        $jsonResponse = array('data' => array());
        
        $query = Tools::getValue('query');
        
        if( !empty($query) ){
            if( $query == '-' ){
                $searchChar = null;
            }
            else{
                $searchChar = $query;
            }
        }
        
        $sql = '
            SELECT *
			FROM `'._DB_PREFIX_.'customer`
        ';
        if( $searchChar ){
            $sql .= ' WHERE `company` LIKE "'.pSQL($query).'%" ';
        }
    
        /*if ($limit) {
            $sql .= ' LIMIT 0, '.(int)$limit;
        }*/
        
        $customers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
    
        $jsonResponse['data'] = $customers;
        
        $this->content = Tools::jsonEncode($jsonResponse);
    }
    
    public function ajaxProcessSearchCustomersByQuery()
    {
        /**
         * 
         * @var DbQueryCore $query
         */
        $query = new DbQuery();
        $query
            ->from('customer', 'c')
            ->select('c.*')
            ->leftJoin('address', 'a', 'a.id_customer = c.id_customer')
            ->leftJoin('orders', 'o', 'o.id_customer = c.id_customer')
            ->leftJoin('order_invoice', 'oi', 'oi.id_order = o.id_order')
            ->leftJoin('order_detail', 'od', 'od.id_order = o.id_order')
            ->leftJoin('product', 'p', 'p.id_product = od.product_id')
            ->leftJoin('supplier', 's', 's.id_supplier = p.id_supplier')
            ->leftJoin('country_lang', 'cntl', 
                'cntl.id_country = c.id_country AND cntl.id_lang = '. $this->context->language->id)
            ->groupBy('c.id_customer')
        ;
        
        $term = $_POST['term'];
        $term_number = intval($term);
        $term_escaped = pSQL($term, false);
        $term_search_in = $_POST['term_options'];
        $responseData = array(
            'customers' => null,
            'found' => false
        );
        
        if( !empty($term_search_in) && is_array($term_search_in) && count($term_search_in) ){
            foreach($term_search_in as $search_in){
                switch($search_in){
                    case 'customer_name':
                        $query->whereOr('c.firstname LIKE "'. $term_escaped .'%" OR c.lastname LIKE "'. $term_escaped .'%"');
                        break;
                    case 'customer_email':
                        $query->whereOr('c.email LIKE "'. $term_escaped .'%"');
                        break;
                    case 'customer_address':
                        $query->whereOr('
                            a.company LIKE "'. $term_escaped .'%"
                            OR a.lastname LIKE "'. $term_escaped .'%" 
                            OR a.firstname LIKE "'. $term_escaped .'%" 
                            OR a.address1 LIKE "'. $term_escaped .'%" 
                            OR a.address2 LIKE "'. $term_escaped .'%" 
                            OR a.postcode LIKE "'. $term_escaped .'%" 
                            OR a.city LIKE "'. $term_escaped .'%" 
                        ');
                        break;
                    case 'customer_phone':
                        $query->whereOr('
                            c.phone LIKE "'. $term_escaped .'%" 
                            OR c.phone_mobile LIKE "'. $term_escaped .'%" 
                            OR a.phone LIKE "'. $term_escaped .'%" 
                            OR a.phone_mobile LIKE "'. $term_escaped .'%" 
                        ');
                        break;
                    case 'product_id':
                        $query->whereOr('od.product_id = '. intval($term));
                        break;
                    case 'product_name':
                        $query->whereOr('od.product_name LIKE "'. $term_escaped .'%" ');
                        break;
                    case 'supplier_reference':
                        $query->whereOr('od.product_supplier_reference LIKE "'. $term_escaped .'%" ');
                        break;
                    case 'invoice_id':
                        if($term_number){
                            $query->whereOr('oi.number = '. intval($term));
                        }
                        break;
                    case 'supplier_name':
                        $query->whereOr('s.name LIKE "'. $term_escaped .'%" ');
                        break;
                    case 'country_name':
                        $query->whereOr('cntl.name LIKE "'. $term_escaped .'%" ');
                        break;
                    case 'company_name':
                        $query->whereOr('c.company LIKE "'. $term_escaped .'%" ');
                        break;
                }
            }
        }
        
        $customers = Db::getInstance()->executeS($query);
        $responseData['q'] = $query->build();
        if( count($customers) ){
            $responseData['customers'] = $customers;
            $responseData['found'] = true;
        }
        
        $this->content = Tools::jsonEncode($responseData);
    }
    
    public function renderList()
    {
        $searchLetters = array();
        for( $c = ord('A'); $c < ord('Z'); $c++ ){
            $searchLetters[] = chr($c);
        }
    
        $this->context->smarty->assign(array(
            'searchbar_letters' => $searchLetters
        ));
    
        return parent::renderList();
    }
    
}


