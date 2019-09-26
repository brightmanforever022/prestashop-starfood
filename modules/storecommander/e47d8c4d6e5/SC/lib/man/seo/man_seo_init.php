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
	prop_tb.addListOption('panel', 'manufacturerseo', 15, "button", '<?php echo _l('SEO',1)?>', "lib/img/description.png");
	allowed_properties_panel[allowed_properties_panel.length] = "manufacturerseo";

	clipboardType_ManufacturerSeo = null;
	needInitManufacturerSeo = 1;
	function initManufacturerSeo()
	{
		if (needInitManufacturerSeo)
		{
			prop_tb._ManufacturerSeoLayout = dhxLayout.cells('b').attachLayout('2E');
			dhxLayout.cells('b').showHeader();
			
			// SEO
			prop_tb._manufacturerSeo = prop_tb._ManufacturerSeoLayout.cells('a');
			prop_tb._manufacturerSeo.setText('<?php echo _l('SEO',1)?>');
			
			prop_tb._manufacturerSeo_tb = prop_tb._manufacturerSeo.attachToolbar();
			prop_tb._manufacturerSeo_tb.addButton("ManufacturerSeo_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
			prop_tb._manufacturerSeo_tb.setItemToolTip('ManufacturerSeo_refresh','<?php echo _l('Refresh grid',1)?>');
			prop_tb._manufacturerSeo_tb.addButton("exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
			prop_tb._manufacturerSeo_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
			
			prop_tb._manufacturerSeo_tb.attachEvent("onClick", function(id){
					if (id=='ManufacturerSeo_refresh')
					{
						displayManufacturerSeo();
					}
					else if (id=='exportcsv'){
						prop_tb._manufacturerSeoGrid.enableCSVHeader(true);
						prop_tb._manufacturerSeoGrid.setCSVDelimiter("\t");
						var csv=prop_tb._manufacturerSeoGrid.serializeToCSV(true);
						displayQuickExportWindow(csv,1);
					}
				});
			
			prop_tb._manufacturerSeoGrid = prop_tb._manufacturerSeo.attachGrid();
			prop_tb._manufacturerSeoGrid._name='_manufacturerSeoGrid';
			prop_tb._manufacturerSeoGrid.setImagePath("lib/js/imgs/");
  			prop_tb._manufacturerSeoGrid.enableDragAndDrop(false);
			prop_tb._manufacturerSeoGrid.enableMultiselect(false);
			
			// UISettings
			prop_tb._manufacturerSeoGrid._uisettings_prefix='man_ManufacturerSeo';
			prop_tb._manufacturerSeoGrid._uisettings_name=prop_tb._manufacturerSeoGrid._uisettings_prefix;
		   	prop_tb._manufacturerSeoGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._manufacturerSeoGrid);
			
			prop_tb._manufacturerSeoGrid.attachEvent("onEditCell",onEditCellManufacturerSeo);
			
			
			prop_tb._manufacturerSeoGrid.attachEvent("onRowSelect",function (idstock){
				if (propertiesPanel=='manufacturerseo'){
					displayGoogleAdwords();
				}
			});
			
			// Context menu for MultiShops Info Product grid
			manufacturerSeo_cmenu=new dhtmlXMenuObject();
			manufacturerSeo_cmenu.renderAsContextMenu();
			function onGridManufacturerSeoContextButtonClick(itemId){
				tabId=prop_tb._manufacturerSeoGrid.contextID.split('_');
				tabId=tabId[0]+"_"+tabId[1]<?php if(SCMS) { ?>+"_"+tabId[2]<?php } ?>;
				if (itemId=="copy"){
					if (lastColumnRightClicked_ManufacturerSeo!=0)
					{
						clipboardValue_ManufacturerSeo=prop_tb._manufacturerSeoGrid.cells(tabId,lastColumnRightClicked_ManufacturerSeo).getValue();
						manufacturerSeo_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+prop_tb._manufacturerSeoGrid.cells(tabId,lastColumnRightClicked_ManufacturerSeo).getTitle());
						clipboardType_ManufacturerSeo=lastColumnRightClicked_ManufacturerSeo;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_ManufacturerSeo!=0 && clipboardValue_ManufacturerSeo!=null && clipboardType_ManufacturerSeo==lastColumnRightClicked_ManufacturerSeo)
					{
						selection=prop_tb._manufacturerSeoGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								var oValue = prop_tb._manufacturerSeoGrid.cells(selArray[i],lastColumnRightClicked_ManufacturerSeo).getValue();
								prop_tb._manufacturerSeoGrid.cells(selArray[i],lastColumnRightClicked_ManufacturerSeo).setValue(clipboardValue_ManufacturerSeo);
								prop_tb._manufacturerSeoGrid.cells(selArray[i],lastColumnRightClicked_ManufacturerSeo).cell.wasChanged=true;
								onEditCellManufacturerSeo(2,selArray[i],lastColumnRightClicked_ManufacturerSeo,clipboardValue_ManufacturerSeo,oValue);
							}
						}
					}
				}
			}
			manufacturerSeo_cmenu.attachEvent("onClick", onGridManufacturerSeoContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="Lang" id="lang" enabled="false"/>'+
					<?php if(SCMS) { ?>'<item text="Shop" id="shop" enabled="false"/>'+<?php } ?>
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			manufacturerSeo_cmenu.loadStruct(contextMenuXML);
			prop_tb._manufacturerSeoGrid.enableContextMenu(manufacturerSeo_cmenu);

			prop_tb._manufacturerSeoGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var disableOnCols=new Array(
						prop_tb._manufacturerSeoGrid.getColIndexById('id_product'),
						<?php if(SCMS) { ?>prop_tb._manufacturerSeoGrid.getColIndexById('shop'),<?php } ?>
						prop_tb._manufacturerSeoGrid.getColIndexById('lang'),
						prop_tb._manufacturerSeoGrid.getColIndexById('meta_title_width'),
						prop_tb._manufacturerSeoGrid.getColIndexById('meta_description_width'),
						prop_tb._manufacturerSeoGrid.getColIndexById('meta_keywords_width')
						);
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_ManufacturerSeo=colidx;
				manufacturerSeo_cmenu.setItemText('object', '<?php echo _l('Manufacturer:')?> '+prop_tb._manufacturerSeoGrid.cells(rowid,prop_tb._manufacturerSeoGrid.getColIndexById('name')).getTitle());
				<?php if(SCMS) { ?>manufacturerSeo_cmenu.setItemText('shop', '<?php echo _l('Shop:')?> '+prop_tb._manufacturerSeoGrid.cells(rowid,prop_tb._manufacturerSeoGrid.getColIndexById('shop')).getTitle());<?php } ?>
				manufacturerSeo_cmenu.setItemText('lang', '<?php echo _l('Lang:')?> '+prop_tb._manufacturerSeoGrid.cells(rowid,prop_tb._manufacturerSeoGrid.getColIndexById('lang')).getTitle());
				if (lastColumnRightClicked_ManufacturerSeo==clipboardType_ManufacturerSeo)
				{
					manufacturerSeo_cmenu.setItemEnabled('paste');
				}else{
					manufacturerSeo_cmenu.setItemDisabled('paste');
				}
				return true;
			});
			
			// GOOGLE ADD
			prop_tb._googleAdwords = prop_tb._ManufacturerSeoLayout.cells('b');
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
		
			needInitManufacturerSeo=0;
		}
	}
	
	
			
	function onEditCellManufacturerSeo(stage,rId,cInd,nValue,oValue)
	{
		if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
		
		if (stage==2 && nValue!=oValue)
		{		
			idxLinkRewrite=prop_tb._manufacturerSeoGrid.getColIndexById('link_rewrite');
			if (nValue!="" && cInd==idxLinkRewrite)
			{
				<?php 
				$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
				if($accented==1) {	?>
					prop_tb._manufacturerSeoGrid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));
				<?php } else { ?>
					prop_tb._manufacturerSeoGrid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(nValue.substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));
				<?php } ?>
			}
		
			var params = {
				name: "man_seo_update_queue",
				row: rId,
				action: "update",
				params: {},
				callback: "callbackManufacturerSeo('"+rId+"','update','"+rId+"');"
			};
			// COLUMN VALUES
			/*prop_tb._manufacturerSeoGrid.forEachCell(rId,function(cellObj,ind){
				params.params[prop_tb._manufacturerSeoGrid.getColumnId(ind)] = prop_tb._manufacturerSeoGrid.cells(rId,ind).getValue();
			});*/
			params.params[prop_tb._manufacturerSeoGrid.getColumnId(cInd)] = prop_tb._manufacturerSeoGrid.cells(rId,cInd).getValue();
			// USER DATA
			/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
			
			params.params = JSON.stringify(params.params);
			addInUpdateQueue(params,prop_tb._manufacturerSeoGrid);
		}
		return true;
	}
	// CALLBACK FUNCTION
	function callbackManufacturerSeo(sid,action,tid)
	{
		if (action=='update') {
			prop_tb._manufacturerSeoGrid.setRowTextNormal(sid);
			displayManufacturerSeo();
			displayManufacturers();
		}
	}
	
	function setPropertiesPanel_ManufacturerSeo(id){
		if (id=='manufacturerseo')
		{
			if(last_manufacturerID!=undefined && last_manufacturerID!="")
			{
				idxProductName=man_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+man_grid.cells(last_manufacturerID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.setItemText('panel', '<?php echo _l('SEO',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/description.png');
			needInitManufacturerSeo = 1;
			initManufacturerSeo();
			propertiesPanel='manufacturerseo';
			if (last_manufacturerID!=0)
			{
				displayManufacturerSeo();
				displayGoogleAdwords();
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_ManufacturerSeo);

	function displayManufacturerSeo()
	{
		prop_tb._manufacturerSeoGrid.clearAll(true);
		var tempIdList = (man_grid.getSelectedRowId()!=null?man_grid.getSelectedRowId():"");
		$.post("index.php?ajax=1&act=man_seo_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
		{
			prop_tb._manufacturerSeoGrid.parse(data);
			nb=prop_tb._manufacturerSeoGrid.getRowsNum();
			prop_tb._manufacturerSeoGrid._rowsNum=nb;
			
   			// UISettings
			loadGridUISettings(prop_tb._manufacturerSeoGrid);
			prop_tb._manufacturerSeoGrid._first_loading=0;
		});
	}

	function displayGoogleAdwords()
	{
		prop_tb._googleAdwords.setHeight(150);
		prop_tb._googleAdwords.attachURL("index.php?ajax=1&act=man_seo_add_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	}



	man_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='manufacturerseo'){
				//initManufacturerSeo();
				displayManufacturerSeo();
				displayGoogleAdwords();
			}
		});
