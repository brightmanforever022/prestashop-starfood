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
	$id_lang=(int)Tools::getValue('id_lang');
	$id_cms_category=(Tools::getValue('gr_id',0));
	
	$id_shop = Tools::getValue('id_shop',0);
	$in_all_shops = Tools::getValue('in_all_shops',0);

	$action = Tools::getValue('action',"");
	
	$field = Tools::getValue('field',"");
	$value = Tools::getValue('value',"");

	$id_parent=(int)Tools::getValue('id_parent',0);
	$name = Tools::getValue('name',"new");

	$id_cms_categories=(Tools::getValue('id_cms_categories',0));

	/*
	 * FUNCTIONS
	 */


	function categoryChildren(&$array, $id_cms_category)
	{
		if (!is_array($array) || !$id_cms_category)
			return false;
		$result = Db::getInstance()->executeS('
		SELECT `id_cms_category`
		FROM `'._DB_PREFIX_.'cms_category`
		WHERE `id_parent` = '.(int)$id_cms_category);
		foreach ($result as $row)
			$array[] = (int)$row['id_cms_category'];
	}

	function SCMSdeleteCmsCategory($id_cms_category,$binCategory)
	{
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
			$sql = 'SELECT id_shop
					FROM '._DB_PREFIX_.'cms_category_shop
					GROUP BY id_shop';
			$shop_list=Db::getInstance()->executeS($sql);
		}
		$cmsCategory=new CMSCategory($id_cms_category);
		if (Validate::isLoadedObject($cmsCategory))
		{
			if ((int)$cmsCategory->id === 0 || (int)$cmsCategory->id === 1)
				return false;

			$children = array();
			categoryChildren($children,$cmsCategory->id);
			foreach ($children as $id_cms_cat)
			{
				$cmsCat = new CMSCategory($id_cms_cat);
				SCMSdeleteCmsCategory($cmsCat->id,$binCategory);
			}
			if ($id_cms_category!=$binCategory)
			{
				if (version_compare(_PS_VERSION_, '1.6.0.12', '>=')) {
					$cmsCategory->id_shop_list = $shop_list;
				}
				$cmsCategory->delete();
			}
		}
	}

	$duplicated_ids = array();
	function duplicateCmsCategories($id_cms_category, $id_parent)
	{
		global $duplicated_ids;
		if(empty($id_cms_category) || empty($id_parent))
			return false;

		$cms_category_parent = new CMSCategory($id_parent);
		$last_position = (int)(Db::getInstance()->getValue('
						SELECT MAX(`position`)
						FROM `'._DB_PREFIX_.'cms_category`
						WHERE `id_parent` = '.(int)$id_parent) + 1);

		// INSERT IN ps_cms_category
		$result = Db::getInstance()->executeS('
						SELECT *
						FROM `'._DB_PREFIX_.'cms_category`
						WHERE `id_cms_category` = '.(int)$id_cms_category);
		if(!empty($result[0]["id_cms_category"]))
		{
			$fields = $result[0];
			unset($fields["id_cms_category"]);
			unset($fields["nleft"]);
			unset($fields["nright"]);
			if(isset($fields["level_depth"]))
				$fields["level_depth"]=$cms_category_parent->level_depth+1;
			if(isset($fields["date_add"]))
				$fields["date_add"]=date("Y-m-d H:i:s");
			if(isset($fields["date_upd"]))
				$fields["date_upd"]=date("Y-m-d H:i:s");
			if(isset($fields["id_parent"]))
				$fields["id_parent"]=$id_parent;
			if(isset($fields["position"]))
				$fields["position"]=$last_position;

			$names = array();
			$values = array();
			foreach($fields as $key=>$val)
			{
				$names[]=pSQL($key);
				$values[]=pSQL($val);
			}

			$sql_insert = 'INSERT INTO `'._DB_PREFIX_.'cms_category` (`'.implode("`,`", $names).'`)
									VALUES ("'.implode('","', $values).'")';
			Db::getInstance()->execute($sql_insert);
			$new_id_cat = Db::getInstance()->Insert_ID();

			if(!empty($new_id_cat))
			{
				$duplicated_ids[$new_id_cat] = $new_id_cat;
				if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
				{
					// INSERT IN ps_cms_category_shop
					$results = Db::getInstance()->executeS('
									SELECT *
									FROM `'._DB_PREFIX_.'cms_category_shop`
									WHERE `id_cms_category` = '.(int)$id_cms_category);
					foreach($results as $result)
					{
						$fields = $result;
						$fields["id_cms_category"]=(int)$new_id_cat;

						$names = array();
						$values = array();
						foreach($fields as $key=>$val)
						{
							$names[]=pSQL($key);
							$values[]=pSQL($val);
						}

						$sql_insert = 'INSERT INTO `'._DB_PREFIX_.'cms_category_shop` (`'.implode("`,`", $names).'`)
										VALUES ("'.implode('","', $values).'")';
						Db::getInstance()->execute($sql_insert);
					}
				}

				// INSERT IN ps_cms_category_lang
				$results = Db::getInstance()->executeS('
								SELECT *
								FROM `'._DB_PREFIX_.'cms_category_lang`
								WHERE `id_cms_category` = '.(int)$id_cms_category);
				foreach($results as $result)
				{
					$fields = $result;
					$fields["id_cms_category"]=(int)$new_id_cat;

					$names = array();
					$values = array();
					foreach($fields as $key=>$val)
					{
						$names[]=pSQL($key);
						$values[]=pSQL($val);
					}

					$sql_insert = 'INSERT INTO `'._DB_PREFIX_.'cms_category_lang` (`'.implode("`,`", $names).'`)
									VALUES ("'.implode('","', $values).'")';
					Db::getInstance()->execute($sql_insert);
				}

				// GET CHILDREN
				$result = Db::getInstance()->executeS('
								SELECT `id_cms_category`
								FROM `'._DB_PREFIX_.'cms_category`
								WHERE `id_parent` = '.(int)$id_cms_category);
				foreach ($result as $row)
				{
					if(empty($duplicated_ids[$row['id_cms_category']]))
						duplicateCmsCategories((int)$row['id_cms_category'], (int)$new_id_cat);
				}
			}
		}
	}
	
	/*
	 * ACTION
	 */
	if(!empty($action) && $action=="insert" && !empty($name))
	{
		$name=str_replace('"', "'", $name);
		$newCmsCategory=new CMSCategory();
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
			$newCmsCategory->id_shop_list=array((int)$id_shop);
		$newCmsCategory->id_parent=(int)$id_parent;
		$newCmsCategory->active=0;
		foreach($languages AS $lang)
		{
			$newCmsCategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
			$newCmsCategory->name[$lang['id_lang']]=$name;
		}
		$newCmsCategory->add();
		echo $newCmsCategory->id;
	}

	if(!empty($action) && $action=="update" && !empty($field))
	{
		if ($field=="name" && !empty($value))
		{
			if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && (empty($id_shop) || !empty($in_all_shops)))
			{
				$shops = Shop::getShops(false);
				foreach($shops as $shop)
				{
					$insert = false;
					
					$exist = "SELECT id_cms_category 
								FROM "._DB_PREFIX_."cms_category_lang 
								WHERE id_cms_category='".(int)$id_cms_category."' AND id_lang='".(int)$id_lang."' AND id_shop='".(int)$shop["id_shop"]."'";
					$exist = Db::getInstance()->ExecuteS($exist);
					if(empty($exist[0]["id_cms_category"]))
						$insert = true;

					if(!$insert)
					{
						$url_rewrite = "";
						if (_s('CMS_SEO_CAT_NAME_TO_URL'))
						{
							$url_rewrite=", `link_rewrite`='".pSQL(link_rewrite($value))."'";
						}
						$sql = "UPDATE "._DB_PREFIX_."cms_category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_cms_category='".(int)$id_cms_category."' AND id_lang='".(int)$id_lang."' AND id_shop='".(int)$shop["id_shop"]."'";
						Db::getInstance()->Execute($sql);
					} else {
						$sql = "INSERT INTO "._DB_PREFIX_."cms_category_lang (id_cms_category,id_shop,id_lang,name,link_rewrite)
										VALUES ('".(int)$id_cms_category."','".(int)$shop["id_shop"]."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
				}
			}
			else
			{
				$insert = false;
				$exist = "SELECT id_cms_category FROM "._DB_PREFIX_."cms_category_lang WHERE id_cms_category='".(int)$id_cms_category."' AND id_lang='".(int)$id_lang."'";
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
					$exist .= " AND id_shop='".(int)$id_shop."'";
				$exist = Db::getInstance()->ExecuteS($exist);
				if(empty($exist[0]["id_cms_category"]))
					$insert = true;
				
				if(!$insert)
				{
					if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
					{
						$sql = "SELECT name FROM "._DB_PREFIX_."cms_category_lang WHERE id_cms_ategory='".(int)$id_cms_category."' AND id_lang='".(int)$id_lang."'";
						$actual = Db::getInstance()->ExecuteS($sql);
						if(!empty($actual[0]["name"]) && preg_match('/^[0-9]+\./', $actual[0]["name"])>0)
						{
							$exp = explode(".",$actual[0]["name"]);
							$value = $exp[0].".".$value;
						}
					}
					
					$url_rewrite = "";
					if (_s('CMS_SEO_CAT_NAME_TO_URL'))
					{
						$url_rewrite=", `link_rewrite`='".pSQL(link_rewrite($value))."'";
					}
					
					$sql = "UPDATE "._DB_PREFIX_."cms_category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_cms_category='".(int)$id_cms_category."' AND id_lang='".(int)$id_lang."'";
					if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
						$sql .= " AND id_shop='".(int)$id_shop."'";
					Db::getInstance()->Execute($sql);
				}
				else
				{
					if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && !empty($id_shop))
					{
						$sql = "INSERT INTO "._DB_PREFIX_."cms_category_lang (id_cms_category,id_shop,id_lang,name,link_rewrite)
								VALUES ('".(int)$id_cms_category."','".(int)$id_shop."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
					else
					{
						$sql = "INSERT INTO "._DB_PREFIX_."cms_category_lang (id_cms_category,id_lang,name,link_rewrite)
								VALUES ('".(int)$id_cms_category."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
				}
			}
		}
		
		if ($field=="active")
		{
			$sql = "UPDATE "._DB_PREFIX_."cms_category SET active='".pSQL($value)."' WHERE id_cms_category='".(int)$id_cms_category."'";
			Db::getInstance()->Execute($sql);
		}
				
	}
	if(!empty($action) && $action=="move")
	{
		$idCateg=(int)Tools::getValue('idCateg');
		$idNewParent=(int)Tools::getValue('idNewParent',0);
		$idNextBrother=(int)Tools::getValue('idNextBrother');
		if ($idCateg!=0 && $idNewParent!=0)
		{
			$k=1;
			$newpos=0;
			$done=false;
			$todo=array();
			$sql="SELECT c.id_cms_category, c.id_parent, c.position FROM "._DB_PREFIX_."cms_category c
					WHERE c.id_parent='".(int)$idNewParent."'
					ORDER BY c.position";
			$res=Db::getInstance()->ExecuteS($sql);
			foreach($res as $row){
				if ($row['id_cms_category']==$idNextBrother)
				{
					$sql2="SELECT c.id_parent,c.position
							 FROM "._DB_PREFIX_."cms_category c
							 WHERE c.id_cms_category='".(int)$idCateg."'";
					$categInfo=Db::getInstance()->getRow($sql2);
					$todo[]="UPDATE "._DB_PREFIX_."cms_category SET id_parent=".(int)$idNewParent.",position=".(int)$k.",date_upd=NOW() WHERE id_cms_category=".(int)$idCateg;
					$done=true;
					$newpos=$k;
					$k++;
				}
				if ($row['id_cms_category']!=$idCateg)
				{
					$todo[]="UPDATE "._DB_PREFIX_."cms_category SET position=".(int)$k."".($done ? ",date_upd=NOW()":"")." WHERE id_cms_category=".(int)$row['id_cms_category'];
				}
				$k++;
			}
			addToHistory('catalog_tree','move_categ','id_parent',(int)$idCateg,$id_lang,_DB_PREFIX_."cms_category",'Parent ID:'.(int)$idNewParent.' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.(int)$newpos:''));
			if (!$done) // Dnd to the end of a branch
			{
				$todo[]="UPDATE "._DB_PREFIX_."cms_category SET id_parent='".(int)$idNewParent."',position=".(int)$k.",date_upd=NOW() WHERE id_cms_category=".(int)$idCateg;
			}
			foreach($todo as $sqlTotal)
			{
				Db::getInstance()->Execute($sqlTotal);
			}

			$sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."cms_category";
			$nbCateg=_qgv($sqlc);
		}		
	}

	if(!empty($action) && $action=="emptybin")
	{
		if ($id_cms_category > 1)
		{
			if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
			{
				$sql = 'SELECT id_cms_category
						FROM '._DB_PREFIX_.'cms_category
						WHERE id_parent = '.(int)$id_cms_category;
				$res = Db::getInstance()->ExecuteS($sql);


				foreach ($res as $cmscategory) {
					$cmsCateg = new CMSCategory((int)$cmscategory['id_cms_category']);
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
						$sql = 'SELECT id_shop
							FROM ' . _DB_PREFIX_ . 'cms_category_shop
							WHERE id_cms_category = ' . (int)$cmscategory['id_cms_category'];
						$shops = Db::getInstance()->ExecuteS($sql);
						$arrayShops = Array();
						foreach ($shops as $shop) {
							$arrayShops[] = $shop['id_shop'];
						}
						if (version_compare(_PS_VERSION_, '1.6.0.12', '>=')){
							$cmsCateg->id_shop_list = $arrayShops;
						}
					}
					$cmsCateg->delete();
				}
			}
		}
	}

	if(!empty($action) && $action=="active_cms")
	{
		if(!empty($id_cms_categories))
		{
			Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'cms`
				SET active = "'.(int)$value.'"
				WHERE id_cms_category IN ('.pSQL($id_cms_categories).')');
		}
	}
	if(!empty($action) && $action=="paste_multiple" && !empty($id_cms_category))
	{
		$id_parent=(int)Tools::getValue('id_parent',0);
		if(!empty($id_parent))
		{
			duplicateCmsCategories($id_cms_category, $id_parent);
		}
	}
