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

class SegmentHook
{
	static $listFiles = array();
	
	public function __construct()
	{
		if (SC_TOOLS && file_exists(SC_SEGMENTS_DIR) && is_dir(SC_SEGMENTS_DIR))
		{
			$files = scanDirectory(SC_SEGMENTS_DIR);
			
			foreach($files as $file)
			{
				if($file["name"]!="index.php" || $file["name"]!="index.html" || $file["name"]!="index.htm")
				{
					if(strpos($file["name"], "Segment.php")!==false)
						self::$listFiles[]=$file["name"];
				}
			}
			
			usort(self::$listFiles, "sortSegment");
		}
	}
	
	
	public static function hook($name, $params=array())
	{
		$return = null;
		if(!empty($name))
		{
			foreach(self::$listFiles as $file)
			{
				if(file_exists(SC_SEGMENTS_DIR.$file))
				{
					require_once(SC_SEGMENTS_DIR.$file);
					$class_name = str_replace(".php", "", $file);
					$instance = new $class_name();
					
					if(!empty($instance->liste_hooks) && in_array($name, $instance->liste_hooks))
					{
						$returned = $instance->executeHook($name, $params);
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
			}
		}
		return $return;
	}
	
	
	public static function hookByIdSegment($name, $segment, $params=array())
	{
		$return = null;
		if(!empty($name) && !empty($segment->id))
		{
			if($segment->type=="auto" && !empty($segment->auto_file))
			{
				$file = $segment->auto_file.".php";
				if(file_exists(SC_SEGMENTS_DIR.$file))
				{
					require_once(SC_SEGMENTS_DIR.$file);
					$instance = new $segment->auto_file();
					if(!empty($instance->liste_hooks) && in_array($name, $instance->liste_hooks))
					{
						$params["auto_params"]=$segment->auto_params;
						$params["id_segment"]=$segment->id;
						$returned = $instance->executeHook($name, $params);
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
			}
		}
		return $return;
	}
	
	public static function getSegmentLevelFromDB($parent_id, $access)
	{
		if(!empty($access) && SCSG)
		{
			$sql = "SELECT *
						FROM "._DB_PREFIX_."sc_segment
						WHERE id_parent = '".intval($parent_id)."'
							AND access LIKE '%-".pSQL($access)."-%'
						ORDER BY name";
		
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res AS $row)
			{
				$manual_add = 0;
				if($row["type"]=="manual")
					$manual_add=1;
				elseif($row["auto_file"])
				{
					$file = $row["auto_file"].".php";
					if(file_exists(SC_SEGMENTS_DIR.$file))
					{
						require_once(SC_SEGMENTS_DIR.$file);
						$instance = new $row["auto_file"]();
						if($instance->manually_add_in=="Y")
							$manual_add=1;
					}
				}

				$image = "blue_folder.png";
				if($row["type"]=="auto")
					$image = "blue_folder_synchro.png";
				
				echo "<item nocheckbox=\"1\" id=\"seg_".$row['id_segment']."\" im0=\"".$image."\" im1=\"".$image."\" im2=\"".$image."\" open=\"1\" >";
					echo	"<itemtext><![CDATA[".$row["name"]."]]></itemtext>";
					echo '  <userdata name="is_segment">1</userdata>';
					echo '  <userdata name="manuel_add">'.intval($manual_add).'</userdata>';
					self::getSegmentLevelFromDB($row['id_segment'], $access);
				echo '</item>'."\n";
			}
		}
	}
	
	public static function accessDBtoString($access)
	{
		$return = "";
		$explodes = explode("-",$access);
		foreach($explodes as $acces)
		{
			if(!empty($acces))
			{
				if(!empty($return))
					$return .= ", ";

				if($acces=="catalog")
					$return .= _l("Catalog");
				elseif($acces=="orders")
					$return .= _l("Orders");
				elseif($acces=="customers")
					$return .= _l("Customers");
				elseif($acces=="customer_service")
					$return .= _l("Customer service");
			}
		}
		return $return;
	}
}

function sortSegment($a, $b)
{
	if ($a == $b) {
		return 0;
	}
	return ($a < $b) ? -1 : 1;
}