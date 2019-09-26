<?php
/*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2015 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @property OrderState $object
 */
class AdminPaymentStatusesControllerCore extends AdminController
{
    public function __construct()
    {
        $this->bootstrap = true;
        //$this->explicitSelect = true;
        $this->table = 'order_payment_status';
        $this->className = 'OrderPaymentStatus';
        $this->deleted = false;
        $this->colorOnBackground = false;
        $this->bulk_actions = array('delete' => array('text' => $this->l('Delete selected'), 'confirm' => $this->l('Delete selected items?')));
        $this->context = Context::getContext();
        $this->multishop_context = Shop::CONTEXT_ALL;
        $this->imageType = 'gif';
        $this->fieldImageSettings = array(
            'name' => 'icon',
            'dir' => 'os'
        );
        parent::__construct();
    }

   
    /**
     * init all variables to render the order status list
     */
    protected function initOrderStatutsList()
    {
        $this->fields_list = array(
            'id_order_payment_status' => array(
                'title' => $this->l('ID'),
                'align' => 'text-center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->l('Name'),
                'width' => 'auto',
                'color' => 'color'
            ),
            
        );
    }

    
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_order_state'] = array(
                'href' => self::$currentIndex.'&addorder_payment_status&token='.$this->token,
                'desc' => $this->l('Add new order payment status', null, null, false),
                'icon' => 'process-icon-new'
            );
        }

        parent::initPageHeaderToolbar();
    }

    /**
     * Function used to render the list to display for this controller
     */
    public function renderList()
    {
        //init and render the first list
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        //$this->addRowActionSkipList('delete', range(1, 14));
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'confirm' => $this->l('Delete selected items?'),
                'icon' => 'icon-trash',
            )
        );
        $this->initOrderStatutsList();
        $lists = parent::renderList();

        
        // call postProcess() to take care of actions and filters
        $this->postProcess();
//        $this->toolbar_title = $this->l('Return statuses');

        parent::initToolbar();

        return $lists;
    }

    
    public function renderForm()
    {
        $this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->l('Order payment status'),
                'icon' => 'icon-time'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Status name'),
                    'name' => 'name',
                    'required' => true,
                    'hint' => array(
                        $this->l('Order status (e.g. \'Pending\').'),
                        $this->l('Invalid characters: numbers and').' !<>,;?=+()@#"{}_$%:'
                    )
                ),
                array(
                    'type' => 'color',
                    'label' => $this->l('Color'),
                    'name' => 'color',
                    'hint' => $this->l('Status will be highlighted in this color. HTML colors only.').' "lightblue", "#CC6600")'
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        
        if (Tools::isSubmit('updateorder_state') || Tools::isSubmit('addorder_payment_status')) 
        {
            return $this->renderOrderStatusForm();
        } 
        else 
        {
            return parent::renderForm();
        }
    }

    protected function renderOrderStatusForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $this->fields_value = [];

        if ($this->getFieldValue($obj, 'color') !== false) {
            $this->fields_value['color'] = $this->getFieldValue($obj, 'color');
        } else {
            $this->fields_value['color'] = "#ffffff";
        }

        return parent::renderForm();
    }

    
    public function postProcess()
    {
        if (Tools::isSubmit($this->table.'Orderby') || Tools::isSubmit($this->table.'Orderway')) {
            $this->filter = true;
        }

        if (Tools::isSubmit('delete'.$this->table) && $orderId = OrderPaymentStatus::isUsed(intval(Tools::getValue('id_order_payment_status')))) 
        {
            $this->errors[] = sprintf($this->l('This status is used in order %d and can\'t be deleted'), $orderId);
            return false;
        }
        elseif(Tools::isSubmit('submitBulkdelete'.$this->table))
        {
            // check all deleting statuses
            $statusIds = $_REQUEST['order_payment_statusBox'];
            if(is_array($statusIds))
            {
                foreach ($statusIds as $statusId)
                {
                    if ($orderId = OrderPaymentStatus::isUsed(intval($statusId)))
                    {
                        $status = new OrderPaymentStatus($statusId);
                        $this->errors[] = sprintf($this->l('Status "%s" is used in order %d and can\'t be deleted'), $status->name, $orderId);
                    }
                }
                
                if (count($this->errors))
                {
                    return false;
                }
            }
        }
        return parent::postProcess();
    }
    

    protected function filterToField($key, $filter)
    {
        $this->initOrderStatutsList();
        
        return parent::filterToField($key, $filter);
    }
}
