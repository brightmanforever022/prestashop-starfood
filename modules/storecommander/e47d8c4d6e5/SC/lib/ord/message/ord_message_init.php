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
 if(_r("GRI_ORD_PROPERTIES_GRID_MESSAGE")) { ?>
	prop_tb.addListOption('panel', 'message', 3, "button", '<?php echo _l('Messages',1)?>', "lib/img/comments.png");
	allowed_properties_panel[allowed_properties_panel.length] = "message";

	prop_tb.addButton("message_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('message_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitmessage = 1;
	function initmessage(){
		if (needinitmessage)
		{
			prop_tb._messageLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._messageLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._messageGrid = prop_tb._messageLayout.cells('a').attachGrid();
			prop_tb._messageGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._messageGrid._uisettings_prefix='ord_message';
			prop_tb._messageGrid._uisettings_name=prop_tb._messageGrid._uisettings_prefix;
		   	prop_tb._messageGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._messageGrid);
			
			needinitmessage=0;
		}
	}


	function setPropertiesPanel_message(id){
		if (id=='message')
		{
			if(lastOrderSelID!=undefined && lastOrderSelID!="")
			{
				idxOrderID=ord_grid.getColIndexById('id_order');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+ord_grid.cells(lastOrderRowSelID,idxOrderID).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('message_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Message',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/comments.png');
			needinitmessage = 1;
			initmessage();
			propertiesPanel='message';
			if (lastOrderSelID!=0)
			{
				displayMessage();
			}
		}
		if (id=='message_refresh')
		{
			displayMessage();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_message);


	function displayMessage()
	{
		prop_tb._messageGrid.clearAll(true);
		prop_tb._messageGrid.loadXML("index.php?ajax=1&act=ord_message_get&id_order="+lastOrderSelID,function()
				{
					nb=prop_tb._messageGrid.getRowsNum();
					prop_tb._sb.setText('');
				
		    		// UISettings
					loadGridUISettings(prop_tb._messageGrid);
					
					// UISettings
					prop_tb._messageGrid._first_loading=0;
				});
	}
	


	ord_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='message' && !dhxLayout.cells('b').isCollapsed()){
				displayMessage();
			}
		});

<?php
	} // end permission
?>