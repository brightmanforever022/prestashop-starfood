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

class Msss_client extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'msss_client';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Wheelronix';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('MSSS Client');
        $this->description = $this->l('Multi shop stock synchronizer client');
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        Configuration::updateValue('MSSS_CLIENT_LIVE_MODE', false);

        include(dirname(__FILE__).'/sql/install.php');

        return parent::install() &&
           // $this->registerHook('actionProductCancel') &&
            $this->registerHook('actionValidateOrder'); // actionOrderStatusPostUpdate
    }

    public function uninstall()
    {
        Configuration::deleteByName('MSSS_CLIENT_LIVE_MODE');

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
        if (((bool)Tools::isSubmit('submitMsss_clientModule')) == true) {
            $this->postProcess();
        }

        $this->context->smarty->assign('module_dir', $this->_path);

        $output = $this->renderForm(); //$this->context->smarty->fetch($this->local_path.'views/templates/admin/configure.tpl');

        return $output;
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
        $helper->submit_action = 'submitMsss_clientModule';
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
                        'type' => 'text',
                        'name' => 'MSSS_CLIENT_SOURCE_ID',
                        'label' => $this->l('Shop id'),
                        'desc' => $this->l('Id of this shop on server'),
                        'size' => 15
                    ),
                    array(
                        //'col' => 3,
                        'type' => 'text',
                        'prefix' => '<i class="icon icon-envelope"></i>',
                        'desc' => $this->l('Enter url to which notifications to server will be sent'),
                        'name' => 'MSSS_CLIENT_SERVER_NOTIFICATION_URL',
                        'label' => $this->l('Server notification url'),
                        'size' => 30
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'MSSS_CLIENT_SECRET',
                        'label' => $this->l('Secret'),
                        'desc' => $this->l('Secret is used to encrypt and decrypt messages between client and server'),
                        'size' => 15
                    ),
                    array(
                        'type' => 'text',
                        'name' => 'MSSS_CLIENT_DEBUG_EMAIL',
                        'label' => $this->l('Debug email'),
                        'desc' => $this->l('debug message from module will be sent to this and to admin email. If this field is empty, '
                                . 'then meesages witll be sent to admin email only.'),
                        'size' => 15
                    ),
                    array(
                        'type' => 'checkbox',
                        'name' => 'MSSS_CLIENT_DONT_SEND_EMAIL_TO_SHOP_ADMIN',
                        //'label' => $this->l('Don\'t send warnings to shop owner'),
                        'desc' => $this->l('If checked, then debug messages will only be sent to debug email above (if it is set).'),
                        'values' => [
                            'query' => [['id'=>'checkbox', 'name'=>$this->l('Don\'t send warnings to shop owner'), 'val'=>1]],
                            'id'=>'id',
                            'name'=>'name'
                        ]
                    ),
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
            'MSSS_CLIENT_SERVER_NOTIFICATION_URL' => Configuration::get('MSSS_CLIENT_SERVER_NOTIFICATION_URL', 
                    'http://dmitri.wheel/vipdress.de1/admin123/msss_notification.php'),
            'MSSS_CLIENT_SECRET' => Configuration::get('MSSS_CLIENT_SECRET', null),
            'MSSS_CLIENT_SOURCE_ID' => Configuration::get('MSSS_CLIENT_SOURCE_ID', null),
            'MSSS_CLIENT_DEBUG_EMAIL' => Configuration::get('MSSS_CLIENT_DEBUG_EMAIL', null),
            'MSSS_CLIENT_DONT_SEND_EMAIL_TO_SHOP_ADMIN_checkbox' => Configuration::get('MSSS_CLIENT_DONT_SEND_EMAIL_TO_SHOP_ADMIN', null),
        );
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
        $form_values = $this->getConfigFormValues();

        foreach (array_keys($form_values) as $key) {
            Configuration::updateValue(str_replace('_checkbox', '', $key), Tools::getValue($key));
        }
    }


    public function hookActionProductCancel($params)
    {
        // report is done in OrderController::reinjectQuantity()
        return;
        if ($this->active)
        {
            include_once 'lib/MSSSClientStockUpdater.php';
            
            // report stock update 
            MSSSClientStockUpdater::scheduleStockUpdate($params['sku'], $params['cancelQty']);
        }
    }

    
    public function hookActionValidateOrder($params)
    {
        if ($this->active)
        {
            include_once 'lib/MSSSClientStockUpdater.php';
            // report stock update 
            $orderDetails = $params['order']->getOrderDetailList();
            
            foreach($orderDetails as $orderDetail)
            {
                MSSSClientStockUpdater::scheduleStockUpdate($orderDetail['product_supplier_reference'], 
                        -$orderDetail['product_quantity']);
            }
        }
    }
    
    
    public function scheduleStockUpdateById($productId, $combinationId, $deltaQty)
    {
        if ($this->active)
        {
            include_once 'lib/MSSSClientStockUpdater.php';
            // report stock update 
            MSSSClientStockUpdater::scheduleStockUpdateById($productId, $combinationId, $deltaQty);
        }
    }
}
