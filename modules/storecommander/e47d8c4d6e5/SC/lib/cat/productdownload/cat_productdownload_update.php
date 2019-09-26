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
$id_objet=intval(Tools::getValue('gr_id',0));
	

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){
		$fields=array('display_filename','filename','date_expiration','nb_days_accessible','nb_downloadable','active');
		$todo=array();
		foreach($fields AS $field)
		{
			if (isset($_POST[$field]))
			{
				if($field=="date_expiration" && (empty($_POST[$field]) || $_POST[$field]=="0000-00-00"))
					$todo[]=$field."=NULL";
				elseif(($field=="nb_days_accessible" || $field=="nb_downloadable") && (empty($_POST[$field]) || !is_numeric($_POST[$field])))
					$todo[]=$field."=NULL";
				else
					$todo[]=$field."='".psql($_POST[$field])."'";
				addToHistory('product_download','modification',$field,intval($id_objet),0,_DB_PREFIX_."product_download",psql(Tools::getValue($field)));
			}
		}
		if (count($todo))
		{
			$sql = "UPDATE "._DB_PREFIX_."product_download SET ".join(' , ',$todo)." WHERE id_product_download=".intval($id_objet);
			Db::getInstance()->Execute($sql);
		}
		$newId = $_POST["gr_id"];
		$action = "update";

		$id_product=intval(Tools::getValue('id_product'));
		// PM Cache
		if(!empty($id_product))
			ExtensionPMCM::clearFromIdsProduct($id_product);
		
	}elseif(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="deleted"){

		$download = new ProductDownload((int)($id_objet));
		$id_product = $download->id_product;
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			@unlink(_PS_DOWNLOAD_DIR_."/".$download->filename);
		else
			@unlink(_PS_DOWNLOAD_DIR_."/".$download->physically_filename);
		$download->delete();
				
		$product = new Product($id_product);
		$product->is_virtual = 0;
		$product->save();

		
		$newId = $_POST["gr_id"];
		$action = "delete";
		
	}
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' tid='".$newId."'/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	echo '</data>';
