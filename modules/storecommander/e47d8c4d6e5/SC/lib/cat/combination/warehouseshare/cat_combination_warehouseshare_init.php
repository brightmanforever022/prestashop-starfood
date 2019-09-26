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
 if(SCAS && _r("ACT_CAT_ADVANCED_STOCK_MANAGEMENT")) { ?>

// INITIALISATION TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id){
	if (id=='combinations')
	{
		prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', 'combi_warehouses', 9, "button", '<?php echo _l('Warehouses',1)?>', "lib/img/building.png");

		prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
			if(id=="combi_warehouses")
			{
				hideSubpropertiesItems();
				prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo _l('Warehouses',1)?>');
				prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', 'lib/img/building.png');
				actual_subproperties = "combi_warehouses";
				initCombinationWarehouseshare();
			}
		});
				
		prop_tb._combinationsGrid.attachEvent("onRowSelect", function(id,ind){
			if (!prop_tb._combinationsLayout.cells('b').isCollapsed())
			{
				if(actual_subproperties == "combi_warehouses"){
			 		getCombinationsWarehouseshares();
				}
			}
		});
		
		prop_tb.combi_subproperties_tb.addButton("warehouseshare_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('warehouseshare_refresh','<?php echo _l('Refresh grid',1)?>');
		prop_tb.combi_subproperties_tb.addButton("warehouseshare_add_select", 100, "", "lib/img/building_add.png", "lib/img/building_add.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('warehouseshare_add_select','<?php echo _l('Add all selected products to all selected warehouses',1)?>');
		prop_tb.combi_subproperties_tb.addButton("warehouseshare_del_select", 100, "", "lib/img/building_delete.png", "lib/img/building_delete.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('warehouseshare_del_select','<?php echo _l('Delete all selected products from all selected warehouses',1)?>');
		//hideSubpropertiesItems();
			
		prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
				if (id=='warehouseshare_add_select')
				{
					if(prop_tb._combinationsWarehousesGrid.getSelectedRowId()!="" && prop_tb._combinationsWarehousesGrid.getSelectedRowId()!=null)
					{
						$.post("index.php?ajax=1&act=cat_combination_warehouseshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId(), "id_warehouse":prop_tb._combinationsWarehousesGrid.getSelectedRowId()},function(data){
							/*var doDisplay = true;
							if(prop_tb._combinationsWarehousesGrid.getSelectedRowId()!=null)
							{
								var warehouses=prop_tb._combinationsWarehousesGrid.getSelectedRowId().split(',');
								$.each( warehouses, function( num, warehouseId ) {
									if(warehouseId==warehouseselection)
									{
										displayProducts('displayCombinations();');
										doDisplay = false;
									}
								});
								if(doDisplay==true)
									getCombinationsWarehouseshares();
								}*/
							getCombinationsWarehouseshares();
							combiWriteRefresh();
						});
					}
				}
				if (id=='warehouseshare_del_select')
				{
					if(prop_tb._combinationsWarehousesGrid.getSelectedRowId()!="" && prop_tb._combinationsWarehousesGrid.getSelectedRowId()!=null)
					{
						$.post("index.php?ajax=1&act=cat_combination_warehouseshare_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId(), "id_warehouse":prop_tb._combinationsWarehousesGrid.getSelectedRowId()},function(data){
							/*var doDisplay = true;
							if(prop_tb._combinationsWarehousesGrid.getSelectedRowId())
							{
								var warehouses=prop_tb._combinationsWarehousesGrid.getSelectedRowId().split(',');
								$.each( warehouses, function( num, warehouseId ) {
									if(warehouseId==warehouseselection)
									{
										displayProducts('displayCombinations();');
										doDisplay = false;
									}
								});
								if(doDisplay==true)
									getCombinationsWarehouseshares();
							}*/
							getCombinationsWarehouseshares();
							combiWriteRefresh();
						});
					}
				}
				if (id=='warehouseshare_refresh')
				{
					getCombinationsWarehouseshares();
				}
		});
	}
});
			
// INIT GRID
function initCombinationWarehouseshare()
{
	 hideSubpropertiesItems();
	prop_tb.combi_subproperties_tb.showItem('warehouseshare_refresh');
	prop_tb.combi_subproperties_tb.showItem('warehouseshare_add_select');
	prop_tb.combi_subproperties_tb.showItem('warehouseshare_del_select');
	
	prop_tb._combinationsWarehousesGrid = prop_tb._combinationsLayout.cells('b').attachGrid();
	prop_tb._combinationsWarehousesGrid.setImagePath("lib/js/imgs/");
	
	prop_tb._combinationsWarehousesGrid.enableDragAndDrop(false);
	prop_tb._combinationsWarehousesGrid.enableMultiselect(true);
	/*prop_tb._combinationsWarehousesGrid.enableAutoSaving('cg_cat_warehouseshare_combi',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
	prop_tb._combinationsWarehousesGrid.enableAutoHiddenColumnsSaving('cg_cat_warehouseshare_combi_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/

	// UISettings
	prop_tb._combinationsWarehousesGrid._uisettings_prefix='cat_combination_warehouseshare';
	prop_tb._combinationsWarehousesGrid._uisettings_name=prop_tb._combinationsWarehousesGrid._uisettings_prefix;
   	prop_tb._combinationsWarehousesGrid._first_loading=1;
   	
	// UISettings
	initGridUISettings(prop_tb._combinationsWarehousesGrid);
	
	prop_tb._combinationsWarehousesGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
	{
		if(stage==1)
		{
			idxPresent=prop_tb._combinationsWarehousesGrid.getColIndexById('present');
		
			var action = "";
			if(cInd==idxPresent)
				action = "present";
			
			if(action=="present")
			{
				var value = prop_tb._combinationsWarehousesGrid.cells(rId,cInd).isChecked();
				$.post("index.php?ajax=1&act=cat_combination_warehouseshare_update&id_warehouse="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
					/*if(rId==warehouseselection)
						displayProducts('displayCombinations();');
					else*/
						getCombinationsWarehouseshares();
					combiWriteRefresh();
				});
			}
			
		}
		else if(stage==2)
		{
			idxLocation=prop_tb._combinationsWarehousesGrid.getColIndexById('location');
			if(idxLocation!=undefined && idxLocation!=null )
			{
				idxPresent=prop_tb._combinationsWarehousesGrid.getColIndexById('present');
				var action = "";
				if(cInd==idxLocation)
					action = "location";
				
				if(action=="location")
				{
					var value = prop_tb._combinationsWarehousesGrid.cells(rId,cInd).getValue();
					$.post("index.php?ajax=1&act=cat_combination_warehouseshare_update&id_warehouse="+rId+"&action="+action+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"value":value,"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
						if(!prop_tb._combinationsWarehousesGrid.cells(rId,idxPresent).isChecked())
							getCombinationsWarehouseshares();
					});
				}
			}
		}
		return true;
	});
	
	prop_tb._combinationsWarehousesGrid.attachEvent("onRowDblClicked", function(rId,cInd){
		idxASM=cat_grid.getColIndexById('advanced_stock_management');
		
		idxQty=prop_tb._combinationsWarehousesGrid.getColIndexById('quantity');
		idxPresent=prop_tb._combinationsWarehousesGrid.getColIndexById('present');		

		if(cInd==idxQty && prop_tb._combinationsGrid.getSelectedRowId().split(",").length==1)
		{
			if(cat_grid.getUserData(cat_grid.getSelectedRowId(),"type_advanced_stock_management")=="2" && prop_tb._combinationsWarehousesGrid.cells(rId,idxPresent).isChecked())
			{
				if (!dhxWins.isWindow("wStockMvt"))
				{
					wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-200), 50, 430, 600);
					wStockMvt.setIcon('lib/img/building.png','../../../lib/img/building.png');
					wStockMvt.setText("<?php echo _l('Create a new stock movement')?>");
					wStockMvt.show();
					$.post("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product="+lastProductSelID+"&id_warehouse="+rId+"&id_lang="+SC_ID_LANG,{"id_product_attribute":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
							$('#jsExecute').html(data);
						});
				}else{
					wStockMvt.setDimension(430, 600);
					wStockMvt.show();
					$.post("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product="+lastProductSelID+"&id_warehouse="+rId+"&id_lang="+SC_ID_LANG,{"id_product_attribute":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
							$('#jsExecute').html(data);
						});
				}
				
				return false;
			}
		}
		return true;
	});
	
	getCombinationsWarehouseshares();
}

function getCombinationsWarehouseshares()
{
	prop_tb._combinationsWarehousesGrid.clearAll(true);
	var tempIdList = (prop_tb._combinationsGrid.getSelectedRowId()!=null?prop_tb._combinationsGrid.getSelectedRowId():"");
	$.post("index.php?ajax=1&act=cat_combination_warehouseshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
	{
		prop_tb._combinationsWarehousesGrid.parse(data);
		nb=prop_tb._combinationsWarehousesGrid.getRowsNum();
		prop_tb._combinationsWarehousesGrid._rowsNum=nb;
		
   	// UISettings
		loadGridUISettings(prop_tb._combinationsWarehousesGrid);
		prop_tb._combinationsWarehousesGrid._first_loading=0;
		
		idxPresent=prop_tb._combinationsWarehousesGrid.getColIndexById('present');
	});
}

<?php } ?>