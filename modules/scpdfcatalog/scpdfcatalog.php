<?php
/**
 * PDF Catalog
 *
 * @category migration_tools
 * @author Store Commander <support@storecommander.com>
 * @copyright 2009-2015 Store Commander
 * @version 2.6.2
 * @license commercial
 *
 **************************************
 **           PDF Catalog             *
 **   http://www.StoreCommander.com   *
 **            V 2.6.2                *
 **************************************
 * +
 * +Languages: EN, FR
 * +PS version: 1.2
 * */

class SCPDFCatalog extends Module
{
	public $carriernames = array();
	public $paymentnames = array();
	public $cataloglang = 1;
	private $excludedFiles=Array( ".", "..", ".DS_Store", ".htaccess", "index.php");
	
	public static $translations = array();
	
	public function __construct()
	{
		$this->name = 'scpdfcatalog';
		$this->author = 'Store Commander';
		if (version_compare(_PS_VERSION_, '1.4.0.0', '>=')){
			$this->tab = 'advertising_marketing';
		}
		else{
			$this->tab = 'Store Commander';
		}
		$this->version = '2.6.2';
		$this->module_key = '84f9114aad31d691c9c5a9b2cd0533dc';
		parent::__construct(); /* The parent construct is required for translations */
		$this->page = basename(__FILE__, '.php');
		$this->displayName = $this->l('PDF Catalog');
		$this->description = $this->l('This module installs the Modules => PDF Catalog tab');
		$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');
		$this->currencies = false;
		
		$this->langs = Language::getLanguages();
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('leftColumn') OR !$this->registerHook('rightColumn'))
			return false;		
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<')){
			if (!@copy(dirname(__FILE__).'/AdminSCPDFCatalog.php',realpath(dirname('index.php')).'/tabs/AdminSCPDFCatalog.php')){
				die($this->l('This folder must be writable to install the module: ').realpath(dirname('index.php')).'/tabs/');
				return false;
			}
			if (!@copy(dirname(__FILE__).'/SCPDFCatalogCreator.php',_PS_CLASS_DIR_.'SCPDFCatalogCreator.php')){
				die($this->l('This folder must be writable to install the module: ')._PS_CLASS_DIR_);
				return false;
			}
			if (!@copy(dirname(__FILE__).'/SCPDF.php',getcwd().'/SCPDF.php')){
				die($this->l('This folder must be writable to install the module: ').getcwd());
				return false;
			}
		}
		$tab = new Tab();
		$tab->class_name = 'AdminSCPDFCatalog';
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')){
			$tab->id_parent = (int)(Tab::getIdFromClassName('AdminParentModules'));
		}else{
		$tab->id_parent = (int)(Tab::getIdFromClassName('AdminModules'));
		}
		$tab->module = $this->name;
		$tab->name[Configuration::get('PS_LANG_DEFAULT')] = 'PDF Catalog';
		$tab->name[Language::getIdByIso('en')] = 'PDF Catalog';
		$tab->name[Language::getIdByIso('fr')] = 'Catalogue PDF';
		if(!$tab->add()) return false;			
		$idTab = Tab::getIdFromClassName('AdminSCPDFCatalog');
		if (version_compare(_PS_VERSION_, '1.4.0.0', '<')){
			if (!@copy(dirname(__FILE__).'/icon1.gif',_PS_IMG_DIR_.'t/'.$idTab.'.gif'))
				return false;
			if (!@copy(dirname(__FILE__).'/icon1.gif',_PS_IMG_DIR_.'t/AdminSCPDFCatalog.gif'))
				return false;
		}
		return true;
	}

	public function uninstall()
	{
		$tab = new Tab(Tab::getIdFromClassName('AdminSCPDFCatalog'));
		if (!$tab->delete()) return false;
		@unlink(realpath(dirname('index.php')).'/tabs/AdminSCPDFCatalog.php');
		@unlink(_PS_CLASS_DIR_.'/SCPDFCatalogCreator.php');
		@unlink(getcwd().'/SCPDF.php');
		$idTab = Tab::getIdFromClassName('AdminSCPDFCatalog');
		@unlink(_PS_IMG_DIR_.'t/'.$idTab.'.gif');
		@unlink(_PS_IMG_DIR_.'t/AdminSCPDFCatalog.gif');
		if (!parent::uninstall())
			return false;
		return true;
	}

	public function getContent()
	{
		global $cookie;
		if(Tools::getValue('submitBlock')){			
			self::updateValue('PS_SC_PDFCATALOG_BLOCKDISPLAY', Tools::getValue('blockdisplay'));
			self::updateValue('PS_SC_PDFCATALOG_BLOCKTITLE', Tools::getValue('divblocktitle'));
			self::updateValue('PS_SC_PDFCATALOG_BLOCKCONTENT', Tools::getValue('divblockcontent'),true);
		}
		
		$output = '<h2>'.$this->displayName.'</h2>';
		$output .= '
		<form action="'.$_SERVER['REQUEST_URI'].'" method="post">';
		// block display
		$blockdisplay = Configuration::get('PS_SC_PDFCATALOG_BLOCKDISPLAY');
		$blocktitle = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKTITLE');
		$blockcontent = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKCONTENT');
		$deflang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$intDivs = 'divblocktitle¤divblockcontent';
		$output .= '
		<div style="clear: both;">
			<label>'.$this->t('Display:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="blockdisplay">
					<option value="0" '.($blockdisplay == '0' ? 'selected="selected"' : '').'>'.$this->t('Not displayed').'</option>
					<option value="1" '.($blockdisplay == '1' ? 'selected="selected"' : '').'>'.$this->t('Left column').'</option>
					<option value="2" '.($blockdisplay == '2' ? 'selected="selected"' : '').'>'.$this->t('Right column').'</option>
				</select>
			</div>
		</div>';
		// block title
		$output .= '
		<div style="clear: both;">
			<label>'.$this->t('Block title:').'</label>
			<div class="margin-form" style="padding-top:5px;">';
			foreach($this->langs as $l)
			{
				$output .= '
				<div id="divblocktitle_'.$l['id_lang'].'" style="display:'.($l['id_lang'] == $deflang ? 'block':'none').';float: left;">
					<input type="text" size="33" id="divblocktitle['.$l['id_lang'].']" name="divblocktitle['.$l['id_lang'].']" value="'.$blocktitle[$l['id_lang']].'"/>
				</div>';
			}
		$output .= $this->displayFlags($this->langs,$deflang,$intDivs,'divblocktitle',true);
		$output .= '
			</div>
		</div>';
		// file list
		$html_catalogs = '';
		$catalogs = $this->scanPDFFiles(_PS_MODULE_DIR_.'scpdfcatalog/export/');
		if(!empty($catalogs))
			$html_catalogs = $catalogs."<br/><br/>".$this->t('=> Right-click on "Open" and then copy the URL link into the block below to allow your visitors to download your catalog.');
		else
			$html_catalogs = $this->t('No catalogs.');
		$output .= '
				<br/>
		<div style="clear: both;">
			<label>'.$this->t('Catalogs:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				'.$html_catalogs.'
			</div>
		</div>
		<script type="text/javascript">
		
			var msg_error_extension_file="'.$this->t('extension de fichier incorrecte. Seules jpg, png ou gif sont autorisées').'";
		
			jQuery("#newlogo").change(function()
			{
				var this_file=jQuery(this).attr("value");
				var re=/(jpg|png|gif)$/i;
	
				if(!re.exec(this_file)){
					alert(msg_error_extension_file);
					jQuery(this).attr("value","");}
			});
	

			function checkParams(){
        err_msg="";
				if ($("#categlist").val()==null)
					err_msg += "\n'.$this->t('You must select a category.').'";
				if ($("#divsaveparam").val()=="")
					err_msg += "\n'.$this->t('You must set a name to your configuration.').'";
				if ($("#divfilename").val()=="")
					err_msg += "\n'.$this->t('You must set a filename to your PDF catalog.').'";
				if (err_msg!=""){
					alert(err_msg);
					return false;
				}
				$("#pdfform").attr("action",$("#pdfform").attr("action")+"&catalogsettings="+$("#divsaveparam").val());
				return true;
			}
			function getJSLink(file){
				return \'<a target="_blank" href="'.__PS_BASE_URI__.'modules/scpdfcatalog/export/'.'\'+file+\'">\'+file+\'</a>\';
			}'."
			function getLinkRewriteFromString(str)
			{
				str = str.toUpperCase();
				str = str.toLowerCase();
				str = str.replace(/[\u0105\u0104\u00E0\u00E1\u00E2\u00E3\u00E4\u00E5]/g,'a');
				str = str.replace(/[\u00E7\u010D\u0107\u0106]/g,'c');
				str = str.replace(/[\u010F]/g,'d');
				str = str.replace(/[\u00E8\u00E9\u00EA\u00EB\u011B\u0119\u0118]/g,'e');
				str = str.replace(/[\u00EC\u00ED\u00EE\u00EF]/g,'i');
				str = str.replace(/[\u0142\u0141]/g,'l');
				str = str.replace(/[\u00F1\u0148]/g,'n');
				str = str.replace(/[\u00F2\u00F3\u00F4\u00F5\u00F6\u00F8\u00D3]/g,'o');
				str = str.replace(/[\u0159]/g,'r');
				str = str.replace(/[\u015B\u015A\u0161]/g,'s');
				str = str.replace(/[\u00DF]/g,'ss');
				str = str.replace(/[\u0165]/g,'t');
				str = str.replace(/[\u00F9\u00FA\u00FB\u00FC\u016F]/g,'u');
				str = str.replace(/[\u00FD\u00FF]/g,'y');
				str = str.replace(/[\u017C\u017A\u017B\u0179\u017E]/g,'z');
				str = str.replace(/[\u00E6]/g,'ae');
				str = str.replace(/[\u0153]/g,'oe');
				str = str.replace(/[\u013E\u013A]/g,'l');
				str = str.replace(/[\u0155]/g,'r');
				str = str.replace(/[^a-z0-9\s\'\:\/\[\]-]/g,'');
				str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
				str = str.replace(/[ ]/g,'-');
				str = str.replace(/[\/]/g,'-');
				return str;
			} ".'
		</script>';
		// block content
		$output .= '
		<div style="clear: both;">
			<label>'.$this->t('Block content:').'</label>
			<div class="margin-form" style="padding-top:5px;">';
			foreach($this->langs as $l){
				$output .= '
				<div id="divblockcontent_'.$l['id_lang'].'" style="display:'.($l['id_lang'] == $deflang ? 'block' : 'none').';float: left;">
					<textarea class="rte" id="divblockcontent['.$l['id_lang'].']" name="divblockcontent['.$l['id_lang'].']">'.$blockcontent[$l['id_lang']].'</textarea>
				</div>';
			}
		$output .= $this->displayFlags($this->langs,$deflang,$intDivs,'divblockcontent',true);
		$output .= '
			</div>
		</div>';
		$output .= '
		<div class="margin-form clear"><br/><br/><br/>
			<input type="submit" value="'.$this->t('Save block options').'" name="submitBlock" class="button" />
		</div>
		</form>
		';
		//TinyMCE
		if (version_compare(_PS_VERSION_,'1.4.1.0','>=')){
			$iso = Language::getIsoById((int)($cookie->id_lang));
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
			$ad = dirname($_SERVER["PHP_SELF"]);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$output .= '
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'modules/scpdfcatalog/tinymce.inc.js"></script>
			<script type="text/javascript">id_language = Number('.$defaultLanguage.')</script>';
		}elseif (version_compare(_PS_VERSION_,'1.4.0.0','>=')){
			$iso = Language::getIsoById((int)($cookie->id_lang));
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
			$ad = dirname($_SERVER["PHP_SELF"]);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$output .= '
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
			<script type="text/javascript">
				tinyMCE.init({
					mode : "textareas",
					theme : "advanced",
					plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
					// Theme options
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfullcut,|,copy,paste,|,undo,redo,|,link,unlink,|,code",
					theme_advanced_buttons2 : "styleselect,formatelect,fontselect,fontsizeselect,|,help",
					theme_advanced_buttons3 : "tablecontrols,|,fullscreen,|,styleprops",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_statusbar_location : "bottom",
					theme_advanced_resizing : true,
					content_css : "/ps14017/themes/prestashop/css/global.css",
					document_base_url : "/ps14017/",
					width: "500",
					height: "200",
					font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
					elements : "nourlconvert,ajaxfilemanager",
					file_browser_callback : "ajaxfilemanager",
					entity_encoding: "raw",
					convert_urls : false,
					language : "'.$isoTinyMCE.'"
				});
				function ajaxfilemanager(field_name, url, type, win) {
					var ajaxfilemanagerurl = "/ps14017/admin-dev/ajaxfilemanager/ajaxfilemanager.php";
					switch (type) {
						case "image":
							break;
						case "media":
							break;
						case "flash":
							break;
						case "file":
							break;
						default:
							return false;
					}
					tinyMCE.activeEditor.windowManager.open({
						url: "/ps14017/admin-dev/ajaxfilemanager/ajaxfilemanager.php",
						width: 782,
						height: 440,
						inline : "yes",
						close_previous : "no"
					},{
						window : win,
						input : field_name
					});
				}
			</script>';
		}elseif (version_compare(_PS_VERSION_,'1.3.0.0','>=')){
			$iso = Language::getIsoById((int)($cookie->id_lang));
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tinymce/jscripts/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
			$ad = dirname($_SERVER["PHP_SELF"]);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		$output .= '
			<script type="text/javascript">
			var iso = \''.$isoTinyMCE.'\' ;
			var pathCSS = \''._THEME_CSS_DIR_.'\' ;
			var ad = \''.$ad.'\' ;
			</script>
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
			<script type="text/javascript">id_language = Number('.$defaultLanguage.')</script>
			<script type="text/javascript">
				function tinyMCEInit(element){
					$().ready(function() {
						$(element).tinymce({
							// Location of TinyMCE script
							script_url : \''.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js\',
							// General options
							theme : "advanced",
							plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,media,searchreplace,contextmenu,paste,directionality,fullscreen",
							// Theme options
							theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfullcut,|,copy,paste,|,undo,redo,|,link,unlink,|,code",
							theme_advanced_buttons2 : "styleselect,formatelect,fontselect,fontsizeselect,|,help",
							theme_advanced_buttons3 : "tablecontrols,|,fullscreen,|,styleprops",
							theme_advanced_buttons4 : "",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_resizing : true,
							content_css : "'.__PS_BASE_URI__.'themes/prestashop/css/global.css",
							document_base_url : "'.__PS_BASE_URI__.'",
							width : "500",
							height : "200",
							// Drop lists for link/image/media/template dialogs
							template_external_list_url : "lists/template_list.js",
							external_link_list_url : "lists/link_list.js",
							external_image_list_url : "lists/image_list.js",
							media_external_list_url : "lists/media_list.js",
							elements : "nourlconvert",
							convert_urls : false,
							language : "'.$isoTinyMCE.'"
						});
					});
				}
			tinyMCEInit(\'textarea.rte\');
			</script>';
		}elseif (version_compare(_PS_VERSION_,'1.2.0.0','>=')){
		$output .= '
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/jquery.tinymce.js"></script>
			<script type="text/javascript">
				function tinyMCEInit(element){
					$().ready(function(){
						$(element).tinymce({
							// Location of TinyMCE script
							script_url : \''.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce.js\',
							// General options
							theme : "advanced",
							plugins : "safari,pagebreak,style,layer,table,advimage,advlink,inlinepopups,preview,media,searchreplace,contextmenu,paste,directionality,fullscreen",
							// Theme options
							elements : "nourlconvert",
							convert_urls : false,
							theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfullcut,|,copy,paste,|,undo,redo,|,link,unlink,|,code",
							theme_advanced_buttons2 : "styleselect,formatelect,fontselect,fontsizeselect,|,help",
							theme_advanced_buttons3 : "tablecontrols,|,fullscreen,|,styleprops",
							theme_advanced_buttons4 : "",
							theme_advanced_toolbar_location : "top",
							theme_advanced_toolbar_align : "left",
							width : "500",
							height : "200",
							theme_advanced_statusbar_location : "bottom",
							theme_advanced_resizing : true,
							content_css : "'.__PS_BASE_URI__.'themes/'._THEME_NAME_.'/css/global.css",
							// Drop lists for link/image/media/template dialogs
							template_external_list_url : "lists/template_list.js",
							external_link_list_url : "lists/link_list.js",
							external_image_list_url : "lists/image_list.js",
							media_external_list_url : "lists/media_list.js"
						});
					});
				}
				tinyMCEInit(\'textarea.rte\');
			</script>';
		}else{
		$output .= '
			<script type="text/javascript" src="'.__PS_BASE_URI__.'js/tinymce/jscripts/tiny_mce/tiny_mce_gzip.js"></script>
			<script type="text/javascript">
				tinyMCE_GZ.init({
					plugins : "contextmenu, directionality, media, paste, preview, safari",
					themes : "advanced",
					languages : "iso",
					disk_cache : false,
					debug : false
				});
			</script>
			<script type="text/javascript">
				tinyMCE.init({
					width: "500",
					height: "200",
					mode : "textareas",
					plugins : "contextmenu, directionality, media, paste, preview, safari",
					theme : "advanced",
					language : "iso",
					elements : "nourlconvert",
					convert_urls : false,
					theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfullcut,|,copy,paste,|,undo,redo,|,link,unlink,|,code",
					theme_advanced_buttons2 : "styleselect,formatelect,fontselect,fontsizeselect,|,help",
					theme_advanced_buttons3 : "tablecontrols,|,fullscreen,|,styleprops",
					theme_advanced_buttons4 : "",
					theme_advanced_toolbar_location : "top",
					theme_advanced_toolbar_align : "left",
					theme_advanced_buttons3_add : "ltr,rtl,pastetext,pasteword,selectall",
					theme_advanced_buttons1_add : "media,preview",
					paste_create_paragraphs : false,
					paste_create_linebreaks : false,
					paste_use_dialog : true,
					paste_auto_cleanup_on_paste : true,
					paste_convert_middot_lists : false,
					paste_unindented_list_class : "unindentedList",
					paste_convert_headers_to_strong : true,
					plugin_preview_width : "500",
					plugin_preview_height : "600",
					extended_valid_elements : "a[name|href|target|title|onclick],img[class|src|border=0|alt|title|hspace|vspace|width|height|align|onmouseover|onmouseout|name],hr[class|width|size|noshade],font[face|size|color|style],span[class|align|style]"
				});
			</script>';
		}
		return $output;
	}

	public function ll($string, $specific = false)
	{
		global $_MODULES, $_MODULE,$_MODULES_PDF;		
		$id_lang = $this->cataloglang;	
		if (!is_array($_MODULES_PDF)){
			$file = _PS_MODULE_DIR_.$this->name.'/'.Language::getIsoById($id_lang).'.php';
			if (file_exists($file) AND empty(self::$translations)){/*include($file)*/
				include($file);
				$_MODULES_PDF = $_MODULE;
				self::$translations = $_MODULE;
			}
			elseif (file_exists($file) AND !empty(self::$translations)){
				$_MODULE = self::$translations;
				$_MODULES_PDF = $_MODULE;
			}
		}
		$source = Tools::strtolower($specific ? $specific : get_class($this));
		$string2 = str_replace('\'', '\\\'', $string);
		
		$currentKey = '<{'.$this->name.'}'._THEME_NAME_.'>'.$source.'_'.md5($string2);
		$defaultKey = '<{'.$this->name.'}prestashop>'.$source.'_'.md5($string2);
		if (key_exists($currentKey, $_MODULES_PDF)){
			$ret = Tools::stripslashes($_MODULES_PDF[$currentKey]);
		}elseif (key_exists($defaultKey, $_MODULES_PDF)){
			$ret = Tools::stripslashes($_MODULES_PDF[$defaultKey]);
		}else{
			$ret = $string;
		}
		
		return str_replace('"', '&quot;', $ret);
	}

	public function hookLeftColumn($params)
	{
		if(Configuration::get('PS_SC_PDFCATALOG_BLOCKDISPLAY') == 1){
			global $smarty, $cookie;
			$title = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKTITLE');
			$content = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKCONTENT');
			$smarty->assign('blocktitle', $title[$cookie->id_lang]);
			$smarty->assign('blockcontent', $content[$cookie->id_lang]);

			return $this->display(__FILE__, 'blockscpdfcatalog.tpl');
		}
	}
	
	public function hookRightColumn($params)
	{
		if(Configuration::get('PS_SC_PDFCATALOG_BLOCKDISPLAY') == 2){
			global $smarty, $cookie;
			$title = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKTITLE');
			$content = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKCONTENT');
			$smarty->assign('blocktitle', $title[$cookie->id_lang]);
			$smarty->assign('blockcontent', $content[$cookie->id_lang]);
			return $this->display(__FILE__, 'blockscpdfcatalog.tpl');
		}
	}

// back office translation
	public function translationData()
	{
	// for the PS parser
	// admin tab
		$this->l(':');
		$this->l('PDF Catalog');
		$this->l('Catalog Options');
		$this->l('Title:');
		$this->l('Cover page message:');
		$this->l('Footer:');
		$this->l('Categories:');
		$this->l('Use Ctrl + Click to select one or several categories');
		$this->l('Click here to select all categories');
		$this->l('Language setting');
		$this->l('Language:');
		$this->l('Currency:');
		$this->l('Name of the PDF file to export:');
		$this->l('Product page format:');
		$this->l('1 product per page (1x1)');
		$this->l('4 products per page (2x2)');
		$this->l('6 products per page (2x3)');
		$this->l('12 products per page (3x4)');
		$this->l('List (Ref-Name-Weight-Stock-Price)');
		$this->l('List (Ref-Name-EAN13-Stock-Price)');
		$this->l('List (Ref-Name-UPC-Stock-Price)');
		$this->l('List (Ref-Photo-Name-Weight-Price)');
		$this->l('List (Ref-Photo-Name-Price)');
		$this->l('List (Ref-Photo-Name)');
		$this->l('Create first page:');
		$this->l('Display page numbers:');
		$this->l('Use links on product names and images:');
		$this->l('Save and create the PDF catalog');
		$this->l('Catalog options has not been updated');
		$this->l('Title error, please check the title');
		$this->l('Legal notice error, please check the legal notice');
		$this->l('Footer error, please check the footer');
		$this->l('Filename error, please check the filename');
		$this->l('Price Incl. Tax:');
		$this->l('Export to file:');
		$this->l('Block Options');
		$this->l('Display:');
		$this->l('Block title:');
		$this->l('Catalogs:');
		$this->l('Block content:');
		$this->l('Add to Block Content');
		$this->l('Save block options');
		$this->l('Not displayed');
		$this->l('Left column');
		$this->l('Right column');
		$this->l('Settings');
		$this->l('Cover page');
		$this->l('Header and footer');
		$this->l('Data source');
		$this->l('Export options');
		$this->l('If checked, the file is saved in the export folder. If not checked, the created pdf catalog is sent to the browser for download operation.');
		$this->l('Use links:');
		$this->l('If this box is ticked, the reader can then click on product names and images in the PDF catalog to access products directly on website and help you sell more! ');
		$this->l('Open');
		$this->l('Table of contents');
		$this->l('Create table of contents:');
		$this->l('Author:');
		$this->l('Document creator:');
		$this->l('Document title:');
		$this->l('Document subject:');
		$this->l('Use category cover page:');
		$this->l('Use category name in page header:');
		$this->l('If checked, the name of the category is displayed in the header of the page.');
		$this->l('Sort by:');
		$this->l('position ASC');
		$this->l('position DESC');
		$this->l('name ASC');
		$this->l('name DESC');
		$this->l('manufacturer ASC');
		$this->l('manufacturer DESC');
		$this->l('price ASC');
		$this->l('price DESC');
		$this->l('List of catalog settings');
		$this->l('Choose your catalog settings:');
		$this->l('Save catalog parameters:');
		$this->l('Save options');
		$this->l('Enter the catalog name for saving parameters.');
		$this->l('Special options');
		$this->l('=> Right-click on "Open" and then copy the URL link into the block below to allow your visitors to download your catalog.');
		$this->l('Default values');
		$this->l('No first page');
		$this->l('Use first page with title and message');
		$this->l('Use template file firstpage.tpl');
		$this->l('You must select a category.');
		$this->l('You must set a name to your configuration.');
		$this->l('You must set a filename to your PDF catalog.');
		$this->l('Use this link to create this pdf catalog from a CRON task:');
		$this->l('Catalog settings');
		$this->l('No category cover page');
		$this->l('Use category name');
		$this->l('Use category name and description');
		$this->l('Use categorypage.tpl');
		$this->l('Delete settings');
		$this->l('Add a new logo:');
		$this->l('Current logo for this catalog:');
		$this->l('Product label:');
		$this->l('Customer group:');
		$this->l('Use customer group prices and categories.');
		$this->l('Product name');
		$this->l('Product short description');
		$this->l('Product name and short description (separator: line break)');
		$this->l('Product title and short Desc (separator: space)');
		$this->l('Product title and short Desc (separator: comma)');
		$this->l('Combination included:');
		$this->l('The file cannot be uploaded.');
		$this->l('Wrong logo file extension. The extension should be');
		$this->l('or');
		$this->l('Are you sure you want to delete this catalog?');
		$this->l('Some files are not writable, please change the permissions of all the content of /modules/scpdfcatalog/ ');
		$this->l('It is possible to add today’s date using a simple tag such as: [date:y-m-d]');
		$this->l('You can write what you want between "[date:" and "]", but the letter "d" will be replaced by day, "m" by month and "y" by year.');
		$this->l('Sample: [date:y-m-d] => 2013-05-01, [date:m/d/y] => 05/01/2013, [date:d/m in y] => 01/05 in 2013');
		$this->l('Use active products only:');
		$this->l('Font name:');
	// pdf class
		$this->l('Product features:');
		$this->l('Product attributes:');
		$this->l('See online');
		$this->l('Ref.');
		$this->l('Reference');
		$this->l('Product');
		$this->l('Weight');
		$this->l('Stock');
		$this->l('Price');
		$this->l('Price Excl. Tax');
		$this->l('Price Exc. Tax');
		$this->l('Price Inc. Tax');
		$this->l('EAN13');
		$this->l('Name');
		$this->l('Back');
		$this->l('PDF file created');
		$this->l('Download');
		$this->l('Products\' images format:');
		$this->l('This format of image allows you to define the format which will be used to generate the products\'s images in the PDF. It is not the final size of the image which will be inserted.');
		$this->l('The bigger the dimensions of the chosen format are, the higher the quality of the generated images will be. However, please note that the weight of the file will be more important as a consequence.');
		$this->l('For all your URLs, please remember to enter the complete path to obtain correct links.');
		$this->l('Samples for link:');
		$this->l('Bad:');
		$this->l('Good:');
		$this->l('Samples for image:');
		$this->l('Use products with stock only:');
		$this->l('Filter by manufacturer:');
		$this->l('All manufacturers');
		$this->l('Add empty column?');
		$this->l('Empty column libelle:');
		$this->l('The PDF file is being generated. Please wait!');
		$this->l('Error while during generating!');
		$this->l('Save and open PDF catalog');
		$this->l('Save, create and open PDF catalog');
		$this->l('Regenerate PDF');
		$this->l('Are you sure that you want you delete these settings?');
		$this->l('Freeserif font needs to be selected to print Cyrillic characters.');
		$this->l('Products created X days ago');
		$this->l('Use image file firstpage.jpg (or .jpeg or .png)');
		$this->l('Prices comparison settings');
		$this->l('Column libelle:');
		$this->l('Price colmuns:');
		$this->l('Price Incl. Tax');
		$this->l('Price Exc. Tax');
		$this->l('Price Incl. and Exc. Tax');
		$this->l('Separator for thousands:');
		$this->l('Separator for decimals:');
		$this->l('Hide currency:');
		$this->l('Hide decimals:');
		$this->l('Do not start a new page for each new category:');
		$this->l('Managing the block display on the front office');
		$this->l('No catalogs.');
		$this->l('Generated catalogs');
		$this->l('Block display');
		$this->l('CRON task');
		$this->l('If working in multistore mode, use WGET to call the CRON task.');
		//$this->l('');
	}

// translation from module with default language
	public function t($str)
	{
		return $this->l($str);
	}

// translation from PDF creator with selected language
	public function tt($str)
	{
		return $this->ll($str);
		//return $this->ll($str);
	}
	
	public static function sc_file_get_contents($param,$querystring='')
	{
		$result='';
		if (function_exists('file_get_contents')) {
			@$result=file_get_contents($param);
		}
		if ($result=='' && function_exists('curl_init')) {
	
			$curl = curl_init();
				
			$header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
			$header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
			$header[] = "Cache-Control: max-age=0";
			$header[] = "Connection: keep-alive";
			$header[] = "Keep-Alive: 300";
			$header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
			$header[] = "Accept-Language: en-us,en;q=0.5";
			$header[] = "Pragma: ";
				
			curl_setopt($curl, CURLOPT_URL, $param);
			curl_setopt($curl, CURLOPT_USERAGENT, 'Store Commander (http://www.storecommander.com)');
			curl_setopt($curl, CURLOPT_HTTPHEADER, $header);
			curl_setopt($curl, CURLOPT_ENCODING, 'gzip,deflate');
			curl_setopt($curl, CURLOPT_AUTOREFERER, true);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $querystring);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
			curl_setopt($curl, CURLOPT_TIMEOUT, 20);
	
			$result= curl_exec($curl);
			$info=curl_getinfo($curl);
			curl_close($curl);
	
			if ((int)$info['http_code']!=200) { return ''; }
	
		}
		return $result;
	}
	
	public function downloadTCPDF($debug=false)
	{
		require_once (dirname(__FILE__).'/pclzip.lib.php');
		$writePermissions=octdec('0'.Tools::substr(decoct(fileperms(realpath(_PS_IMG_DIR_))),-3));
		if($debug)
			echo 'Downloading pack BETA...<br/>';
		$data=self::sc_file_get_contents('http://www.storecommander.com/files/tcpdf01.zip');
		file_put_contents(dirname(__FILE__).'/tcpdf.zip',$data);
		if($debug)
			echo 'Opening zip archive...<br/>';
		$archive = new PclZip(dirname(__FILE__).'/tcpdf.zip');
		if($debug)
			echo 'Extracting zip archive...<br/>';
		$old = umask(0);
		$archive->extract(PCLZIP_OPT_PATH,dirname(__FILE__).'/',PCLZIP_OPT_SET_CHMOD,$writePermissions);
		umask($old);
		unlink(dirname(__FILE__).'/tcpdf.zip');
		if($debug)
			echo 'End of extraction<br/><br/>';
	}
	
	public function scanPDFFiles($dirname,$mask = '')
	{
		$files = scandir($dirname);
		$list = array();
		foreach ($files as $file){
			if (!in_array($file,$this->excludedFiles)){
				if (!is_dir($dirname.'/'.$file) && (file_exists($dirname.'/'.$file))){
					if (version_compare(_PS_VERSION_,'1.2.0.0','>=')){
           	$filename=_MODULE_DIR_.'scpdfcatalog/export/'.$file;
           	$sec_key=md5(_COOKIE_KEY_.date('Ymd'));
           	
           	$temp = explode('.', $file);
           	$ext  = array_pop($temp);
           	$filename = implode('.', $temp);
           	
           	$txt_delete='<input value="X" class="button deletefile" type="button" onclick="if (confirm(\''.$this->t('Are you sure you want to delete this catalog?').'\'))$.get(\''._MODULE_DIR_.'scpdfcatalog/delete_export_file.php?sk='.$sec_key.'&f='.$file.'\',function(data){if (data==1) $(\'div#myfile_'.$filename.'\').remove();})"/>';
						$txt = '<div id="myfile_'.$filename.'">'.$txt_delete.'  '.$file.' '.$this->sizeFormat(filesize($dirname.'/'.$file)).' - <a href="'._MODULE_DIR_.'scpdfcatalog/export/'.$file.'" target="_blank">'.$this->t('Open').'</a>';
           	$txt.='</div>';
                                                
						// TODO : find a way to get third TinyMCE instance and use getContent/setContent
						//  - <a href="javascript:$(document.getElementById(\'divblockcontent[\'+id_language+\']\')).val($(document.getElementById(\'divblockcontent[\'+id_language+\']\')).val()+getJSLink(\''.$file.'\'));void(0);">'.$this->t('Add to Block Content').'</a>
					}else{ // old version if tinyMCE
						$txt = $file.' '.$this->sizeFormat(filesize($dirname.'/'.$file)).' <input type="text" size="50" value="modules/scpdfcatalog/export/'.$file.'"/>';
					}
					if ($mask != ''){
						if (strpos($file,$mask) !== false)
							$list[] = $txt;
					}else{
						$list[] = $txt;
					}
				}
			}
		}
		return join('',$list);                
	}

	public function sizeFormat($size)
	{
	    if($size<-1024){
	        return $size." octets";
	    }else if($size<(1024*1024)){
	        $size=round($size/1024,1);
	        return $size." Ko";
	    }else if($size<(1024*1024*1024)){
	        $size=round($size/(1024*1024),1);
	        return $size." Mo";
	    }else{
	        $size=round($size/(1024*1024*1024),1);
	        return $size." Go";
	    }
	}

	public static function getHttpHost($http = true)
	{
		if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
		{
			return Tools::getHttpHost(true);
		}
		else
		{
			$host = (isset($_SERVER['HTTP_X_FORWARDED_HOST']) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : $_SERVER['HTTP_HOST']);
			if ($entities)
				$host = htmlspecialchars($host, ENT_COMPAT, 'UTF-8');
			if ($http)
				$host = 'http://'.$host;
			return $host;
		}
	}
	public static function getHttp()
	{
		return (version_compare(_PS_VERSION_, '1.3.0.0', '>=') && (defined("_PS_SSL_ENABLED_") && _PS_SSL_ENABLED_)  ? 'https://' : 'http://');
	}
	

	public static function updateValue($key, $values, $html = false, $id_shop_group = null, $id_shop = null)
	{
		if (!Validate::isConfigName($key))
			die(sprintf(Tools::displayError('[%s] is not a valid configuration key'), $key));
	
		if ($id_shop === null)
			$id_shop = Shop::getContextShopID(true);
		if ($id_shop_group === null)
			$id_shop_group = Shop::getContextShopGroupID(true);
	
		if (!is_array($values))
			$values = array($values);
	
		$result = true;
		foreach ($values as $lang => $value)
		{
			$stored_value = Configuration::get($key, $lang, $id_shop_group, $id_shop);
			// if there isn't a $stored_value, we must insert $value
			if ((!is_numeric($value) && $value === $stored_value) || (is_numeric($value) && $value == $stored_value && Configuration::hasKey($key, $lang)))
				continue;
	
			// If key already exists, update value
			if (Configuration::hasKey($key, $lang, $id_shop_group, $id_shop))
			{
				if (!$lang)
				{
					// Update config not linked to lang
					$result &= Db::getInstance()->update(Configuration::$definition['table'], array(
							'value' => pSQL($value, $html),
							'date_upd' => date('Y-m-d H:i:s'),
					), '`name` = \''.pSQL($key).'\''.self::sqlRestriction($id_shop_group, $id_shop), 1, true);
				}
				else
				{
					// Update multi lang
					$sql = 'UPDATE `'._DB_PREFIX_.bqSQL(Configuration::$definition['table']).'_lang` cl
							SET cl.value = \''.pSQL($value, $html).'\',
								cl.date_upd = NOW()
							WHERE cl.id_lang = '.(int)$lang.'
								AND cl.`'.bqSQL(Configuration::$definition['primary']).'` = (
									SELECT c.`'.bqSQL(Configuration::$definition['primary']).'`
									FROM `'._DB_PREFIX_.bqSQL(Configuration::$definition['table']).'` c
									WHERE c.name = \''.pSQL($key).'\''
												.self::sqlRestriction($id_shop_group, $id_shop)
												.')';
					$result &= Db::getInstance()->execute($sql);
				}
			}
			// If key does not exists, create it
			else
			{
				if (!$configID = Configuration::getIdByName($key, $id_shop_group, $id_shop))
				{
					$newConfig = new Configuration();
					$newConfig->name = $key;
					if ($id_shop)
						$newConfig->id_shop = (int)$id_shop;
					if ($id_shop_group)
						$newConfig->id_shop_group = (int)$id_shop_group;
					if (!$lang)
						$newConfig->value = $value;
					$result &= $newConfig->add(true, true);
					$configID = $newConfig->id;
				}
	
				if ($lang)
				{
					$result &= Db::getInstance()->insert(Configuration::$definition['table'].'_lang', array(
							Configuration::$definition['primary'] => $configID,
							'id_lang' => $lang,
							'value' => pSQL($value, $html),
							'date_upd' => date('Y-m-d H:i:s'),
					));
				}
			}
		}
	
		Configuration::set($key, $values, $id_shop_group, $id_shop);
	
		return $result;
	}
	
	protected static function sqlRestriction($id_shop_group, $id_shop)
	{
		if ($id_shop)
			return ' AND id_shop = '.(int)$id_shop;
		elseif ($id_shop_group)
			return ' AND id_shop_group = '.(int)$id_shop_group.' AND (id_shop IS NULL OR id_shop = 0)';
		else
			return ' AND (id_shop_group IS NULL OR id_shop_group = 0) AND (id_shop IS NULL OR id_shop = 0)';
	}
}
