<?php

class OrderInvoice extends OrderInvoiceCore
{
    const InvoiceFolderPath = _PS_ROOT_DIR_.'/invoices/';
    const InvoiceFolderUrl = __PS_BASE_URI__.'invoices/';
    const InvoiceReminderFolderUrl = __PS_BASE_URI__.'invoices/reminders/';
    const InvoiceReminderFolderPath = _PS_ROOT_DIR_.'/invoices/reminders/';
    
    const ReminderNotSent = 0;
    const Reminder1Sent = 1;
    const Reminder2Sent = 2;
    const Reminder3Sent = 3;
    const ReminderInkasso = 4;
    
    const AdminReminderEmailNotSent = 0;
    const AdminReminder1EmailSent = 1;
    const AdminReminder2EmailSent = 2;
    const AdminReminder3EmailSent = 3;
    
    public $template_id;
    public $products;   // contains comma separated order detail ids
    public $due_date;
    public $paid;
    public $sum_to_pay;
    public $payment_date;
    public $comment;
    public $id_employee;
    public $admin_email_sent;
    public $reminder_date;
    public $percent_to_pay;
    public $printed;

    public static $definition = array(
        'table' => 'order_invoice',
        'primary' => 'id_order_invoice',
        'fields' => array(
            'id_order' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'number' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
            'delivery_number' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'delivery_date' =>            array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'total_discount_tax_excl' =>array('type' => self::TYPE_FLOAT),
            'total_discount_tax_incl' =>array('type' => self::TYPE_FLOAT),
            'total_paid_tax_excl' =>    array('type' => self::TYPE_FLOAT),
            'total_paid_tax_incl' =>    array('type' => self::TYPE_FLOAT),
            'total_products' =>            array('type' => self::TYPE_FLOAT),
            'total_products_wt' =>        array('type' => self::TYPE_FLOAT),
            'total_shipping_tax_excl' =>array('type' => self::TYPE_FLOAT),
            'total_shipping_tax_incl' =>array('type' => self::TYPE_FLOAT),
            'shipping_tax_computation_method' => array('type' => self::TYPE_INT),
            'total_wrapping_tax_excl' =>array('type' => self::TYPE_FLOAT),
            'total_wrapping_tax_incl' =>array('type' => self::TYPE_FLOAT),
            'shop_address' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 1000),
            'invoice_address' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 1000),
            'delivery_address' =>        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 1000),
            'note' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 65000),
            'date_add' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'template_id' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'products' =>        array('type' => self::TYPE_STRING),
            'due_date' =>                array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'paid' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'sum_to_pay' =>            array('type' => self::TYPE_FLOAT),
            'payment_date' =>          array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'comment' =>        array('type' => self::TYPE_STRING),
            'id_employee' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            //'admin_email_sent' =>        array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'reminder_date' => array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
            'percent_to_pay' => array('type' => self::TYPE_FLOAT),
            'printed' => array('type' => self::TYPE_BOOL)
        ),
    );
    
    protected static $invoiceIdDefault=[];
    protected static $deliveryIdDefault=[];
    protected static $invoiceNames=[];
    protected static $deliveryNames=[];
    
    
    public function __construct($id = null, $id_lang = null, $id_shop = null)
    {
        parent::__construct($id, $id_lang, $id_shop);
        $this->checkAndSetDefaultTemplateId();
    }
    
    public function hydrate(array $data, $id_lang = null)
    {
        parent::hydrate($data, $id_lang);
        $this->checkAndSetDefaultTemplateId();
    }

    function checkAndSetDefaultTemplateId()
    {
        if (!$this->template_id && $this->id_order)
        {
            // read id lang from order
            $id_lang = Db::getInstance()->getValue('select id_lang from '._DB_PREFIX_.'orders where id_order='.$this->id_order);

            if ($this->delivery_number != 0)
            {
                $this->template_id = self::getDefaultDeliveryTemplateId($id_lang);
            }
            else
            {
                $this->template_id = self::getDefaultInvoiceTemplateId($id_lang);
            }
            //$this->update();
        }
    }
    
    public function getTemplateName($langId)
    {
        self::readTemplates();
        
        // set default template id it was not already set
        if (!$this->template_id)
        {
            if ($this->is_delivery)
            {
                $this->template_id = self::getDefaultDeliveryTemplateId($langId);
            }
            else
            {
                $this->template_id = self::getDefaultInvoiceTemplateId($langId);
            }
        }
        
        if (!empty($this->is_delivery) && $this->is_delivery && ($this->delivery_number != 0))
        {
            // it is delivery slip
            return self::$deliveryNames[$this->template_id];
        }
        else
        {
            return self::$invoiceNames[$this->template_id];
        }
    }

    
    protected static function readTemplates()
    {
        if (count(self::$invoiceIdDefault) == 0)
        {
            // reading names and set default ids
            $templates = Db::getInstance()->executeS('select id, name, status, id_lang from ' . _DB_PREFIX_ . 'ba_prestashop_invoice');
            foreach ($templates as $template)
            {
                if ($template['status'])
                {
                    self::$invoiceIdDefault[$template['id_lang']] = $template['id'];
                }
                self::$invoiceNames[$template['id']] = $template['name'];
            }

            $templates = Db::getInstance()->executeS('select id, name, status, id_lang from ' . _DB_PREFIX_ . 'ba_prestashop_delivery_slip');
            foreach ($templates as $template)
            {
                if ($template['status'])
                {
                    self::$deliveryIdDefault[$template['id_lang']] = $template['id'];
                }
                self::$deliveryNames[$template['id']] = $template['name'];
            }
        }
    }

    
    static function getDefaultInvoiceTemplateId($langId)
    {
        self::readTemplates();
        return self::$invoiceIdDefault[$langId];
    }
    
    static function getDefaultDeliveryTemplateId($langId)
    {
        self::readTemplates();
        return self::$deliveryIdDefault[$langId];
    }
        
    static function getDeliveryTemplateNames()
    {
        self::readTemplates();
        return self::$deliveryNames;
    }
    
    static function getInvoiceTemplateNames()
    {
        self::readTemplates();
        return self::$invoiceNames;
    }
    
    function getProductIds()
    {
        return explode(',', $this->products);
    }
    
    
    function setProductIds($products)
    {
        $this->products = implode(',', $products);
    }
    
    public function getProductsDetail()
    {
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT od.*,p.*,ps.*, m.name as brand_name
		FROM `'._DB_PREFIX_.'order_detail` od
		LEFT JOIN `'._DB_PREFIX_.'product` p
		ON p.id_product = od.product_id
		LEFT JOIN `'._DB_PREFIX_.'product_shop` ps ON (ps.id_product = p.id_product AND ps.id_shop = od.id_shop)
        LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (p.id_manufacturer = m.id_manufacturer)
		WHERE od.`id_order` = '.(int)$this->id_order.
                (!empty($this->products)?' and id_order_detail in ('.$this->products.')':''));
		//.($this->id && $this->number ? ' AND od.`id_order_invoice` = '.(int)$this->id : ''));
    }
    
    /**
     * @return string path there invoice file should be stored (independent if it saved or not). 
     * If object is not initialized returns empty string
     */
    function getInvoiceFilePath()
    {
        $fileName = $this->getInvoiceFileName();
        if (!empty($fileName))
        {
            return self::InvoiceFolderPath.$fileName;
        }
    }
    
    
    /**
     * returns just name of invoice file. If object is not initialized returns empty string
     */
    function getInvoiceFileName()
    {
        if ($this->id_order && $this->id)
        {
            return $this->id_order.'_'.$this->id.'.pdf';
        }
    }
    
    /**
     * Checks if file was generated and gives link on it.
     * @returns url of generated file or false if file was yet generated
     */
    function getInvoiceFileLink()
    {
        $path = $this->getInvoiceFilePath();
        if (empty($path) || !file_exists($path))
        {
            return false;
        }
        
        return self::InvoiceFolderUrl.$this->getInvoiceFileName();
    }
    
    
    function delete()
    {
        if (empty($this->number))
        {
            // this means current object is not created/constructed
            $orderInvoice = new OrderInvoice($this->id);
            $filePath = $orderInvoice->getInvoiceFilePath();
        }
        else
        {
            $filePath = $this->getInvoiceFilePath();
        }
        return parent::delete() && unlink($filePath);
    }
    
    
    /**
     * Set paid status for several invoices
     * @param array $ids array with ids of invoices for that stus need to be set
     * @param bool $paid paid status, true if paid
     */
    public static function setPaidState(array $ids, $paid)
    {
        Db::getInstance()->execute('update '._DB_PREFIX_.'order_invoice set paid='.($paid?'1, payment_date=\''.date('Y-m-d H:i:s').'\'':'0').
                ' where id_order_invoice in ('.implode(',', $ids).')');
    }
    
    
    /**
     * 
     * @return new/current state
     */
    function toggleInvoicePaidState()
    {
        $this->paid = $this->paid?0:1;
        if ($this->paid)
        {
            $this->payment_date = date('Y-m-d H:i:s');
        }
        $this->update(); 
        
        return $this->paid;
    }
    
    
    static function setReminderStatus($invoiceIds, $status)
    {
        Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'order_invoice set reminder_state=' . $status .
                        ', reminder_date=\'' . date('Y-m-d') . '\' where id_order_invoice in(' . implode(',', $invoiceIds) .
                        ')');
    }
    
    
    static function saveComment($id, $comment)
    {
        return Db::getInstance()->execute('update ' . _DB_PREFIX_ . 'order_invoice set comment=\''.addslashes($comment).
                '\' where id_order_invoice='.$id);
    }
    
    
    /**
     * Returns path to saved reminder file, if $checkExistance true returns empty string if file doen't exist, in case if
     * $checkExistance is false returns path anyway, even if file doesn't exist
     * @param type $invoiceId
     * @param type $reminderNum
     * @param type $checkExistance
     */
    static function getReminderFilePath($invoiceId, $reminderNum, $langId, $checkExistance=true)
    {
        $filePath = self::InvoiceReminderFolderPath.self::getReminderFileName($invoiceId, $reminderNum, $langId);
        if ($checkExistance && !file_exists($filePath))
        {
            return '';
        }
        
        return $filePath;
    }
    
    
    static function getReminderFileName($invoiceId, $reminderNum, $langId)
    {
        return $invoiceId.'_'.$reminderNum.'_'.Language::getIsoById($langId).'.pdf';
    }
    
    
    /**
     * Returns url to saved reminder file, if $checkExistance true returns empty string if file doen't exist, in case if
     * $checkExistance is false returns path anyway, even if file doesn't exist
     * @param type $invoiceId
     * @param type $reminderNum
     * @param type $checkExistance
     */
    static function getReminderFileUrl($invoiceId, $reminderNum, $langId, $checkExistance=true)
    {
        if ($checkExistance && self::getReminderFilePath($invoiceId, $reminderNum, $langId, $checkExistance)==false)
        {
            return '';
        }
        
        return self::InvoiceReminderFolderUrl.self::getReminderFileName($invoiceId, $reminderNum, $langId);
    }
    
    
    /**
     * Generates pdf reminder and saves it in reminders folder
     * @param type $invoiceId
     * @param type $reminderNum
     * @return string full path to generated file
     */
    static function generateReminder($invoice, $reminderNum, $langId)
    {
        $context = Context::getContext();
        // generate pdf
        $pdfRenderer = new PDFGenerator((bool) Configuration::get('PS_PDF_USE_CACHE'), '','A4', 0, '', 15, 15, 16,0,9,9);
        // $pdfRenderer->SetMargins(50, 10, 10); 
        $langIso = Language::getIsoById($langId);
        $pdfRenderer->setFontForLang($langIso);
        
        // assign data
        $context->smarty->assign(array(
            'customerFirstName' => $invoice['firstname'],
            'id_currency' => $invoice['id_currency'],
            'customerLastName' => $invoice['lastname'],
            'customerSalutation' => $invoice['salutation'] ? $invoice['salutation'] : 'Frau',
            'customerAddr1' => $invoice['address1'],
            'customerAddr2' => $invoice['address2'],
            'customerCity' => $invoice['city'],
            'customerPostcode' => $invoice['postcode'],
            'customerCountry' => $invoice['countryName'],
            'customerCompany' => $invoice['company'],
            'currentDate' => Tools::displayDateLang(date('Y-m-d'), $langId),
            'invoiceNumber' => sprintf('%1$s%2$06d', Configuration::get('PS_INVOICE_PREFIX', $langId, null, $context->shop->id), $invoice['number']),
            'invoiceDate' => Tools::displayDateLang($invoice['invoice_date'], $langId),
            'invoiceSumToPay' => $invoice['sum_to_pay'],
            'today10' => Tools::displayDateLang(date('Y-m-d', time() + 10 * 3600 * 24), $langId),
        ));
        $pdfRenderer->createHeader($context->smarty->fetch(_PS_MODULE_DIR_ . 'product_list/views/templates/reminders/'.$langIso.'/header.tpl'));
        $pdfRenderer->createFooter($context->smarty->fetch(_PS_MODULE_DIR_ . 'product_list/views/templates/reminders/'.$langIso.'/footer.tpl'), true);
        $pdfRenderer->createContent(
                $context->smarty->fetch(_PS_MODULE_DIR_ . 'product_list/views/templates/reminders/'.$langIso.'/top_address.tpl').
                $context->smarty->fetch(_PS_MODULE_DIR_ . 'product_list/views/templates/reminders/'.$langIso.'/reminder' . $reminderNum . '.tpl'));

        $pdfRenderer->writePage();
        
        // clean the output buffer
        if (ob_get_level() && ob_get_length() > 0)
        {
            ob_clean();
        }
        $filePath = self::getReminderFilePath($invoice['id_order_invoice'], $reminderNum, $langId, false);
        $pdfRenderer->render($filePath, 'F');
        
        return $filePath;
    }
}