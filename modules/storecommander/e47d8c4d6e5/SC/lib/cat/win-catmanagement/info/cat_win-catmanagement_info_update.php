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
	$gr_id=(Tools::getValue('gr_id',0));

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated")
	{
		if(SCMS)
			list($id_category,$id_lang,$id_shop) = explode("_",$gr_id);
		else
			list($id_category,$id_lang) = explode("_",$gr_id);
		
		$fields=array('name','description');
		$todo=array();
		foreach($fields AS $field)
		{
			if (isset($_GET[$field]) || isset($_POST[$field]))
			{
				$val=Tools::getValue($field);
				if($field!="name" || ($field=="name" && !empty($val)))
				{
					if($field=="name")
					{
						if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
						{
							$sql = "SELECT name FROM "._DB_PREFIX_."category_lang WHERE id_category='".intval($id_category)."' AND id_lang='".intval($id_lang)."'";
							$actual = Db::getInstance()->ExecuteS($sql);
							if(!empty($actual[0]["name"]) && preg_match('/^[0-9]+\./', $actual[0]["name"])>0)
							{
								$exp = explode(".",$actual[0]["name"]);
								$val = $exp[0].".".$val;
							}
						}
						if (_s('CAT_SEO_CAT_NAME_TO_URL'))
						{
							$todo[]="`link_rewrite`='".pSQL(link_rewrite($val))."'";
						}
					}
					$todo[]=$field."='".pSQL(html_entity_decode( $val ))."'";
				}
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."category_lang SET ".join(' , ',$todo)." WHERE id_category=".intval($id_category)." AND id_lang=".intval($id_lang)." ";
			if(SCMS)
				$sql .= " AND id_shop=".intval($id_shop);
			Db::getInstance()->Execute($sql);
		}
		
		$newId = $_POST["gr_id"];
		$action = "update";

		// PM Cache
		if(!empty($id_category))
			ExtensionPMCM::clearFromIdsCategory($id_category);
		
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo '</data>';
