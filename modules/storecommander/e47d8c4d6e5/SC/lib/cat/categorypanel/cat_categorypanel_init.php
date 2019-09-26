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
if (SCMS) {
	$sql = "SELECT s.id_shop, s.name
			FROM " . _DB_PREFIX_ . "shop s
			INNER JOIN " . _DB_PREFIX_ . "product_shop ps ON ps.id_shop = s.id_shop
			" . ((!empty($sc_agent->id_employee)) ? " INNER JOIN " . _DB_PREFIX_ . "employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '" . (int)$sc_agent->id_employee . "') " : "") . "
			WHERE s.deleted!='1'
			GROUP BY s.id_shop
			ORDER BY s.name";
	$shops = Db::getInstance()->executeS($sql);
}
 if(_r("GRI_CAT_PROPERTIES_GRID_CATEGORY")) { ?>
		prop_tb.addListOption('panel', 'categories', 4, "button", '<?php echo _l('Categories',1)?>', "lib/img/catalog.png");
		allowed_properties_panel[allowed_properties_panel.length] = "categories";
	<?php } ?>
	
	var for_mb = 0;

	prop_tb.addButton('categ_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
	prop_tb.setItemToolTip('categ_refresh','<?php echo _l('Refresh',1)?>');
	prop_tb.addButtonTwoState('categ_filter', 100, "", "lib/img/filter.png", "lib/img/filter.png");
	prop_tb.setItemToolTip('categ_filter','<?php echo _l('Display only categories used by selected products',1)?>');
	<?php if(SCMS) { ?>
	prop_tb.addButtonTwoState('for_mb', 100, "", "lib/img/folder_table.png", "lib/img/folder_table.png");
	prop_tb.setItemToolTip('for_mb','<?php echo _l('Only display categories associated to the selected shop',1)?>');
	prop_tb.setItemState('for_mb', 1);
	for_mb = 1;
	<?php } ?>
	prop_tb.addButton('categ_go',100,'','lib/img/folder_go.png','lib/img/folder_go.png');
	prop_tb.setItemToolTip('categ_go','<?php echo _l('Open and select category',1)?>');
	prop_tb.addButton('categ_expand',100,'','lib/img/arrow_out.png','lib/img/arrow_out.png');
	prop_tb.setItemToolTip('categ_expand','<?php echo _l('Expand all items',1)?>');
	prop_tb.addButton('categ_collapse',100,'','lib/img/arrow_in.png','lib/img/arrow_in.png');
	prop_tb.setItemToolTip('categ_collapse','<?php echo _l('Collapse all items',1)?>');
	prop_tb.addButton('categ_multi_add',100,'','lib/img/chart_organisation_add_v.png','lib/img/chart_organisation_add_v.png');
	prop_tb.setItemToolTip('categ_multi_add','<?php echo _l('Place selected products in selected categories',1)?>');
	prop_tb.addButton('categ_multi_del',100,'','lib/img/chart_organisation_delete_v.png','lib/img/chart_organisation_delete_v.png');
	prop_tb.setItemToolTip('categ_multi_del','<?php echo _l('Remove selected products from selected categories (if not default category)',1)?>');


	needInitCategories = 1;
	function initCategories(){
		if (needInitCategories)
		{
			prop_tb._categoriesLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._categoriesLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._categoriesGrid = prop_tb._categoriesLayout.cells('a').attachGrid();
			prop_tb._categoriesGrid.setImagePath("lib/js/imgs/");
			prop_tb._categoriesGrid.setFiltrationLevel(-2);
			prop_tb._categoriesGrid.enableTreeCellEdit(0);
			prop_tb._categoriesGrid.enableSmartRendering(true);
/*			prop_tb._categoriesGrid.enableSmartXMLParsing(true);*/
			
			// UISettings
			prop_tb._categoriesGrid._uisettings_prefix='cat_categorypanel';
			prop_tb._categoriesGrid._uisettings_name=prop_tb._categoriesGrid._uisettings_prefix;
		   	prop_tb._categoriesGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._categoriesGrid);
			
			prop_tb._categoriesGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue){
					idxUsed=prop_tb._categoriesGrid.getColIndexById('used');
<?php
	if (_s('CAT_PROD_CAT_DEF_EXT'))
	{
	?>
		if (cInd == idxUsed){
			if(stage==1)
			{
				var selection = cat_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, pId){
					var vars = {"sub_action":prop_tb._categoriesGrid.cells(rId,idxUsed).getValue(),"idlist":pId};
					addCategoryInQueue(rId, "update", cInd, vars);
				});
			}
		}
		<?php

		if (SCMS)
		{
			foreach($shops as $values)
			{
				?>
				idxDefaultShop_<?php echo $values["id_shop"]?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values["id_shop"]?>');
				if (cInd == idxDefaultShop_<?php echo $values["id_shop"]?>)
				{
					if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values["id_shop"]?>).getValue()==1)
					{
						var selection = cat_grid.getSelectedRowId();
						ids=selection.split(',');
						$.each(ids, function(num, pId)
						{
							var vars =
							{
								"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values["id_shop"]?>).getValue(),
								"idlist":pId,
								"id_shop": <?php echo $values["id_shop"]?>
							};
							addCategoryInQueue(rId, "update", cInd, vars);

						});

					}
				}
	<?php	}
		} else
		{?>
			idxDefault=prop_tb._categoriesGrid.getColIndexById('default');
			if (cInd == idxDefault)
			{
				if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
				{
					var selection = cat_grid.getSelectedRowId();
					ids=selection.split(',');
					$.each(ids, function(num, pId)
					{
						var vars = {"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefault).getValue(),"idlist":pId};
						addCategoryInQueue(rId, "update", cInd, vars);
					});
				}
			}
			<?php
		}
		?>
	<?php

	}else
	{
		if (!SCMS) {	?>
			idxDefault=prop_tb._categoriesGrid.getColIndexById('default');
		<?php } ?>
		if (cInd == idxUsed)
		{
			<?php if (SCMS) {
				foreach($shops as $values)
				{ ?>
					idxDefaultShop_<?php echo $values["id_shop"]?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values["id_shop"]?>');
			<?php }
				foreach($shops as $i=>$values)
				{ ?>
					<?php echo ($i>0?"else ":""); ?>if(stage==0 && prop_tb._categoriesGrid.cells(rId,idxUsed).getValue()==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values["id_shop"]?>).getValue()==1)
					return false;
			<?php }
			} else { ?>
			if(stage==0 && prop_tb._categoriesGrid.cells(rId,idxUsed).getValue()==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
				return false;
			<?php } ?>
			else if(stage==1 /*&& !(prop_tb._categoriesGrid.cells(rId,idxUsed).getValue()==0 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)*/)
			{
				var selection = cat_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, pId){
					var vars = {"sub_action":prop_tb._categoriesGrid.cells(rId,idxUsed).getValue(),"idlist":pId};
					addCategoryInQueue(rId, "update", cInd, vars);
				});
			}
		}
		<?php
			if (SCMS)
			{
				foreach($shops as $values)
				{
					?>
					idxDefaultShop_<?php echo $values["id_shop"]?> = prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $values["id_shop"]?>');
					if (cInd == idxDefaultShop_<?php echo $values["id_shop"]?>)
					{
						if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values["id_shop"]?>).getValue()==1)
						{
							prop_tb._categoriesGrid.cells(rId,idxUsed).setValue(1);
							var selection = cat_grid.getSelectedRowId();
							ids=selection.split(',');
							$.each(ids, function(num, pId)
							{
									var vars =
									{
										"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefaultShop_<?php echo $values["id_shop"]?>).getValue(),
										"idlist":pId,
										"id_shop": <?php echo $values["id_shop"]?>
									};
									addCategoryInQueue(rId, "update", cInd, vars);
							});

						}
					}
		<?php	}
			} else
			{?>
				if (cInd == idxDefault)
				{
					if(stage==1 && prop_tb._categoriesGrid.cells(rId,idxDefault).getValue()==1)
					{
						prop_tb._categoriesGrid.cells(rId,idxUsed).setValue(1);
						var selection = cat_grid.getSelectedRowId();
						ids=selection.split(',');
						$.each(ids, function(num, pId)
						{
							var vars = {"sub_action":"default"+prop_tb._categoriesGrid.cells(rId,idxDefault).getValue(),"idlist":pId};
							addCategoryInQueue(rId, "update", cInd, vars);
						});
					}
				}
		<?php
			}
	}
	?>
					return true;
				});
			prop_tb._categoriesGrid.enableMultiselect(true);
			needInitCategories=0;
		}
	}

		prop_tb.attachEvent("onStateChange",function(id,state){
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
			<?php if(SCMS) { ?>
			if (id=='for_mb')
			{
				if (state)
				{
					for_mb=1;
				}else{
					for_mb=0;
				}
				cache_categorypanel_treeticks = [];
				displayCategories('',true);
			}
			<?php } ?>
		});

	function setPropertiesPanel_categories(id){
		if (id=='categories')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('categ_multi_del');
			prop_tb.showItem('categ_multi_add');
			prop_tb.showItem('categ_expand');
			prop_tb.showItem('categ_collapse');
			prop_tb.showItem('categ_refresh');
			prop_tb.showItem('categ_filter');
			<?php if(SCMS) { ?>
			prop_tb.showItem('for_mb');
			<?php } ?>
			prop_tb.showItem('categ_go');
			prop_tb.setItemText('panel', '<?php echo _l('Categories',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/catalog.png');
			needInitCategories = 1;
			initCategories();
			propertiesPanel='categories';
			if (lastProductSelID!=0)
			{
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		}
		if (id=='categ_refresh')
		{
			cache_categorypanel_treeticks = [];
			displayCategories();
		}
		if (id=='categ_go')
		{
			cat_tree.openItem(prop_tb._categoriesGrid.getSelectedRowId());
			cat_tree.selectItem(prop_tb._categoriesGrid.getSelectedRowId(),true);
		}
		if (id=='categ_expand')
		{
			prop_tb._categoriesGrid.expandAll();
		}
		if (id=='categ_collapse')
		{
			prop_tb._categoriesGrid.collapseAll();
		}
		if (id=='categ_multi_add')
		{
			if (prop_tb._categoriesGrid.getSelectedRowId()==null || cat_grid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				var selection = cat_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, pId){
					var vars = {"sub_action":"multi_add","idprod":pId,"idcateg":prop_tb._categoriesGrid.getSelectedRowId()};
					addCategoryInQueue("", "update", "", vars);
				});
			}
		}
		if (id=='categ_multi_del')
		{
			if (prop_tb._categoriesGrid.getSelectedRowId()==null || cat_grid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				var selection = cat_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, pId){
					var vars = {"sub_action":"multi_del","idprod":pId,"idcateg":prop_tb._categoriesGrid.getSelectedRowId()};
					addCategoryInQueue("", "update", "", vars);
				});
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_categories);


cache_categorypanel_treeticks = [];
function displayCategories(callback,force_refresh)
{
	if (prop_tb._categoriesGrid._rowsNum>0 && force_refresh!=true)
	{
		$.post("index.php?ajax=1&act=cat_categorypanel_relation_get&for_mb="+for_mb+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId()},function(data){
				if (data!='')
				{
					if (cache_categorypanel_treeticks.length == 0)
						prop_tb._categoriesGrid.uncheckAll();
					dataArray=data.split('|');
					selArray=dataArray[0].split(',');

					selArray.forEach(function(id){
						var not_associate_FF = cat_tree.getUserData(id,"not_associate_FF");
						if(not_associate_FF == "" || not_associate_FF == "undefined" || not_associate_FF == null){
							not_associate_FF = 0;
						}
						if(not_associate_FF=="0")
						{
							if (prop_tb._categoriesGrid.doesRowExist(id) && (!(id in cache_categorypanel_treeticks) || cache_categorypanel_treeticks[id] == 0)) {
								prop_tb._categoriesGrid.cellById(id,1).setValue(1);
								cache_categorypanel_treeticks[id] = 1;
							}
						}
					});

					if (dataArray[1]!=undefined && dataArray[1]!='')
					{
					<?php if (SCMS)
						{?>
							selArray=dataArray[1].split(',');
							for(var i=0;i< selArray.length; i++)
							{
								middleArray=selArray[i].split('_');
								idxColNum=prop_tb._categoriesGrid.getColIndexById('default_shop_'+middleArray[0]);
								if (prop_tb._categoriesGrid.doesRowExist(middleArray[1]))
									prop_tb._categoriesGrid.cellById(middleArray[1],idxColNum).setChecked(1);
							}
					<?php } else
					{?>
						if (prop_tb._categoriesGrid.doesRowExist(dataArray[1]))
							prop_tb._categoriesGrid.cellById(dataArray[1],3).setChecked(1);
					<?php }?>
					}
					else
					{
						<?php if (SCMS)
						{?>
							prop_tb._categoriesGrid.forEachRow(function(id)
							{
								<?php


								foreach($shops as $shop)
								{
								?>
									idxColNum=prop_tb._categoriesGrid.getColIndexById('default_shop_<?php echo $shop["id_shop"]; ?>');
									if(idxColNum!=undefined && idxColNum!="" && idxColNum!=null && idxColNum!=0)
										prop_tb._categoriesGrid.cellById(id,idxColNum).setChecked(0);
								<?php
								}
								?>
							});
						<?php } else
						{ ?>
								prop_tb._categoriesGrid.forEachRow(function(id)
								{
									prop_tb._categoriesGrid.cellById(id,3).setChecked(0);
								});

						<?php }?>
					}
					if (categoriesFilter)
					{
						prop_tb._categoriesGrid.filterTreeBy(1,1,0);
						prop_tb._categoriesGrid.expandAll();
					}else{
						prop_tb._categoriesGrid.filterTreeBy(1,'',0);
					}
					if (prop_tb._categoriesGrid.getFilterElement(2)!=null && prop_tb._categoriesGrid.getFilterElement(2).value!='')
						prop_tb._categoriesGrid.filterTreeBy(2,prop_tb._categoriesGrid.getFilterElement(2).value,1);
				
		    		// UISettings
					loadGridUISettings(prop_tb._categoriesGrid);

<?php
	if (_s('CAT_PRODPROP_CAT_SHOW_SUBCATCNT'))
	{
?>
					setNbSelected();
<?php
	}
?>
					
					// UISettings
					prop_tb._categoriesGrid._first_loading=0;
				}
			});
	}else{
		if ((cat_grid.getSelectedRowId()==null || cat_grid.getSelectedRowId()=='') && force_refresh!=true) return false;
		prop_tb._categoriesGrid.clearAll(true);
		prop_tb._categoriesGrid.loadXML("index.php?ajax=1&act=cat_categorypanel_get&for_mb="+for_mb+"&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG,function()
				{

					nb=prop_tb._categoriesGrid.getRowsNum();
					prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('categories')?>":" <?php echo _l('category')?>"));
					prop_tb._categoriesGrid._rowsNum=nb;
				
		    		// UISettings
					loadGridUISettings(prop_tb._categoriesGrid);
					
					// UISettings
					prop_tb._categoriesGrid._first_loading=0;
				
					displayCategories();
					
					if (callback!='') eval(callback);
			});

	}
}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='categories'){
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		});

function setNbSelected()
{
	_setNbSelected("");
}
function _setNbSelected(parent_id)
{
	var nb_count = 0;
	
	var row_n = prop_tb._categoriesGrid.getSubItems(parent_id);
	
	if(row_n!=undefined && row_n!=null && row_n!="")
	{
		var rows = row_n.split(",");
		$.each(rows, function(num, id){
			var checked = prop_tb._categoriesGrid.cellById(id,1).getValue();
			if(checked==true)
			{
				nb_count = nb_count*1 + 1;
			}
			
			var nb_children = _setNbSelected(id);
			
			var text_base = prop_tb._categoriesGrid.cellById(id,2).getValue();
			var exp = text_base.split("<strong>");
			text_base = exp[0];
			var text = text_base+" <strong>["+nb_children+"]</strong>";
			prop_tb._categoriesGrid.cellById(id,2).setValue(text);
			
			nb_count = nb_count*1 + nb_children;
		});
	}
	return nb_count;
}

function addCategoryInQueue(rId, action, cIn, vars)
{
	var params = {
		name: "cat_categorypanel_update_queue",
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
	// USER DATA
		/*if(rId!=undefined && rId!=null && rId!="" && rId!=0)
		{
			$.each(prop_tb._categoriesGrid.UserData[rId].keys, function(i, key){
				params.params[key] = prop_tb._categoriesGrid.UserData[rId].values[i];
			});
		}
		$.each(prop_tb._categoriesGrid.UserData.gridglobaluserdata.keys, function(i, key){
			params.params[key] = prop_tb._categoriesGrid.UserData.gridglobaluserdata.values[i];
		});*/
	
	params.params = JSON.stringify(params.params);
	addInUpdateQueue(params,prop_tb._categoriesGrid);
}
		
// CALLBACK FUNCTION
function callbackCategory(sid,action,tid,xml)
{
	if (action=='update')
	{
		prop_tb._categoriesGrid.setRowTextNormal(sid);
		
		if(xml!=undefined && xml!=null && xml!="" && xml!=0)
		{
			var reload_cat = xml.reload_cat;
			if (reload_cat=='1')
				displayTree();
			var refresh_cat = xml.refresh_cat;
			if (refresh_cat=='1')
			{
				cache_categorypanel_treeticks = [];
				displayCategories();
			}
		}
	}
}
