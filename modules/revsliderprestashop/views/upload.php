<?php
include('config/config.php');
if ($_SESSION['verify'] != 'RESPONSIVEfilemanager') die('forbiden');
include('include/utils.php');


/*revolution add*/

include_once(_PS_MODULE_DIR_.'revsliderprestashop/revprestashoploader.php');
include_once(_PS_MODULE_DIR_.'revsliderprestashop/revslider_admin.php');

/*end revolution add*/

$_POST['path'] = $current_path.$_POST['path'];
$_POST['path_thumb'] = $thumbs_base_path.$_POST['path_thumb'];

$storeFolder = $_POST['path'];
$storeFolderThumb = $_POST['path_thumb'];

$path_pos = strpos($storeFolder, $current_path);
$thumb_pos = strpos($_POST['path_thumb'], $thumbs_base_path);

if ($path_pos === false || $thumb_pos === false
	|| preg_match('/\.{1,2}[\/|\\\]/', $_POST['path_thumb']) !== 0
	|| preg_match('/\.{1,2}[\/|\\\]/', $_POST['path']) !== 0)
	die('wrong path');

$path = $storeFolder;
$cycle = true;
$max_cycles = 50;
$i = 0;
while ($cycle && $i < $max_cycles)
{
	$i++;
	if ($path == $current_path) $cycle = false;
	if (file_exists($path.'config.php'))
	{
		require_once($path.'config.php');
		$cycle = false;
	}
	$path = fix_dirname($path).'/';
}

if (!empty($_FILES))
{
	$info = pathinfo($_FILES['file']['name']);
	if (isset($info['extension']) && in_array(fix_strtolower($info['extension']), $ext))
	{
		$tempFile = $_FILES['file']['tmp_name'];

		$targetPath = $storeFolder;
		$targetPathThumb = $storeFolderThumb;
		$_FILES['file']['name'] = fix_filename($_FILES['file']['name'], $transliteration);

		$file_name_splitted = explode('.', $_FILES['file']['name']);
		array_pop($file_name_splitted);
		$_FILES['file']['name'] = implode('-', $file_name_splitted).'.'.$info['extension'];

		if (file_exists($targetPath.$_FILES['file']['name']))
		{
			$i = 1;
			$info = pathinfo($_FILES['file']['name']);
			while (file_exists($targetPath.$info['filename'].'_'.$i.'.'.$info['extension']))
			{
				$i++;
			}
			$_FILES['file']['name'] = $info['filename'].'_'.$i.'.'.$info['extension'];
		}
		$targetFile = $targetPath.$_FILES['file']['name'];
		$targetFileThumb = $targetPathThumb.$_FILES['file']['name'];






		if (in_array(fix_strtolower($info['extension']), $ext_img) && @getimagesize($tempFile) != false)
			$is_img = true;
		else
			$is_img = false;

		if ($is_img)
		{
			 
		/*revolution odl system*/

				$targetFolder = ABSPATH.'/uploads/';
        		$randnum = rand(0000000,9999999);
				$sds_time = time();
        		$NewFileName = $randnum.'-'.$sds_time;
   				$tempFile = $_FILES['file']['tmp_name'];
   				$targetPath = $targetFolder;

 // Validate the file type
                $fileTypes = array('jpg','jpeg','gif','png'); // File extensions
                $fileParts = pathinfo($_FILES['file']['name']);

                if (in_array($fileParts['extension'],$fileTypes)) {
                    $worked = UniteFunctionsWPRev::import_media_img($tempFile, $targetPath, $NewFileName.'.'.$fileParts['extension']);
                    if(!empty($worked))
                            echo '1';

                } else {
                    echo '0';
                }

 
		}
	} else
	{
		header('HTTP/1.1 406 file not permitted', true, 406);
		exit();
	}
} else
{
	header('HTTP/1.1 405 Bad Request', true, 405);
	exit();
}
if (isset($_POST['submit']))
{
	$query = http_build_query(
		array(
			'type' => $_POST['type'],
			'lang' => $_POST['lang'],
			'popup' => $_POST['popup'],
			'field_id' => $_POST['field_id'],
			'fldr' => $_POST['fldr'],
		)
	);
	header('location: dialog.php?'.$query);
}

?>      
