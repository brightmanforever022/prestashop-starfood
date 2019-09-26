<?php

OrderDetailCore::$definition['fields']['in_stock'] = array('type' => OrderDetailCore::TYPE_INT);
OrderDetailCore::$definition['fields']['shipped'] = array('type' => OrderDetailCore::TYPE_INT);
OrderDetailCore::$definition['fields']['shipped_employee_id'] = array('type' => OrderDetailCore::TYPE_INT);
OrderDetailCore::$definition['fields']['shipped_date'] = array('type' => OrderDetailCore::TYPE_DATE);
        
class OrderDetail extends OrderDetailCore
{
    public $in_stock;
    public $shipped;
    public $shipped_employee_id;
    public $shipped_date;
    
    
    public function add($autodate = true, $null_values = false)
    {
        // order quantity already subtracted
        $product_quantity = (int)Product::getQuantity($this->product_id, (int)$this->product_attribute_id);
        $this->in_stock = $product_quantity< 0 ?0:1;
        
        return parent::add($autodate, $null_values);
    }
}