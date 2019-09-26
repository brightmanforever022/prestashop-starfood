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
	lastCSVFileCus='';
	mappingCus='';
	arrayFieldLangCus=new Array();//('name');
	arrayFieldOptionCus=new Array();//('feature');
	var comboArrayCus = null;
	var comboValuesArrayCus = null;
	var optionLabelArrayCus = null;
	dhxlImportCus=wImportCus.attachLayout("3T");
	wImportCus._sb=dhxlImportCus.attachStatusBar();
	dhxlImportCus.cells('a').hideHeader();
	dhxlImportCus.cells('a').setHeight(200);
	wImportCus.tbOptions=dhxlImportCus.cells('a').attachToolbar();
	wImportCus.tbOptions.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	wImportCus.tbOptions.setItemToolTip('help','<?php echo _l('Help',1)?>');
	wImportCus.tbOptions.addButton("download", 0, "", "lib/img/table_go.png", "lib/img/table_go.png");
	wImportCus.tbOptions.setItemToolTip('download','<?php echo _l('Download selected file',1)?>');
	wImportCus.tbOptions.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wImportCus.tbOptions.setItemToolTip('delete','<?php echo _l('Delete marked files',1)?>');
	wImportCus.tbOptions.addButton("upload", 0, "", "lib/img/table_add.png", "lib/img/table_add.png");
	wImportCus.tbOptions.setItemToolTip('upload','<?php echo _l('Upload CSV file',1)?>');
	wImportCus.tbOptions.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wImportCus.tbOptions.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	wImportCus.tbOptions.attachEvent("onClick",
		function(id){
			if (id=='help')
			{
				<?php echo "window.open('".getHelpLink('csvimport')."');"; ?>
			}
			if (id=='refresh')
			{

				displayOptionsCus();
			}
			if (id=='download')
			{
				idxFilename=wImportCus.gridFiles.getColIndexById('filename');
				window.open("index.php?ajax=1&act=all_get-file&path=<?php echo (SC_INSTALL_MODE==0?SC_PS_PATH_ADMIN_REL.'import/':SC_CSV_IMPORT_DIR."customers/");?>&file="+wImportCus.gridFiles.cells(wImportCus.gridFiles.getSelectedRowId(),idxFilename).getValue());

			}
			if (id=='delete')
			{
				idxMarkedFile=wImportCus.gridFiles.getColIndexById('markedfile');
				filesList='';
				wImportCus.gridFiles.forEachRow(function(id){
					if (wImportCus.gridFiles.cells(id,idxMarkedFile).getValue()==true)
					{
						idxFilename=wImportCus.gridFiles.getColIndexById('filename');
						filesList+=wImportCus.gridFiles.cells(id,idxFilename).getValue()+';';
					}
					});
				$.post('index.php?ajax=1&act=cus_win-import_process&action=conf_delete',{'imp_opt_files':filesList},function(data){
						dhtmlx.message({text:data,type:'info'});
						displayOptionsCus();
					});
			}
			if (id=='upload')
			{
				if (!dhxWins.isWindow("wImportCusUpload"))
				{
//					wImportCus._uploadWindow = dhxWins.createWindow("wImportCusUpload", 50, 50, 396, 568);
					wImportCus._uploadWindow = dhxWins.createWindow("wImportCusUpload", 50, 50, 585, 400);
					wImportCus._uploadWindow.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
					wImportCus._uploadWindow.setText('<?php echo _l('Upload CSV files',1)?>');
					ll = new dhtmlXLayoutObject(wImportCus._uploadWindow, "1C");
					ll.cells('a').hideHeader();
					ll.cells('a').attachURL('index.php?ajax=1&act=cus_win-import_upload'+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					wImportCus._uploadWindow.attachEvent("onClose", function(win){
							win.hide();
							return false;
						});
				}else{
					ll.cells('a').attachURL('index.php?ajax=1&act=cus_win-import_upload'+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					wImportCus._uploadWindow.show();
					wImportCus._uploadWindow.bringToTop();
				}
			}
		});
		
	wImportCus.gridFiles=dhxlImportCus.cells('a').attachGrid();
	wImportCus.gridFiles.setImagePath("lib/js/imgs/");
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
	wImportCus.gridFiles.attachEvent("onRowSelect", function(id,ind){
			if (id!=lastCSVFileCus)
			{
				idxFilename=wImportCus.gridFiles.getColIndexById('filename');
				idxmappingCus=wImportCus.gridFiles.getColIndexById('mapping');
				idxLimit=wImportCus.gridFiles.getColIndexById('importlimit');
				wImportCus.tbProcess.setValue('importlimit',wImportCus.gridFiles.cells(id,idxLimit).getValue());
				filename=wImportCus.gridFiles.cells(id,idxFilename).getValue();
				mappingCus=wImportCus.gridFiles.cells(id,idxmappingCus).getValue();
				dhxlImportCus.cells('b').setText("<?php echo _l('mapping')?> "+filename);
				displaymappingCus(filename,mappingCus);
				lastCSVFileCus=id;
			}
		});
	wImportCus.gridFiles.attachEvent('onEditCell',function (stage,rId,cInd,nValue,oValue){
			idxfieldsep=wImportCus.gridFiles.getColIndexById('fieldsep');
			idxvaluesep=wImportCus.gridFiles.getColIndexById('valuesep');
			if (stage==2 && (cInd==idxfieldsep || cInd==idxvaluesep)){
				idxFilename=wImportCus.gridFiles.getColIndexById('filename');
				idxmappingCus=wImportCus.gridFiles.getColIndexById('mapping');
				filename=wImportCus.gridFiles.cells(rId,idxFilename).getValue();
				mappingCus=wImportCus.gridFiles.cells(rId,idxmappingCus).getValue();
				setTimeout("displaymappingCus('"+filename+"','"+mappingCus+"')",500);
			}
			return true;
		});
	wImportCus.gridFilesDataProcessor = new dataProcessor('index.php?ajax=1&act=cus_win-import_config_update');
	wImportCus.gridFilesDataProcessor.enableDataNames(true);
	wImportCus.gridFilesDataProcessor.enablePartialDataSend(true);
	wImportCus.gridFilesDataProcessor.setUpdateMode('cell',true);
	wImportCus.gridFilesDataProcessor.setDataColumns(Array(false,false,false,false,true,true,true,true,true,true,true,true,true));
<?php
	if (_s('CAT_NOTICE_EXPORT_SEPARATOR'))
	{
?>
	wImportCus.gridFilesDataProcessor.attachEvent("onBeforeUpdate",function(id,status){
			if (wImportCus.gridFiles.cells(id,6).getValue()==wImportCus.gridFiles.cells(id,7).getValue())
			{
				dhtmlx.message({text:'<?php echo _l('The field separator and the value separator could not be the same character.')?>',type:'error'});
				return false;
			}
			return true;
		});
<?php
	}
?>
	wImportCus.gridFilesDataProcessor.init(wImportCus.gridFiles);

	displayOptionsCus();//'wImportCus.gridFiles.splitAt(2);');

	dhxlImportCus.cells('b').setText("<?php echo _l('mapping')?>");
	dhxlImportCus.cells('b').setWidth(480);
	wImportCus.tbmappingCus=dhxlImportCus.cells('b').attachToolbar();
	wImportCus.tbmappingCus.addButton("load_by_name", 0, "", "lib/img/table_lightning.png", "lib/img/table_lightning.png");
	wImportCus.tbmappingCus.setItemToolTip('load_by_name','<?php echo _l('Load fields by name',1)?>');
	wImportCus.tbmappingCus.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wImportCus.tbmappingCus.setItemToolTip('delete','<?php echo _l('Delete mapping and reset grid')?>');
	wImportCus.tbmappingCus.addButton("saveasbtn", 0, "", "lib/img/table_save.png", "lib/img/table_save.png");
	wImportCus.tbmappingCus.setItemToolTip('saveasbtn','<?php echo _l('Save mapping')?>');
	wImportCus.tbmappingCus.addInput("saveas", 0,"",100);
	wImportCus.tbmappingCus.setItemToolTip('saveas','<?php echo _l('Save mapping as')?>');
	wImportCus.tbmappingCus.addText('txt_saveas', 0, '<?php echo _l('Save mapping as')?>');
	var opts = [
<?php
	@$files = array_diff( scandir( SC_CSV_IMPORT_DIR."customers/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
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
	wImportCus.tbmappingCus.addButtonSelect("loadmapping", 0, "<?php echo _l('Load')?>", opts, "lib/img/table_relationship.png", "lib/img/table_relationship.png",false,true);
	wImportCus.tbmappingCus.setItemToolTip('loadmapping','<?php echo _l('Load mapping')?>');
	wImportCus.tbmappingCus.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wImportCus.tbmappingCus.setItemToolTip('refresh','<?php echo _l('Refresh')?>');
	function onClickmappingCus(id){
			if (id.substr(0,11)=='loadmapping')
			{
				tmp=id.substr(11,id.length).replace('.map.xml','');
				wImportCus.tbmappingCus.setValue('saveas',tmp);
				$.get('index.php?ajax=1&act=cus_win-import_process&action=mapping_load&filename='+tmp,function(data){
						if (data!='')
						{
							mappingCus=data.split(';');
							wImportCus.gridmappingCus.forEachRow(function(id){
									if (wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),1).getValue()!='')
										for(var i=0; i < mappingCus.length; i++)
										{
											map=(mappingCus[i]).split(',');
											if (wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),1).getValue()==map[0])
											{

												
												wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),0).setValue("1");
												wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),2).setValue(map[1]);
												wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),3).setValue(map[2]);
											}
										}
								});
						}
						setOptionsBGColorCus();
					});
			}
			if (id=='refresh')
			{
				if (typeof filename=='undefined')return;
				if (typeof mappingCus=='undefined')
				{
					idxmappingCus=wImportCus.gridFiles.getColIndexById('mapping');
					mappingCus=wImportCus.gridFiles.cells(lastCSVFileCus,idxmappingCus).getValue();
					//if (mappingCus=='') return;
				}
				displaymappingCus(filename,mappingCus);
			}
			if (id=='saveasbtn')
			{
				if (wImportCus.tbmappingCus.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('mapping name should not be empty')?>',type:'error'});
				}else{
					var mappingCus='';
					wImportCus.gridmappingCus.forEachRow(function(id){
							if (wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),0).getValue()=="1")
							{
								mappingCus+=wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),1).getValue()+','+
												 wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),2).getValue()+','+
												 wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),3).getValue()+';';
							}
						});
					wImportCus.tbmappingCus.setValue('saveas',getLinkRewriteFromStringLight(wImportCus.tbmappingCus.getValue('saveas')));
					$.post('index.php?ajax=1&act=cus_win-import_process&action=mapping_saveas',{'filename':wImportCus.tbmappingCus.getValue('saveas'),'mapping':mappingCus},function(data){
								dhtmlx.message({text:data,type:'info'});
								if (!in_array('loadmapping'+wImportCus.tbmappingCus.getValue('saveas'),wImportCus.tbmappingCus.getAllListOptions('loadmapping')))
								{
									wImportCus.tbmappingCus.addListOption('loadmapping', 'loadmapping'+wImportCus.tbmappingCus.getValue('saveas'), 0, 'button', wImportCus.tbmappingCus.getValue('saveas'))
									wImportCus.tbmappingCus.setListOptionSelected('loadmapping', 'loadmapping'+wImportCus.tbmappingCus.getValue('saveas'));
								}
								displayOptionsCus();
							});
				}
			}
			if (id=='delete')
			{
				if (wImportCus.tbmappingCus.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('mapping name should not be empty')?>',type:'error'});
				}else{
					if (confirm('<?php echo _l('Do you want to delete the current mapping?',1)?>'))
						$.get('index.php?ajax=1&act=cus_win-import_process&action=mapping_delete&filename='+wImportCus.tbmappingCus.getValue('saveas'),function(data){
								wImportCus.gridmappingCus.clearAll(true);
								wImportCus.tbmappingCus.removeListOption('loadmapping', 'loadmapping'+wImportCus.tbmappingCus.getValue('saveas'));
								wImportCus.tbmappingCus.setValue('saveas','');
							});
				}
			}
			if (id=='load_by_name')
			{
				comboArrayCus = new Object();
				comboValuesArrayCus = new Object();
				optionLabelArrayCus = new Object();
				$.each(comboDBField.getKeys(), function(num, value){
					var label = comboDBField.get(value);
					if(label!=undefined && label!=null && label!="" && label!=0)
					{
						comboArrayCus[label] = value;
						comboValuesArrayCus[value] = value;

						if(in_array(value,arrayFieldOptionCus))
							optionLabelArrayCus[value] = label;
					}
				});
				
				idxFileField=wImportCus.gridmappingCus.getColIndexById('file_field');
				idxDBField=wImportCus.gridmappingCus.getColIndexById('db_field');
				idxOptions=wImportCus.gridmappingCus.getColIndexById('options');
				idxUse=wImportCus.gridmappingCus.getColIndexById('use');
				
				wImportCus.gridmappingCus.forEachRow(function(row_id){
					var name = $.trim(wImportCus.gridmappingCus.cells(row_id, idxFileField).getValue());
					var field = wImportCus.gridmappingCus.cells(row_id, idxDBField).getValue();

					if(name!=undefined && name!=null && name!="" && name!=0 && field!=undefined && (field==null || field=="" || field==0))
					{
						var check = false;
						var value = comboArrayCus[name];
						var value_bis = comboValuesArrayCus[name];
						if(value!=undefined && value!=null && value!="" && value!=0)
						{
							wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value);
							check = true;
						}
						else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
						{
							wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value_bis);
							value = value_bis;
							check = true;
						}
						else
						{
							var original_name = name;
							var lang = $.trim(name.slice(-2).toLowerCase());
							name = $.trim(name.substring(0, name.length - 3));
							var value = comboArrayCus[name];
							var value_bis = comboValuesArrayCus[name];
							if(value!=undefined && value!=null && value!="" && value!=0)
							{
								wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value);
								check = true;
							}
							else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
							{
								wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value_bis);
								value = value_bis;
								check = true;
							}
							else
							{
								var encoded_name = unescape(encodeURIComponent(name));
								var value = comboArrayCus[encoded_name];
								var value_bis = comboValuesArrayCus[encoded_name];
								if(value!=undefined && value!=null && value!="" && value!=0)
								{
									wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value);
									check = true;
								}
								else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
								{
									wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value_bis);
									value = value_bis;
									check = true;
								}
								else
								{
									var decoded_name = decodeURIComponent(unescape(name));
									var value = comboArrayCus[decoded_name];
									var value_bis = comboValuesArrayCus[decoded_name];
									if(value!=undefined && value!=null && value!="" && value!=0)
									{
										wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value);
										check = true;
									}
									else if(value_bis!=undefined && value_bis!=null && value_bis!="" && value_bis!=0)
									{
										wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(value_bis);
										value = value_bis;
										check = true;
									}
								}
							}

							if(in_array(value,arrayFieldLangCus))
							{
								wImportCus.gridmappingCus.cells(row_id, idxOptions).setValue(lang);
								onEditCellmappingCus(2,row_id, idxOptions,lang);
							}

							if(!check)
							{
								$.each(optionLabelArrayCus, function(id, label){
									var finded = false;
									var option = "";
									if(name.search(label)>=0)
									{
										finded = true;
										option = $.trim(name.replace(label+" ", ""));
									}
									else
									{
										var encoded_name = unescape(encodeURIComponent(name));
										if(encoded_name.search(label)>=0)
										{
											finded = true;
											option = $.trim(encoded_name.replace(label+" ", ""));
										}
										else
										{
											var decoded_name = decodeURIComponent(unescape(name));
											if(encoded_name.search(label)>=0)
											{
												finded = true;
												option = $.trim(decoded_name.replace(label+" ", ""));
											}
										}
									}

									if(finded)
									{
										wImportCus.gridmappingCus.cells(row_id, idxDBField).setValue(id);
										value = id;
										check = true;
										if(option!=undefined && option!=null && option!="")
											wImportCus.gridmappingCus.cells(row_id, idxOptions).setValue(option);
									}
								});
							}
						}

						if(check)
							onEditCellmappingCus(2,row_id, idxDBField,value);
					}
				});
			}
	}
	wImportCus.tbmappingCus.attachEvent("onClick",onClickmappingCus);
	wImportCus.gridmappingCus=dhxlImportCus.cells('b').attachGrid();
	wImportCus.gridmappingCus.setImagePath("lib/js/imgs/");
	function setOptionsBGColorCus()
	{
		idxMark=wImportCus.gridmappingCus.getColIndexById('use');
		idxDBField=wImportCus.gridmappingCus.getColIndexById('db_field');
		idxOptions=wImportCus.gridmappingCus.getColIndexById('options');
		wImportCus.gridmappingCus.forEachRow(function(rId){
			wImportCus.gridmappingCus.cells(rId,idxOptions).setBgColor(wImportCus.gridmappingCus.cells(rId,idxDBField).getBgColor());
			var flag=false;
			/*if (in_array(wImportCus.gridmappingCus.cells(rId,idxDBField).getValue(),arrayFieldLangCus))
			{
				wImportCus.gridmappingCus.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wImportCus.gridmappingCus.cells(rId,idxDBField).getValue()=='attribute' || wImportCus.gridmappingCus.cells(rId,idxDBField).getValue()=='attribute_multiple')
			{
				wImportCus.gridmappingCus.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}*/
<?php
	//sc_ext::readImportCSVConfigXML('importmappingCusPrepareGrid');
?>
			if (!flag) wImportCus.gridmappingCus.cells(rId,idxOptions).setValue('');
		});
	}
	function checkOptionsCus()
	{
		var flag=true;
		idxDBField=wImportCus.gridmappingCus.getColIndexById('db_field');
		idxOptions=wImportCus.gridmappingCus.getColIndexById('options');
		wImportCus.gridmappingCus.forEachRow(function(rId){
			if (wImportCus.gridmappingCus.cells(rId,0).getValue()=="1")
			{
				/*if (in_array(wImportCus.gridmappingCus.cells(rId,idxDBField).getValue(),arrayFieldLangCus)
							&& wImportCus.gridmappingCus.cells(rId,idxOptions).getValue()=='')
					flag=false;
				if (wImportCus.gridmappingCus.cells(rId,idxDBField).getValue()=='feature'
							&& wImportCus.gridmappingCus.cells(rId,idxOptions).getValue()=='')
					flag=false;*/
<?php
	//sc_ext::readImportCSVConfigXML('importmappingCusCheckGrid');
?>
			}
		});
		return flag;
	}
	function onEditCellmappingCus(stage,rId,cInd,nValue,oValue){
		if(stage==1 && (cInd==2 || cInd==3)){ 
  	  var editor = this.editor; 
	    var pos = this.getPosition(editor.cell);        
    	var y = document.body.offsetHeight-pos[1];   
    	if(y < editor.list.offsetHeight)       
		    editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';   
    }
		idxMark=wImportCus.gridmappingCus.getColIndexById('use');
		idxDBField=wImportCus.gridmappingCus.getColIndexById('db_field');
		idxOptions=wImportCus.gridmappingCus.getColIndexById('options');
		comboDBField = wImportCus.gridmappingCus.getCombo(idxOptions);
		if (cInd == idxDBField && nValue != oValue){
			wImportCus.gridmappingCus.cells(rId,idxMark).setValue(1);
			setOptionsBGColorCus();
		}
		if (cInd == idxOptions)
		{
			comboDBField.clear();
			/*if (in_array(wImportCus.gridmappingCus.cells(rId,idxDBField).getValue(),arrayFieldLangCus))
			{
<?php
	foreach($languages AS $lang)
	{ 
		echo '				comboDBField.put("'.$lang['iso_code'].'","'.$lang['iso_code'].'");';
	}
?>
				return true;
			}
			if (wImportCus.gridmappingCus.cells(rId,idxDBField).getValue()=='feature' || wImportCus.gridmappingCus.cells(rId,idxDBField).getValue()=='feature_custom')
			{
<?php
	$features=Feature::getFeatures($sc_agent->id_lang);
	foreach($features AS $feature)
	{ 
		echo '				comboDBField.put("'.addslashes($feature['name']).'","'.addslashes($feature['name']).'");';
	}
?>
				return true;
			}*/
<?php
	//sc_ext::readImportCSVConfigXML('importmappingCusFillCombo');
?>
			return false;
		}
		return true;
	}
	wImportCus.gridmappingCus.attachEvent('onEditCell',onEditCellmappingCus);
	
	dhxlImportCus.cells('c').setText("<?php echo _l('Process')?>");
//	dhxlImportCus.cells('c').vs["def"].dhxcont.mainCont["def"].style.overflow = "auto";
	
	wImportCus.tbProcess=dhxlImportCus.cells('c').attachToolbar();
	var create_categories=false;
	wImportCus.tbProcess.addButton("loop_tool", 0, "", "lib/img/clock.png", "lib/img/clock.png");
	wImportCus.tbProcess.setItemToolTip('loop_tool','<?php echo _l('Auto-import tool')?>');
	wImportCus.tbProcess.addButton("go_process", 0, "", "lib/img/database_go.png", "lib/img/database_go.png");
	wImportCus.tbProcess.setItemToolTip('go_process','<?php echo _l('Import data')?>');
	wImportCus.tbProcess.addInput("importlimit", 0,500,30);
	wImportCus.tbProcess.setItemToolTip('importlimit','<?php echo _l('Number of the first lines to import from the CSV file')?>');
	wImportCus.tbProcess.addText('txtimportlimit', 0, '<?php echo _l('Lines to import')._l(':')?>');
	wImportCus.tbProcess.attachEvent("onStateChange", function(id,state){
		});
	wImportCus.tbProcess.attachEvent("onClick",
		function(id){
			if (id=='go_process')
			{
				if (!autoImportCusRunning){
					displayProcessCus();
				}else{
					dhtmlx.message({text:'<?php echo _l('AutoImport already running')?>',type:'error'});
				}
			}
			if (id=='loop_tool')
			{
				displayAutoImportToolCus();
			}
		});

	
//#####################################
//############ Load functions
//#####################################

function displayOptionsCus(callback)
{
	wImportCus.gridFiles.clearAll(true);
	wImportCus.gridFiles.loadXML("index.php?ajax=1&act=cus_win-import_config_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
			if (callback)
			{
				eval(callback);
			}else if(lastCSVFileCus!=''){
				wImportCus.gridFiles.selectRowById(lastCSVFileCus);
			}
			});
}

function displaymappingCus(filename,mappingCus)
{
	wImportCus.gridmappingCus.clearAll(true);
	wImportCus.gridmappingCus.loadXML("index.php?ajax=1&act=cus_win-import_mapping_get&id_lang="+SC_ID_LANG+"&imp_opt_file="+filename+"&"+new Date().getTime(),function()
			{
				idxDBField=wImportCus.gridmappingCus.getColIndexById('db_field');
				comboDBField = wImportCus.gridmappingCus.getCombo(idxDBField);
				comboDBField.clear();
<?php
	global $array;
	$array=array();
	// CUSTOMER
	$array[_l('Gender',1)]="comboDBField.put('id_gender','"._l('Gender',1)."');";
	$array[_l('Company',1)]="comboDBField.put('company','"._l('Company',1)."');";
	$array[_l('Siret',1)]="comboDBField.put('siret','"._l('Siret',1)."');";
	$array[_l('APE',1)]="comboDBField.put('ape','"._l('APE',1)."');";
	$array[_l('Firstname',1)]="comboDBField.put('firstname','"._l('Firstname',1)."');";
	$array[_l('Lastname',1)]="comboDBField.put('lastname','"._l('Lastname',1)."');";
	$array[_l('Email',1)]="comboDBField.put('email','"._l('Email',1)."');";
	$array[_l('Password',1)]="comboDBField.put('passwd','"._l('Password',1)."');";
	$array[_l('Birthday',1)]="comboDBField.put('birthday','"._l('Birthday',1)."');";
	$array[_l('Newsletter',1)]="comboDBField.put('newsletter','"._l('Newsletter',1)."');";
	$array[_l('Opt-in (Partners offers)',1)]="comboDBField.put('optin','"._l('Opt-in (Partners offers)',1)."');";
	$array[_l('Active',1)]="comboDBField.put('active','"._l('Active',1)."');";
	$array[_l('id_customer',1)]="comboDBField.put('id_customer','"._l('id_customer',1)."');";
	$array[_l('id_address',1)]="comboDBField.put('id_address','"._l('id_address',1)."');";
	$array[_l('Date of account creation',1)]="comboDBField.put('date_add','"._l('Date of account creation',1)."');";
	if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
		$array[_l('Default group',1)]="comboDBField.put('id_default_group','"._l('Default group',1)."');";
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		//$array[_l('id_shop',1)]="comboDBField.put('id_shop','"._l('id_shop',1)."');";
		$array[_l('Private notes',1)]="comboDBField.put('note','"._l('Private notes',1)."');";
		$array[_l('Website',1)]="comboDBField.put('website','"._l('Website',1)."');";
	}
	
	// ADDRESS
	$array[_l('Address',1).' - '._l('title',1)]="comboDBField.put('address_title','"._l('Address',1).' - '._l('title',1)."');";
	$array[_l('Address',1).' - '._l('country',1)]="comboDBField.put('address_country','"._l('Address',1).' - '._l('country',1)."');";
	$array[_l('Address',1).' - '._l('state',1)]="comboDBField.put('address_state','"._l('Address',1).' - '._l('state',1)."');";
	$array[_l('Address',1).' - '._l('company',1)]="comboDBField.put('address_company','"._l('Address',1).' - '._l('company',1)."');";
	$array[_l('Address',1).' - '._l('lastname',1)]="comboDBField.put('address_lastname','"._l('Address',1).' - '._l('lastname',1)."');";
	$array[_l('Address',1).' - '._l('firstname',1)]="comboDBField.put('address_firstname','"._l('Address',1).' - '._l('firstname',1)."');";
	$array[_l('Address',1).' - '._l('address 1',1)]="comboDBField.put('address_1','"._l('Address',1).' - '._l('address 1',1)."');";
	$array[_l('Address',1).' - '._l('address 2',1)]="comboDBField.put('address_2','"._l('Address',1).' - '._l('address 2',1)."');";
	$array[_l('Address',1).' - '._l('postcode',1)]="comboDBField.put('address_postcode','"._l('Address',1).' - '._l('postcode',1)."');";
	$array[_l('Address',1).' - '._l('city',1)]="comboDBField.put('address_city','"._l('Address',1).' - '._l('city',1)."');";
	$array[_l('Address',1).' - '._l('other',1)]="comboDBField.put('address_other','"._l('Address',1).' - '._l('other',1)."');";
	$array[_l('Address',1).' - '._l('phone',1)]="comboDBField.put('address_phone','"._l('Address',1).' - '._l('phone',1)."');";
	$array[_l('Address',1).' - '._l('phone mobile',1)]="comboDBField.put('address_phonemobile','"._l('Address',1).' - '._l('phone mobile',1)."');";
	$array[_l('Address',1).' - '._l('VAT Number',1)]="comboDBField.put('address_vat_number','"._l('Address',1).' - '._l('VAT Number',1)."');";

	// GROUPS
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
		$array[_l('Groups',1)]="comboDBField.put('groups','"._l('Groups',1)."');";
	
	// ACTIONS
	$array[' '._l('Action: Delete all customers',1)]="comboDBField.put('ActionDeleteAllCustomers','"._l('Action: Delete all customers',1)."');";
	$array[' '._l('Action: Delete all addresses',1)]="comboDBField.put('ActionDeleteAllAddresses','"._l('Action: Delete all addresses',1)."');";
	$array[' '._l('Action: Regenerate all passwords',1)]="comboDBField.put('ActionRegenerateAllPasswords','"._l('Action: Regenerate all passwords',1)."');";
	
	//sc_ext::readImportCSVConfigXML('definition');

	// Sort Alpha
	$arraybis = $array;
	foreach($arraybis as $k=>$v)
	{
		unset($array[$k]);
		$array[strtolower($k)]=$v;
	}
	
	ksort($array);
	echo join("\n",$array);
?>
				if (mappingCus!='')
				{
					onClickmappingCus('loadmapping'+mappingCus);
				}else{
					onClickmappingCus('loadmapping'+filename.replace('.csv','').replace('.CSV',''));
				}
			});
}

function displayProcessCus()
{
	var mappingCus='';
	if (!checkOptionsCus() || lastCSVFileCus=='')
	{
		dhtmlx.message({text:'<?php echo _l('Some options are missing')?>',type:'error'});
		return false;
	}
	wImportCus.gridmappingCus.forEachRow(function(id){
			if (wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),0).getValue()=="1")
			{
				mappingCus+=wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),1).getValue()+','+
								 wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),2).getValue()+','+
								 wImportCus.gridmappingCus.cells(wImportCus.gridmappingCus.getRowIndex(id),3).getValue()+';';
			}
		});
	mappingCus=mappingCus.substr(0,mappingCus.length-1);
	autoImportCusLastState=1;
	wImportCus.setIcon('lib/img/ajax-loader16.gif','../../../lib/img/ajax-loader16.gif');
	$.post('index.php?ajax=1&act=cus_win-import_process&action=mapping_process',{'mapping':mappingCus,'id_lang_sc':SC_ID_LANG,'filename':lastCSVFileCus,'importlimit':wImportCus.tbProcess.getValue('importlimit'),'create_categories':(1*create_categories)},function(data){
			dhxlImportCus.cells('c').attachHTMLString(data);
			wImportCus.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
		});
	setTimeout("displayOptionsCus('wImportCus.gridFiles.selectRowById(getTODOName(lastCSVFileCus), false, true, false)');",500);
}

function displayAutoImportToolCus()
{
	if (!dhxWins.isWindow("wCusAutoImport"))
	{
		wCusAutoImport = dhxWins.createWindow("wCusAutoImport", 550, 350, 220, 68);
		wCusAutoImport.setMinDimension(220, 68);
		wCusAutoImport.setIcon('lib/img/clock.png','../../../lib/img/clock.png');
		wCusAutoImport.setText("<?php echo _l('Auto-import tool')?>");
		wCusAutoImport.button('park').hide();
		wCusAutoImport.button('minmax').hide();
		wCusAutoImport._tb=wCusAutoImport.attachToolbar();
		wCusAutoImport._tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
		wCusAutoImport._tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
/*
		var opts = [
								['0', 'obj', '<?php echo _l('No alert'); ?>', ''],
								['1', 'obj', '<?php echo _l('Sound 1'); ?>', '']
							 ];
		wCusAutoImport._tb.addButtonSelect("alertsound", 0, "", opts, "lib/img/sound.png", "lib/img/sound.png",true,true);
		wCusAutoImport._tb.setItemToolTip('alertsound','<?php echo _l('Sound alert type')?>');
		var opts = [
								['0', 'obj', '<?php echo _l('No alert'); ?>', ''],
								['1', 'obj', '<?php echo _l('Browser popup'); ?>', ''],
								['2', 'obj', '<?php echo _l('Red screen'); ?>', '']
							 ];
		wCusAutoImport._tb.addButtonSelect("alertvisual", 0, "", opts, "lib/img/eye.png", "lib/img/eye.png",true,true);
		wCusAutoImport._tb.setItemToolTip('alertvisual','<?php echo _l('Visual alert type')?>');
		wCusAutoImport._tb.addText('txtsecs', 0, '<?php echo _l('sec  -  Alerts:')?>');
*/
		wCusAutoImport._tb.addText('txtsecs', 0, '<?php echo _l('sec')?>');
		wCusAutoImport._tb.addInput("importinterval", 0,60,30);
		wCusAutoImport._tb.setItemToolTip('importinterval','<?php echo _l('Launch import every X seconds if possible',1)?>');
		wCusAutoImport._tb.addText('txtinterval', 0, '<?php echo _l('Interval:',1)?>');
		wCusAutoImport._tb.addButtonTwoState("play", 0, "", "lib/img/control_play_blue.png", "lib/img/control_play_blue.png");
		wCusAutoImport._tb.setItemToolTip('play','<?php echo _l('Start',1)?>');
		wCusAutoImport._tb.attachEvent("onClick",
			function(id){
				if (id=='help'){
					<?php echo "window.open('".getHelpLink('csvimportauto')."');"; ?>
				}
				if (id=='stop'){
					stopAutoImportCus();
				}
			});
		wCusAutoImport._tb.attachEvent("onStateChange", function(id, state){
				if (id=='play'){
					if (state){
						startAutoImportCus();
					}else{
						stopAutoImportCus();
					}
				}
			});
		wCusAutoImport._tb.setListOptionSelected("alertsound", 0);
		wCusAutoImport._tb.setListOptionSelected("alertvisual", 0);
		wCusAutoImport.attachObject('alertbox');
	}else{
		wCusAutoImport.bringToTop();
	}
}

autoImportCusRunning=false; // check and auto import?
autoImportCusUnit=0; // counter
autoImportCusLastState=0; // 0 : nothing - 1 : waiting reply from server
autoImportCusTODOSize1=0; // Size of TODO file stored in var 1
autoImportCusTODOSize2=0; // Size of TODO file stored in var 2 to compare with autoImportCusTODOSize1


function startAutoImportCus()
{
	autoImportCusUnit=0;
	autoImportCusRunning=true;
	autoImportCusTODOSize1=0;
	autoImportCusTODOSize2=0;
	processAutoImportCus();
	displayProcessCus();
}

function stopAutoImportCus(showAlert)
{
	if (dhxWins.isWindow("wCusAutoImport"))
	{
		autoImportCusUnit=0;
		autoImportCusRunning=false;
		autoImportCusTODOSize1=0;
		autoImportCusTODOSize2=0;
		autoImportCusLastState=0;
		wCusAutoImport._tb.setItemState('play', false);
		if (showAlert){
			$('#alertbox').css('background-color','#FF0000');
			wCusAutoImport.setDimension(350, 168);
		}
	}
}

function processAutoImportCus()
{
	if (!dhxWins.isWindow("wCusAutoImport"))	stopAutoImportCus();
	if (!autoImportCusRunning) return 0;
	autoImportCusUnit++;
	console.log(autoImportCusUnit+" / "+wCusAutoImport._tb.getValue('importinterval')+" / "+autoImportCusLastState);
	if (autoImportCusUnit==wCusAutoImport._tb.getValue('importinterval')*1){
		if(autoImportCusLastState==1 || (autoImportCusTODOSize1>0 && autoImportCusTODOSize1==autoImportCusTODOSize2)){ // still waiting reply OR TODO file didn't change
			stopAutoImportCus(true);
			return 0;
		}
		autoImportCusUnit=0;
		displayProcessCus();
	}
	setTimeout('processAutoImportCus()',1000);
}

function prepareNextStepCus(TODOFileSize)
{
	if (TODOFileSize==0)
	{
		stopAutoImportCus(true);
		return 0;
	}
	autoImportCusTODOSize2=autoImportCusTODOSize1;
	autoImportCusTODOSize1=TODOFileSize;
	autoImportCusLastState=0;
}

function stopAlertCus()
{
	$('#alertbox').css('background-color','#FFFFFF');
	wCusAutoImport.setDimension(350, 68);
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

</script>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopAlertCus();">Click here to close alert.</div>
