<?php
/**
 * Modulu which offer option to choose if the cart rule can be applied to products with already reduced price.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class HTMLTemplateDeliverySlip extends HTMLTemplateDeliverySlipCore
{
	public function __construct(OrderInvoice $order_invoice, $smarty)
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customDeliverySlipReferenceData = $customNumberReferenceData[2];
		if ($customDeliverySlipReferenceData->activate_reference_number)
		{
			parent::__construct($order_invoice, $smarty);
			$this->title = $this->order->crn_delivery_number;
		}
		else
			parent::__construct($order_invoice, $smarty);
	}
	public function getFilename()
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customDeliverySlipReferenceData = $customNumberReferenceData[2];
		if ($customDeliverySlipReferenceData->activate_reference_number)
			return $this->order->crn_delivery_number.'.pdf';
		else
			return parent::getFilename();
	}
}
