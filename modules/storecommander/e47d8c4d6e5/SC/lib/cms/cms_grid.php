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
	cms_grid=cms_pagePanel.attachGrid();
	cms_grid._name='grid';

	var open_cms_grid = "auto";
	var display_cms_after_cat_select = true;
	var display_cms_after_select_view = true;
	var open_cms_id_cat = 0;
	var open_cms_id_page = 0;
	var open_cms_id_attr = 0;
	loadingtime = 0;


	// UISettings
	cms_grid._uisettings_prefix='cms_grid_';
	cms_grid._uisettings_name=cms_grid._uisettings_prefix;
	cms_grid._first_loading=1;

	cms_grid.enableDragAndDrop(true);
	cms_grid.setDragBehavior('child');
	cms_grid.enableSmartRendering(true);
	<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
	cms_grid.enableColumnMove(false);
	<?php } ?>

	cms_grid._key_events.k9_0_0=function(){
		cms_grid.editStop();
		cms_grid.selectCell(cms_grid.getRowIndex(cms_grid.getSelectedRowId())+1,cms_grid.getSelectedCellIndex(),true,false,true,true);
	};

	cms_grid_tb=cms_pagePanel.attachToolbar();
	cms_grid_tb.addButton("help", 0, "", "lib/img/help.png", "lib/img/help.png");
	cms_grid_tb.setItemToolTip('help','<?php echo _l('Help')?>');
	cms_grid_tb.addButton("setposition", 0, "", "lib/img/layers.png", "lib/img/layers_dis.png");
	cms_grid_tb.setItemToolTip('setposition','<?php echo _l('Save CMS positions in the grid as category positions')?>');
	cms_grid_tb.addButton("delete", 0, "", "lib/img/delete.gif", "lib/img/delete.gif");
	cms_grid_tb.setItemToolTip('delete','<?php echo _l('This will permanently delete the selected CMS everywhere in the shop.')?>');
	cms_grid_tb.addButton("add", 0, "", "lib/img/add.png", "lib/img/add.png");
	cms_grid_tb.setItemToolTip('add','<?php echo _l('Create new CMS page')?>');
	cms_grid_tb.addButton("refresh", 0, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	cms_grid_tb.setItemToolTip('refresh','<?php echo _l('Refresh grid')?>');
	cms_grid_tb.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	cms_grid_tb.setItemToolTip('selectall','<?php echo _l('Select all CMS')?>');

	<?php
	$tmp=array();
	$clang=_l('Language');
	$optlang='';
	foreach($languages AS $lang){
		if ($lang['id_lang']==$sc_agent->id_lang)
		{
			$clang=$lang['iso_code'];
			$optlang='cms_lang_'.$lang['iso_code'];
		}
		$tmp[]="['cms_lang_".$lang['iso_code']."', 'obj', '".$lang['name']."', '']";
	}
	if (count($tmp) > 1)
	{
	echo 'var opts = ['.join(',',$tmp).'];';
	?>
	cms_grid_tb.addButtonSelect('lang',0,'<?php echo $clang?>',opts,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);
	cms_grid_tb.setItemToolTip('lang','<?php echo _l('Select CMS language')?>');
	cms_grid_tb.setListOptionSelected('lang', '<?php echo $optlang ?>');
	<?php
	}
	?>
	var gridnames=new Object();
	<?php if(_r("GRI_CMS_VIEW_GRID_LIGHT")) { ?>gridnames['grid_light']='<?php echo _l('Light view',1)?>';<?php } ?>
	<?php if(_r("GRI_CMS_VIEW_GRID_LARGE")) { ?>gridnames['grid_large']='<?php echo _l('Large view',1)?>';<?php } ?>
	<?php if(_r("GRI_CMS_VIEW_GRID_SEO")) { ?>gridnames['grid_seo']='<?php echo _l('SEO',1)?>';<?php } ?>
	<?php if(_r("GRI_CMS_VIEW_GRID_DESCRIPTION")) { ?>gridnames['grid_description']='<?php echo _l('Descriptions',1)?>';<?php } ?>

	var opts = new Array();
	$.each(gridnames, function(index, value) {
		opts[opts.length] = new Array(index, 'obj', value, '');
	});
	if (opts.length > 25)
		$('div.dhx_toolbar_poly_dhx_skyblue').addClass('dhx_toolbar_poly_dhx_skyblue_SCROLLBAR');

	gridView = (in_array('<?php echo _s('CMS_PAGE_GRID_DEFAULT')?>',Object.keys(gridnames))?'<?php echo _s('CMS_PAGE_GRID_DEFAULT')?>':opts[0][0]);
	// UISettings
	cms_grid._uisettings_name=cms_grid._uisettings_prefix+gridView;

	cms_grid_tb.addButtonSelect("gridview", 0, "<?php echo _l('Light view')?>", opts, "lib/img/table_gear.png", "lib/img/table_gear.png",false,true);
	cms_grid_tb.setItemToolTip('gridview','<?php echo _l('Grid view settings')?>');
	if (isIPAD){
		var opts = [['cols123', 'obj', '<?php echo _l('Columns')?> 1 + 2 + 3', ''],
			['cols12', 'obj', '<?php echo _l('Columns')?> 1 + 2', ''],
			['cols23', 'obj', '<?php echo _l('Columns')?> 2 + 3', '']
		];
		cms_grid_tb.addButtonSelect("layout", 0, "", opts, "lib/img/layout.png", "lib/img/layout.png",false,true);
	}

	function gridToolBarOnClick(id){
		if (id.substr(0,5)=='grid_'){

			oldGridView=gridView;
			gridView=id;

			// UISettings
			cms_grid._uisettings_name=cms_grid._uisettings_prefix+gridView;

			cms_grid_tb.setItemText('gridview',gridnames[id]);
			$(document).ready(function(){displayCms();});
		}
		if (id=='help'){
			<?php echo "window.open('".getHelpLink('cms_toolbar_prod')."');"; ?>
		}
		if (id=='filters_reset')
		{
			for(var i=0,l=cms_grid.getColumnsNum();i<l;i++)
			{
				if (cms_grid.getFilterElement(i)!=null) cms_grid.getFilterElement(i).value="";
			}
			cms_grid.filterByAll();
			cms_grid_tb.setListOptionSelected('filters','');
		}
		if (id=='filters_cols_show')
		{
			for(i=0,l=cms_grid.getColumnsNum() ; i < l ; i++)
			{
				cms_grid.setColumnHidden(i,false);
			}
			cms_grid_tb.setListOptionSelected('filters','');
		}
		if (id=='filters_cols_hide')
		{
			idxCmsID=cms_grid.getColIndexById('id');
			idxName=cms_grid.getColIndexById('meta_title');
			cms_grid_tb.setListOptionSelected('filters','');
		}
		flagLang=false; // changelang ; lang modified?
		<?php
		$tmp=array();
		$clang=_l('Language');
		foreach($languages AS $lang){
			echo'
			if (id==\'cms_lang_'.$lang['iso_code'].'\')
			{
				SC_ID_LANG='.$lang['id_lang'].';
				cms_grid_tb.setItemText(\'lang\',\''.$lang['iso_code'].'\');
				flagLang=true;
			}';
		}
		?>
		if (flagLang){

			if(cms.cells('a').isCollapsed())
			{
				displayCms();
			}
			else
			{
				if(SCMS)
					displayTree('');
				else
					displayTree('displayCms()');
			}
		}
		if (id=='refresh'){
			displayCms();
		}
		if (id=='add'){
			if (cmsselection==0){
				alert('<?php echo _l('You need to select a CMS category.',1)?>');
			}else{
				var newId = new Date().getTime();
				newRow=new Array('','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','','');
				newRow=newRow.slice(0,cms_grid.getColumnsNum()-1);
				idxID=cms_grid.getColIndexById('id_cms');
				idxName=cms_grid.getColIndexById('meta_title');
				idxActive=cms_grid.getColIndexById('active');
				idxIndexation=cms_grid.getColIndexById('indexation');
				idxLinkRewrite=cms_grid.getColIndexById('link_rewrite');
				newRow[idxID]=newId;
				newRow[idxName]='new';
				if (idxActive) newRow[idxActive]='<?php echo _s('CMS_PAGE_CREA_ACTIVE');?>';
				if (idxIndexation) newRow[idxIndexation]='<?php echo _s('CMS_PAGE_CREA_INDEX');?>';
				if (idxLinkRewrite) newRow[idxLinkRewrite]='new-cms';
				// INSERT
				cms_grid.addRow(newId,newRow);
				cms_grid.setRowHidden(newId, true);

				var params = {
					name: "cms_page_update_queue",
					row: newId,
					action: "insert",
					params: {callback: "callbackCmsUpdate('"+newId+"','insert','{newid}',{data});"}
				};

				// COLUMN VALUES
				cms_grid.forEachCell(newId,function(cellObj,ind){
					params.params[cms_grid.getColumnId(ind)] = cms_grid.cells(newId,ind).getValue();
				});
				params.params['id_lang'] = SC_ID_LANG;
				if(cmsselection!=undefined && cmsselection!=null && cmsselection!="" && cmsselection!=0)
					params.params['id_cms_category'] = cmsselection;
				// USER DATA
				$.each(cms_grid.UserData.gridglobaluserdata.keys, function(i, key){
					params.params[key] = cms_grid.UserData.gridglobaluserdata.values[i];
				});

				sendInsert(params,cms_pagePanel);
			}
		}
		if (id=="delete")
		{
			if (confirm('<?php echo _l('Permanently delete the selected CMS everywhere in the shop.',1)?>'))
			{
				selection=cms_grid.getSelectedRowId();
				ids=selection.split(',');
				$.each(ids, function(num, rId)
				{
					var params =
					{
						name: "cms_page_update_queue",
						row: rId,
						action: "delete",
						params: {},
						callback: "callbackCmsUpdate('"+rId+"','delete','"+rId+"');"
					};
					params.params = JSON.stringify(params.params);
					cms_grid.setRowTextStyle(rId, "text-decoration: line-through;");
					addInUpdateQueue(params,cms_grid);
				});
			}
		}
		if (id=='selectall'){
			cms_grid.enableSmartRendering(false);
			cms_grid.selectAll();
			getGridStat();
		}

		if (id=='setposition'){
			<?php if(SCMS) { ?>
			if(shopselection==0){
				dhtmlx.message({text:'<?php echo _l('Cms positions cannot be set when \'All shops\' is selected',1);?>',type:'error'});
				return false;
			}
			<?php } ?>
			idxPosition=cms_grid.getColIndexById('position');
			if (idxPosition && cms_grid.getRowsNum()>0 && cmsselection!=0)
			{
				var positions='';
				var idx=0;
				cms_grid.forEachRow(function(id){
					positions+=id+','+cms_grid.getRowIndex(id)+';';
					idx++;
				});
				addCmsInQueue(0, "position", null, {'positions':positions});
			}
		}
		if (id=='cols123')
		{
			cms.cells("a").expand();
			cms.cells("a").setWidth(300);
			cms.cells("b").expand();
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth(500);
		}
		if (id=='cols12')
		{
			cms.cells("a").expand();
			cms.cells("a").setWidth($(document).width()/3);
			cms.cells("b").expand();
			dhxLayout.cells('b').collapse();
		}
		if (id=='cols23')
		{
			cms.cells("a").collapse();
			cms.cells("b").expand();
			cms.cells("b").setWidth($(document).width()/2);
			dhxLayout.cells('b').expand();
			dhxLayout.cells('b').setWidth($(document).width()/2);
		}
	}
	cms_grid_tb.attachEvent("onClick",gridToolBarOnClick);
	cms_grid_tb.attachEvent("onStateChange", function(id,state){
		if (id=='copytocateg'){
			if (state) {
				copytocateg=true;
			}else{
				copytocateg=false;
			}
		}
	});

	cms_grid.setImagePath('lib/js/imgs/');
	cms_grid.enableMultiselect(true);
	cms_grid_sb=cms_pagePanel.attachStatusBar();

	gridToolBarOnClick(gridView);

	cms_grid.attachEvent("onBeforeDrag",function(idsource){
		if (cms_grid.getSelectedRowId()==null) draggedCms=idsource;
		if (cms_tree._dragBehavior!="child")
		{
			cms_tree.setDragBehavior("child");
			cms_tree._dragBehavior="child";
		}
		return true;
	});

	cms_grid.attachEvent("onDragIn",function(idsource,idtarget,sourceobject,targetobject){
		<?php if(SCMS) { ?> if(shopselection==0) return false; <?php } ?>
		if (sourceobject._name=="grid") return true;
		return false;
	});

	cms_grid.rowToDragElement=function(id){
		var text="";
		idxName=cms_grid.getColIndexById('meta_title');
		if (cms_grid.getSelectedRowId()!=null)
		{
			var dragged=cms_grid.getSelectedRowId().split(',');
			if (dragged.length > 1){ // multi
				for (var i=0; i < dragged.length; i++)
				{
					text += cms_grid.cells(dragged[i],idxName).getValue() + "<br/>";
				}
			}else{ // single
				text += cms_grid.cells(dragged,idxName).getValue() + "<br/>";
			}
		}else{ // single
			text += cms_grid.cells(draggedCms,idxName).getValue() + "<br/>";
		}
		return text;
	}

	// multiedition context menu
	cms_grid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
		lastColumnRightClicked=colidx;

		cms_cmenu.setItemText('object', '<?php echo _l('Page :')?> '+cms_grid.cells(rowid,cms_grid.getColIndexById('meta_title')).getValue());
		// paste function
		if (lastColumnRightClicked==clipboardType)
		{
			cms_cmenu.setItemEnabled('paste');
		}else{
			cms_cmenu.setItemDisabled('paste');
		}
		var colType=cms_grid.getColType(colidx);
		if (colType=='ro')
		{
			cms_cmenu.setItemDisabled('copy');
			cms_cmenu.setItemDisabled('paste');
		}else{
			cms_cmenu.setItemEnabled('copy');
		}

		<?php if(SCMS) { ?>
		if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
		{
			cms_cmenu.setItemEnabled('goshop');
		}else{
			cms_cmenu.setItemDisabled('goshop');
		}
		<?php } ?>
		return true;
	});

	function onEditCell(stage,rId,cInd,nValue,oValue){
		var coltype=cms_grid.getColType(cInd);
		if (stage==1 && this.editor && this.editor.obj && coltype!='txt' && coltype!='txttxt') this.editor.obj.select();
		lastEditedCell=cInd;
		if (nValue!=oValue){
			cms_grid.setRowColor(rId,'BlanchedAlmond');
			idxActive=cms_grid.getColIndexById('active');
			idxName=cms_grid.getColIndexById('meta_title');
			idxMetaDescription=cms_grid.getColIndexById('meta_description');
			idxMetaKeywords=cms_grid.getColIndexById('meta_keywords');
			idxLinkRewrite=cms_grid.getColIndexById('link_rewrite');
			if (cInd == idxName){
				cms_grid.cells(rId,idxName).setValue(cms_grid.cells(rId,idxName).getValue());
				<?php if (_s('CMS_SEO_META_TITLE_TO_URL')){ ?>
					<?php
					$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
					if($accented==1) {	?>
					cms_grid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(cms_grid.cells(rId,idxName).getValue().substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));
					<?php } else { ?>
					cms_grid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(cms_grid.cells(rId,idxName).getValue().substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));
					<?php } ?>
				<?php } ?>

			}
			if (cInd == idxMetaDescription){
				cms_grid.cells(rId,idxMetaDescription).setValue(cms_grid.cells(rId,idxMetaDescription).getValue().substr(0,<?php echo _s('CMS_META_DESC_SIZE')?>));
			}
			if (cInd == idxMetaKeywords){
				cms_grid.cells(rId,idxMetaKeywords).setValue(cms_grid.cells(rId,idxMetaKeywords).getValue().substr(0,<?php echo _s('CMS_META_KEYWORDS_SIZE')?>));
			}
			if (cInd == idxLinkRewrite){
				<?php
				$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
				if($accented==1) {	?>
				cms_grid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(cms_grid.cells(rId,idxLinkRewrite).getValue().substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));
				<?php } else { ?>
				cms_grid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(cms_grid.cells(rId,idxLinkRewrite).getValue().substr(0,<?php echo _s('CMS_LINK_REWRITE_SIZE')?>)));
				<?php } ?>
			}
			if (cInd == idxActive){ //Active update
				if (nValue==0){
					cms_grid.cells(rId,idxName).setBgColor('#D7D7D7');
				}else{
					cms_grid.cells(rId,idxName).setBgColor(cms_grid.cells(rId,0).getBgColor());
				}
			}
		}

		if (nValue!=oValue){
			var id = rId;
			console.log(cInd);
			addCmsInQueue(rId, "update", cInd);
			return true;
		}
	}
	cms_grid.attachEvent("onEditCell",onEditCell);

	// Context menu for Grid
	cms_cmenu=new dhtmlXMenuObject();
	cms_cmenu.renderAsContextMenu();
	function onGridCmsContextButtonClick(itemId){
		tabId=cms_grid.contextID.split('_');
		tabId=tabId[0];
		if (itemId=="gopsbo"){
			wModifyCms = dhxWins.createWindow("wModifyCms", 50, 50, 1260, $(window).height()-75);
			wModifyCms.setText('<?php echo _l('Modify the CMS and close this window to refresh the grid',1)?>');
			<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
			wModifyCms.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminCmsContent&updatecms&id_cms="+tabId+"&token=<?php echo $sc_agent->getPSToken('AdminCmsContent');?>");
		<?php } else { ?>
			wModifyCms.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=<?php echo (version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'admincms':'AdminCMSContent');?>&updatecms&id_cms="+tabId+"&id_lang="+SC_ID_LANG+"&adminlang=1&token=<?php echo $sc_agent->getPSToken('AdminCMSContent');?>");
		<?php } ?>
			wModifyCms.attachEvent("onClose", function(win){
				displayCms();
				return true;
			});
		}
		if (itemId=="goshop"){
			var sel=cms_grid.getSelectedRowId();
			if (sel)
			{
				var tabId=sel.split(',');
				var k=1;
				for (var i=0;i<tabId.length;i++)
				{
					if (k > <?php echo _s('CMS_PAGE_OPEN_URL')?>) break;
					idxActive=cms_grid.getColIndexById('active');
					<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) { ?>
						if (idxActive) {
							if (cms_grid.cells(tabId[i], idxActive).getValue() == 0) {
								<?php $preview_url = '&adtoken='.$sc_agent->getPSToken('AdminCmsContent').'&id_employee='.$sc_agent->id_employee; ?>
								var previewUrl = "<?php echo $preview_url; ?>";
							} else {
								var previewUrl = 0;
							}
						}
					<?php } else { ?>
						if (idxActive)
							if (cms_grid.cells(tabId[i],idxActive).getValue()==0)
								continue;
					<?php } ?>
					<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
						if(SCMS) {
							?>
							if (previewUrl != 0) {
								if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
									window.open(shopUrls[shopselection]+'index.php?id_cms='+tabId[i]+'&controller=cms&id_lang='+SC_ID_LANG+previewUrl);
							} else {
								if (shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
									window.open(shopUrls[shopselection] + 'index.php?id_cms=' + tabId[i] + '&controller=cms&id_lang=' + SC_ID_LANG);
							}
						<?php
						}
						else {
						?>
							if (previewUrl != 0) {
								window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms=' + tabId[i] + '&controller=cms&id_lang=' + SC_ID_LANG+previewUrl);
							} else {
								window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms=' + tabId[i] + '&controller=cms&id_lang=' + SC_ID_LANG);
							}
						<?php
						}
					}else{
						?>
						window.open('<?php echo SC_PS_PATH_REL;?>cms.php?id_cms=' + tabId[i]);
					<?php } ?>
					k++;
				}
			}else{
				var tabId=cms_grid.contextID.split('_');
				<?php if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
					if(SCMS) {
					?>
					if(shopUrls[shopselection] != undefined && shopUrls[shopselection] != "" && shopUrls[shopselection] != null)
						window.open(shopUrls[shopselection]+'index.php?id_cms='+tabId[0]+'&controller=cms');
					<?php
					}
					else {
					?>
					window.open('<?php echo SC_PS_PATH_REL;?>index.php?id_cms='+tabId[0]+'&controller=cms');
					<?php
					}
				}else{
				?>
					window.open('<?php echo SC_PS_PATH_REL;?>cms.php?id_cms='+tabId[0]);
				<?php
				}
				?>
			}
		}
		if (itemId=="copy"){
			if (lastColumnRightClicked!=0)
			{
				clipboardValue=cms_grid.cells(tabId,lastColumnRightClicked).getValue();
				cms_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+cms_grid.cells(tabId,lastColumnRightClicked).getTitle().substr(0,30)+'...');
				clipboardType=lastColumnRightClicked;
			}
		}
		if (itemId=="paste"){
			if (lastColumnRightClicked!=0 && clipboardValue!=null && clipboardType==lastColumnRightClicked)
			{
				selection=cms_grid.getSelectedRowId();
				if (selection!='' && selection!=null)
				{
					selArray=selection.split(',');
					for(i=0 ; i < selArray.length ; i++)
					{
						cms_grid.cells(selArray[i],lastColumnRightClicked).setValue(clipboardValue);
						cms_grid.cells(selArray[i],lastColumnRightClicked).cell.wasChanged=true;
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
	cms_cmenu.attachEvent("onClick", onGridCmsContextButtonClick);
	var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
		'<item text="Object" id="object" enabled="false"/>'+
		'<item text="<?php echo _l('See on shop')?>" id="goshop"/>'+
		'<item text="<?php echo _l('Edit in PrestaShop BackOffice')?>" id="gopsbo"/>'+
		'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
		'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
		'</menu>';
	cms_cmenu.loadStruct(contextMenuXML);
	cms_grid.enableContextMenu(cms_cmenu);

	//#####################################
	//############ Events
	//#####################################

	// Click on a cms
	function doOnRowSelected(idcms){
		if (!dhxLayout.cells('b').isCollapsed() && lastcms_pageID!=idcms)
		{
			lastcms_pageID=idcms;
			idxName=cms_grid.getColIndexById('meta_title');

			if (propertiesPanel!='descriptions'){
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cms_grid.cells(lastcms_pageID,idxName).getValue());
			}
			<?php
			echo eval('?>'.$pluginCmsProperties['doOnCmsRowSelected'].'<?php ');
			?>
		}
	}

	cms_grid.attachEvent("onRowSelect",doOnRowSelected);

	// UISettings
	initGridUISettings(cms_grid);

	cms_grid.attachEvent("onColumnHidden",function(indx,state){
		idxImg=cms_grid.getColIndexById('image');
		if (idxImg && !state){
			cms_grid.setAwaitedRowHeight(<?php echo getGridImageHeight(); ?>);
		}else{
			cms_grid.setAwaitedRowHeight(30);
		}
	});
	cms_grid.attachEvent("onFilterEnd", function(elements){
		getGridStat();
	});
	cms_grid.attachEvent("onSelectStateChanged", function(id){
		getGridStat();
	});

	function displayCms(callback)
	{
		if (cmsselection!=undefined && cmsselection!=null && cmsselection!="" && cmsselection!=0)
		{
			oldFilters=new Array();
			for(var i=0,l=cms_grid.getColumnsNum();i<l;i++)
			{
				if (cms_grid.getFilterElement(i)!=null && cms_grid.getFilterElement(i).value!='')
					oldFilters[cms_grid.getColumnId(i)]=cms_grid.getFilterElement(i).value;

			}
			cms_grid.editStop(true);
			cms_grid.clearAll(true);
			cms_grid_sb.setText('');
			oldGridView=gridView;
			cms_grid_sb.setText('<?php echo _l('Loading in progress, please wait...',1)?>');

			var params_supp = "";
			loadingtime = new Date().getTime();

			cms_grid.loadXML("index.php?ajax=1&act=cms_page_get&tree_mode="+tree_mode+"&cmsfrom="+displayCmsFrom+"&idc="+cmsselection+"&view="+gridView+params_supp+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(){
				cms_grid._rowsNum=cms_grid.getRowsNum();

				var limit_smartrendering = 0;
				if(cms_grid.getUserData("", "LIMIT_SMARTRENDERING")!=undefined && cms_grid.getUserData("", "LIMIT_SMARTRENDERING")!=0 && cms_grid.getUserData("", "LIMIT_SMARTRENDERING")!=null)
					limit_smartrendering = cms_grid.getUserData("", "LIMIT_SMARTRENDERING");

				if(limit_smartrendering!=0 && cms_grid._rowsNum > limit_smartrendering)
					cms_grid.enableSmartRendering(true);
				else
					cms_grid.enableSmartRendering(false);

				idxID=cms_grid.getColIndexById('id_cms');
				idxName=cms_grid.getColIndexById('meta_title');
				idxMetaDesc=cms_grid.getColIndexById('meta_description');
				idxMetaKey=cms_grid.getColIndexById('meta_keywords');
				idxContent=cms_grid.getColIndexById('content');
				idxLinkRew=cms_grid.getColIndexById('link_rewrite');
				idxPosition=cms_grid.getColIndexById('position');
				if(idxName !== false)
				{
					cms_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(a_id,idxName).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(b_id,idxName).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxName);
				}
				if(idxMetaDesc !== false)
				{
					cms_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(a_id,idxMetaDesc).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(b_id,idxMetaDesc).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxMetaDesc);
				}
				if(idxLinkRew !== false)
				{
					cms_grid.setCustomSorting(function(a,b,ord,a_id,b_id){
						a = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(a_id,idxLinkRew).getTitle()).toLowerCase()));
						b = sanitizeString(replaceAccentCharacters(latinise(cms_grid.cells(b_id,idxLinkRew).getTitle()).toLowerCase()));
						return ord=="asc"?(a>b?1:-1):(a>b?-1:1);
					}, idxLinkRew);
				}

				if (idxPosition && displayCmsFrom=='all' && tree_mode=='single')
				{
					cms_grid_tb.enableItem('setposition');
				}else{
					cms_grid_tb.disableItem('setposition');
				}

				lastEditedCell=0;
				lastColumnRightClicked=0;
				for(var i=0;i<cms_grid.getColumnsNum();i++)
				{
					if (cms_grid.getFilterElement(i)!=null && oldFilters[cms_grid.getColumnId(i)]!=undefined)
					{
						cms_grid.getFilterElement(i).value=oldFilters[cms_grid.getColumnId(i)];
					}
				}
				cms_grid.filterByAll();

				// UISettings
				loadGridUISettings(cms_grid);

				getGridStat();
				var loadingtimedisplay = ( new Date().getTime() - loadingtime ) / 1000;
				$('#layoutstatusloadingtime').html(" - T: "+loadingtimedisplay+"s");

				if (!cms_grid.doesRowExist(lastcms_pageID))
				{
					lastcms_pageID=0;
				}else{
					cms_grid.selectRowById(lastcms_pageID);
				}

				// UISettings
				cms_grid._first_loading=0;

				<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
				cms_grid.enableColumnMove(false);
				<?php } ?>

				if(open_cms_grid == true)
				{
					if (callback==undefined || callback==null || callback=='')
						callback = ' ';
					callback = callback + 'lastcms_pageID=0;cms_grid.selectRowById('+open_cms_id_page+',false,true,true);';
					open_cms_grid = false;
				}

				if (callback!='') eval(callback);
			});
		}
	}
	function getGridStat(){
		var filteredRows=cms_grid.getRowsNum();
		var selectedRows=(cms_grid.getSelectedRowId()?cms_grid.getSelectedRowId().split(',').length:0);
		cms_grid_sb.setText(cms_grid._rowsNum+' '+(cms_grid._rowsNum>1?'<?php echo _l('CMS pages')?>':'<?php echo _l('CMS page')?>')+(tree_mode=='all'?' <?php echo _l('in this category and all subcategories')?>':' <?php echo _l('in this category')?>')+" - <?php echo _l('Filter')._l(':')?> "+filteredRows+" - <?php echo _l('Selection')._l(':')?> "+selectedRows);
	}

	function addCmsInQueue(rId, action, cIn, vars)
	{
		var params = {
			name: "cms_page_update_queue",
			row: rId,
			action: action,
			params: {},
			callback: "callbackCmsUpdate('"+rId+"','"+action+"','"+rId+"',{data});"
		};

		// COLUMN VALUES
		if(cIn!=undefined && cIn!="" && cIn!=null && cIn!=0)
			params.params[cms_grid.getColumnId(cIn)] = cms_grid.cells(rId,cIn).getValue();
		params.params['id_lang'] = SC_ID_LANG;
		if(cmsselection!=undefined && cmsselection!=null && cmsselection!="" && cmsselection!=0)
			params.params['id_cms_category'] = cmsselection;
		if(vars!=undefined && vars!=null && vars!="" && vars!=0)
		{
			$.each(vars, function(key, value){
				params.params[key] = value;
			});
		}

		// USER DATA
		if(rId!=undefined && rId!=null && rId!="" && rId!=0)
		{
			if (cms_grid.UserData[rId] != undefined && cms_grid.UserData[rId]!=null && cms_grid.UserData[rId]!="" && cms_grid.UserData[rId]!=0) {
				$.each(cms_grid.UserData[rId].keys, function (i, key) {
					params.params[key] = cms_grid.UserData[rId].values[i];
				});
			}
		}
		$.each(cms_grid.UserData.gridglobaluserdata.keys, function(i, key){
			params.params[key] = cms_grid.UserData.gridglobaluserdata.values[i];
		});

		params.params = JSON.stringify(params.params);
		addInUpdateQueue(params,cms_grid);
	}

	function callbackCmsUpdate(sid,action,tid,xml)
	{
		if (action == 'insert') {
			idxCmsID = cms_grid.getColIndexById('id_cms');
			cms_grid.cells(sid, idxCmsID).setValue(tid);
			cms_grid.changeRowId(sid, tid);
			cms_grid.setRowHidden(tid, false);
			cms_grid.showRow(tid);
			cms_pagePanel.progressOff();
		} else if (action == 'update') {
			cms_grid.setRowTextNormal(sid);
		} else if (action == 'delete') {
			cms_grid.deleteRow(sid);
		} else if(action=='position')
		{
			idxPosition=cms_grid.getColIndexById('position');
			displayCms('cms_grid.sortRows('+idxPosition+', "int", "asc");');
		}
	};

</script>
