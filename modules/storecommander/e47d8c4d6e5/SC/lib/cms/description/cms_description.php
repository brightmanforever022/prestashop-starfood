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
	$id_cms=(int)Tools::getValue('id_cms');
	$id_shop=(int)Tools::getValue('id_shop', 0);
	$content=array('content'=>'');
	if ($id_cms!=0)
	{
		$sql = "SELECT content FROM "._DB_PREFIX_."cms_lang WHERE id_cms='".(int)$id_cms."' AND id_lang='".(int)$id_lang."'";
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
			$sql.=" AND id_shop=".(int)$id_shop;
		$content=Db::getInstance()->getRow($sql);
	}
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
var tMCE=0;
var tMCEContent=0;

function ajaxLoad(args,id_cms,id_lang,id_shop) {
	if (tMCE==0) tMCE = CKEDITOR.replace( 'content' , {height: (total_height*60/100) });
	$('#id_cms').val(id_cms);
	$('#id_lang').val(id_lang);
	$('#id_shop').val(id_shop);
	$.get("index.php?ajax=1&act=cms_description_get&content=content"+args, function(data){
		parent.prop_tb._descriptionsLayout.cells('a').progressOff();
		tMCE.setData(data);
		tMCEContent=data;
		tMCE.resetDirty();
		setTimeout(function(){ putInBase()}, 500);
		});
}
function ajaxSave() {
	if (tMCE==0) tMCE = CKEDITOR.replace( 'content' , {height: (total_height*60/100) });
	$("#form_content textarea#conten").val(tMCE.getData());
	$.post("index.php", $("#form_content").serialize(), function(data){
			parent.prop_tb._descriptionsLayout.cells('a').progressOff();
			if (data=='OK')
			{
				tMCE.resetDirty();
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
				<?php } ?>
			}
		});
}
function checkChange() {
	if (tMCE==0) tMCE = CKEDITOR.replace( 'content' , {height: (total_height*30/100) });
	
	if(tMCE.getData()!=$("#base_content").val())
	   if (confirm('<?php echo _l('Do you want to save the content?',1)?>'))
			ajaxSave();
}

$(document).ready(function(){
	tMCE = CKEDITOR.replace( 'content' , {height: (total_height*60/100) });
	setTimeout(function(){ putInBase()}, 500);
});

var total_height = parent.prop_tb._descriptionsLayout.cells('a').getHeight()-190;

function putInBase()
{
	$("#base_content").val(tMCE.getData());
}
</script>
<form id="form_content" method="POST">
<input name="ajax" type="hidden" value="1"/>
<input name="act" type="hidden" value="cms_description_update"/>
<input id="id_cms" name="id_cms" type="hidden" value="<?php echo $id_cms;?>"/>
<input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang;?>"/>
<div id="container_content">
<textarea id="content" name="content" rows="30" style="width: 100%"><?php echo $content['content'];?></textarea>
</div>
<textarea id="base_content" rows="30" style="display:none;"><?php echo $content['content'];?></textarea>

</form>
</body>
</html>
