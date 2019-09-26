<?php
/**
 * Module allow to set custom number of your invoices.
 * 
 * @author    IT Present <cvikenzi@gmail.com>
 * @copyright 2015 IT Present
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class HTMLTemplateOrderSlip extends HTMLTemplateOrderSlipCore
{
	public function __construct(OrderSlip $order_slip, $smarty)
	{
		$customNumberReferenceData = Tools::jsonDecode(Configuration::get('customreferencenumbers'));
		$customDeliverySlipReferenceData = $customNumberReferenceData[3];
		if ($customDeliverySlipReferenceData->activate_reference_number)
		{
			parent::__construct($order_slip, $smarty);
			$this->title = $this->order_slip->crn_order_slip_number;
		}
		else
			parent::__construct($order_slip, $smarty);
	}
	public function getFilename()
	{
		return $this->order_slip->crn_order_slip_number.'.pdf';
	}

}
