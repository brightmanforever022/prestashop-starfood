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
	dhxlAttributes=wAttributes.attachLayout("2U");
	wAttributes._sb=dhxlAttributes.attachStatusBar();
	dhxlAttributes.cells('a').setText("<?php echo _l('Groups')?>");
	wAttributes.tbGroups=dhxlAttributes.cells('a').attachToolbar();
	<?php if(_r("ACT_CAT_ADD_PRODUCT_COMBI")) { ?>
	wAttributes.tbGroups.addButton("create_combination", 0, "<?php echo _l('Create new combination',1)?>", "lib/img/wand.png", "lib/img/wand.png");
	wAttributes.tbGroups.setItemToolTip('create_combination','<?php echo _l('Create new combination with the selected groups',1)?>');
	<?php } ?>
	<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
		wAttributes.tbGroups.addButton("attr_group_setposition", 100, "", "lib/img/layers.png", "lib/img/layers_dis.png");
		wAttributes.tbGroups.setItemToolTip('attr_group_setposition','<?php echo _l('Save positions',1)?>');
	<?php } ?>
	wAttributes.tbGroups.addButton("del_group", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	wAttributes.tbGroups.setItemToolTip('del_group','<?php echo _l('Delete group, all attributes and all combinations using this group',1)?>');
	wAttributes.tbGroups.addButton("duplicate_group", 0, "", "lib/img/page_copy2.png", "lib/img/page_copy2.png");
	wAttributes.tbGroups.setItemToolTip('duplicate_group','<?php echo _l('Duplicate selected groups and their attributes',1)?>');
	wAttributes.tbGroups.addButton("add_group", 0, "", "lib/img/add.png", "lib/img/add.png");
	wAttributes.tbGroups.setItemToolTip('add_group','<?php echo _l('Create a new group,1')?>');
	if (isIPAD)
	{
		wAttributes.tbGroups.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		wAttributes.tbGroups.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	wAttributes.tbGroups.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wAttributes.tbGroups.setItemToolTip('refresh','<?php echo _l('Refresh grid',1)?>');
	wAttributes.tbGroups.attachEvent("onClick",
		function(id){
			if (id=='refresh')
			{
				displayGroups();
			}
			if (id=='add_group')
			{
				var newId = new Date().getTime();
				wAttributes.gridGroups.addRow(newId,[newId,"0","",""]);
			}
			if (id=='duplicate_group')
			{
				if (wAttributes.gridGroups.getSelectedRowId() && confirm('<?php echo _l('Are you sure to duplicate the selected groups and their attributes?',1)?>'))
					$.post("index.php?ajax=1&act=cat_win-attribute_update&id_lang="+SC_ID_LANG,{'groups':wAttributes.gridGroups.getSelectedRowId(),'!nativeeditor_status':'duplicated'},function(data){displayGroups();});
			}
			if (id=='del_group')
			{
				if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
				{
					wAttributes.gridGroups.deleteSelectedRows();
					wAttributes.gridAttributes.clearAll(true);
				}
			}
			if (id=='attr_group_setposition'){
				if (wAttributes.gridGroups.getRowsNum()>0)
				{
					var positions='';
					var idx=0;
					var i = 1 ;
					wAttributes.gridGroups.forEachRow(function(id){
							positions+=id+','+wAttributes.gridGroups.getRowIndex(id)+';';
							idx++;
						});
					$.post("index.php?ajax=1&act=cat_win-attribute_group_update&action=position&"+new Date().getTime(),{ positions: positions },function(){
						displayGroups();
					});
				}
			}
			if (id=='create_combination')
			{
				error='';
				if (propertiesPanel!='combinations') error+="-<?php echo _l('The combination panel is displayed.')?>\n";
				if (lastProductSelID==0) error+="-<?php echo _l('A product is selected.')?>\n";
				if (prop_tb._combinationsGrid.getRowsNum()>0) error+="-<?php echo _l('No combinations already exist for the selected product.')?>\n";
				if (wAttributes.gridGroups.getSelectedRowId()==null) error+="-<?php echo _l('At least one group is selected.')?>\n";
				if (error=='')
				{
					displayCombinations('prop_tb.callEvent("onClick",["force_combi_add"]);',wAttributes.gridGroups.getSelectedRowId());
					wAttributes.hide();
				}else{
					error="<?php echo _l('To create a new combination, check that:')?>\n"+error;
					alert(error);
				}
			}
		});
	wAttributes.gridGroups=dhxlAttributes.cells('a').attachGrid();
	wAttributes.gridGroups._name='groups';
	wAttributes.gridGroups.setImagePath("lib/js/imgs/");
	wAttributes.gridGroups.enableMultiselect(true);
//	wAttributes.gridGroups.enableSmartRendering(true); // useless

	//wAttributes.gridGroups.enableAutoSaving('cg_cat_group');
	//wAttributes.gridGroups.enableAutoHiddenColumnsSaving('cg_cat_group');

	// UISettings
	wAttributes.gridGroups._uisettings_prefix='cat_win-attribute_group';
	wAttributes.gridGroups._uisettings_name=wAttributes.gridGroups._uisettings_prefix;
	wAttributes.gridGroups._first_loading=1;

	// UISettings
	initGridUISettings(wAttributes.gridGroups);

	wAttributes.gridGroups.attachEvent("onEditCell", function(stage, rId, cIn){
			if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
			return true;
		});
	groupsDataProcessorURLBase="index.php?ajax=1&act=cat_win-attribute_group_update&id_lang="+SC_ID_LANG;
	groupsDataProcessor = new dataProcessor(groupsDataProcessorURLBase);
	groupsDataProcessor.enableDataNames(true);
	groupsDataProcessor.enablePartialDataSend(true);
	groupsDataProcessor.setUpdateMode('cell');
	groupsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
			if (action=='insert')
			{
				wAttributes.gridGroups.cells(tid,0).setValue(tid);
			}
			return true;
		});
	groupsDataProcessor.init(wAttributes.gridGroups);

	wAttributes.gridGroups.enableDragAndDrop(true);
	wAttributes.gridGroups.setDragBehavior("child");
	wAttributes.gridGroups.attachEvent("onDrag",function(sourceid,targetid,sourceobject,targetobject){
			if (sourceobject._name=='attributes' && targetid!=undefined && targetid!=null && targetid!=0)
			{
				var attributes=wAttributes.gridAttributes.getSelectedRowId();
				if (attributes==null && draggedAttribute!=0) attributes=draggedAttribute;
				$.post("index.php?ajax=1&act=cat_win-attribute_texture&action=duplicate&id_group="+targetid,{'attributes':attributes},function(data){});
				draggedAttribute=0;
			}
			<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
				if(sourceobject._name=='groups')// || (sourceobject._name=='attributes' && targetid!=undefined && targetid!=null && targetid!=0))
					return true;
				else
					return false;
			<?php } else { ?>
				return false;
			<?php } ?>
		});


	wAttributes.gridAttributes=dhxlAttributes.cells('b').attachGrid();
	wAttributes.gridAttributes._name='attributes';
	wAttributes.gridAttributes.setImagePath("lib/js/imgs/");
	wAttributes.gridAttributes.enableSmartRendering(true);
	/*wAttributes.gridAttributes.enableAutoSaving('cg_cat_attrib');
	wAttributes.gridAttributes.enableAutoHiddenColumnsSaving('cg_cat_attrib');*/

	// UISettings
	wAttributes.gridAttributes._uisettings_prefix='cat_win-attribute';
	wAttributes.gridAttributes._uisettings_name=wAttributes.gridAttributes._uisettings_prefix;
	wAttributes.gridAttributes._first_loading=1;

	// UISettings
	initGridUISettings(wAttributes.gridAttributes);

	function doOnColorChanged(stage, rId, cIn) {
		var coltype=wAttributes.gridAttributes.getColType(cIn);
		if (stage==1 && this.editor && this.editor.obj && coltype!='cp') this.editor.obj.select();
    if (stage==2) {
    	if (wAttributes.gridAttributes.getColIndexById('color')==1)
    	{
        if (cIn == 1) {
            wAttributes.gridAttributes.cells(rId, 2).setValue(wAttributes.gridAttributes.cells(rId, 1).getValue());
        } else if (cIn == 2) {
            wAttributes.gridAttributes.cells(rId, 1).setValue(wAttributes.gridAttributes.cells(rId, 2).getValue());
        }
      }
    }
    return true;
	}
	wAttributes.gridAttributes.attachEvent("onEditCell", doOnColorChanged);


	attributesDataProcessorURLBase="index.php?ajax=1&act=cat_win-attribute_update&id_lang="+SC_ID_LANG;
	attributesDataProcessor = new dataProcessor(attributesDataProcessorURLBase);
	attributesDataProcessor.enableDataNames(true);
	attributesDataProcessor.enablePartialDataSend(true);
	attributesDataProcessor.setUpdateMode('cell');
	attributesDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
			if (action=='insert')
			{
				wAttributes.gridAttributes.cells(tid,0).setValue(tid);
			}
			return true;
		});
	attributesDataProcessor.init(wAttributes.gridAttributes);

	wAttributes.gridAttributes.enableDragAndDrop(true);
	wAttributes.gridAttributes.setDragBehavior("child");
	wAttributes.gridAttributes.attachEvent("onDragIn",function(idsource){
		<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
			return true;
		<?php } else { ?>
			return false;
		<?php } ?>
		});
	wAttributes.gridAttributes.attachEvent("onDrag",function(sourceid,targetid,sourceobject,targetobject){
		<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
		if(targetobject._name==undefined || targetobject._name==null || sourceobject._name!="attributes" || targetobject._name!="attributes")
			return false;
		else
			return true;
		<?php } else { ?>
			return false;
		<?php } ?>
		});
	wAttributes.gridAttributes.attachEvent("onBeforeDrag",function(idsource){
			if (wAttributes.gridAttributes.getSelectedRowId()==null) draggedAttribute=idsource;
			return true;
		});

	draggedAttribute=0;
	lastGroupSelID=0;
	function doOnGroupSelected(idgroup){
		if (lastGroupSelID!=idgroup)
		{
			lastGroupSelID=idgroup;
			displayAttributes(idgroup);
		}
	}
	wAttributes.gridGroups.attachEvent("onRowSelect",doOnGroupSelected);

	displayGroups();

	dhxlAttributes.cells('b').setText("<?php echo _l('Attributes')?>");
	wAttributes.tbAttr=dhxlAttributes.cells('b').attachToolbar();
	wAttributes.tbAttr.addButton("img_del", 0, "", "lib/img/picture_delete.png", "lib/img/picture_delete.png");
	wAttributes.tbAttr.setItemToolTip('img_del','<?php echo _l('Delete texture of selected elements',1)?>');
	wAttributes.tbAttr.addButton("img_add", 0, "", "lib/img/picture_add.png", "lib/img/picture_add.png");
	wAttributes.tbAttr.setItemToolTip('img_add','<?php echo _l('Add texture of selected element',1)?>');
	wAttributes.tbAttr.addSeparator("sep", 0);
	<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
	wAttributes.tbAttr.addButton("attr_setposition", 100, "", "lib/img/layers.png", "lib/img/layers_dis.png");
	wAttributes.tbAttr.setItemToolTip('attr_setposition','<?php echo _l('Save positions',1)?>');
	<?php } ?>
	wAttributes.tbAttr.addButton("merge_attr", 0, "", "lib/img/shape_move_front.png", "lib/img/shape_move_front.png");
	wAttributes.tbAttr.setItemToolTip('merge_attr','<?php echo _l('Merge selected attributes',1)?>');
	wAttributes.tbAttr.addButton("del_attr", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	wAttributes.tbAttr.setItemToolTip('del_attr','<?php echo _l('Delete attribute(s) and all combinations using this attribute',1)?>');
	wAttributes.tbAttr.addButton("add_attr", 0, "", "lib/img/add.png", "lib/img/add.png");
	wAttributes.tbAttr.setItemToolTip('add_attr','<?php echo _l('Create new attributes',1)?>');
	wAttributes.tbAttr.addInput("add_input", 0,"1",30);
	wAttributes.tbAttr.setItemToolTip('add_input','<?php echo _l('Number of attributes to create when clicking on the Create button',1)?>');
	if (isIPAD)
	{
		wAttributes.tbAttr.addButtonTwoState('lightNavigation', 0, "", "lib/img/cursor.png", "lib/img/cursor.png");
		wAttributes.tbAttr.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	wAttributes.tbAttr.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	wAttributes.tbAttr.setItemToolTip('refresh','<?php echo _l('Refresh grid',1)?>');

	wAttributes.tbAttr.attachEvent("onClick",
		function(id){
			if (id=='refresh')
			{
				displayAttributes(lastGroupSelID);
			}
			if (id=='add_attr')
			{
				if (lastGroupSelID!=0)
				{
					var newId = new Date().getTime();
					nb=wAttributes.tbAttr.getValue('add_input');
					if (isNaN(nb)) nb=1;
					for (i=1;i<=nb;i++)
					{
						col2data="";
						if (wAttributes.gridGroups.cells(lastGroupSelID,1).getValue()==1) col2data="#000000";
						wAttributes.gridAttributes.addRow(newId*100+i,[newId*100+i,col2data]);
					}
				}
			}
			if (id=='del_attr')
			{
				if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					wAttributes.gridAttributes.deleteSelectedRows();
			}
			if (id=='merge_attr')
			{
				if (wAttributes.gridAttributes.getSelectedRowId()==null || wAttributes.gridAttributes.getSelectedRowId().split(',').length<2)
				{
					alert('<?php echo _l('You must select one item',1)?>');
				}else if (confirm('<?php echo _l('Are you sure you want to merge the selected items?',1)?>'))
				{
					$.post("index.php?ajax=1&act=cat_win-attribute_update&action=merge",{'attrlist':wAttributes.gridAttributes.getSelectedRowId()},function(data){
							if (data.substr(0,3)=='OK:')
							{
								displayAttributes('wAttributes.gridAttributes.selectRowById('+data.substr(3,10)+',false,true);');
							}else{
								dhtmlx.message({text:'Error: '+data,type:'error'});
							}
						});
				}

			}
			if (id=='attr_setposition'){
				if (wAttributes.gridAttributes.getRowsNum()>0)
				{
					var positions='';
					var idx=0;
					var i = 1 ;
					wAttributes.gridAttributes.forEachRow(function(id){
							positions+=id+','+wAttributes.gridAttributes.getRowIndex(id)+';';
							idx++;
						});
					$.post("index.php?ajax=1&act=cat_win-attribute_update&action=position&"+new Date().getTime(),{ positions: positions },function(){
						displayAttributes(lastGroupSelID);
					});
				}
			}
			if (id=='img_add')
			{
				if (wAttributes.gridAttributes.getSelectedRowId()==null || wAttributes.gridAttributes.getSelectedRowId().split(',').length!=1)
				{
					alert('<?php echo _l('You must select one item',1)?>');
				}else{
					if (dhxWins.isWindow("wAttributeTexture")) wAttributeTexture.close();
					if (!dhxWins.isWindow("wAttributeTexture"))
					{
//						wAttributeTexture = dhxWins.createWindow("wAttributeTexture", 50, 50, 396, 516);
						wAttributeTexture = dhxWins.createWindow("wAttributeTexture", 50, 50, 585, 400);
						wAttributeTexture.setIcon('lib/img/picture_add.png','../../../lib/img/picture_add.png');
						idxAttributeName=wAttributes.gridAttributes.getColIndexById('name¤<?php echo $user_lang_iso;?>');
						wAttributeTexture.setText('<?php echo _l('Texture',1)?> '+wAttributes.gridAttributes.cells(wAttributes.gridAttributes.getSelectedRowId(),idxAttributeName).getValue());
						ll = new dhtmlXLayoutObject(wAttributeTexture, "1C");
						ll.cells('a').hideHeader();
						ll.cells('a').attachURL('index.php?ajax=1&act=cat_win-attribute_texture&action=add&id_attribute='+wAttributes.gridAttributes.getSelectedRowId()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					}
				}
			}
			if (id=='img_del')
			{
				if (wAttributes.gridAttributes.getSelectedRowId()==null)
				{
					alert('<?php echo _l('You must select one item')?>');
				}else if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
				{
					$.post("index.php?ajax=1&act=cat_win-attribute_texture&action=delete",{'id_attribute':wAttributes.gridAttributes.getSelectedRowId()},function(){
							displayAttributes();
						});
				}

			}
		});

	wAttributes.tbGroups.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				wAttributes.gridGroups.enableLightMouseNavigation(true);
			}else{
				wAttributes.gridGroups.enableLightMouseNavigation(false);
			}
		}
	});

	wAttributes.tbAttr.attachEvent("onStateChange",function(id,state){
		if (id=='lightNavigation')
		{
			if (state)
			{
				wAttributes.gridAttributes.enableLightMouseNavigation(true);
			}else{
				wAttributes.gridAttributes.enableLightMouseNavigation(false);
			}
		}
	});

//#####################################
//############ Load functions
//#####################################

function displayGroups()
{
	wAttributes.gridGroups.clearAll(true);
	wAttributes.gridGroups.loadXML("index.php?ajax=1&act=cat_win-attribute_group_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
				nb=wAttributes.gridGroups.getRowsNum();
				wAttributes._sb.setText(nb+(nb>1?" <?php echo _l('groups')?>":" <?php echo _l('group')?>"));
				if(typeof AttrIdToOpen === "number") {
					wAttributes.gridGroups.selectRowById(AttrIdToOpen);
					lastGroupSelID = AttrIdToOpen;
					displayAttributes();
					AttrIdToOpen = null;
				}
    			// UISettings
				loadGridUISettings(wAttributes.gridGroups);
				wAttributes.gridGroups._first_loading=0;
			});
}

function displayAttributes(callback)
{
	wAttributes.gridAttributes.clearAll(true);
	wAttributes.tbAttr.hideItem('sep');
	wAttributes.tbAttr.hideItem('img_add');
	wAttributes.tbAttr.hideItem('img_del');
	idxColorCol=wAttributes.gridGroups.getColIndexById('is_color_group');
	if (lastGroupSelID!=0)
		wAttributes.gridAttributes.loadXML("index.php?ajax=1&act=cat_win-attribute_get&id_attribute_group="+lastGroupSelID+"&iscolor="+wAttributes.gridGroups.cells(lastGroupSelID,idxColorCol).getValue()+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
			{
				attributesDataProcessor.serverProcessor=attributesDataProcessorURLBase+"&id_attribute_group="+lastGroupSelID;
				nb=wAttributes.gridGroups.getRowsNum();
				nb2=wAttributes.gridAttributes.getRowsNum();
				wAttributes._sb.setText(nb+(nb>1?" <?php echo _l('groups')?>":" <?php echo _l('group')?>")+" / "+nb2+(nb2>1?" <?php echo _l('attributes')?>":" <?php echo _l('attribute')?>"));
    		// UISettings
				loadGridUISettings(wAttributes.gridAttributes);
				wAttributes.gridAttributes._first_loading=0;

	    	if (wAttributes.gridAttributes.getColIndexById('color'))
	    	{
					wAttributes.tbAttr.showItem('sep');
					wAttributes.tbAttr.showItem('img_add');
					wAttributes.tbAttr.showItem('img_del');
	    	}
	    	if (callback!='') eval(callback);
			});
}
</script>