<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2017, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.7.1.2
 *
 **/
$action = Tools::getValue('action');

if (isset($action) && $action) {

	switch($action) {
		case 'mapping_saveas':
			$filename=str_replace('.map.xml','',Tools::getValue('filename'));
			$mapping=Tools::getValue('mapping');
			@unlink(SC_TOOLS_DIR.'cat_export/'.$filename.'.map.xml');

			$mapping=preg_split('/;/',$mapping);
			$content="<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n".'<mapping version="'.SC_EXPORT_VERSION.'">';
			$contentArray=array();
			foreach($mapping AS $map)
			{
				$val=explode('|||',$map);
				if (count($val)==8)
				{
					$contentArray[(int)$val[1]]='<field>';
					$contentArray[(int)$val[1]].='<id><![CDATA['.$val[0].']]></id>';
					$contentArray[(int)$val[1]].='<used><![CDATA['.$val[2].']]></used>';
					$contentArray[(int)$val[1]].='<name><![CDATA['.$val[3].']]></name>';
					$contentArray[(int)$val[1]].='<lang><![CDATA['.$val[4].']]></lang>';
					$contentArray[(int)$val[1]].='<options><![CDATA['.$val[5].']]></options>';
					//$contentArray[(int)$val[1]].='<filters><![CDATA['.$val[6].']]></filters>';
					$contentArray[(int)$val[1]].='<modifications><![CDATA['.$val[6].']]></modifications>';
					$contentArray[(int)$val[1]].='<column_name><![CDATA['.$val[7].']]></column_name>';
					$contentArray[(int)$val[1]].='</field>'."\n";
				}
			}
			ksort($contentArray);

			$content.=join('',$contentArray).'</mapping>';
			file_put_contents(SC_TOOLS_DIR.'cat_export/'.$filename.'.map.xml', $content);
			echo _l('Data saved!');
			break;
		case 'mapping_delete':
			$filename=str_replace('.map.xml','',Tools::getValue('filename'));
			@unlink(SC_TOOLS_DIR.'cat_export/'.$filename.'.map.xml');
			echo _l('File deleted');
			break;
	}
}
?>
