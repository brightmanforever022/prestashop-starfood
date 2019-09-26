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

class SegmentCustom
{
	public $name;
	public $liste_hooks = array();
	public $liste_actions_right_clic = array();
	public $manually_add_in = "N";
	
	public function executeHook($name, $params=array())
	{
		$return = null;
		if(!empty($name) && in_array($name, $this->liste_hooks))
		{
			if(method_exists($this, "_executeHook_".$name))
			{
				$returned = $this->{"_executeHook_".$name}($name, $params);
				if(is_array($returned))
				{
					if(empty($return))
						$return = array();
					$return = array_merge($return, $returned);
				}
				else
					$return .= $returned;
			}
		}
		return $return;
	}
	
	
}