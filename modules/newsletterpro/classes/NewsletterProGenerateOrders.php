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

class NewsletterProGenerateOrders
{
	private $customers;

	private $id_address;

	public function __construct()
	{
		include_once _PS_MODULE_DIR_.'bankwire/bankwire.php';

		$this->customers = Customer::getCustomers();
		$id_customer = Db::getInstance()->getValue('SELECT * FROM `'._DB_PREFIX_.'customer` WHERE `email` = "'.pSQL('pub@prestashop.com').'"');

		$pub = new Customer($id_customer);
		$address = $pub->getAddresses(Configuration::get('PS_LANG_DEFAULT'));
		$this->id_address = $address[key($address)]['id_address'];
	}

	public static function newInstance()
	{
		return new self();
	}

	public function generate()
	{
		echo '<pre>';
		$id_carrier = Configuration::get('PS_CARRIER_DEFAULT');
		$orders = 0;
		foreach ($this->customers as $cus)
		{
			$orders++;

			$id_customer = $_GET['id_customer'] = (int)$cus['id_customer'];
			$customer = new Customer((int)$id_customer);

			$this->context = Context::getContext();
			$this->context->customer =& $customer;
			$this->context->shop = new Shop($this->context->customer->id_shop);

			$configuration = Configuration::getMultiple(array(
				'PS_LANG_DEFAULT',
				'PS_CURRENCY_DEFAULT'
			));

			$id_address_delivery = $id_address_invoice = $this->id_address;

			$currency = new Currency((int)$configuration['PS_CURRENCY_DEFAULT']);
			$this->context->currency =& $currency;

			$cart = new Cart();
			$cart->id_currency   = (int)$configuration['PS_CURRENCY_DEFAULT'];
			$cart->id_lang       = (int)$customer->id_lang;
			$cart->id_shop_group = (int)$this->context->shop->id_shop_group;
			$cart->secure_key    = $customer->secure_key;
			$cart->add();
			$id_cart = (int)$cart->id;

			$cart = new Cart((int)$id_cart);
			$this->context->cart =& $cart;

			$cart->id_address_delivery = (int)$id_address_delivery;
			$cart->id_address_invoice  = (int)$id_address_invoice;
			$cart->id_carrier          = (int)$id_carrier;
			$cart->id_customer         = (int)$customer->id;

			$products_id = array(1,2,3,4,5,6,7);
			$qty = rand(1, 3);

			$nb = rand(1, count($products_id) / 2);
			if ($nb < 1)
				$nb = 1;

			for ($i = 0; $i < $nb; $i++)
			{
				$id_product = $products_id[$i];
				$cart->updateQty($qty, $id_product);
			}

			$bankwire = new BankWire();
			$total = (float)($cart->getOrderTotal(true, Cart::BOTH));

			try
			{
				$bankwire->validateOrder($cart->id, Configuration::get('PS_OS_BANKWIRE'), $total, $bankwire->displayName, null, array(), (int)$currency->id, false, $customer->secure_key);
				new Order($bankwire->currentOrder);
			}
			catch(Exception $e)
			{
				echo $customer->email.' -> '.$e->getMessage().'<br>';
			}
		}
		echo '</pre>';
		return $orders;
	}
}