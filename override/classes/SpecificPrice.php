<?php

class SpecificPrice extends SpecificPriceCore
{
    public $comment;
    
    public static $definition = array(
        'table' => 'specific_price',
        'primary' => 'id_specific_price',
        'fields' => array(
            'id_shop_group' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_shop' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_cart' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_product_attribute' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_currency' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_specific_price_rule' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_country' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'id_customer' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'price' =>                    array('type' => self::TYPE_FLOAT, 'validate' => 'isNegativePrice', 'required' => true),
            'from_quantity' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'required' => true),
            'reduction' =>                array('type' => self::TYPE_FLOAT, 'validate' => 'isPrice', 'required' => true),
            'reduction_tax' =>            array('type' => self::TYPE_INT, 'validate' => 'isBool', 'required' => true),
            'reduction_type' =>        array('type' => self::TYPE_STRING, 'validate' => 'isReductionType', 'required' => true),
            'from' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'to' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat', 'required' => true),
            'comment' => array('type' => self::TYPE_STRING, 'required' => false)
        ),
    );
    
}