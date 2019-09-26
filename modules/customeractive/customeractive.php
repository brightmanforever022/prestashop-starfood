<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

class Customeractive extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'customeractive';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'worldopen';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Validate new customer');
        $this->description = $this->l('Modules active new customer register in site, send mail when register and account are active');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('CUSTOMERACTIVE_LIVE_MODE', false);
        Configuration::updateValue('CUSTOMERACTIVE_GUEST_MODE', false);
        $id_group = 1;
        $group = new Group($id_group);
        $group->show_prices = false;
        $group->update();
        $id_group = 2;
        $group = new Group($id_group);
        $group->show_prices = false;
        $group->update();
        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('actionCustomerAccountAdd');
    }

    public function uninstall()
    {
     //   Configuration::deleteByName('CUSTOMERACTIVE_VISTOR_MODE');
       // Configuration::deleteByName('CUSTOMERACTIVE_GUEST_MODE');
        $id_group = 1;
        $group = new Group($id_group);
        $group->show_prices = true;
        $group->update();
        $id_group = 2;
        $group = new Group($id_group);
        $group->show_prices = true;
        $group->update();
        include(dirname(__FILE__).'/sql/uninstall.php');

        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        /**
         * If values have been submitted in the form, process.
         */
        if (((bool)Tools::isSubmit('submitCustomeractiveModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output.$this->renderForm();
    }

    /**
     * Create the form that will be displayed in the configuration of your module.
     */
    protected function renderForm()
    {
        $helper = new HelperForm();

        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $this->context->language->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG', 0);

        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitCustomeractiveModule';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false)
            .'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFormValues(), /* Add values for your inputs */
            'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );

        return $helper->generateForm(array($this->getConfigForm()));
    }

    /**
     * Create the structure of your form.
     */
    protected function getConfigForm()
    {
        return array(
            'form' => array(
                'legend' => array(
                'title' => $this->l('Settings'),
                'icon' => 'icon-cogs',
                ),
                'input' => array(
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Visitor show price'),
                        'name' => 'CUSTOMERACTIVE_VISTOR_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('show price for vistor or not'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hidden')
                            )
                        ),
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Show price Guest'),
                        'name' => 'CUSTOMERACTIVE_GUEST_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('show price for guest or not'),
                        'values' => array(
                            array(
                                'id' => 'active_on',
                                'value' => true,
                                'label' => $this->l('Show')
                            ),
                            array(
                                'id' => 'active_off',
                                'value' => false,
                                'label' => $this->l('Hidden')
                            )
                        ),
                    )
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );
    }

    /**
     * Set values for the inputs.
     */
    protected function getConfigFormValues()
    {
        return array(
            'CUSTOMERACTIVE_GUEST_MODE' => Configuration::get('CUSTOMERACTIVE_GUEST_MODE', false),
            'CUSTOMERACTIVE_VISTOR_MODE' => Configuration::get('CUSTOMERACTIVE_VISTOR_MODE', false)
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();
        
        
        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue($key, Tools::getValue($key));
            if($key=='CUSTOMERACTIVE_GUEST_MODE')
            {
                $id_group = 2;
                $group = new Group($id_group);
                $group->show_prices = (bool)Tools::getValue($key);
                $group->update();
            }
            else if($key=='CUSTOMERACTIVE_VISTOR_MODE')
            {
                $id_group = 1;
                $group = new Group($id_group);
                $group->show_prices = (bool)Tools::getValue($key);
                $group->update();
            }
        }
    }

    /**
    * Add the CSS & JavaScript files you want to be loaded in the BO.
    */
    public function hookBackOfficeHeader()
    {
        if (Tools::getValue('module_name') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
        $configuration = Configuration::getMultiple(
			array(
				'MA_LAST_QTIES',
				'PS_STOCK_MANAGEMENT',
				'PS_SHOP_EMAIL',
				'PS_SHOP_NAME'
			), null, null, $this->context->shop->id
		);
        if(Tools::getIsset('statuscustomer') && Tools::getIsset('id_customer')){
                $customer = New Customer((int)Tools::getValue('id_customer'));
                if($customer->active==0){
                    $template_vars = array(
            				'{firstname}' => $customer->firstname,
            				'{lastname}' => $customer->lastname,
                            '{email}'  => $customer->email,
                            '{passwd}' => Tools::getValue('passwd'),
            				'{shop_name}' => $configuration['PS_SHOP_NAME']
            			);
                        $this->sendMail(
                            $customer->id_lang,
                            'active_account',
                            ($customer->id_lang == 1 ? 'Your Account is actived - '.$configuration['PS_SHOP_NAME'] : 'Ihr Kundenkonto auf '.$configuration['PS_SHOP_NAME'].' wurde freigeschaltet'),
                            $template_vars,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname,
                            $configuration
                        );
                  }   
        }
        if(Tools::isSubmit('submitBulkenableSelectioncustomer')){
             $id_customers = Tools::getValue('customerBox');
            if(count($id_customers)>0)
            {
                 foreach($id_customers as $id_customer){
                    $customer = new Customer($id_customer);
                    if($customer->active==0)
                    {
                        $template_vars = array(
            				'{firstname}' => $customer->firstname,
            				'{lastname}' => $customer->lastname,
                            '{email}'  => $customer->email,
                            '{passwd}' => Tools::getValue('passwd'),
            				'{shop_name}' => $configuration['PS_SHOP_NAME']
            			);
                        $this->sendMail(
                            $customer->id_lang,
                            'active_account',
                            ($customer->id_lang == 1 ? 'Your Account is actived - '.$configuration['PS_SHOP_NAME'] : 'Ihr Kundenkonto auf'.$configuration['PS_SHOP_NAME'].' wurde freigeschaltet'),
                            $template_vars,
                            $customer->email,
                            $customer->firstname.' '.$customer->lastname,
                            $configuration
                        );
                    }   
                 }
           }
        }
       // exit;
    }
    public static function sendMail($id_lang,$template_theme,$subject,$template_vars,$to_mail,$to_name,$configuration){
        return (Mail::Send(
						$id_lang,
						$template_theme,
						$subject,
						$template_vars,
						$to_mail,
						$to_name,
						(string)$configuration['PS_SHOP_EMAIL'],
						(string)$configuration['PS_SHOP_NAME'],
						null,
						null,
						dirname(__FILE__).'/mails/',
						false,
						$id_shop
					));
    }

    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
       if(!$this->context->customer->logged){
            $this->context->controller->addJS($this->_path.'/views/js/front.js');
            $this->context->controller->addCSS($this->_path.'/views/css/front.css');
       }
    }

    public function hookActionCustomerAccountAdd($params)
    {
        $params['newCustomer']->logout();
        
        // send email to admin
        $templateVars = array(
            '{firstname}' => $params['newCustomer']->firstname,
            '{lastname}' => $params['newCustomer']->lastname,
            '{email}' => $params['newCustomer']->email,
            '{passwd}' => '*******'
        );
        Mail::Send(
                $this->context->language->id, 'new_customer_registered', Mail::l('New customer registered'), $templateVars, 
                Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME')
        );

        // send email to customer
        Mail::Send(
                $this->context->language->id, 'register_confirm', 
                sprintf(Mail::l('Registrierung auf %s erfolgreich', $this->context->lanugage->id), $configuration['PS_SHOP_NAME']), 
                $templateVars, $params['newCustomer']->email, $params['newCustomer']->firstname . ' ' . $params['newCustomer']->lastname,
                Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'),
                null, null, dirname(__FILE__).'/mails/'
        );

        // redirect to registration success page
        Tools::redirect($this->context->link->getModuleLink('customeractive', 'successfull'));
    }

}
