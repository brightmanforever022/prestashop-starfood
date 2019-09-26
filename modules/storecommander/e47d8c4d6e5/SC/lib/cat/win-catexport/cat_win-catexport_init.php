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
	dhxlCatImport=wCatExport.attachLayout("1C");
	dhxlCatImport.cells('a').hideHeader();

	wCatExport.gridExport=dhxlCatImport.cells('a').attachGrid();
	wCatExport.gridExport.setImagePath("lib/js/imgs/");
	wCatExport.gridExport.enableColumnMove(true);
	wCatExport.gridExport.enableMultiline(true);
	wCatExport.gridExport.init();

	wCatExport.tbOptions=dhxlCatImport.cells('a').attachToolbar();
	wCatExport.tbOptions.addButton("exportcsv", 0, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	wCatExport.tbOptions.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
	wCatExport.tbOptions.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wCatExport.tbOptions.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	wCatExport.tbOptions.attachEvent("onClick",
		function(id){
			if (id=='refresh')
			{
				displayCatExport();
			}
			if (id=='exportcsv'){
				var colNum=wCatExport.gridExport.getColumnsNum()*1 - 1;
				for(var i=colNum; i>0; i--)
				{
					var isHidden=wCatExport.gridExport.isColumnHidden(i);
					if(isHidden)
						wCatExport.gridExport.deleteColumn(i);
				}

				wCatExport.gridExport.enableCSVHeader(true);
				wCatExport.gridExport.setCSVDelimiter("\t");
				var csv=wCatExport.gridExport.serializeToCSV(true);
				displayQuickExportWindow(csv,1);
			}
		});

	displayCatExport();
	
//#####################################
//############ Load functions
//#####################################

function displayCatExport(callback)
{
	wCatExport.gridExport.clearAll(true);
	wCatExport.gridExport.loadXML("index.php?ajax=1&act=cat_win-catexport_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
		if (callback)
		{
			eval(callback);
		}
		wCatExport.gridExport.enableHeaderMenu();
	});
}
</script>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopCatAlert();">Click here to close alert.</div>