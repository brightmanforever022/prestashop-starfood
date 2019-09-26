<script type="text/javascript">
	var lPageNotFound = new dhtmlXLayoutObject(wPageNotFound, "1C");
	lPageNotFound.cells('a').hideHeader();
	pagenotfound_grid=lPageNotFound.cells('a').attachGrid();
	pagenotfound_grid.setImagePath('lib/js/imgs/');
	pagenotfound_grid.setDateFormat("%Y-%m-%d");
	pagenotfound_grid.enableSmartRendering(true);
	pagenotfound_grid.init();

	pagenotfound_tb=lPageNotFound.cells('a').attachToolbar();
	pagenotfound_tb.addButton("delete404", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	pagenotfound_tb.setItemToolTip('delete404','<?php
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
 echo _l('Delete all items',1)?>');
	pagenotfound_tb.addButton("exportcsv", 0, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	pagenotfound_tb.setItemToolTip('exportcsv','<?php echo _l('Export',1)?>');
	pagenotfound_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	pagenotfound_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid',1)?>');
	pagenotfound_tb.attachEvent("onClick",
		function(id){
			if (id=='refresh'){
				displayPageNotFound();
			}
			if (id=='exportcsv'){
				pagenotfound_grid.enableCSVHeader(true);
				pagenotfound_grid.setCSVDelimiter("\t");
				var csv=pagenotfound_grid.serializeToCSV(true);
				displayQuickExportWindow(csv);
			}
			if (id=='delete404'){
				if (confirm('<?php echo _l('Do you want to delete all items?',1);?>'))
					$.get("index.php?ajax=1&act=ser_win-pagenotfound404_update&action=deleteall",function(data){
									displayPageNotFound();
									dhtmlx.message({text:data,type:'info',expire:5000});
								});
			}
		});

	function displayPageNotFound(callback)
	{
		pagenotfound_grid.clearAll();
		pagenotfound_grid.load("index.php?ajax=1&act=ser_win-pagenotfound404_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
			if (callback!='') eval(callback);
			});
	}
	displayPageNotFound();
</script>