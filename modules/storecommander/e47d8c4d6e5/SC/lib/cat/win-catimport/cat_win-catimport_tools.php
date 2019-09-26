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

	
	function readCatImportConfigXML($files)
	{
		global $importConfig;
		$importConfig=array();
		// read config
		if ($feed = @simplexml_load_file(SC_CSV_IMPORT_DIR."category/".SC_CSV_IMPORT_CONF))
		{
			foreach($feed->csvfile AS $file)
			{
				if (strpos((string) $file->name,'&')===false)
					$importConfig[ (string) $file->name]=array(
														'name' => (string) $file->name,
														'mapping' => (string) $file->mapping,
														'fieldsep' => (string) $file->fieldsep,
														'valuesep' => (string) $file->valuesep,
														'utf8' => (string) $file->utf8,
														'idby' => (string) $file->idby,
														'iffoundindb' => (string) $file->iffoundindb, // garder cette ligne pour convertir les anciens fichiers XML des clients
														'fornewcat' => (string) $file->fornewcat,
														'forfoundcat' => (string) $file->forfoundcat,
														'firstlinecontent' => (string) $file->firstlinecontent,
														'importlimit' => (string) $file->importlimit
													);
			}
		}
		// config by default
		foreach ($files AS $file)
		{
			if ($file!='' && !sc_in_array($file,array_keys($importConfig),'catWinCatImportProcess_arraykeysimportConfig') && strpos($file,'&')===false)
				$importConfig[$file]=array(
													'name' => $file,
													'mapping' => '',
													'fieldsep' => 'dcomma',
													'valuesep' => ',',
													'utf8' => '1',
													'idby' => 'catname',
													'fornewcat' => 'skip',
													'forfoundcat' => 'skip',
													'firstlinecontent' => '',
													'importlimit' => '500'
													);
		}
	}
	
	function writeCatImportConfigXML()
	{
		global $importConfig;
		$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
		$content.='<csvfiles>'."\n";
		foreach($importConfig AS $conf)
		{
			if (file_exists(SC_CSV_IMPORT_DIR."category/".$conf['name']))
			{
				$content.='<csvfile>'."\n";
				$content.='<name><![CDATA['.$conf['name'].']]></name>';
				$content.='<mapping><![CDATA['.$conf['mapping'].']]></mapping>';
				$content.='<fieldsep><![CDATA['.$conf['fieldsep'].']]></fieldsep>';
				$content.='<valuesep><![CDATA['.$conf['valuesep'].']]></valuesep>';
				$content.='<utf8><![CDATA['.$conf['utf8'].']]></utf8>';
				$content.='<idby><![CDATA['.$conf['idby'].']]></idby>';
				$content.='<fornewcat><![CDATA['.$conf['fornewcat'].']]></fornewcat>';
				$content.='<forfoundcat><![CDATA['.$conf['forfoundcat'].']]></forfoundcat>';
				$content.='<firstlinecontent><![CDATA['.$conf['firstlinecontent'].']]></firstlinecontent>';
				$content.='<importlimit><![CDATA['.$conf['importlimit'].']]></importlimit>';
				$content.='</csvfile>'."\n";
			}
		}
		$content.='</csvfiles>';
		return file_put_contents(SC_CSV_IMPORT_DIR."category/".SC_CSV_IMPORT_CONF, $content);
	}
	
	function parseCSVLine($fieldsep,$strline)
	{
		global $firstLineData;
		$strline=join($fieldsep,$firstLineData)."\r\n".$strline."\r\n";
		$csv = new parseCSV();
		$csv->delimiter = $fieldsep;
		$csv->parse($strline);
		if (count($csv->data))
		{
			$result=array_values($csv->data[0]);
		}else{
			$result=array();
		}
		if (count($result)==count($firstLineData)-1)	$result[]='';
		return $result;
	}
	
	function getBoolean($value)
	{
		if (sc_in_array(Tools::strtoupper($value),array('1','YES','TRUE','VRAI','OUI','ON'),'catWinCatImportProcess_getboolean')) return true;
		return false;
	}

	function fieldInMapping($field)
	{
		global $line,$firstLineData,$mappingData;
		$return = false;
		foreach($line AS $k => $v)
		{
			if (sc_in_array($firstLineData[$k],$mappingData['CSVArray'],'catWinCatImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]]==$field)
				$return = true;
		}
		return $return;
	}

	function findCSVLineValue($valueToFind)
	{
		global $line,$firstLineData,$mappingData;
		foreach($line AS $k => $v)
		{
			if (!sc_array_key_exists($k,$firstLineData))
				return '';
			if (sc_in_array($firstLineData[$k],$mappingData['CSVArray'],'catWinCatImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]]==$valueToFind)
				return $v;
		}
		return '';
	}

	function findCSVLineValueByLang($valueToFind,$id_lang)
	{
		global $line,$firstLineData,$mappingData,$getIDlangByISO;
		foreach($line AS $k => $v)
		{
			if (sc_in_array($firstLineData[$k],$mappingData['CSVArray'],'catWinCatImportProcess_CSVArray') && $mappingData['CSV2DB'][$firstLineData[$k]]==$valueToFind && intval($getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$k]]])==$id_lang)
				return $v;
		}
		return '';
	}

	function findAllCSVLineValue($valueToFind,&$arrayToFill,$optionToGet=null,$fromObject=null)
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
			if (sc_in_array($firstLineData[$k],$mappingData['CSVArray'],'catWinCatImportProcess_CSVArray') && sc_array_key_exists($firstLineData[$k],$mappingData['CSV2DB']) && $mappingData['CSV2DB'][$firstLineData[$k]]==$valueToFind)
			{
				//echo $firstLineData[$k].'-YESSS<br/>';
				if ($valueToFind=='attribute_multiple')
				{
					$vArray=explode($importConfig[$TODOfilename]['valuesep'],$v);
					foreach($vArray AS $val)
						@$arrayToFill[]=array(	'object' => $firstLineData[$k],
								'value' => trim($val),
								$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
								'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
								'color_attr_options'=>''
						);
				}elseif ($valueToFind=='attribute'){
					//echo 'aa<br/>';
					$attr_color=findCSVLineValue('attribute_color');
					$attr_texture=findCSVLineValue('attribute_texture');
					@$arrayToFill[]=array(		'object' => $firstLineData[$k],
							'value' => trim($v),
							$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
							'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
							'color_attr_options'=>($attr_color?$attr_color:'').'_|_'.($attr_texture?$attr_texture:'')
					);
					//echo 'bb<br/>';
				}else{
					if (($valueToFind!='feature' && $valueToFind!='feature_custom') || (($valueToFind=='feature' || $valueToFind=='feature_custom') && trim($v)!='-'))
						if(empty($fromObject) || empty($optionToGet))
						{
							@$arrayToFill[]=array(		'object' => $firstLineData[$k],
									'value' => trim($v),
									'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
									'color_attr_options'=>''
							);
						}
						else
						{
							@$arrayToFill[]=array(		'object' => $firstLineData[$k],
									'value' => trim($v),
									$optionToGet => $fromObject[$mappingData['CSV2DBOptions'][$firstLineData[$k]]],
									'option' => $mappingData['CSV2DBOptions'][$firstLineData[$k]],
									'color_attr_options'=>''
							);
						}
				}
				//echo 'cc<br/>';
			}
		}
	}

	function createMultiLangField($field)
	{
		$languages = Language::getLanguages();
		$res = array();
		foreach ($languages AS $lang)
			$res[$lang['id_lang']] = $field;
		return $res;
	}

	function copyImg($id_entity, $url, $entity = 'categories')
	{
		$tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
		$url = str_replace(" ", "%20", $url);
		switch($entity)
		{
			default:
			case 'categories':
				$path = _PS_CAT_IMG_DIR_.intval($id_entity);
				break;
		}
		$copy = copy($url, $tmpfile);
		if ($copy)
		{
			SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image)));
			$imagesTypes = ImageType::getImagesTypes($entity);
			foreach ($imagesTypes AS $k => $imageType)
				SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image),stripslashes($imageType['name'])), $imageType['width'], $imageType['height']);

			if (file_exists(_PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image))))
				SCI::hookExec('watermark', array('id_image' => $id_image, 'id_category' => $id_entity));
		}
		else
		{
			$data = sc_file_get_contents($url);
			$handle = fopen($tmpfile, "w");
			fwrite($handle, $data);
			fclose($handle);
			if (!file_exists($tmpfile))
			{
				@unlink($tmpfile);
				return false;
			}else{
				SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image)));
				$imagesTypes = ImageType::getImagesTypes($entity);
				foreach ($imagesTypes AS $k => $imageType)
					SCI::imageResize($tmpfile, _PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image),stripslashes($imageType['name'])), $imageType['width'], $imageType['height']);

				// Hook watermark optimization
				/*$result = Db::getInstance()->ExecuteS('
					SELECT m.`name` FROM `'._DB_PREFIX_.'module` m
						LEFT JOIN `'._DB_PREFIX_.'hook_module` hm ON hm.`id_module` = m.`id_module`
						LEFT JOIN `'._DB_PREFIX_.'hook` h ON hm.`id_hook` = h.`id_hook`
						WHERE h.`name` = \'watermark\' AND m.`active` = 1');
				if ($result AND sizeof($result))
					if (file_exists(_PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image))))
					foreach ($result AS $k => $module)
					if ($moduleInstance = Module::getInstanceByName($module['name']) AND is_callable(array($moduleInstance, 'hookwatermark')))
					call_user_func(array($moduleInstance, 'hookwatermark'), array('id_image' => $id_image, 'id_product' => $id_entity));*/
				if (file_exists(_PS_PROD_IMG_DIR_.getImgPath(intval($id_entity),intval($id_image))))
					SCI::hookExec('watermark', array('id_image' => $id_image, 'id_category' => $id_entity));

			}
		}
		return true;
	}

	function findImageFileName($filename)
	{
		if (strpos($filename,'http://')!==false)
			return false;
		$basefile=SC_CSV_IMPORT_DIR."category/".'images/'.$filename;
		$files=array(
				$basefile,
				$basefile.'.jpg',
				$basefile.'.png',
				$basefile.'.gif',
				$basefile.'.JPG',$basefile.'.PNG',$basefile.'.GIF',
				$basefile.'.Jpg',$basefile.'.Png',$basefile.'.Gif'
		);
		foreach($files AS $file)
			if (file_exists($file))
			return $file;
		return false;
	}

	function loadCatMapping($filename)
	{
		global $sc_agent;
		if ($filename=='')
			return '';
		if (strpos($filename,'.map.xml')===false)
			$filename=$filename.'.map.xml';
		$content='';
		if (file_exists(SC_CSV_IMPORT_DIR."category/".$filename) && $feed = simplexml_load_file(SC_CSV_IMPORT_DIR."category/".$filename))
		{
			$id_lang=(int)$feed->id_lang;
			if (!$id_lang)
				$id_lang=(int)$sc_agent->id_lang;
			foreach($feed->map AS $map)
			{
				$content.=trim((string)$map->csvname).','.trim((string)$map->dbname).','.trim((string)$map->options).';';
			}
		}
		return $content;
	}



function remove_utf8_bom($text)
{
	$bom = pack('H*','EFBBBF');
	$text = preg_replace("/^$bom/", '', $text);
	return $text;
}

function forceCategoryPathFormat($path)
{
	$tmp=explode('>',$path);
	$tmp=array_map('trim',$tmp);
	return join(' > ',$tmp);
}

$id_cat_root = Configuration::get('PS_ROOT_CATEGORY');
function getCategoryPath($id_category,$path='')
{
	global $categoryNameByID,$categoriesProperties,$id_cat_root;
	if ($id_category!=$id_cat_root)
	{
		if (!sc_array_key_exists($id_category,$categoriesProperties))
			die(_l('You should use the tool "check and fix the level_depth field" from the Catalog > Tools menu to fix your categories.').' (id_category:'.$id_category.')');
		return getCategoryPath($categoriesProperties[$id_category]['id_parent'],' > '.$categoryNameByID[$id_category].$path);
	}else{
		return trim($path,' > ');
	}
}

function checkAndCreateCategory($categList,$id_parent=1)
{
	global $languages,$categoriesFirstLevel,$categoryIDByPath,$categories,$categoryNameByID,$categoriesProperties;

	if (is_array($categList))
	{
		foreach($categList as $categ)
		{
			checkAndCreateCategory(trim($categ));
		}
	}else{
		if (strpos($categList,'>')!=false)
		{
			$categ=explode('>',$categList);
			$categ=array_map('trim',$categ);
			$levdep=1;
			foreach($categ as $k => $c)
			{
				$pathSliced=join(' > ',array_slice($categ,0,$k+1));
				if (!sc_array_key_exists(forceCategoryPathFormat($pathSliced),$categoryIDByPath))
				{
					$newCateg=new Category();
					$newCateg->id_parent=$id_parent;
					foreach($languages AS $lang)
					{
						$newCateg->name[$lang['id_lang']]=trim($c);
						$newCateg->link_rewrite[$lang['id_lang']]=link_rewrite($c);
					}
					$newCateg->level_depth=$levdep;
					$levdep++;
					$newCateg->active=(int)_s('CAT_IMPORT_CATEGCREA_ACTIVE');
					if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
					{
						$newCateg->position=SCI::getLastPositionFromCategory(1);
					}
					$newCateg->save();
					$groups = $newCateg->getGroups();
					if (!sc_in_array(1, $groups,'catWinCatImportProcess_categorygroups_'.$newCateg->id))
						$newCateg->addGroups(array(1));
					if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						$shops=Category::getShopsByCategory((int)$id_parent);
						foreach($shops AS $shop)
						{
							$position = SCI::getLastPositionFromCategory((int)$id_parent, (int)$shop['id_shop']);
							if (!$position)
								$position = 1;
							$newCateg->addPosition($position, $shop['id_shop']);
						}
					}
					$categories[trim($c)]=array('id_category' => $newCateg->id, 'id_parent' => $id_parent);
					$categoryNameByID[$newCateg->id]=$c;
					$categoriesProperties[$newCateg->id]=array('id_category' => $newCateg->id, 'id_parent' => $id_parent);
					$categoryIDByPath[getCategoryPath($newCateg->id)]=$newCateg->id;
				}
				$id_parent=$categoryIDByPath[$pathSliced];
			}
		}else{
			// create categ when no path '>' is set, to categoriesFirstLevel
			if (!sc_in_array($categList,$categoriesFirstLevel,'catWinCatImportProcess_categoriesFirstLevel'))
			{
				$newCateg=new Category();
				$newCateg->id_parent=1;
				foreach($languages AS $lang)
				{
					$newCateg->name[$lang['id_lang']]=trim($categList);
					$newCateg->link_rewrite[$lang['id_lang']]=link_rewrite($categList);
				}
				$newCateg->level_depth=1;
				$newCateg->active=(int)_s('CAT_IMPORT_CATEGCREA_ACTIVE');
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				{
					$newCateg->position=SCI::getLastPositionFromCategory(1);
				}
				$newCateg->save();
				$newCateg->addGroups(array(1));
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					$shops=Category::getShopsByCategory((int)$id_parent);
					foreach($shops AS $shop)
					{
						$position = SCI::getLastPositionFromCategory((int)$id_parent, $shop['id_shop']);
						if (!$position)
							$position = 1;
						$newCateg->addPosition($position, $shop['id_shop']);
					}
				}
				$categories[$categList]=array('id_category' => $newCateg->id, 'id_parent' => 1);
				$categoryNameByID[$newCateg->id]=$categList;
				$categoriesProperties[$newCateg->id]=array('id_category' => $newCateg->id, 'id_parent' => 1);
				$categoryIDByPath[getCategoryPath($newCateg->id)]=$newCateg->id;
				$categoriesFirstLevel[]=$categList;
			}
		}
	}
}