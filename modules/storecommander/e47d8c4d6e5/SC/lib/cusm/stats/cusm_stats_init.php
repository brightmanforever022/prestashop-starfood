	prop_tb.addListOption('panel', 'stats', 4, "button", '<?php
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
 echo _l('Stats',1)?>', "lib/img/application_view_list.png");
	allowed_properties_panel[allowed_properties_panel.length] = "stats";

	prop_tb.addButton("stats_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('stats_refresh','<?php echo _l('Refresh grid',1)?>');


	needinitCusmStatsPage = 1;
	function initCusmStatsPage(){
		if (needinitCusmStatsPage)
		{
			prop_tb._cusmStatsPageLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._cusmStatsPageLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			needinitCusmStatsPage=0;
		}
	}


	function setPropertiesPanel_stats(id){
		if (id=='stats')
		{
			if(lastDiscussionSelID!=undefined && lastDiscussionSelID!="")
			{
				idxCustomerName=cusm_grid.getColIndexById('customer_name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cusm_grid.cells(lastDiscussionSelID,idxCustomerName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('stats_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Stats',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/application_view_list.png');
			needinitCusmStatsPage = 1;
			initCusmStatsPage();
			propertiesPanel='stats';
			if (lastDiscussionSelID!=0)
			{
				displayCusmStatsPage();
			}
		}
		if (id=='stats_refresh')
		{
			displayCusmStatsPage();
		}

	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_stats);


	function displayCusmStatsPage()
	{
		if(lastDiscussionSelID!=null && lastDiscussionSelID>0)
		{
			prop_tb._cusmStatsPageLayout.cells('a').attachURL("index.php?ajax=1&act=cusm_stats_get&id_discussion="+lastDiscussionSelID);
		}
	}


	cusm_grid.attachEvent("onRowSelect",function (idstats){
			if (propertiesPanel=='stats' && !dhxLayout.cells('b').isCollapsed()){
				displayCusmStatsPage();
			}
		});