	prop_tb.addListOption('panel', 'psorder', 3, "button", '<?php
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
 echo _l('Order',1)?>', "lib/img/cart_ps.png");
	allowed_properties_panel[allowed_properties_panel.length] = "psorder";

	prop_tb.addButton("psorder_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('psorder_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitCusmPSOrderPage = 1;
	function initCusmPSOrderPage(){
		if (needinitCusmPSOrderPage)
		{
			prop_tb._cusmPSOrderPageLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._cusmPSOrderPageLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			needinitCusmPSOrderPage=0;
		}
	}


	function setPropertiesPanel_psorder(id){
		if (id=='psorder')
		{
			if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
			{
				idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('psorder_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Order',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/cart_ps.png');
			needinitCusmPSOrderPage = 1;
			initCusmPSOrderPage();
			propertiesPanel='psorder';
			if (lastDiscussionSelID!=0)
			{
				displayCusmPSOrderPage();
			}
		}
		if (id=='psorder_refresh')
		{
			displayCusmPSOrderPage();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_psorder);


	function displayCusmPSOrderPage()
	{
		if(lastDiscussionSelID!=null && lastDiscussionSelID>0)
		{
			idxOrderID=cusm_grid.getColIndexById('id_order');
			var id_order = cusm_grid.cells(lastDiscussionSelID,idxOrderID).getValue();
			if(id_order!=null && id_order>0){
				if (isIPAD){
					window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders');?>");
				}else{
					prop_tb._cusmPSOrderPageLayout.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminOrders&vieworder&id_order="+id_order+"&token=<?php echo $sc_agent->getPSToken('AdminOrders');?>");
				}
			}else
				prop_tb._cusmPSOrderPageLayout.cells('a').attachURL("index.php?ajax=1&act=cusm_psorder_empty");
		}
	}


	cusm_grid.attachEvent("onRowSelect",function (idorder){
			if (propertiesPanel=='psorder' && !dhxLayout.cells('b').isCollapsed()){
				displayCusmPSOrderPage();
			}
		});