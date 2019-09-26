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
			@unlink(SC_CSV_IMPORT_DIR.$filename.'.map.xml');
			$mapping=Tools::getValue('mapping','');
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

			file_put_contents(SC_CSV_IMPORT_DIR.$filename.'.map.xml', $content);
			echo _l('Data saved!');
			break;
		case 'mapping_delete':
			$filename=str_replace('.map.xml','',Tools::getValue('filename'));
			@unlink(SC_CSV_IMPORT_DIR.$filename.'.map.xml');
			break;
	}
}
?>
