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
$mode=intval(Tools::getValue('mode'));

if($mode=="move")
{
	$id_segment=intval(str_replace("seg_","",Tools::getValue('segmentTarget')));
	$droppedCustomers=Tools::getValue('orders');
	$orders=explode(',',$droppedCustomers);
	
	if(!empty($orders) && !empty($id_segment))
	{
		foreach($orders as $order)
		{
			if(!ScSegmentElement::checkInSegment($id_segment, $order, "order"))
			{
				$segment_element = new ScSegmentElement();
				$segment_element->id_segment = intval($id_segment);
				$segment_element->id_element = intval($order);
				$segment_element->type_element = "order";
				$segment_element->save();
			}
		}
	}
}