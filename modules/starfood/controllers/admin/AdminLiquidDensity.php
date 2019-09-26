<?php

class AdminLiquidDensityController extends ModuleAdminController
{
    public $auth = true;
    
    public $bootstrap = true;
    
    public function __construct()
    {
        $this->table = 'liquid_density';
        $this->className = 'LiquidDensity';
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
        $this->fields_list['id_liquid_density'] = array(
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
        
        $this->fields_list['density'] = array(
            'title' => $this->l('Density'),
            'type' => 'float',
        );
    }
    
    public function renderForm()
    {
        $this->fields_form = array(
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Density'),
                    'name' => 'density',
                    'required' => true,
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