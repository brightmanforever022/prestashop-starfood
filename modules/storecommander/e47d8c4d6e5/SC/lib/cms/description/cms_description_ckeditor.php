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

	$id_lang=(int)Tools::getValue('id_lang');
	$id_cms=(int)Tools::getValue('id_cms', 0);
	$id_shop=(int)Tools::getValue('id_shop', 0);

	$content = null;

	if ($id_cms!=0)
	{

		$sql = "SELECT content FROM "._DB_PREFIX_."cms_lang WHERE id_cms='".(int)$id_cms."' AND id_lang='".(int)$id_lang."'";
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
			$sql.=" AND id_shop=".(int)$id_shop;
		$content=Db::getInstance()->getValue($sql);
	}

	$iso = Language::getIsoById((int)$id_lang);
	
	if(empty($iso))
		$iso = UISettings::getSetting('forceSCLangIso');
	if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
		$sql = 'SELECT locale FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
	else
		$sql = 'SELECT language_code FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
	$lang_iso = Db::getInstance()->getValue($sql);
	list($min,$maj) = explode("-", $lang_iso);
	$lang_iso = strtolower($min).'_'.strtoupper($maj);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script src="lib/js/ckeditor/ckeditor.js"></script>
</head>
<body style="padding:0px;margin:0px;">
<script type="text/javascript">
<?php echo 'var pathCSS = \''._THEME_CSS_DIR_.'\' ;' ?>
<?php echo 'var langIso = "'.$lang_iso.'" ;' ?>
<?php
if(version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
	echo 'var fileCSS = "theme.css" ;';
} else {
	echo 'var fileCSS = "global.css" ;';
}
?>

var activeSCAYT = <?php echo (_s('APP_CKEDITOR_AUTOCORRECT_ACTIVE')=="1"?"true":"false"); ?>;
CKEDITOR.config.customConfig="config03.js";
<?php if(_s('CMS_PROPERTIES_DESCRIPTION_CSS')) { ?>CKEDITOR.config.contentsCss = pathCSS+fileCSS ;<?php } ?>
CKEDITOR.config.scayt_sLang = langIso;
var tCKE=0;
var tCKEContent=0;

function ajaxLoad(args,id_cms,id_lang,id_shop) {
	if (tCKE==0) {
		tCKE = CKEDITOR.replace( 'content' , {
			on :
			{
				'instanceReady' : function( evt ) {
					evt.editor.execCommand( 'maximize' );
				}
			}
		});
	}
	$('#id_cms').val(id_cms);
	$('#id_lang').val(id_lang);
	$('#id_shop').val(id_shop);
	$.get("index.php?ajax=1&act=cms_description_get&content=content&id_shop="+id_shop+args, function(data){
		parent.prop_tb._descriptionsLayout.cells('a').progressOff();
		tCKE.setData(data);
		tCKEContent=data;
		tCKE.resetDirty();
		setTimeout(function(){ putInBase()}, 500);
		});
}
function ajaxSave() {
	if (tCKE==0) {
		tCKE = CKEDITOR.replace( 'content' , {
			on :
			{
				'instanceReady' : function( evt ) {
					evt.editor.execCommand( 'maximize' );
				}
			}
		});
	}
	$("#form_content textarea#content").val(tCKE.getData());
	$.post("index.php", $("#form_content").serialize(), function(data){
			parent.prop_tb._descriptionsLayout.cells('a').progressOff();
			if (data=='OK')
			{
				tCKE.resetDirty();
				setTimeout(function(){ putInBase()}, 500);
			}else{
				<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
				if (data=='ERR|content_with_iframe')
				{
					alert('<?php echo _l('Content can\'t include an iframe or is invalid',1); ?>');
				}
				if (data=='ERR|content_invalid')
				{
					alert('<?php echo _l('Content is invalid',1); ?>');
				}
				if (data=='ERR|process')
				{
					alert('<?php echo _l('Error during process',1); ?>');
				}
				<?php } ?>
			}
		});
}
function checkChange() {
	if (tCKE==0) {
		tCKE = CKEDITOR.replace( 'content' , {
			on :
			{
				'instanceReady' : function( evt ) {
					evt.editor.execCommand( 'maximize' );
				}
			}
		});
	}
	<?php if(_s("CMS_NOTICE_SAVE_DESCRIPTION")) { ?>
	if(tCKE.getData()!=$("#base_content").val())
	   if (confirm('<?php echo _l('Do you want to save the content?',1)?>'))
			ajaxSave();
	<?php } ?>
}

$(document).ready(function(){
	// height max !
	tCKE = CKEDITOR.replace( 'content' , {
		on :
		{
			'instanceReady' : function( evt ) {
				evt.editor.execCommand( 'maximize' );
			}
		}
	});
	setTimeout(function(){ putInBase()}, 500);
});

function putInBase()
{
	$("#base_content").val(tCKE.getData());
}
</script>
<form id="form_content" method="POST">
<input name="ajax" type="hidden" value="1"/>
<input name="act" type="hidden" value="cms_description_update"/>
<input id="id_cms" name="id_cms" type="hidden" value="<?php echo $id_cms;?>"/>
<input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang;?>"/>
<input id="id_shop" name="id_shop" type="hidden" value="<?php echo $id_shop;?>"/>
<div id="container_content">
<textarea id="content" name="content" rows="30" style="width: 100%"><?php echo $content;?></textarea>
</div>

<textarea id="base_content" rows="30" style="display:none;"><?php echo $content;?></textarea>

</form>
</body>
</html>
