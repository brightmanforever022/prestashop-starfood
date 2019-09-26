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
$rowslist=Tools::getValue('rowslist','');
if ($rowslist!='')
{
	$rowslistarray=explode(",",$rowslist);
	foreach($rowslistarray AS $id_specific_price)
	{
		if(!empty($id_specific_price))
		{
			$specificPrice = new SpecificPrice((int)($id_specific_price));
			$specificPrice->delete();
		}
	}
}
