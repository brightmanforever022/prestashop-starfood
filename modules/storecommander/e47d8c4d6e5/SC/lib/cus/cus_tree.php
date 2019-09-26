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
	dhxLayout.cells('a').setText('<?php echo _l('Customers',1).' '.addslashes(Configuration::get('PS_SHOP_NAME'));?>');
	dhxLayout.cells('b').setText('<?php echo _l('Properties',1)?>');
	var start_cus_size_prop = getParamUISettings('start_cus_size_prop');
	if(start_cus_size_prop==null || start_cus_size_prop<=0 || start_cus_size_prop=="")
		start_cus_size_prop = 400;
	dhxLayout.cells('b').setWidth(start_cus_size_prop);
	dhxLayout.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cus_size_prop', dhxLayout.cells('b').getWidth())
	});
	var dhxLayoutStatus = dhxLayout.attachStatusBar();
	dhxLayoutStatus.setText("<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>");
	layoutStatusText = "<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' '._l('products')).' - Version '.SC_VERSION.(SC_BETA?' BETA':'').(SC_GRIDSEDITOR_INSTALLED?' GE'.(SC_GRIDSEDITOR_PRO_INSTALLED?'P':''):'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>";
<?php
	createMenu();
?>
	groupselection=0;
	shopselection=$.cookie('sc_shop_selected')*1;
	shop_list=$.cookie('sc_shop_list');
	lastCustomerSelID=0;
	propertiesPanel='<?php echo _s('CUS_PRODPROP_GRID_DEFAULT');?>';
	lastColumnRightClicked_Combi=0;
	clipboardValue=null;
	clipboardType=null;

<?php	//#####################################
			//############ Categories toolbar
			//#####################################
?>

	gridView='<?php echo _s('CUS_CUSTOMER_GRID_DEFAULT')?>';
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
	
	cus = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	cus_firstcolcontent = cus.cells("a").attachLayout("2E");
	cus_storePanel = cus_firstcolcontent.cells('a');
	cus_filterPanel = cus_firstcolcontent.cells('b');
	cus_customerPanel = cus.cells('b');
	cus.cells("a").setText('<?php echo _l('Stores',1)?>');
	cus.cells("a").showHeader();
	cus_storePanel.hideHeader();
	var start_cus_size_store = getParamUISettings('start_cus_size_store');
	if(start_cus_size_store==null || start_cus_size_store<=0 || start_cus_size_store=="")
		start_cus_size_store = 150;
	cus_storePanel.setHeight(start_cus_size_store);
	cus_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cus_size_store', cus_storePanel.getHeight())
	});
	cus_shoptree=cus_storePanel.attachTree();
	cus_shoptree._name='shoptree';
	cus_shoptree.autoScroll=false;
	cus_shoptree.setImagePath('lib/js/imgs/');
	cus_shoptree.enableSmartXMLParsing(true);
	cus_shoptree.enableCheckBoxes(true, false);

	var cusShoptreeTB = cus_storePanel.attachToolbar();
	cusShoptreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cusShoptreeTB.setItemToolTip('help','<?php echo _l('Help')?>');
	cusShoptreeTB.attachEvent("onClick", function(id) {
		if (id=='help')
		{
			var display = "";
			var update = "";
			if(shopselection>0)
			{
				display = cus_shoptree.getItemText(shopselection);
			}
			else if(shopselection==0)
			{
				display = cus_shoptree.getItemText("all");
			}

			var all_checked = $.cookie('sc_shop_list').split(",");
			$.each(all_checked, function(index, id) {
				if(id!="all" && id.search("G")<0)
				{
					if(update!="")
						update += ", ";
					update += cus_shoptree.getItemText(id);
				}
			});
			
			var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:'));?></strong> '+update;
			dhtmlx.message({text:msg,type:'info',expire:10000});
		}
	});
	
	
	displayShopTree();
	function checkWhenSelection(idshop)
	{
		if (idshop == 'all' || idshop==0)
		{
			var children = cus_shoptree.getAllSubItems("all").split(",");
			cus_shoptree.setCheck("all",1);
			$.each(children, function(index, id) {
				cus_shoptree.setCheck(id,1);
				cus_shoptree.disableCheckbox(id,1);
			});
		}
		else
		{
			var children = cus_shoptree.getAllSubItems("all").split(",");
			$.each(children, function(index, id) {
				cus_shoptree.disableCheckbox(id,0);
			});
			if(idshop>0)
			{
				cus_shoptree.setCheck(idshop,1);
				cus_shoptree.disableCheckbox(idshop,1);
			}
		}
	}
	function deSelectParents(idshop)
	{
		if(cus_shoptree.getParentId(idshop)!="")
		{
			var parent_id = cus_shoptree.getParentId(idshop);
			cus_shoptree.setCheck(parent_id,0);
			
			deSelectParents(parent_id);
		}
	}
	function saveCheckSelection()
	{
		var checked = cus_shoptree.getAllChecked();
		if(shopselection=="all" || shopselection=="0")
		{
			checked = cus_shoptree.getAllSubItems("all");
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
		cus_shoptree.deleteChildItems(0);
		cus_shoptree.loadXML("index.php?ajax=1&act=cus_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				if(shopselection!=null && shopselection!=undefined)
					checkWhenSelection(shopselection);
				if(shop_list!=null && shop_list!="")
				{
					var selected = shop_list.split(",");
					$.each(selected, function(index, id) {
						cus_shoptree.setCheck(id,1);
					});
				}
				if (shopselection!=null && shopselection!=undefined && shopselection!=0)
				{
					cus_shoptree.openItem(shopselection);
					cus_shoptree.selectItem(shopselection,true);
				}
				
				if (callback!='') eval(callback);
				cus_shoptree.openAllItems(0);
			});
	}
	cus_shoptree.attachEvent("onClick",function(idshop){
		if (idshop[0]=='G'){
			cus_shoptree.clearSelection();
			cus_shoptree.selectItem(shopselection,false);
			return false;
		}
		if (idshop == 'all'){
			idshop = 0;
		}
		checkWhenSelection(idshop);
		if (idshop != shopselection)
		{
			if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
				cus_shoptree.setCheck(shopselection,0);
			else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
			{
				var children = cus_shoptree.getAllSubItems("all").split(",");
				cus_shoptree.setCheck("all",0);
				$.each(children, function(index, id) {
					if(id!=idshop)
						cus_shoptree.setCheck(id,0);
				});
			}
			shopselection = idshop;
			$.cookie('sc_shop_selected',shopselection, { expires: 60 });
			cus_filterPanel.setText('<?php echo _l('Filters',1).' '._l('for',1)?> '+cus_shoptree.getItemText((shopselection==0?'all':shopselection)));
			displayFilters();
			displayCustomers();
		}
		saveCheckSelection();
	});
	cus_shoptree.attachEvent("onCheck",function(idshop, state){
		if(idshop=="all")
		{
			var children = cus_shoptree.getAllSubItems("all").split(",");
			$.each(children, function(index, id) {
				cus_shoptree.setCheck(id,state);
			});
		}
		else if(idshop.search("G")>=0)
		{
			var children = cus_shoptree.getAllSubItems(idshop).split(",");
			$.each(children, function(index, id) {
				cus_shoptree.setCheck(id,state);
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
		cus_shop_cmenu_tree=new dhtmlXMenuObject();
		cus_shop_cmenu_tree.renderAsContextMenu();
		function onTreeContextButtonClickForShop(itemId){
			if (itemId=="goshop"){
				tabId=cus_shoptree.contextID;
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
		cus_shop_cmenu_tree.attachEvent("onClick", onTreeContextButtonClickForShop);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item text="<?php echo _l('See shop')?>" id="goshop"/>'+
		'</menu>';
		cus_shop_cmenu_tree.loadStruct(contextMenuXML);
		cus_shoptree.enableContextMenu(cus_shop_cmenu_tree);

		cus_shoptree.attachEvent("onBeforeContextMenu", function(itemId){

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
			
			cus_shop_cmenu_tree.setItemText('object', 'ID'+display_id+': '+display_text+cus_shoptree.getItemText(itemId));

			<?php if(SCMS) { ?>
			if(shopUrls[itemId] != undefined && shopUrls[itemId] != "" && shopUrls[itemId] != null)
			{
				cus_shop_cmenu_tree.setItemEnabled('goshop');
			}else{
				cus_shop_cmenu_tree.setItemDisabled('goshop');
			}
			<?php } ?>
			
			return true;
		});
<?php
	}else{
?>
	cus = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	cus_firstcolcontent = cus_filterPanel = cus.cells('a');
	cus_customerPanel = cus.cells('b');
<?php
	}
?>
	var start_cus_size_tree = getParamUISettings('start_cus_size_tree');
	if(start_cus_size_tree==null || start_cus_size_tree<=0 || start_cus_size_tree=="")
		start_cus_size_tree = 250;
	<?php if(SCMS) { ?>
		cus.cells("a").setWidth(start_cus_size_tree);
		cus.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cus_size_tree', cus.cells("a").getWidth())
		}); 
	<?php } else { ?>
		cus_filterPanel.setWidth(start_cus_size_tree);
		cus.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cus_size_tree',cus_filterPanel.getWidth())
		});
	<?php } ?>

<?php
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
?>	
	cat_tb=cus_filterPanel.attachToolbar();
	cat_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cat_tb.setItemToolTip('help','<?php echo _l('Help',1)?>');
	<?php if (
				(version_compare(_PS_VERSION_, '1.6.0.0', '<'))
				||
				(version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_GROUP_FEATURE_ACTIVE")>0)
		) { ?>
	cat_tb.addButton("group_add", 0, "", "lib/img/add.png", "lib/img/add.png");
	cat_tb.setItemToolTip('group_add','<?php echo _l('Add group',1)?>');
	<?php } ?>
	cat_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	cat_tb.setItemToolTip('refresh','<?php echo _l('Refresh tree',1)?>');
	cat_tb.attachEvent("onClick",
		function(id){
			if (id=='help'){
				<?php echo "window.open('".getHelpLink('cus_toolbar_cat')."');"; ?>
			}
			if (id=='refresh'){
				displayFilters();
			}
			if(id=='group_add'){
				var gname=prompt('<?php echo _l('Create a group:',1)?>');
				if (gname!=null)
					$.get("index.php?ajax=1&act=cus_group_update&action=insert&id_lang="+SC_ID_LANG+'&name='+escape(gname),function(id){
						if(id!=null && id!=0 && id!="")
						{
							displayFilters();
						}
					});
			}
		}
		);

<?php
	}
	
			//#####################################
			//############ cus_filter
			//#####################################
?>

	cus_filter=cus_filterPanel.attachTree();
	cus_filter._name='filter';
	cus_filterPanel.setText('<?php echo _l('Filters',1)?>');
	cus_customerPanel.setText('<?php echo _l('Customers',1)?>');
	cus_filter.autoScroll=false;
	cus_filter.setImagePath('lib/js/imgs/');
<?php
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
?>
	cus_filter.enableCheckBoxes(true);
	cus_filter.enableThreeStateCheckboxes(true);

<?php	//#####################################
			//############ Events
			//#####################################
?>
	<?php if(SCSG) { ?>
		var id_selected_segment = 0;
		cus_filter.enableDragAndDrop(true);
		cus_filter.enableDragAndDropScrolling(true);
	
		cus_filter.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
			var is_segment = cus_filter.getUserData(idSource,"is_segment");
			var in_segment = cus_filter.getUserData(idTarget,"is_segment");
			if(sourceobject._name=='filter')
				 return false;

			// Si produit est déplacé dans segment
			// mais celui-ci n'accepte pas l'ajout manuel de produits
			var manuel_add = cus_filter.getUserData(idTarget,"manuel_add");
			if(sourceobject._name=='grid' && in_segment==1 && manuel_add==1)
				return true;
			return false;
		});
		cus_filter.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
			var is_segment = cus_filter.getUserData(idTarget,"is_segment");
			if(sourceobject._name=='filter' && is_segment==1)
				 return false;
		});
		cus_filter.attachEvent("onBeforeDrag",function(sourceid){
			 return false;	
		});
		cus_filter.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
			var is_segment = cus_filter.getUserData(sourceid,"is_segment");
			var in_segment = cus_filter.getUserData(targetid,"is_segment");
			
			if (sourceobject._name=='grid')
			{
				var manuel_add = cus_filter.getUserData(targetid,"manuel_add");

				// Si ce n'est pas un segment et qu'il est déplacé dans un segment (client dans un segment)
				// et accepte l'ajout manuel de produits
				if(is_segment!=1 && in_segment==1 && manuel_add==1)
				{
					if(gridView=="grid_address")
					{
						targetid = cus_grid.getUserData(targetid,"id_customer");
					}
					$.post("index.php?ajax=1&act=cus_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'customers':sourceid},function(){});
				}
				return false;
			}
			else
				 return false;
		});


		// Context menu for grid
		cus_tree_cmenu=new dhtmlXMenuObject();
		cus_tree_cmenu.renderAsContextMenu();
		var lastColumnRightClicked_CusTree = null;
		function onCusTreeContextButtonClick(itemId){
			tabId=cus_filter.contextID.split('_');
			tabId=tabId[0];
			if (itemId=="open_segment"){
				tabId=cus_filter.contextID;

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
		cus_tree_cmenu.attachEvent("onClick", onCusTreeContextButtonClick);
		var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
				'<item text="Object" id="object" enabled="false"/>'+
				'<item text="<?php echo _l('Properties')?>" id="open_segment"/>'+
			'</menu>';
		cus_tree_cmenu.loadStruct(contextMenuXML);
		cus_filter.enableContextMenu(cus_tree_cmenu);

		cus_filter.attachEvent("onBeforeContextMenu", function(itemId){
			var is_segment = cus_filter.getUserData(itemId,"is_segment");
			if(is_segment==1)
			{
				cus_tree_cmenu.setItemText('object', '<?php echo _l('Segment:')?> '+cus_filter.getItemText(itemId));							
				return true;
			}
			else
				return false;
		});
	<?php } ?>
	
	cus_filter.attachEvent("onCheck",function(idfilter, state){
			groupselection=cus_filter.getAllChecked();
			id_selected_segment = null;
			displayCustomers();
		});
	cus_filter.attachEvent("onClick",function(idfilter){
			var is_segment = cus_filter.getUserData(idfilter,"is_segment");

			if(is_segment!="1")
			{
				state=cus_filter.isItemChecked(idfilter);
				cus_filter.setCheck(idfilter,!state);
				groupselection=cus_filter.getAllChecked();
				cus_filter.clearSelection();
				id_selected_segment = null;
			}
			else
			{
				id_selected_segment = idfilter;
			}
			displayCustomers();
		});

<?php
	}else{
		echo 'cus_filterPanel.collapse();';
	}
?>
	displayFilters();


<?php	//#####################################
			//############ Display
			//#####################################
?>

	function displayFilters(callback)
	{
		cus_filter.deleteChildItems(0);
		cus_filter.loadXML("index.php?ajax=1&act=cus_filter_get&id_lang="+SC_ID_LANG+"&id_shop="+shopselection+"&"+new Date().getTime(),function(){
				if (groupselection!=0)
				{
					cus_filter.openItem(groupselection);
					filters=groupselection.split(',');
					for(var i=0;i<filters.length;i++)
						cus_filter.setCheck(filters[i],true);
				}				
				if (callback!='') eval(callback);
			});
	}
</script>
