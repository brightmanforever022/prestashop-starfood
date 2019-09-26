<?php
/**
 * 2007-2015 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2015 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

if (!defined('_CAN_LOAD_FILES_'))
	exit;

//include_once(dirname(__FILE__).'/MailAlert.php');

class MailAlertsOverride extends MailAlerts
{
	
    public function hookActionValidateOrder($params)
	{
		if (!$this->merchant_order || empty($this->merchant_mails))
			return;

		// Getting differents vars
		$context = Context::getContext();
		$id_lang = (int)$context->language->id;
		$id_shop = (int)$context->shop->id;
		$currency = $params['currency'];
		$order = $params['order'];
		$customer = $params['customer'];
		$configuration = Configuration::getMultiple(
			array(
				'PS_SHOP_EMAIL',
				'PS_MAIL_METHOD',
				'PS_MAIL_SERVER',
				'PS_MAIL_USER',
				'PS_MAIL_PASSWD',
				'PS_SHOP_NAME',
				'PS_MAIL_COLOR'
			), $id_lang, null, $id_shop
		);
		$delivery = new Address((int)$order->id_address_delivery);
		$invoice = new Address((int)$order->id_address_invoice);
		$order_date_text = Tools::displayDate($order->date_add);
		$carrier = new Carrier((int)$order->id_carrier);
		$message = $this->getAllMessages($order->id);

		if (!$message || empty($message))
			$message = $this->l('No message');

		$items_table = '';

		$products = $params['order']->getProducts();
		$customized_datas = Product::getAllCustomizedDatas((int)$params['cart']->id);
		Product::addCustomizationPrice($products, $customized_datas);
		foreach ($products as $key => $product)
		{
			$unit_price = Product::getTaxCalculationMethod($customer->id) == PS_TAX_EXC ? $product['product_price'] : $product['product_price_wt'];

			$customization_text = '';
			if (isset($customized_datas[$product['product_id']][$product['product_attribute_id']]))
			{
				foreach ($customized_datas[$product['product_id']][$product['product_attribute_id']][$order->id_address_delivery] as $customization)
				{
					if (isset($customization['datas'][Product::CUSTOMIZE_TEXTFIELD]))
						foreach ($customization['datas'][Product::CUSTOMIZE_TEXTFIELD] as $text)
							$customization_text .= $text['name'].': '.$text['value'].'<br />';

					if (isset($customization['datas'][Product::CUSTOMIZE_FILE]))
						$customization_text .= count($customization['datas'][Product::CUSTOMIZE_FILE]).' '.$this->l('image(s)').'<br />';

					$customization_text .= '---<br />';
				}
				if (method_exists('Tools', 'rtrimString'))
					$customization_text = Tools::rtrimString($customization_text, '---<br />');
				else
					$customization_text = preg_replace('/---<br \/>$/', '', $customization_text);
			}
            
            if (isset($product['image']) && $product['image']->id){
                $name = 'product_mini_'.(int)$product['product_id'].(isset($product['product_attribute_id']) ? '_'.(int)$product['product_attribute_id'] : '').'.jpg';
                $product['product_reference'] = ImageManager::thumbnail(_PS_IMG_DIR_.'p/'.$product['image']->getExistingImgPath().'.jpg', $name, 145, 'jpg');
                $product['product_reference'] = str_replace(__PS_BASE_URI__.'img/tmp/', Tools::getHttpHost(true).__PS_BASE_URI__.'img/tmp/', $product['product_reference']);
            }
            
            if($product['product_supplier_reference']){
                $product['product_name'] = $product['product_supplier_reference'].'<br/>';
            }

			$url = $context->link->getProductLink($product['product_id']);
			$items_table .=
				'<tr style="background-color:'.($key % 2 ? '#DDE2E6' : '#EBECEE').';">
					<td style="padding:0.6em 0.4em;">'.$product['product_reference'].'</td>
					<td style="padding:0.6em 0.4em;">
						<strong><a href="'.$url.'">'.$product['product_name'].'</a>'
							.(isset($product['attributes_small']) ? ' '.$product['attributes_small'] : '')
							.(!empty($customization_text) ? '<br />'.$customization_text : '')
						.'</strong>
                    </td>
					<td style="padding:0.6em 0.4em; text-align:right;">'.Tools::displayPrice($unit_price, $currency, false).'</td>
					<td style="padding:0.6em 0.4em; text-align:center;">'.(int)$product['product_quantity'].'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">'
						.Tools::displayPrice(($unit_price * $product['product_quantity']), $currency, false)
					.'</td>
				</tr>';
		}
		foreach ($params['order']->getCartRules() as $discount)
		{
			$items_table .=
				'<tr style="background-color:#EBECEE;">
						<td colspan="4" style="padding:0.6em 0.4em; text-align:right;">'.$this->l('Voucher code:').' '.$discount['name'].'</td>
					<td style="padding:0.6em 0.4em; text-align:right;">-'.Tools::displayPrice($discount['value'], $currency, false).'</td>
			</tr>';
		}
        
		if ($delivery->id_state)
			$delivery_state = new State((int)$delivery->id_state);
		if ($invoice->id_state)
			$invoice_state = new State((int)$invoice->id_state);

		if (Product::getTaxCalculationMethod($customer->id) == PS_TAX_EXC)
			$total_products = $order->getTotalProductsWithoutTaxes();
		else
			$total_products = $order->getTotalProductsWithTaxes();

		$order_state = $params['orderStatus'];

		// Filling-in vars for email
		$template_vars = array(
			'{firstname}' => $customer->firstname,
			'{lastname}' => $customer->lastname,
			'{email}' => $customer->email,
			'{delivery_block_txt}' => MailAlert::getFormatedAddress($delivery, "\n"),
			'{invoice_block_txt}' => MailAlert::getFormatedAddress($invoice, "\n"),
			'{delivery_block_html}' => MailAlert::getFormatedAddress(
				$delivery, '<br />', array(
					'firstname' => '<span style="color:'.$configuration['PS_MAIL_COLOR'].'; font-weight:bold;">%s</span>',
					'lastname' => '<span style="color:'.$configuration['PS_MAIL_COLOR'].'; font-weight:bold;">%s</span>'
				)
			),
			'{invoice_block_html}' => MailAlert::getFormatedAddress(
				$invoice, '<br />', array(
					'firstname' => '<span style="color:'.$configuration['PS_MAIL_COLOR'].'; font-weight:bold;">%s</span>',
					'lastname' => '<span style="color:'.$configuration['PS_MAIL_COLOR'].'; font-weight:bold;">%s</span>'
				)
			),
			'{delivery_company}' => $delivery->company,
			'{delivery_firstname}' => $delivery->firstname,
			'{delivery_lastname}' => $delivery->lastname,
			'{delivery_address1}' => $delivery->address1,
			'{delivery_address2}' => $delivery->address2,
			'{delivery_city}' => $delivery->city,
			'{delivery_postal_code}' => $delivery->postcode,
			'{delivery_country}' => $delivery->country,
			'{delivery_state}' => $delivery->id_state ? $delivery_state->name : '',
			'{delivery_phone}' => $delivery->phone ? $delivery->phone : $delivery->phone_mobile,
			'{delivery_other}' => $delivery->other,
			'{invoice_company}' => $invoice->company,
			'{invoice_firstname}' => $invoice->firstname,
			'{invoice_lastname}' => $invoice->lastname,
			'{invoice_address2}' => $invoice->address2,
			'{invoice_address1}' => $invoice->address1,
			'{invoice_city}' => $invoice->city,
			'{invoice_postal_code}' => $invoice->postcode,
			'{invoice_country}' => $invoice->country,
			'{invoice_state}' => $invoice->id_state ? $invoice_state->name : '',
			'{invoice_phone}' => $invoice->phone ? $invoice->phone : $invoice->phone_mobile,
			'{invoice_other}' => $invoice->other,
			'{order_name}' => $order->reference,
			'{order_status}' => $order_state->name,
			'{shop_name}' => $configuration['PS_SHOP_NAME'],
			'{date}' => $order_date_text,
			'{carrier}' => (($carrier->name == '0') ? $configuration['PS_SHOP_NAME'] : $carrier->name),
			'{payment}' => Tools::substr($order->payment, 0, 32),
			'{items}' => $items_table,
			'{total_paid}' => Tools::displayPrice($order->total_paid, $currency),
			'{total_products}' => Tools::displayPrice($total_products, $currency),
			'{total_discounts}' => Tools::displayPrice($order->total_discounts, $currency),
			'{total_shipping}' => Tools::displayPrice($order->total_shipping, $currency),
			'{total_tax_paid}' => Tools::displayPrice(
				($order->total_products_wt - $order->total_products) + ($order->total_shipping_tax_incl - $order->total_shipping_tax_excl),
				$currency,
				false
			),
			'{total_wrapping}' => Tools::displayPrice($order->total_wrapping, $currency),
			'{currency}' => $currency->sign,
			'{gift}' => (bool)$order->gift,
			'{gift_message}' => $order->gift_message,
			'{message}' => $message
		);

		// Shop iso
		$iso = Language::getIsoById((int)Configuration::get('PS_LANG_DEFAULT'));

		// Send 1 email by merchant mail, because Mail::Send doesn't work with an array of recipients
		$merchant_mails = explode(self::__MA_MAIL_DELIMITOR__, $this->merchant_mails);
		foreach ($merchant_mails as $merchant_mail)
		{
			// Default language
			$mail_id_lang = $id_lang;
			$mail_iso = $iso;

			// Use the merchant lang if he exists as an employee
			$results = Db::getInstance()->executeS('
				SELECT `id_lang` FROM `'._DB_PREFIX_.'employee`
				WHERE `email` = \''.pSQL($merchant_mail).'\'
			');
			if ($results)
			{
				$user_iso = Language::getIsoById((int)$results[0]['id_lang']);
				if ($user_iso)
				{
					$mail_id_lang = (int)$results[0]['id_lang'];
					$mail_iso = $user_iso;
				}
			}




			$dir_mail = false;
        	if (file_exists(_PS_MODULE_DIR_.'mailalerts/mails/'.$mail_iso.'/new_order.txt') &&
        		file_exists(_PS_MODULE_DIR_.'mailalerts/mails/'.$mail_iso.'/new_order.html'))
        		$dir_mail = _PS_MODULE_DIR_.'mailalerts/mails/';

			if (file_exists(_PS_MAIL_DIR_.$mail_iso.'/new_order.txt') &&
        		file_exists(_PS_MAIL_DIR_.$mail_iso.'/new_order.html'))
        		$dir_mail = _PS_MAIL_DIR_;


			if ($dir_mail)
				Mail::Send(
					$mail_id_lang,
					'new_order',
					sprintf(Mail::l('New order : #%d - %s', $mail_id_lang), $order->id, $order->reference),
					$template_vars,
					$merchant_mail,
					null,
					$configuration['PS_SHOP_EMAIL'],
					$configuration['PS_SHOP_NAME'],
					null,
					null,
					$dir_mail,
					null,
					$id_shop
				);
		}
	}
}
