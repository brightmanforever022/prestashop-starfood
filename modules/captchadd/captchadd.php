<?php
/**
 * 2008-2014 Librasoft
 *
 *  For support feel free to contact us on our website at http://www.librasoft.fr/
 *
 *  @author    Librasoft <contact@librasoft.fr>
 *  @copyright 2008-2014 Librasoft
 *  @version   1.0
 *  @license   One Paid Licence By WebSite Using This Module. No Rent. No Sell. No Share.
 */

if (!defined('_PS_VERSION_'))
	exit;
class Captchadd extends Module
{
	public function __construct()
	{
		$this->name = 'captchadd';
		$this->tab = 'front_office_features';
		$this->version = '1.0';
		$this->author = 'Librasoft';
		$this->module_key = '711e525b16bb4e5abac003204b46de2e';
		$this->bootstrap = false;

		parent::__construct();

		$this->displayName = $this->l('Captcha Add');
		$this->description = $this->l('Protect your shop from spammers with secured captcha');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall this module and delete ALL your parameters ?');

		/* Backward compatibility */
		require(_PS_MODULE_DIR_.$this->name.'/backward_compatibility/backward.php');
	}
	public function install()
	{
		/* APPLICATION D'UN CHMOD 0755 AU REPERTOIRE DU MODULE */
		@chmod(dirname(__FILE__), 0755);
		$this->chmodr(dirname(__FILE__).'/', '');

		/* INSTALLATION DU MODULE */
		if (!parent::install() || !$this->registerHook('createAccountForm'))
			return false;

		/* CREATION DES VARIABLES PARAMETRES */
		Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA', '');
		Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA2', '');
		Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA3', '');
		Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA4', '');
		Configuration::updateValue('CAPTCHADD_TYPECAPTCHA', '');

		/* INSTALLATION DE L'OVERRIDE POUR PRESTASHOP V1.4 */
		if ((_PS_VERSION_ < '1.5'))
		{
			if (!copy('../modules/captchadd/v1.4/ContactController.php', '../override/controllers/ContactController.php'))
				return false;
			if (!copy('../modules/captchadd/v1.4/AuthController.php', '../override/controllers/AuthController.php'))
				return false;
		}

		/* RESET DU CACHE PRESTASHOP */
		$this->clearCache();

		/* TOUT S'EST BIEN PASSE */
		return true;
	}
	public function uninstall()
	{
		/* SUPPRESSION DES PARAMETRES DU MODULE */
		Configuration::deleteByName('CAPTCHADD_BOOLCAPTCHA');
		Configuration::deleteByName('CAPTCHADD_BOOLCAPTCHA2');
		Configuration::deleteByName('CAPTCHADD_BOOLCAPTCHA3');
		Configuration::deleteByName('CAPTCHADD_BOOLCAPTCHA4');
		Configuration::deleteByName('CAPTCHADD_TYPECAPTCHA');

		/* DESINSTALLATION DU MODULE */
		if (!parent::uninstall() || !$this->unregisterHook('createAccountForm'))
			return false;

		/* DESINSTALLATION DE L'OVERRIDE POUR PRESTASHOP V1.4 */
		if ((_PS_VERSION_ < '1.5'))
		{
			if (!unlink('../override/controllers/ContactController.php'))
				return false;
			if (!unlink('../override/controllers/AuthController.php'))
				return false;
		}

		/* DESINSTALLATION DES PATCHS */
		$this->patchContactThemeFile('uninstall');
		$this->patchProductCommentTplFile('uninstall');

		/* RESET DU CACHE PRESTASHOP */
		$this->clearCache();

		/* TOUT S'EST BIEN PASSE */
		return true;
	}
	private function patchContactThemeFile($action = 'check')
	{
		require_once('ClassFilePatching.php');
		$myfile = new LbsFilePatching();
		$myfile->load(_PS_THEME_DIR_.'/contact-form.tpl');
		/* On spécifie la modification souhaitée */
		$buffertoadd = "{include file='"._PS_MODULE_DIR_."captchadd/views/templates/hook/captchadd.tpl'}";
		$myfile->setaction('addbefore', '<div class="submit"', $buffertoadd);
		if ($action == 'uninstall')	// On veut désinstaller
		{
			if (!$myfile->uninstall())
				return false;
			else
			{
				$myfile->setaction('addbefore', '<p class="submit"', $buffertoadd);
				if (!$myfile->uninstall())
					return false;
				else
				{
					$myfile->setaction('addbefore', '<input type="submit"', $buffertoadd);
					if (!$myfile->uninstall())
						return false;
				}
			}
		}

		if ($action == 'install')	// On veut installer
		{
			if (!$myfile->install())
			{
				$myfile->setaction('addbefore', '<p class="submit"', $buffertoadd);
				if (!$myfile->install())
				{
					$myfile->setaction('addbefore', '<input type="submit"', $buffertoadd);
					if (!$myfile->install())
						return false;
				}
			}
		}

		if ($action == 'check')     // On veut checker l'install
		{
			if (!$myfile->isinstalled())
			{
				$myfile->setaction('addbefore', '<p class="submit"', $buffertoadd);
				if (!$myfile->isinstalled())
				{
					$myfile->setaction('addbefore', '<input type="submit"', $buffertoadd);
					if (!$myfile->isinstalled())
						return false;
					else
						return true;
				}
				else
					return true;
			}
			else
				return true;
		}

		return $myfile->save(true, '');
	}
	private function patchProductCommentTplFile($action = 'check')
	{
		require_once('ClassFilePatching.php');
		$myfile = new LbsFilePatching();
		if ($myfile->load(_PS_THEME_DIR_.'/modules/productcomments/productcomments.tpl'))
		{
			/* On spécifie la modification souhaitée */
			$buffertoadd = "{include file='"._PS_MODULE_DIR_."captchadd/views/templates/hook/captchadd.tpl'}";
			$myfile->setaction('addbefore', '<div id="new_comment_form_footer">', $buffertoadd);
			if ($action == 'uninstall')	// On veut désinstaller
			{
				if (!$myfile->uninstall())
					return false;
			}

			if ($action == 'install')	// On veut installer
			{
				if (!$myfile->install())
					return false;
			}

			if ($action == 'check')     // On veut checker l'install
			{
				if (!$myfile->isinstalled())
					return false;
				else
					return true;
			}

			return $myfile->save(true, '');
		}
		else
			return false;
	}
	private function patchProductCommentControllerFile($action = 'check')
	{
		require_once('ClassFilePatching.php');
		$myfile = new LbsFilePatching();
		if ($myfile->load(_PS_MODULE_DIR_.'/productcomments/controllers/front/default.php'))
		{
			/* On spécifie la modification souhaitée */
			$buffertoadd = "include(_PS_MODULE_DIR_.'captchadd/testcaptcha.php');";
			$myfile->setaction('addafter', '$errors = array();', $buffertoadd);
			if ($action == 'uninstall')	// On veut désinstaller
			{
				if (!$myfile->uninstall())
					return false;
			}

			if ($action == 'install')	// On veut installer
			{
				if (!$myfile->install())
					return false;
			}

			if ($action == 'check')     // On veut checker l'install
			{
				if (!$myfile->isinstalled())
					return false;
				else
					return true;
			}

			return $myfile->save(true, '');
		}
		else
			return false;
	}
	public function getContent()
	{
		$output = '';
		if (Tools::isSubmit('confirmSetting'))
		{
			Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA', Tools::getValue('ajcaptcha'));
			Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA2', Tools::getValue('ajcaptcha2'));
			Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA3', Tools::getValue('ajcaptcha3'));
			Configuration::updateValue('CAPTCHADD_BOOLCAPTCHA4', Tools::getValue('ajcaptcha4'));
			Configuration::updateValue('CAPTCHADD_TYPECAPTCHA', Tools::getValue('typeCaptcha'));

			if (Configuration::get('CAPTCHADD_BOOLCAPTCHA2') == 1)
				$this->patchContactThemeFile('install');
			if (Configuration::get('CAPTCHADD_BOOLCAPTCHA2') == '')
				$this->patchContactThemeFile('uninstall');
			if (Configuration::get('CAPTCHADD_BOOLCAPTCHA3') == 1)
			{
				$this->patchProductCommentTplFile('install');
				$this->patchProductCommentControllerFile('install');
			}
			if (Configuration::get('CAPTCHADD_BOOLCAPTCHA3') == '')
			{
				$this->patchProductCommentTplFile('uninstall');
				$this->patchProductCommentControllerFile('uninstall');
			}
			$this->clearCache();
			$output .= $this->displayConfirmation($this->l('Settings saved'));
		}
		return $output.$this->displayForm();
	}
	public function displayForm()
	{
		$output = '<h2>Captcha Add</h2><form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset class="space"><legend><img src="../img/admin/cog.gif">'.$this->l('Settings').'</legend>';
		$captcha = '';
		$captcha2 = '';
		$captcha3 = '';
		$captcha4 = '';
		$type1 = '';
		$type2 = '';
		if (Configuration::get('CAPTCHADD_BOOLCAPTCHA') == 1)
			$captcha = 'checked=checked';
		if (Configuration::get('CAPTCHADD_BOOLCAPTCHA2') == 1)
			$captcha2 = 'checked=checked';
		if (Configuration::get('CAPTCHADD_BOOLCAPTCHA3') == 1)
			$captcha3 = 'checked=checked';
		if (Configuration::get('CAPTCHADD_BOOLCAPTCHA4') == 1)
			$captcha4 = 'checked=checked';
		if (Configuration::get('CAPTCHADD_TYPECAPTCHA') == 1)
			$type1 = 'checked=checked';
		if (Configuration::get('CAPTCHADD_TYPECAPTCHA') == 2)
			$type2 = 'checked=checked';

		$output .= '<p><b>'.$this->l('Captcha locations')." :</b></p><table>
		<tr><td><input type='checkbox' name='ajcaptcha'".$captcha." value='1' style='margin-right:5px'></td><td> "
			.$this->l('Activate the captcha on the user creation form')."</td></tr>
		<tr><td><input type='checkbox' name='ajcaptcha2'".$captcha2." value='1' style='margin-right:5px'></td><td> "
			.$this->l('Activate the captcha on the contact form').'</td></tr>';
		/* Ajout des fonctionnalités pour les commentaires produits et commentaires blogs seulement pour v1.5 et + */
		if (_PS_VERSION_ > '1.5')
		{
			$output .= "<tr><td><input type='checkbox' name='ajcaptcha3'".$captcha3." value='1' style='margin-right:5px'></td><td> "
				.$this->l('Activate the captcha on the product comments form')."</td></tr>
		<tr><td><input type='checkbox' name='ajcaptcha4'".$captcha4." value='1' style='margin-right:5px' disabled='disabled'></td><td> <i>"
				.$this->l('Activate the captcha on the PS Blog comments form').' '.$this->l('(Available in the next version)').'</i></td></tr>';
		}
		$output .= '</table><br>
		<p><b>'.$this->l('Captcha type')." :</b></p>
		<p><input type='radio' name='typeCaptcha'".$type1." value='1'>&nbsp;"
			.$this->l('Mathematical')."&nbsp;<input type='radio' ".$type2." name='typeCaptcha' value='2'>&nbsp;"
			.$this->l('Numbers and letters').'</p>';
		$output .= '</fieldset><br /><input type="submit" name="confirmSetting" value="'
			.$this->l('Save Settings').'" class="button" /></form></fieldset>';
		return $output.$this->displayModuleFooter();
	}
	public function hookcreateAccountForm()
	{
		if (Configuration::get('CAPTCHADD_BOOLCAPTCHA') == 1)
		{
			include_once(_PS_MODULE_DIR_.'captchadd/securimage/securimage.php');
			return $this->display(__FILE__, 'captchadd.tpl');
		}
		else
			return '';
	}

	/* LIBRASOFT REF FUNCTIONS */
	private function deleteDirectory($dirname, $delete_self = true)
	{
		$dirname = rtrim($dirname, '/').'/';
		if (file_exists($dirname))
			if ($files = scandir($dirname))
			{
				foreach ($files as $file)

					if ($file != '.' && $file != '..' && $file != '.svn')
					{
						if (is_dir($dirname.$file))
							$this->deleteDirectory($dirname.$file, true);
						elseif (file_exists($dirname.$file))
							unlink($dirname.$file);
					}
				if ($delete_self)
					if (!rmdir($dirname))
						return false;
				return true;
			}
		return false;
	}
	private function deleteFile($file, $exclude_files = array())
	{
		if (isset($exclude_files) && !is_array($exclude_files))
			$exclude_files = array($exclude_files);

		if (file_exists($file) && is_file($file) && array_search(basename($file), $exclude_files) === false)
			unlink($file);
	}
	private function clearCache()
	{
		/* Reset du cache Smarty */
		foreach (array(_PS_CACHE_DIR_.'smarty/cache', _PS_CACHE_DIR_.'smarty/compile') as $dir)
			if (file_exists($dir))
				foreach (scandir($dir) as $file)
					if ($file[0] != '.' && $file != 'index.php')
						$this->deleteDirectory($dir.DIRECTORY_SEPARATOR.$file);

		/* Reset du cache XML */
		foreach (scandir(_PS_ROOT_DIR_.'/config/xml') as $file)
			if ((pathinfo($file, PATHINFO_EXTENSION) == 'xml') && ($file != 'default.xml'))
				$this->deleteFile(_PS_ROOT_DIR_.'/config/xml/'.$file);

		/* Reset du cache Media */
		foreach (array(_PS_THEME_DIR_.'cache') as $dir)
			if (file_exists($dir))
				foreach (scandir($dir) as $file)
					if ($file[0] != '.' && $file != 'index.php')
						$this->deleteFile($dir.DIRECTORY_SEPARATOR.$file, array('index.php'));
		$version = (int)Configuration::get('PS_CCCJS_VERSION');
		Configuration::updateValue('PS_CCCJS_VERSION', ++$version);
		$version = (int)Configuration::get('PS_CCCCSS_VERSION');
		Configuration::updateValue('PS_CCCCSS_VERSION', ++$version);

		/* Reset du cache de la classe autoload */
		if (_PS_VERSION_ > '1.6')
			PrestaShopAutoload::getInstance()->generateIndex();
		elseif (_PS_VERSION_ > '1.5')
			Autoload::getInstance()->generateIndex();
	}
	private function displayModuleFooter()
	{
		return '</BR>
		<STYLE type="text/css">
			.librasoft {
			background: #e0f3fa; /* Old browsers */
			background: -moz-linear-gradient(45deg, #e0f3fa 0%, #d8f0fc 50%, #b8e2f6 51%, #b6dffd 100%); /* FF3.6+ */
			background: -webkit-gradient(linear, left bottom, right top, color-stop(0%,#e0f3fa), color-stop(50%,#d8f0fc),
			color-stop(51%,#b8e2f6), color-stop(100%,#b6dffd)); /* Chrome,Safari4+ */
			background: -webkit-linear-gradient(45deg, #e0f3fa 0%,#d8f0fc 50%,#b8e2f6 51%,#b6dffd 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(45deg, #e0f3fa 0%,#d8f0fc 50%,#b8e2f6 51%,#b6dffd 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(45deg, #e0f3fa 0%,#d8f0fc 50%,#b8e2f6 51%,#b6dffd 100%); /* IE10+ */
			background: linear-gradient(45deg, #e0f3fa 0%,#d8f0fc 50%,#b8e2f6 51%,#b6dffd 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr=\'#e0f3fa\', endColorstr=\'#b6dffd\',GradientType=1 );
			}
		</style>
		<fieldset class="librasoft">
			<legend><img src="../img/admin/unknown.gif" alt="" class="middle" />'.$this->l('Help').'</legend>
			'.$this->l('Documentations').' :<BR/>
			<BR/>   <a href="../modules/'.$this->name.'/readme_fr.pdf" target="_blank" style="font-weight: bold;">
			<img src="../img/admin/pdf.gif"> Télécharger le Guide utilisateur en Français</a>
			<BR/><BR/>   <a href="../modules/'.$this->name.'/readme_en.pdf" target="_blank" style="font-weight: bold;">
			<img src="../img/admin/pdf.gif"> Download the User Guide in English</a>
			<BR/><BR/><BR/>'.$this->l('For any support, please feel free to contact us on our website at ')
		.' <a href="http://www.librasoft.fr/" target="_blank" style="font-weight: bold;">http://www.librasoft.fr/</a>
			<BR/><BR/>'.$this->l('You Can find ').' <a href="http://addons.prestashop.com/fr/search.php?id_category_search=0&search_query=librasoft"
			target="_blank" style="font-weight: bold;">'.$this->l('HERE ').'</a> '.$this->l('all our others PrestaShop modules.').'
			<BR/><BR/><a href="http://www.librasoft.fr/" target="_blank"><img style="width:180px; height:80 ;" src="../modules/'
		.$this->name.'/img/LibraSoftLogoModule.png" alt="Librasoft"></a>
			<BR/><BR/>Copyright 2008-'.date('Y').' Librasoft.
		</fieldset>';
	}
	private function chmodr($rep, $ssrep)
	{
		if ($dir = opendir($rep))
		{
			while (false !== ($fich = readdir($dir)))
			{
				if ($fich != '.' && $fich != '..')
				{
					$chemin = "$rep$fich";
					if (is_dir($chemin))
						$this->chmodr($chemin.'/', ($ssrep == ''?$fich:$ssrep.'/'.$fich));
					@chmod($chemin, 0755);
				}
			}
		}
	}
}