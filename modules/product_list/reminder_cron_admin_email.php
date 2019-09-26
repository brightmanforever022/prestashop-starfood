<?php

// Wheelronix Ltd. development team
  // site: http://www.wheelronix.com
  // mail: info@wheelronix.com

define('_PS_MODE_DEV_', true);
require '../../config/config.inc.php';

generateReminders1();
generateReminders2();
generateReminders3();
sendRemindersToAdmin();


/**
 * Generates pdf file and set invoice reminder status and date for all invoices there reminder 1 should be sent
 */
function generateReminders1()
{
    // reading invoices for that we need to generate reminders
    $invoices = Db::getInstance()->executeS('select id_order_invoice, sum_to_pay, number, c.firstname, c.lastname, c.address1, c.company, ' .
            'c.address2, c.city, c.postcode, o.id_lang, oi.date_add as invoice_date, gl.name as salutation, cl.name as countryName, '.
            'o.id_currency from ' . _DB_PREFIX_ .
            'order_invoice oi left join ' . _DB_PREFIX_ . 'orders o on o.id_order=oi.id_order left join ' . _DB_PREFIX_ . 'customer c ' .
            'on c.id_customer=o.id_customer left join ' . _DB_PREFIX_ . 'gender_lang gl on c.id_gender=gl.id_gender and gl.id_lang=' .
            'o.id_lang left join ' . _DB_PREFIX_ . 'address a on o.id_address_invoice=a.id_address left join ' .
            _DB_PREFIX_ . 'country_lang cl on cl.id_country=a.id_country and cl.id_lang=o.id_lang '.
            'where oi.number>0 and oi.paid=0 and oi.reminder_state<'.OrderInvoice::Reminder1Sent.' and due_date <=\''.
            date('Y-m-d', time()-Configuration::get('dpReminder1Days')*24*3600).'\'');
    
    // generate reminders
    if (!empty($invoices))
    {
        $invoiceIds = [];
        // generate pdfs
        foreach ($invoices as $invoice)
        {
            $invoiceIds []= $invoice['id_order_invoice'];
            OrderInvoice::generateReminder($invoice, 1, $invoice['id_lang']);
        }
        // mark invoices that reminder was generated
        OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder1Sent);
    }
}


/**
 * Generates pdf file and set invoice reminder status and date for all invoices there reminder 2 should be sent
 */
function generateReminders2()
{
    // reading invoices for that we need to generate reminders
    $invoices = Db::getInstance()->executeS('select id_order_invoice, sum_to_pay, number, c.firstname, c.lastname, c.address1, c.company, ' .
            'c.address2, c.city, c.postcode, o.id_lang, oi.date_add as invoice_date, gl.name as salutation, cl.name as countryName, '.
            'o.id_currency from ' . _DB_PREFIX_ .
            'order_invoice oi left join ' . _DB_PREFIX_ . 'orders o on o.id_order=oi.id_order left join ' . _DB_PREFIX_ . 'customer c ' .
            'on c.id_customer=o.id_customer left join ' . _DB_PREFIX_ . 'gender_lang gl on c.id_gender=gl.id_gender and gl.id_lang=' .
            'o.id_lang left join ' . _DB_PREFIX_ . 'address a on o.id_address_invoice=a.id_address left join ' .
            _DB_PREFIX_ . 'country_lang cl on cl.id_country=a.id_country and cl.id_lang=o.id_lang '.
            'where oi.number>0 and oi.paid=0 and oi.reminder_state='.OrderInvoice::Reminder1Sent.' and reminder_date <=\''.
            date('Y-m-d', time()-Configuration::get('dpReminder2Days')*24*3600).'\'');
    
    // generate reminders
    if (!empty($invoices))
    {
        $invoiceIds = [];
        // generate pdfs
        foreach ($invoices as $invoice)
        {
            $invoiceIds []= $invoice['id_order_invoice'];
            OrderInvoice::generateReminder($invoice, 2, $invoice['id_lang']);
        }
        // mark invoices that reminder was generated
        OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder2Sent);
    }
}


/**
 * Generates pdf file and set invoice reminder status and date for all invoices there reminder 3 should be sent
 */
function generateReminders3()
{
    // reading invoices for that we need to generate reminders
    $invoices = Db::getInstance()->executeS('select id_order_invoice, sum_to_pay, number, c.firstname, c.lastname, c.address1, c.company, ' .
            'c.address2, c.city, c.postcode, o.id_lang, oi.date_add as invoice_date, gl.name as salutation, cl.name as countryName, '.
            'o.id_currency from ' . _DB_PREFIX_ .
            'order_invoice oi left join ' . _DB_PREFIX_ . 'orders o on o.id_order=oi.id_order left join ' . _DB_PREFIX_ . 'customer c ' .
            'on c.id_customer=o.id_customer left join ' . _DB_PREFIX_ . 'gender_lang gl on c.id_gender=gl.id_gender and gl.id_lang=' .
            'o.id_lang left join ' . _DB_PREFIX_ . 'address a on o.id_address_invoice=a.id_address left join ' .
            _DB_PREFIX_ . 'country_lang cl on cl.id_country=a.id_country and cl.id_lang=o.id_lang '.
            'where oi.number>0 and oi.paid=0 and oi.reminder_state='.OrderInvoice::Reminder2Sent.' and reminder_date <=\''.
            date('Y-m-d', time()-Configuration::get('dpReminder3Days')*24*3600).'\'');
    
    // generate reminders
    if (!empty($invoices))
    {
        $invoiceIds = [];
        // generate pdfs
        foreach ($invoices as $invoice)
        {
            $invoiceIds []= $invoice['id_order_invoice'];
            OrderInvoice::generateReminder($invoice, 3, $invoice['id_lang']);
        }
        // mark invoices that reminder was generated
        OrderInvoice::setReminderStatus($invoiceIds, OrderInvoice::Reminder3Sent);
    }
}


/**
 * Checks if it is time to send email with reminders to admin,
 * sends all not yet send reminders
 */
function sendRemindersToAdmin()
{
    // search for invoices to send
    $invoices = Db::getInstance()->executeS('select id_order_invoice, o.id_lang, oi.reminder_state, admin_email_sent from '. _DB_PREFIX_ .
            'order_invoice oi left join '._DB_PREFIX_.'orders o on o.id_order=oi.id_order where paid=0 and reminder_state>admin_email_sent '.
            'and reminder_state<>'.OrderInvoice::ReminderInkasso);
    
    if (empty($invoices))
    {
        return;
    }
    
    // send invoices
    $filesToSend = [];
    $remindersToMark = [OrderInvoice::Reminder1Sent=>[], OrderInvoice::Reminder2Sent=>[], OrderInvoice::Reminder3Sent=>[]];
    $errors = '';
    foreach($invoices as $invoice)
    {
        // we send only last invoice in case if 2 reminders should be sent for 1 invoice 
        $fileToSend = OrderInvoice::getReminderFilePath($invoice['id_order_invoice'], $invoice['reminder_state'], $invoice['id_lang'], true);
        if(empty($fileToSend))
        {
            // generate invoice
            $invoiceData = Db::getInstance()->getRow('select id_order_invoice, sum_to_pay, number, c.firstname, c.lastname, c.address1, c.company, ' .
            'c.address2, c.city, c.postcode, o.id_lang, oi.date_add as invoice_date, gl.name as salutation, cl.name as countryName, '.
            'o.id_currency from ' . _DB_PREFIX_ .
            'order_invoice oi left join ' . _DB_PREFIX_ . 'orders o on o.id_order=oi.id_order left join ' . _DB_PREFIX_ . 'customer c ' .
            'on c.id_customer=o.id_customer left join ' . _DB_PREFIX_ . 'gender_lang gl on c.id_gender=gl.id_gender and gl.id_lang=' .
            'o.id_lang left join ' . _DB_PREFIX_ . 'address a on o.id_address_invoice=a.id_address left join ' .
            _DB_PREFIX_ . 'country_lang cl on cl.id_country=a.id_country and cl.id_lang=o.id_lang '.
            'where id_order_invoice='.$invoice['id_order_invoice']);
            OrderInvoice::generateReminder($invoiceData, $invoice['reminder_state'], $invoice['id_lang']);
            $fileToSend = OrderInvoice::getReminderFilePath($invoice['id_order_invoice'], $invoice['reminder_state'], $invoice['id_lang'], true);
            if (empty($fileToSend))
            {
                // invoice can't be generated
                $errors .= "\n<br/>Reminder {$invoice['reminder_state']} can't be generated for invoice ".$invoice['id_order_invoice'];
                continue;
            }
        }
        
        $filesToSend []= $fileToSend;
        $remindersToMark[$invoice['reminder_state']] []= $invoice['id_order_invoice'];
    }
    
    // generate zip with pdf files
    $saveFileNameZip = tempnam(OrderInvoice::InvoiceReminderFolderPath, 'reminders_');
    if (!$saveFileNameZip)
    {
        exit('Can\'t create zip file');
    }
    
    $zip = new ZipArchive();
    if ($zip->open($saveFileNameZip, ZIPARCHIVE::CREATE) !== TRUE)
    {
        exit('Can\'t open zip file');
    }
    foreach ($filesToSend as $file)
    {
        $zip->addFile($file, basename($file));
    }

    $zip->close();
    
    // send email
    Mail::Send(Language::getIdByIso('de'), 'admin_reminders',
     Mail::l('reminders'), ['{errors}'=>$errors], Configuration::get('PS_SHOP_EMAIL'),
     Configuration::get('PS_SHOP_NAME'), null, null, ['mime'=>'application/zip', 'name'=>'reminders.zip', 
     'content'=>file_get_contents($saveFileNameZip)], null, _PS_MODULE_DIR_.'product_list/mails/'); // , null, _PS_MAIL_DIR_, true, (int)$order->id_shop
    
    unlink($saveFileNameZip);
    
    // mark invoices as sent to admin
    foreach($remindersToMark as $state=>$ids)
    {
        if (count($ids))
        {
            Db::getInstance()->execute('update '._DB_PREFIX_.'order_invoice set admin_email_sent='.$state.' where id_order_invoice in('.
                    implode(',', $ids).')');
        }
    }
}