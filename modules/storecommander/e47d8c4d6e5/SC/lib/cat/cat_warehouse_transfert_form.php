<style type="text/css">
.btn {
	background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #a4bed4;
    color: #34404b;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
	font-weight: bold;
	cursor: pointer;
	float: right;
	margin-right: 15px;
}

div { font-family: Tahoma;
    font-size: 11px !important; }
</style>
<div>
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
 echo _l('You want to transfert all stock from warehouse').' "<strong class="warehouse_A"></strong>"'; ?>
	<br/>
	<br/>
	<?php echo _l('to warehouse :') ;?>
	<select id="warehouse_B">
		<?php
		$shop = SCI::getSelectedShop();
		$results = Warehouse::getWarehouses(true, $shop);
		foreach ($results as $key=>$warehouse) { ?>
			<option value="<?php echo $warehouse["id_warehouse"]; ?>" id="w_<?php echo $warehouse["id_warehouse"]; ?>"><?php echo $warehouse["name"]; ?></option>
		<?php } ?>
	</select>
	<br/>
	<br/>
	<input type="checkbox" id="truncate_A" value="1" /> <?php echo _l('Would you like to dissociate all products from warehouse').' "<strong class="warehouse_A"></strong>"'; ?>
	<?php /* Faux maintenant : echo _l('Caution! This action will delete all stock and movements for destination warehouse!'); */ ?>
	<br/>
	<br/>
	<input type="button" onclick="validTransfert()" class="btn" value="<?php echo _l('Transfert'); ?>" />
</div>
<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
<script>
$(".warehouse_A").html(parent.cat_warehousetree.getItemText(parent.id_selected_warehouse));
$("#w_"+parent.id_selected_warehouse).hide();
function validTransfert()
{
	var id_warehouse = $("#warehouse_B").val();
	if(id_warehouse!=undefined && id_warehouse!=null && id_warehouse!="" && id_warehouse!=0 && id_warehouse!=parent.id_selected_warehouse)
	{
		var truncate_A = 0;
		if( $('#truncate_A').is(':checked') )
			truncate_A = 1;
		
		parent.transfertWarehouse(id_warehouse, truncate_A);
		parent.wWarehouseStockTransfert.close();
	}
}
</script>