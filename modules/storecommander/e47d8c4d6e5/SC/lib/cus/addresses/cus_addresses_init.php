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
 

	if(_r("GRI_CUS_PROPERTIES_GRID_ADDRESS")) 
	{
?>
	prop_tb.addListOption('panel', 'customeraddress', 2, "button", '<?php echo _l('Addresses',1)?>', "lib/img/email_edit.png");
	allowed_properties_panel[allowed_properties_panel.length] = "customeraddress";

	prop_tb.addButton("customeraddress_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('customeraddress_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitCustomerAddress = 1;
	function initCustomerAddress(){
		if (needinitCustomerAddress)
		{
			prop_tb._customerAddressLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._customerAddressLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._customerAddressGrid = prop_tb._customerAddressLayout.cells('a').attachGrid();
			prop_tb._customerAddressGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._customerAddressGrid._uisettings_prefix='cus_addresses';
			prop_tb._customerAddressGrid._uisettings_name=prop_tb._customerAddressGrid._uisettings_prefix;
		   	prop_tb._customerAddressGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._customerAddressGrid);
		
			customerAddressDataProcessorURLBase="index.php?ajax=1&act=cus_addresses_update&id_lang="+SC_ID_LANG;
			customerAddressDataProcessor = new dataProcessor(customerAddressDataProcessorURLBase);
			customerAddressDataProcessor.enableDataNames(true);
			customerAddressDataProcessor.enablePartialDataSend(true);
			customerAddressDataProcessor.setTransactionMode("POST");
			customerAddressDataProcessor.setUpdateMode('cell',true);
			customerAddressDataProcessor.serverProcessor=customerAddressDataProcessorURLBase;
			customerAddressDataProcessor.init(prop_tb._customerAddressGrid);
			
			needinitCustomerAddress=0;
		}
	}


	function setPropertiesPanel_customeraddress(id){
		if (id=='customeraddress')
		{
			if(lastCustomerSelID!=undefined && lastCustomerSelID!="")
			{
				idxLastname=cus_grid.getColIndexById('lastname');
				idxFirstname=cus_grid.getColIndexById('firstname');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cus_grid.cells(lastCustomerSelID,idxFirstname).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('customeraddress_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Addresses',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/email_edit.png');
			needinitCustomerAddress = 1;
			initCustomerAddress();
			propertiesPanel='customeraddress';
			if (lastCustomerSelID!=0)
			{
				displayCustomerAddresses();
			}
		}
		if (id=='customeraddress_refresh')
		{
			displayCustomerAddresses();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_customeraddress);


	function displayCustomerAddresses()
	{
		var customers_id = "";
		if(gridView!="grid_address")
			customers_id = cus_grid.getSelectedRowId();
		else
		{
			idxIdCustomer=cus_grid.getColIndexById('id_customer');
			$.each( cus_grid.getSelectedRowId().split(','), function( num, rowid ) {
				if(customers_id!="")
					customers_id = customers_id+",";
					
				customers_id = customers_id+cus_grid.cells(rowid,idxIdCustomer).getValue();
			});
		}
		prop_tb._customerAddressGrid.clearAll(true);
		$.post("index.php?ajax=1&act=cus_addresses_get&id_lang="+SC_ID_LANG,{'id_customer': customers_id},function(data)
		{
			prop_tb._customerAddressGrid.parse(data);
			nb=prop_tb._customerAddressGrid.getRowsNum();
			prop_tb._sb.setText('');
		
    	// UISettings
			loadGridUISettings(prop_tb._customerAddressGrid);
			
			// UISettings
			prop_tb._customerAddressGrid._first_loading=0;
				});
	}
	


	cus_grid.attachEvent("onRowSelect",function (idcustomer){
			if (propertiesPanel=='customeraddress' && !dhxLayout.cells('b').isCollapsed()){
				displayCustomerAddresses();
			}
		});

<?php
	} // end permission
?>