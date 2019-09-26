// INITIALISATION TOOLBAR
cat_prop_tb.addListOption('cat_prop_subproperties', 'cat_prop_seo', 4, "button", '<?php
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
 echo _l('SEO',1)?>', "lib/img/description.png");

cat_prop_tb.attachEvent("onClick", function(id){
	if(id=="cat_prop_seo")
	{
		hideCatManagementSubpropertiesItems();
		cat_prop_tb.setItemText('cat_prop_subproperties', '<?php echo _l('SEO',1)?>');
		cat_prop_tb.setItemImage('cat_prop_subproperties', 'lib/img/description.png');
		actual_catmanagement_subproperties = "cat_prop_seo";
		initCatManagementPropSeo();
	}
});
				
cat_treegrid_grid.attachEvent("onRowSelect", function(id,ind){
	if (!dhxlCatManagement.cells('b').isCollapsed())
	{
		if(actual_catmanagement_subproperties == "cat_prop_seo"){
	 		getCatManagementPropSeo();
		}
	}
});
		
cat_prop_tb.addButton('cat_prop_seo_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
cat_prop_tb.setItemToolTip('cat_prop_seo_refresh','<?php echo _l('Refresh grid',1)?>');
if (isIPAD)
{
	cat_prop_tb.addButtonTwoState('cat_prop_seo_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
	cat_prop_tb.setItemToolTip('cat_prop_seo_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
}
cat_prop_tb.addButton('cat_prop_seo_selectall',100,'','lib/img/application_lightning.png','lib/img/application_lightning.png');
cat_prop_tb.setItemToolTip('cat_prop_seo_selectall','<?php echo _l('Select all',1)?>');
hideCatManagementSubpropertiesItems();

cat_prop_tb.attachEvent("onClick", function(id){
	if (id=='cat_prop_seo_refresh')
	{
		getCatManagementPropSeo();
	}
	if (id=='cat_prop_seo_selectall')
	{
		cat_prop_seo_grid.selectAll();
	}
});

cat_prop_tb.attachEvent("onStateChange",function(id,state){
	if (id=='cat_prop_seo_lightNavigation')
	{
		if (state)
		{
			cat_prop_seo_grid.enableLightMouseNavigation(true);
		}else{
			cat_prop_seo_grid.enableLightMouseNavigation(false);
		}
	}
});
	
// FUNCTIONS
var cat_prop_seo = null;
var clipboardType_CatPropSeo = null;
function initCatManagementPropSeo()
{
	cat_prop_tb.showItem('cat_prop_seo_refresh');
	cat_prop_tb.showItem('cat_prop_seo_lightNavigation');
	cat_prop_tb.showItem('cat_prop_seo_selectall');
	
	cat_prop_seo = dhxlCatManagement.cells('b').attachLayout("1C");
	dhxlCatManagement.cells('b').showHeader();
	
	// GRID
		cat_prop_seo.cells('a').hideHeader();
		
		
		
		cat_prop_seo_grid = cat_prop_seo.cells('a').attachGrid();
		cat_prop_seo_grid.setImagePath("lib/js/imgs/");
	  	cat_prop_seo_grid.enableDragAndDrop(false);
		cat_prop_seo_grid.enableMultiselect(true);
	
		// UISettings
		cat_prop_seo_grid._uisettings_prefix='cat_prop_seo_grid';
		cat_prop_seo_grid._uisettings_name=cat_prop_seo_grid._uisettings_prefix;
		cat_prop_seo_grid._first_loading=1;
			   	
		// UISettings
		initGridUISettings(cat_prop_seo_grid);
		
		getCatManagementPropSeo();
		
		cat_prop_seo_DataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_seo_update&id_lang="+SC_ID_LANG;
		cat_prop_seo_DataProcessor = new dataProcessor(cat_prop_seo_DataProcessorURLBase);
		cat_prop_seo_DataProcessor.enableDataNames(true);
		cat_prop_seo_DataProcessor.setTransactionMode("GET");
		cat_prop_seo_DataProcessor.enablePartialDataSend(true);
		cat_prop_seo_DataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_seo_update&id_lang="+SC_ID_LANG;
		cat_prop_seo_DataProcessor.serverProcessor=cat_prop_seo_DataProcessorURLBase;
		cat_prop_seo_DataProcessor.init(cat_prop_seo_grid);
		
		/*cat_prop_seo_DataProcessor.attachEvent("onBeforeUpdate",function(rId,status, data){
			if(data["link_rewrite"]!=undefined)
			{
				var nValue = data["link_rewrite"];
				if (nValue!=""){
					idxLinkRewrite=cat_prop_seo_grid.getColIndexById('link_rewrite');
					
					<?php 
					$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
					if($accented==1) {	?>
						cat_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));				
					<?php } else { ?>
						cat_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));				
					<?php } ?>
				}
			}
			return true;
		});*/
		
		cat_prop_seo_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
			if(stage==2 && nValue!=oValue)
			{
				idxLinkRewrite=cat_prop_seo_grid.getColIndexById('link_rewrite');
				if (nValue!="" && cInd==idxLinkRewrite)
				{
					<?php 
					$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
					if($accented==1) {	?>
						cat_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));				
					<?php } else { ?>
						cat_prop_seo_grid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));				
					<?php } ?>
				}
			}
			return true;
		});
		
		// Context menu for grid
		cat_prop_seo_cmenu=new dhtmlXMenuObject();
		cat_prop_seo_cmenu.renderAsContextMenu();
		function onGridCatPropSeoContextButtonClick(itemId){
			tabId=cat_prop_seo_grid.contextID.split('_');
			tabId=tabId[0]+"_"+tabId[1]<?php if(SCMS) { ?>+"_"+tabId[2]<?php } ?>;
			if (itemId=="copy"){
				if (lastColumnRightClicked_CatPropSeo!=0)
				{
					clipboardValue_CatPropSeo=cat_prop_seo_grid.cells(tabId,lastColumnRightClicked_CatPropSeo).getValue();
					cat_prop_seo_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cat_prop_seo_grid.cells(tabId,lastColumnRightClicked_CatPropSeo).getTitle());
					clipboardType_CatPropSeo=lastColumnRightClicked_CatPropSeo;
				}
			}
			if (itemId=="paste"){
				if (lastColumnRightClicked_CatPropSeo!=0 && clipboardValue_CatPropSeo!=null && clipboardType_CatPropSeo==lastColumnRightClicked_CatPropSeo)
				{
					selection=cat_prop_seo_grid.getSelectedRowId();
					if (selection!='' && selection!=null)
					{
						selArray=selection.split(',');
						for(i=0 ; i < selArray.length ; i++)
						{
							cat_prop_seo_grid.cells(selArray[i],lastColumnRightClicked_CatPropSeo).setValue(clipboardValue_CatPropSeo);
							cat_prop_seo_grid.cells(selArray[i],lastColumnRightClicked_CatPropSeo).cell.wasChanged=true;
							cat_prop_seo_DataProcessor.setUpdated(selArray[i],true,"updated");
						}
					}
				}
			}
		}
		cat_prop_seo_cmenu.attachEvent("onClick", onGridCatPropSeoContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="Object" id="object2" enabled="false"/>'+
				<?php if(SCMS) { ?>'<item text="Object" id="object3" enabled="false"/>'+<?php } ?>
				'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
				'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
			'</menu>';
		cat_prop_seo_cmenu.loadStruct(contextMenuXML);
		cat_prop_seo_grid.enableContextMenu(cat_prop_seo_cmenu);

		cat_prop_seo_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			var enableOnCols=new Array(
					cat_prop_seo_grid.getColIndexById('link_rewrite'),
					cat_prop_seo_grid.getColIndexById('meta_title'),
					cat_prop_seo_grid.getColIndexById('meta_description'),
					cat_prop_seo_grid.getColIndexById('meta_keywords')
					);
			if (!in_array(colidx,enableOnCols))
			{
				return false;
			}
			lastColumnRightClicked_CatPropSeo=colidx;
			cat_prop_seo_cmenu.setItemText('object', '<?php echo _l('Category:')?> '+cat_prop_seo_grid.cells(rowid,cat_prop_seo_grid.getColIndexById('id_category')).getTitle());
			cat_prop_seo_cmenu.setItemText('object2', '<?php echo _l('Lang:')?> '+cat_prop_seo_grid.cells(rowid,cat_prop_seo_grid.getColIndexById('lang')).getTitle());
			<?php if(SCMS) { ?>cat_prop_seo_cmenu.setItemText('object3', '<?php echo _l('Shop:')?> '+cat_prop_seo_grid.cells(rowid,cat_prop_seo_grid.getColIndexById('shop')).getTitle());<?php } ?>
			if (lastColumnRightClicked_CatPropSeo==clipboardType_CatPropSeo)
			{
				cat_prop_seo_cmenu.setItemEnabled('paste');
			}else{
				cat_prop_seo_cmenu.setItemDisabled('paste');
			}
			return true;
		});
}

function getCatManagementPropSeo()
{
	cat_prop_seo_grid.clearAll(true);
		var tempIdList = (cat_treegrid_grid.getSelectedRowId()!=null?cat_treegrid_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cat_win-catmanagement_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			cat_prop_seo_grid.parse(data);
				
    		// UISettings
				loadGridUISettings(cat_prop_seo_grid);
				cat_prop_seo_grid._first_loading=0;
		});
}