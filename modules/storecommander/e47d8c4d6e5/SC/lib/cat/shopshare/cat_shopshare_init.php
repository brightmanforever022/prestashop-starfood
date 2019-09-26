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
 if(SCMS) { ?>
<?php /*  ?>
<script>
<?php*/  ?>
	
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_MB_SHARE")) { ?>
		prop_tb.addListOption('panel', 'shopshare', 11, "button", '<?php echo _l('Multistore sharing manager',1)?>', "lib/img/sitemap_color.png");
		allowed_properties_panel[allowed_properties_panel.length] = "shopshare";
	<?php } ?>
	
	var auto_share_imgs = <?php echo _s('CAT_PROD_IMAGE_AUTO_SHOP_SHARE'); ?>;

	prop_tb.addButton("shopshare_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('shopshare_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButton("shopshare_add_select", 100, "", "lib/img/chart_organisation_add_v.png", "lib/img/chart_organisation_add_v.png");
	prop_tb.setItemToolTip('shopshare_add_select','<?php echo _l('Add all the products selected to all the selected shops',1)?>');
	prop_tb.addButton("shopshare_del_select", 100, "", "lib/img/chart_organisation_delete_v.png", "lib/img/chart_organisation_delete_v.png");
	prop_tb.setItemToolTip('shopshare_del_select','<?php echo _l('Delete all the products selected to all the selected shops',1)?>');
	prop_tb.addButton("shopshare_active_select", 100, "", "lib/img/lightbulb.png", "lib/img/lightbulb.png");
	prop_tb.setItemToolTip('shopshare_active_select','<?php echo _l('Activate all selected products for the selected shops',1)?>');
	prop_tb.addButton("shopshare_inactive_select", 100, "", "lib/img/lightbulb_off.png", "lib/img/lightbulb_off.png");
	prop_tb.setItemToolTip('shopshare_inactive_select','<?php echo _l('Desactivate all selected products for the selected shops',1)?>');
	prop_tb.addButtonTwoState('shopshare_auto_share_imgs', 100, "", "lib/img/picture_go.png", "lib/img/picture_go.png");
	prop_tb.setItemToolTip('shopshare_auto_share_imgs','<?php echo _l('Automatically share the products images',1)?>');
	
	needInitShopshare = 1;
	function initShopshare()
	{
		if (needInitShopshare)
		{
			prop_tb._shopshareLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._shopshareLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();

			prop_tb._shopshareGrid = prop_tb._shopshareLayout.cells('a').attachGrid();
			prop_tb._shopshareGrid._name='_shopshareGrid';
			prop_tb._shopshareGrid.setImagePath("lib/js/imgs/");
  			prop_tb._shopshareGrid.enableDragAndDrop(false);
			prop_tb._shopshareGrid.enableMultiselect(true);
			/*prop_tb._shopshareGrid.enableAutoSaving('cg_cat_shopshare',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
			prop_tb._shopshareGrid.enableAutoHiddenColumnsSaving('cg_cat_shopshare_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/
			
			// UISettings
			prop_tb._shopshareGrid._uisettings_prefix='cat_shopshare';
			prop_tb._shopshareGrid._uisettings_name=prop_tb._shopshareGrid._uisettings_prefix;
		   	prop_tb._shopshareGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._shopshareGrid);

			prop_tb._shopshareGrid.attachEvent("onEditCell",function(stage,rId,cInd,nValue,oValue)
			{
				if(stage==1)
				{
					idxPresent=prop_tb._shopshareGrid.getColIndexById('present');
					idxActive=prop_tb._shopshareGrid.getColIndexById('active');
					idxDefault=prop_tb._shopshareGrid.getColIndexById('is_default');
					
					/*prop_tb._shopshareGrid.forEachRow(function(id){
				    	if(id!=rId)
				    		prop_tb._shopshareGrid.cells(id,idxDefault).setValue(0);
				   });*/
				
					var action = "";
					if(cInd==idxActive)
						action = "active";
					else if(cInd==idxPresent)
						action = "present";
					else if(cInd==idxDefault)
						action = "default";
					
					if(action!="")
					{
						var value = prop_tb._shopshareGrid.cells(rId,cInd).isChecked();
						var ids = cat_grid.getSelectedRowId();
						var p_ids = new Array();
						if(ids.search(",")>=0)
							p_ids = ids.split(",");
						else
							p_ids[0] = ids;
					
						var nb_rows = p_ids.length*1 - 1;
					
						$.each(p_ids, function(num, p_id){
							var data = "";
							if(nb_rows!=num)
								data = "noRefreshShop";
						
							var params = {
								name: "cat_shopshare_update_queue",
								row: "",
								action: 'update',
								params: {},
								callback: "callbackShopShare('"+rId+"','update','"+rId+"','"+data+"');"
							};
							// COLUMN VALUES
							params.params['auto_share_imgs'] = auto_share_imgs;
							params.params['action_upd'] = action;
							params.params['value'] = value;
							params.params['id_lang'] = SC_ID_LANG;
							params.params['gr_id'] = p_id;
							params.params['id_shop'] = rId;
							// USER DATA
							/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
							
							params.params = JSON.stringify(params.params);
							addInUpdateQueue(params,prop_tb._shopshareGrid);
						});
					}
				}
				return true;
			});
			
			needInitShopshare=0;
		}
	}

	function setPropertiesPanel_shopshare(id){
		if (id=='shopshare')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('shopshare_refresh');
			prop_tb.showItem('shopshare_add_select');
			prop_tb.showItem('shopshare_del_select');
			prop_tb.showItem('shopshare_active_select');
			prop_tb.showItem('shopshare_inactive_select');
			prop_tb.showItem('shopshare_auto_share_imgs');
			prop_tb.setItemState("shopshare_auto_share_imgs", auto_share_imgs);
			prop_tb.setItemText('panel', '<?php echo _l('Multistore sharing manager',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/sitemap_color.png');
	 		needInitShopshare = 1;
			initShopshare();
			propertiesPanel='shopshare';
			if (lastProductSelID!=0)
			{
				displayShopshare(false);
			}
		}
		if (id=='shopshare_add_select')
		{
			var value = true;
			var ids = cat_grid.getSelectedRowId();
			var p_ids = new Array();
			if(ids.search(",")>=0)
				p_ids = ids.split(",");
			else
				p_ids[0] = ids;
		
			var nb_rows = p_ids.length*1 - 1;
		
			$.each(p_ids, function(num, p_id){
				var data = "noRefreshShop";
				if(nb_rows==num)
					data = "";
			
				var params = {
					name: "cat_shopshare_update_queue",
					row: "",
					action: 'update',
					params: {},
					callback: "callbackShopShare('','update','','"+data+"');"
				};
				// COLUMN VALUES
				params.params['auto_share_imgs'] = auto_share_imgs;
				params.params['action_upd'] = "mass_present";
				params.params['value'] = value;
				params.params['id_lang'] = SC_ID_LANG;
				 params.params['gr_id'] = p_id;
				params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
				// USER DATA
				/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
				
				params.params = JSON.stringify(params.params);
				addInUpdateQueue(params,prop_tb._shopshareGrid);
			});
		}
		if (id=='shopshare_del_select')
		{
			var value = false;
			var ids = cat_grid.getSelectedRowId();
			var p_ids = new Array();
			if(ids.search(",")>=0)
				p_ids = ids.split(",");
			else
				p_ids[0] = ids;
		
			var nb_rows = p_ids.length*1 - 1;
		
			$.each(p_ids, function(num, p_id){
				var data = "noRefreshShop";
				if(nb_rows==num)
					data = "";
			
				var params = {
					name: "cat_shopshare_update_queue",
					row: "",
					action: 'update',
					params: {},
					callback: "callbackShopShare('','update','','"+data+"');"
				};
				// COLUMN VALUES
				params.params['auto_share_imgs'] = auto_share_imgs;
				params.params['action_upd'] = "mass_present";
				params.params['value'] = value;
				params.params['id_lang'] = SC_ID_LANG;
	 			params.params['gr_id'] = p_id;
				params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
				// USER DATA
				/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
				
				params.params = JSON.stringify(params.params);
				addInUpdateQueue(params,prop_tb._shopshareGrid);
			});
		}
		if (id=='shopshare_active_select')
		{
			var value = true;
			var ids = cat_grid.getSelectedRowId();
			var p_ids = new Array();
			if(ids.search(",")>=0)
				p_ids = ids.split(",");
			else
				p_ids[0] = ids;
		
			var nb_rows = p_ids.length*1 - 1;
		
			$.each(p_ids, function(num, p_id){
				var data = "noRefreshShop";
				if(nb_rows==num)
					data = "";
			
				var params = {
					name: "cat_shopshare_update_queue",
					row: "",
					action: 'update',
					params: {},
					callback: "callbackShopShare('','update','','"+data+"');"
				};
				// COLUMN VALUES
				params.params['auto_share_imgs'] = auto_share_imgs;
				params.params['action_upd'] = "mass_active";
				params.params['value'] = value;
				params.params['id_lang'] = SC_ID_LANG;
	 			params.params['gr_id'] = p_id;
				params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
				// USER DATA
				/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
				
				params.params = JSON.stringify(params.params);
				addInUpdateQueue(params,prop_tb._shopshareGrid);
			});
		}
		if (id=='shopshare_inactive_select')
		{
			var value = false;
			var ids = cat_grid.getSelectedRowId();
			var p_ids = new Array();
			if(ids.search(",")>=0)
				p_ids = ids.split(",");
			else
				p_ids[0] = ids;
		
			var nb_rows = p_ids.length*1 - 1;
		
			$.each(p_ids, function(num, p_id){
				var data = "noRefreshShop";
				if(nb_rows==num)
					data = "";
			
				var params = {
					name: "cat_shopshare_update_queue",
					row: "",
					action: 'update',
					params: {},
					callback: "callbackShopShare('','update','','"+data+"');"
				};
				// COLUMN VALUES
				params.params['auto_share_imgs'] = auto_share_imgs;
				params.params['action_upd'] = "mass_active";
				params.params['value'] = value;
				 params.params['gr_id'] = p_id;
				 params.params['id_lang'] = SC_ID_LANG;
				params.params['id_shop'] = prop_tb._shopshareGrid.getSelectedRowId();
				// USER DATA
				/*params.params['marginMatrix_form'] = specificPrice_grid.getUserData("", "marginMatrix_form");*/
				
				params.params = JSON.stringify(params.params);
				addInUpdateQueue(params,prop_tb._shopshareGrid);
			});
		}
		if (id=='shopshare_refresh')
		{
			displayShopshare(false);
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_shopshare);
	prop_tb.attachEvent("onStateChange",function(id,state){
		if (id=='shopshare_auto_share_imgs')
		{
			if (state)
			{
				auto_share_imgs = 1;
			}else{
				auto_share_imgs = 0;
			}
		}
	});

	function displayShopshare(reloadJustChecbox)
	{
		reloadJustChecbox = false;
		if (reloadJustChecbox==true)
		{
			prop_tb._shopshareGrid.uncheckAll();
			$.post("index.php?ajax=1&act=cat_shopshare_relation_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":cat_grid.getSelectedRowId()},function(data)
			{
				idxPresent=prop_tb._shopshareGrid.getColIndexById('present');
				idxActive=prop_tb._shopshareGrid.getColIndexById('active');
				idxDefault=prop_tb._shopshareGrid.getColIndexById('is_default');
				
				if (data!='')
				{
					var shops=data.split(';');
					for(i=0 ; i < shops.length ; i++)
					{
						var values = shops[i].split(',');
						
						if (prop_tb._shopshareGrid.doesRowExist(values[0]))
						{
							prop_tb._shopshareGrid.cells(values[0],idxDefault).setValue(values[3]);
							if(values[1]=="1")
								prop_tb._shopshareGrid.cells(values[0],idxPresent).setValue(1);
							if(values[2]=="1")
								prop_tb._shopshareGrid.cells(values[0],idxActive).setValue(1);
						}
					}
				}
				
				prop_tb._shopshareGrid.forEachRow(function(id){
			    	if(prop_tb._shopshareGrid.cells(id,idxDefault).isChecked())
			    		prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(true);
			    	else
			    		prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(false);
			   });
			});
		}else{
			prop_tb._shopshareGrid.clearAll(true);
			//prop_tb._shopshareGrid.loadXML("index.php?ajax=1&act=cat_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{"idlist":(cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"")},function()
			var tempIdList = (cat_grid.getSelectedRowId()!=null?cat_grid.getSelectedRowId():"");
			$.post("index.php?ajax=1&act=cat_shopshare_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'idlist': tempIdList},function(data)
			{
				prop_tb._shopshareGrid.parse(data);
				nb=prop_tb._shopshareGrid.getRowsNum();
				prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('shops')?>":" <?php echo _l('shop')?>"));
				prop_tb._shopshareGrid._rowsNum=nb;
				
    		// UISettings
				loadGridUISettings(prop_tb._shopshareGrid);
				prop_tb._shopshareGrid._first_loading=0;
				
				idxPresent=prop_tb._shopshareGrid.getColIndexById('present');
				idxActive=prop_tb._shopshareGrid.getColIndexById('active');
				idxDefault=prop_tb._shopshareGrid.getColIndexById('is_default');
				
				prop_tb._shopshareGrid.forEachRow(function(id){
			    	if(prop_tb._shopshareGrid.cells(id,idxDefault).isChecked())
			    		prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(true);
			    	else
			    		prop_tb._shopshareGrid.cells(id,idxPresent).setDisabled(false);
			   });
			});
		}
	}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='shopshare'){
				//initShopshare();
				displayShopshare(false);
			}
		});
		
	// CALLBACK FUNCTION
	function callbackShopShare(sid,action,tid, data)
	{
		if (action=='update')
		{
			var doDisplay = true;
			if(data!="noRefreshProduct")
			{
				if(sid==shopselection)
				{
					displayProducts('displayShopshare(false)');
					doDisplay = false;
				}
			}
			if(data=="noRefreshShop")
				doDisplay = false;
			if(doDisplay==true)
			{
				displayShopshare(false);
				/*if(sid!=undefined && sid!=null && sid!="")
					prop_tb._shopshareGrid.setRowTextNormal(sid);*/
			}
		}
	}
		
<?php /*
$p=new Product(8);
$p->price=16;
$p->id_shop_list=array(1,2);
$p->save();?>

</script>
<?php */  ?>
<?php } ?>