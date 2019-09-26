<?php
/**
 * Modulu which offer option to choose if the cart rule can be applied to products with already reduced price.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

OrderPaymentCore::$definition['fields']['order_reference'] = array('type' => ObjectModel::TYPE_STRING, 'validate' => 'isAnything', 'size' => 255);
class OrderPayment extends OrderPaymentCore
{

}
