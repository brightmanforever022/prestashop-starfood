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
 
	if(_r("GRI_ORD_PROPERTIES_GRID_ORDERS")) 
	{
?>
	prop_tb.addListOption('panel', 'orderorders', 1, "button", '<?php echo _l('Orders and products',1)?>', "lib/img/cart.png");
	allowed_properties_panel[allowed_properties_panel.length] = "orderorders";

	prop_tb.addButton("orderorders_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('orderorders_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitOrderOrders = 1;
	function initOrderOrders(){
		if (needinitOrderOrders)
		{
			prop_tb._orderOrdersLayout = dhxLayout.cells('b').attachLayout('2E');
			prop_tb._orderOrdersLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._orderOrdersGrid = prop_tb._orderOrdersLayout.cells('a').attachGrid();
			prop_tb._orderOrdersGrid.setImagePath("lib/js/imgs/");
			prop_tb._orderOrdersGrid.enableMultiselect(true);
			
			// UISettings
			prop_tb._orderOrdersGrid._uisettings_prefix='ord_orders';
			prop_tb._orderOrdersGrid._uisettings_name=prop_tb._orderOrdersGrid._uisettings_prefix;
		   	prop_tb._orderOrdersGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._orderOrdersGrid);
			
			prop_tb._orderOrdersGrid.attachEvent("onRowSelect",function (idorder){
				if (propertiesPanel=='orderorders' && !dhxLayout.cells('b').isCollapsed()){
					displayOrderOrdersProducts();
				}
			});
			
			prop_tb._orderOrdersLayout.cells('b').setText('<?php echo _l('Products',1)?>');
			prop_tb._orderProductGrid = prop_tb._orderOrdersLayout.cells('b').attachGrid();
			prop_tb._orderProductGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._orderProductGrid._uisettings_prefix='ord_orders_products';
			prop_tb._orderProductGrid._uisettings_name=prop_tb._orderProductGrid._uisettings_prefix;
		   	prop_tb._orderProductGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._orderProductGrid);
			
			needinitOrderOrders=0;
		}
	}


	function setPropertiesPanel_orderOrders(id){
		if (id=='orderorders')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxLastname=ord_grid.getColIndexById('lastname');
				idxFirstname=ord_grid.getColIndexById('firstname');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderSelID,idxFirstname).getValue()+" "+ord_grid.cells(lastOrderSelID,idxLastname).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('orderorders_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Orders and products',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/cart.png');
			needinitOrderOrders = 1;
			initOrderOrders();
			propertiesPanel='orderorders';
			if (lastOrderSelID!=0)
			{
				displayOrderOrders();
			}
		}
		if (id=='orderorders_refresh')
		{
			displayOrderOrders();
			prop_tb._orderProductGrid.clearAll(true);
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_orderOrders);


	function displayOrderOrders()
	{
		var customers_id = "";
		idxIdCustomer=ord_grid.getColIndexById('id_customer');
		$.each( ord_grid.getSelectedRowId().split(','), function( num, rowid ) {
			if(customers_id!="")
				customers_id = customers_id+",";
			customers_id = customers_id+ord_grid.cells(rowid,idxIdCustomer).getValue();
		});
		prop_tb._orderOrdersGrid.clearAll(true);
		prop_tb._orderOrdersGrid.loadXML("index.php?ajax=1&act=ord_orders_get&id_customer="+customers_id+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._orderOrdersGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._orderOrdersGrid);
					
					// UISettings
					prop_tb._orderOrdersGrid._first_loading=0;
		});
	}

	function displayOrderOrdersProducts()
	{
		prop_tb._orderProductGrid.clearAll(true);
		prop_tb._orderProductGrid.loadXML("index.php?ajax=1&act=ord_orders_products_get&id_order="+prop_tb._orderOrdersGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._orderProductGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._orderProductGrid);
					
					// UISettings
					prop_tb._orderProductGrid._first_loading=0;
		});
	}
	


	ord_grid.attachEvent("onRowSelect",function (){
			if (propertiesPanel=='orderorders' && !dhxLayout.cells('b').isCollapsed()){
				displayOrderOrders();
			}
		});

<?php
	} // end permission
?>