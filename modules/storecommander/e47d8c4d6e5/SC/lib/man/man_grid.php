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
	man_grid=man_manufacturerPanel.attachGrid();
	man_grid._name='grid';
	man_manufacturerPanel.setText('<?php echo _l('Manufacturers',1); ?>');

	var open_man_grid = "auto";
	var display_manufacturer_after_man_select = false;
	var display_manufacturer_after_select_view = true;
	var open_man_id_page = 0;
	loadingtime = 0;

	$(document).ready(function(){
		displayManufacturers(null, true);
	});

	// UISettings
	man_grid._uisettings_prefix='man_grid_';
	man_grid._uisettings_name=man_grid._uisettings_prefix;
	man_grid._first_loading=1;

	man_grid.enableDragAndDrop(true);
	man_grid.setDragBehavior('child');
	man_grid.enableSmartRendering(true);
	<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
	man_grid.enableColumnMove(false);
	<?php } ?>

	<?php
		if(SCMS) {
	?>
	man_shoptree.attachEvent("onClick", onClickShopTree);
	function onClickShopTree(idshop, param, callback) {
		if (idshop == 'all') {
			displayManufacturers(null, true);
		} else {
			displayManufacturers();
		}

	}
	<?php
	} else {
	?>
		$(document).ready(function(){
			displayManufacturers(null, true);
		});
	<?php
	}
	?>

	man_grid._key_events.k9_0_0=function(){
		man_grid.editStop();
		man_grid.selectCell(man_grid.getRowIndex(man_grid.getSelectedRowId())+1,man_grid.getSelectedCellIndex(),true,false,true,true);
	};

	man_grid_tb=man_manufacturerPanel.attachToolbar();
    man_grid_tb.addButton("refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
    man_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');
    man_grid_tb.addButton("add", 100, "", "lib/img/add.png", "lib/img/add.png");
    man_grid_tb.setItemToolTip('add','<?php echo _l('Create new manufacturer')?>');
	man_grid_tb.addButton("delete", 100, "", "lib/img/delete.gif", "lib/img/delete.gif");
	man_grid_tb.setItemToolTip('delete','<?php echo _l('This will permanently delete the selected manufacturer')?>');
	man_grid_tb.addButton("selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	man_grid_tb.setItemToolTip('selectall','<?php echo _l('Select all manufacturers')?>');
    <?php if(_r("ACT_CAT_FAST_EXPORT")) { ?>
    man_grid_tb.addButton("exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
    man_grid_tb.setItemToolTip('exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.')?>');
    <?php } ?>
    man_grid_tb.addButton("help", 100, "", "lib/img/help.png", "lib/img/help.png");
    man_grid_tb.setItemToolTip('help','<?php echo _l('Help')?>');

	<?php
	$tmp=array();
	$clang=_l('Language');
	$optlang='';
	foreach($languages AS $lang){
		if ($lang['id_lang']==$sc_agent->id_lang)
		{
			$clang=$lang['iso_code'];
			$optlang='man_lang_'.$lang['iso_code'];
		}
		$tmp[]="['man_lang_".$lang['iso_code']."', 'obj', '".$lang['name']."', '']";
	}
	if (count($tmp) > 1)
	{
	echo 'var opts = ['.join(',',$tmp).'];';
	?>
	man_grid_tb.addButtonSelect('lang',0,'<?php echo $clang?>',opts,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);
	man_grid_tb.setItemToolTip('lang','<?php echo _l('Select manufacturer language')?>');
	man_grid_tb.setListOptionSelected('lang', '<?php echo $optlang ?>');
	<?php
	}
	?>
	var gridnames=new Object();
	<?php if(_r("GRI_MAN_VIEW_GRID_LIGHT")) { ?>gridnames['grid_light']='<?php echo _l('Light view',1)?>';<?php } ?>
	<?php if(_r("GRI_MAN_VIEW_GRID_LARGE")) { ?>gridnames['grid_large']='<?php echo _l('Large view',1)?>';<?php } ?>
	<?php if(_r("GRI_MAN_VIEW_GRID_SEO")) { ?>gridnames['grid_seo']='<?php echo _l('SEO',1)?>';<?php } ?>

	var opts = new Array();
	$.each(gridnames, function(index, value) {
		opts[opts.length] = new Array(index, 'obj', value, '');
	});
	if (opts.length > 25)
		$('div.dhx_toolbar_poly_dhx_skyblue').addClass('dhx_toolbar_poly_dhx_skyblue_SCROLLBAR');

	gridView = (in_array('<?php echo _s('MAN_MANUF_GRID_DEFAULT')?>',Object.keys(gridnames))?'<?php echo _s('MAN_MANUF_GRID_DEFAULT')?>':opts[0][0]);
	// UISettings
	man_grid._uisettings_name=man_grid._uisettings_prefix+gridView;

	man_grid_tb.addButtonSelect("gridview", 0, "<?php echo _l('Light view')?>", opts, "lib/img/table_gear.png", "lib/img/table_gear.png",false,true);
	man_grid_tb.setItemToolTip('gridview','<?php echo _l('Grid view settings')?>');
	if (isIPAD){
		var opts = [['cols123', 'obj', '<?php echo _l('Columns')?> 1 + 2 + 3', ''],
			['cols12', 'obj', '<?php echo _l('Columns')?> 1 + 2', ''],
			['cols23', 'obj', '<?php echo _l('Columns')?> 2 + 3', '']
		];
		man_grid_tb.addButtonSelect("layout", 0, "", opts, "lib/img/layout.png", "lib/img/layout.png",false,true);
	}

	function gridToolBarOnClick(id){
		if (id.substr(0,5)=='grid_'){

			oldGridView=gridView;
			gridView=id;

			// UISettings
			man_grid._uisettings_name=man_grid._uisettings_prefix+gridView;

			man_grid_tb.setItemText('gridview',gridnames[id]);
			$(document).ready(function(){displayManufacturers();});
		}
		if (id=='help'){
			<?php echo "window.open('".getHelpLink('man_toolbar_prod')."');"; ?>
		}
		if (id=='filters_reset')
		{
			for(var i=0,l=man_grid.getColumnsNum();i<l;i++)
			{
				if (man_grid.getFilterElement(i)!=null) man_grid.getFilterElement(i).value="";
			}
			man_grid.filterByAll();
			man_grid_tb.setListOptionSelected('filters','');
		}
		if (id=='filters_cols_show')
		{
			for(i=0,l=man_grid.getColumnsNum() ; i < l ; i++)
			{
				man_grid.setColumnHidden(i,false);
			}
			man_grid_tb.setListOptionSelected('filters','');
		}
		if (id=='filters_cols_hide')
		{
			idxManufacturerID=man_grid.getColIndexById('id');
			idxName=man_grid.getColIndexById('meta_title');
			man_grid_tb.setListOptionSelected('filters','');
		}
        if (id=='exportcsv'){
            man_grid.enableCSVHeader(true);
            man_grid.setCSVDelimiter("\t");
            var csv=man_grid.serializeToCSV(true);
            displayQuickExportWindow(csv,1);
        }
		flagLang=false; // changelang ; lang modified?
		<?php
		$tmp=array();
		$clang=_l('Language');
		foreach($languages AS $lang){
			echo'
			if (id==\'man_lang_'.$lang['iso_code'].'\')
			{
				SC_ID_LANG='.$lang['id_lang'].';
				man_grid_tb.setItemText(\'lang\',\''.$lang['iso_code'].'\');
				flagLang=true;
			}';
		}
		?>
		if (flagLang)
		{
			displayManufacturers();
		}
		if (id=='refresh'){
			displayManufacturers();
		}
		if (id=='add'){
			var newId = new Date().getTime();
			newRow=new Array('','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
			newRow=newRow.slice(0,man_grid.getColumnsNum()-1);
			idxID=man_grid.getColIndexById('id_manufacturer');
			idxName=man_grid.getColIndexById('name');
			idxActive=man_grid.getColIndexById('active');
			newRow[idxID]=newId;
			newRow[idxName]='new';
			if (idxActive) newRow[idxActive]='<?php echo _s('MAN_MANUF_CREA_ACTIVE');?>';
			// INSERT
			man_grid.addRow(newId,newRow);
			man_grid.setRowHidden(newId, true);

			var params = {
				name: "man_manufacturer_update_queue",
				row: newId,
				action: "insert",
				params: {callback: "callbackManufacturerUpdate('"+newId+"','insert','{newid}',{data});"}
			};

			// COLUMN VALUES
			man_grid.forEachCell(newId,function(cellObj,ind){
				params.params[man_grid.getColumnId(ind)] = man_grid.cells(newId,ind).getValue();
			});
			params.params['id_lang'] = SC_ID_LANG;
			// USER DATA
			$.each(man_grid.UserData.gridglobaluserdata.keys, function(i, key){
				params.params[key] = man_grid.UserData.gridglobaluserdata.values[i];
			});

			sendInsert(params,man_manufacturerPanel);
		}
		if (id=="delete")
		{
			if (confirm('<?php echo _l('Permanently delete the selected manufacturer everywhere in the shop.',1)?>'))
			{
				selection=man_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, rId)
				{
					var params =
					{
						name: "man_manufacturer_update_queue",
						row: rId,
						action: "delete",
						params: {},
						callback: "callbackManufacturerUpdate('"+rId+"','delete','"+rId+"');"
					};
					params.params = JSON.stringify(params.params);
					man_grid.setRowTextStyle(rId, "text-decoration: line-through;");
					addInUpdateQueue(params,man_grid);
				});
			}
		}
		if (id=='selectall'){
			man_grid.enableSmartRendering(false);
			man_grid.selectAll();
			getGridStat();
		}

		if (id=='cols123')
		{
			man.cells("a").expand();
			man.cells("a").setWidth(300);
			man.cells("b").expand();
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth(500);
		}
		if (id=='cols12')
		{
			man.cells("a").expand();
			man.cells("a").setWidth($(document).width()/3);
			man.cells("b").expand();
			dhxLayout.cells('b').collapse();
		}
		if (id=='cols23')
		{
			man.cells("a").collapse();
			man.cells("b").expand();
			man.cells("b").setWidth($(document).width()/2);
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth($(document).width()/2);
		}
	}
	man_grid_tb.attachEvent("onClick",gridToolBarOnClick);
	man_grid_tb.attachEvent("onStateChange", function(id,state){
		if (id=='copytocateg'){
			if (state) {
				copytocateg=true;
			}else{
				copytocateg=false;
			}
		}
	});

	man_grid.setImagePath('lib/js/imgs/');
	man_grid.enableMultiselect(true);
	man_grid_sb=man_manufacturerPanel.attachStatusBar();

	gridToolBarOnClick(gridView);

	man_grid.attachEvent("onBeforeDrag",function(idsource){
		if (man_grid.getSelectedRowId()==null) draggedManufacturer=idsource;
		if (man_tree._dragBehavior!="child")
		{
			man_tree.setDragBehavior("child");
			man_tree._dragBehavior="child";
		}
		return true;
	});

	man_grid.attachEvent("onDragIn",function(idsource,idtarget,sourceobject,targetobject){
		<?php if(SCMS) { ?> if(shopselection==0) return false; <?php } ?>
		if (sourceobject._name=="grid") return true;
		return false;
	});

	man_grid.rowToDragElement=function(id){
		var text="";
		idxName=man_grid.getColIndexById('meta_title');
		if (man_grid.getSelectedRowId()!=null)
		{
			var dragged=man_grid.getSelectedRowId().split(',');
			if (dragged.length > 1){ // multi
				for (var i=0; i < dragged.length; i++)
				{
					text += man_grid.cells(dragged[i],idxName).getValue() + "<br/>";
				}
			}else{ // single
				text += man_grid.cells(dragged,idxName).getValue() + "<br/>";
			}
		}else{ // single
			text += man_grid.cells(draggedManufacturer,idxName).getValue() + "<br/>";
		}
		return text;
	}

	// multiedition context menu
	man_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		lastColumnRightClicked=colidx;

		man_menu.setItemText('object', '<?php echo _l('Manufacturer :')?> '+man_grid.cells(rowid,man_grid.getColIndexById('name')).getValue());
		// paste function
		if (lastColumnRightClicked==clipboardType)
		{
			man_menu.setItemEnabled('paste');
		}else{
			man_menu.setItemDisabled('paste');
		}
		var colType=man_grid.getColType(colidx);
		if (colType=='ro')
		{
			man_menu.setItemDisabled('copy');
			man_menu.setItemDisabled('paste');
		}else{
			man_menu.setItemEnabled('copy');
		}

		<?php if(SCMS) { ?>
		if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
		{
			man_menu.setItemEnabled('goshop');
		}else{
			man_menu.setItemDisabled('goshop');
		}
		<?php } ?>
		return true;
	});

	function onEditCell(stage,rId,cInd,nValue,oValue){
		var coltype=man_grid.getColType(cInd);
		if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();
		lastEditedCell=cInd;
		if (nValue!=oValue){
			man_grid.setRowColor(rId,'BlanchedAlmond');
			idxActive=man_grid.getColIndexById('active');
			idxName=man_grid.getColIndexById('name');
			idxMetaDescription=man_grid.getColIndexById('meta_description');
			idxMetaKeywords=man_grid.getColIndexById('meta_keywords');
			if (cInd == idxName){
				man_grid.cells(rId,idxName).setValue(man_grid.cells(rId,idxName).getValue());
			}
			if (cInd == idxMetaDescription){
				man_grid.cells(rId,idxMetaDescription).setValue(man_grid.cells(rId,idxMetaDescription).getValue().substr(0,<?php echo _s('MAN_META_DESC_SIZE')?>));
			}
			if (cInd == idxMetaKeywords){
				man_grid.cells(rId,idxMetaKeywords).setValue(man_grid.cells(rId,idxMetaKeywords).getValue().substr(0,<?php echo _s('MAN_META_KEYWORDS_SIZE')?>));
			}
			if (cInd == idxActive){ //Active update
				if (nValue==0){
					man_grid.cells(rId,idxName).setBgColor('#D7D7D7');
				}else{
					man_grid.cells(rId,idxName).setBgColor(man_grid.cells(rId,0).getBgColor());
				}
			}
		}

		if (nValue!=oValue){
			var id = rId;
//			console.log(cInd);
			addManufacturerInQueue(rId, "update", cInd);
			return true;
		}
	}
	man_grid.attachEvent("onEditCell",onEditCell);

	// Context menu for Grid
	man_menu=new dhtmlXMenuObject();
	man_menu.renderAsContextMenu();
	function onGridManufacturerContextButtonClick(itemId){
		tabId=man_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="gopsbo"){
			wModifyManufacturer = dhxWins.createWindow("wModifyManufacturer", 50, 50, 1260, $(window).height()-75);
			wModifyManufacturer.setText('<?php echo _l('Modify the manufacturer and close this window to refresh the grid',1)?>');
			<?php if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')) { ?>
			wModifyManufacturer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminManufacturers&updatemanufacturer&id_manufacturer="+tabId+"&token=<?php echo $sc_agent->getPSToken('AdminManufacturers');?>");
		<?php } else { ?>
			wModifyManufacturer.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminManufacturers&updatemanufacturer&id_manufacturer="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminManufacturers');?>");
		<?php } ?>
			wModifyManufacturer.attachEvent("onClose", function(win){
				displayManufacturers();
				return true;
			});
		}
		if (itemId=="goshop"){
			var sel=man_grid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				var k=1;
				tabId.forEach(function(id_manufacturer) {
					if (k > <?php echo _s('MAN_MANUFACTURER_OPEN_URL')?>) {
						return false
					}
					idxActive=man_grid.getColIndexById('active');
					<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
					if (idxActive) {
						if (man_grid.cells(id_manufacturer, idxActive).getValue() == 0) {
							<?php $preview_url = '&adtoken='.$sc_agent->getPSToken('AdminManufacturers').'&id_employee='.$sc_agent->id_employee; ?>
							var previewUrl = "<?php echo $preview_url; ?>";
						} else {
							var previewUrl = 0;
						}

					}
					<?php } else { ?>
					if (idxActive)
						if (man_grid.cells(id_manufacturer,idxActive).getValue()==0)
							continue;
					<?php } ?>
					<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
					if(SCMS) {
					?>
					if (previewUrl != 0) {
						if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
							window.open(shopUrls[shopselection]+'index.php?id_manufacturer='+id_manufacturer+'&controller=manufacturer&id_lang='+SC_ID_LANG+previewUrl);
					} else {
						if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
							window.open(shopUrls[shopselection] + 'index.php?id_manufacturer=' + id_manufacturer + '&controller=manufacturer&id_lang=' + SC_ID_LANG);
					}
					<?php
					}
					else {
					?>
					if (previewUrl != 0) {
						window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_manufacturer=' + id_manufacturer + '&controller=manufacturer&id_lang=' + SC_ID_LANG+previewUrl);
					} else {
						window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_manufacturer=' + id_manufacturer + '&controller=manufacturer&id_lang=' + SC_ID_LANG);
					}
					<?php
					}
					}else{
					?>
					window.open('<?php echo SC_PS_PATH_REL;?>manufacturer.php?id_manufacturer=' + id_manufacturer);
					<?php } ?>
					k++;
				});
			}else{
				var tabId=man_grid.contextID.split('_');
				<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
					if(SCMS) {
					?>
					if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
						window.open(shopUrls[shopselection]+'index.php?id_manufacturer='+tabId[0]+'&controller=manufacturer');
					<?php
					}
					else {
					?>
					window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_manufacturer='+tabId[0]+'&controller=manufacturer');
					<?php
					}
				}else{
				?>
					window.open('<?php echo SC_PS_PATH_REL;?>manufacturer.php?id_manufacturer='+tabId[0]);
				<?php
				}
				?>
			}
		}
		if (itemId=="copy"){
			if (lastColumnRightClicked!=0)
			{
				clipboardValue=man_grid.cells(tabId,lastColumnRightClicked).getValue();
				man_menu.setItemText('paste' , '<?php echo _l('Paste')?> '+man_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
				clipboardType=lastColumnRightClicked;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
			{
				selection=man_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						man_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
						man_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
						onEditCell(null,selArray[i],lastColumnRightClicked,clipboardValue,null);
					}
				}
			}
		}

		<?php
		$massupdate_suffix = "";
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$massupdate_suffix = "15";
		?>
	}
	man_menu.attachEvent("onClick", onGridManufacturerContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('See on shop')?>" id="goshop"/>'+
		'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
		'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
		'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
		'</menu>';
	man_menu.loadStruct(contextMenuXML);
	man_grid.enableContextMenu(man_menu);

	//#####################################
	//############ Events
	//#####################################

	// Click on a manufacturer
	function doOnRowSelected(idmanufacturer){
		if (!dhxLayout.cells('b').isCollapsed() && last_manufacturerID!=idmanufacturer)
		{
			last_manufacturerID=idmanufacturer;
			idxName=man_grid.getColIndexById('name');

			if (propertiesPanel!='descriptions'){
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+man_grid.cells(last_manufacturerID,idxName).getValue());
			}
			<?php
			echo eval('?>'.$pluginManufacturerProperties['doOnManufacturerRowSelected'].'<?php ');
			?>
		}
	}

	man_grid.attachEvent("onRowSelect",doOnRowSelected);

	// UISettings
	initGridUISettings(man_grid);

	man_grid.attachEvent("onColumnHidden",function(indx,state){
		idxImg=man_grid.getColIndexById('image');
		if (idxImg && !state){
			man_grid.setAwaitedRowHeight(<?php echo getGridImageHeight(); ?>);
		}else{
			man_grid.setAwaitedRowHeight(30);
		}
	});
	man_grid.attachEvent("onFilterEnd", function(elements){
		getGridStat();
	});
	man_grid.attachEvent("onSelectStateChanged", function(id){
		getGridStat();
	});

	function displayManufacturers(callback, firsttime = null)
	{
		if(firsttime!=undefined && firsttime!=null && firsttime!="") {
			<?php
			$sql_shop ="SELECT id_shop
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'";
			$shops = Db::getInstance()->ExecuteS($sql_shop);
			$value = '';
			foreach($shops as $shop) {
				$value .= (!empty($value) ? ',' : '').$shop['id_shop'];
			}
			echo 'shopselection = "'.$value.'";';
			?>
		}
		if (shopselection!=undefined && shopselection!=null && shopselection!="" && shopselection!=0)
		{
			oldFilters=new Array();
			for(var i=0,l=man_grid.getColumnsNum();i<l;i++)
			{
				if (man_grid.getFilterElement(i)!=null && man_grid.getFilterElement(i).value!='')
					oldFilters[man_grid.getColumnId(i)]=man_grid.getFilterElement(i).value;

			}
			man_grid.editStop(true);
			man_grid.clearAll(true);
			man_grid_sb.setText('');
			oldGridView=gridView;
			man_grid_sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');

			var params_supp = "";
			loadingtime = new Date().getTime();

			man_grid.loadXML("index.php?ajax=1&act=man_manufacturer_get&tree_mode="+tree_mode+"&manufacturerfrom="+displayManufacturersFrom+"&idshop="+shopselection+"&view="+gridView+params_supp+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				man_grid._rowsNum=man_grid.getRowsNum();

				var limit_smartrendering = 0;
				if(man_grid.getUserData("", "LIMIT_SMARTRENDERING")!=undefined && man_grid.getUserData("", "LIMIT_SMARTRENDERING")!=0 && man_grid.getUserData("", "LIMIT_SMARTRENDERING")!=null)
					limit_smartrendering = man_grid.getUserData("", "LIMIT_SMARTRENDERING");

				if(limit_smartrendering!=0 && man_grid._rowsNum > limit_smartrendering)
					man_grid.enableSmartRendering(true);
				else
					man_grid.enableSmartRendering(false);

				idxID=man_grid.getColIndexById('id_manufacturer');
				idxName=man_grid.getColIndexById('meta_title');
				idxMetaDesc=man_grid.getColIndexById('meta_description');
				idxMetaKey=man_grid.getColIndexById('meta_keywords');
				idxContent=man_grid.getColIndexById('content');
				idxLinkRew=man_grid.getColIndexById('link_rewrite');
				idxPosition=man_grid.getColIndexById('position');
				if(idxName !== false)
				{
					man_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(a_id,idxName).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(b_id,idxName).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxName);
				}
				if(idxMetaDesc !== false)
				{
					man_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(a_id,idxMetaDesc).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(b_id,idxMetaDesc).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxMetaDesc);
				}
				if(idxLinkRew !== false)
				{
					man_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(a_id,idxLinkRew).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(man_grid.cells(b_id,idxLinkRew).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxLinkRew);
				}

				lastEditedCell=0;
				lastColumnRightClicked=0;
				for(var i=0;i<man_grid.getColumnsNum();i++)
				{
					if (man_grid.getFilterElement(i)!=null && oldFilters[man_grid.getColumnId(i)]!=undefined)
					{
						man_grid.getFilterElement(i).value=oldFilters[man_grid.getColumnId(i)];
					}
				}
				man_grid.filterByAll();

				// UISettings
				loadGridUISettings(man_grid);

				getGridStat();
				var loadingtimedisplay = ( new Date().getTime() - loadingtime ) / 1000;
				$('#layoutstatusloadingtime').html(" - T: "+loadingtimedisplay+"s");

				if (!man_grid.doesRowExist(last_manufacturerID))
				{
					last_manufacturerID=0;
				}else{
					man_grid.selectRowById(last_manufacturerID);
				}

				// UISettings
				man_grid._first_loading=0;

				<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
				man_grid.enableColumnMove(false);
				<?php } ?>

				if(open_man_grid == true)
				{
					if (callback==undefined || callback==null || callback=='')
						callback = ' ';
					callback = callback + 'last_manufacturerID=0;man_grid.selectRowById('+open_man_id_page+',false,true,true);';
					open_man_grid = false;
				}

				if (callback!='') eval(callback);
			});
		}
	}
	function getGridStat(){
		var filteredRows=man_grid.getRowsNum();
		var selectedRows=(man_grid.getSelectedRowId()?man_grid.getSelectedRowId().split(',').length:0);
		man_grid_sb.setText(man_grid._rowsNum+' '+(man_grid._rowsNum>1?'<?php echo _l('Manufacturer')?>':'<?php echo _l('Manufacturer')?>')+(tree_mode=='all'?' <?php echo _l('in this category and all subcategories')?>':' <?php echo _l('in this category')?>')+" - <?php echo _l('Filter')._l(':')?> "+filteredRows+" - <?php echo _l('Selection')._l(':')?> "+selectedRows);
	}

	function addManufacturerInQueue(rId, action, cIn, vars)
	{
		var params = {
			name: "man_manufacturer_update_queue",
			row: rId,
			action: action,
			params: {},
			callback: "callbackManufacturerUpdate('"+rId+"','"+action+"','"+rId+"',{data});"
		};

		// COLUMN VALUES
		if(cIn!=undefined && cIn!="" && cIn!=null && cIn!=0)
			params.params[man_grid.getColumnId(cIn)] = man_grid.cells(rId,cIn).getValue();
		params.params['id_lang'] = SC_ID_LANG;
		if(manufacturerselection!=undefined && manufacturerselection!=null && manufacturerselection!="" && manufacturerselection!=0)
			params.params['id_manufacturer_category'] = manufacturerselection;
		if(vars!=undefined && vars!=null && vars!="" && vars!=0)
		{
			$.each(vars, function(key, value){
				params.params[key] = value;
			});
		}

		// USER DATA
		if(rId!=undefined && rId!=null && rId!="" && rId!=0)
		{
			if (man_grid.UserData[rId] != undefined && man_grid.UserData[rId]!=null && man_grid.UserData[rId]!="" && man_grid.UserData[rId]!=0) {
				$.each(man_grid.UserData[rId].keys, function (i, key) {
					params.params[key] = man_grid.UserData[rId].values[i];
				});
			}
		}
		$.each(man_grid.UserData.gridglobaluserdata.keys, function(i, key){
			params.params[key] = man_grid.UserData.gridglobaluserdata.values[i];
		});

		params.params = JSON.stringify(params.params);
		addInUpdateQueue(params,man_grid);
	}

	function callbackManufacturerUpdate(sid,action,tid,xml)
	{
		if (action == 'insert') {
			idxManufacturerID = man_grid.getColIndexById('id_manufacturer');
			man_grid.cells(sid, idxManufacturerID).setValue(tid);
			man_grid.changeRowId(sid, tid);
			man_grid.setRowHidden(tid, false);
			man_grid.showRow(tid);
			man_manufacturerPanel.progressOff();
		} else if (action == 'update') {
			man_grid.setRowTextNormal(sid);
		} else if (action == 'delete') {
			man_grid.deleteRow(sid);
		} else if(action=='position')
		{
			idxPosition=man_grid.getColIndexById('position');
			displayManufacturers('man_grid.sortRows('+idxPosition+', "int", "asc");');
		}
	};

</script>
