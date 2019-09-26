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
	
	if(version_compare(_PS_VERSION_, '1.4.0.0', '<'))
	{
		if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		 		header("Content-type: application/xhtml+xml"); 
		} else {
		 		header("Content-type: text/xml");
		}
		echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
		echo '<tree id="0">';
		echo "<item ".
						" id=\"0\"".
						" text=\""._l('Home')."\"".
						" im0=\"catalog.png\"".
						" im1=\"catalog.png\"".
						" im2=\"catalog.png\">";
		echo '</item>'."\n";
		echo '</tree>';
		echo '';
		exit;
	}

	$sql = "SELECT c.id_cms_category FROM "._DB_PREFIX_."cms_category c
					LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$sc_agent->id_lang.")
					WHERE cl.name LIKE '%".psql(_l('SC Recycle Bin'))."'";
	$res=Db::getInstance()->ExecuteS($sql);
	if (count($res)==0)
	{
		$newCmsCategory=new CMSCategory();
		$newCmsCategory->id_parent=1;
		$newCmsCategory->level_depth=1;
		$newCmsCategory->active=0;
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
		{
			// bug PS1.4 - set position
			$_GET['id_parent']=1;
			$newCmsCategory->position=CMSCategory::getLastPosition(1);
		}
		foreach($languages AS $lang)
		{
			$newCmsCategory->link_rewrite[$lang['id_lang']]='category';
			$newCmsCategory->name[$lang['id_lang']]='SC Recycle Bin';
			if ($lang['iso_code']=='fr')
				$newCmsCategory->name[$lang['id_lang']]='SC Corbeille';
		}
		$newCmsCategory->save();
	}

	function getLevelFromDB($parent_id)
	{
		global $id_lang;
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
		{
			$sql = "SELECT c.active,c.id_cms_category,name FROM "._DB_PREFIX_."cms_category c
							LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang.")
							WHERE c.id_parent=".(int)$parent_id."
							ORDER BY cl.name";
		}else{
			$sql = "SELECT c.active,c.id_cms_category,name FROM "._DB_PREFIX_."cms_category c
							LEFT JOIN "._DB_PREFIX_."cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang=".(int)$id_lang.")
							WHERE c.id_parent=".(int)$parent_id."
							ORDER BY c.position";
		}
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $k => $row){
			$style='';
			if (hideCategoryPosition($row['name'])=='')
			{
				$sql2 = 'SELECT c.active,c.id_cms_category,name FROM '._DB_PREFIX_.'cms_category c
						LEFT JOIN '._DB_PREFIX_.'cms_category_lang cl ON (cl.id_cms_category=c.id_cms_category AND cl.id_lang='.(int)$id_lang.'
						WHERE c.id_cms_category='.(int)$row['id_cms_category'];
				$res2=Db::getInstance()->getRow($sql2);
				$style='style="background:lightblue" ';
			}
			$icon=($row['active']?'catalog.png':'catalog_edit.png');
			if (in_array(hideCategoryPosition($row['name']),array('SC Recycle Bin', 'SC Corbeille')))
				$icon='folder_delete.png';
			echo "<item ".($style!='' ? $style:'').
									" id=\"".$row['id_cms_category']."\"".($parent_id==0?' open="1"':'').
									" text=\"".($style==''?formatText(hideCategoryPosition($row['name'])):_l('To Translate:').' '.formatText(hideCategoryPosition($res2['name'])))."\"".
									" im0=\"".$icon."\"".
									" im1=\"".$icon."\"".
									" im2=\"".$icon."\">";
			getLevelFromDB($row['id_cms_category']);
			echo '</item>'."\n";
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	getLevelFromDB(0);
	echo '</tree>';
