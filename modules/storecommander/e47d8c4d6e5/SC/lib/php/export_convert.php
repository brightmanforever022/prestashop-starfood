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


class ExportConvert
{
	public static function convert($name, $datas)
	{
		$new_datas = array();
		$mapping = $datas->field;
		$i = 0;
		foreach($mapping AS $map)
		{
			$new_datas[(int)$i]["id"]=$map->id;
			$new_datas[(int)$i]["used"]=$map->used;
			$new_datas[(int)$i]["name"]=$map->name;
			$new_datas[(int)$i]["lang"]=$map->lang;
			$new_datas[(int)$i]["options"]=$map->options;
			//$new_datas[(int)$i]["filters"]=$map->filters;
			$new_datas[(int)$i]["modifications"]=$map->modifications;
			$new_datas[(int)$i]["column_name"]=$map->column_name;
			$i++;
		}
		
		$attributes = $datas->attributes();
		
		$file_version = 1;
		$actual_version = 1;
		
		if(!empty($attributes->version))
		{
			$file_version = (int)$attributes->version;
		}
		
		if(defined("SC_EXPORT_VERSION") && SC_EXPORT_VERSION>0)
			$actual_version = (int)SC_EXPORT_VERSION;
		
		if($file_version!=$actual_version)
		{
			$start = $file_version+1;
			for ($i=$start; $i<=$actual_version;$i++)
			{
				$new_datas = call_user_func(array(self, '_convert_from_'.$file_version.'_to_'.$i), $new_datas);
				$file_version++;
			}
			
			self::saveXML($name, $actual_version, $new_datas);
			$new_datas = @simplexml_load_file(SC_TOOLS_DIR.'cat_export/'.$name);
		}
		else
			$new_datas = $datas;
		
		return $new_datas;
	}
	
	public static function saveXML($name, $version, $datas)
	{
		$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<mapping version="'.(int)$version.'">';
		$contentArray=array();
		foreach($datas AS $i=>$map)
		{
			$contentArray[(int)$i]='<field>';
			$contentArray[(int)$i].='<id><![CDATA['.$map["id"].']]></id>';
			$contentArray[(int)$i].='<used><![CDATA['.$map["used"].']]></used>';
			$contentArray[(int)$i].='<name><![CDATA['.$map["name"].']]></name>';
			$contentArray[(int)$i].='<lang><![CDATA['.$map["lang"].']]></lang>';
			$contentArray[(int)$i].='<options><![CDATA['.$map["options"].']]></options>';
			//$contentArray[(int)$i].='<filters><![CDATA['.$map["filters"].']]></filters>';
			$contentArray[(int)$i].='<modifications><![CDATA['.$map["modifications"].']]></modifications>';
			$contentArray[(int)$i].='<column_name><![CDATA['.$map["column_name"].']]></column_name>';
			$contentArray[(int)$i].='</field>'."\n";
		}
		$content.=join('',$contentArray).'</mapping>';
		file_put_contents(SC_TOOLS_DIR.'cat_export/'.$name, $content);
	}
		
	public static function _convert_from_1_to_2($datas)
	{
		global $sc_agent;
		$new_datas = $datas;
		
		$defaultLanguageId = intval(Configuration::get('PS_LANG_DEFAULT'));
		if(!empty($sc_agent->id_lang))
			$defaultLanguageId = intval($sc_agent->id_lang);

		$defaultLanguage = Language::getIsoById($defaultLanguageId);
		
		foreach($new_datas AS $i=>$map)
		{
			$new_datas[$i]["column_name"]="";
			$new_datas[$i]["lang"]="";
			if($map["name"]=="attribute" || $map["name"]=="feature")
			{
				$new_datas[$i]["lang"]=$defaultLanguage;
			}
			else
			{
				$new_datas[$i]["lang"] = $map["options"];
				$new_datas[$i]["options"] = "";
			}
		}
		
		return $new_datas;
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