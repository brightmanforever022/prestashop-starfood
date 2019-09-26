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
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<') || Combination::isFeatureActive())
	{
?>

	var lastCombiSelID = null;
	var lastProductSelIDForCombi = null;
	var AttrIdToOpen = null;

	<?php if(_r("GRI_CAT_PROPERTIES_GRID_COMBI")) { ?>
		prop_tb.addListOption('panel', 'combinations', 1, "button", '<?php echo _l('Combinations',1)?>', "lib/img/combinations.gif");
		allowed_properties_panel[allowed_properties_panel.length] = "combinations";
	<?php } ?>

	var opts = [['combi_filters_reset', 'obj', '<?php echo _l('Reset filters',1)?>', ''],
							['separator1', 'sep', '', ''],
							['combi_filters_cols_show', 'obj', '<?php echo _l('Show all columns',1)?>', ''],
							['combi_filters_cols_hide', 'obj', '<?php echo _l('Hide all columns',1)?>', '']
							];
	prop_tb.addButtonSelect("combi_filters", 100, "", opts, "lib/img/filter.png", "lib/img/filter.png",false,true);
	prop_tb.setItemToolTip('combi_filters','<?php echo _l('Filter options',1)?>');
	prop_tb.addButton("combi_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('combi_refresh','<?php echo _l('Refresh grid',1)?>');
	if (isIPAD){
		prop_tb.addButtonTwoState('combi_lightNavigation', 100, "", "lib/img/cursor.png", "lib/img/cursor.png");
		prop_tb.setItemToolTip('combi_lightNavigation','<?php echo _l('Light navigation (simple click on grid)',1)?>');
	}
	prop_tb.addButton("combi_win_attributes", 100, "", "lib/img/asterisk_yellow.png", "lib/img/asterisk_yellow.png");
	prop_tb.setItemToolTip('combi_win_attributes','<?php echo _l('Open attributes and groups window',1)?>');
	prop_tb.addInput("combi_add_input", 100,"1",30);
	prop_tb.setItemToolTip('combi_add_input','<?php echo _l('Number of combinations to create when clicking on the Create button',1)?>');
	prop_tb.addButton("combi_add", 100, "", "lib/img/add.png", "lib/img/add_dis.png");
	prop_tb.setItemToolTip('combi_add','<?php echo _l('Create new combination',1)?>');
	prop_tb.addButton('combi_psautocreate',100,'','lib/img/combinations_ps_create.png','lib/img/combinations_ps_create.png');
	prop_tb.setItemToolTip('combi_psautocreate','<?php echo _l('Open PrestaShop combination creation form',1)?>');
	prop_tb.addButton("combi_exportcsv", 100, "", "lib/img/page_excel.png", "lib/img/page_excel.png");
	prop_tb.setItemToolTip('combi_exportcsv','<?php echo _l('Export grid to clipboard in CSV format for MSExcel with tab delimiter.',1)?>');
	prop_tb.addButton('combi_del',100,'','lib/img/delete.gif','lib/img/delete.gif');
	prop_tb.setItemToolTip('combi_del','<?php echo _l('Delete combination',1)?>');
	prop_tb.addButton("combi_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	prop_tb.setItemToolTip('combi_selectall','<?php echo _l('Select all combinations',1)?>');
	prop_tb.addButtonTwoState('combi_sortByPosition', 100, "", "lib/img/text_list_numbers.png", "lib/img/text_list_numbers.png");
	prop_tb.setItemToolTip('combi_sortByPosition','<?php echo _l('Sort by position',1)?>');

	var marginMatrix_form = "";
	function calculMarginCombination(rId)
	{
		if(prop_tb._combinationsGrid.getColIndexById('margin')!=undefined && prop_tb._combinationsGrid.getColIndexById('wholesale_price')!=undefined)
		{
			var formule = marginMatrix_form;
			
			idxPriceIncTaxes=prop_tb._combinationsGrid.getColIndexById('price');
			idxPriceWithoutTaxes=prop_tb._combinationsGrid.getColIndexById('priceextax');
			idxWholeSalePrice=prop_tb._combinationsGrid.getColIndexById('wholesale_price');
			
			idxMargin=prop_tb._combinationsGrid.getColIndexById('margin');
			
			var price = prop_tb._combinationsGrid.cells(rId,idxPriceWithoutTaxes).getValue();
			if(price==null || price=="")
				price = 0;
			formule = formule.replace("{price}",price)
							.replace("{price}",price)
							.replace("{price}",price);
						
			var price_inc_tax = prop_tb._combinationsGrid.cells(rId,idxPriceIncTaxes).getValue();
			if(price_inc_tax==null || price_inc_tax=="")
				price_inc_tax = 0;	
			formule = formule.replace("{price_inc_tax}",price_inc_tax)
							.replace("{price_inc_tax}",price_inc_tax)
							.replace("{price_inc_tax}",price_inc_tax);
							
			var wholesale_price = prop_tb._combinationsGrid.cells(rId,idxWholeSalePrice).getValue();
			if(wholesale_price==null || wholesale_price=="")
				wholesale_price = 0;	
			formule = formule.replace("{wholesale_price}",wholesale_price)
							.replace("{wholesale_price}",wholesale_price)
							.replace("{wholesale_price}",wholesale_price);
							
			if(wholesale_price>0 && price>0)
				var margin = eval(formule);
			else
				var margin = 0;
			prop_tb._combinationsGrid.cells(rId,idxMargin).setValue(priceFormat(margin));
	
			<?php if (_s('CAT_PROD_GRID_MARGIN_COLOR')!='') { ?>
			if (idxMargin)
			{
				var rules=('<?php echo str_replace("'","",_s('CAT_PROD_GRID_MARGIN_COLOR'));?>').split(';');
				for(var i=(rules.length-1) ; i >= 0 ; i--){
					var rule=rules[i].split(':');
					if ( Number(prop_tb._combinationsGrid.cells(rId,idxMargin).getValue()) < Number(rule[0])){
						prop_tb._combinationsGrid.cells(rId,idxMargin).setBgColor(rule[1]);
						prop_tb._combinationsGrid.cells(rId,idxMargin).setTextColor('#FFFFFF');
					}
				}
			}
			<?php } ?>
		}
	}
	function sumColumn(ind) {
	    var out = 0;
	    prop_tb._combinationsGrid.forEachRow(function(id){
	    	var new_qty = parseInt(prop_tb._combinationsGrid.cells(id, ind).getValue());
	    	if(	new_qty>0)
	    		out += new_qty;
	    });
	    return out;
	}
	function updateProductQuantity()
	{
		idxProductQty=cat_grid.getColIndexById('quantity');
		idxQty=prop_tb._combinationsGrid.getColIndexById('quantity');
		if (idxProductQty) cat_grid.cells(lastProductSelID,idxProductQty).setValue(sumColumn(idxQty));
	}

	needInitCombinations = 1;
	function initCombinations(){
		if (needInitCombinations)
		{
			if (isIPAD){
				prop_tb._combinationsLayout = dhxLayout.cells('b').attachLayout('2E');
				prop_tb._combinationsLayout.cells('b').setText("<?php echo _l('Properties of selected combinations')?>");
			}else{
				prop_tb._combinationsLayout = dhxLayout.cells('b').attachLayout('2U');
				prop_tb._combinationsLayout.cells('b').setText("<?php echo _l('Combinations')?>");
			}
				
			prop_tb._combinationsLayout.cells('a').hideHeader();
			
			dhxLayout.cells('b').showHeader();
			actual_subproperties = "combi_images";
			
			prop_tb._combinationsLayout.cells('b').setWidth(190);
			
			prop_tb.combi_subproperties_tb=prop_tb._combinationsLayout.cells('b').attachToolbar();
			
			var opts = new Array();
			prop_tb.combi_subproperties_tb.addButtonSelect("combiSubProperties", 0, "<?php echo _l('Images')?>", opts, "lib/img/picture.png", "lib/img/picture.png",false,true);
			hideSubpropertiesItems();

			prop_tb._combinationsGrid = prop_tb._combinationsLayout.cells('a').attachGrid();
			
			// UISettings
			<?php if(_s("CAT_PROD_COMBI_METHOD")) { ?>
			prop_tb._combinationsGrid._uisettings_prefix='cat_combination_separate';
			<?php } else { ?>
			prop_tb._combinationsGrid._uisettings_prefix='cat_combination';
			<?php }  ?>
			prop_tb._combinationsGrid._uisettings_name=prop_tb._combinationsGrid._uisettings_prefix;
			prop_tb._combinationsGrid._first_loading=1;
			initGridUISettings(prop_tb._combinationsGrid);
			
			prop_tb._combinationsGrid.setImagePath("lib/js/imgs/");
			prop_tb._combinationsGrid.enableSmartRendering(true);

			<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
				prop_tb._combinationsGrid.enableColumnMove(false);
			<?php } ?>
			

			prop_tb._combinationsGrid.attachEvent("onDhxCalendarCreated",function(calendar){
				calendar.setSensitiveRange("2012-01-01",null);
			});
			
			<?php if(SCAS && _r("ACT_CAT_ADVANCED_STOCK_MANAGEMENT")) { ?>
			prop_tb._combinationsGrid.attachEvent("onRowDblClicked", function(rId,cInd){
				idxQtyMvt=prop_tb._combinationsGrid.getColIndexById('quantityupdate');
				idxQtyPhy=prop_tb._combinationsGrid.getColIndexById('quantity_physical');
				idxQtyUse=prop_tb._combinationsGrid.getColIndexById('quantity_usable');
				idxQtyRea=prop_tb._combinationsGrid.getColIndexById('quantity_real');
		
				if(cInd==idxQtyMvt || cInd==idxQtyPhy || cInd==idxQtyUse || cInd==idxQtyRea)
				{
					var type_advanced_stock_management =  cat_grid.getUserData(lastProductSelID,"type_advanced_stock_management");
					if(type_advanced_stock_management=="2" && prop_tb._combinationsGrid.cells(rId,idxQtyMvt).getBgColor()=="#d7f7bf")
					{
						if (!dhxWins.isWindow("wStockMvt"))
						{
							wStockMvt = dhxWins.createWindow("wStockMvt", ($(window).width()/2-200), 50, 430, 580);
							wStockMvt.setIcon('lib/img/building.png','../../../lib/img/building.png');
							wStockMvt.setText("<?php echo _l('Create a new stock movement')?>");
							wStockMvt.show();
							$.get("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product_attribute="+rId+"&id_lang="+SC_ID_LANG,function(data){
									$('#jsExecute').html(data);
								});
						}else{
							wStockMvt.setDimension(430, 580);
							wStockMvt.show();
							$.get("index.php?ajax=1&act=cat_win-stockmvt_choose_init&id_product_attribute="+rId+"&id_lang="+SC_ID_LANG,function(data){
									$('#jsExecute').html(data);
								});
						}
						
						return false;
					}
				}
				return true;
			});
			<?php } ?>
			
			// Context menu for combinations grid
			combi_cmenu=new dhtmlXMenuObject();
			combi_cmenu.renderAsContextMenu();
			function onGridCombiContextButtonClick(itemId){
				tabId=prop_tb._combinationsGrid.contextID.split('_');
				tabId=tabId[0];
				if (itemId=="copy"){
					if (lastColumnRightClicked_Combi!=0)
					{
						clipboardValue_Combi=prop_tb._combinationsGrid.cells(tabId,lastColumnRightClicked_Combi).getValue();
						combi_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+prop_tb._combinationsGrid.cells(tabId,lastColumnRightClicked_Combi).getTitle());
						clipboardType_Combi=lastColumnRightClicked_Combi;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_Combi!=0 && clipboardValue_Combi!=null && clipboardType_Combi==lastColumnRightClicked_Combi)
					{
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								//console.log(prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi).substr(0,5));
								//if (prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi).substr(0,5)!='attr_')
								if (prop_tb._combinationsGrid.cells(selArray[i],prop_tb._combinationsGrid.getColIndexById('id_product_attribute')).getValue()=='NEW'){
									var tmp = clipboardValue_Combi.split("|||");
									var tmp_name = tmp[0];
									<?php if(version_compare(_PS_VERSION_, '1.5.0.1', '>=')) { ?>
									tmp_name = tmp_name.split(";;;");
									tmp_name = tmp_name[1];
									<?php } ?>
									prop_tb._combinationsGrid.cells(selArray[i],lastColumnRightClicked_Combi).setValue(combiAttrValues[lastColumnRightClicked_Combi][tmp_name]);
									prop_tb._combinationsGrid.cells(selArray[i],lastColumnRightClicked_Combi).cell.wasChanged=true;
									onEditCellCombi(2,selArray[i],lastColumnRightClicked_Combi,clipboardValue_Combi,null);
								}
								else
								{
									prop_tb._combinationsGrid.cells(selArray[i],lastColumnRightClicked_Combi).setValue(clipboardValue_Combi);
									prop_tb._combinationsGrid.cells(selArray[i],lastColumnRightClicked_Combi).cell.wasChanged=true;
									onEditCellCombi(2,selArray[i],lastColumnRightClicked_Combi,clipboardValue_Combi,null);
								}
							}
						}
					}
				}
				if (itemId=="open_the_attribute_group"){
					var AttColId = prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
					AttrIdToOpen = null;
					if (AttColId.indexOf('attr_')!=-1)
					{
						var AttIdArray = AttColId.split('_');
						if (AttIdArray.length == 3)
						{
							AttrIdToOpen = AttIdArray[2]*1
						}

						if (!dhxWins.isWindow('wAttributes'))
						{
							wAttributes = dhxWins.createWindow('wAttributes', 50, 50, 900, $(window).height()-75);
							wAttributes.setIcon('lib/img/asterisk_yellow.png','../../../lib/img/asterisk_yellow.png');
							wAttributes.setText('<?php echo _l('Attributes and groups', 1); ?>');
							$.get('index.php?ajax=1&act=cat_win-attribute_init',function(data){$('#jsExecute').html(data);});
						}else{
							wAttributes.show();
							displayGroups();
						}
					}
				}
				<?php
				$massupdate_suffix = "";
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					$massupdate_suffix = "15";
				?>
				if (itemId=="massupdate_round_price_1"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=1&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_2"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=2&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_3"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=3&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_4"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=4&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_5"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=5&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_6"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=6&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_7"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=7&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_8"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=8&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_9"){
					if (confirm('<?php echo _l('Are you sure that you want to round all these prices up?',1)?>')){
						selection=prop_tb._combinationsGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							 var column=prop_tb._combinationsGrid.getColumnId(lastColumnRightClicked_Combi);
							$.post('index.php?ajax=1&act=cat_combination_massupdate<?php echo $massupdate_suffix; ?>&field=mass_round&todo=9&id_product='+lastProductSelID+'&column='+column,{'combilist':selection},function(data){
									displayCombinations();
							});
						}
					}
				}
				if (itemId=="massupdate_round_price_help"){
					window.open('<?php echo getHelpLink('massupdate_round_price_help') ?>','_blank');
				}
			}
			combi_cmenu.attachEvent("onClick", onGridCombiContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
<?php
if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Combination::isFeatureActive()) && _r("MEN_CAT_ATTRIBUTES_GROUPS"))
{
?>
					'<item text="<?php echo _l('Open the attribute group',1)?>" id="open_the_attribute_group"/>'+
<?php
}
?>
					'<item text="<?php echo _l('Rounding the price up',1)?>" id="massupdate_round_price">'+
						'<item text="<?php echo _l('X,00'); ?>" id="massupdate_round_price_1"/>'+
						'<item text="<?php echo _l('X,X0'); ?>" id="massupdate_round_price_2"/>'+
						'<item text="<?php echo _l('X,X0'); ?> <?php echo _l('or'); ?> <?php echo _l('X,X5'); ?>" id="massupdate_round_price_3"/>'+
						'<item text="<?php echo _l('X,00'); ?> <?php echo _l('or'); ?> <?php echo _l('X,50'); ?>" id="massupdate_round_price_4"/>'+
						'<item text="<?php echo _l('X,49'); ?> <?php echo _l('or'); ?> <?php echo _l('X,99'); ?>" id="massupdate_round_price_7"/>'+
						'<item text="<?php echo _l('X,90'); ?>" id="massupdate_round_price_5"/>'+
						'<item text="<?php echo _l('X,99'); ?>" id="massupdate_round_price_6"/>'+
						'<item text="<?php echo _l('X9'); ?>" id="massupdate_round_price_8"/>'+
						'<item text="<?php echo _l('X99'); ?>" id="massupdate_round_price_9"/>'+
						'<item text="<?php echo _l('X,95'); ?> <?php echo _l('or'); ?> <?php echo _l('X,05'); ?>" id="massupdate_round_price_10"/>'+
						'<item text="<?php echo _l('Help'); ?>" id="massupdate_round_price_help" img="lib/img/help.png" imgdis="lib/img/help.png"/>'+
					'</item>'+
				'</menu>';
			combi_cmenu.loadStruct(contextMenuXML);
			prop_tb._combinationsGrid.enableContextMenu(combi_cmenu);

			prop_tb._combinationsGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
					var disableOnCols=new Array(
							prop_tb._combinationsGrid.getColIndexById('id_product_attribute'),
							prop_tb._combinationsGrid.getColIndexById('ppriceextax'),
							prop_tb._combinationsGrid.getColIndexById('pweight'),
							prop_tb._combinationsGrid.getColIndexById('pprice'),
							prop_tb._combinationsGrid.getColIndexById('default_on'),
							prop_tb._combinationsGrid.getColIndexById('id_product_attribute'),
							prop_tb._combinationsGrid.getColIndexById('margin')
							);
					if (in_array(colidx,disableOnCols))
					{
						return false;
					}
					lastColumnRightClicked_Combi=colidx;
					combi_cmenu.setItemText('object', '<?php echo _l('Combination:')?> '+prop_tb._combinationsGrid.cells(rowid,prop_tb._combinationsGrid.getColIndexById('id_product_attribute')).getValue());
					if (lastColumnRightClicked_Combi==clipboardType_Combi)
					{
						combi_cmenu.setItemEnabled('paste');
					}else{
						combi_cmenu.setItemDisabled('paste');
					}
					if (prop_tb._combinationsGrid.cells(rowid,0).getValue()=='NEW')
					{
						combi_cmenu.setItemDisabled('copy');
					}else{
						combi_cmenu.setItemEnabled('copy');
					}
					
					idxPrice=prop_tb._combinationsGrid.getColIndexById('priceextax');
					idxPriceInTax=prop_tb._combinationsGrid.getColIndexById('price');
					idxWholesalePrice=prop_tb._combinationsGrid.getColIndexById('wholesale_price');
					if(colidx==idxPrice || colidx==idxPriceInTax || colidx==idxWholesalePrice)
					{
						combi_cmenu.setItemEnabled('massupdate_round_price');
						combi_cmenu.setItemEnabled('massupdate_round_price_1');
						combi_cmenu.setItemEnabled('massupdate_round_price_2');
						combi_cmenu.setItemEnabled('massupdate_round_price_3');
						combi_cmenu.setItemEnabled('massupdate_round_price_4');
						combi_cmenu.setItemEnabled('massupdate_round_price_5');
						combi_cmenu.setItemEnabled('massupdate_round_price_6');
						combi_cmenu.setItemEnabled('massupdate_round_price_7');
						combi_cmenu.setItemEnabled('massupdate_round_price_8');
						combi_cmenu.setItemEnabled('massupdate_round_price_9');
						combi_cmenu.setItemEnabled('massupdate_round_price_help');
						var title = "<?php echo _l('price');?>";
						if(colidx==idxPrice)
							title = "<?php echo _l('Price excl. Tax');?>";
						else if(colidx==idxPriceInTax)
							title = "<?php echo _l('Price incl. Tax');?>";
						else if(colidx==idxWholesalePrice)
							title = "<?php echo _l('Wholesale price');?>";
						combi_cmenu.setItemText('massupdate_round_price', '<?php echo _l('Rounding up the');?> '+title);
					}
					else
					{
						combi_cmenu.setItemDisabled('massupdate_round_price');
						combi_cmenu.setItemDisabled('massupdate_round_price_1');
						combi_cmenu.setItemDisabled('massupdate_round_price_2');
						combi_cmenu.setItemDisabled('massupdate_round_price_3');
						combi_cmenu.setItemDisabled('massupdate_round_price_4');
						combi_cmenu.setItemDisabled('massupdate_round_price_5');
						combi_cmenu.setItemDisabled('massupdate_round_price_6');
						combi_cmenu.setItemDisabled('massupdate_round_price_7');
						combi_cmenu.setItemDisabled('massupdate_round_price_8');
						combi_cmenu.setItemDisabled('massupdate_round_price_9');
						combi_cmenu.setItemDisabled('massupdate_round_price_help');
						combi_cmenu.setItemText('massupdate_round_price', '<?php echo _l('Rounding the price up',1);?>');
					}
					
					return true;
				});
				
			lastEditedCellIsAttr=0;
			function onEditCellCombi(stage,rId,cInd,nValue,oValue){
				if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
				if (stage==0) lastEditedCellIsAttr=0;
				if (stage==1 && prop_tb._combinationsGrid.getColumnId(cInd).substr(0,5)=='attr_')
				{
					lastEditedCellIsAttr=1;
			  	  var editor = this.editor;
				    var pos = this.getPosition(editor.cell);
			    	var y = document.body.offsetHeight-pos[1];
			    	if(y < editor.list.offsetHeight)
					    editor.list.style.top = pos[1] - editor.list.offsetHeight + 'px';
			    }
				idxDefault=prop_tb._combinationsGrid.getColIndexById('default_on');
				if (nValue!=oValue){
					idxVAT=cat_grid.getColIndexById(tax_identifier);
					idxQty=prop_tb._combinationsGrid.getColIndexById('quantity');
					idxQtyUpdate=prop_tb._combinationsGrid.getColIndexById('quantityupdate');
					idxPrice=prop_tb._combinationsGrid.getColIndexById('price');
					idxUnitPrice=prop_tb._combinationsGrid.getColIndexById('unit_price_impact');
					idxUnitPriceIncTax=prop_tb._combinationsGrid.getColIndexById('unit_price_impact_inc_tax');
					idxPriceExTax=prop_tb._combinationsGrid.getColIndexById('priceextax');
					idxEcotax=prop_tb._combinationsGrid.getColIndexById('ecotax');
					idxWholeSalePrice=prop_tb._combinationsGrid.getColIndexById('wholesale_price');
					idxWeight=prop_tb._combinationsGrid.getColIndexById('weight');
					prop_tb._combinationsGrid.setUserData(rId, 'ecotaxentered', 0);
					if (cInd == idxQtyUpdate){ //Quantity update
						var qty = prop_tb._combinationsGrid.cells(rId,idxQty).getValue()*1;
						var qtyToAdd = prop_tb._combinationsGrid.cells(rId,idxQtyUpdate).getValue()*1;
						prop_tb._combinationsGrid.cells(rId,idxQty).setValue(qty+qtyToAdd);
					}
					if (cInd == idxQty || cInd == idxQtyUpdate && stage!=-1){ //Quantity update in products grid
						/*idxProductQty=cat_grid.getColIndexById('quantity');
						if (idxProductQty) cat_grid.cells(lastProductSelID,idxProductQty).setValue(sumColumn(idxQty));*/
						<?php // TO DO : FAIRE LE MENAGE S'IL N'Y A PAS DE PROBLEME => PROBLEME !!!
						//if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<')) { ?>
						/*if(idxQty > idxDefault)
						{*/
							prop_tb._combinationsGrid.cells(rId,idxQty).cell.wasChanged=true;
						//}
						<?php //} ?>
					}
					if (cInd == idxWeight){
						nValue = noComma(nValue);
						prop_tb._combinationsGrid.cells(rId,idxWeight).setValue(nValue);
					}
					if (cInd == idxUnitPrice){
						nValue = noComma(nValue);
						var vat = tax_values[cat_grid.cells(lastProductSelID,idxVAT).getTitle()]*1;
						var upit = nValue * vat;
						prop_tb._combinationsGrid.cells(rId,idxUnitPriceIncTax).setValue(upit);
						prop_tb._combinationsGrid.cells(rId,idxUnitPrice).setValue(nValue);
					}
					if (cInd == idxUnitPriceIncTax){
						nValue = noComma(nValue);
						var vat = tax_values[cat_grid.cells(lastProductSelID,idxVAT).getTitle()]*1;
						var upit = noComma(nValue);
						var upet = upit / vat;
						prop_tb._combinationsGrid.cells(rId,idxUnitPrice).setValue(upet);
						prop_tb._combinationsGrid.setUserData(rId, 'unity_price_impact_excl_tax', upet);
					}
					if (cInd == idxEcotax){ //ecotax
						var tax=prop_tb._combinationsGrid.getUserData('','taxrate')/100+1;
						var eco=noComma(nValue);
						prop_tb._combinationsGrid.setUserData(rId, 'ecotaxentered', 1);
						prop_tb._combinationsGrid.cells(rId,idxEcotax).setValue(priceFormat6Dec(nValue));
<?php
	if (_s('CAT_PROD_ECOTAXINCLUDED'))
	{
?>
						prop_tb._combinationsGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((prop_tb._combinationsGrid.cells(rId,idxPrice).getValue() - eco)/tax));
<?php
	}
?>
						calculMarginCombination(rId);
					}
					if (cInd == idxPrice){ //price update
						var tax=prop_tb._combinationsGrid.getUserData('','taxrate')/100+1;
						var eco=0;
						if (idxEcotax)
							eco=prop_tb._combinationsGrid.cells(rId,idxEcotax).getValue()*1;
						prop_tb._combinationsGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(nValue));
						prop_tb._combinationsGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec((noComma(nValue) <?php	if (_s('CAT_PROD_ECOTAXINCLUDED')){?> - eco <?php	} ?>)/tax));
						calculMarginCombination(rId);
					}
					if (cInd == idxPriceExTax){ //priceExTax update
						var tax=prop_tb._combinationsGrid.getUserData('','taxrate')/100+1;
						var eco=0;
						if (idxEcotax)
							eco=prop_tb._combinationsGrid.cells(rId,idxEcotax).getValue()*1;
						prop_tb._combinationsGrid.cells(rId,idxPriceExTax).setValue(priceFormat6Dec(nValue));
						prop_tb._combinationsGrid.cells(rId,idxPrice).setValue(priceFormat6Dec(noComma(nValue)*tax <?php	if (_s('CAT_PROD_ECOTAXINCLUDED')){?> + eco <?php	} ?>));
						calculMarginCombination(rId);
					}
					if (cInd == idxWholeSalePrice){ //Wholesale price
						prop_tb._combinationsGrid.cells(rId,idxWholeSalePrice).setValue(priceFormat6Dec(nValue));
						calculMarginCombination(rId);
					}
				}
<?php
	sc_ext::readCustomCombinationsGridConfigXML('onEditCell');
?>
				if (nValue!=oValue || cInd==idxDefault){
					idxSupplierReference=prop_tb._combinationsGrid.getColIndexById('supplier_reference');
					if(cInd == idxSupplierReference && prop_tb._combinationsGrid.getUserData("","productIDsupplier")==0) {
						dhtmlx.message({text:'<?php echo addslashes(_l('A supplier needs to be associated to the product to set a supplier\'s reference to the combination'))?>',type:'error'});
						return false;
					}
					if(stage==2 || (cInd==idxDefault && stage==1))
					{
						var params = {
							name: "cat_combination_update_queue",
							row: rId,
							action: "update",
							params: {},
							callback: "callbackCombinationsGrid('"+rId+"','update','"+rId+"');"
						};
						// COLUMN VALUES
						prop_tb._combinationsGrid.forEachCell(rId,function(cellObj,ind){
							params.params[prop_tb._combinationsGrid.getColumnId(ind)] = prop_tb._combinationsGrid.cells(rId,ind).getValue();
						});
						// USER DATA
						params.params["id_product"]=lastProductSelID;
						params.params['taxrate'] = prop_tb._combinationsGrid.getUserData("", "taxrate");
						params.params['marginMatrix_form'] = prop_tb._combinationsGrid.getUserData("", "marginMatrix_form");
						params.params['productprice'] = prop_tb._combinationsGrid.getUserData("", "productprice");
						params.params['productweight'] = prop_tb._combinationsGrid.getUserData("", "productweight");
						params.params['productecotax'] = prop_tb._combinationsGrid.getUserData("", "productecotax");
						params.params['ecotaxentered'] = prop_tb._combinationsGrid.getUserData(rId, "ecotaxentered");
						params.params['productpriceinctax'] = prop_tb._combinationsGrid.getUserData("", "productpriceinctax");
						params.params['attr_ids'] = prop_tb._combinationsGrid.getUserData(rId, "attr_ids");
						<?php if(SCMS) { ?>
						params.params['default_shop'] = prop_tb._combinationsGrid.getUserData("", "default_shop");
						<?php } ?>
						params.params['updated_field'] = prop_tb._combinationsGrid.getColumnId(cInd);
						params.params = JSON.stringify(params.params);
						addInUpdateQueue(params,prop_tb._combinationsGrid);
					}
					return true;
				}
			}
			prop_tb._combinationsGrid.attachEvent("onEditCell",onEditCellCombi);
			
			prop_tb._combinationsGrid.attachEvent("onAfterCMove", function(cInd,posInd){
				var nbCols = prop_tb._combinationsGrid.getColumnsNum();
				<?php if (!_s('CAT_PROD_COMBI_METHOD')) { ?>
				prop_tb._combinationsGrid.forEachRow(function(sid){
					var attr_ids = "-";
					for(i=0 ; i < nbCols ; i++)
					{
						var temp_id_tab = prop_tb._combinationsGrid.getColumnId(i);
						if(temp_id_tab!=undefined && temp_id_tab!=null && temp_id_tab!="" && temp_id_tab!=0)
						{
							colIDTab=temp_id_tab.split('_');
							if (colIDTab.length == 3 && colIDTab[0] == 'attr')
							{
								var temp_id = prop_tb._combinationsGrid.cells(sid,i).getValue();
								var temps = temp_id.split("|||");
								var tmp_name = temps[0];
								var tmp_id = temps[1];
								if(tmp_id!=undefined && tmp_id!=null && tmp_id!="" && tmp_id!=0)
									attr_ids = attr_ids+tmp_id+"-";
							}
						}
					}
					prop_tb._combinationsGrid.setUserData(sid, "attr_ids", attr_ids);
	    		});
				<?php } ?>
						
			});
			
			prop_tb._combinationsGrid.attachEvent("onScroll",function(){
				marginMatrix_form = prop_tb._combinationsGrid.getUserData("", "marginMatrix_form");
			   	prop_tb._combinationsGrid.forEachRow(function(id){
			      calculMarginCombination(id);
			   });
			});

					
				
			
			function doOnRowCombiSelected(idcombi){
				if (lastCombiSelID!=idcombi)
				{
					lastCombiSelID=idcombi;
				}
			}
			prop_tb._combinationsGrid.attachEvent("onRowSelect",doOnRowCombiSelected);
				
			needInitCombinations=0;
		}
	}




	function setPropertiesPanel_combinations(id){
		if (id=='combi_del')
		{
			if (prop_tb._combinationsGrid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select a combination',1)?>');
			}else{
				if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					{
						/*$.post("index.php?ajax=1&act=cat_combination_del&id_product="+lastProductSelID,{'id_product_attribute':prop_tb._combinationsGrid.getSelectedRowId()},function(){
								displayCombinations("updateProductQuantity()");
							});*/	
						var tmp_ids = prop_tb._combinationsGrid.getSelectedRowId();
						var ids = new Array();
						if(tmp_ids.search(",")>=0)
							ids = tmp_ids.split(",");
						else
							ids[0] =	tmp_ids;
						var nb = ids.length*1-1;
						$.each(ids, function(ind, id){
							var params = {
								name: "cat_combination_update_queue",
								row: id,
								action: "delete",
								params: {},
								callback: "displayCombinations('updateProductQuantity()');"
							};
							params.params["id_product"]=lastProductSelID;
							params.params["id_product_attribute"]=id;
							params.params = JSON.stringify(params.params);
							if(nb!=ind)
								params.callback = "";
							addInUpdateQueue(params,prop_tb._combinationsGrid);
							});
					}
				}else{
					alert('<?php echo _l('Please select a product',1)?>');
				}
			}
		}
		if (id=='combi_add' || id=='force_combi_add')
		{
<?php
if (_s('CAT_PROD_COMBI_METHOD'))
{
?>
dhtmlx.message({text:'<?php echo _l('You have to use the standard combinations grid format to create new combinations in Store Commander. Please change the settings of SC.',1)?>',type:'error'});
<?php
}else{
?>
			if (getCombinationsNum()==0 && id=='combi_add')
			{
				prop_tb.callEvent("onClick",["combi_win_attributes"]);
			}else{
				var newIdBase = (new Date()).valueOf();
				nb=prop_tb.getValue('combi_add_input');
				
				if(getCombinationsNum()==0)
				{
					<?php if (_s("CAT_NOTICE_CREATE_FIRST_COMBI")) { ?>
					if (nb>0)
						dhtmlx.message({text:'<?php echo _l('Caution: The first combination needs to be created by SC so that you can then add more in bulk. To stop this alert: SC  > Tools > Settings > Alert.',1)?>',type:'info',expire:10000});
					<?php } ?>
					nb=1;
				}
				
				idxCombiLocation=prop_tb._combinationsGrid.getColIndexById('location');
				idxCombiEAN13=prop_tb._combinationsGrid.getColIndexById('ean13');
				idxCombiReference=prop_tb._combinationsGrid.getColIndexById('reference');
				idxCombiSupplierReference=prop_tb._combinationsGrid.getColIndexById('supplier_reference');
				idxCombiQuantity=prop_tb._combinationsGrid.getColIndexById('quantity');
				idxCombiPriceProd=prop_tb._combinationsGrid.getColIndexById('pprice');
				idxCombiPrice=prop_tb._combinationsGrid.getColIndexById('price');
				idxCombiPriceExTaxProd=prop_tb._combinationsGrid.getColIndexById('ppriceextax');
				idxCombiPriceExTax=prop_tb._combinationsGrid.getColIndexById('priceextax');
				idxCombiWeightProd=prop_tb._combinationsGrid.getColIndexById('pweight');
				idxCombiWeight=prop_tb._combinationsGrid.getColIndexById('weight');
				idxCombiDefault=prop_tb._combinationsGrid.getColIndexById('default_on');
				
				if (isNaN(nb)) nb=1;
				for (k=1 ; k <= nb ; k++)
				{
					newId='NEW'+(newIdBase*100+k);
					var values = new Array("NEW","","","","","","","","","","","","");
					var nbCols = prop_tb._combinationsGrid.getColumnsNum();
					for(i=1 ; i < nbCols ; i++) values[i]="";
<?php
	if (SCI::getConfigurationValue('SC_PLUG_DISABLECOMBINATIONS',0))
	{
?>
					idxCombiSC_Active=prop_tb._combinationsGrid.getColIndexById('sc_active');
					values[idxCombiSC_Active] = 1;
<?php
	}
?>
					values[idxCombiPriceProd] = priceFormat6Dec(prop_tb._combinationsGrid.getUserData('','productprice')*(1+prop_tb._combinationsGrid.getUserData('','taxrate')/100));
					values[idxCombiPrice] = values[idxCombiPriceProd];
					values[idxCombiPriceExTaxProd] = prop_tb._combinationsGrid.getUserData('','productprice');
					values[idxCombiPriceExTax] = prop_tb._combinationsGrid.getUserData('','productprice');
					values[idxCombiWeightProd] = prop_tb._combinationsGrid.getUserData('','productweight');
					values[idxCombiWeight] = values[idxCombiWeightProd];
					values[idxCombiQuantity] = <?php echo _s("CAT_PROD_COMBI_CREA_QTY"); ?>;
					values[idxCombiReference] = '';
					if (prop_tb._combinationsGrid.getFilterElement(idxCombiReference)!=null) 
						values[idxCombiReference] = prop_tb._combinationsGrid.getFilterElement(idxCombiReference).value;
					values[idxCombiSupplierReference] = '';
					if (prop_tb._combinationsGrid.getFilterElement(idxCombiSupplierReference)!=null) 
						values[idxCombiSupplierReference] = prop_tb._combinationsGrid.getFilterElement(idxCombiSupplierReference).value;
					values[idxCombiEAN13] = '';
					if (prop_tb._combinationsGrid.getFilterElement(idxCombiEAN13)!=null) 
						values[idxCombiEAN13] = prop_tb._combinationsGrid.getFilterElement(idxCombiEAN13).value;
					values[idxCombiLocation] = '';
					if (prop_tb._combinationsGrid.getFilterElement(idxCombiLocation)!=null) 
						values[idxCombiLocation] = prop_tb._combinationsGrid.getFilterElement(idxCombiLocation).value;
						
					if(getCombinationsNum()==0 && k==1)
						values[idxCombiDefault] = 1;
						
					prop_tb._combinationsGrid.addRow(newId,values);
					prop_tb._combinationsGrid.setRowHidden(newId, true);
					
					var attr_ids = "-";
					for(i=0 ; i < nbCols ; i++)
					{
						colIDTab=prop_tb._combinationsGrid.getColumnId(i).split('_');
						if (colIDTab.length == 3 && colIDTab[0] == 'attr')
						{
							var temp_id = prop_tb._combinationsGrid.cells(newId,i).getValue();
							var temps = temp_id.split("|||");
							var tmp_name = temps[0];
							var tmp_id = temps[1];
							if(tmp_id!=undefined && tmp_id!=null && tmp_id!="" && tmp_id!=0)
								attr_ids = attr_ids+tmp_id+"-";
						}
					}
					prop_tb._combinationsGrid.setUserData(newId, "attr_ids", attr_ids);
					
					// INSERT
						var params = {
							name: "cat_combination_update",
							row: newId,
							action: "insert",
							params: {callback: "callbackCombinationsGrid('"+newId+"','insert','{newid}');"}
						};
						// COLUMN VALUES
						prop_tb._combinationsGrid.forEachCell(newId,function(cellObj,ind){
							params.params[prop_tb._combinationsGrid.getColumnId(ind)] = prop_tb._combinationsGrid.cells(newId,ind).getValue();
						});
						// USER DATA
						params.params["id_product"]=lastProductSelID;
						params.params['taxrate'] = prop_tb._combinationsGrid.getUserData("", "taxrate");
						params.params['marginMatrix_form'] = prop_tb._combinationsGrid.getUserData("", "marginMatrix_form");
						params.params['productprice'] = prop_tb._combinationsGrid.getUserData("", "productprice");
						params.params['productweight'] = prop_tb._combinationsGrid.getUserData("", "productweight");
						params.params['productecotax'] = prop_tb._combinationsGrid.getUserData("", "productecotax");
						params.params['productpriceinctax'] = prop_tb._combinationsGrid.getUserData("", "productpriceinctax");
						params.params['attr_ids'] = prop_tb._combinationsGrid.getUserData(newId, "attr_ids");
						<?php if(SCMS) { ?>
						params.params['default_shop'] = prop_tb._combinationsGrid.getUserData("", "default_shop");
						<?php } ?>

						sendInsert(params,prop_tb._combinationsLayout.cells('a'));
				}
			}
<?php
}
?>
		}

		if (id=='combinations')
		{
			displayCombinationPanel();
		}
		if (id=='combi_win_attributes')
		{
			if (!dhxWins.isWindow("wAttributes"))
			{
				wAttributes = dhxWins.createWindow("wAttributes", 50, 50, 900, $(window).height()-75);
				wAttributes.setIcon('lib/img/asterisk_yellow.png','../../../lib/img/asterisk_yellow.png');
				wAttributes.setText("<?php echo _l('Attributes and groups')?>");
				$.get("index.php?ajax=1&act=cat_win-attribute_init",function(data){
						$('#jsExecute').html(data);
					});
				wAttributes.attachEvent("onClose", function(win){
						wAttributes.hide();
						return false;
					});
			}else{
				wAttributes.show();
			}
		}
		if (id=='combi_psautocreate')
		{
			if (lastProductSelID!=0)
			{
				wPSCombinationCreateForm = dhxWins.createWindow("wPSCombinationCreateForm", 50, 50, 1000, $(window).height()-75);
				wPSCombinationCreateForm.setIcon('lib/img/combinations_ps_create.png','../../../lib/img/combinations_ps_create.png');
				wPSCombinationCreateForm.setText('<?php echo _l('Modify the combinations and close this window to refresh the grid',1)?>');
<?php
	if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
	{
		$url = SC_PS_PATH_ADMIN_REL.'index.php?controller=AdminStoreCommander&REDIRECTADMIN=1&subaction=AdminProducts&token='.$sc_agent->getPSToken('AdminStoreCommander');
		$array = array("id_product"=>"{id_product}","anchor"=>"#tab-step3");
		$query = http_build_query(array('urlParams' => $array));
		if(!empty($query))
			$url .= "&".$query;
		?>
		wPSCombinationCreateForm.attachURL("<?php echo str_replace("%7Bid_product%7D",'"+lastProductSelID+"',$url); ?>");
		<?php
	}
	elseif (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		?>
		wPSCombinationCreateForm.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminAttributeGenerator&id_product="+lastProductSelID+"&attributegenerator&token=<?php echo $sc_agent->getPSToken('AdminAttributeGenerator');?>");
		<?php
	}else{
?>
		wPSCombinationCreateForm.attachURL("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?tab=AdminCatalog&id_product="+lastProductSelID+"&attributegenerator&token=<?php echo $sc_agent->getPSToken('AdminCatalog');?>");
<?php
	}
?>
				wPSCombinationCreateForm.attachEvent("onClose", function(win){
							displayProducts('cat_grid.selectRowById('+lastProductSelID+',false);cat_grid.callEvent("onRowSelect",['+lastProductSelID+']);');
							return true;
						});
			}
		}
		if (id=='combi_refresh')
		{
			if (lastProductSelID!=0)
			{
				displayCombinations();
			}
		}
		if (id=='combi_filters_reset')
		{
			if (lastProductSelID!=0)
			{
				for(var i=0, l=prop_tb._combinationsGrid.getColumnsNum();i<l;i++)
				{
					if (prop_tb._combinationsGrid.getFilterElement(i)!=null) prop_tb._combinationsGrid.getFilterElement(i).value="";
				}
				prop_tb._combinationsGrid.filterByAll();
			}
			prop_tb.setListOptionSelected('combi_filters','');
		}
		if (id=='combi_filters_cols_show')
		{
			for(var i=0 , l=prop_tb._combinationsGrid.getColumnsNum() ; i < l ; i++)
			{
				prop_tb._combinationsGrid.setColumnHidden(i,false);
			}
			prop_tb.setListOptionSelected('combi_filters','');
		}
		if (id=='combi_filters_cols_hide')
		{
			idxCombiID=prop_tb._combinationsGrid.getColIndexById('id_product_attribute');
			idxCombiReference=prop_tb._combinationsGrid.getColIndexById('reference');
			for(i=0, l=prop_tb._combinationsGrid.getColumnsNum() ; i < l ; i++)
			{
				if (i!=idxCombiID && i!=idxCombiReference && (prop_tb._combinationsGrid.getColumnId(i).substr(0,4)!='attr'))
				{
					prop_tb._combinationsGrid.setColumnHidden(i,true);
				}else{
					prop_tb._combinationsGrid.setColumnHidden(i,false);
				}
			}
			prop_tb.setListOptionSelected('combi_filters','');
		}
		if (id=='combi_selectall')
		{
			prop_tb._combinationsGrid.enableSmartRendering(false);
			prop_tb._combinationsGrid.selectAll();
		}
		if (id=='combi_exportcsv'){
			prop_tb._combinationsGrid.enableCSVHeader(true);
			prop_tb._combinationsGrid.setCSVDelimiter("\t");
			var csv=prop_tb._combinationsGrid.serializeToCSV(true);
			displayQuickExportWindow(csv,1);
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_combinations);

	prop_tb.attachEvent("onStateChange",function(id,state){
		if (id=='combi_lightNavigation')
		{
			if (state)
			{
				prop_tb._combinationsGrid.enableLightMouseNavigation(true);
			}else{
				prop_tb._combinationsGrid.enableLightMouseNavigation(false);
			}
		}
		else if (id=='combi_sortByPosition')
		{
			if (state)
			{
				$.cookie('combi_sort_by_position',"1", { expires: 3600 });
			}else{
				$.cookie('combi_sort_by_position',"0", { expires: 3600 });
			}
			if (lastProductSelID!=0)
			{
				displayCombinations();
			}
		}
	});

function displayCombinations(callback,forceGroups)
{
	if (typeof prop_tb._combinationsGrid != 'undefined')
	{
		prop_tb._combinationsGrid.clearAll(true);
		prop_tb._combinationsGrid.loadXML("index.php?ajax=1&act=cat_combination_get&id_product="+lastProductSelID+"&forceGroups="+forceGroups+"&sortByPosition="+$.cookie('combi_sort_by_position')+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
				{
					indqtyupd=prop_tb._combinationsGrid.getColIndexById("quantityupdate");
					idxMargin=prop_tb._combinationsGrid.getColIndexById("margin");
					i=0;
					opt=new Array();
					while(typeof(prop_tb._combinationsGrid.getColumnId(i))!='undefined')
					{
						if (i==indqtyupd){
							opt[i]=false;
						}else{
							opt[i]=true;
						}
						i++;
					}
					//combinationsDataProcessor.setDataColumns(opt);
					nb=prop_tb._combinationsGrid.getRowsNum();
					prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('combinations')?>":" <?php echo _l('combination')?>"));
					
		  		// UISettings
					prop_tb._combinationsGrid._uisettings_name=prop_tb._combinationsGrid._uisettings_prefix+prop_tb._combinationsGrid.getColumnsNum();
					loadGridUISettings(prop_tb._combinationsGrid);
	    		
<?php
		if (_s('CAT_PROD_GRID_MARGIN_COLOR')!='')
		{
?>
					if (idxMargin)
					{
						var rules=('<?php echo str_replace("'","",_s('CAT_PROD_GRID_MARGIN_COLOR'));?>').split(';');
						prop_tb._combinationsGrid.forEachRow(function(rid){
								for(var i=0 ; i < rules.length ; i++){
									var rule=rules[i].split(':');
									if (prop_tb._combinationsGrid.cells(rid,idxMargin).getValue() < Number(rule[0])){
										prop_tb._combinationsGrid.cells(rid,idxMargin).setBgColor(rule[1]);
										prop_tb._combinationsGrid.cells(rid,idxMargin).setTextColor('#FFFFFF');
										break;
									}
								}
							});
					}
<?php
		}
?>
    			firstCombinationsLoading=0;
					if (id_product_attributeToSelect>0)
						prop_tb._combinationsGrid.selectRowById(id_product_attributeToSelect,false,true,true);
						
				marginMatrix_form = prop_tb._combinationsGrid.getUserData("", "marginMatrix_form");
			   	prop_tb._combinationsGrid.forEachRow(function(id){
			      calculMarginCombination(id);
			   });
						
				<?php if(SCMS) { ?>
				if(prop_tb._combinationsGrid.getUserData('','default_shop')!=null)
				{
					var default_shop = prop_tb._combinationsGrid.getUserData('','default_shop');
					var shop_name = cat_shoptree.getItemText(default_shop);
					
					var actual_name =  dhxLayout.cells('b').getText()+" / "+ shop_name;
					dhxLayout.cells('b').setText(actual_name);
				}
				<?php } ?>

				<?php if(_s("APP_DISABLED_COLUMN_MOVE")) { ?>
					prop_tb._combinationsGrid.enableColumnMove(false);
				<?php } ?>
				
				<?php sc_ext::readCustomCombinationsGridConfigXML('afterGetRows'); ?>

				// UISettings
				prop_tb._combinationsGrid._first_loading=0;
			
				colorExistCombi();
			
		    	if (callback!='') eval(callback);
				});
	}
}

function colorExistCombi()
{
	<?php if(_s("CAT_COLOR_SAME_COMBI")) { ?>
		var combi_exist = new Object();
		var nbCols = prop_tb._combinationsGrid.getColumnsNum();
		prop_tb._combinationsGrid.forEachRow(function(rid){
		
			var attr_ids = prop_tb._combinationsGrid.getUserData(rid, "attr_ids");
			var is_empty = false;
			
			if(attr_ids==undefined || attr_ids==null || attr_ids=="" || attr_ids==0 || attr_ids=="-" || attr_ids.search("--")>=0)
				 is_empty = true;
			
			if(is_empty)
			{
				for(i=0 ; i < nbCols ; i++)
				{
					colIDTab=prop_tb._combinationsGrid.getColumnId(i).split('_');
					if (colIDTab.length == 3 && colIDTab[0] == 'attr')
					{
						prop_tb._combinationsGrid.cells(rid,i).setBgColor("#D25411");
						prop_tb._combinationsGrid.cells(rid,i).setTextColor('#FFFFFF');
					}
				}
			}
			else
			{
				if(combi_exist[attr_ids]==undefined || combi_exist[attr_ids]==null)
				{
					combi_exist[attr_ids]=attr_ids;
				
					for(i=0 ; i < nbCols ; i++)
					{
						var temp_id_tab = prop_tb._combinationsGrid.getColumnId(i);
						if(temp_id_tab!=undefined && temp_id_tab!=null && temp_id_tab!="" && temp_id_tab!=0)
						{
							colIDTab=temp_id_tab.split('_');
							if (colIDTab.length == 3 && colIDTab[0] == 'attr')
							{
								prop_tb._combinationsGrid.cells(rid,i).setBgColor(null);
								prop_tb._combinationsGrid.cells(rid,i).setTextColor('#000000');
							}
						}
					}
				}
				else
				{
					for(i=0 ; i < nbCols ; i++)
					{
					
						var temp_id_tab = prop_tb._combinationsGrid.getColumnId(i);
						if(temp_id_tab!=undefined && temp_id_tab!=null && temp_id_tab!="" && temp_id_tab!=0)
						{
							colIDTab=temp_id_tab.split('_');
							if (colIDTab.length == 3 && colIDTab[0] == 'attr')
							{
								prop_tb._combinationsGrid.cells(rid,i).setBgColor("#D01D23");
								prop_tb._combinationsGrid.cells(rid,i).setTextColor('#FFFFFF');
							}
						}
					}
				}
			}
		});
	<?php } ?>
}

function getCombinationsNum()
{
	var i=0;
	prop_tb._combinationsGrid.forEachRow(function(id){ i++ });
	return i;
}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='combinations' && !dhxLayout.cells('b').isCollapsed() && lastProductSelIDForCombi!=idproduct)
			{
				lastProductSelIDForCombi = idproduct;
				displayCombinations();
			}
		});

			
	function combiWriteRefresh()
	{
		idxQty=prop_tb._combinationsGrid.getColIndexById('quantity');
		idxQtyUpdate=prop_tb._combinationsGrid.getColIndexById('quantityupdate');
		idxQtyUse=prop_tb._combinationsGrid.getColIndexById('quantity_usable');
		idxQtyPhy=prop_tb._combinationsGrid.getColIndexById('quantity_physical');
		idxQtyRea=prop_tb._combinationsGrid.getColIndexById('quantity_real');
		
		var ids = prop_tb._combinationsGrid.getSelectedRowId().split(',');
		$.each(ids, function(num, rId) {
			if (idxQty){
				prop_tb._combinationsGrid.setCellExcellType(rId,idxQty,"ro");
				prop_tb._combinationsGrid.cells(rId,idxQty).setValue('<?php echo _l('Refresh',1);?>');
			}

			if (idxQtyUpdate){
				prop_tb._combinationsGrid.setCellExcellType(rId,idxQtyUpdate,"ro");
				prop_tb._combinationsGrid.cells(rId,idxQtyUpdate).setValue('<?php echo _l('Refresh',1);?>');
			}

			if (idxQtyUse){
				prop_tb._combinationsGrid.setCellExcellType(rId,idxQtyUse,"ro");
				prop_tb._combinationsGrid.cells(rId,idxQtyUse).setValue('<?php echo _l('Refresh',1);?>');
			}

			if (idxQtyPhy){
				prop_tb._combinationsGrid.setCellExcellType(rId,idxQtyPhy,"ro");
				prop_tb._combinationsGrid.cells(rId,idxQtyPhy).setValue('<?php echo _l('Refresh',1);?>');
			}

			if (idxQtyRea){
				prop_tb._combinationsGrid.setCellExcellType(rId,idxQtyRea,"ro");
				prop_tb._combinationsGrid.cells(rId,idxQtyRea).setValue('<?php echo _l('Refresh',1);?>');
			}

		});
	}

	function displayCombinationPanel()
	{
		lastProductSelIDForCombi = lastProductSelID;
		hidePropTBButtons();
		prop_tb.showItem('combi_selectall');
		<?php if(version_compare(_PS_VERSION_, '1.5.0.1', '>=')) { ?>
		prop_tb.showItem('combi_sortByPosition');
		prop_tb.setItemState("combi_sortByPosition", $.cookie('combi_sort_by_position'));
		<?php } ?>
		<?php if(_r("ACT_CAT_DELETE_PRODUCT_COMBI")) { ?>
		prop_tb.showItem('combi_del');
		<?php } ?>
		<?php if(_r("ACT_CAT_ADD_PRODUCT_COMBI")) { ?>
		prop_tb.showItem('combi_psautocreate');
		prop_tb.showItem('combi_add');
		prop_tb.showItem('combi_add_input');
		<?php } ?>
		prop_tb.showItem('combi_win_attributes');
		if (isIPAD){
			prop_tb.showItem('combi_lightNavigation');
		}
		prop_tb.showItem('combi_refresh');
		prop_tb.showItem('combi_filters');
		<?php if(_r("ACT_CAT_FAST_EXPORT")) { ?>
		prop_tb.showItem('combi_exportcsv');
		<?php } ?>
		prop_tb.setItemText('panel', '<?php echo _l('Combinations',1)?>');
		prop_tb.setItemImage('panel', 'lib/img/combinations.gif');
		needInitCombinations=1;
		initCombinations();
		needInitCombinationsImage=1;
		initCombinationImage();
		propertiesPanel='combinations';
		if (lastProductSelID!=0)
		{
			displayCombinations();
		}
	}
	
	function hideSubpropertiesItems()
	{
		prop_tb.combi_subproperties_tb.forEachItem(function(itemId){
	        if(itemId!="combiSubProperties")
	        	prop_tb.combi_subproperties_tb.hideItem(itemId);
	    });
	}
	// CALLBACK FUNCTION
	function callbackCombinationsGrid(sid,action,tid)
	{
		if(propertiesPanel=='combinations')
		{
			if (action=='insert')
			{
				var nbCols = prop_tb._combinationsGrid.getColumnsNum();
				for(i=0 ; i < nbCols ; i++)
				{
					colIDTab=prop_tb._combinationsGrid.getColumnId(i).split('_');
					if (colIDTab.length == 3 && colIDTab[0] == 'attr')
					{
						prop_tb._combinationsLayout.cells('a').progressOff();
						idxCombiID=prop_tb._combinationsGrid.getColIndexById('id_product_attribute');
						prop_tb._combinationsGrid.cells(sid,idxCombiID).setValue(tid);
						prop_tb._combinationsGrid.changeRowId(sid,tid);
						prop_tb._combinationsGrid.setRowHidden(tid, false);
						prop_tb._combinationsGrid.showRow(tid);
						break ;
					}
				}
			}
			if (action=='update')
			{
				prop_tb._combinationsGrid.setRowTextNormal(sid);
				<?php if (!_s('CAT_PROD_COMBI_METHOD')) { ?>
				var nbCols = prop_tb._combinationsGrid.getColumnsNum();
				var attr_ids = "-";
				for(i=0 ; i < nbCols ; i++)
				{
					colIDTab=prop_tb._combinationsGrid.getColumnId(i).split('_');
					if (colIDTab.length == 3 && colIDTab[0] == 'attr')
					{
						var temp_id = prop_tb._combinationsGrid.cells(sid,i).getValue();
						var temps = temp_id.split("|||");
						var tmp_name = temps[0];
						var tmp_id = temps[1];
						if(tmp_id!=undefined && tmp_id!=null && tmp_id!="" && tmp_id!=0)
							attr_ids = attr_ids+tmp_id+"-";
					}
				}
				prop_tb._combinationsGrid.setUserData(sid, "attr_ids", attr_ids);
				<?php } ?>
			}
			updateProductQuantity();
			colorExistCombi();
		}
	}


<?php
	}
?>
