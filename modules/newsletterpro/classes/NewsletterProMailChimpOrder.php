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

class NewsletterProMailChimpOrder 
{
	/**
	 * the Order Id
	 * @var string
	 */
	private $id;

	/**
	 * NOT IN USE
	 * optional the Campaign Id to track this order against (see the "mc_cid" query string variable a campaign passes)
	 * @var string
	 */
	private $campaign_id;

	/**
	 * NOT IS USE
	 * optional (kind of) the Email Id of the subscriber we should attach this order to (see the "mc_eid" query string variable a campaign passes) - required if campaign_id is passed, otherwise either this or email is required. If both are provided, email_id takes precedence
	 * @var string
	 */
	private $email_id;

	/**
	 * optional (kind of) the Email Address we should attach this order to - either this or email_id is required. If both are provided, email_id takes precedence
	 * @var string
	 */
	private $email;

	/**
	 * The Order Total (ie, the full amount the customer ends up paying)
	 * @var float
	 */
	private $total;

	/**
	 * optional the date of the order - if this is not provided, we will default the date to now. Should be in the format of 2012-12-30
	 * @var string
	 */
	private $order_date;

	/**
	 * optional the total paid for Shipping Fees
	 * @var float
	 */
	private $shipping;

	/**
	 * optional the total tax paid
	 * @var float
	 */
	private $tax;

	/**
	 * a unique id for the store sending the order in (32 bytes max)
	 * @var string
	 */
	private $store_id;

	/**
	 * optional a "nice" name for the store - typically the base web address (ie, "store.mailchimp.com"). We will automatically update this if it changes (based on store_id)
	 * @var string
	 */
	private $store_name;

	/**
	 * structs for each individual line item including
	 * @var array
	 */
	private $items = array();

	private $db_cache = array();

	public function __construct($id_order, $id_currency = null)
	{
		$order = new Order($id_order);
		
		$id_currency   = $to_currency = (isset($id_currency) ? $id_currency : NewsletterPro::getInstance()->getConfiguration('PS_CURRENCY_DEFAULT'));
		$from_currency = $order->id_currency;
		$tax_excl      = $order->getTaxCalculationMethod() == PS_TAX_EXC;

		$customer = new Customer($order->id_customer);
		$shop     = new Shop($order->id_shop);

		$line_num = 0;
		foreach ($order->getProducts() as $product) 
		{
			$id_category_default = $product['id_category_default'];

			if ($tax_excl)
				$cost = $product['unit_price_tax_excl'];
			else
				$cost = $product['unit_price_tax_incl'];

			$item = array(
				'line_num'      => ++$line_num,
				'product_id'    => (int)$product['product_id'],
				'sku'           => (string)$product['product_reference'],
				'product_name'  => (string)$product['product_name'],
				'category_id'   => (int)$id_category_default,
				'category_name' => $this->getCategoryName($id_category_default, $order->id_lang),
				'qty'           => (float)$product['product_quantity'],
				'cost'          => (float)$cost,
			);

			$this->addItem($item);
		}

		if ($tax_excl)
		{
			$total = $order->total_paid_tax_excl;
			$shipping = $order->total_shipping_tax_excl;
		}
		else
		{
			$total = $order->total_paid_tax_incl;
			$shipping = $order->total_shipping_tax_incl;
		}

		$tax = $order->total_paid_tax_incl - $order->total_paid_tax_excl;

		$this->setId($order->id);
		$this->setEmail($customer->email);
		$this->setTotal($total, $from_currency, $to_currency);
		$this->setOrderDate($order->date_add);
		$this->setShipping($shipping, $from_currency, $to_currency);
		$this->setTax($tax, $from_currency, $to_currency);
		$this->setStoreId($shop->id);
		$this->setStoreName($shop->name);
	}

	public static function newInstance($id_order, $id_currency = null)
	{
		return new self($id_order, $id_currency);
	}

	public static function getOrdersIdSinceDate($date)
	{
		$db_orders = Db::getInstance()->executeS('
			SELECT `id_order`, `date_add` 
			FROM `'._DB_PREFIX_.'orders`
			WHERE `date_add` > "'.pSQL($date).'"
		');

		return $db_orders;
	}

	public function toArray()
	{
		$order = array(
			'id'         => $this->id,
			'email'      => $this->email,
			'total'      => $this->total,
			'order_date' => $this->order_date,
			'shipping'   => $this->shipping,
			'tax'        => $this->tax,
			'store_id'   => $this->store_id,
			'store_name' => $this->store_name,
			'items'      => $this->getValidItems(),
		);

		return $order;
	}

	private function getCategoryName($id_category, $id_lang = null)
	{
		$id_lang = (isset($id_lang) ? $id_lang : NewsletterPro::getInstance()->getConfiguration('PS_LANG_DEFAULT'));

		$sql = trim('
			SELECT `name` FROM `'._DB_PREFIX_.'category` c
			LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
				ON (c.`id_category` = cl.`id_category`)
			WHERE c.`id_category` = '.(int)$id_category.'
			AND cl.`id_lang` = '.(int)$id_lang.'
		');

		if (isset($this->db_cache[$sql]))
			return $this->db_cache[$sql];

		$name = trim(Db::getInstance()->getValue($sql));
		
		$this->db_cache[$sql] = (empty($name) ? 'Unknown' : $name);

		return $this->db_cache[$sql];
	}

	public function setId($value)
	{
		$this->id = (int)$value;
		return $this;
	}

	public function setEmail($value)
	{
		$this->email = (string)$value;
		return $this;
	}

	public function setTotal($value, $from_currency = null, $to_currency = null)
	{
		$this->total = (float)Tools::convertPrice($value, $from_currency, $to_currency);
		return $this;
	}

	public function setOrderDate($value)
	{
		$this->order_date = (string)$value;
		return $this;
	}

	public function setShipping($value, $from_currency = null, $to_currency = null)
	{
		$this->shipping = (float)Tools::convertPrice($value, $from_currency, $to_currency);
		return $this;
	}

	public function setTax($value, $from_currency, $to_currency)
	{
		$this->tax = (float)(float)Tools::convertPrice($value, $from_currency, $to_currency);
		return $this;
	}

	public function setStoreId($value)
	{
		$this->store_id = (string)$value;
		return $this;
	}

	public function setStoreName($value)
	{
		$this->store_name = (string)$value;
		return $this;
	}

	public function addItem($value)
	{
		$this->items[] = $value;
		return $this;
	}

	public function getValidItems()
	{
		$items = array();
		foreach ($this->items as $item) 
		{
			if ((int)$item['product_id'] && (int)$item['category_id'])
				$items[] = $item;
		}
		return $items;
	}
}