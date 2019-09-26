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
 * the purpose I added this class is to make the file system much flexible 
 * for customization.
 * Actually,  this is a kind of interface and you should modify it to fit your system
 * @author Logan Cai (cailongqun [at] yahoo [dot] com [dot] cn)
 * @link www.phpletter.com
 * @since 4/August/2007
 */
	class Auth
	{
		var $__loginIndexInSession = 'ajax_user';
		function __construct()
		{
			
		}
		
		function Auth()
		{
			$this->__construct();
		}
		/**
		 * check if the user has logged
		 *
		 * @return boolean
		 */
		function isLoggedIn()
		{
			return (!empty($_SESSION[$this->__loginIndexInSession])?true:false);
		}
		/**
		 * validate the username & password
		 * @return boolean
		 *
		 */
		function login()
		{
			if ($_POST['username'] == CONFIG_LOGIN_USERNAME && $_POST['password'] == CONFIG_LOGIN_PASSWORD)
			{
				$_SESSION[$this->__loginIndexInSession] = true;
				return true;
			}else 
			{
				return false;
			}
		}
	}
?>