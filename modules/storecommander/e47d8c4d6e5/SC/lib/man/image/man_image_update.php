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
	$id_manufacturer=intval(Tools::getValue('id_product',0));
	$action=Tools::getValue('action',0);
	$manufacturer_ids = explode(',',$id_manufacturer);

	foreach($manufacturer_ids as $id_manufacturer)
	{
		switch ($action) {
			case 'delete':
				$images_types = ImageType::getImagesTypes('manufacturers');
				foreach ($images_types as $k => $image_type) {
					@unlink(_PS_MANU_IMG_DIR_ . $id_manufacturer . '.jpg');
					@unlink(_PS_MANU_IMG_DIR_ . $id_manufacturer . '-' . stripslashes($image_type['name']) . '.jpg');
					if ($generate_hight_dpi_images) {
						@unlink(_PS_MANU_IMG_DIR_ . $id_manufacturer . '-' . stripslashes($image_type['name']) . '2x.jpg');
					}
				}
				break;
		}

	}
