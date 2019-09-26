<?php

// include_once('../../../config/config.inc.php');
// include_once('../../../init.php');

// include_once('revsliderprestashop.php');

include_once('config/config.php');
$iso = '';
if(!defined('ABSPATH'))
   define('ABSPATH', dirname(__FILE__));

$up_media_url = _MODULE_DIR_."revsliderprestashop/views/";
$plugins_url = _MODULE_DIR_."revsliderprestashop/";
/*
$views_urls = admin_url('?view=dialog');
$upload_urls = admin_url('?view=upload');*/
$views_urls = controller_upload_url('&view=dialog');
$upload_urls = controller_upload_url('&view=upload');
$ajax_calls_url = $up_media_url ."ajax_calls.php";

$hash = Tools::encrypt(GlobalsRevSlider::MODULE_NAME);

$tablename =  _DB_PREFIX_. 'revslider_attachment_images';

$url = uploads_url();


 

// $db_results =  Db::getInstance()->executeS("SELECT * FROM {$tablename}");
$results = array();
	// foreach($db_results as $dres){    
	// 	$results[$dres['ID']] = $dres['file_name'];
	// }
$results =  UniteBaseAdminClassRev::get_uploaded_files_result();

$_SESSION["verify"] = "RESPONSIVEfilemanager";

if (isset($_POST['submit']))
	include($upload_urls);
else
{

	include('include/utils.php');

	if (isset($_GET['fldr'])
		&& !empty($_GET['fldr'])
		&& preg_match('/\.{1,2}[\/|\\\]/', urldecode($_GET['fldr'])) === 0
	)
		$subdir = urldecode(trim($_GET['fldr'], '/').'/');
	else
		$subdir = '';


	setcookie('last_position', $subdir, time() + (86400 * 7));

	if ($subdir == '')
	{
		if (!empty($_COOKIE['last_position'])
			&& strpos($_COOKIE['last_position'], '.') === false
		)
			$subdir = trim($_COOKIE['last_position']);
	}

	if ($subdir == '/')
		$subdir = '';



	if (!isset($_SESSION['subfolder'])) $_SESSION['subfolder'] = '';
	$subfolder = '';
	if (!empty($_SESSION['subfolder']) && strpos($_SESSION['subfolder'], '../') === false
		&& strpos($_SESSION['subfolder'], './') === false && strpos($_SESSION['subfolder'], '/') !== 0
		&& strpos($_SESSION['subfolder'], '.') === false
	) $subfolder = $_SESSION['subfolder'];

	if ($subfolder != '' && $subfolder[strlen($subfolder) - 1] != '/') $subfolder .= '/';

	if (!file_exists($current_path.$subfolder.$subdir))
	{
		$subdir = '';
		if (!file_exists($current_path.$subfolder.$subdir))
			$subfolder = '';
	}

	if (trim($subfolder) == '')
	{
		$cur_dir = $upload_dir.$subdir;
		$cur_path = $current_path.$subdir;
		$thumbs_path = $thumbs_base_path;
		$parent = $subdir;
	} else
	{
		$cur_dir = $upload_dir.$subfolder.$subdir;
		$cur_path = $current_path.$subfolder.$subdir;
		$thumbs_path = $thumbs_base_path.$subfolder;
		$parent = $subfolder.$subdir;
	}

	$cycle = true;
	$max_cycles = 50;
	$i = 0;
	while ($cycle && $i < $max_cycles)
	{
		$i++;
		if ($parent == './') $parent = '';
		if (file_exists($current_path.$parent.'config.php'))
		{
			require_once($current_path.$parent.'config.php');
			$cycle = false;
		}

		if ($parent == '') $cycle = false;
		else $parent = fix_dirname($parent).'/';
	}

	if (!is_dir($thumbs_path.$subdir))
	{
		create_folder(false, $thumbs_path.$subdir);
	}

	if (isset($_GET['popup'])) $popup = $_GET['popup']; else $popup = 0;
//Sanitize popup
	$popup = !!$popup;

//view type
	if (!isset($_SESSION['view_type']))
	{
		$view = $default_view;
		$_SESSION['view_type'] = $view;
	}
	if (isset($_GET['view']))
	{
		$view = $_GET['view'];
		$_SESSION['view_type'] = $view;
	}
	$view = $_SESSION['view_type'];

	if (isset($_GET['filter'])) $filter = fix_filename($_GET['filter'], $transliteration);
	else $filter = '';

	if (!isset($_SESSION['sort_by'])) $_SESSION['sort_by'] = '';
	if (isset($_GET['sort_by'])) $sort_by = $_SESSION['sort_by'] = fix_filename($_GET['sort_by'], $transliteration);
	else $sort_by = $_SESSION['sort_by'];

	if (!isset($_SESSION['descending'])) $_SESSION['descending'] = false;
	if (isset($_GET['descending'])) $descending = $_SESSION['descending'] = fix_filename($_GET['descending'], $transliteration) === 'true';
	else $descending = $_SESSION['descending'];


	$lang = $default_language;
	if (isset($_GET['lang']) && $_GET['lang'] != 'undefined' && $_GET['lang'] != '')
		$lang = $_GET['lang'];

	$language_file = 'lang/'.$default_language.'.php';
	if ($lang != $default_language)
	{
		$path_parts = pathinfo($lang);
		if (is_readable('lang/'.$path_parts['basename'].'.php'))
			$language_file = 'lang/'.$path_parts['basename'].'.php';
		else
			$lang = $default_language;
	}


	require_once $language_file;

	if (!isset($_GET['type'])) $_GET['type'] = 0;
	if (!isset($_GET['field_id'])) $_GET['field_id'] = '';

	$get_params = http_build_query(
		array(
			'type' => Tools::safeOutput($_GET['type']),
			'lang' => Tools::safeOutput($lang),
			'popup' => $popup,
			'field_id' => isset($_GET['field_id']) ? (int)$_GET['field_id'] : '',
			'fldr' => ''
		)
	);
	$sds_admin_url = admin_url();
	?>
<script type="text/javascript">
	var g_urlContent = "<?php echo UniteFunctionsWPRev::getUrlContent()?>";
	var g_uniteDirPlagin = "revsliderprestashop";
	var ajaxurl = "<?php echo Context::getContext()->link->getAdminLink('Revolutionslider_ajax'); ?>"
	ajaxurl += '&returnurl=<?php echo urlencode(htmlspecialchars_decode($sds_admin_url))?>';
</script>
		<link rel="shortcut icon" href="img/ico/favicon.ico">
		<link href="<?php echo $up_media_url; ?>css/bootstrap.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo $up_media_url; ?>css/bootstrap-responsive.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo $up_media_url; ?>css/bootstrap-lightbox.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo $up_media_url; ?>css/style.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo $up_media_url; ?>css/dropzone.min.css" type="text/css" rel="stylesheet"/>
		<link href="<?php echo $up_media_url; ?>css/jquery.contextMenu.min.css" rel="stylesheet" type="text/css"/>
		<link href="<?php echo $up_media_url; ?>css/bootstrap-modal.min.css" rel="stylesheet" type="text/css"/>
		 
		<!--[if lt IE 8]>
		<style>
			.img-container span, .img-container-mini span {
				display: inline-block;
				height: 100%;
			}
		</style><![endif]-->
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
		<script type="text/javascript">
			if (typeof jQuery === 'undefined') {
				document.write(unescape("%3Cscript src='js/jquery.js' type='text/javascript'%3E%3C/script%3E"));
			}
		</script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/bootstrap-lightbox.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/dropzone.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/jquery.touchSwipe.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/modernizr.custom.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/bootbox.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/bootstrap-modal.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/bootstrap-modalmanager.min.js"></script>
	 
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/imagesloaded.pkgd.min.js"></script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/jquery.queryloader2.min.js"></script>
		<script type="text/javascript" src="<?php echo _MODULE_DIR_."revsliderprestashop"; ?>/js/jquery-ui/jquery-ui-1.10.3.custom.js"></script>
<script type="text/javascript" src="<?php echo _MODULE_DIR_."revsliderprestashop"; ?>/js/admin.js"></script>
<style>
#main,.page-sidebar #content
{
padding:0;
margin:0;
}
.modal-scrollable,.modal-backdrop,#footer,.bootstrap.panel,.bootstrap .page-head,#footer,#header,#nav-sidebar,#top_container #header,#top_container #footer,#top_container #content > .table
{
display: none;
}
#top_container #main
{
   min-height: 0;
   height: auto;
}
#image_size{
	font: 400 12px/1.42857 "Open Sans",Helvetica,Arial,sans-serif;
}
/*.ls-wrap .row-fluid .span9
{
    width:58%;
}*/
/*#selectable
{
    list-style: none;
    position: relative;
    padding: 0; 
}
#selectable li 
{                
    float: left;
    position: relative;
    width: 150px;
    margin-left: 2.5641%;
    min-height: 30px;
    margin-bottom: 2.5641%;
    -moz-box-shadow:    inset 1px 1px 10px #cccccc;
    -webkit-box-shadow: inset 1px 1px 10px #cccccc;
    box-shadow:         inset 1px 1px 10px #cccccc;
}
#selectable li a
{
    display: block;
    border: 5px solid transparent;               
}
#selectable  li.ui-selected  a
{
    border-color: #0084FF;                
}*/
</style>
		<?php
		if ($aviary_active)
		{
			if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443)
			{
				?>
				<script type="text/javascript" src="https://dme0ih8comzn4.cloudfront.net/js/feather.js"></script>
			<?php }else{ ?>
				<script type="text/javascript" src="http://feather.aviary.com/js/feather.js "></script>
			<?php
			}
		} ?>

		<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
		<!--[if lt IE 9]>
		<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.6.2/html5shiv.js"></script>
		<![endif]-->
		<script src="<?php echo $up_media_url; ?>js/jquery.ui.position.min.js" type="text/javascript"></script>
		<script src="<?php echo $up_media_url; ?>js/jquery.contextMenu.min.js" type="text/javascript"></script>

		<script>
			var ext_img = new Array('<?php echo implode("','", $ext_img)?>');
			var allowed_ext = new Array('<?php echo implode("','", $ext)?>');
			var loading_bar =<?php echo $loading_bar?"true":"false"; ?>;
			var image_editor =<?php echo $aviary_active?"true":"false"; ?>;
			//dropzone config
			Dropzone.options.myAwesomeDropzone = {
				dictInvalidFileType: "<?php echo lang_Error_extension;?>",
				dictFileTooBig: "<?php echo lang_Error_Upload; ?>",
				dictResponseError: "SERVER ERROR",
				paramName: "file", // The name that will be used to transfer the file
				maxFilesize: <?php echo $MaxSizeUpload; ?>, // MB
				url: "<?php echo $upload_urls; ?>",
				accept: function (file, done) {
					var extension = file.name.split('.').pop();
					extension = extension.toLowerCase();
					if ($.inArray(extension, allowed_ext) > -1) {
						done();
					}
					else {
						done("<?php echo lang_Error_extension;?>");
					}
				}
			};
			if (image_editor) {
				var featherEditor = new Aviary.Feather({
					apiKey: "<?php echo $aviary_key; ?>",
					apiVersion: <?php echo $aviary_version; ?>,
					language: "<?php echo $aviary_language; ?>",
					theme: 'light',
					tools: 'all',
					onSave: function (imageID, newURL) {
						show_animation();
						var img = document.getElementById(imageID);
						img.src = newURL;
						$.ajax({
							type: "POST",
							url: "<?php echo $ajax_calls_url;  ?>?action=save_img",
							data: { url: newURL, path: $('#sub_folder').val() + $('#fldr_value').val(), name: $('#aviary_img').data('name') }
						}).done(function (msg) {
							featherEditor.close();
							d = new Date();
							$("figure[data-name='" + $('#aviary_img').data('name') + "']").find('img').each(function () {
								$(this).attr('src', $(this).attr('src') + "?" + d.getTime());
							});
							$("figure[data-name='" + $('#aviary_img').data('name') + "']").find('figcaption a.preview').each(function () {
								$(this).data('url', $(this).data('url') + "?" + d.getTime());
							});
							hide_animation();
						});
						return false;
					},
					onError: function (errorObj) {
						bootbox.alert(errorObj.message);
					}

				});
			}
		</script>
<script>
var ajax_calls_url = "<?php $ajax_calls_url; ?>";
</script>
		<script type="text/javascript" src="<?php echo $up_media_url; ?>js/include.min.js"></script>

	<input type="hidden" id="popup" value="<?php echo Tools::safeOutput($popup); ?>"/>
	<input type="hidden" id="view" value="<?php echo Tools::safeOutput($view); ?>"/>
	<input type="hidden" id="cur_dir" value="<?php echo Tools::safeOutput($cur_dir); ?>"/>
	<input type="hidden" id="cur_dir_thumb" value="<?php echo Tools::safeOutput($subdir); ?>"/>
	<input type="hidden" id="insert_folder_name" value="<?php echo Tools::safeOutput(lang_Insert_Folder_Name); ?>"/>
	<input type="hidden" id="new_folder" value="<?php echo Tools::safeOutput(lang_New_Folder); ?>"/>
	<input type="hidden" id="ok" value="<?php echo Tools::safeOutput(lang_OK); ?>"/>
	<input type="hidden" id="cancel" value="<?php echo Tools::safeOutput(lang_Cancel); ?>"/>
	<input type="hidden" id="rename" value="<?php echo Tools::safeOutput(lang_Rename); ?>"/>
	<input type="hidden" id="lang_duplicate" value="<?php echo Tools::safeOutput(lang_Duplicate); ?>"/>
	<input type="hidden" id="duplicate" value="<?php if ($duplicate_files) echo 1; else echo 0; ?>"/>
	<input type="hidden" id="base_url" value="<?php echo Tools::safeOutput($base_url) ?>"/>
	<input type="hidden" id="base_url_true" value="<?php echo base_url(); ?>"/>
	<input type="hidden" id="fldr_value" value="<?php echo Tools::safeOutput($subdir); ?>"/>
	<input type="hidden" id="sub_folder" value="<?php echo Tools::safeOutput($subfolder); ?>"/>
	<input type="hidden" id="file_number_limit_js" value="<?php echo Tools::safeOutput($file_number_limit_js); ?>"/>
	<input type="hidden" id="descending" value="<?php echo $descending ? "true" : "false"; ?>"/>
	<?php $protocol = 'http'; ?>
	<input type="hidden" id="current_url" value="<?php echo str_replace(array('&filter='.$filter), array(''), $protocol."://".$_SERVER['HTTP_HOST'].Tools::safeOutput($_SERVER['REQUEST_URI'])); ?>"/>
	<input type="hidden" id="lang_show_url" value="<?php echo Tools::safeOutput(lang_Show_url); ?>"/>
	<input type="hidden" id="lang_extract" value="<?php echo Tools::safeOutput(lang_Extract); ?>"/>
	<input type="hidden" id="lang_file_info" value="<?php echo fix_strtoupper(lang_File_info); ?>"/>
	<input type="hidden" id="lang_edit_image" value="<?php echo Tools::safeOutput(lang_Edit_image); ?>"/>
	<input type="hidden" id="transliteration" value="<?php echo $transliteration ? "true" : "false"; ?>"/>
	<?php if ($upload_files)
	{
		?>
		

		<div class="uploader">
			<center>
				<button class="btn btn-inverse close-uploader">
					<i class="icon-backward icon-white"></i> <?php echo Tools::safeOutput(lang_Return_Files_List) ?></button>
			</center>
			<div class="space10"></div>
			<div class="space10"></div>
			<?php if ($java_upload){ ?>
			<div class="tabbable upload-tabbable"> <!-- Only required for left/right tabs -->
				<ul class="nav nav-tabs">
					<li class="active"><a href="#tab1" data-toggle="tab"><?php echo Tools::safeOutput(lang_Upload_base); ?></a></li>
					<li><a href="#tab2" id="uploader-btn" data-toggle="tab"><?php echo Tools::safeOutput(lang_Upload_java); ?></a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="tab1">
						<?php } ?>
						<form action="<?php echo $views_urls; ?>" method="post" enctype="multipart/form-data" id="myAwesomeDropzone" class="dropzone">
							<input type="hidden" name="path" value="<?php echo Tools::safeOutput($subfolder.$subdir); ?>"/>
							<input type="hidden" name="path_thumb" value="<?php echo Tools::safeOutput($subfolder.$subdir); ?>"/>

							<div class="fallback">
								<?php echo lang_Upload_file ?>:<br/>
								<input name="file" type="file"/>
								<input type="hidden" name="fldr" value="<?php echo Tools::safeOutput($subdir); ?>"/>
								<input type="hidden" name="view" value="<?php echo Tools::safeOutput($view); ?>"/>
								<input type="hidden" name="type" value="<?php echo Tools::safeOutput($_GET['type']); ?>"/>
								<input type="hidden" name="field_id" value="<?php echo (int)$_GET['field_id']; ?>"/>
								<input type="hidden" name="popup" value="<?php echo Tools::safeOutput($popup); ?>"/>
								<input type="hidden" name="lang" value="<?php echo Tools::safeOutput($lang); ?>"/>
								<input type="hidden" name="filter" value="<?php echo Tools::safeOutput($filter); ?>"/>
								<input type="submit" name="submit" value="<?php echo lang_OK ?>"/>
							</div>
						</form>
						<div class="upload-help"><?php echo Tools::safeOutput(lang_Upload_base_help); ?></div>
						<?php if ($java_upload){ ?>
					</div>
					<div class="tab-pane" id="tab2">
						<div id="iframe-container">

						</div>
						<div class="upload-help"><?php echo Tools::safeOutput(lang_Upload_java_help); ?></div>
					</div>
					<?php } ?>
				</div>
			</div>

		</div>
		
	<?php } ?>
	<div class="container-fluid">

	<?php

	$class_ext = '';
	$src = '';

	if ($_GET['type'] == 1) $apply = 'apply_img';
	elseif ($_GET['type'] == 2) $apply = 'apply_link';
	elseif ($_GET['type'] == 0 && $_GET['field_id'] == '') $apply = 'apply_none';
	elseif ($_GET['type'] == 3) $apply = 'apply_video';
	else $apply = 'apply';

	$files = scandir($current_path.$subfolder.$subdir);
	$n_files = count($files);

	//php sorting
	$sorted = array();
	$current_folder = array();
	$prev_folder = array();
	foreach ($files as $k => $file)
	{
		if ($file == ".") $current_folder = array('file' => $file);
		elseif ($file == "..") $prev_folder = array('file' => $file);
		elseif (is_dir($current_path.$subfolder.$subdir.$file))
		{
			$date = filemtime($current_path.$subfolder.$subdir.$file);
			$size = foldersize($current_path.$subfolder.$subdir.$file);
			$file_ext = lang_Type_dir;
			$sorted[$k] = array('file' => $file, 'date' => $date, 'size' => $size, 'extension' => $file_ext);
		} else
		{
			$file_path = $current_path.$subfolder.$subdir.$file;
			$date = filemtime($file_path);
			$size = filesize($file_path);
			$file_ext = substr(strrchr($file, '.'), 1);
			$sorted[$k] = array('file' => $file, 'date' => $date, 'size' => $size, 'extension' => $file_ext);
		}
	}

	function filenameSort($x, $y)
	{
		return $x['file'] < $y['file'];
	}

	function dateSort($x, $y)
	{
		return $x['date'] < $y['date'];
	}

	function sizeSort($x, $y)
	{
		return $x['size'] - $y['size'];
	}

	function extensionSort($x, $y)
	{
		return $x['extension'] < $y['extension'];
	}

	switch ($sort_by)
	{
		case 'name':
			usort($sorted, 'filenameSort');
			break;
		case 'date':
			usort($sorted, 'dateSort');
			break;
		case 'size':
			usort($sorted, 'sizeSort');
			break;
		case 'extension':
			usort($sorted, 'extensionSort');
			break;
		default:
			break;

	}

	if ($descending)
	{
		$sorted = array_reverse($sorted);
	}

	$files = array_merge(array($prev_folder), array($current_folder), $sorted);
	?>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container-fluid">
				<button type="button" class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</button>
				<div class="brand"><?php echo Tools::safeOutput(lang_Toolbar); ?> -></div>
				<div class="nav-collapse collapse">
					<div class="filters">
						<div class="row-fluid">
							<div class="span3 half">
								<span><?php echo Tools::safeOutput(lang_Actions); ?>:</span>
								<?php if ($upload_files)
								{
									?>
									<button class="tip btn upload-btn" title="<?php echo Tools::safeOutput(lang_Upload_file); ?>">
										<i class="icon-plus"></i><i class="icon-file"></i></button>
								<?php } ?>
								<?php if ($create_folders)
								{
									?>
									<button class="tip btn new-folder" title="<?php echo Tools::safeOutput(lang_New_Folder) ?>">
										<i class="icon-plus"></i><i class="icon-folder-open"></i></button>
								<?php } ?>
							</div>
							<div class="span3 half view-controller">
								<span><?php echo lang_View; ?>:</span>
								<button class="btn tip<?php if ($view == 0) echo " btn-inverse"; ?>" id="view0" data-value="0" title="<?php echo Tools::safeOutput(lang_View_boxes); ?>">
									<i class="icon-th <?php if ($view == 0) echo "icon-white"; ?>"></i></button>
								<button class="btn tip<?php if ($view == 1) echo " btn-inverse"; ?>" id="view1" data-value="1" title="<?php echo Tools::safeOutput(lang_View_list); ?>">
									<i class="icon-align-justify <?php if ($view == 1) echo "icon-white"; ?>"></i>
								</button>
								<button class="btn tip<?php if ($view == 2) echo " btn-inverse"; ?>" id="view2" data-value="2" title="<?php echo Tools::safeOutput(lang_View_columns_list); ?>">
									<i class="icon-fire <?php if ($view == 2) echo "icon-white"; ?>"></i></button>
							</div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



	<div class="row-fluid">
		<?php
		// $link = $up_media_url."dialog.php?".$get_params;
		$link = $views_urls;
		?>
		<ul class="breadcrumb">
			<li class="pull-left"><a href="<?php echo Tools::safeOutput($link); ?>"><i class="icon-home"></i></a></li>
			<li><span class="divider">/</span></li>
			<?php
			$bc = explode("/", $subdir);
			$tmp_path = '';
			if (!empty($bc))
				foreach ($bc as $k => $b)
				{
					$tmp_path .= $b."/";
					if ($k == count($bc) - 2)
					{
						?>
						<li class="active"><?php echo Tools::safeOutput($b) ?></li><?php
					} elseif ($b != "")
					{
						?>
						<li><a href="<?php echo Tools::safeOutput($link.$tmp_path)?>"><?php echo Tools::safeOutput($b) ?></a></li>
						<li><span class="divider"><?php echo "/"; ?></span></li>
					<?php
					}
				}
			?>
			<li class="pull-right">
				<a id="refresh" class="btn-small" href="<?php echo Tools::safeOutput($link); ?>"><i class="icon-refresh"></i></a>
			</li>
<?php // echo "dialog.php?".Tools::safeOutput($get_params.$subdir."&".uniqid()) ?>
			<li class="pull-right">
				<div class="btn-group">
					<a class="btn dropdown-toggle sorting-btn" data-toggle="dropdown" href="#">
						<i class="icon-signal"></i>
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu pull-left sorting">
						<li>
							<center><strong><?php echo Tools::safeOutput(lang_Sorting) ?></strong></center>
						</li>
						<li><a class="sorter sort-name <?php if ($sort_by == "name")
							{
								echo ($descending) ? "descending" : "ascending";
							} ?>" href="javascript:void('')" data-sort="name"><?php echo Tools::safeOutput(lang_Filename); ?></a></li>
						<li><a class="sorter sort-date <?php if ($sort_by == "date")
							{
								echo ($descending) ? "descending" : "ascending";
							} ?>" href="javascript:void('')" data-sort="date"><?php echo Tools::safeOutput(lang_Date); ?></a></li>
						<li><a class="sorter sort-size <?php if ($sort_by == "size")
							{
								echo ($descending) ? "descending" : "ascending";
							} ?>" href="javascript:void('')" data-sort="size"><?php echo Tools::safeOutput(lang_Size); ?></a></li>
						<li><a class="sorter sort-extension <?php if ($sort_by == "extension")
							{
								echo ($descending) ? "descending" : "ascending";
							} ?>" href="javascript:void('')" data-sort="extension"><?php echo Tools::safeOutput(lang_Type); ?></a></li>
					</ul>
				</div>
			</li>
		</ul>
	</div>

	<div class="row-fluid ff-container">
	<div class="span9" >
	<?php if (@opendir($current_path.$subfolder.$subdir) === false){ ?>
		<br/>
		<div class="alert alert-error">There is an error! The upload folder there isn't. Check your config.php file.
		</div>
	<?php }else{ ?>
	<h4 id="help"><?php echo Tools::safeOutput(lang_Swipe_help); ?></h4>
	<?php if (isset($folder_message))
	{
		?>
		<div class="alert alert-block"><?php echo Tools::safeOutput($folder_message); ?></div>
	<?php } ?>
	<?php if ($show_sorting_bar)
	{
		?>
		<!-- sorter -->
		<div class="sorter-container <?php echo "list-view".Tools::safeOutput($view); ?>">
			<div class="file-name"><a class="sorter sort-name <?php if ($sort_by == "name")
				{
					echo ($descending) ? "descending" : "ascending";
				} ?>" href="javascript:void('')" data-sort="name"><?php echo Tools::safeOutput(lang_Filename); ?></a></div>
			<div class="file-date"><a class="sorter sort-date <?php if ($sort_by == "date")
				{
					echo ($descending) ? "descending" : "ascending";
				} ?>" href="javascript:void('')" data-sort="date"><?php echo Tools::safeOutput(lang_Date); ?></a></div>
			<div class="file-size"><a class="sorter sort-size <?php if ($sort_by == "size")
				{
					echo ($descending) ? "descending" : "ascending";
				} ?>" href="javascript:void('')" data-sort="size"><?php echo Tools::safeOutput(lang_Size); ?></a></div>
			<div class='img-dimension'><?php echo Tools::safeOutput(lang_Dimension); ?></div>
			<div class='file-extension'><a class="sorter sort-extension <?php if ($sort_by == "extension")
				{
					echo ($descending) ? "descending" : "ascending";
				} ?>" href="javascript:void('')" data-sort="extension"><?php echo Tools::safeOutput(lang_Type); ?></a></div>
			<div class='file-operations'><?php echo Tools::safeOutput(lang_Operations); ?></div>
		</div>
	<?php } ?>

	<input type="hidden" id="file_number" value="<?php echo Tools::safeOutput($n_files); ?>"/>
	<!--ul class="thumbnails ff-items"-->
	<ul class="grid cs-style-2 <?php echo "list-view".Tools::safeOutput($view); ?>">
 
<?php
if (!empty($results)):
echo UniteBaseAdminClassRev::get_uploaded_files_markup($results); 
endif; 
?>

 
	</ul>
	<?php } ?>
	</div>

	<div class="span3">

                <div class="well">

                    <div id="imgContainer" class="clearfix">

                  </div>

                    <div class="btn-group">

                        <select id="image_size">

                            <option value="">Full</option>

                            <option value="thumbnail">Thumbnail</option>

                            <option value="medium">Medium</option>

                            <option value="large">Large</option>

                        </select>

                    </div>

                    <div class="btn-group">

                        <button id="txtImageInsert" class="btn" value="3471374_8134193_star.png">Insert</button>

                        <button id="txtImageDelete" class="btn" value="3471374_8134193_star.png">Delete</button>

                    </div>

                </div>

            </div>
	</div>


	<div id="previewLightbox" class="lightbox hide fade" tabindex="-1" role="dialog" aria-hidden="true">
		<div class='lightbox-content'>
			<img id="full-img" src="">
		</div>
	</div>
	


	<div id="loading_container" style="display:none;">
		<div id="loading" style="background-color:#000; position:fixed; width:100%; height:100%; top:0px; left:0px;z-index:100000"></div>
		<img id="loading_animation" src="<?php echo $up_media_url; ?>img/storing_animation.gif" alt="loading" style="z-index:10001; margin-left:-32px; margin-top:-32px; position:fixed; left:50%; top:50%"/>
	</div>

	<div class="modal hide fade" id="previewAV">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3><?php echo lang_Preview; ?></h3>
		</div>
		<div class="modal-body">
			<div class="row-fluid body-preview">
			</div>
		</div>

	</div>

	<img id='aviary_img' src='' class="hide"/>

<?php } ?>
<script type="text/javascript">

<?php $timestamp = time(); ?>

    var imgsizes = ['<?php echo GlobalsRevSlider::IMAGE_SIZE_THUMBNAIL?>',

                        '<?php echo GlobalsRevSlider::IMAGE_SIZE_MEDIUM?>',

                        '<?php echo GlobalsRevSlider::IMAGE_SIZE_LARGE?>'];

    $(function() {

        

        

    

        $('#imageTab a').click(function(e) {

            e.preventDefault();

            $(this).tab('show');

        });

        $('#divImageList ul   li a').click(function(){

            return false;

        });

        

        var splitFileparts = function(filename, size){

            var filerealname = filename.substr(0,filename.lastIndexOf('.'));

            var fileext = filename.substr(filename.lastIndexOf('.'),filename.length - filename.lastIndexOf('.'));

            var newfilename = filerealname+'-'+imgsizes[size]+'x'+imgsizes[size]+fileext;

            return [newfilename,filerealname,fileext];

        };

        

        

        var selectableCb = function(){ 

            $("#selectable").selectable({

                selected: function(event, ui) {


 
                    $("#imgContainer").empty();

                     $(ui.selected).children('figure').children('a').children('div').children('div').children('img.original').clone().appendTo("#imgContainer");

                    $("#txtImageInsert, #txtImageDelete").val($(ui.selected).data('image'));

                }

            });

        };

        selectableCb();

        $('#txtImageInsert').click(function(){

        

            var isize = $('#image_size > option:selected').val();

            var filename = $(this).val();

            if(filename == '') return false;

            

            var nfilename = '';

            switch(isize){

                case 'thumbnail':                    

                    nfilename = splitFileparts(filename,0);                    

                    filename = nfilename[0];

                    break;

                case 'medium':                    

                    nfilename = splitFileparts(filename,1);

                    filename = nfilename[0];

                    break;

                case 'large':                    

                    nfilename = splitFileparts(filename,2);                    

                    filename = nfilename[0];

                    break;

                default:

                    break;

            }

            

            parent.iframe_img = '<?php echo $url?>'+filename;

            parent.tb_remove();

            parent.getImg();

        });

        $('#txtImageDelete').click(function(){

            var img = $(this).val();

            var data = {};

            data.img = img;

            if(img !== undefined || img !== '')

            UniteAdminRev.ajaxRequest("delete_uploaded_image",data,function(response){

                if(response.success !== undefined && response.success == '1'){

                    $("#imgContainer").html('');

                    $(this).val('');

                    $('#txtImageInsert').val('');

                    $('#divImageList').html(response.output);  

                    selectableCb();

                }

            });

        });

        

         



    });

</script>
<style type="text/css">
 

    #selectable li.last {} 

    #selectable li figure{

        display: block;



        border: 5px solid transparent;               

    }

    #selectable  li.ui-selected  figure

    {

        border-color: #0084FF;                

    }            

</style>
