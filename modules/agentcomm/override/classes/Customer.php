<?php

class Customer extends CustomerCore
{
    public $agent_commision;
    
    public $agent_commision_type;
    
    public function __construct($id = null)
    {
        self::$definition['fields']['agent_commision'] = array(
            'type' => self::TYPE_INT, 
            'validate' => 'isUnsignedInt',
            'required' => false
        );
        self::$definition['fields']['agent_commision_type'] = array(
            'type' => self::TYPE_INT,
            'validate' => 'isUnsignedInt',
            'required' => false
        );
        
        
        parent::__construct($id);
    }
}