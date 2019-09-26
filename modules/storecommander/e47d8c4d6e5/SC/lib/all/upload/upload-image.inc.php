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

function	createSrcImage($type, $filename)
{
	switch ($type)
	{
		case 1:
			return imagecreatefromgif($filename);
			break;
		case 3:
			return imagecreatefrompng($filename);
			break;
		case 2:
		default:
			return imagecreatefromjpeg($filename);
			break;
	}
}

function returnDestImage($type, $ressource, $filename)
{
	$flag = false;
	switch ($type)
	{
		case 'gif':
			$flag = imagegif($ressource, $filename);
			break;
		case 'png':
			$flag = imagepng($ressource, $filename, _s('CAT_PROD_IMG_PNGCOMPRESS'));
			break;
		case 'jpeg':
		default:
			$flag = imagejpeg($ressource, $filename, _s('CAT_PROD_IMG_JPGCOMPRESS'));
			break;
	}
	imagedestroy($ressource);
	return $flag;
}


function img_create_from($mime, $w, $h, $from) {
	switch ($mime) {
		case "image/jpg" :
		case "image/jpeg" :
		case "image/jpe" :
		case "image/pjpeg" :
			$rsrc = @imagecreatetruecolor($w, $h);
			$img = @imagecreatefromjpeg($from);
			break;
		case "image/png" :
		case "image/x-png" :
			$rsrc = @imagecreatetruecolor($w, $h);
			$img = @imagecreatefrompng($from);
			break;
		case "image/gif" :
			$rsrc = @imagecreate($w, $h);
			$img = @imagecreatefromgif($from);
			break;
	}
	return array($rsrc, $img);
}

function img_create_to($type, $rsrc, $to) {
	$flag = false;
	switch ($type) {
		case "jpg" :
			if (_s('CAT_PROD_IMG_JPGPROGRESSIVE'))
				@imageinterlace($rsrc, 1);
			$flag = imagejpeg($rsrc, $to, _s('CAT_PROD_IMG_JPGCOMPRESS'));
			break;
		case "png" :
			$flag = imagepng($rsrc, $to, _s('CAT_PROD_IMG_PNGCOMPRESS'));
			break;
		case "gif" :
			$flag = imagegif($rsrc, $to);
			break;
	}
	return $flag;
}

function imageResize($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg')
{
	$type_sc = _s("CAT_PROD_IMAGE_GENERATION_METHOD");
	if (!file_exists($sourceFile))
		return false;
	
	list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
	
	if(!empty($type_sc) && $type_sc=="2")
	{
		if (Configuration::get('PS_IMAGE_QUALITY') == 'png_all'
				|| (Configuration::get('PS_IMAGE_QUALITY') == 'png' && $type == IMAGETYPE_PNG))
			$fileType = 'png';
	}
	
	if (!$sourceWidth)
		return false;
	
	if($type_sc!="2")
		$mime = image_type_to_mime_type($type);
	
	if ($destWidth == NULL) $destWidth = $sourceWidth;
	if ($destHeight == NULL) $destHeight = $sourceHeight;

	$sourceImage = createSrcImage($type, $sourceFile);

	$widthDiff = $destWidth / $sourceWidth;
	$heightDiff = $destHeight / $sourceHeight;
	
	if ($widthDiff > 1 AND $heightDiff > 1)
	{
		$nextWidth = $sourceWidth;
		$nextHeight = $sourceHeight;
	}
	else
	{
		if(!empty($type_sc) && $type_sc=="2")
		{
			if($sourceWidth/$sourceHeight > 1){
				$nextWidth = $destWidth;
				$nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = $nextHeight;
			}
			else{
				$nextHeight = $destHeight;
				$nextWidth  = round(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth  =  $nextWidth;
			}
		}
		elseif(!empty($type_sc) && $type_sc=="1")
		{
			if (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 2 OR (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 AND $widthDiff < $heightDiff))
			{
				$nextHeight = $destHeight;
				$nextWidth = intval(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth = (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destWidth : $nextWidth);
			}
			else
			{
				$nextWidth = $destWidth;
				$nextHeight = intval($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destHeight : $nextHeight);
			}
		}
		else
		{
			if (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 2 OR (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 AND $widthDiff > $heightDiff))
			{
				$nextHeight = $destHeight;
				$nextWidth = intval(($sourceWidth * $nextHeight) / $sourceHeight);
				$destWidth = (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destWidth : $nextWidth);
			}
			else
			{
				$nextWidth = $destWidth;
				$nextHeight = intval($sourceHeight * $destWidth / $sourceWidth);
				$destHeight = (intval(Configuration::get('PS_IMAGE_GENERATION_METHOD')) == 0 ? $destHeight : $nextHeight);
			}
		}
	}
	
	if(!empty($type_sc) && $type_sc=="2")
	{
		$destImage = imagecreatetruecolor($destWidth, $destHeight);
		
		// If image is a PNG and the output is PNG, fill with transparency. Else fill with white background.
		if ($fileType == 'png' && $type == IMAGETYPE_PNG)
		{
			imagealphablending($destImage, false);
			imagesavealpha($destImage, true);
			$transparent = imagecolorallocatealpha($destImage, 255, 255, 255, 127);
			imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $transparent);
		}else
		{
			$white = imagecolorallocate($destImage, 255, 255, 255);
			imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $white);
		}
		
		imagecopyresampled($destImage, $sourceImage, (int)(($destWidth - $nextWidth) / 2), (int)(($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);

        $final_img = (returnDestImage($fileType, $destImage, $destFile));

        $tinypng = _s('CAT_PROD_IMG_TINYPNG');
        if(!empty($tinypng))
        {
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Exception.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/ResultMeta.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Result.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Source.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Client.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify.php");

            try {
                \Tinify\setKey($tinypng);
                \Tinify\validate();
                $source = \Tinify\fromFile($destFile);
                $preservedMeta = $source->preserve("copyright", "creation", "location");
                $preservedMeta->toFile($destFile);
            } catch (Exception $e) {
                return $final_img;
            }
        }

		return $final_img;
	}
	else
	{
		$borderWidth = intval(($destWidth - $nextWidth) / 2);
		$borderHeight = intval(($destHeight - $nextHeight) / 2);
	
	/*	
		$destImage = imagecreatetruecolor($destWidth, $destHeight);
	
		$bgcolor_settings=explode(',',_s('CAT_PROD_IMG_RESIZE_BGCOLOR'));
		if (count($bgcolor_settings)!=3)
			$bgcolor_settings=array(255,255,255);
		$bgcolor = imagecolorallocate($destImage, $bgcolor_settings[0], $bgcolor_settings[1], $bgcolor_settings[2]);
	//	imagefill($destImage, 0, 0, $white); 
		imagefilledrectangle($destImage, 0, 0, $destWidth, $destHeight, $bgcolor);
	
		imagecopyresampled($destImage, $sourceImage, $borderWidth, $borderHeight, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
		imagecolortransparent($destImage, $bgcolor);
		return (returnDestImage($fileType, $destImage, $destFile));
	*/
	
		list($rsrc, $img) = img_create_from(($fileType=='png'?'image/png':$mime), $destWidth, $destHeight, $sourceFile);
	
	
		$bgcolor_settings=explode(',',_s('CAT_PROD_IMG_RESIZE_BGCOLOR'));
		if (count($bgcolor_settings)!=3)
			$bgcolor_settings=array(255,255,255);
		$bgcolor = imagecolorallocate($rsrc, $bgcolor_settings[0], $bgcolor_settings[1], $bgcolor_settings[2]);
		
	
		switch ($fileType) {
			case "png" :
			case "gif" :
			
				$t_indx = imagecolortransparent($img);
				if ($t_indx >= 0) {
					$t_color = imagecolorsforindex($img, $t_indx);
					$t_indx = imagecolorallocate($rsrc, $t_color['red'], $t_color['green'], $t_color['blue']);
					imagefill($rsrc, 0, 0, $t_indx);
					imagecolortransparent($rsrc, $t_indx);
				} else if ($fileType == "png") {
					imagealphablending($rsrc, false);
					$color = imagecolorallocatealpha($rsrc, 0, 0, 0, 127);
					imagefill($rsrc, 0, 0, $color);
					imagesavealpha($rsrc, true);
				}
	
	/*		
				$t_indx = imagecolortransparent($img);
				if ($fileType == "png")
				{
					imagealphablending($rsrc, false);
					$color = imagecolorallocatealpha($rsrc, 0, 0, 0, 127);
	//				imagefilledrectangle($rsrc, 0, 0, $destWidth, $destHeight, $bgcolor);
					imagefill($rsrc, 0, 0, $color);
					imagesavealpha($rsrc, true);
				}
				if ($t_indx >= 0) {
					$t_color = imagecolorsforindex($img, $t_indx);
					$t_indx = imagecolorallocate($rsrc, $t_color['red'], $t_color['green'], $t_color['blue']);
					imagefilledrectangle($rsrc, 0, 0, $destWidth, $destHeight, $bgcolor);
	//				imagefill($rsrc, 0, 0, $t_indx);
					imagecolortransparent($rsrc, $t_indx);
				}*/
				break;
			default :
				$white = imagecolorallocate($rsrc, 255, 255, 255);
				imagefilledrectangle($rsrc, 0, 0, $destWidth, $destHeight, $bgcolor);
	//			imagefill($rsrc, 0, 0, $white);
		}
	
		@imagecopyresampled($rsrc, $img, $borderWidth, $borderHeight, 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
        $final_img = img_create_to($fileType, $rsrc, $destFile);
		@imagedestroy($rsrc);
		@imagedestroy($img);

        $tinypng = _s('CAT_PROD_IMG_TINYPNG');
        if(!empty($tinypng))
        {
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Exception.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/ResultMeta.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Result.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Source.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify/Client.php");
            require_once(SC_DIR."lib/php/tinypng/lib/Tinify.php");

            try {
                \Tinify\setKey($tinypng);
                \Tinify\validate();
                $source = \Tinify\fromFile($destFile);
                $preservedMeta = $source->preserve("copyright", "creation", "location");
                $preservedMeta->toFile($destFile);
            } catch (Exception $e) {
                return $final_img;
            }
        }

		return $final_img;
	}
}

