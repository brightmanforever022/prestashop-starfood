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
	prop_tb.addListOption('panel', 'cmsseo', 15, "button", '<?php echo _l('SEO',1)?>', "lib/img/description.png");
	allowed_properties_panel[allowed_properties_panel.length] = "cmsseo";

	clipboardType_CmsSeo = null;	
	needInitCmsSeo = 1;
	function initCmsSeo()
	{
		if (needInitCmsSeo)
		{
			prop_tb._CmsSeoLayout = dhxLayout.cells('b').attachLayout('2E');
			dhxLayout.cells('b').showHeader();
			
			// SEO
			prop_tb._cmsSeo = prop_tb._CmsSeoLayout.cells('a');
			prop_tb._cmsSeo.setText('<?php echo _l('SEO',1)?>');
			
			prop_tb._cmsSeo_tb = prop_tb._cmsSeo.attachToolbar();
			prop_tb._cmsSeo_tb.addButton("CmsSeo_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
			prop_tb._cmsSeo_tb.setItemToolTip('CmsSeo_refresh','<?php echo _l('Refresh grid',1)?>');
			prop_tb._cmsSeo_tb.addButton("exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
			prop_tb._cmsSeo_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
			
			prop_tb._cmsSeo_tb.attachEvent("onClick", function(id){
					if (id=='CmsSeo_refresh')
					{
						displayCmsSeo();
					}
					else if (id=='exportcsv'){
						prop_tb._cmsSeoGrid.enableCSVHeader(true);
						prop_tb._cmsSeoGrid.setCSVDelimiter("\t");
						var csv=prop_tb._cmsSeoGrid.serializeToCSV(true);
						displayQuickExportWindow(csv,1);
					}
				});
			
			prop_tb._cmsSeoGrid = prop_tb._cmsSeo.attachGrid();
			prop_tb._cmsSeoGrid._name='_cmsSeoGrid';
			prop_tb._cmsSeoGrid.setImagePath("lib/js/imgs/");
  			prop_tb._cmsSeoGrid.enableDragAndDrop(false);
			prop_tb._cmsSeoGrid.enableMultiselect(false);
			
			// UISettings
			prop_tb._cmsSeoGrid._uisettings_prefix='cms_CmsSeo';
			prop_tb._cmsSeoGrid._uisettings_name=prop_tb._cmsSeoGrid._uisettings_prefix;
		   	prop_tb._cmsSeoGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._cmsSeoGrid);
			
			prop_tb._cmsSeoGrid.attachEvent("onEditCell",onEditCellCmsSeo);
			
			
			prop_tb._cmsSeoGrid.attachEvent("onRowSelect",function (idstock){
				if (propertiesPanel=='cmsseo'){
					displayGoogleAdwords();
				}
			});
			
			// Context menu for MultiShops Info Cms grid
			cmsSeo_cmenu=new dhtmlXMenuObject();
			cmsSeo_cmenu.renderAsContextMenu();
			function onGridCmsSeoContextButtonClick(itemId){
				tabId=prop_tb._cmsSeoGrid.contextID.split('_');
				tabId=tabId[0]+"_"+tabId[1]<?php if(SCMS) { ?>+"_"+tabId[2]<?php } ?>;
				if (itemId=="copy"){
					if (lastColumnRightClicked_CmsSeo!=0)
					{
						clipboardValue_CmsSeo=prop_tb._cmsSeoGrid.cells(tabId,lastColumnRightClicked_CmsSeo).getValue();
						cmsSeo_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+prop_tb._cmsSeoGrid.cells(tabId,lastColumnRightClicked_CmsSeo).getTitle());
						clipboardType_CmsSeo=lastColumnRightClicked_CmsSeo;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_CmsSeo!=0 && clipboardValue_CmsSeo!=null && clipboardType_CmsSeo==lastColumnRightClicked_CmsSeo)
					{
						selection=prop_tb._cmsSeoGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								var oValue = prop_tb._cmsSeoGrid.cells(selArray[i],lastColumnRightClicked_CmsSeo).getValue();
								prop_tb._cmsSeoGrid.cells(selArray[i],lastColumnRightClicked_CmsSeo).setValue(clipboardValue_CmsSeo);
								prop_tb._cmsSeoGrid.cells(selArray[i],lastColumnRightClicked_CmsSeo).cell.wasChanged=true;
								onEditCellCmsSeo(2,selArray[i],lastColumnRightClicked_CmsSeo,clipboardValue_CmsSeo,oValue);
							}
						}
					}
				}
			}
			cmsSeo_cmenu.attachEvent("onClick", onGridCmsSeoContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="Lang" id="lang" enabled="false"/>'+
					<?php if(SCMS) { ?>'<item text="Shop" id="shop" enabled="false"/>'+<?php } ?>
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			cmsSeo_cmenu.loadStruct(contextMenuXML);
			prop_tb._cmsSeoGrid.enableContextMenu(cmsSeo_cmenu);

			prop_tb._cmsSeoGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var disableOnCols=new Array(
						prop_tb._cmsSeoGrid.getColIndexById('id_cms'),
						<?php if(SCMS) { ?>prop_tb._cmsSeoGrid.getColIndexById('shop'),<?php } ?>
						prop_tb._cmsSeoGrid.getColIndexById('lang'),
						prop_tb._cmsSeoGrid.getColIndexById('meta_title_width'),
						prop_tb._cmsSeoGrid.getColIndexById('meta_description_width'),
						prop_tb._cmsSeoGrid.getColIndexById('meta_keywords_width')
						);
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_CmsSeo=colidx;
				cmsSeo_cmenu.setItemText('object', '<?php echo _l('Cms:')?> '+prop_tb._cmsSeoGrid.cells(rowid,prop_tb._cmsSeoGrid.getColIndexById('meta_title')).getTitle());
				<?php if(SCMS) { ?>cmsSeo_cmenu.setItemText('shop', '<?php echo _l('Shop:')?> '+prop_tb._cmsSeoGrid.cells(rowid,prop_tb._cmsSeoGrid.getColIndexById('shop')).getTitle());<?php } ?>
				cmsSeo_cmenu.setItemText('lang', '<?php echo _l('Lang:')?> '+prop_tb._cmsSeoGrid.cells(rowid,prop_tb._cmsSeoGrid.getColIndexById('lang')).getTitle());
				if (lastColumnRightClicked_CmsSeo==clipboardType_CmsSeo)
				{
					cmsSeo_cmenu.setItemEnabled('paste');
				}else{
					cmsSeo_cmenu.setItemDisabled('paste');
				}
				return true;
			});
			
			// GOOGLE ADD
			prop_tb._googleAdwords = prop_tb._CmsSeoLayout.cells('b');
			prop_tb._googleAdwords.setHeight(150);
			prop_tb._googleAdwords.setText('<?php echo _l('Google Adwords',1)?>');
			
			prop_tb._googleAdwords_tb = prop_tb._googleAdwords.attachToolbar();
			prop_tb._googleAdwords_tb.addButton("googleAdwords_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
			prop_tb._googleAdwords_tb.setItemToolTip('googleAdwords_refresh','<?php echo _l('Refresh grid',1)?>');
			prop_tb._googleAdwords_tb.attachEvent("onClick", function(id){
				if (id=='googleAdwords_refresh')
				{
					displayGoogleAdwords();
				}
				
			});
		
			needInitCmsSeo=0;
		}
	}
	
	
			
	function onEditCellCmsSeo(stage,rId,cInd,nValue,oValue)
	{
		if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
		
		if (stage==2 && nValue!=oValue)
		{		
			idxLinkRewrite=prop_tb._cmsSeoGrid.getColIndexById('link_rewrite');
			if (nValue!="" && cInd==idxLinkRewrite)
			{
				<?php 
				$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
				if($accented==1) {	?>
					prop_tb._cmsSeoGrid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));				
				<?php } else { ?>
					prop_tb._cmsSeoGrid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));				
				<?php } ?>
			}
		
			var params = {
				name: "cms_seo_update_queue",
				row: rId,
				action: "update",
				params: {},
				callback: "callbackCmsSeo('"+rId+"','update','"+rId+"');"
			};
			// COLUMN VALUES
			/*prop_tb._cmsSeoGrid.forEachCell(rId,function(cellObj,ind){
				params.params[prop_tb._cmsSeoGrid.getColumnId(ind)] = prop_tb._cmsSeoGrid.cells(rId,ind).getValue();
			});*/
			params.params[prop_tb._cmsSeoGrid.getColumnId(cInd)] = prop_tb._cmsSeoGrid.cells(rId,cInd).getValue();
			// USER DATA
			/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
			
			params.params = JSON.stringify(params.params);
			addInUpdateQueue(params,prop_tb._cmsSeoGrid);
		}
		return true;
	}
	// CALLBACK FUNCTION
	function callbackCmsSeo(sid,action,tid)
	{
		if (action=='update')
			prop_tb._cmsSeoGrid.setRowTextNormal(sid);
	}
	
	function setPropertiesPanel_CmsSeo(id){
		if (id=='cmsseo')
		{
			if(lastcms_pageID!=undefined && lastcms_pageID!="")
			{
				idxMetaTitle=cms_grid.getColIndexById('meta_title');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cms_grid.cells(lastcms_pageID,idxMetaTitle).getValue());
			}
			hidePropTBButtons();
			prop_tb.setItemText('panel', '<?php echo _l('SEO',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/description.png');
			needInitCmsSeo = 1;
			initCmsSeo();
			propertiesPanel='cmsseo';
			if (lastcms_pageID!=0)
			{
				displayCmsSeo();
				displayGoogleAdwords();
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_CmsSeo);

	function displayCmsSeo()
	{
		prop_tb._cmsSeoGrid.clearAll(true);
		var tempIdList = (cms_grid.getSelectedRowId()!=null?cms_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=cms_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			prop_tb._cmsSeoGrid.parse(data);
			nb=prop_tb._cmsSeoGrid.getRowsNum();
			prop_tb._cmsSeoGrid._rowsNum=nb;
			
   			// UISettings
			loadGridUISettings(prop_tb._cmsSeoGrid);
			prop_tb._cmsSeoGrid._first_loading=0;
		});
	}

	function displayGoogleAdwords()
	{
		prop_tb._googleAdwords.setHeight(150);
		prop_tb._googleAdwords.attachURL("index.php?ajax=1&act=cms_seo_add_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	}

	cms_grid.attachEvent("onRowSelect",function (idcms){
			if (propertiesPanel=='cmsseo'){
				//initCmsSeo();
				displayCmsSeo();
				displayGoogleAdwords();
			}
		});
