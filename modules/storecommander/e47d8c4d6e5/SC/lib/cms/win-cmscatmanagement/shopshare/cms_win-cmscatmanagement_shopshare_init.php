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
 if(version_compare(_PS_VERSION_, '1.6.0.12', '>=') && SCMS) { ?>
// INITIALISATION TOOLBAR
cms_prop_tb.addListOption('cms_prop_subproperties', 'cms_prop_shopshare', 3, "button", '<?php echo _l('Multistore sharing manager',1)?>', "lib/img/sitemap_color.png");

cms_prop_tb.attachEvent("onClick", function(id){
	if(id=="cms_prop_shopshare")
	{
		hideCmsCatManagementSubpropertiesItems();
		cms_prop_tb.setItemText('cms_prop_subproperties', '<?php echo _l('Multistore sharing manager',1)?>');
		cms_prop_tb.setItemImage('cms_prop_subproperties', 'lib/img/sitemap_color.png');
		actual_cmscatmanagement_subproperties = "cms_prop_shopshare";
		initCmsCatManagementPropShopshare();
	}
});
				
cms_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
	if (!dhxlCmsCatManagement.cells('b').isCollapsed())
	{
		if(actual_cmscatmanagement_subproperties == "cms_prop_shopshare"){
	 		getCmsCatManagementPropShopshare();
		}
	}
});
		
cms_prop_tb.addButton('cms_prop_shopshare_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cms_prop_tb.setItemToolTip('cms_prop_shopshare_refresh','<?php echo _l('Refresh grid',1)?>');
cms_prop_tb.addButton("cms_prop_shopshare_add_select", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
cms_prop_tb.setItemToolTip('cms_prop_shopshare_add_select','<?php echo _l('Add all selected CMS categories to all selected shops',1)?>');
cms_prop_tb.addButton("cms_prop_shopshare_del_select", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
cms_prop_tb.setItemToolTip('cms_prop_shopshare_del_select','<?php echo _l('Delete all selected CMS categories from all selected shops',1)?>');
hideCmsCatManagementSubpropertiesItems();

cms_prop_tb.attachEvent("onClick", function(id){
	if (id=='cms_prop_shopshare_refresh')
	{
		getCmsCatManagementPropShopshare();
	}
	if (id=='cms_prop_shopshare_add_select')
	{
		$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_shopshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cms_treegrid_grid.getSelectedRowId(),"id_shop":cms_prop_shopshare_grid.getSelectedRowId()},function(data){
			getCmsCatManagementPropShopshare();
		});
	}
	if (id=='cms_prop_shopshare_del_select')
	{
		$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_shopshare_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cms_treegrid_grid.getSelectedRowId(),"id_shop":cms_prop_shopshare_grid.getSelectedRowId()},function(data){
			var doDisplay = true;
			if(data!="noRefreshCMS")
			{
				var shops=cms_prop_shopshare_grid.getSelectedRowId().split(',');
				$.each( shops, function( num, shopId ) {
					if(shopId==shopselection)
					{
						displayCms('getCmsCatManagementPropShopshare()');
						doDisplay = false;
						return false;
					}
				});
			}
			if(doDisplay==true)
				getCmsCatManagementPropShopshare();
		});
	}
});

// FUNCTIONS
var cms_prop_shopshare = null;
var clipboardType_CatPropShopshare = null;
function initCmsCatManagementPropShopshare()
{
	cms_prop_tb.showItem('cms_prop_shopshare_refresh');
	cms_prop_tb.showItem('cms_prop_shopshare_add_select');
	cms_prop_tb.showItem('cms_prop_shopshare_del_select');

	cms_prop_shopshare = dhxlCmsCatManagement.cells('b').attachLayout("1C");
	dhxlCmsCatManagement.cells('b').showHeader();
	
	// GRID
		cms_prop_shopshare.cells('a').hideHeader();
		
		cms_prop_shopshare_grid = cms_prop_shopshare.cells('a').attachGrid();
		cms_prop_shopshare_grid.setImagePath("lib/js/imgs/");
	  	cms_prop_shopshare_grid.enableDragAndDrop(false);
		cms_prop_shopshare_grid.enableMultiselect(true);
	
		// UISettings
		cms_prop_shopshare_grid._uisettings_prefix='cms_prop_shopshare_grid';
		cms_prop_shopshare_grid._uisettings_name=cms_prop_shopshare_grid._uisettings_prefix;
		cms_prop_shopshare_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cms_prop_shopshare_grid);
		
		getCmsCatManagementPropShopshare();
		
		cms_prop_shopshare_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
		{
			if(stage==1)
			{
				idxPresent=cms_prop_shopshare_grid.getColIndexById('present');
			
				var action = "";
				if(cInd==idxPresent)
					action = "present";
				
				if(action!="")
				{
					var value = cms_prop_shopshare_grid.cells(rId,cInd).isChecked();
					$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_shopshare_update&id_shop="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cms_treegrid_grid.getSelectedRowId()},function(data){
						if(rId==id_shop)
						{
							displayTreegridCategories();
						}
						getCmsCatManagementPropShopshare();
					});
				}
			}
			return true;
		});
}

function getCmsCatManagementPropShopshare()
{
	cms_prop_shopshare_grid.clearAll(true);
		var tempIdList = (cms_treegrid_grid.getSelectedRowId()!=null?cms_treegrid_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			cms_prop_shopshare_grid.parse(data);
				
    		// UISettings
				loadGridUISettings(cms_prop_shopshare_grid);
				cms_prop_shopshare_grid._first_loading=0;
				
				idxPresent=cms_prop_shopshare_grid.getColIndexById('present');
				
				cms_prop_shopshare_grid.forEachRow(function(id){
			    	cms_prop_shopshare_grid.cells(id,idxPresent).setDisabled(false);
			   });
		});
}
<?php } ?>
