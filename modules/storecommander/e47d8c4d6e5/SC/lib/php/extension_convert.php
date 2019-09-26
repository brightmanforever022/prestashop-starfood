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


class ExtensionConvert
{
	public $type;
	public $file;
	public $dom;
	
	public function convert($type)
	{
		$return = false;
		$this->type = $type;
		$this->file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';
		if(file_exists($this->file))
		{
			$this->dom= new DOMDocument();
			$this->dom->preserveWhiteSpace = false;
			$this->dom->formatOutput = true;
			$this->dom->load($this->file);
			
			$file_version = 1;
			$actual_version = 1;
			
			$nodeVersion = $this->dom->getElementsByTagname('xml_version');
			if($nodeVersion->length>0)
			{
				foreach($nodeVersion as $node)
				{
					if($node->nodeName != '#text')
					{
						$file_version = $node->nodeValue;
						break;
					}
				}
			}
			
			if(defined("SC_EXTENSION_VERSION") && SC_EXTENSION_VERSION>0)
				$actual_version = (int)SC_EXTENSION_VERSION;

			$return = true;
			if($file_version!=$actual_version && !empty($file_version))
			{
				$start = $file_version+1;
				for ($i=$start; $i<=$actual_version;$i++)
				{
					$good = call_user_func(array(self, '_convert_from_'.$file_version.'_to_'.$i));
					$file_version++;
					if(!$good)
					{
						$return = false;
						break;
					}
				}
				
				/*echo '<textarea style="width:100%;height:400px">'.$this->dom->saveXML().'</textarea>';
				exit();*/
				$this->dom->save($this->file);
			}
		}
		return $return;
	}
		
	public function _convert_from_1_to_2()
	{
		if($this->type=="combinations")
			return $this->_convert_from_1_to_2_combinations();
		else
			return $this->_convert_from_1_to_2_others();
	}
	public function _convert_from_1_to_2_combinations()
	{
		$return = false;
		
		// RECUPERATION DE TOUS LES "<field>"
		$nodeFieldsList = array();
		$nodeFieldList = $this->dom->getElementsByTagname('field');
		foreach ( $nodeFieldList as $nodeField )
		{
			if($nodeField->nodeName != '#text')
			{
				foreach($nodeField->childNodes as $nodeFieldParts)
				{
					if($nodeFieldParts->nodeName == 'name')
					{
						$nodeFieldsList[$nodeFieldParts->nodeValue] = $nodeField;
						break;
					}
				}
			}
		}
		
		// CREATION ET INSERTION DANS "<fields>"
		$fieldsElement = $this->dom->createElement("fields");
		$this->dom->appendChild($fieldsElement);
		foreach($nodeFieldsList as $nodeField)
		{
			$fieldsElement->appendChild($nodeField);
		}
		
		// SUPPRESSION DES "<fields>" DANS LE "<grid>"
		// RENOMMAGE DE "<config>"
		$nodeGridList = $this->dom->getElementsByTagname('grid');
		foreach ( $nodeGridList as $nodeGrid )
		{
			foreach($nodeGrid->childNodes as $nodeGridChild)
			{
				if($nodeGridChild->nodeName == 'fields')
				{
					$nodeGridChild->parentNode->removeChild($nodeGridChild);
				}
				if($nodeGridChild->nodeName == 'config')
				{
					$valueElement = $this->dom->createElement("value");
					//$nodeGrid->appendChild($valueElement);
					$nodeGridChild->parentNode->insertBefore($valueElement, $nodeGridChild);

					$v=$valueElement->ownerDocument->createCDATASection($nodeGridChild->nodeValue);
					$valueElement->appendChild($v);
				}
			}
		}
		
		// SUPPRESSION DE "<config>"
		$nodeConfigList = $this->dom->getElementsByTagname('config');
		foreach ( $nodeConfigList as $nodeConfig )
		{
			$nodeConfig->parentNode->removeChild($nodeConfig);
		}
		
		// UPGRADE DE LA VERSION
		$nodeVersion = $this->dom->getElementsByTagname('xml_version');
		if($nodeVersion->length>0)
		{
			foreach($nodeVersion as $node)
			{
				if($node->nodeName != '#text')
				{
					$node->nodeValue='';
					$v=$node->ownerDocument->createCDATASection('2');
					$node->appendChild($v);
					break;
				}
			}
		}
		else
		{
			$versionElement = $this->dom->createElement("xml_version");
			$this->dom->appendChild($versionElement);
				
			$v=$versionElement->ownerDocument->createCDATASection('2');
			$versionElement->appendChild($v);
		}
		
		// ENCAPSULTE DANS "<extension>"
		$extensionElement = $this->dom->createElement("extension");
		$this->dom->appendChild($extensionElement);
		
		$nodeVersion = $this->dom->getElementsByTagname('xml_version');
		$extensionElement->appendChild($nodeVersion->item(0));

		$gridsElement = $this->dom->createElement("grids");
		$extensionElement->appendChild($gridsElement);
		$nodeGrid = $this->dom->getElementsByTagname('grid');
		$gridsElement->appendChild($nodeGrid->item(0));
		
		$nodeFields = $this->dom->getElementsByTagname('fields');
		$extensionElement->appendChild($nodeFields->item(0));
		
		$return = true;
		
		return $return;
	}
	public function _convert_from_1_to_2_others()
	{
		$return = false;
		
		// RECUPERATION DE TOUS LES "<field>"
		$nodeFieldsList = array();
		$nodeFieldList = $this->dom->getElementsByTagname('field');
		foreach ( $nodeFieldList as $nodeField )
		{
			if($nodeField->nodeName != '#text')
			{
				foreach($nodeField->childNodes as $nodeFieldParts)
				{
					if($nodeFieldParts->nodeName == 'name')
					{
						$nodeFieldsList[$nodeFieldParts->nodeValue] = $nodeField;
						break;
					}
				}
			}
		}
		
		// CREATION ET INSERTION DANS "<fields>"
		$fieldsElement = $this->dom->createElement("fields");
		$this->dom->appendChild($fieldsElement);
		foreach($nodeFieldsList as $nodeField)
		{
			$fieldsElement->appendChild($nodeField);
		}
		
		// SUPPRESSION DES "<fields>" DANS LES "<grid>"
		$nodeGridList = $this->dom->getElementsByTagname('grid');
		foreach ( $nodeGridList as $nodeGrid )
		{
			foreach($nodeGrid->childNodes as $nodeGridChild)
			{
				if($nodeGridChild->nodeName == 'fields')
				{
					$nodeGridChild->parentNode->removeChild($nodeGridChild);
				}
				if($nodeGridChild->nodeName == 'text')
				{
					$updatedNode = $nodeGridChild->getElementsByTagname('fr')->item(0);
					$name = $updatedNode->nodeValue;
					$updatedNode->nodeValue='';
					$v=$updatedNode->ownerDocument->createCDATASection(stripslashes($name));
					$updatedNode->appendChild($v);
					
					$updatedNodeBIS = $nodeGridChild->getElementsByTagname('en')->item(0);
					$nameBIS = $updatedNodeBIS->nodeValue;
					$updatedNodeBIS->nodeValue='';
					$v=$updatedNodeBIS->ownerDocument->createCDATASection(stripslashes($nameBIS));
					$updatedNodeBIS->appendChild($v);
				}
			}
		}
		
		// UPGRADE DE LA VERSION
		$nodeVersion = $this->dom->getElementsByTagname('xml_version');
		if($nodeVersion->length>0)
		{
			foreach($nodeVersion as $node)
			{
				if($nodeField->nodeName != '#text')
				{
					$nodeField->nodeValue='';
					$v=$nodeField->ownerDocument->createCDATASection('2');
					$nodeField->appendChild($v);
					break;
				}
			}
		}
		else
		{
			$versionElement = $this->dom->createElement("xml_version");
			$this->dom->appendChild($versionElement);
				
			$v=$versionElement->ownerDocument->createCDATASection('2');
			$versionElement->appendChild($v);
		}
		
		// ENCAPSULTE DANS "<extension>"
		$extensionElement = $this->dom->createElement("extension");
		$this->dom->appendChild($extensionElement);
		
		$nodeVersion = $this->dom->getElementsByTagname('xml_version');
		$extensionElement->appendChild($nodeVersion->item(0));
		
		$nodeGrids = $this->dom->getElementsByTagname('grids');
		$extensionElement->appendChild($nodeGrids->item(0));
		
		$nodeFields = $this->dom->getElementsByTagname('fields');
		$extensionElement->appendChild($nodeFields->item(0));
		
		$return = true;
		
		return $return;
	}
		
	public static function _convert_from_2_to_3($datas)
	{
		global $sc_agent;
		$new_datas = $datas;
		
		foreach($new_datas AS $i=>$map)
		{
			if(!empty($map["modifications"]))
			{
				$new_datas[$i]["modifications"]=str_replace(" ", "&&&", $map["modifications"]);
			}
		}
		
		return $new_datas;
	}
}