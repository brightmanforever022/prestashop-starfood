<?php
/**
 * OrderEdit
 *
 * @category  Module
 * @author    silbersaiten <info@silbersaiten.de>
 * @support   silbersaiten <support@silbersaiten.de>
 * @copyright 2016 silbersaiten
 * @version   1.2.0
 * @link      http://www.silbersaiten.de
 * @license   See joined file licence.txt
 */

class AdminOrderEditController extends AdminOrdersControllerCore
{

    public function initToolbar()
    {
        $res = parent::initToolbar();
        unset($this->toolbar_btn['new']);
        return $res;
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();
        unset($this->page_header_toolbar_btn['new_order']);
    }

    public function createTemplate($tpl_name)
    {
        if (!class_exists('OrderEdit', false)) {
            require_once(_PS_MODULE_DIR_.'orderedit/orderedit.php');

            new OrderEdit();
        }

        $tpl_path = OrderEdit::getTplPath();

        if (file_exists($tpl_path.$tpl_name)) {
            return $this->context->smarty->createTemplate($tpl_path.$tpl_name, $this->context->smarty);
        } else {
            return parent::createTemplate($tpl_name);
        }
    }

    public function setMedia()
    {
        parent::setMedia();

        $this->addCSS(_MODULE_DIR_.'orderedit/views/css/style.css');

        if ($this->tabAccess['edit'] == 1) {
            $this->addJqueryPlugin(array('autocomplete', 'fancybox', 'typewatch'));
            if ($this->display == 'edit' || $this->display == 'view') {
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/timepicker/jquery-ui-timepicker-addon.js');
                $this->addCSS(_MODULE_DIR_.'orderedit/views/css/jquery-ui-timepicker-addon.css');
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/timepicker/jquery-ui-sliderAccess.js');
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/editor.js');

                //$this->assignDownloadProducts();
            } elseif ($this->display == null) {
                $this->addJS(_MODULE_DIR_.'orderedit/views/js/list.js');
            }

            Hook::exec('orderEditHeader');
        }
    }

    protected function l($string, $class = 'AdminTab', $addslashes = false, $htmlentities = true)
    {
        unset($class);
        unset($addslashes);
        unset($htmlentities);
        return Translate::getModuleTranslation('orderedit', $string, get_class($this));
    }

    public function renderView()
    {
        parent::renderView();

        //get saved custom tax rate of product from table, not from tax calculator
        $details = OrderDetail::getList((int)Tools::getValue('id_order'));
        foreach ($details as $detail) {
            if (!(($detail['total_price_tax_incl'] > $detail['total_price_tax_incl']) && $detail['tax_rate'] == 0)) {
                $this->tpl_view_vars['products'][$detail['id_order_detail']]['tax_rate'] = $detail['tax_rate'];
            }

            $this->tpl_view_vars['products'][$detail['id_order_detail']]['id_tax'] = (int)Db::getInstance()->getValue(
                'SELECT id_tax
                FROM `'._DB_PREFIX_.'order_detail_tax`
                WHERE id_order_detail='.(int)$detail['id_order_detail']
            );
        }

        if (property_exists($this->context->smarty, 'inheritance_merge_compiled_includes')) {
            $this->context->smarty->inheritance_merge_compiled_includes = false;
        }

        $helper = new HelperView($this);

        $this->context->smarty->assign(array(
            'iem' => (int)$this->context->cookie->id_employee,
            'iemp' => $this->context->cookie->passwd,
            'orderedit_tpl_dir' => _PS_MODULE_DIR_.'/orderedit/views/templates/admin/_configure/order_edit',
            'ajax_path' => _MODULE_DIR_.'orderedit/ajax.php',
            'ORDEREDIT_HOOK_BEFORE_PRODUCT_LIST' => Hook::exec('orderEditBeforeProductList'),
            'ORDEREDIT_HOOK_TOP' => Hook::exec('orderEditTop'),
            'carriers' => Carrier::getCarriers(
                $this->context->language->id,
                true,
                false,
                false,
                null,
                Carrier::PS_CARRIERS_AND_CARRIER_MODULES_NEED_RANGE
            ),
            'taxes' => Tax::getTaxes($this->context->language->id, true)
        ));

        require_once(_PS_MODULE_DIR_.'orderedit/orderedit.php');

        $helper->module = new OrderEdit();
        $this->setHelperDisplay($helper);
        $helper->tpl_vars = $this->tpl_view_vars;

        if (!is_null($this->base_tpl_view)) {
            $helper->base_tpl = $this->base_tpl_view;
        }

        $view = $helper->generateView();

        return $view;
    }

    public function renderForm()
    {
        if (Tools::getIsset('updateorder')) {
            return $this->renderView();
        }

        if (!$this->loadObject(true)) {
            return;
        }

        return parent::renderForm();
    }
}
