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
$ids=(Tools::getValue('ids', 0));
$idlist = explode(",",$ids);

require_once(dirname(__FILE__).'/../../../all/upload/upload-image.inc.php');

$error = "";
$success = false;
$uploadable = true;
$error_uploadable = array();

if(isset($_POST["submitUpload"]))
{
	if(empty($_FILES['download']['name']))
		$error = _l('You must select a file to upload.', 1);
	
	$extensions = array('.png', '.gif', '.jpg', '.jpeg');
	$extension = strrchr(strtolower($_FILES['download']['name']), '.');
	if(!empty($_FILES['download']['name']) && !sc_in_array($extension, $extensions,"catWinCatImgUpload_extensions"))
		$error = _l('You must upload an image using format png, gif, jpg or jpeg');

	$generate_hight_dpi_images = (bool)SCI::getConfigurationValue('PS_HIGHT_DPI');
	
	if(empty($error))
	{
		$dossier = _PS_CAT_IMG_DIR_;
		$filename = uniqid();
		
		if(move_uploaded_file($_FILES['download']['tmp_name'], $dossier.$filename))
		{
			$temp_name = $dossier.$filename;
		
			foreach ($idlist AS $id_category)
			{
				$image_name = _PS_CAT_IMG_DIR_.(int)$id_category.'.jpg';
				
				@unlink($image_name);
				if(copy ( $temp_name , $image_name ))
				{
					$images_types = ImageType::getImagesTypes('categories');
					foreach ($images_types as $k => $image_type)
					{
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							ImageManager::resize(
								$image_name,
								_PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'.jpg',
								(int)$image_type['width'], (int)$image_type['height']
							);

							if($generate_hight_dpi_images)
								ImageManager::resize(
									$image_name,
									_PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'2x.jpg',
									(int)$image_type['width']*2, (int)$image_type['height']*2
								);
						}
						else
							imageResize($image_name, _PS_CAT_IMG_DIR_.$id_category.'-'.stripslashes($image_type['name']).'.jpg', (int)($image_type['width']), (int)($image_type['height']));
					}

					$success = true;
				}
			}
			
			@unlink($temp_name);
		}
		else
			$error = _l('An error occured during file upload. Please try again.', 1);
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
parent.getCatManagementPropImage();
parent.cat_prop_image.cells('b').collapse();
<?php } ?>
</script>
<?php if($uploadable) { ?>
<form method="POST" action="" enctype="multipart/form-data">

	Fichier : <input type="file" name="download" />
    
    <button class="btn" name="submitUpload" type="submit"><?php echo _l('Upload file');?></button>
    
    <input type="hidden" name="ids" value="<?php echo $ids; ?>" />

</form>
<?php } else {
	
	$error_uploadable[] = _l('All selected categories already have an image.');
	foreach($error_uploadable as $error)
	{
		echo '<strong>'.$error.'</strong><br/><br/>';
	}
	
} ?>