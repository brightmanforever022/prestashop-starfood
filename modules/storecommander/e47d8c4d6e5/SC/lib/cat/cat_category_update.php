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

	$action=Tools::getValue('action');
	$id_lang=intval(Tools::getValue('id_lang'));
	$newId=intval(Tools::getValue('gr_id'));

	function categoryChildren(&$to_delete, $id_category)
	{
		if (!is_array($to_delete) || !$id_category)
			return false;
		$result = Db::getInstance()->executeS('
		SELECT `id_category`
		FROM `'._DB_PREFIX_.'category`
		WHERE `id_parent` = '.(int)$id_category);
		foreach ($result as $row)
			$to_delete[] = (int)$row['id_category'];
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


	switch($action){
		case 'move':
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
					$k=1;
					$newpos=0;
					$done=false;
					$todo=array();
					$sql="SELECT c.id_category, c.id_parent, cs.position FROM "._DB_PREFIX_."category c
								LEFT JOIN "._DB_PREFIX_."category_shop cs ON (c.id_category=cs.id_category AND cs.id_shop=".(int)SCI::getSelectedShop().")
								WHERE c.id_parent='".intval($idNewParent)."'
								ORDER BY cs.position";
					$res=Db::getInstance()->ExecuteS($sql);
					foreach($res as $row){
						if ($row['id_category']==$idNextBrother)
						{
							$sql2="SELECT c.id_parent,cs.position 
										 FROM "._DB_PREFIX_."category c
										 LEFT JOIN "._DB_PREFIX_."category_shop cs ON (c.id_category=cs.id_category ".(SCI::getSelectedShop()>0?"AND cs.id_shop=".(int)SCI::getSelectedShop():"").")
										 WHERE c.id_category='".(int)$idCateg."'";
							$categInfo=Db::getInstance()->getRow($sql2);
							$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent=".intval($idNewParent).",date_upd=NOW() WHERE id_category=".intval($idCateg);
							$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg)." AND id_shop = ".(int)SCI::getSelectedShop();
							$done=true;
							$newpos=$k;
							$k++;
						}
						if ($row['id_category']!=$idCateg)
						{
							$todo[]="UPDATE "._DB_PREFIX_."category SET position=position".($done ? ",date_upd=NOW()":"")." WHERE id_category=".intval($row['id_category']);
							$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($row['id_category'])." AND id_shop = ".(int)SCI::getSelectedShop();
						}
						$k++;
					}
					addToHistory('catalog_tree','move_categ','id_parent',intval($idCateg),$id_lang,_DB_PREFIX_."category",'Parent ID:'.intval($idNewParent).' - Position:'.$newpos,(isset($categInfo)?'Parent ID:'.$categInfo['id_parent'].' - Position:'.intval($newpos):''));
					if (!$done) // Dnd to the end of a branch
					{
						$todo[]="UPDATE "._DB_PREFIX_."category SET id_parent='".intval($idNewParent)."',date_upd=NOW() WHERE id_category=".intval($idCateg);
						$todo[]="UPDATE "._DB_PREFIX_."category_shop SET position=".intval($k)." WHERE id_category=".intval($idCateg)." AND id_shop = ".(int)SCI::getSelectedShop();
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
					$sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."category";
					$nbCateg=_qgv($sqlc);
					if ($nbCateg<=50)
						Category::regenerateEntireNtree();
					SCI::hookExec('categoryUpdate');				
				}
				ExtensionPMCM::clearFromIdsCategory($idCateg);
			}
			break;
		case 'insert':
			$id_parent=intval(Tools::getValue('id_parent',1));
			$name=str_replace('"', "'", (Tools::getValue('name','new')));
			//echo $name;//t'ést" bl&a
			$newcategory=new Category();
			$newcategory->id_parent=$id_parent;
			$newcategory->level_depth=$newcategory->calcLevelDepth();
			$newcategory->active=0;
			if (SCMS && SCI::getSelectedShopActionList()) {
				$newcategory->id_shop_list = SCI::getSelectedShopActionList();
				$newcategory->id_shop_default = SCI::getSelectedShop();
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
			if (!sc_in_array(1,$newcategory->getGroups(),"catCategoryupdate_catgroups".$newcategory->id))
				$newcategory->addGroups(array(1));
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$shops=Category::getShopsByCategory((int)$id_parent);
				foreach($shops AS $shop)
				{
					$position = Category::getLastPosition((int)$id_parent, $shop['id_shop']);
					if (!$position)
						$position = 1;
					$newcategory->addPosition($position, $shop['id_shop']);
				}
			}
			echo $newcategory->id;
			exit;
		break;
		case 'emptybin':
			include_once(SC_PS_PATH_DIR.'/images.inc.php');
			$id_category=intval(Tools::getValue('id_category',0));
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
				ExtensionPMCM::clearFromIdsCategory($id_category);
			}
			exit;
		break;
		case 'changedefault':
/* obsolete
				$default=intval(Tools::getValue('default'));
				if ($default)
				{
					$id_product=intval(Tools::getValue('id_product'));
					$sql="UPDATE "._DB_PREFIX_."product SET date_upd=NOW(),id_category_default='".intval($newId)."' WHERE id_product=".intval($id_product);
					Db::getInstance()->Execute($sql);
				}
*/
				$action='updated';
			break;
		case 'sort_and_save':
			$id_category=intval(Tools::getValue('id_category'));
			$children=(Tools::getValue('children'));
			if (!empty($children))
			{
				$child_cat = explode(',', $children);
				foreach ($child_cat as $key => $value)
				{
					$sql="UPDATE "._DB_PREFIX_."category SET date_upd=NOW(), position='".intval($key)."' WHERE id_category='".intval($value)."'";
					Db::getInstance()->Execute($sql);

					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$sql="UPDATE "._DB_PREFIX_."category_shop SET position='".intval($key)."' WHERE id_category='".intval($value)."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
						Db::getInstance()->Execute($sql);
					}
				}
			}
			$action='updated';
			SCI::hookExec('categoryUpdate');
			ExtensionPMCM::clearFromIdsCategory($id_category);
			break;
		case 'enable':
				$enable=intval(Tools::getValue('enable'));
				$id_category=intval(Tools::getValue('id_category'));
				$sql="UPDATE "._DB_PREFIX_."category SET date_upd=NOW(),active='".intval($enable)."' WHERE id_category=".intval($id_category);
				Db::getInstance()->Execute($sql);
				$action='updated';
				SCI::hookExec('categoryUpdate');
				ExtensionPMCM::clearFromIdsCategory($id_category);
			break;
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<data>';
	echo "<action type='".$action."' sid='".$newId."' tid='".$newId."'/>";
	$debug=false;
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	if ($debug && isset($todo) ) {echo '<sql><![CDATA[';print_r($todo);echo']]></sql>';}
	echo '</data>';
