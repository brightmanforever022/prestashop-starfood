
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
?>

<?php if(_r("GRI_CAT_PROPERTIES_GRID_SORT")) { ?>
	prop_tb.addListOption('panel', 'productsort', 15, "button", '<?php echo _l('Products position',1)?>', "lib/img/layers.png");
	allowed_properties_panel[allowed_properties_panel.length] = "productsort";
<?php } ?>

prop_tb.addButton("productsort_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
prop_tb.setItemToolTip('productsort_refresh','<?php echo _l('Refresh grid',1)?>');
prop_tb.addButton("productsort_setposition", 100, "", "lib/img/layers.png", "lib/img/layers_dis.png");
prop_tb.setItemToolTip('productsort_setposition','<?php echo _l('Save positions',1)?>');

needInitProductsorts = 1;
function initProductsorts(){
	if (needInitProductsorts)
	{
		prop_tb._productsortsLayout = dhxLayout.cells('b').attachLayout('1C');
		prop_tb._productsortsLayout.cells('a').hideHeader();
		dhxLayout.cells('b').showHeader();
		prop_tb._productsortsGrid = prop_tb._productsortsLayout.cells('a').attachGrid();
		prop_tb._productsortsGrid._name='productsort';
		prop_tb._productsortsGrid.setImagePath("lib/js/imgs/");
		prop_tb._productsortsGrid.enableDragAndDrop(true);
		prop_tb._productsortsGrid.setDragBehavior('child');
		
		// UISettings
		prop_tb._productsortsGrid._uisettings_prefix='cat_productsort';
		prop_tb._productsortsGrid._uisettings_name=prop_tb._productsortsGrid._uisettings_prefix;
	   	prop_tb._productsortsGrid._first_loading=1;
	   	
		// UISettings
		initGridUISettings(prop_tb._productsortsGrid);
		
		prop_tb._productsortsGrid.attachEvent("onBeforeSorting", function(ind,type,direction){
		    return false;
		});
		
		prop_tb._productsortsGrid.attachEvent("onDrag", function(sId,tId,sObj,tObj){
		    if(sObj._name!='productsort' && sObj._name!='grid')
				 return false;
				 
			if(sId.search(",")>=0)
			{
				var ids = sId.split(",").reverse();
				$.each(ids, function(num, id){
					if(id!="" && id!=null && id!=0)
						prop_tb._productsortsGrid.moveRow(id,"row_sibling",tId,prop_tb._productsortsGrid);
				});
			}
			else
				prop_tb._productsortsGrid.moveRow(sId,"row_sibling",tId,prop_tb._productsortsGrid);
			
			return false;
		});
		/*prop_tb._productsortsGrid.attachEvent("onDrop", function(sId,tId,beforeId,sObj,tObj){
			console.log(sObj._name);
		    if(sObj._name!='productsort' && sObj._name!='grid')
				 return false;
			console.log("test");
			return false;
		});*/
		needInitProductsorts = 0;
	}
}

function setPropertiesPanel_productsort(id){
		if (id=='productsort')
		{
			dhxLayout.cells('b').setText('<?php echo _l('Products position',1);?>');
			
			hidePropTBButtons();
			prop_tb.showItem('productsort_setposition');
			prop_tb.showItem('productsort_refresh');
			prop_tb.setItemText('panel', '<?php echo _l('Products position',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/layers.png');
			needInitProductsorts = 1;
			initProductsorts();
			propertiesPanel='productsort';
			displayProductsorts();
		}
		if (id=='productsort_refresh'){
			displayProductsorts();
		}
		if (id=='productsort_setposition'){
			if (prop_tb._productsortsGrid.getRowsNum()>0 && catselection!=0)
			{
				var positions='';
				var idx=0;
				var i = 1 ;
				prop_tb._productsortsGrid.forEachRow(function(id){
						positions+=id+','+prop_tb._productsortsGrid.getRowIndex(id)+';';
						idx++;
					});
				$.post("index.php?ajax=1&act=cat_productsort_update&action=position&"+new Date().getTime(),{ id_category: catselection, positions: positions },function(){
					idxPosition=prop_tb._productsortsGrid.getColIndexById('position');
					prop_tb._productsortsGrid.forEachRow(function(id){
						prop_tb._productsortsGrid.cells(id, idxPosition).setValue(String(prop_tb._productsortsGrid.getRowIndex(id)));
					});
					displayProducts();
				});
			}
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_productsort);



function displayProductsorts(callback)
{
	prop_tb._productsortsGrid.clearAll(true);
	if(catselection!=null && catselection!="" && catselection!=0)
	{
		prop_tb._productsortsGrid.loadXML("index.php?ajax=1&act=cat_productsort_get&id_category="+catselection+"&id_lang="+SC_ID_LANG,function()
		{
			nb=prop_tb._productsortsGrid.getRowsNum();
			prop_tb._sb.setText(nb+(nb>1?" <?php echo _l('products')?>":" <?php echo _l('product')?>"));
		
			<?php sc_ext::readCustomProductsortGridConfigXML('afterGetRows'); ?>
		
   			// UISettings
			loadGridUISettings(prop_tb._productsortsGrid);
			prop_tb._productsortsGrid._first_loading=0;
			
	    	if (callback!='') eval(callback);
		});
	}
}


	/*cat_grid.attachEvent("onRowSelect",function (idproduct){
			if (propertiesPanel=='productsort'){
				
				initProductsorts();
				displayProductsorts();
			}
		});*/
	cat_tree.attachEvent("onClick",function(idcategory){
		 if (propertiesPanel=='productsort'){
			//initProductsorts();
			displayProductsorts();
		}
	});
