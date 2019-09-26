<?php

class Customer extends CustomerCore
{
    public $agentsales_commision_type;
    
    public $agentsales_commision_value;
    
    public function __construct($id = null)
    {
        self::$definition['fields']['agentsales_commision_type'] = array(
            'type' => self::TYPE_INT, 
            'validate' => 'isUnsignedInt',
            'required' => false
        );
        self::$definition['fields']['agentsales_commision_value'] = array(
            'type' => self::TYPE_INT,
            'validate' => 'isUnsignedInt',
            'required' => false
        );
        
        
        parent::__construct($id);
    }
}