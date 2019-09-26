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
	$droppedProducts=Tools::getValue('products');
	$products=explode(',',$droppedProducts);

	if(!empty($products) && !empty($id_segment))
	{
		foreach($products as $product)
		{
			if(!ScSegmentElement::checkInSegment($id_segment, $product, "product"))
			{
				$segment_element = new ScSegmentElement();
				$segment_element->id_segment = intval($id_segment);
				$segment_element->id_element = intval($product);
				$segment_element->type_element = "product";
				$segment_element->save();
			}
		}
	}
}