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

$id_lang=intval(Tools::getValue('id_lang'));
$id_product=intval(Tools::getValue('id_product'), 0);
$id_product_attribute=intval(Tools::getValue('id_product_attribute'), 0);
$id_warehouse=intval(Tools::getValue('id_warehouse'), 0);
?>
<?php if(empty($id_product) || empty($id_warehouse)) { ?>
<script type="text/javascript">
wStockMvt.hide();
</script>
<?php die(); }
$product = new Product((int)$id_product, false, (int)$id_lang, (int)SCI::getSelectedShop());

$ref=$product->reference;
$ean=$product->ean13;
$upc=$product->upc;

$name="";
$name .= $product->name;
if(!empty($id_product_attribute))
{
	$sql_attr ="SELECT agl.name as gp, al.name
					FROM "._DB_PREFIX_."product_attribute_combination pac
						INNER JOIN "._DB_PREFIX_."attribute a ON pac.id_attribute = a.id_attribute
							INNER JOIN "._DB_PREFIX_."attribute_group_lang agl ON a.id_attribute_group = agl.id_attribute_group
						INNER JOIN "._DB_PREFIX_."attribute_lang al ON pac.id_attribute = al.id_attribute
					WHERE pac.id_product_attribute = '".$id_product_attribute."'
						AND agl.id_lang = '".$id_lang."'
						AND al.id_lang = '".$id_lang."'
					GROUP BY a.id_attribute
					ORDER BY agl.name";
	$res_attr = Db::getInstance()->executeS($sql_attr);
	foreach($res_attr as $attr)
	{
		if(!empty($attr["gp"]) && !empty($attr["name"]))
		{
			if(!empty($name))
				$name .= ", ";
			$name .= $attr["gp"]." : ".$attr["name"];
		}
	}
}

?>
<script type="text/javascript">
<?php if(!empty($_GET["subform"])) { ?>
dhxlStockMvt_transfert = dhxlStockMvt.cells('b');
<?php } else { ?>
dhxlStockMvt=wStockMvt.attachLayout("1C");
dhxlStockMvt_w = dhxlStockMvt.cells('a');
dhxlStockMvt_w.hideHeader();
dhxlStockMvt_transfert = dhxlStockMvt_w;
<?php } ?>

// FORM
/*
<input type="hidden" value="1" id="is_post" name="is_post">
<input type="hidden" value="1" id="id_product" name="id_product">
<input type="hidden" value="12" id="id_product_attribute" name="id_product_attribute">
<input type="hidden" value="e64b7856d21cafeef447145857152e16" id="check" name="check">
 */
formData = [{
	    type: "settings",
	    position: "label-left",
	    labelWidth: 160,
	    inputWidth: 160
	},
    { type: "fieldset", name: "data", label: "<?php echo _l('Transfer product from one warehouse to another',1)?>", inputWidth: "auto", list:[
			{
				type:"hidden", 
				name: 'is_post',
				value: '1'
			},
			{
				type:"hidden", 
				name: 'id_product',
				value: '<?php echo $id_product; ?>'
			},
			{
				type:"hidden", 
				name: 'id_product_attribute',
				value: '<?php echo $id_product_attribute; ?>'
			},
			{
				type:"hidden", 
				name: 'check',
				value: '<?php echo md5(_COOKIE_KEY_.$id_product.$id_product_attribute);?>'
			},
			{
				type:"input", 
				name: 'reference', 
				label:'<?php echo _l('Product reference:',1)?>',
				disabled: true,
				value: '<?php echo $ref; ?>'
			},
			{
				type:"input", 
				name: 'ean13', 
				label:'<?php echo _l('EAN13:',1)?>',
				disabled: true,
				value: '<?php echo $ean; ?>'
			},
			{
				type:"input", 
				name: 'name', 
				label:'<?php echo _l('Name:',1)?>',
				disabled: true,
				value: '<?php echo str_replace("'", "\'", $name); ?>'
			},
			{
				type:"input", 
				name: 'quantity', 
				label:'<?php echo _l('Quantity to transfer:',1)?>',
				required: true,
				validate: "NotEmpty,ValidNumeric"
			},
		    {
		        type: "select",
				name: 'id_warehouse_from',
		        label: "<?php echo _l('Source Warehouse:',1)?>",
				required: true,
		       	tooltip: '<?php echo _l('Select the warehouse from which you want to transfer the product.',1)?>',
		 		options: [<?php 
		  		$shop = (int)SCI::getSelectedShop();
		  		if($shop == 0)
		  			$shop = null;

		  		$query = new DbQuery();
		  		$query->select('w.id_warehouse, CONCAT(reference, \' - \', name) as name');
		  		$query->from('warehouse', 'w');
		  		$query->innerJoin("warehouse_product_location","wpl","w.id_warehouse = wpl.id_warehouse AND wpl.id_product='".(int)$id_product."' AND wpl.id_product_attribute='".(int)$id_product_attribute."' ");
		  		$query->where('deleted = 0');
		  		$query->orderBy('reference ASC');
		  		//$query->innerJoin('warehouse_shop', 'ws', 'ws.id_warehouse = w.id_warehouse AND ws.id_shop = '.(int)SCI::getSelectedShop());
		  		
		  		$warehouses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		  		foreach($warehouses as $num=>$warehouse)
		  		{
		  			if($num>0)
		  				echo ",";
		  			if($warehouse["id_warehouse"]==$id_warehouse)
		  			{
			  			echo '{
				            text: "'.$warehouse["name"].'",
				            value: "'.$warehouse["id_warehouse"].'",
							selected: true
				        }';
		  			}
		  			else
		  				echo '{
				            text: "'.$warehouse["name"].'",
				            value: "'.$warehouse["id_warehouse"].'"
				        }';
		  		}
				?>
			]},
			{
		        type: "label",
		        label: '<?php echo _l('Available for sale in the source warehouse?',1)?>',
				tooltip: '<?php echo _l('Is this a quantity available for sale?',1)?>',
				list:[
					{
					    type: "radio",
					    name: "usable_from",
					    label: '<?php echo _l('Yes',1)?>',
					    checked: true,
					    value: "1",
					    position: "label-right"
					}, {
					    type: "radio",
					    name: "usable_from",
					    label: '<?php echo _l('No',1)?>',
					    value: "0",
					    position: "label-right"
					}
		    ]},
		    {
		        type: "select",
				name: 'id_warehouse_to',
		        label: "<?php echo _l('Destination Warehouse:',1)?>",
				required: true,
		       	tooltip: '<?php echo _l('Select the warehouse to which to transfer the product.',1)?>',
		 		options: [<?php 
		  		$shop = (int)SCI::getSelectedShop();
		  		if($shop == 0)
		  			$shop = null;

		  		$query = new DbQuery();
		  		$query->select('w.id_warehouse, CONCAT(reference, \' - \', name) as name');
		  		$query->from('warehouse', 'w');
		  		$query->where('deleted = 0');
		  		$query->orderBy('reference ASC');
		  		$warehouses = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
		  		foreach($warehouses as $num=>$warehouse)
		  		{
		  			if($num>0)
		  				echo ",";
		  			echo '{
				            text: "'.$warehouse["name"].'",
				            value: "'.$warehouse["id_warehouse"].'"
				        }';
		  		}
				?>
			]},
			{
		        type: "label",
		        label: '<?php echo _l('Available for sale in the destination warehouse?',1)?>',
				tooltip: '<?php echo _l('Do you want this quantity to be available for sale?',1)?>',
				list:[
					{
					    type: "radio",
					    name: "usable_to",
					    label: '<?php echo _l('Yes',1)?>',
					    checked: true,
						value: "1",
					    position: "label-right"
					}, {
					    type: "radio",
					    name: "usable_to",
					    label: '<?php echo _l('No',1)?>',
						value: "0",
					    position: "label-right"
					}
		    ]},
			{type: "button", name: "transferstock", value: "<?php echo _l('Transfer',1)?>"}
	]}
];

dhxlStockMvt_form = dhxlStockMvt_transfert.attachForm(formData);
dhxlStockMvt_form.attachEvent("onButtonClick", function(name, command){
   	dhxlStockMvt_form.validate();
	 if(name=="transferstock"){
		 //this.send("<?php echo SC_PS_PATH_ADMIN_REL;?>index.php?controller=AdminStockManagement&addstock=1&token=<?php echo $sc_agent->getPSToken('AdminStockManagement');?>","post", function(data){
		this.send("index.php?ajax=1&act=cat_win-stockmvt_insert&transferstock=1&token=<?php echo $sc_agent->getPSToken('AdminStockManagement');?>","post", function(loader, response){
			if(response=="success")
			{
				wStockMvt.close();
			 	displayProducts('if(propertiesPanel=="warehousestock"){displayAdvancedStock();displayStockMovements();}else if(propertiesPanel=="combinations"){displayCombinations();}');
			}
			else
			{
				if(response=="" || response==null)
					response = '<?php echo _l('An error occurred. Please try again later.',1)?>';
				dhtmlx.message({text:'<strong><?php echo _l('Caution!!!',1)?></strong><br/><br/>'+response,type:'error',expire:10000});
			}
		});     
	 }
});

</script>