// INITIALISATION TOOLBAR
cus_group_prop_tb.addListOption('cus_group_prop_subproperties', 'cus_group_prop_seo', 2, "button", '<?php
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
 echo _l('Name',1)?>', "lib/img/description.png");

cus_group_prop_tb.attachEvent("onClick", function(id){
	if(id=="cus_group_prop_seo")
	{
		hideGroupManagementSubpropertiesItems();
		cus_group_prop_tb.setItemText('cus_group_prop_subproperties', '<?php echo _l('Name',1)?>');
		cus_group_prop_tb.setItemImage('cus_group_prop_subproperties', 'lib/img/description.png');
		actual_groupmanagement_subproperties = "cus_group_prop_seo";
		initGroupManagementPropSeo();
	}
});

wGroupManagement.gridGroups.attachEvent("onRowSelect", function(id,ind){
	if(actual_groupmanagement_subproperties == "cus_group_prop_seo"){
		getGroupManagementPropSeo();
	}
});
		
cus_group_prop_tb.addButton('cus_group_prop_seo_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cus_group_prop_tb.setItemToolTip('cus_group_prop_seo_refresh','<?php echo _l('Refresh grid',1)?>');
cus_group_prop_tb.addButton('cus_group_prop_seo_selectall',100,'','lib/img/application_lightning.png','lib/img/application_lightning.png');
cus_group_prop_tb.setItemToolTip('cus_group_prop_seo_selectall','<?php echo _l('Select all',1)?>');
hideGroupManagementSubpropertiesItems();

cus_group_prop_tb.attachEvent("onClick", function(id){
	if (id=='cus_group_prop_seo_refresh')
	{
		getGroupManagementPropSeo();
	}
	if (id=='cus_group_prop_seo_selectall')
	{
		cus_group_prop_seo_grid.selectAll();
	}
});


	
// FUNCTIONS
var cus_group_prop_seo = null;
var clipboardType_GroupPropSeo = null;
function initGroupManagementPropSeo()
{
	cus_group_prop_tb.showItem('cus_group_prop_seo_refresh');
	cus_group_prop_tb.showItem('cus_group_prop_seo_lightNavigation');
	cus_group_prop_tb.showItem('cus_group_prop_seo_selectall');
	
	cus_group_prop_seo = groups_properties_panel.attachLayout("1C");
	groups_properties_panel.showHeader();
	
	// GRID
		cus_group_prop_seo.cells('a').hideHeader();
		cus_group_prop_seo_grid = cus_group_prop_seo.cells('a').attachGrid();
		cus_group_prop_seo_grid.setImagePath("lib/js/imgs/");
	  	cus_group_prop_seo_grid.enableDragAndDrop(false);
		cus_group_prop_seo_grid.enableMultiselect(true);
	
		// UISettings
		cus_group_prop_seo_grid._uisettings_prefix='cus_group_prop_seo_grid';
		cus_group_prop_seo_grid._uisettings_name=cus_group_prop_seo_grid._uisettings_prefix;
		cus_group_prop_seo_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cus_group_prop_seo_grid);
		
		getGroupManagementPropSeo();
		
		// Context menu for grid
		cus_group_prop_seo_cmenu=new dhtmlXMenuObject();
		cus_group_prop_seo_cmenu.renderAsContextMenu();
		function onGridGroupPropSeoContextButtonClick(itemId){
			tabId=cus_group_prop_seo_grid.contextID.split('_');
			tabId=tabId[0]+"_"+tabId[1];
			if (itemId=="copy"){
				if (lastColumnRightClicked_GroupPropSeo!=0)
				{
					clipboardValue_GroupPropSeo=cus_group_prop_seo_grid.cells(tabId,lastColumnRightClicked_GroupPropSeo).getValue();
					cus_group_prop_seo_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cus_group_prop_seo_grid.cells(tabId,lastColumnRightClicked_GroupPropSeo).getTitle());
					clipboardType_GroupPropSeo=lastColumnRightClicked_GroupPropSeo;
				}
			}
			if (itemId=="paste"){
				if (lastColumnRightClicked_GroupPropSeo!=0 && clipboardValue_GroupPropSeo!=null && clipboardType_GroupPropSeo==lastColumnRightClicked_GroupPropSeo)
				{
					selection=cus_group_prop_seo_grid.getSelectedRowId();
					if (selection!='' && selection!=null)
					{
						updateNameOfGroup(selection,clipboardValue_GroupPropSeo);
					}
				}
			}
		}
		cus_group_prop_seo_cmenu.attachEvent("onClick", onGridGroupPropSeoContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
				'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</item>'+
			'</menu>';
		cus_group_prop_seo_cmenu.loadStruct(contextMenuXML);
		cus_group_prop_seo_grid.enableContextMenu(cus_group_prop_seo_cmenu);

		cus_group_prop_seo_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			if (colidx != cus_group_prop_seo_grid.getColIndexById('name'))
			{
				return false;
			}
			lastColumnRightClicked_GroupPropSeo=colidx;
			cus_group_prop_seo_cmenu.setItemText('object', '<?php echo _l('Group:')?> '+cus_group_prop_seo_grid.cells(rowid,cus_group_prop_seo_grid.getColIndexById('id_group')).getTitle());
			if (lastColumnRightClicked_GroupPropSeo==clipboardType_GroupPropSeo)
			{
				cus_group_prop_seo_cmenu.setItemEnabled('paste');
			}else{
				cus_group_prop_seo_cmenu.setItemDisabled('paste');
			}
			return true;
		});

		cus_group_prop_seo_grid.attachEvent("onEditCell",onEditGroupLangCell);
		function onEditGroupLangCell(stage,rId,cInd,nValue,oValue){
			if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
			if(stage==2)
			{
				if (nValue!=oValue)
				{
					updateNameOfGroup(rId,nValue);
				}
			}
			return true;
		}
}

function updateNameOfGroup(rId,nValue)
{
	cus_group_prop_seo_grid.setRowTextBold(rId);
	$.post("index.php?ajax=1&act=cus_win-groupmanagement_seo_update&action=updated&"+new Date().getTime(),{"ids":rId, "value":nValue},function(data){
		if(data == 'OK') {
			cus_group_prop_seo_grid.setRowTextNormal(rId);
		}
	});
}

function getGroupManagementPropSeo()
{
	cus_group_prop_seo_grid.clearAll(true);
	var tempIdList = (wGroupManagement.gridGroups.getSelectedRowId()!=null?wGroupManagement.gridGroups.getSelectedRowId():"");
	$.post("index.php?ajax=1&act=cus_win-groupmanagement_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
	{
		cus_group_prop_seo_grid.parse(data);

		// UISettings
			loadGridUISettings(cus_group_prop_seo_grid);
			cus_group_prop_seo_grid._first_loading=0;
	});
}
