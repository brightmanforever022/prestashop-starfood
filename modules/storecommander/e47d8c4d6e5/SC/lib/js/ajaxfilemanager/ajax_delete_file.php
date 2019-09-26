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
	/**
	 * delete selected files
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/April/2007
	 *
	 */
	require_once(dirname(__FILE__) . DIRECTORY_SEPARATOR . "inc" . DIRECTORY_SEPARATOR . "config.php");
	$error = "";
	if (CONFIG_SYS_VIEW_ONLY || !CONFIG_OPTIONS_DELETE)
	{
		$error = SYS_DISABLED;
	}
	elseif (!empty($_GET['delete']))
	{//delete the selected file from context menu
		if (!file_exists($_GET['delete']))
		{
			$error = ERR_FILE_NOT_AVAILABLE;
		}
		elseif (!isUnderRoot($_GET['delete']))
		{
			$error = ERR_FOLDER_PATH_NOT_ALLOWED;
		}else
		{
				include_once(CLASS_FILE);
				$file = new file();
				if (is_dir($_GET['delete'])
					 &&  isValidPattern(CONFIG_SYS_INC_DIR_PATTERN, getBaseName($_GET['delete'])) 
					 && !isInvalidPattern(CONFIG_SYS_EXC_DIR_PATTERN, getBaseName($_GET['delete'])))
					{
						$file->delete(addTrailingSlash(backslashToSlash($_GET['delete'])));
					}elseif (is_file($_GET['delete']) 
					&& isValidPattern(CONFIG_SYS_INC_FILE_PATTERN, getBaseName($_GET['delete']))
					&& !isInvalidPattern(CONFIG_SYS_EXC_FILE_PATTERN, getBaseName($_GET['delete']))
					)
					{
						$file->delete(($_GET['delete']));
					}			
		}
	}else 
	{
		if (!isset($_POST['selectedDoc']) || !is_array($_POST['selectedDoc']) || sizeof($_POST['selectedDoc']) < 1)
		{
			$error = ERR_NOT_FILE_SELECTED;
		}
		else 
		{

			include_once(CLASS_FILE);
			$file = new file();
			
			foreach($_POST['selectedDoc'] as $doc)
			{
				if (file_exists($doc) && isUnderRoot($doc))
				{
					if (is_dir($doc)
					 &&  isValidPattern(CONFIG_SYS_INC_DIR_PATTERN, $doc) 
					 && !isInvalidPattern(CONFIG_SYS_EXC_DIR_PATTERN, $doc))
					{
						$file->delete(addTrailingSlash(backslashToSlash($doc)));
					}elseif (is_file($doc) 
					&& isValidPattern(CONFIG_SYS_INC_FILE_PATTERN, $doc)
					&& !isInvalidPattern(CONFIG_SYS_EXC_FILE_PATTERN, $doc)
					)
					{
						$file->delete($doc);
					}					
				}

				
			}
		}		
	}

	echo "{error:'" . $error . "'}";
?>