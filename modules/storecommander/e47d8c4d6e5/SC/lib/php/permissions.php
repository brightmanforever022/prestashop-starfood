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

$permissions_list = array(
	 'MEN_CAT_CATALOG' => array('id'=>'MEN_CAT_CATALOG','section1'=>'Menu','section2'=>'Catalog','name'=>'Catalog','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_DISCOUNT' => array('id'=>'MEN_CAT_DISCOUNT','section1'=>'Menu','section2'=>'Catalog','name'=>'Discounts','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_ATTRIBUTES_GROUPS' => array('id'=>'MEN_CAT_ATTRIBUTES_GROUPS','section1'=>'Menu','section2'=>'Catalog','name'=>'Attributes and groups','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_FEATURES' => array('id'=>'MEN_CAT_FEATURES','section1'=>'Menu','section2'=>'Catalog','name'=>'Features','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_MAN_MANUFACTURERS' => array('id'=>'MEN_MAN_MANUFACTURERS','section1'=>'Menu','section2'=>'Manufacturers','name'=>'Manufacturers','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_IMPORT_CSV' => array('id'=>'MEN_CAT_IMPORT_CSV','section1'=>'Menu','section2'=>'Catalog','name'=>'Import CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_EXPORT_CSV' => array('id'=>'MEN_CAT_EXPORT_CSV','section1'=>'Menu','section2'=>'Catalog','name'=>'export CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_CATMANAGEMENT' => array('id'=>'MEN_CAT_CATMANAGEMENT','section1'=>'Menu','section2'=>'Catalog','name'=>'Categories management','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_CATIMPORT_CSV' => array('id'=>'MEN_CAT_CATIMPORT_CSV','section1'=>'Menu','section2'=>'Catalog','name'=>'Categories - Import CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_CATEXPORT_CSV' => array('id'=>'MEN_CAT_CATEXPORT_CSV','section1'=>'Menu','section2'=>'Catalog','name'=>'Categories - export CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_CHECK_FIX_CATEGORIES' => array('id'=>'MEN_CAT_CHECK_FIX_CATEGORIES','section1'=>'Menu','section2'=>'Catalog','name'=>'Check and fix categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_CAT_CALCULATE_TOTAL_STOCK_COMBI' => array('id'=>'MEN_CAT_CALCULATE_TOTAL_STOCK_COMBI','section1'=>'Menu','section2'=>'Catalog','name'=>'Calculate total stock of products with combinations','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'MENU_ORD_ORDERS' => array('id'=>'MENU_ORD_ORDERS','section1'=>'Menu','section2'=>'Orders','name'=>'Orders','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_ORD_EXPORTORDERS' => array('id'=>'MENU_ORD_EXPORTORDERS','section1'=>'Menu','section2'=>'Orders','name'=>'Export orders','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_ORD_DISCOUNT_VOUCHERS' => array('id'=>'MENU_ORD_DISCOUNT_VOUCHERS','section1'=>'Menu','section2'=>'Orders','name'=>'Allow access to discount vouchers','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MENU_CUS_CUSTOMERS' => array('id'=>'MENU_CUS_CUSTOMERS','section1'=>'Menu','section2'=>'Customers','name'=>'Customers','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_CUS_IMPORTCUSTOMERS' => array('id'=>'MENU_CUS_IMPORTCUSTOMERS','section1'=>'Menu','section2'=>'Customers','name'=>'Import CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_CUS_EXPORTCUSTOMERS' => array('id'=>'MENU_CUS_EXPORTCUSTOMERS','section1'=>'Menu','section2'=>'Customers','name'=>'Export CSV','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MENU_CMS_CMSPAGE' => array('id'=>'MENU_CMS_CMSPAGE','section1'=>'Menu','section2'=>'CMS','name'=>'CMS pages','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MENU_MAR_MARKETING' => array('id'=>'MENU_MAR_MARKETING','section1'=>'Menu','section2'=>'Marketing','name'=>'Marketing','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_MAR_SEGMENTATION' => array('id'=>'MEN_MAR_SEGMENTATION','section1'=>'Menu','section2'=>'Marketing','name'=>'Segmentation','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MENU_ACC_ACCOUNTING' => array('id'=>'MENU_ACC_ACCOUNTING','section1'=>'Menu','section2'=>'Accounting','name'=>'Accounting','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MEN_TOO_TOOLS' => array('id'=>'MEN_TOO_TOOLS','section1'=>'Menu','section2'=>'Tools','name'=>'Tools','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_EXTENSIONS' => array('id'=>'MEN_TOO_EXTENSIONS','section1'=>'Menu','section2'=>'Tools','name'=>'Extensions','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_PERMISSIONS' => array('id'=>'MEN_TOO_PERMISSIONS','section1'=>'Menu','section2'=>'Tools','name'=>'Manage user permissions','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_INSTALLATION' => array('id'=>'MEN_TOO_INSTALLATION','section1'=>'Menu','section2'=>'Tools','name'=>'Installation','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_SERVER' => array('id'=>'MEN_TOO_SERVER','section1'=>'Menu','section2'=>'Tools','name'=>'Server','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_HISTORY' => array('id'=>'MEN_TOO_HISTORY','section1'=>'Menu','section2'=>'Tools','name'=>'History','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_SETTINGS' => array('id'=>'MEN_TOO_SETTINGS','section1'=>'Menu','section2'=>'Tools','name'=>'Settings','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_LANGUAGE' => array('id'=>'MEN_TOO_LANGUAGE','section1'=>'Menu','section2'=>'Tools','name'=>'SC language','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MEN_TOO_GRIDSSETTINGS' => array('id'=>'MEN_TOO_GRIDSSETTINGS','section1'=>'Menu','section2'=>'Tools','name'=>'Grids editor','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MENU_LIN_LINKS' => array('id'=>'MENU_LIN_LINKS','section1'=>'Menu','section2'=>'Links','name'=>'Links','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'MENU_HEL_HELP' => array('id'=>'MENU_HEL_HELP','section1'=>'Menu','section2'=>'Help','name'=>'Help','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_HEL_SC_UPDATE' => array('id'=>'MENU_HEL_SC_UPDATE','section1'=>'Menu','section2'=>'Help','name'=>'Update StoreCommander','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'MENU_HEL_SC_LICENCE' => array('id'=>'MENU_HEL_SC_LICENCE','section1'=>'Menu','section2'=>'Help','name'=>'StoreCommander licence','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'ACT_CAT_DELETE_PRODUCT_COMBI' => array('id'=>'ACT_CAT_DELETE_PRODUCT_COMBI','section1'=>'Action','section2'=>'Catalog','name'=>'Delete products and combinations','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_EMPTY_RECYCLE_BIN' => array('id'=>'ACT_CAT_EMPTY_RECYCLE_BIN','section1'=>'Action','section2'=>'Catalog','name'=>'Empty recycle bin','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'ACT_CAT_ADD_PRODUCT_COMBI' => array('id'=>'ACT_CAT_ADD_PRODUCT_COMBI','section1'=>'Action','section2'=>'Catalog','name'=>'Add new products and combinations','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY' => array('id'=>'ACT_CAT_MOVE_PRODUCTS_IN_CATEGORY','section1'=>'Action','section2'=>'Catalog','name'=>'Add/Move products in categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_CONTEXTMENU_MASS_UPDATE_PRODUCT' => array('id'=>'ACT_CAT_CONTEXTMENU_MASS_UPDATE_PRODUCT','section1'=>'Action','section2'=>'Catalog','name'=>'Contextual menu : mass update products','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_ADD_CATEGORY' => array('id'=>'ACT_CAT_ADD_CATEGORY','section1'=>'Action','section2'=>'Catalog','name'=>'Add new categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_MOVE_CATEGORY' => array('id'=>'ACT_CAT_MOVE_CATEGORY','section1'=>'Action','section2'=>'Catalog','name'=>'Move categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY' => array('id'=>'ACT_CAT_CONTEXTMENU_SHOHIDE_CATEGORY','section1'=>'Action','section2'=>'Catalog','name'=>'Contextual menu : show/hide categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CAT_ENABLE_PRODUCTS' => array('id'=>'ACT_CAT_ENABLE_PRODUCTS','section1'=>'Action','section2'=>'Catalog','name'=>'Enable the products','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'ACT_CAT_FAST_EXPORT' => array('id'=>'ACT_CAT_FAST_EXPORT','section1'=>'Action','section2'=>'Catalog','name'=>'Quick export','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'ACT_CAT_ADVANCED_STOCK_MANAGEMENT' => array('id'=>'ACT_CAT_ADVANCED_STOCK_MANAGEMENT','section1'=>'Action','section2'=>'Catalog','name'=>'Advanced Stock Mgmt.','description'=>'Includes access to \'Advanced Stocks\' and \'Warehouses\' grids, and actions on quantities fields and in the \'Warehouses\' panel', 'default_admin'=>1, 'default_value'=>1)
	
	, 'GRI_CAT_VIEW_GRID_LIGHT' => array('id'=>'GRI_CAT_VIEW_GRID_LIGHT','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Light view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_LARGE' => array('id'=>'GRI_CAT_VIEW_GRID_LARGE','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Large view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_DELIVERY' => array('id'=>'GRI_CAT_VIEW_GRID_DELIVERY','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Delivery view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_PRICE' => array('id'=>'GRI_CAT_VIEW_GRID_PRICE','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Prices view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_DISCOUNT' => array('id'=>'GRI_CAT_VIEW_GRID_DISCOUNT','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Discounts view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_SEO' => array('id'=>'GRI_CAT_VIEW_GRID_SEO','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: SEO view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_REFERENCE' => array('id'=>'GRI_CAT_VIEW_GRID_REFERENCE','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: References view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_VIEW_GRID_DESCRIPTION' => array('id'=>'GRI_CAT_VIEW_GRID_DESCRIPTION','section1'=>'Grid','section2'=>'Catalog','name'=>'Product grid: Descriptions view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'GRI_CAT_PROPERTIES_GRID_COMBI' => array('id'=>'GRI_CAT_PROPERTIES_GRID_COMBI','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: combinations','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_DESC' => array('id'=>'GRI_CAT_PROPERTIES_GRID_DESC','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: descriptions','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_IMG' => array('id'=>'GRI_CAT_PROPERTIES_GRID_IMG','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: images','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_ACCESSORIES' => array('id'=>'GRI_CAT_PROPERTIES_GRID_ACCESSORIES','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: accessories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_ATTACHEMENT' => array('id'=>'GRI_CAT_PROPERTIES_GRID_ATTACHEMENT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: attachments','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE' => array('id'=>'GRI_CAT_PROPERTIES_GRID_SPECIFIC_PRICE','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: specific prices','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_FEATURE' => array('id'=>'GRI_CAT_PROPERTIES_GRID_FEATURE','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: features','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_TAG' => array('id'=>'GRI_CAT_PROPERTIES_GRID_TAG','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: tags','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_CATEGORY' => array('id'=>'GRI_CAT_PROPERTIES_GRID_CATEGORY','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_CUSTOMIZED_FIELDS' => array('id'=>'GRI_CAT_PROPERTIES_GRID_CUSTOMIZED_FIELDS','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: customized fields','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_MB_SHARE' => array('id'=>'GRI_CAT_PROPERTIES_GRID_MB_SHARE','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: multishops share','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_MB_PRODUCT' => array('id'=>'GRI_CAT_PROPERTIES_GRID_MB_PRODUCT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: multishops product information','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_MB_COMBI' => array('id'=>'GRI_CAT_PROPERTIES_GRID_MB_COMBI','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: multishops combinations','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_DISCOUNT' => array('id'=>'GRI_CAT_PROPERTIES_GRID_DISCOUNT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Discount','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_CUSTOMERBYPRODUCT' => array('id'=>'GRI_CAT_PROPERTIES_GRID_CUSTOMERBYPRODUCT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Customers','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_SORT' => array('id'=>'GRI_CAT_PROPERTIES_GRID_SORT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Products position','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CAT_PROPERTIES_GRID_STATS' => array('id'=>'GRI_CAT_PROPERTIES_GRID_STATS','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Products stats','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_CMS_VIEW_GRID_LIGHT' => array('id'=>'GRI_CAT_VIEW_GRID_LIGHT','section1'=>'Grid','section2'=>'CMS','name'=>'CMS grid: Light view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CMS_VIEW_GRID_LARGE' => array('id'=>'GRI_CMS_VIEW_GRID_LARGE','section1'=>'Grid','section2'=>'CMS','name'=>'CMS grid: Large view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CMS_VIEW_GRID_SEO' => array('id'=>'GRI_CMS_VIEW_GRID_SEO','section1'=>'Grid','section2'=>'CMS','name'=>'CMS grid: SEO view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CMS_PROPERTIES_GRID_MB_SHARE' => array('id'=>'GRI_CMS_PROPERTIES_GRID_MB_SHARE','section1'=>'Grid','section2'=>'CMS','name'=>'Properties grid: multishops share','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CMS_PROPERTIES_GRID_DESC' => array('id'=>'GRI_CMS_PROPERTIES_GRID_DESC','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Properties grid: descriptions','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_MAN_PROPERTIES_GRID_IMG' => array('id'=>'GRI_MAN_PROPERTIES_GRID_IMG','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Properties grid: images','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_MAN_PROPERTIES_GRID_MB_SHARE' => array('id'=>'GRI_MAN_PROPERTIES_GRID_MB_SHARE','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Properties grid: multishops share','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_MAN_VIEW_GRID_LIGHT' => array('id'=>'GRI_MAN_VIEW_GRID_LIGHT','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Manufacturer grid: Light view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_MAN_VIEW_GRID_LARGE' => array('id'=>'GRI_MAN_VIEW_GRID_LARGE','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Manufacturer grid: Large view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_MAN_VIEW_GRID_SEO' => array('id'=>'GRI_MAN_VIEW_GRID_SEO','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Manufacturer grid: SEO view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_MAN_PROPERTIES_GRID_DESC' => array('id'=>'GRI_MAN_PROPERTIES_GRID_DESC','section1'=>'Grid','section2'=>'Manufacturers','name'=>'Properties grid: descriptions','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_MAN_ENABLE_MANUFACTURER' => array('id'=>'ACT_MAN_ENABLE_MANUFACTURER','section1'=>'Action','section2'=>'Manufacturers','name'=>'Enable the manufacturer','description'=>'', 'default_admin'=>1, 'default_value'=>1)
    , 'ACT_MAN_FAST_EXPORT' => array('id'=>'ACT_MAN_FAST_EXPORT','section1'=>'Action','section2'=>'Manufacturers','name'=>'Quick export','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'ACT_CMS_ENABLE_PAGES' => array('id'=>'ACT_CMS_ENABLE_PAGES','section1'=>'Action','section2'=>'CMS','name'=>'Enable the CMS page','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_ENABLE_INDEXATION' => array('id'=>'ACT_CMS_ENABLE_INDEXATION','section1'=>'Action','section2'=>'CMS','name'=>'Enable the indexation page','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_MOVE_CATEGORY' => array('id'=>'ACT_CMS_MOVE_CATEGORY','section1'=>'Action','section2'=>'CMS','name'=>'Move categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_MOVE_PAGES_IN_CATEGORY' => array('id'=>'ACT_CMS_MOVE_PAGES_IN_CATEGORY','section1'=>'Action','section2'=>'CMS','name'=>'Add/Move CMS page in categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_ADD_CATEGORY' => array('id'=>'ACT_CMS_ADD_CATEGORY','section1'=>'Action','section2'=>'CMS','name'=>'Add new CMS categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_EMPTY_RECYCLE_BIN' => array('id'=>'ACT_CMS_EMPTY_RECYCLE_BIN','section1'=>'Action','section2'=>'CMS','name'=>'Empty recycle bin','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'ACT_CMS_CONTEXTMENU_SHOHIDE_CATEGORY' => array('id'=>'ACT_CMS_CONTEXTMENU_SHOHIDE_CATEGORY','section1'=>'Action','section2'=>'CMS','name'=>'Contextual menu : show/hide categories','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_CUS_VIEW_GRID_LIGHT' => array('id'=>'GRI_CUS_VIEW_GRID_LIGHT','section1'=>'Grid','section2'=>'Customers','name'=>'Customer grid: Light view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_VIEW_GRID_LARGE' => array('id'=>'GRI_CUS_VIEW_GRID_LARGE','section1'=>'Grid','section2'=>'Customers','name'=>'Customer grid: Large view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_VIEW_GRID_ADDRESS' => array('id'=>'GRI_CUS_VIEW_GRID_ADDRESS','section1'=>'Grid','section2'=>'Customers','name'=>'Customer grid: Address view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_VIEW_GRID_CONVERT' => array('id'=>'GRI_CUS_VIEW_GRID_CONVERT','section1'=>'Grid','section2'=>'Customers','name'=>'Customer grid: Convert view','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'ACT_CUS_FAST_EXPORT' => array('id'=>'ACT_CUS_FAST_EXPORT','section1'=>'Action','section2'=>'Customers','name'=>'Quick export','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'GRI_CUS_PROPERTIES_GRID_MESSAGE' => array('id'=>'GRI_CUS_PROPERTIES_GRID_MESSAGE','section1'=>'Grid','section2'=>'Customers','name'=>'Properties grid: Messages','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_PROPERTIES_GRID_GROUPS' => array('id'=>'GRI_CUS_PROPERTIES_GRID_GROUPS','section1'=>'Grid','section2'=>'Customers','name'=>'Properties grid: Groups','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_PROPERTIES_GRID_ORDERS' => array('id'=>'GRI_CUS_PROPERTIES_GRID_ORDERS','section1'=>'Grid','section2'=>'Customers','name'=>'Properties grid: Orders','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_CUS_PROPERTIES_GRID_ADDRESS' => array('id'=>'GRI_CUS_PROPERTIES_GRID_ADDRESS','section1'=>'Grid','section2'=>'Customers','name'=>'Properties grid: Addresses','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_ORD_VIEW_GRID_LIGHT' => array('id'=>'GRI_ORD_VIEW_GRID_LIGHT','section1'=>'Grid','section2'=>'Orders','name'=>'Order grid: Light view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_VIEW_GRID_LARGE' => array('id'=>'GRI_ORD_VIEW_GRID_LARGE','section1'=>'Grid','section2'=>'Orders','name'=>'Order grid: Large view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_VIEW_GRID_DELIVERY' => array('id'=>'GRI_ORD_VIEW_GRID_DELIVERY','section1'=>'Grid','section2'=>'Orders','name'=>'Order grid: Delivery view','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_VIEW_GRID_PICKING' => array('id'=>'GRI_ORD_VIEW_GRID_PICKING','section1'=>'Grid','section2'=>'Orders','name'=>'Order grid: Picking','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'ACT_ORD_FAST_EXPORT' => array('id'=>'ACT_ORD_FAST_EXPORT','section1'=>'Action','section2'=>'Orders','name'=>'Quick export','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_ORD_PROPERTIES_GRID_ORDERHISTORY' => array('id'=>'GRI_ORD_PROPERTIES_GRID_ORDERHISTORY','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Order history','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_PROPERTIES_GRID_MESSAGE' => array('id'=>'GRI_ORD_PROPERTIES_GRID_MESSAGE','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Messages','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_PROPERTIES_GRID_PRODUCT' => array('id'=>'GRI_ORD_PROPERTIES_GRID_PRODUCT','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Products','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	, 'GRI_ORD_PROPERTIES_GRID_ORDERS' => array('id'=>'GRI_ORD_PROPERTIES_GRID_ORDERS','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Orders','description'=>'', 'default_admin'=>1, 'default_value'=>1)
    , 'GRI_ORD_PROPERTIES_GRID_SLIP' => array('id'=>'GRI_ORD_PROPERTIES_GRID_SLIP','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Slips','description'=>'', 'default_admin'=>1, 'default_value'=>1)
    , 'GRI_ORD_PROPERTIES_GRID_INVOICE' => array('id'=>'GRI_ORD_PROPERTIES_GRID_INVOICE','section1'=>'Grid','section2'=>'Orders','name'=>'Properties grid: Invoices','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_CUSM_VIEW_CUSM' => array('id'=>'GRI_CUSM_VIEW_CUSM','section1'=>'Menu','section2'=>'Customers','name'=>'Customer service','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'GRI_CAT_PROPERTIES_GRID_CARRIER' => array('id'=>'GRI_CAT_PROPERTIES_GRID_CARRIER','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Carriers','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'MEN_CAT_SYNCHRO_CATS_POSITIONS' => array('id'=>'MEN_CAT_SYNCHRO_CATS_POSITIONS','section1'=>'Menu','section2'=>'Tools','name'=>'Synchronize the categories positions on multiple shops','description'=>'', 'default_admin'=>1, 'default_value'=>1)
	
	, 'GRI_CAT_PROPERTIES_DOWNLOAD_PRODUCT' => array('id'=>'GRI_CAT_PROPERTIES_DOWNLOAD_PRODUCT','section1'=>'Grid','section2'=>'Catalog','name'=>'Properties grid: Download product','description'=>'', 'default_admin'=>1, 'default_value'=>1)

	, 'MEN_TOO_FOULEFACTORY' => array('id'=>'MEN_TOO_FOULEFACTORY','section1'=>'Menu','section2'=>'Tools','name'=>'FouleFactory','description'=>'', 'default_admin'=>1, 'default_value'=>1)

    , 'ACT_ORD_UPDATE_STATUS' => array('id'=>'ACT_ORD_UPDATE_STATUS','section1'=>'Action','section2'=>'Orders','name'=>'Update status','description'=>'', 'default_admin'=>1, 'default_value'=>1)

);
SC_Ext::readCustomGridsConfigXML("permissions");
SC_Ext::readCustomCustomersGridsConfigXML("permissions");
SC_Ext::readCustomOrdersGridsConfigXML("permissions");

	// ----------------------------------------------------------------------------
	//
	//  Function:   loadPermissions
	//  Purpose:		Load permissions from Configuration table of default values if not found
	//  Arguments:	
	//
	// ----------------------------------------------------------------------------
	function loadPermissions()
	{
		global $permissions_list, $local_permissions;
		$local_permissions = unserialize( SCI::getConfigurationValue('SC_PERMISSIONS') );
		if ($local_permissions===false)
			$local_permissions=array();
	}

	// ----------------------------------------------------------------------------
	//
	//  Function:   _r
	//  Purpose:	get permissions for current employee
	//  Arguments:	string: key
	//
	// ----------------------------------------------------------------------------
	function _r($key){
		global $permissions_list, $local_permissions, $sc_agent;
		$return = 0;
		if(isset($permissions_list[$key]))
		{
			$permissions = $permissions_list[$key];
			
			// permissions de l'employé
			if(isset($local_permissions["employees"][$sc_agent->id_employee][$key]))
			{
				/*if($key=="GRI_CAT_VIEW_GRID_DELIVERY")
					echo "em : ".$sc_agent->id_employee." =>".$local_permissions["employees"][$sc_agent->id_employee][$key];*/
				$return = $local_permissions["employees"][$sc_agent->id_employee][$key];
			}
			// permissions du profil
			elseif(isset($local_permissions["profils"][$sc_agent->id_profile][$key]))
			{
				/*if($key=="GRI_CAT_VIEW_GRID_DELIVERY")
					echo "pr : ".$sc_agent->id_profile." =>".$local_permissions["profils"][$sc_agent->id_profile][$key];*/
				$return = $local_permissions["profils"][$sc_agent->id_profile][$key];
			}
			// permissions par défaut pour super_admin
			elseif($sc_agent->id_profile==1)
			{
				/*if($key=="GRI_CAT_VIEW_GRID_DELIVERY")
					echo "default admin =>".$permissions["default_admin"];*/
				$return = $permissions["default_admin"];
			}
			// permissions par défaut
			else
			{
				/*if($key=="GRI_CAT_VIEW_GRID_DELIVERY")
					echo "default =>".$permissions["default_value"];*/
				$return = $permissions["default_value"];
			}
		}
		return $return;
	}
	
loadPermissions();
