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

if(_r("GRI_CMS_PROPERTIES_GRID_DESC")) { ?>
	prop_tb.addListOption('panel', 'descriptions', 2, "button", '<?php echo _l('Descriptions',1)?>', "lib/img/description.png");
	allowed_properties_panel[allowed_properties_panel.length] = "descriptions";
	prop_tb.addButton("description_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('description_refresh','<?php echo _l('Refresh',1)?>');
	prop_tb.addButton('desc_save',100,'','lib/img/page_save.png','lib/img/page_save.png');
	prop_tb.setItemToolTip('desc_save','<?php echo _l('Save descriptions',1)?>');

	needInitDescriptions = 1;
	function initDescriptions(){
		if (needInitDescriptions)
		{
			prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('1C');
			prop_tb._descriptionsLayout.cells('a').hideHeader();
			<?php if(_s("APP_RICH_EDITOR")==1) { ?>
				prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_tinymce'+URLOptions);
			<?php } else { ?>
				prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_ckeditor'+URLOptions);
			<?php } ?>
			dhxLayout.cells('b').showHeader();
			needInitDescriptions=0;
		}
	}



	function setPropertiesPanel_descriptions(id) {
		// ask to save description if modified
		if (propertiesPanel=='descriptions' && id!='desc_save' && typeof prop_tb._descriptionsLayout!='undefined')
			prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();

		if (id=='descriptions')
		{
			hidePropTBButtons();
			prop_tb.showItem('description_refresh');
			prop_tb.showItem('desc_save');
			prop_tb.setItemText('panel', '<?php echo _l('Descriptions',1)?>');
			prop_tb.setItemImage('panel', 'lib/img/description.png');
			URLOptions='';
			if (lastcms_pageID!=0) URLOptions='&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG+'&id_shop='+shopselection;
			needInitDescriptions = 1;
			initDescriptions();
			propertiesPanel='descriptions';
			dhxLayout.cells('b').setWidth(680);//605
		}

		if (id=='desc_save')
		{
			<?php if(_s("APP_RICH_EDITOR")!=1) { ?>
				prop_tb._descriptionsLayout.cells('a').progressOn();
			<?php } ?>
			prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxSave();
		}

		if (id=='description_refresh')
		{
			<?php if(_s("APP_RICH_EDITOR")!=1) { ?>
				prop_tb._descriptionsLayout.cells('a').progressOn();
			<?php } ?>
			prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
		}

		if(id=='desc_fouleFactory')
		{
			showWCatFoulefactory();
		}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_descriptions);

	<?php if (SCMS) { ?>
		cms_shoptree.attachEvent("onClick",function(){
			if (lastcms_pageID!=0) {
				<?php if(_s("APP_RICH_EDITOR")!=1) { ?>
					prop_tb._descriptionsLayout.cells('a').progressOn();
				<?php } ?>
				prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
			}
		});
	<?php } ?>

	cms_grid_tb.attachEvent("onClick",function(id){
		<?php
		$tmp=array();
		$clang=_l('Language');
		foreach($languages AS $lang){

			if(_s("APP_RICH_EDITOR")==1) {
				$type = 'tinymce';
			} else {
				$type = 'ckeditor';
			}

			echo"
				if (id=='cms_lang_".$lang['iso_code']."')
				{
					if (lastcms_pageID!=0) URLOptions='&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG+'id_shop='+shopselection;
						prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=cms_description_".$type."'+URLOptions);
						
					if (propertiesPanel=='descriptions' && typeof prop_tb._descriptionsLayout!='undefined')
						prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
				}";
		}
		?>
	});


	cms_grid.attachEvent("onRowSelect",function (idCms){
		lastcms_pageID=idCms;
		idxCmsName=cms_grid.getColIndexById('meta_title');
		if (propertiesPanel=='descriptions')
		{
			prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
			dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+cms_grid.cells(lastcms_pageID,idxCmsName).getValue());
			<?php if(_s("APP_RICH_EDITOR")!=1) { ?>
				prop_tb._descriptionsLayout.cells('a').progressOn();
			<?php } ?>
			prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_cms='+lastcms_pageID+'&id_lang='+SC_ID_LANG,lastcms_pageID,SC_ID_LANG,shopselection);
		}
	});

<?php } ?>
