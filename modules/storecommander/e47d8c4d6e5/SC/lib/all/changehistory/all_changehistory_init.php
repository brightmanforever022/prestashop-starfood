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
?>
<script type="text/javascript">
	var lHistory = new dhtmlXLayoutObject(wCatHistory, "1C");
	var clickable = false;
	lHistory.cells('a').setText('<?php echo _l('History',1);?>');
	his_grid=lHistory.cells('a').attachGrid();
	his_grid.setImagePath('lib/js/imgs/');
	his_grid.setHeader("ID,<?php echo _l('ID employee')?>,<?php echo _l('Section')?>,<?php echo _l('Action')?>,<?php echo _l('Object')?>,<?php echo _l('Old value')?>,<?php echo _l('New value')?>,<?php echo _l('Object ID')?>,<?php echo _l('Lang ID')?>,<?php echo _l('Table')?>,<?php echo _l('Date')?><?php if(SCMS) echo ","._l("Shops"); ?>");
	his_grid.setColumnIds("id_history,id_employee,section,action,object,oldvalue,newvalue,object_id,lang_id,table,date_add<?php if(SCMS) echo ",shops"; ?>");
	his_grid.setInitWidths("50,50,90,75,100,100,100,60,60,100,115<?php if(SCMS) echo ",100"; ?>");
	his_grid.setColAlign("right,left,left,left,left,left,left,right,right,left,left<?php if(SCMS) echo ",left"; ?>");
	his_grid.setColTypes("ro,ro,ro,ro,ro,ed,ed,ro,ro,ro,ro<?php if(SCMS) echo ",ro"; ?>");
	his_grid.setColSorting("int,str,str,str,str,str,str,int,int,str,str<?php if(SCMS) echo ",str"; ?>");
	his_grid.attachHeader("#text_filter,#text_filter,#select_filter,#select_filter,#select_filter,#text_filter,#text_filter,#numeric_filter,#numeric_filter,#select_filter,#text_filter<?php if(SCMS) echo ",#text_filter"; ?>");
	his_grid.setDateFormat("%Y-%m-%d");
	his_grid.enableSmartRendering(true);
	his_grid.enableMultiline(true);
	his_grid.init();
	his_grid.enableHeaderMenu();

	his_grid_sb=lHistory.cells('a').attachStatusBar();
	
	his_tb=lHistory.cells('a').attachToolbar();
	his_tb.addButton("delete", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	his_tb.setItemToolTip('delete','<?php echo _l('Delete all history',1)?>');
	his_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	his_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid',1)?>');
	his_tb.addButton("exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	his_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
	his_tb.addButton('history_rollback',100,'','lib/img/table_row_delete.png','lib/img/table_row_delete_grey.png');
	his_tb.setItemToolTip('history_rollback','<?php echo _l('Delete this row and restore the last version',1)?>');
	his_tb.disableItem('history_rollback');

	his_grid.enableMultiselect(true);
	his_grid.attachEvent("onRowSelect", function(id)
	{
		 var cat_product_authorized_list = ["id_supplier","id_manufacturer",
			"id_tax_rules_group","on_sale","online_only","ean13","upc","ecotax",
			"minimal_quantity","price","wholesale_price","unity","unit_price_ratio",
			"additional_shipping_cost","reference","supplier_reference","location",
			"width","height","depth","weight","out_of_stock","quantity_discount",
			"active","redirect_type","id_product_redirected","available_for_order",
			"available_date","condition","show_price","indexed","visibility","date_add"];

		var cat_product_lang_authorized_list = ["description","description_short","link_rewrite","meta_description","meta_keywords","meta_title","name","available_now","available_later"];

		var cat_category_lang_authorized_list = [
			"name","description","link_rewrite","meta_title","meta_keywords","meta_description"];

		var cat_category_authorized_list = ["active","position"];

		var cat_category_shop_authorized_list = ["position"];

		var cat_product_shop_authorized_list = ["id_tax_rules_group","on_sale",
			"online_only","ecotax","minimal_quantity","price","wholesale_price","unity","unit_price_ratio",
			"additional_shipping_cost","active","redirect_type","id_product_redirected","available_for_order",
			"available_date","condition","show_price","indexed","visibility"];

		var global_authorized_array = cat_product_authorized_list.concat(cat_product_lang_authorized_list, cat_category_lang_authorized_list, cat_category_authorized_list, cat_category_shop_authorized_list, cat_product_shop_authorized_list);

		var ids = his_grid.getSelectedRowId();
		if(ids!=undefined && ids!="" && ids!=null && ids!=0)
		{
			idxAction=his_grid.getColIndexById('action');
			idxObject=his_grid.getColIndexById('object');
			ids = ids.split(",");
			$.each(ids, function(i, id)
			{
				action=(his_grid.cells(id,idxAction).getValue());
				object=(his_grid.cells(id,idxObject).getValue());
			});

			if (action == 'modification')
			{
				var is_object_inArray = global_authorized_array.indexOf(object);
				if (is_object_inArray >= 0)
				{
					clickable = true;
				}
			} else {clickable = false;}

			if (clickable == false)
			{
				his_tb.disableItem('history_rollback');
			} else
			{
				his_tb.enableItem('history_rollback');
			}
		}
	});

	his_tb.attachEvent("onClick",
		function(id)
		{
			if (id == 'refresh')
			{
				displayHistory();
			}
			else if (id == 'delete')
			{
				if (confirm('<?php echo _l('This action will delete all history, do you confirm this action?', 1)?>'))
				{
					$.get('index.php?ajax=1&act=all_changehistory_delete', function ()
					{
						displayHistory();
					});
				}
			}
			else if (id == 'history_rollback')
			{
				if(confirm('<?php echo _l('This action will go back to an oldest version,do you confirm this action?',1)?>'))
				{
					$.post("index.php?ajax=1&act=all_changehistory_backup&id_lang=" + SC_ID_LANG, {ids: his_grid.getSelectedRowId()}, function (data)
					{
						if (data == 1)
						{
							dhtmlx.message({
								text: '<?php echo _l('Rollback executed', 1)?>',
								type: 'info',
								expire: 10000
							});
						}
						else {
							dhtmlx.message({
								text: '<?php echo _l('Rollback not executed, check the data compliance', 1)?>',
								type: 'error',
								expire: 10000
							});
						}
					});
				}
			}
			else if (id=='exportcsv'){
				his_grid.enableCSVHeader(true);
				his_grid.setCSVDelimiter("\t");
				var csv=his_grid.serializeToCSV(true);
				displayQuickExportWindow(csv,1);
			}
		});

	function displayHistory(callback)
	{
		his_grid.clearAll();
		his_grid_sb.setText('');
		his_grid_sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');
		his_grid.load("index.php?ajax=1&act=all_changehistory_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
	    getRowsNum=his_grid.getRowsNum();
	 		his_grid_sb.setText(getRowsNum+' '+(getRowsNum>1?'<?php echo _l('actions',1)?>':'<?php echo _l('action')?>'));
	    his_grid.filterByAll();
			if (callback!='') eval(callback);
			});
	}

	displayHistory();
	//lHistory.cells('b').collapse();
</script>