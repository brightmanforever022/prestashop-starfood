<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/

$id_shop = intval(Tools::getValue('id_shop', SCI::getSelectedShop()));
if ($id_shop==0)
	$id_shop = SCI::getSelectedShop();
$id_customer = (int)$_GET["id"];

// fix for specific SC users
if (_s('APP_COMPAT_USERLOGIN'))
{
	header('location: '.SC_PS_PATH_ADMIN_REL.'index.php?tab=AdminStoreCommander&SETLOGUSER=1&id_customer='.$id_customer.'&id_shop='.$id_shop.'&token='.$sc_agent->getPSToken('AdminStoreCommander'));
	exit;
}

	
$domains = null;
$path='';
$cookie_lifetime = (int)(defined('_PS_ADMIN_DIR_') ? Configuration::get('PS_COOKIE_LIFETIME_BO') : Configuration::get('PS_COOKIE_LIFETIME_FO'));
$cookie_lifetime = time() + (max($cookie_lifetime, 1) * 3600);
$link = new Link();
if($id_customer)
{
	if (SCMS)
	{
		if ($id_shop==0)
			exit(_l('There is a problem with the shop ID'));
		$shop = new Shop($id_shop);
		$shop_group=$shop->getGroup();
		if ($shop_group->share_order)
		  $cookie = new Cookie('ps-sg'.$shop_group->id, $path, $cookie_lifetime, $shop->getUrlsSharedCart());
		else
		{
		  if ($shop->domain != $shop->domain_ssl)
		        $domains = array($shop->domain_ssl, $shop->domain);
		
		  $cookie = new Cookie('ps-s'.$shop->id, $path, $cookie_lifetime, $domains);
		}
	}else{
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
		 	 $shop = new Shop((int)Configuration::get('PS_SHOP_DEFAULT'));
			if ($shop->domain != $shop->domain_ssl)
					$domains = array($shop->domain_ssl, $shop->domain);		  
			$cookie = new Cookie('ps-s'.(int)Configuration::get('PS_SHOP_DEFAULT'), $path, $cookie_lifetime, $domains);
		}else{
			$cookie = new Cookie('ps');
		}
	}
	if($cookie->logged)
		$cookie->logout();
	
	Tools::setCookieLanguage($cookie);
	Tools::switchLanguage();
	
	$customer = new Customer(intval($id_customer));
	if (!$customer->active)
		die(_l('Customer not enabled on shop.'));
	$cookie->id_customer = intval($customer->id);
	$cookie->customer_lastname = $customer->lastname;
	$cookie->customer_firstname = $customer->firstname;
	$cookie->logged = 1;
	$cookie->passwd = $customer->passwd;
	$cookie->email = $customer->email;
	if (Configuration::get('PS_CART_FOLLOWING') AND (empty($cookie->id_cart) OR Cart::getNbProducts($cookie->id_cart) == 0))
		$cookie->id_cart = Cart::lastNoneOrderedCart($customer->id);
	if (version_compare(_PS_VERSION_,'1.5.0.0','<'))
			$cookie->id_cart = $customer->getLastCart();
}

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	$order_process = Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order';
	if (SCMS)
	{
		$server_host = Tools::getHttpHost(false, true);
		$protocol = 'http://';
		$protocol_ssl = 'https://';
		$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? $protocol_ssl : $protocol;
		
		// we replace default domain by selected shop domain
		$urltmp = $link->getPageLink($order_process, true);
		$urltmparr=explode('index.php',$urltmp);
		$urlbase = $protocol_link.$shop->domain.$shop->getBaseURI();
		$url=$urlbase.'index.php'.$urltmparr[1];
	}else{
		$url = $link->getPageLink($order_process, true);  //  http://127.0.0.1/ps15301/index.php?controller=order-opc
	}
}else{
	$url = __PS_BASE_URI__.'order.php';
}
header("location: ".$url);
