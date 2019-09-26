<?php
class Customer extends CustomerCore {
    public $phone;
    public $phone_mobile;
    public $address1;
    public $address2;
    public $postcode;
    public $city;
    
    public $id_country;
    
    public $fax;
    public $receivable_number;
    public $client_number;
    
    public static $definition = array(
        'table' => 'customer',
        'primary' => 'id_customer',
        'fields' => array(
            'secure_key' =>                array('type' => self::TYPE_STRING, 'validate' => 'isMd5', 'copy_post' => false),
            'lastname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'firstname' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isName', 'required' => true, 'size' => 32),
            'email' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true, 'size' => 128),
            'passwd' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isPasswd', 'required' => true, 'size' => 32),
            'last_passwd_gen' =>            array('type' => self::TYPE_STRING, 'copy_post' => false),
            'id_gender' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'birthday' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isBirthDate'),
            'newsletter' =>                array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'newsletter_date_add' =>        array('type' => self::TYPE_DATE,'copy_post' => false),
            'ip_registration_newsletter' =>    array('type' => self::TYPE_STRING, 'copy_post' => false),
            'optin' =>                        array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
            'website' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isUrl'),
            'company' =>                    array('type' => self::TYPE_STRING, 'validate' => 'isGenericName'),
            'siret' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'ape' =>                        array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'year_open' =>                    array('type' => self::TYPE_INT, 'validate' => 'isInt'),
            'size_shop'=>                   array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'reference_brand'=>             array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'phone' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'phone_mobile' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'address1' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'address2' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'postcode' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'city'  => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
            'outstanding_allow_amount' =>    array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'copy_post' => false),
            'show_public_prices' =>            array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_risk' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'max_payment_days' =>            array('type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'copy_post' => false),
            'active' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'deleted' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'note' =>                        array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 65000, 'copy_post' => false),
            'is_guest' =>                    array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'copy_post' => false),
            'id_shop' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_shop_group' =>                array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'id_default_group' =>            array('type' => self::TYPE_INT, 'copy_post' => false),
            'id_lang' =>                    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'copy_post' => false),
            'date_add' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' =>                    array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_country' =>                  array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'fax' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 128,
                'required' => false
            ),
            'receivable_number' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 64,
                'required' => false
            ),
            'client_number' => array(
                'type' => self::TYPE_STRING,
                'validate' => 'isString',
                'size' => 64,
                'required' => false
            )
            
        ),
    );
    
    
    public function update($nullValues = false)
    {
        if (!$this->deleted && !empty($this->siret)) 
        {
            Db::getInstance()->execute('update '._DB_PREFIX_.'address set vat_number=\''.addslashes($this->siret).'\', dni=\''.
                    addslashes($this->siret).'\', company=\''.addslashes($this->company).'\' where id_customer='.$this->id);
        }
        return parent::update(true);
    }
    
    
    public function getBoughtProducts($addPhoto = false)
    {
        $sql = 'SELECT o.*, od.*'.($addPhoto?', i.id_image':'').' FROM `'._DB_PREFIX_.'orders` o	LEFT JOIN `'._DB_PREFIX_.
                'order_detail` od ON o.id_order = od.id_order';
        if ($addPhoto)
        {
            $sql .= ' left join '._DB_PREFIX_.'image i on i.id_product=od.product_id and cover=1';
        }
	$sql .= ' WHERE o.valid = 1 AND o.`id_customer` = '.(int)$this->id;
                
        return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);       
    }
    
}

/**
ALTER TABLE `prs_customer` 
ADD `fax` VARCHAR(128) NOT NULL AFTER `id_country`, 
ADD `receivable_number` VARCHAR(64) NOT NULL AFTER `fax`, 
ADD `client_number` VARCHAR(64) NOT NULL AFTER `receivable_number`;
 */