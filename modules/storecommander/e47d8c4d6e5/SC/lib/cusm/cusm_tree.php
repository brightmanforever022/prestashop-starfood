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

<?php if(version_compare(_PS_VERSION_, '1.4.0.0', '<') || !_r("GRI_CUSM_VIEW_CUSM")) { ?>
document.location.href="index.php";
<?php } ?>

	// Create interface
	var dhxLayout = new dhtmlXLayoutObject(document.body, "2U");
	dhxLayout.cells('a').setText('<?php echo _l('Discussions service',1);?>');
	dhxLayout.cells('b').setText('<?php echo _l('Properties',1)?>');
	dhxLayout.cells('b').setWidth(getParamUISettings('start_cusm_size_prop'));
	dhxLayout.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cusm_size_prop', dhxLayout.cells('b').getWidth())
	});
	var dhxLayoutStatus = dhxLayout.attachStatusBar();
	dhxLayoutStatus.setText("<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' ').' - Version '.SC_VERSION.(SC_BETA?' BETA':'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>");
	layoutStatusText = "<?php echo SC_COPYRIGHT.' '.(SC_DEMO?'- Demonstration':'- '._l('License').' '.SCLIMREF.' - '.$SC_SHOP_PRODUCTSCOUNT.' ').' - Version '.SC_VERSION.(SC_BETA?' BETA':'').' (PS '._PS_VERSION_.(defined('_PS_CACHE_ENABLED_') && _PS_CACHE_ENABLED_ ? ' (cache)':'').')';?>";
<?php
	createMenu();
?>
	cookie_selection = $.cookie('sc_cusm_filters_selected');
	if(cookie_selection!=null && cookie_selection!="" && cookie_selection!=0)
		filterselection=$.cookie('sc_cusm_filters_selected');
	else
	{
		filterselection = "st_open";
		$.cookie('sc_cusm_filters_selected',filterselection);
	}
	shopselection=$.cookie('sc_shop_selected')*1;
	shop_list=$.cookie('sc_shop_list');
	lastDiscussionSelID=0;
	propertiesPanel='message';
	lastColumnRightClicked_Combi=0;
	clipboardValue=null;
	clipboardType=null;

<?php	//#####################################
			//############ Categories toolbar
			//#####################################

	if (SCMS)
	{
?>
	// Url array
		var shopUrls = new Array();
		<?php
		$protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
		$sql_shop ="SELECT id_shop
					FROM "._DB_PREFIX_."shop
					WHERE deleted != 1";
		$shops = Db::getInstance()->ExecuteS($sql_shop);
		foreach($shops as $shop)
		{
			$url = Db::getInstance()->ExecuteS('SELECT *, CONCAT(domain, physical_uri, virtual_uri) AS url
				FROM '._DB_PREFIX_.'shop_url
				WHERE id_shop = "'.(int)$shop["id_shop"].'"
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
	
	cusm = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	
	cusm_firstcolcontent = cusm.cells("a").attachLayout("3E");
	cusm_discussionPanel = cusm.cells('b');
	
	cusm_storePanel = cusm_firstcolcontent.cells('a');
	cusm_filterPanel = cusm_firstcolcontent.cells('b');
	cusm_statPanel = cusm_firstcolcontent.cells('c');
	
	cusm_storePanel.setText('<?php echo _l('Stores',1)?>');
	cusm_storePanel.setHeight(getParamUISettings('start_cusm_size_store'));
	cusm_firstcolcontent.attachEvent("onPanelResizeFinish", function(){
		saveParamUISettings('start_cusm_size_store', cusm_storePanel.getHeight())
	});
	cusm_shoptree=cusm_storePanel.attachTree();
	cusm_shoptree._name='shoptree';
	cusm_shoptree.autoScroll=false;
	cusm_shoptree.setImagePath('lib/js/imgs/');
	cusm_shoptree.enableSmartXMLParsing(true);
//	cusm_shoptree.enableCheckBoxes(true, false);

	var cusmShoptreeTB = cusm_storePanel.attachToolbar();
	cusmShoptreeTB.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cusmShoptreeTB.setItemToolTip('help','<?php echo _l('Help')?>');
	cusmShoptreeTB.attachEvent("onClick", function(id) {
		if (id=='help')
		{
			var display = "";
			var update = "";
			if(shopselection>0)
			{
				display = cusm_shoptree.getItemText(shopselection);
			}
			else if(shopselection==0)
			{
				display = cusm_shoptree.getItemText("all");
			}

/*			var all_checked = $.cookie('sc_shop_list').split(",");
			$.each(all_checked, function(index, id) {
				if(id!="all" && id.search("G")<0)
				{
					if(update!="")
						update += ", ";
					update += cusm_shoptree.getItemText(id);
				}
			});*/
			
			var msg = '<strong><?php echo addslashes(_l('Display:'));?></strong> '+display+'<br/><br/><strong><?php echo addslashes(_l('Update:'));?></strong> '+update;
			dhtmlx.message({text:msg,type:'info',expire:10000});
		}
	});
	
	
	displayShopTree();
/*
	function checkWhenSelection(idshop)
	{
		if (idshop == 'all' || idshop==0)
		{
			var children = cusm_shoptree.getAllSubItems("all").split(",");
			cusm_shoptree.setCheck("all",1);
			$.each(children, function(index, id) {
				cusm_shoptree.setCheck(id,1);
				cusm_shoptree.disableCheckbox(id,1);
			});
		}
		else
		{
			var children = cusm_shoptree.getAllSubItems("all").split(",");
			$.each(children, function(index, id) {
				cusm_shoptree.disableCheckbox(id,0);
			});
			if(idshop>0)
			{
				cusm_shoptree.setCheck(idshop,1);
				cusm_shoptree.disableCheckbox(idshop,1);
			}
		}
	}
	function deSelectParents(idshop)
	{
		if(cusm_shoptree.getParentId(idshop)!="")
		{
			var parent_id = cusm_shoptree.getParentId(idshop);
			cusm_shoptree.setCheck(parent_id,0);
			
			deSelectParents(parent_id);
		}
	}
	function saveCheckSelection()
	{
		var checked = cusm_shoptree.getAllChecked();
		if(shopselection=="all" || shopselection=="0")
		{
			checked = cusm_shoptree.getAllSubItems("all");
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
*/
	function displayShopTree(callback) {
		cusm_shoptree.deleteChildItems(0);
		cusm_shoptree.loadXML("index.php?ajax=1&act=cusm_shop_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
/*
				if(shopselection!=null && shopselection!=undefined)
					checkWhenSelection(shopselection);
				if(shop_list!=null && shop_list!="")
				{
					var selected = shop_list.split(",");
					$.each(selected, function(index, id) {
						cusm_shoptree.setCheck(id,1);
					});
				}
*/
				if (shopselection!=null && shopselection!=undefined && shopselection!=0)
				{
					cusm_shoptree.openItem(shopselection);
					cusm_shoptree.selectItem(shopselection,true);
				}
				
				if (callback!='') eval(callback);
				cusm_shoptree.openAllItems(0);
			});
	}
	cusm_shoptree.attachEvent("onClick",function(idshop){
		if (idshop[0]=='G'){
			cusm_shoptree.clearSelection();
			cusm_shoptree.selectItem(shopselection,false);
			return false;
		}
		if (idshop == 'all'){
			idshop = 0;
		}
//		checkWhenSelection(idshop);
		if (idshop != shopselection)
		{
/*
			if(shopselection!=0 && idshop!=0 && idshop[0]!='G')
				cusm_shoptree.setCheck(shopselection,0);
			else if(shopselection==0 && idshop!=0 && idshop[0]!='G')
			{
				var children = cusm_shoptree.getAllSubItems("all").split(",");
				cusm_shoptree.setCheck("all",0);
				$.each(children, function(index, id) {
					if(id!=idshop)
						cusm_shoptree.setCheck(id,0);
				});
			}
*/
			shopselection = idshop;
			$.cookie('sc_shop_selected',shopselection, { expires: 60 });
			cusm_statPanel.attachURL("index.php?ajax=1&act=cusm_statsget&id_lang="+SC_ID_LANG+"&"+new Date().getTime());
			displayFilters('displayDiscussions()');
		}
//		saveCheckSelection();
	});
/*
	cusm_shoptree.attachEvent("onCheck",function(idshop, state){
		if(idshop=="all")
		{
			var children = cusm_shoptree.getAllSubItems("all").split(",");
			$.each(children, function(index, id) {
				cusm_shoptree.setCheck(id,state);
			});
		}
		else if(idshop.search("G")>=0)
		{
			var children = cusm_shoptree.getAllSubItems(idshop).split(",");
			$.each(children, function(index, id) {
				cusm_shoptree.setCheck(id,state);
			});
		}
		else
		{
			deSelectParents(idshop);
		}
		saveCheckSelection();
	});
*/
<?php
	}else{
?>
	cusm = new dhtmlXLayoutObject(dhxLayout.cells("a"), "2U");
	
	cusm_firstcolcontent = cusm.cells("a").attachLayout("2E");
	cusm_discussionPanel = cusm.cells('b');
	
	cusm_filterPanel = cusm_firstcolcontent.cells('a');
	cusm_statPanel = cusm_firstcolcontent.cells('b');
	
<?php
	}
?>
	<?php if(SCMS) { ?>
		cusm.cells("a").setWidth(getParamUISettings('start_cusm_size_tree'));
		cusm.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cusm_size_tree', cusm.cells("a").getWidth())
		}); 
	<?php } else { ?>
		cusm_filterPanel.setWidth(getParamUISettings('start_cusm_size_tree'));
		cusm.attachEvent("onPanelResizeFinish", function(){
			saveParamUISettings('start_cusm_size_tree',cusm_filterPanel.getWidth())
		});
	<?php }

	
			//#####################################
			//############ filters tree
			//#####################################
?>

	cusm_filter=cusm_filterPanel.attachTree();
	cusm_filter._name='filter';
	cusm_filterPanel.setText('<?php echo _l('Filters',1)?>');
	cusm_filter.autoScroll=false;
	cusm_filter.setImagePath('lib/js/imgs/');
<?php
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
	{
?>
	cusm_filter.enableCheckBoxes(true);
	cusm_filter.enableThreeStateCheckboxes(true);
	
	
<?php	//#####################################
			//############ Events
			//#####################################
?>

<?php if(SCSG) { ?>
	var id_selected_segment = 0;
	cusm_filter.enableDragAndDrop(true);
	cusm_filter.enableDragAndDropScrolling(true);

	cusm_filter.attachEvent("onDragIn",function doOnDragIn(idSource,idTarget,sourceobject,targetobject){
		var is_segment = cusm_filter.getUserData(idSource,"is_segment");
		var in_segment = cusm_filter.getUserData(idTarget,"is_segment");
		if(sourceobject._name=='filter')
			 return false;

		// Si produit est déplacé dans segment
		// mais celui-ci n'accepte pas l'ajout manuel de produits
		var manuel_add = cusm_filter.getUserData(idTarget,"manuel_add");
		if(sourceobject._name=='grid' && in_segment==1 && manuel_add==1)
			return true;
		return false;
	});
	cusm_filter.attachEvent("onDrop",function doOnDrop(idSource,idTarget,idBefore,sourceobject,targetTree){
		var is_segment = cusm_filter.getUserData(idTarget,"is_segment");
		if(sourceobject._name=='filter' && is_segment==1)
			 return false;
	});
	cusm_filter.attachEvent("onBeforeDrag",function(sourceid){
		 return false;	
	});
	cusm_filter.attachEvent("onDrag",function(sourceid,targetid,sibling,sourceobject,targetobject){
		var is_segment = cusm_filter.getUserData(sourceid,"is_segment");
		var in_segment = cusm_filter.getUserData(targetid,"is_segment");
		
		if (sourceobject._name=='grid')
		{
			var manuel_add = cusm_filter.getUserData(targetid,"manuel_add");

			// Si ce n'est pas un segment et qu'il est déplacé dans un segment (client dans un segment)
			// et accepte l'ajout manuel de produits
			if(is_segment!=1 && in_segment==1 && manuel_add==1)
			{
				$.post("index.php?ajax=1&act=cusm_segment_dropproductonsegment&mode=move&id_lang="+SC_ID_LANG,{'segmentTarget':targetid,'discussions':sourceid},function(){});
			}
			return false;
		}
		else
			 return false;
	});


	// Context menu for grid
	cusm_tree_cmenu=new dhtmlXMenuObject();
	cusm_tree_cmenu.renderAsContextMenu();
	var lastColumnRightClicked_CusTree = null;
	function onCusTreeContextButtonClick(itemId){
		tabId=cusm_filter.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="open_segment"){
			tabId=cusm_filter.contextID;

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
	cusm_tree_cmenu.attachEvent("onClick", onCusTreeContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item text="<?php echo _l('Properties')?>" id="open_segment"/>'+
		'</menu>';
	cusm_tree_cmenu.loadStruct(contextMenuXML);
	cusm_filter.enableContextMenu(cusm_tree_cmenu);

	cusm_filter.attachEvent("onBeforeContextMenu", function(itemId){
		var is_segment = cusm_filter.getUserData(itemId,"is_segment");
		if(is_segment==1)
		{
			cusm_tree_cmenu.setItemText('object', '<?php echo _l('Segment:')?> '+cusm_filter.getItemText(itemId));							
			return true;
		}
		else
			return false;
	});
<?php } ?>
	
	cusm_filter.attachEvent("onCheck",function(idfilter, state){
			if(idfilter == 'from_to')
			{
				periodselection=idfilter;
				$.cookie('sc_cusm_periodselection',periodselection, { expires: 60 });

				if (dhxWins.isWindow("wCusmFilterFromTo"))
					wCusmFilterFromTo.close();

				wCusmFilterFromTo = dhxWins.createWindow("wCusmFilterFromTo", 170, 150, 340, 400);
				wCusmFilterFromTo.denyPark();
				wCusmFilterFromTo.denyResize();
				wCusmFilterFromTo.setIcon('lib/img/calendar.png','../../../lib/img/calendar.png');
				wCusmFilterFromTo.setText('<?php echo _l('Select the date interval to filter',1)?>');
				$.get("index.php?ajax=1&act=cusm_filter_dates",function(data){
					$('#jsExecute').html(data);
				});
			}
			filterselection=cusm_filter.getAllChecked();
			$.cookie('sc_cusm_filters_selected', filterselection);
			id_selected_segment = null;
			displayDiscussions();
		});
	cusm_filter.attachEvent("onClick",function(idfilter){
			var is_segment = cusm_filter.getUserData(idfilter,"is_segment");
	
			if(is_segment!="1")
			{
				state=cusm_filter.isItemChecked(idfilter);
				cusm_filter.setCheck(idfilter,!state);
				filterselection=cusm_filter.getAllChecked();
				$.cookie('sc_cusm_filters_selected', filterselection);
				cusm_filter.clearSelection();
			}
			else
			{
				id_selected_segment = idfilter;
			}
			displayDiscussions();
		});

<?php
	}else{
		echo 'cusm_filterPanel.collapse();';
	}
?>
	displayFilters('displayDiscussions()');


<?php 
	
			//#####################################
			//############ stats
			//#####################################
?>
		cusm_statPanel.setText('<?php echo _l('Global statistics',1)?>');
		cusm_statPanel.attachURL("index.php?ajax=1&act=cusm_statsget&id_lang="+SC_ID_LANG+"&"+new Date().getTime());

<?php	//#####################################
			//############ Display
			//#####################################
?>

	function displayFilters(callback)
	{
		cusm_filter.deleteChildItems(0);
		cusm_filter.loadXML("index.php?ajax=1&act=cusm_filter_get&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				if(filterselection!=undefined && filterselection!=null && filterselection!="" && filterselection!=0)
				{
					var filters = filterselection.split(",");
					$.each(filters, function(index, filter) {
						cusm_filter.setCheck(filter,1);
					});
				}
			
				if (callback!='') eval(callback);
			});
	}
</script>
