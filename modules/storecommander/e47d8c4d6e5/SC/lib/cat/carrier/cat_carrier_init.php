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

if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
	
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_CARRIER")) { ?>
		prop_tb.addListOption('panel', 'carrier', 5, "button", '<?php echo _l('Carriers',1)?>', "lib/img/lorry.png");
		allowed_properties_panel[allowed_properties_panel.length] = "carrier";
	<?php } ?>

	prop_tb.addButton("carrier_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('carrier_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButton("carrier_mass_add", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
	prop_tb.setItemToolTip('carrier_mass_add','<?php echo _l('Add all the selected products to all the selected carriers',1)?>');
	prop_tb.addButton("carrier_mass_delete", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
	prop_tb.setItemToolTip('carrier_mass_delete','<?php echo _l('Delete all the selected products to all the selected carriers',1)?>');
	
	
	needInitCarrier = 1;
	function initCarrier()
	{
		if (needInitCarrier)
		{
			prop_tb._carrierLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._carrierLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();

			prop_tb._carrierGrid = prop_tb._carrierLayout.cells('a').attachGrid();
			prop_tb._carrierGrid._name='_carrierGrid';
			prop_tb._carrierGrid.setImagePath("lib/js/imgs/");
  			prop_tb._carrierGrid.enableDragAndDrop(false);
			prop_tb._carrierGrid.enableMultiselect(true);
			/*prop_tb._carrierGrid.enableAutoSaving('cg_cat_carrier',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
			prop_tb._carrierGrid.enableAutoHiddenColumnsSaving('cg_cat_carrier_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/
			
			// UISettings
			prop_tb._carrierGrid._uisettings_prefix='cat_carrier';
			prop_tb._carrierGrid._uisettings_name=prop_tb._carrierGrid._uisettings_prefix;
		   	prop_tb._carrierGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._carrierGrid);

			prop_tb._carrierGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
			{
				if(stage==1)
				{
					var value = prop_tb._carrierGrid.cells(rId,cInd).isChecked();
					var selection = cat_grid.getSelectedRowId();
					ids=selection.split(',');
					$.each(ids, function(num, pId){
						var vars = {"sub_action":"present","value":value,"idlist":pId};
						addCarrierInQueue(rId, "update", cInd, vars);
					});
				}
				
				return true;
			});
			
			needInitCarrier=0;
		}
	}
	function setPropertiesPanel_carrier(id){
		if (id=='carrier')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('carrier_refresh');
			prop_tb.showItem('carrier_mass_add');
			prop_tb.showItem('carrier_mass_delete');
			prop_tb.setItemText('panel', '<?php echo _l('Carriers',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/lorry.png');
			needInitCarrier = 1;
			initCarrier();
			propertiesPanel='carrier';
			if (lastProductSelID!=0)
			{
				displayCarrier();
			}
		}
		if (id=='carrier_refresh')
		{
			displayCarrier();
		}
		if (id=='carrier_mass_add')
		{
			var selection = cat_grid.getSelectedRowId();
			ids=selection.split(',');
			$.each(ids, function(num, pId){
				var vars = {"sub_action":"mass_add","carriers":prop_tb._carrierGrid.getSelectedRowId(),"idlist":pId};
				addCarrierInQueue("", "update", "", vars);
			});
		}
		if (id=='carrier_mass_delete')
		{
			var selection = cat_grid.getSelectedRowId();
			ids=selection.split(',');
			$.each(ids, function(num, pId){
				var vars = {"sub_action":"mass_delete","carriers":prop_tb._carrierGrid.getSelectedRowId(),"idlist":pId};
				addCarrierInQueue("", "update", "", vars);
			});
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_carrier);
	
	function displayCarrier()
	{
		prop_tb._carrierGrid.clearAll(true);
		var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cat_carrier_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			prop_tb._carrierGrid.parse(data);
			nb=prop_tb._carrierGrid.getRowsNum();
			prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('carriers')?>":" <?php echo _l('carrier')?>"));
				prop_tb._carrierGrid._rowsNum=nb;
				
    		// UISettings
				loadGridUISettings(prop_tb._carrierGrid);
				prop_tb._carrierGrid._first_loading=0;
		});
	}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='carrier'){
				//initCarrier();
				displayCarrier();
			}
		});
	
function addCarrierInQueue(rId, action, cIn, vars)
{
	var params = {
		name: "cat_carrier_update_queue",
		row: rId,
		action: "update",
		params: {},
		callback: "callbackCarrier('"+rId+"','update','"+rId+"');"
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
			$.each(prop_tb._carrierGrid.UserData[rId].keys, function(i, key){
				params.params[key] = prop_tb._carrierGrid.UserData[rId].values[i];
			});
		}
		$.each(prop_tb._carrierGrid.UserData.gridglobaluserdata.keys, function(i, key){
			params.params[key] = prop_tb._carrierGrid.UserData.gridglobaluserdata.values[i];
		});*/
	
	params.params = JSON.stringify(params.params);
	addInUpdateQueue(params,prop_tb._carrierGrid);
}
		
// CALLBACK FUNCTION
function callbackCarrier(sid,action,tid)
{
	if (action=='update')
	{
		prop_tb._carrierGrid.setRowTextNormal(sid);
		displayCarrier('',0);
	}
}
	
<?php } ?>