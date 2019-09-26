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

$defaultLanguageId = intval(Configuration::get('PS_LANG_DEFAULT'))
?>
<script type="text/javascript">
// INSTALLATION DE LA VIEW
<?php if(_s('CAT_PROD_IMPORT_METHOD')) { ?>
if (!dhxWins.isWindow("wCatImportCreateView"))
{
	wCatImportCreateView = dhxWins.createWindow("wCatImportCreateView", 50, 50, 300, 250);
	wCatImportCreateView.setIcon('lib/img/cog_go.png','../../../lib/img/cog_go.png');
	wCatImportCreateView.setText('<?php echo _l('Preparing import',1)?>');
	wCatImportCreateView.attachURL("index.php?ajax=1&act=cat_win-import_create_view&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	wCatImportCreateView.attachURL("index.php?ajax=1&act=cat_win-import_create_view&etape=2&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	wCatImportCreateView.setModal(true);
}else{
	wCatImportCreateView.attachURL("index.php?ajax=1&act=cat_win-import_create_view&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	wCatImportCreateView.attachURL("index.php?ajax=1&act=cat_win-import_create_view&etape=2&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
	wCatImportCreateView.show();
	wCatImportCreateView.setModal(true);
}
<?php } ?>

// IMPORT
	lastCSVFile='';
	mapping='';
	arrayFieldLang=Array('name','description','description_short','meta_title','meta_description','meta_keywords','link_rewrite','available_now','available_later','tags','customization_field_name','image_legend'<?php echo sc_ext::readImportCSVConfigXML('definitionForLangField');?>);
	arrayFieldOption=Array('feature','feature_custom','attribute','attribute_multiple'<?php if(SCAS) echo ",'quantity','location','add_quantity','remove_quantity','quantity_on_sale'"; if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) echo ",'supplier_reference','wholesale_price','supplier_currency'" ?>);
	var comboArray = null;
	var comboValuesArray = null;
	var optionLabelArray = null;
	dhxlImport=wImport.attachLayout("3T");
	wImport._sb=dhxlImport.attachStatusBar();
	dhxlImport.cells('a').hideHeader();
	dhxlImport.cells('a').setHeight(200);
	wImport.tbOptions=dhxlImport.cells('a').attachToolbar();
	wImport.tbOptions.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	wImport.tbOptions.setItemToolTip('help','<?php echo _l('Help',1)?>');
	<?php if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
	wImport.tbOptions.addButton('import_fouleFactory', 0, "", "lib/img/foulefactory_icon.png", "lib/img/foulefactory_icon.png");
	wImport.tbOptions.setItemToolTip('import_fouleFactory','<?php echo _l('Enhance your product pages in 3 minutes with FouleFactory',1)?>');
	<?php } ?>
	wImport.tbOptions.addButton("readEditCsv", 0, "", "lib/img/table_edit.png", "lib/img/table_edit.png");
	wImport.tbOptions.setItemToolTip('readEditCsv','<?php echo _l('Read and edit rows from csv file.',1)?>');
	wImport.tbOptions.addButton("download", 0, "", "lib/img/table_go.png", "lib/img/table_go.png");
	wImport.tbOptions.setItemToolTip('download','<?php echo _l('Download selected file',1)?>');
	wImport.tbOptions.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wImport.tbOptions.setItemToolTip('delete','<?php echo _l('Delete marked files',1)?>');
	wImport.tbOptions.addButton("upload", 0, "", "lib/img/table_add.png", "lib/img/table_add.png");
	wImport.tbOptions.setItemToolTip('upload','<?php echo _l('Upload CSV file',1)?>');
	wImport.tbOptions.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wImport.tbOptions.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	wImport.tbOptions.attachEvent("onClick",
		function(id){
			if (id=='help')
			{
				<?php echo "window.open('".getHelpLink('csvimport')."');"; ?>
			}
			if (id=='refresh')
			{
				displayOptions();
			}
			if(id=='import_fouleFactory')
			{
				showWCatFoulefactory();
			}
			if (id=='download')
			{
				idxFilename=wImport.gridFiles.getColIndexById('filename');
				window.open("index.php?ajax=1&act=all_get-file&path=<?php echo (SC_INSTALL_MODE==0?SC_PS_PATH_ADMIN_REL.'import/':SC_CSV_IMPORT_DIR);?>&file="+wImport.gridFiles.cells(wImport.gridFiles.getSelectedRowId(),idxFilename).getValue());
			}
			if (id=='delete')
			{
				idxMarkedFile=wImport.gridFiles.getColIndexById('markedfile');
				filesList='';
				wImport.gridFiles.forEachRow(function(id){
					if (wImport.gridFiles.cells(id,idxMarkedFile).getValue()==true)
					{
						idxFilename=wImport.gridFiles.getColIndexById('filename');
						filesList+=wImport.gridFiles.cells(id,idxFilename).getValue()+';';
					}
					});
				$.post('index.php?ajax=1&act=cat_win-import_process&action=conf_delete',{'imp_opt_files':filesList},function(data){
						dhtmlx.message({text:data,type:'info'});
						displayOptions();
					});
			}
			if (id=='upload')
			{
				if (!dhxWins.isWindow("wImportUpload"))
				{
//					wImport._uploadWindow = dhxWins.createWindow("wImportUpload", 50, 50, 396, 568);
					wImport._uploadWindow = dhxWins.createWindow("wImportUpload", 50, 50, 585, 400);
					wImport._uploadWindow.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
					wImport._uploadWindow.setText('<?php echo _l('Upload CSV files',1)?>');
					ll = new dhtmlXLayoutObject(wImport._uploadWindow, "1C");
					ll.cells('a').hideHeader();
					ll.cells('a').attachURL('index.php?ajax=1&act=cat_win-import_upload'+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					wImport._uploadWindow.attachEvent("onClose", function(win){
							win.hide();
							return false;
						});
				}else{
					ll.cells('a').attachURL('index.php?ajax=1&act=cat_win-import_upload'+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					wImport._uploadWindow.show();
					wImport._uploadWindow.bringToTop();
				}
			}
			if(id=='readEditCsv')
			{
				if(typeof idxFilename != 'undefined' && idxFilename != ''){
					var filename = wImport.gridFiles.cells(wImport.gridFiles.getSelectedRowId(),idxFilename).getValue();
					var fileSize = wImport.gridFiles.getUserData(wImport.gridFiles.getSelectedRowId(),"real_size");
					var fieldSep = wImport.gridFiles.cells(wImport.gridFiles.getSelectedRowId(),idxFieldsep).getValue();
					var forceUTF8 = wImport.gridFiles.cells(wImport.gridFiles.getSelectedRowId(),idxForceUTF8).getValue();
					var nbRowEStart = 0;
					var nbRowEnd = 20;
					var stringAfterSAved = '<div id="export_contener" style="height: 100%; overflow: auto;"><img src="lib/img/loading.gif" alt="loading" title="loading" style="height: 100%;width: auto;display: block;" /><div id="export_message" style="padding-left: 10px;font-family: Tahoma; font-size: 11px !important; line-height: 18px;"></div></div>';

					if(fileSize === "0")
					{
						dhtmlx.message({text:"<?php echo _l('File is empty'); ?>",type:'error'});
						return false;
					}
					<?php
						if (version_compare(_PS_VERSION_, '1.4.0.8', '>=')) {
							$domain = Tools::getShopDomain();
						} else {
							$domain = Tools::getHttpHost();
						}
						$url = (SC_INSTALL_MODE==0?SC_PS_PATH_ADMIN_REL.'import/':SC_CSV_IMPORT_DIR);
						if($domain == '127.0.0.1' || $domain == 'localhost'){
							$url = str_replace("\\",'/',$url);
						}
					?>
					var url = "<?php echo $url; ?>"+filename;
					wImport._editorWindow = dhxWins.createWindow("wImportEditor", 50, 50, 1300, 650);
					wImport._editorWindow.setIcon('lib/img/table_edit.png','../../../lib/img/table_edit.png');
					wImport._editorWindow.setText('<?php echo _l('Edit rows of',1)?> '+filename);
					wImport._editorWindow.show();
					wImport._editorWindow.bringToTop();

					ll = new dhtmlXLayoutObject(wImport._editorWindow, "3U");


					wImport.leftPanel = ll.cells('a');
					wImport.leftPanel.setText('<?php echo _l('Raw content',1)?>');
					wImport.leftPanel.setHeight(500);
					wImport.winLeftToolbar=wImport.leftPanel.attachToolbar();
					wImport.winLeftToolbar.addButton("saveLeftRows", 0, "", "lib/img/page_save.png", "lib/img/page_save.png");
					wImport.winLeftToolbar.setItemToolTip('saveLeftRows','<?php echo _l('Save change',1)?>');
					wImport.leftPanel.attachHTMLString('<textarea id="rawContent" style="-webkit-box-sizing: border-box;-moz-box-sizing: border-box;box-sizing: border-box;width:100%;height:100%"></textarea>');
					$.post("index.php?ajax=1&act=cat_win-import_editor_get&"+new Date().getTime(),{
						url:url,
						type:"raw",
						utf8:forceUTF8,
						nbrowstart:nbRowEStart,
						nbrowend:nbRowEnd,
						fieldsep:fieldSep
					},function(data){
						$('textarea#rawContent').text(data);
					});
					wImport.winLeftToolbar.attachEvent("onClick",
						function(id) {
							if(id=='saveLeftRows')
							{
								var csv = $('textarea#rawContent').val();
								wImport.bottomPanel.attachHTMLString(stringAfterSAved);
								setTimeout(function() {
									wImport.bottomPanel.attachURL("index.php?ajax=1&act=cat_win-import_editor_update&" + new Date().getTime(), true, {
										save: 1,
										type:"raw",
										utf8:forceUTF8,
										nbrowstart:nbRowEStart,
										nbrowend:nbRowEnd,
										data: csv,
										url: url,
										fieldsep:fieldSep
									});
								}, 1000);
							}
						}
					);


					wImport.righPanel = ll.cells('b');
					wImport.righPanel.setText('<?php echo _l('CSV content',1)?>');
					wImport.righPanel.setHeight(500);
					wImport.winRightToolbar=wImport.righPanel.attachToolbar();
					wImport.winRightToolbar.addButton("saveRightRows", 0, "", "lib/img/page_save.png", "lib/img/page_save.png");
					wImport.winRightToolbar.setItemToolTip('saveRightRows','<?php echo _l('Save change',1)?>');


					wImport.bottomPanel = ll.cells('c');
					wImport.bottomPanel.setText('<?php echo _l('Result',1)?>');
					wImport.bottomPanel.showHeader();
					wImport._editorGrid = wImport.righPanel.attachGrid();
					var postData = "url="+url+"&fieldsep="+fieldSep+"&utf8="+forceUTF8+"&nbrowstart="+nbRowEStart+"&nbrowend="+nbRowEnd;
					wImport._editorGrid.post("index.php?ajax=1&act=cat_win-import_editor_get&"+new Date().getTime(),postData,function(){},"xml");
					wImport.winRightToolbar.attachEvent("onClick",
						function(id) {
							if(id=='saveRightRows')
							{
								wImport._editorGrid.enableCSVHeader(true);
								wImport._editorGrid.setCSVDelimiter(";");
								var csv = wImport._editorGrid.serializeToCSV()+"\n";
								wImport.bottomPanel.attachHTMLString(stringAfterSAved);
								setTimeout(function() {
									wImport.bottomPanel.attachURL("index.php?ajax=1&act=cat_win-import_editor_update&" + new Date().getTime(), true, {
										save: 1,
										type:"grid",
										utf8:forceUTF8,
										nbrowstart:nbRowEStart,
										nbrowend:nbRowEnd,
										data: csv,
										url: url,
										fieldsep:fieldSep
									});
								}, 1000);
							}
						}
					);
				} else {
					dhtmlx.message({text:"<?php echo _l('You should mark at least one file to edit'); ?>",type:'info'});
				}
			}
		});
		
	wImport.gridFiles=dhxlImport.cells('a').attachGrid();
	wImport.gridFiles.setImagePath("lib/js/imgs/");
	function sort_dateFR(a,b,order){
    var a_array=a.split('/');
    var b_array=b.split('/');
    var new_a=a_array[2]*10000+a_array[1]*100+a_array[0];
    var new_b=b_array[2]*10000+b_array[1]*100+b_array[0];
		if(order=="asc")
			return new_a>new_b?1:-1;
		else
			return new_a<new_b?1:-1;
  }
	wImport.gridFiles.attachEvent("onRowSelect", function(id,ind){
			if (id!=lastCSVFile)
			{
				idxFilename=wImport.gridFiles.getColIndexById('filename');
				idxFileSize=wImport.gridFiles.getColIndexById('size');
				idxMapping=wImport.gridFiles.getColIndexById('mapping');
				idxLimit=wImport.gridFiles.getColIndexById('importlimit');
				idxFieldsep=wImport.gridFiles.getColIndexById('fieldsep');
				idxForceUTF8=wImport.gridFiles.getColIndexById('utf8');
				idxCreateCategories=wImport.gridFiles.getColIndexById('createcategories');
				wImport.tbProcess.setItemState('create_categories',wImport.gridFiles.cells(id,idxCreateCategories).getValue());
				wImport.tbProcess.setValue('importlimit',wImport.gridFiles.cells(id,idxLimit).getValue());
				filename=wImport.gridFiles.cells(id,idxFilename).getValue();
				mapping=wImport.gridFiles.cells(id,idxMapping).getValue();
				dhxlImport.cells('b').setText("<?php echo _l('Mapping')?> "+filename);
				displayMapping(filename,mapping);
				lastCSVFile=id;
			}
		});
	wImport.gridFiles.attachEvent('onEditCell',function (stage,rId,cInd,nValue,oValue){
			idxfieldsep=wImport.gridFiles.getColIndexById('fieldsep');
			idxvaluesep=wImport.gridFiles.getColIndexById('valuesep');
			if (stage==2 && (cInd==idxfieldsep || cInd==idxvaluesep)){
				idxFilename=wImport.gridFiles.getColIndexById('filename');
				idxMapping=wImport.gridFiles.getColIndexById('mapping');
				filename=wImport.gridFiles.cells(rId,idxFilename).getValue();
				mapping=wImport.gridFiles.cells(rId,idxMapping).getValue();
				setTimeout("displayMapping('"+filename+"','"+mapping+"')",500);
			}
			return true;
		});
	wImport.gridFilesDataProcessor = new dataProcessor('index.php?ajax=1&act=cat_win-import_config_update');
	wImport.gridFilesDataProcessor.enableDataNames(true);
	wImport.gridFilesDataProcessor.enablePartialDataSend(true);
	wImport.gridFilesDataProcessor.setUpdateMode('cell',true);
	wImport.gridFilesDataProcessor.setDataColumns(Array(false,false,false,true,true,true,true,true,true,true,true,true,true,true,true,true,false));
<?php
	if (_s('CAT_NOTICE_EXPORT_SEPARATOR'))
	{
?>
	wImport.gridFilesDataProcessor.attachEvent("onBeforeUpdate",function(id,status){
			if (wImport.gridFiles.cells(id,6).getValue()==wImport.gridFiles.cells(id,7).getValue())
			{
				dhtmlx.message({text:'<?php echo _l('The field separator and the value separator could not be the same character.')?>',type:'error'});
				return false;
			}
			return true;
		});
<?php
	}
?>
	wImport.gridFilesDataProcessor.attachEvent("onAfterUpdate",function(id,status){
		getCheck();
		return true;
	});
	wImport.gridFilesDataProcessor.init(wImport.gridFiles);

	displayOptions();//'wImport.gridFiles.splitAt(2);');

	dhxlImport.cells('b').setText("<?php echo _l('Mapping')?>");
	dhxlImport.cells('b').setWidth(600);
	wImport.tbMapping=dhxlImport.cells('b').attachToolbar();
	wImport.tbMapping.addButton("load_by_name", 0, "", "lib/img/table_lightning.png", "lib/img/table_lightning.png");
	wImport.tbMapping.setItemToolTip('load_by_name','<?php echo _l('Load fields by name',1)?>');
	wImport.tbMapping.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wImport.tbMapping.setItemToolTip('delete','<?php echo _l('Delete mapping and reset grid')?>');
	wImport.tbMapping.addButton("saveasbtn", 0, "", "lib/img/table_save.png", "lib/img/table_save.png");
	wImport.tbMapping.setItemToolTip('saveasbtn','<?php echo _l('Save mapping')?>');
	wImport.tbMapping.addInput("saveas", 0,"",200);
	wImport.tbMapping.setItemToolTip('saveas','<?php echo _l('Save mapping as')?>');
	wImport.tbMapping.addText('txt_saveas', 0, '<?php echo _l('Save mapping as')?>');
	var opts = [
<?php
	@$files = array_diff( scandir( SC_CSV_IMPORT_DIR ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
	$content='';
	foreach ($files AS $file)
	{
		if (substr($file,strlen($file)-8,8)=='.map.xml')
		{
			$file=str_replace('.map.xml','',$file);
			$content.="['loadmapping".$file."', 'obj', '".$file."', ''],";
		}
	}
	if ($content=='') echo "['0', 'obj', '"._l('No map available')."', ''],";
	echo substr($content,0,-1);
?>
							];
	wImport.tbMapping.addButtonSelect("loadmapping", 0, "<?php echo _l('Load')?>", opts, "lib/img/table_relationship.png", "lib/img/table_relationship.png",false,true);
	wImport.tbMapping.setItemToolTip('loadmapping','<?php echo _l('Load mapping')?>');
	wImport.tbMapping.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wImport.tbMapping.setItemToolTip('refresh','<?php echo _l('Refresh')?>');
	function onClickMapping(id){
			if (id.substr(0,11)=='loadmapping')
			{
				tmp=id.substr(11,id.length).replace('.map.xml','');
				wImport.tbMapping.setValue('saveas',tmp);
				$.get('index.php?ajax=1&act=cat_win-import_process&action=mapping_load&filename='+tmp,function(data){
						if (data!='')
						{
							mapping=data.split(';');
							wImport.gridMapping.forEachRow(function(id){
									wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),0).setValue("0");
									wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),2).setValue("");
									wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),3).setValue("");
								
									if (wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),1).getValue()!='')
										for(var i=0; i < mapping.length; i++)
										{
											map=(mapping[i]).split(',');
											if (wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),1).getValue()==map[0])
											{
												wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),0).setValue("1");
												wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),2).setValue(map[1]);
												<?php if(SCAS) { ?>
												if(map[1]=='quantity' || map[1]=='location' || map[1]=='add_quantity' || map[1]=='remove_quantity' || map[1]=='quantity_on_sale')
												{
													idxOptions=wImport.gridMapping.getColIndexById('options');
													comboDBField = wImport.gridMapping.getCombo(idxOptions);
													comboDBField.clear();
													comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
													<?php
														$warehouses=Warehouse::getWarehouses(true);
														foreach($warehouses AS $warehouse)
														{ 
															echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes($warehouse['name']).'");';
														}
													?>
												}
												<?php } ?>
												<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
												if(map[1]=='supplier_reference')
												{
													idxOptions=wImport.gridMapping.getColIndexById('options');
													comboDBField = wImport.gridMapping.getCombo(idxOptions);
													comboDBField.clear();
													comboDBField.put("suppref_product","<?php echo _l('Default values display products/combinations grids',1)?>");
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("suppref_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['name']).'");';
														}
													?>
												}
												if(map[1]=='wholesale_price')
												{
													idxOptions=wImport.gridMapping.getColIndexById('options');
													comboDBField = wImport.gridMapping.getCombo(idxOptions);
													comboDBField.clear();
													comboDBField.put("suppprice_product","<?php echo _l('Default values display products/combinations grids',1)?>");
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("suppprice_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['name']).'");';
														}
													?>
												}
												if(map[1]=='supplier_currency')
												{
													idxOptions=wImport.gridMapping.getColIndexById('options');
													comboDBField = wImport.gridMapping.getCombo(idxOptions);
													comboDBField.clear();
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("supcurrency_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['name']).'");';
														}
													?>
												}
												<?php } ?>
												wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),3).setValue(map[2]);
											}
										}
								});
						}
						getCheck();
						setOptionsBGColor();
					});
			}
			if (id=='refresh')
			{
				if (typeof filename=='undefined')return;
				if (typeof mapping=='undefined')
				{
					idxMapping=wImport.gridFiles.getColIndexById('mapping');
					mapping=wImport.gridFiles.cells(lastCSVFile,idxMapping).getValue();
					//if (mapping=='') return;
				}
				displayMapping(filename,mapping);
			}
			if (id=='load_by_name')
			{
				comboArray = new Object();
				comboValuesArray = new Object();
				optionLabelArray = new Object();
				$.each(comboDBField.getKeys(), function(num, value){
					var label = comboDBField.get(value);
					if(label!=undefined && label!=null && label!="" && label!=0)
					{
						comboArray[label] = value;
						comboValuesArray[value] = value;

						if(in_array(value,arrayFieldOption))
							optionLabelArray[value] = label;
					}
				});
				
				idxFileField=wImport.gridMapping.getColIndexById('file_field');
				idxDBField=wImport.gridMapping.getColIndexById('db_field');
				idxOptions=wImport.gridMapping.getColIndexById('options');
				idxUse=wImport.gridMapping.getColIndexById('use');
				
				wImport.gridMapping.forEachRow(function(row_id){
					var name = $.trim(wImport.gridMapping.cells(row_id, idxFileField).getValue());
					var field = wImport.gridMapping.cells(row_id, idxDBField).getValue();
					name = replaceAll("&amp;","&",name);

					if(name!=undefined && name!=null && name!="" && name!=0 && field!=undefined && (field==null || field=="" || field==0))
					{
						// check field image
						var patt = new RegExp("image_id");
						var isImgId = patt.test(name);
						if(isImgId)
							name = "image_id";
							
						var check = false;
						var value = comboArray[name];
						var value_bis = comboValuesArray[name];

						without_supplier = false;
						<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
						if(value=='supplier_reference' || value=='wholesale_price')
						{
							value = undefined;
							value_bis = undefined;
							name = name+" noneeee";
						}
						<?php } ?>
						
						if(value!=undefined && value!=null && value!="" && value!=0)
						{
							wImport.gridMapping.cells(row_id, idxDBField).setValue(value);
							check = true;
						}
						else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
						{
							wImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
							value = value_bis;
							check = true;
						}
						else
						{
							// check field image
							var patt = new RegExp("image_legend");
							var isImgLegend = patt.test(name);
							if(isImgLegend)
								name = $.trim(name.substring(0, name.length - 2));
						
							var original_name = name;
							var lang = $.trim(name.slice(-2).toLowerCase());
							name = $.trim(name.substring(0, name.length - 3));

							var patt = new RegExp("link_to_image");
							var isImg = patt.test(name);
							var patt = new RegExp("link_to_cover_image");
							var isImg_bis = patt.test(name);
							var patt = new RegExp("image_link");
							var isImg_ter = patt.test(name);
							if(isImg || isImg_bis || isImg_ter)
							{
								wImport.gridMapping.cells(row_id, idxDBField).setValue("imageURL");
								value = "imageURL";
								check = true;
							}
							else
							{
								var value = comboArray[name];
								var value_bis = comboValuesArray[name];
								if(value!=undefined && value!=null && value!="" && value!=0)
								{
									wImport.gridMapping.cells(row_id, idxDBField).setValue(value);
									check = true;
								}
								else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
								{
									wImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
									value = value_bis;
									check = true;
								}
								else
								{
									var encoded_name = unescape(encodeURIComponent(name));
									var value = comboArray[encoded_name];
									var value_bis = comboValuesArray[encoded_name];
									if(value!=undefined && value!=null && value!="" && value!=0)
									{
										wImport.gridMapping.cells(row_id, idxDBField).setValue(value);
										check = true;
									}
									else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
									{
										wImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
										value = value_bis;
										check = true;
									}
									else
									{
										var decoded_name = decodeURIComponent(unescape(name));
										var value = comboArray[decoded_name];
										var value_bis = comboValuesArray[decoded_name];
										if(value!=undefined && value!=null && value!="" && value!=0)
										{
											wImport.gridMapping.cells(row_id, idxDBField).setValue(value);
											check = true;
										}
										else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
										{
											wImport.gridMapping.cells(row_id, idxDBField).setValue(value_bis);
											value = value_bis;
											check = true;
										}
									}
								}

								if(in_array(value,arrayFieldLang))
								{
									wImport.gridMapping.cells(row_id, idxOptions).setValue(lang);
									onEditCellMapping(2,row_id, idxOptions,lang);
								}
								if(!check)
								{
									$.each(optionLabelArray, function(id, label){
										var finded = false;
										var option = "";
										if(name.search(label)>=0)
										{
											finded = true;
											<?php if(SCAS) { ?>
											if(id=='quantity' || id=='location' || id=='add_quantity' || id=='remove_quantity' || id=='supplier_reference' || id=='wholesale_price' || id=='quantity_on_sale')
												option = $.trim(original_name.replace(label+" ", ""));
											<?php } else { ?>
											if(id=='supplier_reference' || id=='wholesale_price')
												option = $.trim(original_name.replace(label+" ", ""));
											<?php } ?>
											else 
												option = $.trim(name.replace(label+" ", ""));
											<?php if(SCAS) { ?>
											if(id=='quantity')
											{
												if(
														name.search('<?php echo _l('physical stock',1); ?>')>=0
														|| name.search('<?php echo _l('available stock',1); ?>')>=0
														|| name.search('<?php echo _l('live stock',1); ?>')>=0
													)
												{
													finded = false;
													option = "";
												}
											}
											<?php } ?>
										}
										else
										{
											var encoded_name = unescape(encodeURIComponent(name));
											if(encoded_name.search(label)>=0)
											{
												finded = true;
												<?php if(SCAS) { ?>
												if(id=='quantity' || id=='location' || id=='add_quantity' || id=='remove_quantity' || id=='supplier_reference' || id=='wholesale_price' || id=='quantity_on_sale')
													option = $.trim(original_name.replace(label+" ", ""));
												<?php } else { ?>
												if(id=='supplier_reference' || id=='wholesale_price')
													option = $.trim(original_name.replace(label+" ", ""));
												<?php } ?>
												else 
													option = $.trim(encoded_name.replace(label+" ", ""));
												<?php if(SCAS) { ?>
												if(id=='quantity')
												{
													if(
															encoded_name.search('<?php echo _l('physical stock',1); ?>')>=0
															|| encoded_name.search('<?php echo _l('available stock',1); ?>')>=0
															|| encoded_name.search('<?php echo _l('live stock',1); ?>')>=0
														)
													{
														finded = false;
														option = "";
													}
												}
												<?php } ?>
											}
											else
											{
												var decoded_name = decodeURIComponent(unescape(name));
												if(encoded_name.search(label)>=0)
												{
													finded = true;
													<?php if(SCAS) { ?>
													if(id=='quantity' || id=='location' || id=='add_quantity' || id=='remove_quantity' || id=='supplier_reference' || id=='wholesale_price' || id=='quantity_on_sale')
														option = $.trim(original_name.replace(label+" ", ""));
													<?php } else { ?>
													if(id=='supplier_reference' || id=='wholesale_price')
														option = $.trim(original_name.replace(label+" ", ""));
													<?php } ?>
													else 
														option = $.trim(decoded_name.replace(label+" ", ""));
												}
											}
										}

										if(finded)
										{
											wImport.gridMapping.cells(row_id, idxDBField).setValue(id);
											value = id;
											check = true;

											if(option!=undefined && option!=null && option!="")
											{
												<?php if(SCAS) { ?>
												if(id=='quantity' || id=='location' || id=='add_quantity' || id=='remove_quantity' || id=='quantity_on_sale')
												{
													if("<?php echo _l('No warehouse',1)?>" == option)
														option = "warehouse_none";
													<?php
														$warehouses=Warehouse::getWarehouses(true);
														foreach($warehouses AS $warehouse)
														{ 
															echo 'else if("'._l('Warehouse',1)." ".addslashes($warehouse['name']).'" == option)
																option = "warehouse_'.addslashes($warehouse['id_warehouse']).'";';
														}
													?>

													comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
													<?php
														foreach($warehouses AS $warehouse)
														{ 
															echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes($warehouse['name']).'");';
														}
													?>
												}
												<?php } ?>	
												<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
												if(id=='supplier_reference')
												{
													if("<?php echo _l('Default values display products/combinations grids',1)?>" == option || option=="none")
														option = "suppref_product";
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $supplier)
														{  
															echo 'else if("'._l('Supplier',1)." ".addslashes($supplier['name']).'" == option)
																option = "suppref_supp_'.addslashes($supplier['id_supplier']).'";';
														}
													?>

													comboDBField.put("suppref_product","<?php echo _l('Default values display products/combinations grids',1)?>");
													<?php
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("suppref_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['name']).'");';
														}
													?>
												}
												if(id=='wholesale_price')
												{
													if("<?php echo _l('Default values display products/combinations grids',1)?>" == option || option=="none")
														option = "suppprice_product";
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $supplier)
														{  
															echo 'else if("'._l('Supplier',1)." ".addslashes($supplier['name']).'" == option)
																option = "suppprice_supp_'.addslashes($supplier['id_supplier']).'";';
														}
													?>

													comboDBField.put("suppprice_product","<?php echo _l('Default values display products/combinations grids',1)?>");
													<?php
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("suppprice_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['name']).'");';
														}
													?>
												}
												if(id=='supplier_currency')
												{
													<?php
														$query = new DbQuery();
														$query->select('s.*, sl.`description`');
														$query->from('supplier', 's');
														$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
														$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
														$query->orderBy(' s.`name` ASC');
														$query->groupBy('s.id_supplier');
														
														$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
														foreach($suppliers AS $key=>$supplier)
														{  
															if($key==0)
																echo 'if("'._l('Supplier',1)." ".addslashes($supplier['name']).'" == option)
																	option = "supcurrency_supp_'.addslashes($supplier['id_supplier']).'";';
															else
																echo 'else if("'._l('Supplier',1)." ".addslashes($supplier['name']).'" == option)
																	option = "supcurrency_supp_'.addslashes($supplier['id_supplier']).'";';
														}
													?>

													<?php
														foreach($suppliers AS $supplier)
														{ 
															echo 'comboDBField.put("supcurrency_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes($supplier['id_supplier']).'");';
														}
													?>
												}
												<?php } ?>											
												
												wImport.gridMapping.cells(row_id, idxOptions).setValue(option);
											}
										}
									});
								}
							}
						}

						if(check)
							onEditCellMapping(2,row_id, idxDBField,value);
					}
				});
			}
			if (id=='saveasbtn')
			{
				if (wImport.tbMapping.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Mapping name should not be empty')?>',type:'error'});
				}else{
					var mapping='';
					wImport.gridMapping.forEachRow(function(id){
							if (wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),0).getValue()=="1")
							{
								mapping+=wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),1).getValue()+','+
												 wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),2).getValue()+','+
												 wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),3).getValue()+';';
							}
						});
					wImport.tbMapping.setValue('saveas',getLinkRewriteFromStringLightWithCase(wImport.tbMapping.getValue('saveas')));
					$.post('index.php?ajax=1&act=cat_win-import_mapping_update&action=mapping_saveas',{'filename':wImport.tbMapping.getValue('saveas'),'mapping':mapping},function(data){
								dhtmlx.message({text:data,type:'info'});
								if (!in_array('loadmapping'+wImport.tbMapping.getValue('saveas'),wImport.tbMapping.getAllListOptions('loadmapping')))
								{
									wImport.tbMapping.addListOption('loadmapping', 'loadmapping'+wImport.tbMapping.getValue('saveas'), 0, 'button', wImport.tbMapping.getValue('saveas'))
									wImport.tbMapping.setListOptionSelected('loadmapping', 'loadmapping'+wImport.tbMapping.getValue('saveas'));
								}
								displayOptions();
								getCheck();
							});
				}
			}
			if (id=='delete')
			{
				if (wImport.tbMapping.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Mapping name should not be empty')?>',type:'error'});
				}else{
					if (confirm('<?php echo _l('Do you want to delete the current mapping?',1)?>'))
						$.get('index.php?ajax=1&act=cat_win-import_mapping_update&action=mapping_delete&filename='+wImport.tbMapping.getValue('saveas'),function(data){
								wImport.gridMapping.clearAll(true);
								wImport.tbMapping.removeListOption('loadmapping', 'loadmapping'+wImport.tbMapping.getValue('saveas'));
								wImport.tbMapping.setValue('saveas','');
								getCheck();
							});
				}
			}
	}
	wImport.tbMapping.attachEvent("onClick",onClickMapping);
	wImport.gridMapping=dhxlImport.cells('b').attachGrid();
	wImport.gridMapping.setImagePath("lib/js/imgs/");
	function setOptionsBGColor()
	{
		idxMark=wImport.gridMapping.getColIndexById('use');
		idxDBField=wImport.gridMapping.getColIndexById('db_field');
		idxOptions=wImport.gridMapping.getColIndexById('options');
		wImport.gridMapping.forEachRow(function(rId){
			wImport.gridMapping.cells(rId,idxOptions).setBgColor(wImport.gridMapping.cells(rId,idxDBField).getBgColor());
			var flag=false;
			if (in_array(wImport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang))
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature' || wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature_custom')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='attribute' || wImport.gridMapping.cells(rId,idxDBField).getValue()=='attribute_multiple')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php if (SCAS) { ?>
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='quantity')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='location')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='add_quantity')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_on_sale')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='remove_quantity')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php } ?>
			<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_reference')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='wholesale_price')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_currency')
			{
				wImport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php } ?>
<?php
	sc_ext::readImportCSVConfigXML('importMappingPrepareGrid');
?>
			if (!flag) wImport.gridMapping.cells(rId,idxOptions).setValue('');
		});
	}
	function checkOptions()
	{
		var flag=true;
		idxDBField=wImport.gridMapping.getColIndexById('db_field');
		idxOptions=wImport.gridMapping.getColIndexById('options');
		wImport.gridMapping.forEachRow(function(rId){
			if (wImport.gridMapping.cells(rId,0).getValue()=="1")
			{
				if (in_array(wImport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang)
							&& wImport.gridMapping.cells(rId,idxOptions).getValue()=='')
					flag=false;
				if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature'
							&& wImport.gridMapping.cells(rId,idxOptions).getValue()=='')
					flag=false;
				if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature_custom'
							&& wImport.gridMapping.cells(rId,idxOptions).getValue()=='')
					flag=false;
<?php
	sc_ext::readImportCSVConfigXML('importMappingCheckGrid');
?>
			}
		});
		return flag;
	}
	function onEditCellMapping(stage,rId,cInd,nValue,oValue){
		if(stage==1 && (cInd==2 || cInd==3)){ 
  	  var editor = this.editor; 
	    var pos = this.getPosition(editor.cell);        
    	var y = document.body.offsetHeight-pos[1];   
    	if(y < editor.list.offsetHeight)       
		    editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';   
    }
		idxMark=wImport.gridMapping.getColIndexById('use');
		idxDBField=wImport.gridMapping.getColIndexById('db_field');
		idxOptions=wImport.gridMapping.getColIndexById('options');
		comboDBField = wImport.gridMapping.getCombo(idxOptions);
		if (cInd == idxDBField && nValue != oValue){
			wImport.gridMapping.cells(rId,idxMark).setValue(1);
			setOptionsBGColor();
		}
		if (cInd == idxOptions)
		{
			comboDBField.clear();
			if (in_array(wImport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang))
			{
<?php
	foreach($languages AS $lang)
	{ 
		echo '				comboDBField.put("'.$lang['iso_code'].'","'.$lang['iso_code'].'");';
	}
?>
				return true;
			}
			<?php if(SCAS) { ?>
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='quantity')
			{
				comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{ 
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$warehouse['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='location')
			{
				comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{ 
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$warehouse['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='add_quantity')
			{
				comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{ 
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$warehouse['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_on_sale')
			{
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$warehouse['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='remove_quantity')
			{
				comboDBField.put("warehouse_none","<?php echo _l('No warehouse',1)?>");
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{ 
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$warehouse['name']))).'");';
					}
				?>
				return true;
			}
			<?php } ?>
			<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_reference')
			{
				comboDBField.put("suppref_product","<?php echo _l('Default values display products/combinations grids',1)?>");
				<?php
					$query = new DbQuery();
					$query->select('s.*, sl.`description`');
					$query->from('supplier', 's');
					$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
					$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
					$query->orderBy(' s.`name` ASC');
					$query->groupBy('s.id_supplier');
					
					$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
					foreach($suppliers AS $supplier)
					{ 
						echo 'comboDBField.put("suppref_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$supplier['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='wholesale_price')
			{
				comboDBField.put("suppprice_product","<?php echo _l('Default values display products/combinations grids',1)?>");
				<?php
					$query = new DbQuery();
					$query->select('s.*, sl.`description`');
					$query->from('supplier', 's');
					$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
					$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
					$query->orderBy(' s.`name` ASC');
					$query->groupBy('s.id_supplier');
					
					$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
					foreach($suppliers AS $supplier)
					{ 
						echo 'comboDBField.put("suppprice_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$supplier['name']))).'");';
					}
				?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_currency')
			{
				<?php
					$query = new DbQuery();
					$query->select('s.*, sl.`description`');
					$query->from('supplier', 's');
					$query->leftJoin('supplier_lang', 'sl', 's.`id_supplier` = sl.`id_supplier` AND sl.`id_lang` = '.(int)$defaultLanguageId);
					$query->leftJoin('supplier_shop', 'ss', 's.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)SCI::getSelectedShop());
					$query->orderBy(' s.`name` ASC');
					$query->groupBy('s.id_supplier');
					
					$suppliers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
					foreach($suppliers AS $supplier)
					{ 
						echo 'comboDBField.put("supcurrency_supp_'.addslashes($supplier['id_supplier']).'","'._l('Supplier',1)." ".addslashes(str_replace("\n","",str_replace("\r","",$supplier['name']))).'");';
					}
				?>
				return true;
			}
			<?php } ?>
			if (wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature' || wImport.gridMapping.cells(rId,idxDBField).getValue()=='feature_custom')
			{
<?php
	$features=Db::getInstance()->executeS('
							SELECT *
							FROM `'._DB_PREFIX_.'feature` f
							LEFT JOIN `'._DB_PREFIX_.'feature_lang` fl ON (f.`id_feature` = fl.`id_feature` AND fl.`id_lang` = '.(int)$defaultLanguageId.')');
	foreach($features AS $feature)
	{ 
		echo '				comboDBField.put("'.addslashes(str_replace("\n","",str_replace("\r","",$feature['name']))).'","'.addslashes(str_replace("\n","",str_replace("\r","",$feature['name']))).'");';
	}
?>
				return true;
			}
			if (wImport.gridMapping.cells(rId,idxDBField).getValue().substr(0,9)=='attribute')
			{
<?php
	$groups=Db::getInstance()->executeS('
		SELECT DISTINCT agl.`name`, ag.*, agl.*
		FROM `'._DB_PREFIX_.'attribute_group` ag
		LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
			ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$defaultLanguageId.')
		ORDER BY `name` ASC');
	foreach($groups AS $group)
	{ 
		echo '				comboDBField.put("'.addslashes(str_replace("\n","",str_replace("\r","",$group['name']))).'","'.addslashes(str_replace("\n","",str_replace("\r","",$group['name']))).'");';
	}
?>
				return true;
			}
<?php
	sc_ext::readImportCSVConfigXML('importMappingFillCombo');
?>
			return false;
		}
		return true;
	}
	wImport.gridMapping.attachEvent('onEditCell',onEditCellMapping);
	
	dhxlImport.cells('c').setText("<?php echo _l('Process')?>");
//	dhxlImport.cells('c').vs["def"].dhxcont.mainCont["def"].style.overflow = "auto";
	
	wImport.tbProcess=dhxlImport.cells('c').attachToolbar();
	var create_categories=false;
	wImport.tbProcess.addButton("loop_tool", 0, "", "lib/img/clock.png", "lib/img/clock.png");
	wImport.tbProcess.setItemToolTip('loop_tool','<?php echo _l('Auto-import tool')?>');
	wImport.tbProcess.addButton("go_process", 0, "", "lib/img/database_go.png", "lib/img/database_go.png");
	wImport.tbProcess.setItemToolTip('go_process','<?php echo _l('Import data')?>');
	wImport.tbProcess.addSeparator("sep01", 0);
	wImport.tbProcess.addButton("check", 0, "", "lib/img/accept.png", "lib/img/accept.png");
	wImport.tbProcess.setItemToolTip('check','<?php echo _l('Votre import est-il prêt ?')?>');
	wImport.tbProcess.addSeparator("sep02", 0);
	wImport.tbProcess.addInput("importlimit", 0,500,30);
	wImport.tbProcess.setItemToolTip('importlimit','<?php echo _l('Number of the first lines to import from the CSV file')?>');
	$(wImport.tbProcess.getInput('importlimit')).change(function(){getCheck();});
	wImport.tbProcess.addText('txtimportlimit', 0, '<?php echo _l('Lines to import')._l(':')?>');
	wImport.tbProcess.addButtonTwoState("create_categories", 0, "", "lib/img/folder_add.png", "lib/img/folder_add.png");
	wImport.tbProcess.setItemToolTip('create_categories','<?php echo _l('Check and create categories')?>');
	wImport.tbProcess.attachEvent("onStateChange", function(id,state){
			if (id=='create_categories'){
				create_categories=state;
				getCheck();
			}
		});
	wImport.tbProcess.attachEvent("onClick",
		function(id){
			if (id=='check')
			{
				window.open("<?php echo getHelpLink('cat_win-import_init'); ?>");
			}
			if (id=='go_process')
			{
				if (!autoImportRunning){
					displayProcess();
				}else{
					dhtmlx.message({text:'<?php echo _l('AutoImport already running')?>',type:'error'});
				}
			}
			if (id=='loop_tool')
			{
				displayAutoImportTool();
			}
		});

	
//#####################################
//############ Load functions
//#####################################

function displayOptions(callback)
{
	wImport.gridFiles.clearAll(true);
	wImport.gridFiles.loadXML("index.php?ajax=1&act=cat_win-import_config_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
			if (callback)
			{
				eval(callback);
			}else if(lastCSVFile!=''){
				wImport.gridFiles.selectRowById(lastCSVFile);
			}
			});
}

function displayMapping(filename,mapping)
{
	wImport.gridMapping.clearAll(true);
	wImport.gridMapping.loadXML("index.php?ajax=1&act=cat_win-import_mapping_get&id_lang="+SC_ID_LANG+"&imp_opt_file="+filename+"&"+new Date().getTime(),function()
			{
				idxDBField=wImport.gridMapping.getColIndexById('db_field');
				comboDBField = wImport.gridMapping.getCombo(idxDBField);
				comboDBField.clear();
<?php
	global $array;
	$array=array();
	$array[_l('active',1)]="comboDBField.put('active','"._l('active',1)."');";
	$array[_l('quantity',1)]="comboDBField.put('quantity','"._l('quantity',1)."');";
	$array[_l('quantity - add',1)]="comboDBField.put('add_quantity','"._l('quantity - add',1)."');";
	$array[_l('quantity - remove',1)]="comboDBField.put('remove_quantity','"._l('quantity - remove',1)."');";
	$array[_l('name',1)]="comboDBField.put('name','"._l('name',1)."');";
	$array[_l('description',1)]="comboDBField.put('description','"._l('description',1)."');";
	$array[_l('description_short',1)]="comboDBField.put('description_short','"._l('description_short',1)."');";
	$array[_l('meta_title',1)]="comboDBField.put('meta_title','"._l('meta_title',1)."');";
	$array[_l('meta_description',1)]="comboDBField.put('meta_description','"._l('meta_description',1)."');";
	$array[_l('meta_keywords',1)]="comboDBField.put('meta_keywords','"._l('meta_keywords',1)."');";
	$array[_l('link_rewrite',1)]="comboDBField.put('link_rewrite','"._l('link_rewrite',1)."');";
	$array[_l('available_now',1)]="comboDBField.put('available_now','"._l('available_now',1)."');";
	$array[_l('available_later',1)]="comboDBField.put('available_later','"._l('available_later',1)."');";
	$array[_l('out_of_stock',1)]="comboDBField.put('out_of_stock','"._l('out_of_stock',1)."');";
	$array[_l('reference',1)]="comboDBField.put('reference','"._l('reference',1)."');";
	$array[_l('out_of_stock',1)]="comboDBField.put('out_of_stock','"._l('out_of_stock',1)."');";
	$array[_l('supplier_reference',1)]="comboDBField.put('supplier_reference','"._l('supplier_reference',1)."');";
	$array[_l('supplier',1)]="comboDBField.put('supplier','"._l('supplier',1)."');";
	$array[_l('manufacturer',1)]="comboDBField.put('manufacturer','"._l('manufacturer',1)."');";
	$array[_l('wholesale_price',1)]="comboDBField.put('wholesale_price','"._l('wholesale_price',1)."');";
	$array[_l('ecotax',1)]="comboDBField.put('ecotax','"._l('ecotax',1)."');";
	$array[_l('priceinctax',1)]="comboDBField.put('priceinctax','"._l('priceinctax',1)."');";
	$array[_l('priceinctaxincecotax',1)]="comboDBField.put('priceinctaxincecotax','"._l('priceinctax including ecotax',1)."');";
	$array[_l('priceexctax',1)]="comboDBField.put('priceexctax','"._l('priceexctax',1)."');";
	$array[_l('vat',1)]="comboDBField.put('VAT','"._l('vat',1)."');";
	$array[_l('ean13',1)]="comboDBField.put('EAN13','"._l('ean13',1)."');";
	$array[_l('weight',1)]="comboDBField.put('weight','"._l('weight',1)."');";
	$array[_l('on_sale',1)]="comboDBField.put('on_sale','"._l('on_sale',1)."');";
	$array[_l('reduction_price',1)]="comboDBField.put('reduction_price','"._l('reduction_price',1)."');";
	$array[_l('reduction_percent',1)]="comboDBField.put('reduction_percent','"._l('reduction_percent',1)."');";
	$array[_l('reduction_from',1)]="comboDBField.put('reduction_from','"._l('reduction_from',1)."');";
	$array[_l('reduction_to',1)]="comboDBField.put('reduction_to','"._l('reduction_to',1)."');";
	$array[_l('location',1)]="comboDBField.put('location','"._l('location',1)."');";
	$array[_l('feature',1)]="comboDBField.put('feature','"._l('feature',1)."');";
	$array[_l('feature (custom)',1)]="comboDBField.put('feature_custom','"._l('feature (custom)',1)."');";
	$array[_l('id_category_default',1)]="comboDBField.put('id_category_default','id_category_default');";
	$array[_l('category by default',1)]="comboDBField.put('category_default','"._l('category by default',1)."');";
	$array[_l('categories',1)]="comboDBField.put('categories','"._l('categories',1)."');";
	$array[_l('id_category',1)]="comboDBField.put('id_category','"._l('id_category',1)."');";
	$array[_l('imageURL',1)]="comboDBField.put('imageURL','"._l('imageURL',1)."');";
	$array[_l('image_default_id',1)]="comboDBField.put('image_default_id','"._l('image_default_id',1)."');";
	$array[_l('image_id',1)]="comboDBField.put('image_id','"._l('image_id',1)."');";
	$array[_l('image_legend',1)]="comboDBField.put('image_legend','"._l('image_legend',1)."');";
	$array[_l('tags',1)]="comboDBField.put('tags','"._l('tags',1)."');";
	$array[_l('id_product',1)]="comboDBField.put('id_product','id_product');";
	$array[_l('id_product_attribute',1)]="comboDBField.put('id_product_attribute','id_product_attribute');";
	$array[_l('attribute of combination - multiple values',1)]="comboDBField.put('attribute_multiple','"._l('attribute of combination - multiple values',1)."');";
	$array[_l('attribute of combination',1)]="comboDBField.put('attribute','"._l('attribute of combination',1)."');";
	$array[_l('attribute of combination - color value',1)]="comboDBField.put('attribute_color','"._l('attribute of combination - color value',1)."');";
	$array[_l('attribute of combination - texture',1)]="comboDBField.put('attribute_texture','"._l('attribute of combination - texture',1)."');";
	$array[_l('attribute of combination - default combination',1)]="comboDBField.put('attribute_default_on','"._l('attribute of combination - default combination',1)."');";
	$array[_l('date_add',1)]="comboDBField.put('date_add','date_add');";
	$array[_l('date_upd',1)]="comboDBField.put('date_upd','date_upd');";
	$array[_l('customization field: type',1)]="comboDBField.put('customization_field_type','"._l('customization field: type',1)."');";
	$array[_l('customization field: required',1)]="comboDBField.put('customization_field_required','"._l('customization field: required',1)."');";
	$array[_l('customization field: name',1)]="comboDBField.put('customization_field_name','"._l('customization field: name',1)."');";
	$array[' '._l('Action: Delete images',1)]="comboDBField.put('ActionDeleteImages','"._l('Action: Delete images',1)."');";
	$array[' '._l('Action: Delete tags',1)]="comboDBField.put('ActionDeleteTags','"._l('Action: Delete tags',1)."');";
	$array[' '._l('Action: Delete all combinations',1)]="comboDBField.put('ActionDeleteAllCombinations','"._l('Action: Delete all combinations',1)."');";
	$array[' '._l('Action: Delete all product features',1)]="comboDBField.put('ActionDeleteAllFeatures','"._l('Action: Delete all product features',1)."');";
	$array[_l('price_impact',1)]="comboDBField.put('price_impact','"._l('price impact of combination',1)."');";
	$array[_l('weight impact of combination',1)]="comboDBField.put('weight_impact','"._l('weight impact of combination',1)."');";
	$array[_l('accessories',1)]="comboDBField.put('accessories','"._l('accessories',1)."');";
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
		$array[' '._l('Action: Delete attachements',1)]="comboDBField.put('ActionDeleteAttachments','"._l('Action: Delete attachements',1)."');";
		$array[' '._l('Action: Dissociate attachments',1)]="comboDBField.put('ActionDissociateAttachments','"._l('Action: Dissociate attachments',1)."');";
	}
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		$array[_l('minimal quantity',1)]="comboDBField.put('minimal_quantity','"._l('minimal quantity',1)."');";
		$array[_l('available for order',1)]="comboDBField.put('available_for_order','"._l('available for order',1)."');";
		$array[_l('show price',1)]="comboDBField.put('show_price','"._l('show price',1)."');";
		$array[_l('online only (not sold in store)',1)]="comboDBField.put('online_only','"._l('online only (not sold in store)',1)."');";
		$array[_l('condition (new, used, refurbished)',1)]="comboDBField.put('condition','"._l('condition (new, used, refurbished)',1)."');";
		$array[_l('unit price (combination impact)',1)]="comboDBField.put('unit_price_impact','"._l('unit price (combination impact)',1)."');";
		$array[_l('upc',1)]="comboDBField.put('upc','"._l('upc',1)."');";
		$array[_l('unit (for unit price)',1)]="comboDBField.put('unity','"._l('unit (for unit price)',1)."');";
		$array[_l('unit price',1)]="comboDBField.put('unit_price_ratio','"._l('unit price',1)."');";
		$array[_l('width',1)]="comboDBField.put('width','"._l('width',1)."');";
		$array[_l('height',1)]="comboDBField.put('height','"._l('height',1)."');";
		$array[_l('depth',1)]="comboDBField.put('depth','"._l('depth',1)."');";
		$array[_l('attachments',1)]="comboDBField.put('attachments','"._l('attachments',1)."');";
		$array[_l('specific price')._l(':').' '._l('from quantity',1)]="comboDBField.put('specific_price_from_quantity','"._l('specific price')._l(':').' '._l('from quantity',1)."');";
		//$array[_l('specific price: id_shop',1)]="comboDBField.put('specific_price_id_shop','"._l('specific price: id_shop',1)."');";
		$array[_l('specific price')._l(':').' '._l('id_country',1)]="comboDBField.put('specific_price_id_country','"._l('specific price')._l(':').' '._l('id_country',1)."');";
		$array[_l('specific price')._l(':').' '._l('id_currency',1)]="comboDBField.put('specific_price_id_currency','"._l('specific price')._l(':').' '._l('id_currency',1)."');";
		$array[_l('specific price')._l(':').' '._l('id_group',1)]="comboDBField.put('specific_price_id_group','"._l('specific price')._l(':').' '._l('id_group',1)."');";
		$array[_l('specific price')._l(':').' '._l('price',1)]="comboDBField.put('specific_price_price','"._l('specific price')._l(':').' '._l('price',1)."');";
		$array[_l('specific price')._l(':').' '._l('reduction',1)]="comboDBField.put('specific_price_reduction','"._l('specific price')._l(':').' '._l('reduction',1)."');";
		$array[_l('specific price')._l(':').' '._l('reduction type',1)]="comboDBField.put('specific_price_reduction_type','"._l('specific price')._l(':').' '._l('reduction type',1)."');";
		$array[_l('specific price')._l(':').' '._l('from date',1)]="comboDBField.put('specific_price_from','"._l('specific price')._l(':').' '._l('from date',1)."');";
		$array[_l('specific price')._l(':').' '._l('to date',1)]="comboDBField.put('specific_price_to',\""._l('specific price')._l(':').' '._l('to date')."\");";
		$array[' '._l('Action: Delete specific price',1)]="comboDBField.put('ActionDeleteSpecificPrice','"._l('Action: Delete specific price',1)."');";
		$array[_l('additional_shipping_cost',1)]="comboDBField.put('additional_shipping_cost','"._l('additional shipping cost',1)."');";
	}
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$array[_l('redirect_type',1)]="comboDBField.put('redirect_type','"._l('redirect_type',1)."');";
		$array[_l('redirect_id_product',1)]="comboDBField.put('id_product_redirected','"._l('redirect_id_product',1)."');";
		$array[_l('date_available',1)]="comboDBField.put('date_available','"._l('available date',1)."');";
		$array[_l('currency (supplier price)',1)]="comboDBField.put('supplier_currency','"._l('currency (supplier price)',1)."');";
		$array[_l('supplier - default',1)]="comboDBField.put('supplier_default','"._l('supplier - default',1)."');";
		$array[_l('visibility',1)]="comboDBField.put('visibility','"._l('visibility',1)."');";
		$array[_l('carriers',1)]="comboDBField.put('carriers','"._l('carriers',1)."');";
		$array[_l('Action: Dissociate carriers',1)]="comboDBField.put('ActionDeleteCarriers','"._l('Action: Dissociate carriers',1)."');";
		$array[_l('Action: Dissociate warehouses',1)]="comboDBField.put('ActionDeletesWarehouses','"._l('Action: Dissociate warehouses',1)."');";
		$array[_l('Action: Dissociate suppliers',1)]="comboDBField.put('ActionDeletesSuppliers','"._l('Action: Dissociate suppliers',1)."');";
	}
	if (SCMS)
	{
		$array['id_shop_default']="comboDBField.put('id_shop_default','id_shop_default');";
		$array['id_shop_list']="comboDBField.put('id_shop_list','id_shop_list');";
	}
	if (SCAS)
	{
		$array[_l('stock - advanced stock mgmt.',1)]="comboDBField.put('advanced_stock_management','"._l('stock - advanced stock mgmt.',1)."');";
		$array[_l('quantity : available on sale.',1)]="comboDBField.put('quantity_on_sale','"._l('quantity : available on sale.',1)."');";
	}
	if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
	{
		$array[_l('reduction_tax',1)]="comboDBField.put('reduction_tax','"._l('reduction_tax',1)."');";
		$array[_l('specific price')._l(':').' '._l('Reduction tax',1)]="comboDBField.put('specific_price_reduction_tax','"._l('specific price')._l(':').' '._l('Reduction tax',1)."');";
	}
	if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
	{
		$array[_l('show condition',1)]="comboDBField.put('show_condition','"._l('show condition',1)."');";
		$array[_l('isbn',1)]="comboDBField.put('isbn','"._l('isbn',1)."');";
	}
	$sc_active=SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0);
	if(!empty($sc_active))
		$array[_l('combination - used',1)]="comboDBField.put('sc_active','"._l('combination - used',1)."');";
	sc_ext::readImportCSVConfigXML('definition');

	ksort($array);
	echo join("\n",$array);
?>
				if (mapping!='')
				{
					onClickMapping('loadmapping'+mapping);
				}else{
					onClickMapping('loadmapping'+filename.replace('.csv','').replace('.CSV',''));
				}
			});
}

function displayProcess()
{
	var mapping='';
	if (!checkOptions() || lastCSVFile=='')
	{
		dhtmlx.message({text:'<?php echo _l('Some options are missing')?>',type:'error'});
		return false;
	}
	wImport.gridMapping.forEachRow(function(id){
			if (wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),0).getValue()=="1")
			{
				mapping+=wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),1).getValue()+','+
								 wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),2).getValue()+','+
								 wImport.gridMapping.cells(wImport.gridMapping.getRowIndex(id),3).getValue()+';';
			}
		});
	mapping=mapping.substr(0,mapping.length-1);
	autoImportLastState=1;
	wImport.setIcon('lib/img/ajax-loader16.gif','../../../lib/img/ajax-loader16.gif');
	$.post('index.php?ajax=1&act=cat_win-import_process&action=mapping_process',{'mapping':mapping,'filename':lastCSVFile,'importlimit':wImport.tbProcess.getValue('importlimit'),'create_categories':(1*create_categories)},function(data){
		document.onselectstart = new Function("return true;");
		dhxlImport.cells('c').attachHTMLString(data);
		wImport.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
	});
	setTimeout("displayOptions('wImport.gridFiles.selectRowById(getTODOName(lastCSVFile), false, true, false)');",500);
}

function displayAutoImportTool()
{
	if (!dhxWins.isWindow("wCatAutoImport"))
	{
		wCatAutoImport = dhxWins.createWindow("wCatAutoImport", 550, 350, 220, 68);
		wCatAutoImport.setMinDimension(220, 68);
		wCatAutoImport.setIcon('lib/img/clock.png','../../../lib/img/clock.png');
		wCatAutoImport.setText("<?php echo _l('Auto-import tool')?>");
		wCatAutoImport.button('park').hide();
		wCatAutoImport.button('minmax').hide();
		wCatAutoImport._tb=wCatAutoImport.attachToolbar();
		wCatAutoImport._tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
		wCatAutoImport._tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
/*
		var opts = [
								['0', 'obj', '<?php echo _l('No alert'); ?>', ''],
								['1', 'obj', '<?php echo _l('Sound 1'); ?>', '']
							 ];
		wCatAutoImport._tb.addButtonSelect("alertsound", 0, "", opts, "lib/img/sound.png", "lib/img/sound.png",true,true);
		wCatAutoImport._tb.setItemToolTip('alertsound','<?php echo _l('Sound alert type')?>');
		var opts = [
								['0', 'obj', '<?php echo _l('No alert'); ?>', ''],
								['1', 'obj', '<?php echo _l('Browser popup'); ?>', ''],
								['2', 'obj', '<?php echo _l('Red screen'); ?>', '']
							 ];
		wCatAutoImport._tb.addButtonSelect("alertvisual", 0, "", opts, "lib/img/eye.png", "lib/img/eye.png",true,true);
		wCatAutoImport._tb.setItemToolTip('alertvisual','<?php echo _l('Visual alert type')?>');
		wCatAutoImport._tb.addText('txtsecs', 0, '<?php echo _l('sec  -  Alerts:')?>');
*/
		wCatAutoImport._tb.addText('txtsecs', 0, '<?php echo _l('sec')?>');
		wCatAutoImport._tb.addInput("importinterval", 0,60,30);
		wCatAutoImport._tb.setItemToolTip('importinterval','<?php echo _l('Launch import every X seconds if possible',1)?>');
		wCatAutoImport._tb.addText('txtinterval', 0, '<?php echo _l('Interval:',1)?>');
		wCatAutoImport._tb.addButtonTwoState("play", 0, "", "lib/img/control_play_blue.png", "lib/img/control_play_blue.png");
		wCatAutoImport._tb.setItemToolTip('play','<?php echo _l('Start',1)?>');
		wCatAutoImport._tb.attachEvent("onClick",
			function(id){
				if (id=='help'){
					<?php echo "window.open('".getHelpLink('csvimportauto')."');"; ?>
				}
				if (id=='stop'){
					stopAutoImport();
				}
			});
		wCatAutoImport._tb.attachEvent("onStateChange", function(id, state){
				if (id=='play'){
					if (state){
						startAutoImport();
					}else{
						stopAutoImport();
					}
				}
			});
		wCatAutoImport._tb.setListOptionSelected("alertsound", 0);
		wCatAutoImport._tb.setListOptionSelected("alertvisual", 0);
		wCatAutoImport.attachObject('alertbox');
	}else{
		wCatAutoImport.bringToTop();
	}
}

autoImportRunning=false; // check and auto import?
autoImportUnit=0; // counter
autoImportLastState=0; // 0 : nothing - 1 : waiting reply from server
autoImportTODOSize1=0; // Size of TODO file stored in var 1
autoImportTODOSize2=0; // Size of TODO file stored in var 2 to compare with autoImportTODOSize1


function startAutoImport()
{
	autoImportUnit=0;
	autoImportRunning=true;
	autoImportTODOSize1=0;
	autoImportTODOSize2=0;
	processAutoImport();
	displayProcess();
}

function stopAutoImport(showAlert)
{
	if (dhxWins.isWindow("wCatAutoImport"))
	{
		autoImportUnit=0;
		autoImportRunning=false;
		autoImportTODOSize1=0;
		autoImportTODOSize2=0;
		autoImportLastState=0;
		wCatAutoImport._tb.setItemState('play', false);
		if (showAlert){
			$('#alertbox').css('background-color','#FF0000');
			wCatAutoImport.setDimension(350, 168);
		}
	}
}

function processAutoImport()
{
	if (!dhxWins.isWindow("wCatAutoImport"))	stopAutoImport();
	if (!autoImportRunning) return 0;
	autoImportUnit++;
	if (autoImportUnit>=wCatAutoImport._tb.getValue('importinterval')*1){
		if(autoImportLastState==1 || (autoImportTODOSize1>0 && autoImportTODOSize1==autoImportTODOSize2)){ // still waiting reply OR TODO file didn't change
			stopAutoImport(true);
			return 0;
		}
		autoImportUnit=0;
		displayProcess();
	}
	setTimeout('processAutoImport()',1000);
}

function prepareNextStep(TODOFileSize)
{
	if (TODOFileSize==0)
	{
		stopAutoImport(true);
		return 0;
	}
	autoImportTODOSize2=autoImportTODOSize1;
	autoImportTODOSize1=TODOFileSize;
	autoImportLastState=0;
}

function stopAlert()
{
	$('#alertbox').css('background-color','#FFFFFF');
	wCatAutoImport.setDimension(350, 68);
}

function getTODOName(str)
{
	if (str.substr(0,str.length-9)=='.TODO.csv')
	{
		return str;
	}else{
		return str.substr(0,str.length-4)+'.TODO.csv';
	}
}

function getCheck()
{
	var selectedRow = wImport.gridFiles.getSelectedRowId();
	if(selectedRow!=undefined && selectedRow!="" && selectedRow!=null && selectedRow.search(",")<=0)
	{
		dhxlImport.cells('c').attachHTMLString('<br/><br/><center><img src="lib/img/loading.gif" alt="loading" title="loading" style="" /></center>');
		$.post('index.php?ajax=1&act=cat_win-import_check&id_lang='+SC_ID_LANG,{'mapping':mapping,'mappingname':wImport.tbMapping.getValue('saveas'),'mapppinggridlength':wImport.gridMapping.getRowsNum(),'filename':lastCSVFile,'importlimit':wImport.tbProcess.getValue('importlimit'),'create_categories':(1*create_categories)},function(data){
			dhxlImport.cells('c').attachHTMLString(data);
		});
	}
}

</script>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopAlert();">Click here to close alert.</div>
