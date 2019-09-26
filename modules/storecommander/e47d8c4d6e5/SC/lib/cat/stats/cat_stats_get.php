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

<script>
		var myBarChart1, myBarChart2, myBarChart3;

		var chartData3 = [
<?php
    $view_total_price = (bool)Tools::getValue('stat_view',0);
	$list_id_product = Tools::getValue('list_id_product',0);


	for ($i = 30; $i >=0 ; $i--) {
	    $sum_sql = 'sum(od.product_quantity) AS total';
	    if(!empty($view_total_price)) {
            $sum_sql = 'sum(od.total_price_tax_excl) AS total';
        }
	 	$sql ="SELECT ".pSQL($sum_sql).", DAY(o.date_add) as day FROM ps_orders o
	 				LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order)
	 				WHERE o.valid = 1
	 				AND od.product_id IN (".pSQL($list_id_product).")
	 				AND DATE(o.date_add) = DATE_ADD(CURDATE(), INTERVAL -".(int)$i." DAY)
	 				AND od.id_shop IN (".pSQl(SCI::getSelectedShopActionList(true)).")";
	 	$result = Db::getInstance()->executeS($sql);
	 	echo '{ sales:"'.$result[0]['total'].'", days:"'.$result[0]['day'].'" },'."\n";
	}
?>
		];

		myBarChart3 =  new dhtmlXChart({
			view:"bar",
			container:"chart3",
			value:"#sales#",
			color:"#1515DD",
			width:30,
			radius:2,
			gradient:"rising",
			tooltip:{
				template:"#sales#"
			},
			xAxis:{
				template:"'#days#",
				title:"<?php echo _l('Last 30 days'); ?>"
			},
			yAxis:{
				title:"<?php echo _l('Sales').(!empty($view_total_price) ? ' ('._l('Total products excl. tax').')': ''); ?>"
			}
		});
		myBarChart3.parse(chartData3,"json");
</script>
<style>
    #all_charts{
        display: flex;
        flex-direction: column;
        -ms-flex-direction: column;
        -webkit-flex-direction: column;
        min-height: 100%;
        justify-content: space-between;
        -webkit-justify-content: space-between;
    }

    #all_charts .chart{
        height: 250px;
        display: block;
    }
</style>
<div id="all_charts">
<div id="chart3" class="chart"></div>


<script>
		
		var chartData = [
<?php
	$startYear = date('Y') - 2;
	$currentMonth = date('m');
	$output = '';
    $sum_sql = 'sum(od.product_quantity) AS total';
    if(!empty($view_total_price)) {
        $sum_sql = 'sum(od.total_price_tax_excl) AS total';
    }
	for ($i = $currentMonth; $i < 13; $i++) {
	 	$sql ="SELECT ".pSQL($sum_sql)." FROM ps_orders o
	 				LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order)
	 				WHERE o.valid = 1
	 				AND od.product_id IN (".pSQL($list_id_product).")
	 				AND YEAR(o.date_add) = ".(int)$startYear."
	 				AND MONTH(o.date_add) = ".(int)$i."
	 				AND od.id_shop IN (".pSQl(SCI::getSelectedShopActionList(true)).")";
	 	$result = Db::getInstance()->getValue($sql);
	 	echo '{ sales:"'.$result.'", year:"'.str_pad($i, 2, "0", STR_PAD_LEFT).'" },'."\n";
	}
	for ($i = 1; $i < 13; $i++) {
	 	$sql ="SELECT ".pSQL($sum_sql)." FROM ps_orders o
	 				LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order)
	 				WHERE o.valid = 1
	 				AND od.product_id IN (".pSQL($list_id_product).")
	 				AND YEAR(o.date_add) = ".(int)($startYear+1)."
	 				AND MONTH(o.date_add) = ".(int)$i."
	 				AND od.id_shop IN (".pSQl(SCI::getSelectedShopActionList(true)).")";
	 	$result = Db::getInstance()->getValue($sql);
	 	echo '{ sales:"'.$result.'", year:"'.str_pad($i, 2, "0", STR_PAD_LEFT).'" },'."\n";
	}
	for ($i = 1; $i < ($currentMonth+1); $i++) {
	 	$sql ="SELECT ".pSQL($sum_sql)." FROM ps_orders o
	 				LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order)
	 				WHERE o.valid = 1
	 				AND od.product_id IN (".pSQL($list_id_product).")
	 				AND YEAR(o.date_add) = ".(int)($startYear+2)."
	 				AND MONTH(o.date_add) = ".(int)$i."
	 				AND od.id_shop IN (".pSQl(SCI::getSelectedShopActionList(true)).")";
	 	$result = Db::getInstance()->getValue($sql);
	 	echo '{ sales:"'.$result.'", year:"'.str_pad($i, 2, "0", STR_PAD_LEFT).'" },'."\n";
	}
?>
		];
		myBarChart1 =  new dhtmlXChart({
			view:"bar",
			container:"chart1",
			value:"#sales#",
			color:"#DD1515",
			width:30,
			radius:6,
			gradient:"rising",
			tooltip:{
				template:"#sales#"
			},
			xAxis:{
				template:"'#year#",
				title:"<?php echo _l('Last 24 months'); ?>"
			},
			yAxis:{
				title:"<?php echo _l('Sales').(!empty($view_total_price) ? ' ('._l('Total products excl. tax').')': ''); ?>"
			}
		});
		myBarChart1.parse(chartData,"json");
	</script>

<div id="chart1" class="chart"></div>

<script>
		var chartData2 = [
<?php
	$startYear = date('Y') - 10;
	$currentMonth = date('m');
	$output = '';
    $sum_sql = 'sum(od.product_quantity) AS total';
    if(!empty($view_total_price)) {
        $sum_sql = 'sum(od.total_price_tax_excl) AS total';
    }
	for ($i = $startYear; $i <= $startYear+10; $i++) {
	 	$sql ="SELECT ".pSQL($sum_sql)." FROM ps_orders o
	 				LEFT JOIN ps_order_detail od ON (o.id_order = od.id_order)
	 				WHERE o.valid = 1
	 				AND od.product_id IN (".pSQL($list_id_product).")
	 				AND YEAR(o.date_add) = ".(int)$i."
	 				AND od.id_shop IN (".pSQl(SCI::getSelectedShopActionList(true)).")";
	 	$result = Db::getInstance()->getValue($sql);
	 	echo '{ sales:"'.$result.'", year:"'.$i.'" },'."\n";
	}
?>
		];

		myBarChart2 =  new dhtmlXChart({
			view:"bar",
			container:"chart2",
			value:"#sales#",
			color:"#15DD15",
			width:30,
			radius:6,
			gradient:"rising",
			tooltip:{
				template:"#sales#"
			},
			xAxis:{
				template:"'#year#",
				title:"<?php echo _l('Last 10 years'); ?>"
			},
			yAxis:{
				title:"<?php echo _l('Sales').(!empty($view_total_price) ? ' ('._l('Total products excl. tax').')': ''); ?>"
			}
		});
		myBarChart2.parse(chartData2,"json");
	</script>

<div id="chart2" class="chart"></div>
</div>



