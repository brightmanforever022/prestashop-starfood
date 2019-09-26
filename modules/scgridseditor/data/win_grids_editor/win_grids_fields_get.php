<?php

$type = str_replace("type_","",Tools::getValue("type","products"));
$grid_selected = Tools::getValue("grid");
$is_default = intval(Tools::getValue("is_default"));

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

require(dirname(__FILE__)."/win_grids_tools.php");

	$xml='';
	$array_fields = array();
	
	$content = "";
	$file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';
	if(file_exists($file))
		$content = file_get_contents($file);
	
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
	
	$liste_fields = array();
	if(gridIsInXML($grid_selected, $content))
	{
	
		$grids_xml_conf = simplexml_load_file($file);
		$grid_xml = null;
		foreach($grids_xml_conf->grids->grid AS $grid)
		{
			if($grid->name==$grid_selected)
			{
				$grid_xml = $grid;
			}
		}
			
		if(!empty($grid_xml))
		{
			$liste_fields = explode(",",$grid_xml->value);
		}
	}
	if(empty($liste_fields))
	{
		$grids_default = SCI::getGridViews($type_temp);
		if(!empty($soloGrids[$type_temp]))
			$liste_fields = explode(",",$grids_default);
		else
		{
			if(!empty($grids_default[$grid_selected]))
				$liste_fields = explode(",",$grids_default[$grid_selected]);
		}
	}
	
	if(!empty($liste_fields))
	{
		$grids_xml_conf = "";
		if(file_exists($file))
			$grids_xml_conf = simplexml_load_file($file);
		$params_fields = array();
		$params_fields = SCI::getGridFields($type_temp);
		if($type_temp=="combination")
			$params_fields['ATTR']=array('text' => _l('Attributes'),'width'=>80,'align'=>'right','type'=>'coro','sort'=>'str','color'=>'','filter'=>'#select_filter');
		
		foreach($liste_fields as $name_field)
		{
			$temp_field = "";
			if(!empty($grids_xml_conf))
				$temp_field = getFieldInXML($name_field, $grids_xml_conf);
			if(!empty($temp_field))
			{
				$temp_field = (array) $temp_field;
				$temp_field['text'] = $temp_field['text']->{$iso};
				$temp_field['bg_color'] = "#9ECA92";
				$array_fields[] = $temp_field;
			}
			else if(!empty($params_fields[$name_field]))
			{
				$array_fields[] = array(
						"name"=>$name_field,
						"text"=>$params_fields[$name_field]["text"],
						"celltype"=>$params_fields[$name_field]["type"],
						"align"=>$params_fields[$name_field]["align"],
						"sort"=>$params_fields[$name_field]["sort"],
						"filter"=>$params_fields[$name_field]["filter"],
						"width"=>$params_fields[$name_field]["width"],
						"color"=>$params_fields[$name_field]["color"],
						"footer"=>$params_fields[$name_field]["footer"],
						"bg_color"=>""
				);
			}
		}
	}
	
	foreach($array_fields AS $num=>$row)
	{		
		$xml.=("<row id='".$row["name"]."'>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["name"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["text"]."]]></cell>");
			//$xml.=("<cell><![CDATA[".$row["celltype"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["align"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["sort"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["filter"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["width"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["footer"]."]]></cell>");
			$xml.=("<cell ".(!empty($row["bg_color"])?' bgColor="'.$row["bg_color"].'"':"")."><![CDATA[".$row["color"]."]]></cell>");
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
	<column id="name" width="150" type="ro" align="left" sort="na"><?php echo _l('ID')?></column>
	<column id="text" width="200" type="ed" align="left" sort="na"><?php echo _l('Field')?></column>
	<?php /*?><column id="type" width="100" type="coro" align="left" sort="na"><?php echo _l('Type')?>
		<option value="ro"><?php echo _l('Just display')?></option>
		<option value="ed"><?php echo _l('Editable')?></option>
		<option value="edtxt"><?php echo _l('Secure text')?></option>
		<option value="edn"><?php echo _l('Numeric')?></option>
		<option value="txt"><?php echo _l('Long text')?></option>
		<option value="coro"><?php echo _l('Multiple choices')?></option>
		<option value="co"><?php echo _l('Multiple choices or write value')?></option>
		<option value="dhxCalendarA"><?php echo _l('Date')?></option>
	</column>*/ ?>
	<column id="align" width="100" type="coro" align="left" sort="na"><?php echo _l('Alignment')?>
		<option value="left"><?php echo _l('Left')?></option>
		<option value="center"><?php echo _l('Center')?></option>
		<option value="right"><?php echo _l('Right')?></option>
	</column>
	<column id="sort" width="100" type="coro" align="left" sort="na"><?php echo _l('Sort')?>
		<option value="int"><?php echo _l('Numeric')?></option>
		<option value="str"><?php echo _l('Text')?></option>
		<option value="date"><?php echo _l('Date')?></option>
		<option value="na"><?php echo _l(' None')?></option>
	</column>
	<column id="filter" width="100" type="coro" align="left" sort="na"><?php echo _l('Filter')?>
		<option value="#numeric_filter"><?php echo _l('Numeric')?></option>
		<option value="#text_filter"><?php echo _l('Text')?></option>
		<option value="#select_filter"><?php echo _l('Multiple choices (type A)')?></option>
		<option value="#select_filter_strict"><?php echo _l('Multiple choices (type B)')?></option>
		<option value="na"><?php echo _l(' None')?></option>
	</column>
	<column id="width" width="80" type="edn" align="left" sort="na"><?php echo _l('Width')?></column>
	<column id="footer" width="80" type="coro" align="left" sort="na"><?php echo _l('Footer')?>
		<option value=""> </option>
		<option value="#stat_total"><?php echo _l('Column total')?></option>
		<option value="#stat_max"><?php echo _l('Max. value')?></option>
		<option value="#stat_min"><?php echo _l('Min. value')?></option>
		<option value="#stat_average"><?php echo _l('Column average')?></option>
		<option value="#stat_count"><?php echo _l('Nb. rows')?></option>
	</column>
	<column id="color" width="100" type="cp" align="left" sort="na"><?php echo _l('Color')?></column>
</head>
<?php
	echo 	$xml;
?>
</rows>
