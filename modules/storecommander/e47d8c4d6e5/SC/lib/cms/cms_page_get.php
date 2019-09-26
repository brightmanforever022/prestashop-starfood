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
	
	$view=Tools::getValue('view','grid_light');
	$grids=SCI::getGridViews("cms");
	
	$exportedCms = array();
	$cdata=(isset($_COOKIE['cg_cms_treegrid_col_'.$view])?$_COOKIE['cg_cms_treegrid_col_'.$view]:'');
	//check validity
	$check=explode(',',$cdata);
	foreach($check as $c)
		if ($c=='undefined')
		{
			$cdata='';
			break;
		}
	if ($cdata!='') $grids[$view]=$cdata;

	$cols=explode(',',$grids[$view]);

	$colSettings=array();
	$colSettings=SCI::getGridFields("cms");

	function getColSettingsAsXML()
	{
		global $cols,$colSettings,$view;
		
		$uiset = uisettings::getSetting('cms_grid_'.$view);
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[2]);
		$sizes = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$sizes[$s[0]] = $s[1];
		}
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[0]);
		$hidden = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$hidden[$s[0]] = $s[1];
		}
		
		$xml='';

		foreach($cols AS $id => $col)
		{
			$xml.='<column id="'.$col.'"'.(sc_array_key_exists('format',$colSettings[$col])?
					' format="'.$colSettings[$col]['format'].'"':'').
					' width="'.( sc_array_key_exists($col,$sizes) ? $sizes[$col] : $colSettings[$col]['width']).'"'.
					' hidden="'.( sc_array_key_exists($col,$hidden) ? $hidden[$col] : 0 ).'"'.
					' align="'.$colSettings[$col]['align'].'" 
					type="'.$colSettings[$col]['type'].'" 
					sort="'.$colSettings[$col]['sort'].'" 
					color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text'];
			if (!empty($colSettings[$col]['options']))
			{
				foreach($colSettings[$col]['options'] AS $k => $v)
				{
					$xml.='<option value="'.str_replace('"','\'',$k).'"><![CDATA['.$v.']]></option>'; 
				}
			}
			$xml.='</column>'."\n";
		}
		return $xml;
	}

	function getFooterColSettings()
	{
		global $cols,$colSettings;

		$footer='';
		foreach($cols AS $id => $col)
		{
			if (sc_array_key_exists($col,$colSettings) && sc_array_key_exists('footer',$colSettings[$col]))
				$footer.=$colSettings[$col]['footer'].',';
			else
				$footer.=',';
		}
		return $footer;
	}

	function getFilterColSettings()
	{
		global $cols,$colSettings;
		
		$filters='';
		foreach($cols AS $id => $col)
		{
			if($colSettings[$col]['filter']=="na")
				$colSettings[$col]['filter'] = "";
			$filters.=$colSettings[$col]['filter'].',';
		}
		$filters=trim($filters,',');
		return $filters;
	}


	function getPages($id_cms_category = null)
	{
		global $col,$id_lang,$cols,$colSettings;

		if(SCMS) {
			$id_shop = SCI::getSelectedShop();
			$res = CMS::getCMSPages($id_lang, $id_cms_category, false, ($id_shop > 0 ? (int)$id_shop : (int)Configuration::get("PS_SHOP_DEFAULT")));
		} else {
			$res = CMS::getCMSPages($id_lang, $id_cms_category, false);
		}


		foreach($res as $cmsRow){

			echo '<row id="'.$cmsRow['id_cms'].'">';
			echo '  <userdata name="id_cms">'.(int)$cmsRow['id_cms'].'</userdata>';

			foreach($cols AS $key => $col)
			{
				switch($col){
					case'id':
						echo 	"<cell>".$cmsRow['id_cms']."</cell>"; //  style=\"color:tan\"
						break;
					case'meta_title':case'meta_description':case'meta_keywords':case'content':case'link_rewrite':
						echo '<cell><![CDATA['.$cmsRow[$col].']]></cell>';
						break;
					default:
						if (sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
						{
							if ($colSettings[$col]['buildDefaultValue']=='ID')
								echo "<cell>ID".$cmsRow['id_cms']."</cell>";
						}else{
							if ($cmsRow[$col]=='' || $cmsRow[$col]===0 || $cmsRow[$col]===1) // opti perf is_numeric($cmsRow[$col]) || 
							{
								echo "<cell>".$cmsRow[$col]."</cell>";
							}else{
								echo "<cell><![CDATA[".$cmsRow[$col]."]]></cell>";
							}
						}
				}
			}
			echo "</row>\n";
		}
		if ($_GET['tree_mode']=='all')
			getSubCategoriesPages($id_cms_category);
	}

	function getSubCategoriesPages($parent_id)
	{
		$sql = "SELECT c.id_cms_category FROM "._DB_PREFIX_."cms_category c WHERE c.id_parent=".(int)$parent_id;
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row){
			getPages($row['id_cms_category']);
			getSubCategoriesPages($row['id_cms_category']);
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
		header("Content-type: application/xhtml+xml");
	} else {
		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<rows><head>';
	echo getColSettingsAsXML();
	echo '<afterInit>
						<call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
						<call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call>
					</afterInit>';
	echo '</head>';

	$uiset = uisettings::getSetting('cms_grid_'.$view);
	$tmp = explode('|',$uiset);
	$uiset = "|".$tmp[1]."||".$tmp[3];
	echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
	echo '<userdata name="LIMIT_SMARTRENDERING">'.(int)_s("CMS_PAGE_LIMIT_SMARTRENDERING").'</userdata>';
	echo "\n";
	getPages($_GET['idc']);

	if (isset($_GET['DEBUG'])) echo '<az><![CDATA['.$dd.']]></az>';
	echo '</rows>';
