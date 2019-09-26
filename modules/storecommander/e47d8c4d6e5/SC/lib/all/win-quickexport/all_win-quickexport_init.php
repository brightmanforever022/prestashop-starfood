<div id="divQuickExport" style="margin:10px;display:none;"><textarea id="taQuickExport" style="width:400px;height:200px"></textarea></div>
<script type="text/javascript">
	function displayQuickExportWindow(data,firstline){
		if (!dhxWins.isWindow("wQuickExportWindow"))
		{
			wQuickExportWindow = dhxWins.createWindow("wQuickExportWindow", 50, 50, 450, 460);
			wQuickExportWindow.setIcon('lib/img/page_excel.png','../../../lib/img/page_excel.png');
			wQuickExportWindow.setText("<?php
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
 echo _l('Quick export window') ?>");
			lQEW = new dhtmlXLayoutObject(wQuickExportWindow, "1C");
			lQEW.cells('a').hideHeader();
			wQuickExportWindow.attachEvent("onClose", function(win){
					wQuickExportWindow.hide();
					return false;
				});
			lQEW.cells('a').appendObject('divQuickExport');
			$('#divQuickExport').css('display','block');
			wQuickExportWindow._add_prop_tb=wQuickExportWindow.attachToolbar();
			wQuickExportWindow._add_prop_tb.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
			wQuickExportWindow._add_prop_tb.setItemToolTip('selectall','<?php echo _l('Select all')?>');
			// events
			wQuickExportWindow._add_prop_tb.attachEvent("onClick",function(id){
					if (id=="selectall")
						$('#taQuickExport').select();
				});
		}else{
			wQuickExportWindow.show();
		}
		data_array=data.split("\n");
		firstlinecontent=data_array.splice(0,1);
		content=data_array.splice(firstline,data_array.length);
		$('#taQuickExport').text(firstlinecontent.concat(content).join("\n"));
		$('#taQuickExport').select();
	}
</script>