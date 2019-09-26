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
 if(_r("GRI_CAT_PROPERTIES_GRID_CUSTOMIZED_FIELDS")) { ?>
		prop_tb.addListOption('panel', 'customizations', 10, "button", '<?php echo _l('Customization fields',1)?>', "lib/img/textfield_rename.png");
		allowed_properties_panel[allowed_properties_panel.length] = "customizations";
	<?php } ?>

	prop_tb.addButton("customization_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('customization_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButtonTwoState('customization_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
	prop_tb.setItemToolTip('customization_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	prop_tb.addButton("customization_add", 100, "", "lib/img/textfield_add.png", "lib/img/textfield_add.png");
	prop_tb.setItemToolTip('customization_add','<?php echo _l('Add fields',1)?>');
	prop_tb.addButton("customization_del", 100, "", "lib/img/textfield_delete.png", "lib/img/textfield_delete.png");
	prop_tb.setItemToolTip('customization_del','<?php echo _l('Delete selected fields',1)?>');



	needInitCustomizations = 1;
	function initCustomizations(){
		if (needInitCustomizations)
		{
			prop_tb._customizationsLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._customizationsLayout.cells('a').hideHeader();
			customization_grid=prop_tb._customizationsLayout.cells('a').attachGrid();
			dhxLayout.cells('b').showHeader();
			customization_grid.setImagePath('lib/js/imgs/');
			customization_grid.enableSmartRendering(true);
			customization_grid.enableMultiselect(true);
			/*customization_grid.enableAutoSaving('cg_cat_customiz',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
			customization_grid.enableAutoHiddenColumnsSaving('cg_cat_customiz_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/
			
			// UISettings
			customization_grid._uisettings_prefix='cat_customization';
			customization_grid._uisettings_name=customization_grid._uisettings_prefix;
		   	customization_grid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(customization_grid);

			// update customization/product after used checkbox
			function onEditCellCustomizations(stage,rId,cInd,nValue,oValue){
				idxType=customization_grid.getColIndexById('type');
				if (cInd == idxType){
					if(stage==2)
						$.post("index.php?ajax=1&act=cat_customization_update&customization_list="+rId+"&action=updateType&value="+customization_grid.cells(rId,idxType).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_product":cat_grid.getSelectedRowId()},function(data){});
				}
				idxRequired=customization_grid.getColIndexById('required');
				if (cInd == idxRequired){
					if(stage==2)
						$.post("index.php?ajax=1&act=cat_customization_update&customization_list="+rId+"&action=updateRequired&value="+customization_grid.cells(rId,idxRequired).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"id_product":cat_grid.getSelectedRowId()},function(data){});
				}
				cName=customization_grid.getColumnId(cInd);
				if (cName.substr(0,4) == 'name'){
					if (stage==2)
						$.post("index.php?ajax=1&act=cat_customization_update&customization_list="+rId+"&action=updateName&"+cName+"="+nValue+"&"+new Date().getTime(),{"id_product":cat_grid.getSelectedRowId()},function(data){});
				}
				return true;
			}
			customization_grid.attachEvent("onEditCell",onEditCellCustomizations);
			displayCustomizations();
			needInitCustomizations=0;
		}
	}



	function setPropertiesPanel_customizations(id){
		if (id=='customizations')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('customization_del');
			prop_tb.showItem('customization_add');
			prop_tb.showItem('customization_lightNavigation');
			prop_tb.showItem('customization_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Customization fields',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/textfield_rename.png');
			needInitCustomizations=1;
			initCustomizations();
			propertiesPanel='customizations';
		}
				if (id=='customization_add'){
					$.post("index.php?ajax=1&act=cat_customization_update&action=insert&"+new Date().getTime(),{"id_product":cat_grid.getSelectedRowId()},function(data){
							displayCustomizations();
						});
				}
				if (id=='customization_del'){
					if (confirm('<?php echo _l('Are you sure?',1)?>'))
						$.post("index.php?ajax=1&act=cat_customization_update&action=delete&"+new Date().getTime(),{"customization_list":customization_grid.getSelectedRowId(),"id_product":cat_grid.getSelectedRowId()},function(data){
								customization_grid.deleteSelectedRows();
							});
				}
				if (id=='customization_refresh'){
					displayCustomizations();
				}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_customizations);

	function setPropertiesPanelState_customization(id,state){
		if (id=='customization_lightNavigation')
		{
			if (state)
			{
				customization_grid.enableLightMouseNavigation(true);
			}else{
				customization_grid.enableLightMouseNavigation(false);
			}
		}
	}
	prop_tb.attachEvent("onStateChange", setPropertiesPanelState_customization);

	function displayCustomizations(callback)
	{
		customization_grid.clearAll(true);
		prop_tb._sb.setText('');
		prop_tb._sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');
		customization_grid.load("index.php?ajax=1&act=cat_customization_get&product_list="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				nb=customization_grid.getRowsNum();
				prop_tb._sb.setText(nb+' '+(nb>1?'<?php echo _l('customization fields',1)?>':'<?php echo _l('customization field',1)?>'));
				customization_grid._rowsNum=nb;
				
    		// UISettings
				loadGridUISettings(customization_grid);
				customization_grid._first_loading=0;
	    		
				if (callback!='') eval(callback);
			}); 
	}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='customizations'){
				if (cat_grid.getSelectedRowId().indexOf(',')!=-1)
					dhxLayout.cells('b').setText('<?php echo _l('MULTIPLE EDITION',1)?>');
				displayCustomizations();
			}
		});
