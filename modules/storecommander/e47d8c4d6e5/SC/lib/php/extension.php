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

if (isset($_GET['setextension']))
{
	$local_settings['CORE_USE_EXTENSIONS']['value']=(int)$_GET['setextension'];
	saveSettings();
}

require_once(SC_DIR.'lib/php/extension/extension_pmcachemanager.php');

class SC_Ext
{

	public static function readCustomGridsConfigXML($config, $extData = false)
	{
		global $grids_products_conf;
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_products_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;

		if (!$grids_products_conf)
		{
			$extConvert = new ExtensionConvert();
			$extConvert->convert("products");
			if (!$grids_products_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_products_conf.xml'))
				return false;
		}
		
		switch($config){
			case 'permissions':
				global $permissions_list;
				$commun_list = array('grid_light','grid_large','grid_delivery','grid_price','grid_discount','grid_discount_2','grid_seo','grid_reference','grid_description');
					
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					if(!sc_in_array((string) $grid->name, $commun_list,"extension_communlist"))
					{
						$permissions_list["GRI_CAT_VIEW_GRID_EXT_".$grid->name] = array('id'=>"GRI_CAT_VIEW_GRID_EXT_".$grid->name,'section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid:'.' '.(string) $grid->text->fr,'description'=>'', 'default_admin'=>1, 'default_value'=>1);
					}
				}
				break;
			case 'gridnames':
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					$add=true;
					
					if($grid->name == 'grid_light' && !_r("GRI_CAT_VIEW_GRID_LIGHT"))
						$add=false;
					elseif($grid->name == 'grid_large' && !_r("GRI_CAT_VIEW_GRID_LARGE"))
						$add=false;
					elseif($grid->name == 'grid_delivery' && !_r("GRI_CAT_VIEW_GRID_DELIVERY"))
						$add=false;
					elseif($grid->name == 'grid_price' && !_r("GRI_CAT_VIEW_GRID_PRICE"))
						$add=false;
					elseif($grid->name == 'grid_discount' && !_r("GRI_CAT_VIEW_GRID_DISCOUNT"))
						$add=false;
					elseif($grid->name == 'grid_discount_2' && !_r("GRI_CAT_VIEW_GRID_DISCOUNT"))
						$add=false;
					elseif($grid->name == 'grid_seo' && !_r("GRI_CAT_VIEW_GRID_SEO"))
						$add=false;
					elseif($grid->name == 'grid_reference' && !_r("GRI_CAT_VIEW_GRID_REFERENCE"))
						$add=false;
					elseif($grid->name == 'grid_description' && !_r("GRI_CAT_VIEW_GRID_DESCRIPTION"))
						$add=false;
					elseif(!_r("GRI_CAT_VIEW_GRID_EXT_".$grid->name))
						$add=false;

					if($add==true)
						echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'",(string) $grid->text->fr)."';";
				}
				break;
			case 'toolbar':
				$lines=array();
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					$lines[]="['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
				}
				echo (count($lines)?',':'').join(',',$lines);
				break;
			case 'gridConfig':
				global $grids;
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					$grids[((string) $grid->name)]=((string) $grid->value);
				}
				break;
			case 'colSettings':
				global $colSettings,$arrManufacturers,$arrSuppliers;
				foreach($grids_products_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if (trim((string) $field->options)!='')
						$options = eval($field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
                    $format = "";
                    if(!empty($field->format))
                        $format = $field->format;
                    elseif(!empty($colSettings[(string) $field->name]["format"]))
                        $format = $colSettings[(string) $field->name]["format"];
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
																											'width'=> ((string) $field->width),
																											'align'=> ((string) $field->align),
																											'type'=> ((string) $field->celltype),
																											'sort'=> ((string) $field->sort),
																											'color'=> ((string) $field->color),
																											'format'=> (string)$format,//((string) $field->format),
																											'filter'=> ((string) $field->filter),
																											'footer'=> $footer,
																											'options'=> $options);
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='') 
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
					if ($options!='')
					{
						$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'updateSettings':
				global $fields,$fields_lang,$forceUpdateCombinations,$idproduct,$id_lang;
				foreach($grids_products_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='product')
					{
						$fields[]=((string) $field->name);
					}elseif (((string) $field->table)=='product_lang'){
						$fields_lang[]=((string) $field->name);
					}elseif (((string) $field->table)=='special' && (trim((string) $field->onUpdate)!='')){
						// special actions
						eval((string) $field->onUpdate);
					}
					if (intval($field->forceUpdateCombinationsGrid)==1)
					{
						$forceUpdateCombinations[]=((string) $field->name);
					}
				}
				break;
			case 'rowData':
				global $fields,$fields_lang,$col,$cols,$idproduct,$id_lang,$prodrow,$has_combination,$prodWithAttributes;
				foreach($grids_products_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols, 'cols') && (trim((string) $field->rowData) != ''))
						eval((string) $field->rowData);
					break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$id_lang;
				foreach($grids_products_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataSelect) != ''))
						$sql.=eval((string) $field->SQLSelectDataSelect);
					break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$id_lang;
				foreach($grids_products_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataLeftJoin) != ''))
						$sql.=eval((string) $field->SQLSelectDataLeftJoin);
					break;
			case 'SQLSelectDataWhere':
				global $sql,$view,$cols,$id_lang;
				foreach($grids_products_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols, 'cols') && (trim((string) $field->SQLSelectDataWhere) != ''))
						$sql.=eval((string) $field->SQLSelectDataWhere);
					break;
			case 'onEditCell':
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					echo ((string) $grid->onEditCell);
				}
				foreach($grids_products_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onAfterUpdate':
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onAfterUpdate);
				}
				foreach($grids_products_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $idproduct, $id_product, $id_lang;
				$idproduct = $id_product; // for compatibility with old versions
				foreach($grids_products_conf->fields->field AS $field)
				{
					if ((trim((string) $field->onAfterUpdateSQL) != ''))
						eval((string) $field->onAfterUpdateSQL);
				}
				break;
            case 'onBeforeUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute,$id_lang,$fields,$fields_lang,$fieldsWithHTML;
                $idproduct = $id_product; // for compatibility with old versions
                foreach($grids_products_conf->fields->field AS $field)
                {
                    if ((trim((string) $field->onBeforeUpdateSQL) != ''))
                        eval((string) $field->onBeforeUpdateSQL);
                }
                break;
			case 'onBeforeUpdate':
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onBeforeUpdate);
				}
				foreach($grids_products_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'gridUserData':
				foreach($grids_products_conf->grids->grid AS $grid)
					if ((trim((string) $grid->gridUserData) != ''))
						eval((string) $grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols;
				foreach($grids_products_conf->fields->field AS $field)
				{
					if(is_array($cols) && sc_in_array((string)$field->name, $cols, 'cols'))
						if ((trim((string) $field->rowUserData) != ''))
							eval((string) $field->rowUserData);
				}
				break;
			case 'extraVars':
				global $action,$extraVars,$cols;
				foreach($grids_products_conf->grids->grid AS $grid)
				{
					if ((trim((string) $grid->extraVars) != ''))
						eval((string) $grid->extraVars);
				}
				foreach($grids_products_conf->fields->field AS $field)
				{
					if ((trim((string) $field->extraVars) != ''))
						eval((string) $field->extraVars);
				}
				break;
			case 'afterGetRows':
				foreach($grids_products_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}


	public static function readCustomCombinationsGridConfigXML($config, $extData = false)
	{
		global $grids_combinations_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_combinations_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_combinations_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("combinations");
			if (!$grids_combinations_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_combinations_conf.xml'))
				return false;
		}
		$grids_combinations_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_combinations_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_combinations_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='product_attribute')
						$field->definition = ' return $combArray[$combinaison["id_product_attribute"]]["'.$field->name.'"] = $combinaison["'.$field->name.'"];';
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'updateSettings':
				global $fields;
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='product_attribute')
					{
						$fields[]=((string) $field->name);
					}
				}
				break;
			case 'onEditCell':
				echo ((string) $grids_combinations_conf->grids->grid->onEditCell);
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_combinations_conf->grids->grid->onAfterUpdate);
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
            case 'onAfterUpdateSQL':
                global $idproduct,$id_product,$id_product_attribute, $id_lang;
                foreach($grids_combinations_conf->fields->field AS $field)
                    eval((string) $field->onAfterUpdateSQL);
                break;
			case 'onBeforeUpdate':
				echo((string) $grids_combinations_conf->grids->grid->onBeforeUpdate);
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'gridUserData':
				eval((string) $grids_combinations_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'extraVars':
				global $action,$extraVars,$cols,$all_cols;
				eval((string) $grids_combinations_conf->grids->grid->extraVars);
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					eval((string) $field->extraVars);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols, $id_lang;
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='product_attribute')
						$field->SQLSelectDataSelect = ' return " , pa.`'.$field->name.'` ";';
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols, $id_lang;
				foreach($grids_combinations_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'afterGetRows':
				foreach($grids_combinations_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}


	public static function readExportCSVConfigXML($config)
	{
		//global $export_csv_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !_s('CORE_USE_EXTENSIONS')) return false;
		$files = array();
		
		$all_files = scandir(SC_TOOLS_DIR);
		foreach($all_files as $dir)
		{
			if(is_dir(SC_TOOLS_DIR.$dir) && $dir!=".")
			{
				if (file_exists(SC_TOOLS_DIR.$dir.'/export_csv_conf.xml'))
					$files[] = SC_TOOLS_DIR.$dir.'/export_csv_conf.xml';
			}
		}
		if (file_exists(SC_TOOLS_DIR.'export_csv_conf.xml'))
			$files[] = SC_TOOLS_DIR.'export_csv_conf.xml';

		$return = "";

		foreach ($files as $file)
		{
			if (file_exists($file))
			{
				$exec = true;
				if (!$export_csv_conf = simplexml_load_file($file))
					$exec = false;
				if($exec)
				{
					switch($config){
						case 'definition':// non
							global $array,$fields_lang;
							$tmp=explode(',',(string) $export_csv_conf->definitionLang);
							foreach($tmp AS $t)
								$fields_lang[]=$t;
							eval((string) $export_csv_conf->definition);
							break;
						case 'exportMappingPrepareGrid': // avancée
							global $sc_agent;
							eval((string) $export_csv_conf->exportMappingPrepareGrid);
							break;
						case 'exportMappingCheckGrid': // avancée
							global $sc_agent;
							eval((string) $export_csv_conf->exportMappingCheckGrid);
							break;
						case 'exportMappingFillCombo': // avancée
							global $sc_agent;
							eval((string) $export_csv_conf->exportMappingFillCombo);
							break;
						case 'definitionLang': // permettre la sélection d'une langue dans le mapping
							$tmp=join("','",explode(',',(string) $export_csv_conf->definitionLang));
							$return .= ($tmp==''?'':",'".$tmp."'");
							break;
						case 'exportProcessInitRowVars': // avancée
							global $id_product,$id_product_attribute,$p,$extension_vars;
							eval((string) $export_csv_conf->exportProcessInitRowVars);
							break;
						case 'exportProcessProduct':
							global $switchObject,$switchObjectOption,$switchObjectLang,$getIDlangByISO,$id_product,$id_product_attribute,$field,$fields_lang,$p,$extension_vars,$selected_shops_id,$featuresListByLang,$featuresListNameDefault;
							eval((string) $export_csv_conf->exportProcessProduct);
							break;
						case 'addInCombiFields': // avancée
							global $standardFields;
							eval((string) $export_csv_conf->addInCombiFields);
							break;
					}
				}
			}
		}

		if(!empty($return))
			return $return;
	}
	
	public static function readImportCSVConfigXML($config)
	{
		//global $import_csv_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !_s('CORE_USE_EXTENSIONS')) return false;

		$files = array();
		
		$all_files = scandir(SC_TOOLS_DIR);
		foreach($all_files as $dir)
		{
			if(is_dir(SC_TOOLS_DIR.$dir))
			{
				if (file_exists(SC_TOOLS_DIR.$dir.'/import_csv_conf.xml'))
					$files[] = SC_TOOLS_DIR.$dir.'/import_csv_conf.xml';
			}
		}
		if (file_exists(SC_TOOLS_DIR.'import_csv_conf.xml'))
			$files[] = SC_TOOLS_DIR.'import_csv_conf.xml';

		$return = "";

		foreach ($files as $file)
		{
			if (file_exists($file))
			{
				$exec = true;
				if (!$import_csv_conf = simplexml_load_file($file))
					$exec = false;
				if($exec)
				{
					switch($config){
						case 'definition':
							global $array,$sc_agent;
							eval((string) $import_csv_conf->definition);
							break;
						case 'importMappingPrepareGrid': // avancée
							global $sc_agent;
							eval((string) $import_csv_conf->importMappingPrepareGrid);
							break;
						case 'importMappingCheckGrid': // avancée
							global $sc_agent;
							eval((string) $import_csv_conf->importMappingCheckGrid);
							break;
						case 'importMappingFillCombo': // avancée
							global $sc_agent;
							eval((string) $import_csv_conf->importMappingFillCombo);
							break;
						case 'importProcessIdentifier': // avancée
							global $importConfig,$TODOfilename,$defaultLanguage,$sql,$sc_agent;
							eval((string) $import_csv_conf->importProcessIdentifier);
							break;
						case 'importProcessProduct':
							global $switchObject,$TODO,$id_product,$id_product_attribute,$newprod,$id_lang,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_shop,$getIDlangByISO;
							eval((string) $import_csv_conf->importProcessProduct);
							break;
						case 'importProcessCombination':
							global $switchObject,$TODO,$id_product,$id_product_attribute,$combinationValues,$newprod,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_lang,$id_shop,$getIDlangByISO;
							eval((string) $import_csv_conf->importProcessCombination);
							break;
						case 'importProcessAfterCreateAll':
							global $switchObject,$TODO,$id_product,$id_product_attribute,$combinationValues,$newprod,$importConfig,$TODOfilename,$mappingData,$firstLineData,$key,$sc_agent,$features,$featureValues,$extension_vars,$id_lang,$id_shop,$getIDlangByISO,$value;
							eval((string) $import_csv_conf->importProcessAfterCreateAll);
							break;
						case 'importProcessInitRowVars': // avancée
							global  $switchObject,$id_product,$id_product_attribute,$newprod,$mappingData,$sc_agent,$extension_vars,$id_shop;
							eval((string) $import_csv_conf->importProcessInitRowVars);
							break;
						case 'importProcessImageUpdate': // avancée
							global  $switchObject,$id_product,$id_product_attribute,$newprod,$mappingData,$getIDlangByISO,$sc_agent,$actual_num,$actual_id,$image_id,$TODO_image,$extension_vars,$id_shop;
							eval((string) $import_csv_conf->importProcessImageUpdate);
							break;
						case 'definitionForLangField':
							if(!empty($import_csv_conf->definitionForLangField))
							{
								$tmp=join("','",explode(',',(string) $import_csv_conf->definitionForLangField));
								$return .= ($tmp==''?'':",'".$tmp."'");
							}
							elseif(!empty($import_csv_conf->definitionLang))
							{
								$tmp=join("','",explode(',',(string) $import_csv_conf->definitionLang));
								$return .= ($tmp==''?'':",'".$tmp."'");
							}
							break;
			/*			case 'onEditCell':
							foreach($grid->fields->field AS $field)
							{
								echo ((string) $field->onEditCell);
							}
							break;*/
					}
				}
			}
		}

		if(!empty($return))
			return $return;
	}

	public static function readCustomOrdersGridsConfigXML($config, $extData = false)
	{
		global $grids_orders_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_orders_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
			
		if (!$grids_orders_conf)
		{
			$extConvert = new ExtensionConvert();
			$extConvert->convert("orders");
			if (!$grids_orders_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_orders_conf.xml'))
				return false;
		}
		switch($config){
			case 'permissions':
				global $permissions_list;
				$commun_list = array('grid_light','grid_large','grid_delivery','grid_picking');
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					if(!sc_in_array((string) $grid->name, $commun_list,"extension_communlist"))
					{
						$permissions_list["GRI_ORD_VIEW_GRID_EXT_".$grid->name] = array('id'=>"GRI_ORD_VIEW_GRID_EXT_".$grid->name,'section1'=>'Grid','section2'=>'Orders','name'=>'Order grid:'.' '.(string) $grid->text->fr,'description'=>'', 'default_admin'=>1, 'default_value'=>1);
					}
				}
				break;
			case 'gridnames':
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					$add=true;
					if($grid->name == 'grid_light' && !_r("GRI_ORD_VIEW_GRID_LIGHT"))
						$add=false;
					elseif($grid->name == 'grid_large' && !_r("GRI_ORD_VIEW_GRID_LARGE"))
						$add=false;
					elseif($grid->name == 'grid_delivery' && !_r("GRI_ORD_VIEW_GRID_DELIVERY"))
						$add=false;
					elseif($grid->name == 'grid_picking' && !_r("GRI_ORD_VIEW_GRID_PICKING"))
						$add=false;
					elseif(!_r("GRI_ORD_VIEW_GRID_EXT_".$grid->name))
						$add=false;
					
					if($add==true)
						echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'",(string) $grid->text->fr)."';";
				}
				break;
			case 'toolbar':
				$lines=array();
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					$lines[]="['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
				}
				echo (count($lines)?',':'').join(',',$lines);
				break;
			case 'gridConfig':
				global $grids;
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					$grids[((string) $grid->name)]=((string) $grid->value);
				}
				break;
			case 'colSettings':
				global $colSettings,$arrPayments,$arrStatus;
				foreach($grids_orders_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
																											'width'=> ((string) $field->width),
																											'align'=> ((string) $field->align),
																											'type'=> ((string) $field->celltype),
																											'sort'=> ((string) $field->sort),
																											'color'=> ((string) $field->color),
																											'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
																											'filter'=> ((string) $field->filter),
																											'footer'=> $footer,
																											'options'=> $options);
					/*$answertype=((string) $field->answertype);
					if ($answertype!='') 
					{
						if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='') 
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
					if ($options!='')
					{
						$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'updateSettings':
				global $fields_order,$fields,$fields_customer,$idorder,$id_lang,$fields_address_invoice,$fields_address_delivery;
				foreach($grids_orders_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='orders')
					{
						if(!empty($fields_order))
							$fields_order[]=((string) $field->name);
						if(!empty($fields))
							$fields[]=((string) $field->name);
					}elseif (((string) $field->table)=='customer'){
                        if(!empty($fields_customer))
                            $fields_customer[]=((string) $field->name);
                    }elseif (((string) $field->table)=='address_delivery'){
                        if(!empty($fields_address_delivery))
                            $fields_address_delivery[]=((string) $field->name);
                    }elseif (((string) $field->table)=='address_invoice'){
                        if(!empty($fields_address_invoice))
                            $fields_address_invoice[]=((string) $field->name);
                    }elseif (((string) $field->table)=='special'){
						// special actions
						eval((string) $field->onUpdate);
					}
				}
				break;
			case 'rowData':
				global $fields_order,$fields_customer,$col,$cols,$idorder,$id_lang,$orderrow;
				foreach($grids_orders_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						eval((string) $field->rowData);
					break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$id_lang;
				foreach($grids_orders_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
					break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols;
				foreach($grids_orders_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						$sql.=eval((string) $field->SQLSelectDataLeftJoin);
					break;
			case 'onEditCell':
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					echo ((string) $grid->onEditCell);
				}
				foreach($grids_orders_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onAfterUpdate':
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onAfterUpdate);
				}
				foreach($grids_orders_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $id_order,$id_order_detail;
				foreach($grids_orders_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'onBeforeUpdate':
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onBeforeUpdate);
				}
				foreach($grids_orders_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'gridUserData':
				foreach($grids_orders_conf->grid AS $grid)
				{
					eval((string) $grid->gridUserData);
				}
				break;
			case 'rowUserData':
				global $cols;
				foreach($grids_orders_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'extraVars':
				global $action,$extraVars,$cols;
				foreach($grids_orders_conf->grids->grid AS $grid)
				{
					eval((string) $grid->extraVars);
				}
				foreach($grids_orders_conf->fields->field AS $field)
				{
					eval((string) $field->extraVars);
				}
				break;
			case 'afterGetRows':
				foreach($grids_orders_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomCustomersGridsConfigXML($config, $extData = false)
	{
		global $grids_customers_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_customers_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
				
		if (!$grids_customers_conf)
		{
			$extConvert = new ExtensionConvert();
			$extConvert->convert("customers");
			if (!$grids_customers_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_customers_conf.xml'))
				return false;
		}
		switch($config){
			case 'permissions':
				global $permissions_list;
				$commun_list = array('grid_light','grid_large','grid_address','grid_convert');
					
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					if(!sc_in_array((string) $grid->name, $commun_list,"extension_commun_list"))
					{
						$permissions_list["GRI_CUS_VIEW_GRID_EXT_".$grid->name] = array('id'=>"GRI_CUS_VIEW_GRID_EXT_".$grid->name,'section1'=>'Grid','section2'=>'Customers','name'=>'Customer grid:'.' '.(string) $grid->text->fr,'description'=>'', 'default_admin'=>1, 'default_value'=>1);
					}
				}
				break;
			case 'gridnames':
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					$add=true;
					if($grid->name == 'grid_light' && !_r("GRI_CUS_VIEW_GRID_LIGHT"))
						$add=false;
					elseif($grid->name == 'grid_large' && !_r("GRI_CUS_VIEW_GRID_LARGE"))
						$add=false;
					elseif($grid->name == 'grid_address' && !_r("GRI_CUS_VIEW_GRID_ADDRESS"))
						$add=false;
					elseif($grid->name == 'grid_convert' && !_r("GRI_CUS_VIEW_GRID_CONVERT"))
						$add=false;
					elseif(!_r("GRI_CUS_VIEW_GRID_EXT_".$grid->name))
						$add=false;
					
					if($add==true)
						echo "gridnames['".((string) $grid->name)."']='".str_replace("'", "\'",(string) $grid->text->fr)."';";
				}
				break;
			case 'toolbar':
				$lines=array();
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					$lines[]="['".((string) $grid->name)."', 'obj', gridnames['".((string) $grid->name)."'], '']";
				}
				echo (count($lines)?',':'').join(',',$lines);
				break;
			case 'gridConfig':
				global $grids;
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					$grids[((string) $grid->name)]=((string) $grid->value);
				}
				break;
			case 'colSettings':
				global $colSettings,$arrGenders,$arrGroupes,$arrStates,$arrCountrys;
				foreach($grids_customers_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
																											'width'=> ((string) $field->width),
																											'align'=> ((string) $field->align),
																											'type'=> ((string) $field->celltype),
																											'sort'=> ((string) $field->sort),
																											'color'=> ((string) $field->color),
																											'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
																											'filter'=> ((string) $field->filter),
																											'footer'=> $footer,
																											'options'=> $options);
					/*$answertype=((string) $field->answertype);
					if ($answertype!='') 
					{
						if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='') 
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
					if ($options!='')
					{
						$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'updateSettings':
				global $fields_customer,$fields,$fields_address,$idcustomer,$id_lang;
				foreach($grids_customers_conf->fields->field AS $field)
				{
					if (((string) $field->table)=='customer')
					{
						if(!empty($fields_customer))
							$fields_customer[]=((string) $field->name);
						if(!empty($fields))
							$fields[]=((string) $field->name);
					}elseif (((string) $field->table)=='address'){
						if(!empty($fields_address))
							$fields_address[]=((string) $field->name);
					}elseif (((string) $field->table)=='special'){
						// special actions
						eval((string) $field->onUpdate);
					}
				}
				break;
			case 'rowData':
				global $fields_customer,$fields,$fields_address,$col,$cols,$idcustomer,$id_lang,$customerrow;
				foreach($grids_customers_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						eval((string) $field->rowData);
					break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols;
				foreach($grids_customers_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
					break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols;
				foreach($grids_customers_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						$sql.=eval((string) $field->SQLSelectDataLeftJoin);
					break;
			case 'onEditCell':
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					echo ((string) $grid->onEditCell);
				}
				foreach($grids_customers_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onAfterUpdate':
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onAfterUpdate);
				}
				foreach($grids_customers_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $id_objet;
				foreach($grids_customers_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'onBeforeUpdate':
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					echo((string) $grid->onBeforeUpdate);
				}
				foreach($grids_customers_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'gridUserData':
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					eval((string) $grid->gridUserData);
				}
				break;
			case 'rowUserData':
				global $cols;
				foreach($grids_customers_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $cols,"extension_cols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'extraVars':
				global $action,$extraVars,$cols;
				foreach($grids_customers_conf->grids->grid AS $grid)
				{
					eval((string) $grid->extraVars);
				}
				foreach($grids_customers_conf->fields->field AS $field)
				{
					eval((string) $field->extraVars);
				}
				break;
			case 'afterGetRows':
				foreach($grids_customers_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}


	public static function readCustomImageGridConfigXML($config)
	{
		global $grid_image_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_image_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (empty($grid_image_conf))
		{
			if (!$grid_image_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_image_conf.xml'))
				return false;
		}
	switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grid_image_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grid_image_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grid_image_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grid_image_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grid_image_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grid_image_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grid_image_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grid_image_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grid_image_conf->grids->grid->onEditCell);
				foreach($grid_image_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grid_image_conf->grids->grid->onBeforeUpdate);
				foreach($grid_image_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grid_image_conf->grids->grid->onAfterUpdate);
				foreach($grid_image_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $id_image,$id_product,$id_lang,$col,$val;
				foreach($grid_image_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grid_image_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomCategoriesGridConfigXML($config, $extData = "")
	{
		global $grids_categories_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_categories_conf.xml')) return false;
		if (!$grids_categories_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("categories");
			if (!$grids_categories_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_categories_conf.xml'))
				return false;
		}
		$grids_categories_conf;
		switch($config){
			case 'addHeaderInGet':
				$return = '';
				foreach($grids_categories_conf->fields->field AS $field)
				{
					$return .= '<column id="'.((string) $field->name).'" width="'.((string) $field->width).'" type="'.((string) $field->celltype).'" align="'.((string) $field->align).'" sort="'.((string) $field->sort).'">'.str_replace("'", "\'",(string) $field->text->fr).'</column>'."\n";
				}
				return $return;
				break;
			case 'addFilterInGet':
				$return = "";
				foreach($grids_categories_conf->fields->field AS $field)
				{
					$val = "";
					if(!empty($field->filter))
						$val = "#".((string) $field->filter);
					$return .= ",".$val;
				}
				return $return;
				break;
			case 'addRowValueInGet':
				global $row;
				$return = "";
				foreach($grids_categories_conf->fields->field AS $field)
				{
					$val = "";
					if(!empty($extData[((string) $field->name)]))
						$val = $extData[((string) $field->name)];
					$return .= "<cell><![CDATA[".$val."]]></cell>";
				}
				return $return;
				break;
			case 'onEditCell':
				foreach($grids_categories_conf->fields->field AS $field)
				{
					eval((string) $field->onEditCell);
				}
				break;
			case 'onAfterUpdateSQL':
				global $field,$value,$id_category,$id_lang;
				foreach($grids_categories_conf->fields->field AS $fieldnode)
					eval((string) $fieldnode->onAfterUpdateSQL);
				break;
		}
	}

	public static function readCustomProductsortGridConfigXML($config, $extData = false)
	{
		global $grids_productsort_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_productsort_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_productsort_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("productsort");
			if (!$grids_productsort_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_productsort_conf.xml'))
				return false;
		}
		$grids_productsort_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_productsort_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_productsort_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_productsort_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_productsort_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_productsort_conf->grids->grid->gridUserData);
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_productsort_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_productsort_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'afterGetRows':
				foreach($grids_productsort_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomMsProductGridConfigXML($config, $extData = false)
	{
		global $grids_msproduct_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_msproduct_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_msproduct_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("msproduct");
			if (!$grids_msproduct_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_msproduct_conf.xml'))
				return false;
		}
		$grids_msproduct_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_msproduct_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_msproduct_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_msproduct_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_msproduct_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grids_msproduct_conf->grids->grid->onEditCell);
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grids_msproduct_conf->grids->grid->onBeforeUpdate);
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_msproduct_conf->grids->grid->onAfterUpdate);
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $idproduct,$id_product,$id_product_attribute;
				foreach($grids_msproduct_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grids_msproduct_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomMsCombinationGridConfigXML($config, $extData = false)
	{
		global $grids_mscombination_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_mscombination_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_mscombination_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("mscombination");
			if (!$grids_mscombination_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_mscombination_conf.xml'))
				return false;
		}
		$grids_mscombination_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_mscombination_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_mscombination_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_mscombination_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_mscombination_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grids_mscombination_conf->grids->grid->onEditCell);
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grids_mscombination_conf->grids->grid->onBeforeUpdate);
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_mscombination_conf->grids->grid->onAfterUpdate);
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $idproduct,$id_product,$id_product_attribute;
				foreach($grids_mscombination_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grids_mscombination_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomPropSpePriceGridConfigXML($config, $extData = false)
	{
		global $grids_propspeprice_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_propspeprice_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_propspeprice_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("propspeprice");
			if (!$grids_propspeprice_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_propspeprice_conf.xml'))
				return false;
		}
		$grids_propspeprice_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_propspeprice_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_propspeprice_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_propspeprice_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_propspeprice_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grids_propspeprice_conf->grids->grid->onEditCell);
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grids_propspeprice_conf->grids->grid->onBeforeUpdate);
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_propspeprice_conf->grids->grid->onAfterUpdate);
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $idproduct,$id_product,$id_product_attribute;
				foreach($grids_propspeprice_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grids_propspeprice_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomWinSpePriceGridConfigXML($config, $extData = false)
	{
		global $grids_winspeprice_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_winspeprice_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_winspeprice_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("winspeprice");
			if (!$grids_winspeprice_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_winspeprice_conf.xml'))
				return false;
		}
		$grids_winspeprice_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_winspeprice_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_winspeprice_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
					 if ($answertype!='')
					 {
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
					 if ($options!='')
					 {
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_winspeprice_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_winspeprice_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grids_winspeprice_conf->grids->grid->onEditCell);
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grids_winspeprice_conf->grids->grid->onBeforeUpdate);
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_winspeprice_conf->grids->grid->onAfterUpdate);
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $id_product,$id_specific_price;
				foreach($grids_winspeprice_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grids_winspeprice_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCustomPropSupplierGridConfigXML($config, $extData = false)
	{
		global $grids_propsupplier_conf;
		$setextension = Tools::getValue("setextension", 1);
		if (!SC_TOOLS || !file_exists(SC_TOOLS_DIR.'grids_propsupplier_conf.xml') || !_s('CORE_USE_EXTENSIONS')) return false;
		if (!$grids_propsupplier_conf)
		{
			$ext= new ExtensionConvert();
			$ext->convert("propsupplier");
			if (!$grids_propsupplier_conf = simplexml_load_file(SC_TOOLS_DIR.'grids_propsupplier_conf.xml'))
				return false;
		}
		$grids_propsupplier_conf;
		switch($config){
			case 'gridConfig':
				global $sourceGridFormat;
				if(!empty($grids_propsupplier_conf->grids->grid->value))
				{
					$sourceGridFormat=(string) $grids_propsupplier_conf->grids->grid->value;
				}
				break;
			case 'definition':
				global $combArray,$combinaison,$cols,$all_cols;
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string)$field->definition);
				}
				break;
			case 'colSettings':
				global $colSettings;
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					if((string) $field->filter=="na")
						$field->filter = "";
					$footer = "";
					$options = "";
					if(!empty($colSettings[((string) $field->name)]))
					{
						$footer = $colSettings[((string) $field->name)]["footer"];
						$options = $colSettings[((string) $field->name)]["options"];
					}
					if (trim((string) $field->footer)!='')
						$footer = (string)$field->footer;
					if(trim((string) $field->options)!='')
						$options = eval((string)$field->options);
					else
					{
						$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
							if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
						}
					}
					$colSettings[((string) $field->name)]=array('text' => str_replace("'", "\'",(string) $field->text->fr),
							'width'=> ((string) $field->width),
							'align'=> ((string) $field->align),
							'type'=> ((string) $field->celltype),
							'sort'=> ((string) $field->sort),
							'color'=> ((string) $field->color),
							'format'=> $colSettings[((string) $field->name)]["format"],//((string) $field->format),
							'filter'=> ((string) $field->filter),
							'footer'=> $footer,
							'options'=> $options);
					/*$answertype=((string) $field->answertype);
						if ($answertype!='')
						{
					if ($answertype=='YESNO') $options=array(0=>_l('No'),1=>_l('Yes'));
					$colSettings[((string) $field->name)]['options']=$options;
					}*/
					$buildDefaultValue=((string) $field->buildDefaultValue);
					if ($buildDefaultValue!='')
					{
						$colSettings[((string) $field->name)]['buildDefaultValue']=$buildDefaultValue;
					}
					/*$options=((string) $field->options);
						if ($options!='')
						{
					$colSettings[((string) $field->name)]['options']=eval($options);
					}*/
				}
				break;
			case 'gridUserData':
				eval((string) $grids_propsupplier_conf->grids->grid->gridUserData);
				break;
			case 'rowUserData':
				global $cols,$all_cols;
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						eval((string) $field->rowUserData);
				}
				break;
			case 'SQLSelectDataSelect':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
						$sql.=eval((string) $field->SQLSelectDataSelect);
				}
				break;
			case 'SQLSelectDataLeftJoin':
				global $sql,$view,$cols,$all_cols;
				foreach($grids_propsupplier_conf->fields->field AS $field)
					if(sc_in_array((string)$field->name, $all_cols,"extension_allcols"))
					$sql.=eval((string) $field->SQLSelectDataLeftJoin);
				break;
			case 'onEditCell':
				echo ((string) $grids_propsupplier_conf->grids->grid->onEditCell);
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					echo ((string) $field->onEditCell);
				}
				break;
			case 'onBeforeUpdate':
				echo((string) $grids_propsupplier_conf->grids->grid->onBeforeUpdate);
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					echo((string) $field->onBeforeUpdate);
				}
				break;
			case 'onAfterUpdate':
				echo((string) $grids_propsupplier_conf->grids->grid->onAfterUpdate);
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					echo((string) $field->onAfterUpdate);
				}
				break;
			case 'onAfterUpdateSQL':
				global $idproduct,$id_product,$id_product_attribute;
				foreach($grids_propsupplier_conf->fields->field AS $field)
					eval((string) $field->onAfterUpdateSQL);
				break;
			case 'afterGetRows':
				foreach($grids_propsupplier_conf->fields->field AS $field)
				{
					if(!empty($field->afterGetRows))
						eval((string)$field->afterGetRows);
				}
				break;
		}
	}

	public static function readCatImportCSVConfigXML($config)
	{

	}
	
	public static function addNewField($type, $field, $view="")
	{
		if(!empty($type) && !empty($field))
		{
			$file = SC_TOOLS_DIR.'grids_'.$type.'_conf.xml';

			// UPDATE FIELD
			$dom = new DOMDocument();
			$dom->preserveWhiteSpace = false;
			$dom->formatOutput = true;
			$dom->load($file);
			
			$alone_grids = array("combinations","productsort","msproduct","mscombination","image");
			if(sc_in_array($type, $alone_grids,"extension_alone_grids"))
			{
				$nodeGridList = $dom->getElementsByTagname('grid');
				foreach ( $nodeGridList as $nodeGrid )
				{
					$nodeText = $nodeGrid->getElementsByTagname("value")->item(0);
					$value = $nodeText->nodeValue.",".$field;
					$nodeText->nodeValue='';
					$v=$nodeText->ownerDocument->createCDATASection($value);
					$nodeText->appendChild($v);
				}
				$dom->save($file);
			}
				
			$content = file_get_contents($file);
			$content = str_replace("<grids/>","<grids></grids>",$content);
			$content = str_replace("<fields/>","<fields></fields>",$content);
			file_put_contents($file, $content);
		}
	}
}

/*$ext= new ExtensionConvert();
$ext->convert("combinations");*/