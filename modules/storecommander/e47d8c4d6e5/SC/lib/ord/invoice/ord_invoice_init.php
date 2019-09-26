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
if(_r("GRI_ORD_PROPERTIES_GRID_INVOICE"))
{
?>
	prop_tb.addListOption('panel', 'orderinvoice', 0, "button", '<?php echo _l('Invoices',1)?>', "lib/img/calculator.png");
	allowed_properties_panel[allowed_properties_panel.length] = "orderinvoice";

	prop_tb.addButton("orderinvoice_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('orderinvoice_refresh','<?php echo _l('Refresh grid',1)?>');


	lastOrderInvoiceSelID = 0;

	needinitOrderInvoice = 1;
	function initOrderInvoice(){
		if (needinitOrderInvoice)
		{
			prop_tb._orderInvoiceLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._orderInvoiceLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._orderInvoiceGrid = prop_tb._orderInvoiceLayout.cells('a').attachGrid();
			prop_tb._orderInvoiceGrid.setImagePath("lib/js/imgs/");
			prop_tb._orderInvoiceGrid.enableMultiselect(true);
			prop_tb._orderInvoiceGrid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");

			// UISettings
			prop_tb._orderInvoiceGrid._uisettings_prefix='ord_invoice';
			prop_tb._orderInvoiceGrid._uisettings_name=prop_tb._orderInvoiceGrid._uisettings_prefix;
		  prop_tb._orderInvoiceGrid._first_loading=1;

			// UISettings
			initGridUISettings(prop_tb._orderInvoiceGrid);

			function onEditCellOrderInvoice(stage,rId,cInd,nValue,oValue){
					if(stage==2)
					{
						$.post("index.php?ajax=1&act=ord_invoice_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_order_invoice': rId, 'col': prop_tb._orderInvoiceGrid.getColumnId(cInd), val: nValue.replace(/#/g,'')},function(data){});
					}
					return true;
			}
			prop_tb._orderInvoiceGrid.attachEvent("onEditCell",onEditCellOrderInvoice);

			prop_tb._orderInvoiceGrid.attachEvent("onDhxCalendarCreated",function(calendar){
				calendar.loadUserLanguage("<?php echo $user_lang_iso;?>");
			});


			prop_tb._orderInvoiceGrid.attachEvent("onRowSelect",function (idorder){
				if (propertiesPanel=='orderinvoice' && !dhxLayout.cells('b').isCollapsed()){
					if (lastOrderInvoiceSelID != prop_tb._orderInvoiceGrid.getSelectedRowId()){
						lastOrderInvoiceSelID = prop_tb._orderInvoiceGrid.getSelectedRowId();
					}
				}
			});


			needinitOrderInvoice=0;
		}
	}


	function setPropertiesPanel_orderInvoice(id){
		if (id=='orderinvoice')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxLastname=ord_grid.getColIndexById('lastname');
				idxFirstname=ord_grid.getColIndexById('firstname');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderSelID,idxFirstname).getValue()+" "+ord_grid.cells(lastOrderSelID,idxLastname).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('orderinvoice_refresh');
			prop_tb.showItem('orderinvoice_add');
			prop_tb.setItemText('panel', '<?php echo _l('Invoices',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/calculator.png');
			needinitOrderInvoice = 1;
			initOrderInvoice();
			propertiesPanel='orderinvoice';
			if (lastOrderSelID!=0)
			{
				displayOrderInvoice();
			}
		}
		if (id=='orderinvoice_refresh')
		{
			lastOrderInvoiceSelID = 0;
			displayOrderInvoice();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_orderInvoice);


	function displayOrderInvoice()
	{
		var customers_id = "";
		idxIdCustomer=ord_grid.getColIndexById('id_customer');
		$.each( ord_grid.getSelectedRowId().split(','), function( num, rowid ) {
			if(customers_id!="")
				customers_id = customers_id+",";
			customers_id = customers_id+ord_grid.cells(rowid,idxIdCustomer).getValue();
		});
		prop_tb._orderInvoiceGrid.clearAll(true);
		prop_tb._orderInvoiceGrid.loadXML("index.php?ajax=1&act=ord_invoice_get&id_customer="+customers_id+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._orderInvoiceGrid.getRowsNum();
			prop_tb._sb.setText('');

		  // UISettings
			loadGridUISettings(prop_tb._orderInvoiceGrid);

			// UISettings
			prop_tb._orderInvoiceGrid._first_loading=0;
		});
	}

	ord_grid.attachEvent("onRowSelect",function (){
			if (propertiesPanel=='orderinvoice' && !dhxLayout.cells('b').isCollapsed()){
					displayOrderInvoice();
			}
		});

<?php
}
?>