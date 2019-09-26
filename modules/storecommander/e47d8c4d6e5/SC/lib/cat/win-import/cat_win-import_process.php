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
	$create_categories=intval(Tools::getValue('create_categories',-1));

	if(!empty($CRON) && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
		Context::getContext()->employee = new Employee( Tools::getValue('id_employee') );

	if (!isset($CRON)) $CRON=0;

	if(SCAS)
		$stock_manager = StockManagerFactory::getManager();

	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
		include_once(SC_PS_PATH_DIR.'images.inc.php');

	include_once(SC_DIR.'lib/php/parsecsv.lib.php');
	require_once(SC_DIR.'lib/cat/win-import/cat_win-import_tools.php');
	
	switch($action){
		case 'conf_delete':
			$imp_opt_files=Tools::getValue('imp_opt_files','');
			if ($imp_opt_files=='') die(_l('You should mark at least one file to delete'));
			$imp_opt_file_array=preg_split('/;/',$imp_opt_files);
			foreach($imp_opt_file_array as $imp_opt_file)
			{
				if ($imp_opt_file!='')
				{
					if (@unlink(SC_CSV_IMPORT_DIR.$imp_opt_file))
					{
						echo $imp_opt_file." "._l('deleted')."\n";
					}else{
						echo _l("Unable to delete this file, please check write permissions:")." ".$imp_opt_file."\n";
					}
				}
			}
			break;
		case 'mapping_load':
			echo loadMapping(Tools::getValue('filename',''));
			break;
		case 'addSupplier':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				addSupplier($data);
				die('<b>'._l('Supplier(s) created!').'</b>');
			}
			break;
		case 'addManufacturer':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				addManufacturer($data);
				die('<b>'._l('Manufacturer(s) created!').'</b>');
			}
			break;
		case 'addFeature':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				$features=explode("y|y",$data);
				sort($features);
				foreach($features AS $feature)
				{
					if ($feature!='')
					{
						$newFeature=new Feature();
						$newFeature->name[intval(SCI::getConfigurationValue('PS_LANG_DEFAULT'))]=trim(cleanQuotes(Tools::substr($feature,0,128)));
						$newFeature->save();
					}
				}
				die('<b>'._l('Feature(s) created!').'</b>');
			}
			break;
		case 'addFeatureValue':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				addFeatureValue($data);
				die('<b>'._l('Feature(s) created!').'</b>');
			}
			break;
		case 'addAttributeGroup':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				$groups=explode("y|y",$data);
				sort($groups);
				foreach($groups AS $group)
				{
					if ($group!='')
					{
						$newGroup=new AttributeGroup();
						if (version_compare(_PS_VERSION_, '1.5.0', '>='))
						{
							foreach($languages AS $lang)
							{
                                $newGroup->name[$lang['id_lang']]=cleanQuotes(Tools::substr($group,0,64));
                                $newGroup->public_name[$lang['id_lang']]=cleanQuotes(Tools::substr($group,0,64));
							}
                            $newGroup->position=AttributeGroup::getHigherPosition()+1;
                            $newGroup->group_type='select';
						}
                        $newGroup->add();
					}
				}
				die('<b>'._l('Attribute group(s) created!').'</b>');
			}
			break;
		case 'addAttributeValue':
			$data=Tools::getValue('data','');
			if ($data!='')
			{
				addAttributeValue($data);
				die('<b>'._l('Attribute(s) created!').'</b>');
			}
			break;
		case 'mapping_process':
			echo '<div id="outputResult" style="height:100%;overflow:auto;">';
			if (_s('APP_DEBUG_CATALOG_IMPORT'))
				$time_start = microtime(true);

			if (_s('APP_DEBUG_CATALOG_IMPORT'))
			{
				// Affichage des modules greffer au hook UpdateProduct
				if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
					$hook_id = Hook::getIdByName("actionProductUpdate");
				else
					$hook_id = Hook::get("actionProductUpdate");
				if(!empty($hook_id))
				{
					$hook = new Hook((int)$hook_id);
					$modules = $hook->getHookModuleList();
					if(!empty($modules[(int)$hook_id]))
					{
						echo "<br/><br/>"._l('Module list')." :";
						foreach($modules[(int)$hook_id] as $module)
						{
							echo "<br/>".$module["name"]." (".($module["active"]?_l('Enabled'):_l('Disabled')).")";
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
			global $id_product,$id_product_attribute;
			$id_product=0; $id_product_attribute=0;
			$warehousesArray = array();
			$productsStockAdvancedTypeArray = array();
			$defaultLanguageId = intval(SCI::getConfigurationValue('PS_LANG_DEFAULT'));
			$defaultLanguage=new Language($defaultLanguageId);
			$getIDlangByISO=array();

			foreach($languages AS $lang)
			{
				$getIDlangByISO[$lang['iso_code']]=$lang['id_lang'];
			}
			$files = array_diff( scandir( SC_CSV_IMPORT_DIR ), array_merge( Array( ".", "..", "index.php", ".htaccess", SC_CSV_IMPORT_CONF)) );
			readImportConfigXML($files);
			$filename=Tools::getValue('filename',0);
			if ($create_categories <= 0) $create_categories=intval($importConfig[$filename]['createcategories']);
			$importlimit=intval(Tools::getValue('importlimit',0));
			$importlimit=($importlimit > 0 ? $importlimit : intval($importConfig[$filename]['importlimit']));
			if ($importConfig[$filename]['firstlinecontent']!='') $importlimit--;
			if ($CRON) $mapping=loadMapping($importConfig[$filename]['mapping']);
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
				case'prodname':
					if (!sc_in_array('name_'.$defaultLanguage->iso_code,$mappingData['CSV2DBOptionsMerged'],"catWinImportProcess_CSV2DBOptionsMerged"))
						die(_l('Wrong mapping, mapping should contain the field name in ').$defaultLanguage->iso_code.'<br/><br/>'.
						_l('For each line of the mapping you need to double click in the Database field column to fill the mapping.').'<br/><br/>'.
						_l('If the cell in the Options column becomes blue, you need to edit this cell and complete the mapping.'));
					break;
				case'prodref':
					if (!sc_in_array('reference',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field reference'));
					break;
				case'prodrefthenprodname':
					if (!sc_in_array('reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") || !sc_in_array('name_'.$defaultLanguage->iso_code,$mappingData['CSV2DBOptionsMerged'],"catWinImportProcess_CSV2DBOptionsMerged"))
						die(_l('Wrong mapping, mapping should contain the fields reference and name in ').$defaultLanguage->iso_code);
					break;
				case'suprefthenprodname':
					if (!sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") || !sc_in_array('name_'.$defaultLanguage->iso_code,$mappingData['CSV2DBOptionsMerged'],"catWinImportProcess_CSV2DBOptionsMerged"))
						die(_l('Wrong mapping, mapping should contain the fields supplier_reference and name in ').$defaultLanguage->iso_code);
					break;
				case'supref':
					if (!sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field supplier_reference'));
					break;
				case'prodrefandsupref':
					if (!sc_in_array('reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") || !sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the fields reference and supplier_reference'));
					break;
				case'prodnameandsupref':
					if (!sc_in_array('name_'.$defaultLanguage->iso_code,$mappingData['CSV2DBOptionsMerged'],"catWinImportProcess_CSV2DBOptionsMerged") || !sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the fields supplier_reference and name in ').$defaultLanguage->iso_code);
					break;
				case'idproduct':
					if (!sc_in_array('id_product',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field: id_product'));
					break;
				case'idproductattribute':
					if (!sc_in_array('id_product_attribute',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the field id_product_attribute'));
					break;
				case'ean13':
					if (!sc_in_array('EAN13',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
						die(_l('Wrong mapping, mapping should contain the EAN field'));
					break;
                case'upc':
                    if (!sc_in_array('upc',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
                        die(_l('Wrong mapping, mapping should contain the UPC field'));
                    break;
                case'isbn':
                    if (!sc_in_array('isbn',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
                        die(_l('Wrong mapping, mapping should contain the ISBN field'));
                    break;
			}

			if (SCMS) {
				if (!sc_in_array('id_shop_list', $mappingData['DBArray'], 'catWinImportCheck_idShopList_in_DBArray')) {
					die(_l("id_shop_list is required in multistore mode."));
				}
			}

			// create TODO file
			if (substr($filename,strlen($filename)-9,9)=='.TODO.csv' && !file_exists(SC_CSV_IMPORT_DIR.$filename))
				die(_l('The TODO file has been deleted, please select the original CSV file.'));
			if (substr($filename,strlen($filename)-9,9)!='.TODO.csv')
			{
				$TODOfilename=substr($filename,0,-4).'.TODO.csv';
				if (!file_exists(SC_CSV_IMPORT_DIR.$TODOfilename))
				{
					copy(SC_CSV_IMPORT_DIR.$filename,SC_CSV_IMPORT_DIR.$TODOfilename);
					foreach($importConfig[$filename] AS $k => $v)
					{
						$importConfig[$TODOfilename][$k]=$v;
						if ($k=='name') $importConfig[$TODOfilename][$k]=$TODOfilename;
					}
					writeImportConfigXML();
				}
			}else{
				$TODOfilename=$filename;
			}
			$needSaveTODO=false;

			if(empty($importConfig[$TODOfilename]['fornewproduct']))
				$importConfig[$TODOfilename]['fornewproduct'] = "skip";
			if(empty($importConfig[$TODOfilename]['forfoundproduct']))
				$importConfig[$TODOfilename]['forfoundproduct'] = "skip";

			// open csv filename
			if ($importConfig[$TODOfilename]['fieldsep']=='dcomma') $importConfig[$TODOfilename]['fieldsep']=';';
            if ($importConfig[$TODOfilename]['fieldsep']=='dcommamac') $importConfig[$TODOfilename]['fieldsep']=';';
            if ($importConfig[$TODOfilename]['fieldsep']=='tab') $importConfig[$TODOfilename]['fieldsep']=" ";
			// get first line
			$DATAFILE=remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR.$TODOfilename));
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
			if(count($firstLineData)==1 && $importConfig[$TODOfilename]['fieldsep']==" ")
            {
                $importConfig[$TODOfilename]['fieldsep']="\t";
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
			if (
					sc_in_array('feature',$mappingData['DBArray'],"catWinImportProcess_DBArray")
					|| sc_in_array('feature_custom',$mappingData['DBArray'],"catWinImportProcess_DBArray")
					|| sc_in_array('feature_add',$mappingData['DBArray'],"catWinImportProcess_DBArray")
					|| sc_in_array('feature_delete',$mappingData['DBArray'],"catWinImportProcess_DBArray")
				)
			{
				refreshCacheFeature();
			}
			if (isCombination())
			{
				refreshCacheAttribute();
			}
			if (sc_in_array('id_category_default',$mappingData['DBArray'],"catWinImportProcess_DBArray") 
				|| sc_in_array('category_default',$mappingData['DBArray'],"catWinImportProcess_DBArray") 
				|| sc_in_array('categories',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
			{
				$categories=array();
				$categoriesProperties=array();
				$categoryNameByID=array();
				$categoryIDByPath=array();
				$categoriesFirstLevel=array();
				refreshCacheCategory();
			}

			// get carriers
			$dataDB_carrier=array();
			$dataDB_carrierByName=array();
			//$DB_carrier=Carrier::getCarriers(intval($defaultLanguage->id), false);
			$sql = 'SELECT c.*
				FROM `'._DB_PREFIX_.'carrier` c
				'.(version_compare(_PS_VERSION_, '1.4.5.0', '>=')?' WHERE c.`deleted` = "0" ':'').'
				GROUP BY c.`id_carrier`
			'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?' ORDER BY c.`position` ASC ':'').'';
			$DB_carrier = Db::getInstance()->executeS($sql);
			foreach($DB_carrier AS $carrier)
			{
				$dataDB_manufacturer[$carrier['id_reference']]=$carrier['name'];
				$dataDB_carrierByName[$carrier['name']]=$carrier['id_reference'];
			}

			// VAT first check
			if ((sc_in_array('priceinctax',$mappingData['DBArray'],"catWinImportProcess_DBArray") || sc_in_array('priceinctaxincecotax',$mappingData['DBArray'],"catWinImportProcess_DBArray")) && !sc_in_array('VAT',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
					die(_l('Error in mapping: price including VAT found in CSV columns but no VAT colmun found. You need to indicate the VAT or use only price excluding VAT.'));

			if ((sc_in_array('priceinctax',$mappingData['DBArray'],"catWinImportProcess_DBArray") || sc_in_array('priceinctaxincecotax',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				&& sc_in_array('priceexctax',$mappingData['DBArray'],"catWinImportProcess_DBArray")
				&& sc_in_array('VAT',$mappingData['DBArray'],"catWinImportProcess_DBArray")) {
					die(_l('Price excluding VAT, price including VAT and VAT found in CSV columns. You must use only price excluding VAT with VAT.'));
			}

			$err_VAT=array();
			$dataArray_VAT=array();
			$dataArray_supplier=array();
			$dataArray_manufacturer=array();
			$dataArray_feature=array();
			$dataArray_multiplefeature=array();
			$dataArray_attributegroup=array();
			$dataArray_attributegroup=array();
//			$dataArray_attributegroupmultiple=array();
			$err='';

			for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA)-1)) && $line = parseCSVLine($importConfig[$TODOfilename]['fieldsep'],$DATA[$current_line])) && ($current_line <= $importlimit)) ; $current_line++)
			{
				if ($DATA[$current_line]=='') continue;
				if ($importConfig[$TODOfilename]['utf8']==1)
					utf8_encode_array($line);
				$line=array_map('cleanQuotes',$line);
				if (sc_in_array('VAT',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					$vat=importConv2Float(findCSVLineValue('VAT'));
					if (!in_array($vat,$dataArray_VAT)) $dataArray_VAT[]=$vat;
					if ($vat < 0 || $vat > 100)
						$err_VAT[]=$current_line;
				}
				if (sc_in_array('supplier',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					$dataArray_supplier[]=findCSVLineValue('supplier');
				}
				if (sc_in_array('supplier_default',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					$dataArray_supplier[]=findCSVLineValue('supplier_default');
				}
				if (sc_in_array('manufacturer',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					$dataArray_manufacturer[]=findCSVLineValue('manufacturer');
				}
				if (sc_in_array('feature',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					findAllCSVLineValue('feature',$dataArray_feature,'id_feature',$features);
				}
				if (sc_in_array('feature_add',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					findAllCSVLineValue('feature_add',$dataArray_multiplefeature,'id_feature',$features);
				}
				if (isCombination())
				{
					findAllCSVLineValue('attribute',$dataArray_attributegroup,'id_attribute_group',$attributeGroups);
					findAllCSVLineValue('attribute_multiple',$dataArray_attributegroup,'id_attribute_group',$attributeGroups);
				}
				if (count($line)!=count($firstLineData))
					$err.=_l("Error on line ").($current_line+1)._l(": wrong column count: ").substr(join($importConfig[$TODOfilename]['fieldsep'],$line),0,22)." (".count($line)."-".count($firstLineData).")<br/>";
			}
/*echo "<pre>";
print_r($mappingData);
echo "############################";
print_r($attributeGroups);
echo "############################";
print_r($dataArray_attributegroup);
echo "</pre>";*/

			// VAT second check
			$isErr=false;
			if ($err_VAT)
				$err.=_l('Error: the VAT value should be between 0 and 100 on line(s) ').join(',',$err_VAT).'<br/>'.
							'<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'index.php?tab=AdminTaxes&token='.$sc_agent->getPSToken('AdminTaxes').'">'._l('Click here to fix the problem').'</a><br/>';
			$dataDB_VAT=array();
			if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
			{
				$DB_VAT=Tax::getTaxes();
				foreach($DB_VAT as $tax)
				{
					$dataDB_VAT[]=importConv2Float($tax['rate']);
				}
			}else{
				$sql='SELECT trg.name, trg.id_tax_rules_group,t.rate
				FROM `'._DB_PREFIX_.'tax_rules_group` trg
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = '.(int)SCI::getDefaultCountryId().' AND tr.`id_state` = 0)
		 	  LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
		    WHERE trg.active=1';
				$DB_VAT=Db::getInstance()->ExecuteS($sql);
				foreach($DB_VAT as $tax)
					$dataDB_VAT[]=importConv2Float($tax['rate']);
			}
			$check_VAT=array_diff($dataArray_VAT,$dataDB_VAT);
			if (count($check_VAT))
			{
				foreach($check_VAT AS $tax)
				{
					if ($tax!=0)
					{
						$err.=_l('Error: tax doesn\'t exist: ').$tax.'<br/>';
						$isErr=true;
					}
				}
			}
			if ($isErr) $err.='<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'index.php?tab=AdminTaxes&token='.$sc_agent->getPSToken('AdminTaxes').'">'._l('Click here to fix the problem').'</a><br/>';
			// supplier check
			$isErr=false;
			$dataDB_supplier=array();
			$dataDB_supplierByName=array();
			//$DB_supplier=Supplier::getSuppliers(false,0,false);
			$DB_supplier = Db::getInstance()->ExecuteS('SELECT m.*
				FROM `'._DB_PREFIX_.'supplier` m
				ORDER BY m.`name` ASC');
			foreach($DB_supplier AS $supplier)
			{
				$dataDB_supplier[$supplier['id_supplier']]=$supplier['name'];
				$dataDB_supplierByName[$supplier['name']]=$supplier['id_supplier'];
			}
			$check_supplier=array_diff($dataArray_supplier,$dataDB_supplier);
			$check_supplier=arr_unique($check_supplier);
			if (count($check_supplier))
			{
				$err_unique=array();
				foreach($check_supplier AS $supplier)
				{
					if ($supplier!='')// && !in_array($supplier,$err_unique))
					{
						$err.=_l('This supplier doesn\'t exist: ').$supplier.'<br/>';
						$err_unique[]=$supplier;
						$isErr=true;
					}
				}
			}
			if ($isErr)
			{
				if ($importConfig[$TODOfilename]['createelements']==1)
				{
					addSupplier($err_unique);
				}else{
					$err.='<form name="fSupplierCreation" id="fSupplierCreation">
										<textarea name="data" style="display:none">';
					$err.=join("_|_",$err_unique);
					$err.='</textarea>';
					$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addSupplier\',$(\'#fSupplierCreation\').serialize(),function(data){$(\'#fSupplierCreation\').html(data);});void(0);">'._l('Click here to create these suppliers').'</a>';
					$err.='</form>';
				}
			}
			// visibility check
			$isErr=false;
			$dataDB_visibility=array();
			$dataDB_visibilityByName=array();
				$dataDB_visibility["both"]="both";
				$dataDB_visibilityByName["both"]="both";
				$dataDB_visibility["both"]=strtolower(_l('Both'));
				$dataDB_visibilityByName[strtolower(_l('Both'))]="both";

				$dataDB_visibility["catalog"]="catalog";
				$dataDB_visibilityByName["catalog"]="catalog";
				$dataDB_visibility["catalog"]=strtolower(_l('Catalog'));
				$dataDB_visibilityByName[strtolower(_l('Catalog'))]="catalog";

				$dataDB_visibility["search"]="search";
				$dataDB_visibilityByName["search"]="search";
				$dataDB_visibility["search"]=strtolower(_l('Search'));
				$dataDB_visibilityByName[strtolower(_l('Search'))]="search";

				$dataDB_visibility["none"]="none";
				$dataDB_visibilityByName["none"]="none";
				$dataDB_visibility["none"]=strtolower(_l('None'));
				$dataDB_visibilityByName[strtolower(_l('None'))]="none";

			// manufacturer check
			$isErr=false;
			$dataDB_manufacturer=array();
			$dataDB_manufacturerByName=array();
			//$DB_manufacturer=Manufacturer::getManufacturers(false,$id_lang,false);
			$DB_manufacturer = Db::getInstance()->ExecuteS('SELECT m.*
				FROM `'._DB_PREFIX_.'manufacturer` m
				ORDER BY m.`name` ASC');
			foreach($DB_manufacturer AS $manufacturer)
			{
				$dataDB_manufacturer[$manufacturer['id_manufacturer']]=$manufacturer['name'];
				$dataDB_manufacturerByName[$manufacturer['name']]=$manufacturer['id_manufacturer'];
			}
			$check_manufacturer=arrayDiffEmulation($dataArray_manufacturer,$dataDB_manufacturer);
			if (count($check_manufacturer))
			{
				$err_unique=array();
				foreach($check_manufacturer AS $manufacturer)
				{
					if ($manufacturer!='' && !in_array($manufacturer,$err_unique))
					{
						$err.=_l('This manufacturer doesn\'t exist: ').$manufacturer.'<br/>';
						$err_unique[]=$manufacturer;
						$isErr=true;
					}
				}
			}
			if ($isErr)
			{
				if ($importConfig[$TODOfilename]['createelements']==1)
				{
					addManufacturer($err_unique);
				}else{
					$err.='<form name="fManufacturerCreation" id="fManufacturerCreation">
										<textarea name="data" style="display:none">';
					$err.=join("_|_",$err_unique);
					$err.='</textarea>';
					$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addManufacturer\',$(\'#fManufacturerCreation\').serialize(),function(data){$(\'#fManufacturerCreation\').html(data);});void(0);">'._l('Click here to create these manufacturers').'</a>';
					$err.='</form>';
				}
			}
			// feature check

			$isErr=false;
			$err_unique=array();
			foreach($mappingData['CSV2DBOptions'] AS $CSVFieldName => $featureName)
			{
				if ( sc_in_array($mappingData['CSV2DB'][$CSVFieldName], array('feature','feature_custom','feature_add','feature_delete'),"catWinImportProcess_featurefields"))
				{
					if (!sc_in_array($featureName,array_keys($features),"catWinImportProcess_features") && !in_array($featureName,$err_unique))
					{
						$err.=_l('This feature doesn\'t exist: ').$featureName.'<br/>';
						$err_unique[]=$featureName;
						$isErr=true;
					}
				}
			}

			if ($isErr)
			{
				if ($importConfig[$TODOfilename]['createelements']==1)
				{
					$data='';
					foreach($err_unique AS $feature)
					{
						$data.=$feature."y|y";
					}
					addFeature($data);
					refreshCacheFeature();
					$isErr = false;
					//$err.='<script type="text/javascript">$(document).ready(function(){$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addFeature\',$(\'#fFeatureCreation\').serialize(),function(data){$(\'#fFeatureCreation\').html(data);})});</script>';
				}else{
					$err.='<form name="fFeatureCreation" id="fFeatureCreation">
										<textarea name="data" style="display:none">';
					foreach($err_unique AS $feature)
					{
						$err.=$feature."y|y";
					}
					$err.='</textarea>';
					$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addFeature\',$(\'#fFeatureCreation\').serialize(),function(data){$(\'#fFeatureCreation\').html(data);});void(0);">'._l('Click here to create these features').'</a>';
					$err.='</form>';
				}
			}

			// feature value check
			if (!$isErr)
			{
				$isErr=false;
				$dataArray_feature_unique=array();
				// only for multiplefeature management
				foreach($dataArray_multiplefeature AS $key => $val)
				{
					$vals = explode($importConfig[$TODOfilename]['valuesep'],$val['value']);
					foreach ($vals AS $k => $uval)
					{
						$uarr = $dataArray_multiplefeature[$key];
						$uarr['value'] = $uval;
						$dataArray_feature[] = $uarr;
					}
				}
				foreach($dataArray_feature AS $f)
				{
					//echo $f['value'].'X';
					//echo $f['id_feature'].'x|x'.$f['object'].'x|x'.$f['value'].'<br/>';
					//var_dump($dataArray_feature_unique);
					//var_dump($featureValues);
					/*
					var_dump($dataArray_feature);
					die('rr');
					if ($f['value']=='CR250R'){
					if($f['value']!='') echo 'C1OK';
					if(!in_array($f['id_feature'].'_|_'.$f['value'],array_keys($featureValues))) echo 'C2OK';
					if(!in_array($f['id_feature'].'x|x'.$f['object'].'x|x'.$f['id_feature'].'_|_'.$f['value'],$dataArray_feature_unique)) echo 'C3OK';
					if ($f['value']!='' && !in_array($f['id_feature'].'_|_'.$f['value'],array_keys($featureValues)) && !in_array($f['id_feature'].'x|x'.$f['object'].'x|x'.$f['id_feature'].'_|_'.$f['value'],$dataArray_feature_unique)) echo 'TOTOK';
					echo 'XXX<br/>';
					die('rr');
					}*/
					$f['value']=trim($f['value']);
					if ($f['value']!='' && !sc_in_array($f['id_feature'].'_|_'.$f['value'],array_keys($featureValues),"catWinImportProcess_featurevalues") && !in_array($f['id_feature'].'x|x'.$f['object'].'x|x'.$f['id_feature'].'_|_'.$f['value'],$dataArray_feature_unique))
					{
						$err.=_l('This feature doesn\'t exist: ').$f['object'].' &gt; '.$f['value'].'<br/>';
						$dataArray_feature_unique[]=$f['id_feature'].'x|x'.$f['object'].'x|x'.$f['id_feature'].'_|_'.$f['value'];
						$isErr=true;
					}
				}
				if ($isErr)
				{
					$data='';
					foreach($dataArray_feature_unique AS $featureValue)
					{
						$fv=explode('x|x',$featureValue);
						$data.=$fv[0].'x|x'.$fv[2]."y|y";
					}
					if ($importConfig[$TODOfilename]['createelements']==1)
					{
						addFeatureValue($data);
						refreshCacheFeature();
						findAllCSVLineValue('feature',$dataArray_feature,'id_feature',$features);
						$isErr = false;
					}else{
						$err.='<form name="fFeatureCreation" id="fFeatureCreation">
											<textarea name="data" style="display:none">';
						$err.=$data;
						$err.='</textarea>';
						$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addFeatureValue\',$(\'#fFeatureCreation\').serialize(),function(data){$(\'#fFeatureCreation\').html(data);});void(0);">'._l('Click here to create these features').'</a>';
						$err.='</form>';
					}
				}
			}

			// attribute group check
			$isErr=false;
			$err_unique=array();
			foreach($mappingData['CSV2DBOptions'] AS $CSVFieldName => $attributeGroupName)
			{
				if ($mappingData['CSV2DB'][$CSVFieldName]=='attribute' || $mappingData['CSV2DB'][$CSVFieldName]=='attribute_multiple')
				{
					if (!sc_in_array($attributeGroupName,array_keys($attributeGroups),"catWinImportProcess_attributeGroups") && !in_array($attributeGroupName,$err_unique))
					{
						$err.=_l('This attribute group doesn\'t exist: ').$attributeGroupName.'<br/>';
						$err_unique[]=$attributeGroupName;
						$isErr=true;
					}
				}
			}
			if ($isErr)
			{
				$data = "";
				foreach($err_unique AS $attributeGroup)
				{
					$data.=$attributeGroup."y|y";
				}
				if ($importConfig[$TODOfilename]['createelements']==1)
				{
					addAttributeGroup($data);
					refreshCacheAttribute();
					$isErr = false;
					//$err.='<script type="text/javascript">$(document).ready(function(){$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addAttributeGroup\',$(\'#fAttributeGroupCreation\').serialize(),function(data){$(\'#fAttributeGroupCreation\').html(data);})});</script>';
				}else{

					$err.='<form name="fAttributeGroupCreation" id="fAttributeGroupCreation">
									<textarea name="data" style="display:none">'.$data.'</textarea>';
					$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addAttributeGroup\',$(\'#fAttributeGroupCreation\').serialize(),function(data){$(\'#fAttributeGroupCreation\').html(data);});void(0);">'._l('Click here to create these attribute groups').'</a>';

					$err.='</form>';
				}
			}
			// attribute check
			if (!$isErr)
			{
				$isErr=false;
				$dataArray_attribute_unique=array();
				foreach($dataArray_attributegroup AS $ag)
				{
//					echo 'H'.$ag['value'].' '.(in_array($ag['value'],array_keys($attributeValues))).'K'.'[[[ '.$ag['id_attribute_group'].'x|x'.$ag['object'].'x|x'.$ag['value'].' ]]]<br/>';
//!in_array($ag['value'],array_keys($attributeValues),true)
					if ($ag['value']!='' && !sc_array_key_exists($ag['id_attribute_group'].'_|_'.$ag['value'],$attributeValues) && !in_array($ag['id_attribute_group'].'x|x'.$ag['object'].'x|x'.$ag['id_attribute_group'].'_|_'.$ag['value'],$dataArray_attribute_unique))
					{
						$err.=_l('This attribute doesn\'t exist: ').$ag['object'].' &gt; '.$ag['value'].'<br/>';
						$dataArray_attribute_unique[]=$ag['id_attribute_group'].'x|x'.$ag['object'].'x|x'.$ag['id_attribute_group'].'_|_'.$ag['value'].'_|_'.$ag['color_attr_options'];
						$isErr=true;
					}
				}
				if ($isErr)
				{
					$data='';
					foreach($dataArray_attribute_unique AS $attributeValue)
					{
						$av=explode('x|x',$attributeValue);
						$data.=$av[0].'x|x'.$av[2]."y|y";
					}
					if ($importConfig[$TODOfilename]['createelements']==1)
					{
						addAttributeValue($data);
						refreshCacheAttribute();
						$isErr = false;
					}else{
						$err.='<form name="fAttributeCreation" id="fAttributeCreation">
										<textarea name="data" style="display:none">';
						$err.=$data;
						$err.='</textarea>';
						$err.='<a href="javascript:$.post(\'index.php?ajax=1&act=cat_win-import_process&action=addAttributeValue\',$(\'#fAttributeCreation\').serialize(),function(data){$(\'#fAttributeCreation\').html(data);});void(0);">'._l('Click here to create these attributes').'</a>';
						$err.='</form>';
					}
				}
			}

			if ($err!='' && $importConfig[$TODOfilename]['createelements']!=1)
				die($err.'<br/><br/>'._l('The process has been stopped before any modification in the database. You need to fix these errors first.'));

			// CHECK IF CATEGORY EXISTS
			$categ=Db::getInstance()->getRow("
			SELECT c.id_category
			FROM `"._DB_PREFIX_."category` c
			LEFT JOIN `"._DB_PREFIX_."category_lang` cl ON (c.`id_category` = cl.`id_category`)
			WHERE `name` = '".pSQL($TODOfilename)."'
			GROUP BY c.id_category");
			if (is_array($categ) && $categ['id_category']!='')
			{
				$id_category=intval($categ['id_category']);
			}else{
				$newcategory=new Category();
				$newcategory->id_parent=SCI::getConfigurationValue("PS_ROOT_CATEGORY");//1;
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
				refreshCacheCategory();
				//$newcategory->addGroups(array(1));
				$id_category=$newcategory->id;
			}

			$refsupp_option = "";
			if(sc_in_array($importConfig[$TODOfilename]['idby'], array('supref'),"catWinImportProcess_supref"))
			{
				foreach($mappingData['CSV2DB'] as $key=>$field)
				{
					if($field=="supplier_reference")
					{
						$refsupp_option = $mappingData['CSV2DBOptions'][$key];
						break;
					}
				}
			}

			$stats=array('created' => 0,'modified' => 0,'skipped' => 0,'no_wholesaleprice' => 0);
			$noWholesalepriceArray = array();
			$CSVDataStr = remove_utf8_bom(file_get_contents(SC_CSV_IMPORT_DIR.$TODOfilename));
			$CSVData = preg_split("/(?:\r\n|\r|\n)/", $CSVDataStr);
			$lastIdentifier='';
			$lastid_product=0;
			$updated_products = array();
			$id_shop_list=array();
			$id_shop_list_default=array(1);
			if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
			{
				$id_shop_list_default=array((int)SCI::getConfigurationValue('PS_SHOP_DEFAULT'));
				$cache_id_shop = array();
				$cache_current_line_for_combination = array();
			}
			$imageList=array();
			$productsWithTagUpdatedList=array();
//			for ($current_line = $FIRST_CONTENT_LINE; (($current_line <= (count($DATA)-1)) && $line = explode($importConfig[$TODOfilename]['fieldsep'],$DATA[$current_line])) && $current_line <= $importlimit ; $current_line++)
//$aa= parseCSVLine($importConfig[$TODOfilename]['fieldsep'],$DATA[1]);
//var_dump($aa);
			for ($current_line = $FIRST_CONTENT_LINE; ((($current_line <= (count($DATA)-1)) && $line = parseCSVLine($importConfig[$TODOfilename]['fieldsep'],$DATA[$current_line])) && ($current_line <= $importlimit)) ; $current_line++)
			{
				if ($DATA[$current_line]=='') continue;
				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>"._l('Start line')." : $time "._l('seconds');
				}
				$extension_vars = array();
				$id_shop_list=$id_shop_list_default;
				$useSpecificPrices=false;
				$line=array_map('cleanQuotes',$line);
				if ($scdebug) echo 'line '.$current_line.': ';
				$line[count($line)-1]=rtrim($line[count($line)-1]);
				$TODO=array();
				$TODOSHOP=array();
				if ($importConfig[$TODOfilename]['utf8']==1)
					utf8_encode_array($line);

				$filter_supplier_id = intval($importConfig[$TODOfilename]['supplier']);

				$where_shop_list = "";
				if(SCMS && findCSVLineValue('id_shop_list')!='' && $importConfig[$TODOfilename]['fornewproduct']=='skip' && $importConfig[$TODOfilename]['forfoundproduct']=='update')
					$where_shop_list = findCSVLineValue('id_shop_list');

				$has_view = (_s('CAT_PROD_IMPORT_METHOD') && SCI::getConfigurationValue('PS_SC_IMPORT_VIEW')=="1"?true:false);
				$res=array();
				switch($importConfig[$TODOfilename]['idby'])
				{
					case 'prodname':
						$name = findCSVLineValue('name');
						if(!empty($name))
						{
							$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product p
										LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
									WHERE pl.name='".psql($name)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
						}
						else
							$sql = "";
					break;
					case 'idproduct':
						$id_product_find = findCSVLineValue('id_product');
						if(!empty($id_product_find))
						{
							$sql="SELECT p.id_product,p.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product p
									WHERE p.id_product='".intval($id_product_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
						}
						else
							$sql = "";
					break;
					case 'idproductattribute':
						$id_product_attribute_find = findCSVLineValue('id_product_attribute');
						if(!empty($id_product_attribute_find))
						{
							if($has_view)
							{
								$sql = "SELECT id_product,id_product_attribute,date_upd,id_supplier FROM "._DB_PREFIX_."sc_import_index
									WHERE
										id_product_attribute='".intval($id_product_attribute_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
							}
							else
							{
								$sql="SELECT pa.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product_attribute pa
										LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
									WHERE pa.id_product_attribute='".intval($id_product_attribute_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
							}
						}
						else
							$sql = "";
					break;
                    case 'ean13':
                        $EAN13_find = findCSVLineValue('EAN13');
                        if(!empty($EAN13_find))
                        {
                            $sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product_attribute pa
										LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
									WHERE pa.ean13='".psql($EAN13_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            $res=Db::getInstance()->ExecuteS($sql);
                            if (count($res)==0)
                            {
                                $sql="SELECT p.id_product,p.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product p
									WHERE p.ean13='".psql($EAN13_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                    (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            }
                            else
                                $sql = "";
                        }
                        else
                            $sql = "";
                        break;
                    case 'isbn':
                        $ISBN_find = findCSVLineValue('ISBN');
                        if(!empty($ISBN_find))
                        {
                            $sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product_attribute pa
										LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
									WHERE pa.isbn='".psql($ISBN_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            $res=Db::getInstance()->ExecuteS($sql);
                            if (count($res)==0)
                            {
                                $sql="SELECT p.id_product,p.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product p
									WHERE p.isbn='".psql($ISBN_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                    (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            }
                            else
                                $sql = "";
                        }
                        else
                            $sql = "";
                        break;
                    case 'upc':
                        $upc_find = findCSVLineValue('upc');
                        if(!empty($upc_find))
                        {
                            $sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product_attribute pa
										LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
									WHERE pa.upc='".psql($upc_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            $res=Db::getInstance()->ExecuteS($sql);
                            if (count($res)==0)
                            {
                                $sql="SELECT p.id_product,p.date_upd,p.id_supplier
									FROM "._DB_PREFIX_."product p
									WHERE p.upc='".intval($upc_find)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                    (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
									LIMIT 1";
                            }
                            else
                                $sql = "";
                        }
                        else
                            $sql = "";
                        break;
					case 'prodref':
						$search_reference = findCSVLineValue('reference');
						if(!empty($search_reference))
						{
							if($has_view)
							{
								$sql = "SELECT id_product,id_product_attribute,date_upd,id_supplier FROM "._DB_PREFIX_."sc_import_index
									WHERE
										( p_reference='".psql($search_reference)."' OR pa_reference='".psql($search_reference)."' )
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
							}
							else
							{
								$sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier FROM "._DB_PREFIX_."product p
										LEFT JOIN "._DB_PREFIX_."product_attribute pa on (p.id_product=pa.id_product)
										WHERE  pa.reference='".psql($search_reference)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
										" LIMIT 1";
								$res=Db::getInstance()->ExecuteS($sql);
								if (count($res)==0)
								{
									$sql="SELECT p.id_product,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product p
											WHERE reference='".psql($search_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)."".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
								}
								else
									$sql = "";
							}
						}
						else
							$sql = "";
					break;
					case 'supref':
						$search_ref = findCSVLineValue('supplier_reference');
						if(!empty($search_ref))
						{
							if($has_view)
							{
								$search_supp_ref = "";
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
									$search_supp_ref = " OR product_supplier_reference LIKE '%||".psql($search_ref)."||%' ";

								$sql = "SELECT id_product,id_product_attribute,date_upd,id_supplier FROM "._DB_PREFIX_."sc_import_index
									WHERE
										( pa_supplier_reference='".psql($search_ref)."' OR p_supplier_reference='".psql($search_ref)."' ".$search_supp_ref.")
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
							}
							else
							{
								if (version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
								{
									$sql="SELECT ps.id_product,ps.id_product_attribute,pa.date_upd,p.id_supplier FROM "._DB_PREFIX_."product_supplier ps
											LEFT JOIN "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
											LEFT JOIN "._DB_PREFIX_."product p on (ps.id_product=p.id_product)
											WHERE ps.product_supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" ORDER BY id_product_attribute DESC LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
                                    if (count($res)==0)
                                    {
                                        $sql="SELECT ps.id_product,p.date_upd,p.id_supplier FROM "._DB_PREFIX_."product_supplier ps
											LEFT JOIN "._DB_PREFIX_."product p on (ps.id_product=p.id_product)
											WHERE ps.product_supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                            (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
                                            " LIMIT 1";
                                        $res=Db::getInstance()->ExecuteS($sql);
                                    }
								}
								if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
                                {
                                    if (count($res)==0)
                                    {
                                        $sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier FROM "._DB_PREFIX_."product p
											LEFT JOIN "._DB_PREFIX_."product_attribute pa on (p.id_product=pa.id_product)
											WHERE  pa.supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                            (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
                                            " ORDER BY id_product_attribute DESC LIMIT 1";
                                        $res=Db::getInstance()->ExecuteS($sql);
                                    }
                                    else
                                        $sql = "";
                                    if (count($res)==0)
                                    {
                                        $sql="SELECT p.id_product,p.date_upd,p.id_supplier FROM "._DB_PREFIX_."product p
											WHERE supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)."".
                                            (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
                                            " LIMIT 1";
                                    }
                                    else
                                        $sql = "";
                                }
                                else
                                {
                                    if (count($res)==0)
                                    {
                                        $sql="SELECT p.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier FROM "._DB_PREFIX_."product p
											LEFT JOIN "._DB_PREFIX_."product_attribute pa on (p.id_product=pa.id_product)
											WHERE  pa.supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.(int)$filter_supplier_id)." ".
                                            " ORDER BY id_product_attribute DESC LIMIT 1";
                                        $res=Db::getInstance()->ExecuteS($sql);
                                    }
                                    else
                                        $sql = "";
                                    if (count($res)==0)
                                    {
                                        $sql="SELECT p.id_product,p.date_upd,p.id_supplier FROM "._DB_PREFIX_."product p
											WHERE supplier_reference='".psql($search_ref)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)."".
                                            (!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
                                            " LIMIT 1";
                                    }
                                    else
                                        $sql = "";
                                }
							}
						}
						else
							$sql = "";

					break;
					case 'prodrefandsupref':
						$search_supplier_reference = findCSVLineValue('supplier_reference');
						$search_reference = findCSVLineValue('reference');
						if(!empty($search_supplier_reference) && !empty($search_reference))
						{
							if($has_view)
							{
								$search_supp_ref = "";
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
									$search_supp_ref = " OR product_supplier_reference LIKE '%||".psql($search_supplier_reference)."||%' ";

								$sql = "SELECT id_product,id_product_attribute,date_upd,id_supplier FROM "._DB_PREFIX_."sc_import_index
									WHERE
										( pa_supplier_reference='".psql($search_supplier_reference)."' OR p_supplier_reference='".psql($search_supplier_reference)."' ".$search_supp_ref.")
										AND ( pa_reference='".psql($search_reference)."' OR p_reference='".psql($search_reference)."')
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND id_supplier='.(int)$filter_supplier_id)." ".
																		(!empty($where_shop_list)? ' AND id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
							}
							else
							{
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
								{
									$sql="SELECT ps.id_product,ps.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
											WHERE pa.reference='".psql($search_reference)."'
												AND (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR pa.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT ps.id_product,ps.id_product_attribute,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product p on (ps.id_product=p.id_product)
											WHERE p.reference='".psql($search_reference)."'
												AND (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR p.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
								}
								else
								{
									$sql="SELECT pa.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
											WHERE pa.reference='".psql($search_reference)."'
												AND pa.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT p.id_product,p.id_product_attribute,p.date_upd,p.id_supplier
											FROM product p on (ps.id_product=p.id_product)
											WHERE p.reference='".psql($search_reference)."'
												AND p.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
								}
							}
						}
						else
							$sql = "";
					break;
					case 'prodnameandsupref':
						$search_supplier_reference = findCSVLineValue('supplier_reference');
						$search_name = findCSVLineValue('name');
						if(!empty($search_supplier_reference) && !empty($search_name))
						{
							if($has_view)
							{
								$search_supp_ref = "";
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
									$search_supp_ref = " OR ii.product_supplier_reference LIKE '%||".psql($search_supplier_reference)."||%' ";

								$sql = "SELECT ii.id_product,ii.id_product_attribute,ii.date_upd,ii.id_supplier FROM "._DB_PREFIX_."sc_import_index ii
										LEFT JOIN "._DB_PREFIX_."product_lang pl on (ii.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
									WHERE
										( ii.pa_supplier_reference='".psql($search_supplier_reference)."' OR ii.p_supplier_reference='".psql($search_supplier_reference)."' ".$search_supp_ref.")
										AND pl.name='".psql($search_name)."'
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND ii.id_supplier='.$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND ii.id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
							}
							else
							{
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
								{
									$sql="SELECT ps.id_product,ps.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												AND (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR pa.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT ps.id_product,ps.id_product_attribute,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product p on (ps.id_product=p.id_product)
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												AND (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR p.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
								}
								else
								{
									$sql="SELECT pa.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												AND pa.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT p.id_product,p.id_product_attribute,p.date_upd,p.id_supplier
											FROM product p on (ps.id_product=p.id_product)
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												AND p.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
								}
							}
						}
						else
							$sql = "";
					break;
					case 'suprefthenprodname':
						$search_supplier_reference = findCSVLineValue('supplier_reference');
						$search_name = findCSVLineValue('name');
						if(!empty($search_supplier_reference) && !empty($search_name))
						{
							if($has_view)
							{
								$search_supp_ref = "";
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
									$search_supp_ref = " OR ii.product_supplier_reference LIKE '%||".psql($search_supplier_reference)."||%' ";

								$sql = "SELECT ii.id_product,ii.id_product_attribute,ii.date_upd,ii.id_supplier FROM "._DB_PREFIX_."sc_import_index ii
									WHERE
										( ii.pa_supplier_reference='".psql($search_supplier_reference)."' OR ii.p_supplier_reference='".psql($search_supplier_reference)."' ".$search_supp_ref.")
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND ii.id_supplier='.$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND ii.id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
								$res=Db::getInstance()->ExecuteS($sql);
								if (count($res)==0)
								{
									$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
										FROM "._DB_PREFIX_."product p
											LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
										WHERE pl.name='".psql($search_name)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
										LIMIT 1";
								}
								else
									$sql = "";
							}
							else
							{
								if(version_compare(_PS_VERSION_,'1.5.0.0','>=') && $refsupp_option!="suppref_product")
								{
									$sql="SELECT ps.id_product,ps.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
											WHERE (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR pa.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT ps.id_product,ps.id_product_attribute,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_supplier ps
												LEFT JOIN "._DB_PREFIX_."product p on (ps.id_product=p.id_product)
											WHERE (ps.product_supplier_reference='".psql($search_supplier_reference)."' OR p.supplier_reference='".psql($search_supplier_reference)."')
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product p
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
											LIMIT 1";
									}
									else
										$sql = "";
								}
								else
								{
									$sql="SELECT pa.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
												LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
											WHERE pa.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql="SELECT p.id_product,p.id_product_attribute,p.date_upd,p.id_supplier
											FROM product p on (ps.id_product=p.id_product)
											WHERE p.supplier_reference='".psql($search_supplier_reference)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
											" LIMIT 1";
									}
									else
										$sql = "";
									$res=Db::getInstance()->ExecuteS($sql);
									if (count($res)==0)
									{
										$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
											FROM "._DB_PREFIX_."product p
												LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
											WHERE pl.name='".psql($search_name)."'
												".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
												(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
											LIMIT 1";
									}
									else
										$sql = "";
								}
							}
						}
						else
							$sql = "";
					break;
					case 'prodrefthenprodname':
						$search_reference = findCSVLineValue('reference');
						$search_name = findCSVLineValue('name');
						if(!empty($search_reference) && !empty($search_name))
						{
							if($has_view)
							{
								$sql = "SELECT ii.id_product,ii.id_product_attribute,ii.date_upd,ii.id_supplier FROM "._DB_PREFIX_."sc_import_index ii
									WHERE
										( ii.pa_reference='".psql($search_reference)."' OR ii.p_reference='".psql($search_reference)."')
										".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND ii.id_supplier='.$filter_supplier_id)." ".
										(!empty($where_shop_list)? ' AND ii.id_shop IN ('.psql($where_shop_list).')' : '')."
									ORDER BY id_product_attribute DESC
									LIMIT 1";
								$res=Db::getInstance()->ExecuteS($sql);
								if (count($res)==0)
								{
									$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
										FROM "._DB_PREFIX_."product p
											LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
										WHERE pl.name='".psql($search_name)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
										LIMIT 1";
								}
								else
									$sql = "";
							}
							else
							{
								$sql="SELECT pa.id_product,pa.id_product_attribute,pa.date_upd,p.id_supplier
										FROM "._DB_PREFIX_."product_attribute pa on (ps.id_product_attribute=pa.id_product_attribute)
											LEFT JOIN "._DB_PREFIX_."product p on (pa.id_product=p.id_product)
										WHERE pa.reference='".psql($search_reference)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE pa.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
										" LIMIT 1";
								$res=Db::getInstance()->ExecuteS($sql);
								if (count($res)==0)
								{
									$sql="SELECT p.id_product,p.id_product_attribute,p.date_upd,p.id_supplier
										FROM product p on (ps.id_product=p.id_product)
										WHERE p.reference='".psql($search_reference)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '').
										" LIMIT 1";
								}
								else
									$sql = "";
								$res=Db::getInstance()->ExecuteS($sql);
								if (count($res)==0)
								{
									$sql = "SELECT p.id_product,p.date_upd,p.id_supplier
										FROM "._DB_PREFIX_."product p
											LEFT JOIN "._DB_PREFIX_."product_lang pl on (p.id_product=pl.id_product AND pl.id_lang=".intval($defaultLanguage->id).")
										WHERE pl.name='".psql($search_name)."'
											".(version_compare(_PS_VERSION_,'1.5.0.0','<') || empty($filter_supplier_id) ? '' : ' AND p.id_supplier='.$filter_supplier_id)." ".
											(!empty($where_shop_list)? ' AND EXISTS (SELECT * FROM '._DB_PREFIX_.'product_shop ps WHERE p.id_product=ps.id_product AND id_shop IN ('.psql($where_shop_list).'))' : '')."
										LIMIT 1";
								}
								else
									$sql = "";

							}
						}
						else
							$sql = "";
					break;
					case 'specialIdentifier':
						sc_ext::readImportCSVConfigXML('importProcessIdentifier');
					break;
				}

				if(!empty($sql))
					$res=Db::getInstance()->executeS($sql);
				if(!empty($res[0]))
					$res = $res[0];
				else
					$res = array();

				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>"._l('Results of identification queries')." : $time "._l('seconds');
					echo "<br/><br/>".$sql;
					echo "<br/><br/>";
					print_r($res);
					echo "<br/><br/>";
				}

				if (is_array($res) && count($res))
				{
					$id_product=$res['id_product'];
					if ($importConfig[$TODOfilename]['idby']=='idproductattribute'
						|| $importConfig[$TODOfilename]['idby']=='prodref'
						|| $importConfig[$TODOfilename]['idby']=='supref'
						|| $importConfig[$TODOfilename]['idby']=='prodrefthenprodname'
						|| $importConfig[$TODOfilename]['idby']=='suprefthenprodname'
                        || $importConfig[$TODOfilename]['idby']=='ean13'
                        || $importConfig[$TODOfilename]['idby']=='isbn'
						|| $importConfig[$TODOfilename]['idby']=='upc')
					{
						$id_product_attribute=intval((sc_array_key_exists('id_product_attribute',$res) ? $res['id_product_attribute']:0));
					}else{
						$id_product_attribute=0;
					}
				}else{
					$id_product=0;
					$id_product_attribute=0;
				}
				if ($scdebug) echo findCSVLineValue('reference').' : '.$id_product.' '.$id_product_attribute.'<br/>';
				if ($scdebug) echo 'a';

				if ($CRON && isset($CRON_OLDERTHAN) && $CRON_OLDERTHAN > 0)
				{
					$date_upd=strtotime($res['date_upd']);
					$nowres=Db::getInstance()->getRow('SELECT UNIX_TIMESTAMP() AS ut');
					$now=($nowres ? $nowres['ut'] : 0);
					if (($date_upd > ($now - ((int)$CRON_OLDERTHAN * 60))) // if not a recent updated object...
								&& !(isCombination($line) && $id_product_attribute==0) // and not a combination to create (in this case only date_upd of the product is got from the DB)
						 )
					{
						$stats['skipped']++;
						$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
						continue;
					}
				}

				//$needUnitPriceRatio=(findCSVLineValue('quantity') !== '' ? true : false );

				// SUPPLIER FILTER
				if(!empty($importConfig[$TODOfilename]['supplier']) && !empty($id_product))
				{
					if(empty($res['id_supplier']))
					{
						$stats['skipped']++;
						if (_s('CAT_IMPORT_IGNORED_LINES')==1)
						{
							unset($CSVData[$current_line]);
							$needSaveTODO=true;
						}
						continue;
					}
					elseif(!empty($res['id_supplier']))
					{
						if($res['id_supplier']!=$importConfig[$TODOfilename]['supplier'])
						{
							if(version_compare(_PS_VERSION_,'1.5.0.0','<='))
							{
								$sql="SELECT id_product FROM "._DB_PREFIX_."product_supplier
										WHERE id_product='".intval($id_product)."'
											AND id_supplier='".intval($importConfig[$TODOfilename]['supplier'])."' ";
								if(!empty($id_product_attribute))
									$sql .= " AND id_product_attribute='".intval($id_product_attribute)."' ";

								$res=Db::getInstance()->executeS($sql);
								if(empty($res[0]["id_product"]))
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
				}

				if (SCMS)
				{
					if(!empty($cache_current_line_for_combination[$current_line]))
						$id_product_attribute = (int)$cache_current_line_for_combination[$current_line];

					$id_shop=null;
					if (findCSVLineValue('id_shop_list')!='')
					{
						$id_shop_list_in_csv = explode(',',findCSVLineValue('id_shop_list'));
						$id_shop_list = $id_shop_list_in_csv;
						foreach($id_shop_list_in_csv as $id_shop_to_check)
						{
							if (sc_array_key_exists($id_product.'-'.$id_product_attribute, $cache_id_shop) && in_array($id_shop_to_check, $cache_id_shop[$id_product.'-'.$id_product_attribute]))
								continue;
							$id_shop = $id_shop_to_check;
						}
					}
				}
				else
					$id_shop=(int)SCI::getConfigurationValue('PS_SHOP_DEFAULT');

				// PRODUIT EXISTANT
				if(!empty($id_product))
				{
					// IGNORE LIGNE
					if($importConfig[$TODOfilename]['forfoundproduct']=='skip')
					{
						$stats['skipped']++;
						if (_s('CAT_IMPORT_IGNORED_LINES')==1)
						{
							unset($CSVData[$current_line]);
							$needSaveTODO=true;
						}
						continue;
					}
					// MODIFIE LE PRODUIT
					elseif($importConfig[$TODOfilename]['forfoundproduct']=='update')
					{
						if (SCMS)
						{
							$newprod=new Product($id_product, false, null, $id_shop);
							if ($id_shop==null)
								$id_shop_list=array($newprod->id_shop_default);
						}
						elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							$newprod=new Product($id_product, false, null,$id_shop);
						else
							$newprod=new Product($id_product);
						$newprod->date_upd = date("Y-m-d H:i:s");
						$stats['modified']++;
					}
					// CREE DOUBLON
					elseif($importConfig[$TODOfilename]['forfoundproduct']=='create')
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
						if ($importConfig[$TODOfilename]['idby']=='prodname'
								&& isCombination()
								&& $lastIdentifier==findCSVLineValue('name')
						)
						{
							if (SCMS)
							{
								$newprod=new Product($lastid_product, false, null, $id_shop);
								if ($id_shop==null)
									$id_shop_list=array($newprod->id_shop_default);
							}
							elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newprod=new Product($lastid_product, false, null,$id_shop);
							else
								$newprod=new Product($lastid_product);
							$stats['modified']++;
						}else{
							// create new product with default values
							$newprod=new Product();
							$newprod->id_category_default=$id_category;
							$newprod->id_supplier=intval($importConfig[$TODOfilename]['supplier']);
							$newprod->active=intval(_s('CAT_IMPORT_ACTIVE_DEFAULT'));
							$newprod->link_rewrite[$defaultLanguage->id]='p';
							$newprod->quantity=0;
							$newprod->price=0;
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newprod->id_shop_default = $id_shop_list[0];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								$newprod->id_tax=0;
							}else{
								$newprod->id_tax_rules_group=0;
							}
							if (version_compare(_PS_VERSION_, '1.3.0.4', '>=')) // DATE => DATETIME field format
							{
								$newprod->reduction_from = date('Y-m-d H:i:s');
								$newprod->reduction_to = date('Y-m-d H:i:s');
							}else{
								$newprod->reduction_from = date('Y-m-d');
								$newprod->reduction_to = date('Y-m-d');
							}
							foreach($languages AS $lang)
							{
								$newprod->link_rewrite[$lang['id_lang']]='product';
								$newprod->name[$lang['id_lang']]='product';
								$newprod->description_short[$lang['id_lang']]='';
								$newprod->description[$lang['id_lang']]='';
							}
							$stats['created']++;
						}
					}
				}
				// NOUVEAU PRODUIT
				elseif(empty($id_product))
				{
					// IGNORE LIGNE
					if($importConfig[$TODOfilename]['fornewproduct']=='skip')
					{
						$stats['skipped']++;
						if (_s('CAT_IMPORT_IGNORED_LINES')==1)
						{
							unset($CSVData[$current_line]);
							$needSaveTODO=true;
						}
						continue;
					}
					// CREE NOUVEAU PRODUIT
					elseif($importConfig[$TODOfilename]['fornewproduct']=='create')
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
						if ($importConfig[$TODOfilename]['idby']=='prodname'
									&& isCombination()
									&& $lastIdentifier==findCSVLineValue('name')
								)
						{
							if (SCMS)
							{
								$newprod=new Product($lastid_product, false, null, $id_shop);
								if ($id_shop==null)
									$id_shop_list=array($newprod->id_shop_default);
							}
							elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newprod=new Product($lastid_product, false, null,$id_shop);
							else
								$newprod=new Product($lastid_product);
							$stats['created']++;
						}else{
							// create new product with default values
							$newprod=new Product();
							$newprod->id_category_default=$id_category;
							$newprod->id_supplier=intval($importConfig[$TODOfilename]['supplier']);
							$newprod->active=intval(_s('CAT_IMPORT_ACTIVE_DEFAULT'));
							$newprod->link_rewrite[$defaultLanguage->id]='p';
							$newprod->quantity=0;
							$newprod->price=0;
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								$newprod->id_shop_default = $id_shop_list[0];
							if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
							{
								$newprod->id_tax=0;
							}else{
								$newprod->id_tax_rules_group=0;
							}
							if (version_compare(_PS_VERSION_, '1.3.0.4', '>=')) // DATE => DATETIME field format
							{
								$newprod->reduction_from = date('Y-m-d H:i:s');
								$newprod->reduction_to = date('Y-m-d H:i:s');
							}else{
								$newprod->reduction_from = date('Y-m-d');
								$newprod->reduction_to = date('Y-m-d');
							}
							foreach($languages AS $lang)
							{
								$newprod->link_rewrite[$lang['id_lang']]='product';
								$newprod->name[$lang['id_lang']]='product';
								$newprod->description_short[$lang['id_lang']]='';
								$newprod->description[$lang['id_lang']]='';
							}
							$stats['created']++;
						}
					}
				}

				/*// IGNORER PRODUITS EXISTANTS
				if ($importConfig[$TODOfilename]['iffoundindb']=='skip' && $id_product)
				{}
				// MODIFIER PRODUITS EXISTANTS
				elseif ($importConfig[$TODOfilename]['iffoundindb']=='replace' && $id_product)
				{}
				// NE PAS CREER LES NOUVEAUX PRODUITS
				elseif ($importConfig[$TODOfilename]['iffoundindb']=='replaceonly' && $id_product==0)
				{}
				// MODIFIER PRODUITS EXISTANTS
				elseif ($importConfig[$TODOfilename]['iffoundindb']=='replaceonly' && $id_product)
				{}
				// CREER LES NOUVEAUX PRODUITS
				else{}*/

				// on écrase le prix du produit uniquement si on n'utilise pas "laisser le prix de base du produit à 0" sinon l'import de déclinaison est faussé
				if ($newprod->id && version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _s('CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI') == 1 /*&& !SCMS*/)
				{
					if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
					{
						Context::getContext()->shop->id=$id_shop;
					}
					if(empty($newprod->price))
					{
						$tmp_price = Product::getPriceStatic((int)$newprod->id, false, null, 6, null, false, false, 1, false);
						if(!empty($tmp_price))
							$newprod->price = $tmp_price;
					}
					//if($needUnitPriceRatio)
					$newprod->unit_price = ($newprod->unit_price_ratio != 0  ? $newprod->price / $newprod->unit_price_ratio : 0);
				}
				if ($scdebug) echo 'b';

				if (SCMS)
				{
					if(empty($newprod->id))
						$newprod->id_shop_list=$id_shop_list;
					else
						$newprod->id_shop_list=$id_shop;
					if(!is_array($newprod->id_shop_list))
						$newprod->id_shop_list = array($newprod->id_shop_list);
				}
				
				// CACHE VAT					
				$cache_VAT = array("id_tax"=>0,"rate"=>1,"id_tax_rules_group"=>0);
				if (sc_in_array('VAT',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					$value = findCSVLineValue('VAT');
					if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
					{
						$cache_VAT["rate"] = 1+(importConv2Float($value)/100);
						$cache_VAT["id_tax"] = intval(Tax::getTaxIdByRate($value));
					}
					else
					{
						$vat_number=trim( trim( trim ( importConv2Float($value) , '0' )) , '.');
						$vat_string=trim( ($value) );
						if(!empty($vat_string) || (empty($vat_string) && $vat_string=="0") || !empty($vat_number))
						{
							$search_number = (!empty($vat_number) || (empty($vat_number) && $vat_number!="" && $vat_number==="0")?true:false);
							$search_string = (!empty($vat_string)?true:false);
							$sql="SELECT trg.name, trg.id_tax_rules_group,t.rate
								FROM `"._DB_PREFIX_."tax_rules_group` trg
								INNER JOIN `"._DB_PREFIX_."tax_rule` tr ON (trg.`id_tax_rules_group` = tr.`id_tax_rules_group` AND tr.`id_country` = ".(int)SCI::getDefaultCountryId()." AND tr.`id_state` = 0)
							  INNER JOIN `"._DB_PREFIX_."tax` t ON (t.`id_tax` = tr.`id_tax`)
							WHERE trg.active=1
								".(version_compare(_PS_VERSION_, '1.6.0.10', '>=')?" AND (trg.deleted!=1) ":"")."
								AND (
									".($search_number?" (trg.name LIKE '%".psql($vat_number)."%' OR trg.name LIKE '%".psql(str_replace(',','.',$vat_number))."%') ":"")."
									".($search_number && $search_string?" OR ":"")."
									".($search_string?" trg.name = '".psql($vat_string)."' ":"")."
								)
								";
							$DB_VAT=Db::getInstance()->getRow($sql);

							if(!empty($DB_VAT['rate']))
								$cache_VAT["rate"] = 1+(floatval($DB_VAT['rate'])/100);
							if(!empty($DB_VAT['id_tax_rules_group']))
								$cache_VAT["id_tax_rules_group"] = intval($DB_VAT['id_tax_rules_group']);
						}
					}
				}

				foreach($line AS $key => $value)
				{
					$value=trim($value);
					$GLOBALS['import_value']=$value;
					if ($scdebug && !sc_array_key_exists($key,$firstLineData)) echo 'ERR'.$key.'x'.$current_line.'x'.join(';',$line).'xxx'.join(';',array_keys($firstLineData)).'<br/>';
					if (sc_array_key_exists($key,$firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinImportProcess_CSVArray"))
					{
						if ($scdebug) echo 'c';
						@$id_lang=intval($getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
						$switchObject=$mappingData['CSV2DB'][$firstLineData[$key]];
						switch($switchObject)
						{
							case 'active':$newprod->active=intval(getBoolean($value));break;
							case 'visibility':
								$value = strtolower(trim($value));
								$newprod->visibility=(sc_array_key_exists($value,$dataDB_visibilityByName)? $dataDB_visibilityByName[$value]:"both");
							break;
							case 'date_add':
								$TODO[]="UPDATE "._DB_PREFIX_."product SET date_add='".psql($value)."' WHERE id_product=ID_PRODUCT";
								if (SCMS && !empty($id_shop))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_add='".psql($value)."' WHERE id_shop='".(int)$id_shop."' AND id_product=ID_PRODUCT";
								elseif (!empty($id_shop_list) && is_numeric($id_shop_list))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_add='".psql($value)."' WHERE id_shop='".(int)$id_shop_list."' AND id_product=ID_PRODUCT";
								elseif (!empty($id_shop_list) && count($id_shop_list)==1 && is_numeric($id_shop_list[0]))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_add='".psql($value)."' WHERE id_shop='".(int)$id_shop_list[0]."' AND id_product=ID_PRODUCT";
							break;
							case 'date_upd':
								$TODO[]="UPDATE "._DB_PREFIX_."product SET date_upd='".psql($value)."' WHERE id_product=ID_PRODUCT";
								if (SCMS && !empty($id_shop))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_upd='".psql($value)."' WHERE id_shop='".(int)$id_shop."' AND id_product=ID_PRODUCT";
								elseif (!empty($id_shop_list) && is_numeric($id_shop_list))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_upd='".psql($value)."' WHERE id_shop='".(int)$id_shop_list."' AND id_product=ID_PRODUCT";
								elseif (!empty($id_shop_list) && count($id_shop_list)==1 && is_numeric($id_shop_list[0]))
									$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET date_upd='".psql($value)."' WHERE id_shop='".(int)$id_shop_list[0]."' AND id_product=ID_PRODUCT";
							break;
							case 'manufacturer':$newprod->id_manufacturer=(sc_array_key_exists($value,$dataDB_manufacturerByName)? intval($dataDB_manufacturerByName[$value]):0);break;
							case 'quantity':
								if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
									$newprod->quantity=importConv2Int($value);
								break;
							case 'add_quantity':
								if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
									$TODO[]="UPDATE "._DB_PREFIX_."product SET quantity=quantity+".psql($value)." WHERE id_product=ID_PRODUCT";
								break;
							case 'remove_quantity':
								if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
									$TODO[]="UPDATE "._DB_PREFIX_."product SET quantity=quantity-".psql($value)." WHERE id_product=ID_PRODUCT";
								break;
							case 'minimal_quantity':$newprod->minimal_quantity=intval($value);break;
							case 'available_for_order':$newprod->available_for_order=intval($value);break;
							case 'show_price':$newprod->show_price=intval($value);break;
							case 'online_only':$newprod->online_only=intval($value);break;
							case 'date_available':$newprod->available_date=psql($value);break;
							case 'additional_shipping_cost':$newprod->additional_shipping_cost=importConv2Float($value);break;
                            case 'condition':$newprod->condition = psql($value);break;
                            case 'show_condition':$newprod->show_condition = intval($value);break;
							case 'reference':
/*								if ($importConfig[$TODOfilename]['iffoundindb']=='replace'
										&& ($importConfig[$TODOfilename]['idby']=='prodref'
												|| $importConfig[$TODOfilename]['idby']=='prodrefandsupref')) break;*/
								escapeCharForPS($value);
								if (isCombination($line) || isCombinationWithID())
								{
									if ($newprod->reference=='' && _s('CAT_IMPORT_FORCE_PROD_REF_TO_FIRST_COMBI')==1)
										$newprod->reference=psql($value);
									if ($newprod->reference=='' && _s('CAT_IMPORT_FORCE_PROD_REF_TO_FIRST_COMBI')==2)
										$newprod->reference=psql($value).'P';
								}else{
										$newprod->reference=psql($value);
								}
								break;
							case 'supplier_reference':
								escapeCharForPS($value);
								if((version_compare(_PS_VERSION_, '1.5.0.0', '>=')))
									$id_supplier = (str_replace("suppref_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
								if (
										(version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										||
										( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
									)
								{
									if (isCombination($line) || isCombinationWithID())
									{
										if ($newprod->supplier_reference=='' && _s('CAT_IMPORT_FORCE_PROD_SUPP_REF_TO_FIRST_COMBI')==1)
                                            $newprod->supplier_reference=psql($value);
                                        if ($newprod->supplier_reference=='' && _s('CAT_IMPORT_FORCE_PROD_SUPP_REF_TO_FIRST_COMBI')==2)
                                            $newprod->supplier_reference=psql($value).'P';
									}else{
										$newprod->supplier_reference=psql($value);
									}
								}
								break;
                            case 'EAN13':
                                //$newprod->ean13=psql($value);
                                if (isCombination($line) || isCombinationWithID())
                                {
                                    /*if ($newprod->ean13=='')
                                        $newprod->ean13=psql($value);*/
                                    if ($newprod->ean13=='' && _s('CAT_IMPORT_FORCE_PROD_EAN_TO_FIRST_COMBI')==1)
                                        $newprod->ean13=psql($value);
                                }else{
                                    $newprod->ean13=psql($value);
                                }
                                break;
                            case 'ISBN':
                                if (isCombination($line) || isCombinationWithID())
                                {
                                    if ($newprod->isbn=='' && _s('CAT_IMPORT_FORCE_PROD_ISBN_TO_FIRST_COMBI')==1)
                                        $newprod->isbn=psql($value);
                                }else{
                                    $newprod->isbn=psql($value);
                                }
                                break;
							case 'upc':
								//$newprod->upc=psql($value);
								if (isCombination($line) || isCombinationWithID())
								{
									/*if ($newprod->upc=='')
										$newprod->upc=psql($value);*/
									if ($newprod->upc=='' && _s('CAT_IMPORT_FORCE_PROD_UPC_TO_FIRST_COMBI')==1)
										$newprod->upc=psql($value);
								}else{
										$newprod->upc=psql($value);
								}
								break;
							case 'weight':
								if (isCombination($line) || isCombinationWithID())
								{
									if ($newprod->weight==0 && _s('CAT_IMPORT_FORCE_PROD_WEIGHT_TO_FIRST_COMBI')==1)
										$newprod->weight=importConv2Float($value);
								}else{
									$newprod->weight=importConv2Float($value);
								}
								break;
							case 'wholesale_price':
								if((version_compare(_PS_VERSION_, '1.5.0.0', '>=')))
									$id_supplier = (str_replace("suppprice_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));

								if (
										(version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										||
										( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
								)
								{
									if (isCombination($line) || isCombinationWithID())
									{
										if ($newprod->wholesale_price==0)
											$newprod->wholesale_price=importConv2Float($value);
									}else{
										$newprod->wholesale_price=importConv2Float($value);
									}
								}

								break;
							case 'ecotax':
								if (isCombination($line) || isCombinationWithID())
								{
									if ($newprod->ecotax==0)
										$newprod->ecotax=importConv2Float($value);
								}else{
									$newprod->ecotax=importConv2Float($value);
								}
								break;
							case 'location':
								escapeCharForPS($value);
								if(!SCAS)
								{
									if (isCombination($line) || isCombinationWithID())
									{
										if ($newprod->location=='')
											$newprod->location=$value;
									}else{
											$newprod->location=$value;
									}
								}
								break;
							case 'on_sale':$newprod->on_sale=intval(getBoolean($value));break;
							case 'reduction_price':
								if ($value != '')
								{
									$value=importConv2Float($value);
									if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
									{
										$useSpecificPrices=true;
										if ($value!=0)
												$TODO[]="UPDATE "._DB_PREFIX_."specific_price SET reduction='".floatval($value)."',reduction_type='amount' WHERE id_product=ID_PRODUCT AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									}else{
										$newprod->reduction_price=$value;
									}
								}
								break;
							case 'reduction_percent':
								if ($value != '')
								{
									$value=importConv2Float($value);
									if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
									{
										$useSpecificPrices=true;
										if ($value!=0)
											$TODO[]="UPDATE "._DB_PREFIX_."specific_price SET reduction='".floatval($value/100)."',reduction_type='percentage' WHERE id_product=ID_PRODUCT AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									}else{
										$newprod->reduction_percent=$value;
									}
								}
								break;
							case 'reduction_tax':
								if ($value != '')
								{
									$value=importConv2Float($value);
									if (version_compare(_PS_VERSION_, '1.6.0.11', '>='))
									{
										$useSpecificPrices=true;
										if ($value!=0)
											$TODO[]="UPDATE "._DB_PREFIX_."specific_price SET reduction_tax='".intval($value)."'WHERE id_product=ID_PRODUCT AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									}
								}
								break;
							case 'reduction_from':
								if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
								{
									if ($value!='')
									{
										$useSpecificPrices=true;
										$TODO[]="UPDATE "._DB_PREFIX_."specific_price SET `from`='".psql($value)."' WHERE id_product=ID_PRODUCT AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									}
								}elseif (version_compare(_PS_VERSION_, '1.3.0.4', '>=')) // DATE => DATETIME field format
								{
									$newprod->reduction_from=importConv2DateTime($value);
								}else{
									$newprod->reduction_from=importConv2Date($value);
								}
								break;
							case 'reduction_to':
								if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
								{
									if ($value!='')
									{
										$useSpecificPrices=true;
										$TODO[]="UPDATE "._DB_PREFIX_."specific_price SET `to`='".psql($value)."' WHERE id_product=ID_PRODUCT AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1";
									}
								}elseif (version_compare(_PS_VERSION_, '1.3.0.4', '>=')) // DATE => DATETIME field format
								{
									$newprod->reduction_to=importConv2DateTime($value);
								}else{
									$newprod->reduction_to=importConv2Date($value);
								}
								break;
							case 'VAT':
								$value=importConv2Float($value);
								if (version_compare(_PS_VERSION_, '1.4.0.0', '<'))
								{
									$newprod->tax_rate=importConv2Float($value);
									$newprod->id_tax = $cache_VAT["id_tax"];
								}else{
									$newprod->tax_rate=importConv2Float($value);
									$newprod->id_tax_rules_group = $cache_VAT["id_tax_rules_group"];
								}
								break;
							case 'priceexctax':
								if (isCombination($line) || isCombinationWithID())
								{
									// set price for first combination)
									if ($newprod->price==0 && _s('CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI'))
										$newprod->price=importConv2Float($value);
								}else{
									$newprod->price=importConv2Float($value);
								}
								break;
							case 'priceinctax':
								$rate = $cache_VAT["rate"];
								if (isCombination($line) || isCombinationWithID())
								{
									// set price for first combination)
									if ($newprod->price==0 && _s('CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI'))
									{
										$newprod->price=importConv2Float(importConv2Float($value)/$rate);
									}
								}else{
									$newprod->price=importConv2Float(importConv2Float($value)/$rate);
								}
								break;
							case 'priceinctaxincecotax':
								$rate = $cache_VAT["rate"];
								if (isCombination($line) || isCombinationWithID())
								{
									// set price for first combination)
									if ($newprod->price==0 && _s('CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI'))
									{
										$newprod->price=importConv2Float((importConv2Float($value)-$newprod->ecotax)/$rate);
									}

								}else{
									$newprod->price=importConv2Float((importConv2Float($value)-$newprod->ecotax)/$rate);
								}
								break;
							case 'out_of_stock':
								if(version_compare(_PS_VERSION_, '1.5.0.0', '<'))
								{
									$arr = array(_l('Deny orders') => 0,_l('Allow orders') => 1,_l('Default(Pref)') => 2);
									if (sc_array_key_exists($value,$arr))
										$value = $arr[$value];
									$newprod->out_of_stock = (int)$value;
								}
								break;
							case 'link_rewrite':if ($value!='') $newprod->link_rewrite[$id_lang]=link_rewrite($value);break;
							case 'name':
								escapeCharForPS($value);
								if ($importConfig[$TODOfilename]['idby']=='prodname' || $importConfig[$TODOfilename]['idby']=='prodrefthenprodname' || $importConfig[$TODOfilename]['idby']=='suprefthenprodname')
									$lastIdentifier=$value;
								$newprod->name[$id_lang]=$value;
								if (!sc_in_array('link_rewrite',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
								{
									foreach($languages AS $lang)
									{
										//if ($newprod->link_rewrite[$lang['id_lang']]=='' || $newprod->link_rewrite[$lang['id_lang']]=='product')
										if(_s('CAT_SEO_NAME_TO_URL'))
											$newprod->link_rewrite[$id_lang]=link_rewrite($value);
									}
								}
								break;
							case 'description_short':escapeCharForPS($value,true);$newprod->description_short[$id_lang]=$value;break;
							case 'description':
							escapeCharForPS($value,true);
							$newprod->description[$id_lang]=$value;break;
							case 'meta_title':escapeCharForPS($value);$newprod->meta_title[$id_lang]=$value;break;
							case 'meta_description':escapeCharForPS($value);$newprod->meta_description[$id_lang]=$value;break;
							case 'meta_keywords':escapeCharForPS($value);$newprod->meta_keywords[$id_lang]=$value;break;
							case 'available_now':escapeCharForPS($value);$newprod->available_now[$id_lang]=$value;break;
							case 'available_later':
								if (!isCombination($line) || (isCombination($line) && SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")!="1"))
								{
									escapeCharForPS($value);
									$newprod->available_later[$id_lang]=$value;
								}
								break;
							case 'feature':
								if ($value!='')
								{
									$id_feature=0;
									$id_feature_value=0;
									$value = trim($value);
									if (sc_in_array($mappingData['CSV2DBOptions'][$firstLineData[$key]],array_keys($features),"catWinImportProcess_arraykeysfeatures"))
										$id_feature=intval($features[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
									if (sc_in_array($id_feature.'_|_'.$value,array_keys($featureValues),"catWinImportProcess_arraykeysfeaturevalues"))
										$id_feature_value=intval($featureValues[$id_feature.'_|_'.$value]);
									if ($id_feature && $id_feature_value)
									{
										if ($id_product!=0)
											$TODO[]="DELETE FROM "._DB_PREFIX_."feature_product WHERE id_feature=".intval($id_feature)." AND id_product=".intval($id_product);
										$TODO[]="INSERT IGNORE INTO "._DB_PREFIX_."feature_product (id_feature_value,id_feature,id_product) VALUES (".intval($id_feature_value).",".intval($id_feature).",ID_PRODUCT)";
									}elseif($id_feature && $id_feature_value==0 && $value=='-'){
										$TODO[]="DELETE FROM "._DB_PREFIX_."feature_product WHERE id_feature=".intval($id_feature)." AND id_product=".intval($id_product);
									}else{
										die(_l('Feature not found: ').$mappingData['CSV2DBOptions'][$firstLineData[$key]].' - value: '.$value.' id_feature:'.$id_feature.' id_feature_value:'.$id_feature_value);
									}
								}
								break;
							case 'feature_custom':
								if ($value!='')
								{
									$id_feature=0;
									$id_feature_value=0;
									if (sc_in_array($mappingData['CSV2DBOptions'][$firstLineData[$key]],array_keys($features),"catWinImportProcess_arraykeysfeatures"))
										$id_feature=intval($features[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
									if ($id_feature > 0)
									{
										$sql = "SELECT id_feature_value FROM "._DB_PREFIX_."feature_product WHERE id_feature=".intval($id_feature)." AND id_product=".intval($id_product);
										$fv=Db::getInstance()->getRow($sql);
										$id_feature_value_OLD=intval($fv['id_feature_value']);
										if($value=='-'){
											$TODO[]="DELETE FROM "._DB_PREFIX_."feature_product WHERE id_feature=".intval($id_feature)." AND id_product=ID_PRODUCT";
											$TODO[]="DELETE FROM "._DB_PREFIX_."feature_value_lang WHERE id_feature_value IN (SELECT fv.id_feature_value FROM "._DB_PREFIX_."feature_value fv WHERE fv.id_feature_value=".intval($id_feature_value_OLD)." AND fv.custom=1)";
											$TODO[]="DELETE FROM "._DB_PREFIX_."feature_value WHERE id_feature_value=".intval($id_feature_value_OLD)." AND custom=1";
										}else{
											$sql = "SELECT custom FROM "._DB_PREFIX_."feature_value WHERE id_feature_value=".intval($id_feature_value_OLD)." AND id_feature=".intval($id_feature);
											$fv=Db::getInstance()->getRow($sql);
											if ($fv['custom'])
											{
												foreach($languages AS $lang){
													$TODO[]="UPDATE "._DB_PREFIX_."feature_value_lang SET value='".psql($value)."' WHERE id_feature_value=".intval($id_feature_value_OLD)." AND id_lang=".intval($lang['id_lang']);
												}
											}else{
												$TODO[]="DELETE FROM "._DB_PREFIX_."feature_product WHERE id_feature=".intval($id_feature)." AND id_product=ID_PRODUCT";
												$sql="INSERT INTO "._DB_PREFIX_."feature_value (id_feature,custom) VALUES (".intval($id_feature).",1)";
												Db::getInstance()->Execute($sql);
												$id_value = Db::getInstance()->Insert_ID();
												$TODO[]="INSERT IGNORE INTO "._DB_PREFIX_."feature_product (id_feature_value,id_feature,id_product) VALUES (".intval($id_value).",".intval($id_feature).",ID_PRODUCT)";
												foreach($languages AS $lang){
													$TODO[]="INSERT INTO "._DB_PREFIX_."feature_value_lang (id_feature_value,id_lang,value) VALUES (".intval($id_value).",".intval($lang['id_lang']).",'".psql($value)."')";
												}
											}
										}
									}
								}
								break;
							case 'category_default':
								$value=trim($value,'>');
								if ($value!='')
								{
									checkAndCreateCategory(array($value));
									if ((strpos($value,'>')!=false) || (in_array($value,$categoriesFirstLevel)))
									{
										$newprod->id_category_default=(sc_array_key_exists(forceCategoryPathFormat($value),$categoryIDByPath) ? intval($categoryIDByPath[forceCategoryPathFormat($value)]) : $id_category);
									}else{ // single category
										$newprod->id_category_default=(in_array($value,array_keys($categories)) ? intval($categories[$value]['id_category']) : $id_category);
									}
									if ($newprod->id_category_default!=$id_category) // if id_category_default is not the "importxxx.csv" category
									{
										$sql="SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($newprod->id_category_default)." AND id_product=".intval($id_product);
										$nbrow=Db::getInstance()->ExecuteS($sql,1,0);
										if (count($nbrow)==0)
										{
											$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($newprod->id_category_default);
											$row=Db::getInstance()->getRow($sql,0);
											$TODO[]="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=ID_PRODUCT AND id_category=".intval($newprod->id_category_default);
											$TODO[]="INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES (".intval($newprod->id_category_default).",ID_PRODUCT,".($row['maxpos']+1).")";
										}
									}
								}
								break;
							case 'id_category_default':
								$value=(int)($value);
								if ($value!=0)
								{
									$newprod->id_category_default=$value;
									$sql="SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($newprod->id_category_default)." AND id_product=".intval($id_product);
									$nbrow=Db::getInstance()->ExecuteS($sql,1,0);
									if (count($nbrow)==0)
									{
										$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($newprod->id_category_default);
										$row=Db::getInstance()->getRow($sql,0);
										$TODO[]="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=ID_PRODUCT AND id_category=".intval($newprod->id_category_default);
										$TODO[]="INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES (".intval($newprod->id_category_default).",ID_PRODUCT,".($row['maxpos']+1).")";
									}
								}
								break;
							case 'categories':
								$value=trim(trim(trim($value),'>'));
								if ($value!='')
								{
									$categ=explode($importConfig[$TODOfilename]['categorysep'],$value);
									checkAndCreateCategory($categ);
									if (_s('CAT_IMPORT_DELETE_CATEGORIES'))
									{
										$sql="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=".intval($id_product)." AND id_category!=".intval($newprod->id_category_default);
										Db::getInstance()->Execute($sql);
									}
									foreach($categ AS $c)
									{
										$c=cleanQuotes($c);
										$idc=0;
										if ((strpos($c,'>')!==false) || (in_array(trim($c),$categoriesFirstLevel))) // category path
										{
											$idc=(sc_array_key_exists(forceCategoryPathFormat($c),$categoryIDByPath) ? intval($categoryIDByPath[forceCategoryPathFormat($c)]) : 0);
										}else{
											if (in_array($c,array_keys($categories))) // single category
											{
												$idc=$categories[$c]['id_category'];
											}
										}
										if ($idc!=0)
										{
											$sql="SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($idc)." AND id_product=".intval($id_product);
											$nbrow=Db::getInstance()->ExecuteS($sql,1,0);
											if (count($nbrow)==0)
											{
												$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($idc);
												$row=Db::getInstance()->getRow($sql,0);
												$TODO[]="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=ID_PRODUCT AND id_category=".intval($idc);
												$TODO[]="INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES (".intval($idc).",ID_PRODUCT,".($row['maxpos']+1).")";
											}
										}
									}
								}
								break;
							case 'id_category':
								$value=trim($value);
								if ($value!='')
								{
									$categ=explode($importConfig[$TODOfilename]['valuesep'],$value);
									if (_s('CAT_IMPORT_DELETE_CATEGORIES'))
									{
										$sql="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=".intval($id_product)." AND id_category!=".intval($newprod->id_category_default);
										Db::getInstance()->Execute($sql);
									}
									foreach($categ AS $c)
									{
										$c=cleanQuotes($c);
										$idc=0;
										if (is_numeric($c))
											$idc=(int)$c;
										if ($idc!=0)
										{
											$sql="SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($idc)." AND id_product=".intval($id_product);
											$nbrow=Db::getInstance()->ExecuteS($sql);
											if (count($nbrow)==0)
											{
												$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($idc);
												$row=Db::getInstance()->getRow($sql);
												$TODO[]="DELETE FROM "._DB_PREFIX_."category_product WHERE id_product=ID_PRODUCT AND id_category=".intval($idc);
												$TODO[]="INSERT INTO "._DB_PREFIX_."category_product (id_category,id_product,position) VALUES (".intval($idc).",ID_PRODUCT,".($row['maxpos']+1).")";
											}
										}
									}
								}
								break;
							case 'width':$newprod->width=importConv2Float($value);break;
							case 'height':$newprod->height=importConv2Float($value);break;
							case 'depth':$newprod->depth=importConv2Float($value);break;
							case 'unity':$newprod->unity=psql($value);break;
							case 'unit_price_ratio':
								if (importConv2Float($value)>0)
								{
									$newprod->unit_price_ratio=$newprod->price/importConv2Float($value);
									$newprod->unit_price=importConv2Float($value);
								}else{
									$newprod->unit_price_ratio=0;
									$newprod->unit_price=0;
								}
								break;
							case 'id_shop_default':$newprod->id_shop_default=(int)$value;break;
							case 'redirect_type':$newprod->redirect_type=psql($value);break;
							case 'id_product_redirected':$newprod->id_product_redirected=(int)$value;break;
							default:
								sc_ext::readImportCSVConfigXML('importProcessProduct');
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
					echo "<br/><br/>"._l('Before')." product->save : $time "._l('seconds');
				}
				foreach($languages AS $lang)
					if ($newprod->link_rewrite[$lang['id_lang']]=='')
						$newprod->link_rewrite[$lang['id_lang']] = 'product';

				if ($newprod->save())
				{
					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>"._l('After')." product->save : $time "._l('seconds');
					}
					$lastid_product=$newprod->id;
					if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
						SCI::hookExec('updateProduct', array('product' => $newprod));
					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>"._l('After')." updateProduct Hook : $time "._l('seconds');
					}
					if ($scdebug) echo 'e';
					if ($newprod->id_category_default==$id_category)
					{
						$sql="SELECT id_product FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_category)." AND id_product=".intval($newprod->id);
						$nbrow=Db::getInstance()->ExecuteS($sql,1,0);
						if (count($nbrow)==0)
						{
							$sql="SELECT MAX(position) as maxpos FROM "._DB_PREFIX_."category_product WHERE id_category=".intval($id_category);
							$row=Db::getInstance()->getRow($sql,0);
							$sql="INSERT INTO "._DB_PREFIX_."category_product VALUE (".intval($id_category).",".$newprod->id.",".($row['maxpos']+1).")";
							Db::getInstance()->Execute($sql);
						}
					}
					if ($useSpecificPrices) // only used in PS 1.4 for specific prices
					{
						$res=Db::getInstance()->getRow("SELECT COUNT(*) AS nb FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($newprod->id)." AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1 GROUP BY reduction_type");
						if ((int)$res['nb']==0)
						{
							$sql = "INSERT INTO "._DB_PREFIX_."specific_price (reduction,reduction_type,id_product,id_group,id_currency,id_country,from_quantity".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?',id_customer,id_product_attribute,price':'').") VALUES ('0','amount',".intval($newprod->id).",0,0,0,1".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?',0,0,-1':'').")";
							Db::getInstance()->Execute($sql);
						}
					}

					if (SCAS && empty($id_product))
					{
						$value = 0;
						$type = _s("CAT_ADVANCEDSTOCK_DEFAULT");
						$idproduct = (int)$newprod->id;
						if($type==1) // disabled
						{
							$value = 0;
							foreach ($id_shop_list as $shop)
								StockAvailable::setProductDependsOnStock(intval($idproduct), false, $shop);
						}
						elseif($type==2) // enabled
						{
							$value = 1;
							foreach ($id_shop_list as $shop)
								StockAvailable::setProductDependsOnStock(intval($idproduct), true, $shop);
						}
						elseif($type==3) // enabled + manual
						{
							$value = 1;
							foreach ($id_shop_list as $shop)
								StockAvailable::setProductDependsOnStock(intval($idproduct), false, $shop);
						}

						$TODO[]="UPDATE "._DB_PREFIX_."product SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newprod->id)."";
						$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";
					}

					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>"._l('Before product')." TODO  : $time "._l('seconds');
					}
					$TODO[]="UPDATE "._DB_PREFIX_."product SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id)."";
					if(SCMS)
						$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";
					foreach($TODO AS $sql)
					{
						$sql=str_replace('ID_PRODUCT',$newprod->id,$sql);
						Db::getInstance()->Execute($sql);
					}
					if (_s('APP_DEBUG_CATALOG_IMPORT'))
					{
						$time_end = microtime(true);
						$time = $time_end - $time_start;
						echo "<br/><br/>"._l('After product')." TODO : $time "._l('seconds');
					}

					if(!empty($newprod->id))
					{
						if (SCMS)
						{
							$newprod=new Product($newprod->id, false, null, $id_shop);
							if(findCSVLineValue('id_shop_default')!='' && $newprod->id_shop_default != findCSVLineValue('id_shop_default'))
                            {
                                $newprod->id_shop_default = (int)findCSVLineValue('id_shop_default');
								$newprod->id_shop_list=$id_shop_list;
                                $newprod->save();
                            }
							if ($id_shop==null)
								$id_shop_list=array($newprod->id_shop_default);
						}
						elseif(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							$newprod=new Product($newprod->id, false, null,$id_shop);
						else
							$newprod=new Product($newprod->id);
						$newprod->date_upd = date("Y-m-d H:i:s");
					}

					if ($useSpecificPrices) // only used in PS 1.4 for specific prices
						Db::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."specific_price WHERE id_product=".intval($newprod->id)." AND price=0 AND reduction=0 AND id_group=0 AND id_currency=0 AND id_country=0 AND from_quantity=1");
					$attributesList=array();
					$imagesListFromCSVLine=array(); // contains images to link to the current combination
					$imagesListFromDB=array(); // contains images of current product
					if (_s('CAT_PROD_IMG_SAVE_FILENAME'))
					{
						$sql="SELECT id_image,sc_path FROM "._DB_PREFIX_."image WHERE id_product=".intval($newprod->id);
						$res=Db::getInstance()->ExecuteS($sql);
						foreach($res AS $img)
						{
							$imagesListFromDB[$newprod->id.'_|_'.$img['sc_path']]=$img['id_image'];
						}
					}
					$combinationValues=array(
																'id_product' => $newprod->id,
																'price' => 0,
																'weight' => 0,
																'ecotax' => 0,
																'quantity' => 0,
																'reference' => '',
																'supplier_reference' => '',
																'ean13' => '',
																'upc' => '',
																'location' => '',
																'available_date' => '',
																'wholesale_price' => 0,
																'default_on' => 0
															);
                    if (version_compare(_PS_VERSION_,'1.4.0.0','>='))
                        $combinationValues['minimal_quantity']=0;
                    if (version_compare(_PS_VERSION_,'1.7.0.0','>='))
                        $combinationValues['isbn']='';
					$combinationUsed=false;
					$skip_line_scas = false;
					$image_id_array = array();
					$image_legend_array = array();
					sc_ext::readImportCSVConfigXML('importProcessInitRowVars');

					$TODO=array();
					$TODO_SET=array();
					$TODO_SET_SHOP=array();

					foreach($line AS $key => $value)
					{
						$GLOBALS['import_value']=$value;
						if ($scdebug && !in_array($key,array_keys($firstLineData))) {print_r($line);}
						if (sc_array_key_exists($key,$firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinImportProcess_CSVArray"))
						{
							@$id_lang=intval($getIDlangByISO[$mappingData['CSV2DBOptions'][$firstLineData[$key]]]);
							$switchObject=$mappingData['CSV2DB'][$firstLineData[$key]];
							if (_s('APP_DEBUG_CATALOG_IMPORT'))
							{
								$time_end = microtime(true);
								$time = $time_end - $time_start;
								echo "<br/><br/>"._l('Before')." ".$switchObject." column : $time "._l('seconds');
							}
							if(SCAS) {
								$arrayWarehouseOptions=array();
								findAllCSVLineValue('quantity_on_sale',$arrayWarehouseOptions);
								$warehouseOptionCache=array();
								foreach($arrayWarehouseOptions as $warehouseOption) {
									$id_warehouse = intval(str_replace("warehouse_", "", $warehouseOption["option"]));
									$warehouseOptionCache[$id_warehouse] = (int)$warehouseOption["value"];
								}
							}

							switch($switchObject)
							{
								case 'attachments':
									$attachments=explode($importConfig[$TODOfilename]['valuesep'],$value);
									$attachmentsID=array();
									if(!isset($cacheAttachments)) $cacheAttachments=array();
									foreach($attachments AS $attachment)
									{
										$attachment=trim($attachment);
										if (sc_array_key_exists($attachment,$cacheAttachments))
										{
											$attachmentsID[]=$cacheAttachments[$attachment];
										}else{
											$sql = "SELECT id_attachment FROM `"._DB_PREFIX_."attachment` WHERE file_name='".psql($attachment)."'";
											$res=Db::getInstance()->ExecuteS($sql);
											if (count($res))
												$attachmentsID[]=$res[0]['id_attachment'];
										}
									}
									if (count($attachmentsID))
									{
										$TODO[] = "DELETE FROM `"._DB_PREFIX_."product_attachment` WHERE `id_attachment` IN (".join(',',$attachmentsID).") AND `id_product`=".$newprod->id."";
										foreach($attachmentsID AS $aid)
											$TODO[] = "INSERT INTO `"._DB_PREFIX_."product_attachment` (id_product,id_attachment) VALUES (".$newprod->id.",".intval($aid).")";
										if (version_compare(_PS_VERSION_,'1.4.0.2','>='))
											$TODO[]="UPDATE `"._DB_PREFIX_."product` SET cache_has_attachments=1 WHERE `id_product`=".$newprod->id."";
									}
									break;
								case 'carriers':
									$value=trim(trim($value),$importConfig[$TODOfilename]['valuesep']);
									if(!empty($value))
									{
										$carriers = explode($importConfig[$TODOfilename]['valuesep'], $value);
										if(!empty($carriers) && count($carriers)>0)
										{
											$sql_carriers = "";
											foreach($carriers as $carrier)
											{
												if(!empty($dataDB_carrierByName[$carrier]))
												{
													$id_carrier = $dataDB_carrierByName[$carrier];
													$sql = "SELECT id_product FROM `"._DB_PREFIX_."product_carrier` WHERE id_product = '".intval($newprod->id)."' AND id_carrier_reference = '".intval($id_carrier)."' AND id_shop='".intval($id_shop)."'";
													$findCarrierProduct = Db::getInstance()->executeS($sql);
													if(count($findCarrierProduct)==0 || empty($findCarrierProduct[0]["id_product"]))
													{
														$sql_carriers.='('.(int)$newprod->id.','.(int)$id_carrier.','.(int)$id_shop.'),';
													}
												}
											}
											if ($sql_carriers!='')
											{
												$sql_carriers = trim($sql_carriers,',');
												$TODO[]="INSERT INTO `"._DB_PREFIX_."product_carrier` (id_product,id_carrier_reference,id_shop) VALUES ".psql($sql_carriers);
											}
										}
									}
									break;
								case 'accessories':
									$value=trim(trim($value),$importConfig[$TODOfilename]['valuesep']);
									if(!empty($value))
									{
										$refs = explode($importConfig[$TODOfilename]['valuesep'], $value);
										if(!empty($refs) && count($refs)>0)
										{
											$sql_accessories = "";
											foreach($refs as $ref)
											{
												$sql = "SELECT id_product FROM `"._DB_PREFIX_."product` WHERE reference = '".psql(trim($ref))."' LIMIT 1";
												$findAccessory = Db::getInstance()->executeS($sql);
												if(!empty($findAccessory[0]["id_product"]) && $findAccessory[0]["id_product"]!=$newprod->id)
												{
													$id_accessory = $findAccessory[0]["id_product"];

													$sql = "SELECT id_product_1 FROM `"._DB_PREFIX_."accessory` WHERE id_product_1='".(int)$newprod->id."' AND id_product_2='".(int)$id_accessory."'";
													$isAccessory = Db::getInstance()->executeS($sql);
													if(empty($isAccessory[0]["id_product_1"]))
														$sql_accessories.='('.(int)$newprod->id.','.(int)$id_accessory.'),';
												}
											}
											if ($sql_accessories!='')
											{
												$sql_accessories = trim($sql_accessories,',');
												$TODO[]="INSERT INTO `"._DB_PREFIX_."accessory` (id_product_1,id_product_2) VALUES ".psql($sql_accessories);
											}
										}
									}
									break;
								case 'supplier':
									$id_supplier = (sc_array_key_exists($value,$dataDB_supplierByName)? intval($dataDB_supplierByName[$value]):0);
									if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
									{
										//$newprod->id_supplier=(int)$id_supplier;
									}else{
										//$newprod->id_supplier=(int)$id_supplier;
										$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, (int)$id_product_attribute, (int)$id_supplier);
										if (!$id_product_supplier && $newprod->id)
										{
											//create new record
											$product_supplier_entity = new ProductSupplier();
											$product_supplier_entity->id_product = (int)$newprod->id;
											$product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
											$product_supplier_entity->id_supplier = (int)$id_supplier;
											$product_supplier_entity->product_supplier_reference = pSQL(findCSVLineValue('supplier_reference'));
											$product_supplier_entity->product_supplier_price_te = 0;
											$product_supplier_entity->id_currency = 0;
											$product_supplier_entity->save();
										}else{
											$product_supplier = new ProductSupplier((int)$id_product_supplier);
											$product_supplier->product_supplier_reference = pSQL(findCSVLineValue('supplier_reference'));
											$product_supplier->update();
										}
									}
									if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										$TODO[]="UPDATE "._DB_PREFIX_."product SET id_supplier='".(int)$id_supplier."' WHERE id_product=".(int)$newprod->id;
									break;
								case 'supplier_default':
									$id_supplier = (sc_array_key_exists($value,$dataDB_supplierByName)? intval($dataDB_supplierByName[$value]):0);
									if(!empty($id_supplier))
									{
										if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										{
											//$newprod->id_supplier=(int)$id_supplier;
										}else{
											//$newprod->id_supplier=(int)$id_supplier;
											$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, 0, (int)$id_supplier);

											if (empty($id_product_supplier) && $newprod->id)
											{
												//create new record
												$product_supplier_entity = new ProductSupplier();
												$product_supplier_entity->id_product = (int)$newprod->id;
												$product_supplier_entity->id_product_attribute = 0;
												$product_supplier_entity->id_supplier = (int)$id_supplier;
												$product_supplier_entity->product_supplier_reference = pSQL(findCSVLineValue('supplier_reference'));
												$product_supplier_entity->product_supplier_price_te = 0;
												$product_supplier_entity->id_currency = 0;
												$product_supplier_entity->save();
											}else{
												$product_supplier = new ProductSupplier((int)$id_product_supplier);
												$product_supplier->product_supplier_reference = pSQL(findCSVLineValue('supplier_reference'));
												$product_supplier->update();
											}
										}
										$TODO[]="UPDATE "._DB_PREFIX_."product SET id_supplier='".(int)$id_supplier."' WHERE id_product=".(int)$newprod->id;
									}
									break;
								case 'out_of_stock':
									if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									{
										$arr = array(_l('Deny orders') => 0,_l('Allow orders') => 1,_l('Default(Pref)') => 2);
										if (sc_array_key_exists($value,$arr))
											$value = $arr[$value];
										StockAvailable::setProductOutOfStock($newprod->id, (int)$value, (int)$id_shop, intval($id_product_attribute));
									}
									break;
								case 'priceexctax':
									if (isCombination($line))
									{
										$rate = $cache_VAT["rate"];
										if (version_compare(_PS_VERSION_, '1.4.0.4', '<'))
										{
											$combinationValues['price']=(importConv2Float($value)-$newprod->price)*$rate;
										}else{
											$combinationValues['price']=(importConv2Float($value)-$newprod->price);
										}
									}
									if (isCombinationWithID())
									{
										$rate = $cache_VAT["rate"];
										if (version_compare(_PS_VERSION_, '1.4.0.4', '<'))
										{
											$TODO_SET[] = "price=".importConv2Float((importConv2Float($value)*$rate)-($newprod->price*$rate));
										}else{
											$TODO_SET[] = "price=".importConv2Float(importConv2Float($value)-$newprod->price);
										}
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[] = "price=".importConv2Float(importConv2Float($value)-$newprod->price);
									}
									break;
								case 'price_impact':
									if (isCombination($line))
										$combinationValues['price']=importConv2Float($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]= "price=".importConv2Float($value);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="price=".importConv2Float($value);
									}
									break;
                                case 'unit_price_ratio':
                                    if (importConv2Float($value)>0)
                                    {
                                        $TODO[]="UPDATE "._DB_PREFIX_."product SET unit_price_ratio=price/".importConv2Float($value)." WHERE id_product=".intval($newprod->id);
                                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                                            $TODO[]="UPDATE "._DB_PREFIX_."product_shop SET unit_price_ratio=price/".importConv2Float($value)." WHERE id_product=".intval($newprod->id)." AND id_shop IN (".join(',',$id_shop_list).")";
                                        if (importConv2Float($value)>0)
                                        {
                                            $newprod->unit_price_ratio=$newprod->price/importConv2Float($value);
                                            $newprod->unit_price=importConv2Float($value);
                                        }else{
                                            $newprod->unit_price_ratio=0;
                                            $newprod->unit_price=0;
                                        }
                                    }
                                    break;
								case 'sc_active':
									if (isCombination($line))
										$combinationValues['sc_active']=($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]= "sc_active=".(int)$value;
									}
									break;
								case 'attribute_default_on':
									if (isCombinationWithID() && !empty($id_product_attribute) && getBoolean($value))
									{
										if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
										{
											if($value) {
												$TODO[] = "UPDATE " . _DB_PREFIX_ . "product_attribute_shop SET default_on=0 WHERE id_shop IN (" . join(',',
														$id_shop_list) . ")";
												$TODO_SET_SHOP[] = "default_on=1";
												$TODO[] = "UPDATE " . _DB_PREFIX_ . "product_shop SET cache_default_attribute = " . (int)$id_product_attribute . " WHERE id_product = " . (int)$id_product . " AND id_shop IN (" . join(',',
														$id_shop_list) . ")";
											}
										}elseif (version_compare(_PS_VERSION_,'1.4.0.0','>='))
										{
											if($value)
												$newprod->setDefaultAttribute($id_product_attribute);
										}else{
											if($value)
											{
												$newprod->deleteDefaultAttributes();
												$TODO_SET[]= "default_on=1";
											}
										}
									}
									break;
								case 'priceinctax':
									if (isCombination($line))
									{
										$rate = $cache_VAT["rate"];
										if (version_compare(_PS_VERSION_, '1.4.0.4', '<'))
										{
											$combinationValues['price']=importConv2Float((importConv2Float(importConv2Float($value)/$rate)-$newprod->price)*$rate);
										}else{
											$combinationValues['price']=importConv2Float(importConv2Float($value)/$rate-$newprod->price);
										}
									}
									if (isCombinationWithID())
									{
										$rate = $cache_VAT["rate"];
										if (version_compare(_PS_VERSION_, '1.4.0.4', '<'))
										{
											$TODO_SET[]="price=".importConv2Float(((importConv2Float($value)/$rate)-$newprod->price)*$rate);
										}else{
											$TODO_SET[]="price=".(importConv2Float(importConv2Float($value)/$rate)-$newprod->price);
										}
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="price=".(importConv2Float(importConv2Float($value)/$rate)-$newprod->price);
									}
									break;
								case 'unit_price_impact':
									if (isCombination($line))
										$combinationValues['unit_price_impact']=importConv2Float($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="unit_price_impact=".floatval(importConv2Float($value));
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="unit_price_impact=".floatval(importConv2Float($value));
									}
									break;
								case 'weight':
									if (isCombination($line))
										$combinationValues['weight']=importConv2Float($value)-$newprod->weight;
									if (isCombinationWithID())
									{
										$TODO_SET[]="weight=".floatval(importConv2Float($value)-$newprod->weight);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="weight=".floatval(importConv2Float($value)-$newprod->weight);
									}
									break;
								case 'weight_impact':
									if (isCombination($line))
										$combinationValues['weight']=importConv2Float($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="weight=".floatval(importConv2Float($value));
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="weight=".floatval(importConv2Float($value));
									}
									break;
								case 'EAN13':
									if (isCombination($line))
										$combinationValues['ean13']=$value;
									if (isCombinationWithID())
										$TODO_SET[]="ean13='".psql($value)."'";
									break;
								case 'upc':
									if (isCombination($line))
										$combinationValues['upc']=$value;
									if (isCombinationWithID())
										$TODO_SET[]="upc='".psql($value)."'";
									break;
                                case 'ISBN':
                                    if (isCombination($line))
                                        $combinationValues['isbn']=$value;
                                    if (isCombinationWithID())
										$TODO_SET[]="isbn='".psql($value)."'";
                                    break;
								case 'advanced_stock_management':
									if (/*!isCombination($line) &&*/ SCAS)
									{
										$type = intval($value);
										if(!sc_in_array($type, array(1,2,3),"catWinImportProcess_asmtypes") && empty($id_product))// si 0 et création
											$type = _s("CAT_ADVANCEDSTOCK_DEFAULT");

										if(!empty($type))
										{
											$value = 0;
											$idproduct = (int)$newprod->id;
											if($type==1) // disabled
											{
												$value = 0;
												foreach ($id_shop_list as $shop)
													StockAvailable::setProductDependsOnStock(intval($idproduct), false, $shop);
											}
											elseif($type==2) // enabled
											{
												$value = 1;
												foreach ($id_shop_list as $shop)
													StockAvailable::setProductDependsOnStock(intval($idproduct), true, $shop);
											}
											elseif($type==3) // enabled + manual
											{
												$value = 1;
												foreach ($id_shop_list as $shop)
													StockAvailable::setProductDependsOnStock(intval($idproduct), false, $shop);
											}

											$TODO[]="UPDATE "._DB_PREFIX_."product SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newprod->id)."";
											$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET `advanced_stock_management`='".psql(html_entity_decode($value))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";

											$newprod->advanced_stock_management = $value;
											$productsStockAdvancedTypeArray[$id_shop][$id_product] = $type;
										}
									}
									break;
								case 'location':
									$no_scas = true;
									if(SCAS && !isCombination($line))
									{
										if(empty($productsStockAdvancedTypeArray[$id_shop][$newprod->id]))
										{
											$type_advanced_stock_management = 1;
											if($newprod->advanced_stock_management==1)
											{
												$type_advanced_stock_management = 2;
												if(!StockAvailable::dependsOnStock((int)$newprod->id, $id_shop))
													$type_advanced_stock_management = 3;
											}
											$productsStockAdvancedTypeArray[$id_shop][$newprod->id] = $type_advanced_stock_management;
										}
										else
											$type_advanced_stock_management = $productsStockAdvancedTypeArray[$id_shop][$newprod->id];

										$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));

										if(($type_advanced_stock_management==2 || $type_advanced_stock_management==3) && !empty($value))
										{
											$no_scas = false;
											if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value))
											{
												$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, 0, (int)$id_warehouse);
												if(empty($in_warehouse)) // s'il n'est pas lié à l'entrepot
												{
													$new = new WarehouseProductLocation();
													$new->id_product = (int)$newprod->id;
													$new->id_product_attribute = 0;
													$new->id_warehouse = (int)$id_warehouse;
													$new->location = $value;
													$new->save();
												}
												else
												{
													$new = new WarehouseProductLocation($in_warehouse);
													$new->location = $value;
													$new->save();
												}
											}
										}
										elseif($type_advanced_stock_management==1 && !empty($value) && empty($id_warehouse)) // pour produit avec SA désactivé
										{
											$TODO[]="UPDATE "._DB_PREFIX_."product SET location='".psql($value)."' WHERE id_product=".intval($newprod->id);
											$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET location='".psql($value)."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".join(',',$id_shop_list).")";;
										}
									}

									if($no_scas && !SCAS)
									{
										if (isCombination($line))
											$combinationValues['location']=$value;
										if (isCombinationWithID())
										{
											$TODO_SET[]="location='".psql($value)."'";
											if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
												$TODO_SET_SHOP[]="location='".psql($value)."'";
										}
									}
									break;
								case 'wholesale_price':
									if (isCombination($line))
										$combinationValues['wholesale_price']=importConv2Float($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="wholesale_price=".importConv2Float($value);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="wholesale_price=".importConv2Float($value);
									}

									if (isCombinationWithID())
									{
										$id_supplier = (str_replace("suppprice_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
										if (
												(version_compare(_PS_VERSION_, '1.5.0.0', '<'))
												||
												( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
										)
										{
											$TODO_SET[]="wholesale_price=".importConv2Float($value);
											if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
												$TODO_SET_SHOP[]="wholesale_price=".importConv2Float($value);
										}
										elseif( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && $id_supplier!="product")
										{
											$id_supplier = intval(str_replace("supp_","",$id_supplier));
											if(!empty($id_supplier))
											{
												$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, $id_product_attribute, (int)$id_supplier);
												if ($id_product_supplier)
													$product_supplier = new ProductSupplier((int)$id_product_supplier);
												else
												{
													$product_supplier = new ProductSupplier();
													$product_supplier->id_product = (int)$newprod->id;
													$product_supplier->id_product_attribute = $id_product_attribute;
													$product_supplier->id_supplier = (int)$id_supplier;
												}
												$product_supplier->product_supplier_price_te = importConv2Float($value);
												// CURRENCY SUPPLIER
													$supplier_currencies = array();
													findAllCSVLineValue('supplier_currency',$supplier_currencies);
													foreach($supplier_currencies as $supplier_currency)
													{
														$currency_id_supplier = intval(str_replace("supcurrency_supp_","", $supplier_currency["option"]));
														if(!empty($currency_id_supplier) && is_numeric($currency_id_supplier) && $supplier_currency["value"])
														{
															if($currency_id_supplier==$id_supplier)
															{
																$supplier_id_currency = Currency::getIdByIsoCode($supplier_currency["value"]);
																if(!empty($supplier_id_currency) && is_numeric($supplier_id_currency))
																	$product_supplier->id_currency = $supplier_id_currency;
															}
														}
													}
												$product_supplier->save();
												if($id_supplier==$newprod->id_supplier)
													$TODO_SET_SHOP[]="wholesale_price='".psql(importConv2Float($value))."'";
											}
										}
									}
									// only for product without combinations
									if (!isCombination($line) && !isCombinationWithID() && version_compare(_PS_VERSION_, '1.5.0.0', '>=') && $id_product_attribute==0)
									{
										$id_supplier = (str_replace("suppprice_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
										if($id_supplier!="product")
										{
											$id_supplier = intval(str_replace("supp_","",$id_supplier));
											if(!empty($id_supplier))
											{
												$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, 0, (int)$id_supplier);
												if ($id_product_supplier)
													$product_supplier = new ProductSupplier((int)$id_product_supplier);
												else
												{
													$product_supplier = new ProductSupplier();
													$product_supplier->id_product = (int)$newprod->id;
													$product_supplier->id_product_attribute = 0;
													$product_supplier->id_supplier = (int)$id_supplier;
												}
												$product_supplier->product_supplier_price_te = importConv2Float($value);
												// CURRENCY SUPPLIER
													$supplier_currencies = array();
													findAllCSVLineValue('supplier_currency',$supplier_currencies);
													foreach($supplier_currencies as $supplier_currency)
													{
														$currency_id_supplier = intval(str_replace("supcurrency_supp_","", $supplier_currency["option"]));
														if(!empty($currency_id_supplier) && is_numeric($currency_id_supplier) && $supplier_currency["value"])
														{
															if($currency_id_supplier==$id_supplier)
															{
																$supplier_id_currency = Currency::getIdByIsoCode($supplier_currency["value"]);
																if(!empty($supplier_id_currency) && is_numeric($supplier_id_currency))
																	$product_supplier->id_currency = $supplier_id_currency;
															}
														}
													}
												$product_supplier->save();

												if($id_supplier==$newprod->id_supplier)
													$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET wholesale_price='".psql(importConv2Float($value))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".join(',',$id_shop_list).")";;
											}
										}
									}
									break;
								case 'ecotax':
									if (isCombination($line))
										$combinationValues['ecotax']=importConv2Float($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="ecotax=".importConv2Float($value);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="ecotax=".importConv2Float($value);
									}
									break;
								case 'date_available':case 'available_date': // was a bug in SC: date_available was used instead of available_date. Keep this line of code please.
									if (isCombination($line))
										$combinationValues['available_date']=pSQL($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="available_date='".pSQL($value)."'";
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="available_date='".pSQL($value)."'";
									}
									break;
								case 'minimal_quantity':
									if (isCombination($line))
										$combinationValues['minimal_quantity']=intval($value);
									if (isCombinationWithID())
									{
										$TODO_SET[]="minimal_quantity=".(int)$value;
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											$TODO_SET_SHOP[]="minimal_quantity=".(int)$value;
									}
									break;
								case 'quantity'://$mappingData['CSV2DBOptions'][$firstLineData[$key]]
									$value = importConv2Int($value);
									if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									{
										// FOR PRODUCT ONLY
										if (!empty($newprod->id) && !isCombination($line) && empty($id_product_attribute))
										{
											$no_scas = true;
											if(SCAS)
											{
												if(empty($productsStockAdvancedTypeArray[$id_shop][$newprod->id]))
												{
													$type_advanced_stock_management = 1;
													if($newprod->advanced_stock_management==1)
													{
														$type_advanced_stock_management = 2;
														if(!StockAvailable::dependsOnStock((int)$newprod->id, $id_shop))
															$type_advanced_stock_management = 3;
													}
													$productsStockAdvancedTypeArray[$id_shop][$newprod->id] = $type_advanced_stock_management;
												}
												else
													$type_advanced_stock_management = $productsStockAdvancedTypeArray[$id_shop][$newprod->id];

												$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));

												if($type_advanced_stock_management==2)
												{
													$no_scas = false;
													if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 &&  $value>=0)
													{
														$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, 0, (int)$id_warehouse);
														if(empty($in_warehouse)) // s'il n'est pas lié à l'entrepot
														{
															$new = new WarehouseProductLocation();
															$new->id_product = (int)$newprod->id;
															$new->id_product_attribute = 0;
															$new->id_warehouse = (int)$id_warehouse;
															$new->save();
														}

														if($value>=0) // add stock
														{
															if(empty($warehousesArray[$id_warehouse]))
															{
																$warehouse = new Warehouse($id_warehouse);
																$warehousesArray[$id_warehouse] = $warehouse;
															}
															else
																$warehouse = $warehousesArray[$id_warehouse];

															// EMPTY ACUTAL STOCK FOR PRODUCT
															try {
																$query = new DbQuery();
																$query->select('SUM(st.physical_quantity) as physical_quantity');
																$query->from('stock', "st");
																$query->where('st.id_product = '.(int)$newprod->id.'');
																$query->where('st.id_warehouse = '.(int)$id_warehouse.'');
																$avanced_quantities = Db::getInstance()->getRow($query);
																if(!empty($avanced_quantities["physical_quantity"]))
																{
																	$stock_manager->removeProduct($newprod->id, 0, $warehouse, $avanced_quantities["physical_quantity"], 4, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));
																}
															} catch (Exception $e) {
																echo _l("You are trying to add quantities to a product with combinations: product ID ".$newprod->id);die();
															}

															// ADD STOCK FOR PRODUCT
															if($value>0)
															{
																$price = $newprod->wholesale_price;

																if(!empty($price) && $price>0)
																{
																	// First convert price to the default currency
																	$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse->id_currency, false);
																	// Convert the new price from default currency to needed currency
																	$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
																}
                                                                else
                                                                {
                                                                    $ws_price = findCSVLineValue("wholesale_price");
                                                                    if(!empty($ws_price))
                                                                    {
                                                                        // First convert price to the default currency
                                                                        $price_converted_to_default_currency = Tools::convertPrice($ws_price, $warehouse->id_currency, false);
                                                                        // Convert the new price from default currency to needed currency
                                                                        $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                                    }
                                                                }

																if ($stock_manager->addProduct((int)$newprod->id, 0, $warehouse, (int)$value, 1, floatval($price), (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1)))
																{
																	StockAvailable::synchronize((int)$newprod->id);
																}
																else
																{
																	if(empty($noWholesalepriceArray[(int)$newprod->id]))
																	{
																		$stats['skipped']++;
																		$stats['no_wholesaleprice']++;
																		$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
																		$noWholesalepriceArray[(int)$newprod->id] = 1;
																	}
																	else
																	{
																		$stats['modified']-=1;
																	}
																	$skip_line_scas = true;
																}
															}
															else
                                                                StockAvailable::synchronize((int)$newprod->id);
														}
														/*elseif(!empty($value) && $value<0) // delete stock
														{
															if(empty($warehousesArray[$id_warehouse]))
															{
																$warehouse = new Warehouse($id_warehouse);
																$warehousesArray[$id_warehouse] = $warehouse;
															}
															else
																$warehouse = $warehousesArray[$id_warehouse];
						          							$value = $value * -1;
															$removed_products = $stock_manager->removeProduct((int)$newprod->id, 0, $warehouse, $value, 2, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));

															if (count($removed_products) > 0)
															{
																StockAvailable::synchronize((int)$newprod->id);
															}
															else
															{
																if(empty($noWholesalepriceArray[(int)$newprod->id]))
																{
																	$stats['skipped']++;
																	$stats['no_wholesaleprice']++;
																	$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
																	$noWholesalepriceArray[(int)$newprod->id] = 1;
																}
																else
																{
																	$stats['modified']-=1;
																}
																$skip_line_scas = true;
															}
														}*/
													}
												}
											}

											if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
											{
												if(empty($newprod->id))
												{
													foreach($id_shop_list AS $id_shop_temp)
														SCI::setQuantity((int)$newprod->id, 0, (int)$value, $id_shop_temp);
												}
												else
													SCI::setQuantity((int)$newprod->id, 0, (int)$value, $id_shop);
											}
										}
									}
									elseif (isCombination($line))
										$combinationValues['quantity']=(int)$value;

									if (isCombinationWithID() && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										$TODO_SET[]="quantity=".(int)$value;
									break;
								case 'add_quantity':
									$value = importConv2Int($value);
									if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									{
										// FOR PRODUCT ONLY
										if (!empty($newprod->id) && !isCombination($line) && empty($id_product_attribute) && !$newprod->hasAttributes())
										{
											$no_scas = true;

											if(SCAS)
											{
												if(empty($productsStockAdvancedTypeArray[$id_shop][$newprod->id]))
												{
													$type_advanced_stock_management = 1;
													if($newprod->advanced_stock_management==1)
													{
														$type_advanced_stock_management = 2;
														if(!StockAvailable::dependsOnStock((int)$newprod->id, $id_shop))
															$type_advanced_stock_management = 3;
													}
													$productsStockAdvancedTypeArray[$id_shop][$newprod->id] = $type_advanced_stock_management;
												}
												else
													$type_advanced_stock_management = $productsStockAdvancedTypeArray[$id_shop][$newprod->id];

												$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));

												if($type_advanced_stock_management==2)
												{
													$no_scas = false;

													if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value))
													{
														$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, 0, (int)$id_warehouse);
														if(empty($in_warehouse)) // s'il n'est pas lié à l'entrepot
														{
															$new = new WarehouseProductLocation();
															$new->id_product = (int)$newprod->id;
															$new->id_product_attribute = 0;
															$new->id_warehouse = (int)$id_warehouse;
															$new->save();
														}

														if(!empty($value) && $value>0) // add stock
														{
															if(empty($warehousesArray[$id_warehouse]))
															{
																$warehouse = new Warehouse($id_warehouse);
																$warehousesArray[$id_warehouse] = $warehouse;
															}
															else
																$warehouse = $warehousesArray[$id_warehouse];

															// ADD STOCK FOR PRODUCT
															$price = $newprod->wholesale_price;

															if(!empty($price) && $price>0)
															{
																// First convert price to the default currency
																$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse->id_currency, false);
																// Convert the new price from default currency to needed currency
																$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
															}
                                                            else
                                                            {
                                                                $ws_price = findCSVLineValue("wholesale_price");
                                                                if(!empty($ws_price))
                                                                {
                                                                    // First convert price to the default currency
                                                                    $price_converted_to_default_currency = Tools::convertPrice($ws_price, $warehouse->id_currency, false);
                                                                    // Convert the new price from default currency to needed currency
                                                                    $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                                }
                                                            }

															if ($stock_manager->addProduct((int)$newprod->id, 0, $warehouse, (int)$value, 1, floatval($price), (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1)))
															{
																StockAvailable::synchronize((int)$newprod->id);
															}
															else
															{
																if(empty($noWholesalepriceArray[(int)$newprod->id]))
																{
																	$stats['skipped']++;
																	$stats['no_wholesaleprice']++;
																	$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
																	$noWholesalepriceArray[(int)$newprod->id] = 1;
																}
																else
																{
																	$stats['modified']-=1;
																}
																$skip_line_scas = true;
															}
														}
													}
												}
											}

											if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
											{
												if(empty($newprod->id))
												{
													foreach($id_shop_list AS $id_shop_temp)
													{
														$id_stock_available = StockAvailable::getStockAvailableIdByProductId((int)$newprod->id, 0, (int)$id_shop_temp);

														if ($id_stock_available)
															SCI::updateQuantity((int)$newprod->id, 0, (int)$value, (int)$id_shop_temp);
														else
															SCI::setQuantity((int)$newprod->id, 0, (int)$value, $id_shop_temp);
													}
												}
												else
												{
													$id_stock_available = StockAvailable::getStockAvailableIdByProductId((int)$newprod->id, 0, (int)$id_shop);

													if ($id_stock_available)
														SCI::updateQuantity((int)$newprod->id, 0, (int)$value, (int)$id_shop);
													else
														SCI::setQuantity((int)$newprod->id, 0, (int)$value, $id_shop);
												}
											}
										}
									}
									elseif (isCombination($line) && !isCombinationWithID())// si nouvelle déclinaison, on met directement dans quantité
										$combinationValues['quantity']=(int)$value;

									if (isCombinationWithID() && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										$TODO_SET[]="quantity=quantity+".(int)$value;
									break;
								case 'remove_quantity':
									$value = importConv2Int($value);
									if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
									{
										// FOR PRODUCT ONLY
										if (!empty($newprod->id) && !isCombination($line) && empty($id_product_attribute) && !$newprod->hasAttributes())
										{
											$no_scas = true;

											if(SCAS)
											{
												if(empty($productsStockAdvancedTypeArray[$id_shop][$newprod->id]))
												{
													$type_advanced_stock_management = 1;
													if($newprod->advanced_stock_management==1)
													{
														$type_advanced_stock_management = 2;
														if(!StockAvailable::dependsOnStock((int)$newprod->id, $id_shop))
															$type_advanced_stock_management = 3;
													}
													$productsStockAdvancedTypeArray[$id_shop][$newprod->id] = $type_advanced_stock_management;
												}
												else
													$type_advanced_stock_management = $productsStockAdvancedTypeArray[$id_shop][$newprod->id];

												$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));

												if($type_advanced_stock_management==2)
												{
													$no_scas = false;

													if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value))
													{
														$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, 0, (int)$id_warehouse);
														if(empty($in_warehouse)) // s'il n'est pas lié à l'entrepot
														{
															$new = new WarehouseProductLocation();
															$new->id_product = (int)$newprod->id;
															$new->id_product_attribute = 0;
															$new->id_warehouse = (int)$id_warehouse;
															$new->save();
														}

														if(!empty($value) && $value>0) // add stock
														{
															if(empty($warehousesArray[$id_warehouse]))
															{
																$warehouse = new Warehouse($id_warehouse);
																$warehousesArray[$id_warehouse] = $warehouse;
															}
															else
																$warehouse = $warehousesArray[$id_warehouse];

															$removed_products = $stock_manager->removeProduct((int)$newprod->id, 0, $warehouse, $value, 2, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));

															if (count($removed_products) > 0)
															{
																StockAvailable::synchronize((int)$newprod->id);
															}
															else
															{
																if(empty($noWholesalepriceArray[(int)$newprod->id]))
																{
																	$stats['skipped']++;
																	$stats['no_wholesaleprice']++;
																	$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
																	$noWholesalepriceArray[(int)$newprod->id] = 1;
																}
																else
																{
																	$stats['modified']-=1;
																}
																$skip_line_scas = true;
															}
														}
													}
												}
											}

											if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
											{
												if(empty($newprod->id))
												{
													foreach($id_shop_list AS $id_shop_temp)
														SCI::updateQuantity((int)$newprod->id, 0, (int)($value*-1), $id_shop_temp);
												}
												else
													SCI::updateQuantity((int)$newprod->id, 0, (int)($value*-1), $id_shop);
											}
										}
									}
									elseif (isCombination($line) && !isCombinationWithID())// si nouvelle déclinaison, on met directement dans quantité
										$combinationValues['quantity']=0;

									if (isCombinationWithID() && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
										$TODO_SET[]="quantity=quantity-".(int)$value;
									break;
								case 'reference':
									if (isCombination($line))
										$combinationValues['reference']=$value;
									if (isCombinationWithID())
										$TODO_SET[]="reference='".psql($value)."'";
									break;
								case 'supplier_reference':
									if (isCombination($line))
										$combinationValues['supplier_reference']=$value;
									if (isCombinationWithID())
									{
										$id_supplier = (str_replace("suppref_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
										if (
												(version_compare(_PS_VERSION_, '1.5.0.0', '<'))
												||
												( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
											)
										{
											$TODO_SET[]="supplier_reference='".psql($value)."'";
										}
										elseif( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && $id_supplier!="product")
										{
											$id_supplier = intval(str_replace("supp_","",$id_supplier));
											if(!empty($id_supplier))
											{
												$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, (int)$id_product_attribute, (int)$id_supplier);
												if ($id_product_supplier)
													$product_supplier = new ProductSupplier((int)$id_product_supplier);
												else
												{
													$product_supplier = new ProductSupplier();
													$product_supplier->id_product = (int)$newprod->id;
													$product_supplier->id_product_attribute = $id_product_attribute;
													$product_supplier->id_supplier = (int)$id_supplier;
												}
												$product_supplier->product_supplier_reference = psql($value);
												$product_supplier->save();
											}
										}
									}
									// only for product without combinations
									if (!isCombination($line) && !isCombinationWithID() && version_compare(_PS_VERSION_, '1.5.0.0', '>=')&& $id_product_attribute==0)
									{
										$id_supplier = (str_replace("suppref_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
										if($id_supplier!="product")
										{
											$id_supplier = intval(str_replace("supp_","",$id_supplier));
											if(!empty($id_supplier))
											{
												$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, 0, (int)$id_supplier);
												if ($id_product_supplier)
													$product_supplier = new ProductSupplier((int)$id_product_supplier);
												else
												{
													$product_supplier = new ProductSupplier();
													$product_supplier->id_product = (int)$newprod->id;
													$product_supplier->id_product_attribute = 0;
													$product_supplier->id_supplier = (int)$id_supplier;
												}
												$product_supplier->product_supplier_reference = psql($value);
												$product_supplier->save();
											}
										}
									}
									break;
								case 'name':
									if (isCombination($line) || isCombinationWithID())
									{
										escapeCharForPS($value);
										$sql="UPDATE "._DB_PREFIX_."product_lang SET name='".psql($value,true)."' WHERE id_product=".$newprod->id." AND id_lang=".intval($id_lang);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && is_array($id_shop_list) && count($id_shop_list))
											$sql.=" AND id_shop IN (".join(',',$id_shop_list).")";
										if ($scdebug) echo $sql;
										Db::getInstance()->Execute($sql);
									}
									break;
								case 'description_short':
									if (isCombination($line) || isCombinationWithID())
									{
										escapeCharForPS($value,true);
										$sql="UPDATE "._DB_PREFIX_."product_lang SET description_short='".psql($value,true)."' WHERE id_product=".$newprod->id." AND id_lang=".intval($id_lang);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && is_array($id_shop_list) && count($id_shop_list))
											$sql.=" AND id_shop IN (".join(',',$id_shop_list).")";
										if ($scdebug) echo $sql;
										Db::getInstance()->Execute($sql);
									}
									break;
								case 'description':
									if (isCombination($line) || isCombinationWithID())
									{
										escapeCharForPS($value,true);
										$sql="UPDATE "._DB_PREFIX_."product_lang SET description='".psql($value,true)."' WHERE id_product=".$newprod->id." AND id_lang=".intval($id_lang);
										if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && is_array($id_shop_list) && count($id_shop_list))
											$sql.=" AND id_shop IN (".join(',',$id_shop_list).")";
										if ($scdebug) echo $sql;
										Db::getInstance()->Execute($sql);
									}
									break;
								case 'ActionDeleteTags':
									if ($value!='' && $value!='0' && $newprod->id)
										$newprod->deleteTags();
									break;
								case 'ActionDeleteCarriers':
									if ($value!='' && $value!='0' && $newprod->id)
									{
										$sql = "DELETE FROM `"._DB_PREFIX_."product_carrier` WHERE id_product = '".intval($newprod->id)."' AND id_shop='".intval($id_shop)."'";
										Db::getInstance()->execute($sql);
									}
									break;
                                case 'ActionDeletesWarehouses':
                                    if ($value!='' && $value!='0' && $newprod->id)
                                    {
                                        $sql = "DELETE FROM `"._DB_PREFIX_."warehouse_product_location` WHERE id_product = '".intval($newprod->id)."' ".(!empty($id_product_attribute)?" AND id_product_attribute = '".intval($id_product_attribute)."' ":'');
                                        Db::getInstance()->execute($sql);
                                    }
                                    break;
                                case 'ActionDeletesSuppliers':
                                    if ($value!='' && $value!='0' && $newprod->id)
                                    {
                                        $sql = "DELETE FROM `"._DB_PREFIX_."product_supplier` WHERE id_product = '".intval($newprod->id)."' ".(!empty($id_product_attribute)?" AND id_product_attribute = '".intval($id_product_attribute)."' ":'');
                                        Db::getInstance()->execute($sql);
                                        $sql = "UPDATE `"._DB_PREFIX_."product` SET id_supplier = 0 WHERE id_product = '".intval($newprod->id)."'";
                                        Db::getInstance()->execute($sql);
                                    }
                                    break;
								case 'tags':
									if (count($productsWithTagUpdatedList)==0 || (!sc_array_key_exists($id_lang,$productsWithTagUpdatedList) || !sc_in_array($newprod->id,$productsWithTagUpdatedList[$id_lang],"catWinImportProcess_productsWithTagUpdatedList".$id_lang)))
									{
										$value=trim(trim($value),$importConfig[$TODOfilename]['valuesep']);
										if ($value!='')
										{
											//Tag::addTags($id_lang, $newprod->id, join(',',explode($importConfig[$TODOfilename]['valuesep'],$value)));
											$tag_list = explode($importConfig[$TODOfilename]['valuesep'],$value);

											if (!is_array($tag_list))
												$tag_list = array_filter(array_unique(array_map('trim', preg_split('#\\'.$separator.'#', $tag_list, null, PREG_SPLIT_NO_EMPTY))));

											$list = array();
											foreach ($tag_list as $tag)
											{
												$id_tag = 0;
												if (Validate::isGenericName($tag))
												{
													$resTag = Db::getInstance()->executeS('SELECT id_tag
													FROM `'._DB_PREFIX_.'tag`
													WHERE name = "'.pSQL($tag).'"
														AND id_lang = "'.(int)$id_lang.'"');
													if(empty($resTag[0]["id_tag"]))
													{
														$tag_obj = new Tag(null, trim($tag), (int)$id_lang);

														/* Tag does not exist in database */
														if (!Validate::isLoadedObject($tag_obj))
														{
															$tag_obj->name = trim($tag);
															$tag_obj->id_lang = (int)$id_lang;
															$tag_obj->add();
														}
														if (!in_array($tag_obj->id, $list))
															$list[] = $tag_obj->id;
													}
													else
													{
														$id_tag = $resTag[0]["id_tag"];
													}
												}

												if(!empty($id_tag))
												{
													$hasTag = Db::getInstance()->executeS('SELECT id_tag
														FROM `'._DB_PREFIX_.'product_tag`
														WHERE id_product = "'.(int)$newprod->id.'"
															AND id_tag = "'.(int)$id_tag.'"');
													if(empty($hasTag[0]["id_tag"]))
														if (!in_array($id_tag, $list))
															$list[] = $id_tag;
												}
											}
											$data = '';
											foreach ($list as $tag)
												$data .= '('.(int)$tag.','.(int)$newprod->id.'),';
											$data = rtrim($data, ',');
											if(!empty($data))
											{
												Db::getInstance()->execute('
												INSERT INTO `'._DB_PREFIX_.'product_tag` (`id_tag`, `id_product`)
												VALUES '.$data);
											}

											$productsWithTagUpdatedList[$id_lang][]=$newprod->id;
										}
									}
									break;
								case 'ActionDeleteSpecificPrice':
									if ($value!='' && $value!='0' && $newprod->id)
										Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'specific_price` WHERE `id_product` = '.(int)$newprod->id);
									break;
								case 'ActionDeleteAllCombinations':
									if (getBoolean($value) && $newprod->id && is_null($cache_id_shop[$id_product.'-'.$id_product_attribute]))
									{
										$sql = "SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product=".(int)$newprod->id;
										$res=Db::getInstance()->ExecuteS($sql);
										$attributes=array();
										foreach($res as $v)
											$attributes[]=$v['id_product_attribute'];
										if (count($attributes))
										{
											Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute` WHERE `id_product` = '.(int)$newprod->id);
											Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_combination` WHERE `id_product_attribute` IN ('.join(',',$attributes).')');
											Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_image` WHERE `id_product_attribute` IN ('.join(',',$attributes).')');
											Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product` SET '.(version_compare(_PS_VERSION_, '1.4.0.0', '>=')?'cache_default_attribute = 0, ':'').'quantity = 0 WHERE `id_product` = '.(int)$newprod->id);
											if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<'))
											{
												Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock_mvt` WHERE `id_product_attribute` > 0 AND `id_product_attribute` IN ('.join(',',$attributes).')');
											}
											if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
											{
												$sql = "SELECT id_stock FROM "._DB_PREFIX_."stock WHERE id_product=".(int)$newprod->id;
												$res=Db::getInstance()->ExecuteS($sql);
												foreach($res as $v)
													Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock_mvt` WHERE `id_stock` = "'.(int)$v["id_stock"].'"');

												Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_shop` WHERE `id_product_attribute` IN ('.join(',',$attributes).')');
												Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock_available` WHERE `id_product_attribute` > 0 AND `id_product_attribute` IN ('.join(',',$attributes).')');
												Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'stock` WHERE `id_product_attribute` > 0 AND `id_product_attribute` IN ('.join(',',$attributes).')');
												Db::getInstance()->Execute('DELETE FROM `'._DB_PREFIX_.'warehouse_product_location` WHERE `id_product_attribute` > 0 AND `id_product_attribute` IN ('.join(',',$attributes).')');

												Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'stock_available` SET quantity = 0 WHERE `id_product` = '.(int)$newprod->id);
												Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product_shop` SET cache_default_attribute = 0 WHERE `id_product` = '.(int)$newprod->id);
											}
										}
									}
									break;
								case 'specific_price_from_quantity':
									if ($value != '')
									{
										if ($newprod->id)
										{
											//$specific_price_id_shop=intval(findCSVLineValue('specific_price_id_shop')); useless, we use id_shop_list
											$specific_price_id_currency=intval(findCSVLineValue('specific_price_id_currency'));
											$specific_price_id_country=intval(findCSVLineValue('specific_price_id_country'));
											$specific_price_id_group=intval(findCSVLineValue('specific_price_id_group'));
											if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && findCSVLineValue('specific_price_price')=='')
											{
												$specific_price_price = -1;
											}else{
												$specific_price_price=importConv2Float(findCSVLineValue('specific_price_price'));
											}
											$specific_price_from_quantity=(_s("APP_COMPAT_MODULE_PPE")?floatval($value):(int)$value);
											$specific_price_reduction=importConv2Float(findCSVLineValue('specific_price_reduction'));
											$specific_price_reduction_type=psql(findCSVLineValue('specific_price_reduction_type'));
											$specific_price_from=psql(findCSVLineValue('specific_price_from'));
											$specific_price_to=psql(findCSVLineValue('specific_price_to'));
											$specificPrice = new SpecificPrice();
											$specificPrice->id_product = $newprod->id;
											$specificPrice->id_product_attribute = (int)$id_product_attribute;
											$specificPrice->id_shop = (int)($id_shop);
											$specificPrice->id_currency = (int)($specific_price_id_currency);
											$specificPrice->id_customer = 0;
											$specificPrice->id_country = (int)($specific_price_id_country);
											$specificPrice->id_group = (int)($specific_price_id_group);
											$specificPrice->price = (float)($specific_price_price);
											$specificPrice->from_quantity = (_s("APP_COMPAT_MODULE_PPE")?floatval($specific_price_from_quantity):(int)$specific_price_from_quantity);
											$specificPrice->reduction = (float)($specific_price_reduction_type == 'percentage' || $specific_price_reduction_type == 'percent' || $specific_price_reduction_type == 1 ? (floatval($specific_price_reduction) / 100) : $specific_price_reduction);
											$specificPrice->reduction_type = psql($specific_price_reduction_type=='percentage' || $specific_price_reduction_type == 'percent' || $specific_price_reduction_type==1 ? 'percentage' : 'amount');
											$specificPrice->from = !$specific_price_from ? '0000-00-00 00:00:00' : str_replace('/','-',$specific_price_from);
											$specificPrice->to = !$specific_price_to ? '0000-00-00 00:00:00' : str_replace('/','-',$specific_price_to);
											if(version_compare(_PS_VERSION_, '1.6.0.11', '>='))
											{
												$specific_price_reduction_tax=intval(findCSVLineValue('specific_price_reduction_tax'));
												$specificPrice->reduction_tax = $specific_price_reduction_tax;
											}
											$specificPrice->add();
										}else{
											echo 'The product must be created before using specific price import.<br/>';
										}
									}
									break;
								case 'customization_field_type':
									$cfrequired=intval(findCSVLineValue('customization_field_required'));
                                    if((!empty($value) && $value=="1") || $value==="0")
                                    {
                                        $value = intval($value);
                                        if ($newprod->id) {
                                            if (!sc_in_array($cfrequired, array(0, 1), "catWinImportProcess_cfrequired")) $cfrequired = 0;
                                            if (!sc_in_array($value, array(0, 1), "catWinImportProcess_cfvalue")) $cfvalue = 0;
                                            Db::getInstance()->Execute("INSERT INTO `" . _DB_PREFIX_ . "customization_field` (id_product,type,required) VALUES (" . (int)$newprod->id . "," . (int)$value . "," . (int)$cfrequired . ")");
                                            $id_customization_field = Db::getInstance()->Insert_ID();
                                            foreach ($languages AS $lang) {
                                                $name = findCSVLineValueByLang('customization_field_name', (int)$lang['id_lang']);
                                                $sql = "INSERT INTO `" . _DB_PREFIX_ . "customization_field_lang` (id_customization_field,id_lang,name) VALUES (" . (int)$id_customization_field . "," . (int)$lang['id_lang'] . ",'" . psql($name) . "')";
                                                Db::getInstance()->Execute($sql);
                                            }
                                            Db::getInstance()->Execute("UPDATE `" . _DB_PREFIX_ . "product` p SET customizable=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product),
																																	text_fields=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1),
																																	uploadable_files=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)
																																	WHERE p.id_product=" . (int)$newprod->id);
                                            if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
                                                Db::getInstance()->Execute("UPDATE `" . _DB_PREFIX_ . "product_shop` p SET customizable=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product),
																																	text_fields=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=1),
																																	uploadable_files=(SELECT count(id_customization_field) FROM `" . _DB_PREFIX_ . "customization_field` cf WHERE cf.id_product=p.id_product AND cf.type=0)
																																	WHERE p.id_product=" . (int)$newprod->id." AND p.id_shop=".(int)$id_shop);
                                        } else {
                                            echo 'The product must be created before using customization field import.<br/>';
                                        }
                                    }
									break;
								case 'ActionDeleteAttachments':
									if ($value!='' && $value!='0' && $newprod->id)
									{
										$attachments = Db::getInstance()->ExecuteS('SELECT id_attachment FROM `'._DB_PREFIX_.'product_attachment` WHERE `id_product` = '.(int)$newprod->id);
										foreach ($attachments AS $attachment)
										{
											$attachmentObj = new Attachment(intval($attachment['id_attachment']));
											if (Validate::isLoadedObject($attachmentObj))
												$attachmentObj->delete();
										}
									}
									break;
								case 'ActionDissociateAttachments':
									if ($value!='' && $value!='0' && $newprod->id)
									{
										Db::getInstance()->ExecuteS('DELETE FROM `'._DB_PREFIX_.'product_attachment` WHERE `id_product` = '.(int)$newprod->id);
									}
									break;
								case 'ActionDeleteAllFeatures':
									if ($value!='' && $value!='0' && $newprod->id)
									{
										Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'feature_product` WHERE `id_product` = '.(int)$newprod->id);
									}
									break;
								case 'ActionDeleteImages':
									if ($value!='' && $value!='0' && $newprod->id)
									{
										if (SCMS)
										{
											$result = Db::getInstance()->executeS('
												SELECT `id_image`
												FROM `'._DB_PREFIX_.'image`
												WHERE `id_product` = '.(int)$newprod->id
											);
											if ($result)
											{
												foreach ($result as $row)
												{
													$image = new Image($row['id_image']);
													$image->deleteImage();
													Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'product_attribute_image` WHERE `id_image` = '.(int)$row['id_image']);
													Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'image_shop` WHERE `id_image` = '.(int)$row['id_image']);
													Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'image_lang` WHERE `id_image` = '.(int)$row['id_image']);
													Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'image` WHERE `id_image` = '.(int)$row['id_image']);
												}
												$imagesListFromDB = array();
											}
										}else{
											$newprod->deleteImages();
											$imagesListFromDB = array();
										}
									}
									break;
                                case 'image_default_id' :
                                    if(!empty($value) && $newprod->id)
                                    {
                                        $img_default = new Image((int)$value);
                                        if(!empty($img_default->id_product) && $img_default->id_product==$newprod->id)
                                        {
                                            Db::getInstance()->execute('
                                            UPDATE `'._DB_PREFIX_.'image`
                                            SET `cover` = 0
                                            WHERE `id_product` = '.(int)$newprod->id
                                            );
                                            if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
                                                Db::getInstance()->execute('
                                                UPDATE `'._DB_PREFIX_.'image` i, `'._DB_PREFIX_.'image_shop` image_shop
                                                SET image_shop.`cover` = 0
                                                WHERE image_shop.id_shop = "'.intval($id_shop).'" AND image_shop.id_image = i.id_image AND i.`id_product` = '.(int)$newprod->id);

                                            Db::getInstance()->execute('
                                            UPDATE `'._DB_PREFIX_.'image`
                                            SET `cover` = 1
                                            WHERE `id_image` = '.(int)$value
                                            );
                                            if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
                                                Db::getInstance()->execute('
                                                UPDATE `'._DB_PREFIX_.'image_shop` image_shop
                                                SET `cover` = 1
                                                WHERE id_shop = "'.intval($id_shop).'" AND id_image = "'.intval($value).'"');

                                        }
                                    }
                                    break;
								case 'image_id':
									if (empty($image_id_array))
									{
										findAllCSVLineValue('image_id', $image_id_array);
									}
									if(!empty($value))
									{
										$inProduct = Db::getInstance()->executeS('SELECT *
											FROM `'._DB_PREFIX_.'image`
											WHERE `id_image` = '.(int)$value.' AND id_product = "'.(int)$newprod->id.'"');
										if(!empty($inProduct[0]["id_product"]))
										{
											if (SCMS)
											{
												$not_exist = Db::getInstance()->executeS('SELECT *
												FROM `' . _DB_PREFIX_ . 'image_shop`
												WHERE `id_image` = ' . (int)$value . ' AND id_shop = "' . (int)$id_shop . '"');
												if (empty($not_exist))
												{
													Db::getInstance()->executeS('INSERT INTO `' . _DB_PREFIX_ . 'image_shop` (id_image,id_shop)
													VALUES ("' . (int)$value . '","' . (int)$id_shop . '")');
												}
											}
											if (isCombination($line) || isCombinationWithID())
											{
												$not_exist = Db::getInstance()->executeS('SELECT *
												FROM `' . _DB_PREFIX_ . 'product_attribute_image`
												WHERE `id_image` = ' . (int)$value . ' AND id_product_attribute = "' . (int)$id_product_attribute . '"');
												if (empty($not_exist))
												{
													Db::getInstance()->executeS('INSERT INTO `' . _DB_PREFIX_ . 'product_attribute_image` (id_image,id_product_attribute)
													VALUES ("' . (int)$value . '","' . (int)$id_product_attribute . '")');
												}
											}
										}
									}
									break;
								case 'image_legend':
									if (empty($image_legend_array))
									{
										findAllCSVLineValue('image_legend', $image_legend_array);
									}
									break;
								case 'imageURL':case 'imageURL1':case 'imageURL2':case 'imageURL3':case 'imageURL4':case 'imageURL5':case 'imageURL6':case 'imageURL7':case 'imageURL8':case 'imageURL9':case 'imageURL10':
									if ($value!='')
									{
										$values=explode($importConfig[$TODOfilename]['valuesep'],$value);
										$languages = Language::getLanguages();
										foreach($values AS $val)
										{
											$imagefilename=findImageFileName($val);
											$imagefilenameshort=substr($imagefilename,strlen(SC_CSV_IMPORT_DIR.'images/'),strlen($imagefilename));

											if (	!empty($val)
												&& (_s('CAT_IMPORT_FORCE_IMG_DOWNLOAD') || !sc_array_key_exists($val,$imageList))
												&& (!_s('CAT_PROD_IMG_SAVE_FILENAME')
														|| (_s('CAT_PROD_IMG_SAVE_FILENAME') && $imagefilename!==false && !sc_array_key_exists($newprod->id.'_|_'.substr($imagefilename,strlen(SC_CSV_IMPORT_DIR.'images/'),strlen($imagefilename)),$imagesListFromDB))
														|| (_s('CAT_PROD_IMG_SAVE_FILENAME') && $imagefilename===false && !sc_array_key_exists($newprod->id.'_|_'.$val,$imagesListFromDB))
													 )
													)
											{
												$image = new Image();
												$image->id_product = intval($newprod->id);
												$image->position = Image::getHighestPosition($newprod->id) + 1;
												$productHasImages = (bool)Image::getImages($defaultLanguageId, intval($newprod->id));
												$image->cover = (!$productHasImages) ? true : false;
												if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
													$image->id_shop_list=$id_shop_list;
                                                if(version_compare(_PS_VERSION_, '1.5.0.0', '<') || version_compare(_PS_VERSION_, '1.5.6.1', '>=') ){
//													$image->legend = createMultiLangField(str_replace('#','',Tools::substr($newprod->name[$defaultLanguageId],0,128)));
													$res = array();
													foreach ($languages AS $lang){
														$res[$lang['id_lang']] = $newprod->name[$lang['id_lang']];
													}
													$image->legend = $res;
												}
                                                if ($image->add())
												{
													$flag=false;
													$imageList[$val]=$image->id;
													if (strpos($val,'http')===false)
													{
														if ($imagefilename)
														{
															if (!copyImg($newprod->id, $image->id, $imagefilename))
															{
																echo _l('Impossible to copy image:').' '.$val.'<br/>';
																$image->delete();
															}else{
																$imagesListFromDB[$newprod->id.'_|_'.$imagefilenameshort]=$image->id;
																if (_s('CAT_PROD_IMG_SAVE_FILENAME'))
																{
																	$flag=true;
																	$sql="UPDATE "._DB_PREFIX_."image SET sc_path='".psql($imagefilenameshort)."' WHERE id_image = ".intval($image->id);
																	Db::getInstance()->Execute($sql);
																}
																SCI::addToShops('image', array((int)$image->id));
															}
														}else{
															echo _l('Image not found:').' '.$val.'<br/>';
															$image->delete();
														}
													}else{
														if (!copyImg($newprod->id, $image->id, $val))
														{
															echo _l('Impossible to copy image:').' '.$val.'<br/>';
															$image->delete();
														}else{
															$imagesListFromDB[$newprod->id.'_|_'.$val]=$image->id;
															if (_s('CAT_PROD_IMG_SAVE_FILENAME'))
															{
																$flag=true;
																$sql="UPDATE "._DB_PREFIX_."image SET sc_path='".psql($val)."' WHERE id_image = ".intval($image->id);
																Db::getInstance()->Execute($sql);
															}
															SCI::addToShops('image', array((int)$image->id));
														}
													}
												}
											}
											if ($val!='' && sc_array_key_exists($newprod->id.'_|_'.$val,$imagesListFromDB))
											{
												$imagesListFromCSVLine[]=$imagesListFromDB[$newprod->id.'_|_'.$val];
											}
										}
									}
									break;
								default:
									sc_ext::readImportCSVConfigXML('importProcessCombination');
							}
							if ($value!='' && isCombination())
							{
								if ($mappingData['CSV2DB'][$firstLineData[$key]]=='attribute')
								{
									$complexValue=getIDAttributeGroupByCSVColumnName($firstLineData[$key]);
									if ($complexValue)
									{
										$complexValue.='_|_'.$value;
										$attributesList[][$attributeValues[$complexValue]]=$attributeValues[$complexValue];
										$combinationUsed=true;
									}
								}
								if ($mappingData['CSV2DB'][$firstLineData[$key]]=='attribute_multiple')
								{
									$temp=explode($importConfig[$TODOfilename]['valuesep'],$value);
									$k=count($attributesList);
									$complexValue=getIDAttributeGroupByCSVColumnName($firstLineData[$key]).'_|_';
									foreach($temp AS $t)
									{
										if ($t!='')
										{
											$csvVal=$complexValue.trim($t);
											$attributesList[$k][$attributeValues[$csvVal]]=$attributeValues[$csvVal];
											$combinationUsed=true;
										}
									}
								}
							}

							if (_s('APP_DEBUG_CATALOG_IMPORT'))
							{
								$time_end = microtime(true);
								$time = $time_end - $time_start;
								echo "<br/><br/>"._l('After')." ".$switchObject." column : $time "._l('seconds');
							}
						}



          		}// foreach cols

				$TODO[]="UPDATE "._DB_PREFIX_."product SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id)."";
				if(SCMS)
					$TODO[]="UPDATE "._DB_PREFIX_."product_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).")";

				foreach($TODO AS $sql)
					Db::getInstance()->Execute($sql);

				$todoSet_to_insert = implode(', ', $TODO_SET);
				$todoSetShop_to_insert = implode(', ', $TODO_SET_SHOP);
				if(!empty($TODO_SET)) {
					$sql = "UPDATE " . _DB_PREFIX_ . "product_attribute SET " . $todoSet_to_insert . " WHERE id_product_attribute=" . (int)$id_product_attribute . ";";
					if(!empty($TODO_SET_SHOP)) {
						$sql .= "UPDATE " . _DB_PREFIX_ . "product_attribute_shop SET " . $todoSetShop_to_insert . " WHERE id_product_attribute=" . (int)$id_product_attribute . " AND id_shop IN(" . psql(join(',',
							$id_shop_list)) . ")";
					}
					Db::getInstance()->Execute($sql);
				}

          		if (_s('APP_DEBUG_CATALOG_IMPORT'))
          		{
          			$time_end = microtime(true);
          			$time = $time_end - $time_start;
          			echo "<br/><br/>"._l('Fields processins after product creation')." : $time "._l('seconds');
          		}
          		if(!empty($image_id_array))
          		{
          			$TODO_image = array();
          			foreach($image_id_array as $actual_num=>$image_id)
          			{
          				$actual_id = $image_id["value"];
						$sql="SELECT * FROM "._DB_PREFIX_."image_lang WHERE id_image=".(int)$actual_id;
						$legends = Db::getInstance()->executeS($sql);
						$legendsByLang = array();
						foreach($legends as $legend){
							$legendsByLang[$legend['id_lang']]['id_image'] = $legend['id_image'];
						}

          				if(!empty($image_legend_array[$actual_num]))
          				{
							$legend = $image_legend_array[$actual_num];
							$actual_legend = $legend["value"];
							$actual_legend_lang = (int)$getIDlangByISO[$legend["option"]];

							if(!empty($actual_legend_lang))
							{
								if(!empty($legendsByLang[$actual_legend_lang]['id_image']))
								{
									$TODO_image[]="UPDATE "._DB_PREFIX_."image_lang SET legend='".psql($actual_legend)."' WHERE id_image='".intval($actual_id)."' AND id_lang = ".intval($actual_legend_lang);
								}
								else
								{
									$TODO_image[]="INSERT INTO "._DB_PREFIX_."image_lang (id_image, id_lang, legend)
													VALUES ('".intval($actual_id)."','".intval($actual_legend_lang)."','".psql($actual_legend)."')";
								}
							}
          				}
						sc_ext::readImportCSVConfigXML('importProcessImageUpdate');
          			}
          			//print_r($TODO_image);
          			if(!empty($TODO_image))
						foreach($TODO_image AS $sql)
							Db::getInstance()->Execute($sql);
          		}

				if($skip_line_scas)
					continue;

				if(!empty($id_product_attribute))
					$combinations_ids = array($id_product_attribute);
				else
					$combinations_ids = array($id_product_attribute);

					// combinations management
					if (isCombination() && $combinationUsed)
					{
						// $options[id_group][id_attr]
						$combinations = array_values(createCombinations($attributesList));

/*
echo '<br/>attributeValues<br/>';
print_r($attributeValues);
echo '<br/>combinationValues<br/>';
print_r($combinationValues);
*/
/*
echo '<br/>combinations<br/>';
print_r($combinations);
*/
						$values = array_values(array_map('addAttribute', $combinations)); // combinationValues ??
/*
echo '<br/>values<br/>';
var_dump($combinations);

exit;*/
// TODO edit values REF - MAIS on perd l'association images > déclinaisons car on cherche par référence...
						if (_s('CAT_IMPORT_CREATE_REFERENCE_1'))
						{
							foreach($values AS $k => $v)
							{ // le groupe attr couleur doit être créé avant taille
								$values[$k]['reference']=$values[$k]['reference'].'_'.$attributeValuesNames[$combinations[$k][0]];
							}
						}
						if (version_compare(_PS_VERSION_,'1.5.0.0','>='))
						{
							$combinations_ids = array();
							foreach($values AS $k => $v)
							{
//function addAttribute($price, $weight, $unit_impact, $ecotax, $id_images, $reference, $ean13,
//								 $default, $location = null, $upc = null, $minimal_quantity = 1, array $id_shop_list = array())
                                if (version_compare(_PS_VERSION_,'1.7.0.0','>='))
                                {
                                    $id_product_attribute = $newprod->addAttribute(
                                        importConv2Float(round(importConv2Float($v['price']),6)),
                                        $v['weight'],
                                        0, // unit_impact
                                        importConv2Float($v['ecotax']),
                                        0, // id_images
                                        $v['reference'],
                                        $v['ean13'],
                                        $v['default_on'],
                                        $v['location'],
                                        $v['upc'],
                                        $v['minimal_quantity'],
                                        $id_shop_list,
                                        null,
                                        0,
                                        $v['isbn']
                                    );
                                }
                                else
                                {
                                    $id_product_attribute = $newprod->addAttribute(
                                        importConv2Float(round(importConv2Float($v['price']),6)),
                                        $v['weight'],
                                        0, // unit_impact
                                        importConv2Float($v['ecotax']),
                                        0, // id_images
                                        $v['reference'],
                                        $v['ean13'],
                                        $v['default_on'],
                                        $v['location'],
                                        $v['upc'],
                                        $v['minimal_quantity'],
                                        $id_shop_list
                                    );
                                }

                                // if available_date set in mapping, we update
                                $sqlDA = "UPDATE "._DB_PREFIX_."product_attribute SET available_date = '".pSQL($combinationValues['available_date'])."' WHERE id_product_attribute = ".(int)$id_product_attribute.";";
                                if(version_compare(_PS_VERSION_,'1.5.0.0','>='))
                                    $sqlDA .= "UPDATE "._DB_PREFIX_."product_attribute_shop SET available_date = '".pSQL($combinationValues['available_date'])."' WHERE id_product_attribute = ".(int)$id_product_attribute." AND id_shop IN (".psql(join(',',$id_shop_list)).");";
								Db::getInstance()->execute($sqlDA);
								
								$combinations_ids[] = $id_product_attribute;

								if(!fieldInMapping("quantity"))
								{
									$qty = $v['quantity'];
									if($qty==0)
										$qty = _s("CAT_PROD_COMBI_CREA_QTY");
									foreach($id_shop_list AS $id_shop)
									{
										SCI::setQuantity($newprod->id, $id_product_attribute, $qty,$id_shop);
									}
								}
								$combination = new Combination((int)$id_product_attribute);
								$combination->id_product = $newprod->id;
								$combination->setAttributes($combinations[$k]);
								//$combination->wholesale_price=$v['wholesale_price'];
								$combination->minimal_quantity=max(1,(int)$combination->minimal_quantity);
								$combination->id_shop_list=$id_shop_list;
								if(findCSVLineValue('attribute_default_on')==1)
								{
									$sql = 'UPDATE '._DB_PREFIX_.'product_attribute SET default_on = NULL
										WHERE id_product='.(int)$newprod->id;
									Db::getInstance()->execute($sql);
									$sql = 'UPDATE '._DB_PREFIX_.'product_attribute_shop SET default_on = NULL
										WHERE id_product='.(int)$newprod->id.' 
										AND id_shop IN ('.implode(',',$id_shop_list).')';
									Db::getInstance()->execute($sql);
								}
								$combination->default_on=(findCSVLineValue('attribute_default_on')==1?1:0);
								$combination->save();
								if (_s('APP_COMPAT_HOOK') && !_s('APP_COMPAT_EBAY'))
									SCI::hookExec('updateProductAttribute', array('product' => $newprod));

								/*if (isset($newprod->id_supplier))
								{
									$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, (int)$id_product_attribute, (int)$newprod->id_supplier);
									if ($id_product_supplier)
										$product_supplier = new ProductSupplier((int)$id_product_supplier);
									else
										$product_supplier = new ProductSupplier();
									$product_supplier->id_product = (int)$newprod->id;
									$product_supplier->id_product_attribute = (int)$id_product_attribute;
									$product_supplier->id_supplier = (int)$newprod->id_supplier;
									$product_supplier->product_supplier_reference = psql($v['supplier_reference']);
									$product_supplier->save();
								}*/
							}
							if(!fieldInMapping('attribute_default_on'))
								$newprod->checkDefaultAttributes();
							// add default combination if not exist
							Db::getInstance()->execute('UPDATE '._DB_PREFIX_.'product_attribute_shop product_attribute_shop, '._DB_PREFIX_.'product_attribute pa
								SET product_attribute_shop.default_on=1
								WHERE product_attribute_shop.id_product_attribute=pa.id_product_attribute AND pa.id_product='.(int)$newprod->id.' AND pa.default_on = 1');
							Db::getInstance()->Execute('UPDATE `'._DB_PREFIX_.'product_shop` SET cache_default_attribute = '.(int)$combination->id.' WHERE `id_product` = '.(int)$newprod->id);

						}else{
							$res = $newprod->addProductAttributeMultiple($values,!$newprod->checkDefaultAttributes());
							$newprod->addAttributeCombinationMultiple($res, $combinations);
							$newprod->checkDefaultAttributes();
						}
//						$newprod->deleteDefaultAttributes();
          }

			if(_s("CAT_APPLY_ALL_CART_RULES"))
				SpecificPriceRule::applyAllRules(array((int)$newprod->id));

          // FOR COMBINATION
          if (version_compare(_PS_VERSION_,'1.5.0.0','>=') && !empty($id_product_attribute))
          {
	          	if(SCAS)
		        {
		          	if(empty($productsStockAdvancedTypeArray[$id_shop][$newprod->id]))
		          	{
		          		$type_advanced_stock_management = 1;
		          		if($newprod->advanced_stock_management==1)
		          		{
		          			$type_advanced_stock_management = 2;
							if(!StockAvailable::dependsOnStock((int)$newprod->id, $id_shop))
		          				$type_advanced_stock_management = 3;
		          		}
		          		$productsStockAdvancedTypeArray[$id_shop][$newprod->id] = $type_advanced_stock_management;
		          	}
		          	else
		          		$type_advanced_stock_management = $productsStockAdvancedTypeArray[$id_shop][$newprod->id];
		        }

		        $TODO="UPDATE "._DB_PREFIX_."product SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id).";";
		        if(SCMS)
		        	$TODO.="UPDATE "._DB_PREFIX_."product_shop SET `date_upd`='".psql(date("Y-m-d H:i:s"))."' WHERE id_product=".intval($newprod->id)." AND id_shop IN (".psql(join(',',$id_shop_list)).");";
		      Db::getInstance()->Execute($TODO);

		        $noWholesalepriceArray = array();
		        $skip_line_scas = false;
	          	foreach($line AS $key => $value)
	          	{
	          		$value=trim($value);
	          		if (sc_array_key_exists($key,$firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinImportProcess_CSVArray"))
	          		{

	          			$switchObject=$mappingData['CSV2DB'][$firstLineData[$key]];
						if(SCAS) {
							$arrayWarehouseOptions=array();
							findAllCSVLineValue('quantity_on_sale',$arrayWarehouseOptions);
							$warehouseOptionCache=array();
							foreach($arrayWarehouseOptions as $warehouseOption) {
								$id_warehouse = intval(str_replace("warehouse_", "", $warehouseOption["option"]));
								$warehouseOptionCache[$id_warehouse] = (int)$warehouseOption["value"];
							}
						}
	          			switch($switchObject)
	          			{
	          				case 'supplier_reference':
	          					$id_supplier = (str_replace("suppref_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
	          					if( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && $id_supplier!="product")
	          					{
	          						$id_supplier = intval(str_replace("supp_","",$id_supplier));
	          						if(!empty($id_supplier))
	          						{
	          							foreach($combinations_ids as $id_product_attribute)
	          							{
		          							$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, $id_product_attribute, (int)$id_supplier);
		          							if ($id_product_supplier)
		          								$product_supplier = new ProductSupplier((int)$id_product_supplier);
		          							else
		          							{
		          								$product_supplier = new ProductSupplier();
		          								$product_supplier->id_product = (int)$newprod->id;
		          								$product_supplier->id_product_attribute = $id_product_attribute;
		          								$product_supplier->id_supplier = (int)$id_supplier;
		          							}
		          							$product_supplier->product_supplier_reference = psql($value);
		          							$product_supplier->save();

											if($newprod->id_supplier==$id_supplier)
											{
												$TODO="UPDATE "._DB_PREFIX_."product_attribute SET supplier_reference='".psql($value)."' WHERE id_product_attribute=".intval($id_product_attribute);
												Db::getInstance()->Execute($TODO);
											}
	          							}
	          						}
	          					}
	          					elseif (!empty($id_supplier) && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
	          					{
									foreach($combinations_ids as $id_product_attribute)
									{
										$TODO="UPDATE "._DB_PREFIX_."product_attribute SET supplier_reference='".psql($value)."' WHERE id_product_attribute=".intval($id_product_attribute);
										Db::getInstance()->Execute($TODO);

										if(!empty($newprod->id_supplier))
										{
											$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, $id_product_attribute, (int)$newprod->id_supplier);
											if ($id_product_supplier)
												$product_supplier = new ProductSupplier((int)$id_product_supplier);
											else
											{
												$product_supplier = new ProductSupplier();
												$product_supplier->id_product = (int)$newprod->id;
												$product_supplier->id_product_attribute = $id_product_attribute;
												$product_supplier->id_supplier = (int)$newprod->id_supplier;
											}
											$product_supplier->product_supplier_reference = psql($value);
											$product_supplier->save();
										}
									}
	          					}
							break;
							case 'supplier_default':
								$id_supplier = (sc_array_key_exists($value,$dataDB_supplierByName)? intval($dataDB_supplierByName[$value]):0);
								if(!empty($id_supplier))
								{
									foreach($combinations_ids as $id_product_attribute)
									{
										$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, (int)$id_product_attribute, (int)$id_supplier);
										if (empty($id_product_supplier) && $newprod->id)
										{
											//create new record
											$product_supplier_entity = new ProductSupplier();
											$product_supplier_entity->id_product = (int)$newprod->id;
											$product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
											$product_supplier_entity->id_supplier = (int)$id_supplier;
											$product_supplier_entity->product_supplier_price_te = 0;
											$product_supplier_entity->id_currency = 0;
											$product_supplier_entity->save();
											$newprod->id_supplier = (int)$id_supplier;
										}
									}
								}
							break;
							case 'supplier':
								$id_supplier = (sc_array_key_exists($value,$dataDB_supplierByName)? intval($dataDB_supplierByName[$value]):0);
								if(!empty($id_supplier))
								{
									foreach($combinations_ids as $id_product_attribute)
									{
										$id_product_supplier = (int)ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, (int)$id_product_attribute, (int)$id_supplier);
										if (!$id_product_supplier && $newprod->id)
										{

											//create new record
											$product_supplier_entity = new ProductSupplier();
											$product_supplier_entity->id_product = (int)$newprod->id;
											$product_supplier_entity->id_product_attribute = (int)$id_product_attribute;
											$product_supplier_entity->id_supplier = (int)$id_supplier;
											$product_supplier_entity->product_supplier_price_te = 0;
											$product_supplier_entity->id_currency = 0;
											$product_supplier_entity->save();
										}
									}
								}
							break;
	          				case 'wholesale_price':
	          					$id_supplier = (str_replace("suppprice_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
	          					if( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && $id_supplier!="product")
	          					{
	          						$id_supplier = intval(str_replace("supp_","",$id_supplier));
	          						if(!empty($id_supplier))
	          						{
	          							foreach($combinations_ids as $id_product_attribute)
	          							{
		          							$id_product_supplier = ProductSupplier::getIdByProductAndSupplier((int)$newprod->id, $id_product_attribute, (int)$id_supplier);
		          							if ($id_product_supplier)
		          								$product_supplier = new ProductSupplier((int)$id_product_supplier);
		          							else
		          							{
		          								$product_supplier = new ProductSupplier();
		          								$product_supplier->id_product = (int)$newprod->id;
		          								$product_supplier->id_product_attribute = $id_product_attribute;
		          								$product_supplier->id_supplier = (int)$id_supplier;
		          							}
		          							$product_supplier->product_supplier_price_te = importConv2Float($value);
		          							// CURRENCY SUPPLIER
		          							$supplier_currencies = array();
		          							findAllCSVLineValue('supplier_currency',$supplier_currencies);
		          							foreach($supplier_currencies as $supplier_currency)
		          							{
		          								$currency_id_supplier = intval(str_replace("supcurrency_supp_","", $supplier_currency["option"]));
		          								if(!empty($currency_id_supplier) && is_numeric($currency_id_supplier) && $supplier_currency["value"])
		          								{
		          									if($currency_id_supplier==$id_supplier)
		          									{
		          										$supplier_id_currency = Currency::getIdByIsoCode($supplier_currency["value"]);
		          										if(!empty($supplier_id_currency) && is_numeric($supplier_id_currency))
		          											$product_supplier->id_currency = $supplier_id_currency;
		          									}
		          								}
		          							}
		          							$product_supplier->save();
		          							if($id_supplier==$newprod->id_supplier)
		          								Db::getInstance()->Execute("UPDATE "._DB_PREFIX_."product_attribute_shop SET wholesale_price='".psql(importConv2Float($value))."' WHERE id_product_attribute=".intval($id_product_attribute)." AND id_shop IN (".join(',',$id_shop_list).")");
	          							}
	          						}
	          					}
	          					elseif( version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !empty($id_supplier) && ( (!empty($id_supplier) && $id_supplier=="product") || empty($id_supplier)))
	          					{
	          						foreach($combinations_ids as $id_product_attribute)
	          						{
		          						$combination = new Combination((int)$id_product_attribute);
                                        $combination->id_product = (int)$newprod->id;
		          						$combination->id_shop_list=$id_shop_list;
		          						$combination->wholesale_price=importConv2Float($value);
		          						$combination->minimal_quantity = 1;
		          						$combination->save();
	          						}
	          					}
							break;
	          				case 'location':
	         					if(SCAS)
	         					{
						          	$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
		          					if(($type_advanced_stock_management==2 || $type_advanced_stock_management==3) && !empty($value))
		          					{
		          						if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value))
		          						{
		          							foreach($combinations_ids as $id_product_attribute)
		          							{
			          							$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, $id_product_attribute, (int)$id_warehouse);
			          							if(empty($in_warehouse)) // s'il n'est pas lié à l'entrepot
			          							{
			          								$new = new WarehouseProductLocation();
			          								$new->id_product = (int)$newprod->id;
			          								$new->id_product_attribute = $id_product_attribute;
			          								$new->id_warehouse = (int)$id_warehouse;
			          								$new->location = $value;
			          								$new->save();
			          							}
			          							else
			          							{
			          								$new = new WarehouseProductLocation($in_warehouse);
			          								$new->location = $value;
			          								$new->save();
			          							}
		          							}
		          						}
		          					}
		          					elseif($type_advanced_stock_management==1 && !empty($value) && empty($id_warehouse)) // pour produit avec SA désactivé
		          					{
			          					foreach($combinations_ids as $id_product_attribute)
			          					{
			          						$TODO="UPDATE "._DB_PREFIX_."product_attribute SET location='".psql($value)."' WHERE id_product_attribute=".intval($id_product_attribute).";";
			          						$TODO.="UPDATE "._DB_PREFIX_."product_attribute_shop SET location='".psql($value)."' WHERE id_product_attribute=".intval($id_product_attribute)." AND id_shop IN (".join(',',$id_shop_list).");";
			          						Db::getInstance()->Execute($TODO);
			          					}
		          					}
	         					}
							break;
							case 'available_later':
								if (SCI::getConfigurationValue("SC_DELIVERYDATE_INSTALLED")=="1")
								{
									escapeCharForPS($value);
									$id_sc_available_later = 0;

									$sql = "SELECT id_sc_available_later FROM "._DB_PREFIX_."sc_available_later WHERE available_later='".pSQL($value)."' AND id_lang='".(int)$id_lang."'";
									$find_available_later=Db::getInstance()->ExecuteS($sql);
									if(!empty($find_available_later[0]["id_sc_available_later"]))
										$id_sc_available_later = $find_available_later[0]["id_sc_available_later"];
									else
									{
										$sql = "INSERT INTO "._DB_PREFIX_."sc_available_later (id_lang, available_later) VALUES ('".(int)$id_lang."', '".pSQL($value)."')";
										Db::getInstance()->Execute($sql);
										$id_sc_available_later = Db::getInstance()->Insert_ID();
									}

									$TODO="UPDATE "._DB_PREFIX_."product_attribute SET id_sc_available_later=".(int)$id_sc_available_later." WHERE id_product_attribute=".intval($id_product_attribute);
									Db::getInstance()->Execute($TODO);
								}
								break;
	          				case 'quantity':
	         					$no_scas = true;
	         					$value = importConv2Int($value);
	         					if(SCAS)
	         					{
						          	$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
						          	if($type_advanced_stock_management==2) // Advanced stock activé
						          	{
						          		$no_scas = false; // empèche la modif si option non entrepot
						          		if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && $value>=0) // Si option avec entrepot
						          		{
				          					foreach($combinations_ids as $id_product_attribute)
				          					{
							          			$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, (int)$id_product_attribute, (int)$id_warehouse);
							          			if(empty($in_warehouse) && $value!=0) // s'il n'est pas lié à l'entrepot
							          			{
							          				$new = new WarehouseProductLocation();
							          				$new->id_product = (int)$newprod->id;
							          				$new->id_product_attribute = (int)$id_product_attribute;
							          				$new->id_warehouse = (int)$id_warehouse;
							          				$new->save();
							          			}

							          			if($value>=0) // add stock
							          			{
							          				$combination = new Combination((int)$id_product_attribute, null, $id_shop);
							          				if(empty($warehousesArray[$id_warehouse]))
													{
														$warehouse = new Warehouse($id_warehouse);
														$warehousesArray[$id_warehouse] = $warehouse;
													}
													else
														$warehouse = $warehousesArray[$id_warehouse];

													// EMPTY ACUTAL STOCK FOR COMBINATION
													$query = new DbQuery();
													$query->select('SUM(st.physical_quantity) as physical_quantity');
													$query->from('stock', "st");
													$query->where('st.id_product = '.(int)$newprod->id.'');
													$query->where('st.id_product_attribute = '.(int)$id_product_attribute.'');
													$query->where('st.id_warehouse = '.(int)$id_warehouse.'');
													$avanced_quantities = Db::getInstance()->getRow($query);
													if(!empty($avanced_quantities["physical_quantity"]))
													{
														$stock_manager->removeProduct((int)$newprod->id, (int)$id_product_attribute, $warehouse, $avanced_quantities["physical_quantity"], 4, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));
													}

													if($value>0)
													{
														// ADD STOCK FOR COMBINATION
								          				$price = $combination->wholesale_price;

								          				if(!empty($price) && $price>0)
								          				{
								          					// First convert price to the default currency
								          					$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse->id_currency, false);
								          					// Convert the new price from default currency to needed currency
								          					$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
								          				}
                                                        else
                                                        {
                                                            $ws_price = findCSVLineValue("wholesale_price");
                                                            if(!empty($ws_price))
                                                            {
                                                                // First convert price to the default currency
                                                                $price_converted_to_default_currency = Tools::convertPrice($ws_price, $warehouse->id_currency, false);
                                                                // Convert the new price from default currency to needed currency
                                                                $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                            }
                                                        }

								          				if ($stock_manager->addProduct((int)$newprod->id, (int)$id_product_attribute, $warehouse, (int)$value, 1, floatval($price), (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1)))
								          				{
								          					StockAvailable::synchronize((int)$newprod->id);
								          				}
								          				else
								          				{
									          				if(empty($noWholesalepriceArray[(int)$id_product_attribute]))
									          				{
									          					$stats['skipped']++;
									          					$stats['no_wholesaleprice']++;
									          					$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
									          					$noWholesalepriceArray[(int)$id_product_attribute] = 1;
									          				}
									          				$stats['modified']-=1;
									          				$skip_line_scas = true;
								          				}
													}
							          			}
							          			/*elseif(!empty($value) && $value<0) // delete stock
							          			{
							          				if(empty($warehousesArray[$id_warehouse]))
													{
														$warehouse = new Warehouse($id_warehouse);
														$warehousesArray[$id_warehouse] = $warehouse;
													}
													else
														$warehouse = $warehousesArray[$id_warehouse];
							          				$value = $value * -1;
							          				$removed_products = $stock_manager->removeProduct((int)$newprod->id, (int)$id_product_attribute, $warehouse, $value, 2, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));

							          				if (count($removed_products) > 0)
							          				{
							          					StockAvailable::synchronize((int)$newprod->id);
							          				}
							          				else
							          				{
								          				if(empty($noWholesalepriceArray[(int)$id_product_attribute]))
								          				{
								          					$stats['skipped']++;
								          					$stats['no_wholesaleprice']++;
								          					$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
								          					$noWholesalepriceArray[(int)$id_product_attribute] = 1;
								          				}
								          				$stats['modified']-=1;
								          				$skip_line_scas = true;
							          				}
							          			}*/
				          					}
						          		}
						          	}
	         					}

					          	if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
					          	{
		          					foreach($combinations_ids as $id_product_attribute)
		          					{
						          		SCI::setQuantity((int)$newprod->id, (int)$id_product_attribute, (int)$value, $id_shop);
		          					}
					          	}
							break;
	          				case 'add_quantity':
	         					$no_scas = true;
	         					$value = importConv2Int($value);
	         					if(SCAS)
	         					{
						          	$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
						          	if($type_advanced_stock_management==2) // Advanced stock activé
						          	{
						          		$no_scas = false; // empèche la modif si option non entrepot
						          		if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value)) // Si option avec entrepot
						          		{
				          					foreach($combinations_ids as $id_product_attribute)
				          					{
							          			$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, (int)$id_product_attribute, (int)$id_warehouse);
							          			if(empty($in_warehouse) && $value!=0) // s'il n'est pas lié à l'entrepot
							          			{
							          				$new = new WarehouseProductLocation();
							          				$new->id_product = (int)$newprod->id;
							          				$new->id_product_attribute = (int)$id_product_attribute;
							          				$new->id_warehouse = (int)$id_warehouse;
							          				$new->save();
							          			}

							          			if(!empty($value) && $value>0) // add stock
							          			{
							          				$combination = new Combination((int)$id_product_attribute, null, $id_shop);
							          				if(empty($warehousesArray[$id_warehouse]))
													{
														$warehouse = new Warehouse($id_warehouse);
														$warehousesArray[$id_warehouse] = $warehouse;
													}
													else
														$warehouse = $warehousesArray[$id_warehouse];

													// ADD STOCK FOR COMBINATION
							          				$price = $combination->wholesale_price;

							          				if(!empty($price) && $price>0)
							          				{
							          					// First convert price to the default currency
							          					$price_converted_to_default_currency = Tools::convertPrice($price, $warehouse->id_currency, false);
							          					// Convert the new price from default currency to needed currency
							          					$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                    }
							          				else
                                                    {
                                                        $ws_price = findCSVLineValue("wholesale_price");
                                                        if(!empty($ws_price))
                                                        {
                                                            // First convert price to the default currency
                                                            $price_converted_to_default_currency = Tools::convertPrice($ws_price, $warehouse->id_currency, false);
                                                            // Convert the new price from default currency to needed currency
                                                            $price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
                                                        }
                                                    }

							          				if ($stock_manager->addProduct((int)$newprod->id, (int)$id_product_attribute, $warehouse, (int)$value, 1, floatval($price), (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1)))
							          				{
							          					StockAvailable::synchronize((int)$newprod->id);
							          				}
							          				else
							          				{
								          				if(empty($noWholesalepriceArray[(int)$id_product_attribute]))
								          				{
								          					$stats['skipped']++;
								          					$stats['no_wholesaleprice']++;
								          					$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
								          					$noWholesalepriceArray[(int)$id_product_attribute] = 1;
								          				}
								          				$stats['modified']-=1;
								          				$skip_line_scas = true;
							          				}
							          			}
				          					}
						          		}
						          	}
	         					}

					          	if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
					          	{
		          					foreach($combinations_ids as $id_product_attribute)
		          					{
										$id_stock_available = StockAvailable::getStockAvailableIdByProductId((int)$newprod->id, (int)$id_product_attribute, (int)$id_shop);

										if ($id_stock_available)
											SCI::updateQuantity((int)$newprod->id, (int)$id_product_attribute, (int)$value, (int)$id_shop);
										else
					          				SCI::setQuantity((int)$newprod->id, (int)$id_product_attribute, (int)$value, $id_shop);
									}
					          	}
							break;
	          				case 'remove_quantity':
	         					$no_scas = true;
	         					$value = importConv2Int($value);
	         					if(SCAS)
	         					{
						          	$id_warehouse = intval(str_replace("warehouse_","",$mappingData['CSV2DBOptions'][$firstLineData[$key]]));
						          	if($type_advanced_stock_management==2) // Advanced stock activé
						          	{
						          		$no_scas = false; // empèche la modif si option non entrepot
						          		if(!empty($id_warehouse) && is_numeric($id_warehouse) && $id_warehouse>0 && !empty($value)) // Si option avec entrepot
						          		{
				          					foreach($combinations_ids as $id_product_attribute)
				          					{
							          			$in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$newprod->id, (int)$id_product_attribute, (int)$id_warehouse);
							          			if(empty($in_warehouse) && $value!=0) // s'il n'est pas lié à l'entrepot
							          			{
							          				$new = new WarehouseProductLocation();
							          				$new->id_product = (int)$newprod->id;
							          				$new->id_product_attribute = (int)$id_product_attribute;
							          				$new->id_warehouse = (int)$id_warehouse;
							          				$new->save();
							          			}

							          			if(!empty($value) && $value>0) // add stock
							          			{
							          				if(empty($warehousesArray[$id_warehouse]))
													{
														$warehouse = new Warehouse($id_warehouse);
														$warehousesArray[$id_warehouse] = $warehouse;
													}
													else
														$warehouse = $warehousesArray[$id_warehouse];

							          				$removed_products = $stock_manager->removeProduct((int)$newprod->id, (int)$id_product_attribute, $warehouse, $value, 2, (isset($warehouseOptionCache[$id_warehouse]) ? $warehouseOptionCache[$id_warehouse] : 1));

							          				if (count($removed_products) > 0)
							          				{
							          					StockAvailable::synchronize((int)$newprod->id);
							          				}
							          				else
							          				{
								          				if(empty($noWholesalepriceArray[(int)$id_product_attribute]))
								          				{
								          					$stats['skipped']++;
								          					$stats['no_wholesaleprice']++;
								          					$importlimit++; // on suppose que tous les éléments ont été créés en BDD : le cron ne sert que pour mettre à jour stock et/ou prix
								          					$noWholesalepriceArray[(int)$id_product_attribute] = 1;
								          				}
								          				$stats['modified']-=1;
								          				$skip_line_scas = true;
							          				}
							          			}
				          					}
						          		}
						          	}
	         					}

					          	if($no_scas && ( !SCAS || (SCAS && empty($id_warehouse) ) ) ) // Si désactivé et que option non entrepot
					          	{
		          					foreach($combinations_ids as $id_product_attribute)
		          					{
										SCI::updateQuantity((int)$newprod->id, (int)$id_product_attribute, (int)($value*-1), (int)$id_shop);
		          					}
					          	}
							break;
							default:
	          			}
	          		}
	          	}
	          	if($skip_line_scas)
	          		continue;
          	}// FIN COMBINATIONS

          	if (_s('APP_DEBUG_CATALOG_IMPORT'))
          	{
          		$time_end = microtime(true);
          		$time = $time_end - $time_start;
          		echo "<br/><br/>"._l('Combinations processing')." : $time "._l('seconds');
          	}

					// link images to combinations
         	if (((isCombination() && $combinationUsed) || isCombinationWithID()))
         	{
         		if (isCombinationWithID())
         		{
         			$sql="SELECT * FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=".intval($id_product_attribute);
         			$res=Db::getInstance()->ExecuteS($sql);
         			foreach($res AS $v)
         			{
         				$imagesListFromCSVLine[]=$v['id_image'];
         			}
					$sql="DELETE FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=".intval($id_product_attribute);
					Db::getInstance()->Execute($sql);
					$sqlpart=array();
					foreach($imagesListFromCSVLine AS $k => $v)
					{
						$sqlpart[]="(".intval($id_product_attribute).",".intval($v).")";
					}
					$sqlpart=array_unique($sqlpart);
					if (count($sqlpart))
					{
						$sql="INSERT INTO "._DB_PREFIX_."product_attribute_image (id_product_attribute,id_image) VALUES ".join(',',$sqlpart);
						Db::getInstance()->Execute($sql);
					}
				}
				if (isCombination() && $combinationUsed)
				{
					$sql='';
					if (sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") && findCSVLineValue('supplier_reference')!='')
						$sql="SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE supplier_reference='".psql(findCSVLineValue('supplier_reference'))."'".(_s('CAT_IMPORT_CREATE_REFERENCE_1')?" OR supplier_reference LIKE '".psql(findCSVLineValue('supplier_reference'))."_%'":"");
					if (version_compare(_PS_VERSION_,'1.5.0.0','>=') && sc_in_array('supplier_reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") && findCSVLineValue('supplier_reference')!='')
						$sql="SELECT id_product_attribute FROM "._DB_PREFIX_."product_supplier WHERE product_supplier_reference='".psql(findCSVLineValue('supplier_reference'))."'".(_s('CAT_IMPORT_CREATE_REFERENCE_1')?" OR product_supplier_reference LIKE '".psql(findCSVLineValue('supplier_reference'))."_%'":"");
					if (sc_in_array('reference',$mappingData['DBArray'],"catWinImportProcess_DBArray") && findCSVLineValue('reference')!='')
						$sql="SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE reference='".psql(findCSVLineValue('reference'))."'".(_s('CAT_IMPORT_CREATE_REFERENCE_1')?" OR reference LIKE '".psql(findCSVLineValue('reference'))."_%'":"");
					if ($sql!='')
					{
						$res=Db::getInstance()->ExecuteS($sql);
						foreach($res AS $vpa)
						{
							$sql="SELECT * FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=".intval($vpa['id_product_attribute']);
							$resi=Db::getInstance()->ExecuteS($sql);
							foreach($resi AS $vi)
							{
								$imagesListFromCSVLine[]=$vi['id_image'];
							}
							$sql="DELETE FROM "._DB_PREFIX_."product_attribute_image WHERE id_product_attribute=".intval($vpa['id_product_attribute']);
							Db::getInstance()->Execute($sql);
							$sqlpart=array();
							foreach($imagesListFromCSVLine AS $k => $v)
							{
								$sqlpart[]="(".intval($vpa['id_product_attribute']).",".intval($v).")";
							}
							$sqlpart=array_unique($sqlpart);
							if (count($sqlpart))
							{
								$sql="INSERT INTO "._DB_PREFIX_."product_attribute_image (id_product_attribute,id_image) VALUES ".join(',',$sqlpart);
								Db::getInstance()->Execute($sql);
							}
						}
					}
				}
         	}
         	// update date_upd field for combinations
         	if ($id_product_attribute!=0)
         	{

						$sql="UPDATE "._DB_PREFIX_."product_attribute SET date_upd='".psql(date("Y-m-d H:i:s"))."' WHERE id_product_attribute = ".intval($id_product_attribute);
						Db::getInstance()->Execute($sql);
         	}

					foreach($line AS $key => $value)
					{
						$value = trim($value);
						if (sc_array_key_exists($key, $firstLineData) && sc_in_array($firstLineData[$key],$mappingData['CSVArray'],"catWinImportProcess_CSVArray")
						) {

							$switchObject = $mappingData['CSV2DB'][$firstLineData[$key]];
							sc_ext::readImportCSVConfigXML('importProcessAfterCreateAll');
						}
					}

         		$cache_current_line_for_combination[$current_line] = $id_product_attribute;

					if (SCMS && $importConfig[$TODOfilename]['forfoundproduct']!='skip' && ( (!sc_array_key_exists($id_product.'-'.$id_product_attribute,$cache_id_shop) && count($id_shop_list_in_csv)>1) || count($cache_id_shop[$id_product.'-'.$id_product_attribute]) < count($id_shop_list_in_csv)-1))
					{
						//mise en cache
						$cache_id_shop[$id_product.'-'.$id_product_attribute][] = $id_shop;
						$current_line--;
					}else{
						unset($CSVData[$current_line]);
						file_put_contents(SC_CSV_IMPORT_DIR.$TODOfilename,join("\n",$CSVData));
					}
					$id_product_attribute=0;
					$needSaveTODO=false;
				}else{
					if ($scdebug) echo 'Z<br/>';
				}
				if ($scdebug) echo 'f<br/>';

				if (_s('APP_DEBUG_CATALOG_IMPORT'))
				{
					$time_end = microtime(true);
					$time = $time_end - $time_start;
					echo "<br/><br/>"._l('Total time for the line').": $time "._l('seconds');
					if(!empty($id_product))
						die();
				}
				$updated_products[$id_product]=$id_product;
			} // FIN BOUCLE LINE

			// PM Cache
			if(!empty($updated_products))
				ExtensionPMCM::clearFromIdsProduct($updated_products);

			// DELETE TODO CATEGORY
			//ALTER TABLE ps_tag AUTO_INCREMENT = (SELECT (MAX(id_tag)+1) FROM ps_tag)
			if(!empty($id_category))
			{
				$sql="SELECT COUNT(id_product) AS nb FROM "._DB_PREFIX_."category_product WHERE id_category = ".intval($id_category);
				$res = Db::getInstance()->executeS($sql);
				if(empty($res[0]["nb"]))
				{
					$categoryTODO = new Category((int)$id_category);
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
						if (GroupReduction::getGroupsReductionByCategoryId((int)$categoryTODO->id))
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
			}

			if (sc_in_array('id_category_default',$mappingData['DBArray'],"catWinImportProcess_DBArray") || sc_in_array('category_default',$mappingData['DBArray'],"catWinImportProcess_DBArray") || sc_in_array('categories',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
			{
				fixLevelDepth();
				if (version_compare(_PS_VERSION_, '1.4.0.17', '>='))
					Category::regenerateEntireNtree();
			}
			if ($needSaveTODO)
				file_put_contents(SC_CSV_IMPORT_DIR.$TODOfilename,join("\n",$CSVData));
			echo '<b>'._l('Stats:').'</b><br/>';
			$msg = _l('New products:').' '.$stats['created'].'<br/>';
			$msg.= _l('Modified products:').' '.$stats['modified'].'<br/>';
			$msg.= _l('Skipped lines:').' '.$stats['skipped'].'<br/>';
			if(SCAS && !empty($stats['no_wholesaleprice']))
			{
			$msg.= '<br/>'.$stats['skipped'].' '._l('products ignored (no wholesale price is associated, incorrect quantity, etc)').'<br/>';
			}
			echo $msg.'<br/>';
			if ((count($CSVData)==1) || (count($CSVData)==2 && $CSVData[0]==join('',$CSVData)) || (filesize(SC_CSV_IMPORT_DIR.$TODOfilename)==0))
			{
				@unlink(SC_CSV_IMPORT_DIR.$TODOfilename);
				echo _l('All products have been imported. The TODO file is deleted.').'<br/><br/>';
				echo '<b>'._l('End of import process.').'</b><br/><br/>';
				echo '<b>'._l('You need to refresh the page, click here:').' <a target="_top" href="index.php">Go!</a></b><br/>';
				echo '<script type="text/javascript">window.top.displayOptions();window.top.stopAutoImport(true);</script>';
				$msg2 = 'All products have been imported.';
			}else{
				echo '<b>'._l('There are still products to be imported in the working file. It can mean errors you need to correct or lines which have been ignored on purpose. Once corrections have been made, click again on the import icon to proceed further.').'</b><br/><br/>';
				if (sc_in_array('priceinctax',$mappingData['DBArray'],"catWinImportProcess_DBArray"))
				{
					echo '<b>'._l('Your import includes \'description\' and/or \'short description\', please check carriage returns.').'</b><br/>';
					echo '<a href="'._l('http://support.storecommander.com/entries/22130421-Importing-your-description-efficiently').'" target="_blank">'._l('http://support.storecommander.com/entries/22130421-Importing-your-description-efficiently').'</a>';
					echo '<br/>';
				}
				echo '<script type="text/javascript">window.top.displayOptions();autoImportUnit=999999;window.top.prepareNextStep('.($stats['created']+$stats['modified']+$stats['skipped']==0?0:filesize(SC_CSV_IMPORT_DIR.$TODOfilename)).');</script>';
				// TODO add autoImportUnit=999999; to boost import
				$msg2 = 'Need fix and run import again.';
			}
			$msg3='';
			if ($CRON)
			{
				$msg3 .= _l('CRON task name')._l(':').' '.$CRON_NAME.'<br/>';
				$msg3 .= ( isset($CRON_DELETETODO) && $CRON_DELETETODO ? $TODOfilename.' '._l('deleted').'<br/>':'');
				$msg3 .= _l('Update products older than').' '.$CRON_OLDERTHAN;
			}
			addToHistory('catalog_import','import','','','','','Imported file: '.$TODOfilename.'<br/>'.$msg.$msg2.($msg3!=''?'<br/>'.$msg3:''),'');
			if (_s('APP_DEBUG_CATALOG_IMPORT'))
			{
				$time_end = microtime(true);
				$time = $time_end - $time_start;
				echo "<br/><br/>Total: $time "._l('seconds');
			}
			echo '</div>';
			break;
		}
