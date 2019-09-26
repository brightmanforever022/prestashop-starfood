<?php

class CustomerThread extends CustomerThreadCore
{
    public static function getCustomerMessagesByOrderId($id_order)
    {
      $sql = 'SELECT ct.*,cm.*, c.`firstname` AS cfirstname, c.`lastname` AS clastname, e.`firstname` AS efirstname, e.`lastname` AS elastname
       FROM '._DB_PREFIX_.'customer_thread ct
       LEFT JOIN '._DB_PREFIX_.'customer_message cm
        ON ct.id_customer_thread = cm.id_customer_thread
       LEFT JOIN `'._DB_PREFIX_.'customer` c
        ON ct.`id_customer` = c.`id_customer`
       LEFT JOIN '._DB_PREFIX_.'employee e
        ON cm.id_employee = e.id_employee
       WHERE id_order = '.(int)$id_order.' ORDER BY cm.id_customer_message DESC';
        
      return Db::getInstance()->executeS($sql);
    }
    
}