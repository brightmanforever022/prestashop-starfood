<script type="text/javascript">
	var lQueueLogs = new dhtmlXLayoutObject(wAllQueueLogs, "1C");
	lQueueLogs.cells('a').setText('<?php
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
 echo _l('Tasks error logs',1);?>');
	queuelogs_grid=lQueueLogs.cells('a').attachGrid();
	queuelogs_grid.setImagePath('lib/js/imgs/');
	queuelogs_grid.setHeader("ID,<?php echo _l('ID employee')?>,<?php echo _l('Date')?>,<?php echo _l('File')?>,<?php echo _l('Action')?>,<?php echo _l('Row')?>,<?php echo _l('Settings')?>");
	queuelogs_grid.setColumnIds("id_sc_queue_log,id_employee,date_add,name,action,row,params");
	queuelogs_grid.setInitWidths("50,50,130,200,100,60,350");
	queuelogs_grid.setColAlign("right,right,center,left,left,right,left");
	queuelogs_grid.setColTypes("ro,ro,ro,ro,ro,ro,ro");
  	queuelogs_grid.enableSmartRendering(true);
  	queuelogs_grid.enableMultiselect(true);
	queuelogs_grid.setColSorting("int,int,str,str,str,str,str");
	queuelogs_grid.attachHeader("#text_filter,#select_filter,#text_filter,#select_filter,#text_filter,#text_filter,#text_filter");
	queuelogs_grid.setDateFormat("%Y-%m-%d %H:%i%s");
	queuelogs_grid.init();
	queuelogs_grid.enableHeaderMenu();

	queuelogs_grid_sb=lQueueLogs.cells('a').attachStatusBar();
	
	queuelogs_tb=lQueueLogs.cells('a').attachToolbar();
	queuelogs_tb.addButton("delete", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	queuelogs_tb.setItemToolTip('delete','<?php echo _l('Delete all history',1)?>');
	queuelogs_tb.addButton("addInQueue", 0, "", "lib/img/cog_go.png", "lib/img/cog_go.png");
	queuelogs_tb.setItemToolTip('addInQueue','<?php echo _l('Run the task again',1)?>');
	queuelogs_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	queuelogs_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid',1)?>');
	queuelogs_tb.attachEvent("onClick",
		function(id){
			if (id=='refresh'){
				displayQueueLogs();
			}
			if (id=='delete'){
				if (queuelogs_grid.getSelectedRowId()!=null)
				{
					if (confirm('<?php echo _l('Are you sure you want to delete these rows?',1)?>'))
						$.post('index.php?ajax=1&act=all_queuelogs_delete',{ids: queuelogs_grid.getSelectedRowId()},function(){displayQueueLogs();});
				}
			}
			if (id=='addInQueue'){
				if (queuelogs_grid.getSelectedRowId()!=null)
				{
					if (confirm('<?php echo _l('Are you sure you want to run these tasks again?',1)?>'))
						$.post('index.php?ajax=1&act=all_queuelogs_getruntasks',{ids: queuelogs_grid.getSelectedRowId()},function(data){
							if(data!=undefined && data!=null && data!="" && data!=0)
							{
								data = JSON.parse(data);
								$.each(data, function(num,params){
									addInUpdateQueue(params);
								});
							}
							displayQueueLogs();
						});
				}
			}
		});

	function displayQueueLogs(callback)
	{
		queuelogs_grid.clearAll();
		queuelogs_grid_sb.setText('');
		queuelogs_grid_sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');
		queuelogs_grid.load("index.php?ajax=1&act=all_queuelogs_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
	    getRowsNum=queuelogs_grid.getRowsNum();
	 		queuelogs_grid_sb.setText(getRowsNum+' '+(getRowsNum>1?'<?php echo _l('actions',1)?>':'<?php echo _l('action')?>'));
	   		 queuelogs_grid.filterByAll();

	   		idxDate=queuelogs_grid.getColIndexById('date_add');
	   		queuelogs_grid.groupBy(idxDate);
	   		 
			if (callback!='') eval(callback);
		});
	}

	displayQueueLogs();
	//lQueueLogs.cells('b').collapse();
</script>