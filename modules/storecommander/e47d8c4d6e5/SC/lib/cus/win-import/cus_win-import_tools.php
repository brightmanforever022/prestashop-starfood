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

	
	function readCusImportConfigXML($files)
	{
		global $importConfig;
		$importConfig=array();
		// read config
		if ($feed = @simplexml_load_file(SC_CSV_IMPORT_DIR."customers/".SC_CSV_IMPORT_CONF))
		{
			foreach($feed->csvfile AS $file)
			{
				if (strpos((string) $file->name,'&')===false)
					$importConfig[ (string) $file->name]=array(
														'name' => (string) $file->name,
														'supplier' => (string) $file->supplier,
														'mapping' => (string) $file->mapping,
														'fieldsep' => (string) $file->fieldsep,
														'valuesep' => (string) $file->valuesep,
														'utf8' => (string) $file->utf8,
														'idby' => (string) $file->idby,
														'iffoundindb' => (string) $file->iffoundindb,
														'firstlinecontent' => (string) $file->firstlinecontent,
														'importlimit' => (string) $file->importlimit,
														'id_shop' => (int) $file->id_shop,
													);
			}
		}
		// config by default
		foreach ($files AS $file)
		{
			if ($file!='' && !sc_in_array($file,array_keys($importConfig),"cusWinImportProcess_importConfig") && strpos($file,'&')===false)
			{
				$importConfig[$file]=array(
													'name' => $file,
													'supplier' => '',
													'mapping' => '',
													'fieldsep' => 'dcomma',
													'valuesep' => ',',
													'utf8' => '1',
													'idby' => 'email',
													'iffoundindb' => 'skip',
													'firstlinecontent' => '',
													'importlimit' => '500',
													'id_shop' => '0'
													);
				if(SCMS)
					$importConfig[$file]["id_shop"]=(int)Configuration::get('PS_SHOP_DEFAULT');
			}
		}
	}
	
	function writeCusImportConfigXML()
	{
		global $importConfig;
		$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content.='<csvfiles>'."\n";
		foreach($importConfig AS $conf)
		{
			if (file_exists(SC_CSV_IMPORT_DIR."customers/".$conf['name']))
			{
				$content.='<csvfile>'."\n";
				$content.='<name><![CDATA['.$conf['name'].']]></name>';
				$content.='<supplier><![CDATA['.$conf['supplier'].']]></supplier>';
				$content.='<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
				$content.='<id_shop><![CDATA['.$conf['id_shop'].']]></id_shop>';
				$content.='<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
				$content.='<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
				$content.='<utf8><![CDATA['.$conf['utf8'].']]></utf8>';
				$content.='<idby><![CDATA['.$conf['idby'].']]></idby>';
				$content.='<iffoundindb><![CDATA['.$conf['iffoundindb'].']]></iffoundindb>';
				$content.='<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
				$content.='<importlimit><![CDATA['.$conf['importlimit'].']]></importlimit>';
				$content.='</csvfile>'."\n";
			}
		}
		$content.='</csvfiles>';
		return file_put_contents(SC_CSV_IMPORT_DIR."customers/".SC_CSV_IMPORT_CONF, $content);
	}
	
	function findAllCSVLineValueCus($valueToFind,&$arrayToFill,$optionToGet,$fromObject)
	{
		global $line,$firstLineData,$mappingData,$importConfig,$TODOfilename;
		/*print_r($line);
		 echo '<br/>';
		print_r($firstLineData);
		echo '<br/>';*/
		foreach($line AS $k => $v)
		{
			/*
			 echo $firstLineData[$k].'<br/>';
			echo (in_array($firstLineData[$k],$mappingData['CSVArray'])?1:0) .' '. (sc_array_key_exists($firstLineData[$k],$mappingData['CSV2DB']) && $mappingData['CSV2DB'][$firstLineData[$k]]==$valueToFind?1:0).'<br/>';
			*/
			if (sc_in_array($firstLineData[$k],$mappingData['CSVArray'],"cusWinImportProcess_CSVArray") && sc_array_key_exists($firstLineData[$k],$mappingData['CSV2DB']) && $mappingData['CSV2DB'][$firstLineData[$k]]==$valueToFind)
			{
				//echo $firstLineData[$k].'-YESSS<br/>';
				if ($valueToFind=='attribute_multiple')
				{
					$vArray=explode($importConfig[$TODOfilename]['valuesep'],$v);
					foreach($vArray AS $val)
						@$arrayToFill[]=array(	'object' => $firstLineData[$k],
								'value' => trim($val),
								$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
								'color_attr_options'=>''
						);
				}elseif ($valueToFind=='attribute'){
					//echo 'aa<br/>';
					$attr_color=findCSVLineValue('attribute_color');
					$attr_texture=findCSVLineValue('attribute_texture');
					@$arrayToFill[]=array(		'object' => $firstLineData[$k],
							'value' => trim($v),
							$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
							'color_attr_options'=>($attr_color?$attr_color:'').'_|_'.($attr_texture?$attr_texture:'')
					);
					//echo 'bb<br/>';
				}else{
					if (($valueToFind!='feature' && $valueToFind!='feature_custom') || (($valueToFind=='feature' || $valueToFind=='feature_custom') && trim($v)!='-'))
						@$arrayToFill[]=array(		'object' => $firstLineData[$k],
								'value' => trim($v),
								$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
								'color_attr_options'=>''
						);
				}
				//echo 'cc<br/>';
			}
		}
	}
	
	function loadMappingCus($filename)
	{
		global $sc_agent;
		if ($filename=='')
			return '';
		if (strpos($filename,'.map.xml')===false)
			$filename=$filename.'.map.xml';
		$content='';
		if (file_exists(SC_CSV_IMPORT_DIR."customers/".$filename) && $feed = simplexml_load_file(SC_CSV_IMPORT_DIR."customers/".$filename))
		{
			$id_lang=(int)$feed->id_lang;
			if (!$id_lang)
				$id_lang=(int)$sc_agent->id_lang;
			/*$groups=Db::getInstance()->executeS('
				SELECT DISTINCT agl.`name`, ag.*, agl.*
				FROM `'._DB_PREFIX_.'attribute_group` ag
				LEFT JOIN `'._DB_PREFIX_.'attribute_group_lang` agl
					ON (ag.`id_attribute_group` = agl.`id_attribute_group` AND `id_lang` = '.(int)$id_lang.')
				ORDER BY `name` ASC');
			$groupsName=array();
			foreach($groups AS $g)
				$groupsName[]=$g['name'];*/
			foreach($feed->map AS $map)
			{
				/*if (!in_array(trim((string)$map->dbname),array('attribute','attribute_multiple'))
						|| (in_array(trim((string)$map->dbname),array('attribute','attribute_multiple')) && in_array(trim((string)$map->options),$groupsName))
						|| (SCAS && ($map->dbname=="quantity" || $map->dbname=="location")))
					$content.=trim((string)$map->csvname).','.trim((string)$map->dbname).','.trim((string)$map->options).';';
				else // we skip attribute group value if not available*/
					$content.=trim((string)$map->csvname).','.trim((string)$map->dbname).',;';
			}
		}
		return $content;
	}
	
	function hasAddress()
	{
		global $mappingData,$addressFields;
		foreach($addressFields as $addressField)
		{
			if (sc_in_array($addressField,$mappingData['DBArray'],"cusWinImportProcess_DBArray"))
				return true;
		}
		return false;
	}
