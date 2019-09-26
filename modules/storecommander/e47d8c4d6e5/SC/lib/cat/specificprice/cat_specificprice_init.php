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
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
?>

	
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE")) { ?>
		prop_tb.addListOption('panel', 'specificprices', 6, "button", '<?php echo _l('Specific prices',1)?>', "lib/img/text_list_numbers.png");
		allowed_properties_panel[allowed_properties_panel.length] = "specificprices";
	<?php } ?>


	prop_tb.addButton('specificprice_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
	prop_tb.setItemToolTip('specificprice_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButtonTwoState('specificprice_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
	prop_tb.setItemToolTip('specificprice_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	prop_tb.addButton("specificprice_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	prop_tb.setItemToolTip('specificprice_selectall','<?php echo _l('Select all',1)?>');
	prop_tb.addButton('specificprice_add',100,'','lib/img/add.png','lib/img/add.png');
	prop_tb.setItemToolTip('specificprice_add','<?php echo _l('Create new specific price',1)?>');
	prop_tb.addButton('specificprice_del',100,'','lib/img/delete.gif','lib/img/delete.gif');
	prop_tb.setItemToolTip('specificprice_del','<?php echo _l('Delete selected item',1)?>');
	prop_tb.addButton("specificprice_export_grid", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	prop_tb.setItemToolTip('specificprice_export_grid','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.', 1)?>');
	

	clipboardType_Specificprices = null;	
	needInitSpecificPrices = 1;
	customername = null;
	function initSpecificPrices(){
		if (needInitSpecificPrices)
		{
			prop_tb._specificpricesLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._specificpricesLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._specificpricesGrid = prop_tb._specificpricesLayout.cells('a').attachGrid();
			prop_tb._specificpricesGrid.setImagePath("lib/js/imgs/");
			prop_tb._specificpricesGrid.enableMultiselect(true);
			prop_tb._specificpricesGrid.setDateFormat("%Y-%m-%d %H:%i:%s","%Y-%m-%d %H:%i:%s");
			/*prop_tb._specificpricesGrid.enableAutoSaving('cg_cat_spepri',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
			prop_tb._specificpricesGrid.enableAutoHiddenColumnsSaving('cg_cat_spepri_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/
			
			// UISettings
			prop_tb._specificpricesGrid._uisettings_prefix='cat_specificprice';
			prop_tb._specificpricesGrid._uisettings_name=prop_tb._specificpricesGrid._uisettings_prefix;
		   	prop_tb._specificpricesGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._specificpricesGrid);
			prop_tb._specificpricesGrid.enableColumnMove(false);
			
			function onEditCellSpecificpricesGrid(stage, rId, cIn,nValue,oValue){
				var checkTypeOfRule = prop_tb._specificpricesGrid.getUserData(rId,'id_specific_price_rule');
				if (checkTypeOfRule > 0){
					return false;
				}

				<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
					var is_combination = prop_tb._specificpricesGrid.getUserData(rId,"is_combination");
					if (is_combination=="1")
						return false;
				<?php } ?>
						
				if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select(); 
				
				
				<?php sc_ext::readCustomPropSpePriceGridConfigXML('onEditCell'); ?>
				if (nValue!=oValue)
				{
					if(stage==2)
					{
					<?php sc_ext::readCustomPropSpePriceGridConfigXML('onBeforeUpdate'); ?>
						var params = {
							name: "cat_specificprice_update",
							row: rId,
							action: "update",
							params: {},
							callback: "callbackSpecificPrice('"+rId+"','update','"+rId+"');"
						};
						// CHECK ID_CUSTOMER
						if (prop_tb._specificpricesGrid.getColumnId(cIn) == "id_customer") {
							var cellValue = prop_tb._specificpricesGrid.cells(rId,cIn).getValue();
							var cellValueInt = parseInt(cellValue);
							if (!Number.isInteger(cellValueInt) && cellValue != 0) {
								dhtmlx.message({text:'<?php echo _l('This customer in unknown'); ?>',type:'error',expire:3000});
								return false;
							}
						}

						// COLUMN VALUES
						params.params[prop_tb._specificpricesGrid.getColumnId(cIn)] = prop_tb._specificpricesGrid.cells(rId,cIn).getValue();
						// USER DATA
						/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
						
						params.params = JSON.stringify(params.params);
						addInUpdateQueue(params,prop_tb._specificpricesGrid);

						if (customername) {
							prop_tb._specificpricesGrid.cells(rId,cIn).setValue(customername);
							customername = null;
						}
					}
				}
				
				return true;
			}
			prop_tb._specificpricesGrid.attachEvent("onEditCell", onEditCellSpecificpricesGrid);
			
			/*specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_specificprice_update&id_product="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG;
			specificpricesDataProcessor = new dataProcessor(specificpricesDataProcessorURLBase);
			specificpricesDataProcessor.enableDataNames(true);
			specificpricesDataProcessor.enablePartialDataSend(true);
			specificpricesDataProcessor.setTransactionMode("POST");
			specificpricesDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
					if (action=='insert')
					{
						specificpricesDataProcessor.enablePartialDataSend(true);
						prop_tb._specificpricesGrid.cells(tid,0).setValue(tid);
					}
				});
			specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_specificprice_update&id_product="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG;
			specificpricesDataProcessor.serverProcessor=specificpricesDataProcessorURLBase;
			specificpricesDataProcessor.init(prop_tb._specificpricesGrid);*/
			needInitSpecificPrices=0;
			
			prop_tb._specificpricesGrid.attachEvent("onDhxCalendarCreated",function(calendar){
				calendar.setSensitiveRange("2012-01-01",null);
			});
	
			/* COMMENTED AT 07/11/14
			prop_tb._specificpricesGrid.attachEvent("onBeforeSorting", function(ind,type,direction){
				idxReductionFrom=prop_tb._specificpricesGrid.getColIndexById('from');
				idxReductionTo=prop_tb._specificpricesGrid.getColIndexById('to');
				if(ind==idxReductionFrom || ind==idxReductionTo)
					prop_tb._specificpricesGrid.setColumnExcellType(ind,"ed");
			    return true;
			});
			prop_tb._specificpricesGrid.attachEvent("onAfterSorting", function(ind,type,direction){
				idxReductionFrom=prop_tb._specificpricesGrid.getColIndexById('from');
				idxReductionTo=prop_tb._specificpricesGrid.getColIndexById('to');
				if(ind==idxReductionFrom || ind==idxReductionTo)
					prop_tb._specificpricesGrid.setColumnExcellType(ind,"dhxCalendarA");
			    return true;
			});*/
			
			// Context menu for grid
			specificprices_cmenu=new dhtmlXMenuObject();
			specificprices_cmenu.renderAsContextMenu();
			function onGridSpecificpricesContextButtonClick(itemId){
				tabId=prop_tb._specificpricesGrid.contextID.split('_');
				tabId=tabId[0];
				if (itemId=="copy"){
					if (lastColumnRightClicked_Specificprices!=0)
					{
						clipboardValue_Specificprices=prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
						if(lastColumnRightClicked_Specificprices == prop_tb._specificpricesGrid.getColIndexById('id_customer')) {
							var mask = prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
							$.post('index.php?ajax=1&act=cat_specificprice_customer_get&ajaxCall=1&getIdCus=1',{'mask':mask},function(data)			{
								var res = JSON.parse(data);
								clipboardValue_Specificprices=parseInt(res.id_customer);
								customername = res.name;
							});
						} else {
							clipboardValue_Specificprices=prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getValue();
						}
						specificprices_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+prop_tb._specificpricesGrid.cells(tabId,lastColumnRightClicked_Specificprices).getTitle());
						clipboardType_Specificprices=lastColumnRightClicked_Specificprices;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_Specificprices!=0 && clipboardValue_Specificprices!=null && clipboardType_Specificprices==lastColumnRightClicked_Specificprices)
					{
						selection=prop_tb._specificpricesGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								if (prop_tb._specificpricesGrid.getColumnId(lastColumnRightClicked_Specificprices).substr(0,5)!='attr_')
								{
									<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
									var is_combination = prop_tb._specificpricesGrid.getUserData(selArray[i],"is_combination");
									if (is_combination=="0")
									{
									<?php } ?>
									prop_tb._specificpricesGrid.cells(selArray[i],lastColumnRightClicked_Specificprices).setValue(clipboardValue_Specificprices);
									onEditCellSpecificpricesGrid(2,selArray[i],lastColumnRightClicked_Specificprices,clipboardValue_Specificprices,null);
									
									//prop_tb._specificpricesGrid.cells(selArray[i],lastColumnRightClicked_Specificprices).cell.wasChanged=true;
									//specificpricesDataProcessor.setUpdated(selArray[i],true,"updated");
									<?php if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
									}
									<?php } ?>
								}
							}
						}
					}
				}
			}
			specificprices_cmenu.attachEvent("onClick", onGridSpecificpricesContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			specificprices_cmenu.loadStruct(contextMenuXML);
			prop_tb._specificpricesGrid.enableContextMenu(specificprices_cmenu);

			prop_tb._specificpricesGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var checkTypeOfRule = prop_tb._specificpricesGrid.getUserData(rowid,'id_specific_price_rule');
				if (checkTypeOfRule > 0) {
					var disableOnCols=new Array(
						prop_tb._specificpricesGrid.getColIndexById('id_product'),
						prop_tb._specificpricesGrid.getColIndexById('id_specific_price'),
						prop_tb._specificpricesGrid.getColIndexById('id_product_attribute'),
						prop_tb._specificpricesGrid.getColIndexById('reference'),
						prop_tb._specificpricesGrid.getColIndexById('name'),
						prop_tb._specificpricesGrid.getColIndexById('id_shop'),
						prop_tb._specificpricesGrid.getColIndexById('id_shop_group'),
						prop_tb._specificpricesGrid.getColIndexById('id_group'),
						prop_tb._specificpricesGrid.getColIndexById('from_quantity'),
						prop_tb._specificpricesGrid.getColIndexById('price'),
						prop_tb._specificpricesGrid.getColIndexById('reduction'),
						prop_tb._specificpricesGrid.getColIndexById('reduction_tax'),
						prop_tb._specificpricesGrid.getColIndexById('from'),
						prop_tb._specificpricesGrid.getColIndexById('to'),
						prop_tb._specificpricesGrid.getColIndexById('id_country'),
						prop_tb._specificpricesGrid.getColIndexById('id_currency'),
						prop_tb._specificpricesGrid.getColIndexById('image'),
						prop_tb._specificpricesGrid.getColIndexById('supplier_reference'),
						prop_tb._specificpricesGrid.getColIndexById('ean13'),
						prop_tb._specificpricesGrid.getColIndexById('upc'),
						prop_tb._specificpricesGrid.getColIndexById('active'),
						prop_tb._specificpricesGrid.getColIndexById('price_exl_tax'),
						prop_tb._specificpricesGrid.getColIndexById('price_inc_tax'),
						prop_tb._specificpricesGrid.getColIndexById('id_manufacturer'),
						prop_tb._specificpricesGrid.getColIndexById('id_supplier'),
						prop_tb._specificpricesGrid.getColIndexById('id_specific_price_rule')
					);
				} else {
					var disableOnCols=new Array(
						prop_tb._specificpricesGrid.getColIndexById('id_product'),
						prop_tb._specificpricesGrid.getColIndexById('id_specific_price'),
						prop_tb._specificpricesGrid.getColIndexById('id_specific_price_rule')
					);
				}
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_Specificprices=colidx;
				specificprices_cmenu.setItemText('object', '<?php echo _l('Specific price:')?> '+prop_tb._specificpricesGrid.cells(rowid,prop_tb._specificpricesGrid.getColIndexById('id_specific_price')).getTitle());
				if (lastColumnRightClicked_Specificprices==clipboardType_Specificprices)
				{
					specificprices_cmenu.setItemEnabled('paste');
				}else{
					specificprices_cmenu.setItemDisabled('paste');
				}
				return true;
			});
		}
	}




	function setPropertiesPanel_discounts(id){
		if (id=='specificprices')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('specificprice_del');
			prop_tb.showItem('specificprice_add');
			prop_tb.showItem('specificprice_refresh');
			prop_tb.showItem('specificprice_lightNavigation');
			prop_tb.showItem('specificprice_selectall');
			prop_tb.showItem('specificprice_export_grid');
			prop_tb.setItemText('panel', '<?php echo _l('Specific prices',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/text_list_numbers.png');
			needInitSpecificPrices = 1;
			initSpecificPrices();
			propertiesPanel='specificprices';
			if (lastProductSelID!=0)
				displaySpecificPrices();
		}
		if (id=='specificprice_refresh')
		{
			if (lastProductSelID!=0)
				displaySpecificPrices();
		}
		if (id=='specificprice_selectall')
		{
			prop_tb._specificpricesGrid.selectAll();
		}
		if (id=='specificprice_add')
		{
			if (lastProductSelID==0){
				alert('<?php echo _l('Please select a product',1)?>');
			}else{
				var newId = new Date().getTime();
				/*specificpricesDataProcessorURLBase="index.php?ajax=1&act=cat_specificprice_update&id_lang="+SC_ID_LANG;
				specificpricesDataProcessor.serverProcessor=specificpricesDataProcessorURLBase;
				specificpricesDataProcessor.enablePartialDataSend(false);*/
				var maxQuantity=1;
				var maxValue=10;
				var percent='';
				
				
				// INSERT
					<?php 
					$sourceGridFormat=SCI::getGridViews("propspeprice");
					$sql_gridFormat = $sourceGridFormat;
					sc_ext::readCustomPropSpePriceGridConfigXML('gridConfig');
					$gridFormat=$sourceGridFormat;
					$cols=explode(',',$gridFormat);
					
					$insert = '';
					foreach($cols as $col)
					{
						$default = "''";
						if($col=="id_specific_price")
							$default = 'newId';
						elseif($col=="id_product")
							$default = 'cat_grid.getSelectedRowId()';
						elseif($col=="id_product_attribute")
							$default = "0";
						elseif($col=="id_shop")
							$default = "0";
						elseif($col=="id_shop_group")
							$default = "0";
						elseif($col=="id_group")
							$default = "0";
						elseif($col=="from_quantity")
							$default = "1";
						elseif($col=="price")
							$default = (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?-1:0);
						elseif($col=="reduction_tax")
							$default = (version_compare(_PS_VERSION_, '1.6.0.11', '>=')?"'"._s('CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX')."'":"''");
						elseif($col=="id_country")
							$default = "0";
						elseif($col=="id_currency")
							$default = "0";
						
						if(!empty($insert))
							$insert .= ",";
						$insert .= $default;
					}
					?>
					newRow=new Array(<?php echo $insert; ?>);
					prop_tb._specificpricesGrid.addRow(newId,newRow);
					prop_tb._specificpricesGrid.setRowHidden(newId, true);
				
					var params = {
						name: "cat_specificprice_update",
						row: newId,
						action: "insert",
						params: {callback: "callbackSpecificPrice('"+newId+"','insert','{newid}');"}
					};
					// COLUMN VALUES
					prop_tb._specificpricesGrid.forEachCell(newId,function(cellObj,ind){
						params.params[prop_tb._specificpricesGrid.getColumnId(ind)] = prop_tb._specificpricesGrid.cells(newId,ind).getValue();
					});
					// USER DATA

					sendInsert(params,prop_tb._specificpricesLayout.cells('a'));

			}
		}
		if (id=='specificprice_del')
		{
			if (prop_tb._specificpricesGrid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					{
						//prop_tb._specificpricesGrid.deleteSelectedRows();
						selection=prop_tb._specificpricesGrid.getSelectedRowId();
						/*$.post('index.php?ajax=1&act=cat_specificprice_del',{'rowslist':selection},function(data){
								if (selection!='' && selection!=null)
								{
									displaySpecificPrices();
								}
							});*/
							
						ids=selection.split(',');
						$.each(ids, function(num, rId){
							var params = {
								name: "cat_specificprice_update",
								row: rId,
								action: "delete",
								params: {},
								callback: "callbackSpecificPrice('"+rId+"','delete','"+rId+"');"
							};					
							params.params = JSON.stringify(params.params);
							addInUpdateQueue(params,prop_tb._specificpricesGrid);
						});
					}
				}else{
					alert('<?php echo _l('Please select a product',1)?>');
				}
			}
		}
		if (id=='specificprice_export_grid')
		{
			prop_tb._specificpricesGrid.enableCSVHeader(true);
			prop_tb._specificpricesGrid.setCSVDelimiter("\t");
			var csv=prop_tb._specificpricesGrid.serializeToCSV(true);
			displayQuickExportWindow(csv, 1);
			//window.clipboardData.setData('Text',csv);
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_discounts);

	prop_tb.attachEvent("onStateChange",function(id,state){
		if (id=='specificprice_lightNavigation')
		{
			if (state)
			{
				prop_tb._specificpricesGrid.enableLightMouseNavigation(true);
			}else{
				prop_tb._specificpricesGrid.enableLightMouseNavigation(false);
			}
		}
	});	

	
	function displaySpecificPrices()
	{
		prop_tb._specificpricesGrid.clearAll(true);
		$.post("index.php?ajax=1&act=cat_specificprice_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': cat_grid.getSelectedRowId()},function(data)
		{
			prop_tb._specificpricesGrid.parse(data);
			nb=prop_tb._specificpricesGrid.getRowsNum();
			prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('specific prices')?>":" <?php echo _l('specific price')?>"));
			
   		// UISettings
			loadGridUISettings(prop_tb._specificpricesGrid);
			prop_tb._specificpricesGrid._first_loading=0;
			
			 <?php sc_ext::readCustomPropSpePriceGridConfigXML('afterGetRows'); ?>
		});
	}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='specificprices'){
				//initSpecificPrices();
				displaySpecificPrices();
			}
		});
		
	// CALLBACK FUNCTION
	function callbackSpecificPrice(sid,action,tid)
	{
		<?php sc_ext::readCustomPropSpePriceGridConfigXML('onAfterUpdate'); ?>
		if (action=='insert')
		{
			idxSpeID=prop_tb._specificpricesGrid.getColIndexById('id_specific_price');
			prop_tb._specificpricesGrid.cells(sid,idxSpeID).setValue(tid);
			prop_tb._specificpricesGrid.changeRowId(sid,tid);
			prop_tb._specificpricesGrid.setRowHidden(tid, false);
			prop_tb._specificpricesGrid.showRow(tid);
			prop_tb._specificpricesLayout.cells('a').progressOff();
		}
		else if (action=='update')
			prop_tb._specificpricesGrid.setRowTextNormal(sid);
		else if(action=='delete')
			prop_tb._specificpricesGrid.deleteRow(sid);
	}

<?php
	}
