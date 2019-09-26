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
 if(_r("GRI_CAT_PROPERTIES_GRID_CUSTOMERBYPRODUCT")) { ?>
		prop_tb.addListOption('panel', 'customerbyproduct', 16, "button", '<?php echo _l('Customers',1)?>', "lib/img/user.png");
		allowed_properties_panel[allowed_properties_panel.length] = "customerbyproduct";

	
	prop_tb.addButton("customerbyproduct_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('customerbyproduct_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButton("customerbyproduct_view_customer_ps", 100, "", "lib/img/user_ps.png", "lib/img/user_ps.png");
	prop_tb.setItemToolTip('customerbyproduct_view_customer_ps','<?php echo _l('View selected customers in Prestashop',1)?>');
	prop_tb.addButton("customerbyproduct_view_order_ps", 100, "", "lib/img/cart_ps.png", "lib/img/cart_ps.png");
	prop_tb.setItemToolTip('customerbyproduct_view_customer_ps','<?php echo _l('View selected customers in Prestashop',1)?>');
	prop_tb.addButton("customerbyproduct_view_order", 100, "", "lib/img/cart.png", "lib/img/cart.png");
	prop_tb.setItemToolTip('customerbyproduct_view_order','<?php echo _l('View selected orders in StoreCommander',1)?>');
	prop_tb.addButton("customerbyproduct_exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	prop_tb.setItemToolTip('customerbyproduct_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter. Please read help to enable clipboard access for Store Commander.',1)?>');
	customerbyproductFilter=0;


	needInitCustomerByProduct = 1;
	function initCustomerByProduct(){
		if (needInitCustomerByProduct)
		{
			prop_tb._customerByProductLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._customerByProductLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._customerByProductGrid = prop_tb._customerByProductLayout.cells('a').attachGrid();
			prop_tb._customerByProductGrid._name='_customerByProductGrid';
			prop_tb._customerByProductGrid.setImagePath("lib/js/imgs/");
			prop_tb._customerByProductGrid.enableMultiselect(true);
            prop_tb._customerByProductGrid.attachEvent("onRowSelect",doOnRowSelected);

             prop_tb._customerByProductGrid.attachEvent("onFilterEnd", function(elements){
                getCusGridStat();
             });
             prop_tb._customerByProductGrid.attachEvent("onSelectStateChanged", function(id){
                 getCusGridStat();
             });
			
			// UISettings
			prop_tb._customerByProductGrid._uisettings_prefix='cat_customerbyproduct';
			prop_tb._customerByProductGrid._uisettings_name=prop_tb._customerByProductGrid._uisettings_prefix;
		   	prop_tb._customerByProductGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._customerByProductGrid);
			
			customerbyproductFilter=0;
			needInitCustomerByProduct=0;
		}
	}

    function doOnRowSelected() {
        getCusGridStat();
    }

	function setPropertiesPanel_customerbyproduct(id){
		if (id=='customerbyproduct')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('customerbyproduct_view_customer_ps');
			prop_tb.showItem('customerbyproduct_view_order_ps');
			prop_tb.showItem('customerbyproduct_view_order');
			prop_tb.showItem('customerbyproduct_exportcsv');
			prop_tb.showItem('customerbyproduct_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Customers',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/user.png');
			needInitCustomerByProduct=1; 
			initCustomerByProduct();
			propertiesPanel='customerbyproduct';
			if (lastProductSelID!=0)
			{
				displayCustomers();
			}
		}
		if (id=='customerbyproduct_exportcsv')
		{
			prop_tb._customerByProductGrid.enableCSVHeader(true);
			prop_tb._customerByProductGrid.setCSVDelimiter("\t");
			var csv=prop_tb._customerByProductGrid.serializeToCSV(true);
			displayQuickExportWindow(csv);
		}
		if (id=='customerbyproduct_view_order_ps'){
			var sel=prop_tb._customerByProductGrid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				for (var i=0;i<tabId.length;i++)
				{
					idxIdOrder=prop_tb._customerByProductGrid.getColIndexById('id_order');
					id_order=prop_tb._customerByProductGrid.cells(tabId[i],idxIdOrder).getValue();
					if (mustOpenBrowserTab){
						window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders');?>");
					}else{
					 	<?php  if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
		 					wViewOrder = dhxWins.createWindow(i+"wViewOrder"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
					 	<?php  } else { ?>
		 					wViewOrder = dhxWins.createWindow(i+"wViewOrder"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
					 	<?php  } ?>
						wViewOrder.setText('<?php echo _l('Order',1)?> '+id_order);
						wViewOrder.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders');?>");
					}
				}
			}
		}
	 	if (id=='customerbyproduct_view_order') {
			var sel=prop_tb._customerByProductGrid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				for (var i=0;i<tabId.length;i++)
				{
					idxIdOrder=prop_tb._customerByProductGrid.getColIndexById('id_order');
					id_order=prop_tb._customerByProductGrid.cells(tabId[i],idxIdOrder).getValue();
					if (id_order!='' && id_order!=null)
					{
						var url = "?page=ord_tree&open_ord="+id_order;
						window.open(url,'_blank');
					}
				}
			}
		}
		if (id=='customerbyproduct_view_customer_ps'){
			var sel=prop_tb._customerByProductGrid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				for (var i=0;i<tabId.length;i++)
				{
					idxIdCustomer=prop_tb._customerByProductGrid.getColIndexById('id_customer');
					id_customer=prop_tb._customerByProductGrid.cells(tabId[i],idxIdCustomer).getValue();
					if (mustOpenBrowserTab){
						window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
					}else{
					 	<?php  if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
	 						wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
					 	<?php  } else { ?>
							wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
	 					<?php  } ?>
						wViewCustomer.setText('<?php echo _l('Customer',1)?> '+id_customer);
						wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
					}
				}
			}
		}
		if (id=='customerbyproduct_refresh')
		{
			displayCustomers();
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_customerbyproduct);

	function displayCustomers()
	{
		prop_tb._customerByProductGrid.clearAll(true);
		prop_tb._customerByProductGrid.loadXML("index.php?ajax=1&act=cat_customerbyproduct_get&ids="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
				{
                    prop_tb._customerByProductGrid._rowsNum=prop_tb._customerByProductGrid.getRowsNum();
					nb = 0;
					prop_tb._customerByProductGrid.forEachRow(function(id){
				      nb = nb*1 + 1;
				   });
                    getCusGridStat();

		    		// UISettings
					loadGridUISettings(prop_tb._customerByProductGrid);
					
					// UISettings
					prop_tb._customerByProductGrid._first_loading=0;
				});
	}

    function getCusGridStat(){
        var filteredRows=prop_tb._customerByProductGrid.getRowsNum();
        var selectedRows=(prop_tb._customerByProductGrid.getSelectedRowId()?prop_tb._customerByProductGrid.getSelectedRowId().split(',').length:0);
        prop_tb._sb.setText(prop_tb._customerByProductGrid._rowsNum+' '+(prop_tb._customerByProductGrid._rowsNum>1?'<?php echo _l('customers')?>':'<?php echo _l('customer')?>')+" - <?php echo _l('Filter')._l(':')?> "+filteredRows+" - <?php echo _l('Selection')._l(':')?> "+selectedRows);
    }

	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='customerbyproduct')
			{
				if (cat_grid.getSelectedRowId()!=null)
					displayCustomers();
			}
		});

	<?php } /* end permission */ ?>
