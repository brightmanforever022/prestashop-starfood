<?php

class LiquidDensity extends ObjectModel
{
    public $density;
    
    public $name;
    
    public static $definition = array(
        'table' => 'liquid_density',
        'primary' => 'id_liquid_density',
        'fields' => array(
            'density' => array(
                'type' => self::TYPE_FLOAT,
                'required' => true
            ),
            'name' => array(
                'type' => self::TYPE_STRING,
                'required' => true,
                'size' => 32
            )
        )
        
    );
}