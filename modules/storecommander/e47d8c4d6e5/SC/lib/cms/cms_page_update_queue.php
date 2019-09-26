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
				$id_cms=$row->row;
				$updated_cms[$id_cms]=$id_cms;
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
					$id_cms_category=Tools::getValue('id_cms_category',null);
					$newcms=new CMS();
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && SCMS)
						$newcms->id_shop_list = SCI::getSelectedShopActionList(false);
					$newcms->active=_s('CMS_PAGE_CREA_ACTIVE');
					if(!empty($id_cms_category)) {
						$newcms->id_cms_category = (int)$id_cms_category;
					}
					foreach($languages AS $lang)
					{
						$newcms->meta_title[$lang['id_lang']]='new';
						$newcms->meta_description[$lang['id_lang']]='';
						$newcms->meta_keywords[$lang['id_lang']]='';
						$newcms->content[$lang['id_lang']]='';
						$newcms->link_rewrite[$lang['id_lang']]='new-cms';
					}
					$newcms->save();
					$newId = $newcms->id;

					if(!empty($newId))
					{
                        $callbacks = str_replace("{newid}", $newId, $callbacks) ;
					}
					
				}
				elseif(!empty($action) && $action=="delete" && !empty($gr_id))
				{
					$cms=new CMS((int)$gr_id);
					if (SCMS)
					{
						$sql="SELECT id_shop FROM "._DB_PREFIX_."cms_shop WHERE id_cms=".(int)$cms->id;
						$id_shop_list_array=Db::getInstance()->ExecuteS($sql);
						$id_shop_list = array();
						foreach ($id_shop_list_array as $array_shop)
							$id_shop_list[] = $array_shop['id_shop'];
						$cms->id_shop_list = $id_shop_list;
					}
					$cms->delete();
					addToHistory('cms_tree','delete',"cms",(int)$cms->id,null,_DB_PREFIX_."cms",null,null);
				}
				elseif(!empty($action) && $action=="update" && !empty($gr_id))
				{

					$id_lang=(int)Tools::getValue('id_lang');
					$id_cms = $id_cms; // for compatibility with old extensions - DO NOT REMOVE
					$fields=array('id_cms','position','active');
					if (version_compare(_PS_VERSION_, '1.5.6.1', '>=')) {
						$fields[] = 'indexation';
					}
					$fields_lang=array('meta_title','meta_description','meta_keywords','content','link_rewrite');
					$fieldsWithHTML=array('content');
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
									if( _r('ACT_CMS_ENABLE_PAGES')){
										$todo[]="`active`='".psql(Tools::getValue($field))."'";
									}
									break;
								case 'indexation':
									if( _r('ACT_CMS_ENABLE_INDEXATION')){
										$todo[]="`indexation`='".psql(Tools::getValue($field))."'";
									}
									break;
							}
						}
					}

					foreach($fields_lang AS $field)
					{
						if (isset($_POST[$field]))
						{
							$value=psql(Tools::getValue($field),(sc_in_array($field,$fieldsWithHTML,"cmsPageUpdateQueue_fieldsWithHTML")?true:false));
							if ($field == 'meta_title' && _s('CMS_SEO_META_TITLE_TO_URL')) {
								$todo_lang[]="`link_rewrite`='".pSQL(link_rewrite($value))."'";
							}
							$todo_lang[]="`".$field."`='".$value."'";
							addToHistory('cms_tree','modification',$field,(int)$id_cms,$id_lang,_DB_PREFIX_."cms_lang",$value);
						}
					}
					if (count($todo))
					{
						$sql = "UPDATE "._DB_PREFIX_."cms SET ".join(' , ',$todo)." WHERE id_cms=".(int)$id_cms;
						Db::getInstance()->Execute($sql);
					}
					if (count($todo_lang))
					{
						$sql = "UPDATE "._DB_PREFIX_."cms_lang SET ".join(' , ',$todo_lang)." WHERE id_cms=".(int)$id_cms." AND id_lang=".(int)$id_lang;
						if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
							$sql .= " AND id_shop IN (".psql(SCI::getSelectedShopActionList(true)).")";
						if ($debug) $dd.=$sql2."\n";
						Db::getInstance()->Execute($sql);
					}
				}

				elseif(!empty($action) && $action=="position")
				{
					$id_cms_category=(int)Tools::getValue('id_cms_category');
					$todo=array();
					$row=explode(';',Tools::getValue('positions'));
					foreach($row AS $v)
					{
						if ($v!='')
						{
							$pos=explode(',',$v);
							$todo[]="UPDATE "._DB_PREFIX_."cms SET position=".(int)$pos[1]." WHERE id_cms_category=".(int)$id_cms_category." AND id_cms=".(int)$pos[0];
						}
					}

					foreach($todo AS $task)
					{
						Db::getInstance()->Execute($task);
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
