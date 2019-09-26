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
	$id_lang=intval(Tools::getValue('id_lang'));
	$id_category=(Tools::getValue('gr_id',0));
	
	$id_shop = Tools::getValue('id_shop',0);
	$in_all_shops = Tools::getValue('in_all_shops',0);

	$action = Tools::getValue('action',"");
	
	$field = Tools::getValue('field',"");
	$value = Tools::getValue('value',"");

	$id_parent=intval(Tools::getValue('id_parent',0));
	$name = Tools::getValue('name',"new");

	$id_categories=(Tools::getValue('id_categories',0));
	$id_bin = intval(Tools::getValue('id_bin',"0"));

	/*
	 * FUNCTIONS
	 */


	function categoryChildren(&$array, $id_category)
	{
		if (!is_array($array) || !$id_category)
			return false;
		$result = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)$id_category);
		foreach ($result as $row)
			$array[] = (int)$row['id_category'];
	}
	
	function SCMSdeleteCategory($id_category,$binCategory)
	{
		$category=new Category($id_category);
		if (Validate::isLoadedObject($category) && !$category->isRootCategoryForAShop())
		{
			if ((int)$category->id === 0 || (int)$category->id === 1)
				return false;
	
			$children = array();
			categoryChildren($children,$category->id);
			foreach ($children as $id_cat)
			{
				$cat = new Category($id_cat);
				if ($cat->isRootCategoryForAShop())
					continue;
				SCMSdeleteCategory($cat->id,$binCategory);
			}
			if ($id_category!=$binCategory)
			{
				$category->id_shop_list = $category->getAssociatedShops();
				$category->deleteLite();
				$category->deleteImage(true);
				$category->cleanGroups();
				$category->cleanAssoProducts();
				// Delete associated restrictions on cart rules
				CartRule::cleanProductRuleIntegrity('categories', array($category->id));
				SCMSCleanPositionsInAllShops($category->id_parent);
				/* Delete Categories in GroupReduction */
				if (GroupReduction::getGroupsReductionByCategoryId((int)$category->id))
					GroupReduction::deleteCategory($category->id);
				Hook::exec('actionCategoryDelete', array('category' => $category));
			}else{
				Category::regenerateEntireNtree();
			}
		}
	}
	
	function SCMSCleanPositionsInAllShops($id_category_parent = null)
	{
		if ($id_category_parent === null)
			return;
		$return = true;
	
		$id_shop_list = Shop::getShops(false, null, true);
	
		foreach($id_shop_list AS $id_shop)
		{
	
			$result = Db::getInstance()->executeS('
				SELECT c.`id_category`
				FROM `'._DB_PREFIX_.'category` c
				LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
					ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
				WHERE c.`id_parent` = '.(int)$id_category_parent.'
				ORDER BY cs.`position`
			');
			$count = count($result);
			for ($i = 0; $i < $count; $i++)
			{
			$sql = '
			UPDATE `'._DB_PREFIX_.'category` c
					LEFT JOIN `'._DB_PREFIX_.'category_shop` cs
						ON (c.`id_category` = cs.`id_category` AND cs.`id_shop` = '.(int)$id_shop.')
							SET cs.`position` = '.(int)$i.'
							WHERE c.`id_parent` = '.(int)$id_category_parent.'
							AND c.`id_category` = '.(int)$result[$i]['id_category'];
							$return &= Db::getInstance()->execute($sql);
			}
		}
		return $return;
	}

	$duplicated_ids = array();
    function duplicateCategories($id_category, $id_parent)
    {
        global $duplicated_ids;
        if(empty($id_category) || empty($id_parent))
            return false;

        $category_parent = new Category($id_parent);
        $last_position = (int)(Db::getInstance()->getValue('
                    SELECT MAX(`position`)
                    FROM `'._DB_PREFIX_.'category`
                    WHERE `id_parent` = '.(int)$id_parent) + 1);

        // INSERT IN ps_category
        $result = Db::getInstance()->executeS('
                    SELECT *
                    FROM `'._DB_PREFIX_.'category`
                    WHERE `id_category` = '.(int)$id_category);
        if(!empty($result[0]["id_category"]))
        {
            $fields = $result[0];
            unset($fields["id_category"]);
            unset($fields["nleft"]);
            unset($fields["nright"]);
            if(isset($fields["level_depth"]))
                $fields["level_depth"]=$category_parent->level_depth+1;
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
                $names[]=$key;
                $values[]=$val;
            }

            $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category` (`'.implode("`,`", $names).'`)
                                VALUES ("'.implode('","', $values).'")';
            Db::getInstance()->execute($sql_insert);
            $new_id_cat = Db::getInstance()->Insert_ID();

            if(!empty($new_id_cat))
            {
                $duplicated_ids[$new_id_cat] = $new_id_cat;
                if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                {
                    // INSERT IN ps_category_shop
                    $results = Db::getInstance()->executeS('
                                SELECT *
                                FROM `'._DB_PREFIX_.'category_shop`
                                WHERE `id_category` = '.(int)$id_category);
                    foreach($results as $result)
                    {
                        $fields = $result;
                        $fields["id_category"]=(int)$new_id_cat;
                        $fields["position"]=Category::getLastPosition($id_parent, (int)$fields["id_shop"]);

                        $names = array();
                        $values = array();
                        foreach($fields as $key=>$val)
                        {
                            $names[]=$key;
                            $values[]=$val;
                        }

                        $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_shop` (`'.implode("`,`", $names).'`)
                                    VALUES ("'.implode('","', $values).'")';
                        Db::getInstance()->execute($sql_insert);
                    }
                }

                // INSERT IN ps_category_lang
                $results = Db::getInstance()->executeS('
                            SELECT *
                            FROM `'._DB_PREFIX_.'category_lang`
                            WHERE `id_category` = '.(int)$id_category);
                foreach($results as $result)
                {
                    $fields = $result;
                    $fields["id_category"]=(int)$new_id_cat;

                    $names = array();
                    $values = array();
                    foreach($fields as $key=>$val)
                    {
                        $names[]=$key;
                        $values[]=$val;
                    }

                    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_lang` (`'.implode("`,`", $names).'`)
                                VALUES ("'.implode('","', $values).'")';
                    Db::getInstance()->execute($sql_insert);
                }

                // INSERT IN ps_category_group
                $results = Db::getInstance()->executeS('
                            SELECT *
                            FROM `'._DB_PREFIX_.'category_group`
                            WHERE `id_category` = '.(int)$id_category);
                foreach($results as $result)
                {
                    $fields = $result;
                    $fields["id_category"]=(int)$new_id_cat;

                    $names = array();
                    $values = array();
                    foreach($fields as $key=>$val)
                    {
                        $names[]=$key;
                        $values[]=$val;
                    }

                    $sql_insert = 'INSERT INTO `'._DB_PREFIX_.'category_group` (`'.implode("`,`", $names).'`)
                                VALUES ("'.implode('","', $values).'")';
                    Db::getInstance()->execute($sql_insert);
                }

                // ADD IMAGE
                $actual_image_name = _PS_CAT_IMG_DIR_.(int)$id_category.'.jpg';
                $new_image_name = _PS_CAT_IMG_DIR_.(int)$new_id_cat.'.jpg';
                if(file_exists($actual_image_name))
                {
                    if(copy ( $actual_image_name , $new_image_name ))
                    {
                        $images_types = ImageType::getImagesTypes('categories');
                        foreach ($images_types as $k => $image_type)
                        {
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                ImageManager::resize(
                                    $new_image_name,
                                    _PS_CAT_IMG_DIR_.$new_id_cat.'-'.stripslashes($image_type['name']).'.jpg',
                                    (int)$image_type['width'], (int)$image_type['height']
                                );
                            }
                            else
                                imageResize($new_image_name, _PS_CAT_IMG_DIR_.$new_id_cat.'-'.stripslashes($image_type['name']).'.jpg', (int)($image_type['width']), (int)($image_type['height']));
                        }
                    }
                }

                // GET CHILDREN
                $result = Db::getInstance()->executeS('
                            SELECT `id_category`
                            FROM `'._DB_PREFIX_.'category`
                            WHERE `id_parent` = '.(int)$id_category);
                foreach ($result as $row)
                {
                    if(empty($duplicated_ids[$row['id_category']]))
                        duplicateCategories((int)$row['id_category'], (int)$new_id_cat);
                }
            }
        }
    }
	
	/*
	 * ACTION
	 */
	if(!empty($action) && $action=="insert" && !empty($name))
	{
		$position = 0;
		$last_position = Db::getInstance()->executeS('
		SELECT MAX(c.`position`) as position
		FROM `'._DB_PREFIX_.'category` c
		WHERE c.`id_parent` = '.(int)$id_parent);
		if(!empty($last_position[0]["position"]))
			$position = $last_position[0]["position"] + 1;
		
		$name=str_replace('"', "'", $name);
		$newcategory=new Category();
		$newcategory->id_parent=$id_parent;
		$newcategory->level_depth=$newcategory->calcLevelDepth();
		$newcategory->position = $position;
		$newcategory->active=0;
		$id_shop = (int)Tools::getValue('id_shop');
		if (SCMS && SCI::getSelectedShopActionList()) {
			$newcategory->id_shop_list = array($id_shop);
			$newcategory->id_shop_default = $id_shop;
			$_POST['checkBoxShopAsso_category'] = array();
			foreach ($newcategory->id_shop_list as $id) {
				$_POST['checkBoxShopAsso_category'][$id] = $id;
			}
		}
		foreach($languages AS $lang)
		{
			$newcategory->link_rewrite[$lang['id_lang']]=link_rewrite($name);
			$newcategory->name[$lang['id_lang']]=$name;
		}
		$newcategory->add();
		if (!sc_in_array(1,$newcategory->getGroups(),"catWinCatManaUpdate_groups_".$newcategory->id))
			$newcategory->addGroups(array(1));
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if(SCMS)
			{
				Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'category_shop`
				WHERE `id_category` = '.(int)$newcategory->id);
			}
			$shops=Category::getShopsByCategory((int)$id_parent);
			foreach($shops AS $shop)
			{
				$position = Category::getLastPosition((int)$id_parent, $shop['id_shop']);
				if (!$position)
					$position = 1;
				$newcategory->addPosition($position, $shop['id_shop']);
			}
		}
		/*if (SCMS)
		{
			$shop_list = array();
			if($in_all_shops || empty($id_shop))
			{
				$shops = Shop::getShops(false);
				foreach ($shops as $shop)
					$shop_list[] = $shop["id_shop"];
			}
			elseif(!empty($id_shop))
				$shop_list = array($id_shop);
			
			if(!empty($shop_list))
				$newcategory->id_shop_list = $shop_list;
		}*/
		echo $newcategory->id;

	}
	if(!empty($action) && $action=="update" && !empty($field))
	{
		if ($field=="name" && !empty($value))
		{
			
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (empty($id_shop) || !empty($in_all_shops)))
			{
				$shops = Shop::getShops(false);
				foreach($shops as $shop)
				{
					$insert = false;
					
					$exist = "SELECT id_category FROM "._DB_PREFIX_."category_lang WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."' AND id_shop='".intval($shop["id_shop"])."'";
					$exist = Db::getInstance()->ExecuteS($exist);
					if(empty($exist[0]["id_category"]))
						$insert = true;

					if(!$insert)
					{
						$url_rewrite = "";
						if (_s('CAT_SEO_CAT_NAME_TO_URL'))
						{
							$url_rewrite=", `link_rewrite`='".pSQL(link_rewrite($value))."'";
						}
						$sql = "UPDATE "._DB_PREFIX_."category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."' AND id_shop='".intval($shop["id_shop"])."'";
						Db::getInstance()->Execute($sql);
					}
					else
					{
						$sql = "INSERT INTO "._DB_PREFIX_."category_lang (id_category, id_shop,id_lang,name,link_rewrite)
										VALUES ('".(int)$id_category."','".(int)$shop["id_shop"]."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
				}
			}
			else
			{
				$insert = false;
				$exist = "SELECT id_category FROM "._DB_PREFIX_."category_lang WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."'";
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
					$exist .= " AND id_shop='".intval($id_shop)."'";
				$exist = Db::getInstance()->ExecuteS($exist);
				if(empty($exist[0]["id_category"]))
					$insert = true;
				
				if(!$insert)
				{
					if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
					{
						$sql = "SELECT name FROM "._DB_PREFIX_."category_lang WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."'";
						$actual = Db::getInstance()->ExecuteS($sql);
						if(!empty($actual[0]["name"]) && preg_match('/^[0-9]+\./', $actual[0]["name"])>0)
						{
							$exp = explode(".",$actual[0]["name"]);
							$value = $exp[0].".".$value;
						}
					}
					
					$url_rewrite = "";
					if (_s('CAT_SEO_CAT_NAME_TO_URL'))
					{
						$url_rewrite=", `link_rewrite`='".pSQL(link_rewrite($value))."'";
					}
					
					$sql = "UPDATE "._DB_PREFIX_."category_lang SET name='".pSQL($value)."' ".$url_rewrite." WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."'";
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
						$sql .= " AND id_shop='".intval($id_shop)."'";
					Db::getInstance()->Execute($sql);
				}
				else
				{
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_shop))
					{
						$sql = "INSERT INTO "._DB_PREFIX_."category_lang (id_category, id_shop,id_lang,name,link_rewrite)
								VALUES ('".(int)$id_category."','".(int)$id_shop."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
					else
					{
						$sql = "INSERT INTO "._DB_PREFIX_."category_lang (id_category,id_lang,name,link_rewrite)
								VALUES ('".(int)$id_category."','".(int)$id_lang."','".pSQL($value)."','".pSQL(link_rewrite($value))."')";
						Db::getInstance()->Execute($sql);
					}
				}
			}
		}
		
		if ($field=="active")
		{
			$sql = "UPDATE "._DB_PREFIX_."category SET active='".pSQL($value)."' WHERE id_category='".intval($id_category)."'";
			Db::getInstance()->Execute($sql);
		}

		// PM Cache
		if(!empty($id_category))
			ExtensionPMCM::clearFromIdsCategory($id_category);
		
		SC_Ext::readCustomCategoriesGridConfigXML("onAfterUpdateSQL");
				
	}
	if(!empty($action) && $action=="move")
	{
			$idCateg=intval(Tools::getValue('idCateg'));
			$idNewParent=intval(Tools::getValue('idNewParent',0));
			$idNextBrother=intval(Tools::getValue('idNextBrother'));
			if ($idCateg!=0 && $idNewParent!=0)
			{
				if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
				{
					$sql="SELECT c.id_category, c.id_parent, cl.name FROM "._DB_PREFIX_."category c
								LEFT JOIN "._DB_PREFIX_."category_lang cl ON (c.id_category=cl.id_category AND cl.id_lang=".intval($id_lang).")
								WHERE c.id_parent='".intval($idNewParent)."' AND cl.id_category!='".intval($idCateg)."'
								ORDER BY cl.name";
					$res=Db::getInstance()->ExecuteS($sql);
					$k=1;
					$newpos=0;
					$done=false;
					$todo=array();
					foreach($res as $row){
						if ($row['id_category']==$idNextBrother)
						{
							$sql2="SELECT c.id_parent,cl.name FROM "._DB_PREFIX_."category c, "._DB_PREFIX_."category_lang cl WHERE c.id_category=cl.id_category AND cl.id_lang=".intval($id_lang)." AND c.id_category='".intval($idCateg)."'";
							$categInfo=Db::getInstance()->getRow($sql2);
							$todo[]="UPDATE "._DB_PREFIX_."category_lang SET name='".psql(($k<10?'0':'').$k.'.'.hideCategoryPosition($categInfo['name']))."' WHERE id_category=".intval($idCateg)." AND id_lang=".intval($id_lang)."";
							$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."' WHERE id_category=".intval($idCateg);
							$done=true;
							$newpos=$k;
							$k++;
						}
						$todo[]="UPDATE "._DB_PREFIX_."category_lang SET name='".psql(($k<10?'0':'').$k.'.'.hideCategoryPosition($row['name']))."' WHERE id_category=".intval($row['id_category'])." AND id_lang=".intval($id_lang)."";
						$k++;
					}
					addToHistory('catalog_tree','move_categ','id_parent',intval($idCateg),$id_lang,_DB_PREFIX_."category",'Parent ID:'.intval($idNewParent).' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.intval(substr($categInfo['name'],0,2)):''));
					if (!$done) // Dnd to the end of a branch
					{
						$sql3="SELECT cl.name FROM "._DB_PREFIX_."category c, "._DB_PREFIX_."category_lang cl WHERE c.id_category=cl.id_category AND cl.id_lang=".intval($id_lang)." AND c.id_category='".$idCateg."'";
						$categInfo=Db::getInstance()->getRow($sql3);
						$todo[]="UPDATE "._DB_PREFIX_."category_lang SET name='".psql(($k<10?'0':'').$k.'.'.hideCategoryPosition($categInfo['name']))."' WHERE id_category=".intval($idCateg)." AND id_lang=".intval($id_lang)."";
						$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."' WHERE id_category=".intval($idCateg);
					}
					foreach($todo as $sqlTotal)
					{
						Db::getInstance()->Execute($sqlTotal);
					}
					fixLevelDepth();
				}elseif (version_compare(_PS_VERSION_, '1.5.0.0', '<')){
					$k=1;
					$newpos=0;
					$done=false;
					$todo=array();
					$sql="SELECT c.id_category, c.id_parent, c.position FROM "._DB_PREFIX_."category c
								WHERE c.id_parent='".intval($idNewParent)."'
								ORDER BY c.position";
					$res=Db::getInstance()->ExecuteS($sql);
					foreach($res as $row){
						if ($row['id_category']==$idNextBrother)
						{
							$sql2="SELECT c.id_parent,c.position FROM "._DB_PREFIX_."category c WHERE c.id_category='".intval($idCateg)."'";
							$categInfo=Db::getInstance()->getRow($sql2);
							$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent=".intval($idNewParent).",position=".intval($k).",date_upd=NOW() WHERE id_category=".intval($idCateg);
							$done=true;
							$newpos=$k;
							$k++;
						}
						if ($row['id_category']!=$idCateg)
							$todo[]="UPDATE "._DB_PREFIX_."category SET position=".intval($k).($done ? ",date_upd=NOW()":"")." WHERE id_category=".intval($row['id_category']);
						$k++;
					}
					addToHistory('catalog_tree','move_categ','id_parent',intval($idCateg),$id_lang,_DB_PREFIX_."category",'Parent ID:'.intval($idNewParent).' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.intval($newpos):''));
					if (!$done) // Dnd to the end of a branch
					{
						$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."',position=".intval($k).",date_upd=NOW() WHERE id_category=".intval($idCateg);
					}
					foreach($todo as $sqlTotal)
					{
						Db::getInstance()->Execute($sqlTotal);
					}
					fixLevelDepth();
/*
	 	Trop long � executer pour les boutiques avec + de 50 cat�gories :
		l'utilisateur doit le faire une fois qu'il a termin� de d�placer ses cat�gories par le menu Catalogue > Outils > V�rifier et corriger les cat�gories
*/
					if (version_compare(_PS_VERSION_, '1.4.0.17', '>='))
					{
						$sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."category";
						$nbCateg=_qgv($sqlc);
						if ($nbCateg<=50)
							Category::regenerateEntireNtree();
					}
					SCI::hookExec('categoryUpdate');
				}else{ // PS 1.5
					if(!empty($id_shop))
					{
						$k=1;
						$newpos=0;
						$done=false;
						$todo=array();
						$sql="SELECT c.id_category, c.id_parent, cs.position FROM "._DB_PREFIX_."category c
									LEFT JOIN "._DB_PREFIX_."category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop=".(int)$id_shop.")
									WHERE c.id_parent='".intval($idNewParent)."'
									ORDER BY cs.position";
						$res=Db::getInstance()->ExecuteS($sql);
						foreach($res as $row){
							if ($row['id_category']==$idNextBrother)
							{
								$sql2="SELECT c.id_parent,cs.position 
											 FROM "._DB_PREFIX_."category c
											 LEFT JOIN "._DB_PREFIX_."category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop=".(int)$id_shop.")
											 WHERE c.id_category='".(int)$idCateg."'";
								$categInfo=Db::getInstance()->getRow($sql2);
								$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent=".intval($idNewParent).",date_upd=NOW() WHERE id_category=".intval($idCateg);
								if($in_all_shops)
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg);
								else
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg)." AND id_shop = ".(int)$id_shop;
								$done=true;
								$newpos=$k;
								$k++;
							}
							if ($row['id_category']!=$idCateg)
							{
								$todo[]="UPDATE "._DB_PREFIX_."category SET position=position".($done ? ",date_upd=NOW()":"")." WHERE id_category=".intval($row['id_category']);
								if($in_all_shops)
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($row['id_category']);
								else
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($row['id_category'])." AND id_shop = ".(int)$id_shop;
							}
							$k++;
						}
						addToHistory('catalog_tree','move_categ','id_parent',intval($idCateg),$id_lang,_DB_PREFIX_."category",'Parent ID:'.intval($idNewParent).' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.intval($newpos):''));
						if (!$done) // Dnd to the end of a branch
						{
							$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."',date_upd=NOW() WHERE id_category=".intval($idCateg);
							if($in_all_shops)
								$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg);
							else
								$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg)." AND id_shop = ".(int)$id_shop;
						}
						foreach($todo as $sqlTotal)
						{
							Db::getInstance()->Execute($sqlTotal);
						}	
					}
					else
					{
						$k=1;
						$newpos=0;
						$done=false;
						$todo=array();
						$sql="SELECT c.id_category, c.id_parent, c.position FROM "._DB_PREFIX_."category c
								WHERE c.id_parent='".intval($idNewParent)."'
								ORDER BY c.position";
						$res=Db::getInstance()->ExecuteS($sql);
						foreach($res as $row){
							if ($row['id_category']==$idNextBrother)
							{
								$sql2="SELECT c.id_parent,c.position
										 FROM "._DB_PREFIX_."category c
										 WHERE c.id_category='".(int)$idCateg."'";
								$categInfo=Db::getInstance()->getRow($sql2);
								$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent=".intval($idNewParent).",position=".intval($k).",date_upd=NOW() WHERE id_category=".intval($idCateg);
								if($in_all_shops)
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg);
								$done=true;
								$newpos=$k;
								$k++;
							}
							if ($row['id_category']!=$idCateg)
							{
								$todo[]="UPDATE "._DB_PREFIX_."category SET position=".intval($k)."".($done ? ",date_upd=NOW()":"")." WHERE id_category=".intval($row['id_category']);
								if($in_all_shops)
									$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($row['id_category']);
							}
							$k++;
						}
						addToHistory('catalog_tree','move_categ','id_parent',intval($idCateg),$id_lang,_DB_PREFIX_."category",'Parent ID:'.intval($idNewParent).' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.intval($newpos):''));
						if (!$done) // Dnd to the end of a branch
						{
							$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."',position=".intval($k).",date_upd=NOW() WHERE id_category=".intval($idCateg);
							if($in_all_shops)
								$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg);
						}
						foreach($todo as $sqlTotal)
						{
							Db::getInstance()->Execute($sqlTotal);
						}
					}	
					fixLevelDepth();

					$sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."category";
					$nbCateg=_qgv($sqlc);
					if ($nbCateg<=50)
						Category::regenerateEntireNtree();
					SCI::hookExec('categoryUpdate');			
				}
				// PM Cache
				if(!empty($idCateg))
					ExtensionPMCM::clearFromIdsCategory($idCateg);
			}		
	}
	if(!empty($action) && $action=="emptybin")
	{
		include_once(SC_PS_PATH_DIR.'/images.inc.php');
		if ($id_category > 1)
		{
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				Category::regenerateEntireNtree();
				$category=new Category($id_category);
				if (Validate::isLoadedObject($category) && !$category->isRootCategoryForAShop())
				{
					if (SCMS)
					{
						/*$id_shop_list=Db::getInstance()->getValue('SELECT GROUP_CONCAT(`id_shop`) AS id_shop_list FROM `'._DB_PREFIX_.'category_shop` WHERE `id_category` = '.(int)$id_category,false);
						 $category->id_shop_list=explode(',',$id_shop_list);
						$category->delete();*/
						SCMSdeleteCategory((int)$id_category,(int)$id_category);
					}else{
						$category->delete();
					}
		
					/* Delete products which were not in others categories */
					$result = Db::getInstance()->ExecuteS('
						SELECT `id_product`
						FROM `'._DB_PREFIX_.'product`
						WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)');
					foreach ($result as $p)
					{
						$product = new Product((int)$p['id_product']);
						if (Validate::isLoadedObject($product))
							$product->delete();
					}
					/* For products not deleted because of stock management or other... we place it in Recycle bin*/
					$result = Db::getInstance()->ExecuteS('
						SELECT `id_product`
						FROM `'._DB_PREFIX_.'product`
						WHERE `id_product` NOT IN (SELECT `id_product` FROM `'._DB_PREFIX_.'category_product`)');
					foreach ($result as $p)
					{
						$product = new Product((int)$p['id_product']);
						if (Validate::isLoadedObject($product))
							$product->addToCategories($id_category);
					}
		
					/* Set category default to one category used where category no more exists */
					$result = Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'product_shop` ps
						SET ps.`id_category_default` = (SELECT cp.id_category FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.id_product=ps.id_product LIMIT 1)
						WHERE `id_category_default` NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)');
		
				}
			}else{  // versions < 1.5
				$category=new Category($id_category);
				if (Validate::isLoadedObject($category))
				{
					$category->delete();
		
					/* Set category default to one category used where category no more exists */
					$result = Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'product` ps
						SET ps.`id_category_default` = (SELECT cp.id_category FROM `'._DB_PREFIX_.'category_product` cp WHERE cp.id_product=ps.id_product LIMIT 1)
						WHERE `id_category_default` NOT IN (SELECT `id_category` FROM `'._DB_PREFIX_.'category`)');
				}
		
				/* recreate SC Bin */
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
					$sql="INSERT INTO "._DB_PREFIX_."category (id_category,id_parent,level_depth,active,date_upd,position) VALUES (".(int)$category->id.",1,1,0,'".psql($category->date_upd)."',".(int)$category->position.")";
				else
					$sql="INSERT INTO "._DB_PREFIX_."category (id_category,id_parent,level_depth,active,date_upd) VALUES (".(int)$category->id.",1,1,0,'".psql($category->date_upd)."')";
				Db::getInstance()->Execute($sql);
				$sql="INSERT INTO "._DB_PREFIX_."category_group (id_category,id_group) VALUES (".(int)$category->id.",1)";
				Db::getInstance()->Execute($sql);
				foreach($languages AS $lang)
				{
					$sql="INSERT INTO "._DB_PREFIX_."category_lang (id_category,id_lang,name,link_rewrite) VALUES (".(int)$category->id.",".(int)$lang['id_lang'].",'SC Recycle Bin','SC-Recycle-Bin')";
					Db::getInstance()->Execute($sql);
				}
		
			}
			// PM Cache
			if(!empty($id_category))
				ExtensionPMCM::clearFromIdsCategory($id_category);
		}
	}
	if(!empty($action) && $action=="active_products")
	{
		if(!empty($id_categories))
		{
			if(empty($id_shop) || $in_all_shops)
			{
				Db::getInstance()->Execute('
				UPDATE `'._DB_PREFIX_.'product`
				SET active = "'.pSQL($value).'"
				WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pSQL($id_categories).'))');
				
				if(SCMS && $in_all_shops)
				{
					Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'product_shop`
					SET active = "'.pSQL($value).'"
					WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pSQL($id_categories).'))');
					
				}
				elseif(empty($id_shop) && version_compare(_PS_VERSION_, '1.5', '>='))
				{
					Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'product_shop`
					SET active = "'.pSQL($value).'"
					WHERE `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pSQL($id_categories).'))');
				}
			}
			elseif(SCMS && !empty($id_shop))
			{
				Db::getInstance()->Execute('
					UPDATE `'._DB_PREFIX_.'product_shop`
					SET active = "'.pSQL($value).'"
					WHERE id_shop = "'.(int)$id_shop.'" AND `id_product` IN (SELECT id_product FROM '._DB_PREFIX_.'category_product WHERE id_category IN ('.pSQL($id_categories).'))');
			}
			// PM Cache
			if(!empty($id_categories))
				ExtensionPMCM::clearFromIdsCategory($id_categories);
		}
	}
	if(!empty($action) && $action=="paste_multiple" && !empty($id_category))
	{
		require_once(dirname(__FILE__).'/../../all/upload/upload-image.inc.php');
		$id_parent=intval(Tools::getValue('id_parent',0));
		if(!empty($id_parent))
		{
			duplicateCategories($id_category, $id_parent);

            if (version_compare(_PS_VERSION_, '1.4.0.17', '>='))
            {
                $sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."category";
                $nbCateg=_qgv($sqlc);
                if ($nbCateg<=50)
                    Category::regenerateEntireNtree();
            }
            
			// PM Cache
			if(!empty($id_category))
				ExtensionPMCM::clearFromIdsCategory($id_category);
		}
	}
