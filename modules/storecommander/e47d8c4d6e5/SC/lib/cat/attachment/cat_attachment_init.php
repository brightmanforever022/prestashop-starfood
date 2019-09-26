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
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
?>
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_ATTACHEMENT")) { ?>
		prop_tb.addListOption('panel', 'attachments', 9, "button", '<?php echo _l('Attachments',1)?>', "lib/img/attach.png");
		allowed_properties_panel[allowed_properties_panel.length] = "attachments";
	<?php } ?>

	prop_tb.addButton("attachment_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('attachment_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButtonTwoState('attachment_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
	prop_tb.setItemToolTip('attachment_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	prop_tb.addButtonTwoState("attachment_filter", 100, "", "lib/img/filter.png", "lib/img/filter.png");
	prop_tb.setItemToolTip('attachment_filter','<?php echo _l('View only attachments used in the same category',1)?>');
	prop_tb.addButton("attachment_add", 100, "", "lib/img/attach_add.png", "lib/img/attach_add.png");
	prop_tb.setItemToolTip('attachment_add','<?php echo _l('Add attachments',1)?>');
	prop_tb.addButton("attachment_del", 100, "", "lib/img/attach_del.png", "lib/img/attach_del.png");
	prop_tb.setItemToolTip('attachment_del','<?php echo _l('Delete selected attachments',1)?>');
	prop_tb.addButton("attachment_add_select", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
	prop_tb.setItemToolTip('attachment_add_select','<?php echo _l('Add link between selected attachments and selected products',1)?>');
	prop_tb.addButton("attachment_del_select", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
	prop_tb.setItemToolTip('attachment_del_select','<?php echo _l('Delete link between selected attachments and selected products',1)?>');
	prop_tb.addButton("attachment_edit", 100, "", "lib/img/page_edit.png", "lib/img/page_edit.png");
	prop_tb.setItemToolTip('attachment_edit','<?php echo _l('Edit file',1)?>');
	<?php if(_r("ACT_CAT_FAST_EXPORT")) { ?>
	prop_tb.addButton("attachment_exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	prop_tb.setItemToolTip('attachment_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
	<?php } ?>

	clipboardType_attachment = null;
	needInitAttachments = 1;
	function initAttachments(){
		if (needInitAttachments)
		{
			prop_tb._attachmentsLayout = dhxLayout.cells('b').attachLayout('2E');

			prop_tb._attachmentsLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._attachmentsLayout.cells('b').setText('<?php echo _l('Upload document',1)?>');
			prop_tb._attachmentsLayout.cells('b').collapse();
			lAttachment = prop_tb._attachmentsLayout;
			attachment_grid=lAttachment.cells('a').attachGrid();
			attachment_grid.setImagePath('lib/js/imgs/');
			attachment_grid.enableSmartRendering(true);
			attachment_grid.enableMultiselect(true);
			
			// UISettings
			attachment_grid._uisettings_prefix='cat_attachment';
			attachment_grid._uisettings_name=attachment_grid._uisettings_prefix;
			attachment_grid._first_loading=1;
			initGridUISettings(attachment_grid);
			
			attachmentFilter=0;
			// update attachment/product after used checkbox
			function onEditCellAttachments(stage,rId,cInd,nValue,oValue){
				idxUsed=attachment_grid.getColIndexById('used');
				if (cInd == idxUsed){
					if(stage==1)
						$.post("index.php?ajax=1&act=cat_attachment_update&attachment_list="+rId+"&action=update&value="+attachment_grid.cells(rId,idxUsed).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId()},function(data){});
				}
				idxFilename=attachment_grid.getColIndexById('file_name');
				if (cInd == idxFilename){
					if (stage==2)
						$.post("index.php?ajax=1&act=cat_attachment_update&attachment_list="+rId+"&action=updateFilename&value="+nValue+"&"+new Date().getTime(),function(data){});
				}
				cName=attachment_grid.getColumnId(cInd);
				if (cName.substr(0,4) == 'name'){
					if (stage==2)
						$.post("index.php?ajax=1&act=cat_attachment_update&attachment_list="+rId+"&action=updateName&"+cName+"="+nValue+"&"+new Date().getTime(),function(data){});
				}
				cDescription=attachment_grid.getColumnId(cInd);
				if (cDescription.substr(0,11) == 'description'){
					if (stage==2)
						$.post("index.php?ajax=1&act=cat_attachment_update&attachment_list="+rId+"&action=updateDescription&"+cDescription+"="+nValue+"&"+new Date().getTime(),function(data){});
				}
				return true;
			}
			attachment_grid.attachEvent("onEditCell",onEditCellAttachments);
			displayAttachments();
			needInitAttachments=0;

			
			// Context menu for grid
			attachment_cmenu=new dhtmlXMenuObject();
			attachment_cmenu.renderAsContextMenu();
			function onGridattachmentContextButtonClick(itemId){
				tabId=attachment_grid.contextID.split('_');
				tabId=tabId[0];
				if (itemId=="copy"){
					if (lastColumnRightClicked_attachment!=0)
					{
						clipboardValue_attachment=attachment_grid.cells(tabId,lastColumnRightClicked_attachment).getValue();
						attachment_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+attachment_grid.cells(tabId,lastColumnRightClicked_attachment).getTitle());
						clipboardType_attachment=lastColumnRightClicked_attachment;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_attachment!=0 && clipboardValue_attachment!=null && clipboardType_attachment==lastColumnRightClicked_attachment)
					{
						selection=attachment_grid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								attachment_grid.cells(selArray[i],lastColumnRightClicked_attachment).setValue(clipboardValue_attachment);
								onEditCellAttachments(2,selArray[i],lastColumnRightClicked_attachment,clipboardValue_attachment,null);
							}
						}
					}
				}
			}
			attachment_cmenu.attachEvent("onClick", onGridattachmentContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			attachment_cmenu.loadStruct(contextMenuXML);
			attachment_grid.enableContextMenu(attachment_cmenu);

			attachment_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var disableOnCols=new Array(
					attachment_grid.getColIndexById('id_attachment'),
					attachment_grid.getColIndexById('used'),
					attachment_grid.getColIndexById('file_size'),
					attachment_grid.getColIndexById('file_name')
				);
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_attachment=colidx;
				attachment_cmenu.setItemText('object', '<?php echo _l('Attachment:')?> '+attachment_grid.cells(rowid,attachment_grid.getColIndexById('id_attachment')).getTitle());
				if (lastColumnRightClicked_attachment==clipboardType_attachment)
				{
					attachment_cmenu.setItemEnabled('paste');
				}else{
					attachment_cmenu.setItemDisabled('paste');
				}
				return true;
			});
		}
	}

	function setPropertiesPanel_attachments(id){
		if (id=='attachments')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('attachment_del_select');
			prop_tb.showItem('attachment_add_select');
			prop_tb.showItem('attachment_del');
			prop_tb.showItem('attachment_add');
			prop_tb.showItem('attachment_filter');
			prop_tb.showItem('attachment_refresh');
			prop_tb.showItem('attachment_lightNavigation');
			prop_tb.showItem('attachment_exportcsv');
			prop_tb.showItem('attachment_edit');
			prop_tb.setItemText('panel', '<?php echo _l('Attachments',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/attach.png');
			needInitAttachments=1;
			initAttachments();
			propertiesPanel='attachments';
		}
		if (id=='attachment_exportcsv'){
			attachment_grid.enableCSVHeader(true);
			attachment_grid.setCSVDelimiter("\t");
			var csv=attachment_grid.serializeToCSV(true);
			displayQuickExportWindow(csv,1);
		}
		if (id=='attachment_add_select'){
			if (confirm('<?php echo _l('Are you sure?',1)?>'))
				$.post("index.php?ajax=1&act=cat_attachment_update&action=addSelAttachment&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(),"attachment_list":attachment_grid.getSelectedRowId()},function(data){
						getAttachmentRelations();
					});
		}
		if (id=='attachment_del_select'){
			if (confirm('<?php echo _l('Are you sure you want to dissociate the selected items?',1)?>'))
				$.post("index.php?ajax=1&act=cat_attachment_update&action=deleteSelAttachment&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(),"attachment_list":attachment_grid.getSelectedRowId()},function(data){
						getAttachmentRelations();
					});
		}
		if (id=='attachment_add'){
			if (!dhxWins.isWindow("wCatAddAttachment"))
			{
				wCatAddAttachment = dhxWins.createWindow("wCatAddAttachment", 50, 50, 585, 400);
				wCatAddAttachment.setIcon('lib/img/attach_add.png','../../../lib/img/attach_add.png');				
				wCatAddAttachment.setText("<?php echo _l('Add attachments') ?>");
				ll = new dhtmlXLayoutObject(wCatAddAttachment, "1C");
				ll.cells('a').hideHeader();
				ll.cells('a').attachURL("index.php?ajax=1&act=cat_attachment_upload&product_list="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
				wCatAddAttachment.attachEvent("onClose", function(win){
						wCatAddAttachment.hide();
						displayAttachments('',true);
						return false;
					});				
				wCatAddAttachment._add_prop_tb=wCatAddAttachment.attachToolbar();
				// checked status
				wCatAddAttachment._add_prop_tb.addButtonTwoState("attachment_checked", 0, "", "lib/img/link_add.png", "lib/img/link_add.png");
				wCatAddAttachment._add_prop_tb.setItemToolTip('attachment_checked','<?php echo _l('Link these attachments to selected products when created',1)?>');
				wCatAddAttachment._add_prop_tb.setItemState('attachment_checked', 1);
				wCatAddAttachment._linkToProducts=1;
				// events
				wCatAddAttachment._add_prop_tb.attachEvent("onStateChange",function(id,state){
						if (id=='attachment_checked')
						{
							if (state){
								wCatAddAttachment._linkToProducts=1;
							}else{
								wCatAddAttachment._linkToProducts=0;
							}
						}
					});
			}else{
				ll.cells('a').attachURL("index.php?ajax=1&act=cat_attachment_upload&product_list="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
				wCatAddAttachment.show();
			}
		}
		if (id=='attachment_del'){
			if (confirm('<?php echo _l('Are you sure?',1)?>'))
				$.post("index.php?ajax=1&act=cat_attachment_update&action=delete&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId(),"attachment_list":attachment_grid.getSelectedRowId()},function(data){
						attachment_grid.deleteSelectedRows();
					});
		}
		if (id=='attachment_refresh'){
			displayAttachments('',true);
		}
		if (id=='attachment_edit')
		{

		var ids_split = null;
			var ids = attachment_grid.getSelectedRowId();
			if(ids!=undefined && ids!=null && ids!=0)
				ids_split = ids.split(",");
			if(ids_split!=null && ids_split.length>1)
				dhtmlx.message({text:'<?php echo _l('To edit a document, you must select one row only.',1)?>',type:'error',expire:10000});
			else if(ids==null || ids==0)
				dhtmlx.message({text:'<?php echo _l('To edit a document, you must select a row.',1)?>',type:'error',expire:10000});
			else if(ids_split!=null && ids_split.length==1)
			{
				prop_tb._attachmentsLayout.cells('b').expand();
				idxFilename=attachment_grid.getColIndexById('file_name');
				var name = attachment_grid.cells(attachment_grid.getSelectedRowId(),idxFilename).getValue();
				prop_tb._attachmentsLayout.cells('b').setText('<?php echo _l('Edit document file',1)?> "'+name+'"');
				prop_tb._attachmentsLayout.cells('b').attachURL("index.php?ajax=1&act=cat_attachment_upload_file&id_attachment_download="+attachment_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&ids="+ids+"&action=upload_file&"+new Date().getTime(),function(data){});
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_attachments);
	prop_tb.attachEvent("onStateChange",function(id,state){
		if (id=='attachment_filter')
		{
			if (state){
				attachmentFilter=1;
			}else{
				attachmentFilter=0;
			}
			displayAttachments('',true);
		}
		if (id=='attachment_lightNavigation')
		{
			if (state)
			{
				attachment_grid.enableLightMouseNavigation(true);
			}else{
				attachment_grid.enableLightMouseNavigation(false);
			}
		}
	});

	function displayAttachments(callback)
	{
		attachment_grid.clearAll(true);
		prop_tb._sb.setText('');
		prop_tb._sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');
		attachment_grid.load("index.php?ajax=1&act=cat_attachment_get&product_list="+cat_grid.getSelectedRowId()+"&attachmentFilter="+attachmentFilter+"&id_lang="+SC_ID_LANG+"&id_category="+catselection+"&"+new Date().getTime(),function(){
				nb=attachment_grid.getRowsNum();
				prop_tb._sb.setText(nb+' '+(nb>1?'<?php echo _l('attachments',1)?>':'<?php echo _l('attachment',1)?>'));
				attachment_grid._rowsNum=nb;
				
    		// UISettings
				loadGridUISettings(attachment_grid);
	    		
				getAttachmentRelations();
				
				// UISettings
				attachment_grid._first_loading=0;
				
				if (callback!='') eval(callback);
			}); 
	}
		
	function getAttachmentRelations()
	{
		if (attachment_grid._rowsNum >0)
		{
			$.post("index.php?ajax=1&act=cat_attachment_relation_get&attachmentFilter="+attachmentFilter+"&id_lang="+SC_ID_LANG+"&id_category="+catselection+"&"+new Date().getTime(),{"product_list":cat_grid.getSelectedRowId()},function(data){
					if (data!='')
					{
						attachment_grid.uncheckAll();
						dataArray=data.split(',');
						attachment_grid.forEachRow(function(id){
								if (in_array(id,dataArray))
								{
									attachment_grid.cellById(id,1).setValue(1);
								}
							});	
					}else{
						attachment_grid.uncheckAll();
					}
				});
		}
	}
	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='attachments'){
				getAttachmentRelations();
			}
		});

<?php
	}
