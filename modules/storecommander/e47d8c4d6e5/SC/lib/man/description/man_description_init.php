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

if(_r("GRI_MAN_PROPERTIES_GRID_DESC")) { ?>
	prop_tb.addListOption('panel', 'descriptions', 2, "button", '<?php echo _l('Descriptions',1)?>', "lib/img/description.png");
	allowed_properties_panel[allowed_properties_panel.length] = "descriptions";
	prop_tb.addButton("description_refresh", 100, "", "lib/img/arrow_refresh.png", "lib/img/arrow_refresh.png");
	prop_tb.setItemToolTip('description_refresh','<?php echo _l('Refresh',1)?>');
	prop_tb.addButton('desc_save',100,'','lib/img/page_save.png','lib/img/page_save.png');
	prop_tb.setItemToolTip('desc_save','<?php echo _l('Save descriptions',1)?>');
	prop_tb.addText('txt_descriptionsize', 100, '<?php echo _l('Short description charset')._l(':').' '.'0/'._s('man_SHORT_DESC_SIZE')?>');
	prop_tb.addButtonTwoState('desc_twodesc', 100, "", "lib/img/application_tile_vertical.png", "lib/img/application_tile_vertical.png");
	prop_tb.setItemToolTip('desc_twodesc','<?php echo _l('Display all descriptions',1)?>');
	<?php if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
		prop_tb.addButton('desc_fouleFactory', 100, "", "lib/img/foulefactory_icon.png", "lib/img/foulefactory_icon.png");
		prop_tb.setItemToolTip('desc_fouleFactory','<?php echo _l('Enhance your product pages in 3 minutes with FouleFactory',1)?>');
	<?php } ?>

	needInitDescriptions = 1;
	function initDescriptions(){
	if (needInitDescriptions)
	{
	prop_tb._descriptionsLayout = dhxLayout.cells('b').attachLayout('1C');
	prop_tb._descriptionsLayout.cells('a').hideHeader();
	<?php if(_s("APP_RICH_EDITOR")==1) { ?>
		prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_tinymce'+URLOptions);
	<?php } else { ?>
		prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_ckeditor'+URLOptions);
	<?php } ?>
	dhxLayout.cells('b').showHeader();
	needInitDescriptions=0;
	}
	}



	function setPropertiesPanel_descriptions(id){
	// ask to save description if modified
	if (propertiesPanel=='descriptions' && id!='desc_save' && typeof prop_tb._descriptionsLayout!='undefined')
	prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();

	if (id=='descriptions')
	{
	hidePropTBButtons();
	prop_tb.showItem('description_refresh');
	prop_tb.showItem('desc_save');
	prop_tb.showItem('desc_twodesc');
	<?php if (_s('APP_FOULEFACTORY') && SCI::getFFActive()) { ?>
		prop_tb.showItem('desc_fouleFactory');
	<?php } ?>
	prop_tb.showItem('txt_descriptionsize');
	prop_tb.setItemState("desc_twodesc", 1);
	prop_tb.setItemText('panel', '<?php echo _l('Descriptions',1)?>');
	prop_tb.setItemImage('panel', 'lib/img/description.png');
	URLOptions='';
	if (last_manufacturerID!=0) URLOptions='&id_manufacturer='+last_manufacturerID+'&id_lang='+SC_ID_LANG;
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
	if (last_manufacturerID!=0) URLOptions='&id_manufacturer='+last_manufacturerID+'&id_lang='+SC_ID_LANG;
	<?php if(_s("APP_RICH_EDITOR")==1) { ?>
		prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_tinymce'+URLOptions);
	<?php } else { ?>
		prop_tb._descriptionsLayout.cells('a').attachURL('index.php?ajax=1&act=man_description_ckeditor'+URLOptions);
	<?php } ?>
	}
	if(id=='desc_fouleFactory')
	{
	showWCatFoulefactory();
	}
	}
	prop_tb.attachEvent("onClick", setPropertiesPanel_descriptions);

	prop_tb.attachEvent("onStateChange",function(id,state){
	if (id=='desc_twodesc')
	{
	if (state)
	{
	prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.showShortDesc();
	}else{
	prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.hideShortDesc();
	}
	}
	});


	man_grid_tb.attachEvent("onClick",function(id){
	<?php
	$tmp=array();
	$clang=_l('Language');
	foreach($languages AS $lang){
		echo'
			if (id==\'man_lang_'.$lang['iso_code'].'\')
			{
				if (propertiesPanel==\'descriptions\' && typeof prop_tb._descriptionsLayout!=\'undefined\')
					prop_tb._descriptionsLayout.cells(\'a\').getFrame().contentWindow.checkChange();
			}
';
	}
	?>
	});


	man_grid.attachEvent("onRowSelect",function (idproduct){
	last_manufacturerID=idproduct;
	idxProductName=man_grid.getColIndexById('name');
	if (propertiesPanel=='descriptions')
	{
	if (prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkSize())
	{
	prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.checkChange();
	dhxLayout.cells('b').setText('<?php echo _l('Properties',1).' '._l('of',1)?> '+man_grid.cells(last_manufacturerID,idxProductName).getValue());
	<?php if(_s("APP_RICH_EDITOR")!=1) { ?>
		prop_tb._descriptionsLayout.cells('a').progressOn();
	<?php } ?>
	prop_tb._descriptionsLayout.cells('a').getFrame().contentWindow.ajaxLoad('&id_manufacturer='+last_manufacturerID+'&id_lang='+SC_ID_LANG,last_manufacturerID,SC_ID_LANG);
	}else{
	dhtmlx.message({text:'<?php echo _l('Short description charset must be < ')._s('man_SHORT_DESC_SIZE').' '._l('chars',1)?>',type:'error'});
	}
	}

	});

<?php } ?>
