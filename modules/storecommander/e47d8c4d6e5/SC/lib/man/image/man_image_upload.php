<style type="text/css">@import url(<?php
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
 echo SC_PLUPLOAD;?>js/jquery.plupload.queue/css/jquery.plupload.queue.css);</style>
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script type="text/javascript" src="lib/js/jquery.cokie.js"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/browserplus-2.4.21-min.js"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/plupload.full.js"></script>
<script type="text/javascript" src="<?php echo SC_PLUPLOAD;?>js/jquery.plupload.queue/jquery.plupload.queue.js"></script>
<script type="text/javascript" src="<?php echo SC_JSFUNCTIONS;?>"></script>
<?php

echo (sc_in_array($user_lang_iso,array('cs','da','de','es','fi','fr','hr','hu','it','ja','lv','nl','pt','br','ro','ru','sr','sv'),"manImageUpload_ISO")?'<script type="text/javascript" src="'.SC_PLUPLOAD.'js/i18n/'.$user_lang_iso.'.js"></script>':'');

$id_lang=Tools::getValue('id_lang');
$manufacturer_list=Tools::getValue('manufacturer_list');
?>
<script type="text/javascript">
	// Convert divs to queue widgets when the DOM is ready
	$(window).ready(function(){
		noerror=true;
		var uploader = $("#uploader").pluploadQueue({
			// General settings
			runtimes : 'gears,html5,flash,silverlight,browserplus',
			url : 'index.php?ajax=1&act=all_upload&obj=manufacturer_logo&manufacturer_list=<?php echo $manufacturer_list;?>&id_lang=<?php echo $id_lang;?>',
			max_file_size : '20mb',
			chunk_size : '500ko',
			unique_names : false,
			// Specify what files to browse for
			filters : [
				{title : "Images", extensions : "jpg,jpeg,png,gif,bmp"}
			],
			init : {
	            BeforeUpload : function(up){
					$("#uploader").pluploadQueue().settings.url='index.php?ajax=1&act=all_upload&obj=manufacturer_logo&manufacturer_list=<?php echo $manufacturer_list;?>&id_lang=<?php echo $id_lang;?>';
	            },
	            UploadComplete : function(up){
	            	if (noerror)
						top.prop_tb._imagesUploadWindow['<?php echo $manufacturer_list; ?>'].hide();
					//if (top.last_manufacturerID==<?php echo (int)$manufacturer_list;?>)
					top.displayImages();
					top.displayManufacturers();
	            },
	            Error : function(up){
								noerror=false;
							},
	            FileUploaded : function(up, file, info){
	            	if (!isJSON(info.response))
	            	{
						noerror=false;
						top.dhtmlx.message({text:'Adding image to manufacturer ID <?php echo (int)$manufacturer_list;?>: Wrong JSON format, please check FireBug or contact SC support.',type:'error',expire:-1});
						if (typeof console != "undefined")
		            		console.log(info);
	            	}else{
									var jsonobj=eval('('+info.response+')');
	            		if (jsonobj.error)
	            		{
							noerror=false;
							top.dhtmlx.message({text:'id_manufacturer <?php echo (int)$manufacturer_list;?>: ERR('+jsonobj.error.code+') '+jsonobj.error.message,type:'error',expire:-1});
						}
					}
	            },
	            FilesAdded : function(up, files) {
		        	if($.cookie('sc_man_img_auto_upload')==1)
		        	{
		            	if ($("#uploader").pluploadQueue().files.length > 0)
		            	{
		            		$("#uploader").pluploadQueue().start();
		            		parent.prop_tb._imagesUploadWindow['<?php echo $manufacturer_list; ?>'].park();
		            	}
		        	}
	    	    }
			},
	      // Flash settings  
	      flash_swf_url : '<?php echo SC_PLUPLOAD;?>js/plupload.flash.swf',  
	      // Silverlight settings  
	      silverlight_xap_url : '<?php echo SC_PLUPLOAD;?>js/plupload.silverlight.xap' 
		});
		// Client side form validation
		$('form').submit(function(e){
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
#divAddImages{ width:inherit;height:inherit;}
#uploader{ width:inherit;height:inherit;}
.formDragDrop{ width:inherit;height:inherit;margin:0;}
</style>	

<div id="divAddImages">
	<form class="formDragDrop">
		<div id="uploader">
			<p>Your browser doesn't have Gears, BrowserPlus or HTML5 support.</p>
		</div>
	</form>
</div>
