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

@error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
@ini_set("display_errors", "ON");

$id_lang = Tools::getValue('id_lang','0');
$action = Tools::getValue('action','');

$return = "ERROR: Try again later";


// FUNCTIONS
$debug=false;
$extraVars='';
$updated_cms = array();
$return_datas = array();

// Récupération de toutes les modifications à effectuer
if(!empty($_POST["rows"]) || $action=="insert")
{
	if($action!="insert")
	{
		if(_PS_MAGIC_QUOTES_GPC_)
			$_POST["rows"] = stripslashes($_POST["rows"]);
		$rows = json_decode($_POST["rows"]);
	}
	else
	{
		$rows = array();
		$rows[0] = new stdClass();
		$rows[0]->name = Tools::getValue('act','');
		$rows[0]->action = Tools::getValue('action','');
		$rows[0]->row = Tools::getValue('gr_id','');
		$rows[0]->callback = Tools::getValue('callback','');
		$rows[0]->params = $_POST;
	}

	if(is_array($rows) && count($rows)>0)
	{
		$callbacks = '';
		
		// Première boucle pour remplir la table sc_queue_log 
		// avec toutes ces modifications
		$log_ids = array();
		$date = date("Y-m-d H:i:s");

		foreach($rows as $num => $row)
		{
			$id = QueueLog::add($row->name, $row->row, $row->action, (!empty($row->params)?$row->params:array()), (!empty($row->callback)?$row->callback:null), $date);
			$log_ids[$num] = $id;
		}
		
		// Deuxième boucle pour effectuer les 
		// actions les une après les autres

		foreach($rows as $num => $row)
		{

			if(!empty($log_ids[$num]))
			{
				$gr_id = (int)$row->row;
				$id_manufacturer=$row->row;
				$updated_cms[$id_manufacturer]=$id_manufacturer;
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}

				if(!empty($action) && $action=="insert")
				{
					$newManufacturer=new Manufacturer();
					$newManufacturer->name=$_POST['name'];
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$newManufacturer->id_shop_list = SCI::getSelectedShopActionList();
					$newManufacturer->active=_s('MAN_MANUF_CREA_ACTIVE');

					foreach($languages AS $lang)
					{
						$newManufacturer->meta_title[$lang['id_lang']]='new';
						$newManufacturer->link_rewrite[$lang['id_lang']]='new-manufacturer';
					}
					$newManufacturer->save();
					$newId = $newManufacturer->id;

					if(!empty($newId))
					{
                        $callbacks = str_replace("{newid}", $newId, $callbacks) ;
					}
					
				}
				elseif(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$manufacturer=new Manufacturer((int)$gr_id);
					if (SCMS)
					{
						$sql="SELECT id_shop FROM "._DB_PREFIX_."manufacturer_shop WHERE id_manufacturer=".(int)$manufacturer->id;
						$id_shop_list_array=Db::getInstance()->ExecuteS($sql);
						$id_shop_list = array();
						foreach ($id_shop_list_array as $array_shop)
							$id_shop_list[] = $array_shop['id_shop'];
						$manufacturer->id_shop_list = $id_shop_list;
					}
					$manufacturer->delete();
					addToHistory('man_tree','delete',"manufacturer",(int)$manufacturer->id,null,_DB_PREFIX_."manufacturer",null,null);
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{
					$id_lang=(int)Tools::getValue('id_lang');
					$id_manufacturer = $id_manufacturer; // for compatibility with old extensions - DO NOT REMOVE
					$fields=array('id_manufacturer','name','active');
					$fields_lang=array('meta_title','meta_description','meta_keywords');
					$fieldsWithHTML=array();
					$todo=array();
					$todoshop=array();
					$todo_lang=array();
					$versSuffix='';

					foreach($fields AS $field)
					{
						if (isset($_POST[$field]))
						{
							switch($field) {
								case 'active':
									if( _r('ACT_MAN_ENABLE_MANUFACTURER')){
										$todo[]="`active`='".psql(Tools::getValue($field))."'";
									}
									break;
								default:
									$value=psql(Tools::getValue($field),(sc_in_array($field,$fieldsWithHTML,"manufacturerUpdateQueue_fieldsWithHTML")?true:false));
									$todo[]="`".$field."`='".$value."'";
									break;
							}
						}
					}

					foreach($fields_lang AS $field)
					{
						if (isset($_POST[$field]))
						{
							$value=psql(Tools::getValue($field),(sc_in_array($field,$fieldsWithHTML,"manufacturerUpdateQueue_fieldsWithHTML")?true:false));
							$todo_lang[]="`".$field."`='".$value."'";
							addToHistory('man_tree','modification',$field,(int)$id_manufacturer,$id_lang,_DB_PREFIX_."manufacturer_lang",$value);
						}
					}
					if (count($todo))
					{
						$sql = "UPDATE "._DB_PREFIX_."manufacturer SET ".join(' , ',$todo)." WHERE id_manufacturer=".(int)$id_manufacturer;
						Db::getInstance()->Execute($sql);
					}
					if (count($todo_lang))
					{
						$sql = "UPDATE "._DB_PREFIX_."manufacturer_lang SET ".join(' , ',$todo_lang)." WHERE id_manufacturer=".(int)$id_manufacturer." AND id_lang=".(int)$id_lang;
						if ($debug) $dd.=$sql2."\n";
						Db::getInstance()->Execute($sql);
					}
				}

				$return_callback = "";
				foreach($return_datas as $key=>$val)
				{
					if(!empty($key))
					{
						if(!empty($return_callback))
							$return_callback .= ",";
						$return_callback .= $key.":'".str_replace("'","\'", $val)."'";
					}
				}
				if(!empty($extraVars))
				{
					if(!empty($return_callback))
						$return_callback .= ",";
					$return_callback .= $extraVars;
				}
				$return_callback = "{".$return_callback."}";
				$callbacks = str_replace("{data}", $return_callback, $callbacks) ;
				
				QueueLog::delete(($log_ids[$num]));
			}

		}

		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}



echo $return;
