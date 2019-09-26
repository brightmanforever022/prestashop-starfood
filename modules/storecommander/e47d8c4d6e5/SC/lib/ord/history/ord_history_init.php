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
 if(_r("GRI_ORD_PROPERTIES_GRID_ORDERHISTORY")) { ?>
	prop_tb.addListOption('panel', 'orderhistory', 2, "button", '<?php echo _l('Order history',1)?>', "lib/img/text_list_numbers.png");
	allowed_properties_panel[allowed_properties_panel.length] = "orderhistory";

	prop_tb.addButton("orderhistory_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('orderhistory_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitOrderHistory = 1;
	function initOrderHistory(){
		if (needinitOrderHistory)
		{
			prop_tb._orderHistoryLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._orderHistoryLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._orderHistoryGrid = prop_tb._orderHistoryLayout.cells('a').attachGrid();
			prop_tb._orderHistoryGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._orderHistoryGrid._uisettings_prefix='ord_history';
			prop_tb._orderHistoryGrid._uisettings_name=prop_tb._orderHistoryGrid._uisettings_prefix;
		   	prop_tb._orderHistoryGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._orderHistoryGrid);
			
			needinitOrderHistory=0;
		}
	}


	function setPropertiesPanel_orderhistory(id){
		if (id=='orderhistory')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxOrderID=ord_grid.getColIndexById('id_order');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderRowSelID,idxOrderID).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('orderhistory_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Order history',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/text_list_numbers.png');
			needinitOrderHistory = 1;
			initOrderHistory();
			propertiesPanel='orderhistory';
			if (lastOrderSelID!=0)
			{
				displayOrderHistory();
			}
		}
		if (id=='orderhistory_refresh')
		{
			displayOrderHistory();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_orderhistory);


	function displayOrderHistory()
	{
		prop_tb._orderHistoryGrid.clearAll(true);
		prop_tb._orderHistoryGrid.loadXML("index.php?ajax=1&act=ord_history_get&id_order="+lastOrderSelID,function()
				{
					nb=prop_tb._orderHistoryGrid.getRowsNum();
					prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._orderHistoryGrid);
					
					// UISettings
					prop_tb._orderHistoryGrid._first_loading=0;
				});
	}
	


	ord_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='orderhistory' && !dhxLayout.cells('b').isCollapsed()){
				displayOrderHistory();
			}
		});

<?php
	} // end permission
?>