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
$ids=explode(',',Tools::getValue('ids','0'));

$return = array();

foreach($ids as $id)
{
	if(!empty($id) && is_numeric($id))
	{
		$return[] = QueueLog::getForRun($id);
		QueueLog::delete($id);
	}
}

if(!empty($return) && count($return))
	echo json_encode($return);