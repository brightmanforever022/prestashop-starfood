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

	// Create interface
	var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
	dhxLayout.cells('a').setText('<?php echo _l('Catalog',1).' '.addslashes(Configuration::get('PS_SHOP_NAME'));?>');
	dhxLayout.cells('b').setText('<?php echo _l('Properties',1)?>');
	var start_cat_size_prop = getParamUISettings('start_cat_size_prop');
	if(start_cat_size_prop==null || start_cat_size_prop<=0 || start_cat_size_prop=="")
		start_cat_size_prop = 400;
	dhxLayout.cells('b').setWidth(start_cat_size_prop);
	dhxLayout.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cat_size_prop', dhxLayout.cells('b').getWidth())
	});
	var dhxLayoutStatus = dhxLayout.attachStatusBar();
	dhxLayoutStatus.setText('<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;"><img src="lib/img/ajax-loader16.gif" style="height: 10px;" /> <span></span></div>'+"<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').' - PHP '.phpversion().') <span id=\"layoutstatusloadingtime\"></span>';?>");
	layoutStatusText = '<div id="layoutstatusqueue" style="float: right; color: #ff0000; font-weight: bold;"><img src="lib/img/ajax-loader16.gif" style="height: 10px;" /> <span></span></div>'+"<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').' - PHP '.phpversion().') <span id=\"layoutstatusloadingtime\"></span>';?>";

	var EcotaxTaxRate=<?php echo SCI::getEcotaxTaxRate();?>;
	var tax_values={};
<?php
	createMenu();
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
	{
		echo "tax_values['-']=1;\n";
		$sql='SELECT trg.name, trg.id_tax_rules_group,t.rate
		FROM `'._DB_PREFIX_.'tax_rules_group` trg
		LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
 	  LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
    WHERE trg.active=1';
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row)
			echo "tax_values['".addslashes($row['name'])."']=".($row['rate']/100+1).";\n";
		echo "var tax_identifier='id_tax_rules_group';\n";
	}else{
		$sql = "SELECT id_tax,rate FROM "._DB_PREFIX_."tax";
		$res=Db::getInstance()->ExecuteS($sql);
		foreach($res as $row)
			echo "tax_values['".$row['rate']."']=".($row['rate']/100+1).";\n";
		echo "var tax_identifier='id_tax';\n";
	}
?>
	catselection=0;
	segselection=0;
	shopselection=$.cookie('sc_shop_selected')*1;
	shop_list=$.cookie('sc_shop_list');
	warehouseselection=$.cookie('sc_warehouse_selected')*1;
	warehouse_list=$.cookie('sc_warehouse_list');
	lastProductSelID=0;
	lightMouseNavigation=0;
	propertiesPanel='<?php echo _s('CAT_PRODPROP_GRID_DEFAULT');?>';
	tree_mode='single';
	displayProductsFrom='all'; // all = all categories ; default = by id_category_default
	lastColumnRightClicked_Combi=0;
	clipboardValue_Combi=null;
	clipboardType_Combi=null;
	copytocateg=false;
	clipboardValue=null;
	clipboardType=null;
	tmpcollapsedcell=false;
	featuresFilter=0;
	categoriesFilter=0;
	draggedProduct=0;
	firstProductsLoading=1;
	firstCombinationsLoading=1;
	dragdropcache='';
	combiAttrValues=new Array();
	msgFixCategories=true;

<?php	//#####################################
			//############ Categories toolbar
			//#####################################
?>

	gridView='<?php echo _s('CAT_PROD_GRID_DEFAULT')?>';
	oldGridView='';
<?php
	if (SCMS)
	{
?>
	// Url array
		var shopUrls = new Array();
		<?php
		$protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
		$sql_shop ="SELECT id_shop
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'";
		$shops = Db::getInstance()->ExecuteS($sql_shop);
		foreach($shops as $shop)
		{
			$url = Db::getInstance()->ExecuteS('SELECT *, CONCAT(domain, physical_uri, virtual_uri) AS url
				FROM '._DB_PREFIX_.'shop_url
				WHERE id_shop = '.(int)$shop["id_shop"].'
					AND active = "1"
				ORDER BY main DESC
				LIMIT 1');
			if(!empty($url[0]["url"]))
			{
				echo 'shopUrls['.$shop["id_shop"].'] = "'.$protocol.$url[0]["url"].'";'."\n";
			}
		}
		?>
	// End url array
	
	cat = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");

	<?php if(SCAS) { ?>
		cat_firstcolcontent = cat.cells("a").attachLayout("3E");
	
		cat_storePanel = cat_firstcolcontent.cells('a');
		cat_warehousePanel = cat_firstcolcontent.cells('c');
		cat_warehousePanel_name = 'c';
		cat_categoryPanel = cat_firstcolcontent.cells('b');
	<?php } else { ?>
		cat_firstcolcontent = cat.cells("a").attachLayout("2E");
		
		cat_storePanel = cat_firstcolcontent.cells('a');
		cat_categoryPanel = cat_firstcolcontent.cells('b');
	<?php } ?>
	
	cat_productPanel = cat.cells('b');


	<?php	//#####################################
				//############ Boutiques Tree
				//#####################################
	?>
	var has_shop_restrictions = false;
	
	cat.cells("a").setText('<?php echo _l('Stores',1)?>');
	cat.cells("a").showHeader();
	cat_storePanel.hideHeader();
	var start_cat_size_store = getParamUISettings('start_cat_size_store');
	if(start_cat_size_store==null || start_cat_size_store<=0 || start_cat_size_store=="")
		start_cat_size_store = 150;
	cat_storePanel.setHeight(start_cat_size_store);
	/*cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cat_size_store', cat_storePanel.getHeight())
	});*/
	cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(names){
	    $.each(names, function(num, name){
		    if(name=="a")
		    	saveParamUISettings('start_cat_size_store', cat_storePanel.getHeight())
	    });
	});
	cat_shoptree=cat_storePanel.attachTree();
	cat_shoptree._name='shoptree';
	cat_shoptree.autoScroll=false;
	cat_shoptree.setImagePath('lib/js/imgs/');
	cat_shoptree.enableSmartXMLParsing(true);
	cat_shoptree.enableCheckBoxes(true, false);

	var catShoptreeTB = cat_storePanel.attachToolbar();
	catShoptreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	catShoptreeTB.setItemToolTip('help','<?php echo _l('Help')?>');
	catShoptreeTB.attachEvent("onClick", function(id) {
		if (id=='help')
		{
			var display = "";
			var update = "";
			if(shopselection>0)
			{
				display = cat_shoptree.getItemText(shopselection);
			}
			else if(shopselection==0)
			{
				display = cat_shoptree.getItemText("all");
			}

			var all_checked = $.cookie('sc_shop_list').split(",");
			$.each(all_checked, function(index, id) {
				if(id!="all" && id.search("G")<0)
				{
					if(update!="")
						update += ", ";
					update += cat_shoptree.getItemText(id);
				}
			});
			
			var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:'));?></strong> '+update;
			dhtmlx.message({text:msg,type:'info',expire:10000});
		}
	});
	
	
	displayShopTree();
	function checkWhenSelection(idshop)
	{
		if ((idshop == 'all' || idshop==0) && has_shop_restrictions==0)
		{
			var children = cat_shoptree.getAllSubItems("all").split(",");
			cat_shoptree.setCheck("all",1);
			$.each(children, function(index, id) {
				cat_shoptree.setCheck(id,1);
				cat_shoptree.disableCheckbox(id,1);
			});
		}
		else
		{
			if(has_shop_restrictions==0)
			{
				var children = cat_shoptree.getAllSubItems("all").split(",");
				$.each(children, function(index, id) {
					cat_shoptree.disableCheckbox(id,0);
				});
			}
			else
			{
				var children = cat_shoptree.getAllSubItems(0).split(",");
				$.each(children, function(index, id) {
					cat_shoptree.disableCheckbox(id,0);
				});
			}
			if(idshop>0)
			{
				cat_shoptree.setCheck(idshop,1);
				cat_shoptree.disableCheckbox(idshop,1);
			}
		}
	}
	function deSelectParents(idshop)
	{
		if(cat_shoptree.getParentId(idshop)!="")
		{
			var parent_id = cat_shoptree.getParentId(idshop);
			cat_shoptree.setCheck(parent_id,0);
			
			deSelectParents(parent_id);
		}
	}
	function saveCheckSelection()
	{
		var checked = cat_shoptree.getAllChecked();
		if(shopselection=="all" || shopselection=="0")
		{
			checked = cat_shoptree.getAllSubItems("all");
		}
		var all_checked = checked.split(",");
		var cookie_checked = "";
		$.each(all_checked, function(index, id) {
			if(id!="all" && id.search("G")<0)
			{
				if(cookie_checked!="")
					cookie_checked += ",";
				cookie_checked += id;
			}
		});
		if(shopselection!=undefined && shopselection!="")
		{
			if(cookie_checked!="")
				cookie_checked += ",";
			cookie_checked += shopselection;
		}
		$.cookie('sc_shop_list',cookie_checked, { expires: 60 });
	}
	function displayShopTree(callback) {
		cat_shoptree.deleteChildItems(0);
		cat_shoptree.loadXML("index.php?ajax=1&act=cat_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				has_shop_restrictions = cat_shoptree.getUserData(0, "has_shop_restrictions");
				
				if(shopselection!=null && shopselection!=undefined)
					checkWhenSelection(shopselection);
				if(shop_list!=null && shop_list!="")
				{
					var selected = shop_list.split(",");
					$.each(selected, function(index, id) {
						cat_shoptree.setCheck(id,1);
					});
				}
				if (shopselection!=null && shopselection!=undefined && shopselection!=0)
				{
					cat_shoptree.openItem(shopselection);
					cat_shoptree.selectItem(shopselection,true);
				}
				
				if(has_shop_restrictions)
				{
					selected = cat_shoptree.getSelectedItemId();
					if(selected==undefined || selected==null || selected=="")
					{
						var all = cat_shoptree.getAllSubItems(0);
						if(all!=undefined && all!=null && all!="")
						{
							all = all.split(",");
							var id_to_select = "";
							$.each(all, function(index, id) {
								if(id.search("G")<0)
								{
									if(id_to_select=="")
										id_to_select = id;
								}
							});
							shopselection = id_to_select;
							cat_shoptree.openItem(shopselection);
							cat_shoptree.selectItem(shopselection,true);
							$.cookie('sc_shop_selected',shopselection, { expires: 60 });
						}
					}
				}

				if (callback!='') eval(callback);
				cat_shoptree.openAllItems(0);
			});
	}
	cat_shoptree.attachEvent("onClick",onClickShopTree);
	function onClickShopTree(idshop, param,callback){
		if (idshop[0]=='G'){
			cat_shoptree.clearSelection();
			cat_shoptree.selectItem(shopselection,false);
			return false;
		}
		if (idshop == 'all'){
			idshop = 0;
		}
		checkWhenSelection(idshop);
		if (idshop != shopselection)
		{
			if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
				cat_shoptree.setCheck(shopselection,0);
			else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
			{
				if(has_shop_restrictions==0)
				{
					var children = cat_shoptree.getAllSubItems("all").split(",");
					cat_shoptree.setCheck("all",0);
					$.each(children, function(index, id) {
						if(id!=idshop)
							cat_shoptree.setCheck(id,0);
					});
				}
				else
				{
					var children = cat_shoptree.getAllSubItems(0).split(",");
					cat_shoptree.setCheck("all",0);
					$.each(children, function(index, id) {
						if(id!=idshop)
							cat_shoptree.setCheck(id,0);
					});
				}
				
			}
			shopselection = idshop;
			$.cookie('sc_shop_selected',shopselection, { expires: 60 });
			cat_categoryPanel.setText('<?php echo _l('Categories',1).' '._l('of',1)?> '+cat_shoptree.getItemText(shopselection));
			displayTree(callback_refresh);
		}
		else
		{
			var callback_refresh = "";
			if(callback!=undefined && callback!=null && callback!="")
				callback_refresh = callback_refresh + callback;
				
			displayTree(callback_refresh);
		}
		saveCheckSelection();
	}
	
	cat_shoptree.attachEvent("onCheck",function(idshop, state){
		if(idshop=="all")
		{
			var children = cat_shoptree.getAllSubItems("all").split(",");
			$.each(children, function(index, id) {
				cat_shoptree.setCheck(id,state);
			});
		}
		else if(idshop.search("G")>=0)
		{
			var children = cat_shoptree.getAllSubItems(idshop).split(",");
			$.each(children, function(index, id) {
				cat_shoptree.setCheck(id,state);
			});
		}
		else
		{
			deSelectParents(idshop);
		}
		saveCheckSelection();
	});




	<?php	//#####################################
				//############ Context menu
				//#####################################
	?>
		var drag_disabled_for_sort = true; // for disable the drag  in tree after  sort and  "sort and save"
		cat_shop_cmenu_tree=new dhtmlXMenuObject();
		cat_shop_cmenu_tree.renderAsContextMenu();
		function onTreeContextButtonClickForShop(itemId){
			if (itemId=="goshop"){
				tabId=cat_shoptree.contextID;
	<?php
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		{
			if(SCMS) {
	?>
				if(shopUrls[tabId] != undefined && shopUrls[tabId] != "" && shopUrls[tabId] != null)
					window.open(shopUrls[tabId]);
	<?php
			}
			else { ?>
				window.open('<?php echo SC_PS_PATH_REL;?>');
			<?php }
		}else{
	?>
				window.open('<?php echo SC_PS_PATH_REL;?>');
	<?php
		}
	?>
			}
		}
		cat_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item text="<?php echo _l('See shop')?>" id="goshop"/>'+
		'</menu>';
		cat_shop_cmenu_tree.loadStruct(contextMenuXML);
		cat_shoptree.enableContextMenu(cat_shop_cmenu_tree);

		cat_shoptree.attachEvent("onBeforeContextMenu", function(itemId){

			var display_id = itemId;
			var display_text = '<?php echo _l('Shop:')?> ';
			if(itemId=="all")
			{
				return false;
			}
			else if(itemId.search("G")>=0)
			{
				var display_id = itemId.replace("G","");
				var display_text = '';
			}
			
			cat_shop_cmenu_tree.setItemText('object', 'ID'+display_id+': '+display_text+cat_shoptree.getItemText(itemId));

			<?php if(SCMS) { ?>
			if(shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null)
			{
				cat_shop_cmenu_tree.setItemEnabled('goshop');
			}else{
				cat_shop_cmenu_tree.setItemDisabled('goshop');
			}
			<?php } ?>
			
			return true;
		});
<?php
	}else{
?>
	cat = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");

	<?php if(SCAS) { ?>
	cat_firstcolcontent = cat.cells("a").attachLayout("2E");
	
	cat_warehousePanel = cat_firstcolcontent.cells('b');
	cat_warehousePanel_name = 'b';
	cat_categoryPanel = cat_firstcolcontent.cells('a');
	<?php } else { ?>
	cat_firstcolcontent = cat_categoryPanel = cat.cells('a');
	<?php } ?>
	cat_productPanel = cat.cells('b');
<?php
	}
	//#####################################
	//############ WAREHOUSE TREE
	//#####################################
	if(SCAS) { ?>
	cat_warehousePanel.setText('<?php echo _l('Warehouses',1)?>');
	cat_warehousePanel.showHeader();
	var start_cat_size_warehouse = getParamUISettings('start_cat_size_warehouse');
	if(start_cat_size_warehouse==null || start_cat_size_warehouse<=0 || start_cat_size_warehouse=="")
		start_cat_size_warehouse = 150;
	cat_warehousePanel.setHeight(start_cat_size_warehouse);
	/*cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cat_size_warehouse', cat_warehousePanel.getHeight())
	});*/
	cat_firstcolcontent.attachEvent("onPanelResizeFinish", function(names){
	    $.each(names, function(num, name){
		    if(name==cat_warehousePanel_name)
		    	saveParamUISettings('start_cat_size_warehouse', cat_warehousePanel.getHeight())
	    });
	});
	
	cat_warehousetree=cat_warehousePanel.attachTree();
	cat_warehousetree._name='warehousetree';
	cat_warehousetree.autoScroll=false;
	cat_warehousetree.setImagePath('lib/js/imgs/');
	cat_warehousetree.enableSmartXMLParsing(true);
	//cat_warehousetree.enableCheckBoxes(true, false);
	
	<?php if(_r("ACT_CAT_ADVANCED_STOCK_MANAGEMENT")) { ?>
	var catWarehousetreeTB = cat_warehousePanel.attachToolbar();
	catWarehousetreeTB.addButton("warehouses_manage", 100, "", "lib/img/building_edit.png", "lib/img/building_edit.png");
	catWarehousetreeTB.setItemToolTip('warehouses_manage','<?php echo _l('Manage warehouses',1)?>');
	/*catWarehousetreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	catWarehousetreeTB.setItemToolTip('help','<?php echo _l('Help')?>');*/
	catWarehousetreeTB.attachEvent("onClick", function(id) {
		/*if (id=='help')
		{
			var display = "";
			var update = "";
			if(warehouseselection>0)
			{
				display = cat_warehousetree.getItemText(warehouseselection);
			}
	
			var all_checked = $.cookie('sc_warehouse_list');
			if(all_checked!="" && all_checked!=null)
			{
				all_checked = all_checked.split(",");
				$.each(all_checked, function(index, id) {
					if(id!="all" && id.search("G")<0)
					{
						if(update!="")
							update += ", ";
						update += cat_warehousetree.getItemText(id);
					}
				});
				
				var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:'));?></strong> '+update;
				dhtmlx.message({text:msg,type:'info',expire:10000});
			}
		}*/
		if (id=='warehouses_manage')
		{
			if (!dhxWins.isWindow("wWarehouseManag"))
			{
				wWarehouseManag = dhxWins.createWindow("wWarehouseManag", 50, 50, 1000, $(window).height()-75);
				wWarehouseManag.button('park').hide();
				wWarehouseManag.button('minmax').hide();
				wWarehouseManag.setText('<?php echo _l('Manage warehouses',1)?>');
				wWarehouseManag.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminWarehouses&token=<?php echo $sc_agent->getPSToken('AdminWarehouses');?>");
				wWarehouseManag.attachEvent("onClose", function(win){
					displayWarehouseTree();
					return true;
				});
			}
		}
	});
	<?php } ?>
	
	<?php if(SCMS) { ?>
	cat_shoptree.attachEvent("onClick",function(idshop){
		displayWarehouseTree();
	});
	<?php } ?>
	
	function checkWhenWarehouseSelection(idwarehouse)
	{
		if(idwarehouse>0)
		{
			cat_warehousetree.setCheck(idwarehouse,1);
			cat_warehousetree.disableCheckbox(idwarehouse,1);
		}
	}
	function saveCheckWarehouseSelection()
	{
		var checked = cat_warehousetree.getAllChecked();
		var all_checked = checked.split(",");
		var cookie_checked = "";
		$.each(all_checked, function(index, id) {
			if(cookie_checked!="")
				cookie_checked += ",";
			cookie_checked += id;
		});
		if(warehouseselection!=undefined && warehouseselection!="")
		{
			if(cookie_checked!="")
				cookie_checked += ",";
			cookie_checked += warehouseselection;
		}
		$.cookie('sc_warehouse_list',cookie_checked, { expires: 60 });
		warehouse_list = cookie_checked;
	}
	function displayWarehouseTree(callback) {
		cat_warehousetree.deleteChildItems("0");
		cat_warehousetree.loadXML("index.php?ajax=1&act=cat_warehouse_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				
				/*if(warehouseselection!=null && warehouseselection!=0 && warehouseselection!=undefined)
					 checkWhenWarehouseSelection(warehouseselection);*/
	
				if(
						(warehouseselection==null || warehouseselection==0 || warehouseselection==undefined)
						||
						(
							   cat_warehousetree.getIndexById(warehouseselection)==undefined 
							|| cat_warehousetree.getIndexById(warehouseselection)==0 
							|| cat_warehousetree.getIndexById(warehouseselection)==null
						)
					)
				{
					var children = cat_warehousetree.getAllSubItems("0").split(",");
					/*$.each(children, function(index, id) {
						cat_warehousetree.disableCheckbox(id,0);
						cat_warehousetree.setCheck(id,0);
					});*/
					warehouseselection = cat_warehousetree.getItemIdByIndex(0,0);
					//checkWhenWarehouseSelection( warehouseselection );
				}
				
				if(warehouse_list!=null && warehouse_list!="")
				{
					/*var selected = warehouse_list.split(",");
					$.each(selected, function(index, id) {
						cat_warehousetree.setCheck(id,1);
					});*/
				}
				
				if (warehouseselection!=null && warehouseselection!=undefined && warehouseselection!=0)
				{
					cat_warehousetree.selectItem(warehouseselection,false);
					$.cookie('sc_warehouse_selected',warehouseselection, { expires: 60 });
				}
				
				if (callback!='') eval(callback);
			});
	}
	<?php if(SCMS) { ?>
	if(shopselection==0)
		displayWarehouseTree();
	<?php } else { ?>
	displayWarehouseTree();
	<?php } ?>
	cat_warehousetree.attachEvent("onClick",function(idwarehouse){
		//checkWhenWarehouseSelection(idwarehouse);
		if (idwarehouse != warehouseselection)
		{
			/*var children = cat_warehousetree.getAllSubItems("0").split(",");
			$.each(children, function(index, id) {
				if(id!=idwarehouse)
				{
					if(id!=warehouseselection && cat_warehousetree.isItemChecked(id))
					{
						cat_warehousetree.disableCheckbox(id,0);
						cat_warehousetree.setCheck(id,1);
					}
					else
					{
						cat_warehousetree.disableCheckbox(id,0);
						cat_warehousetree.setCheck(id,0);
					}
				}
			});*/
			
			warehouseselection = idwarehouse;
			$.cookie('sc_warehouse_selected',warehouseselection, { expires: 60 });
		}
		//saveCheckWarehouseSelection();
		displayProducts();
	});
	cat_warehousetree.attachEvent("onCheck",function(idwarehouse, state){
		//saveCheckWarehouseSelection();
	});



	<?php	//#####################################
				//############ Context menu
				//#####################################
	?>
		var id_selected_warehouse = 0;
		cat_warehouse_cmenu_tree=new dhtmlXMenuObject();
		cat_warehouse_cmenu_tree.renderAsContextMenu();
		function onTreeWarehouseContextButtonClick(itemId){
			if (itemId=="truncate"){
				askConfirmation(itemId);
			}
			if (itemId=="empty"){
				askConfirmation(itemId, 1);
			}
			if (itemId=="transfert"){
				if (dhxWins.isWindow("wWarehouseStockTransfert"))
					wWarehouseStockTransfert.close();
				wWarehouseStockTransfert = dhxWins.createWindow("wWarehouseStockTransfert", 50, 50, 450, 180);
				wWarehouseStockTransfert.setIcon('lib/img/building_go.png','../../../lib/img/building_go.png');
				wWarehouseStockTransfert.setText('<?php echo _l('Transfert stock between two warehouses',1)?>');
				$.get("index.php?ajax=1&act=cat_warehouse_transfert_window",function(data){
						$('#jsExecute').html(data);
					});
			}
			if (itemId=="synchronize"){
				$.post("index.php?ajax=1&act=cat_warehouse_synchronize&id_lang="+SC_ID_LANG,{'id_warehouse':id_selected_warehouse},function(data){
					if (data.type=='success')
						dhtmlx.message({text:'<?php echo addslashes(_l('This warehouse was successfully synchronized'));?>',type:'success',expire:5000});
					else if (data.type=='error')
						dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during synchronize operation'));?>',type:'error',expire:5000});
					if(data.debug!=undefined && data.debug!="")
						console.log(data.debug);
				}, "JSON");
			}
		}
		
		function askConfirmation(itemId, history)
		{
			var confirmation = prompt('<?php echo _l('Enter "ok" and click "Validate" (?) to empty the warehouse.',1)?>',"");
			confirmation = confirmation.toLowerCase();
			if (confirmation!=undefined && confirmation=="ok")
			{
				truncateWarehouse(itemId, history);
			} 
			else if (confirmation!=undefined && confirmation!="ok")
			{
				askConfirmation(itemId, history);
			}
		}
		function truncateWarehouse(itemId, history)
		{
			if(itemId!=undefined && itemId!=null && itemId!="")
			{
				if(history==undefined && history==null && history=="")
					history = 0;
				$.post("index.php?ajax=1&act=cat_warehouse_truncate&id_lang="+SC_ID_LANG,{'id_warehouse':id_selected_warehouse, 'history':history},function(data){
					if (data.type=='success')
						dhtmlx.message({text:'<?php echo addslashes(_l('This warehouse was successfully truncated'));?>',type:'success',expire:5000});
					else if (data.type=='error')
						dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during truncate'));?>',type:'error',expire:5000});

					if(data.debug!=undefined && data.debug!="")
						console.log(data.debug);
				}, "JSON");
			}
		}

		function transfertWarehouse(itemId, truncate_A)
		{
			if(truncate_A==undefined || truncate_A=="" || truncate_A==null)
				truncate_A = 0;
			if(itemId!=undefined && itemId!=null && itemId!="" &&  itemId!=0 && itemId!=id_selected_warehouse)
			{
				$.post("index.php?ajax=1&act=cat_warehouse_transfert&id_lang="+SC_ID_LANG,{'id_warehouse_A':id_selected_warehouse,'id_warehouse_B':itemId, 'truncate_A':truncate_A},function(data){
					if (data.type=='success')
						dhtmlx.message({text:'<?php echo addslashes(_l('The stock was successfully transfered'));?>',type:'success',expire:5000});
					else if (data.type=='error')
						dhtmlx.message({text:'<?php echo addslashes(_l('An error occured during transfer'));?>',type:'error',expire:5000});

					if(data.debug!=undefined && data.debug!="")
						console.log(data.debug);
				}, "JSON");
			}
		}
		
		cat_warehouse_cmenu_tree.attachEvent("onClick", onTreeWarehouseContextButtonClick);
		var contextMenuXMLWarehouse='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item text="<?php echo _l('Clear warehouse',1)?>" id="truncate"/>'+
			'<item text="<?php echo _l('Clear warehouse (keep history)',1)?>" id="empty"/>'+
			'<item text="<?php echo _l('Transfert',1)?>" id="transfert"/>'+
			'<item text="<?php echo _l('Synchronize',1)?>" id="synchronize"/>'+
		'</menu>';
			
		cat_warehouse_cmenu_tree.loadStruct(contextMenuXMLWarehouse);
		cat_warehousetree.enableContextMenu(cat_warehouse_cmenu_tree);

		cat_warehousetree.attachEvent("onBeforeContextMenu", function(itemId){
			id_selected_warehouse = itemId;
			cat_warehouse_cmenu_tree.setItemText('object', 'ID'+itemId+': <?php echo _l('Warehouse:',1)?> '+cat_warehousetree.getItemText(itemId));
			return true;
		});
	<?php } ?>
	
	/* CATEGORIES */
	var start_cat_size_tree = getParamUISettings('start_cat_size_tree');
	if(start_cat_size_tree==null || start_cat_size_tree<=0 || start_cat_size_tree=="")
		start_cat_size_tree = 250;
	<?php if(SCAS || SCMS) { ?>
		cat.cells("a").setWidth(start_cat_size_tree);
		cat.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cat_size_tree', cat.cells("a").getWidth())
		}); 
	<?php } else { ?>
		cat_categoryPanel.setWidth(start_cat_size_tree);
		cat.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cat_size_tree',cat_categoryPanel.getWidth())
		});
	<?php } ?>
	cat_tb=cat_categoryPanel.attachToolbar();
	cat_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cat_tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
	<?php if(_r("ACT_CAT_EMPTY_RECYCLE_BIN")) { ?>
	cat_tb.addButton("bin", 0, "", "lib/img/folder_delete.png", "lib/img/folder_delete.png");
	cat_tb.setItemToolTip('bin','<?php echo _l('Empty bin',1)?>');
	<?php } ?>
	<?php if(_r("ACT_CAT_ADD_CATEGORY")) { ?>
	cat_tb.addButton("add_ps", 0, "", "lib/img/add_ps.png", "lib/img/add_ps.png");
	cat_tb.setItemToolTip('add_ps','<?php echo _l('Create new category with the PrestaShop form',1)?>');
	cat_tb.addButton("add", 0, "", "lib/img/add.png", "lib/img/add.png");
	cat_tb.setItemToolTip('add','<?php echo _l('Create new category',1)?>');
	<?php } ?>
	cat_tb.addButton("cat_management", 0, "", "lib/img/folder_wrench.png", "lib/img/folder_wrench.png");
	cat_tb.setItemToolTip('cat_management','<?php echo _l('Categories management',1)?>');
	cat_tb.addButtonTwoState("fromIDCategDefault", 0, "", "lib/img/tree_id_categ_default.png", "lib/img/tree_id_categ_default.png");
	cat_tb.setItemToolTip('fromIDCategDefault','<?php echo _l('If enabled: display products only from their default category',1)?>');
	cat_tb.addButtonTwoState("withSubCateg", 0, "", "lib/img/chart_organisation_add.png", "lib/img/chart_organisation_add.png");
	cat_tb.setItemToolTip('withSubCateg','<?php echo _l('If enabled: display products from all subcategories',1)?>');
	cat_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	cat_tb.setItemToolTip('refresh','<?php echo _l('Refresh tree',1)?>');
	cat_tb.attachEvent("onClick",
		function(id){
			if (id=='help'){
				<?php echo "window.open('".getHelpLink('cat_toolbar_cat')."');"; ?>
			}
			if (id=='refresh'){
				displayTree();
			}
			if (id=='cat_management'){
				if (!dhxWins.isWindow("wCatManagement"))
				{
					wCatManagement = dhxWins.createWindow("wCatManagement", 0, 28, $(window).width(), $(window).height()-28);
					wCatManagement.setIcon('lib/img/folder_wrench.png','../../../lib/img/folder_wrench.png');
					wCatManagement.setText('<?php echo _l('Categories management',1)?>');
					$.get("index.php?ajax=1&act=cat_win-catmanagement_init",function(data){
							$('#jsExecute').html(data);
						});
					wCatManagement.attachEvent("onClose", function(win){
							wCatManagement.hide();
							return false;
						});
				}else{
					$.get("index.php?ajax=1&act=cat_win-catmanagement_init",function(data){
							$('#jsExecute').html(data);
						});
					wCatManagement.show();
				}
			}
			if (id=='bin'){
				if (confirm('<?php echo _l('Are you sure to delete all categories and products placed in the recycled bin?',1)?>'))
				{
					var id_bin=cat_tree.findItemIdByLabel('<?php echo _l('SC Recycle Bin')?>',0,1);
					if (id_bin==null)
						id_bin=cat_tree.findItemIdByLabel('SC Recycle Bin',0,1);
					if (id_bin!=null)
						$.get("index.php?ajax=1&act=cat_category_update&action=emptybin&id_category="+id_bin+'&id_lang='+SC_ID_LANG,function(id){
								lastProductSelID=0;
								childlist=cat_tree.getAllSubItems(id_bin).split(',');
								displayTree();
								if (catselection==id_bin || in_array(catselection,childlist))
								{
									lastProductSelID=0;
									cat_grid.clearAll();
									cat_grid_sb.setText('');
								}
							});
				}
			}
			if (id=='add'){
				if (catselection!=0)
				{
					var cname=prompt('<?php echo _l('Create a category:',1)?>');
					if (cname!=null)
						$.post("index.php?ajax=1&act=cat_category_update&action=insert&id_parent="+catselection+'&id_lang='+SC_ID_LANG,{name: (cname)},function(id){
								cat_tree.insertNewChild(catselection,id,cname,0,'../../img/folder_grey.png','../../img/folder_grey.png','../../img/folder_grey.png');
							});
				}else{
					alert('<?php echo _l('You need to select a parent category before creating a category',1)?>');
				}
			}
			if (id=='add_ps'){
				if (!dhxWins.isWindow("wNewCategory"))
				{
					wNewCategory = dhxWins.createWindow("wNewCategory", 50, 50, 1000, $(window).height()-75);
					wNewCategory.setText('<?php echo _l('Create the new category and close this window to refresh the tree',1)?>');
<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')){ ?>
					wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=admincategories&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCategories');?>");
<?php }else{ ?>
					wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCatalog&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCatalog');?>");
<?php } ?>
					wNewCategory.attachEvent("onClose", function(win){
								displayTree();
								return true;
							});
				}
			}
		}
		);
	cat_tb.attachEvent("onStateChange", function(id,state){
			if (id=='withSubCateg'){
				if (state) {
					tree_mode='all';
//				  cat_grid.enableSmartRendering(true);
//				  cat_grid_tb.disableItem('selectall');
				  cat_grid_tb.disableItem('setposition');
				}else{
					tree_mode='single';
//				  cat_grid.enableSmartRendering(false);
//				  cat_grid_tb.enableItem('selectall');
				  cat_grid_tb.enableItem('setposition');
				}
				displayProducts();
			}
			if (id=='fromIDCategDefault'){
				if (state) {
					displayProductsFrom='default';
				}else{
					displayProductsFrom='all';
				}
				displayProducts();
			}
		});
		$(document).ready(function(){
				if (<?php echo Tools::getValue('displayAllProducts',0);?>)
					onMenuClick('cat_grid','','');
		});

<?php	//#####################################
			//############ cat_tree
			//#####################################
?>

	cat_tree=cat_categoryPanel.attachTree();
	cat_tree._name='tree';
	cat_categoryPanel.setText('<?php echo _l('Categories',1)?><?php if(SCSG) echo ' '._l('& segments',1) ?>');
	cat_productPanel.setText('<?php echo _l('Products',1)?>');
	cat_tree.autoScroll=false;
	cat_tree.setImagePath('lib/js/imgs/');
	cat_tree.enableSmartXMLParsing(true);
	<?php if(!SCSG && !_r("ACT_CAT_MOVE_CATEGORY") && !_r("ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY")) { ?>
		cat_tree.enableDragAndDrop(false);
	<?php } else { ?>
		cat_tree.enableDragAndDrop(true);
	<?php } ?>
	cat_tree.setDragBehavior("complex");
	cat_tree._dragBehavior="complex";


	function nameSortCatTree(idA,idB)
	{
		var a = latinise(cat_tree.getItemText(idA)).toLowerCase();
		var b = latinise(cat_tree.getItemText(idB)).toLowerCase();
		if ( a < b )
			return -1;
		if ( a > b )
			return 1;
		return 0;
	}
	
<?php if (!SCMS){ ?>
		displayTree();
<?php } ?>


<?php	//#####################################
			//############ Context menu
			//#####################################
?>
	cat_cmenu_tree=new dhtmlXMenuObject();
	cat_cmenu_tree.renderAsContextMenu();
	function onTreeContextButtonClick(itemId){
		if (itemId=="gopsbo"){
			tabId=cat_tree.contextID;
			wModifyCategory = dhxWins.createWindow("wModifyCategory", 50, 50, 1000, $(window).height()-75);
			wModifyCategory.setText('<?php echo _l('Modify the category and close this window to refresh the tree',1)?>');
			wModifyCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'admincategories':'AdminCatalog');?>&updatecategory&id_category="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken((version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'AdminCategories':'AdminCatalog'));?>");
			wModifyCategory.attachEvent("onClose", function(win){
						displayTree();
						return true;
					});
		}
		if (itemId=="goshop"){
			tabId=cat_tree.contextID;
<?php
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		if(SCMS) {
?>
			if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
				window.open(shopUrls[shopselection]+'index.php?id_category='+tabId+'&controller=category&id_lang='+SC_ID_LANG);
<?php
		}
		else { ?>
			window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_category='+tabId+'&controller=category&id_lang='+SC_ID_LANG);
		<?php }
	}else{
?>
			window.open('<?php echo SC_PS_PATH_REL;?>category.php?id_category='+tabId);
<?php
	}
?>
		}
		if (itemId=="expand"){
			tabId=cat_tree.contextID;
			cat_tree.openAllItems(tabId);
		}
		if (itemId=="collapse"){
			tabId=cat_tree.contextID;
			cat_tree.closeAllItems(tabId);
			if (tabId==1) cat_tree.openItem(1);
		}
		if (itemId=="sort"){
			drag_disabled_for_sort = false;
			tabId=cat_tree.contextID;
			cat_tree.setCustomSortFunction(nameSortCatTree);
			cat_tree.sortTree(tabId,'ASC',1);
			dhtmlx.message({text:'<?php echo addslashes(_l('Category sorted, click on the Refresh icon to allow reorder (drag and drop) on the categories tree.'));?>',type:'info',expire:5000});
		}
		if (itemId=="sort_and_save"){
			drag_disabled_for_sort = false;
			tabId=cat_tree.contextID;
			cat_tree.setCustomSortFunction(nameSortCatTree);

			var children = cat_tree.getSubItems(tabId).split(",");
			cat_tree.sortTree(tabId,'ASC',1);
			children = cat_tree.getSubItems(tabId);

			$.post("index.php?ajax=1&act=cat_category_update&action=sort_and_save&id_category="+tabId,{'children':children},function(){
				dhtmlx.message({text:'<?php echo addslashes(_l('Category sorted and positions recorded'));?>',type:'success',expire:5000});});

		}
		if (itemId=="enable"){
			tabId=cat_tree.contextID;
			todo=(cat_tree.getItemImage(tabId,0,false)=='catalog.png'?0:1);
			$.get("index.php?ajax=1&act=cat_category_update&action=enable&id_category="+tabId+'&enable='+todo,function(id){
					if (todo){
						cat_tree.setItemImage2(tabId,'catalog.png','catalog.png','catalog.png');
					}else{
						cat_tree.setItemImage2(tabId,'folder_grey.png','folder_grey.png','folder_grey.png');
					}
				});
		}
		if (itemId=="open_segment"){
			tabId=cat_tree.contextID;

			if (!dhxWins.isWindow("toolsSegmentationWindow"))
			{
				toolsSegmentationWindow = dhxWins.createWindow("toolsSegmentationWindow", 50, 50, $(window).width()-100, $(window).height()-100);
				toolsSegmentationWindow.setIcon('lib/img/segmentation.png','../../../lib/img/segmentation.png');
				toolsSegmentationWindow.setText("Segmentation");
				toolsSegmentationWindow.attachEvent("onClose", function(win){
						toolsSegmentationWindow.hide();
						return false;
					});
				$.get("index.php?ajax=2&p=segmentation/win_segmentation&selectedSegmentId="+tabId.replace("seg_",""),function(data){
						$('#jsExecute').html(data);
					});
				
			}else{
				$.get("index.php?ajax=2&p=segmentation/win_segmentation&selectedSegmentId="+tabId.replace("seg_",""),function(data){
						$('#jsExecute').html(data);
					});
				toolsSegmentationWindow.show();
			}
		}
		<?php if(SCSG) echo SegmentHook::hook("productSegmentRightClickItemsAction"); ?>
	}
	cat_cmenu_tree.attachEvent("onClick", onTreeContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('Expand')?>" id="expand"/>'+
		'<item text="<?php echo _l('Collapse')?>" id="collapse"/>'+
		'<item text="<?php echo _l('Sort')?>" id="sort"/>'+
		'<item text="<?php echo _l('Sort and save')?>" id="sort_and_save"/>'+
		'<item text="<?php echo _l('See on shop')?>" id="goshop"/>'+
		'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
		<?php if(_r("ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
		'<item text="<?php echo _l('Enable / Disable')?>" id="enable"/>'+
		<?php } ?>
		<?php if(SCSG) { ?>
		'<item text="<?php echo _l('Properties')?>" id="open_segment"/>'+
		<?php echo SegmentHook::hook("productSegmentRightClickDefinition"); ?>
		<?php } ?>
	'</menu>';
	cat_cmenu_tree.loadStruct(contextMenuXML);
	cat_tree.enableContextMenu(cat_cmenu_tree);

<?php	//#####################################
			//############ Events
			//#####################################
?>
	cat_tree.attachEvent("onClick",function(idcategory){
			var is_segment = cat_tree.getUserData(idcategory,"is_segment");

			if(is_segment==1)
			{
				if (idcategory!=catselection)
				{
					catselection=idcategory;
					segselection = idcategory;
					//catDataProcessor.serverProcessor=catDataProcessorURLBase+'&id_segment='+catselection;
					displayProducts();
					if (propertiesPanel=='accessories' && accessoriesFilter)
					{
						prop_tb._accessoriesGrid.clearAll(true);
						prop_tb._accessoriesGrid._rowsNum=0;
						displayAccessories('',0);
					}
				}
				cat_productPanel.setText('<?php echo _l('Products',1).' '._l('of segment',1)?> '+cat_tree.getItemText(catselection));
			}
			else
			{			
				if (idcategory!=catselection || SCMS)
				{
					catselection=idcategory;
					segselection = 0;
					//catDataProcessor.serverProcessor=catDataProcessorURLBase+'&id_category='+catselection;
					if(display_products_after_cat_select)
						displayProducts();
					else
						display_products_after_cat_select = true;
					if (propertiesPanel=='accessories' && accessoriesFilter)
					{
						prop_tb._accessoriesGrid.clearAll(true);
						prop_tb._accessoriesGrid._rowsNum=0;
						displayAccessories('',0);
					}
				}
				cat_productPanel.setText('<?php echo _l('Products',1).' '._l('of',1)?> '+cat_tree.getItemText(catselection)+(shopselection?' / '+cat_shoptree.getItemText(shopselection):''));
			}
		});
	cat_tree.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
			var is_segment = cat_tree.getUserData(idSource,"is_segment");
			var in_segment = cat_tree.getUserData(idTarget,"is_segment");
			if (drag_disabled_for_sort ==  false){
				if ( sourceobject._name=='tree' ){
					return false ;
				}
			}
			if(sourceobject._name=='tree' && is_segment==1)
				 return false;
			if(sourceobject._name=='tree' && in_segment==1)
				 return false;

			var is_FF = cat_tree.getUserData(idSource,"is_FF");
            var in_FF = cat_tree.getUserData(idTarget,"is_FF");
            var not_associate_FF = cat_tree.getUserData(idTarget,"not_associate_FF");
			if(sourceobject._name=='tree' && is_FF==1)
				return false;
            if(sourceobject._name=='tree' && in_FF==1)
                return false;
            if(sourceobject._name=='grid' && in_FF==1 && not_associate_FF==1)
                return false;

			if(idSource!=undefined && idSource!=0 && is_segment!=1)
			{
				var is_home = cat_tree.getUserData(idSource,"is_home");
				if(is_home==1)
				{
					return false;
				}
			}
			<?php if(!_r("ACT_CAT_MOVE_CATEGORY")) { ?>
				if (sourceobject._name=='tree') return false;
			<?php } ?>
			<?php if(!_r("ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY")) { ?>
				if (sourceobject._name=='grid' && targetobject._name=='tree' && is_segment!=1) return false;
			<?php } ?>

			// Si produit est déplacé dans segment
			// mais celui-ci n'accepte pas l'ajout manuel de produits
			var manuel_add = cat_tree.getUserData(idTarget,"manuel_add");
			if(sourceobject._name=='grid' && in_segment==1 && manuel_add!=1)
				return false;

			if (sourceobject._name=='tree' || sourceobject._name=='grid') return true;
			return false;
		});
	cat_tree.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
			var is_segment = cat_tree.getUserData(idTarget,"is_segment");
			if(sourceobject._name=='tree' && is_segment==1)
				 return false;

			var real_parent_id = idTarget;
			if(real_parent_id==0)
			{
				real_parent_id = cat_tree.getUserData(idSource,"parent_root");
			}
			 
			if (sourceobject._name=='tree')
				$.get("index.php?ajax=1&act=cat_category_update&action=move&idCateg="+idSource+"&idNewParent="+real_parent_id+"&idNextBrother="+idBefore+'&id_lang='+SC_ID_LANG, function(data){
<?php
$sqlc="SELECT COUNT(*) AS nbc FROM "._DB_PREFIX_."category";
$nbCateg=_qgv($sqlc);
if ($nbCateg > 10)
{
?>
						if (msgFixCategories)
						{
							dhtmlx.message({text:'<?php echo addslashes(_l('Note: you will need to use the menu "Catalog > Tools > Check and fix categories" after your moves operation.'));?>',type:'info',expire:10000});							
							msgFixCategories=false;
						}
<?php
}
?>
					});
		});
	cat_tree.attachEvent("onBeforeContextMenu", function(itemId){
			var is_segment = cat_tree.getUserData(itemId,"is_segment");
			if(is_segment==1)
			{
				cat_cmenu_tree.setItemText('object', '<?php echo _l('Segment:')?> '+cat_tree.getItemText(itemId));
				cat_cmenu_tree.hideItem('sort');
				cat_cmenu_tree.hideItem('goshop');
				cat_cmenu_tree.hideItem('gopsbo');
				<?php if(_r("ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
				cat_cmenu_tree.hideItem('enable');
				<?php } ?>
				cat_cmenu_tree.showItem('open_segment');
				<?php if(SCSG) echo SegmentHook::hook("productSegmentRightClickShowItems"); ?>
			}
			else
			{
				cat_cmenu_tree.setItemText('object', 'ID'+itemId+': <?php echo _l('Category:')?> '+cat_tree.getItemText(itemId));
				cat_cmenu_tree.showItem('sort');
				cat_cmenu_tree.showItem('goshop');
				cat_cmenu_tree.showItem('gopsbo');
				<?php if(_r("ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
				cat_cmenu_tree.showItem('enable');
				<?php } ?>
				cat_cmenu_tree.hideItem('open_segment');
				<?php if(SCMS) { ?>
				if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
				{
					cat_cmenu_tree.setItemEnabled('goshop');
				}else{
					cat_cmenu_tree.setItemDisabled('goshop');
				}
				<?php } ?>
				<?php if(SCSG) echo SegmentHook::hook("productSegmentRightClickHideItems"); ?>
			}
			return true;
		});
	cat_tree.attachEvent("onBeforeDrag",function(sourceid){
		var is_segment = cat_tree.getUserData(sourceid,"is_segment");

		if(is_segment==1)
			 return false;

		 return true;		
	});
	cat_tree.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
		var is_segment = cat_tree.getUserData(sourceid,"is_segment");
		var in_segment = cat_tree.getUserData(targetid,"is_segment");

		if(sourceobject._name=='tree' && is_segment==1)
			 return false;
		if(sourceobject._name=='tree' && in_segment==1)
			 return false;

		if(sourceid!=undefined && sourceid!=0 && targetid!=undefined && targetid!=0 && is_segment!=1)
		{
			var is_recycle_bin = cat_tree.getUserData(targetid,"is_recycle_bin");
			if(is_recycle_bin==1)
			{
				var not_deletable = cat_tree.getUserData(sourceid,"not_deletable");
				if(not_deletable==1)
					return false;
			}
			var is_home = cat_tree.getUserData(sourceid,"is_home");
			if(is_home==1)
			{
				return false;
			}
		}

		<?php if(!_r("ACT_CAT_MOVE_CATEGORY")) { ?>
			if (sourceobject._name=='tree') return false;
		<?php } ?>
		<?php if(!_r("ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY")) { ?>
			if (sourceobject._name=='grid' && targetobject._name=='tree' && is_segment!=1) return false;
		<?php } ?>
		if (targetid==0) {targetid=cat_tree.getUserData("","parent_root"); /*return false;*/}
		if (sourceobject._name=='grid')
		{
			var manuel_add = cat_tree.getUserData(targetid,"manuel_add");

			// Si ce n'est pas un segment et qu'il n'est pas déplacé dans un segment (produit dans une catégorie)
			if(is_segment!=1 && in_segment!=1)
			{
				if (copytocateg)
				{
					targetobject.setItemStyle(targetid,'background-color:#fedead;');
					var products=cat_grid.getSelectedRowId();
					if (products==null && draggedProduct!=0) products=draggedProduct;
					draggedProduct=0;
					if (dragdropcache!=catselection+'-'+targetid+'-'+products)
					{
						$.post("index.php?ajax=1&act=cat_category_dropproductoncategory&mode=copy&id_lang="+SC_ID_LANG,{'displayProductsFrom':displayProductsFrom,'categoryTarget':targetid,'categorySource':catselection,'products':products},function(){
							if (propertiesPanel=='categories')
								displayCategories();
							});
						dragdropcache=catselection+'-'+targetid+'-'+products;
					}
				}else{
					targetobject.setItemStyle(targetid,'background-color:#fedead;');
					var products=cat_grid.getSelectedRowId();
					if (products==null && draggedProduct!=0) products=draggedProduct;
					if (dragdropcache!=catselection+'-'+targetid+'-'+products)
					{
						var categorySource = catselection;
						if(tree_mode=='all')
						{
							categorySource = cat_tree.getAllSubItems(catselection);
						}
					$.post("index.php?ajax=1&act=cat_category_dropproductoncategory&mode=move&id_lang="+SC_ID_LANG,{'categoryTarget':targetid,'categorySource':categorySource,'products':products},function(){
						if (draggedProduct>0)
						{
							setTimeout('cat_grid.deleteRow('+draggedProduct+');',200);
						}else{
							setTimeout('cat_grid.deleteSelectedRows();',200);
						}
						if (propertiesPanel=='categories')
							displayCategories();
						draggedProduct=0;
						});
						dragdropcache=catselection+'-'+targetid+'-'+products;
					}
				}
			}
			// Si ce n'est pas un segment et qu'il est déplacé dans un segment (produit dans un segment)
			// et accepte l'ajout manuel de produits
			else if(is_segment!=1 && in_segment==1 && manuel_add==1)
			{
				var products=cat_grid.getSelectedRowId();
				if (products==null && draggedProduct!=0) products=draggedProduct;
				$.post("index.php?ajax=1&act=cat_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'products':products},function(){});
			}
			return false;
		}else{
			if (sourceobject._name=='tree')
				return true;
			return false;
		}
		});
		
	/*cat_tree.attachEvent("onBeforeDrag",function(idsource){
			if (cat_tree._dragBehavior!="sibling")
			{
				cat_tree.setDragBehavior("complex");
				cat_tree._dragBehavior="complex";
			}
			return true;
		});*/

<?php	//#####################################
			//############ Display
			//#####################################
?>

	function displayTree(callback)
	{
		cat_tree.deleteChildItems(0);
		cat_tree.loadXML("index.php?ajax=1&act=cat_category_get&id_lang="+SC_ID_LANG+"&id_shop="+shopselection+"&"+new Date().getTime(),function(){
				if (catselection!=0)  //  && !cat_categoryPanel.isCollapsed()
				{
					var cat_pos = cat_tree.getIndexById(catselection);
					if((cat_pos!=undefined && cat_pos!==false && cat_pos!=null && cat_pos!="") || cat_pos===0)
					{
						cat_tree.openItem(catselection);
						cat_tree.selectItem(catselection,true);
						
						if (callback!='') eval(callback);
					}
					else
					{
						cat_grid.clearAll(true);
					}

				}
				else
				{
					if (callback!='') eval(callback);
				}
			   drag_disabled_for_sort = true;
		});
	}

	<?php if(SCMS) { ?>
	if(shopselection=="all" || shopselection=="0")
		displayTree();
	<?php } ?>
</script>
