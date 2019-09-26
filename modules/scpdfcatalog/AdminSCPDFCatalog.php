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

if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
	include_once (PS_ADMIN_DIR . '/../classes/AdminTab.php');

class AdminSCPDFCatalog extends AdminTab
{
	private $version = '2.6.2';
	private $getverionref = 'scpdfcatalog';
	private $mod;
	private $langs;
	private $logo_extensions;
	private $logo_erreurs;
	private $excludedFiles=Array( ".", "..", ".DS_Store", ".htaccess", "index.php");
	public $controller_type='admin'; // used for PS 1.5

	public function __construct()
	{
		$this->mod = Module::getInstanceByName('scpdfcatalog');
		$this->langs = Language::getLanguages();
    $this->logo_extensions=array('jpg','gif','png');
    $this->logo_erreurs=array(
        'extension'=>$this->mod->t('Wrong logo file extension. The extension should be').' '.implode(' '.$this->mod->t('or').' ',$this->logo_extensions),
        'move_uploaded_file'=>$this->mod->t('The file cannot be uploaded.')
      );
		parent::__construct();
	}

     private function _deleteLogo($settings,$format){
     	$dir    =_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/';
     	$files=scandir($dir);
     	while(list($c,$v)=each($files)){
     	     $pattern='/'.$settings.'-[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}\.'.implode('|',$this->logo_extensions).'$/';
     	
     	     if(preg_match($pattern,$v,$tmatches))
     	          @unlink($dir.$v);
			}
     }

     // fonction temporaire pour un affichage en mode debug (cette fonction sera effacée prochainement)
     private function _debug($str){
				echo '<div class="debug">';
        print_r($str);
        echo '</div>';
     }

     public function dirCheckWritable($dir,&$files) {
     	$dir=rtrim($dir,'/');
     	if (!is_writable($dir)) $files[]=$dir;
     	if (is_dir($dir)) {
     		$objects = scandir($dir);
     		foreach ($objects as $object) {
     			if ($object != "." && $object != "..") {
     				if ($object == "Thumbs.db" || $object == "desktop.ini")
     				{
     					@unlink($dir."/".$object);
     					continue;
     				}
     				if (!is_writable($dir."/".$object)) $files[]=$dir."/".$object;
     				if (is_dir($dir."/".$object)) $this->dirCheckWritable($dir."/".$object,$files);
     			}
     		}
     	}
     }
     
	public function display(){
		global $currentIndex,$cookie;
		$format = Tools::getValue('format','1x1');

		if(!file_exists(_PS_MODULE_DIR_.'scpdfcatalog/sctcpdf/tcpdf.php'))
		{
			$this->mod->downloadTCPDF();
		}
		
		$notWritableFiles=array();
		$writePermissions=octdec('0'.Tools::substr(decoct(fileperms(realpath(_PS_IMG_DIR_))),-3));
		$writePermissionsOCT=Tools::substr(decoct(fileperms(realpath(_PS_IMG_DIR_))),-3);
		$this->dirCheckWritable(_PS_MODULE_DIR_.'scpdfcatalog',$notWritableFiles);
		if (count($notWritableFiles))
		{
			echo '<div class="error">'.$this->mod->t('Some files are not writable, please change the permissions of all the content of /modules/scpdfcatalog/ ').' ('.$writePermissionsOCT.')'.'</div>';
		}
		
		if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
		{
			if(file_exists(_PS_MODULE_DIR_.'scpdfcatalog/translation'))
			{
				$files = scandir(_PS_MODULE_DIR_.'scpdfcatalog/translation');
				if(!empty($files))
				{
					foreach($files as $file)
					{
						if ($file!="index.php" && Tools::substr($file, -4)==".php")
						{
							if(file_exists(_PS_MODULE_DIR_.'scpdfcatalog/'.$file))
								rename(_PS_MODULE_DIR_.'scpdfcatalog/'.$file, _PS_MODULE_DIR_.'scpdfcatalog/'.str_replace(".php","_back.php",$file));
							
							copy(_PS_MODULE_DIR_.'scpdfcatalog/translation/'.$file, _PS_MODULE_DIR_.'scpdfcatalog/'.$file);
						}
					}	
				}
				
				rename(_PS_MODULE_DIR_.'scpdfcatalog/translation', _PS_MODULE_DIR_.'scpdfcatalog/translation_off');
			}
		}
			
		$get_deletesettings = Tools::getValue('deletesettings');
		if (isset($get_deletesettings) && file_exists(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/'.$get_deletesettings.'.xml'))
		{
			$this->_deleteLogo($get_deletesettings,$format);
      		@unlink(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/'.$get_deletesettings.'.xml');
			$_GET['catalogsettings']='settings';
		}

		$deflang = (int)(Configuration::get('PS_LANG_DEFAULT'));
		unset($_POST["catalogsettings"]);
		$pdfconf = Tools::getValue('catalogsettings','settings');

		if (!$pdfConfigDef = simplexml_load_file(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/settings.xml'))
			return false;
		
		$specialOptions = (string)$pdfConfigDef->specialoptions;
		if (!$pdfConfig = simplexml_load_file(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/'.($pdfconf == 'settings' ? '' : 'catalog/').$pdfconf.'.xml'))
			return false;

		if($pdfconf == 'settings')
		{
			$pdfConfig->idlang = Configuration::get("PS_LANG_DEFAULT");
			$pdfConfig->currency = Configuration::get("PS_CURRENCY_DEFAULT");
    		$pdfConfig->pc_currency_1 = Configuration::get("PS_CURRENCY_DEFAULT");
    		$pdfConfig->pc_currency_2 = Configuration::get("PS_CURRENCY_DEFAULT");
		}
    	$title = (string)$pdfConfig->title;
		$legalnotice = (string)$pdfConfig->legalnotice;
		$footer = (string)$pdfConfig->footer;
		$filename = (string)$pdfConfig->filename;
		$saveparam = (string)$pdfConfig->name;
		$idlang = (int)$pdfConfig->idlang;
		$firstpage = (int)$pdfConfig->firstpage;
		$tocdisplay = (int)$pdfConfig->tocdisplay;
		$pagenumber = (int)$pdfConfig->pagenumber;
		$format = (string)$pdfConfig->format;
		$uselinks = (int)$pdfConfig->uselinks;
		$usecategcover = (int)$pdfConfig->usecategcover;
		$usecategheader = (int)$pdfConfig->usecategheader;
		$activeproduct = (int)$pdfConfig->activeproduct;
		$withstockproduct = (int)$pdfConfig->withstockproduct;
		$filterbybrand = (int)$pdfConfig->filterbybrand;
		$orderby = (string)$pdfConfig->orderby;
		$xdaysago = (string)$pdfConfig->xdaysago;
		$doctitle = (string)$pdfConfig->doctitle;
		$docsubject = (string)$pdfConfig->docsubject;
		$doccreator = (string)$pdfConfig->doccreator;
   		$doclogo=(string)$pdfConfig->doclogo;
		$author = (string)$pdfConfig->author;
		$categlist = explode(',',(string)$pdfConfig->categlist);
		$currency = (int)$pdfConfig->currency;
		$usecustomergroup = (int)$pdfConfig->usecustomergroup;
		$productimageformat = $pdfConfig->productimageformat;
		$fontname = $pdfConfig->fontname;
		$vatinc = (int)$pdfConfig->vatinc;
    	$vatexc = (int)$pdfConfig->vatexc;
    	$emptycol = (int)$pdfConfig->emptycol;
    	$emptycollibelle = (string)$pdfConfig->emptycollibelle;
    	$combinationsinc = (int)$pdfConfig->combinationsinc;
    	$product_title=(string)$pdfConfig->product_title;
    	$thousandssep=(string)$pdfConfig->thousandssep;
    	$decimalssep=(string)$pdfConfig->decimalssep;
    	$hidecurrency=(int)$pdfConfig->hidecurrency;
        $showSupplierReference = (int)$pdfConfig->showSupplierReference;
    	$hidedecimals=(int)$pdfConfig->hidedecimals;
    	$categorynewpage=(int)$pdfConfig->categorynewpage;
    	
    	$pc_collibelle_1 = (string)$pdfConfig->pc_collibelle_1;
    	$pc_collibelle_2 = (string)$pdfConfig->pc_collibelle_2;
    	$pc_currency_1 = (int)$pdfConfig->pc_currency_1;
    	$pc_currency_2 = (int)$pdfConfig->pc_currency_2;
    	$pc_usecustomergroup_1 = (int)$pdfConfig->pc_usecustomergroup_1;
    	$pc_usecustomergroup_2 = (int)$pdfConfig->pc_usecustomergroup_2;
    	$pc_pricecolumns_1 = (string)$pdfConfig->pc_pricecolumns_1;
    	$pc_pricecolumns_2 = (string)$pdfConfig->pc_pricecolumns_2;

		if ($specialOptions != ''){
			$specialOptionsGet = (string)$pdfConfigDef->specialoptionsgetval;
			eval($specialOptionsGet);
		}
		$blockdisplay = Configuration::get('PS_SC_PDFCATALOG_BLOCKDISPLAY');
		$blocktitle = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKTITLE');
		$blockcontent = Configuration::getInt('PS_SC_PDFCATALOG_BLOCKCONTENT');

		// GENERATION DU PDF
		$display_SCPDF = Tools::getValue("display_SCPDF", 0);
		$generate_SCPDF = Tools::getValue("generate_SCPDF", 0);
		if($display_SCPDF=="1" || $generate_SCPDF=="1")
		{
			$PDFCatalogFromAdmin = 1;
			$_GET['key']=md5(_COOKIE_KEY_);
			$_GET['format']=$format;
			$_GET['divsaveparam']=$saveparam;
			if($display_SCPDF=="1")
				$_GET['display_now']="1";
			require_once(_PS_MODULE_DIR_.'scpdfcatalog/SCPDF.php');
			if($generate_SCPDF=="1")
			{
				echo '<script>window.top.opener.$(".conf").show();window.close();</script>';die();
			}
		}

		// SINON AFFICHAGE DE L'ADMIN
		
    $intDivs = 'divblocktitle¤divblockcontent';
		echo '<script type="text/javascript">id_language='.$deflang.';</script>
		<h2>'.$this->mod->t('PDF Catalog').' '.$this->version.'</h2><style>.width4 h2{clear:both;padding-top:25px;}</style>

			<form id="pdfform" action="'.$_SERVER['REQUEST_URI'].'" method="post"  enctype="multipart/form-data" onSubmit="rewriteNames(); return checkParams();">';

          echo '<fieldset class="width4"><legend><img src="../img/admin/pdf.gif" />'.$this->mod->t('Settings').'</legend>';
          echo '
		<style>

			.width4 {width:850px;}
			label {width:200px;}
			.margin-form {padding: 0 0 1em 220px;}
               .error-msg {display:inline-block;color:#fff;padding:2%;margin:1% 0 4% 0;width:96%;
                    background-color:#aa0000;box-shadow:3px 3px 3px 0 #444;}
               .info {display:inline-block;color:#000;padding:2%;margin:1% 0 4% 0;width:96%;
                    background-color:#ffcc00;box-shadow:3px 3px 3px 0 #444;}
               .newlogo {display:inline-block;float:left;clear:left;margin:-17px 0 0 0}
               .debug {display:inline-block;padding:2%;margin:4% 0;width:96%;background-color:#ccc;box-shadow:3px 3px 3px 0px #444}
               .button.deletefile {border:none;padding:2px;font-size:10px;margin-left:10px;}
		</style>';
		echo '<h2>'.$this->mod->t('List of catalog settings').'</h2>';
		// product page format
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Product page format:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select id="format" name="format" onchange="javascript:document.location=String(document.location).split(\'&format=\')[0]+\'&format=\'+$(\'#format\').val()">';
					$formats = array_diff(scandir(_PS_MODULE_DIR_.'scpdfcatalog/templates/'), $this->excludedFiles);
					$iso_lang = Language::getIsoById((int)($cookie->id_lang));
					foreach($formats as $form){
						$fieldsetting = simplexml_load_file(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$form.'/settings.xml');
						$field = $fieldsetting->formatname;
						$compatibility = (string)$fieldsetting->compatibility;
						if (version_compare($compatibility , _PS_VERSION_, '<=')){
							if ($iso_lang == 'fr'){
								echo '<option value="'.$form.'" '.($form == Tools::getValue('format') ? 'selected="selected"' : '').'>'.(string)$field->fr.'</option>';
							}else{
								echo '<option value="'.$form.'" '.($form == Tools::getValue('format') ? 'selected="selected"' : '').'>'.(string)$field->en.'</option>';
							}
						}
					}
			echo '
				</select>
			</div>
		</div>';
		// load params
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Choose your catalog settings:').'</label>
			<div class="margin-form" style="padding-top:5px">
				<select id="catalogsettings" name="catalogsettings" onchange="javascript:document.location=String(document.location).split(\'&format=\')[0]+\'&format=\'+$(\'#format\').val()+\'&catalogsettings=\'+$(\'#catalogsettings\').val()">';
					echo '<option value="settings">'.$this->mod->t('Default values').'</option>';
					$params = scandir(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/');
					$nbSettings=0;
					foreach($params as $param)
					{
						$xml = Tools::substr($param,-4,4);
						if($xml == '.xml')
						{
							$param = Tools::substr($param,0,-4);
							echo '<option value="'.$param.'" '.($saveparam == $param ? 'selected="selected"' : '').'>'.$param.'</option>';
							$nbSettings++;
						}
					}
				echo '
				</select> '.($saveparam!='settings' ? ' 
						<a href="'.$_SERVER['REQUEST_URI'].'&deletesettings='.$saveparam.'" onclick="return confirm(\''.$this->mod->t('Are you sure that you want you delete these settings?').'\')"><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/scpdfcatalog/delete.png" alt="'.$this->mod->t('Delete settings').' '.$saveparam.'" title="'.$this->mod->t('Delete settings').' '.$saveparam.'" /></a>
						<br/><br/>
						<a href="'.SCPDFCatalog::getHttpHost().$_SERVER['REQUEST_URI'].'&display_SCPDF=1" target="_blank"><img src="'._PS_BASE_URL_.__PS_BASE_URI__.'modules/scpdfcatalog/page_refresh.png" alt="'.$this->mod->t('Regenerate PDF').' '.$saveparam.'" title="'.$this->mod->t('Regenerate PDF').' '.$saveparam.'" style="margin-left: 4px;" /> '.$this->mod->t('Regenerate PDF').' '.$saveparam.'</a>' : '').'
			</div>
		</div>
		</fieldset><br/><br/>';

    echo '<h1>'.$this->mod->t('Format').$this->mod->t(':').' '.$format.'</h1>';

		echo '<fieldset class="width4"><legend><img src="../img/admin/pdf.gif" />'.$this->mod->t('Catalog settings').$this->mod->t(':').' '.($saveparam=='settings'?$this->mod->t('Default values'):$saveparam).'</legend>';
		echo '<h2>'.$this->mod->t('Language setting').'</h2>';
		// language
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Language:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="id_lang">';
				foreach($this->langs as $lang)
					echo '<option value="'.$lang['id_lang'].'" '.($idlang == $lang['id_lang'] ? 'selected="selected"' : '').'>'.$lang['name'].'</option>';
				echo '
				</select>
			</div>
		</div>';

		echo '<h2>'.$this->mod->t('Cover page').'</h2>';
		// first page
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Create first page:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="firstpage">
					<option value="0"'.($firstpage==0?' selected="selected"':'').'>'.$this->mod->t('No first page').'</option>
					<option value="1"'.($firstpage==1?' selected="selected"':'').'>'.$this->mod->t('Use first page with title and message').'</option>
					<option value="2"'.($firstpage==2?' selected="selected"':'').'>'.$this->mod->t('Use template file firstpage.tpl').'</option>
					<option value="3"'.($firstpage==3?' selected="selected"':'').'>'.$this->mod->t('Use image file firstpage.jpg (or .jpeg or .png)').'</option>
				</select>
			</div>
		</div>';
		// title
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Title:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" size="33" id="divtitle" name="divtitle" value="'.$title.'"/>
			</div>
		</div>';
		// legal notice
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Cover page message:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<textarea id="divlegalnotice" name="divlegalnotice" class="rte">'.$legalnotice.'</textarea>
			</div>
			<em>'.$this->mod->t('It is possible to add today’s date using a simple tag such as: [date:y-m-d]').'<br/>
				'.$this->mod->t('You can write what you want between "[date:" and "]", but the letter "d" will be replaced by day, "m" by month and "y" by year.').'<br/>
				<br/>
				'.$this->mod->t('Sample: [date:y-m-d] => 2013-05-01, [date:m/d/y] => 05/01/2013, [date:d/m in y] => 01/05 in 2013').'</em>
		</div>';
		echo '<h2>'.$this->mod->t('Table of contents').'</h2>';

		// TOC
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Create table of contents:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="tocdisplay" value="1" '.($tocdisplay ? 'checked="checked"' : '').'/>
			</div>
		</div>';
		echo '<h2>'.$this->mod->t('Header and footer').'</h2>';

		  /****************
           * //// add a custom logo
           ****************/
          echo '
		<div style="clear: both;margin:0 0 60px 0">
			<label>'.$this->mod->t('Current logo for this catalog:').'</label>';
               $_FILES=array();
               $dir_logo_path = _PS_MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/';
               //is there any custom logo ?
              if(Tools::strlen($doclogo)>0 && file_exists($dir_logo_path.$doclogo)){

                     echo '<input type="hidden" name="defaultlogo_q" value="no"/>';
                     echo '<input type="hidden" name="thelogo" value="'.$doclogo.'"/>';

                    $image_path=$dir_logo_path.$doclogo;

                    // check if the image file exists (really)
                    if(file_exists($image_path)){
                         echo '<div class="margin-form" style="padding-top:5px;">';
                         //Vérification de la taille
                         $size=  getimagesize($image_path);

                         if($size[0]>900){
                              $width=700;
                              $height=floor($size[1]*$width/$size[0]);
                            /*  echo '<div class="info">';
                                   echo $this->mod->t('picture\'s dimensions are  => ');
                                   echo $this->mod->t('width').':'.$size[0];
                                   echo ' x '.$this->mod->t('height').':'.$size[1];

                                   echo ' *  ';
                                   echo $this->mod->t(' resized to fit in this page in  ').$width.' x '.$height;
                              echo '</div>';*/
					}
                         else {
                              $width=$size[0];
                              $height=$size[1];

					}
                         echo '<img width="'.$width.'" height="'.$height.'" src="'._MODULE_DIR_.'scpdfcatalog/templates/'.$format.'/catalog/'.$doclogo.'" alt="logo"/>';
                         echo '</div>';
				}
			}
               else {

                    //no custom logo... so we pick the logo shop
                    echo '<div class="margin-form" style="padding-top:5px;">';
                         echo '<img src="'.__PS_BASE_URI__.'img/logo.jpg" alt="logo"/>';
                         echo '<input type="hidden" name="defaultlogo_q" value="yes"/>';
                         echo '<input type="hidden" name="thelogo" value=""/>';
                    echo '</div>';
			}

               echo '<label>'.$this->mod->t('Add a new logo:').'</label>';
                    echo '<div class="margin-form" style="padding-top:0px;">';
                         // Affichage du message d'erreur en cas d'extension de logo incorrecte
                         if(isset($_SESSION['scpdfcatalog']['error_logo'])) {

                              while(list($c,$v)=each($_SESSION['scpdfcatalog']['error_logo'])){
                                   echo '<div class="error-msg">';
                                        echo $this->logo_erreurs[$v];
                                   echo '</div>';
						}

                              unset($_SESSION['scpdfcatalog']['error_logo']);
					}
                         echo '<input type="file" id="newlogo" name="newlogo" class="newlogo"/>';
                    echo '</div>';
          echo '</div>';
          /****************
           * //// end custom logo
           ****************/

		// footer
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Footer:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<textarea id="divfooter" name="divfooter" class="rte">'.$footer.'</textarea>
			</div>
			<em>'.$this->mod->t('It is possible to add today’s date using a simple tag such as: [date:y-m-d]').'<br/>
				'.$this->mod->t('You can write what you want between "[date:" and "]", but the letter "d" will be replaced by day, "m" by month and "y" by year.').'<br/>
				<br/>
				'.$this->mod->t('Sample: [date:y-m-d] => 2013-05-01, [date:m/d/y] => 05/01/2013, [date:d/m in y] => 01/05 in 2013').'</em><br/><br/>
		</div>';
		// page number
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Display page numbers:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="pagenumber" value="1" '.($pagenumber ? 'checked="checked"' : '').'/>
			</div>
		</div>';

		

		echo '<h2>'.$this->mod->t('Data source').'</h2>';

		// categories list
				$categories = Category::getCategories((int)($cookie->id_lang), false);
		echo '
		<div id="modelist" style="clear: both;">
		<label>'.$this->mod->t('Categories:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select id="categlist" size="20" name="categlist[]" multiple="multiple">';
				$this->recurseCategory($categories, $categories[0][1], $categlist, 1);
				echo '
				</select><br/>'.$this->mod->t('Use Ctrl + Click to select one or several categories').'<br/>
				<a href="javascript:selectAll(\'categlist\',true);void(0);">'.$this->mod->t('Click here to select all categories').'</a>
				<script type="text/javascript">
					function selectAll(selectBox,selectAll) {
						if (typeof selectBox == "string") {
							selectBox = document.getElementById(selectBox);
						}
						if (selectBox.type == "select-multiple") {
							for (var i = 0; i < selectBox.options.length; i++) {
								selectBox.options[i].selected = selectAll;
							}
						}
					}
				</script>
			</div>
		</div>';
		// order by
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Sort by:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="orderby">
					<option value="position$asc" '.($orderby == 'position$asc' ? 'selected="selected"' : '').'>'.$this->mod->t('position ASC').'</option>
					<option value="position$desc" '.($orderby == 'position$desc' ? 'selected="selected"' : '').'>'.$this->mod->t('position DESC').'</option>
					<option value="name$asc" '.($orderby == 'name$asc' ? 'selected="selected"' : '').'>'.$this->mod->t('name ASC').'</option>
					<option value="name$desc" '.($orderby == 'name$desc' ? 'selected="selected"' : '').'>'.$this->mod->t('name DESC').'</option>
					<option value="manufacturer$asc" '.($orderby == 'manufacturer$asc' ? 'selected="selected"' : '').'>'.$this->mod->t('manufacturer ASC').'</option>
					<option value="manufacturer$desc" '.($orderby == 'manufacturer$desc' ? 'selected="selected"' : '').'>'.$this->mod->t('manufacturer DESC').'</option>
					<option value="price$asc" '.($orderby == 'price$asc' ? 'selected="selected"' : '').'>'.$this->mod->t('price ASC').'</option>
					<option value="price$desc" '.($orderby == 'price$desc' ? 'selected="selected"' : '').'>'.$this->mod->t('price DESC').'</option>
                                        <option value="supplier_reference$asc" '.($orderby == 'supplier_reference$asc' ? 'selected="selected"' : '').'>'.$this->mod->t('supplier reference ASC').'</option>    
                                        <option value="supplier_reference$desc" '.($orderby == 'supplier_reference$desc' ? 'selected="selected"' : '').'>'.$this->mod->t('supplier reference DESC').'</option>    
				</select>
			</div>
		</div>';
		// X days ago
		echo '
		<div style="clear: both; padding-top: 15px">
			<label>'.$this->mod->t('Products created X days ago').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" size="33" id="xdaysago" name="xdaysago" value="'.(!empty($xdaysago)?$xdaysago:'').'"/>
			</div>
		</div>';
		// Active product
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Use active products only:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="activeproduct" value="1" '.($activeproduct ? 'checked="checked"' : '').'/><br/><br/>
			</div>
		</div>';
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Use products with stock only:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="withstockproduct" value="1" '.($withstockproduct ? 'checked="checked"' : '').'/><br/><br/>
			</div>
		</div>';
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Filter by manufacturer:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="filterbybrand">
					<option value="">'.$this->mod->t('All manufacturers').'</option>
					<option value="-"'.($filterbybrand=="-" ? ' selected="selected"' : '').'>-</option>';
		
				$brands = Manufacturer::getManufacturers();
				foreach($brands as $brand)
					echo '<option value="'.$brand["id_manufacturer"].'" '.($filterbybrand==$brand["id_manufacturer"] ? ' selected="selected"' : '').'>'.$brand["name"].'</option>';
		
		echo '		</select>
			</div>
		</div>';
		// use category cover page
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Use category cover page:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="usecategcover">
					<option value="0"'.($usecategcover==0 ? ' selected="selected"' : '').'>'.$this->mod->t('No category cover page').'</option>
					<option value="1"'.($usecategcover==1 ? ' selected="selected"' : '').'>'.$this->mod->t('Use category name').'</option>
					<option value="2"'.($usecategcover==2 ? ' selected="selected"' : '').'>'.$this->mod->t('Use category name and description').'</option>
					<option value="3"'.($usecategcover==3 ? ' selected="selected"' : '').'>'.$this->mod->t('Use categorypage.tpl').'</option>
				</select>
			</div>
		</div>';
		// use category name in header
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Use category name in page header:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="usecategheader" value="1" '.($usecategheader ? 'checked="checked"' : '').'/><br/><br/>
				'.$this->mod->t('If checked, the name of the category is displayed in the header of the page.').'
			</div>
		</div>';
		if($format=="list-prices")
		{
			echo '<h2>'.$this->mod->t('Prices comparison settings').'</h2>';

			echo '<div style="float: left; width: 45%;">';
				// libelle
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Column libelle:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<input type="text" name="pc_collibelle_1" value="'.$pc_collibelle_1.'"/>
					</div>
				</div>';
				// currency
				$currencies = array();
				$currs = Currency::getCurrencies();
				foreach($currs as $curr)
					$currencies[$curr['id_currency']] = $curr;
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Currency:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_currency_1">';
				foreach($currencies as $curr)
					echo '<option value="'.$curr['id_currency'].'" '.($pc_currency_1 == $curr['id_currency'] ? 'selected="selected"' : '').'>'.$curr['name'].'</option>';
				echo '
						</select>
					</div>
				</div>';
				// customer group
				$groups=Group::getGroups((int)$cookie->id_lang);
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Customer group:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_usecustomergroup_1">';
				foreach($groups AS $group)
				{
					echo '<option value="'.$group['id_group'].'"'.($pc_usecustomergroup_1==$group['id_group'] ? ' selected="selected"' : '').'>'.$group['name'].' -'.$group['reduction'].'%</option>';
				}
				echo '
						</select><br/><br/>
						'.$this->mod->t('Use customer group prices and categories.').'
					</div>
				</div>';
				// price colmuns
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Price colmuns:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_pricecolumns_1">';
						echo '<option value="ttc" '.(!empty($pc_pricecolumns_1) && $pc_pricecolumns_1 == "ttc" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Incl. Tax').'</option>';
						echo '<option value="ht" '.(!empty($pc_pricecolumns_1) && $pc_pricecolumns_1 == "ht" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Exc. Tax').'</option>';
						echo '<option value="ttc_ht" '.(!empty($pc_pricecolumns_1) && $pc_pricecolumns_1 == "ttc_ht" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Incl. and Exc. Tax').'</option>';
				echo '
						</select>
					</div>
				</div>';
			echo '</div>';

			echo '<div style="float: left; width: 45%; margin-left: 2%;">';
				// libelle
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Column libelle:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<input type="text" name="pc_collibelle_2" value="'.$pc_collibelle_2.'"/>
					</div>
				</div>';
				// currency
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Currency:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_currency_2">';
				foreach($currencies as $curr)
					echo '<option value="'.$curr['id_currency'].'" '.($pc_currency_2 == $curr['id_currency'] ? 'selected="selected"' : '').'>'.$curr['name'].'</option>';
				echo '
						</select>
					</div>
				</div>';
				// customer group
				$groups=Group::getGroups((int)$cookie->id_lang);
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Customer group:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_usecustomergroup_2">';
				foreach($groups AS $group)
				{
					echo '<option value="'.$group['id_group'].'"'.($pc_usecustomergroup_2==$group['id_group'] ? ' selected="selected"' : '').'>'.$group['name'].' -'.$group['reduction'].'%</option>';
				}
				echo '
						</select><br/><br/>
						'.$this->mod->t('Use customer group prices and categories.').'
					</div>
				</div>';
				// price colmuns
				echo '
				<div style="clear: both;">
					<label>'.$this->mod->t('Price colmuns:').'</label>
					<div class="margin-form" style="padding-top:5px;">
						<select name="pc_pricecolumns_2">';
						echo '<option value="ttc" '.(!empty($pc_pricecolumns_2) && $pc_pricecolumns_2 == "ttc" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Incl. Tax').'</option>';
						echo '<option value="ht" '.(!empty($pc_pricecolumns_2) && $pc_pricecolumns_2 == "ht" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Exc. Tax').'</option>';
						echo '<option value="ttc_ht" '.(!empty($pc_pricecolumns_2) && $pc_pricecolumns_2 == "ttc_ht" ? 'selected="selected"' : '').'>'.$this->mod->t('Price Incl. and Exc. Tax').'</option>';
				echo '
						</select>
					</div>
				</div>';		
			echo '</div>';
			
			echo '<h2>'.$this->mod->t('Settings').'</h2>';
		}
		else
		{
			echo '<h2>'.$this->mod->t('Settings').'</h2>';
			// currency
			$currencies = Currency::getCurrencies();
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Currency:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<select name="currency">';
					foreach($currencies as $curr)
					echo '<option value="'.$curr['id_currency'].'" '.($currency == $curr['id_currency'] ? 'selected="selected"' : '').'>'.$curr['name'].'</option>';
					echo '
					</select>
				</div>
			</div>';
			// customer group
			$groups=Group::getGroups((int)$cookie->id_lang);
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Customer group:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<select name="usecustomergroup">';
			foreach($groups AS $group)
			{
				echo '<option value="'.$group['id_group'].'"'.($usecustomergroup==$group['id_group'] ? ' selected="selected"' : '').'>'.$group['name'].' -'.$group['reduction'].'%</option>';
			}
			echo '
					</select><br/><br/>
					'.$this->mod->t('Use customer group prices and categories.').'
				</div>
			</div>';
	
			// including vat
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Price Incl. Tax:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="vatinc" value="1" '.($vatinc ? 'checked="checked"' : '').'/>
				</div>
			</div>';
	          /****************
	           * ////  price without vat
	           ****************/
			
	          echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Price Excl. Tax').$this->mod->t(':').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="vatexc" value="1" '.($vatexc ? 'checked="checked"' : '').'/>
				</div>
			</div>';
		}
          /****************
           * ////  Empty col
          ****************/
          echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Add empty column?').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="checkbox" name="emptycol" value="1" '.($emptycol ? 'checked="checked"' : '').'/>
			</div>
		</div>';
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Empty column libelle:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" name="emptycollibelle" value="'.$emptycollibelle.'"/>
			</div>
		</div>';
          
            /****************
           * ////  product title
           ****************/

?>

		<div style="clear: both;">
			<label><?php echo $this->mod->t('Product label:'); ?></label>
			<div class="margin-form" style="padding-top:5px;">
				
                    <select name="productTitle">
                         <?php
                         $selected='';
                         if(preg_match('/title/',$pdfConfig->product_title))
                                 $selected='selected="selected"';
                         ?>
                         <option name="title" value="title" <?php echo $selected;?>><?php echo $this->mod->t('Product name'); ?></option>
                         <?php
                         $selected='';
						 
						 
                         if(preg_match('/short/',$pdfConfig->product_title))
                                  $selected='selected="selected"';
                         ?>
                         <option name="shortDesc" value="shortDesc" <?php echo $selected;?>><?php echo $this->mod->t('Product short description');?></option>
                          <?php
                         $selected='';
                         //if(preg_match('/both\-space/',$pdfConfig->product_title))
                         //         $selected='selected="selected"';
                       ?>
                         <option name="both-space" value="both-space" <?php echo $selected;?>><?php echo $this->mod->t('Product title and short Desc (separator: space)');?></option>
					 <?php
                         $selected='';
                         if(preg_match('/both\-comma/',$pdfConfig->product_title))
                                  $selected='selected="selected"';
                         ?>
					<option name="both-comma" value="both-comma" <?php echo $selected;?>><?php echo $this->mod->t('Product title and short Desc (separator: comma)');?></option>
					 <?php 
                         $selected='';
                         if(preg_match('/both\-line/',$pdfConfig->product_title))
                                  $selected='selected="selected"';
                         ?>
					<option name="both-linebreak" value="both-linebreak" <?php echo $selected;?>><?php echo $this->mod->t('Product name and short description (separator: line break)');?></option>
					  
                    </select>

			</div>
		</div>
          <?php
           /****************
           * //// end  product title
           ****************/

          // image product format
          $imagesTypes= ImageType::getImagesTypes('products');
          echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Products\' images format:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="productimageformat">';
          $has_selected = false;
          foreach($imagesTypes AS $imagesType)
          {
	          	$select = '';
	          	if(!empty($productimageformat) && $productimageformat==$imagesType['name'])
	          		$select = 'selected="selected"';
	          	elseif(empty($productimageformat) && ($imagesType["name"]=='large_default' || $imagesType["name"]=='large') )
	          	{	$select = 'selected="selected"'; $has_selected=true; }
	          	elseif(empty($has_selected) && empty($productimageformat) && ($imagesType["name"]=='medium_default' || $imagesType["name"]=='medium') )
	          	{	$select = 'selected="selected"'; $has_selected=true; }
	          	
	          	echo '<option value="'.$imagesType['name'].'" '.$select.'>'.$imagesType['name'].'</option>';
          }
          echo '
				</select><br/><br/>
				'.$this->mod->t('This format of image allows you to define the format which will be used to generate the products\'s images in the PDF. It is not the final size of the image which will be inserted.').'<br/>
				'.$this->mod->t('The bigger the dimensions of the chosen format are, the higher the quality of the generated images will be. However, please note that the weight of the file will be more important as a consequence.').'
			</div>
		</div>';
          
          // font
          if(empty($fontname))
          	$fontname = "helvetica";
          echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Font name:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<select name="fontname">';
          $font_dir = dirname(__FILE__)."/sctcpdf/fonts/";
          foreach (glob($font_dir."*.php") as $font)
          {
          	$font = basename($font, ".php");
          	$select = '';
          	if(!empty($fontname) && $fontname==$font)
          		$select = 'selected="selected"';
          	
          	echo '<option value="'.$font.'" '.$select.'>'.Tools::ucfirst($font).'</option>';
          }
          echo '
				</select><br/><br/>
				'.$this->mod->t('Freeserif font needs to be selected to print Cyrillic characters.').'<br/>
			</div>
		</div>';
          
          // Prices settings
          echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Separator for thousands:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="text" name="thousandssep" value="'.(!empty($thousandssep)?$thousandssep:" ").'"/>
				</div>
			</div>';
          echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Separator for decimals:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="text" name="decimalssep" value="'.(!empty($decimalssep)?$decimalssep:",").'"/>
				</div>
			</div>';
          echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Show supplier reference:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="showSupplierReference" value="1" '.($showSupplierReference ? 'checked="checked"' : '').'/>
				</div>
			</div>
                        <div style="clear: both;">
				<label>'.$this->mod->t('Show stock:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="showStock" value="1" '.((int)$pdfConfig->showStock ? 'checked="checked"' : '').'/>
				</div>
			</div>
                        <div style="clear: both;">
				<label>'.$this->mod->t('Take fake combinations into account:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="dontShowFakeCombinations" value="1" '.((int)$pdfConfig->dontShowFakeCombinations ? 'checked="checked"' : '').'/>
				</div>
			</div>
                        ';
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Hide currency:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="hidecurrency" value="1" '.($hidecurrency ? 'checked="checked"' : '').'/>
				</div>
			</div>';
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Hide decimals:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="hidedecimals" value="1" '.($hidedecimals ? 'checked="checked"' : '').'/>
				</div>
			</div>';
			echo '
			<div style="clear: both;">
				<label>'.$this->mod->t('Do not start a new page for each new category:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<input type="checkbox" name="categorynewpage" value="1" '.($categorynewpage ? 'checked="checked"' : '').'/>
				</div>
			</div>';
          
          /*
           * Display combinations if the catalog is a list or is 1x1
           */

          if(preg_match('/list/',$format)){
               echo '
               <div style="clear: both;">
                    <label>'.$this->mod->t('Combination included:').'</label>
                    <div class="margin-form" style="padding-top:5px;">
                         <input type="checkbox" name="combinationsinc" value="1" '.($combinationsinc ? 'checked="checked"' : '').'/>
                    </div>
               </div>';
		}
          else{
                echo '<input type="hidden" name="combinationsinc" value="0"/>';
		}


		// links
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Use links:').'</label>
			<div class="margin-form" style="padding-top:5px">
				<input type="checkbox" name="uselinks" value="1" '.($uselinks ? 'checked="checked"' : '').'/><br/><br/>
				'.$this->mod->t('If this box is ticked, the reader can then click on product names and images in the PDF catalog to access products directly on website and help you sell more! ').'
			</div>
		</div>';
		// author
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Author:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" name="author" value="'.$author.'"/>
			</div>
		</div>';
		// doccreator
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Document creator:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" name="doccreator" value="'.$doccreator.'"/>
			</div>
		</div>';
		// doctitle
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Document title:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" name="doctitle" value="'.$doctitle.'"/>
			</div>
		</div>';
		// docsubject
		echo '
		<div style="clear: both;">
			<label>'.$this->mod->t('Document subject:').'</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" name="docsubject" value="'.$docsubject.'"/>
			</div>
		</div>';

        


		// special options
		if ($specialOptions != ''){
			echo '<h2>'.$this->mod->t('Special options').'</h2>';
			eval($specialOptions);
			echo $specialoptionsContent;
		}
		// filename
		echo '
		<div style="clear: both; padding-top: 15px">
			<label>'.$this->mod->t('Name of the PDF file to export:').' (.pdf)</label>
			<div class="margin-form" style="padding-top:5px;">
				<input type="text" size="33" id="divfilename" name="divfilename" value="'.($filename!='settings'?$filename:'').'" onblur="$(\'#divfilename\').val(cleanFileName($(\'#divfilename\').val()))"/>
			</div>
		</div>';
		echo '<h2>'.$this->mod->t('Save options').'</h2>';
		// save catalog parameters
		echo '
		<div style="clear: both; padding-top: 15px">
			<label>'.$this->mod->t('Save catalog parameters:').'</label>
			<div class="margin-form" style="padding-top:5px">
				<input type="text" size="33" id="divsaveparam" name="divsaveparam" value="'.($saveparam!='settings'?$saveparam:'').'" onblur="$(\'#divsaveparam\').val(cleanName($(\'#divsaveparam\').val()))"/><br/><br/>
				'.$this->mod->t('Enter the catalog name for saving parameters.').'<br/>
			</div>
		</div><br/>';
		echo '
		<div class="margin-form"><br/><br/>
			<input type="submit" value="'.$this->mod->t('Save and create the PDF catalog').'" name="submitCreate" id="submitCreate" class="button"/>
			<input type="submit" value="'.$this->mod->t('Save, create and open PDF catalog').'" name="submitPrint" id="submitPrint" class="button"/>
		</div>
		</form>
		</fieldset><br/><br/>';
		$html_catalogs = '';
		$catalogs = $this->mod->scanPDFFiles(_PS_MODULE_DIR_.'scpdfcatalog/export/');
		if(!empty($catalogs))
			$html_catalogs = $catalogs;
		else
			$html_catalogs = $this->mod->t('No catalogs.');
    	echo '<fieldset class="width4">
          	<legend>'.$this->mod->t('Generated catalogs').'</legend>
    		<div style="clear: both;">
				<label>'.$this->mod->t('Catalogs:').'</label>
				<div class="margin-form" style="padding-top:5px;">
					'.$html_catalogs.'
				</div>
			</div>
		</fieldset><br/>
    	<fieldset class="width4">
          	<legend>'.$this->mod->t('Block display').'</legend>
    		<div style="clear: both;">
    			<label>'.$this->mod->t('Configuration').'</label>
				<div class="margin-form" style="padding-top:5px;">
					<a href="index.php?'.(version_compare(_PS_VERSION_,'1.5.0.0','>=')?'controller=AdminModules&configure=scpdfcatalog&token=':'tab=AdminModules&configure=scpdfcatalog&token=').Tools::getAdminTokenLite('AdminModules').'">'.$this->mod->t('Managing the block display on the front office').'</a>
				</div>
			</div>
		</fieldset><br/>';
    	$url = '/modules/scpdfcatalog/SCPDF.php?key='.md5(_COOKIE_KEY_).'&format='.$format.'&divsaveparam='.$saveparam.'';
    	if(version_compare(_PS_VERSION_,'1.5.0.0','>='))
    	{
    		$url = SCPDFCatalog::getHttp().$this->context->shop->domain.$url;	
    	}
    	echo '<fieldset class="width4">
          	<legend>'.$this->mod->t('CRON task').'</legend>
			<div style="clear: both;">
				<label>'.$this->mod->t('Use this link to create this pdf catalog from a CRON task:').'</label>
				<div class="margin-form" style="padding-top:5px;">'.$url.'</div>
				<br/><br/>
				<em style="color: #585A69;">
					'.(version_compare(_PS_VERSION_,'1.5.0.0','>=')?$this->mod->t('If working in multistore mode, use WGET to call the CRON task.').'<br/><br/>':'').'
					'.$this->mod->t('For all your URLs, please remember to enter the complete path to obtain correct links.').'<br/>
					<br/>
					'.$this->mod->t('Samples for link:').'
						<dd><strong>'.$this->mod->t('Bad:').'</strong> /my_page.html</dd>
						<dd><strong>'.$this->mod->t('Good:').'</strong>  '.SCPDFCatalog::getHttpHost().'/my_page.html</dd>
					<br/>';
			
				$temp = explode("/modules/",str_replace("\\", "/", dirname(__FILE__)));
				$dir_path = $temp[0];
			
			echo '
					'.$this->mod->t('Samples for image:').'
						<dd><strong>'.$this->mod->t('Bad:').'</strong> ../img/you_image.jpg</dd>
						<dd><strong>'.$this->mod->t('Good:').'</strong>  '.$dir_path.'/img/you_image.jpg</dd>
				</em>
			</div>
       </fieldset>'.
		"<script>
				
			function cleanFileName(str)
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
				str = str.replace(/[^a-z0-9\.\s\'\:\/\[\]-]/g,'');
				str = str.replace(/\.pdf$/i ,'');
				str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
				str = str.replace(/[ ]/g,'-');
				str = str.replace(/[\/]/g,'-');
				return str;
			} 
			function cleanName(str)
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
				str = str.replace(/[^a-z0-9\_\.\s\'\:\/\[\]-]/g,'');
				str = str.replace(/[\.]/g,'_');
				str = str.replace(/[\s\'\:\/\[\]-]+/g,' ');
				str = str.replace(/[ ]/g,'-');
				str = str.replace(/[\/]/g,'-');
				return str;
			} 
					
			function rewriteNames()
			{
				$('#divfilename').val(cleanFileName($('#divfilename').val()));
				$('#divsaveparam').val(cleanName($('#divsaveparam').val()));
			}
					
			function checkParams(){
        		err_msg='';
				if ($('#categlist').val()==null)
					err_msg += '".str_replace("'","\'",$this->mod->t('You must select a category.'))."';
				if ($('#divsaveparam').val()=='')
					err_msg += '".str_replace("'","\'",$this->mod->t('You must set a name to your configuration.'))."';
				if ($('#divfilename').val()=='')
					err_msg += '".str_replace("'","\'",$this->mod->t('You must set a filename to your PDF catalog.'))."';
				if (err_msg!=''){
					alert(err_msg);
					return false;
				}
				$('#pdfform').attr('action',$('#pdfform').attr('action')+'&catalogsettings='+$('#divsaveparam').val());
				return true;
			}
		</script>";
		//TinyMCE
		if (version_compare(_PS_VERSION_,'1.4.1.0','>=')){
			$iso = Language::getIsoById((int)($cookie->id_lang));
			$isoTinyMCE = (file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en');
			$ad = dirname($_SERVER["PHP_SELF"]);
			$defaultLanguage = (int)(Configuration::get('PS_LANG_DEFAULT'));
		echo  '
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
		echo  '
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
		echo  '
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
		echo  '
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
		echo  '
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
	}

	public function postProcess()
	{
		global $currentIndex,$link,$paramFormat,$divsaveparam;
		$link = new Link();

		if(Tools::getValue('submitPrint') || Tools::getValue('submitCreate'))
		{
			$paramFormat = Tools::getValue('format');
			if (!$pdfConfigDef = simplexml_load_file(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/settings.xml'))
				return false;

	      $divsaveparam = Tools::getValue('divsaveparam');
				$divsaveparam = trim($divsaveparam);
				
	      $logo_config=Tools::getValue('thelogo');
	
			/*
			 * Validation of the custom logo
			 */
	
	      if (isset($_FILES['newlogo']) AND isset($_FILES['newlogo']['tmp_name']) AND !empty($_FILES['newlogo']['tmp_name']))
		  {
	      	/*
	      	* Check if the logo's extension is valid
	      	*/
	      	
	      	$regex_extensions=implode('|',$this->logo_extensions);
	      	$error_logo=false;
	      	
	      	if(!preg_match('/\.('.$regex_extensions.')$/i',$_FILES['newlogo']['name'],$tmatches))
	      	{
	      	     $error_logo=true;
	      	     $_SESSION['scpdfcatalog']['error_logo'][]='extension';
	      	}
	      	
	      	$extension_image=$tmatches[0];
	      	if($error_logo!=TRUE){
	      	     //add a time marker to prevent firefox image cache problem
	      	     $logo_config='logo-'.$divsaveparam.'-'.date('Y-m-d-H-i-s').$extension_image;
	      	
	      	     if (!move_uploaded_file($_FILES['newlogo']['tmp_name'],_PS_MODULE_DIR_.'scpdfcatalog/templates/'.Tools::getValue('format').'/catalog/'.$logo_config))
	      	          $_SESSION['scpdfcatalog']['error_logo'][]='move_uploaded_file';
	      	
	      	     @unlink(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.Tools::getValue('format').'/catalog/'.Tools::getValue('thelogo'));
					}
				}else if(Tools::getValue('defaultlogo_q')=='yes') {
	
	           //there's no custom logo, so we pick up the shop's one
	           //Even it creates a copy of the existing shop log, it will get things more simplier
	           //in the next part (createCatalog)
	
	           $logo_shop_path=_PS_IMG_DIR_.'/logo.jpg';
	
	           copy($logo_shop_path,_PS_MODULE_DIR_.'scpdfcatalog/templates/'.Tools::getValue('format').'/catalog/logo-'.$divsaveparam.'-'.date('Y-m-d-H-i-s').'.jpg');
	           $logo=_PS_MODULE_DIR_.'scpdfcatalog/templates/'.Tools::getValue('format').'/catalog/logo-'.$divsaveparam.'.jpg';
	           $logo_config='logo-'.$divsaveparam.'-'.date('Y-m-d-H-i-s').'.jpg';
	           
	      }else if(Tools::getValue('defaultlogo_q')=='no'){
	           $logo_config=Tools::getValue('thelogo');
	      }
	
	      /*
	       * Bilan :
	       * Si aucun logo n'a été indiqué, le logo de la boutique est automatiquement dupliqué dans le format .
	       * Si un logo a été spécifié alors il figure dans le répertoire du format.
	       * Ainsi il existe toujours un logo pour le catalogue, toujours situé au même endroit
	       */

			$content = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
			$content.= '<pdfparams>'."\n";

			$content.= '<name><![CDATA['.$divsaveparam.']]></name>'."\n";
			$content.= '<title><![CDATA['.Tools::getValue('divtitle').']]></title>'."\n";
			$content.= '<filename><![CDATA['.Tools::getValue('divfilename').']]></filename>'."\n";
			$data = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', Tools::getValue('divlegalnotice'));
			$content.= '<legalnotice><![CDATA['.$data.']]></legalnotice>'."\n";
			$data = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', Tools::getValue('divfooter'));
			$content.= '<footer><![CDATA['.$data.']]></footer>'."\n";
			$content.= '<idlang>'.Tools::getValue('id_lang').'</idlang>'."\n";
			$content.= '<firstpage>'.Tools::getValue('firstpage').'</firstpage>'."\n";
			$content.= '<tocdisplay>'.Tools::getValue('tocdisplay').'</tocdisplay>'."\n";
			$content.= '<pagenumber>'.Tools::getValue('pagenumber').'</pagenumber>'."\n";
			$content.= '<format><![CDATA['.Tools::getValue('format').']]></format>'."\n";
			$content.= '<uselinks>'.Tools::getValue('uselinks').'</uselinks>'."\n";
			$content.= '<usecategcover>'.Tools::getValue('usecategcover').'</usecategcover>'."\n";
			$content.= '<usecategheader>'.Tools::getValue('usecategheader').'</usecategheader>'."\n";
			$content.= '<activeproduct>'.Tools::getValue('activeproduct').'</activeproduct>'."\n";
			$content.= '<withstockproduct>'.Tools::getValue('withstockproduct').'</withstockproduct>'."\n";
			$content.= '<filterbybrand>'.Tools::getValue('filterbybrand').'</filterbybrand>'."\n";
			$content.= '<orderby><![CDATA['.Tools::getValue('orderby').']]></orderby>'."\n";
			$content.= '<xdaysago><![CDATA['.Tools::getValue('xdaysago').']]></xdaysago>'."\n";
			$content.= '<doctitle><![CDATA['.Tools::getValue('doctitle').']]></doctitle>'."\n";
			$content.= '<docsubject><![CDATA['.Tools::getValue('docsubject').']]></docsubject>'."\n";
			$content.= '<doccreator><![CDATA['.Tools::getValue('doccreator').']]></doccreator>'."\n";
      		$content.= '<doclogo><![CDATA['.$logo_config.']]></doclogo>'."\n";
			$content.= '<author><![CDATA['.Tools::getValue('author').']]></author>'."\n";
			$post_categlist = Tools::getValue("categlist",0);
			$content.= '<categlist><![CDATA['.(!empty($post_categlist) && is_array($post_categlist)?join(',',$post_categlist):'').']]></categlist>'."\n";
			$content.= '<currency>'.Tools::getValue('currency').'</currency>'."\n";
			$content.= '<usecustomergroup>'.Tools::getValue('usecustomergroup').'</usecustomergroup>'."\n";
			$content.= '<productimageformat>'.Tools::getValue('productimageformat').'</productimageformat>'."\n";
			$content.= '<fontname>'.Tools::getValue('fontname').'</fontname>'."\n";
			$content.= '<vatinc>'.Tools::getValue('vatinc').'</vatinc>'."\n";
		    $content.= '<vatexc>'.Tools::getValue('vatexc').'</vatexc>'."\n";
		    $content.= '<emptycol>'.Tools::getValue('emptycol').'</emptycol>'."\n";
		    $content.= '<emptycollibelle>'.Tools::getValue('emptycollibelle').'</emptycollibelle>'."\n";
		    $content.= '<product_title>'.Tools::getValue('productTitle').'</product_title>'."\n";
		    $content.= '<combinationsinc>'.Tools::getValue('combinationsinc').'</combinationsinc>'."\n";
		    $content.= '<thousandssep>'.Tools::getValue('thousandssep').'</thousandssep>'."\n";
		    $content.= '<decimalssep>'.Tools::getValue('decimalssep').'</decimalssep>'."\n";
		    $content.= '<hidecurrency>'.Tools::getValue('hidecurrency').'</hidecurrency>'."\n";
                    $content.= '<showSupplierReference>'.Tools::getValue('showSupplierReference').'</showSupplierReference>'."\n";
                    $content.= '<showStock>'.Tools::getValue('showStock').'</showStock>'."\n";
                    $content.= '<dontShowFakeCombinations>'.Tools::getValue('dontShowFakeCombinations').'</dontShowFakeCombinations>'."\n";
		    $content.= '<hidedecimals>'.Tools::getValue('hidedecimals').'</hidedecimals>'."\n";
		    $content.= '<categorynewpage>'.Tools::getValue('categorynewpage').'</categorynewpage>'."\n";
		    

		    $content.= '<pc_collibelle_1>'.Tools::getValue('pc_collibelle_1','').'</pc_collibelle_1>'."\n";
		    $content.= '<pc_collibelle_2>'.Tools::getValue('pc_collibelle_2','').'</pc_collibelle_2>'."\n";
		    $content.= '<pc_currency_1>'.Tools::getValue('pc_currency_1','').'</pc_currency_1>'."\n";
		    $content.= '<pc_currency_2>'.Tools::getValue('pc_currency_2','').'</pc_currency_2>'."\n";
		    $content.= '<pc_usecustomergroup_1>'.Tools::getValue('pc_usecustomergroup_1','').'</pc_usecustomergroup_1>'."\n";
		    $content.= '<pc_usecustomergroup_2>'.Tools::getValue('pc_usecustomergroup_2','').'</pc_usecustomergroup_2>'."\n";
		    $content.= '<pc_pricecolumns_1>'.Tools::getValue('pc_pricecolumns_1','').'</pc_pricecolumns_1>'."\n";
		    $content.= '<pc_pricecolumns_2>'.Tools::getValue('pc_pricecolumns_2','').'</pc_pricecolumns_2>'."\n";
		    
			$specialoptionssetval = (string)$pdfConfigDef->specialoptionssetval;
			if ($specialoptionssetval != ''){
				eval($specialoptionssetval);
			}
			$content.= '</pdfparams>'."\n";

      		/*if(Tools::strlen($divsaveparam)>0)
      		{
				file_put_contents(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/catalog/'.$divsaveparam.'.xml', $content);
    		}*/

			file_put_contents(_PS_MODULE_DIR_.'scpdfcatalog/templates/'.$paramFormat.'/catalog/'.$divsaveparam.'.xml', $content);
			if(Tools::getValue('submitCreate'))
			{
				/*$PDFCatalogFromAdmin = 1;
				$_GET['key']=md5(_COOKIE_KEY_);
				$_GET['format']=$paramFormat;
				$_GET['divsaveparam']=$divsaveparam;
				require_once(_PS_MODULE_DIR_.'scpdfcatalog/SCPDF.php');*/
				/*echo '
				<div class="conf">
					<img src="../modules/scpdfcatalog/ok.png" />
					'.$this->mod->t('PDF file created').' <a target="_blank" href="'._MODULE_DIR_.'scpdfcatalog/export/'.$filename.'"> » '.$this->mod->t('Download').'</a>
				</div>';*/
				/*echo '<div id="result_pdf" class="warn"><img src="../modules/scpdfcatalog/loading.gif" /> '.$this->mod->t('The PDF file is being generated. Please wait!').'</div>
           		<script>
           		$(document).ready(function(){
					$.post(SCPDFCatalog::getHttpHost().$_SERVER['REQUEST_URI'].'&display_SCPDF=1", function( data ) {
						if(data!=undefined && data!=null && data=="ok")
                    		$("#result_pdf").attr("class", "").addClass("conf").html(\'<img src="../modules/scpdfcatalog/ok.png" /> '.$this->mod->t('PDF file created').' <a target="_blank" href="'._MODULE_DIR_.'scpdfcatalog/export/'.Tools::getValue('divfilename').'.pdf"> » '.$this->mod->t('Download').'</a>\');
                		else
                			$("#result_pdf").attr("class", "").addClass("error").html("'.$this->mod->t('Error while during PDF generating!').' "+data);
					});
				});
           		</script>';*/
				echo '<div class="conf" style="display: none;">
					<img src="../modules/scpdfcatalog/ok.png" /> '.$this->mod->t('PDF file created').' <a target="_blank" href="'._MODULE_DIR_.'scpdfcatalog/export/'.Tools::getValue('divfilename').'.pdf"> » '.$this->mod->t('Download').'</a>
				</div>';
				echo '<script>window.open("'.SCPDFCatalog::getHttpHost().$_SERVER['REQUEST_URI'].'&catalogsettings='.Tools::getValue('divfilename').'&generate_SCPDF=1")</script>';
			}
			if(Tools::getValue('submitPrint'))
				echo '<script>window.open("'.SCPDFCatalog::getHttpHost().$_SERVER['REQUEST_URI'].'&catalogsettings='.Tools::getValue('divfilename').'&display_SCPDF=1")</script>';
		}
		if(Tools::getValue('submitBlock')){
			Configuration::updateValue('PS_SC_PDFCATALOG_BLOCKDISPLAY', Tools::getValue('blockdisplay'));
			Configuration::updateValue('PS_SC_PDFCATALOG_BLOCKTITLE', Tools::getValue('divblocktitle'));
			Configuration::updateValue('PS_SC_PDFCATALOG_BLOCKCONTENT', Tools::getValue('divblockcontent'),true);
			Tools::redirectAdmin('index.php?tab=AdminSCPDFCatalog&token='.$this->token);
		}
	}

	public function recurseCategory($categories, $current, $selectedcategories, $id_category = 1)
	{
		if($id_category != 1 && $this->hideCategoryPosition($current['infos']['name'])!='SC Recycle Bin'){
			echo '<option value="'.$id_category.'"'.(in_array($id_category,$selectedcategories) ? ' selected="selected"' : '').'>'.
			str_repeat(' ', $current['infos']['level_depth'] * 5).$this->hideCategoryPosition(Tools::stripslashes($current['infos']['name'])).'</option>';
		}
		if (isset($categories[$id_category]) && $this->hideCategoryPosition($current['infos']['name'])!='SC Recycle Bin')
			foreach ($categories[$id_category] AS $key => $row)
					$this->recurseCategory($categories, $categories[$id_category][$key], $selectedcategories, $key);
	}

	public function hideCategoryPosition($name)
	{
		return preg_replace('/^[0-9]+\./', '', $name);
	}

	public static function addJqueryPlugin()
	{}
	public static function addJquery()
	{}
	public static function addCss()
	{}
	public static function addJs()
	{}
	public static function addJqueryUI()
	{}

}
