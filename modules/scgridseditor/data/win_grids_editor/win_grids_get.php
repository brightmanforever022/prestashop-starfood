<?php

$type = str_replace("type_","",Tools::getValue("type","products"));
$id_lang = intval(Tools::getValue("id_lang",0));
$iso = "en";
if(strtolower(Language::getIsoById($id_lang))=="fr")
	$iso = "fr";

$soloGrids = array();
$soloGrids["combination"] = "combination";
$soloGrids["productsort"] = "productsort";
$soloGrids["msproduct"] = "msproduct";
$soloGrids["mscombination"] = "mscombination";
$soloGrids["image"] = "image";
$soloGrids["propspeprice"] = "propspeprice";
$soloGrids["winspeprice"] = "winspeprice";
$soloGrids["propsupplier"] = "propsupplier";

if($type=="products")
	$type_temp="product";
elseif($type=="customers")
	$type_temp="customer";
elseif($type=="orders")
	$type_temp="order";
elseif($type=="combinations")
	$type_temp="combination";
else
	$type_temp=$type;

	$xml='';
	
	$grids = array();
	if(!empty($soloGrids[$type_temp]))
	{
		if($type_temp=="combination")
			$grids["grid_".$type_temp]["name"]=_l('Combinations');
		elseif($type_temp=="productsort")
			$grids["grid_".$type_temp]["name"]=_l('Product sort');
		elseif($type_temp=="msproduct")
			$grids["grid_".$type_temp]["name"]=_l('Multistore - products information');
		elseif($type_temp=="mscombination")
			$grids["grid_".$type_temp]["name"]=_l('Multistore - combinations');
		elseif($type_temp=="image")
			$grids["grid_".$type_temp]["name"]=_l('Product images');
		elseif($type_temp=="propspeprice")
			$grids["grid_".$type_temp]["name"]=_l('Properties - specific prices');
		elseif($type_temp=="winspeprice")
			$grids["grid_".$type_temp]["name"]=_l('Specific prices management');
		elseif($type_temp=="propsupplier")
			$grids["grid_".$type_temp]["name"]=_l('Suppliers');
		
		$grids["grid_".$type_temp]["color"]="#dddddd";
		
		if(file_exists(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml'))
		{
			$grids_xml_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml');
			foreach($grids_xml_conf->grids->grid AS $grid)
			{
				$grids["grid_".$type_temp]["color"]="";
			}
		}
	}
	else
	{
		$grids_default = SCI::getGridViews($type_temp);
		$grids=array();
		foreach($grids_default AS $id=>$value)
		{
			$grids[$id]["color"]="#dddddd";
			if($id=="grid_light")
				$grids[$id]["name"]=_l('Light view');
			elseif($id=="grid_large")
				$grids[$id]["name"]=_l('Large view');
			elseif($id=="grid_delivery")
				$grids[$id]["name"]=_l('Delivery');
			elseif($id=="grid_price")
				$grids[$id]["name"]=_l('Prices');
			elseif($id=="grid_discount")
				$grids[$id]["name"]=_l('Discounts');
			elseif($id=="grid_discount_2")
				$grids[$id]["name"]=_l('Discounts and margins');
			elseif($id=="grid_seo")
				$grids[$id]["name"]=_l('SEO');
			elseif($id=="grid_reference")
				$grids[$id]["name"]=_l('References');
			elseif($id=="grid_description")
				unset($grids[$id]);//$grids[$id]["name"]=_l('Descriptions');
			elseif($id=="grid_combination_price")
				unset($grids[$id]);//$grids[$id]["name"]=_l('Descriptions');
			elseif($id=="grid_address")
				$grids[$id]["name"]=_l('Addresses');
			elseif($id=="grid_convert")
				$grids[$id]["name"]=_l('Convert');
			elseif($id=="grid_picking")
				$grids[$id]["name"]=_l('Picking');
		}
		
		if(file_exists(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml'))
		{
			$grids_xml_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_'.$type.'_conf.xml');
			foreach($grids_xml_conf->grids->grid AS $grid)
			{
				$grids[(string) $grid->name]["name"]=(string) $grid->text->{$iso};
				$grids[(string) $grid->name]["color"]="";
			}
		}	
	}
	
	foreach($grids AS $id=>$row)
	{		
		$xml.=("<row id='".$id."'>");
			$xml.=('<userdata name="is_default">'.(!empty($row["color"])?'1':"0").'</userdata>');
			$xml.=("<cell ".(!empty($row["color"])?' bgColor="'.$row["color"].'"':"")."><![CDATA[".$id."]]></cell>");
			$xml.=("<cell ".(!empty($row["color"])?' bgColor="'.$row["color"].'"':"")."><![CDATA[".$row["name"]."]]></cell>");
		$xml.=("</row>");
	}
	
	//include XML Header (as response will be in xml format)
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");

?>
<rows id="0">
<head>
	<column id="id_grid" width="80" type="ro" align="left" sort="int"><?php echo _l('ID')?></column>
	<column id="name" width="200" type="ed" align="left" sort="str"><?php echo _l('Grid')?></column>
</head>
<?php
	echo 	$xml;
?>
</rows>
