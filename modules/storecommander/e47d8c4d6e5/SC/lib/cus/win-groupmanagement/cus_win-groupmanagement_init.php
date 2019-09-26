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
	dhxlGroups=wGroupManagement.attachLayout("2U");
	wGroupManagement._sb=dhxlGroups.attachStatusBar();
	groups_panel = dhxlGroups.cells('a');
	groups_panel.setText("<?php echo _l('Groups')?>");
	groups_panel.setWidth(600);

	//#### GROUP TOOLBAR ####
	wGroupManagement.tb=groups_panel.attachToolbar();
	wGroupManagement.tb.addButton("del_group", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	wGroupManagement.tb.setItemToolTip('del_group','<?php echo _l('Delete selected groups')?>');
	wGroupManagement.tb.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning.png");
	wGroupManagement.tb.setItemToolTip('selectall','<?php echo _l('Select all groups')?>');
	wGroupManagement.tb.addButton("add_group", 0, "", "lib/img/add.png", "lib/img/add.png");
	wGroupManagement.tb.setItemToolTip('add_group','<?php echo _l('Create a new group')?>');
	wGroupManagement.tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wGroupManagement.tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');
	wGroupManagement.tb.attachEvent("onClick",
		function(id){
			if (id=='refresh')
			{
				displayGroupsList();
			}
			if (id=='selectall'){
				wGroupManagement.gridGroups.enableSmartRendering(false);
				wGroupManagement.gridGroups.selectAll();
			}
			if (id=='add_group')
			{
				// INSERT
				var newId = new Date().getTime();
				newRow=new Array('','','','','','','');
				newRow=newRow.slice(0,wGroupManagement.gridGroups.getColumnsNum()-1);
				idxID=wGroupManagement.gridGroups.getColIndexById('id_group');
				idxName=wGroupManagement.gridGroups.getColIndexById('name');
				newRow[idxID]=newId;
				newRow[idxName]='new';
				wGroupManagement.gridGroups.addRow(newId,newRow);
				wGroupManagement.gridGroups.setRowHidden(newId, true);

				var params = {
					name: "cus_win-groupmanagement_update_queue",
					row: newId,
					action: "insert",
					params: {callback: "callbackGroupUpdate('"+newId+"','insert','{newid}',{data});"}
				};

				// COLUMN VALUES
				wGroupManagement.gridGroups.forEachCell(newId,function(cellObj,ind){
					params.params[wGroupManagement.gridGroups.getColumnId(ind)] = wGroupManagement.gridGroups.cells(newId,ind).getValue();
				});
				params.params['id_lang'] = SC_ID_LANG;

				// USER DATA
				$.each(wGroupManagement.gridGroups.UserData.gridglobaluserdata.keys, function(i, key){
					params.params[key] = wGroupManagement.gridGroups.UserData.gridglobaluserdata.values[i];
				});

				sendInsert(params,groups_panel);
			}
			if (id=='del_group')
			{
				if (confirm('<?php echo _l('Permanently delete the selected products everywhere in the shop.',1)?>'))
				{
					selection=wGroupManagement.gridGroups.getSelectedRowId();
					ids=selection.split(',');
					$.each(ids, function(num, rId)
					{
						var params =
						{
							name: "cus_win-groupmanagement_update_queue",
							row: rId,
							action: "delete",
							params: {},
							callback: "callbackGroupUpdate('"+rId+"','delete','"+rId+"');"
						};
						params.params = JSON.stringify(params.params);
						wGroupManagement.gridGroups.setRowTextStyle(rId, "text-decoration: line-through;");
						addInUpdateQueue(params,wGroupManagement.gridGroups);
					});
				}
			}
		});

	//#### GROUPS PANEL ####
	wGroupManagement.gridGroups=groups_panel.attachGrid();
	wGroupManagement.gridGroups.setImagePath("lib/js/imgs/");
	wGroupManagement.gridGroups.enableMultiselect(true);


	//#### UISETTINGS ####
	wGroupManagement.gridGroups._uisettings_prefix='cus_win-groupmanagement';
	wGroupManagement.gridGroups._uisettings_name=wGroupManagement.gridGroups._uisettings_prefix;
	wGroupManagement.gridGroups._first_loading=1;
	initGridUISettings(wGroupManagement.gridGroups);

	//#### TRAITEMENT EDIT CELL ####
	wGroupManagement.gridGroups.attachEvent("onEditCell",onEditCell);
	function onEditCell(stage,rId,cInd,nValue,oValue){
		if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
		if(stage==2)
		{
			if (nValue!=oValue)
			{
				var ids = wGroupManagement.gridGroups.getSelectedRowId();
				var p_ids = new Array();
				if(ids.search(",")>=0)
					p_ids = ids.split(",");
				else
					p_ids[0] = ids;

				var nb_rows = p_ids.length*1 - 1;

				$.each(p_ids, function(num, p_id){
					var data = "";
					if(nb_rows!=num)
						data = "noUnBold";

					var params = {
						name: "cus_win-groupmanagement_update_queue",
						row: rId,
						action: "update",
						params: {},
						callback: "callbackGroupUpdate('"+rId+"','update','"+rId+"','"+data+"');"
					};
					// COLUMN VALUES
					wGroupManagement.gridGroups.forEachCell(rId,function(cellObj,ind){
						if(cInd == ind) {
							params.params[wGroupManagement.gridGroups.getColumnId(ind)] = wGroupManagement.gridGroups.cells(rId, ind).getValue();
						}
					});
					params.params["id_group"] = p_id;
					params.params["id_lang"] = SC_ID_LANG;

					params.params = JSON.stringify(params.params);
					addInUpdateQueue(params,wGroupManagement.gridGroups);
				});
			}
		}
		return true;
	}

	displayGroupsList();

	//#### PROPRIETES ####
	groups_properties_panel = dhxlGroups.cells('b');
	wGroupManagement.prop=groups_properties_panel.attachGrid();
	wGroupManagement.prop.setImagePath("lib/js/imgs/");

	//#### PROPRIETES TOOLBAR ####
	groups_properties_panel.setText("<?php echo _l('Properties')?>");
	cus_group_prop_tb=groups_properties_panel.attachToolbar();
	actual_groupmanagement_subproperties = "cus_group_shopshare";
	var opts = new Array();
	cus_group_prop_tb.addButtonSelect("cus_group_prop_subproperties", 100, "<?php echo _l('Multistore sharing manager')?>", opts, "lib/img/sitemap_color.png", "lib/img/sitemap_color.png",false,true);

	<?php
	@$sub_files = scandir(SC_DIR.'lib/cus/win-groupmanagement');
	foreach ($sub_files as $sub_item)
		if ($sub_item != '.' && $sub_item != '..')
			if (is_dir(SC_DIR.'lib/cus/win-groupmanagement/'.$sub_item) && file_exists(SC_DIR.'lib/cus/win-groupmanagement/'.$sub_item.'/cus_win-groupmanagement_'.$sub_item.'_init.php'))
			{
				require_once(SC_DIR.'lib/cus/win-groupmanagement/'.$sub_item.'/cus_win-groupmanagement_'.$sub_item.'_init.php');
			}
	?>
	initGroupManagementPropShopshare();


	//#### MENU ####
	cus_group_cmenu=new dhtmlXMenuObject();
	cus_group_cmenu.renderAsContextMenu();

	wGroupManagement.gridGroups.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		lastColumnRightClicked=colidx;

		cus_group_cmenu.setItemText('object', '<?php echo _l('Group:')?> '+wGroupManagement.gridGroups.cells(rowid,wGroupManagement.gridGroups.getColIndexById('name')).getValue());
		// paste function
		if (lastColumnRightClicked==clipboardType)
		{
			cus_group_cmenu.setItemEnabled('paste');
		}else{
			cus_group_cmenu.setItemDisabled('paste');
		}
		var colType=wGroupManagement.gridGroups.getColType(colidx);
		if (colType=='ro')
		{
			cus_group_cmenu.setItemDisabled('copy');
			cus_group_cmenu.setItemDisabled('paste');
		}else{
			cus_group_cmenu.setItemEnabled('copy');
		}
		return true;
	});

	cus_group_cmenu.attachEvent("onClick", onGridCusGroupContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
		'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
		'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
		'</item>'+
		'</menu>';
	cus_group_cmenu.loadStruct(contextMenuXML);
	wGroupManagement.gridGroups.enableContextMenu(cus_group_cmenu);

	function onGridCusGroupContextButtonClick(itemId) {
		tabId=wGroupManagement.gridGroups.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="gopsbo"){
			wModifyGroup = dhxWins.createWindow("wModifyGroup", 50, 50, 1000, $(window).height()-75);
			wModifyGroup.setText('<?php echo _l('Modify the group and close this window to refresh the grid',1)?>');
			<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
				wModifyGroup.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminGroups&id_group="+tabId+"&updategroup&token=<?php echo $sc_agent->getPSToken('AdminGroups');?>");

			<?php } else { ?>
				wModifyGroup.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminGroups&id_group="+tabId+"&updategroup&token=<?php echo $sc_agent->getPSToken('AdminGroups');?>");
			<?php } ?>
			wModifyGroup.attachEvent("onClose", function(win){
				displayGroupsList();
				return true;
			});
		}
		if (itemId=="copy"){
			if (lastColumnRightClicked!=0)
			{
				clipboardValue=wGroupManagement.gridGroups.cells(tabId,lastColumnRightClicked).getValue();
				cus_group_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+wGroupManagement.gridGroups.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
				clipboardType=lastColumnRightClicked;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
			{
				selection=wGroupManagement.gridGroups.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					selArray=selection.split(',');
					var i;
					for(i=0 ; i < selArray.length ; i++)
					{
						if(
							((wGroupManagement.gridGroups.cells(selArray[i],lastColumnRightClicked).getBgColor()!="#D7D7D7"
							&& wGroupManagement.gridGroups.cells(selArray[i],lastColumnRightClicked).getBgColor()!="#d7d7d7"))
						)
						{
							var oValue = wGroupManagement.gridGroups.cells(selArray[i],lastColumnRightClicked).getValue();
							wGroupManagement.gridGroups.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
							wGroupManagement.gridGroups.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
							onEditCell(2,selArray[i],lastColumnRightClicked,clipboardValue,oValue);
						}
					}
				}
			}
		}
	}


// LOAD FUNCTION
function displayGroupsList()
{
	wGroupManagement.gridGroups.clearAll(true);
	wGroupManagement.gridGroups.loadXML("index.php?ajax=1&act=cus_win-groupmanagement_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
	{
		nb=wGroupManagement.gridGroups.getRowsNum();
		wGroupManagement._sb.setText(nb+(nb>1?" <?php echo _l('groups')?>":" <?php echo _l('group')?>"));
	// UISettings
		loadGridUISettings(wGroupManagement.gridGroups);
		wGroupManagement.gridGroups._first_loading=0;
		lastColumnRightClicked = 0;
	});
}
	
// FUNCTION
function hideGroupManagementSubpropertiesItems()
{
	cus_group_prop_tb.forEachItem(function(itemId){
		if(itemId!="cus_group_prop_subproperties")
			cus_group_prop_tb.hideItem(itemId);
	});
}

// CALLBACK FUNCTION
function callbackGroupUpdate(sid,action,tid,xml)
{
	if (action=='insert')
	{
		idxGroupID=wGroupManagement.gridGroups.getColIndexById('id_group');
		wGroupManagement.gridGroups.cells(sid,idxGroupID).setValue(tid);
		wGroupManagement.gridGroups.changeRowId(sid,tid);
		wGroupManagement.gridGroups.setRowHidden(tid, false);
		wGroupManagement.gridGroups.showRow(tid);
		groups_panel.progressOff();
	}
	else if (action=='update')
		wGroupManagement.gridGroups.setRowTextNormal(sid);
	else if(action=='delete')
		wGroupManagement.gridGroups.deleteRow(sid);
}

</script>
