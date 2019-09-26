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

	error_reporting(E_ERROR);
	@ini_set('display_errors', 'on');

	$action=Tools::getValue('action');
	$id_lang=intval(Tools::getValue('id_lang'));
	$mapping=Tools::getValue('mapping','');

	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
	{
		include_once(SC_PS_PATH_DIR.'images.inc.php');
		require_once(dirname(__FILE__).'/../../../all/upload/upload-image.inc.php');
	}

	include_once(SC_DIR.'lib/php/parsecsv.lib.php');
	require_once(SC_DIR.'lib/cat/win-catimport/cat_win-catimport_tools.php');
	
	switch($action){
		case 'conf_delete':
			$imp_opt_files=Tools::getValue('imp_opt_files','');
			if ($imp_opt_files=='') die(_l('You should mark at least one file to delete'));
			$imp_opt_file_array=preg_split('/;/',$imp_opt_files);
			foreach($imp_opt_file_array as $imp_opt_file)
			{
				if ($imp_opt_file!='')
				{
					if (@unlink(SC_CSV_IMPORT_DIR."category/".$imp_opt_file))
					{
						echo $imp_opt_file." "._l('deleted')."\n";
					}else{
						echo _l("Unable to delete this file, please check write permissions:")." ".$imp_opt_file."\n";
					}
				}
			}
			break;
		case 'mapping_load':
			echo loadCatMapping(Tools::getValue('filename',''));
			break;
		case 'mapping_delete':
			$filename=str_replace('.map.xml','',Tools::getValue('filename'));
			@unlink(SC_CSV_IMPORT_DIR."category/".$filename.'.map.xml');
			break;
		case 'mapping_saveas':
			$filename=str_replace('.map.xml','',Tools::getValue('filename'));
			@unlink(SC_CSV_IMPORT_DIR."category/".$filename.'.map.xml');
			$mapping=preg_split('/;/',$mapping);
			$content='<mapping><id_lang>'.(int)$sc_agent->id_lang.'</id_lang>';
			foreach($mapping AS $map)
			{
				$val=preg_split('/,/',$map);
				if (count($val)==3)
				{
					$content.='<map>';
					$content.='<csvname><![CDATA['.$val[0].']]></csvname>';
					$content.='<dbname><![CDATA['.$val[1].']]></dbname>';
					$content.='<options><![CDATA['.$val[2].']]></options>';
					$content.='</map>';
				}
			}
			$content.='</mapping>';
			file_put_contents(SC_CSV_IMPORT_DIR."category/".$filename.'.map.xml', $content);
			echo _l('Data saved!');
			break;
		case 'mapping_process':
			echo '<div style="width: 100%; height: 100%; overflow: auto;">';
			if (_s('APP_DEBUG_CATALOG_IMPORT'))
				$time_start = microtime(true);

			if (_s('APP_DEBUG_CATALOG_IMPORT'))
			{
				// Affichage des modules greffer au hook UpdateProduct
				if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
					$hook_id = Hook::getIdByName("actionCategoryUpdate");
				else
					$hook_id = Hook::get("categoryUpdate");
				if(!empty($hook_id))
				{
					$hook = new Hook((int)$hook_id);
					$modules = $hook->getHookModuleList();
					if(!empty($modules[(int)$hook_id]))
					{
						echo "<br/><br/>Liste des modules :";
						foreach($modules[(int)$hook_id] as $module)
						{
							echo "<br/>".$module["name"]." (".($module["active"]?"Activé":"Désactivé").")";
						}
					}
				}
			}
			checkDB();
			$scdebug=false;
			global $switchObject; // variable for custom import fields check
			$switchObject='';
			global $TODO; // actions
			$TODO=array();
			global $id_category;
			$id_category=0;
			$id_category_todo=0;
			$defaultLanguageId = intval(Configuration::get('PS_LANG_DEFAULT'));
			$defaultLanguage=new Language($defaultLanguageId);
			$getIDlangByISO=array();
			$generate_hight_dpi_images = (bool)SCI::getConfigurationValue('PS_HIGHT_DPI');

			foreach($languages AS $lang)
			{
				$getIDlangByISO[$lang['iso_code']]=$lang['id_lang'];
			}
			$files = array_diff( scandir( SC_CSV_IMPORT_DIR."category/" ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
			readCatImportConfigXML($files);
			$filename=Tools::getValue('filename',0);
			$importlimit=intval(Tools::getValue('importlimit',0));
			$importlimit=($importlimit > 0 ? $importlimit : intval($importConfig[$filename]['importlimit']));
			if ($importConfig[$filename]['firstlinecontent']!='') $importlimit--;
			if ($filename===0 || $mapping=='')
				die(_l('You have to select a file and a mapping.'));

			$mapping = str_replace("&amp;", "&", $mapping);
			$mappingDataArray=explode(';',$mapping);
			$mappingData=array('CSVArray' => array(),'DBArray' => array(),'CSV2DB' => array(),'CSV2DBOptions' => array(),'CSV2DBOptionsMerged' => array());
			foreach($mappingDataArray AS $val)
			{
				if ($val!='')
				{
					$tmp=explode(',',$val);
					$tmp2=$tmp[0];
					escapeCharForPS($tmp2);
					$mappingData['CSVArray'][]=$tmp2;
					$mappingData['DBArray'][]=$tmp[1];
					$mappingData['CSV2DB'][$tmp[0]]=$tmp[1];
					$mappingData['CSV2DBOptions'][$tmp[0]]=$tmp[2];
					$mappingData['CSV2DBOptionsMerged'][$tmp[0]]=$tmp[1].'_'.$tmp[2];
				}
			}
			// check mapping
			switch ($importConfig[$filename]['idby']){
				case'catname':
					if (!sc_in_array('name_'.$defaultLanguage->iso_code,$mappingData['CSV2DBOptionsMerged'],"catWinCatImportProcess_CSV2DBOptionsMerged"))
						die(_l('Wrong mapping, mapping should contain the field name in ').$defaultLanguage->iso_code.'<br/><br/>'.
						_l('For each line of the mapping you need to double click in the Database field column to fill the mapping.').'<br/><br/>'.
						_l('If the cell in the Options column becomes blue, you need to edit this cell and complete the mapping.'));
					break;
				case'idcategory':
					if (!sc_in_array('id_category',$mappingData['DBArray'],"catWinCatImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field: id_category'));
					break;
				case'path':
					if (!sc_in_array('path',$mappingData['DBArray'],"catWinCatImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field: path'));
					break;
			}

			if($importConfig[$TODOfilename]['fornewcat']=='create')
			{
				if(!((fieldInMapping("name") && fieldInMapping("parents")) || fieldInMapping("path")))
					die(_l('Wrong mapping, mapping should contain the fields: name + parents or path'));
			}

			// create TODO file
			if (substr($filename,strlen($filename)-9,9)=='.TODO.csv' && !file_exists(SC_CSV_IMPORT_DIR."category/".$filename))
				die(_l('The TODO file has been deleted, please select the original CSV file.'));
			if (substr($filename,strlen($filename)-9,9)!='.TODO.csv')
			{
				$TODOfilename=substr($filename,0,-4).'.TODO.csv';
				if (!file_exists(SC_CSV_IMPORT_DIR."category/".$TODOfilename))
				{
					copy(SC_CSV_IMPORT_DIR."category/".$filename,SC_CSV_IMPORT_DIR."category/".$TODOfilename);
					foreach($importConfig[$filename] AS $k => $v)
					{
						$importConfig[$TODOfilename][$k]=$v;
						if ($k=='name') $importConfig[$TODOfilename][$k]=$TODOfilename;
					}
					writeCatImportConfigXML();
				}
			}else{
				$TODOfilename=$filename;
			}
			$needSaveTODO=false;

			if(empty($importConfig[$TODOfilename]['fornewcat']))
				$importConfig[$TODOfilename]['fornewcat'] = "skip";
			if(empty($importConfig[$TODOfilename]['forfoundcat']))
				$importConfig[$TODOfilename]['forfoundcat'] = "skip";

			// open csv filename
			if ($importConfig[$TODOfilename]['fieldsep']=='dcomma') $importConfig[$TODOfilename]['fieldsep']=';';
			if ($importConfig[$TODOfilename]['fieldsep']=='dcommamac') $importConfig[$TODOfilename]['fieldsep']=';';
			// get first line
			$DATAFILE=remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR."category/".$TODOfilename));
			$DATA = preg_split("/(?:\r\n|\r|\n)/", $DATAFILE);
			if ($importConfig[$TODOfilename]['firstlinecontent']!='')
			{
				$importConfig[$TODOfilename]['firstlinecontent'] = str_replace("&amp;","&",$importConfig[$TODOfilename]['firstlinecontent']);
				$firstLineData=explode($importConfig[$TODOfilename]['fieldsep'],$importConfig[$TODOfilename]['firstlinecontent']);
				$FIRST_CONTENT_LINE=0;
			}else{
				$DATA[0] = str_replace("&amp;","&",$DATA[0]);
				$firstLineData=explode($importConfig[$TODOfilename]['fieldsep'],$DATA[0]);
				$FIRST_CONTENT_LINE=1;
			}
			if (count($firstLineData)!=count(array_unique($firstLineData)))
				die(_l('Error : at least 2 columns have the same name in CSV file. You must use a unique name by column in the first line of your CSV file.'));
			foreach($firstLineData AS $key => $val)
				escapeCharForPS($firstLineData[$key]);
			$firstLineData=array_map('cleanQuotes',$firstLineData);
			if ($importConfig[$TODOfilename]['utf8'])
				utf8_encode_array($firstLineData);

			// CHECK FILE VALIDITY
			if (count($mappingData['CSVArray']) > count($firstLineData))
				die(_l('Error in mapping: too much field to import').' (CSVArray:'.count($mappingData['CSVArray']).' - firstLineData:'.count($firstLineData).')');
			foreach($mappingData['CSVArray'] AS $val)
			{
				if (!in_array($val,$firstLineData))
					die(_l('Error in mapping: the fields are not in the CSV file')._l(':').$val);
			}

			// PLACE VALUES IN CACHE

			$err='';


			// VAT second check


			if ($err!='' && $importConfig[$TODOfilename]['createelements']!=1)
				die($err.'<br/><br/>'._l('The process has been stopped before any modification in the database. You need to fix these errors first.'));

			// CHECK IF CATEGORY EXISTS
			/*$categ=Db::getInstance()->getRow("
			SELECT c.id_category
			FROM `"._DB_PREFIX_."category` c
			LEFT JOIN `"._DB_PREFIX_."category_lang` cl ON (c.`id_category` = cl.`id_category`)
			WHERE `name` = '".pSQL($TODOfilename)."'
			GROUP BY c.id_category");
			if (is_array($categ) && $categ['id_category']!='')
			{
				$id_category_todo=intval($categ['id_category']);
			}else{
				$newcategory=new Category();
				$newcategory->id_parent=Configuration::get("PS_ROOT_CATEGORY");//1;
				$newcategory->level_depth=$newcategory->calcLevelDepth();
				$newcategory->active=0;
				foreach($languages AS $lang)
				{
					$newcategory->link_rewrite[$lang['id_lang']]='import';
					$newcategory->name[$lang['id_lang']]=$TODOfilename;
				}
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				{
					$newcategory->position=SCI::getLastPositionFromCategory(1);
				}
				$newcategory->save();
				//$newcategory->addGroups(array(1));
				$id_category_todo=$newcategory->id;
			}*/

			$stats=array('created' => 0,'modified' => 0,'skipped' => 0);
			$CSVDataStr = remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR."category/".$TODOfilename));
			$CSVData = preg_split("/(?:\r\n|\r|\n)/", $CSVDataStr);
			$lastIdentifier='';
			$lastid_category=0;
			$updated_categories = array();
			$id_shop_list=array();
			$id_shop_list_default=array(1);
			if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
			{
				$id_shop_list_default=array((int)Configuration::get('PS_SHOP_DEFAULT'));
				$cache_id_shop = array();
				$cache_current_line_for_combination = array();
			}
			$imageList=array();
			$shop_home_category=array();

			$sql="SELECT c.id_category,c.id_parent,cl.name,c.level_depth
							FROM "._DB_PREFIX_."category c
							LEFT JOIN "._DB_PREFIX_."category_lang cl ON (cl.id_category=c.id_category AND cl.id_lang=".intval($defaultLanguage->id).")
							GROUP BY c.id_category
							ORDER BY c.level_depth ASC";
			$res=Db::getInstance()->ExecuteS($sql);
			$categories=array();
			$categoriesProperties=array();
			$categoryNameByID=array();
			$categoryIDByPath=array();
			$categoriesFirstLevel=array();
			foreach($res AS $categ)
			{
				if ($categ['id_category']==$categ['id_parent']) die(_l('A category cannot be parent of itself, you must fix this error for category ID').' '.$categ['id_category'].' - '.trim(hideCategoryPosition($categ['name'])));
				$categories[trim(hideCategoryPosition($categ['name']))]=array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
				$categoryNameByID[$categ['id_category']]=hideCategoryPosition($categ['name']);
				$categoriesProperties[$categ['id_category']]=array('id_category' => $categ['id_category'], 'id_parent' => $categ['id_parent']);
				$categoryIDByPath[forceCategoryPathFormat(getCategoryPath($categ['id_category']))]=$categ['id_category'];
				if ($categ['level_depth']==1) $categoriesFirstLevel[]=hideCategoryPosition($categ['name']);
			}


			$has_customergroup=false;
			for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA)-1)) && $line = parseCSVLine($importConfig[$TODOfilename]['fieldsep'],$DATA[$current_line])) && ($current_line <= $importlimit)) ; $current_line++)
			{
				if ($DATA[$current_line]=='') continue;
				if ($importConfig[$TODOfilename]['utf8']==1)
					utf8_encode_array($line);
				$line=array_map('cleanQuotes',$line);
				if (fieldInMapping("customergroups"))
					$has_customergroup=true;
				if (count($line)!=count($firstLineData))
					$err.=_l("Error on line ").($current_line+1)._l(": wrong column count: ").substr(join($importConfig[$TODOfilename]['fieldsep'],$line),0,22)." (".count($line)."-".count($firstLineData).")<br/>";
			}
			$dataDB_customergroup=array();
			$dataDB_customergroupByName=array();
			if($has_customergroup)
			{
				if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
					$DB_customergroup = Db::getInstance()->ExecuteS('SELECT g.id_group,gl.name
					FROM `'._DB_PREFIX_.'group_shop` g
						INNER JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group=gl.id_group AND gl.id_lang = '.(int)$defaultLanguageId.')
					GROUP BY g.id_group,g.id_shop');
				else
					$DB_customergroup = Db::getInstance()->ExecuteS('SELECT g.id_group,gl.name
					FROM `'._DB_PREFIX_.'group` g
						INNER JOIN `'._DB_PREFIX_.'group_lang` gl ON (g.id_group=gl.id_group AND gl.id_lang = '.(int)$defaultLanguageId.')
					GROUP BY g.id_group');
				foreach($DB_customergroup AS $customergroup)
				{
					$dataDB_customergroup[$customergroup['id_group']]=$customergroup['name'];
					$dataDB_customergroupByName[$customergroup['name']]=$customergroup['id_group'];
				}
			}


			for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA)-1)) && $line = parseCSVLine($importConfig[$TODOfilename]['fieldsep'],$DATA[$current_line])) && ($current_line <= $importlimit)) ; $current_line++)
			{
				if ($DATA[$current_line]=='') continue;
				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>Start line : $time seconds";
				}
				$extension_vars = array();
				$id_shop_list=$id_shop_list_default;
				$line=array_map('cleanQuotes',$line);
				if ($scdebug) echo 'line '.$current_line.': ';
				$line[count($line)-1]=rtrim($line[count($line)-1]);
				$TODO=array();
				$TODOSHOP=array();
				if ($importConfig[$TODOfilename]['utf8']==1)
					utf8_encode_array($line);

				$where_shop_list = "";
				if(SCMS && findCSVLineValue('id_shop_list')!='' && $importConfig[$TODOfilename]['fornewcat']=='skip' && $importConfig[$TODOfilename]['forfoundcat']=='update')
					$where_shop_list = findCSVLineValue('id_shop_list');

				$res=array();
				$id_category = 0;
				switch($importConfig[$TODOfilename]['idby'])
				{
					case 'catname':
						$name = findCSVLineValue('name');
						if(!empty($name))
						{
							$sql = "SELECT c.id_category,c.date_upd
									FROM "._DB_PREFIX_."category c
										LEFT JOIN "._DB_PREFIX_."category_lang cl on (c.id_category=cl.id_category AND cl.id_lang=".intval($defaultLanguage->id).")
									WHERE cl.name='".psql($name)."'
										".(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'category_shop cs WHERE c.id_category=cs.id_category AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
						}
						else
							$sql = "";
					break;
					case 'idcategory':
						$id_category_find = findCSVLineValue('id_category');
						if(!empty($id_category_find))
						{
							$sql="SELECT c.id_category,c.date_upd
									FROM "._DB_PREFIX_."category c
									WHERE c.id_category='".intval($id_category_find)."'
										".(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'category_shop cs WHERE c.id_category=cs.id_category AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
						}
						else
							$sql = "";
						break;
					case 'path':
						$path_find = findCSVLineValue('path');

						$c=$path_find;
						$c=forceCategoryPathFormat($c);
						$c=cleanQuotes($c);

						if ((strpos($c,'>')!==false) || in_array(trim($c),$categoriesFirstLevel)) // category path
						{
							$id_category=(sc_array_key_exists(($c),$categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
						}
						break;
					case 'specialIdentifier':
						sc_ext::readCatImportCSVConfigXML('importProcessIdentifier');
					break;
				}

				if($importConfig[$TODOfilename]['idby']!="path")
				{
					if(!empty($sql))
						$res=Db::getInstance()->executeS($sql);
					if(!empty($res[0]))
						$res = $res[0];
					else
						$res = array();
				}

				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>Results of identification queries : $time seconds";
					echo "<br/><br/>".$sql;
					echo "<br/><br/>";
					print_r($res);
					echo "<br/><br/>";
				}

				if($importConfig[$TODOfilename]['idby']!="path")
				{
					if (is_array($res) && count($res))
					{
						$id_category = $res['id_category'];
					}
				}
				if ($scdebug) echo findCSVLineValue('reference').' : '.$id_category.'<br/>';
				if ($scdebug) echo 'a';

				//$needUnitPriceRatio=(findCSVLineValue('quantity') !== '' ? true : false );

				if (SCMS)
				{
					$id_shop=(int)Configuration::get('PS_SHOP_DEFAULT');
					if (findCSVLineValue('id_shop_list')!='')
					{
						$id_shop_list_in_csv = explode(',',findCSVLineValue('id_shop_list'));
						$id_shop_list = $id_shop_list_in_csv;
						foreach($id_shop_list_in_csv as $id_shop_to_check)
						{
							if (sc_array_key_exists($id_category, $cache_id_shop) && in_array($id_shop_to_check, $cache_id_shop[$id_category]))
								continue;
							$id_shop = $id_shop_to_check;
						}
					}
				}
				else
					$id_shop=(int)Configuration::get('PS_SHOP_DEFAULT');

				$id_category_home = Configuration::get("PS_ROOT_CATEGORY");
				if(SCMS)
				{
					if(empty($shop_home_category[$id_shop]))
					{
						$shop = new Shop((int)$id_shop);
						$shop_home_category[$id_shop] = $shop->id_category;
					}
					if(!empty($shop_home_category[$id_shop]))
					{
						$id_category_home = $shop_home_category[$id_shop];
					}
				}


                if (SCMS)
                {
                    $_POST['checkBoxShopAsso_category'] = array();
                    foreach ($id_shop_list as $id)
                        $_POST['checkBoxShopAsso_category'][$id] = $id;
                }

				// CATEGORY EXISTANTE
				if(!empty($id_category))
				{
					// IGNORE LIGNE
					if($importConfig[$TODOfilename]['forfoundcat']=='skip')
					{
						$stats['skipped']++;
						if (_s('CAT_IMPORT_IGNORED_LINES')==1)
						{
							unset($CSVData[$current_line]);
							$needSaveTODO=true;
						}
						continue;
					}
					// MODIFIE LA CATEGORY
					elseif($importConfig[$TODOfilename]['forfoundcat']=='update')
					{
						if (SCMS)
						{
							$newcat=new Category($id_category, null, $id_shop);
							if ($id_shop==null)
								$id_shop_list=array($newcat->id_shop_default);
						}
						elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							$newcat=new Category($id_category, null,1);
						else
							$newcat=new Category($id_category);
						$newcat->date_upd = date("Y-m-d H:i:s");

                        $newcat->groupBox = $newcat->getGroups();

						$stats['modified']++;
					}
					// CREE DOUBLON
					/*elseif($importConfig[$TODOfilename]['forfoundcat']=='create')
					{
						if (findCSVLineValue('name')=='') // TODO ajouter msg erreur
						{
							$stats['skipped']++;
							if (_s('CAT_IMPORT_IGNORED_LINES')==1)
							{
								unset($CSVData[$current_line]);
								$needSaveTODO=true;
							}
							continue;
						}
						// if combination with same identifier
						if ($importConfig[$TODOfilename]['idby']=='catname'
								&& isCombination()
								&& $lastIdentifier==findCSVLineValue('name')
						)
						{
							if (SCMS)
							{
								$newcat=new Category($lastid_category, null, $id_shop);
								if ($id_shop==null)
									$id_shop_list=array($newcat->id_shop_default);
							}
							elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newcat=new Category($lastid_category, null,1);
							else
								$newcat=new Category($lastid_category);
							$stats['modified']++;
						}else{
							// create new category with default values
							$newcat=new Category();
							$newcat->id_parent=$id_category_todo;
							$newcat->active=0;
							$newcat->link_rewrite[$defaultLanguage->id]='new cat';
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newcat->id_shop_default = $id_shop_list[0];
							foreach($languages AS $lang)
							{
								$newcat->link_rewrite[$lang['id_lang']]='category';
								$newcat->name[$lang['id_lang']]='category';
								$newcat->description[$lang['id_lang']]='';
							}
							$stats['created']++;
						}
					}*/
				}
				// NOUVELLE CATEGORY
				elseif(empty($id_category))
				{
					// IGNORE LIGNE
					if($importConfig[$TODOfilename]['fornewcat']=='skip')
					{
						$stats['skipped']++;
						if (_s('CAT_IMPORT_IGNORED_LINES')==1)
						{
							unset($CSVData[$current_line]);
							$needSaveTODO=true;
						}
						continue;
					}
					// CREE NOUVELLE CATEGORIE
					elseif($importConfig[$TODOfilename]['fornewcat']=='create' || $importConfig[$TODOfilename]['fornewcat']=='createall')
					{
						$id_parent = 0;
						$name_cat = "category";
						if(fieldInMapping("path"))
						{
							if (findCSVLineValue('path')=='') // TODO ajouter msg erreur
							{
								$stats['skipped']++;
								if (_s('CAT_IMPORT_IGNORED_LINES')==1)
								{
									unset($CSVData[$current_line]);
									$needSaveTODO=true;
								}
								continue;
							}
							else
							{
								$c = findCSVLineValue("path");
								$c = cleanQuotes($c);
								$c = forceCategoryPathFormat($c);
								$idc = 0;

								// IF CATEGORY HAS PARENTS
								if (strpos($c, '>') !== false)
								{
									$exp = explode(">", $c);
									$name_cat = $exp[count($exp) - 1];
									array_pop($exp);
									$c = implode(">", $exp);
									$c = forceCategoryPathFormat($c);

									if ((strpos($c, '>') !== false) || (in_array(trim($c), $categoriesFirstLevel))) // category path
									{
										$idc = (sc_array_key_exists(($c), $categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
									}
									if ($idc != 0) // IF PARENTS EXIST
									{
										$id_parent = intval($idc);
									} //  IF PARENTS NOT EXIST & NOT CREATE THEM
									elseif ($importConfig[$TODOfilename]['fornewcat'] == 'create') {
										$stats['skipped']++;
										if (_s('CAT_IMPORT_IGNORED_LINES') == 1) {
											unset($CSVData[$current_line]);
											$needSaveTODO = true;
										}
										continue;
									} //  IF PARENTS NOT EXIST & CREATE THEM
									elseif ($importConfig[$TODOfilename]['fornewcat'] == 'createall') {
										checkAndCreateCategory($c);

										if ((strpos($c, '>') !== false) || (in_array(trim($c), $categoriesFirstLevel))) // category path
										{
											$idc = (sc_array_key_exists(($c), $categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
										}
										if ($idc != 0) {
											$id_parent = intval($idc);
										} else {
											$stats['skipped']++;
											if (_s('CAT_IMPORT_IGNORED_LINES') == 1) {
												unset($CSVData[$current_line]);
												$needSaveTODO = true;
											}
											continue;
										}
									}
								}
								else
								{
									$id_parent = $id_category_home;
									$name_cat = trim($c);
								}
							}
						}
						elseif(fieldInMapping("name") && fieldInMapping("parents"))
						{
							if (findCSVLineValue('name')=='') // TODO ajouter msg erreur
							{
								$stats['skipped']++;
								if (_s('CAT_IMPORT_IGNORED_LINES')==1)
								{
									unset($CSVData[$current_line]);
									$needSaveTODO=true;
								}
								continue;
							}
							else
								$name_cat = trim(findCSVLineValue('name'));
							if (findCSVLineValue('parents')!='')
							{
								$c=findCSVLineValue('parents');
								$c=forceCategoryPathFormat($c);
								$c=cleanQuotes($c);
								$idc=0;

								if ((strpos($c,'>')!==false) || in_array(trim($c),$categoriesFirstLevel)) // category path
								{
									$idc=(sc_array_key_exists(($c),$categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
								}

								if ($idc!=0) // IF PARENTS EXIST
								{
									$id_parent=intval($idc);
								}
								//  IF PARENTS NOT EXIST & NOT CREATE THEM
								elseif($importConfig[$TODOfilename]['fornewcat']=='create')
								{
									$stats['skipped']++;
									if (_s('CAT_IMPORT_IGNORED_LINES')==1)
									{
										unset($CSVData[$current_line]);
										$needSaveTODO=true;
									}
									continue;
								}
								//  IF PARENTS NOT EXIST & CREATE THEM
								elseif($importConfig[$TODOfilename]['fornewcat']=='createall')
								{
									checkAndCreateCategory($c);

									if ((strpos($c,'>')!==false) || (in_array(trim($c),$categoriesFirstLevel))) // category path
									{
										$idc=(sc_array_key_exists(($c),$categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
									}
									if ($idc!=0)
									{
										$id_parent=intval($idc);
									}
									else
									{
										$stats['skipped']++;
										if (_s('CAT_IMPORT_IGNORED_LINES')==1)
										{
											unset($CSVData[$current_line]);
											$needSaveTODO=true;
										}
										continue;
									}
								}

							}
							else
								$id_parent = $id_category_home;

						}

						if (empty($id_parent)) // TODO ajouter msg erreur
						{
							$stats['skipped']++;
							if (_s('CAT_IMPORT_IGNORED_LINES')==1)
							{
								unset($CSVData[$current_line]);
								$needSaveTODO=true;
							}
							continue;
						}
						if(empty($name_cat))
							$name_cat = "Category";

						// create new category with default values
						if (SCMS)
                        {
                            $newcat=new Category(null, null, $id_shop_list[0]);
                        }
                        elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            $newcat=new Category(null, null,1);
                        else
                            $newcat=new Category();
						//$newcat->id_parent=$id_category_todo;
						$newcat->id_parent=$id_parent;
						$newcat->active=(int)_s('CAT_IMPORT_CATEGCREA_ACTIVE');
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            $newcat->id_shop_default = $id_shop_list[0];
						$newcat->name[$defaultLanguage->id]=trim($name_cat);
						$newcat->link_rewrite[$defaultLanguage->id]=link_rewrite(($name_cat));
						foreach($languages AS $lang)
						{
							$newcat->name[$lang['id_lang']]=trim($name_cat);
							$newcat->link_rewrite[$lang['id_lang']]=link_rewrite(($name_cat));
							$newcat->description[$lang['id_lang']]='';
						}
						$stats['created']++;
					}
				}

				if (SCMS)
				{
					if(empty($newcat->id))
						$newcat->id_shop_list=$id_shop_list;
					else
						$newcat->id_shop_list=$id_shop;
					if(!is_array($newcat->id_shop_list))
						$newcat->id_shop_list = array($newcat->id_shop_list);
				}

				foreach($line AS $key => $value)
				{
					$value=trim($value);
					$GLOBALS['import_value']=$value;
					if ($scdebug && !sc_array_key_exists($key,$firstLineData)) echo 'ERR'.$key.'x'.$current_line.'x'.join(';',$line).'xxx'.join(';',array_keys($firstLineData)).'<br/>';
					if (sc_array_key_exists($key,$firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinCatImportProcess_CSVArray"))
					{
						if ($scdebug) echo 'c';
						@$id_lang=intval($getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
						$switchObject=$mappingData['CSV2DB'][$firstLineData[$key]];
						switch($switchObject)
						{
							case 'active':$newcat->active=intval(getBoolean($value));break;
							case 'position':$newcat->position=intval($value);break;
							case 'id_parent':
								if(Category::categoryExists((int)$value))
									$newcat->id_parent=intval($value);
								break;
							case 'link_rewrite':if ($value!='') $newcat->link_rewrite[$id_lang]=link_rewrite($value);break;
							case 'name':
								escapeCharForPS($value);
								$newcat->name[$id_lang]=$value;
								if (!sc_in_array('link_rewrite',$mappingData['DBArray'],"catWinCatImportProcess_DBArray"))
								{
									foreach($languages AS $lang)
									{
										if(_s('CAT_SEO_NAME_TO_URL'))
											$newcat->link_rewrite[$id_lang]=link_rewrite($value);
									}
								}
								break;
							case 'description':
							escapeCharForPS($value,true);
							$newcat->description[$id_lang]=$value;break;
							case 'meta_title':escapeCharForPS($value);$newcat->meta_title[$id_lang]=$value;break;
							case 'meta_description':escapeCharForPS($value);$newcat->meta_description[$id_lang]=$value;break;
							case 'meta_keywords':escapeCharForPS($value);$newcat->meta_keywords[$id_lang]=$value;break;
							case 'id_shop_default':$newcat->id_shop_default=(int)$value;break;
							/*case 'parents':
								$value=trim(trim(trim($value),'>'));
								if ($value!='' && !empty($newcat->id))
								{
									$c=$value;
									//checkAndCreateCategory($c);
									$c=forceCategoryPathFormat($c);
									$c=cleanQuotes($c);
									$idc=0;

									if ((strpos($c,'>')!==false) || (in_array(trim($c),$categoriesFirstLevel))) // category path
									{
										$idc=(sc_array_key_exists(($c),$categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
									}
									if ($idc!=0)
									{
										$newcat->id_parent=intval($idc);
									}
								}
								break;
							case 'path':
								$value=trim(trim(trim($value),'>'));
								if ($value!='' && !empty($newcat->id))
								{
									$c=$value;

									//checkAndCreateCategory($c);
									$c=cleanQuotes($c);
									$c=forceCategoryPathFormat($c);
									$idc=0;

									// IF CATEGORY HAS PARENTS
									if(strpos($c,'>')!==false)
									{
										$exp = explode(">",$c);
										$name_cat = $exp[count($exp)-1];
										array_pop($exp);
										$c = implode(">",$exp);
										$c=forceCategoryPathFormat($c);

										if ((strpos($c,'>')!==false) || (in_array(trim($c),$categoriesFirstLevel))) // category path
										{
											$idc=(sc_array_key_exists(($c),$categoryIDByPath) ? intval($categoryIDByPath[($c)]) : 0);
										}
										if ($idc!=0)
										{
											//$newcat->id_parent=intval($idc);
											foreach($languages AS $lang)
												$newcat->name[$lang['id_lang']]=$name_cat;
										}
									}
									else
									{
										// IF CATEGORY NOT EXIST
										if(!( (in_array(trim($c),$categoriesFirstLevel)) || sc_array_key_exists(forceCategoryPathFormat($c),$categoryIDByPath) ))
										{
											//$newcat->id_parent=$id_category_home;
											foreach($languages AS $lang)
												$newcat->name[$lang['id_lang']]=$c;
										}
									}
								}
								break;*/
							default:
								sc_ext::readCatImportCSVConfigXML('importProcessCategory');
						}
/* inutile pour le moment, on gère champ par champ
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')
							&& ($def = ObjectModel::getDefinition('Product'))
							&& isset($def['fields'][$switchObject]['shop'])
							&& $def['fields'][$switchObject]['shop'])
						{
							$TODOSHOP[]='`'.$field."`='".psql($value)."'";
						}*/

					}
				}
				if ($scdebug) echo 'd';
				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>Before category->save : $time seconds";
				}
				foreach($languages AS $lang)
					if ($newcat->link_rewrite[$lang['id_lang']]=='')
						$newcat->link_rewrite[$lang['id_lang']] = 'category';

				if ($newcat->save())
				{
					$categories[trim(hideCategoryPosition($newcat->name[$defaultLanguage->id]))]=array('id_category' => $newcat->id, 'id_parent' => $newcat->id_parent);
					$categoryNameByID[$newcat->id]=hideCategoryPosition($newcat->name[$defaultLanguage->id]);
					$categoriesProperties[$newcat->id]=array('id_category' => $newcat->id, 'id_parent' => $newcat->id_parent);
					$categoryIDByPath[forceCategoryPathFormat(getCategoryPath($newcat->id))]=$newcat->id;
					if ($newcat->level_depth==1) $categoriesFirstLevel[]=hideCategoryPosition($newcat->name[$defaultLanguage->id]);

					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>After product->save : $time seconds";
					}
					$lastid_category=$newcat->id;
					if (_s('APP_COMPAT_HOOK'))
						SCI::hookExec('categoryUpdate', array('category' => $newcat));
					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>After categoryUpdate Hook : $time seconds";
					}
					if ($scdebug) echo 'e';

					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>Before category TODO  : $time seconds";
					}
					$TODO[]="UPDATE "._DB_PREFIX_."category SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_category=".intval($newcat->id)."";
					/*if(SCMS)
						$TODO[]="UPDATE "._DB_PREFIX_."category_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_category=".intval($newcat->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";*/
					foreach($TODO AS $sql)
					{
						$sql=str_replace('ID_PRODUCT',$newcat->id,$sql);
						Db::getInstance()->Execute($sql);
					}
					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>After category TODO : $time seconds";
					}

					if(!empty($newcat->id))
					{
						if (SCMS)
						{
							$newcat=new Category($newcat->id, null, $id_shop);
							if ($id_shop==null)
								$id_shop_list=array($newcat->id_shop_default);
						}
						elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							$newcat=new Category($newcat->id, null,1);
						else
							$newcat=new Category($newcat->id);
						$newcat->date_upd = date("Y-m-d H:i:s");
					}

					$imagesListFromCSVLine=array(); // contains images to link to the current combination
					$imagesListFromDB=array(); // contains images of current product

					sc_ext::readCatImportCSVConfigXML('importProcessInitRowVars');
					foreach($line AS $key => $value)
					{
						$GLOBALS['import_value']=$value;
						$TODO=array();
						if ($scdebug && !in_array($key,array_keys($firstLineData))) {print_r($line);}
						if (sc_array_key_exists($key,$firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinCatImportProcess_CSVArray"))
						{
							@$id_lang=intval($getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
							$switchObject=$mappingData['CSV2DB'][$firstLineData[$key]];
							if (_s('APP_DEBUG_CATALOG_IMPORT'))
							{
								$time_end = microtime(true);
								$time = $time_end - $time_start;
								echo "<br/><br/>Before ".$switchObject." column : $time seconds";
							}
							switch($switchObject)
							{
								case 'ActionCleanGroups':
									if ($value!='' && $value!='0' && $newcat->id)
									{
										$newcat->cleanGroups();
									}
									break;
								case 'customergroups':
									if ($value!='' && $value!='0' && $newcat->id)
									{
										$group_list = explode($importConfig[$TODOfilename]['valuesep'],$value);
                                        $groups = array();
                                        $already_groups = array();
										if(!empty($newcat->id))
                                            $already_groups = $newcat->getGroups();
										foreach($group_list as $group)
										{
											$id_group = (sc_array_key_exists($group,$dataDB_customergroupByName)? intval($dataDB_customergroupByName[$group]):0);

											if(!empty($id_group) && !in_array((int)$id_group, $already_groups))
												$groups[] = $id_group;
										}
										if(!empty($groups) && count($groups)>0)
											$newcat->addGroups($groups);
									}
									break;
								case 'ActionDeleteImages':
									if ($value!='' && $value!='0' && $newcat->id)
									{
										$newcat->deleteImage(true);
									}
									break;
								case 'imageURL':
									if ($value!='')
									{
										$imagefilename=findImageFileName($value);
										if($imagefilename==false)
										{
											$imagefilename = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
											$copy = copy($value, $imagefilename);
										}
										$image_name = _PS_CAT_IMG_DIR_.(int)$newcat->id.'.jpg';

										@unlink($image_name);
										if(copy ( $imagefilename , $image_name ))
										{
											$images_types = ImageType::getImagesTypes('categories');
											foreach ($images_types as $k => $image_type)
											{
												if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
												{
													ImageManager::resize(
														$image_name,
														_PS_CAT_IMG_DIR_.$newcat->id.'-'.stripslashes($image_type['name']).'.jpg',
														(int)$image_type['width'], (int)$image_type['height']
													);

													if($generate_hight_dpi_images)
														ImageManager::resize(
															$image_name,
															_PS_CAT_IMG_DIR_.$newcat->id.'-'.stripslashes($image_type['name']).'2x.jpg',
															(int)$image_type['width']*2, (int)$image_type['height']*2
														);
												}
												else
													imageResize($image_name, _PS_CAT_IMG_DIR_.$newcat->id.'-'.stripslashes($image_type['name']).'.jpg', (int)($image_type['width']), (int)($image_type['height']));
											}
										}
									}
									break;
								default:
									sc_ext::readCatImportCSVConfigXML('importProcessCombination');
							}

							$TODO[]="UPDATE "._DB_PREFIX_."category SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_category=".intval($newcat->id)."";
							/*if(SCMS)
								$TODO[]="UPDATE "._DB_PREFIX_."category_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_category=".intval($newcat->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";*/

							foreach($TODO AS $sql)
								Db::getInstance()->Execute($sql);

							if (_s('APP_DEBUG_CATALOG_IMPORT'))
							{
								$time_end = microtime(true);
								$time = $time_end - $time_start;
								echo "<br/><br/>After ".$switchObject." column : $time seconds";
							}
						}



          		}// foreach cols
          		if (_s('APP_DEBUG_CATALOG_IMPORT'))
          		{
          			$time_end = microtime(true);
          			$time = $time_end - $time_start;
          			echo "<br/><br/>Fields processins after category creation : $time seconds";
          		}

				if(!empty($skip_line_scas))
					continue;

				foreach($line AS $key => $value)
				{
					$value = trim($value);
					if (sc_array_key_exists($key, $firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinCatImportProcess_CSVArray")
					) {

						$switchObject = $mappingData['CSV2DB'][$firstLineData[$key]];
						sc_ext::readCatImportCSVConfigXML('importProcessAfterCreateAll');
					}
				}

					if (SCMS && $importConfig[$TODOfilename]['forfoundcat']!='skip' && ( (!sc_array_key_exists($id_category,$cache_id_shop) && count($id_shop_list_in_csv)>1) || count($cache_id_shop[$id_category]) < count($id_shop_list_in_csv)-1))
					{
						//mise en cache
						$cache_id_shop[$id_category][] = $id_shop;
						$current_line--;
					}else{
						unset($CSVData[$current_line]);
						file_put_contents(SC_CSV_IMPORT_DIR."category/".$TODOfilename,join("\n",$CSVData));
					}
					$needSaveTODO=false;
				}else{
					if ($scdebug) echo 'Z<br/>';
				}
				if ($scdebug) echo 'f<br/>';

				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>Total time for the line: $time seconds";
					if(!empty($id_product))
						die();
				}
				$updated_categories[$id_category]=$id_category;
			} // FIN BOUCLE LINE

			// PM Cache
			if(!empty($updated_categories))
				ExtensionPMCM::clearFromIdsCategory($updated_categories);

			// DELETE TODO CATEGORY
			/*if(!empty($id_category_todo))
			{
				$sql="SELECT COUNT(id_category) AS nb FROM "._DB_PREFIX_."category WHERE id_parent = ".intval($id_category_todo);
				$res = Db::getInstance()->executeS($sql);
				if(empty($res[0]["nb"]))
				{
					$categoryTODO = new Category((int)$id_category_todo);
					if (SCMS)
					{
						$categoryTODO->id_shop_list = $categoryTODO->getAssociatedShops();
						$categoryTODO->deleteLite();
						$categoryTODO->deleteImage(true);
						$categoryTODO->cleanGroups();
						$categoryTODO->cleanAssoProducts();
						// Delete associated restrictions on cart rules
						CartRule::cleanProductRuleIntegrity('categories', array($categoryTODO->id));
						/* Delete Categories in GroupReduction */
						/*if (GroupReduction::getGroupsReductionByCategoryId((int)$categoryTODO->id))
							GroupReduction::deleteCategory($categoryTODO->id);
						Hook::exec('actionCategoryDelete', array('category' => $categoryTODO));
					}
					else
						$categoryTODO->delete();
					$sql="SELECT (MAX(id_category)+1) as maxId FROM "._DB_PREFIX_."category";
					$resMax = Db::getInstance()->executeS($sql);
					if(!empty($resMax[0]["maxId"]) && is_numeric($resMax[0]["maxId"]))
					{
						$sql="ALTER TABLE "._DB_PREFIX_."category AUTO_INCREMENT = ".(int)$resMax[0]["maxId"];
						@Db::getInstance()->execute($sql);
					}
				}
			}*/

			fixLevelDepth();
			if (version_compare(_PS_VERSION_, '1.4.0.17', '>='))
				Category::regenerateEntireNtree();

			if ($needSaveTODO)
				file_put_contents(SC_CSV_IMPORT_DIR."category/".$TODOfilename,join("\n",$CSVData));
			echo '<b>'._l('Stats:').'</b><br/>';
			$msg = _l('New categories:').' '.$stats['created'].'<br/>';
			$msg.= _l('Modified categories:').' '.$stats['modified'].'<br/>';
			$msg.= _l('Skipped lines:').' '.$stats['skipped'].'<br/>';

			echo $msg.'<br/>';
			if ((count($CSVData)==1) || (count($CSVData)==2 && $CSVData[0]==join('',$CSVData)) || (filesize(SC_CSV_IMPORT_DIR."category/".$TODOfilename)==0))
			{
				@unlink(SC_CSV_IMPORT_DIR."category/".$TODOfilename);
				echo _l('All categories have been imported. The TODO file is deleted.').'<br/><br/>';
				echo '<b>'._l('End of import process.').'</b><br/><br/>';
				echo '<b>'._l('You need to refresh the page, click here:').' <a target="_top" href="index.php">Go!</a></b><br/>';
				echo '<script type="text/javascript">window.top.displayCatOptions();window.top.stopCatAutoImport(true);</script>';
				$msg2 = 'All categories have been imported.';
			}else{
				echo '<b>'._l('There are still categories to be imported in the working file. It can mean errors you need to correct or lines which have been ignored on purpose. Once corrections have been made, click again on the import icon to proceed further.').'</b><br/><br/>';
				echo '<script type="text/javascript">window.top.displayCatOptions();autoCatImportUnit=999999;window.top.prepareCatNextStep('.($stats['created']+$stats['modified']+$stats['skipped']==0?0:filesize(SC_CSV_IMPORT_DIR."category/".$TODOfilename)).');</script>';
				// TODO add autoImportUnit=999999; to boost import
				$msg2 = 'Need fix and run import again.';
			}
			$msg3='';
			addToHistory('catalog_catimport','catimport','','','','','Imported file: '.$TODOfilename.'<br/>'.$msg.$msg2.($msg3!=''?'<br/>'.$msg3:''),'');
			if (_s('APP_DEBUG_CATALOG_IMPORT'))
			{
				$time_end = microtime(true);
				$time = $time_end - $time_start;
				echo "<br/><br/>Total: $time seconds";
			}
			echo '</div>';
			break;
		}
