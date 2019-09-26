<?php

class AdminTabsController extends AdminTabsControllerCore
{
    // fixes problem with redirect on add tab fields validation error
    public function postProcess()
    {
        /* PrestaShop demo mode */
        if (_PS_MODE_DEMO_) {
            $this->errors[] = Tools::displayError('This functionality has been disabled.');
            return;
        }
        /* PrestaShop demo mode*/

        if (($id_tab = (int)Tools::getValue('id_tab')) && ($direction = Tools::getValue('move')) && Validate::isLoadedObject($tab = new Tab($id_tab))) {
            if ($tab->move($direction)) {
                Tools::redirectAdmin(self::$currentIndex.'&token='.$this->token);
            }
        } elseif (Tools::getValue('position') && !Tools::isSubmit('submitAdd'.$this->table)) {
            if ($this->tabAccess['edit'] !== '1') {
                $this->errors[] = Tools::displayError('You do not have permission to edit this.');
            } elseif (!Validate::isLoadedObject($object = new Tab((int)Tools::getValue($this->identifier)))) {
                $this->errors[] = Tools::displayError('An error occurred while updating the status for an object.').
                    ' <b>'.$this->table.'</b> '.Tools::displayError('(cannot load object)');
            }
            if (!$object->updatePosition((int)Tools::getValue('way'), (int)Tools::getValue('position'))) {
                $this->errors[] = Tools::displayError('Failed to update the position.');
            } else {
                Tools::redirectAdmin(self::$currentIndex.'&conf=5&token='.Tools::getAdminTokenLite('AdminTabs'));
            }
        } elseif (Tools::isSubmit('submitAdd'.$this->table) && Tools::getValue('id_tab') === Tools::getValue('id_parent')) {
            $this->errors[] = Tools::displayError('You can\'t put this menu inside itself. ');
        } elseif (Tools::isSubmit('submitAdd'.$this->table) && $id_parent = (int)Tools::getValue('id_parent')) {
            $this->redirect_after = self::$currentIndex.'&id_'.$this->table.'='.$id_parent.'&details'.$this->table.'&conf=4&token='.$this->token;
        } elseif (isset($_GET['details'.$this->table]) && is_array($this->bulk_actions)) {
            $submit_bulk_actions = array_merge(array(
                'enableSelection' => array(
                    'text' => $this->l('Enable selection'),
                    'icon' => 'icon-power-off text-success'
                ),
                'disableSelection' => array(
                    'text' => $this->l('Disable selection'),
                    'icon' => 'icon-power-off text-danger'
                )
            ), $this->bulk_actions);
            foreach ($submit_bulk_actions as $bulk_action => $params) {
                if (Tools::isSubmit('submitBulk'.$bulk_action.$this->table) || Tools::isSubmit('submitBulk'.$bulk_action)) {
                    if ($this->tabAccess['edit'] === '1') {
                        $this->action = 'bulk'.$bulk_action;
                        $this->boxes = Tools::getValue($this->list_id.'Box');
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                    }
                    break;
                } elseif (Tools::isSubmit('submitBulk')) {
                    if ($this->tabAccess['edit'] === '1') {
                        $this->action = 'bulk'.Tools::getValue('select_submitBulk');
                        $this->boxes = Tools::getValue($this->list_id.'Box');
                    } else {
                        $this->errors[] = Tools::displayError('You do not have permission to edit this.');
                    }
                    break;
                }
            }
        } else {
            // Temporary add the position depend of the selection of the parent category
            if (!Tools::isSubmit('id_tab')) { // @todo Review
                $_POST['position'] = Tab::getNbTabs(Tools::getValue('id_parent'));
            }
        }

        // here is fix
        if (!count($this->errors)) {
            if (!AdminController::postProcess())
            {
                $this->redirect_after = false;
            }
        }
    }

}

