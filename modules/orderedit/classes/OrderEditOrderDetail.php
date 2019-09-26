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

class OrderEditOrderDetail extends OrderDetail
{
    public function __construct($id = null, $id_lang = null, $context = null)
    {
        // remove id_warehouse and product_price from required fields
        // (unable to set 0, validateController returns errors)
        self::$definition['fields']['id_warehouse'] = array(
            'type' => self::TYPE_INT,
            'validate' => 'isUnsignedId',
            'required' => false
        );
        self::$definition['fields']['product_price'] = array(
            'type' => self::TYPE_FLOAT,
            'validate' => 'isPrice',
            'required' => false
        );

        parent::__construct($id, $id_lang, $context);
    }
}
