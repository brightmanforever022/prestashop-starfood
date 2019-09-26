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
	// INITIALISATION TOOLBAR
	cus_group_prop_tb.addListOption('cus_group_prop_subproperties', 'cus_group_categories', 1, "button", '<?php echo _l('Categories sharing',1)?>', "lib/img/catalog.png");

	cus_group_prop_tb.attachEvent("onClick", function(id){
		if(id=="cus_group_categories")
		{
			hideGroupManagementSubpropertiesItems();
			cus_group_prop_tb.setItemText('cus_group_prop_subproperties', '<?php echo _l('Multistore sharing manager',1)?>');
			cus_group_prop_tb.setItemImage('cus_group_prop_subproperties', 'lib/img/sitemap_color.png');
			actual_groupmanagement_subproperties = "cus_group_categories";
			initGroupManagementPropCategories();
			displayCategories('',true);
		}
	});

	var lastGroupID = 0
	
	var for_mb = 0;

	cus_group_prop_tb.addButton('categ_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
	cus_group_prop_tb.setItemToolTip('categ_refresh','<?php echo _l('Refresh',1)?>');
	cus_group_prop_tb.addButtonTwoState('categ_filter', 100, "", "lib/img/filter.png", "lib/img/filter.png");
	cus_group_prop_tb.setItemToolTip('categ_filter','<?php echo _l('Display only categories used by selected products',1)?>');
	<?php if(SCMS) { ?>
	cus_group_prop_tb.addButtonTwoState('for_mb', 100, "", "lib/img/folder_table.png", "lib/img/folder_table.png");
	cus_group_prop_tb.setItemToolTip('for_mb','<?php echo _l('Only display categories associated to the selected shop',1)?>');
	cus_group_prop_tb.setItemState('for_mb', 1);
	for_mb = 1;
	<?php } ?>
	cus_group_prop_tb.addButton('categ_go',100,'','lib/img/folder_go.png','lib/img/folder_go.png');
	cus_group_prop_tb.setItemToolTip('categ_go','<?php echo _l('Open and select category',1)?>');
	cus_group_prop_tb.addButton('categ_expand',100,'','lib/img/arrow_out.png','lib/img/arrow_out.png');
	cus_group_prop_tb.setItemToolTip('categ_expand','<?php echo _l('Expand all items',1)?>');
	cus_group_prop_tb.addButton('categ_collapse',100,'','lib/img/arrow_in.png','lib/img/arrow_in.png');
	cus_group_prop_tb.setItemToolTip('categ_collapse','<?php echo _l('Collapse all items',1)?>');
	cus_group_prop_tb.addButton('categ_multi_add',100,'','lib/img/chart_organisation_add_v.png','lib/img/chart_organisation_add_v.png');
	cus_group_prop_tb.setItemToolTip('categ_multi_add','<?php echo _l('Place selected products in selected categories',1)?>');
	cus_group_prop_tb.addButton('categ_multi_del',100,'','lib/img/chart_organisation_delete_v.png','lib/img/chart_organisation_delete_v.png');
	cus_group_prop_tb.setItemToolTip('categ_multi_del','<?php echo _l('Remove selected products from selected categories (if not default category)',1)?>');

// FUNCTIONS

	function initGroupManagementPropCategories() {
		if(lastGroupID!=undefined && lastGroupID!="")
		{
			idsGroupName=wGroupManagement.gridGroups.getColIndexById('name');
			groups_properties_panel.setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+wGroupManagement.gridGroups.cells(lastGroupID,idsGroupName).getValue());
		}
		hidePropTBButtons();
		cus_group_prop_tb.showItem('categ_multi_del');
		cus_group_prop_tb.showItem('categ_multi_add');
		cus_group_prop_tb.showItem('categ_expand');
		cus_group_prop_tb.showItem('categ_collapse');
		cus_group_prop_tb.showItem('categ_refresh');
		cus_group_prop_tb.showItem('categ_filter');
		cus_group_prop_tb.showItem('categ_go');
		needInitCategories = 1;
		initCategories();
		propertiesPanel='categories';
		if (lastGroupID!=0)
		{
			cache_categorypanel_treeticks = [];
			displayCategories();
		}

		wGroupManagement.gridGroups.attachEvent("onRowSelect", function(id,ind){
			if(actual_groupmanagement_subproperties == "cus_group_categories"){
				displayCategories();
			}
		});
	}

	needInitCategories = 1;
	function initCategories()
	{
		if (needInitCategories)
		{
			cus_group_prop_tb._categoriesLayout = groups_properties_panel.attachLayout('1C');
			cus_group_prop_tb._categoriesLayout.cells('a').hideHeader();
			groups_properties_panel.showHeader();
			cus_group_prop_tb._categoriesGrid = cus_group_prop_tb._categoriesLayout.cells('a').attachGrid();
			cus_group_prop_tb._categoriesGrid.setImagePath("lib/js/imgs/");
			cus_group_prop_tb._categoriesGrid.setFiltrationLevel(-2);
			cus_group_prop_tb._categoriesGrid.enableTreeCellEdit(0);
			cus_group_prop_tb._categoriesGrid.enableSmartRendering(true);

			// UISettings
			cus_group_prop_tb._categoriesGrid._uisettings_prefix='cat_categorypanel';
			cus_group_prop_tb._categoriesGrid._uisettings_name=cus_group_prop_tb._categoriesGrid._uisettings_prefix;
		   	cus_group_prop_tb._categoriesGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(cus_group_prop_tb._categoriesGrid);
			
			cus_group_prop_tb._categoriesGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
			{
				idxUsed=cus_group_prop_tb._categoriesGrid.getColIndexById('used');
				if (cInd == idxUsed)
				{
					if(stage==1)
					{
						var selection = wGroupManagement.gridGroups.getSelectedRowId();
						ids=selection.split(',');
						$.each(ids, function(num, pId){
							var vars = {"sub_action":cus_group_prop_tb._categoriesGrid.cells(rId,idxUsed).getValue(),"idlist":pId};
							addCategoryInQueue(rId, "update", cInd, vars);
						});
					}
				}
				return true;
			});
			cus_group_prop_tb._categoriesGrid.enableMultiselect(true);
			needInitCategories=0;
		}
	}

		cus_group_prop_tb.attachEvent("onStateChange",function(id,state){
			if (id=='categ_filter')
			{
				if (state)
				{
					categoriesFilter=1;
				}else{
					categoriesFilter=0;
				}
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		});

	function setPropertiesPanel_categories(id){
		if (id=='categories')
		{
			if(lastGroupID!=undefined && lastGroupID!="")
			{
				idsGroupName=wGroupManagement.gridGroups.getColIndexById('name');
				groups_properties_panel.setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+wGroupManagement.gridGroups.cells(lastGroupID,idsGroupName).getValue());
			}
			hidePropTBButtons();
			cus_group_prop_tb.showItem('categ_multi_del');
			cus_group_prop_tb.showItem('categ_multi_add');
			cus_group_prop_tb.showItem('categ_expand');
			cus_group_prop_tb.showItem('categ_collapse');
			cus_group_prop_tb.showItem('categ_refresh');
			cus_group_prop_tb.showItem('categ_filter');
			<?php if(SCMS) { ?>
			cus_group_prop_tb.showItem('for_mb');
			<?php } ?>
			cus_group_prop_tb.showItem('categ_go');
			cus_group_prop_tb.setItemText('panel', '<?php echo _l('Categories',1)?>');
			cus_group_prop_tb.setItemImage('panel', 'lib/img/catalog.png');
			needInitCategories = 1;
			initCategories();
			propertiesPanel='categories';
			if (lastGroupID!=0)
			{
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		}
		if (id=='categ_refresh')
		{
			cache_categorypanel_treeticks = [];
			displayCategories('',true);
		}
		if (id=='categ_go')
		{
			cat_tree.openItem(cus_group_prop_tb._categoriesGrid.getSelectedRowId());
			cat_tree.selectItem(cus_group_prop_tb._categoriesGrid.getSelectedRowId(),true);
		}
		if (id=='categ_expand')
		{
			cus_group_prop_tb._categoriesGrid.expandAll();
		}
		if (id=='categ_collapse')
		{
			cus_group_prop_tb._categoriesGrid.collapseAll();
		}
		if (id=='categ_multi_add')
		{
			if (cus_group_prop_tb._categoriesGrid.getSelectedRowId()==null || wGroupManagement.gridGroups.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				var ids_groups = wGroupManagement.gridGroups.getSelectedRowId();
				var vars = {"sub_action":"multi_add","ids_groups":ids_groups,"ids_categs":cus_group_prop_tb._categoriesGrid.getSelectedRowId()};
				addCategoryInQueue("", "update", "", vars);
			}
		}
		if (id=='categ_multi_del')
		{
			if (cus_group_prop_tb._categoriesGrid.getSelectedRowId()==null || wGroupManagement.gridGroups.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				var ids_groups = wGroupManagement.gridGroups.getSelectedRowId();
				var vars = {"sub_action":"multi_del","ids_groups":ids_groups,"ids_categs":cus_group_prop_tb._categoriesGrid.getSelectedRowId()};
				addCategoryInQueue("", "update", "", vars);
			}
		}
	}
	cus_group_prop_tb.attachEvent("onClick", setPropertiesPanel_categories);


cache_categorypanel_treeticks = [];
function displayCategories(callback,force_refresh)
{
	if (cus_group_prop_tb._categoriesGrid._rowsNum>0 && force_refresh!=true)
	{
		$.post("index.php?ajax=1&act=cus_win-groupmanagement_categorypanel_relation_get&for_mb="+for_mb+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":wGroupManagement.gridGroups.getSelectedRowId()},function(data){
				if (data!='')
				{

					cus_group_prop_tb._categoriesGrid.uncheckAll();
					selArray=data.split(',');

					selArray.forEach(function(id){
						if (cus_group_prop_tb._categoriesGrid.doesRowExist(id)) {
							cus_group_prop_tb._categoriesGrid.cellById(id,1).setValue(1);
							cache_categorypanel_treeticks[id] = 1;
						}
					});

					if (categoriesFilter)
					{
						cus_group_prop_tb._categoriesGrid.filterTreeBy(1,1,0);
						cus_group_prop_tb._categoriesGrid.expandAll();
					}else{
						cus_group_prop_tb._categoriesGrid.filterTreeBy(1,'',0);
					}
					if (cus_group_prop_tb._categoriesGrid.getFilterElement(2)!=null && cus_group_prop_tb._categoriesGrid.getFilterElement(2).value!='')
						cus_group_prop_tb._categoriesGrid.filterTreeBy(2,cus_group_prop_tb._categoriesGrid.getFilterElement(2).value,1);
				
		    		// UISettings
					loadGridUISettings(cus_group_prop_tb._categoriesGrid);
					
					// UISettings
					cus_group_prop_tb._categoriesGrid._first_loading=0;
				}
			});
	}else{
		if ((wGroupManagement.gridGroups.getSelectedRowId()==null || wGroupManagement.gridGroups.getSelectedRowId()=='') && force_refresh!=true) return false;
		cus_group_prop_tb._categoriesGrid.clearAll(true);
		cus_group_prop_tb._categoriesGrid.loadXML("index.php?ajax=1&act=cus_win-groupmanagement_categorypanel_get&for_mb="+for_mb+"&id_group="+lastGroupID+"&id_lang="+SC_ID_LANG,function()
		{
			nb=cus_group_prop_tb._categoriesGrid.getRowsNum();
			cus_group_prop_tb._categoriesGrid._rowsNum=nb;

			// UISettings
			loadGridUISettings(cus_group_prop_tb._categoriesGrid);

			// UISettings
			cus_group_prop_tb._categoriesGrid._first_loading=0;

			cache_categorypanel_treeticks = [];
			displayCategories();

			if (callback!='') eval(callback);
		});
	}
}

function setNbSelected()
{
	_setNbSelected("");
}
function _setNbSelected(parent_id)
{
	var nb_count = 0;
	
	var row_n = cus_group_prop_tb._categoriesGrid.getSubItems(parent_id);
	
	if(row_n!=undefined && row_n!=null && row_n!="")
	{
		var rows = row_n.split(",");
		$.each(rows, function(num, id){
			var checked = cus_group_prop_tb._categoriesGrid.cellById(id,1).getValue();
			if(checked==true)
			{
				nb_count = nb_count*1 + 1;
			}
			
			var nb_children = _setNbSelected(id);
			
			var text_base = cus_group_prop_tb._categoriesGrid.cellById(id,2).getValue();
			var exp = text_base.split("<strong>");
			text_base = exp[0];
			var text = text_base+" <strong>["+nb_children+"]</strong>";
			cus_group_prop_tb._categoriesGrid.cellById(id,2).setValue(text);
			
			nb_count = nb_count*1 + nb_children;
		});
	}
	return nb_count;
}

function addCategoryInQueue(rId, action, cIn, vars)
{
	var params = {
		name: "cus_win-groupmanagement_categorypanel_update_queue",
		row: rId,
		action: "update",
		params: {},
		callback: "callbackCategory('"+rId+"','update','"+rId+"',{data});"
	};
	// COLUMN VALUES
		params.params["id_lang"] = SC_ID_LANG;
		if(vars!=undefined && vars!=null && vars!="" && vars!=0)
		{
			$.each(vars, function(key, value){
				params.params[key] = value;
			});
		}
	
	params.params = JSON.stringify(params.params);
	addInUpdateQueue(params,cus_group_prop_tb._categoriesGrid);
}
		
// CALLBACK FUNCTION
function callbackCategory(sid,action,tid,xml)
{
	if (action=='update')
	{
		cus_group_prop_tb._categoriesGrid.setRowTextNormal(sid);
		
		if(xml!=undefined && xml!=null && xml!="" && xml!=0)
		{
			var refresh_cat = xml.refresh_cat;
			if (refresh_cat=='1')
			{
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		}
	}
}
