//
// SCPDFCatalog Module for PrestaShop created by http://www.storecommander.com
// Version 2.0.1
//

tinyMCE.init({
	mode : "textareas",
	theme : "advanced",
	skin: "cirkuit",
	plugins : "safari,pagebreak,style,table,advimage,advlink,inlinepopups,media,contextmenu,paste,fullscreen,xhtmlxtras,preview",
	editor_selector : "rte",
	// Theme options
	theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfullcut,|,copy,paste,|,undo,redo,|,link,unlink,|,code",
	theme_advanced_buttons2 : "styleselect,formatselect,fontselect,fontsizeselect,|,help",
	theme_advanced_buttons3 : "tablecontrols,|,fullscreen,|,styleprops",
	theme_advanced_buttons4 : "",
	theme_advanced_toolbar_location : "top",
	theme_advanced_toolbar_align : "left",
	theme_advanced_statusbar_location : "bottom",
	theme_advanced_resizing : true,
	width : '500',
	height : '200',
	verify_html : true,
	content_css : pathCSS+"global.css",
	document_base_url : ad,
	font_size_style_values : "8pt, 10pt, 12pt, 14pt, 18pt, 24pt, 36pt",
	elements : "nourlconvert,ajaxfilemanager",
	file_browser_callback : "ajaxfilemanager",
	entity_encoding: "raw",
	convert_urls : false,
	language : iso
});

function ajaxfilemanager(field_name, url, type, win) {
	var ajaxfilemanagerurl = ad+"/ajaxfilemanager/ajaxfilemanager.php";
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
		width: 782,
		height: 440,
		inline : "yes",
		close_previous : "no"
	},{
		window : win,
		input : field_name
	});
}
