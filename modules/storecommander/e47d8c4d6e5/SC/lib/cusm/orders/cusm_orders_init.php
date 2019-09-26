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
 
	if(_r("GRI_CUS_PROPERTIES_GRID_ORDERS")) 
	{
?>
	prop_tb.addListOption('panel', 'customerorder', 1, "button", '<?php echo _l('Orders and products',1)?>', "lib/img/cart.png");
	allowed_properties_panel[allowed_properties_panel.length] = "customerorder";

	prop_tb.addButton("customerorder_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('customerorder_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitCustomerOrder = 1;
	function initCustomerOrder(){
		if (needinitCustomerOrder)
		{
			prop_tb._customerOrderLayout = dhxLayout.cells('b').attachLayout('2E');
			prop_tb._customerOrderLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._customerOrderGrid = prop_tb._customerOrderLayout.cells('a').attachGrid();
			prop_tb._customerOrderGrid.setImagePath("lib/js/imgs/");
			prop_tb._customerOrderGrid.enableMultiselect(true);
			
			// UISettings
			prop_tb._customerOrderGrid._uisettings_prefix='cus_orders';
			prop_tb._customerOrderGrid._uisettings_name=prop_tb._customerOrderGrid._uisettings_prefix;
		   	prop_tb._customerOrderGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._customerOrderGrid);
			
			prop_tb._customerOrderGrid.attachEvent("onRowSelect",function (idorder){
				if (propertiesPanel=='customerorder' && !dhxLayout.cells('b').isCollapsed()){
					displayCustomerOrderProducts();
				}
			});
			
			prop_tb._customerOrderLayout.cells('b').setText('<?php echo _l('Products',1)?>');
			prop_tb._customerProductGrid = prop_tb._customerOrderLayout.cells('b').attachGrid();
			prop_tb._customerProductGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._customerProductGrid._uisettings_prefix='cus_orders_products';
			prop_tb._customerProductGrid._uisettings_name=prop_tb._customerProductGrid._uisettings_prefix;
		   	prop_tb._customerProductGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._customerProductGrid);
			
			needinitCustomerOrder=0;
		}
	}


	function setPropertiesPanel_customerorder(id){
		if (id=='customerorder')
		{
			if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
			{
				idxCustomerName=cusm_grid.getColIndexById('customer_name');
			}
			hidePropTBButtons();
			prop_tb.showItem('customerorder_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Orders and products',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/cart.png');
			needinitCustomerOrder = 1;
			initCustomerOrder();
			propertiesPanel='customerorder';
			if (lastCustomerSelID!=0)
			{
				displayCustomerOrders();
			}
		}
		if (id=='customerorder_refresh')
		{
			displayCustomerOrders();
			prop_tb._customerProductGrid.clearAll(true);
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_customerorder);


	function displayCustomerOrders()
	{
		var customers_id = "";
		$.each( cusm_grid.getSelectedRowId().split(','), function( num, rowid ) {
			if(customers_id!="")
				customers_id = customers_id+",";

			customers_id = customers_id+cusm_grid.getUserData(rowid,'id_customer');
		});
		prop_tb._customerOrderGrid.clearAll(true);
		prop_tb._customerOrderGrid.loadXML("index.php?ajax=1&act=cus_orders_get&id_customer="+customers_id+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._customerOrderGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._customerOrderGrid);
					
					// UISettings
					prop_tb._customerOrderGrid._first_loading=0;
		});
	}

	function displayCustomerOrderProducts()
	{
		prop_tb._customerProductGrid.clearAll(true);
		prop_tb._customerProductGrid.loadXML("index.php?ajax=1&act=cus_orders_products_get&id_order="+prop_tb._customerOrderGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._customerProductGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._customerProductGrid);
					
					// UISettings
					prop_tb._customerProductGrid._first_loading=0;
		});
	}
	


	cusm_grid.attachEvent("onRowSelect",function (idcustomer){
			if (propertiesPanel=='customerorder' && !dhxLayout.cells('b').isCollapsed()){
				prop_tb._customerProductGrid.clearAll(true);
				displayCustomerOrders();
			}
		});

<?php
	} // end permission
?>
