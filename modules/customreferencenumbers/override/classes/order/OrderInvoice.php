<?php
/**
 * Module allow to set custom number of your invoices.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class OrderInvoice extends OrderInvoiceCore
{
	public function getInvoiceNumberFormatted($id_lang, $id_shop = null)
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customInvoiceReferenceData = $customNumberReferenceData[1];
		if ($customInvoiceReferenceData->activate_reference_number)
			return self::getCrnInvoiceNumberByOrderId($this->id_order);
		else
			return parent::getInvoiceNumberFormatted($id_lang, $id_shop);
	}
	public static function getCrnInvoiceNumberByOrderId($orderId)
	{
		$sql = 'SELECT crn_invoice_number FROM '._DB_PREFIX_.'orders WHERE id_order = '.(int)$orderId;
		if ($row = Db::getInstance()->getRow($sql))
			return $row['crn_invoice_number'];
	}
}
