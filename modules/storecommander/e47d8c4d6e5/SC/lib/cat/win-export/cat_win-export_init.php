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
	mapping='';
	lastScriptFile='';
	arrayFieldLang=Array('name','name_with_attributes','description','description_short','meta_title','meta_description','meta_keywords','link_rewrite','available_now','available_later','availability_message','tags','link_to_cover_image','link_to_product','category_default','category_default_full_path','category_full_path','categories','link_to_image01','link_to_image02','link_to_image03','link_to_image04','link_to_image05','link_to_image06','link_to_image07','link_to_image08','link_to_image09','link_to_image10','image_link','image_url','image_legend','links_to_all_images','urls_to_all_images','links_to_all_images_for_product','urls_to_all_images_for_product','attribute','feature'<?php echo sc_ext::readExportCSVConfigXML('definitionLang');?>);
	dhxlExport=wExport.attachLayout("4I");
	wExport._sb=dhxlExport.attachStatusBar();
	dhxlExport.cells('a').hideHeader();
	dhxlExport.cells('a').setHeight(200);
	wExport.tbOptions=dhxlExport.cells('a').attachToolbar();
	wExport.tbOptions.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	wExport.tbOptions.setItemToolTip('help','<?php echo _l('Help',1)?>');
//	wExport.tbOptions.addButton("findfromweb", 0, "", "lib/img/script_palette.png", "lib/img/script_palette.png");
//	wExport.tbOptions.setItemToolTip('findfromweb','<?php echo _l('Find scripts on StoreCommander.com',1)?>');
//	wExport.tbOptions.addButton("duplicate", 0, "", "lib/img/page_copy2.png", "lib/img/page_copy2.png");
//	wExport.tbOptions.setItemToolTip('duplicate','<?php echo _l('Duplicate selected file',1)?>');
	wExport.tbOptions.addButton("delete", 0, "", "lib/img/script_delete.png", "lib/img/script_delete.png");
	wExport.tbOptions.setItemToolTip('delete','<?php echo _l('Delete marked files',1)?>');
	wExport.tbOptions.addButton("add", 0, "", "lib/img/script_add.png", "lib/img/script_add.png");
	wExport.tbOptions.setItemToolTip('add','<?php echo _l('Create new export script',1)?>');
	wExport.tbOptions.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wExport.tbOptions.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	wExport.tbOptions.attachEvent("onClick",
		function(id){
			if (id=='help')
			{
				<?php echo "window.open('".getHelpLink('csvexport')."');"; ?>
			}
			if (id=='refresh')
			{
				displayExportOptions();
			}
			if (id=='duplicate')
			{
				idxFilename=wExport.gridFiles.getColIndexById('filename');
//				window.open("index.php?ajax=1&act=all_get-file&path=../import/&file="+wExport.gridFiles.cells(wExport.gridFiles.getSelectedRowId(),idxFilename).getValue());
			}
			if (id=='add')
			{
				scriptname=prompt('<?php echo _l('New script name:',1); ?>','myScript');
				scriptname=replaceAll(" ","_",scriptname);
				if (scriptname!='')
					$.get('index.php?ajax=1&act=cat_win-export_process&action=conf_add&scriptname='+scriptname.replace('\'',''),function(data){
							displayExportOptions();
						});
			}
			if (id=='delete')
			{
				idxMarkedFile=wExport.gridFiles.getColIndexById('markedfile');
				filesList='';
				wExport.gridFiles.forEachRow(function(id){
					if (wExport.gridFiles.cells(id,idxMarkedFile).getValue()==true)
					{
						idxFilename=wExport.gridFiles.getColIndexById('filename');
						filesList+=wExport.gridFiles.cells(id,idxFilename).getValue()+';';
					}
					});
				$.post('index.php?ajax=1&act=cat_win-export_process&action=conf_delete',{'exp_opt_files':filesList},function(data){
						dhtmlx.message({text:data,type:'info'});
						displayExportOptions();
					});
			}
		});
		
	wExport.gridFiles=dhxlExport.cells('a').attachGrid();
	wExport.gridFiles.setImagePath("lib/js/imgs/");
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
	wExport.gridFiles.attachEvent("onRowSelect", function(id,ind){
			if (id!=lastScriptFile)
			{
				idxFilename=wExport.gridFiles.getColIndexById('filename');
				idxMapping=wExport.gridFiles.getColIndexById('mapping');
				idxCategSelection=wExport.gridFiles.getColIndexById('categoriessel');
				filename=wExport.gridFiles.cells(id,idxFilename).getValue();
				mapping=wExport.gridFiles.cells(id,idxMapping).getValue();
				categselection=wExport.gridFiles.cells(id,idxCategSelection).getValue();
				if (mapping!='')
				{
					dhxlExport.cells('b').setText("<?php echo _l('Mapping').' '._l('Export')?> "+mapping);
					wExport.tbMapping.setValue('saveas',mapping.replace('.map.xml',''));
					displayExportMapping(mapping);
				}else{
					dhxlExport.cells('b').setText("<?php echo _l('Mapping').' '._l('Export')?> ");
					wExport.tbMapping.setValue('saveas','');
					wExport.gridMapping.clearAll();
				}
				if (categselection!='' && categselection!='all' && categselection!='all_enabled' && categselection!='all_disabled')
				{
					dhxlExport.cells('c').setText("<?php echo _l('Categories selection')?> "+categselection);
					wExport.tbCategories.setValue('saveas',categselection.replace('.sel.xml',''));
					displayExportCategories(categselection);
				}else{
					dhxlExport.cells('c').setText("<?php echo _l('Categories selection')?>");
					wExport.tbCategories.setValue('saveas','');
					displayExportCategories();
				}
				lastScriptFile=id;
			}
		getExportCheck();
		});
	wExport.gridFilesDataProcessor = new dataProcessor('index.php?ajax=1&act=cat_win-export_config_update');
	wExport.gridFilesDataProcessor.enableDataNames(true);
	wExport.gridFilesDataProcessor.enablePartialDataSend(true);
	wExport.gridFilesDataProcessor.setUpdateMode('cell',true);
	wExport.gridFilesDataProcessor.setDataColumns(Array(false,false,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true,true));
	wExport.gridFilesDataProcessor.init(wExport.gridFiles);

	displayExportOptions();

	dhxlExport.cells('b').setText("<?php echo _l('Mapping').' '._l('Export')?>");
	dhxlExport.cells('b').setWidth(640);
	wExport.tbMapping=dhxlExport.cells('b').attachToolbar();
	wExport.tbMapping.addButton("loadallforimport", 0, "", "lib/img/database_go.png", "lib/img/database_go.png");
	wExport.tbMapping.setItemToolTip('loadallforimport','<?php echo _l('Load all fields for import',1)?>');
	wExport.tbMapping.addButton("loadall", 0, "", "lib/img/table_lightning.png", "lib/img/table_lightning.png");
	wExport.tbMapping.setItemToolTip('loadall','<?php echo _l('Load all fields',1)?>');
	wExport.tbMapping.addButton("fielddelete", 0, "", "lib/img/table_row_delete.png", "lib/img/table_row_delete.png");
	wExport.tbMapping.setItemToolTip('fielddelete','<?php echo _l('Delete item',1)?>');
	wExport.tbMapping.addButton("fieldinsert", 0, "", "lib/img/table_row_insert.png", "lib/img/table_row_insert.png");
	wExport.tbMapping.setItemToolTip('fieldinsert','<?php echo _l('Insert item',1)?>');
	wExport.tbMapping.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning.png");
	wExport.tbMapping.setItemToolTip('selectall','<?php echo _l('Select all',1)?>');
	wExport.tbMapping.addSeparator("sep001",0);
	wExport.tbMapping.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wExport.tbMapping.setItemToolTip('delete','<?php echo _l('Delete mapping and reset grid',1)?>');
	wExport.tbMapping.addButton("saveasbtn", 0, "", "lib/img/table_save.png", "lib/img/table_save.png");
	wExport.tbMapping.setItemToolTip('saveasbtn','<?php echo _l('Save mapping',1)?>');
	wExport.tbMapping.addInput("saveas", 0,"",100);
	wExport.tbMapping.setItemToolTip('saveas','<?php echo _l('Save mapping as',1)?>');
	wExport.tbMapping.addText('txt_saveas', 0, '<?php echo _l('Save mapping as',1)?>');
	var opts = [
<?php
	$files = array_diff( scandir( SC_TOOLS_DIR.'cat_export/' ), array_merge( Array( ".", "..", "index.php", ".htaccess")) );
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
	wExport.tbMapping.addButtonSelect("loadmapping", 0, "<?php echo _l('Load')?>", opts, "lib/img/table_relationship.png", "lib/img/table_relationship.png",false,true);
	wExport.tbMapping.setItemToolTip('loadmapping','<?php echo _l('Load mapping',1)?>');
	wExport.tbMapping.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wExport.tbMapping.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	function onClickMappingExport(id){
			if (id.substr(0,11)=='loadmapping')
			{
				tmp=id.substr(11,id.length).replace('.map.xml','');
				displayExportMapping(tmp);
				wExport.tbMapping.setValue('saveas',tmp);
			}
			if (id=='refresh')
			{
				if (typeof filename=='undefined')return;
				if (typeof mapping=='undefined')
				{
					idxMapping=wExport.gridFiles.getColIndexById('mapping');
					mapping=wExport.gridFiles.cells(lastScriptFile,idxMapping).getValue();
					if (mapping=='') return;
				}
				displayExportMapping(mapping);
			}
			if (id=='selectall')
			{
				wExport.gridMapping.selectAll();
			}
			if (id=='loadall')
			{
				if(wExport.gridMapping.getAllRowIds()!="")
				{
					if(confirm('<?php echo _l('Are you sure that you want load all the fields in this mapping?',1)?>'))
						loadAllFieldsInMapping();
				}
				else
					loadAllFieldsInMapping();
			}
			if (id=='loadallforimport')
			{
				if(wExport.gridMapping.getAllRowIds()!="")
				{
					if(confirm('<?php echo _l('Are you sure that you want load all the fields in this mapping?',1)?>'))
						loadAllFieldsInMapping(true);
				}
				else
					loadAllFieldsInMapping(true);
			}
			if (id=='saveasbtn')
			{
				if (wExport.tbMapping.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Mapping name should not be empty',1)?>',type:'error'});
				}
				else if (checkOptions()==false)
				{
					dhtmlx.message({text:'<?php echo _l('Some options are missing')?>',type:'error'});
				}
				else{
					var mapping='';
					wExport.gridMapping.forEachRow(function(id){
								mapping+=id+'|||'+
												 wExport.gridMapping.getRowIndex(id)+'|||'+
												 wExport.gridMapping.cells(id,0).getValue()+'|||'+
												 wExport.gridMapping.cells(id,1).getValue()+'|||'+
												 wExport.gridMapping.cells(id,2).getValue()+'|||'+
												 wExport.gridMapping.cells(id,3).getValue()+'|||'+
												 wExport.gridMapping.cells(id,4).getValue()+'|||'+
												 wExport.gridMapping.cells(id,5).getValue()+';';
						});
					wExport.tbMapping.setValue('saveas',getLinkRewriteFromStringLightWithCase(wExport.tbMapping.getValue('saveas')));
					$.post('index.php?ajax=1&act=cat_win-export_mapping_update&action=mapping_saveas',{'filename':wExport.tbMapping.getValue('saveas'),'mapping':mapping},function(data){
								if (!in_array('loadmapping'+wExport.tbMapping.getValue('saveas'),wExport.tbMapping.getAllListOptions('loadmapping')))
								{
									wExport.tbMapping.addListOption('loadmapping', 'loadmapping'+wExport.tbMapping.getValue('saveas'), 0, 'button', wExport.tbMapping.getValue('saveas'))
									wExport.tbMapping.setListOptionSelected('loadmapping', 'loadmapping'+wExport.tbMapping.getValue('saveas'));
								}
								dhtmlx.message({text:'<?php echo _l('Data saved!',1)?>',type:'info'});
								displayExportOptions();
							});
				}
			}
			if (id=='delete')
			{
				if (wExport.tbMapping.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Mapping name should not be empty',1)?>',type:'error'});
				}else{
					if (confirm('<?php echo _l('Delete mapping and reset grid',1)._l('?')?>'))
						$.get('index.php?ajax=1&act=cat_win-export_mapping_update&action=mapping_delete&filename='+wExport.tbMapping.getValue('saveas'),function(data){
							wExport.gridMapping.clearAll();
							wExport.tbMapping.removeListOption('loadmapping', 'loadmapping'+wExport.tbMapping.getValue('saveas'));
							wExport.tbMapping.setValue('saveas','');
							});
				}
			}
			if (id=='fieldinsert')
			{
				var ii=0;
				var nb=prompt('<?php echo _l('Create # lines',1)._l(':')?>')*1;
				
				wExport.gridMapping.filterBy(0,""); //unfilter
				wExport.gridMapping._f_rowsBuffer = null; //clear cache
				
				for(var j=1;j<=nb;j++)
				{
					var newId = (new Date()).valueOf()+ii;
					wExport.gridMapping.addRow(newId,[0,'','','','']);
					ii++;
				}
				
				wExport.gridMapping.filterByAll();  //reset filters back
			}
			if (id=='fielddelete')
			{
				if (confirm('<?php echo _l('Delete selected item',1)._l('?')?>'))
				{
					wExport.gridMapping.filterBy(0,""); //unfilter
					wExport.gridMapping._f_rowsBuffer = null; //clear cache
					
					var ids = wExport.gridMapping.getSelectedRowId();
					if(ids!="" && ids!=null)
					{
						ids = ids.split("|");
						$.each(ids,function(rid){
							wExport.gridMapping.deleteRow(rid);
						});
						
					}
					wExport.gridMapping.deleteSelectedRows();
					
					wExport.gridMapping.filterByAll();  //reset filters back
				}
			}
	}
	wExport.tbMapping.attachEvent("onClick",onClickMappingExport);
	wExport.gridMapping=dhxlExport.cells('b').attachGrid();
	wExport.gridMapping._name='exportGrid';
	wExport.gridMapping.setImagePath("lib/js/imgs/");
	wExport.gridMapping.enableMultiselect(true);
	wExport.gridMapping.enableDragAndDrop(true);
	wExport.gridMapping.attachEvent("onDragIn",function(idsource,idtarget,sourceobject,targetobject){
			if (sourceobject._name=="exportGrid") return true;
			return false;
		});

	displayExportMapping('');
	function setExportOptionsBGColor()
	{
		idxMark=wExport.gridMapping.getColIndexById('use');
		idxDBField=wExport.gridMapping.getColIndexById('name');
		idxOptions=wExport.gridMapping.getColIndexById('options');
		idxLang=wExport.gridMapping.getColIndexById('lang');
		wExport.gridMapping.forEachRow(function(rId){
			wExport.gridMapping.cells(rId,idxOptions).setBgColor(wExport.gridMapping.cells(rId,idxDBField).getBgColor());
			wExport.gridMapping.cells(rId,idxLang).setBgColor(wExport.gridMapping.cells(rId,idxDBField).getBgColor());
			var flag=false;
			if (in_array(wExport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang))
			{
				wExport.gridMapping.cells(rId,idxLang).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='feature'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='priceinctaxwithshipping')
			{
				wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='wholesale_price'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_reference')
			{
				wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php if(SCAS) { ?>
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity' 
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='location'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_physical'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_usable'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_real'
			)
			{
				wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php } ?>
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='attribute' || wExport.gridMapping.cells(rId,idxDBField).getValue()=='attribute_multiple')
			{
				wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				flag=true;
			}
			<?php echo sc_ext::readExportCSVConfigXML('exportMappingPrepareGrid');?>
			if (!flag)
			{
				wExport.gridMapping.cells(rId,idxOptions).setValue('');
				wExport.gridMapping.cells(rId,idxLang).setValue('');
			}
		});
	}
	function checkExportOptions()
	{
		var flag=true;
		idxDBField=wExport.gridMapping.getColIndexById('name');
		idxOptions=wExport.gridMapping.getColIndexById('options');
		idxLang=wExport.gridMapping.getColIndexById('lang');
		wExport.gridMapping.forEachRow(function(rId){
			if (wExport.gridMapping.cells(rId,0).getValue()=="1")
			{
				if (in_array(wExport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang)
							&& wExport.gridMapping.cells(rId,idxLang).getValue()=='')
					flag=false;
				if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='feature'
							&& wExport.gridMapping.cells(rId,idxOptions).getValue()=='')
					flag=false;
				<?php echo sc_ext::readExportCSVConfigXML('exportMappingCheckGrid');?>
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
		idxMark=wExport.gridMapping.getColIndexById('use');
		idxDBField=wExport.gridMapping.getColIndexById('name');
		idxOptions=wExport.gridMapping.getColIndexById('options');
		idxLang=wExport.gridMapping.getColIndexById('lang');
		comboDBField = wExport.gridMapping.getCombo(idxOptions);
		comboDBFieldLang = wExport.gridMapping.getCombo(idxLang);
		comboPriceIncTaxWithShipping = wExport.gridMapping.getCombo(idxOptions);
		if (cInd == idxDBField && nValue != oValue){
			wExport.gridMapping.cells(rId,idxMark).setValue(1);
			setExportOptionsBGColor();
		}
		if (cInd == idxLang)
		{
			comboDBFieldLang.clear();
			if (in_array(wExport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang))
			{
<?php
	foreach($languages AS $lang)
	{ 
		echo '				comboDBFieldLang.put("'.$lang['iso_code'].'","'.$lang['iso_code'].'");';
	}
?>
				return true;
			}
		}
		if (cInd == idxOptions)
		{
			comboDBField.clear();
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='feature')
			{
<?php
	$features=Feature::getFeatures($sc_agent->id_lang);
	foreach($features AS $feature)
	{ 
		echo '				comboDBField.put("'.addslashes($feature['name']).'","'.addslashes($feature['name']).'");';
	}
?>
				return true;
			}
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='wholesale_price'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_reference')
			{
				<?php
					echo '		comboDBField.put("supplier_none","'._l('Default values display products/combinations grids').'");';
					$suppliers=Supplier::getSuppliers(false, $sc_agent->id_lang, false);
					foreach($suppliers AS $supplier)
					{ 
						echo '	comboDBField.put("'.addslashes($supplier['name']).'","'.addslashes($supplier['name']).'");';
					}
				?>
				return true;
			}
			if (wExport.gridMapping.cells(rId,idxDBField).getValue().substr(0,9)=='attribute')
			{
<?php
	$groups=AttributeGroup::getAttributesGroups($sc_agent->id_lang);
	foreach($groups AS $group)
	{ 
		echo '				comboDBField.put("'.addslashes($group['name']).'","'.addslashes($group['name']).'");';
	}
?>
				return true;
			}
			<?php if(SCAS) { ?>

			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity' 
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='location' 
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_physical'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_usable'
				|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_real'
			)
			{
				if(!(wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_physical'
					|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_usable'
					|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_real'))
					comboDBField.put("warehouse_none","<?php echo _l('No warehouse')?>");
				<?php
					$warehouses=Warehouse::getWarehouses(true);
					foreach($warehouses AS $warehouse)
					{ 
						echo 'comboDBField.put("warehouse_'.addslashes($warehouse['id_warehouse']).'","'._l('Warehouse')." ".addslashes($warehouse['name']).'");';
					}
				?>
				return true;
			}
			<?php } ?>
			if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='priceinctaxwithshipping') {

				<?php
					$carriers = Carrier::getCarriers(SC_ID_LANG, true);
					foreach($carriers AS $carrier)
					{
						echo '	comboPriceIncTaxWithShipping.put("'.addslashes($carrier['name']).'","'.addslashes($carrier['name']).'");';
					}
				?>
				return true;
			}

			<?php echo sc_ext::readExportCSVConfigXML('exportMappingFillCombo');?>
			return false;
		}
		return true;
	}
	wExport.gridMapping.attachEvent('onEditCell',onEditCellMapping);

	wExport.gridMapping.attachEvent("onBeforeDrag", function(id){
		var not_filtering = true;

		for(var i=0; i<=5; i++)
		{
			if(wExport.gridMapping.getFilterElement(i).value!="")
				not_filtering = false;
		}
		if(!not_filtering)
			dhtmlx.message({text:'<?php echo _l('You can\'t manage the positions when there is a filter on a column',1)?>',type:'error'});
		return not_filtering;
	});


	// Context menu for grid
	clipboardType_Mapping = null;	
	mapping_cmenu=new dhtmlXMenuObject();
	mapping_cmenu.renderAsContextMenu();
	function onGridMappingContextButtonClick(itemId){
		if (itemId=="check"){
			var ids = wExport.gridMapping.getSelectedRowId();
			if(ids!=null && ids!=0 && ids!="")
			{
				ids = ids.split(",");
				$.each(ids, function(index, id){
					wExport.gridMapping.cells(id,0).setValue(1);
				});
			}
		}
		if (itemId=="uncheck"){
			var ids = wExport.gridMapping.getSelectedRowId();
			if(ids!=null && ids!=0 && ids!="")
			{
				ids = ids.split(",");
				$.each(ids, function(index, id){
					wExport.gridMapping.cells(id,0).setValue(0);
				});
			}
		}
	}
	mapping_cmenu.attachEvent("onClick", onGridMappingContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="<?php echo _l('Check')?>" id="check"/>'+
			'<item text="<?php echo _l('Uncheck')?>" id="uncheck"/>'+
		'</menu>';
	mapping_cmenu.loadStruct(contextMenuXML);
	wExport.gridMapping.enableContextMenu(mapping_cmenu);

	wExport.gridMapping.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		lastColumnRightClicked_Mapping=colidx;
		return true;
	});
	
	
	dhxlExport.cells('c').setText("<?php echo _l('Categories selection')?>");
	
	wExport.tbCategories=dhxlExport.cells('c').attachToolbar();
	wExport.tbCategories.addButton("delete", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
	wExport.tbCategories.setItemToolTip('delete','<?php echo _l('Delete selection and reset grid',1)?>');
	wExport.tbCategories.addButton("saveasbtn", 0, "", "lib/img/table_save.png", "lib/img/table_save.png");
	wExport.tbCategories.setItemToolTip('saveasbtn','<?php echo _l('Save selection',1)?>');
	wExport.tbCategories.addInput("saveas", 0,"",100);
	wExport.tbCategories.setItemToolTip('saveas','<?php echo _l('Save selection as',1)?>');
	wExport.tbCategories.addText('txt_saveas', 0, '<?php echo _l('Save selection as',1)?>');
	var opts = [
<?php
	$files = array_diff( scandir( SC_TOOLS_DIR.'cat_categories_sel/' ), array_merge( Array( ".", "..", "index.php", ".htaccess")) );
	$content='';
	foreach ($files AS $file)
	{
		if (substr($file,strlen($file)-8,8)=='.sel.xml')
		{
			$file=str_replace('.sel.xml','',$file);
			$content.="['loadselection".$file."', 'obj', '".$file."', ''],";
		}
	}
	if ($content=='') echo "['0', 'obj', '"._l('No selection available')."', ''],";
	echo substr($content,0,-1);
?>
							];
	wExport.tbCategories.addButtonSelect("loadselection", 0, "<?php echo _l('Load')?>", opts, "lib/img/table_relationship.png", "lib/img/table_relationship.png",false,true);
	wExport.tbCategories.setItemToolTip('loadselection','<?php echo _l('Load selection',1)?>');
	wExport.tbCategories.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wExport.tbCategories.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
	function onClickCategSelectionExport(id){
			if (id.substr(0,13)=='loadselection')
			{
				tmp=id.substr(13,id.length);
				wExport.tbCategories.setValue('saveas',tmp);
				displayExportCategories(tmp);
			}
			if (id=='refresh')
			{
				if (typeof filename=='undefined')return;
				if (typeof categselection=='undefined')
				{
					idxCategSelection=wExport.gridFiles.getColIndexById('categoriessel');
					categselection=wExport.gridFiles.cells(lastScriptFile,idxCategSelection).getValue();
//					if (categselection=='') return;
				}
				displayExportCategories(categselection);
			}
			if (id=='saveasbtn')
			{
				if (wExport.tbCategories.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Selection name should not be empty',1)?>',type:'error'});
				}else{
					var categchecked=wExport.treeCategories.getAllChecked();
					wExport.tbCategories.setValue('saveas',getLinkRewriteFromStringLight(wExport.tbCategories.getValue('saveas')));
					$.post('index.php?ajax=1&act=cat_win-export_process&action=categselection_saveas',{'filename':wExport.tbCategories.getValue('saveas'),'categselection':categchecked},function(data){
							if (!in_array('loadselection'+wExport.tbCategories.getValue('saveas'),wExport.tbCategories.getAllListOptions('loadselection')))
							{
								wExport.tbCategories.addListOption('loadselection', 'loadselection'+wExport.tbCategories.getValue('saveas'), 0, 'button', wExport.tbCategories.getValue('saveas'))
								wExport.tbCategories.setListOptionSelected('loadselection', 'loadselection'+wExport.tbCategories.getValue('saveas'));
							}
							dhtmlx.message({text:'<?php echo _l('Data saved!',1)?>',type:'info'});
							displayExportOptions();
						});
				}
			}
			if (id=='delete')
			{
				if (wExport.tbCategories.getValue('saveas')=='')
				{
					dhtmlx.message({text:'<?php echo _l('Selection name should not be empty',1)?>',type:'error'});
				}else{
					if (confirm('<?php echo _l('Delete selection',1)._l('?')?>'))
						$.get('index.php?ajax=1&act=cat_win-export_process&action=categselection_delete&filename='+wExport.tbCategories.getValue('saveas'),function(data){
							wExport.treeCategories.setSubChecked(1,0);
							wExport.tbCategories.removeListOption('loadselection', 'loadselection'+wExport.tbCategories.getValue('saveas'));
							wExport.tbCategories.setValue('saveas','');
						});
				}
			}
	}
	wExport.tbCategories.attachEvent("onClick",onClickCategSelectionExport);	
	wExport.treeCategories=dhxlExport.cells('c').attachTree();
	wExport.treeCategories.setImagePath("lib/js/imgs/");
	wExport.treeCategories.autoScroll=false;
	wExport.treeCategories.enableSmartXMLParsing(true);
	wExport.treeCategories.enableCheckBoxes(true);
	recursiveCheckBoxes=1;
	wExport.treeCategories.enableThreeStateCheckboxes(recursiveCheckBoxes);
	wExport.treeCategories.enableMultiselection(true);
	wExport_cmenu_categ=new dhtmlXMenuObject();
	wExport_cmenu_categ.renderAsContextMenu();
	function onTreecmenu_categButtonClick(itemId){
		if (itemId=="recursive"){
			recursiveCheckBoxes=!recursiveCheckBoxes;
			wExport.treeCategories.enableThreeStateCheckboxes(recursiveCheckBoxes);
			wExport_cmenu_categ.setItemText('recursive', (recursiveCheckBoxes?'<?php echo _l('Disable recursive selection',1)?>':'<?php echo _l('Enable recursive selection',1)?>'));
		}
		if (itemId=="expand"){
			tabId=wExport.treeCategories.contextID;
			wExport.treeCategories.openAllItems(tabId);
		}
		if (itemId=="collapse"){
			tabId=wExport.treeCategories.contextID;
			wExport.treeCategories.closeAllItems(tabId);
		}
		if (itemId=="mark"){
			var list=wExport.treeCategories.getSelectedItemId().split(",");
			if (list.length)
			{
				var act=1;
				if (wExport.treeCategories.isItemChecked(list[0]))
					act=0;
				for (var i=0; i < list.length; i++) {
					wExport.treeCategories.setCheck(list[i],act);
				}
			}
		}
	}
	wExport_cmenu_categ.attachEvent("onClick", onTreecmenu_categButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('Expand')?>" id="expand"/>'+
		'<item text="<?php echo _l('Collapse')?>" id="collapse"/>'+
		'<item text="<?php echo _l('Mark / Unmark')?>" id="mark"/>'+
		'<item id="sepTools" type="separator"/>'+
		'<item text="<?php echo _l('Disable recursive selection')?>" id="recursive"/>'+
	'</menu>';
	wExport_cmenu_categ.loadStruct(contextMenuXML);
	wExport.treeCategories.enableContextMenu(wExport_cmenu_categ);
	wExport.treeCategories.attachEvent("onBeforeContextMenu", function(itemId){
			wExport_cmenu_categ.setItemText('object', 'ID'+itemId+': <?php echo _l('Category:')?> '+wExport.treeCategories.getItemText(itemId));
			return true;
		});

	displayExportCategories();

	var export_cell = dhxlExport.cells('d').attachLayout("2U");
	export_cell.cells('a').setText("<?php echo _l('Export process')?>");

	wExport.tbProcess=export_cell.cells('a').attachToolbar();
	wExport.tbProcess.addButton("go_auto_export", 0, "", "lib/img/control_play_blue.png", "lib/img/control_play_blue.png");
	wExport.tbProcess.setItemToolTip('go_auto_export','<?php echo _l('Export in several times the products corresponding to the selected script',1)?>');
	wExport.tbProcess.addInput("export_interval", 0,60,30);
	wExport.tbProcess.setItemToolTip('export_interval','<?php echo _l('Launch export every X seconds if possible',1)?>');
	wExport.tbProcess.addText('export_txt_interval', 0, '<?php echo _l('Interval:',1)?>');
	wExport.tbProcess.addInput("export_limit", 0,500,30);
	wExport.tbProcess.setItemToolTip('export_limit','<?php echo _l('Number of the first lines to export into the CSV file')?>');
	wExport.tbProcess.addText('export_txt_limit', 0, '<?php echo _l('Lines to export',1)._l(':',1)?>');
	wExport.tbProcess.addButton("export", 0, "", "lib/img/database_go.png", "lib/img/database_go.png");
	wExport.tbProcess.setItemToolTip('export','<?php echo _l('Export in one step the products corresponding to the selected script',1)?>');
	wExport.tbProcess.addSeparator("export_sep", 0);
	wExport.tbProcess.addButtonTwoState("use_auto_export", 0, "", "lib/img/clock.png", "lib/img/clock.png");
	wExport.tbProcess.setItemToolTip('use_auto_export','<?php echo _l('Use auto export',1)?>');

	wExport.tbProcess.hideItem("export_txt_limit");
	wExport.tbProcess.hideItem("export_limit");
	wExport.tbProcess.hideItem("export_txt_interval");
	wExport.tbProcess.hideItem("export_txt_limit");
	wExport.tbProcess.hideItem("export_interval");
	wExport.tbProcess.hideItem("go_auto_export");

	wExport.tbProcess.attachEvent("onStateChange", function(id,state){
		if (id=='use_auto_export' && state){
			wExport.tbProcess.hideItem("export");

			wExport.tbProcess.showItem("export_txt_limit");
			wExport.tbProcess.showItem("export_limit");
			wExport.tbProcess.showItem("export_txt_interval");
			wExport.tbProcess.showItem("export_txt_limit");
			wExport.tbProcess.showItem("export_interval");
			wExport.tbProcess.showItem("go_auto_export");
		}
		else if (id=='use_auto_export' && !state){
			wExport.tbProcess.hideItem("export_txt_limit");
			wExport.tbProcess.hideItem("export_limit");
			wExport.tbProcess.hideItem("export_txt_interval");
			wExport.tbProcess.hideItem("export_txt_limit");
			wExport.tbProcess.hideItem("export_interval");
			wExport.tbProcess.hideItem("go_auto_export");

			wExport.tbProcess.showItem("export");

		}
	});
	wExport.tbProcess.attachEvent("onClick", function(id){
		if (id=='export')
		{
			displayExportProcess();
		}
		if(id=='go_auto_export')
		{
				var go_auto_export = true;
				var export_limit = wExport.tbProcess.getValue('export_limit');
				var export_interval = wExport.tbProcess.getValue('export_interval');
				if(export_interval==undefined || export_interval==null || export_interval==0 || export_interval=="")
				{
					go_auto_export = false;
					dhtmlx.message({text:'<?php echo _l('Please put the interval to export.',1)?>',type:'error'});
				}
				if(export_limit==undefined || export_limit==null || export_limit==0 || export_limit=="")
				{
					go_auto_export = false;
					dhtmlx.message({text:'<?php echo _l('Please put the number of lines to export.',1)?>',type:'error'});
				}
				if (lastScriptFile=='')
				{
					go_auto_export = false;
					dhtmlx.message({text:'<?php echo _l('Please select an export script.',1)?>',type:'error'});
				}
				if(go_auto_export)
					displayAutoExportProcess(lastScriptFile,export_limit,export_interval);
		}
	});


	export_cell.cells('b').setText("<?php echo _l('Exported files')?>");
	export_cell.cells('b').setWidth(300);

	export_cell_files_tbOptions=export_cell.cells('b').attachToolbar();

	export_cell_files_tbOptions.addButton("refresh_export", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	export_cell_files_tbOptions.setItemToolTip('refresh_export','<?php echo _l('Refresh',1)?>');
	export_cell_files_tbOptions.attachEvent("onClick", function(id)

	{
		if (id=='refresh_export')
		{
			displayExportLastGrid()
		}
	});

	export_cell_files_tbOptions.addButton('del_export',100,'','lib/img/delete.gif','lib/img/delete.gif');
	export_cell_files_tbOptions.setItemToolTip('del_export','<?php echo _l('Delete exported files',1)?>');
	export_cell_files_tbOptions.attachEvent("onClick", function(id)
	{
		if (id=='del_export')
		{
			if ( confirm('<?php echo _l('Are you sure you want to delete the selected items?', 1)?>'))
			{
				filenamexport = wExport.gridResult.getSelectedRowId();

				$.post("index.php?ajax=1&act=cat_win-export_files_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'filenamexport':filenamexport}, function() {
					wExport.gridResult.deleteSelectedRows();

				});
			}
		}
	});

	wExport.gridResult=export_cell.cells('b').attachGrid();
	wExport.gridResult.setImagePath("lib/js/imgs/");
	wExport.gridResult.enableMultiselect(true);

	displayExportLastGrid();

	//#####################################
//############ Load functions
//#####################################

function displayExportLastGrid()
{
	wExport.gridResult.clearAll(true);
	wExport.gridResult.load("index.php?ajax=1&act=cat_win-export_files_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), function(){
		wExport.gridResult.sortRows(2,"str","des");
	});

}

function displayExportOptions(callback)
{
	wExport.gridFiles.clearAll(true);
	wExport.gridFiles.loadXML("index.php?ajax=1&act=cat_win-export_config_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
			if (callback)
			{
				eval(callback);
			}else if(lastScriptFile!=''){
				wExport.gridFiles.selectRowById(lastScriptFile);
			}
			});
}


function displayExportCategories(categselection,callback)
{
		wExport.treeCategories.deleteChildItems(0);
//		wExport.treeCategories.loadXML("index.php?ajax=1&act=cat_win-export_categ_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
		<?php if(SCMS && SCI::getSelectedShop()>0) { ?>
		wExport.treeCategories.loadXML("index.php?ajax=1&act=cat_category_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
		<?php } else { ?>
		wExport.treeCategories.loadXML("index.php?ajax=1&act=cat_category_get&forceDisplayAllCategories=1&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
		<?php } ?>
		if (categselection)
			$.post('index.php?ajax=1&act=cat_win-export_process&action=categselection_load',{'filename':categselection},function(data){
				if (data!='')
				{
						categsel=data.split(';');
						var list = wExport.treeCategories.getAllChecked().split(",");
						for (var i=0; i<list.length; i++) {
							wExport.treeCategories.setCheck(list[i],0);
						}
						var list = wExport.treeCategories.getAllUnchecked().split(",");
						for (var i=0; i<list.length; i++) {
							if (in_array(list[i],categsel))
								wExport.treeCategories.setCheck(list[i],1);
						}
				}
			});
			if (callback!='') eval(callback);
		});
}


function displayExportMapping(mapping)
{
	wExport.gridMapping.clearAll(true);
	wExport.gridMapping.loadXML("index.php?ajax=1&act=cat_win-export_mapping_get&id_lang="+SC_ID_LANG+"&exp_mapping_file="+mapping.replace('.map.xml','')+"&"+new Date().getTime(),function()
	{
		setExportOptionsBGColor();
	});
}
function displayExportProcess()
{
	if (lastScriptFile=='')
	{
		dhtmlx.message({text:'<?php echo _l('Please select an export script.',1)?>',type:'error'});
	}else{
		$("#export_contener img").fadeIn();
		wExport.setIcon('lib/img/ajax-loader16.gif','../../../lib/img/ajax-loader16.gif');
		export_cell.cells('a').attachHTMLString('<br/><br/><center><img src="lib/img/loading.gif" alt="loading" title="loading" style="" /></center>');
		$.get('index.php?ajax=1&act=cat_win-export_process&action=export_process&filename='+lastScriptFile,function(data){
			wExport.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
			var key;
			var exportings = false;
			for (key in autoExports) {
			    if (autoExports[key] != undefined && autoExports[key].exporting != undefined && autoExports[key].exporting==true) {
			    	exportings=true;
			    }
			}
			if(exportings==false)
				$("#export_contener img").hide();
			export_cell.cells('a').attachHTMLString(data);
			displayExportLastGrid();
		});
	}
}

var autoExports = new Object();
function displayAutoExportProcess(export_name, export_limit,export_interval)
{
	var exporting = false;
	if(autoExports[export_name]!=undefined && autoExports[export_name]!=null)
	{
		if(autoExports[export_name].exporting!=undefined && autoExports[export_name].exporting==true)
			exporting = true;
	}

	if(!exporting)
	{
		$("#export_contener img").fadeIn();

		autoExports[export_name] = {
				name: export_name,
				limit: export_limit,
				interval: export_interval*1000,
				exporting: true,
				actual_exporting: false,
				timeout: null
			};

		_displayAutoExportProcess(export_name, 1);
	}
	else
		dhtmlx.message({text:'<?php echo _l('This export is already in progress.',1)?>',type:'error'});
}

function _displayAutoExportProcess(export_name, first_interval)
{
	var actual_exporting = false;
	if(autoExports[export_name]!=undefined && autoExports[export_name]!=null)
	{
		if(autoExports[export_name].actual_exporting!=undefined && autoExports[export_name].actual_exporting==true)
			actual_exporting = true;
	}

	var autoExport = autoExports[export_name];
	autoExports[export_name].actual_exporting = true;

	if(!actual_exporting)
	{

		wExport.setIcon('lib/img/ajax-loader16.gif','../../../lib/img/ajax-loader16.gif');
		$.get('index.php?ajax=1&act=cat_win-export_process&action=export_process&auto_export=1&export_limit='+autoExport.limit+'&first_interval='+first_interval+'&filename='+export_name,function(data){
			wExport.setIcon('lib/img/database_add.png','../../../lib/img/database_add.png');
			if(first_interval == 1) {
				export_cell.cells('a').attachHTMLString('<div id="export_contener" style="height: 100%; overflow: auto;"><img src="lib/img/loading.gif" alt="loading" title="loading" style="float: left; margin:10px;" /><div id="export_message" style="padding-left: 10px;font-family: Tahoma; font-size: 11px !important; line-height: 18px;"></div></div>');
			}

			if(data.type==undefined || data.type==null || data.type=="")
				$("#export_message").prepend('<?php echo _l('An error occured during export.',1)?>');

			if(data.type!=undefined && data.type=="error" && data.content!=undefined && data.content!=null && data.content!="")
				$("#export_message").prepend(data.content+"<br/>");
				//dhtmlx.message({text:data.content,type:'error'});

			if(data.type!=undefined && data.type=="success" && data.content!=undefined && data.content!=null && data.content!="")
			{
				$("#export_message").prepend(data.content+"<br/>");
			}
			if(data.debug!=undefined && data.debug!=null && data.debug!="")
			{
				console.log(data.debug);
			}

			if(data.filename!=undefined && data.filename!=null && data.filename!="")
			{
				if(data.stop!=undefined && data.stop=="1")
				{
					clearTimeout(autoExports[data.filename].timeout);
					autoExports[data.filename].exporting = false;
				}
				autoExports[data.filename].actual_exporting = false;

				var key;
				var exportings = false;
				for (key in autoExports) {
				    if (autoExports[key] != undefined && autoExports[key].exporting != undefined && autoExports[key].exporting==true) {
				    	exportings=true;
				    }
				}
				if(exportings==false)
					$("#export_contener img").hide();
			}
		}, 'json');

		autoExports[export_name].timeout = setTimeout("_displayAutoExportProcess('"+export_name+"', 0);",autoExport.interval);
	}
	else
		autoExports[export_name].timeout = setTimeout("_displayAutoExportProcess('"+export_name+"', 0);",autoExport.interval);
}

function checkOptions()
{
	var flag=true;
	idxUse=wExport.gridMapping.getColIndexById('use');
	idxName=wExport.gridMapping.getColIndexById('name');
	idxLang=wExport.gridMapping.getColIndexById('lang');
	idxOptions=wExport.gridMapping.getColIndexById('options');

	wExport.gridMapping.forEachRow(function(rId){
		if (wExport.gridMapping.cells(rId,idxUse).getValue()=="1")
		{
			if (wExport.gridMapping.cells(rId,idxLang).getBgColor()=='#CCCCEE'
				&& wExport.gridMapping.cells(rId,idxLang).getValue()=='')
				flag=false;
			if (wExport.gridMapping.cells(rId,idxOptions).getBgColor()=='#CCCCEE'
				&& wExport.gridMapping.cells(rId,idxOptions).getValue()=='')
				flag=false;
		}
	});
	return flag;
}

function loadAllFieldsInMapping(limited)
{
	if(limited==undefined || limited==null || limited=="" || limited==0)
		limited = false;
	var ii=0;
	var listFields = new Array(<?php
		$js_fields = "";
		$fields = getExportCSVFields();
		foreach ($fields as $field)
		{
			if($field!="_fixed_value")
			{
				if(!empty($js_fields))
					$js_fields .= ",";
				$js_fields .= "'".$field."'";
			}
		}
		echo $js_fields;
	?>);
	var listLimitedFields = new Array(<?php
		$js_fields = "";
		$fields = getExportCSVLimitedFields();
		foreach ($fields as $field)
		{
			if($field!="_fixed_value")
			{
				if(!empty($js_fields))
					$js_fields .= ",";
				$js_fields .= "'".$field."'";
			}
		}
		echo $js_fields;
	?>);
	var listLimitedLangFields = new Array('attribute','feature','category_default_full_path','category_full_path','links_to_all_images','urls_to_all_images','link_rewrite');

	idxMark=wExport.gridMapping.getColIndexById('use');
	idxDBField=wExport.gridMapping.getColIndexById('name');
	idxOptions=wExport.gridMapping.getColIndexById('options');
	idxLang=wExport.gridMapping.getColIndexById('lang');

	if(limited==true)
		listFields = listLimitedFields;

	var nb_max = listFields.length*1;
	for(var j=0;j<nb_max;j++)
	{
		// CREATE ROW
		var newId = (new Date()).valueOf()+ii;
		wExport.gridMapping.addRow(newId,[1,listFields[j],'','','']);
		ii++;

		// MANAGE COLOR
		var rId = newId;
		wExport.gridMapping.cells(rId,idxOptions).setBgColor(wExport.gridMapping.cells(rId,idxDBField).getBgColor());
		wExport.gridMapping.cells(rId,idxLang).setBgColor(wExport.gridMapping.cells(rId,idxDBField).getBgColor());
		var flag=false;
		var flag_lang=false;
		var flag_feature=false;
		var flag_attribute=false;
		var flag_supplier=false;
		var flag_warehouse=false;
		var flag_warehouse_all=false;
		if (in_array(wExport.gridMapping.cells(rId,idxDBField).getValue(),arrayFieldLang))
		{
			wExport.gridMapping.cells(rId,idxLang).setBgColor('#CCCCEE');
			flag=true;
			flag_lang=true;
		}
		if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='feature')
		{
			wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
			flag=true;
			flag_feature=true;
		}
		if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='wholesale_price'
			|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='supplier_reference')
		{
			wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
			flag=true;
			flag_supplier=true;
		}
		<?php if(SCAS) { ?>
		if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity'
			|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='location'
		)
		{
			wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
			flag=true;
			flag_warehouse_all=true;
		}
		if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_physical'
			|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_usable'
			|| wExport.gridMapping.cells(rId,idxDBField).getValue()=='quantity_real'
		)
		{
			wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
			flag=true;
			flag_warehouse=true;
		}
		<?php } ?>
		if (wExport.gridMapping.cells(rId,idxDBField).getValue()=='attribute' || wExport.gridMapping.cells(rId,idxDBField).getValue()=='attribute_multiple')
		{
			wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
			flag=true;
			flag_attribute=true;
		}
		if (!flag)
		{
			wExport.gridMapping.cells(rId,idxOptions).setValue('');
			wExport.gridMapping.cells(rId,idxLang).setValue('');
		}

		// LANG
		if(flag_lang)
		{
			var one_lang = false;
			if($.inArray(wExport.gridMapping.cells(rId,idxDBField).getValue(), listLimitedLangFields)>=0 && limited==true)
				one_lang = true;
			<?php
			foreach($languages AS $num=>$lang)
			{
				?> if(one_lang==false) { <?php
					if($num==0) { ?>
						wExport.gridMapping.cells(rId, idxLang).setValue('<?php echo $lang['iso_code'] ?>');
					<?php } else { ?>
						var newId = (new Date()).valueOf() + ii;
						wExport.gridMapping.addRow(newId, [1, listFields[j], '<?php echo $lang['iso_code'] ?>', '', '']);
						ii++;
						var rId = newId;
						wExport.gridMapping.cells(rId, idxLang).setBgColor('#CCCCEE');
					<?php } ?>
				} else {
					if(SC_ID_LANG=='<?php echo $lang['id_lang'] ?>')
					{
						wExport.gridMapping.cells(rId, idxLang).setValue('<?php echo $lang['iso_code'] ?>');
					}
				}
				<?php
				// ATTRIBUTE
				?>
				if(flag_attribute && (one_lang==false || (one_lang==true && SC_ID_LANG=='<?php echo $lang['id_lang'] ?>')))
				{
					<?php
					foreach($groups AS $num_group=>$group)
					{
						if($num_group==0) {
							echo "wExport.gridMapping.cells(rId,idxOptions).setValue('".addslashes($group['name'])."');
							wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');";
						} else { ?>
							var newId = (new Date()).valueOf()+ii;
							wExport.gridMapping.addRow(newId,[1,listFields[j],'<?php echo $lang['iso_code'] ?>','<?php echo addslashes($group['name']) ?>','']);
							ii++;
							var rId = newId;
							wExport.gridMapping.cells(rId,idxLang).setBgColor('#CCCCEE');
							wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
						<?php }
					}
					?>
				}
				<?php
				// FEATURE
				?>
				if(flag_feature && (one_lang==false || (one_lang==true && SC_ID_LANG=='<?php echo $lang['id_lang'] ?>')))
				{
					<?php
					foreach($features AS $num_feature=>$feature)
					{
						if($num_feature==0) {
							echo "wExport.gridMapping.cells(rId,idxOptions).setValue('".addslashes($feature['name'])."');
							wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');";
						} else { ?>
							var newId = (new Date()).valueOf()+ii;
							wExport.gridMapping.addRow(newId,[1,listFields[j],'<?php echo $lang['iso_code'] ?>','<?php echo addslashes($feature['name']) ?>','']);
							ii++;
							var rId = newId;
							wExport.gridMapping.cells(rId,idxLang).setBgColor('#CCCCEE');
							wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
						<?php }
					}
					?>
				}
				<?php
			}
			?>
		}

		if(flag_supplier)
		{
			<?php
			//echo '		comboDBField.put("supplier_none","'._l('Default values display products/combinations grids').'");';
			foreach($suppliers AS $num_supplier=>$supplier)
			{
				if($num_supplier==0) {
					echo "wExport.gridMapping.cells(rId,idxOptions).setValue('supplier_none');
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');";
				}
				?>
					var newId = (new Date()).valueOf()+ii;
					wExport.gridMapping.addRow(newId,[1,listFields[j],'','<?php echo addslashes($supplier['name']) ?>','']);
					ii++;
					var rId = newId;
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				<?php
			}
			?>
		}

		<?php if (!empty($warehouses)) { ?>
		if(flag_warehouse_all)
		{
			<?php
			//"'._l('Warehouse')." ".addslashes($warehouse['name']).'");';<?php echo _l('No warehouse')
			foreach($warehouses AS $num_warehouse=>$warehouse)
			{
				if($num_warehouse==0) {
					echo "wExport.gridMapping.cells(rId,idxOptions).setValue('warehouse_none');
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');";
				}
				?>
					var newId = (new Date()).valueOf()+ii;
					wExport.gridMapping.addRow(newId,[1,listFields[j],'','warehouse_<?php echo addslashes($warehouse['id_warehouse']); ?>','']);
					ii++;
					var rId = newId;
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				<?php
			}
			?>
		}
		if(flag_warehouse)
		{
			<?php
			//"'._l('Warehouse')." ".addslashes($warehouse['name']).'");';<?php echo _l('No warehouse')
			foreach($warehouses AS $num_warehouse=>$warehouse)
			{
				if($num_warehouse==0) {
					echo "wExport.gridMapping.cells(rId,idxOptions).setValue('warehouse_".addslashes($warehouse['id_warehouse'])."');
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');";
				} else {
				?>
					var newId = (new Date()).valueOf()+ii;
					wExport.gridMapping.addRow(newId,[1,listFields[j],'','warehouse_<?php echo addslashes($warehouse['id_warehouse']); ?>','']);
					ii++;
					var rId = newId;
					wExport.gridMapping.cells(rId,idxOptions).setBgColor('#CCCCEE');
				<?php }
			}
			?>
		}
		<?php } ?>
	}

	/*var rows = new Array();
	wExport.gridMapping.forEachRow(function(id){
		var name = wExport.gridMapping.cells(id,idxDBField).getValue();
		var option = wExport.gridMapping.cells(id,idxOptions).getValue();
		var lang = wExport.gridMapping.cells(id,idxLang).getValue();

		var printTo = name;
		if(option!=undefined && option!=0 && option!="" && option!=null)
			printTo = printTo+" "+option;
		if(lang!=undefined && lang!=0 && lang!="" && lang!=null)
			printTo = printTo+" "+lang;

		rows[rows.length] = printTo;
	});
	rows.sort();
	var blabla = "";
	$.each(rows, function(i, row){
		blabla = blabla+","+row;
	});
	console.log(blabla);*/
}

function getExportCheck()
{
	var selectedRow = wExport.gridFiles.getSelectedRowId();
	if(selectedRow!=undefined && selectedRow!="" && selectedRow!=null && selectedRow.search(",")<=0)
	{
		var idColExportFilename = wExport.gridFiles.getColIndexById('exportfilename');
		var ExportFileName = wExport.gridFiles.cells(selectedRow,idColExportFilename).getValue();
		<?php if(SCMS) { ?>
		var idColShop = wExport.gridFiles.getColIndexById('shops');
		var Shop = wExport.gridFiles.cells(selectedRow,idColShop).getTitle();
		<?php } ?>
		var idColCateg = wExport.gridFiles.getColIndexById('categoriessel');
		var category = wExport.gridFiles.cells(selectedRow,idColCateg).getTitle();
		var params = {
			'mapping':mapping,
			'mappingname':wExport.tbMapping.getValue('saveas'),
			'mapppinggridlength':wExport.gridMapping.getRowsNum(),
			'filename':lastScriptFile,
			'exportfilename':ExportFileName,
			<?php if(SCMS) { ?>
			'shop':Shop,
			<?php } ?>
			'category':category,
		};
		export_cell.cells('a').attachHTMLString('<br/><br/><center><img src="lib/img/loading.gif" alt="loading" title="loading" style="" /></center>');
		$.post('index.php?ajax=1&act=cat_win-export_check&id_lang='+SC_ID_LANG,params,function(data){
			export_cell.cells('a').attachHTMLString(data);
		});
	}
}

$( document ).on( "click", ".reset_export", function() {
	var export_id = $(this).attr("id").replace("export_","");
	if(export_id!=undefined && export_id!=null && export_id!="" && export_id!=0)
	{
		$.post('index.php?ajax=1&act=cat_win-export_process&action=reset_export',{'export_id':export_id},function(data){
			if (data!=null && data!="")
			{
				$("#export_message").prepend(data+"<br/>");
			}
		});
	}
});
</script>
