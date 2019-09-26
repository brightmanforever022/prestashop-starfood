		$('textarea.tinymce1').tinymce({
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
 echo (_s('CAT_PROPERTIES_DESCRIPTION_CSS')?'content_css : pathCSS+"global.css",':''); ?>
			width: "100%",
			height: "150px",
			font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
			elements : "nourlconvert,ajaxfilemanager",
			file_browser_callback : "ajaxfilemanager",
			entity_encoding: "raw",
			convert_urls : false,
	    language : iso,
			onchange_callback : "checkSizetMCE",
			handle_event_callback : "checkSizetMCE"
		});
		$('textarea.tinymce2').tinymce({
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
			height: "220px",
			font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
			elements : "nourlconvert,ajaxfilemanager",
			file_browser_callback : "ajaxfilemanager",
			entity_encoding: "raw",
			convert_urls : false,
	    language : iso,
			onchange_callback : "checkSizetMCE",
			handle_event_callback : "checkSizetMCE"
		});