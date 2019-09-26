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


?>
<script type="text/javascript">
	dhxlCatImport=wImpExpTranslation.attachLayout("1C");
	dhxlCatImport.cells('a').hideHeader();

	var init_tabs = [
		'group_feature',
		'feature_value',
		'group_attribute',
		'attribute_value'
	];

	var all_tabs = [];
	init_tabs.forEach(function(tab){
		all_tabs.push({
			id : tab+'_export',
			text: "<?php echo _l('Export',1)?>"
		},{
			id : tab+'_import',
			text: "<?php echo _l('Import',1)?>"
		});

	});

	wImpExpTranslation.tabbar=dhxlCatImport.cells('a').attachTabbar({
		tabs: all_tabs
	});

	wImpExpTranslation.tbOptions=dhxlCatImport.cells('a').attachToolbar();
	var opts = [
		['select_group_feature', 'obj', "<?php echo _l('Features - groups')?>", ''],
		['select_feature', 'obj', "<?php echo _l('Features - values')?>", ''],
		['select_group_attribute', 'obj', "<?php echo _l('Combinations - groups')?>", ''],
		['select_attribute', 'obj', "<?php echo _l('Combinations - attributes')?>", '']
	];

	wImpExpTranslation.setText('<?php echo _l('Export/Import translations for',1)?> <strong>'+ opts[0][2] + '</strong>');
	wImpExpTranslation.tbOptions.addButton('import',100,'','lib/img/database_add.png','lib/img/database_add.png');
	wImpExpTranslation.tbOptions.setItemToolTip('import','<?php echo _l('Import translations',1)?>');
    wImpExpTranslation.tbOptions.addButton('help',100,'','lib/img/help.png','lib/img/help.png');
    wImpExpTranslation.tbOptions.setItemToolTip('help','<?php echo _l('Help',1)?>');
	wImpExpTranslation.tbOptions.addButton("selectall", 0, "", "lib/img/application_lightning.png", "lib/img/application_lightning_dis.png");
	wImpExpTranslation.tbOptions.setItemToolTip('selectall','<?php echo _l('Select all')?>');
	wImpExpTranslation.tbOptions.addButtonSelect("selected_item", 0, opts[0][2], opts, "lib/img/table_gear.png", "lib/img/table_gear.png",false,true);
	wImpExpTranslation.tbOptions.attachEvent("onClick",
		function(id){
			if (id=='help')
			{
                <?php
                $iso = Language::getIsoById((int)($sc_agent->id_lang));
                if($iso == 'fr') {
                ?>
                    window.open('https://www.storecommander.com/support/fr/sonia/1125-outil-de-traduction-caracteristiques-et-attributs-de-declinaisons.html');
                <?php } else { ?>
                    window.open('https://www.storecommander.com/support/en/sonia/1125-translation-tool-for-features-combination-attributes.html');
                <?php } ?>

			}
			if (id=='selectall')
			{
				var activeTab = wImpExpTranslation.tabbar.getActiveTab();
				$('#'+activeTab).select();
			}
			if (id=='import')
			{
                var activeTab = wImpExpTranslation.tabbar.getActiveTab();
                var exportTab = activeTab.replace('_import', '_export');
                var export_content = $('#' + exportTab).val();
                var export_content_arr = export_content.split('\n');

				var tmp_check_import = activeTab.split('_');
				if(tmp_check_import[2] == 'import') {
					var content = $('#' + activeTab).val();
					var content_arr = content.split('\n');

                    if(export_content_arr[0] !== content_arr[0]) {
                        dhtmlx.message({text:'<?php echo _l('The first import line does not match the first export line',1)?>',type:'error'});
                    } else {
                        content = JSON.stringify(content);
                        if(content != 'undefined' && content != '' && content != false) {
                            $.post('index.php?ajax=1&act=cat_win-impexptranslation_update',{'tab':activeTab, 'id_lang':SC_ID_LANG, 'content':content},function(data){
                                if(data.error === true) {
                                    dhtmlx.message({text:data.message,type:'error'});
                                } else {
                                    dhtmlx.message({text:data.message,type:'info'});
                                    afterSelectSection(data.id_item);
                                }

                            },"json");
                        }
                    }
				} else {
					dhtmlx.message({text:'<?php echo _l('You need to open the "import" tab',1)?>',type:'error'});
				}
			}
			if (id=='select_group_feature')
			{
				afterSelectSection(0);
			}
			if (id=='select_feature')
			{
				afterSelectSection(1);
			}
			if (id=='select_group_attribute')
			{
				afterSelectSection(2);
			}
			if (id=='select_attribute')
			{
				afterSelectSection(3);
			}
		});

	displayDataExport();
	
//#####################################
//############ Load functions
//#####################################

function afterSelectSection(id)
{
	var title_opt = opts[id][2];
	wImpExpTranslation.tbOptions.setItemText("selected_item", title_opt);
	wImpExpTranslation.setText('<?php echo _l('Export/Import translations for',1)?> <strong>'+ title_opt + '</strong>');
	displayDataExport(init_tabs[id]);
}

function displayDataExport(tab_section)
{
	var tab_to_show = init_tabs[0];
	if(tab_section != 'undefined' && tab_section != '' && tab_section != null) {
		tab_to_show = tab_section;
	}

	all_tabs.forEach(function(tab)
	{
		var content = '<textarea id="' + tab.id + '" style="width:100%;height:100%"></textarea>';
        if(tab.id == tab_to_show+'_import') {
            content = '<textarea id="' + tab.id + '" style="width:100%;height:100%" placeholder="<?php echo _l('Copy/paste the data',1)?>"></textarea>';
        }

		wImpExpTranslation.tabbar.cells(tab.id).attachHTMLString(content);
		if(tab.id != tab_to_show+'_export' && tab.id != tab_to_show+'_import') {
			wImpExpTranslation.tabbar.cells(tab.id).hide();
		} else {
			wImpExpTranslation.tabbar.cells(tab.id).show();

			if(tab.id == tab_to_show+'_export') {
				wImpExpTranslation.tabbar.cells(tab.id).setActive();
				$.post('index.php?ajax=1&act=cat_win-impexptranslation_get',{'action':tab_to_show, 'id_lang':SC_ID_LANG},function(data){
					$('#'+tab.id).html(data);
					$('#'+tab.id).select();
				});
			}
		}
	});
}

</script>
<div id="alertbox" style="width:400px;height:200px;color:#FFFFFF" onclick="stopCatAlert();">Click here to close alert.</div>
