<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

class NewsletterProBlockNewsletter extends ObjectModel
{
	public $id_shop;

	public $id_shop_group;

	public $email;

	public $ip_registration_newsletter;

	public $http_referer;

	public $active;

	public static $definition = array(
		'table'     => 'newsletter',
		'primary'   => 'id',
		'fields' => array(
			'id_shop'                    => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_shop_group'              => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'email'                      => array('type' => self::TYPE_STRING, 'validate' => 'isEmail', 'required' => true),
			'ip_registration_newsletter' => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'http_referer'               => array('type' => self::TYPE_STRING, 'validate' => 'isString'),
			'active'                     => array('type' => self::TYPE_BOOL, 'validate' => 'isBool'),
		)
	);

	public function __construct($id = null)
	{
		parent::__construct($id);
	}

	public function newInstance($id = null)
	{
		return new self($id);
	}
}