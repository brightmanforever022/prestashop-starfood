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
$id_product=intval(Tools::getValue('id_product', 0));
$id_product_download=intval(Tools::getValue('id_product_download', 0));

$error = "";
$success = false;
$uploadable = true;
$error_uploadable = array();

if(isset($_POST["submitUpload"]))
{
	if(empty($_FILES['download']['name']))
		$error = _l('You must select a file to upload.', 1);
	
	if(empty($id_product_download))
	{
		$exist = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT `id_product_download`
				FROM `'._DB_PREFIX_.'product_download`
				WHERE `id_product` = '.(int)$id_product.'
			');
		if(empty($error) && !empty($exist[0]["id_product_download"]))
			$error = _l('This product already has a downloadable file.', 1);
	}
	
	if(empty($error))
	{
		$dossier = _PS_DOWNLOAD_DIR_;
		$display_filename = basename($_FILES['download']['name']);
		$filename = ProductDownload::getNewFilename();
		if(move_uploaded_file($_FILES['download']['tmp_name'], $dossier.$filename))
		{
			if(empty($id_product_download))
			{
				$download = new ProductDownload();
				$download->id_product = $id_product;
				$download->display_filename = $display_filename;
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					$download->filename = $filename;
					$download->date_add = date('Y-m-d H:i:s');
				}
				else
				{
					$download->physically_filename = $filename;
					$download->date_deposit = date('Y-m-d H:i:s');
				}
				$download->date_expiration = null;
				$download->nb_days_accessible = null;
				$download->nb_downloadable = null;
				$download->active = 1;
				$download->save();
				
				$product = new Product($id_product);
				$product->is_virtual = 1;
				$product->save();
			}
			else
			{
				$download = new ProductDownload($id_product_download);
				
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					@unlink(_PS_DOWNLOAD_DIR_."/".$download->filename);
				else
					@unlink(_PS_DOWNLOAD_DIR_."/".$download->physically_filename);
				
				$download->display_filename = $display_filename;
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$download->filename = $filename;
				else
					$download->physically_filename = $filename;
				
				if($download->date_expiration=="0000-00-00 00:00:00")
					$download->date_expiration = null;
				
				$download->save();

				// PM Cache
				if(!empty($id_product))
					ExtensionPMCM::clearFromIdsProduct($id_product);
			}
			$success = true;
		}
		else
			$error = _l('An error occured during file upload. Please try again.', 1);
	}
}

if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	$product = new Product($id_product, false, null, (int)SCI::getSelectedShop());
else
	$product = new Product($id_product);

if($product->hasAttributes())
{
	$uploadable = false;
	$error_uploadable[] = _l('A virtual product cannot have combinations.');
}
if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
{
	if($product->advanced_stock_management)
	{
		$uploadable = false;
		$error_uploadable[] = _l('A virtual product cannot use the advanced stock management.');
	}
}

?><style type="text/css">
.btn {
	background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #a4bed4;
    color: #34404b;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
	font-weight: bold;
	cursor: pointer;
	float: right;
	margin-top: 6px;
}
</style>
<script type="text/javascript">
<?php if(!empty($error)) { ?>
parent.dhtmlx.message({text:'<?php echo $error; ?>',type:'error',expire:10000});
<?php }
if($success) { ?>
parent.displayProductDownload();
parent.prop_tb._productdownloadLayout.cells('b').collapse();
<?php } ?>
</script>
<?php if($uploadable) { ?>
<form method="POST" action="" enctype="multipart/form-data">

	Fichier : <input type="file" name="download" />
    
    <button class="btn" name="submitUpload" type="submit"><?php echo _l('Upload file');?></button>
    
    <input type="hidden" name="id_product" value="<?php echo $id_product; ?>" />

</form>
<?php } else {

	foreach($error_uploadable as $error)
	{
		echo '<strong>'.$error.'</strong><br/><br/>';
	}
	
} ?>