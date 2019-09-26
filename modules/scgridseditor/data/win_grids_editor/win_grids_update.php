<?php

$action = Tools::getValue("action");
$type = str_replace("type_","",Tools::getValue("type","products"));
$newvalue = Tools::getValue("newvalue","");
$is_default = intval(Tools::getValue("is_default"));
$dupplicate = Tools::getValue("dupplicate","");
$name = Tools::getValue("name","custom");

$name_lang = intval(Tools::getValue("id_lang",0));
$iso = "en";
if(strtolower(Language::getIsoById($name_lang))=="fr")
	$iso = "fr";

require(dirname(__FILE__)."/win_grids_tools.php");
$types_list  = array("products","combinations","customers","orders","productsort","msproduct","mscombination","image","propspeprice","winspeprice","propsupplier");

if(!empty($type) && in_array($type,$types_list) && !empty($action) && !empty($name))
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
	
	// CREATE FILE IF NOT EXIST
	if(!file_exists($file))
	{
		$content = '<?xml version="1.0" encoding="UTF-8"?>
<extension>
  <xml_version><![CDATA['.SC_EXTENSION_VERSION.']]></xml_version>
  <grids></grids>
  <fields></fields>
</extension>';
		file_put_contents($file, $content);
	}
	
	if($action=="insert")
	{
		$content = file_get_contents($file);
		
		// CHECK AND UPDATE NAME
		$original_name = $name;
		$name = str_replace("-","_",link_rewrite(strtolower($name)));
		if(strpos($name, "grid_")===false)
			$name = "grid_".$name;
		$name = testName($name, $content);
		
		// ADD NEW GRID
		addNewGrid($type, $content, $name, $original_name);
	}
	elseif($action=="update" && !empty($newvalue))
	{
		if(!$is_default)
		{
			if(!empty($newvalue))
			{
				$dom = new DOMDocument();
				$dom->preserveWhiteSpace = false;
				$dom->formatOutput = true;
				$dom->load($file);
				
				$nodeGridList = $dom->getElementsByTagname('grid');
				foreach ( $nodeGridList as $nodeGrid )
				{
					$nodeName = $nodeGrid->getElementsByTagname('name')->item(0);
					if($nodeName->nodeValue == $name)
					{
						$nodeText = $nodeGrid->getElementsByTagname($iso)->item(0);
						$nodeText->nodeValue='';
						$v=$nodeText->ownerDocument->createCDATASection($newvalue);
						$nodeText->appendChild($v);
					}
				}
				$dom->save($file);
				
				$content = file_get_contents($file);
				$content = str_replace("<grids/>","<grids></grids>",$content);
				$content = str_replace("<fields/>","<fields></fields>",$content);
				file_put_contents($file, $content);
			}
		}
		else
		{
			$content = file_get_contents($file);

			// ADD NEW GRID BUT MODIFY TEXT NAME
			addNewGrid($type, $content, $name, $newvalue);
		}
	}
	elseif($action=="delete")
	{
		$dom = new DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput = true;
		$dom->load($file);
		
		$nodeGridList = $dom->getElementsByTagname('grid');
		foreach ( $nodeGridList as $nodeGrid )
		{
			$nodeName = $nodeGrid->getElementsByTagname('name')->item(0);
			if($nodeName->nodeValue == $name)
			{
				$nodeGrid->parentNode->removeChild($nodeGrid);
			}
		}
		$dom->save($file);
				
		$content = file_get_contents($file);
		$content = str_replace("<grids/>","<grids></grids>",$content);
		$content = str_replace("<fields/>","<fields></fields>",$content);
		file_put_contents($file, $content);
	}
	elseif($action=="dupplicate" && !empty($dupplicate))
	{
		if(!$is_default)
		{
			// CHECK AND UPDATE NAME
			$original_name = $name;
			$name = str_replace("-","_",link_rewrite(strtolower($name)));
			if(strpos($name, "grid_")===false)
				$name = "grid_".$name;
				
			$name = testName($name, $content);
			
			// UPDATE FILE
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->load($file);
			
			$nodeGridList = $dom->getElementsByTagname('grid');
			foreach ( $nodeGridList as $nodeGrid )
			{
				$nodeName = $nodeGrid->getElementsByTagname('name')->item(0);
				if($nodeName->nodeValue == $dupplicate)
				{
					$cloneNode = $nodeGrid->cloneNode(true);
					
					$cloneNodeName = $cloneNode->getElementsByTagname('name')->item(0);
					$cloneNodeName->nodeValue='';
					$v=$cloneNodeName->ownerDocument->createCDATASection($name);
					$cloneNodeName->appendChild($v);
					
					$cloneNodeFr = $cloneNode->getElementsByTagname('fr')->item(0);
					$cloneNodeFr->nodeValue='';
					$v=$cloneNodeFr->ownerDocument->createCDATASection($original_name);
					$cloneNodeFr->appendChild($v);
					
					$cloneNodeEn = $cloneNode->getElementsByTagname('en')->item(0);
					$cloneNodeEn->nodeValue='';
					$v=$cloneNodeEn->ownerDocument->createCDATASection($original_name);
					$cloneNodeEn->appendChild($v);
					
        			$nodeGrid->parentNode->appendChild($cloneNode);
				}
			}
			$dom->save($file);
				
			$content = file_get_contents($file);
			$content = str_replace("<grids/>","<grids></grids>",$content);
			$content = str_replace("<fields/>","<fields></fields>",$content);
			file_put_contents($file, $content);
		}
		else
		{
			$content = file_get_contents($file);

			// CHECK AND UPDATE NAME
			$original_name = $name;
			$name = str_replace("-","_",link_rewrite(strtolower($name)));
			if(strpos($name, "grid_")===false)
				$name = "grid_".$name;
			
			$name = testName($name, $content);
			
			// GET DEFAULT FIELDS
			$fields = "";
			$grids_default = SCI::getGridViews($type_temp);
			if(!empty($grids_default[$dupplicate]))
				$fields = $grids_default[$dupplicate];

			// ADD NEW GRID BUT MODIFY TEXT AND NAME
			addNewGrid($type, $content, $name, $original_name, $fields);
		}
	}
}






