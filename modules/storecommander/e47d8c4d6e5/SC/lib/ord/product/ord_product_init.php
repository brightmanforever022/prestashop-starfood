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
 

	if(_r("GRI_ORD_PROPERTIES_GRID_PRODUCT")) 
	{ 
?>
	prop_tb.addListOption('panel', 'orderproduct', 1, "button", '<?php echo _l('Products',1)?>', "lib/img/bricks.png");
	allowed_properties_panel[allowed_properties_panel.length] = "orderproduct";

	prop_tb.addButton("orderproduct_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('orderproduct_refresh','<?php echo _l('Refresh grid',1)?>');

	prop_tb.addButton("exportcsv", 101, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	prop_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');

	prop_tb.addButton("gotocatalog", 101, "", "lib/img/table_go.png", "lib/img/table_go.png");
	prop_tb.setItemToolTip('gotocatalog','<?php echo _l('Go to the product in catalog.')?>');


	needinitOrderProduct = 1;
	function initOrderProduct(){
		if (needinitOrderProduct)
		{
			prop_tb._orderProductLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._orderProductLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._orderProductGrid = prop_tb._orderProductLayout.cells('a').attachGrid();
			prop_tb._orderProductGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._orderProductGrid._uisettings_prefix='ord_product';
			prop_tb._orderProductGrid._uisettings_name=prop_tb._orderProductGrid._uisettings_prefix;
		   	prop_tb._orderProductGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._orderProductGrid);
			
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
?>
			prop_tb._orderProductGrid_sb=prop_tb._orderProductLayout.cells('a').attachStatusBar();
			prop_tb._orderProductGrid_sb.setText('<span style="color:#CC0000"><?php echo _l('Warning: Store Commander doesn\'t recalculate order\'s totals.',1)?></span></>');
<?php
}
?>

			orderProductDataProcessorURLBase="index.php?ajax=1&act=ord_product_update&id_order="+lastOrderSelID+"&id_lang="+SC_ID_LANG;
			orderProductDataProcessor = new dataProcessor(orderProductDataProcessorURLBase);
			orderProductDataProcessor.enableDataNames(true);
			orderProductDataProcessor.enablePartialDataSend(false);
			orderProductDataProcessor.setTransactionMode("GET");
			orderProductDataProcessor.setUpdateMode('cell',true);
			orderProductDataProcessor.serverProcessor=orderProductDataProcessorURLBase;
			orderProductDataProcessor.init(prop_tb._orderProductGrid);
			
			needinitOrderProduct=0;
		}
	}


	function setPropertiesPanel_orderproduct(id){
		if (id=='orderproduct')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxOrderID=ord_grid.getColIndexById('id_order');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderRowSelID,idxOrderID).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('exportcsv');
			prop_tb.showItem('orderproduct_refresh');
			prop_tb.showItem('gotocatalog');
			prop_tb.setItemText('panel', '<?php echo _l('Products',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/bricks.png');
			needinitOrderProduct = 1;
			initOrderProduct();
			propertiesPanel='orderproduct';
			if (lastOrderSelID!=0)
			{
				displayOrderProducts();
			}
		}
		if(id=='gotocatalog')
		{
			selection=prop_tb._orderProductGrid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				var rowIds = selection.split(",");
				var rowId = rowIds[0];
		
				var open_cat_grid_ids  = prop_tb._orderProductGrid.getUserData(rowId, "open_cat_grid");
				if (open_cat_grid_ids!='' && open_cat_grid_ids!=null)
				{
					var url = "?page=cat_tree&open_cat_grid="+open_cat_grid_ids;
					window.open(url,'_blank');
				}
			}
		}
		if (id=='orderproduct_refresh')
		{
			displayOrderProducts();
		}
		if (id=='exportcsv'){
			prop_tb._orderProductGrid.enableCSVHeader(true);
			prop_tb._orderProductGrid.setCSVDelimiter("\t");
			var csv=prop_tb._orderProductGrid.serializeToCSV(true);
			displayQuickExportWindow(csv,1);
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_orderproduct);


	function displayOrderProducts()
	{
		prop_tb._orderProductGrid.clearAll(true);
		prop_tb._orderProductGrid.loadXML("index.php?ajax=1&act=ord_product_get&id_order="+lastOrderSelIDs,function()
				{
					nb=prop_tb._orderProductGrid.getRowsNum();
					prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._orderProductGrid);
					
					// UISettings
					prop_tb._orderProductGrid._first_loading=0;
				});
	}
	


	ord_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='orderproduct' && !dhxLayout.cells('b').isCollapsed()){
				displayOrderProducts();
			}
		});

<?php
	} // end permission
?>