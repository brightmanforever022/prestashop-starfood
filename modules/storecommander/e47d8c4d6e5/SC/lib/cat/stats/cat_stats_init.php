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
	<?php if(_r("GRI_CAT_PROPERTIES_GRID_STATS")) { ?>
		prop_tb.addListOption('panel', 'stats', 2, "button", '<?php echo _l('Stats',1)?>', "lib/img/chart_curve.png");
		allowed_properties_panel[allowed_properties_panel.length] = "stats";
	<?php } ?>

	prop_tb.addButton("stats_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('stats_refresh','<?php echo _l('Refresh',1)?>');
    var options_stats_view = [
        ['product_quantity', 'obj', '<?php echo _l('Total amount of products sold')?>', ''],
        ['product_total_price', 'obj', '<?php echo _l('Total sales tax excl.')?>', '']
    ]
    prop_tb.addButtonSelect('options_view',100,'<?php echo _l('Total amount of products sold')?>',options_stats_view,'lib/img/flag_blue.png','lib/img/flag_blue.png',false,true);

	needInitStats = 1;
	function initStats(){
		if (needInitStats)
		{
			prop_tb._statsLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._statsLayout.cells('a').hideHeader();
			dhxLayout.cells('b').showHeader();
			needInitStats=0;
		}
	}

    var options_stats_view_selected = 0;

	function setPropertiesPanel_stats(id){
		if (id=='stats')
		{
			hidePropTBButtons();
			prop_tb.showItem('stats_refresh');
			prop_tb.showItem('options_view');
			prop_tb.setItemText('panel', '<?php echo _l('Stats',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/chart_curve.png');
			URLOptions='';
			if (lastProductSelID!=0) URLOptions='&id_product='+lastProductSelID+'&id_lang='+SC_ID_LANG;
			needInitStats = 1;
			initStats();
			propertiesPanel='stats';
			dhxLayout.cells('b').setWidth(680);//605
			displayStats();
		}
		if (id=='stats_refresh'){
			displayStats();
		}
		if (id=='product_quantity'){
            options_stats_view_selected = 0;
            displayStats();
		}
		if (id=='product_total_price'){
            options_stats_view_selected = 1;
			displayStats();
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_stats);

	cat_grid.attachEvent("onRowSelect",function (idproduct){
			lastProductSelID=idproduct;
			idxProductName=cat_grid.getColIndexById('name');
			if (propertiesPanel=='stats')
			{
				dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cat_grid.cells(lastProductSelID,idxProductName).getValue());
				displayStats();
			}

		});

	function displayStats(){
		dhxLayout.cells('b').attachURL('index.php?ajax=1&act=cat_stats_get&stat_view='+options_stats_view_selected+'&list_id_product='+cat_grid.getSelectedRowId(),true);
	}
