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

function createMenu()
{
	global $languages,$page,$sc_agent,$menuConfiguration,$user_lang_iso,$menu_js_action;
	$updateAvailable=checkSCVersion(false,false);
	//$updateAvailable=false;
?>
	var dhxMenu = dhxLayout.attachMenu();
	var XMLMenuData=''+
'<menu>'+
<?php if(_r("MEN_CAT_CATALOG")) { ?>
'<item id="catalog" text="<?php echo _l('Catalog',1)?>" img="lib/img/catalog_edit.png" imgdis="lib/img/catalog_edit.png">'+
	'<item id="cat_tree" text="<?php echo _l('Categories and products',1)?>" img="lib/img/application_side_tree.png" imgdis="lib/img/application_side_tree.png"/>'+
	'<item id="cat_grid" text="<?php echo _l('Products list',1)?>" img="lib/img/application_view_list.png" imgdis="lib/img/application_view_list.png"/>'+
	'<item id="cat_categories" text="<?php echo _l('Categories',1)?>" img="lib/img/folder_wrench.png" imgdis="lib/img/folder_wrench.png" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'>'+
		<?php if(_r("MEN_CAT_CATMANAGEMENT")) { ?>
		'<item id="cat_management" text="<?php echo _l('Categories management',1)?>" img="lib/img/folder_wrench.png" imgdis="lib/img/folder_wrench.png" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
		<?php } if(_r("MEN_CAT_CATIMPORT_CSV")) { ?>
		'<item id="cat_catimport" text="<?php echo _l('CSV Import',1)?>" img="lib/img/database_add.png" imgdis="lib/img/database_add.png" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
		<?php } if(_r("MEN_CAT_CATEXPORT_CSV")) { ?>
		'<item id="cat_catexport" text="<?php echo _l('CSV Export',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'/>'+
		<?php } ?>
	'</item>'+
	<?php
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<') && _r("MEN_CAT_DISCOUNT"))
	{
?>
	'<item id="cat_discount" text="<?php echo _l('Discounts',1)?>" img="lib/img/medal_gold_delete.png" imgdis="lib/img/medal_gold_delete.png">'+
		'<item id="cat_resetpricedropdates" text="<?php echo _l('Reset prices drop dates',1)?>" img="lib/img/medal_gold_delete.png" imgdis="lib/img/medal_gold_delete.png"/>'+
		'<item id="cat_resetpricedropreductions" text="<?php echo _l('Reset prices drop reductions',1)?>" img="lib/img/medal_gold_delete.png" imgdis="lib/img/medal_gold_delete.png"/>'+
<?php
if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
{
?>
		'<item id="cat_resetpricedrop" text="<?php echo _l('Delete all prices drop',1)?>" img="lib/img/medal_gold_delete.png" imgdis="lib/img/medal_gold_delete.png"/>'+
<?php
}
?>
	'</item>'+
<?php
	}
	if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Combination::isFeatureActive()) && _r("MEN_CAT_ATTRIBUTES_GROUPS"))
	{
?>
	'<item id="cat_attribute" text="<?php echo _l('Attributes and groups',1)?>" img="lib/img/asterisk_yellow.png" imgdis="lib/img/asterisk_yellow.png">'+
		'<item id="cat_attributes" text="<?php echo _l('Attribute and group management',1)?>" img="lib/img/page_edit.png" imgdis="lib/img/page_edit.png"/>'+
		'<item id="cat_impexp_attr_translation" text="<?php echo _l('Export/Import translations',1)?>" img="lib/img/flag_blue.png" imgdis="lib/img/flag_blue.png"/>'+
	'</item>'+
<?php
	}
	if ((version_compare(_PS_VERSION_, '1.5.0.0', '<') || Feature::isFeatureActive()) && _r("MEN_CAT_FEATURES"))
	{
?>
	'<item id="cat_feature" text="<?php echo _l('Features',1)?>" img="lib/img/eye.png" imgdis="lib/img/eye.png">'+
		'<item id="cat_features" text="<?php echo _l('Feature management',1)?>" img="lib/img/page_edit.png" imgdis="lib/img/page_edit.png"/>'+
		'<item id="cat_impexp_feat_translation" text="<?php echo _l('Export/Import translations',1)?>" img="lib/img/flag_blue.png" imgdis="lib/img/flag_blue.png"/>'+
	'</item>'+
<?php
	}
	if(version_compare(_PS_VERSION_, '1.3.0.0', '>=') && _r("MEN_MAN_MANUFACTURERS")) { ?>
		'<item id="man_tree" text="<?php echo _l('Manufacturers',1)?>" img="lib/img/application_view_list.png" imgdis="lib/img/application_view_list.png"></item>'+
<?php
	}
	if (version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _r("GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE"))
	{
	?>
	'<item id="cat_specificprice" text="<?php echo _l('Specific prices',1)?>" img="lib/img/text_list_numbers.png" imgdis="lib/img/text_list_numbers.png" '+(SC_PAGE!='cat_tree'?' disabled="true"':'')+' />'+	
	<?php
	}
	if(_r("MEN_CAT_IMPORT_CSV")) { ?>
	(!isIPAD?'<item id="cat_import" text="<?php echo _l('CSV Import',1)?>" img="lib/img/database_add.png" imgdis="lib/img/database_add.png"/>':'')+
	<?php }
	if(_r("MEN_CAT_EXPORT_CSV")) { ?>
	'<item id="cat_export" text="<?php echo _l('CSV Export',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png"/>'+
	<?php } ?>
	'<item id="cat_tools" text="<?php echo _l('Tools',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png">'+
		'<item id="cat_tools_clearcookies" text="<?php echo _l('Clear grid preferences for products',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'></item>'+
		'<item id="cat_tools_clearcookies_all" text="<?php echo _l('Clear all grids preferences for products',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'></item>'+
		'<item id="cat_tools_clearcookies_combi" text="<?php echo _l('Clear grid preferences for combinations',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'></item>'+
		<?php if(_r("MEN_CAT_CHECK_FIX_CATEGORIES")) { ?>
		'<item id="cat_rebuildleveldepth" text="<?php echo _l('Check and fix categories',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
<?php
		}
		if(SCMS && _r("MEN_CAT_SYNCHRO_CATS_POSITIONS")) { ?>
		'<item id="cat_synchro_cats_positions" text="<?php echo _l('Synchronize the categories positions on multiple shops',1)?>" img="lib/img/folder_synchro.png" imgdis="lib/img/folder_synchro.png"></item>'+
<?php
		}
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
	{
?>
		'<item id="cat_rebuildlangfield" text="<?php echo _l('Check and fix language fields of products for all languages',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
		'<item id="cat_rebuildproductprice" text="<?php echo _l('Set product prices to their default combination prices',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
		'<item id="cat_fillcoverimage" text="<?php echo _l('Set a cover image for products without cover image',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
<?php
	}else{
?>
		'<item id="cat_rebuildproductprice" text="<?php echo _l('Set product prices to their default combination prices',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
		<?php if(_r("MEN_CAT_CALCULATE_TOTAL_STOCK_COMBI")) { ?>
		'<item id="cat_rebuildsumstock" text="<?php echo _l('Calculate total stock of products with combinations',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
<?php	}
	}
?>
	'</item>'+
'</item>'+
<?php } ?>
<?php if(_r("MENU_ORD_ORDERS")) { ?>
'<item id="order" text="<?php echo _l('Orders',1)?>" img="lib/img/cart.png" imgdis="lib/img/cart.png">'+
	'<item id="ord_orders" text="<?php echo _l('Orders',1)?>" img="lib/img/application_view_list.png" imgdis="lib/img/application_view_list.png"></item>'+
<?php
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<') && version_compare(_PS_VERSION_, '1.2.0.0', '>=') && _r("MENU_ORD_EXPORTORDERS"))
		if (Configuration::get('SC_EXPORTORDERS_INSTALLED'))
		{
?>
	'<item id="ord_exportorders" text="<?php echo _l('Export orders',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png"></item>'+
<?php
		}else{
?>
	'<item id="teaser_exportorders" text="<?php echo _l('Export orders',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png">'+
		'<item id="teaser_exportorders_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
		}
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>=')) {
		if(_r("MENU_ORD_DISCOUNT_VOUCHERS")) {
?>
			'<item id="ord_cartrules" text="<?php echo _l('Discount voucher',1)?>" img="lib/img/tag_blue.png" imgdis="lib/img/tag_blue.png"></item>'+
<?php 	}
	}
?>
	'<item id="ord_tools" text="<?php echo _l('Tools',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png">'+
		'<item id="ord_tools_clearcookies" text="<?php echo _l('Clear cookies data: grid preferences for orders',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='ord_tree'?' disabled="true"':'')+'></item>'+
		'<item id="ord_tools_clearcookies_all" text="<?php echo _l('Clear all grids preferences for orders',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='ord_tree'?' disabled="true"':'')+'></item>'+
//		'<item id="ord_tools_clearcookies_combi" text="<?php echo _l('Clear cookies data: grid preferences for combinations',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cat_tree'?' disabled="true"':'')+'></item>'+
	'</item>'+
'</item>'+
<?php } ?>
<?php if(_r("MENU_CUS_CUSTOMERS")) { ?>
'<item id="customer" text="<?php echo _l('Customers',1)?>" img="lib/img/user.png" imgdis="lib/img/user.png">'+
	'<item id="cus_customers" text="<?php echo _l('Customers',1)?>" img="lib/img/application_view_list.png" imgdis="lib/img/application_view_list.png"></item>'+
	<?php if(version_compare(_PS_VERSION_, '1.4.0.0', '>=') && _r("GRI_CUSM_VIEW_CUSM")) { ?>
	'<item id="cusm_customersservice" text="<?php echo _l('Customer service',1); ?>" img="lib/img/user_comment.png" imgdis="lib/img/user_comment.png"></item>'+
	<?php } ?>
	'<item id="cus_groupmanagement" text="<?php echo _l('Customer group',1); ?>" img="lib/img/user_comment.png" imgdis="lib/img/user_comment.png"></item>'+
	<?php
	if(version_compare(_PS_VERSION_, '1.2.0.0', '>=') && _r("MENU_CUS_IMPORTCUSTOMERS"))
	{ ?>
	(!isIPAD?'<item id="cus_import" text="<?php echo _l('CSV Import',1)?>" img="lib/img/database_add.png" imgdis="lib/img/database_add.png"/>':'')+
	<?php
	}

	if(version_compare(_PS_VERSION_, '1.2.0.0', '>=') && _r("MENU_CUS_EXPORTCUSTOMERS"))
	{
		if (!Configuration::get('SC_CUSTOMERSEXPORT_INSTALLED'))
		{	?>
	'<item id="teaser_cus_export" text="<?php echo _l('CSV Export',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png">'+
		'<item id="teaser_cus_export_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"></item>'+
	'</item>'+
	<?php
		}else{ ?>
	'<item id="cus_export" text="<?php echo _l('CSV Export',1)?>" img="lib/img/database_go.png" imgdis="lib/img/database_go.png"/>'+
	<?php
		}
	}
	?>
	'<item id="cus_tools" text="<?php echo _l('Tools',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png">'+
		'<item id="cus_tools_clearcookies" text="<?php echo _l('Clear cookies data: grid preferences for customers',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cus_tree'?' disabled="true"':'')+'></item>'+
		'<item id="cus_tools_clearcookies_all" text="<?php echo _l('Clear all grids preferences for customers',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"'+(SC_PAGE!='cus_tree'?' disabled="true"':'')+'></item>'+
	'</item>'+
'</item>'+
<?php } ?>
<?php if(version_compare(_PS_VERSION_, '1.4.0.17', '>=') && _r("MENU_CMS_CMSPAGE")) { ?>
	'<item id="cms" text="<?php echo _l('CMS',1)?>" img="lib/img/page_edit.png" imgdis="lib/img/page_edit.png">'+
		'<item id="cms_tree" text="<?php echo _l('CMS page',1)?>" img="lib/img/application_view_list.png" imgdis="lib/img/application_view_list.png"></item>'+
		'</item>'+
<?php } ?>
<?php if(_r("MENU_MAR_MARKETING")) { ?>
'<item id="marketing" text="<?php echo _l('Marketing',1)?>" img="lib/img/money.png" imgdis="lib/img/money.png">'+
<?php
		if (Configuration::get('SC_AFFILIATION_INSTALLED'))
		{
?>
	'<item id="mar_affiliation" text="<?php echo _l('Affiliation',1)?>" img="lib/img/group_link.png" imgdis="lib/img/group_link.png"></item>'+
<?php
		}else{
?>
	'<item id="teaser_affiliation" text="<?php echo _l('Affiliation',1)?>" img="lib/img/group_link.png" imgdis="lib/img/group_link.png">'+
		'<item id="teaser_affiliation_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
		}
		if (_r("MEN_MAR_SEGMENTATION"))
		if(SCSG)
		{
?>
	'<item id="mar_segmentation" text="<?php echo _l('Segmentation',1)?>" img="lib/img/segmentation.png" imgdis="lib/img/segmentation.png"></item>'+
<?php
		}else{
?>
	'<item id="teaser_segmentation" text="<?php echo _l('Segmentation',1)?>" img="lib/img/segmentation.png" imgdis="lib/img/segmentation.png">'+
		'<item id="teaser_segmentation_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
		}
?>
'</item>'+
<?php } ?>
<?php if(_r("MENU_ACC_ACCOUNTING")) { ?>
'<item id="accounting" text="<?php echo _l('Accounting',1)?>" img="lib/img/calculator.png" imgdis="lib/img/calculator.png">'+
<?php
	if (version_compare(_PS_VERSION_, '1.2.0.0', '>='))
		if (Configuration::get('SC_QUICKACCOUNTING_INSTALLED'))
		{
?>
	'<item id="acc_quickaccounting" text="<?php echo _l('Quick Accounting',1)?>" img="lib/img/calculator_edit.png" imgdis="lib/img/calculator_edit.png"></item>'+
<?php
		}else{
?>
	'<item id="teaser_quickaccounting" text="<?php echo _l('Quick Accounting',1)?>" img="lib/img/calculator_edit.png" imgdis="lib/img/calculator_edit.png">'+
		'<item id="teaser_quickaccounting_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
		}
?>
'</item>'+
<?php } ?>
//'<item id="emailing" text="<?php echo _l('Emailing (Preview)',1)?>" img="lib/img/email.png" imgdis="lib/img/email.png"></item>'+
<?php if(_r("MEN_TOO_TOOLS")) { ?>
'<item id="config" text="<?php echo _l('Tools',1)?>" img="lib/img/cog.png" imgdis="lib/img/cog.png">'+
<?php
	if (_s('APP_COMPAT_EBAY'))
	{
		?>	'<item id="config_ebay" text="eBay" img="lib/img/ebay.gif" imgdis="lib/img/ebay.gif"></item>'+ <?php
	}
	if(_r("MEN_TOO_EXTENSIONS"))
		echo eval('?>'.$menuConfiguration['Tools'].'<?php ');
?>
	<?php if(_r("MEN_TOO_INSTALLATION")) { ?>
	'<item id="config_install" text="<?php echo _l('Installation',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png">'+
<?php if (SC_INSTALL_MODE==0 && version_compare(_PS_VERSION_, '1.5.0.0', '<')) { ?>		'<item id="config_createquickaccess" text="<?php echo _l('Create link in the PrestaShop Quick access menu',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+<?php } ?>
		'<item id="config_createcsvimportsample" text="<?php echo _l('Create files example for CSV import',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
		'<item id="config_createcsvexportsample" text="<?php echo _l('Create script files example for CSV export',1)?>" img="lib/img/cog_go.png" imgdis="lib/img/cog_go.png"></item>'+
		'<item id="config_changehashdir" text="<?php echo _l('Change security key',1)?>" img="lib/img/alert.gif" imgdis="lib/img/alert.gif"></item>'+
	'</item>'+
	<?php } ?>
	<?php if(_r("MEN_TOO_SERVER")) { ?>
	'<item id="config_server" text="<?php echo _l('Server',1)?>" img="lib/img/server.png" imgdis="lib/img/server.png">'+
		'<item id="ser_page404" text="<?php echo _l('Page not found 404',1)?>" img="lib/img/page_white_error.png" imgdis="lib/img/page_white_error.png"></item>'+
		'<item id="ser_emptysmartycache" text="<?php echo _l('Empty Smarty cache',1)?>" img="lib/img/html_delete.png" imgdis="lib/img/html_delete.png"></item>'+
	'</item>'+
	<?php } ?>
/*
	'<item id="core_labs" text="<?php echo _l('Laboratory',1)?>" img="lib/img/lightbulb.png" imgdis="lib/img/lightbulb.png">'+
		'<item id="core_labs_intro" text="<?php echo _l('The laboratory contains experimental tools. We need your comments before implementing these tools in Store Commander. Thanks!',1)?>" img="lib/img/lightbulb.png" imgdis="lib/img/lightbulb.png"></item>'+
	'</item>'+
*/
	<?php
	if (_s('APP_FOULEFACTORY') && SCI::getFFActive() && _r("MEN_TOO_FOULEFACTORY") /*&& _r("MEN_TOO_HISTORY")*/)
	{
		?>
		'<item id="cat_foulefactory" text="<?php echo _l('FouleFactory',1)?>" img="lib/img/foulefactory_icon.png" imgdis="lib/img/foulefactory_icon.png"/>'+
		<?php
	}
	if (!_s('APP_DISABLE_CHANGE_HISTORY') && _r("MEN_TOO_HISTORY"))
	{
		?>
		'<item id="cat_history" text="<?php echo _l('History',1)?>" img="lib/img/time.png" imgdis="lib/img/time.png"/>'+
		<?php
	}
	?>
	'<item id="core_queuelogs" text="<?php echo _l('Tasks error logs',1)?>" img="lib/img/bug.png" imgdis="lib/img/bug.png"/>'+
	<?php if(_r("MEN_TOO_SETTINGS")) { ?>
	'<item id="core_settings" text="<?php echo _l('Settings',1)?>" img="lib/img/cog_edit.png" imgdis="lib/img/cog_edit.png"/>'+
	<?php } ?>
	<?php if(_r("MEN_TOO_LANGUAGE")) { ?>
	'<item id="core_language" text="<?php echo _l('SC language',1)?>" img="lib/img/flag_blue.png" imgdis="lib/img/flag_blue.png">'+
		'<item id="core_language_" text="<?php echo _l('Use PrestaShop backoffice language',1)?>"></item>'+
<?php
	$files = array_diff( scandir( SC_DIR.'lang' ), array_merge( Array( ".", "..", "index.php", "index.htm", "index.html", ".htaccess", "php.ini")) );
	foreach($files as $file)
	{
		echo '\'<item id="core_language_'.str_replace('.php','',$file).'" text="'.strtoupper(str_replace('.php','',$file)).'"'.(str_replace('.php','',$file) == $user_lang_iso ?' img="lib/img/flag_blue.png" imgdis="lib/img/flag_blue.png"':'').'></item>\'+';
	}
?>
		'<item id="core_languagehelp" text="<?php echo _l('Help us to translate Store Commander!',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"></item>'+
		'<item id="core_languageupdate" text="<?php echo _l('Update Store Commander translations',1)?>" img="lib/img/database_refresh.png" imgdis="lib/img/database_refresh.png"></item>'+
	'</item>'+
	<?php } ?>
	<?php if(_r("MEN_TOO_GRIDSSETTINGS")) {
		if (!SC_GRIDSEDITOR_INSTALLED && !SC_GRIDSEDITOR_PRO_INSTALLED)
		{	?>
	'<item id="teaser_gridseditor" text="<?php echo _l('Grids editor',1)?>" img="lib/img/table_gear.png" imgdis="lib/img/table_gear.png">'+
		'<item id="teaser_gridseditor_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"></item>'+
	'</item>'+
	<?php }else{ ?>
	'<item id="win_grids_editor" text="<?php echo _l('Grids editor',1)?>" img="lib/img/table_gear.png" imgdis="lib/img/table_gear.png"/>'+
	<?php }
		} 
		if(_r("MEN_MAR_SEGMENTATION"))
		if (SCSG)
		{
?>
	'<item id="too_segmentation" text="<?php echo _l('Segmentation',1)?>" img="lib/img/segmentation.png" imgdis="lib/img/segmentation.png"></item>'+
<?php
		}else{
?>
	'<item id="too_teaser_segmentation" text="<?php echo _l('Segmentation',1)?>" img="lib/img/segmentation.png" imgdis="lib/img/segmentation.png">'+
		'<item id="too_teaser_segmentation_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
		} 
		if(_r("MEN_TOO_PERMISSIONS")) { ?>
	'<item id="permissions" text="<?php echo _l('Manage user permissions',1)?>" img="lib/img/user_go.png" imgdis="lib/img/user_go.png"/>'+
	<?php } ?>
<?php
	if (strpos($menuConfiguration['Tools'],'FixMyPrestashop')===false)
	{
?>
	'<item id="teaser_fixmyps" text="<?php echo _l('FixMyPrestashop',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png">'+
		'<item id="teaser_fixmyps_read" text="<?php echo _l('Read more',1)?>" img="lib/img/tick.png" imgdis="lib/img/tick.png"/>'+
	'</item>'+
<?php
	}
?>
'</item>'+
<?php } ?>
<?php if(KAI9DF4!=1) { ?>
'<item id="trends" text="<?php echo _l('Trends',1)?>" img="lib/img/chart_bar.png" imgdis="lib/img/chart_bar.png">'+
	'<item id="trends_project" text="<?php echo _l('The project',1)?>" img="lib/img/help.png" imgdis="lib/img/help.png"/>'+
'</item>'+
<?php } ?>
<?php if(_r("MENU_LIN_LINKS")) { ?>
'<item id="link" text="<?php echo _l('Links',1)?>" img="lib/img/lightning.png" imgdis="lib/img/lightning.png">'+
	<?php if(SCMS) {
		$sql_shop ="SELECT id_shop, name
					FROM "._DB_PREFIX_."shop
					WHERE deleted != '1'";
		$shops = Db::getInstance()->ExecuteS($sql_shop);
		if(!empty($shops) && count($shops)>1)
		{
			$protocol = (version_compare(_PS_VERSION_, '1.5.0.2', '>=') ? Tools::getShopProtocol() : (SCI::getConfigurationValue('PS_SSL_ENABLED') ? 'https://' : 'http://'));
			$shopUrls = array();
			?>'<item id="link_psfront_shops" text="<?php echo _l('Your shops',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png">'+<?php
			foreach($shops as $shop)
			{
				$url = Db::getInstance()->ExecuteS('SELECT *, CONCAT(domain, physical_uri, virtual_uri) AS url
					FROM '._DB_PREFIX_.'shop_url
					WHERE id_shop = '.(int)$shop["id_shop"].'
						AND active = "1"
					ORDER BY main DESC
					LIMIT 1');
				if(!empty($url[0]["url"]))
				{
					$shopUrls[$shop["id_shop"]] = $protocol.$url[0]["url"];
					$name = str_replace("&", '+', $shop["name"]);
					$name = str_replace('"', "'", $name);
					$name = str_replace("'", "\'", $name);
					?>
					'<item id="link_psfront_shop_<?php echo $shop["id_shop"]; ?>" text="<?php echo $name; ?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
					<?php
				}
			}
			?>'</item>'+<?php
		}
		elseif(!empty($shops) && count($shops)==0)
		{ ?>
		'<item id="link_psfront" text="<?php echo _l('Your Shop',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
		<?php }
	} else { ?>
	'<item id="link_psfront" text="<?php echo _l('Your Shop',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
	<?php } ?>
	'<item id="link_psbo" text="<?php echo _l('PrestaShop BackOffice',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
	'<item id="link_ps" text="<?php echo _l('Visit PrestaShop.com',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
	'<item id="link_pse" text="<?php echo _l('Visit StoreCommander.com',1)?>" img="lib/img/lightning_go.png" imgdis="lib/img/lightning_go.png"></item>'+
'</item>'+
<?php } ?>
<?php if(_r("MENU_HEL_HELP")) { ?>
'<item id="help" text="<?php echo _l('Help',1)?>" img="lib/img/help.png" imgdis="lib/img/help.png">'+
	'<item id="help_help" text="<?php echo _l('Documentation',1)?>" img="lib/img/help.png" imgdis="lib/img/help.png"></item>'+
	'<item id="help_tips" text="<?php echo _l('Tips',1)?>" img="lib/img/lightbulb.png" imgdis="lib/img/lightbulb.png">'+
		'<item id="help_tips_display" text="<?php echo _l('Open Tips',1)?>" img="lib/img/lightbulb.png" imgdis="lib/img/lightbulb.png"></item>'+
		'<item id="help_tips_settings" text="<?php echo _l('Settings',1)?>" img="lib/img/lightbulb.png" imgdis="lib/img/lightbulb.png"></item>'+
	'</item>'+
	'<item id="help_bug" text="<?php echo _l('Send a comment, a bug, a request',1)?>" img="lib/img/bug.png" imgdis="lib/img/bug.png"></item>'+
	<?php if(_r("MENU_HEL_SC_UPDATE")) { ?>
	'<item id="help_updates" text="<?php echo _l('Update history',1)?>" img="lib/img/cup.png" imgdis="lib/img/cup.png"></item>'+
	'<item id="version" text="<?php echo _l('Update Store Commander',1).(SC_BETA?' BETA':'')?>" img="lib/img/database_refresh.png" imgdis="lib/img/database_refresh.png"></item>'+
	<?php } ?>
	'<item id="--456" type ="separator"></item>'+
	<?php if(_r("MENU_HEL_SC_LICENCE")) { ?>
	'<item id="help_upgradelicense" text="<?php echo _l('Upgrade your account!',1)?>" img="lib/img/key_add.png" imgdis="lib/img/key_add.png"></item>'+
	'<item id="help_enterlicense" text="<?php echo _l('Register your license',1)?>" img="lib/img/textfield_key.png" imgdis="lib/img/textfield_key.png"></item>'+
	<?php } ?>
	//	'<item id="help_upgradesupport" text="<?php echo _l('Extend your support and automatic updates',1)?>" img="lib/img/wand.png" imgdis="lib/img/wand.png"></item>'+
<?php if (0 && SC_BETA){ ?>
	'<item id="--54456" type ="separator"></item>'+
	'<item id="help_test" text="test" img="lib/img/bug.png" imgdis="lib/img/bug.png"></item>'+
<?php  } ?>
'</item>'+
<?php } ?>
<?php if ($updateAvailable && _r("MENU_HEL_SC_UPDATE")){ ?>
(!isIPAD?'<item id="newversion" text="<?php echo _l('NEW VERSION AVAILABLE!',1);?>" img="lib/img/heart.png">'+
	'<item id="help_updates2" text="<?php echo _l('Update history',1)?>" img="lib/img/cup.png" imgdis="lib/img/cup.png"></item>'+
	'<item id="version2" text="<?php echo _l('Update Store Commander',1).(SC_BETA?' BETA':'')?>" img="lib/img/database_refresh.png" imgdis="lib/img/database_refresh.png"></item>'+
'</item>':'')+
<?php  } ?>
'</menu>';

	dhxMenu.loadStruct(XMLMenuData);

	function onMenuClick(id, zoneId, casState){
		<?php echo $menu_js_action; ?>
	}

	function clearConfigCookie(object)
	{
		if (object=='products')
		{
			//ui_settings['gridSettingscg_cat_treegrid_'+gridView] = null;
			cat_grid.clearConfigCookie('cg_cat_treegrid_'+gridView);
			$.cookie("gridSettingscg_cat_treegrid_"+gridView,null, { expires: 0 });
			$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'cat_grid_'+gridView, "data":""},function(data){
			});

			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}
		if (object=='allproducts')
		{
			$.each(gridnames, function(gridname,gridtitle){
				cat_grid.clearConfigCookie('cg_cat_treegrid_'+gridname);
				$.cookie('gridSettingscg_cat_treegrid_'+gridname,null, { expires: 0 });
				//ui_settings['gridSettingscg_cat_treegrid_'+gridname] = null;

				$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'cat_grid_'+gridname, "data":""},function(data){
				});
			});
			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}
		if (object=='combinations')
		{
			if (prop_tb._specificpricesGrid)
			{
				prop_tb._specificpricesGrid.clearConfigCookie('cg_cat_spepri');
				prop_tb._specificpricesGrid.clearConfigCookie('cg_cat_spepri_col');
			}
			if (prop_tb._combinationsGrid)
			{
				//prop_tb._combinationsGrid.clearConfigCookie('cg_cat_combi'+prop_tb._combinationsGrid.getColumnsNum());
				//ui_settings['gridSettingscg_cat_combi'+prop_tb._combinationsGrid.getColumnsNum()] = null;
				prop_tb._combinationsGrid.clearConfigCookie('cg_cat_combi'+prop_tb._combinationsGrid.getColumnsNum());
				$.cookie('gridSettingscg_cat_combi'+prop_tb._combinationsGrid.getColumnsNum(),null, { expires: 0 });
				$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'cat_combination'+prop_tb._combinationsGrid.getColumnsNum(), "data":""},function(data){
				});
				alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
			}else{
				alert('<?php echo _l('The combinations grid should be displayed before using this tool.',1)?>');
			}
		}
		if (object=='orders')
		{
			//ui_settings['gridSettingscg_ord_treegrid_'+gridView] = null;
			ord_grid.clearConfigCookie('cg_ord_treegrid_'+gridView);
			$.cookie("gridSettingscg_ord_treegrid_"+gridView,null, { expires: 0 });
			$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'ord_grid_'+gridView, "data":""},function(data){
			});

			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}
		if (object=='allorders')
		{
			$.each(gridnames, function(gridname,gridtitle){
				ord_grid.clearConfigCookie('cg_ord_treegrid_'+gridname);
				$.cookie('gridSettingscg_ord_treegrid_'+gridname,null, { expires: 0 });
				//ui_settings['gridSettingscg_ord_treegrid_'+gridname] = null;

				$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'ord_grid_'+gridname, "data":""},function(data){
				});
			});

			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}
		if (object=='customers')
		{
			//ui_settings['gridSettingscg_cus_treegrid_'+gridView] = null;
			cus_grid.clearConfigCookie('cg_cus_treegrid_'+gridView);
			$.cookie("gridSettingscg_cus_treegrid_"+gridView,null, { expires: 0 });
			$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'cus_grid_'+gridView, "data":""},function(data){
			});

			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}
		if (object=='allcustomers')
		{
			$.each(gridnames, function(gridname,gridtitle){
				cus_grid.clearConfigCookie('cg_cus_treegrid_'+gridname);
				$.cookie('gridSettingscg_cus_treegrid_'+gridname,null, { expires: 0 });
				//ui_settings['gridSettingscg_cus_treegrid_'+gridname] = null;

				$.post("index.php?ajax=1&act=all_uisettings_update&id_lang="+SC_ID_LANG+"&"+new Date().getTime(), {"name":'cus_grid_'+gridname, "data":""},function(data){
				});
			});

			alert('<?php echo _l('You need to refresh the whole page (F5 or Apple+R) to reset the application.',1)?>');
		}

	}

	dhxMenu.attachEvent("onClick",onMenuClick);
<?php
}
