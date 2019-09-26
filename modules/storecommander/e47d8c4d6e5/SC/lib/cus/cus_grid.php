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
	cus_grid=cus_customerPanel.attachGrid();
	cus_grid._name='grid';

	cus_grid.enableDistributedParsing(true,1000,100);

	<?php if(SCSG) { ?>
		cus_grid.enableDragAndDrop(true);
	<?php } ?>

	// UISettings
	cus_grid._uisettings_prefix='cus_grid_';
	cus_grid._uisettings_name=cus_grid._uisettings_prefix+gridView;
	cus_grid._first_loading=1;
	
	cus_grid_tb=cus_customerPanel.attachToolbar();
	cus_grid_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cus_grid_tb.setItemToolTip('help','<?php echo _l('Help', 1)?>');
	if (!isIPAD){
		cus_grid_tb.addButton("print", 0, "", "lib/img/printer.png", "lib/img/printer.png");
		cus_grid_tb.setItemToolTip('print','<?php echo _l('Print grid', 1)?>');
	}
	<?php if(_r("ACT_CUS_FAST_EXPORT")) { ?>
	cus_grid_tb.addButton("exportcsv", 0, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	cus_grid_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1)?>');
	<?php } ?>
	cus_grid_tb.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	cus_grid_tb.setItemToolTip('selectall','<?php echo _l('Select all products', 1)?>');
	cus_grid_tb.addButton("add_discount", 0, "", "lib/img/tag_blue_add.png", "lib/img/tag_blue_add.png");
	cus_grid_tb.setItemToolTip('add_discount','<?php echo _l('Create a new discount code', 1)?>');
	cus_grid_tb.addButton("user_go", 0, "", "lib/img/user_orange_go.png", "lib/img/user_orange_go.png");
	cus_grid_tb.setItemToolTip('user_go','<?php echo _l('login as selected customer on the front office', 1)?>');
	cus_grid_tb.addButton("view_customer_ps", 0, "", "lib/img/user_ps.png", "lib/img/user_ps.png");
	cus_grid_tb.setItemToolTip('view_customer_ps','<?php echo _l('View selected customers in Prestashop', 1)?>');
	cus_grid_tb.addButton("add_ps", 0, "", "lib/img/add_ps.png", "lib/img/add_ps.png");
	cus_grid_tb.setItemToolTip('add_ps','<?php echo _l('Create new customer with the PrestaShop form', 1)?>');
	if (isIPAD){
		cus_grid_tb.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		cus_grid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	cus_grid_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	cus_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid', 1)?>');
	var opts = [['filters_reset', 'obj', '<?php echo _l('Reset filters', 1)?>', ''],
							['separator1', 'sep', '', ''],
							['filters_cols_show', 'obj', '<?php echo _l('Show all columns', 1)?>', ''],
							['filters_cols_hide', 'obj', '<?php echo _l('Hide all columns', 1)?>', '']
							];
	cus_grid_tb.addButtonSelect("filters", 0, "", opts, "lib/img/filter.png", "lib/img/filter.png",false,true);
	cus_grid_tb.setItemToolTip('filters','<?php echo _l('Filter options', 1)?>');
	var gridnames=new Object();
	<?php if(_r("GRI_CUS_VIEW_GRID_LIGHT")) { ?>gridnames['grid_light']='<?php echo _l('Light view',1)?>';<?php } ?>
	<?php if(_r("GRI_CUS_VIEW_GRID_LARGE")) { ?>gridnames['grid_large']='<?php echo _l('Large view',1)?>';<?php } ?>
	<?php if(_r("GRI_CUS_VIEW_GRID_ADDRESS")) { ?>gridnames['grid_address']='<?php echo _l('Addresses',1)?>';<?php } ?>
	<?php if(_r("GRI_CUS_VIEW_GRID_CONVERT")) { ?>gridnames['grid_convert']='<?php echo _l('Convert',1)?>';<?php } ?>
	<?php
	sc_ext::readCustomCustomersGridsConfigXML('gridnames');
	?>

	cus_grid.setColumnIds("id_customer,id_gender,firstname,lastname,email,active,newsletter,optin,cart_lang,date_add,date_connection");
	
	var opts = new Array();
	$.each(gridnames, function(index, value) {
		opts[opts.length] = new Array(index, 'obj', value, '');
	});
	
	//var opts_custom = [<?php /*sc_ext::readCustomCustomersGridsConfigXML('toolbar');*/ ?>];
	//gridView = opts[0][0];
	// UISettings
	cus_grid._uisettings_name=cus_grid._uisettings_prefix+gridView;
	cus_grid_tb.addButtonSelect("gridview", 0, "<?php echo _l('Light view')?>", opts, "lib/img/table_gear.png", "lib/img/table_gear.png",false,true);
	cus_grid_tb.setItemToolTip('gridview','<?php echo _l('Grid view settings')?>');
	if (isIPAD){
		var opts = [['cols123', 'obj', '<?php echo _l('Columns')?> 1 + 2 + 3', ''],
								['cols12', 'obj', '<?php echo _l('Columns')?> 1 + 2', ''],
								['cols23', 'obj', '<?php echo _l('Columns')?> 2 + 3', '']
								];
		cus_grid_tb.addButtonSelect("layout", 0, "", opts, "lib/img/layout.png", "lib/img/layout.png",false,true);
	}

	function gridToolBarOnClick(id){
			if (id.substr(0,5)=='grid_'){
				oldGridView=gridView;
				gridView=id;
				customer_columns = new Array();
/* a revoir avec filtres qui correspondent bien aux colonnes */
				//filter_params = "";
				//oldFilters = new Array();
				
				// UISettings
				cus_grid._uisettings_name=cus_grid._uisettings_prefix+gridView;
				
				cus_grid_tb.setItemText('gridview',gridnames[id]);
				$(document).ready(function(){displayCustomers();});
			}
			if (id=='help'){
				<?php echo "window.open('".getHelpLink('cus_toolbar_prod')."');"; ?>
			}
			if (id=='filters_reset')
			{
				for(var i=0,l=cus_grid.getColumnsNum();i<l;i++)
				{
					if (cus_grid.getFilterElement(i)!=null) cus_grid.getFilterElement(i).value="";
				}
				cus_grid.filterByAll();
				cus_grid_tb.setListOptionSelected('filters','');
				oldFilters = new Array();
				//displayCustomers();
			}
			if (id=='filters_cols_show')
			{
				for(i=0,l=cus_grid.getColumnsNum() ; i < l ; i++)
				{
					cus_grid.setColumnHidden(i,false);
				}
				cus_grid_tb.setListOptionSelected('filters','');
			}
			if (id=='filters_cols_hide')
			{
				idxCustomerID=cus_grid.getColIndexById('id_customer');
				idxCustomerEmail=cus_grid.getColIndexById('email');
				for(i=0 , l=cus_grid.getColumnsNum(); i < l ; i++)
				{
					if (i!=idxCustomerID && i!=idxCustomerEmail)
					{
						cus_grid.setColumnHidden(i,true);
					}else{
						cus_grid.setColumnHidden(i,false);
					}
				}
				cus_grid_tb.setListOptionSelected('filters','');
			}
			if (id=='refresh'){
				displayCustomers();
			}
			if (id=='print'){
				cus_grid.printView();
			}
			if (id=='user_go'){
				var sel=cus_grid.getSelectedRowId();
				if (sel)
				{
					var tabId=sel.split(',');
					if (tabId.length==1){
						idxIdCustomer=cus_grid.getColIndexById('id_customer');
						idxIdShop=cus_grid.getColIndexById('id_shop');
						id_customer=cus_grid.cells(tabId[0],idxIdCustomer).getValue();
						id_shop=0;
						if (idxIdShop)
							id_shop=cus_grid.cells(tabId[0],idxIdShop).getValue();
						window.open("index.php?ajax=1&act=all_loggued-as-user&id="+id_customer+"&id_shop="+id_shop);
					}else{
						dhtmlx.message({text:'<?php echo addslashes(_l('Alert: You need to select only one customer'))?>',type:'error'});
					}
				}
			}
			if (id=='view_customer_ps'){
				var sel=cus_grid.getSelectedRowId();
				if (sel)
				{
					var tabId=sel.split(',');
					for (var i=0;i<tabId.length;i++)
					{
						idxIdCustomer=cus_grid.getColIndexById('id_customer');
						id_customer=cus_grid.cells(tabId[i],idxIdCustomer).getValue();
						if (mustOpenBrowserTab){
							window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
						}else{
							<?php  if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
							wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1250, $(window).height()-75);
							<?php  } else { ?>
							wViewCustomer = dhxWins.createWindow(i+"wViewCustomer"+new Date().getTime(), 50+i*40, 50+i*40, 1000, $(window).height()-75);
							<?php  } ?>
							wViewCustomer.setText('<?php echo _l('Customer',1)?> '+id_customer);
							wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
						}
					}
				}
			}
			if (id=='add_discount'){
				if (mustOpenBrowserTab){
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
?>
					window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminDiscounts&adddiscount&token=<?php echo $sc_agent->getPSToken('AdminDiscounts');?>");
<?php
}else{
?>
					window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCartRules&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules');?>");
<?php
}
?>
				}else{
					wCreateDiscountCode = dhxWins.createWindow("wCreateDiscountCode"+new Date().getTime(), 50, 50, 1000, $(window).height()-75);
					wCreateDiscountCode.setText('<?php echo _l('Create discount code',1)?>');
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
{
?>
					wCreateDiscountCode.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminDiscounts&adddiscount&token=<?php echo $sc_agent->getPSToken('AdminDiscounts');?>");
<?php
}else{
?>
					wCreateDiscountCode.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCartRules&addcart_rule&token=<?php echo $sc_agent->getPSToken('AdminCartRules');?>");
<?php
}
?>
					wCreateDiscountCode.attachEvent("onClose", function(win){
						displayCustomers();
						return true;
					});
				}
			}
			if (id=='add_ps'){
				if (mustOpenBrowserTab){
					window.open("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&addcustomer&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
				}else{
					if (!dhxWins.isWindow("wNewCustomer"))
					{
						<?php  if(version_compare(_PS_VERSION_, '1.6.0.0', '>=')) { ?>
						wNewCustomer = dhxWins.createWindow("wNewCustomer", 50, 50, 1250, $(window).height()-75);
						<?php  } else { ?>
						wNewCustomer = dhxWins.createWindow("wNewCustomer", 50, 50, 1000, $(window).height()-75);
						<?php  } ?>
						wNewCustomer.setText('<?php echo _l('Create the new customer and close this window to refresh the grid',1)?>');
						wNewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&addcustomer&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
						wNewCustomer.attachEvent("onClose", function(win){
									displayCustomers();
									return true;
								});
					}
				}
			}
			if (id=='selectall'){
			  cus_grid.enableSmartRendering(false);
			  cus_grid.selectAll();
			  getGridStat();
			}
			if (id=='exportcsv'){
				cus_grid.enableCSVHeader(true);
				cus_grid.setCSVDelimiter("\t");
				var csv=cus_grid.serializeToCSV(true);
				displayQuickExportWindow(csv,1);
			}
			if (id=='cols123')
			{
				cus.cells("a").expand();
				cus.cells("a").setWidth(200);
				cus.cells("b").expand();
				dhxLayout.cells('b').expand();
				dhxLayout.cells('b').setWidth(500);
			}
			if (id=='cols12')
			{
				cus.cells("a").expand();
				cus.cells("a").setWidth(200);
				cus.cells("b").expand();
				dhxLayout.cells('b').collapse();
			}
			if (id=='cols23')
			{
				cus.cells("a").collapse();
				cus.cells("b").expand();
				cus.cells("b").setWidth($(document).width()/2);
				dhxLayout.cells('b').expand();
				dhxLayout.cells('b').setWidth($(document).width()/2);
			}
		}
	cus_grid_tb.attachEvent("onClick",gridToolBarOnClick);

	cus_grid.setImagePath('lib/js/imgs/');
<?php
			if (version_compare(_PS_VERSION_, '1.3.0.4', '<')) // DATE => DATETIME field format
			{
				echo 'cus_grid.setDateFormat("%Y-%m-%d","%Y-%m-%d");';
			}else{
				echo 'cus_grid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");';
			}
?>
	cus_grid.enableMultiselect(true);
	cus_grid_sb=cus_customerPanel.attachStatusBar();
	gridToolBarOnClick(gridView);

	// multiedition context menu
	cus_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			lastColumnRightClicked=colidx;
			/*idxMsgAvailableNow=cus_grid.getColIndexById('available_now');
			idxMsgAvailableLater=cus_grid.getColIndexById('available_later');
			idxReductionPrice=cus_grid.getColIndexById('reduction_price');
			idxReductionPercent=cus_grid.getColIndexById('reduction_percent');
			idxReductionFrom=cus_grid.getColIndexById('reduction_from');
			idxReductionTo=cus_grid.getColIndexById('reduction_to');*/
			cus_cmenu.setItemText('object', '<?php echo _l('Customer:')?> '+cus_grid.cells(rowid,cus_grid.getColIndexById('lastname')).getValue());
			// paste function
			if (lastColumnRightClicked==clipboardType)
			{
				cus_cmenu.setItemEnabled('paste');
			}else{
				cus_cmenu.setItemDisabled('paste');
			}
			var colType=cus_grid.getColType(colidx);
			if (colType=='ro')
			{
				cus_cmenu.setItemDisabled('copy');
				cus_cmenu.setItemDisabled('paste');
			}else{
				cus_cmenu.setItemEnabled('copy');
			}
			return true;
		});
	
	function onEditCell(stage,rId,cInd,nValue,oValue){
		var coltype=cus_grid.getColType(cInd);
		if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();
		lastEditedCell=cInd;
		if (nValue!=oValue){
			cus_grid.setRowColor(rId,'BlanchedAlmond');
		}
<?php
		sc_ext::readCustomCustomersGridsConfigXML('onEditCell');
?>
		if (nValue!=oValue){
			return true;
		}
    if(stage==1 && (cInd == -5)) // only for ed type
    {
			var editor = this.editor;
			var pos = this.getPosition(editor.cell);
			var y = document.body.offsetHeight-pos[1];
			if(y < editor.list.offsetHeight)
				editor.list.style.top = (pos[1] - editor.list.offsetHeight)+'px';
    }
	}
	cus_grid.attachEvent("onEditCell",onEditCell);
	cus_grid.attachEvent("onDhxCalendarCreated",function(calendar){
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
	cusDataProcessorURLBase="index.php?ajax=1&act=cus_customer_update&id_lang="+SC_ID_LANG;
	cusDataProcessor = new dataProcessor(cusDataProcessorURLBase);
	cusDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
<?php
	sc_ext::readCustomCustomersGridsConfigXML('onAfterUpdate');
?>
		var dbQty = xml.getAttribute("quantity");
		if (dbQty!='')
		{
			idxQty=cus_grid.getColIndexById('quantity');
			if (idxQty!=null)
				cus_grid.cells(sid,idxQty).setValue(dbQty);
		}
		var doUpdateCombinations = xml.getAttribute("doUpdateCombinations");
		if (doUpdateCombinations==1 && propertiesPanel=='combinations')
		{
			displayCombinations();
		}
	});
	cusDataProcessor.attachEvent("onBeforeUpdate",function(id,status, dat){
<?php
	sc_ext::readCustomCustomersGridsConfigXML('onBeforeUpdate');
?>
		var new_url = cusDataProcessorURLBase;
		
		if(gridView=="grid_address")
			new_url = new_url+'&type=address';
		else
			new_url = new_url+'&type=customer';

		cusDataProcessor.serverProcessor = new_url;
		return true;
	});
	cusDataProcessor.enableDataNames(true);
	cusDataProcessor.enablePartialDataSend(true);
	cusDataProcessor.setUpdateMode('cell',true);
	cusDataProcessor.setTransactionMode("POST");
	cusDataProcessor.init(cus_grid);

	// Context menu for Grid
	cus_cmenu=new dhtmlXMenuObject();
	cus_cmenu.renderAsContextMenu();
	function onGridCusContextButtonClick(itemId){
		tabId=cus_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="gopsbo"){
			id_customer=tabId;
			wViewCustomer = dhxWins.createWindow("wViewCustomer"+new Date().getTime(), 50+40, 50+40, 1000, $(window).height()-75);
			wViewCustomer.setText('<?php echo _l('Customer',1)?> '+id_customer);
			wViewCustomer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCustomers&viewcustomer&id_customer="+id_customer+"&token=<?php echo $sc_agent->getPSToken('AdminCustomers');?>");
			wViewCustomer.attachEvent("onClose", function(win){
				displayCustomers();
				return true;
			});
		}
		if (itemId=="copy"){
			if (lastColumnRightClicked!=0)
			{
				clipboardValue=cus_grid.cells(tabId,lastColumnRightClicked).getValue();
				cus_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cus_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
				clipboardType=lastColumnRightClicked;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
			{
				selection=cus_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						cus_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
						cus_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
						onEditCell(null,selArray[i],lastColumnRightClicked,clipboardValue,null);
						cusDataProcessor.setUpdated(selArray[i],true,"updated");
					}
				}
			}
		}
	}
	cus_cmenu.attachEvent("onClick", onGridCusContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
		'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
		'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
	'</menu>';
	cus_cmenu.loadStruct(contextMenuXML);
	cus_grid.enableContextMenu(cus_cmenu);

	//#####################################
	//############ Events
	//#####################################

	// Click on a customer
	function doOnRowSelected(idproduct){
		if (!dhxLayout.cells('b').isCollapsed() && lastCustomerSelID!=idproduct)
		{
			lastCustomerSelID=idproduct;
			idxLastname=cus_grid.getColIndexById('lastname');
			idxFirstame=cus_grid.getColIndexById('firstname');

			if (propertiesPanel!='descriptions'){
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cus_grid.cells(lastCustomerSelID,idxFirstame).getValue()+" "+cus_grid.cells(lastCustomerSelID,idxLastname).getValue());
			}
<?php
echo eval('?>'.$pluginCustomerProperties['doOnCustomerRowSelected'].'<?php ');
?>
		}
	}

	cus_grid.attachEvent("onRowSelect",doOnRowSelected);

	// UISettings
	initGridUISettings(cus_grid);
	
cus_grid.attachEvent("onFilterEnd", function(elements){
		old_filter_params = filter_params;
		filter_params = "";
		var nb_cols = cus_grid.getColumnsNum();
		if(nb_cols>0)
		{
			for(var i=0; i<nb_cols; i++)
			{
				var colId=customer_columns[i];
				if(cus_grid.getFilterElement(i)!=null 
						&& ( colId =="id_address"
						|| colId =="id_customer"
						|| colId =="firstname"
						|| colId =="lastname"
						|| colId =="email"
						|| colId =="postcode"
						|| colId =="city" )

					)
				{
					var colValue = cus_grid.getFilterElement(i).value;
					if((colValue!=null && colValue!="") || (oldFilters[colId]!=null && oldFilters[colId]!=""))
					{
						if(filter_params!="")
							filter_params = filter_params + ",";
						filter_params = filter_params + colId+"|||"+colValue;
						oldFilters[colId] = cus_grid.getFilterElement(i).value;
					}
				}
			}
		}
		//alert(filter_params);
		if(filter_params!="" && filter_params!=old_filter_params)
		{
			displayCustomers();
		}
		getGridStat();
		
	});
cus_grid.attachEvent("onSelectStateChanged", function(id){
		getGridStat();
	});

cus_grid.attachEvent("onDhxCalendarCreated",function(calendar){
	calendar.setSensitiveRange("2012-01-01",null);
});


cus_grid_tb.attachEvent("onStateChange",function(id,state){
	if (id=='lightNavigation')
	{
		if (state)
		{
			cus_grid.enableLightMouseNavigation(true);
		}else{
			cus_grid.enableLightMouseNavigation(false);
		}
	}
});


var customer_columns = new Array();
var filter_params = "";
var oldFilters = new Object();
<?php if(!empty($_GET["open_cus"])) { ?>
var need_cus_filter = 1;
<?php } ?>

function displayCustomers(callback)
{
	oldFilters=new Array();
	for(var i=0,l=cus_grid.getColumnsNum();i<l;i++)
	{
		if (cus_grid.getFilterElement(i)!=null && cus_grid.getFilterElement(i).value!='')
			oldFilters[cus_grid.getColumnId(i)]=cus_grid.getFilterElement(i).value;

	}
	cus_grid.editStop(true);
	cus_grid.clearAll(true);
	cus_grid_sb.setText('');
	oldGridView=gridView;
  	firstProductsLoading=0;
	cus_grid_sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');

	var loadUrl = "index.php?ajax=1&act=cus_customer_get&filters="+groupselection+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
	<?php if(SCSG) { ?>
	if(id_selected_segment!=undefined && id_selected_segment!=null && id_selected_segment!=0)
		loadUrl = "index.php?ajax=1&act=cus_customer_get&id_segment="+id_selected_segment+"&filter_params="+filter_params+"&view="+gridView+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime();
	<?php } ?>
	
	cus_grid.loadXML(loadUrl,function(){
		<?php if(!empty($_GET["open_cus"])) { ?>
		if(need_cus_filter == 1)
		{
			need_cus_filter = 0;
			idxCustomerID = cus_grid.getColIndexById('id_customer');
			cus_grid.getFilterElement(idxCustomerID).value='<?php echo intval($_GET["open_cus"]); ?>';
			setTimeout(function(){cus_grid.filterByAll();}, 1000);
			
		}
		<?php } ?>

		// Tri indifféremment de la case ou du caractère
		idxFirstname=cus_grid.getColIndexById('firstname');
		cus_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
			a = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(a_id,idxFirstname).getTitle()).toLowerCase()));
			b = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(b_id,idxFirstname).getTitle()).toLowerCase()));
			return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
		}, idxFirstname);
		idxLastname=cus_grid.getColIndexById('lastname');
		cus_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
			a = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(a_id,idxLastname).getTitle()).toLowerCase()));
			b = sanitizeString(replaceAccentCharacters(latinise(cus_grid.cells(b_id,idxLastname).getTitle()).toLowerCase()));
			return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
		}, idxLastname);
		
		cus_grid._rowsNum=cus_grid.getRowsNum();
		
		var limit_smartrendering = 0;
		if(cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=undefined && cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=0 && cus_grid.getUserData("", "LIMIT_SMARTRENDERING")!=null)
			limit_smartrendering = cus_grid.getUserData("", "LIMIT_SMARTRENDERING");
		
		if(limit_smartrendering!=0 && cus_grid._rowsNum > limit_smartrendering)
			cus_grid.enableSmartRendering(true);
		else
			cus_grid.enableSmartRendering(false);
		
		//idxActive=cus_grid.getColIndexById('active');
		lastEditedCell=0;  
		lastColumnRightClicked=0;
		//cus_grid.filterByAll();
		customer_columns = new Array();
		var nb_cols = cus_grid.getColumnsNum();
		if(nb_cols>0)
		{
			for(var i=0; i<nb_cols; i++)
			{
				var colId=cus_grid.getColumnId(i);
				customer_columns[i] = colId;
			}
		}
		//alert(customer_columns);
		
		colorFilter();
		
		// UISettings
		loadGridUISettings(cus_grid);
		
		getGridStat();

		var testCustomerAddress = cus_grid.getColIndexById('id_address');
		if (typeof testCustomerAddress !== "undefined")
		{
			idxCustomerID = cus_grid.getColIndexById('id_customer');
			CustomerIDs = cus_grid.findCell(lastCustomerSelID,idxCustomerID,0);
			preserv = 0;
			if (CustomerIDs.length)
				for(var i = 0 ; i< CustomerIDs.length ; i++){
					cus_grid.selectRowById(CustomerIDs[i][0],preserv,true,false);
					preserv++;
				}
		}else{
			
			if (!cus_grid.doesRowExist(lastCustomerSelID))
			{
				lastCustomerSelID=0;
			}else{
				cus_grid.selectRowById(lastCustomerSelID);
			}
		}

		for(var i=0;i<cus_grid.getColumnsNum();i++)
		{
			if (cus_grid.getFilterElement(i)!=null && oldFilters[cus_grid.getColumnId(i)]!=undefined)
			{
				cus_grid.getFilterElement(i).value=oldFilters[cus_grid.getColumnId(i)];
			}
		}
		cus_grid.filterByAll();

		<?php sc_ext::readCustomCustomersGridsConfigXML('afterGetRows'); ?>

		// UISettings
		cus_grid._first_loading=0;

		<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
		cus_grid.enableColumnMove(false);
		<?php } ?>

  		if (callback!='') eval(callback);
  		
		});
}
function getGridStat(){
  filteredRows=cus_grid.getRowsNum();
	selectedRows=(cus_grid.getSelectedRowId()?cus_grid.getSelectedRowId().split(',').length:0);
	cus_grid_sb.setText(cus_grid._rowsNum+' '+(cus_grid._rowsNum>1?'<?php echo _l('customers')?>':'<?php echo _l('customer')?>')+" - <?php echo _l('Filter')._l(':')?> "+filteredRows+" - <?php echo _l('Selection')._l(':')?> "+selectedRows);
}

function colorFilter()
{
/*
	if(gridView!="grid_address")
	{
		$(cus_grid.getFilterElement(0)).css('background-color','#EDEDFF');
		<?php if(SCMS) { ?>
		$(cus_grid.getFilterElement(3)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(4)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(5)).css('background-color','#EDEDFF');
		<?php } else { ?>
		$(cus_grid.getFilterElement(2)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(3)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(4)).css('background-color','#EDEDFF');
		<?php } ?>
	}
	else
	{
		$(cus_grid.getFilterElement(0)).css('background-color','#EDEDFF');
		<?php if(SCMS) { ?>
		$(cus_grid.getFilterElement(2)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(3)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(4)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(7)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(8)).css('background-color','#EDEDFF');
		<?php } else { ?>
		$(cus_grid.getFilterElement(1)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(2)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(3)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(4)).css('background-color','#EDEDFF');
		$(cus_grid.getFilterElement(7)).css('background-color','#EDEDFF');
		<?php } ?>
	}
*/
}

	<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
	cus_grid.enableColumnMove(false);
	<?php } ?>
</script>
