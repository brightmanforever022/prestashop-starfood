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
// INITIALISATION TOOLBAR
cms_prop_tb.addListOption('cms_prop_subproperties', 'cms_prop_info', 1, "button", '<?php echo _l('Name & description',1)?>', "lib/img/description.png");

cms_prop_tb.attachEvent("onClick", function(id){
	if(id=="cms_prop_info")
	{
		hideCmsCatManagementSubpropertiesItems();
		cms_prop_tb.setItemText('cms_prop_subproperties', '<?php echo _l('Name & description',1)?>');
		cms_prop_tb.setItemImage('cms_prop_subproperties', 'lib/img/description.png');
		actual_cmscatmanagement_subproperties = "cms_prop_info";
		initCmsCatManagementPropInfo();
	}
});

cms_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
	if(actual_cmscatmanagement_subproperties == "cms_prop_info"){
		getCmsCatManagementPropInfo();
	}
});
		
cms_prop_tb.addButton('cms_prop_info_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cms_prop_tb.setItemToolTip('cms_prop_info_refresh','<?php echo _l('Refresh grid',1)?>');
if (isIPAD)
{
	cms_prop_tb.addButtonTwoState('cms_prop_info_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
	cms_prop_tb.setItemToolTip('cms_prop_info_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
}
hideCmsCatManagementSubpropertiesItems();

cms_prop_tb.attachEvent("onClick", function(id){
	if (id=='cms_prop_info_refresh')
	{
		getCmsCatManagementPropInfo();
	}
});


	cms_prop_tb.attachEvent("onStateChange",function(id,state){
		if (id=='cms_prop_info_lightNavigation')
		{
			if (state)
			{
				cms_prop_info_grid.enableLightMouseNavigation(true);
			}else{
				cms_prop_info_grid.enableLightMouseNavigation(false);
			}
		}
	});

// FUNCTIONS
var cms_prop_info = null;
var clipboardType_CmsCatPropInfo = null;
function initCmsCatManagementPropInfo()
{
	cms_prop_tb.showItem('cms_prop_info_refresh');
	cms_prop_tb.showItem('cms_prop_info_lightNavigation');
	
	cms_prop_info = dhxlCmsCatManagement.cells('b').attachLayout("1C");
	dhxlCmsCatManagement.cells('b').showHeader();
	
	// GRID
		cms_prop_info.cells('a').hideHeader();
		
		cms_prop_info_grid = cms_prop_info.cells('a').attachGrid();
		cms_prop_info_grid.setImagePath("lib/js/imgs/");
	  	cms_prop_info_grid.enableDragAndDrop(false);
		cms_prop_info_grid.enableMultiselect(true);
	
		// UISettings
		cms_prop_info_grid._uisettings_prefix='cms_prop_info_grid';
		cms_prop_info_grid._uisettings_name=cms_prop_info_grid._uisettings_prefix;
		cms_prop_info_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cms_prop_info_grid);
		
		getCmsCatManagementPropInfo();

		// Data update
		cms_prop_info_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
			idxName=cms_prop_info_grid.getColIndexById('name');
			idxDescription=cms_prop_info_grid.getColIndexById('description');

			if(stage==0 || stage==1)
			{
				var is_recycle_bin = cms_prop_info_grid.getUserData(rId,"is_recycle_bin");
				if(is_recycle_bin=="1")
					return false;
			}

			var field = '';
			if(idxName==cInd) {
				field = 'name';
			}else if(idxDescription==cInd) {
				field = 'description';
			}
			var enableOnCols=new Array(
					idxName,
					idxDescription
				);
			if (!in_array(cInd,enableOnCols))
				return false;

			if(stage==2)
			{
				$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_info_update&action=update&gr_id="+rId+"&field="+field+"&value="+nValue+'&id_shop='+id_shop+'&id_lang='+SC_ID_LANG, function(data){
					var valueLength = nValue.length;
					idDescriptionWidthCol=cms_prop_info_grid.getColIndexById('description_width');
					cms_prop_info_grid.cells(rId,idDescriptionWidthCol).setValue(valueLength);
				});
			}
			return true;
		});
		
		
		// Context menu for grid
		cms_prop_info_cmenu=new dhtmlXMenuObject();
		cms_prop_info_cmenu.renderAsContextMenu();
		function onGridCmsCatPropInfoContextButtonClick(itemId){
			tabId=cms_prop_info_grid.contextID.split('_');
			tabId=tabId[0]+"_"+tabId[1]<?php if(SCMS) { ?>+"_"+tabId[2]<?php } ?>;
			if (itemId=="copy"){
				if (lastColumnRightClicked_CmsCatPropInfo!=0)
				{
					clipboardValue_CmsCatPropInfo=cms_prop_info_grid.cells(tabId,lastColumnRightClicked_CmsCatPropInfo).getValue();
					cms_prop_info_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cms_prop_info_grid.cells(tabId,lastColumnRightClicked_CmsCatPropInfo).getTitle());
					clipboardType_CmsCatPropInfo=lastColumnRightClicked_CmsCatPropInfo;
				}
			}
			if (itemId=="paste"){
				if (lastColumnRightClicked_CmsCatPropInfo!=0 && clipboardValue_CmsCatPropInfo!=null && clipboardType_CmsCatPropInfo==lastColumnRightClicked_CmsCatPropInfo)
				{
					selection=cms_prop_info_grid.getSelectedRowId();
					if (selection!='' && selection!=null)
					{
						idxName=cms_prop_info_grid.getColIndexById('name');
						idxDescription=cms_prop_info_grid.getColIndexById('description');

						selArray=selection.split(',');
						for(i=0 ; i < selArray.length ; i++)
						{
							cms_prop_info_grid.cells(selArray[i],lastColumnRightClicked_CmsCatPropInfo).setValue(clipboardValue_CmsCatPropInfo);
							var field = 'name';
							if(idxDescription==lastColumnRightClicked_CmsCatPropInfo)
								field = 'description';
							$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_info_update&action=update&DEBUG=1&gr_id="+selArray[i]+"&field="+field+"&value="+clipboardValue_CmsCatPropInfo+'&id_shop='+id_shop+'&id_lang='+id_actual_lang, function(data){});
							colorActive();
						}
					}
				}
			}
		}
		cms_prop_info_cmenu.attachEvent("onClick", onGridCmsCatPropInfoContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="Object" id="object2" enabled="false"/>'+
				<?php if(SCMS) { ?>'<item text="Object" id="object3" enabled="false"/>'+<?php } ?>
				'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
				'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
			'</menu>';
		cms_prop_info_cmenu.loadStruct(contextMenuXML);
		cms_prop_info_grid.enableContextMenu(cms_prop_info_cmenu);

		cms_prop_info_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			var enableOnCols=new Array(
					cms_prop_info_grid.getColIndexById('name'),
					cms_prop_info_grid.getColIndexById('description')
					);
			if (!in_array(colidx,enableOnCols))
			{
				return false;
			}
			lastColumnRightClicked_CmsCatPropInfo=colidx;
			cms_prop_info_cmenu.setItemText('object', '<?php echo _l('Cms Category:')?> '+cms_prop_info_grid.cells(rowid,cms_prop_info_grid.getColIndexById('id_cms_category')).getTitle());
			cms_prop_info_cmenu.setItemText('object2', '<?php echo _l('Lang:')?> '+cms_prop_info_grid.cells(rowid,cms_prop_info_grid.getColIndexById('lang')).getTitle());
			<?php if(SCMS) { ?>cms_prop_info_cmenu.setItemText('object3', '<?php echo _l('Shop:')?> '+cms_prop_info_grid.cells(rowid,cms_prop_info_grid.getColIndexById('shop')).getTitle());<?php } ?>
			if (lastColumnRightClicked_CmsCatPropInfo==clipboardType_CmsCatPropInfo)
			{
				cms_prop_info_cmenu.setItemEnabled('paste');
			}else{
				cms_prop_info_cmenu.setItemDisabled('paste');
			}
			return true;
		});
}

function getCmsCatManagementPropInfo()
{
	oldFilters=new Array();
	for(var i=0,l=cms_prop_info_grid.getColumnsNum();i<l;i++)
	{
		if (cms_prop_info_grid.getFilterElement(i)!=null && cms_prop_info_grid.getFilterElement(i).value!='') {
			oldFilters[cms_prop_info_grid.getColumnId(i)]=cms_prop_info_grid.getFilterElement(i).value;
		}
	}

	cms_prop_info_grid.clearAll(true);
		var tempIdList = (cms_treegrid_grid.getSelectedRowId()!=null?cms_treegrid_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_info_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			cms_prop_info_grid.parse(data);

			for(var i=0;i<cms_prop_info_grid.getColumnsNum();i++)
			{
				if (cms_prop_info_grid.getFilterElement(i)!=null && oldFilters[cms_prop_info_grid.getColumnId(i)]!=undefined)
				{
					cms_prop_info_grid.getFilterElement(i).value=oldFilters[cms_prop_info_grid.getColumnId(i)];
				}
			}
			cms_prop_info_grid.filterByAll();

    		// UISettings
				loadGridUISettings(cms_prop_info_grid);
				cms_prop_info_grid._first_loading=0;
		});
}
