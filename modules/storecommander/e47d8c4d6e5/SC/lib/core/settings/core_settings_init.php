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
	var lSettings = new dhtmlXLayoutObject(wCoreSettings, "1C");
	lSettings.cells('a').hideHeader();
	settings_grid=lSettings.cells('a').attachGrid();
	settings_grid.setImagePath('lib/js/imgs/');
	settings_grid.setHeader("<?php echo _l('Tool')?>,<?php echo _l('Section')?>,<?php echo _l('Item')?>,<?php echo _l('Value')?>,<?php echo _l('Description')?>,<?php echo _l('Default value')?>");
	settings_grid.setColumnIds("section1,section2,id,value,description,default_value");
	settings_grid.setInitWidths("75,75,200,100,400,100");
	settings_grid.setColAlign("left,left,left,left,left,left");
	settings_grid.setColTypes("ro,ro,ro,ed,ro,ro");
  settings_grid.enableSmartRendering(true);
  settings_grid.enableMultiline(true);
	settings_grid.setColSorting("str,str,str,str,str,str");
	settings_grid.attachHeader("#select_filter,#select_filter,#text_filter,#text_filter,#text_filter,#text_filter");
	settings_grid.init();

	settingsDataProcessorURLBase="index.php?ajax=1&act=core_settings_update";
	settingsDataProcessor = new dataProcessor(settingsDataProcessorURLBase);
	settingsDataProcessor.enableDataNames(true);
	settingsDataProcessor.enablePartialDataSend(true);
	settingsDataProcessor.setUpdateMode('cell');
	settingsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
			if (action=='updateAndRefresh')
				dhtmlx.message({text:'<?php echo _l('You need to refresh Store Commander to use the new settings.')?>',type:'error'});
		});

	settingsDataProcessor.init(settings_grid);  

	settings_tb=lSettings.cells('a').attachToolbar();
//	settings_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
//	settings_tb.setItemToolTip('help','<?php echo _l('Help')?>');
	settings_tb.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
	settings_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	settings_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	settings_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');
	settings_tb.attachEvent("onClick",
		function(id){
			if (id=='help'){
				<?php /*echo "window.open('".getHelpLink('settings')."');";*/ ?>
			}
			if (id=='refresh'){
				displaySettings();
			}
		});


	settings_tb.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				settings_grid.enableLightMouseNavigation(true);
			}else{
				settings_grid.enableLightMouseNavigation(false);
			}
		}
	});


	function displaySettings(callback)
	{
		settings_grid.clearAll();
		settings_grid.load("index.php?ajax=1&act=core_settings_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
	    getRowsNum=settings_grid.getRowsNum();
	    settings_grid.filterByAll();
			if (callback!='') eval(callback);
			});
	}

	displaySettings();
</script>