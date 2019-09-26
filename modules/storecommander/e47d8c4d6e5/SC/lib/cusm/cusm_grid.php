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
	oldFilters = new Array();

	cusm_discussionCol = cusm_discussionPanel.attachLayout("2E");

	cusm_discussionGrid = cusm_discussionCol.cells("a");
	cusm_discussionGrid.setText('<?php echo _l('Discussions',1)?>');

	cusm_discussionMessages = cusm_discussionCol.cells("b").attachLayout("2E");
	cusm_discussionLastMessages = cusm_discussionMessages.cells("a");
	cusm_discussionLastMessages.setText('<?php echo _l('Last Messages',1)?>');
	
	cusm_discussionForm = cusm_discussionMessages.cells("b");
	cusm_discussionForm.setText('<?php echo _l('Answer',1)?>');

	cusm_grid=cusm_discussionGrid.attachGrid();
	cusm_grid._name='grid';

	cusm_grid.enableDistributedParsing(true,1000,100);

	<?php if(SCSG) { ?>
		cusm_grid.enableDragAndDrop(true);
	<?php } ?>

	// UISettings
	cusm_grid._uisettings_prefix='cusm_grid';
	cusm_grid._uisettings_name=cusm_grid._uisettings_prefix;
	cusm_grid._first_loading=1;
	
	cusm_grid_tb=cusm_discussionGrid.attachToolbar();
	cusm_grid_tb.addButton("delete", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	cusm_grid_tb.setItemToolTip('delete','<?php echo _l('Delete')?>');
	cusm_grid_tb.addButton("send_mail", 0, "", "lib/img/email_go.png", "lib/img/email_go.png");
	cusm_grid_tb.setItemToolTip('send_mail','<?php echo _l('Send mail to customer')?>');
	cusm_grid_tb.addButton("user_go", 0, "", "lib/img/user_orange_go.png", "lib/img/user_orange_go.png");
	cusm_grid_tb.setItemToolTip('user_go','<?php echo _l('login as selected customer on the front office')?>');
	if (isIPAD)
	{
		cusm_grid_tb.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		cusm_grid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	cusm_grid_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	cusm_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');

	if (isIPAD){
		var opts = [['cols123', 'obj', '<?php echo _l('Columns')?> 1 + 2 + 3', ''],
								['cols12', 'obj', '<?php echo _l('Columns')?> 1 + 2', ''],
								['cols23', 'obj', '<?php echo _l('Columns')?> 2 + 3', '']
								];
		cusm_grid_tb.addButtonSelect("layout", 0, "", opts, "lib/img/layout.png", "lib/img/layout.png",false,true);
	}


	function gridToolBarOnClick(id){
		if (id=='refresh'){
			displayDiscussions();
		}

		if (id=='user_go'){
			var sel=cusm_grid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				if (tabId.length==1){
					id_customer=cusm_grid.getUserData(tabId[0],'id_customer');
					window.open("index.php?ajax=1&act=all_loggued-as-user&id="+id_customer+"&id_shop="+shopselection);
				}else{
					dhtmlx.message({text:'<?php echo addslashes(_l('Alert: You need to select only one order'))?>',type:'error'});
				}
			}
		}
		if (id=='send_mail') {
			cusm_grid._sendMailWindow = dhxWins.createWindow("wSendMail", 200, 50, 500, 600);
			cusm_grid._sendMailWindow.setIcon('lib/img/email_go.png','../../../lib/img/email_go.png');
			cusm_grid._sendMailWindow.setText('<?php echo _l('Send mail to customer',1)?>');
			cusm_grid._sendMailWindow.attachURL("index.php?ajax=1&act=cusm_send_mail&id_shop="+shopselection+"&id_lang="+SC_ID_LANG);
		}
		if (id=='delete'){
			if (cusm_grid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select a discussion',1)?>');
			}else{
				if (confirm('<?php echo _l('Are you sure you want to delete the selected discussions?',1)?>'))
				{
					cusm_grid.deleteSelectedRows();
				}
			}
		}
		if (id=='cols123')
		{
			cusm.cells("a").expand();
			cusm.cells("a").setWidth(200);
			cusm.cells("b").expand();
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth(500);
		}
		if (id=='cols12')
		{
			cusm.cells("a").expand();
			cusm.cells("a").setWidth($(document).width()/3);
			cusm.cells("b").expand();
			dhxLayout.cells('b').collapse();
		}
		if (id=='cols23')
		{
			cusm.cells("a").collapse();
			cusm.cells("b").expand();
			cusm.cells("b").setWidth($(document).width()/2);
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth($(document).width()/2);
		}
	}
	cusm_grid_tb.attachEvent("onClick",gridToolBarOnClick);

	cusm_grid_tb.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				cusm_grid.enableLightMouseNavigation(true);
			}else{
				cusm_grid.enableLightMouseNavigation(false);
			}
		}
	});

	cusm_grid.setImagePath('lib/js/imgs/');
<?php
			if (version_compare(_PS_VERSION_, '1.3.0.4', '<')) // DATE => DATETIME field format
			{
				echo 'cusm_grid.setDateFormat("%Y-%m-%d","%Y-%m-%d");';
			}else{
				echo 'cusm_grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");';
			}
?>
	cusm_grid.enableMultiselect(true);

	// multiedition context menu
	cusm_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			var disableOnCols=new Array(
					cusm_grid.getColIndexById('status'),
					cusm_grid.getColIndexById('id_contact')
					);
			if (!in_array(colidx,disableOnCols))
			{
				return false;
			}
			lastColumnRightClicked=colidx;
			cusm_cmenu.setItemText('object', '<?php echo _l('Discussion:')?> '+cusm_grid.cells(rowid,cusm_grid.getColIndexById('id_customer_thread')).getValue());
			// paste function
			if (lastColumnRightClicked==clipboardType)
			{
				cusm_cmenu.setItemEnabled('paste');
			}else{
				cusm_cmenu.setItemDisabled('paste');
			}
			var colType=cusm_grid.getColType(colidx);
			if (colType=='ro')
			{
				cusm_cmenu.setItemDisabled('copy');
				cusm_cmenu.setItemDisabled('paste');
			}else{
				cusm_cmenu.setItemEnabled('copy');
			}
			return true;
		});
	
	cusm_grid.attachEvent("onDhxCalendarCreated",function(calendar){
			dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso;?>"] = <?php echo _l('{
		dateformat: "%Y-%m-%d",
		monthesFNames: ["January","February","March","April","May","June","July","August","September","October","November","December"],
		monthesSNames: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
		daysFNames: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
		daysSNames: ["Su","Mo","Tu","We","Th","Fr","Sa"],
		weekstart: 1
	}')?>;
			calendar.loadUserLanguage("<?php echo $user_lang_iso;?>");
<?php
			if (version_compare(_PS_VERSION_, '1.3.0.4', '<')) // DATE => DATETIME field format
				echo 'calendar.hideTime();';
?>
		});
			
	cusmDataProcessorURLBase="index.php?ajax=1&act=cusm_discussion_update&id_lang="+SC_ID_LANG;
	cusmDataProcessor = new dataProcessor(cusmDataProcessorURLBase);
	cusmDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
		return true;
	});
	cusmDataProcessor.attachEvent("onBeforeUpdate",function(id,status, dat){
		return true;
	});
	cusmDataProcessor.enableDataNames(true);
	cusmDataProcessor.enablePartialDataSend(true);
	cusmDataProcessor.setUpdateMode('cell',true);
	cusmDataProcessor.setTransactionMode("POST");
	cusmDataProcessor.init(cusm_grid);

	// Context menu for Grid
	cusm_cmenu=new dhtmlXMenuObject();
	cusm_cmenu.renderAsContextMenu();
	function onGridCusContextButtonClick(itemId){
		tabId=cusm_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="copy"){
			if (lastColumnRightClicked!=0)
			{
				clipboardValue=cusm_grid.cells(tabId,lastColumnRightClicked).getValue();
				cusm_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cusm_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
				clipboardType=lastColumnRightClicked;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
			{
				selection=cusm_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						cusm_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
						cusm_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
						cusmDataProcessor.setUpdated(selArray[i],true,"updated");
					}
				}
			}
		}
	}
	cusm_cmenu.attachEvent("onClick", onGridCusContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
		'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
	'</menu>';
	cusm_cmenu.loadStruct(contextMenuXML);
	cusm_grid.enableContextMenu(cusm_cmenu);

	//#####################################
	//############ Events
	//#####################################

	// Click on a discussion
	function doOnRowSelected(idDiscussion){
		if (!cusm_discussionGrid.isCollapsed() && lastDiscussionSelID!=idDiscussion)
		{
			lastDiscussionSelID=idDiscussion;		
			idxCustomerName=cusm_grid.getColIndexById('customer_name');
			lastCustomerSelID=cusm_grid.getUserData(lastDiscussionSelID,'id_customer');
			dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cusm_grid.cells(lastDiscussionSelID,idxCustomerName).getValue());

<?php
if (0)
	echo eval('?>'.$pluginProductProperties['doOnProductRowSelected'].'<?php ');
?>
		}
		displayLastMessages("",true);
	}

	cusm_grid.attachEvent("onRowSelect",doOnRowSelected);

	// UISettings
	initGridUISettings(cusm_grid);
	
cusm_grid.attachEvent("onFilterEnd", function(elements){
		old_filter_params = filter_params;
		filter_params = "";
		var nb_cols = cusm_grid.getColumnsNum();
		if(nb_cols>0)
		{
			for(var i=0; i<nb_cols; i++)
			{
				var colId=discussion_columns[i];
				if(cusm_grid.getFilterElement(i)!=null 
						&& ( colId =="id_customer_thread"
							|| colId =="customer_name"
							|| colId =="email"
							|| colId =="status"
							|| colId =="id_employee"
							|| colId =="date_add"
							|| colId =="date_upd"
							|| colId =="message"
							|| colId =="shop"
							|| colId =="id_order" )

					)
				{
					var colValue = cusm_grid.getFilterElement(i).value;
					if((colValue!=null && colValue!="") || (oldFilters!=undefined && oldFilters[i]!=undefined && oldFilters[i]!=null && oldFilters[i]!=""))
					{
						if(filter_params!="")
							filter_params = filter_params + ",";
						filter_params = filter_params + colId+"-"+colValue;
						oldFilters[i] = cusm_grid.getFilterElement(i).value;
					}
				}
			}
		}
		if(filter_params!="" && filter_params!=old_filter_params)
		{
			displayDiscussions();
		}
		
	});

cusm_grid.attachEvent("onDhxCalendarCreated",function(calendar){
	calendar.setSensitiveRange("2012-01-01",null);
});


var discussion_columns = new Array();
var filter_params = "";

function displayDiscussions(callback)
{
	cusm_grid.editStop(true);
	cusm_grid.clearAll(true);
  	firstProductsLoading=0;

  	var loadUrl = "index.php?ajax=1&act=cusm_discussion_get&filters="+filterselection+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
  	<?php if(SCSG) { ?>
	if(id_selected_segment!=undefined && id_selected_segment!=null && id_selected_segment!=0)
		loadUrl = "index.php?ajax=1&act=cusm_discussion_get&id_segment="+id_selected_segment+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
	<?php } ?>
	
	cusm_grid.loadXML(loadUrl,function(){
		cusm_grid._rowsNum=cusm_grid.getRowsNum();
		
		
		//idxActive=cusm_grid.getColIndexById('active');
		lastEditedCell=0;  
		lastColumnRightClicked=0;
		//cusm_grid.filterByAll();
		discussion_columns = new Array();
		var nb_cols = cusm_grid.getColumnsNum();
		if(nb_cols>0)
		{
			for(var i=0; i<nb_cols; i++)
			{
				var colId=cusm_grid.getColumnId(i);
				discussion_columns[i] = colId;
			}
		}
		
		// UISettings
		loadGridUISettings(cusm_grid);
		
		if (!cusm_grid.doesRowExist(lastDiscussionSelID))
		{
			lastDiscussionSelID=0;
		}else{
			cusm_grid.selectRowById(lastDiscussionSelID);
		}

		for(var i=0;i<cusm_grid.getColumnsNum();i++)
		{
			if (cusm_grid.getFilterElement(i)!=null && oldFilters!=undefined && oldFilters[i]!=undefined)
			{
				cusm_grid.getFilterElement(i).value=oldFilters[i];
			}
		}
		cusm_grid.filterByAll();

		// UISettings
		cusm_grid._first_loading=0;

  		if (callback!='') eval(callback);
  		
	});
}


//#####################################
//############ LAST MESSAGES
//#####################################
var cusm_lastmessages_grid = cusm_discussionLastMessages.attachGrid();
cusm_lastmessages_grid.setImagePath('lib/js/imgs/');

cusm_lastmessages_tb=cusm_discussionLastMessages.attachToolbar();
cusm_lastmessages_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
cusm_lastmessages_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');

function gridLastMsgToolBarOnClick(id){
	if (id=='refresh'){
		displayLastMessages("", false);
	}
}
cusm_lastmessages_tb.attachEvent("onClick",gridLastMsgToolBarOnClick);

function displayLastMessages(callback, loadForm)
{
	if(cusm_lastmessages_grid!=undefined)
		cusm_lastmessages_grid.clearAll(true);
	cusm_lastmessages_grid.loadXML("index.php?ajax=1&act=cusm_discussion_lastmessage_get&id="+lastDiscussionSelID+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
		cusm_lastmessages_grid._rowsNum=cusm_lastmessages_grid.getRowsNum();

		if (callback!='') eval(callback);
	});

	if(loadForm!=undefined && loadForm==true && lastDiscussionSelID!=undefined && lastDiscussionSelID!=null && lastDiscussionSelID!="" && lastDiscussionSelID>0)
		cusm_discussionForm.attachURL("index.php?ajax=1&x=&act=cusm_discussion_answer&id="+lastDiscussionSelID+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime());
}


//#####################################
//############ ANSWER FORM
//#####################################
cusm_answer_form = null;
//cusm_discussionForm.attachURL("index.php?ajax=1&x=&act=cusm_discussion_answer&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

function successAnswer()
{
	if (propertiesPanel=='message' && !dhxLayout.cells('b').isCollapsed())
		displayDiscussions('displayLastMessages();displayMessage();');
	else
		displayDiscussions('displayLastMessages();');
	
	dhtmlx.message({text:'<?php echo _l('The message was successfully sent',1); ?>',type:'success',expire:3000});
}

</script>
