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
	$id_product=intval(Tools::getValue('id_product'),0);
	$descriptions=array('description_short'=>'','description'=>'');
	if ($id_product!=0)
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			$id_shop = SCI::getSelectedShop();
			if(empty($id_shop))
			{
				$product = new Product($id_product);
				$id_shop = $product->id_shop_default;
			}
		}
		
		$sql = "SELECT description_short,description FROM "._DB_PREFIX_."product_lang WHERE id_product='".intval($id_product)."' AND id_lang='".intval($id_lang)."'";
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$sql.=" AND id_shop=".(int)$id_shop;
		$descriptions=Db::getInstance()->getRow($sql);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
<link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE;?>" />
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script type="text/javascript" src="lib/js/tiny_mce/tiny_mce.js"></script>
<script type="text/javascript" src="lib/js/tiny_mce/jquery.tinymce.js"></script>
</head>
<body style="padding:0px;margin:0px;">
<?php
		$iso = UISettings::getSetting('forceSCLangIso');
		if(empty($iso))
			$iso = Language::getIsoById((int)($sc_agent->id_lang));
		echo '
<script type="text/javascript">
var iso = \''.(file_exists('lib/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
var pathCSS = \''._THEME_CSS_DIR_.'\' ;
var pathTiny = \'lib/js/tiny_mce/tiny_mce.js\' ;
var add = \'lib/js/\' ;
</script>';
?>
<script type="text/javascript">
	$().ready(function() {
<?php
	if (file_exists(SC_TOOLS_DIR.'cat_description/tiny_config.php'))
	{
		require_once(SC_TOOLS_DIR.'cat_description/tiny_config.php');
	}else{
		require_once('tiny_config.php');
	}
?>
	});

	function ajaxfilemanager(field_name, url, type, win) {
		var ajaxfilemanagerurl = add+"ajaxfilemanager/ajaxfilemanager.php?language="+(iso=='fr'?'fr':'en'); // not ready ?language="+iso;
		switch (type) {
			case "image":
				break;
			case "media":
				break;
			case "flash": 
				break;
			case "file":
				break;
			default:
				return false;
	}
    tinyMCE.activeEditor.windowManager.open({
        url: ajaxfilemanagerurl,
        width: 580,
        height: 440,
        inline : "yes",
        close_previous : "no"
    },{
        window : win,
        input : field_name
    });
	}

function checkSizetMCE() {
	var tiny=$('#description_short').tinymce();
	window.top.prop_tb.setItemText('txt_descriptionsize','<?php echo _l('Short description charset',1)._l(':')?> '+tiny.getContent()/*.replace(/<[^>]+>/g, '')*/.length+'/<?php echo _s('CAT_SHORT_DESC_SIZE')?>');
	return true;
}
function checkSize() {
	var tiny=$('#description_short').tinymce();
	if (tiny.getContent().replace(/<[^>]+>/g, '').length <= <?php echo _s('CAT_SHORT_DESC_SIZE')?>) return true;
	return false;
}
var tMCE1=0;
var tMCE2=0;
var tMCE1Content=0;
var tMCE2Content=0;

function ajaxLoad(args,id_product,id_lang) {
	if (tMCE1==0) tMCE1 = $('#description_short').tinymce();
	if (tMCE2==0) tMCE2 = $('#description').tinymce();
	$('#id_product').val(id_product);
	$('#id_lang').val(id_lang);
	tMCE1.setProgressState(1);
	tMCE2.setProgressState(1);
	$.get("index.php?ajax=1&act=cat_description_get&content=description_short"+args, function(data){
		tMCE1.setProgressState(0);
		tMCE1.setContent(data);
		tMCE1Content=data;
		tMCE1.isNotDirty=1; // change modified state of tinyMCE
		checkSizetMCE();
		});
	$.get("index.php?ajax=1&act=cat_description_get&content=description"+args, function(data){
		tMCE2.setProgressState(0);
		tMCE2.setContent(data);
		tMCE2Content=data;
		tMCE2.isNotDirty=1; // change modified state of tinyMCE
		});
}
function ajaxSave() {
	if (tMCE1==0) tMCE1 = $('#description_short').tinymce();
	if (tMCE2==0) tMCE2 = $('#description').tinymce();
	tMCE1.setProgressState(1);
	tMCE2.setProgressState(1);
	$.post("index.php", $("#form_descriptions").serialize(), function(data){
			tMCE1.setProgressState(0);
			tMCE2.setProgressState(0);
			if (data=='OK')
			{
				tMCE1.isNotDirty=1;
				tMCE2.isNotDirty=1;
			}else{
				if (data=='ERR|description_short_size')
				{
					alert('<?php echo _l('Short description size must be < ',1)._s('CAT_SHORT_DESC_SIZE')?>');
				}
				<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
				if (data=='ERR|description_short_with_iframe')
				{
					alert('<?php echo _l('Short description can\'t include an iframe or is invalid',1); ?>');
				}
				if (data=='ERR|description_with_iframe')
				{
					alert('<?php echo _l('Description can\'t include an iframe or is invalid',1); ?>');
				}
				if (data=='ERR|description_short_invalid')
				{
					alert('<?php echo _l('Short description is invalid',1); ?>');
				}
				if (data=='ERR|description_invalid')
				{
					alert('<?php echo _l('Description is invalid',1); ?>');
				}
				<?php } ?>
			}
		});
}
function checkChange() {
	if (tMCE1==0) tMCE1 = $('#description_short').tinymce();
	if (tMCE2==0) tMCE2 = $('#description').tinymce();
	<?php if(_s("CAT_NOTICE_SAVE_DESCRIPTION")) { ?>
	if (tMCE1.isDirty() || tMCE2.isDirty())
	   if (confirm('<?php echo _l('Do you want to save the descriptions?',1)?>'))
	   	ajaxSave();
	<?php } ?>
}


function showShortDesc()
{
	$("#container_description_short").show();
}
function hideShortDesc()
{
	$("#container_description_short").hide();
}
</script>
<form id="form_descriptions" method="POST">
<input name="ajax" type="hidden" value="1"/>
<input name="act" type="hidden" value="cat_description_update"/>
<input id="id_product" name="id_product" type="hidden" value="<?php echo $id_product;?>"/>
<input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang;?>"/>
<div id="container_description_short">
<textarea id="description_short" name="description_short" class="tinymce1 rte" cols="50" rows="10" style=""><?php echo $descriptions['description_short'];?></textarea>
</div>
<textarea id="description" name="description" class="tinymce2 rte" cols="50" rows="30" style=""><?php echo $descriptions['description'];?></textarea>
</form>
</body>
</html>