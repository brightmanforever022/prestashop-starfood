<?php
class AdminCustomersController extends AdminCustomersControllerCore 
{
    protected $countriesList = [];
    
    public function __construct()
    {
        $this->bootstrap = true;
        $this->required_database = true;
        $this->required_fields = array('newsletter','optin');
        $this->table = 'customer';
        $this->className = 'Customer';
        $this->lang = false;
        $this->deleted = true;
        $this->explicitSelect = true;
        $this->list_no_link = true;
        
        $this->_join = ' left join (select city, postcode from '._DB_PREFIX_.'address addr1 where addr1.id_customer=a.id_customer '
                . 'order by addr1.id_address desc limit 1) ';

        $this->allow_export = true;

        $this->addRowAction('edit');
        $this->addRowAction('view');
        $this->addRowAction('delete');
        $this->bulk_actions = array(
            'excelList' => array('text' => $this->l('Export to excel'), 'icon' => 'icon-road', 'targetBlank'=>1),
            'divider2' => array(
                    'text' => 'divider'
                ),
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash'
            )
        );

        $this->context = Context::getContext();

        $this->default_form_language = $this->context->language->id;

        $titles_array = array();
        $genders = Gender::getGenders($this->context->language->id);
        $gendersList = [];
        foreach ($genders as $gender) {
            /** @var Gender $gender */
            $titles_array[$gender->id_gender] = $gender->name;
            $gendersList []= ['id'=>$gender->id_gender, 'name'=>$gender->name];
        }

        $this->_join = 'LEFT JOIN '._DB_PREFIX_.'gender_lang gl ON (a.id_gender = gl.id_gender AND gl.id_lang = '.(int)$this->context->language->id.')';
        $this->_use_found_rows = false;
        $this->fields_list = array(
            'id_customer' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->l('Social title'),
                'filter_key' => 'a!id_gender',
                'type' => 'select',
                'list' => $titles_array,
                'filter_type' => 'int',
                'order_key' => 'gl!name',
                'class'=>'gender'
            ),
            'firstname' => array(
                'title' => $this->l('First name'),
                'class' => 'firstName'
            ),
            'lastname' => array(
                'title' => $this->l('Last name'),
                'class' => 'lastName'
            ),
            'email' => array(
                'title' => $this->l('Email address'),
                'class' => 'email'
            ),
        );

        if (Configuration::get('PS_B2B_ENABLE')) {
            $this->fields_list = array_merge($this->fields_list, array(
                'company' => array(
                    'title' => $this->l('Company'),
                'class' => 'company'
                ),
            ));
        }

        $countries = Country::getCountries($this->context->language->id, false, false, false);
        $countriesList2 = [];
        foreach ($countries as $country) 
        {
            $this->countriesList[$country['id_country']] = $country['name'];
            $countriesList2 []= ['id'=>$country['id_country'], 'name'=>$country['name']];
        }
        
        $this->fields_list = array_merge($this->fields_list, array(
            'total_spent' => array(
                'title' => $this->l('Sales'),
                'type' => 'price',
                'search' => false,
                'havingFilter' => true,
                'align' => 'text-right',
                'badge_success' => true
            ),
            'client_number' => array(
                'title' => $this->l('Client number'),
            ),
            'active' => array(
                'title' => $this->l('Enabled'),
                'align' => 'text-center',
                'active' => 'status',
                'type' => 'bool',
                'orderby' => false,
                'filter_key' => 'a!active'
            ),
            'city' => array(
                'title' => $this->l('City'),
                'class' => 'city'
            ),
            'receivable_number' => array(
                'title' => $this->l('Accounts receivable number'),
            ),
            'address1' => array(
                'title' => $this->l('Address 1'),
                'class' => 'address1'
            ),
            'address2' => array(
                'title' => $this->l('Address 2'),
                'class' => 'address2'
            ),
            'postcode' => array(
                'title' => $this->l('Post code'),
                'class' => 'postcode'
            ),
            'country' => array('title' => $this->l('Country'), 'type' => 'select', 
                'list' => $this->countriesList,
                'filter_key' => 'a!id_country',
                'callback' => 'showCountry',
                'class' => 'country'
                ),
            'note' => array(
                'title' => $this->l('Note'),
                'class' => 'note'
            ),
            /*
            'newsletter' => array(
                'title' => $this->l('Newsletter'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printNewsIcon',
                'orderby' => false
            ),
            'optin' => array(
                'title' => $this->l('Opt-in'),
                'align' => 'text-center',
                'type' => 'bool',
                'callback' => 'printOptinIcon',
                'orderby' => false
            ),
             * 
             */
            'date_add' => array(
                'title' => $this->l('Registration'),
                'type' => 'date',
                'align' => 'text-right'
            ),
            'connect' => array(
                'title' => $this->l('Last visit'),
                'type' => 'datetime',
                'search' => false,
                'havingFilter' => true
            )
        ));

        $this->shopLinkType = 'shop';
        $this->shopShareDatas = Shop::SHARE_CUSTOMER;

        AdminController::__construct();

        $this->_select = '
        a.date_add, gl.name as title, (
            SELECT SUM(total_paid / conversion_rate)
            FROM '._DB_PREFIX_.'orders o
            WHERE o.id_customer = a.id_customer
            '.Shop::addSqlRestriction(Shop::SHARE_ORDER, 'o').'
            AND o.valid = 1
        ) as total_spent, (
            SELECT c.date_add FROM '._DB_PREFIX_.'guest g
            LEFT JOIN '._DB_PREFIX_.'connections c ON c.id_guest = g.id_guest
            WHERE g.id_customer = a.id_customer
            ORDER BY c.date_add DESC
            LIMIT 1
        ) as connect, a.address1, a.address2';

        // Check if we can add a customer
        if (Shop::isFeatureActive() && (Shop::getContext() == Shop::CONTEXT_ALL || Shop::getContext() == Shop::CONTEXT_GROUP)) {
            $this->can_add_customer = false;
        }

        self::$meaning_status = array(
            'open' => $this->l('Open'),
            'closed' => $this->l('Closed'),
            'pending1' => $this->l('Pending 1'),
            'pending2' => $this->l('Pending 2')
        );
        
        // lists to edit fields
        $this->context->smarty->assign('countriesList', $countriesList2);
        $this->context->smarty->assign('gendersList', $gendersList);
    }
    
    
    public function renderForm()
    {
        /** @var Customer $obj */
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $genders = Gender::getGenders();
        $list_genders = array();
        foreach ($genders as $key => $gender) {
            /** @var Gender $gender */
            $list_genders[$key]['id'] = 'gender_'.$gender->id;
            $list_genders[$key]['value'] = $gender->id;
            $list_genders[$key]['label'] = $gender->name;
        }

        $years = Tools::dateYears();
        $months = Tools::dateMonths();
        $days = Tools::dateDays();

        $groups = Group::getGroups($this->default_form_language, true);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l('Customer'),
                'icon' => 'icon-user'
            ),
            'input' => array(
                array(
                    'type' => 'radio',
                    'label' => $this->l('Social title'),
                    'name' => 'id_gender',
                    'required' => false,
                    'class' => 't',
                    'values' => $list_genders
                ),
                array(
                'type' => 'text',
                'label' => $this->l('Company'),
                'name' => 'company'
            ),
                array(
                    'type' => 'text',
                    'label' => $this->l('First name'),
                    'name' => 'firstname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Last name'),
                    'name' => 'lastname',
                    'required' => true,
                    'col' => '4',
                    'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Client number'),
                    'name' => 'client_number',
                    'required' => false,
                    'col' => '4',
                    //'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Accounts receivable number'),
                    'name' => 'receivable_number',
                    'required' => false,
                    'col' => '4',
                    //'hint' => $this->l('Invalid characters:').' 0-9!&lt;&gt;,;?=+()@#"°{}_$%:'
                ),
                
                array(
                    'type' => 'text',
                    'prefix' => '<i class="icon-envelope-o"></i>',
                    'label' => $this->l('Email address'),
                    'name' => 'email',
                    'col' => '4',
                    'required' => true,
                    'autocomplete' => false
                ),
                array(
                    'type' => 'password',
                    'label' => $this->l('Password'),
                    'name' => 'passwd',
                    'required' => ($obj->id ? false : true),
                    'col' => '4',
                    'hint' => ($obj->id ? $this->l('Leave this field blank if there\'s no change.') :
                        sprintf($this->l('Password should be at least %s characters long.'), Validate::PASSWORD_LENGTH))
                ),
                array(
                    'type' => 'birthday',
                    'label' => $this->l('Birthday'),
                    'name' => 'birthday',
                    'options' => array(
                        'days' => $days,
                        'months' => $months,
                        'years' => $years
                    )
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Enabled'),
                    'name' => 'active',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'hint' => $this->l('Enable or disable customer login.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Newsletter'),
                    'name' => 'newsletter',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'newsletter_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'newsletter_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'disabled' =>  (bool)!Configuration::get('PS_CUSTOMER_NWSL'),
                    'hint' => $this->l('This customer will receive your newsletter via email.')
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->l('Opt-in'),
                    'name' => 'optin',
                    'required' => false,
                    'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'optin_on',
                            'value' => 1,
                            'label' => $this->l('Enabled')
                        ),
                        array(
                            'id' => 'optin_off',
                            'value' => 0,
                            'label' => $this->l('Disabled')
                        )
                    ),
                    'disabled' =>  (bool)!Configuration::get('PS_CUSTOMER_OPTIN'),
                    'hint' => $this->l('This customer will receive your ads via email.')
                ),
                array(
				    'type' => 'text',
				    'label' => $this->l('Gründungsjahr'),
				    'name' => 'year_open',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('Gründungsjahr')
				),
				array(
				    'type' => 'text',
				    'label' => $this->l('Phone'),
				    'name' => 'phone',
				    'required' => false,
			//	    'required' =>  in_array('phone', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    //'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
				),
				array(
				    'type' => 'text',
				    'label' => $this->l('Mobile phone'),
				    'name' => 'phone_mobile',
				    'required' => false,
			//	    'required' =>  in_array('phone_mobile', $required_fields) || Configuration::get('PS_ONE_PHONE_AT_LEAST'),
                    'col' => '4',
                    //'hint' => Configuration::get('PS_ONE_PHONE_AT_LEAST') ? sprintf($this->l('You must register at least one phone number.')) : ''
				),
                array(
                    'type' => 'text',
                    'label' => $this->l('Fax'),
                    'name' => 'fax',
                    'col' => '4',
                    'required' => false,
                ),
                
				array(
				    'type' => 'text',
				    'label' => $this->l('Straße, Hausnummer'),
				    'name' => 'address1',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('Straße, Hausnummer')
				),
				array(
				    'type' => 'text',
				    'label' => $this->l('PLZ'),
				    'name' => 'postcode',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('PLZ')
				),
				array(
				    'type' => 'text',
				    'label' => $this->l('Ort'),
				    'name' => 'city',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('Ort')
				),
				array(
                    'type' => 'select',
                    'label' => $this->l('Country'),
                    'name' => 'id_country',
                    'required' => false,
                    'col' => '4',
                    'default_value' => (int) $this->context->country->id,
                                    'autoCompleteOff' => true,
                    'options' => array(
                        'query' => Country::getCountries($this->context->language->id),
                        'id' => 'id_country',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'select',
                    'autoCompleteOff' => true,
                    'label' => $this->l('Language'),
                    'name' => 'id_lang',
                    'required' => true,
                    'col' => '4',
                    'default_value' => (int) $this->context->language->id,
                    'options' => array(
                        'query' => Language::getLanguages(),
                        'id' => 'id_lang',
                        'name' => 'name'
                    ),
                ),
                
                array(
				    'type' => 'text',
				    'label' => $this->l('Referenzmarken'),
				    'name' => 'reference_brand',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('Referenzmarken')
				),
				// array(
				//     'type' => 'text',
				//     'label' => $this->l('Referenz Lieferanten'),
				//     'name' => 'reference_supplier',
				//     'required' => false,
				//     'col' => '4',
				//     'hint' => $this->l('Referenz Lieferanten')
				// ),
				array(

				    'type' => 'text',
				    'label' => $this->l('Verkaufsfläche Ihres Ladengeschäfts'),
				    'name' => 'size_shop',
				    'required' => false,
				    'col' => '4',
				    'hint' => $this->l('Verkaufsfläche Ihres Ladengeschäfts')
				),
            )
        );

        // if we add a customer via fancybox (ajax), it's a customer and he doesn't need to be added to the visitor and guest groups
        if (Tools::isSubmit('addcustomer') && Tools::isSubmit('submitFormAjax')) {
            $visitor_group = Configuration::get('PS_UNIDENTIFIED_GROUP');
            $guest_group = Configuration::get('PS_GUEST_GROUP');
            foreach ($groups as $key => $g) {
                if (in_array($g['id_group'], array($visitor_group, $guest_group))) {
                    unset($groups[$key]);
                }
            }
        }

        $this->fields_form['input'] = array_merge(
            $this->fields_form['input'],
            array(
                array(
                    'type' => 'group',
                    'label' => $this->l('Group access'),
                    'name' => 'groupBox',
                    'values' => $groups,
                    'required' => true,
                    'col' => '6',
                    'hint' => $this->l('Select all the groups that you would like to apply to this customer.')
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Default customer group'),
                    'name' => 'id_default_group',
                    'options' => array(
                        'query' => $groups,
                        'id' => 'id_group',
                        'name' => 'name'
                    ),
                    'col' => '4',
                    'hint' => array(
                        $this->l('This group will be the user\'s default group.'),
                        $this->l('Only the discount for the selected group will be applied to this customer.')
                    )
                )
            )
        );

        // if customer is a guest customer, password hasn't to be there
        if ($obj->id && ($obj->is_guest && $obj->id_default_group == Configuration::get('PS_GUEST_GROUP'))) {
            foreach ($this->fields_form['input'] as $k => $field) {
                if ($field['type'] == 'password') {
                    array_splice($this->fields_form['input'], $k, 1);
                }
            }
        }

        if (Configuration::get('PS_B2B_ENABLE')) {
            $risks = Risk::getRisks();

            $list_risks = array();
            foreach ($risks as $key => $risk) {
                /** @var Risk $risk */
                $list_risks[$key]['id_risk'] = (int)$risk->id;
                $list_risks[$key]['name'] = $risk->name;
            }

            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Umsatzsteuer Id Nummer'),
                'name' => 'siret'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Handelsregisternummer / Ort'),
                'name' => 'ape'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Website'),
                'name' => 'website'
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Allowed outstanding amount'),
                'name' => 'outstanding_allow_amount',
                'hint' => $this->l('Valid characters:').' 0-9',
                'suffix' => $this->context->currency->sign
            );
            $this->fields_form['input'][] = array(
                'type' => 'text',
                'label' => $this->l('Maximum number of payment days'),
                'name' => 'max_payment_days',
                'hint' => $this->l('Valid characters:').' 0-9'
            );
            $this->fields_form['input'][] = array(
                'type' => 'select',
                'label' => $this->l('Risk rating'),
                'name' => 'id_risk',
                'required' => false,
                'class' => 't',
                'options' => array(
                    'query' => $list_risks,
                    'id' => 'id_risk',
                    'name' => 'name'
                ),
            );
        }

        $this->fields_form['submit'] = array(
            'title' => $this->l('Save'),
        );

        $birthday = explode('-', $this->getFieldValue($obj, 'birthday'));

        $this->fields_value = array(
            'years' => $this->getFieldValue($obj, 'birthday') ? $birthday[0] : 0,
            'months' => $this->getFieldValue($obj, 'birthday') ? $birthday[1] : 0,
            'days' => $this->getFieldValue($obj, 'birthday') ? $birthday[2] : 0,
        );

        // Added values of object Group
        if (!Validate::isUnsignedId($obj->id)) {
            $customer_groups = array();
        } else {
            $customer_groups = $obj->getGroups();
        }
        $customer_groups_ids = array();
        if (is_array($customer_groups)) {
            foreach ($customer_groups as $customer_group) {
                $customer_groups_ids[] = $customer_group;
            }
        }

        // if empty $carrier_groups_ids : object creation : we set the default groups
        if (empty($customer_groups_ids)) {
            $preselected = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'), Configuration::get('PS_CUSTOMER_GROUP'));
            $customer_groups_ids = array_merge($customer_groups_ids, $preselected);
        }

        foreach ($groups as $group) {
            $this->fields_value['groupBox_'.$group['id_group']] =
                Tools::getValue('groupBox_'.$group['id_group'], in_array($group['id_group'], $customer_groups_ids));
        }

        return AdminController::renderForm();
    }
    
   public function processAdd()
   {
       $customer = parent::processAdd();
       // if customer added create address based on entered data
       if($customer && count($this->errors)==0)
       {
           $address = new Address();
           $address->id_customer = $customer->id;
           $address->firstname = $customer->firstname;
           $address->lastname = $customer->lastname;
           $address->address1 = $customer->address1;
           $address->address2 = $customer->address2;
           $address->postcode = $customer->postcode;
           $address->city = $customer->city;
           $address->id_country = $customer->id_country;
           $address->phone = $customer->phone;
           $address->phone_mobile = $customer->phone_mobile;
           $address->alias = $this->l('My address');
           $address->company = $customer->company;
           $address->vat_number = $customer->siret;
           $address->dni = $customer->siret;
           $address->save();
       }
       return $customer;
   }
   
   
   public function renderView()
   {
        /** @var Customer $customer */
        if (!($customer = $this->loadObject())) {
            return;
        }

        $this->context->customer = $customer;
        $gender = new Gender($customer->id_gender, $this->context->language->id);
        $gender_image = $gender->getImage();

        $customer_stats = $customer->getStats();
        $sql = 'SELECT SUM(total_paid) FROM '._DB_PREFIX_.'orders WHERE id_customer = %d AND valid = 1';
        if ($total_customer = Db::getInstance()->getValue(sprintf($sql, $customer->id))) {
            $sql = 'SELECT SQL_CALC_FOUND_ROWS COUNT(*) FROM '._DB_PREFIX_.'orders WHERE valid = 1 AND id_customer != '.(int)$customer->id.' GROUP BY id_customer HAVING SUM(total_paid) > %d';
            Db::getInstance()->getValue(sprintf($sql, (int)$total_customer));
            $count_better_customers = (int)Db::getInstance()->getValue('SELECT FOUND_ROWS()') + 1;
        } else {
            $count_better_customers = '-';
        }

        $orders = Order::getCustomerOrders($customer->id, true);
        $total_orders = count($orders);
        for ($i = 0; $i < $total_orders; $i++) {
            $orders[$i]['total_paid_real_not_formated'] = $orders[$i]['total_paid'];
            $orders[$i]['total_paid_real'] = Tools::displayPrice($orders[$i]['total_paid'], new Currency((int)$orders[$i]['id_currency']));
        }

        $messages = CustomerThread::getCustomerMessages((int)$customer->id);

        $total_messages = count($messages);
        for ($i = 0; $i < $total_messages; $i++) {
            $messages[$i]['message'] = substr(strip_tags(html_entity_decode($messages[$i]['message'], ENT_NOQUOTES, 'UTF-8')), 0, 75);
            $messages[$i]['date_add'] = Tools::displayDate($messages[$i]['date_add'], null, true);
            if (isset(self::$meaning_status[$messages[$i]['status']])) {
                $messages[$i]['status'] = self::$meaning_status[$messages[$i]['status']];
            }
        }

        $groups = $customer->getGroups();
        $total_groups = count($groups);
        for ($i = 0; $i < $total_groups; $i++) {
            $group = new Group($groups[$i]);
            $groups[$i] = array();
            $groups[$i]['id_group'] = $group->id;
            $groups[$i]['name'] = $group->name[$this->default_form_language];
        }

        $total_ok = 0;
        $orders_ok = array();
        $orders_ko = array();
        foreach ($orders as $order) {
            if (!isset($order['order_state'])) {
                $order['order_state'] = $this->l('There is no status defined for this order.');
            }

            if ($order['valid']) {
                $orders_ok[] = $order;
                $total_ok += $order['total_paid_real_not_formated'];
            } else {
                $orders_ko[] = $order;
            }
        }

        $products = $customer->getBoughtProducts(true);
        foreach($products as &$boughtProduct)
        {
            if ($boughtProduct['id_image'])
            {
                $boughtProduct['image_url'] = $this->context->link->getImageLink($boughtProduct['product_name'], $boughtProduct['id_image'], 'cart_default');
            }
        }

        $carts = Cart::getCustomerCarts($customer->id);
        $total_carts = count($carts);
        for ($i = 0; $i < $total_carts; $i++) {
            $cart = new Cart((int)$carts[$i]['id_cart']);
            $this->context->cart = $cart;
            $currency = new Currency((int)$carts[$i]['id_currency']);
            $this->context->currency = $currency;
            $summary = $cart->getSummaryDetails();
            $carrier = new Carrier((int)$carts[$i]['id_carrier']);
            $carts[$i]['id_cart'] = sprintf('%06d', $carts[$i]['id_cart']);
            $carts[$i]['date_add'] = Tools::displayDate($carts[$i]['date_add'], null, true);
            $carts[$i]['total_price'] = Tools::displayPrice($summary['total_price'], $currency);
            $carts[$i]['name'] = $carrier->name;
        }

        $this->context->currency = Currency::getDefaultCurrency();

        $sql = 'SELECT DISTINCT cp.id_product, c.id_cart, c.id_shop, cp.id_shop AS cp_id_shop, i.id_image, product_supplier_reference
				FROM '._DB_PREFIX_.'cart_product cp
				JOIN '._DB_PREFIX_.'cart c ON (c.id_cart = cp.id_cart)
				JOIN '._DB_PREFIX_.'product p ON (cp.id_product = p.id_product)
                                left JOIN '._DB_PREFIX_.'image i ON (i.id_product = p.id_product and cover=1)
                                left JOIN '._DB_PREFIX_.'product_supplier ps ON (ps.id_product = cp.id_product and ps.id_product_attribute = cp.id_product_attribute)    
				WHERE c.id_customer = '.(int)$customer->id.'
					AND NOT EXISTS (
							SELECT 1
							FROM '._DB_PREFIX_.'orders o
							JOIN '._DB_PREFIX_.'order_detail od ON (o.id_order = od.id_order)
							WHERE product_id = cp.id_product AND o.valid = 1 AND o.id_customer = '.(int)$customer->id.'
						)';
        $interested = Db::getInstance()->executeS($sql);
        $total_interested = count($interested);
        for ($i = 0; $i < $total_interested; $i++) {
            $product = new Product($interested[$i]['id_product'], false, $this->default_form_language, $interested[$i]['id_shop']);
            if (!Validate::isLoadedObject($product)) {
                continue;
            }
            $interested[$i]['url'] = $this->context->link->getProductLink(
                $product->id,
                $product->link_rewrite,
                Category::getLinkRewrite($product->id_category_default, $this->default_form_language),
                null,
                null,
                $interested[$i]['cp_id_shop']
            );
            $interested[$i]['id'] = (int)$product->id;
            $interested[$i]['name'] = Tools::htmlentitiesUTF8($product->name);
            $interested[$i]['image_url'] = $this->context->link->getImageLink($product->name, $interested[$i]['id_image'], 'cart_default');
        }

        $emails = $customer->getLastEmails();

        $connections = $customer->getLastConnections();
        if (!is_array($connections)) {
            $connections = array();
        }
        $total_connections = count($connections);
        for ($i = 0; $i < $total_connections; $i++) {
            $connections[$i]['http_referer'] = $connections[$i]['http_referer'] ? preg_replace('/^www./', '', parse_url($connections[$i]['http_referer'], PHP_URL_HOST)) : $this->l('Direct link');
        }

        $referrers = Referrer::getReferrers($customer->id);
        $total_referrers = count($referrers);
        for ($i = 0; $i < $total_referrers; $i++) {
            $referrers[$i]['date_add'] = Tools::displayDate($referrers[$i]['date_add'], null, true);
        }

        $customerLanguage = new Language($customer->id_lang);
        $shop = new Shop($customer->id_shop);
        $this->tpl_view_vars = array(
            'customer' => $customer,
            'gender' => $gender,
            'gender_image' => $gender_image,
            // General information of the customer
            'registration_date' => Tools::displayDate($customer->date_add, null, true),
            'customer_stats' => $customer_stats,
            'last_visit' => Tools::displayDate($customer_stats['last_visit'], null, true),
            'count_better_customers' => $count_better_customers,
            'shop_is_feature_active' => Shop::isFeatureActive(),
            'name_shop' => $shop->name,
            'customer_birthday' => Tools::displayDate($customer->birthday),
            'last_update' => Tools::displayDate($customer->date_upd, null, true),
            'customer_exists' => Customer::customerExists($customer->email),
            'id_lang' => $customer->id_lang,
            'customerLanguage' => $customerLanguage,
            // Add a Private note
            'customer_note' => Tools::htmlentitiesUTF8($customer->note),
            // Messages
            'messages' => $messages,
            // Groups
            'groups' => $groups,
            // Orders
            'orders' => $orders,
            'orders_ok' => $orders_ok,
            'orders_ko' => $orders_ko,
            'total_ok' => Tools::displayPrice($total_ok, $this->context->currency->id),
            // Products
            'products' => $products,
            // Addresses
            'addresses' => $customer->getAddresses($this->default_form_language),
            // Discounts
            'discounts' => CartRule::getCustomerCartRules($this->default_form_language, $customer->id, false, false),
            // Carts
            'carts' => $carts,
            // Interested
            'interested' => $interested,
            // Emails
            'emails' => $emails,
            // Connections
            'connections' => $connections,
            // Referrers
            'referrers' => $referrers,
            'show_toolbar' => true
        );

        return AdminController::renderView();
    }
    
    
    public function showCountry($countryId, $row)
    {
         return $this->countriesList[$countryId];
    }
    
    
    public function setMedia()
    {
        parent::setMedia();

        $this->addJS(_PS_JS_DIR_.'admin/editable_field.js');
        $this->addJS(_PS_JS_DIR_.'admin/customers_list.js');
    }
    
    
    function ajaxProcessEditSimpleTxtField()
    {
        try{
            $customer = new Customer(intval($_POST['tr__id']));
            if (!Validate::isLoadedObject($customer))
            {
                throw new Exception($this->l('Customer not found'));
            }
            $field = $_REQUEST['field'];
            if (!property_exists($customer, $field))
            {
                throw new Exception($this->l('Invalid field name'));
            }
            
            $customer->$field = $_POST[$field];
            $customer->update();
        
            die(json_encode(['error'=>0]));
        }
        catch(Exception $e)
        {
            die(json_encode(['error'=>$e->getMessage()]));
        }
    }
    
    
    public function postProcess()
    {
        if (Tools::isSubmit('submitBulkexcelListcustomer'))
        {
            $customerIds = Tools::getValue('customerBox', []);
            if (count($customerIds))
            {
                array_walk($customerIds, function(&$item, $key){ $item = intval($item); });
                // setup xls
                require_once _PS_TOOL_DIR_ . 'php_excel/PHPExcel.php';
                require_once _PS_TOOL_DIR_ . 'php_excel/PHPExcel/Writer/Excel5.php';
                $xls = new PHPExcel();
                $xls->setActiveSheetIndex(0);
                $sheet = $xls->getActiveSheet();
                $sheet->setTitle($this->l('Kunden'));
                // borders around cells
                $style1 = ['borders' => [
                        'outline' => [
                            'style' => PHPExcel_Style_Border::BORDER_THIN,
                            'color' => ['rgb' => '000000']
                        ]],
                        //'alignment'=>['wrap' => true]
                ];
                
                $sheet->getColumnDimension('B')->setAutoSize(true); 
                $sheet->getColumnDimension('C')->setAutoSize(true); 
                $sheet->getColumnDimension('D')->setAutoSize(true); 
                $sheet->getColumnDimension('E')->setAutoSize(true); 
                $sheet->getColumnDimension('F')->setAutoSize(true); 
                $sheet->getColumnDimension('B')->setAutoSize(true); 
                $sheet->getColumnDimension('G')->setAutoSize(true); 
                $sheet->getColumnDimension('H')->setAutoSize(true); 
                $sheet->getColumnDimension('J')->setAutoSize(true); 
                $sheet->getColumnDimension('K')->setAutoSize(true); 
                $sheet->getColumnDimension('M')->setAutoSize(true); 
                $sheet->getColumnDimension('L')->setAutoSize(true);

                // add header ->getStyle()->applyFromArray($style1)
                $sheet->setCellValue('A1', $this->l('Social title'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('B1', $this->l('Firstname'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('C1', $this->l('Lastname'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('D1', $this->l('Company'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('E1', $this->l('Address 1'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('F1', $this->l('Address 2'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('G1', $this->l('Postcode'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('H1', $this->l('City'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('I1', $this->l('Country'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('J1', $this->l('Phone'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('K1', $this->l('Phone mobile'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('L1', $this->l('Email'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('M1', $this->l('Note'), true)->getStyle()->applyFromArray($style1);
                $sheet->setCellValue('N1', $this->l('Active'), true)->getStyle()->applyFromArray($style1);
                
                $db = Db::getInstance();
                $sqlRes = $db->executeS('select gl.name as gender, firstname, lastname, company, address1, address2, postcode, city, cl.name as country,'
                        . 'phone, phone_mobile, email, note, active from '._DB_PREFIX_.'customer c left join '._DB_PREFIX_.'country_lang cl on '
                        . 'c.id_country=cl.id_country and cl.id_lang='.$this->context->language->id.' left join '._DB_PREFIX_.'gender_lang gl on '
                        . 'gl.id_gender=c.id_gender and gl.id_lang='.$this->context->language->id.' where c.id_customer in ('.
                        implode(',', $customerIds).')', false);
                $curRow = 2;
                while($row=$db->nextRow($sqlRes))
                {
                    $sheet->setCellValue('A'.$curRow, $row['gender'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('B'.$curRow, $row['firstname'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('C'.$curRow, $row['lastname'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('D'.$curRow, $row['company'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('E'.$curRow, $row['address1'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('F'.$curRow, $row['address2'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('G'.$curRow, $row['postcode'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('H'.$curRow, $row['city'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('I'.$curRow, $row['country'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('J'.$curRow, $row['phone'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('K'.$curRow, $row['phone_mobile'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('L'.$curRow, $row['email'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('M'.$curRow, $row['note'], true)->getStyle()->applyFromArray($style1);
                    $sheet->setCellValue('N'.$curRow, $row['active']?$this->l('Yes'):$this->l('No'), true)->getStyle()->applyFromArray($style1);
                    $curRow++;
                }
                // output to browser
                header("Expires: Mon, 1 Apr 1974 05:00:00 GMT");
                header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
                header("Cache-Control: no-cache, must-revalidate");
                header("Pragma: no-cache");
                header("Content-type: application/vnd.ms-excel");
                header("Content-Disposition: attachment; filename=kunden.xls");

                $objWriter = new PHPExcel_Writer_Excel5($xls);
                $objWriter->save('php://output');
                exit;
            }
            else
            {
                echo Tools::displayError('No customers selected');
            }
            
        }
        parent::postProcess();
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
    
    public function processFilter()
    {
        parent::processFilter();
        $searchChar = null;

        if( !empty($this->context->cookie->customers_search_char) ){
            $searchChar = $this->context->cookie->customers_search_char;
        }
        
        if( !empty($_POST['search_char']) ){
            if( $_POST['search_char'] == '-' ){
                $searchChar = null;
                unset($this->context->cookie->customers_search_char);
            }
            else{
                $searchChar = $_POST['search_char'];
            }
        }

        if( !empty($searchChar) ){
            $this->_filter .= ' AND a.company LIKE "'. pSQL($searchChar) .'%" ';
            $this->context->cookie->customers_search_char = $searchChar;
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
}
