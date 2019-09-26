<?php
define('SCRIPT_FOLDER', realpath(dirname(__FILE__)));
require SCRIPT_FOLDER . '/../../config/config.inc.php';


$context = Context::getContext();

$adminFolder = 'admin971jqkmvw';
if( strpos('koehlert.com', $context->shop['domain']) ){
    $adminFolder = 'admin1234';
}

$adminEmail = 'info@koehlert.com';
$remindersQuery = '
    SELECT nm.*, cm.`message`
    FROM `' . _DB_PREFIX_ . 'note_reminder` nm
    INNER JOIN `' . _DB_PREFIX_ . 'customer_message` cm
        ON cm.`id_customer_message` = nm.`id_customer_message`
    WHERE nm.`remind_date` = "'. date('Y-m-d') .'"
        AND nm.`remind_sent` = 0
';

$reminders = Db::getInstance()->executeS($remindersQuery);

if(is_array($reminders) && count($reminders)){
    foreach($reminders as $reminder){
        $orderLink = Tools::getAdminUrl( $adminFolder .'/'. $context->link->getAdminLink('AdminOrders', false) . 
            '&id_order='.$reminder['id_order'] .'&vieworder' );
        $mailText = 
            'Order link: '. $orderLink . "\n\r"
            . "================\n\r"
            . $reminder['message']
        ;
        mail($adminEmail, 'Order note reminder #'. $reminder['id_order'], $mailText);
        
        Db::getInstance()->update('note_reminder', array('remind_sent' => '1'), 
            'id = '. $reminder['id']);
    }
}