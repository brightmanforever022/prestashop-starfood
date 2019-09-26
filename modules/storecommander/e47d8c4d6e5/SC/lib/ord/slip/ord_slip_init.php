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
 
	if(_r("GRI_ORD_PROPERTIES_GRID_SLIP")) 
	{
?>
	prop_tb.addListOption('panel', 'orderslip', 0, "button", '<?php echo _l('Slips',1)?>', "lib/img/money_delete.png");
	allowed_properties_panel[allowed_properties_panel.length] = "orderslip";

	prop_tb.addButton("orderslip_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('orderslip_refresh','<?php echo _l('Refresh grid',1)?>');
	/*prop_tb.addButton('orderslip_add',100,'','lib/img/add.png','lib/img/add.png');
	prop_tb.setItemToolTip('orderslip_add','<?php echo _l('Create new slip',1)?>');*/


	lastOrderSlipSelID = 0;

	needinitOrderSlip = 1;
	function initOrderSlip(){
		if (needinitOrderSlip)
		{
			prop_tb._orderSlipLayout = dhxLayout.cells('b').attachLayout('2E');
			prop_tb._orderSlipLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._orderSlipGrid = prop_tb._orderSlipLayout.cells('a').attachGrid();
			prop_tb._orderSlipGrid.setImagePath("lib/js/imgs/");
			prop_tb._orderSlipGrid.enableMultiselect(true);
			
			// UISettings
			prop_tb._orderSlipGrid._uisettings_prefix='ord_slip';
			prop_tb._orderSlipGrid._uisettings_name=prop_tb._orderSlipGrid._uisettings_prefix;
		  prop_tb._orderSlipGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._orderSlipGrid);

			function onEditCellOrderSlip(stage,rId,cInd,nValue,oValue){
					if(stage==2)
					{
						$.post("index.php?ajax=1&act=ord_slip_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_order_slip': rId, 'col': prop_tb._orderSlipGrid.getColumnId(cInd), val: nValue.replace(/#/g,'')},function(data){});
					}
					return true;
			}
			prop_tb._orderSlipGrid.attachEvent("onEditCell",onEditCellOrderSlip);


			prop_tb._orderSlipGrid.attachEvent("onRowSelect",function (idorder){
				if (propertiesPanel=='orderslip' && !dhxLayout.cells('b').isCollapsed()){
					if (lastOrderSlipSelID != prop_tb._orderSlipGrid.getSelectedRowId()){
						lastOrderSlipSelID = prop_tb._orderSlipGrid.getSelectedRowId();
						displayOrderSlipDetail();
					}
				}
			});
			
			prop_tb._orderSlipLayout.cells('b').setText('<?php echo _l('Products',1)?>');
			prop_tb._slipDetailGrid = prop_tb._orderSlipLayout.cells('b').attachGrid();
			prop_tb._slipDetailGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._slipDetailGrid._uisettings_prefix='ord_slip_detail';
			prop_tb._slipDetailGrid._uisettings_name=prop_tb._slipDetailGrid._uisettings_prefix;
		   	prop_tb._slipDetailGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._slipDetailGrid);


			function onEditCellOrderSlipDetail(stage,rId,cInd,nValue,oValue){
					if(stage==2)
					{
						$.post("index.php?ajax=1&act=ord_slip_detail_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_order_slip__id_order_detail': rId, 'col': prop_tb._slipDetailGrid.getColumnId(cInd), val: nValue.replace(/#/g,'')},function(data){});
					}
					return true;
			}
			prop_tb._slipDetailGrid.attachEvent("onEditCell",onEditCellOrderSlipDetail);

			needinitOrderSlip=0;
		}
	}


	function setPropertiesPanel_orderSlip(id){
		if (id=='orderslip')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxLastname=ord_grid.getColIndexById('lastname');
				idxFirstname=ord_grid.getColIndexById('firstname');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderSelID,idxFirstname).getValue()+" "+ord_grid.cells(lastOrderSelID,idxLastname).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('orderslip_refresh');
			prop_tb.showItem('orderslip_add');
			prop_tb.setItemText('panel', '<?php echo _l('Slips',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/money_delete.png');
			needinitOrderSlip = 1;
			initOrderSlip();
			propertiesPanel='orderslip';
			if (lastOrderSelID!=0)
			{
				displayOrderSlip();
			}
		}
		if (id=='orderslip_refresh')
		{
			lastOrderSlipSelID = 0;
			displayOrderSlip();
			prop_tb._slipDetailGrid.clearAll(true);
		}
		if (id=='orderslip_add')
		{
			if (lastOrderSelID==0){
				alert('<?php echo _l('Please select an order',1)?>');
			}else if(confirm('sure?'))
			{
					$.post("index.php?ajax=1&act=ord_slip_update&action=insert&id_order="+lastOrderSelID+"&id_lang="+SC_ID_LANG,{ id_order: lastOrderSelID },function(){
							displayOrderSlip();
						});
			}

		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_orderSlip);


	function displayOrderSlip()
	{
		var customers_id = "";
		idxIdCustomer=ord_grid.getColIndexById('id_customer');
		$.each( ord_grid.getSelectedRowId().split(','), function( num, rowid ) {
			if(customers_id!="")
				customers_id = customers_id+",";
			customers_id = customers_id+ord_grid.cells(rowid,idxIdCustomer).getValue();
		});
		prop_tb._orderSlipGrid.clearAll(true);
		prop_tb._orderSlipGrid.loadXML("index.php?ajax=1&act=ord_slip_get&id_customer="+customers_id+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._orderSlipGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		  // UISettings
			loadGridUISettings(prop_tb._orderSlipGrid);
					
			// UISettings
			prop_tb._orderSlipGrid._first_loading=0;
		});
	}

	function displayOrderSlipDetail()
	{
		prop_tb._slipDetailGrid.clearAll(true);
		prop_tb._slipDetailGrid.loadXML("index.php?ajax=1&act=ord_slip_detail_get&id_order_slip="+prop_tb._orderSlipGrid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._slipDetailGrid.getRowsNum();
			prop_tb._sb.setText('');
				
		  // UISettings
			loadGridUISettings(prop_tb._slipDetailGrid);
					
			// UISettings
			prop_tb._slipDetailGrid._first_loading=0;
		});
	}
	


	ord_grid.attachEvent("onRowSelect",function (){
			if (propertiesPanel=='orderslip' && !dhxLayout.cells('b').isCollapsed()){
					displayOrderSlip();
			}
		});

<?php
	} // end permission
?>