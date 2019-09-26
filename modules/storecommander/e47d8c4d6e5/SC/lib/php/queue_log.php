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
class QueueLog
{
	public static function add($name, $row, $action, $params=array(), $callback=null, $date=null)
	{
		global $sc_agent;
		$return = 0;
		
		if(is_array($params))
			$params = json_encode($params);
		
		if(!empty($name)/* && !empty($row)*/ && !empty($action))
		{
			if(empty($date))
				$date = date("Y-m-d H:i:s");
			
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'sc_queue_log` (`name`,`row`,`action`,`params`,`callback`,`id_employee`,`date_add`)
				VALUES ("'.pSQL($name).'","'.pSQL($row).'","'.pSQL($action).'","'.pSQL($params).'","'.pSQL($callback).'","'.intval($sc_agent->id_employee).'","'.pSQL($date).'")');
			$id = Db::getInstance()->Insert_ID();
			if(!empty($id))
				$return = $id;
		}
		return $return;
	}
	
	public static function delete($id)
	{
		if(!empty($id) && is_numeric($id))
		{
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'sc_queue_log`
				WHERE `id_sc_queue_log` = '.(int)$id);
		}
	}
	
	public static function getForRun($id)
	{
		$return = array();
		if(!empty($id) && is_numeric($id))
		{
			$row = Db::getInstance()->executeS('
				SELECT * FROM `'._DB_PREFIX_.'sc_queue_log`
				WHERE `id_sc_queue_log` = "'.(int)$id.'"');
			if(!empty($row[0]["id_sc_queue_log"]))
			{
				$return = array(
					"name"=>$row[0]["name"],
					"row"=>$row[0]["row"],
					"action"=>$row[0]["action"],
					"params"=>$row[0]["params"],
					"callback"=>$row[0]["callback"],
				);
			}
		}
		return $return;
	}
}