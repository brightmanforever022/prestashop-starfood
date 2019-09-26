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
	 *Session Action Class 
	 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
	 * @link www.phpletter.com
	 * @since 22/May/2007
	 *
	 */
	class SessionAction
	{
		var $actionIndex = 'ajax_file_action';
		var $selectedDocIndex = 'ajax_selected_doc';
		var $fromFolderIndex = 'ajax_from_folder';
		function __construct()
		{
			if (!isset($_SESSION[$this->actionIndex]))
			{
				$_SESSION[$this->actionIndex] = '';
			}
			if (!isset($_SESSION[$this->selectedDocIndex]) || !is_array($_SESSION[$this->selectedDocIndex]))
			{
				$_SESSION[$this->selectedDocIndex] = array();
			}
			if (!isset($_SESSION[$this->fromFolderIndex]))
			{
				$_SESSION[$this->fromFolderIndex] = '';
			}
		}
		
		function SessionAction()
		{
			$this->__construct();
		}
		/**
		 * count the  number of selected documents
		 *
		 */
		function count()
		{
			return (isset($_SESSION[$this->selectedDocIndex])?sizeof($_SESSION[$this->selectedDocIndex]):0);
		}
		/**
		 * assign the selected documents
		 *
		 * @param array $selectedDocuments
		 */
		function set($selectedDocuments)
		{
			$_SESSION[$this->selectedDocIndex] = $selectedDocuments;

		}
		/**
		 * get the selected documents
		 * @return array
		 */
		function get()
		{
			return (isset($_SESSION[$this->selectedDocIndex])?$_SESSION[$this->selectedDocIndex]:array());
		}
		
		function setAction($action)
		{
			$_SESSION[$this->actionIndex] = $action;			
		}
		/**
		 * get the action
		 *
		 * @return unknown
		 */
		function getAction()
		{
			return (isset($_SESSION[$this->actionIndex])?$_SESSION[$this->actionIndex]:'');
		}
		/**
		 * set the folder
		 *
		 * @param string $folder
		 */
		function setFolder($folder)
		{
			$_SESSION[$this->fromFolderIndex] = $folder;
		}
		/**
		 * get the folder
		 *
		 * @return string
		 */
		function getFolder()
		{
			return (isset($_SESSION[$this->fromFolderIndex])?$_SESSION[$this->fromFolderIndex]:'');
		}
	}
?>