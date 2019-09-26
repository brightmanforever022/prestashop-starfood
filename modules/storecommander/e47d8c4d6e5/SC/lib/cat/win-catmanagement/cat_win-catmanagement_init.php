<script type="text/javascript">
dhxlCatManagement=wCatManagement.attachLayout("2U");

var id_shop = 0;
var id_shop_default = '<?php
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
 if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { echo Configuration::get('PS_SHOP_DEFAULT'); } else {echo "0";} ?>';
var in_all_shops = 0;
var id_actual_lang = SC_ID_LANG;
var nb_deleting = 0;

// CATEGORIES TREE GRID
	dhxlCatManagement.cells('a').hideHeader();
	dhxlCatManagement.cells('a').setWidth($(document).width()/2);
	var cat_treegrid_tb = dhxlCatManagement.cells('a').attachToolbar();
	
	cat_treegrid_tb.addButton('cat_treegrid_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
	cat_treegrid_tb.setItemToolTip('cat_treegrid_refresh','<?php echo _l('Refresh',1)?>');
	if (isIPAD)
	{
		cat_treegrid_tb.addButtonTwoState('lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
		cat_treegrid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	<?php if(SCMS) { ?>
	cat_treegrid_tb.addText('cat_treegrid_shop_text', 100, '<?php echo _l('Display:',1)?>');

	has_shop_restrictions = cat_shoptree.getUserData(0, "has_shop_restrictions");
	if(has_shop_restrictions)
	{
		var opts = [<?php
				$shops = Shop::getShops(false);
				$i=0;
				foreach($shops as $shop) { 
					if($i>0)
						echo ",";
					?>
					['shop-<?php echo $shop["id_shop"]; ?>', 'obj', '<?php echo str_replace("'", "\'", $shop['name']);?>', '']
				<?php $i++; } ?>
				];
		cat_treegrid_tb.addButtonSelect("cat_treegrid_shop", 100, '', opts, "","",false,true);
		cat_treegrid_tb.setItemToolTip('cat_treegrid_shop','<?php echo _l('Shop')?>');
		in_all_shops=0;
	}
	else
	{
		var opts = [['shop-all', 'obj', '<?php echo _l('All shops',1)?>', ''],
				['separator1', 'sep', '', '']
			<?php
			$shops = Shop::getShops(false);
			foreach($shops as $shop) { ?>
				,['shop-<?php echo $shop["id_shop"]; ?>', 'obj', '<?php echo str_replace("'", "\'", $shop['name']);?>', '']
			<?php } ?>
			];
		cat_treegrid_tb.addButtonSelect("cat_treegrid_shop", 100, '<?php echo _l('All shops',1)?>', opts, "","",false,true);
		cat_treegrid_tb.setItemToolTip('cat_treegrid_shop','<?php echo _l('Shop')?>');
		cat_treegrid_tb.addButtonTwoState("cat_treegrid_inallshops", 100, '<?php echo _l('Update all shops')?>', "lib/img/checkbox_false.png", "lib/img/checkbox_true.png");
		cat_treegrid_tb.setItemToolTip('cat_treegrid_inallshops','<?php echo _l('If enabled: update all shops when you edit a category')?>');
		cat_treegrid_tb.setItemState("cat_treegrid_inallshops", true);
		in_all_shops=1;
		cat_treegrid_tb.setItemImage("cat_treegrid_inallshops", "lib/img/checkbox_true.png");
		cat_treegrid_tb.disableItem("cat_treegrid_inallshops");
	}
	<?php } ?>
	<?php
		$tmp=array();
		$clang=_l('Language');
		$optlang='';
		$active_langs = _s("CAT_PROD_LANGUAGE_ALL");		
		foreach($languages AS $lang){
			if($active_langs || (!$active_langs && $lang['active']))
			{
				if ($lang['id_lang']==$sc_agent->id_lang)
				{
					$clang=$lang['iso_code'];
					$optlang='cat_treegrid_lang_'.$lang['iso_code'];
				}
				$tmp[]="['cat_treegrid_lang_".$lang['iso_code']."', 'obj', '".$lang['name']."', '']";
			}
		}
		if (count($tmp) > 1)
		{
			echo 'var opts = ['.join(',',$tmp).'];';
	?>
		cat_treegrid_tb.addButtonSelect('cat_treegrid_lang',100,'<?php echo strtoupper($clang); ?>',opts,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);
		cat_treegrid_tb.setItemToolTip('cat_treegrid_lang','<?php echo _l('Select catalog language')?>');
		cat_treegrid_tb.setListOptionSelected('cat_treegrid_lang', '<?php echo $optlang ?>');
	<?php
		}
	?>
	cat_treegrid_tb.addSeparator("sep001",100);
	cat_treegrid_tb.addButton("cat_treegrid_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_selectall','<?php echo _l('Select all',1)?>');
	cat_treegrid_tb.addButton("cat_treegrid_seeinsc", 100, "", "lib/img/zoom.png", "lib/img/zoom.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_seeinsc','<?php echo _l('Open this category in SC',1)?>');
	<?php if(_r("ACT_CAT_ADD_CATEGORY")) { ?>
	cat_treegrid_tb.addButton("cat_treegrid_add", 100, "", "lib/img/add.png", "lib/img/add.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_add','<?php echo _l('Create new category',1)?>');
	<?php /*cat_treegrid_tb.addButton("cat_treegrid_add_ps", 100, "", "lib/img/add_ps.png", "lib/img/add_ps.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_add_ps','<?php echo _l('Create new category with the PrestaShop form',1)?>');*/ ?>
	<?php } ?>
	cat_treegrid_tb.addButton("cat_treegrid_in_bin", 100, "", "lib/img/bin_closed.png", "lib/img/bin_closed.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_in_bin','<?php echo _l('Put in the bin/trash',1)?>');
	<?php if(_r("ACT_CAT_EMPTY_RECYCLE_BIN")) { ?>
	cat_treegrid_tb.addButton("cat_treegrid_bin", 100, "", "lib/img/folder_delete.png", "lib/img/folder_delete.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_bin','<?php echo _l('Empty bin',1)?>');
	<?php } ?>
	<?php if(_r("MEN_CAT_CHECK_FIX_CATEGORIES") || (SCMS && _r("MEN_CAT_SYNCHRO_CATS_POSITIONS"))) { ?>
	cat_treegrid_tb.addSeparator("sep002",100);
	<?php }
	if(_r("MEN_CAT_CHECK_FIX_CATEGORIES")) { ?>
	cat_treegrid_tb.addButton("cat_treegrid_rebuildleveldepth", 100, "", "lib/img/cog_go.png", "lib/img/cog_go.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_rebuildleveldepth','<?php echo _l('Check and fix categories',1)?>');
	<?php }
	if(SCMS && _r("MEN_CAT_SYNCHRO_CATS_POSITIONS")) { ?>
	cat_treegrid_tb.addButton("cat_treegrid_synchro_cats_positions", 100, "", "lib/img/folder_synchro.png", "lib/img/folder_synchro.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_synchro_cats_positions','<?php echo _l('Synchronize the categories positions on multiple shops',1)?>');
	<?php } ?>
	cat_treegrid_tb.addSeparator("sep003",100);
	cat_treegrid_tb.addButton("cat_treegrid_active_products", 100, "", "lib/img/lightbulb.png", "lib/img/lightbulb.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_active_products','<?php echo _l('Activate all products for the selected categories',1)?>');
	cat_treegrid_tb.addButton("cat_treegrid_deactive_products", 100, "", "lib/img/lightbulb_off.png", "lib/img/lightbulb_off.png");
	cat_treegrid_tb.setItemToolTip('cat_treegrid_deactive_products','<?php echo _l('Deactivate all products for the selected categories',1)?>');

	<?php if(SCMS) { ?>
	if(has_shop_restrictions)
	{
		id_shop = shopselection;
		cat_treegrid_tb.setListOptionSelected('cat_treegrid_shop','shop-'+shopselection);
		cat_treegrid_tb.setItemText('cat_treegrid_shop',cat_treegrid_tb.getListOptionText('cat_treegrid_shop', 'shop-'+shopselection));
	}
	<?php } ?>
	cat_treegrid_tb.attachEvent("onClick", function (id){
		var shop = id.split("-");

		<?php if(SCMS) { ?>
		if(shop[0]!=undefined && shop[0]=="shop")
		{
			if(shop[1]!=undefined && shop[1]=="all")
			{
				cat_treegrid_tb.setItemText('cat_treegrid_shop','<?php echo _l('All shops',1)?>');
				id_shop = 0;
				cat_treegrid_tb.setItemState("cat_treegrid_inallshops", true);
				in_all_shops=1;
				cat_treegrid_tb.setItemImage("cat_treegrid_inallshops", "lib/img/checkbox_true.png");
				cat_treegrid_tb.disableItem("cat_treegrid_inallshops");
			}
			<?php foreach($shops as $shop) { ?>
			if(shop[1]!=undefined && shop[1]=="<?php echo $shop["id_shop"]; ?>")
			{
				cat_treegrid_tb.setItemText('cat_treegrid_shop','<?php echo str_replace("'", "\'", $shop['name']);?>');
				id_shop = "<?php echo $shop["id_shop"]; ?>";
				if(!has_shop_restrictions)
					cat_treegrid_tb.enableItem("cat_treegrid_inallshops");
			}
			<?php } ?>
			cat_treegrid_tb.setListOptionSelected('cat_treegrid_shop',id);
			displayTreegridCategories();
		}
		<?php } ?>

		flagLang=false; // changelang ; lang modified?
		<?php
		$tmp=array();
		$clang=_l('Language');
		foreach($languages AS $lang){ ?>
				if (id=='cat_treegrid_lang_<?php echo $lang['iso_code']; ?>')
				{
					id_actual_lang='<?php echo $lang['id_lang']; ?>';
					cat_treegrid_tb.setListOptionSelected('cat_treegrid_lang',id);
					cat_treegrid_tb.setItemText('cat_treegrid_lang','<?php echo strtoupper($lang['iso_code']); ?>');
					flagLang=true;
				}
		<?php } ?>
		if (flagLang){
			displayTreegridCategories();
		}
		
		if (id=='cat_treegrid_refresh')
		{
			displayTreegridCategories();
		}
		if (id=='cat_treegrid_selectall')
		{
			cat_treegrid_grid.selectAll();
		}
		if (id=='cat_treegrid_seeinsc')
		{
			selection=cat_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				selArray=selection.split(',');
				
				var shop_id = id_shop;
				var category_id = selArray[0];

				if(category_id!=0)
				{
					<?php if(SCMS) { ?>
						var action_after = "cat_tree.openItem("+category_id+");cat_tree.selectItem("+category_id+",false);catselection="+category_id+";displayProducts();";
	
						cat_shoptree.selectItem("all",false);
						if(shop_id!=0)
						{
							cat_shoptree.openItem(shop_id);
							cat_shoptree.selectItem(shop_id,false);
						}
						else
							shop_id = "all";
						onClickShopTree(shop_id, null,action_after);
					<?php } else { ?>
						cat_tree.openItem(category_id);
						cat_tree.selectItem(category_id,false);
						catselection=category_id;
						displayProducts();
					<?php } ?>
					wCatManagement.close();
				}
			}
		}
		if (id=='cat_treegrid_add'){
			selection=cat_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				selArray=selection.split(',');
				var parent_id = selArray[0];
				var cname=prompt('<?php echo _l('Create a category:',1)?>');
				if (cname!=null)
				{
					$.post("index.php?ajax=1&act=cat_win-catmanagement_update&action=insert&id_parent="+parent_id+'&id_lang='+id_actual_lang+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops,{name: (cname)},function(id){
						row_adding = id;
						row_adding_name = cname;
						cat_treegrid_grid.addRow(id, cname, null, parent_id,'../../catalog_edit.png');
						cat_treegrid_grid.openItem(parent_id);
						cat_treegrid_grid.selectRowById(id);
					});
				}
			}else{
				alert('<?php echo _l('You need to select a parent category before creating a category',1)?>');
			}
		}
		if (id=='cat_treegrid_add_ps')
		{
			if (!dhxWins.isWindow("wNewCategory"))
			{
				wNewCategory = dhxWins.createWindow("wNewCategory", 50, 50, 1000, $(window).height()-75);
				wNewCategory.button('park').hide();
				wNewCategory.button('minmax').hide();
				wNewCategory.setText('<?php echo _l('Create the new category and close this window to refresh the tree',1)?>');
				<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')){ ?>
					wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=admincategories&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCategories');?>");
				<?php }else{ ?>
					wNewCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCatalog&addcategory&id_parent="+catselection+"&token=<?php echo $sc_agent->getPSToken('AdminCatalog');?>");
				<?php } ?>
				wNewCategory.attachEvent("onClose", function(win){
					displayTreegridCategories();
					return true;
				});
			}
		}
		if (id=='cat_treegrid_bin'){
			if (confirm('<?php echo _l('Are you sure to delete all categories and products placed in the recycled bin?',1)?>'))
			{
				var id_bin=null;

				 cat_treegrid_grid.forEachRow(function(rId){
					 var is_recycle_bin = cat_treegrid_grid.getUserData(rId,"is_recycle_bin");
					 if(is_recycle_bin=="1")
						 id_bin = rId;
				 });
				
				if (id_bin!=null)
					$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=emptybin&gr_id="+id_bin+'&id_lang='+SC_ID_LANG,function(id){
						displayTreegridCategories();
					});
			}
		}
		if (id=='cat_treegrid_in_bin'){
			selection=cat_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				var id_bin=null;

				 cat_treegrid_grid.forEachRow(function(rId){
					 var is_recycle_bin = cat_treegrid_grid.getUserData(rId,"is_recycle_bin");
					 if(is_recycle_bin=="1")
						 id_bin = rId;
				 });

				if (id_bin!=null)
				{
					selArray=selection.split(',');
					var nb_ids = selArray.length*1 - 1;
					$.each(selArray, function(num,rId){
						nb_deleting = nb_deleting*1 + 1;
						$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=move&idCateg="+rId+"&idNewParent="+id_bin+"&idNextBrother="+id_bin+'&id_shop=0&in_all_shops=1&id_lang='+id_actual_lang, function(data){	
						}).always(function() {
							nb_deleting = nb_deleting*1 - 1;
							if(nb_deleting<=0)
								displayTreegridCategories();
						});
					});
				}
			}
		}
		if (id=='cat_treegrid_rebuildleveldepth'){
			$.get("index.php?ajax=1&act=cat_rebuildleveldepth",function(data){
					dhtmlx.message({text:data,type:'info',expire:5000});
				});
		}
		if(id=='cat_treegrid_synchro_cats_positions'){
			if (!dhxWins.isWindow("wSynchroCatsPos"))
			{
				wSynchroCatsPos = dhxWins.createWindow("wSynchroCatsPos", 50, 50, 800, 500);
//				wSynchroCatsPos.setIcon('lib/img/cog_go.png','../../../lib/img/cog_go.png');
wSynchroCatsPos.setIconCss('AAA');
				wSynchroCatsPos.setText('<?php echo _l('Synchronize the categories positions on multiple shops',1)?>');
				$.get("index.php?ajax=1&act=cat_win-categorysynch_init",function(data){
						$('#jsExecute').html(data);
					});
				wSynchroCatsPos.setModal(true);
			}else{
				$.get("index.php?ajax=1&act=cat_win-categorysynch_init",function(data){
						$('#jsExecute').html(data);
					});
				wSynchroCatsPos.show();
				wSynchroCatsPos.setModal(true);
			}
		}
		if(id=="cat_treegrid_active_products")
		{
			selection=cat_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=active_products&id_categories="+selection+"&value=1&id_shop="+id_shop+"&in_all_shops="+in_all_shops+"&id_lang="+id_actual_lang, function(data){	
					dhtmlx.message({text:'<?php echo _l('The products has been updated correctly.',1)?>',type:'info',expire:5000});
				});
			}
		}
		if(id=="cat_treegrid_deactive_products")
		{
			selection=cat_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=active_products&id_categories="+selection+"&value=0&id_shop="+id_shop+"&in_all_shops="+in_all_shops+"&id_lang="+id_actual_lang, function(data){	
					dhtmlx.message({text:'<?php echo _l('The products has been updated correctly.',1)?>',type:'info',expire:5000});
				});
			}
		}
	});
	cat_treegrid_tb.attachEvent("onStateChange", function(id,state){
		if (id=='cat_treegrid_inallshops'){
			if (state) {
				in_all_shops=1;
				cat_treegrid_tb.setItemImage(id, "lib/img/checkbox_true.png");
			}else{
				in_all_shops=0;
				cat_treegrid_tb.setItemImage(id, "lib/img/checkbox_false.png");
			}
		}
		if (id=='lightNavigation')
		{
			if (state)
			{
				cat_treegrid_grid.enableLightMouseNavigation(true);
			}else{
				cat_treegrid_grid.enableLightMouseNavigation(false);
			}
		}
	});
	
	cat_treegrid_grid = dhxlCatManagement.cells('a').attachGrid();
	cat_treegrid_grid._name='cat_treegrid';
	cat_treegrid_grid.setImagePath("lib/js/imgs/");
	cat_treegrid_grid.setFiltrationLevel(-2);
	cat_treegrid_grid.enableTreeCellEdit(0);
<?php
	$sql = "SELECT COUNT(*) AS total FROM "._DB_PREFIX_."category";
	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	if ($res['total'] > 10)
	{
?>
	cat_treegrid_grid.enableSmartRendering(true);
<?php
	}
?>
	cat_treegrid_grid.enableDragAndDrop(true);
	cat_treegrid_grid.setDragBehavior("complex");
	cat_treegrid_grid.setAwaitedRowHeight(<?php echo getGridImageHeight(); ?>);
	cat_treegrid_grid._dragBehavior="complex";
	cat_treegrid_grid.enableMultiselect(true);

	var row_adding = 0;
	var row_adding_name = "";
	cat_treegrid_grid.attachEvent("onRowAdded", function(rId){
		if(rId==row_adding)
		{
			idxId=cat_treegrid_grid.getColIndexById('id_category');
			idxImage=cat_treegrid_grid.getColIndexById('image');
			idxName=cat_treegrid_grid.getColIndexById('name');
			idxNbProducts=cat_treegrid_grid.getColIndexById('nb_products');
			idxNbProductsSeo=cat_treegrid_grid.getColIndexById('nb_products_seo');
			idxActive=cat_treegrid_grid.getColIndexById('active');

			cat_treegrid_grid.cells(rId,idxId).setValue(rId);
			<?php
            if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
                $image = '<img src="'._PS_IMG_.'404.gif" height="60px" alt="" />';
            elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $image = '<img src="'._PS_IMG_.'c/fr.jpg" height="60px" alt="" />';
			else
				$image = '<img src="'._PS_IMG_.'c/en.jpg" height="60px" alt="" />';
			?>
			cat_treegrid_grid.cells(rId,idxImage).setValue('<?php echo $image; ?>');
			cat_treegrid_grid.cells(rId,idxName).setValue(row_adding_name);
			cat_treegrid_grid.cells(rId,idxNbProducts).setValue("0");
			cat_treegrid_grid.cells(rId,idxNbProductsSeo).setValue("0");
			cat_treegrid_grid.cells(rId,idxActive).setValue("0");

			row_adding = 0;
			row_adding_name = "";
			
			colorActive();
		}
	});  

	cat_treegrid_grid.attachEvent("onColumnHidden",function(indx,state){
		idxImg=cat_treegrid_grid.getColIndexById('image');
		if (idxImg && !state){
			cat_treegrid_grid.setAwaitedRowHeight(<?php echo getGridImageHeight(); ?>);
		}else{
			cat_treegrid_grid.setAwaitedRowHeight(30);
		}
	});
	
	// UISettings
	cat_treegrid_grid._uisettings_prefix='cat_catmanagement_treegrid';
	cat_treegrid_grid._uisettings_name=cat_treegrid_grid._uisettings_prefix;
	cat_treegrid_grid._first_loading=1;
		
	// UISettings
	initGridUISettings(cat_treegrid_grid);

	displayTreegridCategories();

	// Data update
	 cat_treegrid_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
		idxActive=cat_treegrid_grid.getColIndexById('active');
		idxName=cat_treegrid_grid.getColIndexById('name');

		if(stage==0 || stage==1)
		{
			 var is_recycle_bin = cat_treegrid_grid.getUserData(rId,"is_recycle_bin");
			 if(is_recycle_bin=="1")
				 return false;
		}

		var field = 'active';
		if(idxName==cInd)
			field = 'name';	
		var enableOnCols=new Array(
				idxActive,
				idxName
				);
		<?php SC_Ext::readCustomCategoriesGridConfigXML("onEditCell"); ?>
		if (!in_array(cInd,enableOnCols))
			return false;

		if(stage==2)
		{
			$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=update&gr_id="+rId+"&field="+field+"&value="+nValue+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});
		}
		return true;
	});
	/*cat_treegridDataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_update&id_lang="+id_actual_lang;
	cat_treegridDataProcessor = new dataProcessor(cat_treegridDataProcessorURLBase);
	cat_treegridDataProcessor.enableDataNames(true);
	cat_treegridDataProcessor.setTransactionMode("GET");
	cat_treegridDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
		if (action=='insert')
			cat_treegrid_grid.cells(tid,0).setValue(tid);
		colorActive();
	});
	cat_treegridDataProcessorURLBase="index.php?ajax=1&act=cat_win-catmanagement_update&id_lang="+id_actual_lang;
	cat_treegridDataProcessor.serverProcessor=cat_treegridDataProcessorURLBase;
	cat_treegridDataProcessor.init(cat_treegrid_grid);*/

	// Event drag & drop
	cat_treegrid_grid.attachEvent("onDrop",function (idSourceIds,idTarget,idBefore,sourceobject,targetTree){
		var idSources = new Array();
		if(idSourceIds.search(",")>0)
		{
			idSources = idSourceIds.split(",");
		}
		else
		{
			idSources[0] = idSourceIds;
		}
		$.each(idSources, function(num, idSource){
			var parent_id = cat_treegrid_grid.getParentId(idSource);
			var real_parent_id = parent_id;
			if(real_parent_id==0)
			{
				real_parent_id = cat_treegrid_grid.getUserData("","parent_root");
			}
	
			var next_brother_id = parent_id;
			var subItems = cat_treegrid_grid.getAllSubItems(parent_id);
			var actual_level = cat_treegrid_grid.getLevel(idSource);
			if(subItems!=undefined && subItems!=null && subItems!="")
			{
				subItems = subItems.split(",");
				var is_id = false;
				$.each(subItems, function(num, id){
					if(cat_treegrid_grid.getLevel(id)==actual_level)
					{
						if(is_id==true)
						{
							next_brother_id = id;
							is_id = false;
						}
						if(id==idSource)
							is_id = true;
					}
				});
			}
			var id_shop_tmp = id_shop;
			<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
			if(id_shop_tmp==0)
				id_shop_tmp = id_shop_default;
			<?php } ?>
			$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=move&idCateg="+idSource+"&idNewParent="+real_parent_id+"&idNextBrother="+next_brother_id+'&id_shop='+id_shop_tmp+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});
		});
		return false;
	});
	cat_treegrid_grid.attachEvent("onDragIn",function(idSource,idTarget,sourceobject,targetobject){
		var is_FF = cat_treegrid_grid.getUserData(idSource,"is_FF");
		var in_FF = cat_treegrid_grid.getUserData(idTarget,"is_FF");
		if(sourceobject._name=='tree' && is_FF==1)
			return false;
		if(sourceobject._name=='tree' && in_FF==1)
			return false;
		return true;
	});
	cat_treegrid_grid.attachEvent("onDrag",function(sourceid,targetid,sObj,tObj,sInd,tInd){
		if(sourceid!=undefined && sourceid!=0 && targetid!=undefined && targetid!=0)
		{
			var is_FF = cat_treegrid_grid.getUserData(sourceid,"is_FF");
			var in_FF = cat_treegrid_grid.getUserData(targetid,"is_FF");
			if(is_FF==1 || in_FF==1)
				return false;

			var is_recycle_bin = cat_treegrid_grid.getUserData(targetid,"is_recycle_bin");
			if(is_recycle_bin==1)
			{
				var not_deletable = cat_treegrid_grid.getUserData(sourceid,"not_deletable");
				if(not_deletable==1)
					return false;
			}
		}
		return true;
	});
	
	// Context menu for grid
	cat_treegrid_cmenu=new dhtmlXMenuObject();
	cat_treegrid_cmenu.renderAsContextMenu();
	clipboardType_CatTreegrid = null;
	var copy_multiple_id = null;
	function onGridCatTreegridContextButtonClick(itemId){
		tabId=cat_treegrid_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="copy"){
			if (lastColumnRightClicked_CatTreegrid!=0)
			{
				clipboardValue_CatTreegrid=cat_treegrid_grid.cells(tabId,lastColumnRightClicked_CatTreegrid).getValue();
				cat_treegrid_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cat_treegrid_grid.cells(tabId,lastColumnRightClicked_CatTreegrid).getTitle());
				clipboardType_CatTreegrid=lastColumnRightClicked_CatTreegrid;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked_CatTreegrid!=0 && clipboardValue_CatTreegrid!=null && clipboardType_CatTreegrid==lastColumnRightClicked_CatTreegrid)
			{
				selection=cat_treegrid_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					idxActive=cat_treegrid_grid.getColIndexById('active');
					idxName=cat_treegrid_grid.getColIndexById('name');
					
					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						if (cat_treegrid_grid.getColumnId(lastColumnRightClicked_CatTreegrid).substr(0,5)!='attr_')
						{
							cat_treegrid_grid.cells(selArray[i],lastColumnRightClicked_CatTreegrid).setValue(clipboardValue_CatTreegrid);
							/*cat_treegrid_grid.cells(selArray[i],lastColumnRightClicked_CatTreegrid).cell.wasChanged=true;
							cat_treegridDataProcessor.setUpdated(selArray[i],true,"updated");*/
							var field = 'active';
							if(idxName==lastColumnRightClicked_CatTreegrid)
								field = 'name';	
							$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=update&gr_id="+selArray[i]+"&field="+field+"&value="+clipboardValue_CatTreegrid+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});
							colorActive();
						}
					}
				}
			}
		}
		if (itemId=="expand"){
			cat_treegrid_grid.openItem(tabId);
			expandChildren(tabId);
		}
		if (itemId=="collapse"){
			cat_treegrid_grid.closeItem(tabId);
			collapseChildren(tabId);
		}
		if (itemId=="goshop"){
			<?php
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					if(SCMS) {
			?>
						var shop = id_shop;
						if(shop==0 || shop==null || shop=="")
							shop = id_shop_default;
						if(shopUrls[shop] != undefined && shopUrls[shop] != "" && shopUrls[shop] != null)
							window.open(shopUrls[shop]+'index.php?id_category='+tabId+'&controller=category&id_lang='+SC_ID_LANG);
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
		if (itemId=="gopsbo"){
			wModifyCategory = dhxWins.createWindow("wModifyCategory", 50, 50, 1000, $(window).height()-75);
			wModifyCategory.setText('<?php echo _l('Modify the category and close this window to refresh the tree',1)?>');
			wModifyCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'admincategories':'AdminCatalog');?>&updatecategory&id_category="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken((version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'AdminCategories':'AdminCatalog'));?>");
			wModifyCategory.attachEvent("onClose", function(win){
				displayTreegridCategories();
				return true;
			});
		}
		if (itemId=="enable"){
			var idxActive = cat_treegrid_grid.getColIndexById('active');
			var actual_active = cat_treegrid_grid.cells(tabId,idxActive).getValue();

			if(actual_active=="1" || actual_active==1)
				actual_active = "0";
			else
				actual_active = "1";
			
			cat_treegrid_grid.cells(tabId,idxActive).setValue(actual_active);
			/*cat_treegrid_grid.cells(tabId,idxActive).cell.wasChanged=true;
			cat_treegridDataProcessor.setUpdated(tabId,true,"updated");*/
			$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=update&gr_id="+tabId+"&field=active&value="+actual_active+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});

			var icon_active = 'lib/js/imgs/catalog.png';
			var icon_deactive = 'lib/js/imgs/catalog_edit.png';

			var actual_image = cat_treegrid_grid.getItemImage(tabId);
			if(actual_active=="1" && icon_deactive)
				cat_treegrid_grid.setItemImage(tabId,icon_active);
			else if(actual_active=="0" && icon_active)
				cat_treegrid_grid.setItemImage(tabId,icon_deactive);
			
			colorActive();
		}
		if (itemId=="copy_multiple"){
			copy_multiple_id = tabId;
		}
		if (itemId=="paste_multiple"){
			if(copy_multiple_id!=undefined && copy_multiple_id!=null && copy_multiple_id!="" && copy_multiple_id>0)
				if(tabId!=undefined && tabId!=null && tabId!="" && tabId>0)
			$.get("index.php?ajax=1&act=cat_win-catmanagement_update&action=paste_multiple&gr_id="+copy_multiple_id+"&id_parent="+tabId+"&id_lang="+id_actual_lang, function(data){
				displayTreegridCategories();
			});
		}
	}
	cat_treegrid_cmenu.attachEvent("onClick", onGridCatTreegridContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
			'<item text="Object" id="object" enabled="false"/>'+
			'<item id="sep_00" type="separator"/>'+
			'<item text="<?php echo _l('Expand')?>" id="expand"/>'+
			'<item text="<?php echo _l('Collapse')?>" id="collapse"/>'+
			'<item id="sep_01" type="separator"/>'+
			'<item text="<?php echo _l('Copy structure')?>" id="copy_multiple"/>'+
			'<item text="<?php echo _l('Paste categories')?>" id="paste_multiple"/>'+
			'<item id="sep_02" type="separator"/>'+
			'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
			'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
			'<item id="sep_03" type="separator"/>'+
			'<item text="<?php echo _l('See on shop')?>" id="goshop"/>'+
			'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
			<?php if(_r("ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY")) { ?>
			'<item id="sep_04" type="separator"/>'+
			'<item text="<?php echo _l('Enable / Disable')?>" id="enable"/>'+
			<?php } ?>
		'</menu>';
	cat_treegrid_cmenu.loadStruct(contextMenuXML);
	cat_treegrid_grid.enableContextMenu(cat_treegrid_cmenu);

	cat_treegrid_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		var copiable = false;
		var enableOnCols=new Array(
				cat_treegrid_grid.getColIndexById('name'),
				cat_treegrid_grid.getColIndexById('active')
				);
		if (in_array(colidx,enableOnCols))
			copiable = true;
		lastColumnRightClicked_CatTreegrid=colidx;
		cat_treegrid_cmenu.setItemText('object', '<?php echo _l('Category:')?> '+cat_treegrid_grid.cells(rowid,cat_treegrid_grid.getColIndexById('name')).getTitle());
		if(copiable)
		{
			cat_treegrid_cmenu.showItem('copy');
			cat_treegrid_cmenu.showItem('paste');
			cat_treegrid_cmenu.showItem('sep_02');
			if (lastColumnRightClicked_CatTreegrid==clipboardType_CatTreegrid)
			{
				cat_treegrid_cmenu.setItemEnabled('paste');
			}else{
				cat_treegrid_cmenu.setItemDisabled('paste');
			}
		}
		else
		{
			cat_treegrid_cmenu.hideItem('copy');
			cat_treegrid_cmenu.hideItem('paste');
			cat_treegrid_cmenu.hideItem('sep_02');
		}

		if(copy_multiple_id!=undefined && copy_multiple_id!=null && copy_multiple_id!="" && copy_multiple_id>0)
			cat_treegrid_cmenu.setItemEnabled('paste_multiple');
		else
			cat_treegrid_cmenu.setItemDisabled('paste_multiple');

		var numberSelectedRows = parseInt(grid.selectedRows.length);
		if (numberSelectedRows > 1) {
			cat_treegrid_cmenu.setItemDisabled('copy');
			cat_treegrid_cmenu.setItemDisabled('paste');
			cat_treegrid_cmenu.setItemDisabled('copy_multiple');
			cat_treegrid_cmenu.setItemDisabled('paste_multiple');
		} else if (numberSelectedRows === 1) {
			cat_treegrid_cmenu.setItemEnabled('copy');
			cat_treegrid_cmenu.setItemEnabled('paste');
			cat_treegrid_cmenu.setItemEnabled('copy_multiple');
			cat_treegrid_cmenu.setItemEnabled('paste_multiple');
		}

		<?php if(SCMS) { ?>
		var shop = id_shop;
		if(shop==0 || shop==null || shop=="")
			shop = id_shop_default;
		if(shopUrls[shop] != undefined && shopUrls[shop] != "" && shopUrls[shop] != null)
		{
			cat_treegrid_cmenu.setItemEnabled('goshop');
		}else{
			cat_treegrid_cmenu.setItemDisabled('goshop');
		}
		<?php } ?>

		return true;
	});

// PROPERTIES
	dhxlCatManagement.cells('b').setText('<?php echo _l('Properties',1)?>');
	var cat_prop_tb = dhxlCatManagement.cells('b').attachToolbar();


	actual_catmanagement_subproperties = "cat_prop_info";
	
	var opts = new Array();
	cat_prop_tb.addButtonSelect("cat_prop_subproperties", 100, "<?php echo _l('Name & description')?>", opts, "lib/img/description.png", "lib/img/application_form_magnify.png",false,true);
	
	<?php 
	@$sub_files = scandir(SC_DIR.'lib/cat/win-catmanagement');
	foreach ($sub_files as $sub_item)
		if ($sub_item != '.' && $sub_item != '..')
		if (is_dir(SC_DIR.'lib/cat/win-catmanagement/'.$sub_item) && file_exists(SC_DIR.'lib/cat/win-catmanagement/'.$sub_item.'/cat_win-catmanagement_'.$sub_item.'_init.php'))
		{
			require_once(SC_DIR.'lib/cat/win-catmanagement/'.$sub_item.'/cat_win-catmanagement_'.$sub_item.'_init.php');
		}
	?>
	initCatManagementPropInfo();
/*
 * FUNCTIONS
 */

 function displayTreegridCategories()
 {
	 cat_treegrid_grid.clearAll(true);
	cat_treegrid_grid.loadXML("index.php?ajax=1&act=cat_win-catmanagement_get&id_shop="+id_shop+"&id_lang="+id_actual_lang,function()
	{
		// UISettings
		loadGridUISettings(cat_treegrid_grid);
			
		// UISettings
		cat_treegrid_grid._first_loading=0;
		cat_treegrid_grid.expandAll();
		colorActive();
	});
 }

 function colorActive()
 {
	idxActive=cat_treegrid_grid.getColIndexById('active');
	idxName=cat_treegrid_grid.getColIndexById('name');
	
	 cat_treegrid_grid.forEachRow(function(rId){
		 var value = cat_treegrid_grid.cells(rId,idxActive).getValue();
		 if(value==null || value=="" || value==0 || value=="0")
			 cat_treegrid_grid.cells(rId,idxName).setBgColor('#cccccc');  
		 else
			 cat_treegrid_grid.cells(rId,idxName).setBgColor('');  
	 });
 }
	
function hideCatManagementSubpropertiesItems()
{
	cat_prop_tb.forEachItem(function(itemId){
        if(itemId!="cat_prop_subproperties")
        	cat_prop_tb.hideItem(itemId);
    });
}



function expandChildren(parent_Id)
{
	var children = cat_treegrid_grid.getAllSubItems(parent_Id);

	if(children!=undefined && children!=null && children!=0 && children!="")
	{
		var ids = children.split(',');
		$.each(ids, function(num, id){
			cat_treegrid_grid.openItem(id);
			expandChildren(id);
		});
	}
}
function collapseChildren(parent_Id)
{
	var children = cat_treegrid_grid.getAllSubItems(parent_Id);

	if(children!=undefined && children!=null && children!=0 && children!="")
	{
		var ids = children.split(',');
		$.each(ids, function(num, id){
			cat_treegrid_grid.closeItem(id);
			collapseChildren(id);
		});
	}
}
</script>
