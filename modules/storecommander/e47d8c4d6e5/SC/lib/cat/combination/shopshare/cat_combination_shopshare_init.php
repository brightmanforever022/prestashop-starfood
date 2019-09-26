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

if(SCMS && _r("GRI_CAT_PROPERTIES_GRID_MB_SHARE") && version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>

// INITIALISATION TOOLBAR
prop_tb.attachEvent("onClick", function setPropertiesPanel_combinations(id){
	if (id=='combinations')
	{
		prop_tb.combi_subproperties_tb.addListOption('combiSubProperties', 'combi_shop', 9, "button", '<?php echo _l('Multistore sharing manager',1)?>', "lib/img/sitemap_color.png");

		prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
			if(id=="combi_shop")
			{
				hideSubpropertiesItems();
				prop_tb.combi_subproperties_tb.setItemText('combiSubProperties', '<?php echo _l('Multistore sharing manager',1)?>');
				prop_tb.combi_subproperties_tb.setItemImage('combiSubProperties', 'lib/img/sitemap_color.png');
				actual_subproperties = "combi_shop";
				initCombinationshopshare();
			}
		});
				
		prop_tb._combinationsGrid.attachEvent("onRowSelect", function(id,ind){
			if (!prop_tb._combinationsLayout.cells('b').isCollapsed())
			{
				if(actual_subproperties == "combi_shop"){
			 		getCombinationsshopshares();
				}
			}
		});
		
		prop_tb.combi_subproperties_tb.addButton("shopshare_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('shopshare_refresh','<?php echo _l('Refresh grid',1)?>');
		prop_tb.combi_subproperties_tb.addButton("shopshare_add_select", 100, "", "lib/img/building_add.png", "lib/img/building_add.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('shopshare_add_select','<?php echo _l('Add all selected combinations to all selected shop',1)?>');
		prop_tb.combi_subproperties_tb.addButton("shopshare_del_select", 100, "", "lib/img/building_delete.png", "lib/img/building_delete.png");
		prop_tb.combi_subproperties_tb.setItemToolTip('shopshare_del_select','<?php echo _l('Delete all selected combinations from all selected shop',1)?>');
		//hideSubpropertiesItems();
			
		prop_tb.combi_subproperties_tb.attachEvent("onClick", function(id){
				if (id=='shopshare_add_select')
				{
					if(prop_tb._combinationsshopGrid.getSelectedRowId()!="" && prop_tb._combinationsshopGrid.getSelectedRowId()!=null)
					{
						$.post("index.php?ajax=1&act=cat_combination_shopshare_update&id_product="+lastProductSelID+"&action=mass_present&value=true&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_shop":prop_tb._combinationsshopGrid.getSelectedRowId(),"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
							getCombinationsshopshares();
						});
					}
				}
				if (id=='shopshare_del_select')
				{
					if(prop_tb._combinationsshopGrid.getSelectedRowId()!="" && prop_tb._combinationsshopGrid.getSelectedRowId()!=null)
					{
						$.post("index.php?ajax=1&act=cat_combination_shopshare_update&id_product="+lastProductSelID+"&action=mass_present&value=false&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_shop":prop_tb._combinationsshopGrid.getSelectedRowId(),"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
							getCombinationsshopshares();
						});
					}
				}
				if (id=='shopshare_refresh')
				{
					getCombinationsshopshares();
				}
		});
	}
});
			
// INIT GRID
function initCombinationshopshare()
{
	hideSubpropertiesItems();
	prop_tb.combi_subproperties_tb.showItem('shopshare_refresh');
	prop_tb.combi_subproperties_tb.showItem('shopshare_add_select');
	prop_tb.combi_subproperties_tb.showItem('shopshare_del_select');
	
	prop_tb._combinationsshopGrid = prop_tb._combinationsLayout.cells('b').attachGrid();
	prop_tb._combinationsshopGrid.setImagePath("lib/js/imgs/");
	
	prop_tb._combinationsshopGrid.enableDragAndDrop(false);
	prop_tb._combinationsshopGrid.enableMultiselect(true);

	// UISettings
	prop_tb._combinationsshopGrid._uisettings_prefix='cat_combination_shopshare';
	prop_tb._combinationsshopGrid._uisettings_name=prop_tb._combinationsshopGrid._uisettings_prefix;
   	prop_tb._combinationsshopGrid._first_loading=1;
   	
	// UISettings
	initGridUISettings(prop_tb._combinationsshopGrid);
	
	prop_tb._combinationsshopGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
	{
		if(stage==1)
		{
			idxPresent=prop_tb._combinationsshopGrid.getColIndexById('present');
		
			var action = "";
			if(cInd==idxPresent)
				action = "present";
			
			if(action=="present")
			{
				var has_one_check = false;
			   var value = prop_tb._combinationsshopGrid.cells(rId,cInd).isChecked();
			   	if(value!="1")
			   	{
					prop_tb._combinationsshopGrid.forEachRow(function(id){
				    	var value_row = prop_tb._combinationsshopGrid.cells(id,cInd).isChecked();
				    	if(value_row=="1")
				    		has_one_check = true;
				   });
				}
				else
					has_one_check=true;
					
				if(has_one_check==true)
				{
					$.post("index.php?ajax=1&act=cat_combination_shopshare_update&id_product="+lastProductSelID+"&id_shop="+rId+"&action="+action+"&value="+value+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":prop_tb._combinationsGrid.getSelectedRowId()},function(data){
						getCombinationsshopshares();
					});
				}
				else
				{
					dhtmlx.message({text:'<?php echo _l('At least one shop needs to be ticked',1)?>',type:'error',expire:10000});
					prop_tb._combinationsshopGrid.cells(rId,cInd).setValue(1);
				}
			}
			
		}
		return true;
	});
	
	getCombinationsshopshares();
}

function getCombinationsshopshares()
{
	prop_tb._combinationsshopGrid.clearAll(true);
	var tempIdList = (prop_tb._combinationsGrid.getSelectedRowId()!=null?prop_tb._combinationsGrid.getSelectedRowId():"");
	$.post("index.php?ajax=1&act=cat_combination_shopshare_get&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
	{
		prop_tb._combinationsshopGrid.parse(data);
		nb=prop_tb._combinationsshopGrid.getRowsNum();
		prop_tb._combinationsshopGrid._rowsNum=nb;
		
   	// UISettings
		loadGridUISettings(prop_tb._combinationsshopGrid);
		prop_tb._combinationsshopGrid._first_loading=0;
		
		idxPresent=prop_tb._combinationsshopGrid.getColIndexById('present');
		
		/*prop_tb._combinationsshopGrid.forEachRow(function(id){
	    	if(prop_tb._combinationsshopGrid.getUserData(id,"disabled")=="1")
	    		prop_tb._combinationsshopGrid.cells(id,idxPresent).setDisabled(true);
	    	else
	    		prop_tb._combinationsshopGrid.cells(id,idxPresent).setDisabled(false);
	   });*/
	});
}

<?php } ?>