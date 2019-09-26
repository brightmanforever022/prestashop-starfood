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


class ImportConvert
{
	public static function convertActionSettings($files)
	{
		global $importConfig;
		$needConvert = false;
		foreach ($files AS $file)
		{
			if (strtolower(substr($file,strlen($file)-4,4))=='.csv' && strpos($file,'&')===false)
			{
				if (!isset($importConfig[$file]['iffoundindb']) || $importConfig[$file]['iffoundindb'] == '') continue;
				if ($importConfig[$file]['iffoundindb'] == 'skip')
				{
					$importConfig[$file]['fornewproduct'] = "skip";
					$importConfig[$file]['forfoundproduct'] = "skip";
				}
				if ($importConfig[$file]['iffoundindb'] == 'replace')
				{
					$importConfig[$file]['fornewproduct'] = "create";
					$importConfig[$file]['forfoundproduct'] = "update";
				}
				if ($importConfig[$file]['iffoundindb'] == 'replaceonly')
				{
					$importConfig[$file]['fornewproduct'] = "skip";
					$importConfig[$file]['forfoundproduct'] = "update";
				}
				if ($importConfig[$file]['iffoundindb'] == 'create')
				{
					$importConfig[$file]['fornewproduct'] = "create";
					$importConfig[$file]['forfoundproduct'] = "create";
				}
				if(empty($importConfig[$file]['fornewproduct']))
					$importConfig[$file]['fornewproduct'] = "skip";
				if(empty($importConfig[$file]['forfoundproduct']))
					$importConfig[$file]['forfoundproduct'] = "skip";
				$needConvert = true;
			}
		}
		if ($needConvert)
			writeImportConfigXML();
	}

}