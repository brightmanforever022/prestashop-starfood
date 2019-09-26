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

$idlist=Tools::getValue('idlist',0);
$id_lang=intval(Tools::getValue('id_lang'));

	function getDownloads()
	{
		global $idlist,$id_lang;
		
		$sql = 'SELECT *
				FROM `'._DB_PREFIX_.'product_download`
				WHERE id_product IN ('.pSQL($idlist).')';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$sql .= 'ORDER BY `date_add` ASC';
		elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$sql .= 'ORDER BY `date_deposit` ASC';
			
		$downloads = Db::getInstance()->executeS($sql);

		$products = array();
		
		foreach ($downloads as $download)
		{
			if(empty($products[$download["id_product"]]))
			{
				$product = new Product($download["id_product"], false, $id_lang);
				$products[$download["id_product"]] = $product; 
			}
			else
				$product = $products[$download["id_product"]];
				
			$color = '';
			if(!file_exists(_PS_DOWNLOAD_DIR_."/".$download['filename']))
				$color = 'style="background-color: #ffe1e1;"';
			
			echo "<row ".$color." id=\"".$download['id_product_download']."\">";
			echo 		"<cell>".$download['id_product_download']."</cell>";
			echo 		"<cell>".$download['id_product']."</cell>";
			echo 		"<cell><![CDATA[".$product->reference."]]></cell>";
			echo 		"<cell><![CDATA[".$product->supplier_reference."]]></cell>";
			echo 		"<cell><![CDATA[".$product->name."]]></cell>";
			echo 		"<cell><![CDATA[".$download['display_filename']."]]></cell>";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				echo 		"<cell>".$download['date_add']."</cell>";
			else
				echo 		"<cell>".$download['date_deposit']."</cell>";
			echo 		"<cell>".$download['date_expiration']."</cell>";
			echo 		"<cell>".$download['nb_days_accessible']."</cell>";
			echo 		"<cell>".$download['nb_downloadable']."</cell>";
			echo 		"<cell>".$download['active']."</cell>";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				echo 		"<cell><![CDATA[".$download['filename']."]]></cell>";
			else
				echo 		"<cell><![CDATA[".$download['physically_filename']."]]></cell>";
			echo "</row>";
		}
		
	}

	if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	 		header("Content-type: application/xhtml+xml");
	}else{
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#text_filter,#select_filter,]]></param></call>
</beforeInit>
<column id="id_product_download" width="70" type="ro" align="right" sort="str"><?php echo _l('ID pdt download')?></column>
<column id="id_product" width="40" type="ro" align="right" sort="str"><?php echo _l('id_product')?></column>
<column id="reference" width="80" type="ro" align="left" sort="str"><?php echo _l('Ref')?></column>
<column id="supplier_reference" width="80" type="ro" align="left" sort="str"><?php echo _l('Supplier Ref.')?></column>
<column id="name" width="120" type="ro" align="left" sort="str"><?php echo _l('Product')?></column>
<column id="display_filename" width="200" type="ed" align="left" sort="str"><?php echo _l('Display filename')?></column>
<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
<column id="date_add" width="80" type="ro" align="left" sort="str"><?php echo _l('Upload date')?></column>
<?php } else { ?>
<column id="date_deposit" width="80" type="ro" align="left" sort="str"><?php echo _l('Upload date')?></column>
<?php } ?>
<column id="date_expiration" width="80" type="dhxCalendarA" align="right" sort="str" format="%Y-%m-%d"><?php echo _l('Expiration date')?></column>
<column id="nb_days_accessible" width="40" type="edn" align="right" sort="int"><?php echo _l('Nb days accessible')?></column>
<column id="nb_downloadable" width="40" type="edn" align="right" sort="int"><?php echo _l('Nb of authorized downloads')?></column>
<column id="active" width="45" type="coro" align="center" sort="int"><?php echo _l('Active')?>
	<option value="0"><?php echo _l('No')?></option>
	<option value="1"><?php echo _l('Yes')?></option>
</column>
<column id="<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '<')) echo 'physically_'; ?>filename" width="120" type="ro" align="left" sort="str"><?php echo _l('Filename on the server')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_productdownload').'</userdata>'."\n";
	getDownloads();
	//echo '</rows>';
?>
</rows>