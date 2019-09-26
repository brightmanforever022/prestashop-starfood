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
<script type="text/javascript">
	dhxlFeatures=wFeatures.attachLayout("2U");
	wFeatures._sb=dhxlFeatures.attachStatusBar();
	dhxlFeatures.cells('a').setText("<?php echo _l('Features')?>");
	wFeatures.tbFeatures=dhxlFeatures.cells('a').attachToolbar();
	<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
	wFeatures.tbFeatures.addButton("feature_setposition", 100, "", "lib/img/layers.png", "lib/img/layers_dis.png");
	wFeatures.tbFeatures.setItemToolTip('feature_setposition','<?php echo _l('Save features positions',1)?>');
	<?php } ?>
	wFeatures.tbFeatures.addButton("del_feature", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	wFeatures.tbFeatures.setItemToolTip('del_feature','<?php echo _l('Delete selected features and their values')?>');
	wFeatures.tbFeatures.addButton("duplicate_feature", 0, "", "lib/img/page_copy2.png", "lib/img/page_copy2.png");
	wFeatures.tbFeatures.setItemToolTip('duplicate_feature','<?php echo _l('Duplicate selected features')?>');
	wFeatures.tbFeatures.addButton("add_feature", 0, "", "lib/img/add.png", "lib/img/add.png");
	wFeatures.tbFeatures.setItemToolTip('add_feature','<?php echo _l('Create a new feature')?>');
	wFeatures.tbFeatures.addButton("exportcsv_features",100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	wFeatures.tbFeatures.setItemToolTip('exportcsv_features','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
	if (isIPAD)
	{
		wFeatures.tbFeatures.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		wFeatures.tbFeatures.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	wFeatures.tbFeatures.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wFeatures.tbFeatures.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');
	wFeatures.tbFeatures.attachEvent("onClick",
		function(id){
			if (id=='refresh')
			{
				displayFeaturesList();
			}
			if (id=='add_feature')
			{
				var newId = new Date().getTime();
				wFeatures.gridFeatures.addRow(newId,[newId,"new"]);
			}
			if (id=='duplicate_feature')
			{
				if (wFeatures.gridFeatures.getSelectedRowId() && confirm('<?php echo _l('Are you sure to duplicate the selected features and their values?',1)?>'))
					$.post("index.php?ajax=1&act=cat_win-feature_value_update",{'features':wFeatures.gridFeatures.getSelectedRowId(),'id_lang':SC_ID_LANG,'!nativeeditor_status':'duplicated'},function(data){displayFeaturesList();});
			}
			if (id=='del_feature')
			{
				if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
				{
					wFeatures.gridFeatures.deleteSelectedRows();
					wFeatures.gridFValues.clearAll(true);
				}
			}
			if (id=='feature_setposition'){
				if (wFeatures.gridFeatures.getRowsNum()>0)
				{
					var positions='';
					var idx=0;
					var i = 1 ;
					wFeatures.gridFeatures.forEachRow(function(id){
							positions+=id+','+wFeatures.gridFeatures.getRowIndex(id)+';';
							idx++;
						});
					$.post("index.php?ajax=1&act=cat_win-feature_update&action=position&"+new Date().getTime(),{ positions: positions },function(){
						displayFeaturesList();
					});
				}
			}
			if (id=='exportcsv_features')
			{
				wFeatures.gridFeatures.enableCSVHeader(true);
				wFeatures.gridFeatures.setCSVDelimiter("\t");
				var csv = wFeatures.gridFeatures.serializeToCSV(true);
				displayQuickExportWindow(csv, 1);
			}
		});
	wFeatures.gridFeatures=dhxlFeatures.cells('a').attachGrid();
	wFeatures.gridFeatures.setImagePath("lib/js/imgs/");
	wFeatures.gridFeatures.enableMultiselect(true);
	<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
	wFeatures.gridFeatures.enableDragAndDrop(true);
	<?php } ?>
	
	// UISettings
	wFeatures.gridFeatures._uisettings_prefix='cat_win-feature';
	wFeatures.gridFeatures._uisettings_name=wFeatures.gridFeatures._uisettings_prefix;
	wFeatures.gridFeatures._first_loading=1;
   	
	// UISettings
	initGridUISettings(wFeatures.gridFeatures);
	
	wFeatures.gridFeatures.attachEvent("onEditCell", function(stage, rId, cIn){
			if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
			return true;
		});
	featuresDataProcessorURLBase="index.php?ajax=1&act=cat_win-feature_update&id_lang="+SC_ID_LANG;
	featuresDataProcessor = new dataProcessor(featuresDataProcessorURLBase);
	featuresDataProcessor.enableDataNames(true);
	featuresDataProcessor.enablePartialDataSend(true);
	featuresDataProcessor.setUpdateMode('cell');
	featuresDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
			if (action=='insert')
			{
				wFeatures.gridFeatures.cells(tid,0).setValue(tid);
			}
			return true;
		});
	featuresDataProcessor.init(wFeatures.gridFeatures);  

	wFeatures.gridFValues=dhxlFeatures.cells('b').attachGrid();
	wFeatures.gridFValues.setImagePath("lib/js/imgs/");
	/*wFeatures.gridFValues.enableAutoSaving('cg_cat_fvalues');
	wFeatures.gridFValues.enableAutoHiddenColumnsSaving('cg_cat_fvalues');*/
	
	// UISettings
	wFeatures.gridFValues._uisettings_prefix='cat_win-feature_value';
	wFeatures.gridFValues._uisettings_name=wFeatures.gridFValues._uisettings_prefix;
	wFeatures.gridFValues._first_loading=1;
   	
	// UISettings
	initGridUISettings(wFeatures.gridFValues);
	
	wFeatures.gridFValues.attachEvent("onEditCell", function(stage, rId, cIn){
			if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
			return true;
		});

	FValuesDataProcessorURLBase="index.php?ajax=1&act=cat_win-feature_value_update&id_lang="+SC_ID_LANG;
	FValuesDataProcessor = new dataProcessor(FValuesDataProcessorURLBase);
	FValuesDataProcessor.enableDataNames(true);
	FValuesDataProcessor.enablePartialDataSend(true);
	FValuesDataProcessor.setUpdateMode('cell');
	FValuesDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
			if (action=='insert')
			{
				wFeatures.gridFValues.cells(tid,0).setValue(tid);
			}
			return true;
		});
	FValuesDataProcessor.init(wFeatures.gridFValues);  
	
	lastFeatureSelID=0;
	function doOnFeatureSelected(idfeature){
		if (lastFeatureSelID!=idfeature)
		{
			lastFeatureSelID=idfeature;
			displayFValues(idfeature);
		}
	}
	wFeatures.gridFeatures.attachEvent("onRowSelect",doOnFeatureSelected);

	displayFeaturesList();

	dhxlFeatures.cells('b').setText("<?php echo _l('Feature values')?>");
	wFeatures.tbAttr=dhxlFeatures.cells('b').attachToolbar();
	wFeatures.tbAttr.addButton("merge_feat", 0, "", "lib/img/shape_move_front.png", "lib/img/shape_move_front.png");
	wFeatures.tbAttr.setItemToolTip('merge_feat','<?php echo _l('Merge selected Features',1)?>');
	wFeatures.tbAttr.addButton("del_attr", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	wFeatures.tbAttr.setItemToolTip('del_attr','<?php echo _l('Delete selected values')?>');
	wFeatures.tbAttr.addButton("add_attr", 0, "", "lib/img/add.png", "lib/img/add.png");
	wFeatures.tbAttr.setItemToolTip('add_attr','<?php echo _l('Create new feature value')?>');
	wFeatures.tbAttr.addInput("add_input", 0,"1",30);
	wFeatures.tbAttr.setItemToolTip('add_input','<?php echo _l('Number of values to create when clicking on the Create button')?>');
	wFeatures.tbAttr.addButton("exportcsv_features_value",100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	wFeatures.tbAttr.setItemToolTip('exportcsv_features_value','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
	if (isIPAD)
	{
		wFeatures.tbAttr.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		wFeatures.tbAttr.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	wFeatures.tbAttr.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wFeatures.tbAttr.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');


	wFeatures.tbAttr.attachEvent("onClick",
		function(id){
			if (id=='merge_feat')
			{
				if (wFeatures.gridFValues.getSelectedRowId()==null || wFeatures.gridFValues.getSelectedRowId().split(',').length<2)
				{
					alert('<?php echo _l('You must select at least two items',1)?>');
				}else if (confirm('<?php echo _l('Are you sure you want to merge the selected items?',1)?>'))
				{
					$.post("index.php?ajax=1&act=cat_win-feature_value_update&action=merge",{'featlist':wFeatures.gridFValues.getSelectedRowId()},function(data){
						displayFValues(lastFeatureSelID);
					});
				}
			}
			if (id=='refresh')
			{
				displayFValues(lastFeatureSelID);
			}
			if (id=='add_attr')
			{
				if (lastFeatureSelID!=0)
				{
					var newId = new Date().getTime();
					nb=wFeatures.tbAttr.getValue('add_input');
					if (isNaN(nb)) nb=1;
					for (i=1;i<=nb;i++)
					{
						col2data="";
						if (wFeatures.gridFeatures.cells(lastFeatureSelID,1).getValue()==1) col2data="#000000";
						wFeatures.gridFValues.addRow(newId*100+i,[newId*100+i,col2data]);
					}
				}
			}
			if (id=='del_attr')
			{
				if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					wFeatures.gridFValues.deleteSelectedRows();
			}
			if (id=='exportcsv_features_value')
			{
				wFeatures.gridFValues.enableCSVHeader(true);
				wFeatures.gridFValues.setCSVDelimiter("\t");
				var csv = wFeatures.gridFValues.serializeToCSV(true);
				displayQuickExportWindow(csv, 1);
			}
		});

	wFeatures.tbFeatures.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				wFeatures.gridFeatures.enableLightMouseNavigation(true);
			}else{
				wFeatures.gridFeatures.enableLightMouseNavigation(false);
			}
		}
	});

	wFeatures.tbAttr.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				wFeatures.gridFValues.enableLightMouseNavigation(true);
			}else{
				wFeatures.gridFValues.enableLightMouseNavigation(false);
			}
		}
	});



//#####################################
//############ Load functions
//#####################################

function displayFeaturesList()
{
	wFeatures.gridFeatures.clearAll(true);
	wFeatures.gridFeatures.loadXML("index.php?ajax=1&act=cat_win-feature_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
				nb=wFeatures.gridFeatures.getRowsNum();
				wFeatures._sb.setText(nb+(nb>1?" <?php echo _l('features')?>":" <?php echo _l('feature')?>"));
    		// UISettings
				loadGridUISettings(wFeatures.gridFeatures);
				wFeatures.gridFeatures._first_loading=0;
			});
}

function displayFValues()
{
	wFeatures.gridFValues.clearAll(true);
	if (lastFeatureSelID!=0)
		wFeatures.gridFValues.loadXML("index.php?ajax=1&act=cat_win-feature_value_get&id_feature="+lastFeatureSelID+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
				FValuesDataProcessor.serverProcessor=FValuesDataProcessorURLBase+"&id_feature="+lastFeatureSelID;
				nb=wFeatures.gridFeatures.getRowsNum();
				nb2=wFeatures.gridFValues.getRowsNum();
				wFeatures._sb.setText(nb+(nb>1?" <?php echo _l('features')?>":" <?php echo _l('feature')?>")+" / "+nb2+(nb2>1?" <?php echo _l('feature values')?>":" <?php echo _l('feature values')?>"));
    		// UISettings
				loadGridUISettings(wFeatures.gridFValues);
				wFeatures.gridFValues._first_loading=0;
			});
}
</script>