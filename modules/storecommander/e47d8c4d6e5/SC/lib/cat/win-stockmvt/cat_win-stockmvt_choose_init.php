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
$id_product=intval(Tools::getValue('id_product'), 0);
$id_product_attribute=intval(Tools::getValue('id_product_attribute'), 0);
$id_warehouse= intval(Tools::getValue('id_warehouse', SCI::getSelectedWarehouse()));

if(!empty($id_product_attribute) && empty($id_product))
{
	$pa = new Combination($id_product_attribute);
	$id_product = $pa->id_product;
}

?>
<?php if(empty($id_product) || empty($id_warehouse)) { ?>
<script type="text/javascript">
wStockMvt.hide();
</script>
<?php die(); }
?>
<script type="text/javascript">
dhxlStockMvt=wStockMvt.attachLayout("2E");
dhxlStockMvt_w = dhxlStockMvt.cells('a');
dhxlStockMvt_w.hideHeader();
dhxlStockMvt_w.setHeight(200);
dhxlStockMvt.cells('b').setHeight(450);
dhxlStockMvt.cells('b').hideHeader();

dhxlStockMvt_w.attachURL("index.php?ajax=1&act=cat_win-stockmvt_choose_form&id_product=<?php echo $id_product; ?>&id_lang="+SC_ID_LANG);

stockmvtChooseAdd();

function stockmvtChooseAdd()
{
	wStockMvt.setDimension(430, 680);
	dhxlStockMvt_w.setHeight(200);
	dhxlStockMvt.cells('b').setHeight(480);
	$.get("index.php?ajax=1&act=cat_win-stockmvt_add_init&subform=1&id_product=<?php echo $id_product; ?>&id_product_attribute=<?php echo $id_product_attribute; ?>&id_warehouse=<?php echo $id_warehouse; ?>&id_lang="+SC_ID_LANG,function(data){
			$('#jsExecute').html(data);
		});
	wStockMvt.show();
}
function stockmvtChooseRemove()
{
	wStockMvt.setDimension(430, 580);
	dhxlStockMvt_w.setHeight(200);
	dhxlStockMvt.cells('b').setHeight(380);
	$.get("index.php?ajax=1&act=cat_win-stockmvt_delete_init&subform=1&id_product=<?php echo $id_product; ?>&id_product_attribute=<?php echo $id_product_attribute; ?>&id_warehouse=<?php echo $id_warehouse; ?>&id_lang="+SC_ID_LANG,function(data){
			$('#jsExecute').html(data);
		});
	wStockMvt.show();
}

</script>