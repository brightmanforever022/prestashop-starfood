<?php

/**
 * Class is purposed to work with db table there scheduled stock updates are stored
 */
class MSSSClientStockUpdater
{
    // statuses of messages
    const StatusNew= 0;
    const StatusInProcess = 1;
    const StatusError = 2;
    const TableName = 'ss_notification_slave';
    
    var $pid;
    
    /**
     * Schedules stock update for send to server
     * @param type $sku sku of updated product
     * @param type $delta quantity change
     */
    static function scheduleStockUpdate($sku, $delta)
    {
        // insert new record
        $delta = intval($delta);
        if (!empty($sku) && $delta)
        {
            Db::getInstance()->execute('insert into '.self::TableName.'(sku, change_qty, status) values(\''.addslashes($sku).'\','.$delta.','.
                    self::StatusNew.')');
        }
    }
    
    
    /**
     * Schedules stock update for send to server. Difference from scheduleStockUpdate is that update is created by ids, not by sku
     * @param int $productId
     * @param int $attributeId
     * @param int $delta
     */
    static function scheduleStockUpdateById($productId, $attributeId, $delta)
    {
        // reading sku
        if ($attributeId)
        {
            // it is product with attributes, get sku from attribute
            $combination = new Combination($attributeId);
            $sku = $combination->supplier_reference;
        }
        else
        {
            // it is product w/o attributes read sku from product
            $product =  new Product($productId, false);
            $sku = $product->supplier_reference;
        }
        self::scheduleStockUpdate($sku, $delta);
    }
    
    
    /**
     * @returns array with messages that need to be sent
     */
    function getMessagesToSend()
    {
        // mark messages in process 
        $this->pid = time();
        Db::getInstance()->execute('update '.self::TableName.' set status='.self::StatusInProcess.', pid='.$this->pid.' where status in ('.
                self::StatusNew.', '.self::StatusError.') and pid=0');
        
        // read not unique skus
        $skus = Db::getInstance()->executeS('select distinct ps1.product_supplier_reference, greatest(ps1.id_product, ps2.id_product)'
                .' as product_id, if(ps1.id_product>ps2.id_product, ps1.id_product_attribute, ps2.id_product_attribute) as combination_id '.
                'from '._DB_PREFIX_.'product_supplier ps1 inner join '._DB_PREFIX_.'product_supplier ps2 on 
                ps1.product_supplier_reference=ps2.product_supplier_reference and ps1.id_product_supplier<>ps2.id_product_supplier');
        
        $notUniqueSkus = [];
        foreach($skus as $sku)
        {
            $notUniqueSkus []= $sku['product_supplier_reference'];
        }
        
        if (count($notUniqueSkus))
        {
            $lastWarningSendTime = Configuration::get('MSSS_NOT_UNIQUE_SKU_WARNING_SEND_TIME');
            if (empty($lastWarningSendTime) || $lastWarningSendTime<time()-3600)
            {
                MSSSLog::reportError('errors during stock update by client notification', 
                    'Following products have not unique skus and will not be updated in server:'."\n".print_r($skus, true));
                Configuration::updateValue('MSSS_NOT_UNIQUE_SKU_WARNING_SEND_TIME', time());
            }
        }
            
        // read all that need to be sent and resent
        $messages = Db::getInstance()->executeS('select sku, sum(change_qty) as `change` from '.self::TableName.' where status ='.
                self::StatusInProcess.' and pid='.$this->pid.(count($notUniqueSkus)?' and sku not in (\''.implode('\',\'', $notUniqueSkus).
                        '\')':'').' group by sku');
        
        // filterout messages with zero sum and delete all corresponding records
        $resultMessages = [];
        foreach ($messages as $message)
        {
            if ($message['change']!=0)
            {
                $resultMessages [] = $message;
            }
            else
            {
                // delete corresponding records
                Db::getInstance()->execute('delete from '.self::TableName.' where status ='.self::StatusInProcess.' and pid='.$this->pid.
                        ' and sku=\''.addslashes($message['sku']).'\'');
            }
        }
        
        return $resultMessages;
    }
    
    
    /**
     * Marks all messages read in previous getUpdatesList call , that has specified destination, successfully processed
     */
    function markMessagesProcessed()
    {
        Db::getInstance()->execute('delete from '.self::TableName.' where pid='.$this->pid);
    }
    
    
    /**
     * Marks all messages read in previous getUpdatesList call processed with error
     */
    function markMessagesProcessedWithError()
    {
        Db::getInstance()->execute('update '.self::TableName.' set status='.self::StatusError.' where pid='.$this->pid);
    }
    
    
    /**
     * Updates stock of products in this shop
     * @param type $messages array of [sku=> sku of updating product, qty=>new quantity
     * @returns string with errors, if they occurred, empty string if all ok
     */
    static function updateStockBySku($messages)
    {
        $errors = '';
        foreach ($messages as $message)
        {
            // search for product by sku
            $psIds = Db::getInstance()->getRow('select ps.id_product_attribute, ps.id_product from ' . _DB_PREFIX_ . 'product_supplier ps'
                    . ' where ps.product_supplier_reference=\'' . addslashes($message['sku']) .'\'');
            if ($psIds)
            {
                // product found, update it
                StockAvailableCore::setQuantity($psIds['id_product'], $psIds['id_product_attribute'], $message['qty']);
            }
            else
            {
                $errors .= "\n" . 'Product with sku "' . $message['sku'] . '" not found';
            }
        }
        
        if (!empty($errors))
        {
            $errors = "Following products were not found in local db and were not updated: \n".$errors;
        }
        return $errors;
    }
}
