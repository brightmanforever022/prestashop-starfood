<?php
OrderPaymentCore::$definition['fields']['order_reference'] = array('type' => ObjectModel::TYPE_STRING, 'validate' => 'isAnything', 'size' => 255);
class OrderPayment extends OrderPaymentCore
{
}
