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

	$id_attribute=explode(',',Tools::getValue('id_attribute','0'));
	$action=Tools::getValue('action','0');
	if ($id_attribute==0) exit;
	if ($action=='delete')
	{
		foreach($id_attribute AS $v)
		{
			if(file_exists(_PS_COL_IMG_DIR_.$v.'.jpg'))
				@unlink(_PS_COL_IMG_DIR_.$v.'.jpg');
		}
	}
	if ($action=='duplicate')
	{
		$attributes=explode(',',Tools::getValue('attributes',0));
		$id_group=Tools::getValue('id_group',0);
		if ($id_group==0 || $attributes==0) exit;
		foreach($attributes AS $a)
		{
			$srcAttr=new Attribute($a);
			$newAttr=new Attribute();
			$newAttr->id_attribute_group=$id_group;
			foreach($languages AS $lang)
			{
				$newAttr->name[$lang['id_lang']]=($srcAttr->name[$lang['id_lang']]!=''?$srcAttr->name[$lang['id_lang']]:' ');
			}
			$newAttr->color=$srcAttr->color;
			$newAttr->add();
			if(file_exists(_PS_COL_IMG_DIR_.$a.'.jpg'))
				@copy(_PS_COL_IMG_DIR_.$a.'.jpg',_PS_COL_IMG_DIR_.$newAttr->id.'.jpg');
		}
	}
	if ($action=='add')
	{
		$id_attribute=$id_attribute[0];

?>


<style type="text/css">@import url(<?php echo SC_PLUPLOAD;?>js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/browserplus-2.4.21-min.js"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS;?>"></script>
<?php
echo (sc_in_array($user_lang_iso,array('cs','da','de','es','fi','fr','hr','hu','it','ja','lv','nl','pt','br','ro','ru','sr','sv'),"catWinAttrTexture_ISO")?'<script type="text/javascript" src="'.SC_PLUPLOAD.'js/i18n/'.$user_lang_iso.'.js"></script>':'');
?>
<script type="text/javascript">
	
	// Convert divs to queue widgets when the DOM is ready
	$(window).ready(function(){
		noerror=true;
		$("#uploaderAttrTexture").pluploadQueue({
			// General settings
			runtimes : 'gears,html5,flash,silverlight,browserplus',
			url : 'index.php?ajax=1&act=all_upload&obj=attrtexture&id_attribute=<?php echo $id_attribute;?>',
			max_file_size : '20mb',
			chunk_size : '500ko',
			unique_names : false,
			// Specify what files to browse for
			filters : [
				{title : "Images", extensions : "jpg,jpeg,png,gif"}
			],
			init : {
	            BeforeUpload : function(up){
					$("#uploaderAttrTexture").pluploadQueue().settings.url='index.php?ajax=1&act=all_upload&obj=attrtexture&id_attribute=<?php echo $id_attribute;?>';
	            },
	            UploadComplete : function(up){
	            	top.displayAttributes();
	            	top.wAttributeTexture.hide();
	            }
			},
      // Flash settings  
      flash_swf_url : '<?php echo SC_PLUPLOAD;?>js/plupload.flash.swf',  
      // Silverlight settings  
      silverlight_xap_url : '<?php echo SC_PLUPLOAD;?>js/plupload.silverlight.xap' 
		});
		// Client side form validation
		$('form').submit(function(e){
			var uploader = $('#uploaderAttrTexture').pluploadQueue();
			// Files in queue upload them first
			if (uploader.files.length > 0){
				// When all files are uploaded submit form
				uploader.bind('StateChanged', function(){
					if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)){
						$('form')[0].submit();
					}
				});					
				uploader.start();
			}else{
				alert('You must queue at least one file.');
			}
			return false;
		});
	});
</script>

<style>
body{ width:550px;height:320px;margin:0;background:#DFDFDF;}
#divAddAttrTexture{ width:inherit;height:inherit;}
#uploaderAttrTexture{ width:inherit;height:inherit;}
.formDragDrop{ width:inherit;height:inherit;margin:0;}
</style>	

<div id="divAddAttrTexture">
	<form class="formDragDrop">
		<div id="uploaderAttrTexture">
			<p>Your browser doesn't have Gears, BrowserPlus or HTML5 support.</p>
		</div>
	</form>
</div>



<?php
	}
