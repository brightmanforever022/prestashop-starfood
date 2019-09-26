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
	dhxLayout.cells('a').setText('<?php echo _l('Orders',1).' '.addslashes(Configuration::get('PS_SHOP_NAME'));?>');
	dhxLayout.cells('b').setText('<?php echo _l('Properties',1)?>');
	var start_ord_size_prop = getParamUISettings('start_ord_size_prop');
	if(start_ord_size_prop==null || start_ord_size_prop<=0 || start_ord_size_prop=="")
		start_ord_size_prop = 400;
	dhxLayout.cells('b').setWidth(start_ord_size_prop);
	dhxLayout.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_ord_size_prop', dhxLayout.cells('b').getWidth())
	});
	var dhxLayoutStatus = dhxLayout.attachStatusBar();
	dhxLayoutStatus.setText("<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>");
	layoutStatusText = "<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>";
<?php
	createMenu();
?>
	statusselection=($.cookie('sc_ord_statusselection')!=null?$.cookie('sc_ord_statusselection'):'');
	periodselection=($.cookie('sc_ord_periodselection')==null?'3days':$.cookie('sc_ord_periodselection'));
	shopselection=$.cookie('sc_shop_selected')*1;
	shop_list=$.cookie('sc_shop_list');
	lastOrderSelID=0;
	lastOrderSelIDs=0;
	lightMouseNavigation=0;
	propertiesPanel='<?php echo _s('ORD_ORDPROP_GRID_DEFAULT');?>';
	tree_mode='single';
	lastColumnRightClicked_Combi=0;
	clipboardValue_Combi=null;
	clipboardType_Combi=null;
	clipboardValue=null;
	clipboardType=null;

<?php	//#####################################
			//############ Categories toolbar
			//#####################################
?>

	gridView='<?php echo _s('ORD_ORDER_GRID_DEFAULT')?>';
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
				WHERE id_shop = "'.$shop["id_shop"].'"
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
	
	ord = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	ord_firstcolcontent = ord.cells("a").attachLayout("2E");
	ord_storePanel = ord_firstcolcontent.cells('a');
	ord_filterPanel = ord_firstcolcontent.cells('b');
	ord_orderPanel = ord.cells('b');
	ord.cells("a").setText('<?php echo _l('Stores',1)?>');
	ord.cells("a").showHeader();
	ord_storePanel.hideHeader();
	var start_ord_size_store = getParamUISettings('start_ord_size_store');
	if(start_ord_size_store==null || start_ord_size_store<=0 || start_ord_size_store=="")
		start_ord_size_store = 150;
	ord_storePanel.setHeight(start_ord_size_store);
	ord_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_ord_size_store', ord_storePanel.getHeight())
	});
	ord_shoptree=ord_storePanel.attachTree();
	ord_shoptree._name='shoptree';
	ord_shoptree.autoScroll=false;
	ord_shoptree.setImagePath('lib/js/imgs/');
	ord_shoptree.enableSmartXMLParsing(true);
//	ord_shoptree.enableCheckBoxes(true, false);

	var ordShoptreeTB = ord_storePanel.attachToolbar();
	ordShoptreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	ordShoptreeTB.setItemToolTip('help','<?php echo _l('Help')?>');
	ordShoptreeTB.attachEvent("onClick", function(id) {
		if (id=='help')
		{
			var display = "";
			if(shopselection>0)
			{
				display = ord_shoptree.getItemText(shopselection);
			}
			else if(shopselection==0)
			{
				display = ord_shoptree.getItemText("all");
			}
			
			var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong>';
			dhtmlx.message({text:msg,type:'info',expire:10000});
		}
	});
	
	
	displayShopTree();
	function displayShopTree(callback) {
		ord_shoptree.deleteChildItems(0);
		ord_shoptree.loadXML("index.php?ajax=1&act=ord_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				if(shopselection!=null && shopselection!=undefined)
				if(shop_list!=null && shop_list!="")
				{
					var selected = shop_list.split(",");
					$.each(selected, function(index, id) {
						ord_shoptree.setCheck(id,1);
					});
				}
				if (shopselection!=null && shopselection!=undefined && shopselection!=0)
				{
					ord_shoptree.openItem(shopselection);
					ord_shoptree.selectItem(shopselection,true);
				}
				
				if (callback!='') eval(callback);
				ord_shoptree.openAllItems(0);
			});
	}
	ord_shoptree.attachEvent("onClick",function(idshop){
		if (idshop[0]=='G'){
			ord_shoptree.clearSelection();
			ord_shoptree.selectItem(shopselection,false);
			return false;
		}
		if (idshop == 'all'){
			idshop = 0;
		}
		if (idshop != shopselection)
		{
			if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
				ord_shoptree.setCheck(shopselection,0);
			else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
			{
				var children = ord_shoptree.getAllSubItems("all").split(",");
				ord_shoptree.setCheck("all",0);
				$.each(children, function(index, id) {
					if(id!=idshop)
						ord_shoptree.setCheck(id,0);
				});
			}
			shopselection = idshop;
			$.cookie('sc_shop_selected',shopselection, { expires: 60 });
			ord_filterPanel.setText('<?php echo _l('Categories',1).' '._l('of',1)?> '+ord_shoptree.getItemText(shopselection));
			displayFilters('displayOrders()');
		}
	});

	<?php	//#####################################
				//############ Context menu
				//#####################################
	?>
		ord_shop_cmenu_tree=new dhtmlXMenuObject();
		ord_shop_cmenu_tree.renderAsContextMenu();
		function onTreeContextButtonClickForShop(itemId){
			if (itemId=="goshop"){
				tabId=ord_shoptree.contextID;
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
		ord_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item text="<?php echo _l('See shop')?>" id="goshop"/>'+
		'</menu>';
		ord_shop_cmenu_tree.loadStruct(contextMenuXML);
		ord_shoptree.enableContextMenu(ord_shop_cmenu_tree);

		ord_shoptree.attachEvent("onBeforeContextMenu", function(itemId){

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
			
			ord_shop_cmenu_tree.setItemText('object', 'ID'+display_id+': '+display_text+ord_shoptree.getItemText(itemId));

			<?php if(SCMS) { ?>
			if(shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null)
			{
				ord_shop_cmenu_tree.setItemEnabled('goshop');
			}else{
				ord_shop_cmenu_tree.setItemDisabled('goshop');
			}
			<?php } ?>
			
			return true;
		});
<?php
	}else{
?>
	ord = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	ord_firstcolcontent = ord_filterPanel = ord.cells('a');
	ord_orderPanel = ord.cells('b');
<?php
	}
?>
	var start_ord_size_tree = getParamUISettings('start_ord_size_tree');
	if(start_ord_size_tree==null || start_ord_size_tree<=0 || start_ord_size_tree=="")
		start_ord_size_tree = 250;
	ord.cells('a').setWidth(start_ord_size_tree);
	ord.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_ord_size_tree',ord.cells('a').getWidth())
	});

	var ps_order_states = [
	<?php
		$order_states = OrderState::getOrderStates($sc_agent->id_lang);
		$return ='';
		foreach($order_states as $state) {
			$return .= (!empty($return) ? ',"'.$state['id_order_state'].'"' : '"'.$state['id_order_state'].'"');
		}
		echo $return;
	?>
	];
	var periods = [
		'1days',
		'2days',
		'3days',
		'5days',
		'10days',
		'15days',
		'30days',
		'3months',
		'6months',
		'1year',
		'all',
		'from_to',
		'inv_from_to'
	];

	// menu des filter views
	var filter_used = '';
	var filter_views_list = [];
	var filter_views = [];
	filter_views.push(['all', 'obj', '<?php echo _l('All orders')?>', '']);
	var custom_filter_views = <?php echo json_encode(CustomSettings::getCustomSettingDetail('ord', 'filters')); ?>;
	if(custom_filter_views!= null && custom_filter_views != '') {
		custom_filter_views.forEach(function (item) {
			filter_views.push([item.name, 'obj', item.name, '']);
			filter_views_list[item.name] = [item.value,item.periodselection];
		});
	}
	filter_views.push(['separator1', 'sep', '', '']);
	filter_views.push(['btn_save', 'obj', '<?php echo _l('Save filters in view')?>', '']);
	filter_views.push(['btn_del', 'obj', '<?php echo _l('Delete the filter view')?>', '']);

	ord_tb=ord_filterPanel.attachToolbar();
	ord_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	ord_tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
	ord_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	ord_tb.setItemToolTip('refresh','<?php echo _l('Refresh tree',1)?>');
	ord_tb.addButtonSelect('filter_view',0,'<?php echo _l('Filters view')?>',filter_views,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);

	ord_tb.attachEvent("onClick", function(id){
		if (id=='help'){
			<?php echo "window.open('".getHelpLink('ord_toolbar_cat')."');"; ?>
		}
		if (id=='refresh'){
			displayFilters();
		}

		if(id=='all') {
			filter_used = id;
			ps_order_states.forEach(function(item){
				ord_filter.setCheck(item,1);
			});
			ord_filter.setCheck('all',1);
			ord_filterPanel.setText('<?php echo _l('Filters',1)?>: <?php echo _l('All orders')?>');
			statusselection=ord_filter.getAllChecked();
			$.cookie('sc_ord_statusselection',statusselection, { expires: 60 });
			$.cookie('sc_ord_periodselection','all', { expires: 60 });
			displayOrders();
		}

		if (id=='btn_save'){
			filter_state_arr = [];
			filterselections = ord_filter.getAllChecked();
			filterselections = filterselections.split(',');

			filterselections.forEach(function(item){
				if(ps_order_states.indexOf(item) != -1) {
					filter_state_arr.push(item);
				}
			});

			filter_view_encoded = filter_state_arr.join();
			var filter_view_name=prompt('<?php echo _l('Name of your filter view:',1)?>');
			if (filter_view_name!=null && filter_view_name!='') {
				$.post("index.php?ajax=1&act=ord_tree_custom_update", {'action':'add', 'filter_view_encoded': filter_view_encoded, 'filter_view_name': filter_view_name, 'periodselection':periodselection}, function (data) {
					if(data !== 'KO') {
						var positionNew = 1;
                        for (var i = 0; i < filter_views.length; i++) {
                            if(filter_views[i][0] == 'btn_save') {
                                positionNew = i;
                            }
                        }
                        ord_tb.addListOption('filter_view', data, positionNew,'button',data);
                        statusselection=ord_filter.getAllChecked();
                        filter_views.push([data, 'obj', data, '']);
                        filter_views_list[data] = [filter_view_encoded,periodselection];
						dhtmlx.message({text:'<?php echo _l('Filter view added',1)?>',type:'info',expire:3000});
					} else {
						dhtmlx.message({text:'<?php echo _l('Error during add filter view',1)?>',type:'error',expire:3000});
					}
				});
			} else {
				dhtmlx.message({text:'<?php echo _l('Please choose a valid name for filter view',1)?>',type:'error',expire:3000});
			}
		}

		if (id=='btn_del'){

			if(filter_used == 'all') {
				alert('<?php echo _l('You can not delete this filter view',1)?>');
			} else {
				dhtmlx.confirm("<?php echo _l('You will delete the filter',1)?>: "+filter_used, function(result)
				{
					if(result)
					{
						$.post("index.php?ajax=1&act=ord_tree_custom_update", {'action':'delete', 'filter_used': filter_used}, function (data) {
							if(data !== 'KO') {
                                ord_tb.removeListOption('filter_view', data);
                                ord_filterPanel.setText('<?php echo _l('Filters',1)?>');
								dhtmlx.message({text:'<?php echo _l('Filter',1)?>: '+filter_used+' <?php echo _l('deleted',1)?>',type:'info',expire:3000});
							}
						});
					}
				});
			}
		}

		//filter view selection
		if (filter_views_list[id]!= null && filter_views_list[id]!= undefined && filter_views_list[id]!= ''){
			filter_used = id;
			ord_filterPanel.setText('<?php echo _l('Filters',1)?>: '+id);
			var caseToCheck = filter_views_list[id];
			statusselection=caseToCheck[0];
			periodselection=caseToCheck[1];
			$.cookie('sc_ord_statusselection',statusselection, { expires: 60 });
			$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
			displayFilters('displayOrders()');
		}
	});

<?php	//#####################################
			//############ ord_filter
			//#####################################
?>

	ord_filter = ord_filterPanel.attachTree();
	ord_filter._name='filter';
	ord_filterPanel.setText('<?php echo _l('Filters',1)?>');
	ord_orderPanel.setText('<?php echo _l('Orders',1)?>');
	ord_filter.autoScroll=false;
	ord_filter.setImagePath('lib/js/imgs/');
	ord_filter.enableCheckBoxes(true);
	ord_filter.enableThreeStateCheckboxes(true);

	displayFilters();


<?php	//#####################################
			//############ Events
			//#####################################
?>
	var timeout_filters_click = null;


	<?php if(SCSG) { ?>
		var id_selected_segment = 0;
		ord_filter.enableDragAndDrop(true);
		ord_filter.enableDragAndDropScrolling(true);
	
		ord_filter.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
			var is_segment = ord_filter.getUserData(idSource,"is_segment");
			var in_segment = ord_filter.getUserData(idTarget,"is_segment");
			if(sourceobject._name=='filter')
				 return false;

			// Si produit est déplacé dans segment
			// mais celui-ci n'accepte pas l'ajout manuel de commandes
			var manuel_add = ord_filter.getUserData(idTarget,"manuel_add");
			if(sourceobject._name=='grid' && in_segment==1 && manuel_add==1)
				return true;
			return false;
		});
		ord_filter.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
			var is_segment = ord_filter.getUserData(idTarget,"is_segment");
			if(sourceobject._name=='filter' && is_segment==1)
				 return false;
		});
		ord_filter.attachEvent("onBeforeDrag",function(sourceid){
			 return false;	
		});
		ord_filter.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
			var is_segment = ord_filter.getUserData(sourceid,"is_segment");
			var in_segment = ord_filter.getUserData(targetid,"is_segment");
			
			if (sourceobject._name=='grid')
			{
				var manuel_add = ord_filter.getUserData(targetid,"manuel_add");

				// Si ce n'est pas un segment et qu'il est déplacé dans un segment (client dans un segment)
				// et accepte l'ajout manuel de produits
				if(is_segment!=1 && in_segment==1 && manuel_add==1)
				{
					if(gridView=="grid_picking")
					{
						targetid = ord_grid.getUserData(targetid,"id_order");
					}
					$.post("index.php?ajax=1&act=ord_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'orders':sourceid},function(){});
				}
				return false;
			}
			else
				 return false;
		});


		// Context menu for grid
		ord_tree_cmenu=new dhtmlXMenuObject();
		ord_tree_cmenu.renderAsContextMenu();
		var lastColumnRightClicked_CusTree = null;
		function onCusTreeContextButtonClick(itemId){
			tabId=ord_filter.contextID.split('_');
			tabId=tabId[0];
			if (itemId=="open_segment"){
				tabId=ord_filter.contextID;

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
		}
		ord_tree_cmenu.attachEvent("onClick", onCusTreeContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="<?php echo _l('Properties')?>" id="open_segment"/>'+
			'</menu>';
		ord_tree_cmenu.loadStruct(contextMenuXML);
		ord_filter.enableContextMenu(ord_tree_cmenu);

		ord_filter.attachEvent("onBeforeContextMenu", function(itemId){
			var is_segment = ord_filter.getUserData(itemId,"is_segment");
			if(is_segment==1)
			{
				ord_tree_cmenu.setItemText('object', '<?php echo _l('Segment:')?> '+ord_filter.getItemText(itemId));							
				return true;
			}
			else
				return false;
		});
	<?php } ?>
	
	ord_filter.attachEvent("onCheck",function(idfilter, state){
			var no_display = false;
			if(!in_array(idfilter,new Array('1days','2days','3days','5days','10days','15days','30days','3months','6months','1year','all','period','from_to','inv_from_to')))
			{
				statusselection=ord_filter.getAllChecked();
				$.cookie('sc_ord_statusselection',statusselection, { expires: 60 });
			}
			if(in_array(idfilter,new Array('1days','2days','3days','5days','10days','15days','30days','3months','6months','1year','all')))
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
			}
			if(idfilter == 'from_to')
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
				
				if (dhxWins.isWindow("wOrdFilterFromTo"))
					wOrdFilterFromTo.close();
				
				wOrdFilterFromTo = dhxWins.createWindow("wOrdFilterFromTo", 170, 150, 340, 400);
				wOrdFilterFromTo.denyPark();
				wOrdFilterFromTo.denyResize();
				wOrdFilterFromTo.setIcon('lib/img/calendar.png','../../../lib/img/calendar.png');
				wOrdFilterFromTo.setText('<?php echo _l('Select the date interval to filter',1)?>');
				$.get("index.php?ajax=1&act=ord_filter_dates",function(data){
						$('#jsExecute').html(data);
					});
	
				no_display = true;
			}
			if(idfilter == 'inv_from_to')
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
				
				if (dhxWins.isWindow("wOrdFilterFromTo"))
					wOrdFilterFromTo.close();
				
				wOrdFilterFromTo = dhxWins.createWindow("wOrdFilterFromTo", 170, 150, 340, 400);
				wOrdFilterFromTo.denyPark();
				wOrdFilterFromTo.denyResize();
				wOrdFilterFromTo.setIcon('lib/img/calendar.png','../../../lib/img/calendar.png');
				wOrdFilterFromTo.setText('<?php echo _l('Select the date interval to filter',1)?>');
				$.get("index.php?ajax=1&act=ord_filter_dates&inv=1",function(data){
						$('#jsExecute').html(data);
					});
	
				no_display = true;
			}
			ord_filter.clearSelection();
			if(no_display==false && !in_array(idfilter,new Array('period')))
			{
				clearTimeout(timeout_filters_click);
				timeout_filters_click = setTimeout("displayOrders()",1000); 
			}
			id_selected_segment = null;
		});
	ord_filter.attachEvent("onClick",function(idfilter){
		var is_segment = ord_filter.getUserData(idfilter,"is_segment");

		if(is_segment!="1")
		{
			var no_display = false;
			if(!in_array(idfilter,new Array('1days','2days','3days','5days','10days','15days','30days','3months','6months','1year','all','period','from_to','inv_from_to')))
			{
				state=ord_filter.isItemChecked(idfilter);
				ord_filter.setCheck(idfilter,!state);
				statusselection=ord_filter.getAllChecked();
				$.cookie('sc_ord_statusselection',statusselection, { expires: 60 });
			}
			if(in_array(idfilter,new Array('1days','2days','3days','5days','10days','15days','30days','3months','6months','1year','all')))
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
			}
			if(idfilter == 'from_to')
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
				
				if (dhxWins.isWindow("wOrdFilterFromTo"))
					wOrdFilterFromTo.close();
				
				wOrdFilterFromTo = dhxWins.createWindow("wOrdFilterFromTo", 170, 150, 340, 400);
				wOrdFilterFromTo.denyPark();
				wOrdFilterFromTo.denyResize();
				wOrdFilterFromTo.setIcon('lib/img/calendar.png','../../../lib/img/calendar.png');
				wOrdFilterFromTo.setText('<?php echo _l('Select the date interval to filter',1)?>');
				$.get("index.php?ajax=1&act=ord_filter_dates",function(data){
						$('#jsExecute').html(data);
					});
	
				no_display = true;
			}
			if(idfilter == 'inv_from_to')
			{
				ord_filter.setCheck(idfilter,true);
				periodselection=idfilter;
				$.cookie('sc_ord_periodselection',periodselection, { expires: 60 });
				
				if (dhxWins.isWindow("wOrdFilterFromTo"))
					wOrdFilterFromTo.close();
				
				wOrdFilterFromTo = dhxWins.createWindow("wOrdFilterFromTo", 170, 150, 340, 400);
				wOrdFilterFromTo.denyPark();
				wOrdFilterFromTo.denyResize();
				wOrdFilterFromTo.setIcon('lib/img/calendar.png','../../../lib/img/calendar.png');
				wOrdFilterFromTo.setText('<?php echo _l('Select the date interval to filter',1)?>');
				$.get("index.php?ajax=1&act=ord_filter_dates&inv=1",function(data){
						$('#jsExecute').html(data);
					});
	
				no_display = true;
			}
			ord_filter.clearSelection();
			if(no_display==false && !in_array(idfilter,new Array('period')))
			{
				clearTimeout(timeout_filters_click);
				timeout_filters_click = setTimeout("displayOrders()",1000); 
			}
			id_selected_segment = null;
		}
		else
		{
			id_selected_segment = idfilter;
			displayOrders();
		}
	});

<?php	//#####################################
			//############ Display
			//#####################################
?>


	
	function displayFilters(callback)
	{
		ord_filter.deleteChildItems(0);
		ord_filter.loadXML("index.php?ajax=1&act=ord_filter_get&id_lang="+SC_ID_LANG+"&id_shop="+shopselection+"&"+new Date().getTime(),function(){
				ord_filter.enableRadioButtons('period', true);
				ord_filter.openAllItems(0);
				if (statusselection!='')
				{
					filters=statusselection.split(',');
					for(var i=0;i<filters.length;i++)
						ord_filter.setCheck(filters[i],true);
				}
				
				var periodselection_temp = periodselection;
				if(periodselection_temp.search("inv_from_to_")>=0)
					periodselection_temp = "inv_from_to";
				else if(periodselection_temp.search("from_to_")>=0)
					periodselection_temp = "from_to";
				ord_filter.setCheck(periodselection_temp,true);

				var temp = $.cookie('sc_ord_fromto_dates');
				if(temp!=undefined && temp!=null && temp!="")
				{
					var dates = temp.split("_");
					var from = dates[0];
					var to = "";
					if(dates[1]!=undefined && dates[1]!=null && dates[1]!="")
						to = dates[1];

					ord_filter.setItemText('from_to','<?php echo _l('Ord from'); ?> '+from+" <?php echo _l('to'); ?> "+to);
				}

				var temp = $.cookie('sc_ord_inv_fromto_dates');
				if(temp!=undefined && temp!=null && temp!="")
				{
					var dates = temp.split("_");
					var from = dates[0];
					var to = "";
					if(dates[1]!=undefined && dates[1]!=null && dates[1]!="")
						to = dates[1];

					ord_filter.setItemText('inv_from_to','<?php echo _l('Inv from'); ?> '+from+" <?php echo _l('to'); ?> "+to);
				}
				
				if (callback!='') eval(callback);
			});
	}
</script>
