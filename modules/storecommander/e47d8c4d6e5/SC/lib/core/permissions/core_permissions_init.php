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
	var lPermissions = new dhtmlXLayoutObject(wCorePermissions, "2U");
	var col_profils = lPermissions.cells('a');
	var col_permissions = lPermissions.cells('b');
	
	// TREE DES PROFILS
		col_profils.setText('<?php echo _l('Profiles',1)?>');
		col_profils.setWidth(200);		
		
		profils_tree=col_profils.attachTree();
		profils_tree._name='tree';
		profils_tree.autoScroll=false;
		profils_tree.setImagePath('lib/js/imgs/');
		profils_tree.enableSmartXMLParsing(true);
		profils_tree.enableDragAndDrop(true);
		profils_tree.setDragBehavior("simple");
		profils_tree._dragBehavior="simple";
		profils_tree.enableDragAndDropScrolling(true);
		
		profils_tb=col_profils.attachToolbar();
		profils_tb.addButton("delete", 0, "", "lib/img/user_delete.png", "lib/img/user_delete.png");
		profils_tb.setItemToolTip('delete','<?php echo _l('Reset permissions',1)?>');
		profils_tb.addButton("open_ps", 0, "", "lib/img/user_go_ps.png", "lib/img/user_go_ps.png");
		profils_tb.setItemToolTip('open_ps','<?php echo _l('See in Prestashop',1)?>');
		profils_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
		profils_tb.setItemToolTip('refresh','<?php echo _l('Refresh',1)?>');
		profils_tb.attachEvent("onClick", function(id){
			if (id=='refresh'){
				displayProfils();
			}
			if (id=='delete'){
				if(confirm('<?php echo _l('Are you sure that you want reset this permissions?',1); ?>'))
				{
					var id = profils_tree.getSelectedItemId();
					$.post("index.php?ajax=1&act=core_permissions_delete&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id':id},function(data){
						displayProfils("callbackProfils('"+id+"')");
					});
				}
			}
			if (id=='open_ps'){
				var id = profils_tree.getSelectedItemId();
				if (!dhxWins.isWindow("wSeePSProfile"))
				{
					wSeePSProfile = dhxWins.createWindow("wSeePSProfile", 50, 50, 1000, $(window).height()-75);
					wSeePSProfile.setText('<?php echo _l('See the profile in Prestashop',1)?>');
					
					var temp = id.split('_');
					if(temp[0]=='em')
						var url = "<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminEmployees&id_employee="+temp[1]+"&updateemployee&token=<?php echo $sc_agent->getPSToken('AdminEmployees');?>";
					else
						var url = "<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminAccess&token=<?php echo $sc_agent->getPSToken('AdminAccess');?>";
					
					wSeePSProfile.attachURL(url);
					wSeePSProfile.attachEvent("onClose", function(win){
								displayProfils("callbackProfils('"+id+"')");
								return true;
							});
				}
			}
		});

		profils_tree.attachEvent("onClick",function(id){
			displayPermissions(id);
		});

		profils_tree.attachEvent("onDrag", function(sId,tId,id,sObject,tObject){
			if(sId!=tId)
			{
				var temp = sId.split('_');
				if(confirm('<?php echo _l('Do you want to duplicate these permissions?',1); ?>'))
				{
					$.post("index.php?ajax=1&act=core_permissions_duplicate&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_source':sId,'id_target':tId},function(data){
						setTimeout("displayProfils('callbackProfils(\""+tId+"\")')",500); // avoid dhtmlx problem
					});
				}
			}
			return false;
		});

	// GRID DES PERMISSIONS
		col_permissions.setText('<?php echo _l('Permissions',1)?>');
		//col_permissions.setWidth(800);
		
		permissions_grid=col_permissions.attachGrid();
		permissions_grid.setImagePath('lib/js/imgs/');
		permissions_grid.setHeader("<?php echo _l('Tool')?>,<?php echo _l('Section')?>,<?php echo _l('Name')?>,<?php echo _l('Access')?>,<?php echo _l('Description')?>,<?php echo _l('Profile access')?>,<?php echo _l('Different from profile:')?>");
		permissions_grid.setColumnIds("section1,section2,id,value,description,profil_value,profil_diff");
		permissions_grid.setInitWidths("75,75,200,60,*,60,60");
		permissions_grid.setColAlign("left,left,left,left,left,left,left");
		permissions_grid.setColTypes("ro,ro,ro,coro,ro,ro,ro");
	  	permissions_grid.enableMultiline(true);
	  	permissions_grid.enableMultiselect(true);
		permissions_grid.setColSorting("str,str,str,str,str,str,str");
		permissions_grid.attachHeader("#select_filter,#select_filter,#text_filter,#select_filter,#text_filter,#select_filter,#select_filter");
		permissions_grid.init();
		permissions_grid.enableHeaderMenu();

		permissions_tb=col_permissions.attachToolbar();
		permissions_tb.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning.png");
		permissions_tb.setItemToolTip('selectall','<?php echo _l('Select all',1)?>');
		permissions_tb.addButton("delete_mass", 0, "", "lib/img/table_delete.png", "lib/img/table_delete.png");
		permissions_tb.setItemToolTip('delete_mass','<?php echo _l('Delete access',1)?>');
		permissions_tb.addButton("add_mass", 0, "", "lib/img/table_add.png", "lib/img/table_add.png");
		permissions_tb.setItemToolTip('add_mass','<?php echo _l('Give access',1)?>');
		permissions_tb.attachEvent("onClick", function(id){
			if(id=="add_mass")
			{
				var id = profils_tree.getSelectedItemId();
				var permissions = permissions_grid.getSelectedRowId();
				$.post("index.php?ajax=1&act=core_permissions_update&action=add_mass&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'profil':id,'permissions':permissions},function(data){
					displayProfils("callbackProfils('"+id+"')");
				});
			}
			if(id=="delete_mass")
			{
				var id = profils_tree.getSelectedItemId();
				var permissions = permissions_grid.getSelectedRowId();
				$.post("index.php?ajax=1&act=core_permissions_update&action=delete_mass&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'profil':id,'permissions':permissions},function(data){
					displayProfils("callbackProfils('"+id+"')");
				});
			}
			if (id=='selectall')
			{
				permissions_grid.selectAll();
			}
		});

		idxValue=permissions_grid.getColIndexById('value');
		var combo = permissions_grid.getCombo(idxValue);
		combo.put("1",'<?php echo _l('Yes',1)?>');
		combo.put("0",'<?php echo _l('No',1)?>');
		combo.save();

		idxProfilValue=permissions_grid.getColIndexById('profil_value');
		idxProfilDiff=permissions_grid.getColIndexById('profil_diff');
		var combo_profil = permissions_grid.getCombo(idxProfilValue);
		combo_profil.put("1",'<?php echo _l('Yes',1)?>');
		combo_profil.put("0",'<?php echo _l('No',1)?>');
		combo_profil.save();
		permissions_grid.setColumnHidden(idxProfilValue,true);
		permissions_grid.setColumnHidden(idxProfilDiff,true);
		
		permissionsDataProcessorURLBase="index.php?ajax=1&act=core_permissions_update";
		permissionsDataProcessor = new dataProcessor(permissionsDataProcessorURLBase);
		permissionsDataProcessor.enableDataNames(true);
		permissionsDataProcessor.enablePartialDataSend(true);
		permissionsDataProcessor.setUpdateMode('cell');
		permissionsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
			var id = profils_tree.getSelectedItemId();
			/*var temp = id.split('_');
			if(temp[0]=='em')*/
				displayProfils('callbackProfils("'+id+'")');
		});

		permissionsDataProcessor.init(permissions_grid);


		// Context menu for MultiShops Info Product grid
		/*permissions_cmenu=new dhtmlXMenuObject();
		permissions_cmenu.renderAsContextMenu();
		var lastColumnRightClicked_Permissions = null;
		function onGridPermissionsContextButtonClick(itemId){
			tabIdtmp=permissions_grid.contextID.split('_');
			tabId=tabIdtmp[0];
			for(var i=1;i<(tabIdtmp.length-1);i++)
				tabId=tabId+"_"+tabIdtmp[i];
			if (itemId=="copy"){
				if (lastColumnRightClicked_Permissions!=0)
				{
					clipboardValue_Permissions=permissions_grid.cells(tabId,lastColumnRightClicked_Permissions).getValue();
					permissions_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+permissions_grid.cells(tabId,lastColumnRightClicked_Permissions).getTitle());
					clipboardType_Permissions=lastColumnRightClicked_Permissions;
				}
			}
			if (itemId=="paste"){
				if (lastColumnRightClicked_Permissions!=0 && clipboardValue_Permissions!=null && clipboardType_Permissions==lastColumnRightClicked_Permissions)
				{
					selection=permissions_grid.getSelectedRowId();
					if (selection!='' && selection!=null)
					{
						selArray=selection.split(',');
						for(i=0 ; i < selArray.length ; i++)
						{
							if (permissions_grid.getColumnId(lastColumnRightClicked_Permissions).substr(0,5)!='attr_')
							{
								permissions_grid.cells(selArray[i],lastColumnRightClicked_Permissions).setValue(clipboardValue_Permissions);
								permissions_grid.cells(selArray[i],lastColumnRightClicked_Permissions).cell.wasChanged=true;
								permissionsDataProcessor.setUpdated(selArray[i],true,"updated");
							}
						}
					}
				}
			}
		}
		permissions_cmenu.attachEvent("onClick", onGridPermissionsContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
				'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
			'</menu>';
		permissions_cmenu.loadStruct(contextMenuXML);
		permissions_grid.enableContextMenu(permissions_cmenu);
		clipboardType_Permissions = null;	
		permissions_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
			var disableOnCols=new Array(
					permissions_grid.getColIndexById('section1'),
					permissions_grid.getColIndexById('section2'),
					permissions_grid.getColIndexById('id'),
					permissions_grid.getColIndexById('description'),
					permissions_grid.getColIndexById('profil_value'),
					permissions_grid.getColIndexById('id_shop')
					);
			if (in_array(colidx,disableOnCols))
			{
				return false;
			}
			lastColumnRightClicked_Permissions=colidx;
			permissions_cmenu.setItemText('object', '<?php echo _l('Access').":"; ?> '+permissions_grid.cells(rowid,permissions_grid.getColIndexById('id')).getTitle());
			if (lastColumnRightClicked_Permissions==clipboardType_Permissions)
			{
				permissions_cmenu.setItemEnabled('paste');
			}else{
				permissions_cmenu.setItemDisabled('paste');
			}
			return true;
		});*/

		/*permissions_grid.attachEvent("onRowSelect",function(id){
			alert(id);
		});*/

	// INIT
			displayProfils();
			
	// FUNCTIONS
		function displayPermissions(id,callback)
		{
			permissions_grid.clearAll();

			var patt=/pr_/g;
			if(patt.test(id))
			{
				permissions_grid.setColumnHidden(idxProfilValue,true);
				permissions_grid.setColumnHidden(idxProfilDiff,true);
				permissions_grid.setColLabel(idxValue,"<?php echo _l('Profile access',1)?>");
			}
			else
			{
				permissions_grid.setColumnHidden(idxProfilValue,false);
				permissions_grid.setColumnHidden(idxProfilDiff,false);
				permissions_grid.setColLabel(idxValue,"<?php echo _l('Employee access',1)?>");
				
				var profil_id = profils_tree.getParentId(id);
				permissions_grid.setColLabel(idxProfilValue,"<?php echo _l('Profile access:',1)?> "+profils_tree.getItemText(profil_id));
				permissions_grid.setColLabel(idxProfilDiff,"<?php echo _l('Different from profile:',1)?> "+profils_tree.getItemText(profil_id));
			}
			
			if(id!=undefined && id!="")
			{
				permissions_grid.load("index.php?ajax=1&act=core_permissions_get&id="+id+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				    getRowsNum=permissions_grid.getRowsNum();
				    permissions_grid.filterByAll();

				    if(id=="pr_1")
				    {
				    	//permissions_grid.enableEditEvents(false, false, false);
				    	permissions_grid.enableEditEvents(true, true, true);
						is_super_admin = true;
				    }
				    else
				    {
					    var is_super_admin = false;
						var super_admins = profils_tree.getAllSubItems("pr_1").split(',');
				    	$.each(super_admins, function(index, id_child) {
							if(id==id_child)
								is_super_admin = true;
						});
						/*if(is_super_admin)
				    		permissions_grid.enableEditEvents(false, false, false);
						else*/
				    		permissions_grid.enableEditEvents(true, true, true);
				    }

				    var ids=permissions_grid.getAllRowIds().split(",");
				    $.each(ids, function(index, id_row) {
					    var row_value = permissions_grid.cells(id_row,idxValue).getValue();
					    if(row_value==1)
				    		permissions_grid.setRowColor(id_row,"#d4ffd5");
					    else
				    		permissions_grid.setRowColor(id_row,"#ffdbdb");
			    		
					    permissions_grid.cells(id_row,idxProfilValue).setTextColor("#888888");
					    permissions_grid.cells(id_row,idxProfilDiff).setTextColor("#888888");

						var tmp_exp = id_row.split("#");
					    if(is_super_admin && tmp_exp[1]!=undefined && tmp_exp[1]=="MEN_TOO_PERMISSIONS")
					    {
						    permissions_grid.cells(id_row,idxValue).setDisabled(true);
						    permissions_grid.cells(id_row,idxValue).setTextColor("#888888");
					    }
					});
					
					if (callback!='') eval(callback);
				});
			}
		}
		
		function displayProfils(callback)
		{
			profils_tree.deleteChildItems(0);
			permissions_grid.clearAll();
			profils_tree.loadXML("index.php?ajax=1&act=core_permissions_profil_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				
				/*profils_tree.setItemColor("pr_1","#888888","#888888");
				var super_admins = profils_tree.getAllSubItems("pr_1").split(',');
				$.each(super_admins, function(index, id) {
					profils_tree.setItemColor(id,"#888888","#888888");
				});*/

				profils_tree.openAllItems(0);
				if (callback!='') eval(callback);
			});
		}

		function callbackProfils(id)
		{
			var temp = id.split('_');
			if(temp[0]=='em')
			{
				var parent_id = profils_tree.getParentId(id);
				profils_tree.openItem(parent_id);
			}
			profils_tree.selectItem(id,true);
		}
</script>