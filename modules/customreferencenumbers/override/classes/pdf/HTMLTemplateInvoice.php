<?php
/**
 * Module allow to set custom number of your invoices.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class HTMLTemplateInvoice extends HTMLTemplateInvoiceCore
{
	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		parent::__construct($order_invoice, $smarty);
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customInvoiceReferenceData = $customNumberReferenceData[1];
		if ($customInvoiceReferenceData->activate_reference_number)
			$this->title = $order_invoice->getInvoiceNumberFormatted(Context::getContext()->language->id);
	}
	public function getFilename()
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customInvoiceReferenceData = $customNumberReferenceData[1];
		if ($customInvoiceReferenceData->activate_reference_number)
			return $this->order->crn_invoice_number.'.pdf';
		else
			return parent::getFilename();
	}
}
