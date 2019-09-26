<?php/**
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
dhxlCmsCatManagement=wCmsCatManagement.attachLayout("2U");

var id_shop = 0;
var id_shop_default = '<?php
 if(version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { echo Configuration::get('PS_SHOP_DEFAULT'); } else {echo "0";} ?>';
var in_all_shops = 0;
var id_actual_lang = SC_ID_LANG;
var nb_deleting = 0;

// CATEGORIES TREE GRID
	dhxlCmsCatManagement.cells('a').hideHeader();
	dhxlCmsCatManagement.cells('a').setWidth($(document).width()/2);
	var cms_treegrid_tb = dhxlCmsCatManagement.cells('a').attachToolbar();

	cms_treegrid_tb.addButton('cms_treegrid_refresh',100,'','lib/img/arrow_refresh.png','lib/img/arrow_refresh.png');
	cms_treegrid_tb.setItemToolTip('cms_treegrid_refresh','<?php echo _l('Refresh',1)?>');
	if (isIPAD)
	{
		cms_treegrid_tb.addButtonTwoState('lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
		cms_treegrid_tb.setItemToolTip('lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	<?php if(SCMS) { ?>
	cms_treegrid_tb.addText('cms_treegrid_shop_text', 100, '<?php echo _l('Display:',1)?>');

	has_shop_restrictions = cms_shoptree.getUserData(0, "has_shop_restrictions");
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
		cms_treegrid_tb.addButtonSelect("cms_treegrid_shop", 100, '', opts, "","",false,true);
		cms_treegrid_tb.setItemToolTip('cms_treegrid_shop','<?php echo _l('Shop')?>');
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
		cms_treegrid_tb.addButtonSelect("cms_treegrid_shop", 100, '<?php echo _l('All shops',1)?>', opts, "","",false,true);
		cms_treegrid_tb.setItemToolTip('cms_treegrid_shop','<?php echo _l('Shop')?>');
		cms_treegrid_tb.addButtonTwoState("cms_treegrid_inallshops", 100, '<?php echo _l('Update all shops')?>', "lib/img/checkbox_false.png", "lib/img/checkbox_true.png");
		cms_treegrid_tb.setItemToolTip('cms_treegrid_inallshops','<?php echo _l('If enabled: update all shops when you edit a category')?>');
		cms_treegrid_tb.setItemState("cms_treegrid_inallshops", true);
		in_all_shops=1;
		cms_treegrid_tb.setItemImage("cms_treegrid_inallshops", "lib/img/checkbox_true.png");
		cms_treegrid_tb.disableItem("cms_treegrid_inallshops");
	}
	<?php } ?>
	<?php
		$tmp=array();
		$clang=_l('Language');
		$optlang='';
		$active_langs = _s("CMS_PAGE_LANGUAGE_ALL");
		foreach($languages AS $lang){
			if($active_langs || (!$active_langs && $lang['active']))
			{
				if ($lang['id_lang']==$sc_agent->id_lang)
				{
					$clang=$lang['iso_code'];
					$optlang='cms_treegrid_lang_'.$lang['iso_code'];
				}
				$tmp[]="['cms_treegrid_lang_".$lang['iso_code']."', 'obj', '".$lang['name']."', '']";
			}
		}
		if (count($tmp) > 1)
		{
			echo 'var opts = ['.join(',',$tmp).'];';
	?>
		cms_treegrid_tb.addButtonSelect('cms_treegrid_lang',100,'<?php echo strtoupper($clang); ?>',opts,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);
		cms_treegrid_tb.setItemToolTip('cms_treegrid_lang','<?php echo _l('Select catalog language')?>');
		cms_treegrid_tb.setListOptionSelected('cms_treegrid_lang', '<?php echo $optlang ?>');
	<?php
		}
	?>
	cms_treegrid_tb.addSeparator("sep001",100);
	cms_treegrid_tb.addButton("cms_treegrid_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_selectall','<?php echo _l('Select all',1)?>');
	cms_treegrid_tb.addButton("cms_treegrid_seeinsc", 100, "", "lib/img/zoom.png", "lib/img/zoom.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_seeinsc','<?php echo _l('Open this category in SC',1)?>');
	<?php if(_r("ACT_CMS_ADD_CATEGORY")) { ?>
	cms_treegrid_tb.addButton("cms_treegrid_add", 100, "", "lib/img/add.png", "lib/img/add.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_add','<?php echo _l('Create new category',1)?>');
	<?php } ?>
	cms_treegrid_tb.addButton("cms_treegrid_in_bin", 100, "", "lib/img/bin_closed.png", "lib/img/bin_closed.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_in_bin','<?php echo _l('Put in the bin/trash',1)?>');
	<?php if(_r("ACT_CMS_EMPTY_RECYCLE_BIN")) { ?>
	cms_treegrid_tb.addButton("cms_treegrid_bin", 100, "", "lib/img/folder_delete.png", "lib/img/folder_delete.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_bin','<?php echo _l('Empty bin',1)?>');
	<?php } ?>
	cms_treegrid_tb.addSeparator("sep003",100);
	cms_treegrid_tb.addButton("cms_treegrid_active_cms", 100, "", "lib/img/lightbulb.png", "lib/img/lightbulb.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_active_cms','<?php echo _l('Activate all cms for the selected categories',1)?>');
	cms_treegrid_tb.addButton("cms_treegrid_desactive_cms", 100, "", "lib/img/lightbulb_off.png", "lib/img/lightbulb_off.png");
	cms_treegrid_tb.setItemToolTip('cms_treegrid_desactive_cms','<?php echo _l('Desactivate all cms for the selected categories',1)?>');

	<?php if(SCMS) { ?>
	if(has_shop_restrictions)
	{
		id_shop = shopselection;
		cms_treegrid_tb.setListOptionSelected('cms_treegrid_shop','shop-'+shopselection);
		cms_treegrid_tb.setItemText('cms_treegrid_shop',cms_treegrid_tb.getListOptionText('cms_treegrid_shop', 'shop-'+shopselection));
	}
	<?php } ?>
	cms_treegrid_tb.attachEvent("onClick", function (id){
		var shop = id.split("-");

		<?php if(SCMS) { ?>
		if(shop[0]!=undefined && shop[0]=="shop")
		{
			if(shop[1]!=undefined && shop[1]=="all")
			{
				cms_treegrid_tb.setItemText('cms_treegrid_shop','<?php echo _l('All shops',1)?>');
				id_shop = 0;
				cms_treegrid_tb.setItemState("cms_treegrid_inallshops", true);
				in_all_shops=1;
				cms_treegrid_tb.setItemImage("cms_treegrid_inallshops", "lib/img/checkbox_true.png");
				cms_treegrid_tb.disableItem("cms_treegrid_inallshops");
			}
			<?php foreach($shops as $shop) { ?>
			if(shop[1]!=undefined && shop[1]=="<?php echo $shop["id_shop"]; ?>")
			{
				cms_treegrid_tb.setItemText('cms_treegrid_shop','<?php echo str_replace("'", "\'", $shop['name']);?>');
				id_shop = "<?php echo $shop["id_shop"]; ?>";
				if(!has_shop_restrictions)
					cms_treegrid_tb.enableItem("cms_treegrid_inallshops");
			}
			<?php } ?>
			cms_treegrid_tb.setListOptionSelected('cms_treegrid_shop',id);
			displayTreegridCategories();
		}
		<?php } ?>

		flagLang=false; // changelang ; lang modified?
		<?php
		$tmp=array();
		$clang=_l('Language');
		foreach($languages AS $lang){ ?>
				if (id=='cms_treegrid_lang_<?php echo $lang['iso_code']; ?>')
				{
					id_actual_lang='<?php echo $lang['id_lang']; ?>';
					cms_treegrid_tb.setListOptionSelected('cms_treegrid_lang',id);
					cms_treegrid_tb.setItemText('cms_treegrid_lang','<?php echo strtoupper($lang['iso_code']); ?>');
					flagLang=true;
				}
		<?php } ?>
		if (flagLang){
			displayTreegridCategories();
		}

		if (id=='cms_treegrid_refresh')
		{
			displayTreegridCategories();
		}
		if (id=='cms_treegrid_selectall')
		{
			cms_treegrid_grid.selectAll();
		}
		if (id=='cms_treegrid_seeinsc')
		{
			selection=cms_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				selArray=selection.split(',');

				var shop_id = id_shop;
				var cms_category_id = selArray[0];

				if(cms_category_id!=0)
				{
					<?php if(SCMS) { ?>
						var action_after = "cms_tree.openItem("+cms_category_id+");cms_tree.selectItem("+cms_category_id+",false);cmsselection="+cms_category_id+";displayCms();";

						cms_shoptree.selectItem("all",false);
						if(shop_id!=0)
						{
							cms_shoptree.openItem(shop_id);
							cms_shoptree.selectItem(shop_id,false);
						}
						else
							shop_id = "all";
						onClickShopTree(shop_id, null,action_after);
					<?php } else { ?>
						cms_tree.openItem(cms_category_id);
						cms_tree.selectItem(cms_category_id,false);
						cmscatselection=cms_category_id;
						displayCms();
					<?php } ?>
					wCmsCatManagement.close();
				}
			}
		}
		if (id=='cms_treegrid_add'){
			selection=cms_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				selArray=selection.split(',');
				var parent_id = selArray[0];
				var cname=prompt('<?php echo _l('Create a CMS category:',1)?>');
				if (cname!=null)
				{
					$.post("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=insert&id_parent="+parent_id+'&id_lang='+id_actual_lang+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops,{name: (cname)},function(id){
						row_adding = id;
						row_adding_name = cname;
						cms_treegrid_grid.addRow(id, cname, null, parent_id,'../../folder_grey.png');
						cms_treegrid_grid.openItem(parent_id);
						cms_treegrid_grid.selectRowById(id);
					});
				}
			}else{
				alert('<?php echo _l('You need to select a parent category before creating a category',1)?>');
			}
		}
		if (id=='cms_treegrid_bin'){
			if (confirm('<?php echo _l('Are you sure to delete all CMS categories and CMS pages placed in the recycled bin?',1)?>'))
			{
				var id_bin=null;

				 cms_treegrid_grid.forEachRow(function(rId){
					 var is_recycle_bin = cms_treegrid_grid.getUserData(rId,"is_recycle_bin");
					 if(is_recycle_bin=="1")
						 id_bin = rId;
				 });

				if (id_bin!=null)
					$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=emptybin&gr_id="+id_bin+'&id_lang='+SC_ID_LANG,function(id){
						displayTreegridCategories();
					});
			}
		}
		if (id=='cms_treegrid_in_bin'){
			selection=cms_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				var id_bin=null;

				 cms_treegrid_grid.forEachRow(function(rId){
					 var is_recycle_bin = cms_treegrid_grid.getUserData(rId,"is_recycle_bin");
					 if(is_recycle_bin=="1")
						 id_bin = rId;
				 });

				if (id_bin!=null)
				{
					selArray=selection.split(',');
					var nb_ids = selArray.length*1 - 1;
					$.each(selArray, function(num,rId){
						nb_deleting = nb_deleting*1 + 1;
						$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=move&idCateg="+rId+"&idNewParent="+id_bin+"&idNextBrother="+id_bin+'&id_shop=0&in_all_shops=1&id_lang='+id_actual_lang, function(data){
						}).always(function() {
							nb_deleting = nb_deleting*1 - 1;
							if(nb_deleting<=0)
								displayTreegridCategories();
						});
					});
				}
			}
		}
		if(id=="cms_treegrid_active_cms")
		{
			selection=cms_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=active_cms&id_cms_categories="+selection+"&value=1&id_shop="+id_shop+"&in_all_shops="+in_all_shops+"&id_lang="+id_actual_lang, function(data){
					dhtmlx.message({text:'<?php echo _l('The CMS page has been updated correctly.',1)?>',type:'info',expire:5000});
				});
			}
		}
		if(id=="cms_treegrid_desactive_cms")
		{
			selection=cms_treegrid_grid.getSelectedRowId();
			if (selection!='' && selection!=null)
			{
				$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=active_cms&id_cms_categories="+selection+"&value=0&id_shop="+id_shop+"&in_all_shops="+in_all_shops+"&id_lang="+id_actual_lang, function(data){
					dhtmlx.message({text:'<?php echo _l('The CMS page has been updated correctly.',1)?>',type:'info',expire:5000});
				});
			}
		}
	});

	cms_treegrid_grid = dhxlCmsCatManagement.cells('a').attachGrid();
	cms_treegrid_grid._name='cms_treegrid';
	cms_treegrid_grid.setImagePath("lib/js/imgs/");
	cms_treegrid_grid.setFiltrationLevel(-2);
	cms_treegrid_grid.enableTreeCellEdit(0);
<?php
	$sql = "SELECT COUNT(*) AS total FROM "._DB_PREFIX_."cms_category";
	$res = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($sql);
	if ($res['total'] > 10)
	{
?>
	cms_treegrid_grid.enableSmartRendering(true);
<?php
	}
?>
	cms_treegrid_grid.enableDragAndDrop(true);
	cms_treegrid_grid.setDragBehavior("complex");
	cms_treegrid_grid.setAwaitedRowHeight(<?php echo getGridImageHeight(); ?>);
	cms_treegrid_grid._dragBehavior="complex";
	cms_treegrid_grid.enableMultiselect(true);

	var row_adding = 0;
	var row_adding_name = "";
	cms_treegrid_grid.attachEvent("onRowAdded", function(rId){
		if(rId==row_adding)
		{
			idxId=cms_treegrid_grid.getColIndexById('id_cms_category');
			idxName=cms_treegrid_grid.getColIndexById('name');
			idxNbCms = cms_treegrid_grid.getColIndexById('nb_cms');
			idxActive=cms_treegrid_grid.getColIndexById('active');

			cms_treegrid_grid.cells(rId,idxId).setValue(rId);
			cms_treegrid_grid.cells(rId,idxName).setValue(row_adding_name);
			cms_treegrid_grid.cells(rId,idxNbCms).setValue("0");
			cms_treegrid_grid.cells(rId,idxActive).setValue("0");

			row_adding = 0;
			row_adding_name = "";

			colorActive();
		}
	});

	// UISettings
	cms_treegrid_grid._uisettings_prefix='cms_catmanagement_treegrid';
	cms_treegrid_grid._uisettings_name=cms_treegrid_grid._uisettings_prefix;
	cms_treegrid_grid._first_loading=1;

	// UISettings
	initGridUISettings(cms_treegrid_grid);

	displayTreegridCategories();

	// Data update
	 cms_treegrid_grid.attachEvent("onEditCell", function(stage,rId,cInd,nValue,oValue){
		idxActive=cms_treegrid_grid.getColIndexById('active');
		idxName=cms_treegrid_grid.getColIndexById('name');

		if(stage==0 || stage==1)
		{
			 var is_recycle_bin = cms_treegrid_grid.getUserData(rId,"is_recycle_bin");
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
		if (!in_array(cInd,enableOnCols))
			return false;

		if(stage==2)
		{
			$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=update&gr_id="+rId+"&field="+field+"&value="+nValue+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){
				if (field === 'active') {
					var icon_active = 'lib/js/imgs/catalog.png';
					var icon_desactive = 'lib/js/imgs/folder_grey.png';

					if (nValue == "1")
						cms_treegrid_grid.setItemImage(rId, icon_active);
					else if (nValue == "0")
						cms_treegrid_grid.setItemImage(rId, icon_desactive);
				}
			});
		}
		return true;
	});

	// Event drag & drop
	cms_treegrid_grid.attachEvent("onDrop",function (idSourceIds,idTarget,idBefore,sourceobject,targetTree){
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
			var parent_id = cms_treegrid_grid.getParentId(idSource);
			var real_parent_id = parent_id;
			if(real_parent_id==0)
			{
				real_parent_id = cms_treegrid_grid.getUserData("","parent_root");
			}

			var next_brother_id = parent_id;
			var subItems = cms_treegrid_grid.getAllSubItems(parent_id);
			var actual_level = cms_treegrid_grid.getLevel(idSource);
			if(subItems!=undefined && subItems!=null && subItems!="")
			{
				subItems = subItems.split(",");
				var is_id = false;
				$.each(subItems, function(num, id){
					if(cms_treegrid_grid.getLevel(id)==actual_level)
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
			$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=move&idCateg="+idSource+"&idNewParent="+real_parent_id+"&idNextBrother="+next_brother_id+'&id_shop='+id_shop_tmp+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});
		});
		return false;
	});
	cms_treegrid_grid.attachEvent("onDragIn",function(idSource,idTarget,sourceobject,targetobject){
		if(sourceobject._name=='tree')
			return false;
		return true;
	});
	cms_treegrid_grid.attachEvent("onDrag",function(sourceid,targetid,sObj,tObj,sInd,tInd){
		if(sourceid!=undefined && sourceid!=0 && targetid!=undefined && targetid!=0)
		{
			var is_recycle_bin = cms_treegrid_grid.getUserData(targetid,"is_recycle_bin");
			if(is_recycle_bin==1)
			{
				var not_deletable = cms_treegrid_grid.getUserData(sourceid,"not_deletable");
				if(not_deletable==1)
					return false;
			}
		}
		return true;
	});

	// Context menu for grid
	cms_treegrid_cmenu=new dhtmlXMenuObject();
	cms_treegrid_cmenu.renderAsContextMenu();
	clipboardType_CatTreegrid = null;
	var copy_multiple_id = null;
	function onGridCatTreegridContextButtonClick(itemId){
		tabId=cms_treegrid_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="copy"){
			if (lastColumnRightClicked_CatTreegrid!=0)
			{
				clipboardValue_CatTreegrid=cms_treegrid_grid.cells(tabId,lastColumnRightClicked_CatTreegrid).getValue();
				cms_treegrid_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cms_treegrid_grid.cells(tabId,lastColumnRightClicked_CatTreegrid).getTitle());
				clipboardType_CatTreegrid=lastColumnRightClicked_CatTreegrid;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked_CatTreegrid!=0 && clipboardValue_CatTreegrid!=null && clipboardType_CatTreegrid==lastColumnRightClicked_CatTreegrid)
			{
				selection=cms_treegrid_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					idxActive=cms_treegrid_grid.getColIndexById('active');
					idxName=cms_treegrid_grid.getColIndexById('name');

					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						if (cms_treegrid_grid.getColumnId(lastColumnRightClicked_CatTreegrid).substr(0,5)!='attr_')
						{
							cms_treegrid_grid.cells(selArray[i],lastColumnRightClicked_CatTreegrid).setValue(clipboardValue_CatTreegrid);
							var field = 'active';
							if(idxName==lastColumnRightClicked_CatTreegrid)
								field = 'name';
							$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=update&gr_id="+selArray[i]+"&field="+field+"&value="+clipboardValue_CatTreegrid+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});
							colorActive();
						}
					}
				}
			}
		}
		if (itemId=="expand"){
			cms_treegrid_grid.openItem(tabId);
			expandChildren(tabId);
		}
		if (itemId=="collapse"){
			cms_treegrid_grid.closeItem(tabId);
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
							window.open(shopUrls[shop]+'index.php?id_cms_category='+tabId+'&controller=cms&id_lang='+SC_ID_LANG);
			<?php
					}
					else { ?>
						window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms_category='+tabId+'&controller=cms&id_lang='+SC_ID_LANG);
					<?php }
				}else{
			?>
						window.open('<?php echo SC_PS_PATH_REL;?>cms.php?id_cms_category='+tabId);
			<?php
				}
			?>
		}
		if (itemId=="gopsbo"){
			wModifyCategory = dhxWins.createWindow("wModifyCategory", 50, 50, 1260, $(window).height()-75);
			wModifyCategory.setText('<?php echo _l('Modify the category and close this window to refresh the tree',1)?>');
			wModifyCategory.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'admincmscontent':'AdminCmsContent');?>&updatecms_category&id_cms_category="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminCmsContent'); ?>");
			wModifyCategory.attachEvent("onClose", function(win){
				displayTreegridCategories();
				return true;
			});
		}
		if (itemId=="enable"){
			var idxActive = cms_treegrid_grid.getColIndexById('active');
			var actual_active = cms_treegrid_grid.cells(tabId,idxActive).getValue();

			if(actual_active=="1" || actual_active==1)
				actual_active = "0";
			else
				actual_active = "1";

			cms_treegrid_grid.cells(tabId,idxActive).setValue(actual_active);
			$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=update&gr_id="+tabId+"&field=active&value="+actual_active+'&id_shop='+id_shop+'&in_all_shops='+in_all_shops+'&id_lang='+id_actual_lang, function(data){});

			var icon_active = 'lib/js/imgs/catalog.png';
			var icon_desactive = 'lib/js/imgs/folder_grey.png';

			if(actual_active=="1" && icon_desactive)
				cms_treegrid_grid.setItemImage(tabId,icon_active);
			else if(actual_active=="0" && icon_active)
				cms_treegrid_grid.setItemImage(tabId,icon_desactive);

			colorActive();
		}
		if (itemId=="copy_multiple"){
			copy_multiple_id = tabId;
		}
		if (itemId=="paste_multiple"){
			if(copy_multiple_id!=undefined && copy_multiple_id!=null && copy_multiple_id!="" && copy_multiple_id>0) {
				if (tabId != undefined && tabId != null && tabId != "" && tabId > 0) {
					$.get("index.php?ajax=1&act=cms_win-cmscatmanagement_update&action=paste_multiple&gr_id=" + copy_multiple_id + "&DEBUG=1&id_parent=" + tabId + "&id_lang=" + id_actual_lang, function (data) {
						displayTreegridCategories();
					});
				}
			}
		}
	}
	cms_treegrid_cmenu.attachEvent("onClick", onGridCatTreegridContextButtonClick);
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
	cms_treegrid_cmenu.loadStruct(contextMenuXML);
	cms_treegrid_grid.enableContextMenu(cms_treegrid_cmenu);

	cms_treegrid_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		var copiable = false;
		var enableOnCols=new Array(
				cms_treegrid_grid.getColIndexById('name'),
				cms_treegrid_grid.getColIndexById('active')
				);
		if (in_array(colidx,enableOnCols))
			copiable = true;
		lastColumnRightClicked_CatTreegrid=colidx;
		cms_treegrid_cmenu.setItemText('object', '<?php echo _l('Category:')?> '+cms_treegrid_grid.cells(rowid,cms_treegrid_grid.getColIndexById('name')).getTitle());
		if(copiable)
		{
			cms_treegrid_cmenu.showItem('copy');
			cms_treegrid_cmenu.showItem('paste');
			cms_treegrid_cmenu.showItem('sep_02');
			if (lastColumnRightClicked_CatTreegrid==clipboardType_CatTreegrid)
			{
				cms_treegrid_cmenu.setItemEnabled('paste');
			}else{
				cms_treegrid_cmenu.setItemDisabled('paste');
			}
		}
		else
		{
			cms_treegrid_cmenu.hideItem('copy');
			cms_treegrid_cmenu.hideItem('paste');
			cms_treegrid_cmenu.hideItem('sep_02');
		}

		if(copy_multiple_id!=undefined && copy_multiple_id!=null && copy_multiple_id!="" && copy_multiple_id>0)
			cms_treegrid_cmenu.setItemEnabled('paste_multiple');
		else
			cms_treegrid_cmenu.setItemDisabled('paste_multiple');

		var numberSelectedRows = parseInt(grid.selectedRows.length);
		if (numberSelectedRows > 1) {
			cms_treegrid_cmenu.setItemDisabled('copy');
			cms_treegrid_cmenu.setItemDisabled('paste');
			cms_treegrid_cmenu.setItemDisabled('copy_multiple');
			cms_treegrid_cmenu.setItemDisabled('paste_multiple');
		} else if (numberSelectedRows === 1) {
			cms_treegrid_cmenu.setItemEnabled('copy');
			cms_treegrid_cmenu.setItemEnabled('paste');
			cms_treegrid_cmenu.setItemEnabled('copy_multiple');
			cms_treegrid_cmenu.setItemEnabled('paste_multiple');
		}

		<?php if(SCMS) { ?>
		var shop = id_shop;
		if(shop==0 || shop==null || shop=="")
			shop = id_shop_default;
		if(shopUrls[shop] != undefined && shopUrls[shop] != "" && shopUrls[shop] != null)
		{
			cms_treegrid_cmenu.setItemEnabled('goshop');
		}else{
			cms_treegrid_cmenu.setItemDisabled('goshop');
		}
		<?php } ?>

		return true;
	});

// PROPERTIES
	dhxlCmsCatManagement.cells('b').setText('<?php echo _l('Properties',1)?>');
	var cms_prop_tb = dhxlCmsCatManagement.cells('b').attachToolbar();

	actual_cmscatmanagement_subproperties = "cms_prop_info";

	var opts = new Array();
	cms_prop_tb.addButtonSelect("cms_prop_subproperties", 100, "<?php echo _l('Name & description')?>", opts, "lib/img/description.png", "lib/img/application_form_magnify.png",false,true);

	<?php
	@$sub_files = scandir(SC_DIR.'lib/cms/win-cmscatmanagement');
	foreach ($sub_files as $sub_item) {
		if ($sub_item != '.' && $sub_item != '..') {
			if (is_dir(SC_DIR . 'lib/cms/win-cmscatmanagement/' . $sub_item) && file_exists(SC_DIR . 'lib/cms/win-cmscatmanagement/' . $sub_item . '/cms_win-cmscatmanagement_' . $sub_item . '_init.php')) {
				require_once(SC_DIR . 'lib/cms/win-cmscatmanagement/' . $sub_item . '/cms_win-cmscatmanagement_' . $sub_item . '_init.php');
			}
		}
	}
	?>
	initCmsCatManagementPropInfo();

/*
 * FUNCTIONS
 */

function displayTreegridCategories()
{
	cms_treegrid_grid.clearAll(true);
	cms_treegrid_grid.loadXML("index.php?ajax=1&act=cms_win-cmscatmanagement_get&id_shop="+id_shop+"&id_lang="+id_actual_lang,function()
	{
//		 UISettings
		loadGridUISettings(cms_treegrid_grid);

//		 UISettings
		cms_treegrid_grid._first_loading=0;
		cms_treegrid_grid.expandAll();
		colorActive();
	});
}

function colorActive()
{
	idxActive=cms_treegrid_grid.getColIndexById('active');
	idxName=cms_treegrid_grid.getColIndexById('name');

	cms_treegrid_grid.forEachRow(function(rId){
		var value = cms_treegrid_grid.cells(rId,idxActive).getValue();
		if(value==null || value=="" || value==0 || value=="0")
			cms_treegrid_grid.cells(rId,idxName).setBgColor('#cccccc');
		else
			cms_treegrid_grid.cells(rId,idxName).setBgColor('');
	});
}

function hideCmsCatManagementSubpropertiesItems()
{
	cms_prop_tb.forEachItem(function(itemId){
		if(itemId!="cms_prop_subproperties")
			cms_prop_tb.hideItem(itemId);
	});
}

function expandChildren(parent_Id)
{
	var children = cms_treegrid_grid.getAllSubItems(parent_Id);

	if(children!=undefined && children!=null && children!=0 && children!="")
	{
		var ids = children.split(',');
		$.each(ids, function(num, id){
			cms_treegrid_grid.openItem(id);
			expandChildren(id);
		});
	}
}
function collapseChildren(parent_Id)
{
	var children = cms_treegrid_grid.getAllSubItems(parent_Id);

	if(children!=undefined && children!=null && children!=0 && children!="")
	{
		var ids = children.split(',');
		$.each(ids, function(num, id){
			cms_treegrid_grid.closeItem(id);
			collapseChildren(id);
		});
	}
}
</script>
