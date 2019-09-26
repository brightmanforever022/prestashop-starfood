<?php

class MeasureUnit extends ObjectModel
{
    public $liquid;
    
    public $measure_abs;
    
    public $name;
    
    public static $definition = array(
        'table' => 'measure_unit',
        'primary' => 'id_measure_unit',
        'fields' => array(
            'liquid' => array(
                'type' => self::TYPE_BOOL,
                'required' => true
            ),
            'measure_abs' => array(
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