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

class NewsletterProMailChimpController extends NewsletterProMailChimp
{
	public $module;
	public $configuration;
	public $id_list;
	public $id_grouping;

	public $sync_step = 500;

	private $response;

	public static $instance;

	const ERROR_NO_LIST            = 100;
	const ERROR_NO_GROUPING        = 101;
	const ERROR_NO_CUSTOMERS_GROUP = 102;
	const ERROR_DELITING_GROUP     = 103;
	const ERROR_CREATE_GROUP       = 104;
	const ERROR_CHIMP_UPDATE_NAME  = 105;

	const CONFIG_NAME = 'CHIMP';
	const GROUPINGS_NAME = 'Customer Group';

	public function __construct($key)
	{
		self::$instance =& $this;
		parent::__construct($key);

		$this->module = NewsletterPro::getInstance();

		$this->initInitConfiguration();
		$this->initConfiguration();

		$this->id_list     = $this->getContextConfig('ID_LIST');
		$this->id_grouping = $this->getContextConfig('ID_GROUPING');

		$this->response = NewsletterProAjaxResponse::newInstance();
	}

	public function install()
	{
		return ($this->installFields() && $this->installGrouping($this->id_grouping, self::GROUPINGS_NAME));
	}

	public function uninstall()
	{
		return true;
	}

	public function initInitConfiguration()
	{
		if (isset($this->module->ini_config))
		{
			$config = $this->module->ini_config;
			if (isset($config['chimp_sync_step']))
				$this->sync_step = (int)$config['chimp_sync_step'];
		}
	}

	public function initConfiguration()
	{
		if (!isset($this->module->configuration[self::CONFIG_NAME]))
			$this->module->configuration[self::CONFIG_NAME] = array();

		$this->configuration =& $this->module->configuration[self::CONFIG_NAME];
	}

	public function getInstance()
	{
		return self::$instance;
	}

	public function isInstalled()
	{
		return $this->getContextConfig('INSTALLED');
	}

	public function installConfiguration($api_key, $list_id)
	{
		$configuration = $this->module->configuration;

		$configuration['CHIMP'] = array(
			'INSTALLED'           => false,
			'API_KYE'             => $api_key,
			'ID_LIST'             => $list_id,
			'ID_GROUPING'         => '',
			'CUSTOMERS_GROUP_IDS' => array(),
			'FIELDS'              => array(),
			'CUSTOMERS_CHECKBOX'  => 0,
			'VISITORS_CHECKBOX'   => 0,
			'ADDED_CHECKBOX'      => 0,
			'ORDERS_CHECKBOX'     => 0,
		);

		return $this->module->updateDbConfiguration();
	}

	public function getConfiguration($name)
	{
		return $this->module->getConfiguration($name);
	}

	public function updateConfiguration($name, $value)
	{
		return $this->module->updateConfiguration($name, $value);
	}

	public function updateContextConfig($name, $value)
	{
		$this->configuration[$name] = $value;

		switch ($name)
		{
			case 'ID_LIST':
				$this->id_list = $value;
				break;

			case 'ID_GROUPING':
				$this->id_grouping = $value;
				break;
		}

		return $this->module->updateDbConfiguration();
	}

	public function deleteContextConfig($name)
	{
		if (isset($this->configuration[$name]))
			unset($this->configuration[$name]);
		$this->module->updateDbConfiguration();
	}

	public function getContextConfig($name)
	{
		if (isset($this->configuration[$name]))
			return $this->configuration[$name];
		return false;
	}

	public function installGrouping($id_grouping = 0, $grouping_name)
	{
		$errors = array();

		$shop_config = $this->getShopConfig();
		$shop_groups = $shop_config['groups'];
		$grouping_creation = self::grep($shop_groups, 'name');

		$id_list = $this->id_list;
		$grouping = $this->listGetGroupings($id_list);

		if ($grouping)
		{
			foreach ($grouping as $grouping_child)
			{
				if ($grouping_child['id'] == $id_grouping)
				{
					$group_user_type = $grouping_child;
					break;
				}
			}

			if (isset($group_user_type))
			{
				$groups_name = self::grep($group_user_type['groups'], 'name');
				$groups_name_remove = array_diff($groups_name, $grouping_creation);
				$groups_name_add = array_diff($grouping_creation, $groups_name);

				foreach ($groups_name_remove as $name)
				{
					$response = $this->listDeleteGroup($id_list, $name, $id_grouping);
					if (!$response)
						$this->mergeErrors($errors);
				}

				foreach ($groups_name_add as $group_name)
					$this->listAddGroup($id_list, $group_name, $id_grouping);
			}
			else
			{
				$grouping_creation = array_unique($grouping_creation);
				$response = $this->listAddGrouping($id_list, $grouping_name, 'checkboxes', $grouping_creation);

				if ($response)
				{
					// update configuration with group id
					$id_grouping = $response;
				}
				else
				{
					foreach ($grouping as $grouping_child)
					{
						if ($grouping_child['name'] == $grouping_name)
						{
							$id_grouping_delete = $grouping_child['id'];
							break;
						}
					}

					if (isset($id_grouping_delete))
					{
						$response = $this->listDeleteGrouping($id_grouping_delete);

						if ($response)
						{
							$response = $this->listAddGrouping($id_list, $grouping_name, 'checkboxes', $grouping_creation);

							if ($response)
								$id_grouping = $response;
							else
								$this->mergeErrors($errors);
						}
						else
							$this->mergeErrors($errors);
					}
					else
						$this->mergeErrors($errors);
				}
			}
		}
		else
		{
			$id_grouping = 0;
			$response = $this->listAddGrouping($id_list, $grouping_name, 'checkboxes', $grouping_creation);
				if ($response)
					$id_grouping = $response;

			$this->mergeErrors($errors);
		}

		$this->updateContextConfig('ID_GROUPING', $id_grouping);

		if (empty($errors))
			return true;
		else
			$this->errors = $errors;

		return false;
	}

	public function getAddedSql($cfg = array())
	{
		$select = null;
		$join   = null;
		$where  = null;
		$and    = null;
		$end    = null;

		extract($cfg);

		$sql = 'SELECT 	n.`id_newsletter_pro_email` AS `id`, 
						n.`email`, 
						n.`firstname`, 
						n.`lastname`, 
						n.`id_shop`, 
						n.`id_lang`,
						n.`date_add`, 
						n.`active`, 
						n.`ip_registration_newsletter` AS `ip`,
						s.`name` AS `shop_name`, 
						l.`name` AS `language`,
						l.`iso_code` AS `lang_iso`
						';
		$sql .= (isset($select) ? ' , '.$select.' ': '');
		$sql .= ' FROM `'._DB_PREFIX_.'newsletter_pro_email` n 
				LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`) ';
		$sql .= (isset($join) ? ' '.$join.' ': '');
		$sql .= (isset($where) ? ' WHERE '.$where.' ': ' WHERE 1 ');
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND n.`active` = 1 ' : '';
		$sql .= (isset($and) ? ' AND '.$and.' ': '');
		$sql .= ' ORDER BY n.`id_newsletter_pro_email` ASC';
		$sql .= (isset($end) ? ' '.$end.' ': '');
		$sql .= ';';

		return $sql;
	}

	public function getVisitorsSql($cfg = array())
	{
		$select = null;
		$join   = null;
		$where  = null;
		$and    = null;
		$end    = null;

		extract($cfg);

		$sql_check = "SELECT COUNT(*) AS `count` 
					FROM INFORMATION_SCHEMA.TABLES 
					WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
					AND TABLE_NAME = '"._DB_PREFIX_."newsletter'";

		if (Db::getInstance()->getValue($sql_check))
		{
			$sql = 'SELECT  n.`id`, n.`id_shop`, 
							n.`ip_registration_newsletter` AS `ip`, 
							n.`newsletter_date_add` AS `date_add`,
							n.`email`, n.`active`, 
							sh.`name` AS `shop_name` 
							';

			$sql .= (isset($select) ? ' , '.$select.' ': '');

			$sql .= ' FROM `'._DB_PREFIX_.'newsletter` n
					INNER JOIN `'._DB_PREFIX_.'shop` sh ON (n.`id_shop` = sh.`id_shop`) ';
			$sql .= (isset($join) ? ' '.$join.' ': '');

			$sql .= (isset($where) ? ' WHERE '.$where.' ': ' WHERE 1 ');
			$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND n.`active` = 1 ' : '';
			$sql .= (isset($and) ? ' AND '.$and.' ': '');

			$sql .= ' ORDER BY n.`id` ASC ';
			$sql .= (isset($end) ? ' '.$end.' ': '');
			$sql .= ';';

			return $sql;
		}
		else
			return array();
	}

	public function getVisitorsNPSql($cfg = array())
	{
		$select = null;
		$join   = null;
		$where  = null;
		$and    = null;
		$end    = null;

		extract($cfg);

		$sql = 'SELECT 	n.`id_newsletter_pro_subscribers` AS `id`, 
						n.`email`, 
						n.`birthday`, 
						n.`firstname`, 
						n.`lastname`, 
						n.`id_shop`, 
						n.`id_lang`,
						n.`id_gender`,
						n.`date_add`, 
						n.`active`, 
						n.`ip_registration_newsletter` AS `ip`,
						s.`name` AS `shop_name`, 
						l.`name` AS `language`,
						l.`iso_code` AS `lang_iso`
						';
		$sql .= (isset($select) ? ' , '.$select.' ': '');
		$sql .= ' FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` n 
				LEFT JOIN `'._DB_PREFIX_.'lang` l ON (l.`id_lang` = n.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'shop` s ON (s.`id_shop` = n.`id_shop`) ';
		$sql .= (isset($join) ? ' '.$join.' ': '');
		$sql .= (isset($where) ? ' WHERE '.$where.' ': ' WHERE 1 ');
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND n.`active` = 1 ' : '';
		$sql .= (isset($and) ? ' AND '.$and.' ': '');
		$sql .= ' ORDER BY n.`id_newsletter_pro_subscribers` ASC';
		$sql .= (isset($end) ? ' '.$end.' ': '');
		$sql .= ';';

		return $sql;
	}

	public function getAdded($start_id = null, $limit = null)
	{
		$cfg = array();

		if (isset($start_id))
			$cfg['and'] = 'n.`id_newsletter_pro_email` >='.(int)$start_id;

		if (isset($limit))
			$cfg['end'] = 'LIMIT '.(int)$limit;

		$sql = $this->getAddedSql($cfg);
		$result = Db::getInstance()->executeS($sql);

		return $result;
	}

	public function getAddedByDate($from, $to = null)
	{
		$and = ' n.`date_add` >= "'.pSQL($from).'" ';
		if (isset($to))
			$and .= '  AND n.`date_add` <= "'.pSQL($to).'" ';

		$sql = $this->getAddedSql(array(
			'and' => $and,
		));
		$result = Db::getInstance()->executeS($sql);

		return $result;
	}

	public function syncAddedByDate($from, $to = null)
	{
		$added = $this->getAddedByDate($from, $to);
		$users = $this->getAddedUsers($added);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $added,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function getAddedUsers($added)
	{
		$chimp_users = new NewsletterProMailChimpUsers();
		foreach ($added as $add)
		{
			$chimp_users->addUser(array(
				'email'      => $add['email'],
				'firstname'  => $add['firstname'],
				'lastname'   => $add['lastname'],
				'shop'       => $add['shop_name'],
				'language'   => $add['language'],
				'user_type'  => NewsletterProMailChimpUsers::USER_TYPE_ADDED,
				'ip'         => $add['ip'],
				'lang_iso'   => $add['lang_iso'],
				'subscribed' => $add['active'],
				'date_add'   => $add['date_add'],
				'date'       => date('m/d/Y'),
			));
		}

		return $chimp_users->getUsers();
	}

	public function getVisitorsNPUsers($vistitors)
	{
		$chimp_users = new NewsletterProMailChimpUsers();
		foreach ($vistitors as $vistitor)
		{
			$chimp_users->addUser(array(
				'email'      => $vistitor['email'],
				'birthday'   => $vistitor['birthday'],
				'firstname'  => $vistitor['firstname'],
				'lastname'   => $vistitor['lastname'],
				'shop'       => $vistitor['shop_name'],
				'language'   => $vistitor['language'],
				'user_type'  => NewsletterProMailChimpUsers::USER_TYPE_VISITOR,
				'ip'         => $vistitor['ip'],
				'lang_iso'   => $vistitor['lang_iso'],
				'subscribed' => $vistitor['active'],
				'date_add'   => $vistitor['date_add'],
				'date'       => date('m/d/Y'),
			));
		}

		return $chimp_users->getUsers();
	}

	public function syncAdded($start_id = null, $limit = null)
	{
		$added = $this->getAdded($start_id, $limit);
		$users = $this->getAddedUsers($added);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $added,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function getVisitorsByDate($from, $to = null)
	{
		$and = ' n.`newsletter_date_add` >= "'.pSQL($from).'" ';
		if (isset($to))
			$and .= '  AND n.`newsletter_date_add` <= "'.pSQL($to).'" ';

		$sql = $this->getVisitorsSql(array(
			'and' => $and,
		));

		$result = Db::getInstance()->executeS($sql);
		return $result;
	}

	public function getVisitorsNPByDate($from, $to = null)
	{
		$and = ' n.`date_add` >= "'.pSQL($from).'" ';
		if (isset($to))
			$and .= '  AND n.`date_add` <= "'.pSQL($to).'" ';

		$sql = $this->getVisitorsNPSql(array(
			'and' => $and,
		));

		$result = Db::getInstance()->executeS($sql);
		return $result;
	}

	public function getVisitors($start_id = null, $limit = null)
	{
		$cfg = array();

		if (isset($start_id))
			$cfg['and'] = 'n.`id` >='.(int)$start_id;

		if (isset($limit))
			$cfg['end'] = 'LIMIT '.(int)$limit;

		$sql = $this->getVisitorsSql($cfg);
		$result = Db::getInstance()->executeS($sql);
		return $result;
	}

	public function getVisitorsNP($start_id = null, $limit = null)
	{
		$cfg = array();

		if (isset($start_id))
			$cfg['and'] = 'n.`id_newsletter_pro_subscribers` >='.(int)$start_id;

		if (isset($limit))
			$cfg['end'] = 'LIMIT '.(int)$limit;

		$sql = $this->getVisitorsNPSql($cfg);
		$result = Db::getInstance()->executeS($sql);
		return $result;
	}

	public function countCustomers()
	{
		$sql = 'SELECT 	COUNT(*) FROM `'._DB_PREFIX_.'customer` WHERE 1 ';
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND `newsletter` = 1 ' : '';

		return Db::getInstance()->getValue($sql);
	}

	public function countOrders($from)
	{
		return Db::getInstance()->getValue('SELECT 	COUNT(*) FROM `'._DB_PREFIX_.'orders` WHERE `date_add` > "'.pSQL($from).'" ');
	}

	public function countVisitors()
	{
		$sql = 'SELECT 	COUNT(*) FROM `'._DB_PREFIX_.'newsletter` WHERE 1 ';
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND `active` = 1 ' : '';

		return Db::getInstance()->getValue($sql);
	}

	public function countVisitorsNP()
	{
		$sql = 'SELECT 	COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_subscribers` WHERE 1';
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND `active` = 1 ' : '';

		return Db::getInstance()->getValue($sql);
	}

	public function countAdded()
	{
		$sql = 'SELECT 	COUNT(*) FROM `'._DB_PREFIX_.'newsletter_pro_email` WHERE 1';
		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND `active` = 1 ' : '';

		return Db::getInstance()->getValue($sql);
	}

	public function getCustomersSql($cfg = array())
	{
		$select = null;
		$from   = null;
		$join   = null;
		$where  = null;
		$and    = null;
		$end    = null;

		extract($cfg);

		$sql = 'SELECT 	c.`firstname`, c.`lastname`, c.`email`, c.`id_customer` AS `id`,
						c.`id_lang`, c.`id_shop`, c.`id_default_group`, c.`newsletter`, sh.`name` AS `shop_name`,
						GROUP_CONCAT(gl.`name`) AS `groups`,
						npcc.`categories`,
						c.`date_add`,
						c.`birthday`,
						lg.`name` AS `language`,
						c.`ip_registration_newsletter` AS `ip`,
						lg.`iso_code` AS `lang_iso`,
						adr.`company` AS `company`,
						adr.`lastname` AS `address_lastname`,
						adr.`firstname` AS `address_firstname`,
						adr.`address1` AS `address1`,
						adr.`address2` AS `address2`,
						adr.`postcode` AS `postcode`,
						adr.`city` AS `city`,
						adr.`phone` AS `phone`,
						adr.`phone_mobile` AS `phone_mobile`,
						cty.`iso_code` AS `country_iso`,
						sta.`name` AS state,
						po.`date_add` as `last_order` ';

		$sql .= (isset($select) ? ', '.$select.' ': '');
		$sql .= ' FROM `'._DB_PREFIX_.'customer` c ';
		$sql .= (isset($from) ? ' '.$from.' ': '');
		$sql .= ' LEFT JOIN `'._DB_PREFIX_.'customer_group` cg ON ( cg.`id_customer` = c.`id_customer` )
				LEFT JOIN `'._DB_PREFIX_.'newsletter_pro_customer_category` npcc ON (c.`id_customer` = npcc.`id_customer`) 
				LEFT JOIN `'._DB_PREFIX_.'lang` lg ON (c.`id_lang` = lg.`id_lang`)
				LEFT JOIN `'._DB_PREFIX_.'shop` sh ON (c.`id_shop` = sh.`id_shop`) 
				LEFT JOIN `'._DB_PREFIX_.'group_lang` gl ON (cg.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)pqnp_config('PS_LANG_DEFAULT').' )
				LEFT JOIN `'._DB_PREFIX_.'address` adr ON 
					(
						c.`id_customer` = adr.`id_customer`
						AND adr.id_address = (
							SELECT MAX(id_address) FROM `'._DB_PREFIX_.'address`
								WHERE `id_customer` = adr.`id_customer`
								LIMIT 1
						)
					) 
				LEFT JOIN `'._DB_PREFIX_.'country` cty ON (cty.`id_country` = adr.`id_country`)
				LEFT JOIN `'._DB_PREFIX_.'state` sta ON (sta.`id_state` = adr.`id_state`)
				LEFT JOIN  `'._DB_PREFIX_.'orders` po 
					ON (c.`id_customer` = po.`id_customer`
						AND po.`date_add` = (
							SELECT MAX(`date_add`)
							FROM `'._DB_PREFIX_.'orders`
							WHERE `id_customer` = po.`id_customer`
						)
					)
				';
		$sql .= (isset($join) ? ' '.$join.' ': '');

		$sql .= (isset($where) ? ' WHERE '.$where.' ': ' WHERE 1 ');

		$sql .= !(int)pqnp_config('CHIMP_SYNC_UNSUBSCRIBED') ? ' AND c.`newsletter` = 1 ' : '';

		$sql .= (isset($and) ? ' AND '.$and.' ': '');

		$sql .= ' GROUP BY c.id_customer 
				ORDER BY c.`id_customer` ASC ';
		$sql .= (isset($end) ? ' '.$end.' ': '');
		$sql .= ';';

		return $sql;
	}

	public function getCustomers($start_id = null, $limit = null)
	{
		$cfg = array();

		if (isset($start_id))
			$cfg['and'] = 'c.`id_customer` >='.(int)$start_id;

		if (isset($limit))
			$cfg['end'] = 'LIMIT '.(int)$limit;

		$sql = $this->getCustomersSql($cfg);
		$result = Db::getInstance()->executeS($sql);

		return $result;
	}

	public function getCustomersByDate($from, $to = null)
	{
		$and = ' c.`date_upd` >= "'.pSQL($from).'" ';
		if (isset($to))
			$and .= '  AND c.`date_upd` <= "'.pSQL($to).'" ';

		$sql = $this->getCustomersSql(array(
			'and' => $and,
		));
		$result = Db::getInstance()->executeS($sql);

		return $result;
	}

	public function getCustomersUsers($customers)
	{
		$chimp_users = new NewsletterProMailChimpUsers();
		foreach ($customers as $customer)
		{
			$groups = explode(',', trim($customer['groups'], ','));

			if (isset($groups[0]) && !$groups[0])
				$groups = array();

			$chimp_users->addUser(array(
				'email' => $customer['email'],
				'firstname' => $customer['firstname'],
				'lastname' => $customer['lastname'],
				'shop' => $customer['shop_name'],
				'language' => $customer['language'],
				'user_type' => NewsletterProMailChimpUsers::USER_TYPE_CUSTOMER,
				'ip' => $customer['ip'],
				'lang_iso' => $customer['lang_iso'],
				'phone' => $customer['phone'],
				'phone_mobile' => $customer['phone_mobile'],
				'company' => $customer['company'],
				'subscribed' => $customer['newsletter'],
				'birthday' => $customer['birthday'],
				'last_order' => $customer['last_order'],
				'date_add' => $customer['date_add'],
				'date' => date('m/d/Y'),
				'groups' => array(
					'id' => $this->id_grouping,
					'groups' => $groups,
				),
				'address' => array(
					'addr1' => $customer['address1'],
					'addr2' => $customer['address2'],
					'city' => $customer['city'],
					'state' => $customer['state'],
					'zip' => $customer['postcode'],
					'country' => $customer['country_iso'],
				),
			));
		}

		return $chimp_users->getUsers();
	}

	public function getSyncEmptyResonse()
	{
		return array(
				'db_users'       => array(),
				'chimp_response' =>
					array(
						'adds' => array(),
						'updates' => array(),
						'errors' => array(),
					),
				);
	}

	public function syncOrdersByDate($date)
	{
		// syncOrdersByDate
		$orders = NewsletterProMailChimpOrder::getOrdersIdSinceDate($date);

		if (empty($orders))
			return $this->getSyncEmptyResonse();
		else
		{
			$response = array();
			foreach ($orders as $order)
			{
				$id_order = $order['id_order'];

				$mc_order = NewsletterProMailChimpOrder::newInstance($id_order, Configuration::get('PS_CURRENCY_DEFAULT'));
				$response[$id_order] = $this->orderAdd($mc_order->toArray());
			}

			return array(
				'db_users'       => $orders,
				'chimp_response' => $response
			);
		}
		
	}

	public function syncCustomersByDate($from, $to = null)
	{
		$customers = $this->getCustomersByDate($from, $to);
		$users = $this->getCustomersUsers($customers);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $customers,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function syncCustomers($start_id = null, $limit = null)
	{
		$customers = $this->getCustomers($start_id, $limit);
		$users = $this->getCustomersUsers($customers);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $customers,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function getVisitorsUsers($vistitors)
	{
		$default_language = Language::getLanguage((int)$this->getConfiguration('PS_LANG_DEFAULT'));

		$chimp_users = new NewsletterProMailChimpUsers();
		foreach ($vistitors as $vistitor)
		{
			$chimp_users->addUser(array(
				'email' => $vistitor['email'],
				'shop' => $vistitor['shop_name'],
				'language' => $default_language['name'],
				'user_type' => NewsletterProMailChimpUsers::USER_TYPE_VISITOR,
				'ip' => $vistitor['ip'],
				'lang_iso' => $default_language['iso_code'],
				'subscribed' => $vistitor['active'],
				'date_add' => $vistitor['date_add'],
				'date' => date('m/d/Y'),
			));
		}

		return $chimp_users->getUsers();
	}

	public function syncVisitorsByDate($from, $to = null)
	{
		$vistitors = $this->getVisitorsByDate($from, $to);
		$users = $this->getVisitorsUsers($vistitors);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $vistitors,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function syncVisitorsNPByDate($from, $to = null)
	{
		$vistitors = $this->getVisitorsNPByDate($from, $to);
		$users = $this->getVisitorsNPUsers($vistitors);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $vistitors,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function syncVisitors($start_id = null, $limit = null)
	{
		$vistitors = $this->getVisitors($start_id, $limit);
		$users = $this->getVisitorsUsers($vistitors);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $vistitors,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function syncVisitorsNP($start_id = null, $limit = null)
	{
		$vistitors = $this->getVisitorsNP($start_id, $limit);
		$users = $this->getVisitorsNPUsers($vistitors);

		if (empty($users))
			return $this->getSyncEmptyResonse();
		else
		{
			return array(
				'db_users'       => $vistitors,
				'chimp_response' => $this->subscribe($this->id_list, $users)
			);
		}
	}

	public function getShopConfig()
	{
		$config = array();

		$id_lang_default                 = $this->getConfiguration('PS_LANG_DEFAULT');
		$config['id_lang_default']       = $id_lang_default;
		$config['locale_country']        = $this->getConfiguration('PS_LOCALE_COUNTRY');
		$config['id_shop_default']       = $this->getConfiguration('PS_SHOP_DEFAULT');

		$groups                          = Group::getGroups($id_lang_default);
		$config['groups']                = $groups;
		$config['groups_name']           = self::grep($groups, 'name');

		$shops                           = Shop::getShops(false);
		$config['shops']                 = $shops;
		$config['shops_name']            = self::grep($shops, 'name');
		$config['default_shop_name']     = $this->getConfiguration('PS_SHOP_NAME');

		$default_language                = Language::getLanguage($id_lang_default);
		$config['default_language']      = $default_language;
		$config['default_language_name'] = $default_language['name'];

		$languages                       = Language::getLanguages(false);
		$config['languages']             = $languages;
		$config['languages_name']        = self::grep($languages, 'name');

		return $config;
	}

	private function getGroupingById($id, $groupings)
	{
		foreach ($groupings as $group)
			if ($group['id'] == $id)
				return $group;
		return false;
	}

	private function deleteGroups($id_list, $id_grouping, &$groups_chimp)
	{
		$errors = array();
		foreach ($groups_chimp as $key => $group)
		{
			$response = $this->listDeleteGroup($id_list, $group['name'], $id_grouping);
			if (!$response)
				$errors[self::ERROR_DELITING_GROUP] = $this->getErrors(true);
			else
				unset($groups_chimp[$key]);
		}

		return $errors;
	}

	private function createGroups($id_list, $id_grouping, &$groups_shop)
	{
		$errors = array();
		foreach ($groups_shop as $key => $group)
		{
			$response = $this->listAddGroup($id_list, $group['name'], $id_grouping);
			if (!$response)
				$errors[self::ERROR_CREATE_GROUP] = $this->getErrors(true);
			else
				unset($groups_shop[$key]);
		}
		return $errors;
	}

	private function writeGroupsConfig($id_list, $id_grouping)
	{
		$config = array();
		$groupings 	     = $this->listGetGroupings($id_list);
		$customers_group = $this->getGroupingById($id_grouping, $groupings);
		$shop_config     = $this->getShopConfig();

		$groups_chimp    = $customers_group['groups'];
		$groups_shop     = $shop_config['groups'];

		foreach ($groups_chimp as $chimp_group)
		{
			foreach ($groups_shop as $shop_group)
			{
				if ($shop_group['name'] == $chimp_group['name'])
					$config[$shop_group['id_group']] = $chimp_group['id'];
			}
		}

		$this->updateContextConfig('CUSTOMERS_GROUP_IDS', $config);

		return $config;
	}

	public function checkChimpConfig()
	{
		$errors = array();
		$shop_config = $this->getShopConfig();

		// the id_list is the same with the ID_LIST fom configuration
		$id_list = $this->id_list;
		$list = $this->getListById($id_list);
		if (!$list)
			$errors[self::ERROR_NO_LIST] = $this->getErrors(true);
		else
		{
			$groupings = $this->listGetGroupings($id_list);

			if (!$groupings)
				$errors[self::ERROR_NO_GROUPING] = $this->getErrors(true);
			else
			{
				// the id_grouping is the same with the ID_GROUPING fom configuration
				$id_grouping = $this->id_grouping;
				$customers_group = $this->getGroupingById($id_grouping, $groupings);

				if (!$customers_group)
					$errors[self::ERROR_NO_CUSTOMERS_GROUP] = array('The grouping Customers Group is not find with the id '.$id_grouping.'.');
				else
				{
					$groups_chimp = $customers_group['groups'];
					$groups_shop  = $shop_config['groups'];

					$customers_group_ids = $this->getContextConfig('CUSTOMERS_GROUP_IDS');

					$groups_chimp_ids = array();
					foreach ($groups_chimp as $key => $group)
						$groups_chimp_ids[$key] = $group['id'];

					$groups_shop_ids = array();
					foreach ($groups_shop as $key => $group)
						$groups_shop_ids[$key] = $group['id_group'];

					foreach ($customers_group_ids as $group_shop_id => $group_chimp_id)
					{
						$search_shop_key  = array_search($group_shop_id, $groups_shop_ids);
						$search_chimp_key = array_search($group_chimp_id, $groups_chimp_ids);

						if (self::searchFind($search_shop_key) && self::searchFind($search_chimp_key))
						{
							$item_shop  = $groups_shop[$search_shop_key];

							if (isset($groups_chimp[$search_chimp_key]))
							{
								$item_chimp = $groups_chimp[$search_chimp_key];

								if ($item_shop['name'] != $item_chimp['name'])
								{
									$response = $this->listUpdateGroup($id_list, $item_chimp['name'], $item_shop['name']);
									if (!$response)
										$errors[self::ERROR_CHIMP_UPDATE_NAME] = $this->getErrors(true);
								}
								unset($groups_chimp[$search_chimp_key]);
							}

							unset($groups_shop[$search_shop_key]);
						}
						else
						{
							// not necessary
							unset($customers_group_ids[$group_shop_id]);
						}
					}

					// remove chimp unused groups
					$response = $this->deleteGroups($id_list, $id_grouping, $groups_chimp);
					if (!empty($response))
						self::arrayMerge($errors, $response);

					// add chimp groups
					$response = $this->createGroups($id_list, $id_grouping, $groups_shop);
					if (!empty($response))
						self::arrayMerge($errors, $response);

					$this->writeGroupsConfig($id_list, $id_grouping);
				}
			}
		}

		return $errors;
	}

	public function syncCheck()
	{
		$errors          = array();
		$codes           = array();
		$response_errors = $this->checkChimpConfig();
		$shop_config     = $this->getShopConfig();

		if (!empty($response_errors))
		{
			$codes = array_keys($response_errors);

			if (in_array(self::ERROR_NO_LIST, $codes))
				$errors[] = 'The chimp list do not exists.';

			if (in_array(self::ERROR_NO_CUSTOMERS_GROUP, $codes) || in_array(self::ERROR_NO_GROUPING, $codes))
			{
				if (!$this->installGrouping( $this->id_grouping, self::GROUPINGS_NAME))
					array_merge($errors, $this->errors);
			}

			if (in_array(self::ERROR_CHIMP_UPDATE_NAME, $codes))
				$errors[] = 'The the name of the group cannot be updated.';
		}

		if (!in_array(self::ERROR_NO_LIST, $codes))
		{
			$list = $this->listGetVars($this->id_list);

			if (!$list)
				$this->mergeErrors($errors);
			else
			{
				$db_fields = $this->getContextConfig('FIELDS');
				$db_fields_name = self::grep($db_fields, 'tag');

				$fields = $list['merge_vars'];
				$fields_tag = array();

				foreach ($fields as $field)
					if ($field['tag'] != 'EMAIL')
						$fields_tag[] = $field['tag'];

				foreach ($db_fields_name as $field)
				{
					if ($field != 'EMAIL' && !in_array($field, $fields_tag))
						if (!$this->installFields())
							array_merge($errors, $this->errors);
						break;
				}

				$shop_tag = null;
				foreach ($fields as $field)
				{
					if ($field['tag'] == 'SHOP')
					{
						$shop_tag = $field;
						break;
					}
				}

				$db_shops = $shop_config['shops'];
				$db_shops_name = self::grep($db_shops, 'name');
				if (isset($shop_tag))
				{
					$shop_choices = $shop_tag['choices'];
					$shop_add    = array_diff($db_shops_name, $shop_choices);
					$shop_remove = array_diff($shop_choices, $db_shops_name);

					if (!empty($shop_add) || !empty($shop_remove))
					{
						$response = $this->listUpdateVar($this->id_list, 'SHOP', 'SHOP', array(
							'options' => array(
								'choices' => $db_shops_name,
							)
						));

						if (!$response)
							array_merge($errors, $this->errors);
					}
				}

			}
		}

		if (empty($errors))
			return true;
		else
			$this->errors = $errors;

		return false;
	}

	public function syncListsBack($start = 0, $limit = 25)
	{
		try
		{
			$result = $this->syncListsBackStep($start, $limit);
			$this->response->setArray($result);
		}
		catch (Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function syncListsBackStep($start = 0, $limit = 25)
	{
		$data = $this->getListMembers($this->id_list, $start, $limit);
	
		if (!$data)
		{
			$errors = $this->getErrors();

			if (count($errors))
				foreach ($errors as $value)
					throw new Exception($value['error']);
		}

		$list = $this->getListById($this->id_list);
		$member_count = $list['stats']['member_count'];

		$created = 0;
		$updated = 0;
		$errors = 0;

		foreach ($data as $chimp_data) 
		{
			try
			{
				$user = NewsletterProMailChimpUserImport::newInstance($chimp_data['email']);
				$user->set($chimp_data);

				if ($user->userExists())
				{
					if ($user->save())
						$updated++;
					else
						$errors++;
				}
				else
				{
					if ($user->save())
						$created++;
					else
						$errors++;
				}
			}
			catch(Exception $e)
			{
				NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
				$errors++;
			}
		}

		$success = $created + $updated;
		$total = $success + $errors;


		return array(
			'total' => $total,
			'success' => $success,
			'created' => $created,
			'updated' => $updated,
			'errors_count' => $errors,
			'member_count' => $member_count,
			'start' => $start,
			'limit' => $limit,
		);
	}

	public function syncLists($from, $to = null)
	{
		$errors = array();
		$couts = array();

		if (!$this->syncCheck())
			$errors = $this->errors;
		else
		{
			if ($this->getContextConfig('ADDED_CHECKBOX'))
			{
				$response = $this->syncAddedByDate($from, $to);
				$response = $response['chimp_response'];
				if (!$response)
					$this->mergeErrors($errors);
				else
				{
					$couts['added'] = array(
						'adds'    => count($response['adds']),
						'updates' => count($response['updates']),
						'errors'  => count($response['errors']),
					);
				}
			}
			else
				$errors[] = $this->l('The "added" list is not activated for the synchronization. You need to check it from the module.');

			if ($this->getContextConfig('VISITORS_CHECKBOX'))
			{
				$response = ((bool)$this->getConfiguration('SUBSCRIPTION_ACTIVE') ? $this->syncVisitorsNPByDate($from, $to) : $this->syncVisitorsByDate($from, $to));

				$response = $response['chimp_response'];
				if (!$response)
					$this->mergeErrors($errors);
				else
				{
					$couts['visitors'] = array(
						'adds'    => count($response['adds']),
						'updates' => count($response['updates']),
						'errors'  => count($response['errors']),
					);
				}
			}
			else
				$errors[] = $this->l('The "visitors" list is not activated for the synchronization. You need to check it from the module.');

			if ($this->getContextConfig('CUSTOMERS_CHECKBOX'))
			{
				$response = $this->syncCustomersByDate($from, $to);
				$response = $response['chimp_response'];
				if (!$response)
					$this->mergeErrors($errors);
				else
				{
					$couts['customers'] = array(
						'adds'    => count($response['adds']),
						'updates' => count($response['updates']),
						'errors'  => count($response['errors']),
					);
				}
			}
			else
				$errors[] = $this->l('The "customers" list is not activated for the synchronization. You need to check it from the module.');

			if ($this->getContextConfig('ORDERS_CHECKBOX'))
			{
				$orders_date = NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS');

				$response = $this->syncOrdersByDate($orders_date);

				$chimp_response = $response['chimp_response'];
				$db_users       = $response['db_users'];

				$db_last_date_add = false;
				if (!empty($db_users))
				{
					$db_date_add = self::grep($db_users, 'date_add');
					
					$db_date_add_time = array();
					foreach ($db_date_add as $date) 
						$db_date_add_time[$date] = strtotime($date);

					if (!empty($db_date_add_time))
						$db_last_date_add = date('Y-m-d H:i:s', max($db_date_add_time));
				}

				$adds = 0;
				$errors_cout = 0;

				if ($db_last_date_add)
				{
					foreach ($chimp_response as $value)
					{
						if (isset($value['complete']) && $value['complete'])
							$adds++;
						else
							$errors_cout++;
					}
				}

				if (!$response)
					$this->mergeErrors($errors);
				else
				{
					$couts['orders'] = array(
						'adds'    => $adds,
						'updates' => 0,
						'errors'  => $errors_cout,
					);

					if ($db_last_date_add)
						NewsletterProConfig::save('CHIMP_LAST_DATE_SYNC_ORDERS', $db_last_date_add);
				}
			}
			else
				$errors[] = $this->l('The "orders" are not activated for the synchronization. You need to check it from the module.');

		}

		return array(
				'errors' => $errors,
				'couts' => $couts,
			);
	}

	public function installFields()
	{
		$errors = array();
		$save_tags_response = array();
		$id_list = $this->id_list;
		if ($list_vars = $this->listGetVars($id_list))
		{
			$mcv = new NewsletterProMailChimpFields($list_vars['merge_vars']);
			$save_tags = $mcv->getSyncVars();

			foreach ($save_tags as $type => $tag)
			{
				if ($type == 'add')
				{
					foreach ($tag as $tag_name => $tag_value)
					{
						$response = $this->listAddVar($id_list, $tag_name, $tag_value['name'], array(
							'options' => $tag_value['options']
						));

						if (!$response)
							$this->mergeErrors($errors);
						else
							$save_tags_response[] = $response;
					}
				}
				else if ($type == 'update')
				{
					foreach ($tag as $tag_name => $tag_value)
					{
						unset($tag_value['options']['field_type']);

						$response = $this->listUpdateVar($id_list, $tag_name, $tag_value['name'], array(
							'options' => $tag_value['options']
						));

						if (!$response)
							$this->mergeErrors($errors);
						else
							$save_tags_response[] = $response;
					}
				}
			}
		}
		else
			$this->mergeErrors($errors);

		$fields = array();

		foreach ($save_tags_response as $value)
			$fields[] = array(
				'name' => $value['name'],
				'tag'  => $value['tag'],
			);

		$this->updateContextConfig('FIELDS', $fields);

		if (empty($errors))
			return $save_tags_response;
		else
			$this->errors = $errors;

		return false;
	}

	public function pingChimp()
	{
		$errors = array();
		$response = array(
			'status'  => false,
			'errors'  => &$errors,
			'message' => '',
		);

		if ($ping = $this->ping())
		{
			$response['message'] = $ping;
			$response['status'] = true;
		}
		else
			$this->mergeErrors($errors);

		if (empty($errors))
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	public function installChimp($api_key, $list_id)
	{
		$errors = array();
		$response = array(
			'status'  => false,
			'errors'  => &$errors,
			'message' => '',
		);

		try
		{
			if (!extension_loaded('curl'))
				throw new Exception(sprintf($this->module->l('The availability of php %s library is not available on your server. You can talk with the hosting provider to enable it.'), 'curl'));

			if (empty($api_key))
				$errors[] = $this->l('The Api Key field is empty.');
			if (empty($list_id))
				$errors[] = $this->l('The List ID field is empty.');

			if (empty($errors))
			{
				if ($this->installConfiguration($api_key, $list_id))
				{
					if ($this->ping())
					{
						if ($this->getListById($list_id) && $this->install())
						{
							$this->updateContextConfig('INSTALLED', true);

							$response['message'] = $this->l('Mail Chimp was successfully installed!');
							$response['status'] = true;
						}
						else
							$this->mergeErrors($errors);
					}
					else
						$errors[] = $this->l('Invalid MailChimp Api Key: ').$api_key;
				}
				else
					$errors[] = $this->l('The Mail Chimp database configuration cannot be installed.');
			}
		}
		catch (Exception $e)
		{
			$errors[] = $e->getMessage();
		}

		return Tools::jsonEncode($response);
	}

	public function uninstallConfiguration()
	{
		unset($this->module->configuration[self::CONFIG_NAME]);
		return $this->module->updateDbConfiguration();
	}

	public function uninstallChimp()
	{
		$errors = array();
		$response = array(
			'status' => false,
			'errors' => &$errors,
			'message' => '',
		);

		if ($this->uninstallConfiguration())
		{
			$response['message'] = $this->l('Mail Chimp was successfully uninstalled!');
			$response['status'] = true;
		}
		else
			$errors[] = $this->l('Mail Chimp cannot be uninstalled.');

		return Tools::jsonEncode($response);
	}

	public function updateSyncCheckbox($name, $value)
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);
		if (!$this->updateContextConfig($name, $value))
			$errors[] = $this->l('Invalid checkbox selection.');
		else
			$response['status'] = true;

		return Tools::jsonEncode($response);
	}

	public function l($value)
	{
		return $this->module->l($value);
	}

	public function setSyncLists($data)
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);
		$chimp_sync = array();

		if (!$this->syncCheck())
			$errors = $this->errors;
		else
		{
			if ($data['ADDED_CHECKBOX'])
				$chimp_sync['ADDED_CHECKBOX'] = array(
					'id'          => 1,
					'total'       => $this->countAdded(),
					'updates'     => 0,
					'created'     => 0,
					'errors'      => 0,
					'errors_message' => array(),
					'in_progress' => false,
					'done'        => false,
				);

			$visitors_count = ((bool)$this->getConfiguration('SUBSCRIPTION_ACTIVE') ? $this->countVisitorsNP() : $this->countVisitors());

			if ($data['VISITORS_CHECKBOX'])
				$chimp_sync['VISITORS_CHECKBOX'] = array(
					'id'          => 1,
					'total'       => $visitors_count,
					'updates'     => 0,
					'created'     => 0,
					'errors'      => 0,
					'errors_message' => array(),
					'in_progress' => false,
					'done'        => false,
				);

			if ($data['CUSTOMERS_CHECKBOX'])
				$chimp_sync['CUSTOMERS_CHECKBOX'] = array(
					'id'          => 1,
					'total'       => $this->countCustomers(),
					'updates'     => 0,
					'created'     => 0,
					'errors'      => 0,
					'errors_message' => array(),
					'in_progress' => false,
					'done'        => false,
				);

			if ($data['ORDERS_CHECKBOX'])
				$chimp_sync['ORDERS_CHECKBOX'] = array(
					'id'          => 1,
					'total'       => $this->countOrders(NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS')),
					'date_add'	  => NewsletterProConfig::get('CHIMP_LAST_DATE_SYNC_ORDERS'),
					'updates'     => 0,
					'created'     => 0,
					'errors'      => 0,
					'errors_message' => array(),
					'in_progress' => false,
					'done'        => false,
				);

			// set ajx loader
			if (isset($chimp_sync['ADDED_CHECKBOX']))
				$chimp_sync['ADDED_CHECKBOX']['in_progress'] = true;
			else if (isset($chimp_sync['VISITORS_CHECKBOX']))
				$chimp_sync['VISITORS_CHECKBOX']['in_progress'] = true;
			else if (isset($chimp_sync['CUSTOMERS_CHECKBOX']))
				$chimp_sync['CUSTOMERS_CHECKBOX']['in_progress'] = true;
			else if (isset($chimp_sync['ORDERS_CHECKBOX']))
				$chimp_sync['ORDERS_CHECKBOX']['in_progress'] = true;

			$chimp_sync['ERRORS'] = array();

			if (NewsletterProConfig::saveArray('CHIMP_SYNC', $chimp_sync))
				$response['status'] = true;
			else
				$errors[] = $this->l('Sync list not started. The sync list configuration cannot be saved.');
		}

		return Tools::jsonEncode($response);
	}

	public function deleteChimpOrders()
	{
		$response = NewsletterProAjaxResponse::newInstance(array(
			'msg'      => '',
			'date_add' => '0000-00-00 00:00:00'
		));

		$chimp_orders = $this->getAllOrders(0, 500);

		$msg_set = false;
		$success = 0;
		if (!empty($chimp_orders['data']))
		{
			foreach ($chimp_orders['data'] as $order) 
			{
				if ($this->orderDelete($order['store_id'], $order['order_id']))
					$success++;
				else
					$response->mergeErrors($this->getErrors(true, true));

			}
		}
		else
		{
			$errors = $this->getErrors(true, true);
			if (!empty($errors))
				$response->mergeErrors($errors);
			else
			{
				$msg_set = true;
				$response->set('msg', $this->l('There no MailChimp orders to delete.'));
			}
		}

		if (!$msg_set)
			$response->set('msg', sprintf($this->l('(%s) was deleted successfully, (%s) errors'), $success, count($response->getErrors())));

		$reset_date = '0000-00-00 00:00:00';

		$response->set('date_add', $reset_date);
		NewsletterProConfig::save('CHIMP_LAST_DATE_SYNC_ORDERS', $reset_date);

		return $response->display();
	}

	public function resetSyncOrderDate()
	{
		$reset_date = '0000-00-00 00:00:00';
		$response = NewsletterProAjaxResponse::newInstance(array(
			'date_add' => $reset_date,
		));

		$response->set('date_add', $reset_date);
		if (!NewsletterProConfig::save('CHIMP_LAST_DATE_SYNC_ORDERS', $reset_date))
			$response->addError($this->l('An error occurred.'));

		return $response->display();
	}

	public function getAllOrders($start = 0, $limit = 500, $orders = null)
	{
		if (!isset($orders))
			$orders = array(
				'total'      => 0,
				'total_find' => 0,
				'data'       => array()
			);

		$chimp_orders = $this->getOrders(null, $start, $limit);
		if (!$chimp_orders)
			return $orders;
		else
		{
			$orders['total'] = $chimp_orders['total'];
			$orders['total_find'] += count($chimp_orders['data']);
			$orders['data'] = array_merge($orders['data'], $chimp_orders['data']);

			if ($orders['total'] > $orders['total_find'] && count($chimp_orders['data']) > 0)
			{
				$start++;
				return $this->getAllOrders($start, $limit, $orders);
			}
		}

		return $orders;
	}

	public function getSyncListName($chimp_sync)
	{
		if (is_array($chimp_sync))
		{
			foreach ($chimp_sync as $name => $list)
				if (isset($list['done']) && $list['done'] == false)
					return $name;
		}

		return false;
	}

	public function processSync()
	{
		$chimp_sync = NewsletterProConfig::getArray('CHIMP_SYNC');
		$name       = $this->getSyncListName($chimp_sync);

		if ($name)
			$list =& $chimp_sync[$name];
		else
			return false;

		try
		{
			switch ($name)
			{
				case 'ADDED_CHECKBOX':
					$response     = $this->syncAdded($list['id'], $this->sync_step);
					break;

				case 'VISITORS_CHECKBOX':
					if ((bool)$this->getConfiguration('SUBSCRIPTION_ACTIVE'))
						$response = $this->syncVisitorsNP($list['id'], $this->sync_step);
					else
						$response = $this->syncVisitors($list['id'], $this->sync_step);
					break;

				case 'CUSTOMERS_CHECKBOX':
					$response = $this->syncCustomers($list['id'], $this->sync_step);
					break;

				case 'ORDERS_CHECKBOX':
					//this is the usual function syncOrders
					$response = $this->syncOrdersByDate($list['date_add']);
					break;

				default:
					return false;
			}

			switch ($name)
			{
				case 'ADDED_CHECKBOX':
				case 'VISITORS_CHECKBOX':
				case 'CUSTOMERS_CHECKBOX':

					if ($response['chimp_response'])
					{
						$db_last_id = 0;
						if (!empty($response['db_users']))
						{
							$db_ids = self::grep($response['db_users'], 'id');
							if (!empty($db_ids))
								$db_last_id = max($db_ids);
							else
								throw new Exception('Invalid db_users id for the list "'.$name.'"');
						}

						if ($db_last_id == 0)
						{
							$list['in_progress'] = false;
							$list['done']        = true;
						}
						else
						{
							$chimp_response      = $response['chimp_response'];
							$list['id']          = $db_last_id + 1;
							$list['created']     += $chimp_response['add_count'];
							$list['updates']     += $chimp_response['update_count'];
							$list['errors']      += $chimp_response['error_count'];
							$list['errors_message'] = $chimp_response['errors'];
							$list['in_progress'] = true;
							$list['done']        = false;
						}
					}
					else if (empty($response['db_users']))
					{
						$list['in_progress'] = false;
						$list['done']        = true;
					}
					else
					{
						$list['done']        = true;
						$list['in_progress'] = false;

						if (!isset($chimp_sync['ERRORS']))
							$chimp_sync['ERRORS'] = array();

						$chimp_sync['ERRORS'] = array_unique(array_merge($chimp_sync['ERRORS'], $this->getErrors(true, true)));
					}

					$chimp_sync['ERRORS_MESSAGE'] = $list['errors_message'];

					break;

				case 'ORDERS_CHECKBOX':

						if ($response['chimp_response'])
						{
							$db_last_id = 0;
							$db_last_date_add = false;

							if (!empty($response['db_users']))
							{
								$db_ids = self::grep($response['db_users'], 'id_order');
								if (!empty($db_ids))
									$db_last_id = max($db_ids);
								else
									throw new Exception('Invalid db_users id for the list "'.$name.'"');

								$db_date_add = self::grep($response['db_users'], 'date_add');
								
								$db_date_add_time = array();
								foreach ($db_date_add as $date) 
									$db_date_add_time[$date] = strtotime($date);

								if (!empty($db_date_add_time))
									$db_last_date_add = date('Y-m-d H:i:s', max($db_date_add_time));
							}

							if (!$db_last_date_add || $db_last_id == 0)
							{
								$list['in_progress'] = false;
								$list['done']        = true;
							}
							else
							{
								$chimp_response      = $response['chimp_response'];

								foreach ($chimp_response as $value) 
								{
									if (isset($value['complete']) && $value['complete'])
										$list['created']++;
									else
										$list['errors']++;
								}

								$chimp_last_date_sync_orders = date('Y-m-d H:i:s', strtotime($db_last_date_add) + 1);

								$list['id']          = $db_last_id + 1;
								$list['date_add']	 = $chimp_last_date_sync_orders;
								$list['updates']     = 0;
								$list['in_progress'] = true;
								$list['done']        = false;

								NewsletterProConfig::save('CHIMP_LAST_DATE_SYNC_ORDERS', $chimp_last_date_sync_orders );
							}
						}
						else if (empty($response['db_users']))
						{
							$list['in_progress'] = false;
							$list['done']        = true;
						}
						else
						{
							$list['done']        = true;
							$list['in_progress'] = false;

							if (!isset($chimp_sync['ERRORS']))
								$chimp_sync['ERRORS'] = array();

							$chimp_sync['ERRORS'] = array_unique(array_merge($chimp_sync['ERRORS'], $this->getErrors(true, true)));
						}

					break;
			}		
		}
		catch (Exception $e)
		{
			$list['done']        = true;
			$list['in_progress'] = false;

			$chimp_sync['ERRORS'][] = $e->getMessage();
		}

		NewsletterProConfig::saveArray('CHIMP_SYNC', $chimp_sync);
	}

	public function stopSync()
	{
		$errors = array();
		$response = array('status' => false, 'errors' => &$errors);

		if (NewsletterProConfig::saveArray('CHIMP_SYNC', array()))
			$response['status'] = true;
		else
			$errors[] = $this->l('The synchronization cannot be stopped.');

		return Tools::jsonEncode($response);
	}

	public function startSyncLists()
	{
		@ini_set('max_execution_time', '2880');

		if ($this->ping())
			NewsletterProConfig::save('LAST_DATE_CHIMP_SYNC', date('Y-m-d H:i:s') );

		$this->processSync();
		// run in background
		return Tools::jsonEncode(NewsletterProConfig::getArray('CHIMP_SYNC'));
	}

	public function getSyncListsStatus()
	{
		$response = array(
			'chimp_sync' => NewsletterProConfig::getArray('CHIMP_SYNC')
		);
		return Tools::jsonEncode($response);
	}

	public function getAllTemplates()
	{
		$response = array('status' => false, 'errors' => array(), 'templates' => array());

		$templates = $this->getTemplates();
		if (!$templates)
			$response['errors'] = $this->getErrors(true);
		else
		{
			$response['status'] = true;
			$response['templates'] = $templates;
		}
		return Tools::jsonEncode($response);
	}

	public function getTemplateSource($template_id, $type)
	{
		$response = array('status' => false, 'errors' => array(), 'template' => '');

		$template = $this->getTemplateContent($template_id, $type);

		if (!$template)
			$response['errors'] = $this->getErrors(true);
		else
		{
			$response['status']   = true;
			$response['template'] = $template['source'];
		}

		return Tools::jsonEncode($response);
	}

	public function importTemplate($name, $content, $override = false)
	{
		try
		{
			$this->response->setArray(array(
				'worning' => array(),
				'template_name' => '',
				// 'template' => '',
			));

			$name = NewsletterProTemplate::formatName($name);

			if (($validate_error = $this->module->verifyName($name)) !== true)
				throw new Exception($validate_error);

			$name = $name.'.html';

			$template = NewsletterProTemplate::newString(array($name, $content))->load();

			$this->response->set('template_name', $template->name);

			if ($override)
				$template->save();
			else
			{
				if (NewsletterProTemplate::templateExists($name))
				{
					$this->response->set('worning', array(
						101 => sprintf($this->l('A file with the name "%s" already exists! Do you want to override the file?'), $name)
					));
				}
				else
					$template->save();
			}
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}

	public function exportTemplate($name, $id_lang, $filename, $override = false)
	{
		$this->response->setArray(array(
			'status' => false,
			'name_exists' => false,
			'message' => '',
		));

		try
		{
			$template = NewsletterProTemplate::newFile($filename, array(null, NewsletterProTemplateUser::USER_TYPE_EMPLOYEE))->load($id_lang);
			$content = $template->renderMailChimp(NewsletterProTemplateContent::CONTENT_HTML);

			$all_templates = $this->getTemplates(array(
				'user'    => true,
				'gallery' => false,
				'base'    => false,
			), array('include_inactive' => true));

			if (!$all_templates)
			{
				$errors = $this->getErrors(true);

				foreach ($errors as $error)
					throw new Exception($error);
			}

			$templates      = $all_templates['user'];
			$templates_name = array();
			foreach ($templates as $key => $value)
				$templates_name[$key] = $value['name'];

			$key_search = array_search($name, $templates_name);

			if ($key_search !== false)
			{
				$find_template = $templates[$key_search];

				if (!$override)
				{
					$this->response->set('name_exists', true);
					$this->response->set('message', sprintf($this->l('A template with the name "%s" already exists. Do you want to override it?'), $name));
				}
				else
				{
					$update_response = $this->templateUpdate($find_template['id'], $name, $content);
					if (!$update_response)
					{
						$req_errors = $this->getErrors(true);

						foreach ($req_errors as $error)
							throw new Exception($error);
					}

					if (!(int)$find_template['active'])
					{
						$undel_response = $this->templateUndel($find_template['id']);

						if (!$undel_response)
						{
							$req_errors = $this->getErrors(true);

							foreach ($req_errors as $error)
								throw new Exception($error);
						}
					}
				}
			}
			else
			{
				$add_response = $this->templateAdd($name, $content);

				if (!$add_response)
				{
					$req_errors = $this->getErrors(true);

					foreach ($req_errors as $error)
						throw new Exception($error);
				}
			}
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}
}
?>