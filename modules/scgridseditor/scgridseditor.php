<?php
/**
* SC Grids Editor
*
* @category administration
* @author Store Commander <support@storecommander.com>
* @copyright 2009-2017 Store Commander
* @version 1.4
* @license commercial
*
**************************************
**          SC Grids Editor          *
**   http://www.StoreCommander.com   *
**              V 1.4                *
**************************************
* +
* +Languages: EN, FR
* +PS version: 1.2 to 1.7
* */

class SCGridsEditor extends Module {

	private $_html;

	public function __construct()
	{
		$this->name		= 'scgridseditor';
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')){
			$this->tab = 'administration';
		}
		else{
			$this->tab = 'Store Commander';
		}
		$this->version	= '1.4';
		$this->author	= 'Store Commander';
		parent::__construct();
		$this->bootstrap = true;
		$this->page										= basename(__FILE__, '.php');
		$this->displayName 						= $this->l('Grids Editor');
		$this->description 						= $this->l('Add and update views in Store Commander');
		$this->scfolder								= Configuration::get('SC_FOLDER_HASH');
		$this->currentExtensionFolder	= 'win_grids_editor';

		$errorMessageFolderSC = ' '.$this->l('This folder must be writable:').' /modules/storecommander/'.$this->scfolder.'/SC_TOOLS';
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>=')) {
			if(Configuration::get('SC_INSTALLED') && (is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder) && !is_writeable(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS')))
				$this->_errors[] = $errorMessageFolderSC;
		} else {
			$warning='';
			if(Configuration::get('SC_INSTALLED') && (is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder) && !is_writeable(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS')))
				$warning .= $errorMessageFolderSC;
			if ($warning!='')
				$this->warning = $warning;
		}

	}

	public function install()
	{
		if (version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
			if (isset($this->_errors) && $this->_errors) {
				return false;
			}
		}
		
		if (!parent::install())
			return false;
		if (Configuration::get('SC_INSTALLED') && (!$this->createCurrentExtensionFolder() || !$this->createCurrentExtensionContent()))
			return false;
		Configuration::updateValue('SC_GRIDSEDITOR_INSTALLED',1);
		return true;
	}

	public function uninstall()
	{
		Configuration::updateValue('SC_GRIDSEDITOR_INSTALLED',0);
		if (Configuration::get('SC_INSTALLED'))
			$this->removeCurrentExtensionFolder();
		parent::uninstall();
		return true;
	}

	// recursive copy of directory
	private function recursive_copy($src,$dst) { 
    $dir = opendir($src); 
    @mkdir($dst); 
    while(false !== ( $file = readdir($dir)) ) { 
        if (( $file != '.' ) && ( $file != '..' )) { 
            if ( is_dir($src . '/' . $file) ) { 
                $this->recursive_copy($src . '/' . $file,$dst . '/' . $file); 
            } 
            else { 
                copy($src . '/' . $file,$dst . '/' . $file); 
            } 
        } 
    } 
    closedir($dir); 
} 

	// recursive delete of directory
	private function rrmdir($dir)
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object)
				if ($object != "." && $object != "..")
					if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
			reset($objects);
			rmdir($dir);
		}
		return true;
	}

	private function createCurrentExtensionFolder()
	{
		if ($this->scfolder != '' && is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder))
		{
			if (!is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder))
				return mkdir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder);
		}else{
			if (is_dir('SC_TOOLS'))
				return mkdir('SC_TOOLS/'.$this->currentExtensionFolder);
		}
		return false;
	}

	private function removeCurrentExtensionFolder()
	{
		if (is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder))
			$this->rrmdir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder);
		return true;
	}

	private function createCurrentExtensionContent()
	{
		if (is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder))
		{
			$this->recursive_copy(dirname(__FILE__).'/data/'.$this->currentExtensionFolder,_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder);
			if (file_exists(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC_TOOLS/'.$this->currentExtensionFolder.'/index.htm'))
				return true;
		}else{
			if (is_dir('SC_TOOLS'))
				$this->recursive_copy(dirname(__FILE__).'/data/'.$this->currentExtensionFolder,'SC_TOOLS/'.$this->currentExtensionFolder);
			if (file_exists('SC_TOOLS/'.$this->currentExtensionFolder.'/index.htm'))
				return true;
		}
		return false;
	}
	
	function getContent()
	{
		if(version_compare(_PS_VERSION_, "1.5", '>='))
			$cookie = $this->context->cookie;
		else
			global $cookie;
		
		$currentFileName = array_reverse(explode("/", $_SERVER['SCRIPT_NAME']));
		$psadminpath=$currentFileName[1];
		$datelastregen=Db::getInstance()->getValue('SELECT last_passwd_gen FROM '._DB_PREFIX_.'employee WHERE id_employee='.intval($cookie->id_employee));

		if(version_compare(_PS_VERSION_, "1.6.0.0", '>=')) {
			$this->_html .= '<div class="panel"><fieldset>
							<legend>'.$this->l('Grids editor').'</legend>';
		} else {
			$this->_html .= '<fieldset style="width: 45%; float: left;">
							<legend>'.$this->l('Grids editor').'</legend><br/><br/>';
		}

		if(is_dir(_PS_ROOT_DIR_.'/modules/storecommander/'.$this->scfolder.'/SC'))
			$this->_html .= '
				<strong>'.$this->l('Have you modified grids and or added customized grids with our module?').'<br/>
				'.$this->l('However, you have made a configuration mistake or change a setting affecting Store Commander to work correctly?').'</strong><br/><br/>
				'.$this->l('To fix these issues, you can access Store Commander without your customization by').' <a href="../modules/storecommander/'.$this->scfolder.'/SC/index.php?ide='.$cookie->id_employee.'&psap='.$psadminpath.'&key='.md5($cookie->id_employee.$datelastregen).(version_compare(_PS_VERSION_, '1.4.0.0', '>=')?'':'&id_lang='.$cookie->id_lang).'&setextension=0" target="_blank">'.$this->l('clicking here').'</a>
				<br/><br/>
				<strong>'.$this->l('Normal operation can be restored from Store Commander > Tools > Settings > Use SC Extensions = 1').'</strong>
				<br/>';
		else
			$this->_html .= '<br/><br/>
				'.$this->l('Your Store Commander should be installed in the modules folder of Prestashop. Please read this article:').' '.
				($cookie->id_lang == Language::getIdByIso('fr') ? '<a href="http://www.storecommander.com/redir.php?dest=9467812" target="_blank">cliquez ici</a>':
																													'<a href="http://www.storecommander.com/redir.php?dest=9467814" target="_blank">click here</a>').
				'<br/><br/><br/>';

		if(version_compare(_PS_VERSION_, "1.6.0.0", '>=')) {
			$this->_html .= '</fieldset></div>';
		} else {
			$this->_html .= '</fieldset>';
		}

		return $this->_html;
	}
}
