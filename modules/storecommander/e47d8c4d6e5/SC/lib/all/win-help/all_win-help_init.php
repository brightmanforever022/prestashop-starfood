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
/*
**
** displayHelpWindow( htype , hcontenturl , hwidth , hheight , htitle )
**
** htype : grid , ...
** hcontenturl : filename (cat_win-help_scas_xml)
** hwidth : width
** hheight : height
** htitle : translated title
**
*/
?>
<script type="text/javascript">
	function displayHelpWindow(htype,hcontenturl,hwidth,hheight,htitle){
		if (!dhxWins.isWindow("wHelp"+hcontenturl))
		{
			wHelp = dhxWins.createWindow("wHelp"+hcontenturl, ($(window).width()/2-200), 100, hwidth, hheight);
			wHelp.setIcon('lib/img/help.png','../../../lib/img/help.png');
			wHelp.setText(htitle);
			wHelp._content = wHelp.attachLayout("1C");
			wHelp._content.cells('a').hideHeader();
			wHelp.attachEvent("onClose", function(win){
					win.hide();
					return false;
				});
			if (htype == 'grid'){
				wHelp._content._grid = wHelp._content.cells('a').attachGrid();
				wHelp._content._grid.enableMultiline(true);
				wHelp._content._grid.setImagePath("lib/js/imgs/");
			}
			wHelp.show();
			$.post("index.php?ajax=1&act="+hcontenturl+"&id_lang="+SC_ID_LANG,function(data)
			{
				wHelp._content._grid.parse(data);
			});
		}else{
			dhxWins.window("wHelp"+hcontenturl).show();
		}	
	}
</script>