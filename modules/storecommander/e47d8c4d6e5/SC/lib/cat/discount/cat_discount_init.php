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
	if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
	{
?>
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_DISCOUNT")) { ?>
		prop_tb.addListOption('panel', 'discounts', 6, "button", '<?php echo _l('Quantity discounts',1)?>', "lib/img/text_list_numbers.png");
		allowed_properties_panel[allowed_properties_panel.length] = "discounts";
	<?php } ?>
	


	prop_tb.addButton('discount_add',100,'','lib/img/add.png','lib/img/add.png');
	prop_tb.setItemToolTip('discount_add','<?php echo _l('Create new quantity discount',1)?>');
	prop_tb.addButton('discount_del',100,'','lib/img/delete.gif','lib/img/delete.gif');
	prop_tb.setItemToolTip('discount_del','<?php echo _l('Delete selected item',1)?>');




	needinitDiscounts = 1;
	function initDiscounts(){
		if (needinitDiscounts)
		{
			prop_tb._discountsLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._discountsLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			prop_tb._discountsGrid = prop_tb._discountsLayout.cells('a').attachGrid();
			prop_tb._discountsGrid.setImagePath("lib/js/imgs/");
			
			// UISettings
			prop_tb._discountsGrid._uisettings_prefix='cat_discount';
			prop_tb._discountsGrid._uisettings_name=prop_tb._discountsGrid._uisettings_prefix;
		   	prop_tb._discountsGrid._first_loading=1;
		   	
			// UISettings
			initGridUISettings(prop_tb._discountsGrid);
			
			prop_tb._discountsGrid.attachEvent("onEditCell", function(stage, rId, cIn){
					if (stage==1 && this.editor && this.editor.obj) this.editor.obj.select();
					return true;
				});
			discountsDataProcessorURLBase="index.php?ajax=1&act=cat_discount_update&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG;
			discountsDataProcessor = new dataProcessor(discountsDataProcessorURLBase);
			discountsDataProcessor.enableDataNames(true);
			discountsDataProcessor.enablePartialDataSend(false);
			discountsDataProcessor.setTransactionMode("GET");
			discountsDataProcessor.setUpdateMode('cell',true);
			discountsDataProcessor.attachEvent("onAfterUpdate",function(sid,action,tid,xml){
					if (action=='insert')
						prop_tb._discountsGrid.cells(tid,0).setValue(tid);
				});
			discountsDataProcessor.serverProcessor=discountsDataProcessorURLBase;
			discountsDataProcessor.init(prop_tb._discountsGrid);

			needinitDiscounts=0;
		}
	}




	function setPropertiesPanel_discounts(id){
		if (id=='discounts')
		{
			if(lastProductSelID!=undefined && lastProductSelID!="")
			{
				idxProductName=cat_grid.getColIndexById('name');
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
			}
			hidePropTBButtons();
			prop_tb.showItem('discount_del');
			prop_tb.showItem('discount_add');
			prop_tb.setItemText('panel', '<?php echo _l('Quantity discounts',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/text_list_numbers.png');
			needinitDiscounts = 1;
			initDiscounts();
			propertiesPanel='discounts';
			if (lastProductSelID!=0)
			{
				displayDiscounts();
			}
		}
		if (id=='discount_add')
		{
			if (lastProductSelID==0){
				alert('<?php echo _l('Please select a product',1)?>');
			}else{
				var newId = new Date().getTime();
				discountsDataProcessorURLBase="index.php?ajax=1&act=cat_discount_update&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG;
				discountsDataProcessor.serverProcessor=discountsDataProcessorURLBase;
				var maxQuantity=1;
				var maxValue=10;
				var percent='';
				prop_tb._discountsGrid.forEachRow(function(id){
						if (percent=='' && String(prop_tb._discountsGrid.cells(id,2).getValue()).indexOf('%')!=-1) percent='%';
						maxQuantity=Math.max(maxQuantity,prop_tb._discountsGrid.cells(id,1).getValue()*1+1);
						maxValue=Math.max(maxValue,String(prop_tb._discountsGrid.cells(id,2).getValue()).replace('%','')*1+1);
					});
				newRow=new Array(newId,maxQuantity,maxValue+percent);
				prop_tb._discountsGrid.addRow(newId,newRow);
			}
		}
		if (id=='discount_del')
		{
			if (prop_tb._discountsGrid.getSelectedRowId()==null)
			{
				alert('<?php echo _l('Please select an item',1)?>');
			}else{
				if (lastProductSelID!=0)
				{
					if (confirm('<?php echo _l('Are you sure you want to delete the selected items?',1)?>'))
					{
						prop_tb._discountsGrid.deleteSelectedRows();
					}
				}else{
					alert('<?php echo _l('Please select a product',1)?>');
				}
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_discounts);



function displayDiscounts()
{
	prop_tb._discountsGrid.clearAll(true);
	prop_tb._discountsGrid.loadXML("index.php?ajax=1&act=cat_discount_get&id_product="+lastProductSelID+"&id_lang="+SC_ID_LANG,function()
			{
				nb=prop_tb._discountsGrid.getRowsNum();
				prop_tb._sb.setText('');
				
	    		// UISettings
				loadGridUISettings(prop_tb._discountsGrid);
				
				// UISettings
				prop_tb._discountsGrid._first_loading=0;
			});
}



	cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='discounts'){
				displayDiscounts();
			}
		});

<?php
	}
