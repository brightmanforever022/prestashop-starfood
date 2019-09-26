<?php
/**
 * Module allow to set custom number of your invoices.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

OrderSlipCore::$definition['fields']['crn_order_slip_number'] = array('type' => ObjectModel::TYPE_STRING);
class OrderSlip extends OrderSlipCore
{
	public $crn_order_slip_number;
	public function add($autodate = true, $null_values = false)
	{
		if (!parent::add())
			return false;
		if (!self::setCRNOrderSlipNumber($this->id, $this->id_order))
			return false;
		return true;
	}
	public static function setCRNOrderSlipNumber($order_slip_id, $order_id)
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customOrderSlipReferenceData = $customNumberReferenceData[3];
		if ($customOrderSlipReferenceData->activate_reference_number)
		{
			$sql = 'UPDATE `'._DB_PREFIX_.'order_slip` SET `crn_order_slip_number` = \''.pSQL(self::getCustomOrderSlipReferenceNumber($customOrderSlipReferenceData, $order_id)).'\'';
			$sql .= ' WHERE `id_order_slip` = '.(int)$order_slip_id;
			return Db::getInstance()->execute($sql);
		}
		return true;
	}
	public static function getCustomOrderSlipReferenceNumber($customOrderSlipReferenceData, $order_id)
	{
		$finalReference = $customOrderSlipReferenceData->reference_number_format;
		if (strpos($finalReference, '{COUNTER}') !== false)
		{
			Order::checkForCounterReset(3);
			$getAllCounterData = Tools::jsonDecode(Configuration::getGlobalValue('counterNumberData'), true);
			$currentCounterData = $getAllCounterData[3];
			$finalReference = str_replace('{COUNTER}', Order::getCounterValue($currentCounterData), $finalReference);
			$nextCounterValue = (int)$currentCounterData['counter_actual_value'] + (int)$currentCounterData['counter_increment_by'];
			$currentCounterData['counter_actual_value'] = $nextCounterValue;
			$getAllCounterData[3] = $currentCounterData;
			Configuration::updateGlobalValue('counterNumberData', Tools::jsonEncode($getAllCounterData));
		}
		$finalReference = str_replace('{RANDOM_NUMBER}', Tools::strtoupper(Tools::passwdGen($customOrderSlipReferenceData->random_number_length, 'NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHABETIC}', Tools::strtoupper(Tools::passwdGen($customOrderSlipReferenceData->random_alphabetic_length, 'NO_NUMERIC')), $finalReference);
		$finalReference = str_replace('{RANDOM_ALPHANUMERIC}', Tools::strtoupper(Tools::passwdGen($customOrderSlipReferenceData->random_alphanumeric_length, 'ALPHANUMERIC')), $finalReference);
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
	public static function getCMROrderSlipsByOrderID($orderId)
	{
		return Db::getInstance()->executeS('
			SELECT id_order_slip, crn_order_slip_number
			FROM `'._DB_PREFIX_.'order_slip`
			WHERE id_order = '.(int)$orderId);
	}
}