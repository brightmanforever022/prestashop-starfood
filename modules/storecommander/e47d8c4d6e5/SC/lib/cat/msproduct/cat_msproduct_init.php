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

if(SCMS) { ?>
<?php /* ?>
<script>
<?php */  ?>
	
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_MB_PRODUCT")) { ?>
		prop_tb.addListOption('panel', 'msproduct', 12, "button", '<?php echo _l('Multistore : products information',1)?>', "lib/img/table_multiple.png");
		allowed_properties_panel[allowed_properties_panel.length] = "msproduct";
	<?php } ?>

	var opts = [['msfilters_reset', 'obj', '<?php echo _l('Reset filters')?>', ''],
		['separator1', 'sep', '', ''],
		['msfilters_cols_show', 'obj', '<?php echo _l('Show all columns')?>', ''],
		['msfilters_cols_hide', 'obj', '<?php echo _l('Hide all columns')?>', '']
	];
	prop_tb.addButtonSelect("msproduct_filters", 100, "", opts, "lib/img/filter.png", "lib/img/filter.png",false,true);
	prop_tb.setItemToolTip('msproduct_filters','<?php echo _l('Filter options', 1)?>');
	prop_tb.addButton("msproduct_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('msproduct_refresh','<?php echo _l('Refresh grid',1)?>');
	prop_tb.addButton("msproduct_selectall", 100, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	prop_tb.setItemToolTip('msproduct_selectall','<?php echo _l('Select all',1)?>');

	var marginMatrix_form = "";
	function calculMargin(rId)
	{
		if(prop_tb._msproductGrid.getColIndexById('margin')!=undefined && prop_tb._msproductGrid.getColIndexById('wholesale_price')!=undefined)
		{
			var formule = marginMatrix_form;
			
			idxPriceWithoutTaxes=prop_tb._msproductGrid.getColIndexById('price');
			idxPriceIncTaxes=prop_tb._msproductGrid.getColIndexById('price_inc_tax');
			idxWholeSalePrice=prop_tb._msproductGrid.getColIndexById('wholesale_price');
			idxMargin=prop_tb._msproductGrid.getColIndexById('margin');
			
			var price = prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).getValue();
			if(price==null || price=="")
				price = 0;
			formule = formule.replace("{price}",price)
							.replace("{price}",price)
							.replace("{price}",price);
						
			var price_inc_tax = prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).getValue();
			if(price_inc_tax==null || price_inc_tax=="")
				price_inc_tax = 0;	
			formule = formule.replace("{price_inc_tax}",price_inc_tax)
							.replace("{price_inc_tax}",price_inc_tax)
							.replace("{price_inc_tax}",price_inc_tax);
							
			var wholesale_price = prop_tb._msproductGrid.cells(rId,idxWholeSalePrice).getValue();
			if(wholesale_price==null || wholesale_price=="")
				wholesale_price = 0;	
			formule = formule.replace("{wholesale_price}",wholesale_price)
							.replace("{wholesale_price}",wholesale_price)
							.replace("{wholesale_price}",wholesale_price);
							
			if(wholesale_price>0 && price>0)
				var margin = eval(formule);
			else
				var margin = 0;
			prop_tb._msproductGrid.cells(rId,idxMargin).setValue(priceFormat(margin));
	
			<?php if (_s('CAT_PROD_GRID_MARGIN_COLOR')!='') { ?>
			if (idxMargin)
			{
				var rules=('<?php echo str_replace("'","",_s('CAT_PROD_GRID_MARGIN_COLOR'));?>').split(';');
				for(var i=(rules.length-1) ; i >= 0 ; i--){
					var rule=rules[i].split(':');
					if ( Number(prop_tb._msproductGrid.cells(rId,idxMargin).getValue()) < Number(rule[0])){
						prop_tb._msproductGrid.cells(rId,idxMargin).setBgColor(rule[1]);
						prop_tb._msproductGrid.cells(rId,idxMargin).setTextColor('#FFFFFF');
					}
				}
			}
			<?php } ?>
		}
	}
	
	function onEditCellMscproduct(stage,rId,cInd,nValue,oValue)
	{
		idxShop=prop_tb._msproductGrid.getColIndexById('id_shop');
		idxASM=prop_tb._msproductGrid.getColIndexById('advanced_stock_management');
		if(cInd==idxShop || cInd==idxASM)
			return false;
			
		if (stage==2)
		{
			nValue = prop_tb._msproductGrid.cells(rId,cInd).getValue();
		
			idxProductName=prop_tb._msproductGrid.getColIndexById('name');
			if (cInd == idxProductName){
				<?php if (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && !_s("CAT_SEO_NAME_TO_URL") && _s("CAT_NOTICE_UPDATE_PRODUCT_URL_REWRITE")) { ?>
					dhtmlx.message({text:'<?php echo _l('Caution: The option located in Prestashop > Products > Force update of friendly URL is set to NO: friendly url will not be saved automatically. To stop this alert: SC  > Tools > Settings > Alert.',1)?>',type:'error',expire:15000});
				<?php } ?>
			}
			
			idxLinkRewrite=prop_tb._msproductGrid.getColIndexById('link_rewrite');
			if (cInd == idxLinkRewrite){
				<?php 
				$accented = Configuration::get('PS_ALLOW_ACCENTED_CHARS_URL');
				if($accented==1) {	?>
					prop_tb._msproductGrid.cells(rId,idxLinkRewrite).setValue(getAccentedLinkRewriteFromString(prop_tb._msproductGrid.cells(rId,idxLinkRewrite).getValue().substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));
				<?php } else { ?>
					prop_tb._msproductGrid.cells(rId,idxLinkRewrite).setValue(getLinkRewriteFromString(prop_tb._msproductGrid.cells(rId,idxLinkRewrite).getValue().substr(0,<?php echo _s('CAT_LINK_REWRITE_SIZE')?>)));
				<?php } ?>
			}
		
			idxRef=prop_tb._msproductGrid.getColIndexById('reference');
			if (cInd == idxRef)
			{
			 	var splitted = rId.split("_");
				var product_id = splitted[0];
				if(product_id!=null && product_id!=undefined)
				{
					prop_tb._msproductGrid.forEachRow(function(id){
						var temp_id = "_"+id;
						if(temp_id.search("_"+product_id+"_")>=0)
						{
							prop_tb._msproductGrid.cells(id,idxRef).setValue(nValue);
						}
				   });
				}
			}
		
			idxRefSupplier=prop_tb._msproductGrid.getColIndexById('supplier_reference');
			if (cInd == idxRefSupplier)
			{
			 	var splitted = rId.split("_");
				var product_id = splitted[0];
				if(product_id!=null && product_id!=undefined)
				{
					prop_tb._msproductGrid.forEachRow(function(id){
						var temp_id = "_"+id;
						if(temp_id.search("_"+product_id+"_")>=0)
						{
							prop_tb._msproductGrid.cells(id,idxRefSupplier).setValue(nValue);
						}
				   });
				}
			}
			
			idxPriceWithoutTaxes=prop_tb._msproductGrid.getColIndexById('price');
			idxPriceIncTaxes=prop_tb._msproductGrid.getColIndexById('price_inc_tax');
			idxEcotax=prop_tb._msproductGrid.getColIndexById('ecotax');
			idxWholeSalePrice=prop_tb._msproductGrid.getColIndexById('wholesale_price');
			idxVAT=prop_tb._msproductGrid.getColIndexById(tax_identifier);
			if ((cInd == idxPriceWithoutTaxes)){ //Price
				var vat = tax_values[prop_tb._msproductGrid.cells(rId,idxVAT).getTitle()]*1;
				var pwt = noComma(nValue);
				var eco = 0;
				if (idxEcotax && EcotaxTaxRate!=-1)
					eco = prop_tb._msproductGrid.cells(rId,idxEcotax).getValue()*1;
				var pit = vat * pwt + eco;
				if (idxWholeSalePrice)
				{
					var wholesaleprice = prop_tb._msproductGrid.cells(rId,idxWholeSalePrice).getValue();
				<?php
					if (_s('CAT_NOTICE_WHOLESALEPRICEHIGHER'))
					{
				?>

					if (wholesaleprice > pwt)
						dhtmlx.message({text:'<?php echo _l('Alert: wholesale price higher than sell price!',1)?>',type:'error'});
				<?php
					}
				?>
				}
				prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).setValue(priceFormat(pwt));
				prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).setValue(priceFormat(pit));
				calculMargin(rId);
			}
			if ((cInd ==idxVAT )){ //VAT
				var vat = tax_values[prop_tb._msproductGrid.cells(rId,idxVAT).getTitle()]*1;
				var pwt = noComma(prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).getValue());
				var eco = 0;
				if (idxEcotax)
					eco = prop_tb._msproductGrid.cells(rId,idxEcotax).getValue()*1;
				var pit = vat * pwt + eco;
				if (idxWholeSalePrice)
				{
					var wholesaleprice = prop_tb._msproductGrid.cells(rId,idxWholeSalePrice).getValue();
				<?php
					if (_s('CAT_NOTICE_WHOLESALEPRICEHIGHER'))
					{
					?>
					if (wholesaleprice > pwt)
						dhtmlx.message({text:'<?php echo _l('Alert: wholesale price higher than sell price!',1)?>',type:'error'});
				<?php
					}
				?>
				}
				prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).setValue(priceFormat(pwt));
				prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).setValue(priceFormat(pit));
				calculMargin(rId);
			}
			if ((cInd == idxPriceIncTaxes)){ //Price including taxes
				var vat = tax_values[prop_tb._msproductGrid.cells(rId,idxVAT).getTitle()]*1;
				var pit = noComma(nValue);
				prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).setValue(pit);
				var eco = 0;
				if (idxEcotax)
					eco = prop_tb._msproductGrid.cells(rId,idxEcotax).getValue()*1;
				var newpwt = (pit-eco) / vat;
				if (idxWholeSalePrice)
				{
					var wholesaleprice = prop_tb._msproductGrid.cells(rId,idxWholeSalePrice).getValue()*1;
				<?php
					if (_s('CAT_NOTICE_WHOLESALEPRICEHIGHER'))
					{
				?>
					if (wholesaleprice > newpwt)
						dhtmlx.message({text:'<?php echo _l('Alert: wholesale price higher than sell price!',1)?>',type:'error'});
				<?php
					}
				?>
				}
				prop_tb._msproductGrid.setUserData(rId, 'tax', vat);
				if (idxEcotax)
					prop_tb._msproductGrid.setUserData(rId, 'ecotax', eco);
				prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).setValue(priceFormat(pit));
				prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).setValue(priceFormat(newpwt));
				calculMargin(rId);
			}
			if ((cInd == idxEcotax)){ //EcoTax
				var vat = tax_values[prop_tb._msproductGrid.cells(rId,idxVAT).getTitle()]*1;
				var pwt = noComma(prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).getValue());
				var eco = noComma(nValue);
				var pit = noComma(prop_tb._msproductGrid.cells(rId,idxPriceIncTaxes).getValue());
				var newpwt = (pit-eco) / vat;
				prop_tb._msproductGrid.setUserData(rId, 'tax', vat);
				prop_tb._msproductGrid.setUserData(rId, 'price_inc_tax', pit);
				prop_tb._msproductGrid.cells(rId,idxEcotax).setValue(priceFormat(nValue));
				prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).setValue(priceFormat(newpwt));
				calculMargin(rId);
			}
			if (cInd == idxWholeSalePrice){ //Wholesale price
				var pwt = prop_tb._msproductGrid.cells(rId,idxPriceWithoutTaxes).getValue()*1;
				var wholesaleprice = noComma(nValue);
				prop_tb._msproductGrid.cells(rId,idxWholeSalePrice).setValue(priceFormat<?php echo (_s('CAT_PROD_WHOLESALEPRICE4DEC')?'4Dec':'');?>(wholesaleprice));
			<?php
				if (_s('CAT_NOTICE_WHOLESALEPRICEHIGHER'))
				{
			?>
				if (wholesaleprice > pwt)
						dhtmlx.message({text:'<?php echo _l('Alert: wholesale price higher than sell price!',1)?>',type:'error'});
			<?php
				}
			?>
				calculMargin(rId);
			}
			<?php sc_ext::readCustomMsProductGridConfigXML('onEditCell'); ?>
			if(nValue!=oValue)
			{
				<?php sc_ext::readCustomMsProductGridConfigXML('onBeforeUpdate'); ?>
				addMsProductInQueue(rId, "update", cInd);
			}
		}
			
		return true;
	}
	
	clipboardType_Msproduct = null;	
	needInitMsproduct = 1;
	function initMsproduct()
	{
		if (needInitMsproduct)
		{
			prop_tb._msproductLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._msproductLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();

			prop_tb._msproductGrid = prop_tb._msproductLayout.cells('a').attachGrid();
			prop_tb._msproductGrid._name='_msproductGrid';
			prop_tb._msproductGrid.setImagePath("lib/js/imgs/");
  			prop_tb._msproductGrid.enableDragAndDrop(false);
			prop_tb._msproductGrid.enableMultiselect(true);
			/*prop_tb._msproductGrid.enableAutoSaving('cg_cat_msproduct',"expires=Fri, 31-Dec-2021 23:59:59 GMT");
			prop_tb._msproductGrid.enableAutoHiddenColumnsSaving('cg_cat_msproduct_col',"expires=Fri, 31-Dec-2021 23:59:59 GMT");*/
			
			// UISettings
			prop_tb._msproductGrid._uisettings_prefix='cat_msproduct';
			prop_tb._msproductGrid._uisettings_name=prop_tb._msproductGrid._uisettings_prefix;
		   	prop_tb._msproductGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._msproductGrid);
			
			prop_tb._msproductGrid.attachEvent("onEditCell",onEditCellMscproduct);
			
			prop_tb._msproductGrid.attachEvent("onScroll",function(){
				marginMatrix_form = prop_tb._msproductGrid.getUserData("", "marginMatrix_form");
			   	prop_tb._msproductGrid.forEachRow(function(id){
			      calculMargin(id);
			   });
			});
			
			prop_tb._msproductGrid.attachEvent("onDhxCalendarCreated",function(calendar){
				calendar.hideTime();
				calendar.setSensitiveRange("2012-01-01",null);
				
				dhtmlXCalendarObject.prototype.langData["<?php echo $user_lang_iso;?>"] = <?php echo _l('{
		dateformat: "%Y-%m-%d",
		monthesFNames: ["January","February","March","April","May","June","July","August","September","October","November","December"],
		monthesSNames: ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"],
		daysFNames: ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"],
		daysSNames: ["Su","Mo","Tu","We","Th","Fr","Sa"],
		weekstart: 1
	}')?>;
			calendar.loadUserLanguage("<?php echo $user_lang_iso;?>");
			});
			
			/*groupsDataProcessor_msproductURLBase="index.php?ajax=1&act=cat_msproduct_update&id_lang="+SC_ID_LANG+"&"+new Date();
			groupsDataProcessor_msproduct = new dataProcessor(groupsDataProcessor_msproductURLBase);
			groupsDataProcessor_msproduct.enableDataNames(true);
			groupsDataProcessor_msproduct.enablePartialDataSend(false);
			groupsDataProcessor_msproduct.setUpdateMode('cell');
			groupsDataProcessor_msproduct.attachEvent("onAfterUpdate",function(sid,action,tid,xml_node){
					
					idxShop=prop_tb._msproductGrid.getColIndexById('id_shop');
					var shopId = prop_tb._msproductGrid.cells(sid,idxShop).getValue();
					if(shopId==shopselection)
						displayProducts();
			
					return true;
				});
			groupsDataProcessor_msproduct.init(prop_tb._msproductGrid); */
			
			
			// Context menu for MultiShops Info Product grid
			msproduct_cmenu=new dhtmlXMenuObject();
			msproduct_cmenu.renderAsContextMenu();
			function onGridMsproductContextButtonClick(itemId){
				tabId=prop_tb._msproductGrid.contextID.split('_');
				tabId=tabId[0]+"_"+tabId[1];
				if (itemId=="copy"){
					if (lastColumnRightClicked_Msproduct!=0)
					{
						clipboardValue_Msproduct=prop_tb._msproductGrid.cells(tabId,lastColumnRightClicked_Msproduct).getValue();
						msproduct_cmenu.setItemText('paste' , '<?php echo _l('Paste')?> '+prop_tb._msproductGrid.cells(tabId,lastColumnRightClicked_Msproduct).getTitle());
						clipboardType_Msproduct=lastColumnRightClicked_Msproduct;
					}
				}
				if (itemId=="paste"){
					if (lastColumnRightClicked_Msproduct!=0 && clipboardValue_Msproduct!=null && clipboardType_Msproduct==lastColumnRightClicked_Msproduct)
					{
						selection=prop_tb._msproductGrid.getSelectedRowId();
						if (selection!='' && selection!=null)
						{
							selArray=selection.split(',');
							for(i=0 ; i < selArray.length ; i++)
							{
								if (prop_tb._msproductGrid.getColumnId(lastColumnRightClicked_Msproduct).substr(0,5)!='attr_')
								{
									prop_tb._msproductGrid.cells(selArray[i],lastColumnRightClicked_Msproduct).setValue(clipboardValue_Msproduct);
									prop_tb._msproductGrid.cells(selArray[i],lastColumnRightClicked_Msproduct).cell.wasChanged=true;
									onEditCellMscproduct(2,selArray[i],lastColumnRightClicked_Msproduct);
									//groupsDataProcessor_msproduct.setUpdated(selArray[i],true,"updated");
								}
							}
						}
					}
				}
			}
			msproduct_cmenu.attachEvent("onClick", onGridMsproductContextButtonClick);
			var contextMenuXML='<menu absolutePosition="auto" mode="popup" maxItems="8"  globalCss="contextMenu" globalSecondCss="contextMenu" globalTextCss="contextMenuItem">'+
					'<item text="Object" id="object" enabled="false"/>'+
					'<item text="Shop" id="shop" enabled="false"/>'+
					'<item text="<?php echo _l('Copy')?>" id="copy"/>'+
					'<item text="<?php echo _l('Paste')?>" id="paste"/>'+
				'</menu>';
			msproduct_cmenu.loadStruct(contextMenuXML);
			prop_tb._msproductGrid.enableContextMenu(msproduct_cmenu);

			prop_tb._msproductGrid.attachEvent("onBeforeContextMenu", function(rowid,colidx,grid){
				var disableOnCols=new Array(
						prop_tb._msproductGrid.getColIndexById('id_product'),
						prop_tb._msproductGrid.getColIndexById('reference'),
						prop_tb._msproductGrid.getColIndexById('id_shop')
						);
				if (in_array(colidx,disableOnCols))
				{
					return false;
				}
				lastColumnRightClicked_Msproduct=colidx;
				msproduct_cmenu.setItemText('object', '<?php echo _l('Product:')?> '+prop_tb._msproductGrid.cells(rowid,prop_tb._msproductGrid.getColIndexById('name')).getTitle());
				msproduct_cmenu.setItemText('shop', '<?php echo _l('Shop:')?> '+prop_tb._msproductGrid.cells(rowid,prop_tb._msproductGrid.getColIndexById('id_shop')).getTitle());
				if (lastColumnRightClicked_Msproduct==clipboardType_Msproduct)
				{
					msproduct_cmenu.setItemEnabled('paste');
				}else{
					msproduct_cmenu.setItemDisabled('paste');
				}
				return true;
			});
			
			needInitMsproduct=0;
		}
	}

	function setPropertiesPanel_msproduct(id){
		if (id=='msproduct')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('msproduct_filters');
			prop_tb.showItem('msproduct_refresh');
			prop_tb.showItem('msproduct_selectall');
			prop_tb.setItemText('panel', '<?php echo _l('Multistore : products information',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/table_multiple.png');
			needInitMsproduct = 1;
			initMsproduct();
			propertiesPanel='msproduct';
			if (lastProductSelID!=0)
			{
				displayMsproduct();
			}
		}
		if (id=='msfilters_reset')
		{
			for(var i=0,l=prop_tb._msproductGrid.getColumnsNum();i<l;i++)
			{
				if (prop_tb._msproductGrid.getFilterElement(i)!=null) prop_tb._msproductGrid.getFilterElement(i).value="";
			}
			prop_tb._msproductGrid.filterByAll();
		}
		if (id=='msfilters_cols_show')
		{
			for(i=0,l=prop_tb._msproductGrid.getColumnsNum() ; i < l ; i++)
			{
				prop_tb._msproductGrid.setColumnHidden(i,false);
			}
		}
		if (id=='msfilters_cols_hide')
		{
			idxProductID=prop_tb._msproductGrid.getColIndexById('id');
			idxProductName=prop_tb._msproductGrid.getColIndexById('name');
			idxProductReference=prop_tb._msproductGrid.getColIndexById('reference');
			for(i=1, l=prop_tb._msproductGrid.getColumnsNum(); i < l ; i++)
			{
				if (i!=idxProductID && i!=idxProductName && i!=idxProductReference)
				{
					prop_tb._msproductGrid.setColumnHidden(i,true);
				}else{
					prop_tb._msproductGrid.setColumnHidden(i,false);
				}
			}
		}
		if (id=='msproduct_refresh')
		{
			displayMsproduct();
		}
		if (id=='msproduct_selectall')
		{
			prop_tb._msproductGrid.enableSmartRendering(false);
			prop_tb._msproductGrid.selectAll();
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_msproduct);

	function displayMsproduct(reloadJustChecbox)
	{
		prop_tb._msproductGrid.clearAll(true);
		prop_tb._msproductGrid.loadXML("index.php?ajax=1&act=cat_msproduct_get"+(cat_grid.getSelectedRowId()!=null?"&idlist="+cat_grid.getSelectedRowId():"")+"&id_lang="+SC_ID_LANG+"&"+new Date().getTime(),function()
		{
			nb=prop_tb._msproductGrid.getRowsNum();
			prop_tb._msproductGrid._rowsNum=nb;
			
   			// UISettings
			loadGridUISettings(prop_tb._msproductGrid);
			prop_tb._msproductGrid._first_loading=0;
	    	
	    	marginMatrix_form = prop_tb._msproductGrid.getUserData("", "marginMatrix_form");
		   	prop_tb._msproductGrid.forEachRow(function(id){
			      calculMargin(id);
			   });
			   
			 <?php sc_ext::readCustomMsProductGridConfigXML('afterGetRows'); ?>
		});
	}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='msproduct'){
				//initMsproduct();
				displayMsproduct(false);
			}
		});
		

function addMsProductInQueue(rId, action, cIn)
{
	var params = {
		name: "cat_msproduct_update_queue",
		row: rId,
		action: "update",
		params: {},
		callback: "callbackMsProduct('"+rId+"','update','"+rId+"');"
	};
	// COLUMN VALUES
		params.params["id_lang"] = SC_ID_LANG;	
		prop_tb._msproductGrid.forEachCell(rId,function(cellObj,ind){
			params.params[prop_tb._msproductGrid.getColumnId(ind)] = prop_tb._msproductGrid.cells(rId,ind).getValue();
		});
	// USER DATA
		if(rId!=undefined && rId!=null && rId!="" && rId!=0)
		{
			if(prop_tb._msproductGrid.UserData[rId]!=undefined && prop_tb._msproductGrid.UserData[rId]!=null && prop_tb._msproductGrid.UserData[rId]!="")
			{
				$.each(prop_tb._msproductGrid.UserData[rId].keys, function(i, key){
					params.params[key] = prop_tb._msproductGrid.UserData[rId].values[i];
				});
			}
		}
		if(prop_tb._msproductGrid.UserData.gridglobaluserdata.keys!=undefined && prop_tb._msproductGrid.UserData.gridglobaluserdata.keys!=null && prop_tb._msproductGrid.UserData.gridglobaluserdata.keys!="")
		{
			$.each(prop_tb._msproductGrid.UserData.gridglobaluserdata.keys, function(i, key){
				params.params[key] = prop_tb._msproductGrid.UserData.gridglobaluserdata.values[i];
			});
		}
	
	params.params = JSON.stringify(params.params);
	addInUpdateQueue(params,prop_tb._msproductGrid);
}
		
// CALLBACK FUNCTION
function callbackMsProduct(sid,action,tid)
{
	<?php sc_ext::readCustomMsProductGridConfigXML('onAfterUpdate'); ?>
	if (action=='update')
	{
		prop_tb._msproductGrid.setRowTextNormal(sid);
	}
	
	idxShop=prop_tb._msproductGrid.getColIndexById('id_shop');
	var shopId = prop_tb._msproductGrid.cells(sid,idxShop).getValue();
	if(shopId==shopselection)
		displayProducts();
}
		
<?php /* ?>
</script>
<?php */  ?>
<?php } ?>
