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
	 * ajax preview
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	include_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");	
	if (!empty($_GET['path']) && file_exists($_GET['path']) && is_file($_GET['path']))
	{
		include_once(CLASS_IMAGE);
		$image = new Image(true);
		if ($image->loadImage($_GET['path']))
		{
			if ($image->resize(CONFIG_IMG_THUMBNAIL_MAX_X, CONFIG_IMG_THUMBNAIL_MAX_Y, true, true))
			{
				$image->showImage();
			}else 
			{
				echo PREVIEW_NOT_PREVIEW . ".";	
			}
		}else 
		{
			echo PREVIEW_NOT_PREVIEW . "..";			
		}

			
	}else 
	{
		echo PREVIEW_NOT_PREVIEW . "...";
	}

