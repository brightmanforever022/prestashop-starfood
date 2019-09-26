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

class NewsletterProMailChimpUsers
{
	public $user = array();
	public $users = array();
	public $errors = array();

	public $input_user;

	const ERROR_EMAIL_NOT_SET = 101;

	const USER_TYPE_CUSTOMER  = 'Customer';
	const USER_TYPE_VISITOR   = 'Visitor';
	const USER_TYPE_ADDED     = 'Added';

	public function __construct()
	{
	}

	public function addUserVar($name, $value)
	{
		$this->user[$name] = $value;
	}

	public function addUserGroupings($grouping)
	{
		if (!isset($this->user['GROUPINGS']))
			$this->user['GROUPINGS'] = array();

		$this->user['GROUPINGS'][] = $grouping;
	}

	public function inputUserGet($name)
	{
		if (isset($this->input_user[$name]))
			return $this->input_user[$name];
		return false;
	}

	public function inputUserExists($name)
	{
		if (isset($this->input_user[$name]))
			return true;
		return false;
	}

	public function addUser($input_user = array())
	{
		$this->input_user = $input_user;
		$this->user = array();

		if ($this->inputUserExists('email'))
			$this->setEmail($this->inputUserGet('email'));
		else
		{
			$this->addError('The field email is not set.', self::ERROR_EMAIL_NOT_SET);
			return false;
		}

		if ($this->inputUserExists('firstname'))
			$this->setFName($this->inputUserGet('firstname'));

		if ($this->inputUserExists('lastname'))
			$this->setLName($this->inputUserGet('lastname'));

		if ($this->inputUserExists('shop'))
			$this->setShop($this->inputUserGet('shop'));

		if ($this->inputUserExists('language'))
			$this->setLanguage($this->inputUserGet('language'));

		if ($this->inputUserExists('user_type'))
			$this->setUserType($this->inputUserGet('user_type'));

		if ($this->inputUserExists('ip'))
			$this->setIP($this->inputUserGet('ip'));

		if ($this->inputUserExists('lang_iso'))
			$this->setLanguageISO($this->inputUserGet('lang_iso'));

		if ($this->inputUserExists('phone'))
			$this->setPhone($this->inputUserGet('phone'));

		if ($this->inputUserExists('birthday'))
			$this->setBirthday($this->inputUserGet('birthday'));

		if ($this->inputUserExists('birthday'))
			$this->setBirthday($this->inputUserGet('birthday'));

		if ($this->inputUserExists('last_order'))
			$this->setLastOrder($this->inputUserGet('last_order'));

		if ($this->inputUserExists('date_add'))
			$this->setDateAdd($this->inputUserGet('date_add'));

		if ($this->inputUserExists('date'))
			$this->setDate($this->inputUserGet('date'));

		if ($this->inputUserExists('subscribed'))
			$this->setSubscribed($this->inputUserGet('subscribed'));

		if ($this->inputUserExists('phone_mobile'))
			$this->setPhoneMobile($this->inputUserGet('phone_mobile'));

		if ($this->inputUserExists('company'))
			$this->setCompany($this->inputUserGet('company'));

		if ($this->inputUserExists('groups'))
			$this->setGroups($this->inputUserGet('groups'));

		if ($this->inputUserExists('address'))
			$this->setAddress($this->inputUserGet('address'));

		$this->users[] = $this->getUser();
	}

	public function getUser()
	{
		return $this->user;
	}

	public function getUsers()
	{
		return $this->users;
	}

	public function setEmail($email)
	{
		$this->addUserVar('EMAIL', $email);
	}

	public function setFName($fname)
	{
		$this->addUserVar('FNAME', $fname);
	}

	public function setLName($lname)
	{
		$this->addUserVar('LNAME', $lname);
	}

	public function setGroups($cfg = array())
	{
		if (isset($cfg['groups']))
			$groups = $cfg['groups'];
		else
			$groups = array();

		$grouping = array();
		if (isset($cfg['id']))
			$grouping['id'] = $cfg['id'];
		if (isset($cfg['name']))
			$grouping['name'] = $cfg['name'];

		$grouping['groups'] = $groups;
		$this->addUserGroupings($grouping);
	}

	public function setShop($shop)
	{
		$this->addUserVar('SHOP', $shop);
	}

	public function setLanguage($language)
	{
		$this->addUserVar('LANGUAGE', $language);
	}

	public function setUserType($user_type)
	{
		$this->addUserVar('USER_TYPE', $user_type);
	}

	public function setLastOrder($date)
	{
		$date_fromated = self::makeDate($date);
		$this->addUserVar('LAST_ORDER', $date_fromated);
	}

	public function setSubscribed($subscribed)
	{
		$value = 'yes';
		switch ($subscribed)
		{
			case true:
				$value = 'yes';
				break;
			case false:
				$value = 'no';
				break;
			default:
				$value = 'yes';
				break;
		}

		$this->addUserVar('SUBSCRIBED', $value);
	}

	public function setPhoneMobile($mobile)
	{
		if (isset($mobile) && $mobile)
		{
			$mobile_formated = self::formatPhone($mobile);
			$this->addUserVar('PHONE_MOB', $mobile_formated);
		}
	}

	public function setCompany($company)
	{
		$this->addUserVar('COMPANY', $company);
	}

	public function setIP($ip)
	{
		$this->addUserVar('OPTIN_IP', $ip);
	}

	public function setBirthday($date)
	{
		$date_fromated = self::makeDate($date, 'm/d');
		$this->addUserVar('BIRTHDAY', $date_fromated);
	}

	public function setLanguageISO($iso)
	{
		$iso = Tools::strtolower($iso);
		$this->addUserVar('MC_LANGUAGE', $iso);
	}

	public function setAddress($address)
	{
		if (isset($address['addr1']))
			$address['addr1'] = $address['addr1'];

		if (isset($address['addr2']))
			$address['addr2'] = $address['addr2'];

		if (isset($address['city']))
			$address['city'] = $address['city'];

		if (isset($address['state']))
			$address['state'] = $address['state'];

		if (isset($address['zip']))
			$address['zip'] = $address['zip'];

		if (isset($address['country']))
			$address['country'] = Tools::strtoupper($address['country']);

		$this->addUserVar('ADDRESS', $address);
	}

	public function setDateAdd($date)
	{
		$date_fromated = self::makeDate($date);
		$this->addUserVar('DATE_ADD', $date_fromated);
	}

	public function setDate($date)
	{
		$date_fromated = self::makeDate($date);
		$this->addUserVar('DATE', $date_fromated);
	}

	public function setPhone($phone)
	{
		if (isset($phone) && $phone)
		{
			$phone_formated = self::formatPhone($phone);
			$this->addUserVar('PHONE', $phone_formated);
		}
	}

	public static function makeDate($date, $format = 'm/d/Y')
	{
		return NewsletterProMailChimpApi::makeDate($date, $format);
	}

	public static function formatPhone($phone)
	{
		return NewsletterProMailChimpApi::formatPhone($phone);
	}

	public function addError($error, $code = null)
	{
		$add_error = array(
			'code' => $code,
			'error' => Tools::displayError($error),
		);

		$this->errors[] = $add_error;
	}

	public function hasErrors()
	{
		return !empty($this->errors);
	}

	public function getErrors($only_errors = false, $collapse_same_code = false)
	{
		$errors = $this->errors;

		if ($collapse_same_code)
		{
			$errors_collapse = array();
			$errors_coldes = array();

			foreach ($errors as $error)
				if (!in_array($error['code'], $errors_coldes))
				{
					$errors_collapse[] = $error;
					$errors_coldes[] = $error['code'];
				}

			$errors = $errors_collapse;
		}

		$return_errors = array();
		if ($only_errors)
		{
			foreach ($errors as $error)
				$return_errors[] = $error['error'];
		}
		else
			$return_errors = $errors;

		return $return_errors;
	}
}