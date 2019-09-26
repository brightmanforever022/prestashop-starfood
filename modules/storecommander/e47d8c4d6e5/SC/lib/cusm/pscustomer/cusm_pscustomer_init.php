	prop_tb.addListOption('panel', 'pscustomer', 2, "button", '<?php
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
 echo _l('Customer',1)?>', "lib/img/user_ps.png");
	allowed_properties_panel[allowed_properties_panel.length] = "pscustomer";

	prop_tb.addButton("pscustomer_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('pscustomer_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitCusmPSCustomerPage = 1;
	function initCusmPSCustomerPage(){
		if (needinitCusmPSCustomerPage)
		{
			prop_tb._cusmPSCustomerPageLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._cusmPSCustomerPageLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			needinitCusmPSCustomerPage=0;
		}
	}


	function setPropertiesPanel_pscustomer(id){
		if (id=='pscustomer')
		{
			if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
			{
				idxDiscussionID=cusm_grid.getColIndexById('id_customer_thread');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cusm_grid.cells(lastDiscussionSelID,idxDiscussionID).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('pscustomer_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Customer',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/user_ps.png');
			needinitCusmPSCustomerPage = 1;
			initCusmPSCustomerPage();
			propertiesPanel='pscustomer';
			if (lastDiscussionSelID!=0)
			{
				displayCusmPSCustomerPage();
			}
		}
		if (id=='pscustomer_refresh')
		{
			displayCusmPSCustomerPage();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_pscustomer);


	function displayCusmPSCustomerPage()
	{
		var id_customer =  cusm_grid.getUserData(lastDiscussionSelID,"id_customer");
		if(id_customer!=null && id_customer>0){
			if (isIPAD){
				window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
			}else{
				prop_tb._cusmPSCustomerPageLayout.cells('a').attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
			}
		}
	}


	cusm_grid.attachEvent("onRowSelect",function (idorder){
			if (propertiesPanel=='pscustomer' && !dhxLayout.cells('b').isCollapsed()){
				displayCusmPSCustomerPage();
			}
		});