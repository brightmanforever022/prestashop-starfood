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
 if(SCMS) { ?>
// INITIALISATION TOOLBAR
cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_shopshare', 3, "button", '<?php echo _l('Multistore sharing manager',1)?>', "lib/img/sitemap_color.png");

cat_prop_tb.attachEvent("onClick", function(id){
	if(id=="cat_prop_shopshare")
	{
		hideCatManagementSubpropertiesItems();
		cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('Multistore sharing manager',1)?>');
		cat_prop_tb.setItemImage('cat_prop_subproperties', 'lib/img/sitemap_color.png');
		actual_catmanagement_subproperties = "cat_prop_shopshare";
		initCatManagementPropShopshare();
	}
});
				
cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
	if (!dhxlCatManagement.cells('b').isCollapsed())
	{
		if(actual_catmanagement_subproperties == "cat_prop_shopshare"){
	 		getCatManagementPropShopshare();
		}
	}
});
		
cat_prop_tb.addButton('cat_prop_shopshare_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cat_prop_tb.setItemToolTip('cat_prop_shopshare_refresh','<?php echo _l('Refresh grid',1)?>');
cat_prop_tb.addButton("cat_prop_shopshare_add_select", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
cat_prop_tb.setItemToolTip('cat_prop_shopshare_add_select','<?php echo _l('Add all selected categories to all selected shops',1)?>');
cat_prop_tb.addButton("cat_prop_shopshare_del_select", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
cat_prop_tb.setItemToolTip('cat_prop_shopshare_del_select','<?php echo _l('Delete all selected categories from all selected shops',1)?>');
hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
	if (id=='cat_prop_shopshare_refresh')
	{
		getCatManagementPropShopshare();
	}
	if (id=='cat_prop_shopshare_add_select')
	{
		$.post("index.php?ajax=1&act=cat_win-catmanagement_shopshare_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId(),"id_shop":cat_prop_shopshare_grid.getSelectedRowId()},function(data){
			getCatManagementPropShopshare();
		});
	}
	if (id=='cat_prop_shopshare_del_select')
	{
		$.post("index.php?ajax=1&act=cat_win-catmanagement_shopshare_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId(),"id_shop":cat_prop_shopshare_grid.getSelectedRowId()},function(data){
			var doDisplay = true;
			if(data!="noRefreshProduct")
			{
				var shops=cat_prop_shopshare_grid.getSelectedRowId().split(',');
				$.each( shops, function( num, shopId ) {
					if(shopId==shopselection)
					{
						displayProducts('getCatManagementPropShopshare()');
						doDisplay = false;
						return false;
					}
				});
			}
			if(doDisplay==true)
				getCatManagementPropShopshare();
		});
	}
});

// FUNCTIONS
var cat_prop_shopshare = null;
var clipboardType_CatPropShopshare = null;
function initCatManagementPropShopshare()
{
	cat_prop_tb.showItem('cat_prop_shopshare_refresh');
	cat_prop_tb.showItem('cat_prop_shopshare_add_select');
	cat_prop_tb.showItem('cat_prop_shopshare_del_select');
	
	cat_prop_shopshare = dhxlCatManagement.cells('b').attachLayout("1C");
	dhxlCatManagement.cells('b').showHeader();
	
	// GRID
		cat_prop_shopshare.cells('a').hideHeader();
		
		cat_prop_shopshare_grid = cat_prop_shopshare.cells('a').attachGrid();
		cat_prop_shopshare_grid.setImagePath("lib/js/imgs/");
	  	cat_prop_shopshare_grid.enableDragAndDrop(false);
		cat_prop_shopshare_grid.enableMultiselect(true);
	
		// UISettings
		cat_prop_shopshare_grid._uisettings_prefix='cat_prop_shopshare_grid';
		cat_prop_shopshare_grid._uisettings_name=cat_prop_shopshare_grid._uisettings_prefix;
		cat_prop_shopshare_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cat_prop_shopshare_grid);
		
		getCatManagementPropShopshare();
		
		cat_prop_shopshare_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
		{
			if(stage==1)
			{
				idxPresent=cat_prop_shopshare_grid.getColIndexById('present');
				idxDefault=cat_prop_shopshare_grid.getColIndexById('is_default');
				
				cat_prop_shopshare_grid.forEachRow(function(id){
			    	if(id!=rId)
			    		cat_prop_shopshare_grid.cells(id,idxDefault).setValue(0);
			   });
			
				var action = "";
				if(cInd==idxPresent)
					action = "present";
				else if(cInd==idxDefault)
					action = "default";
				
				if(action!="")
				{
					var value = cat_prop_shopshare_grid.cells(rId,cInd).isChecked();
					$.post("index.php?ajax=1&act=cat_win-catmanagement_shopshare_update&id_shop="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId()},function(data){
						if(rId==id_shop)
						{
							displayTreegridCategories();
						}
						getCatManagementPropShopshare();
					});
				}
			}
			return true;
		});
}

function getCatManagementPropShopshare()
{
	cat_prop_shopshare_grid.clearAll(true);
		var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cat_win-catmanagement_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			cat_prop_shopshare_grid.parse(data);
				
    		// UISettings
				loadGridUISettings(cat_prop_shopshare_grid);
				cat_prop_shopshare_grid._first_loading=0;
				
				idxPresent=cat_prop_shopshare_grid.getColIndexById('present');
				idxDefault=cat_prop_shopshare_grid.getColIndexById('is_default');
				
				cat_prop_shopshare_grid.forEachRow(function(id){
			    	if(cat_prop_shopshare_grid.cells(id,idxDefault).isChecked())
			    		cat_prop_shopshare_grid.cells(id,idxPresent).setDisabled(true);
			    	else
			    		cat_prop_shopshare_grid.cells(id,idxPresent).setDisabled(false);
			   });
		});
}
<?php } ?>