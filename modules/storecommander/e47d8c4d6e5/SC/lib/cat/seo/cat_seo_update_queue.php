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

@error_reporting(E_ERROR | E_PARSE);
@ini_set("display_errors", "ON");

$id_lang = Tools::getValue('id_lang','0');
$action = Tools::getValue('action','');

$return = "ERROR: Try again later";

// FUNCTIONS
$updated_products = array();


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
				$gr_id = $row->row;
				$action = $row->action;
				
				if(!empty($row->callback))
					$callbacks .= $row->callback.";";

				if($action!="insert")
				{
					$_POST=array();
					$_POST = (array) json_decode($row->params);
				}

				if(!empty($action) && $action=="update" && !empty($gr_id))
				{
					if(SCMS)
						list($id_product,$id_lang, $id_shop) = explode("_",$gr_id);
					else
						list($id_product,$id_lang) = explode("_",$gr_id);
					$updated_products[$id_product]=$id_product;
					
					$list_lang_fields = "name,link_rewrite,meta_title,meta_description,meta_keywords";
					
					// LANG
					$fields=explode(",",$list_lang_fields);
					$todo=array();
					$link_rewrite = "";
					foreach($fields AS $field)
					{
						if (isset($_POST[$field]))
						{
							$val=Tools::getValue($field);
							$todo[]="`".$field."`='".psql(html_entity_decode( $val ))."'";
					
							if ($field=='name' && _s('CAT_SEO_NAME_TO_URL'))
							{
								$link_rewrite="`link_rewrite`='".link_rewrite($val)."'";
							}
						}
					}
					
					if(!empty($link_rewrite))
						$todo[]=$link_rewrite;
					
					if (count($todo))
					{
						if(SCMS)
							$sql = "UPDATE "._DB_PREFIX_."product_lang SET ".join(' , ',$todo)." WHERE id_product='".intval($id_product)."' AND id_shop='".intval($id_shop)."' AND id_lang='".intval($id_lang)."'";
						else
							$sql = "UPDATE "._DB_PREFIX_."product_lang SET ".join(' , ',$todo)." WHERE id_product='".intval($id_product)."' AND id_lang='".intval($id_lang)."'";
						Db::getInstance()->Execute($sql);
					}

					//update date_upd
					$sql = "UPDATE "._DB_PREFIX_."product SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product=".(int)$id_product.";";
					if(SCMS) {
						$sql .= "UPDATE "._DB_PREFIX_."product_shop SET date_upd = '".pSQL(date("Y-m-d H:i:s"))."' WHERE id_product=".(int)$id_product." AND id_shop = ".(int)$id_shop;
					}
					Db::getInstance()->Execute($sql);
				}
				
				QueueLog::delete(($log_ids[$num]));
			}
		}

		// PM Cache
		if(!empty($updated_products))
			ExtensionPMCM::clearFromIdsProduct($updated_products);
		
		// RETURN
		$return = json_encode(array("callback"=>$callbacks));
	}	
}
echo $return;