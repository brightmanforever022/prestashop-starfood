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
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script src="lib/js/ckeditor/ckeditor.js"></script>
</head>
<body style="padding:0px;margin:0px;">
<script type="text/javascript">
var tCKE1=0;
var tCKE2=0;
var tCKE1Content=0;
var tCKE2Content=0;


function checkSizetCKE() {
	if (tCKE1==0) tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	window.top.prop_tb.setItemText('txt_descriptionsize','<?php echo _l('Short description charset',1)._l(':')?> '+tCKE1.getData()/*.replace(/<[^>]+>/g, '')*/.length+'/<?php echo _s('CAT_SHORT_DESC_SIZE')?>');
	return true;
}
function checkSize() {
	if (tCKE1==0) tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	if (tCKE1.getData().replace(/<[^>]+>/g, '').length <= <?php echo _s('CAT_SHORT_DESC_SIZE')?>) return true;
	return false;
}

function ajaxLoad(args,id_product,id_lang) {
	if (tCKE1==0) tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	if (tCKE2==0) tCKE2 = CKEDITOR.replace( 'description' , {height: (total_height*60/100) });
	$('#id_product').val(id_product);
	$('#id_lang').val(id_lang);
	$.get("index.php?ajax=1&act=cat_description_get&content=description_short"+args, function(data){
		tCKE1.setData(data);
		tCKE1Content=data;
		checkSizetCKE();
		tCKE1.resetDirty();
		setTimeout(function(){ putInBase()}, 500);
		});
	$.get("index.php?ajax=1&act=cat_description_get&content=description"+args, function(data){
		parent.prop_tb._descriptionsLayout.cells('a').progressOff();
		tCKE2.setData(data);
		tCKE2Content=data;
		tCKE2.resetDirty();
		setTimeout(function(){ putInBase()}, 500);
		});
}
function ajaxSave() {
	if (tCKE1==0) tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	if (tCKE2==0) tCKE2 = CKEDITOR.replace( 'description' , {height: (total_height*60/100) });
	$("#form_descriptions textarea#description_short").val(tCKE1.getData());
	$("#form_descriptions textarea#description").val(tCKE2.getData());
	$.post("index.php", $("#form_descriptions").serialize(), function(data){
			parent.prop_tb._descriptionsLayout.cells('a').progressOff();
			if (data=='OK')
			{
				tCKE1.resetDirty();
				tCKE2.resetDirty();
				setTimeout(function(){ putInBase()}, 500);
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
	if (tCKE1==0) tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	if (tCKE2==0) tCKE2 = CKEDITOR.replace( 'description' , {height: (total_height*60/100) });
	//if (tCKE1.checkDirty() || tCKE2.checkDirty())
	
	if(tCKE2.getData()!=$("#base_description").val() || tCKE1.getData()!=$("#base_description_short").val())
	   if (confirm('<?php echo _l('Do you want to save the descriptions?',1)?>'))
			ajaxSave();
}

$(document).ready(function(){
	tCKE1 = CKEDITOR.replace( 'description_short' , {height: (total_height*30/100) });
	tCKE2 = CKEDITOR.replace( 'description' , {height: (total_height*60/100) });
	setTimeout(function(){ putInBase()}, 500);
	checkSizetCKE();
});

var total_height = parent.prop_tb._descriptionsLayout.cells('a').getHeight()-190;

function showShortDesc()
{
	tCKE2.resize( "100%", (total_height*60/100), true );
	$("#container_description_short").show();
}
function hideShortDesc()
{
	$("#container_description_short").hide();
	tCKE2.resize( "100%", (total_height*1+70), true );
}

function putInBase()
{
	$("#base_description_short").val(tCKE1.getData());
	$("#base_description").val(tCKE2.getData());
}
</script>
<form id="form_descriptions" method="POST">
<input name="ajax" type="hidden" value="1"/>
<input name="act" type="hidden" value="cat_description_update"/>
<input id="id_product" name="id_product" type="hidden" value="<?php echo $id_product;?>"/>
<input id="id_lang" name="id_lang" type="hidden" value="<?php echo $id_lang;?>"/>
<div id="container_description_short">
<textarea id="description_short" name="description_short" rows="10" style="width: 100%; height: 100%;"><?php echo $descriptions['description_short'];?></textarea>
</div>
<div id="container_description">
<textarea id="description" name="description" rows="30" style="width: 100%"><?php echo $descriptions['description'];?></textarea>
</div>

<textarea id="base_description_short" rows="10" style="display:none;"><?php echo $descriptions['description_short'];?></textarea>
<textarea id="base_description" rows="30" style="display:none;"><?php echo $descriptions['description'];?></textarea>

</form>
</body>
</html>