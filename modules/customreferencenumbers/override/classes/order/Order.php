<?php
/**
 * Modulu which offer option to choose if the cart rule can be applied to products with already reduced price.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

OrderCore::$definition['fields']['crn_invoice_number'] = array('type' => ObjectModel::TYPE_STRING);
OrderCore::$definition['fields']['crn_delivery_number'] = array('type' => ObjectModel::TYPE_STRING);
class Order extends OrderCore
{
	public $crn_invoice_number;
	public $crn_delivery_number;
	public function setInvoice($use_existing_payment = false)
	{
		if (!$this->hasInvoice())
		{
			parent::setInvoice($use_existing_payment);
			$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
			$customInvoiceReferenceData = $customNumberReferenceData[1];
			$this->crn_invoice_number = self::getCustomInvoiceReferenceNumber($customInvoiceReferenceData, $this->id);
			$this->update();
		}
	}
	public function setDelivery()
	{
		parent::setDelivery();
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customDeliverySlipReferenceData = $customNumberReferenceData[2];
		$this->crn_delivery_number = self::getCustomDeliverySlipReferenceNumber($customDeliverySlipReferenceData, $this->id);
		$this->update();	
	}
	public static function getCustomDeliverySlipReferenceNumber($customDeliverySlipReferenceData, $order_id)
	{
		$finalReference = $customDeliverySlipReferenceData->reference_number_format;
		if (strpos($finalReference, '{COUNTER}') !== false)
		{
			self::checkForCounterReset(2);
			$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
			$currentCounterData = $getAllCounterData[2];
			$finalReference = str_replace('{COUNTER}', self::getCounterValue($currentCounterData), $finalReference);
			$nextCounterValue = (int)$currentCounterData['counter_actual_value'] + (int)$currentCounterData['counter_increment_by'];
			$currentCounterData['counter_actual_value'] = $nextCounterValue;
			$getAllCounterData[2] = $currentCounterData;
			Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($getAllCounterData));
		}
		$finalReference = str_replace('{RANDOM_NUMBER}', Tools::strtoupper(Tools::passwdGen($customDeliverySlipReferenceData->random_number_length, 'NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHABETIC}', Tools::strtoupper(Tools::passwdGen($customDeliverySlipReferenceData->random_alphabetic_length, 'NO_NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHANUMERIC}', Tools::strtoupper(Tools::passwdGen($customDeliverySlipReferenceData->random_alphanumeric_length, 'ALPHANUMERIC')), $finalReference);
		$finalReference = str_replace('{ORDER_ID}', (int)$order_id, $finalReference);
		$finalReference = str_replace('{SHOP_ID}', (int)Context::getContext()->shop->id, $finalReference);
		$finalReference = str_replace('{YEAR}', date('Y'), $finalReference);
		$finalReference = str_replace('{YEARSHORTCUT}', date('y'), $finalReference);
		$finalReference = str_replace('{MONTH}', date('m'), $finalReference);
		$finalReference = str_replace('{WEEK}', date('W'), $finalReference);
		$finalReference = str_replace('{DAYINWEEK}', date('N'), $finalReference);
		$finalReference = str_replace('{DAY}', date('j'), $finalReference);
		return $finalReference;
	}
	public static function getCustomInvoiceReferenceNumber($customInvoiceReferenceData, $order_id)
	{
		$finalReference = $customInvoiceReferenceData->reference_number_format;
		if (strpos($finalReference, '{COUNTER}') !== false)
		{
			self::checkForCounterReset(1);
			$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
			$currentCounterData = $getAllCounterData[1];
			$finalReference = str_replace('{COUNTER}', self::getCounterValue($currentCounterData), $finalReference);
			$nextCounterValue = (int)$currentCounterData['counter_actual_value'] + (int)$currentCounterData['counter_increment_by'];
			$currentCounterData['counter_actual_value'] = $nextCounterValue;
			$getAllCounterData[1] = $currentCounterData;
			Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($getAllCounterData));
		}
		$finalReference = str_replace('{RANDOM_NUMBER}', Tools::strtoupper(Tools::passwdGen($customInvoiceReferenceData->random_number_length, 'NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHABETIC}', Tools::strtoupper(Tools::passwdGen($customInvoiceReferenceData->random_alphabetic_length, 'NO_NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHANUMERIC}', Tools::strtoupper(Tools::passwdGen($customInvoiceReferenceData->random_alphanumeric_length, 'ALPHANUMERIC')), $finalReference);
		$finalReference = str_replace('{ORDER_ID}', (int)$order_id, $finalReference);
		$finalReference = str_replace('{SHOP_ID}', (int)Context::getContext()->shop->id, $finalReference);
		$finalReference = str_replace('{YEAR}', date('Y'), $finalReference);
		$finalReference = str_replace('{YEARSHORTCUT}', date('y'), $finalReference);
		$finalReference = str_replace('{MONTH}', date('m'), $finalReference);
		$finalReference = str_replace('{WEEK}', date('W'), $finalReference);
		$finalReference = str_replace('{DAYINWEEK}', date('N'), $finalReference);
		$finalReference = str_replace('{DAY}', date('j'), $finalReference);
		return $finalReference;
	}
	public static function generateReference()
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customOrderReferenceData = $customNumberReferenceData[0];
		if ($customOrderReferenceData->activate_reference_number)
		{
			$finalReference = $customOrderReferenceData->reference_number_format;
			if (strpos($finalReference, '{COUNTER}') !== false)
			{
				self::checkForCounterReset(0);
				$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
				$currentCounterData = $getAllCounterData[0];
				$finalReference = str_replace('{COUNTER}', self::getCounterValue($currentCounterData), $finalReference);
				$nextCounterValue = (int)$currentCounterData['counter_actual_value'] + (int)$currentCounterData['counter_increment_by'];
				$currentCounterData['counter_actual_value'] = $nextCounterValue;
				$getAllCounterData[0] = $currentCounterData;
				Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($getAllCounterData));
			}
			$finalReference = str_replace('{RANDOM_NUMBER}', Tools::strtoupper(Tools::passwdGen($customOrderReferenceData->random_number_length, 'NUMERIC')), $finalReference);
			$finalReference = str_replace('{RANDOM_ALPHABETIC}', Tools::strtoupper(Tools::passwdGen($customOrderReferenceData->random_alphabetic_length, 'NO_NUMERIC')), $finalReference);
			$finalReference = str_replace('{RANDOM_ALPHANUMERIC}', Tools::strtoupper(Tools::passwdGen($customOrderReferenceData->random_alphanumeric_length, 'ALPHANUMERIC')), $finalReference);
			$finalReference = str_replace('{ORDER_ID}', (int)self::getLastOrderId() + 1, $finalReference);
			$finalReference = str_replace('{SHOP_ID}', (int)Context::getContext()->shop->id, $finalReference);
			$finalReference = str_replace('{YEAR}', date('Y'), $finalReference);
			$finalReference = str_replace('{YEARSHORTCUT}', date('y'), $finalReference);
			$finalReference = str_replace('{MONTH}', date('m'), $finalReference);
			$finalReference = str_replace('{WEEK}', date('W'), $finalReference);
			$finalReference = str_replace('{DAYINWEEK}', date('N'), $finalReference);
			$finalReference = str_replace('{DAY}', date('j'), $finalReference);
			return $finalReference;
		}
		else
			return Tools::strtoupper(Tools::passwdGen(9, 'NO_NUMERIC'));
	}
	public static function getLastOrderId()
	{
		$sql = 'SELECT MAX(id_order) as lastId FROM '._DB_PREFIX_.'orders';
		if ($row = Db::getInstance()->getRow($sql))
			return $row['lastId'];
	}
	public static function getCMRDeliverySlipByOrderID($orderId)
	{
		if ($orderId)
		{
			$sql = 'SELECT crn_delivery_number FROM '._DB_PREFIX_.'orders where id_order = '.(int)$orderId;
				if ($row = Db::getInstance()->getRow($sql))
					return $row['crn_delivery_number'];
		}
	}
	public static function checkForCounterReset($type)
	{
		$counterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
		$currentCounterData = $counterData[$type];
		$today = date("Y-m-d");
		if ((int)$currentCounterData['counter_reset_interval'] == 0) //reset tyzdna
		{
			if (strtotime($currentCounterData['counter_last_set_date']) < strtotime($today)
				&& (((int)date("W", strtotime($currentCounterData['counter_last_set_date'])) != (int)date("W", strtotime($today)))
				|| (((int)date("W", strtotime($currentCounterData['counter_last_set_date'])) == (int)date("W", strtotime($today))) && ((int)date("Y", strtotime($currentCounterData['counter_last_set_date'])) < (int)date("Y", strtotime($today))))
				))
				{
				$currentCounterData['counter_actual_value'] = $currentCounterData['counter_start_at'];
			}
		}
		else if ((int)$currentCounterData['counter_reset_interval'] == 1) //reset mesiaca
		{
			if (strtotime($currentCounterData['counter_last_set_date']) < strtotime($today)
				&& (((int)date("n", strtotime($currentCounterData['counter_last_set_date'])) != (int)date("n", strtotime($today)))
				|| (((int)date("n", strtotime($currentCounterData['counter_last_set_date'])) == (int)date("n", strtotime($today))) && ((int)date("Y", strtotime($currentCounterData['counter_last_set_date'])) < (int)date("Y", strtotime($today))))
				))
				{
				$currentCounterData['counter_actual_value'] = $currentCounterData['counter_start_at'];
			}
		}
		else if ((int)$currentCounterData['counter_reset_interval'] == 2) //reset roka
		{
			if (strtotime($currentCounterData['counter_last_set_date']) < strtotime($today)
				&& ((int)date("Y", strtotime($currentCounterData['counter_last_set_date'])) < (int)date("Y", strtotime($today))))
				{
				$currentCounterData['counter_actual_value'] = $currentCounterData['counter_start_at'];
			}
		}
		else if ((int)$currentCounterData['counter_reset_interval'] == 3){ //reset podla cisla
			if ((int)$currentCounterData['counter_actual_value'] > $currentCounterData['counter_reset_number']) {
				$currentCounterData['counter_actual_value'] = $currentCounterData['counter_start_at'];
			}
		}
		else if ((int)$currentCounterData['counter_reset_interval'] == 4) //reset podla datumu
		{
			if (strtotime($currentCounterData['counter_last_set_date']) < strtotime($today)
				&& strtotime($currentCounterData['counter_reset_date']) <= strtotime($today)
				&& $currentCounterData['counter_reset_date'] != '')
				{
				$currentCounterData['counter_actual_value'] = $currentCounterData['counter_start_at'];
				$currentCounterData['counter_reset_date'] = '';
			}
		}
		$currentCounterData['counter_last_set_date'] = $today;
		$counterData[$type] = $currentCounterData;
		Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($counterData));
	}
	public static function getCounterValue($currentCounterData)
	{
		$nextCounterValue = '';
		if ((int)$currentCounterData['counter_reference_length_in_use'] == 1)
		{
			if ((int)$currentCounterData['counter_reference_length'] > Tools::strlen((string)$currentCounterData['counter_actual_value']))
			{
				for ($i = 0; $i < (int)$currentCounterData['counter_reference_length'] - Tools::strlen((string)$currentCounterData['counter_actual_value']); $i++)
				{
					$nextCounterValue .= "0";
				}
			}
		}
		$nextCounterValue .= $currentCounterData['counter_actual_value'];
		return $nextCounterValue;
	}
}