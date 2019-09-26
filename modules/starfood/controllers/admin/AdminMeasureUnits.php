<?php

class AdminMeasureUnitsController extends ModuleAdminControllerCore
{
    public $auth = true;
    
    public $bootstrap = true;
    
    public function __construct()
    {
        $this->table = 'measure_unit';
        $this->className = 'MeasureUnit';
        $this->lang = false;
        $this->explicitSelect = true;
        $this->addRowAction('edit');
        $this->addRowAction('delete');
        
        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->l('Delete selected'),
                'icon' => 'icon-trash',
                'confirm' => $this->l('Delete selected items?')
            )
        );
        
        parent::__construct();
        
        $this->fields_list = array();
        $this->fields_list['id_measure_unit'] = array(
            'title' => $this->l('ID'),
            'align' => 'center',
            'class' => 'fixed-width-xs',
            'type' => 'int'
        );
        $this->fields_list['name'] = array(
            'title' => $this->l('Name'),
            //'filter_key' => 'b!name',
            //'callback' => 'showName'
        );
        
        $this->fields_list['liquid'] = array(
            'title' => $this->l('Liquid'),
            'type' => 'bool',
        );
        $this->fields_list['measure_abs'] = array(
            'title' => $this->l('Absolute'),
            'type' => 'float',
        );
        
    }
    
    public function renderForm()
    {
        $this->fields_form = array(
            'input' => array(
                array(
                    'type' => 'switch',
                    'label' => $this->l('Liquid'),
                    'name' => 'liquid',
                    'required' => true,
                    //'class' => 't',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->l('No')
                        )
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Weight/Volume absolute'),
                    'name' => 'measure_abs',
                    'required' => false,
                    'hint' => $this->l('Regarding shop default weight and volume measures')
                ),
                
                array(
                    'type' => 'text',
                    'label' => $this->l('Name'),
                    'name' => 'name',
                    'required' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save')
            )
            
        );
        
        return parent::renderForm();
    }
}

