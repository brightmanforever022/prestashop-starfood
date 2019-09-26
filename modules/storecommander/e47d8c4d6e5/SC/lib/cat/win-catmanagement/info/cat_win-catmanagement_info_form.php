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
 
$id_lang=intval(Tools::getValue('id_lang'));
$id_row=(Tools::getValue('id_row', 0));

if(SCMS)
	list($id_category,$id_lang,$id_shop) = explode("_",$id_row);
else
	list($id_category,$id_lang) = explode("_",$id_row);

$error = "";
$success = false;

if(isset($_POST["submitUpdate"]) && isset($_POST["description"]))
{
	$sql = "UPDATE "._DB_PREFIX_."category_lang SET description='".pSQL($_POST["description"],1)."' WHERE id_category=".intval($id_category)." AND id_lang=".intval($id_lang)." ";
	if(SCMS)
		$sql .= " AND id_shop=".intval($id_shop);
	Db::getInstance()->Execute($sql);
	$success = true;
}

$sql = "SELECT description FROM "._DB_PREFIX_."category_lang WHERE id_category=".intval($id_category)." AND id_lang=".intval($id_lang)." ";
if(SCMS)
	$sql .= " AND id_shop=".intval($id_shop);
$res = Db::getInstance()->ExecuteS($sql);
$description_val = "";
if(!empty($res[0]["description"]))
	$description_val = $res[0]["description"];

?><style type="text/css">
.btn {
	background: linear-gradient(#e2efff, #d3e7ff) repeat scroll 0 0 rgba(0, 0, 0, 0);
    border: 1px solid #a4bed4;
    color: #34404b;
    font-size: 11px;
    height: 27px;
    overflow: hidden;
    position: relative;
	font-weight: bold;
	cursor: pointer;
	float: right;
	margin-top: 6px;
}
</style>
<?php if(version_compare(_PS_VERSION_, '1.5.6.0', '>=')) { ?>
	<link type="text/css" rel="stylesheet" href="<?php echo SC_CSSSTYLE;?>" />
	<script type="text/javascript" src="<?php echo SC_JQUERY;?>"></script>
	<script type="text/javascript">
		<?php echo 'var pathCSS = \''._THEME_CSS_DIR_.'\' ;' ?>
	</script>
	<?php if(_s("APP_RICH_EDITOR")==1) { ?>
		<script type="text/javascript" src="lib/js/tiny_mce/tiny_mce.js"></script>
		<script type="text/javascript" src="lib/js/tiny_mce/jquery.tinymce.js"></script>
		<?php
				$iso = Language::getIsoById((int)($sc_agent->id_lang));
				echo '
		<script type="text/javascript">
		var iso = \''.(file_exists('lib/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en').'\' ;
		var pathTiny = \'lib/js/tiny_mce/tiny_mce.js\' ;
		var add = \'lib/js/\' ;
		</script>';
		?>
		<script type="text/javascript">
		$().ready(function() {
			$('textarea#description').tinymce({
				script_url : 'lib/js/tiny_mce/tiny_mce.js',
				mode : "specific_textareas",
				theme : "advanced",
				skin:"default",
				editor_selector : "rte",
				editor_deselector : "noEditor",
				plugins : "spellchecker,safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
				theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,media,|,ltr,rtl,pagebreak,|,fullscreen,|,spellchecker",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true,
				theme_advanced_source_editor_width : 580,
				extended_valid_elements : "iframe[src|width|height|name|align]",
		    <?php echo (_s('CAT_PROPERTIES_DESCRIPTION_CSS')?'content_css : pathCSS+"global.css",':''); ?>
				width: "100%",
				height: "150px",
				font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
				elements : "nourlconvert,ajaxfilemanager",
				file_browser_callback : "ajaxfilemanager",
				entity_encoding: "raw",
				convert_urls : false,
		    	language : iso
			});
		});
	
		function ajaxfilemanager(field_name, url, type, win) {
			var ajaxfilemanagerurl = add+"ajaxfilemanager/ajaxfilemanager.php?language="+(iso=='fr'?'fr':'en'); // not ready ?language="+iso;
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
		        url: ajaxfilemanagerurl,
		        width: 580,
		        height: 440,
		        inline : "yes",
		        close_previous : "no"
		    },{
		        window : win,
		        input : field_name
		    });
		}
		</script>
	<?php } else {
        $iso = UISettings::getSetting('forceSCLangIso');
        if(empty($iso))
            $iso = Language::getIsoById((int)($sc_agent->id_lang));
        if (version_compare(_PS_VERSION_, '1.7.0.0', '>='))
            $sql = 'SELECT locale FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
        else
            $sql = 'SELECT language_code FROM '._DB_PREFIX_.'lang WHERE iso_code = "'.pSQL($iso).'"';
        $lang_iso = Db::getInstance()->getValue($sql);
        list($min,$maj) = explode("-", $lang_iso);
        $lang_iso = strtolower($min).'_'.strtoupper($maj);
	    ?>
		<script src="lib/js/ckeditor/ckeditor.js"></script>
		<script type="text/javascript">
        <?php echo 'var langIso = "'.$lang_iso.'" ;' ?>

        var activeSCAYT = <?php echo (_s('APP_CKEDITOR_AUTOCORRECT_ACTIVE')=="1"?"true":"false"); ?>;
		CKEDITOR.config.customConfig="config03.js";
		$(document).ready(function(){
			CKEDITOR.replace( 'description');
			<?php if(_s('CAT_PROPERTIES_DESCRIPTION_CSS')) { ?>CKEDITOR.config.contentsCss = pathCSS+"global.css" ;<?php } ?>
		});
		</script>
		
	<?php }
} ?>
<script type="text/javascript">
<?php if($success) { ?>
parent.getCatManagementPropInfo();
parent.cat_prop_info.cells('b').collapse();
<?php } ?>
</script>

<form method="POST" action="">

	<?php if(version_compare(_PS_VERSION_, '1.5.6.0', '<')) { ?>Description :<br/><?php } ?>
	<textarea name="description" id="description" style="width: 100%; height: 10em;"><?php echo $description_val; ?></textarea>
	<?php if(version_compare(_PS_VERSION_, '1.5.6.0', '<')) { ?><br/><?php } ?> 
    <button class="btn" name="submitUpdate" type="submit"><?php echo _l('Update');?></button>
    
    <input type="hidden" name="id_row" value="<?php echo $id_row; ?>" />

</form>
