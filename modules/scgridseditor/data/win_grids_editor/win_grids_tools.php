<?php

// CHECK IF GRID EXIST IN THIS FILE
// WITHOUT DomDocument
function gridIsInXML($id, $content)
{
	$return = false;
	
	if(strpos($content, "<name><![CDATA[".$id."]]></name>")!==false || strpos($content, "<name>".$id."</name>")!==false)
	{
		$return = true;
	}
	
	return $return;
}
function fieldIsInXML($id, $content)
{
	$return = false;
	
	if(strpos($content, "<name><![CDATA[".$id."]]></name>")!==false || strpos($content, "<name>".$id."</name>")!==false)
	{
		$return = true;
	}
	
	return $return;
}

// CHECK IF GRID NAME ALREADY EXIST
// WITHOUT DomDocument
function testName($name, $content, $basename=null, $i=2)
{
	if(empty($basename))
		$basename = $name;
	if(!empty($name))
	{
		if(gridIsInXML($name, $content))
		{
			$temp_name = $basename."_".$i;
		
			$name = testName($temp_name, $content, $basename, ($i+1));
		}
	}
	return $name;
}
function testNameField($name, $content, $basename=null, $i=2)
{
	if(empty($basename))
		$basename = $name;
	if(!empty($name))
	{
		if(fieldIsInXML($name, $content))
		{
			$temp_name = $basename."_".$i;
		
			$name = testNameField($temp_name, $content, $basename, ($i+1));
		}
	}
	return $name;
}

// GET FIELD CONFIG (IF EXIST) FROM FILE
// WITHOUT DomDocument
function getFieldInXML($name, $xml)
{
	$return = null;
	foreach($xml->fields->field as $field)
	{
		if($field->name==$name)
		{
			$return = $field;
			break;
		}
	}
	return $return;
}

// ADD A NEW GRID IN FILE
// WITHOUT DomDocument
function addNewGrid($type, $content,$name,$text=null,$fields="by_default")
{
	$file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';
	
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
	
	$soloGrids = array();
	$soloGrids["combination"] = "combination";
	$soloGrids["productsort"] = "productsort";
	$soloGrids["msproduct"] = "msproduct";
	$soloGrids["mscombination"] = "mscombination";
	$soloGrids["image"] = "image";
	$soloGrids["propspeprice"] = "propspeprice";
	$soloGrids["winspeprice"] = "winspeprice";
	$soloGrids["propsupplier"] = "propsupplier";
	
	if(empty($text))
	{
		if($name=="grid_light")
			$original_name_en=('Light view');
		elseif($name=="grid_large")
			$original_name_en=('Large view');
		elseif($name=="grid_delivery")
			$original_name_en=('Delivery');
		elseif($name=="grid_price")
			$original_name_en=('Prices');
		elseif($name=="grid_discount")
			$original_name_en=('Discounts');
		elseif($name=="grid_discount_2")
			$original_name_en=('Discounts and margins');
		elseif($name=="grid_seo")
			$original_name_en=('SEO');
		elseif($name=="grid_reference")
			$original_name_en=('References');
		elseif($name=="grid_address")
			$original_name_en=('Addresses');
		elseif($name=="grid_convert")
			$original_name_en=('Convert');
		elseif($name=="grid_picking")
			$original_name_en=('Picking');
			
		$original_name_fr = _l($original_name_en);
	}
	else
	{
		$original_name_fr = $text;
		$original_name_en = $text;
	}
	
	if(!empty($fields) && $fields=="by_default")
	{
		$fields = "";
		$grids_default = SCI::getGridViews($type_temp);
		if($type!="combinations" && !empty($grids_default[$name]))
			$fields = $grids_default[$name];
		elseif((!empty($soloGrids[$type_temp])) && !empty($grids_default))
			$fields = $grids_default;
	}
	
	$grid_xml = '	<grid>
		<name><![CDATA['.$name.']]></name>
		<text>
			<fr><![CDATA['.$original_name_fr.']]></fr>
			<en><![CDATA['.$original_name_en.']]></en>
		</text>
		<value><![CDATA['.$fields.']]></value>
	</grid>';
	$content = str_replace('</grids>', $grid_xml."\n".'</grids>', $content);
	file_put_contents($file, $content);
}

// ADD A NEW FIELD IN FILE
// WITHOUT DomDocument
function addNewField($type, $content, $name)
{
	$file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';
	
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
	
	$field_xml = '	<field>
		<name><![CDATA['.$name.']]></name>
		<text>
			<fr><![CDATA['.$name.']]></fr>
			<en><![CDATA['.$name.']]></en>
		</text>
		<table><![CDATA[none]]></table>
		<width><![CDATA[60]]></width>
		<align><![CDATA[left]]></align>
		<celltype><![CDATA[ro]]></celltype>
		<answertype><![CDATA[]]></answertype>
		<sort><![CDATA[str]]></sort>
		<color><![CDATA[]]></color>
		<filter><![CDATA[#text_filter]]></filter>
		<footer><![CDATA[#text_filter]]></footer>
		<forceUpdateCombinationsGrid><![CDATA[]]></forceUpdateCombinationsGrid>
		<options><![CDATA[]]></options>
		<onEditCell><![CDATA[]]></onEditCell>
		<onAfterUpdate><![CDATA[]]></onAfterUpdate>
		<onBeforeUpdate><![CDATA[]]></onBeforeUpdate>
		<SQLSelectDataSelect><![CDATA[]]></SQLSelectDataSelect>
		<rowData><![CDATA[]]></rowData>
		<afterGetRows><![CDATA[]]></afterGetRows>
	</field>';
	$content = str_replace('</fields>', $field_xml."\n".'</fields>', $content);
	file_put_contents($file, $content);
}