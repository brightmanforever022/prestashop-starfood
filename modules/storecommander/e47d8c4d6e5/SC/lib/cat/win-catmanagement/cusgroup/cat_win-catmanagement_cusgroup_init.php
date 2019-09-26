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
?>

cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_cusgroup', 3, "button", '<?php echo _l('Customer groups',1)?>', "lib/img/group.png");

cat_prop_tb.attachEvent("onClick", function(id){
	if(id=="cat_prop_cusgroup")
	{
		hideCatManagementSubpropertiesItems();
		cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('Customer groups',1)?>');
		cat_prop_tb.setItemImage('cat_prop_subproperties', 'lib/img/group.png');
		actual_catmanagement_subproperties = "cat_prop_cusgroup";
		initCatManagementPropCusgroup();
	}
});
				
cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
	if (!dhxlCatManagement.cells('b').isCollapsed())
	{
		if(actual_catmanagement_subproperties == "cat_prop_cusgroup"){
	 		getCatManagementPropCusgroup();
		}
	}
});
		
cat_prop_tb.addButton('cat_prop_cusgroup_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cat_prop_tb.setItemToolTip('cat_prop_cusgroup_refresh','<?php echo _l('Refresh grid',1)?>');
cat_prop_tb.addButton("cat_prop_cusgroup_add_select", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
cat_prop_tb.setItemToolTip('cat_prop_cusgroup_add_select','<?php echo _l('Add all selected categories to all selected groups',1)?>');
cat_prop_tb.addButton("cat_prop_cusgroup_del_select", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
cat_prop_tb.setItemToolTip('cat_prop_cusgroup_del_select','<?php echo _l('Delete all selected categories from all selected groups',1)?>');
hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
	if (id=='cat_prop_cusgroup_refresh')
	{
		getCatManagementPropCusgroup();
	}
	if (id=='cat_prop_cusgroup_add_select')
	{
		$.post("index.php?ajax=1&act=cat_win-catmanagement_cusgroup_update&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId(), "id_group":cat_prop_cusgroup_grid.getSelectedRowId()},function(data){
			getCatManagementPropCusgroup();
		});
	}
	if (id=='cat_prop_cusgroup_del_select')
	{
		$.post("index.php?ajax=1&act=cat_win-catmanagement_cusgroup_update&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId(), "id_group":cat_prop_cusgroup_grid.getSelectedRowId()},function(data){
			getCatManagementPropCusgroup();
		});
	}
});

// FUNCTIONS
var cat_prop_cusgroup = null;
var clipboardType_CatPropCusgroup = null;
function initCatManagementPropCusgroup()
{
	cat_prop_tb.showItem('cat_prop_cusgroup_refresh');
	cat_prop_tb.showItem('cat_prop_cusgroup_add_select');
	cat_prop_tb.showItem('cat_prop_cusgroup_del_select');
	
	cat_prop_cusgroup = dhxlCatManagement.cells('b').attachLayout("1C");
	dhxlCatManagement.cells('b').showHeader();
	
	// GRID
		cat_prop_cusgroup.cells('a').hideHeader();
		
		cat_prop_cusgroup_grid = cat_prop_cusgroup.cells('a').attachGrid();
		cat_prop_cusgroup_grid.setImagePath("lib/js/imgs/");
	  	cat_prop_cusgroup_grid.enableDragAndDrop(false);
		cat_prop_cusgroup_grid.enableMultiselect(true);
	
		// UISettings
		cat_prop_cusgroup_grid._uisettings_prefix='cat_prop_cusgroup_grid';
		cat_prop_cusgroup_grid._uisettings_name=cat_prop_cusgroup_grid._uisettings_prefix;
		cat_prop_cusgroup_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cat_prop_cusgroup_grid);
		
		getCatManagementPropCusgroup();
		
		cat_prop_cusgroup_grid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
		{
			if(stage==1)
			{
				idxPresent=cat_prop_cusgroup_grid.getColIndexById('present');
			
				var action = "";
				if(cInd==idxPresent)
					action = "present";
				
				if(action!="")
				{
					var value = cat_prop_cusgroup_grid.cells(rId,cInd).isChecked();
					$.post("index.php?ajax=1&act=cat_win-catmanagement_cusgroup_update&id_group="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_treegrid_grid.getSelectedRowId()},function(data){
						getCatManagementPropCusgroup();
					});
				}
			}
			return true;
		});
}

function getCatManagementPropCusgroup()
{
	cat_prop_cusgroup_grid.clearAll(true);
		var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cat_win-catmanagement_cusgroup_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			cat_prop_cusgroup_grid.parse(data);
				
    		// UISettings
				loadGridUISettings(cat_prop_cusgroup_grid);
				cat_prop_cusgroup_grid._first_loading=0;
				
				idxPresent=cat_prop_cusgroup_grid.getColIndexById('present');
		});
}