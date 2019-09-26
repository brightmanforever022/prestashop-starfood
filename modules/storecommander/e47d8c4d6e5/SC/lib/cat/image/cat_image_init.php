
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
 if(_r("GRI_CAT_PROPERTIES_GRID_IMG")) { ?>
		prop_tb.addListOption('panel', 'images', 3, "button", '<?php echo _l('Images',1)?>', "lib/img/picture.png");
		allowed_properties_panel[allowed_properties_panel.length] = "images";
	<?php } ?>

	prop_tb.addButton("image_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('image_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButton("image_add", 100, "", "lib/img/picture_add.png", "lib/img/picture_add.png");
	prop_tb.setItemToolTip('image_add','<?php echo _l('Upload new images',1)?>');
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>='))
{
	$sql ="SELECT COUNT(*) AS cnt FROM "._DB_PREFIX_."lang";
	$res = Db::getInstance()->getValue($sql);
	if ($res > 1) {
?>
	var opts = [['lang_select', 'obj', '<?php echo _l('Assign product name to selected image(s) legend')?>', ''],
		['separator1', 'sep', '', ''],
		['image_fill_legend_current_lang', 'obj', '<?php echo _l('For the current language')?>', ''],
		['image_fill_legend_all_lang', 'obj', '<?php echo _l('For all languages')?>', '']
		];
	prop_tb.addButtonSelect("image_fill_legend", 100, "",opts, "lib/img/textfield_key.png", "lib/img/textfield_key.png",false,true);
	prop_tb.setItemToolTip("image_fill_legend","<?php echo _l('Assign product name to selected image(s) legend',1)?>");

<?php
	} else {
?>
	prop_tb.addButton("image_fill_legend", 100, "", "lib/img/textfield_key.png", "lib/img/textfield_key.png");
	prop_tb.setItemToolTip('image_fill_legend','<?php echo _l('Assign product name to selected image(s) legend',1)?>');
<?php
	}
}
?>
	prop_tb.addButton("image_setposition", 100, "", "lib/img/layers.png", "lib/img/layers_dis.png");
	prop_tb.setItemToolTip('image_setposition','<?php echo _l('Save image positions',1)?>');
	prop_tb.addButton('image_del',100,'','lib/img/delete.gif','lib/img/delete.gif');
	prop_tb.setItemToolTip('image_del','<?php echo _l('Delete selected images',1)?>');
	prop_tb.addButton("image_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	prop_tb.setItemToolTip('image_selectall','<?php echo _l('Select all images',1)?>');
	<?php if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
		prop_tb.addButton('image_fouleFactory', 100, "", "lib/img/foulefactory_icon.png", "lib/img/foulefactory_icon.png");
		prop_tb.setItemToolTip('image_fouleFactory','<?php echo _l('Enhance your product pages in 3 minutes with FouleFactory',1)?>');
	<?php } ?>

	prop_tb._imagesUploadWindow=new Array();

	
	var product_shop_default = 0;

	clipboardType_Images = null;	
	needInitImages = 1;
	function initImages(){
		if (needInitImages)
		{
			prop_tb._imagesLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._imagesLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._imagesGrid = prop_tb._imagesLayout.cells('a').attachGrid();
			prop_tb._imagesGrid.setImagePath("lib/js/imgs/");
			prop_tb._imagesGrid.enableDragAndDrop(true);
			prop_tb._imagesGrid.setDragBehavior('child');
			prop_tb._imagesGrid.enableMultiselect(true);
			
			// UISettings
			prop_tb._imagesGrid._uisettings_prefix='cat_image';
			prop_tb._imagesGrid._uisettings_name=prop_tb._imagesGrid._uisettings_prefix;
		   	prop_tb._imagesGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._imagesGrid);
			
			function onEditCellImage(stage,rId,cInd,nValue,oValue){
					idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
					idxCover=prop_tb._imagesGrid.getColIndexById('cover');
					<?php sc_ext::readCustomImageGridConfigXML('onEditCell'); ?>
					if (cInd == idxLegend){
						col='legend';
						if(stage==2)
						{
							<?php sc_ext::readCustomImageGridConfigXML('onBeforeUpdate'); ?>
							$.post("index.php?ajax=1&act=cat_image_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, col: col, val: nValue.replace(/#/g,''), 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(data){
								<?php sc_ext::readCustomImageGridConfigXML('onAfterUpdate'); ?>
							});
							idxLegend=prop_tb._imagesGrid.cells(rId,cInd).setValue(nValue.replace(/#/g,''));
						}
					}
<?php if (SCMS) { ?>
					if (cInd == idxCover){
						if (prop_tb._imagesGrid.cells(rId,prop_tb._imagesGrid.getColIndexById("shop_"+shopselection)).getValue()=='0')
							return false;
					}
<?php } ?>
					return true;
			}
			prop_tb._imagesGrid.attachEvent("onEditCell",onEditCellImage);
			function onCheckImage(rId,cInd,state)
			{
				var cId=prop_tb._imagesGrid.getColumnId(cInd);
				if (cId!='cover')
				{
					var shop = cId.replace("shop_", "");
					$.post("index.php?ajax=1&act=cat_image_update&action=shop&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, 'list_id_image':rId,shop:shop,val:Number(state), is_cover: prop_tb._imagesGrid.cells(rId,prop_tb._imagesGrid.getColIndexById("cover")).getValue()},function(data){});
				}else{
					$.post("index.php?ajax=1&act=cat_image_update&action=update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_product': lastProductSelID, 'list_id_image':rId,col:'cover',val:Number(state)},function(data){});
				}
			}
			prop_tb._imagesGrid.attachEvent("onCheck", onCheckImage);
			needInitImages=0;
			
			
			// Context menu for grid
			images_cmenu=new dhtmlXMenuObject();
			images_cmenu.renderAsContextMenu();
			function onGridImagesContextButtonClick(itemId){
				tabId=prop_tb._imagesGrid.contextID.split('_');
				tabId=tabId[0];
				if (itemId=="copy"){
					if (lastColumnRightClicked_Images!=0)
					{
						clipboardValue_Images=prop_tb._imagesGrid.cells(tabId,lastColumnRightClicked_Images).getValue();
						images_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+clipboardValue_Images);
						clipboardType_Images=lastColumnRightClicked_Images;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_Images!=0 && clipboardValue_Images!=null && clipboardType_Images==lastColumnRightClicked_Images)
					{
						selection=prop_tb._imagesGrid.getSelectedRowId();
						idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								prop_tb._imagesGrid.cells(selArray[i],lastColumnRightClicked_Images).setValue(clipboardValue_Images);
								if(lastColumnRightClicked_Images==idxLegend)
									onEditCellImage(2,selArray[i],lastColumnRightClicked_Images,clipboardValue_Images,null);
								else
									onCheckImage(selArray[i],lastColumnRightClicked_Images,clipboardValue_Images);
							}
						}
					}
				}
			}
			images_cmenu.attachEvent("onClick", onGridImagesContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			images_cmenu.loadStruct(contextMenuXML);
			prop_tb._imagesGrid.enableContextMenu(images_cmenu);

			prop_tb._imagesGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var disableOnCols=new Array(
						prop_tb._imagesGrid.getColIndexById('id_product'),
						prop_tb._imagesGrid.getColIndexById('id_image'),
						prop_tb._imagesGrid.getColIndexById('image'),
						prop_tb._imagesGrid.getColIndexById('reference'),
						prop_tb._imagesGrid.getColIndexById('name'),
						prop_tb._imagesGrid.getColIndexById('position'),
						prop_tb._imagesGrid.getColIndexById('cover')
						);
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_Images=colidx;
				images_cmenu.setItemText('object', '<?php echo _l('Image:')?> '+prop_tb._imagesGrid.cells(rowid,prop_tb._imagesGrid.getColIndexById('id_image')).getTitle());
				if (lastColumnRightClicked_Images==clipboardType_Images)
				{
					images_cmenu.setItemEnabled('paste');
				}else{
					images_cmenu.setItemDisabled('paste');
				}
				return true;
			});
		}
	}



	function setPropertiesPanel_images(id){
		if (id=='images')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('image_del');
			prop_tb.showItem('image_selectall');
			<?php if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
				prop_tb.showItem('image_fouleFactory');
			<?php } ?>
			prop_tb.showItem('image_setposition');
			prop_tb.showItem('image_add');
			prop_tb.showItem('image_refresh');
<?php
if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>='))
{
?>
			prop_tb.showItem('image_fill_legend');
<?php
}
?>
			prop_tb.setItemText('panel', '<?php echo _l('Images',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/picture.png');
			needInitImages=1;
			initImages();
			propertiesPanel='images';
			if (lastProductSelID!=0)
			{
				displayImages();
			}
		}
		if (id=='image_refresh'){
			displayImages();
		}
		if (id=='image_selectall'){
			prop_tb._imagesGrid.selectAll();
		}
		if (id=='image_fouleFactory')
		{
			showWCatFoulefactory();
		}
		if (id=='image_add'){
			var products_ids = cat_grid.getSelectedRowId();
			if (products_ids!=0)
			{
				if (!dhxWins.isWindow("wProductImages"+products_ids))
				{
                    <?php if(_s('APP_SKIN_INTERFACE')=="1") { ?>
                    prop_tb._imagesUploadWindow[products_ids] = dhxWins.createWindow("prop_tb._imagesUploadWindow[products_ids]", 50, 50, 600, 450);
                    <?php } else { ?>
					prop_tb._imagesUploadWindow[products_ids] = dhxWins.createWindow("prop_tb._imagesUploadWindow[products_ids]", 50, 50, 585, 400);
                    <?php } ?>
					prop_tb._imagesUploadWindow[products_ids].setIcon('lib/img/picture_add.png','../../../lib/img/picture_add.png');				
					if(products_ids.search(",")<0)
							prop_tb._imagesUploadWindow[products_ids].setText('<?php echo _l('Upload images',1)?>: '+cat_grid.cells(products_ids,idxProductName).getValue());
						else
							prop_tb._imagesUploadWindow[products_ids].setText('<?php echo _l('Upload images',1)?>');
					ll = new dhtmlXLayoutObject(prop_tb._imagesUploadWindow[products_ids], "1C");
					ll.cells('a').hideHeader();
					
					ll_toolbar=ll.cells('a').attachToolbar();
					ll_toolbar.addButtonTwoState("auto_upload", 0, "", "lib/img/picture_go.png", "lib/img/picture_go.png");
					ll_toolbar.setItemToolTip('auto_upload','<?php echo _l('If enabled: Images will be automatically uploaded once selected',1)?>');
					ll_toolbar.setItemState('auto_upload', ($.cookie('sc_cat_img_auto_upload')==1?1:0));
					
					ll_toolbar.attachEvent("onStateChange", function(id,state){
							if (id=='auto_upload'){
								var auto_upload = 0;
								if (state) {
								  auto_upload=1;
								}else{
								  auto_upload=0;
								}
								$.cookie('sc_cat_img_auto_upload',auto_upload, { expires: 60 });
							}
						});
	
					ll.cells('a').attachURL("index.php?ajax=1&act=cat_image_upload&product_list="+products_ids+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function(data){});
					prop_tb._imagesUploadWindow[products_ids].attachEvent("onClose", function(win){
							win.hide();
							displayImages();
							return false;
						});
				}else{
					prop_tb._imagesUploadWindow[products_ids].show();
				}
			}else{
				alert('<?php echo _l('Please select a product',1)?>');
			}
		}
		if (id=='image_setposition'){
			if (prop_tb._imagesGrid.getRowsNum()>0 && lastProductSelID!=0)
			{
				var positions='';
				var idx=0;
				var i = 1 ;
				prop_tb._imagesGrid.forEachRow(function(id){
						positions+=id+','+prop_tb._imagesGrid.getRowIndex(id)+';';
						idx++;
					});
				$.post("index.php?ajax=1&act=cat_image_update&action=position&"+new Date().getTime(),{ id_product: lastProductSelID, positions: positions },function(){
						idxPosition=prop_tb._imagesGrid.getColIndexById('position');
						displayImages('prop_tb._imagesGrid.sortRows('+idxPosition+', "int", "asc");');
					});
			}
		}
		if (id=='image_del')
		{
			if (prop_tb._imagesGrid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an image',1)?>');
			}else{
				if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					{
						$.post("index.php?ajax=1&act=cat_image_update&action=delete&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{id_product: lastProductSelID, 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(){
								displayImages();
							});
					}
				}else{
					alert('<?php echo _l('Please select a product',1)?>');
				}
			}
		}
		if (id=='image_fill_legend_current_lang' || id=='image_fill_legend')
		{ 
			if (prop_tb._imagesGrid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an image',1)?>');
			}else{
				if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to change the legend of the selected image(s) with the product name for the current language?',1)?>'))
					{
						list=prop_tb._imagesGrid.getSelectedRowId().split(',');
						idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
						idxProductName=cat_grid.getColIndexById('name');
						$.post("index.php?ajax=1&act=cat_image_update&action=image_fill_legend_current_lang&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_products': cat_grid.getSelectedRowId(), 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(){

						});
						for(i=0 ; i < list.length ; i++)
						{
							prop_tb._imagesGrid.cells(list[i],idxLegend).setValue(cat_grid.cells(lastProductSelID,idxProductName).getValue().replace(/#/g,''));
						}
					}
				}else{
					alert('<?php echo _l('Please select a product',1)?>');
				}
			}
		}
		if (id=='image_fill_legend_all_lang')
			{
				if (prop_tb._imagesGrid.getSelectedRowId()==null)
				{
					alert('<?php echo _l('Please select an image',1)?>');
				}else{
					if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to change the legend of the selected image(s) with the product name for all languages?',1)?>'))
				{
					list=prop_tb._imagesGrid.getSelectedRowId().split(',');
					idxLegend=prop_tb._imagesGrid.getColIndexById('legend');
					idxProductName=cat_grid.getColIndexById('name');
					$.post("index.php?ajax=1&act=cat_image_update&action=image_fill_legend_all_lang&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),{'id_products': cat_grid.getSelectedRowId(), 'list_id_image':prop_tb._imagesGrid.getSelectedRowId()},function(){

					});
					for(i=0 ; i < list.length ; i++)
						{
						prop_tb._imagesGrid.cells(list[i],idxLegend).setValue(cat_grid.cells(lastProductSelID,idxProductName).getValue().replace(/#/g,''));
					}
				}
			}else{
					alert('<?php echo _l('Please select a product',1)?>');
			}
		}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_images);



function displayImages(callback)
{
	prop_tb._imagesGrid.clearAll(true);
	prop_tb._imagesGrid.loadXML("index.php?ajax=1&act=cat_image_get&id_product="+cat_grid.getSelectedRowId()+"&id_lang="+SC_ID_LANG,function()
			{
				nb=prop_tb._imagesGrid.getRowsNum();
				prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('images')?>":" <?php echo _l('image')?>"));

	   			// UISettings
				loadGridUISettings(prop_tb._imagesGrid);
				prop_tb._imagesGrid._first_loading=0;

				<?php sc_ext::readCustomImageGridConfigXML('afterGetRows'); ?>

		    	if (callback!='') eval(callback);
			});
}


	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='images'){

				initImages();
				displayImages();
			}
		});
