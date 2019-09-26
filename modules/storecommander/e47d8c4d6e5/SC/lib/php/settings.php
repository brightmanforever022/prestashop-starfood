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

$default_settings=array(
		 'CAT_SHORT_DESC_SIZE' => array('id'=>'CAT_SHORT_DESC_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Database','default_value'=>(version_compare(_PS_VERSION_, '1.4.5.1', '>=')?800:400),'name'=>'max charset in short description field','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CAT_META_TITLE_SIZE' => array('id'=>'CAT_META_TITLE_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Database','default_value'=>128,'name'=>'meta title field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CAT_META_DESC_SIZE' => array('id'=>'CAT_META_DESC_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Database','default_value'=>255,'name'=>'meta description field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CAT_META_KEYWORDS_SIZE' => array('id'=>'CAT_META_KEYWORDS_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Database','default_value'=>255,'name'=>'meta keywords field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CAT_LINK_REWRITE_SIZE' => array('id'=>'CAT_LINK_REWRITE_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Database','default_value'=>128,'name'=>'link rewrite field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CAT_PROD_GRID_DEFAULT' => array('id'=>'CAT_PROD_GRID_DEFAULT','section1'=>'Catalog','section2'=>'Interface','default_value'=>'grid_light','name'=>'default product grid view','description'=>'Set product grid view displayed when you launch SC. (grid_light, grid_large, grid_delivery, grid_price, grid_discount, grid_seo, grid_reference)')
		,'CAT_PROD_GRID_DESCRIPTION' => array('id'=>'CAT_PROD_GRID_DESCRIPTION','section1'=>'Catalog','section2'=>'Interface','default_value'=>'0','name'=>'enable descriptions grid','description'=>'Enable descriptions grid. Note: the product html code can create defects in Store Commander. You should use it with small text descriptions only.')
		,'CAT_PROD_LANGUAGE_ALL' => array('id'=>'CAT_PROD_LANGUAGE_ALL','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'1','name'=>'display all languages','description'=>'Possible values:<br/>0: Only enabled languages are available in the interface.<br/>1: All languages are available in the interface.')
		,'CAT_PROD_GRID_TABULATION' => array('id'=>'CAT_PROD_GRID_TABULATION','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'0','name'=>'tabulation direction','description'=>'When the tabulation key is pressed, the next element to edit is:<br/>0: the next column<br/>1: the next line<br/>(you need to restart Store Commander)')
		,'CAT_PROD_GRID_DISABLE_IMAGE' => array('id'=>'CAT_PROD_GRID_DISABLE_IMAGE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'0','name'=>'disable images in grids','description'=>'Disable product images in grids to improve performance.<br/>Possible values: 0: images are present in the grids<br/>1: images are not present and the grid is loaded faster.')
		,'CAT_PROD_GRID_IMAGE_SIZE' => array('id'=>'CAT_PROD_GRID_IMAGE_SIZE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'small'.(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?'_default':''),'name'=>'size of images in grids','description'=>'Set the size of the images displayed in the grids. The possible values are the name of the image format in PrestaShop (in Tab Preferences > Image:small, medium,...)')
		,'CAT_PROD_IMG_JPGCOMPRESS' => array('id'=>'CAT_PROD_IMG_JPGCOMPRESS','section1'=>'Catalog','section2'=>'Image','default_value'=>'80','name'=>'JPG compression level','description'=>'Set compression level for uploaded product images. Possible values: 20 to 100 (100 is highest)')
		,'CAT_PROD_IMG_JPGPROGRESSIVE' => array('id'=>'CAT_PROD_IMG_JPGPROGRESSIVE','section1'=>'Catalog','section2'=>'Image','default_value'=>'0','name'=>'JPG progressive','description'=>'The image is created as a progressive JPEG.')
		,'CAT_PROD_IMG_PNGCOMPRESS' => array('id'=>'CAT_PROD_IMG_PNGCOMPRESS','section1'=>'Catalog','section2'=>'Image','default_value'=>'7','name'=>'PNG compression level','description'=>'Set compression level for uploaded product images in PNG format. Possible values: 0 to 9 (0 is highest)')
		,'CAT_PROD_IMG_SAVE_FILENAME' => array('id'=>'CAT_PROD_IMG_SAVE_FILENAME','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'save image filename in database','description'=>'Possible values:<br/>0: The image name is not saved, this is the Prestashop standard behavior.<br/>1: The filename is saved to skip the import process and display of the filename in the grid of images.')
		,'CAT_PROD_IMG_DISPLAY_FILENAME' => array('id'=>'CAT_PROD_IMG_DISPLAY_FILENAME','section1'=>'Catalog','section2'=>'Image','default_value'=>'0','name'=>'display image filename in grid','description'=>'Possible values:<br/>0: The image name is not displayed.<br/>1: The filename is displayed in the grid of images if the name has been saved previously.')
		,'CAT_PROD_IMG_RESIZE_BGCOLOR' => array('id'=>'CAT_PROD_IMG_RESIZE_BGCOLOR','section1'=>'Catalog','section2'=>'Image','default_value'=>'255,255,255','name'=>'background color of resized images','description'=>'Set the background color of resized images when you upload new images. (R,G,B format)')
		,'CAT_PROD_IMG_PNG_METHOD' => array('id'=>'CAT_PROD_IMG_PNG_METHOD','section1'=>'Catalog','section2'=>'Image','default_value'=>'0','name'=>'use PNG format','description'=>'Enable PNG format support in Store Commander and Prestashop.<br/>Possible values:<br/>0: No PNG support (Prestashop standard)<br/>1: PNG file is renamed with JPG file extension<br/>2: Both PNG and JPG format are used.<br/><a target="_blank" href="https://www.storecommander.com/support/en/product-amp-combination-management/701-does-store-commander-support-png-format.html">See documentation</a>')
		,'CAT_PROD_IMG_OLD_PATH' => array('id'=>'CAT_PROD_IMG_OLD_PATH','section1'=>'Catalog','section2'=>'Image','default_value'=>'0','name'=>'use old image path','description'=>'Force the image file path to the old system [id_product]-[id_image]-[size].jpg. Usefull for servers with "safemode".')
		,'CAT_PROD_CAT_DEF_EXT' => array('id'=>'CAT_PROD_CAT_DEF_EXT','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Category','default_value'=>'0','name'=>'allow external default category','description'=>'Allow you to set a default category for a product even if the product is not present in this category.')
		,'CAT_PROD_CREA_QTY' => array('id'=>'CAT_PROD_CREA_QTY','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'1','name'=>'new product quantity default','description'=>'Product quantity used when the product is created in SC.')
		,'CAT_PROD_CREA_REF' => array('id'=>'CAT_PROD_CREA_REF','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'','name'=>'new product reference default','description'=>'Product reference used when the product is created in SC.')
		,'CAT_PROD_CREA_SUPREF' => array('id'=>'CAT_PROD_CREA_SUPREF','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'','name'=>'new product supplier reference default','description'=>'Product supplier reference used when the product is created in SC.')
		,'CAT_PROD_CREA_ACTIVE' => array('id'=>'CAT_PROD_CREA_ACTIVE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'0','name'=>'new product active state default','description'=>'Active state used when the product is created in SC. The active column must be present in the grid.')
		,'CAT_PROD_CREA_MANUFACTURER' => array('id'=>'CAT_PROD_CREA_MANUFACTURER','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'0','name'=>'default manufacturer for new products','description'=>'id_manufacturer used when the product is created in SC. The manufacturer column must be present in the grid.')
		,'CAT_PROD_CREA_SUPPLIER' => array('id'=>'CAT_PROD_CREA_SUPPLIER','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'0','name'=>'new product supplier default','description'=>'id_supplier used when the product is created in SC. The supplier column must be present in the grid.')
		,'CAT_PRODPROP_GRID_DEFAULT' => array('id'=>'CAT_PRODPROP_GRID_DEFAULT','section1'=>'Catalog','section2'=>'Interface','default_value'=>'images','name'=>'default product properties panel','description'=>'Set product properties panel displayed when you launch SC. (combinations, descriptions, images, categories, features, discounts, accessories, tags, specificprices)')
		,'CAT_PRODPROP_CAT_SHOW_SUBCATCNT' => array('id'=>'CAT_PRODPROP_CAT_SHOW_SUBCATCNT','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'1','name'=>'display subcategories count','description'=>'Possible values:<br/>0: no count displayed (best display performance)<br/>1: count displayed (best user experience)')
		,'CAT_PROD_GRID_DRAG2CAT_DEFAULT' => array('id'=>'CAT_PROD_GRID_DRAG2CAT_DEFAULT','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'move','name'=>'product drag&drop default behavior','description'=>'Set the product drag&drop on category default behavior when you launch SC. (move, copy)')
		,'CAT_IMPORT_FORCE_IMG_DOWNLOAD' => array('id'=>'CAT_IMPORT_FORCE_IMG_DOWNLOAD','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'import images already imported','description'=>'Possible values:<br/>0: The image found in the CSV file is imported only the first time<br/>1: The image found in the CSV file is always imported')
		,'CAT_IMPORT_DELETE_CATEGORIES' => array('id'=>'CAT_IMPORT_DELETE_CATEGORIES','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'reset product categories before import','description'=>'Possible values:<br/>0: The products\' categories are not modified<br/>1: The product affectation to categories is deleted before import. It allows you to move product from an old category to another one.')
		,'CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_PRICE_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'product price','description'=>'Possible values:<br/>0: The product price is set to 0 and each combination has its own price.<br/>1: The product price is set to the first combination price found in your CSV and other combinations prices are set by subtraction from this price in the database.(Prestashop standard)')
        ,'CAT_IMPORT_FORCE_PROD_REF_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_REF_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'product reference','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product reference is not altered and each combination has its own reference.<br/>1: The product reference becomes the first combination reference.<br/>2: The product reference becomes the first combination reference + "P".')
        ,'CAT_IMPORT_FORCE_PROD_SUPP_REF_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_SUPP_REF_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'product supplier reference','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product reference is not altered and each combination has its own reference.<br/>1: The product reference becomes the first combination reference.<br/>2: The product reference becomes the first combination reference + "P".')
        ,'CAT_IMPORT_FORCE_PROD_WEIGHT_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_WEIGHT_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'product weight','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product weight is not altered and each combination has its own weight.<br/>1: The product weight becomes the first combination weight.')
		,'CAT_IMPORT_CREATE_REFERENCE_1' => array('id'=>'CAT_IMPORT_CREATE_REFERENCE_1','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'auto create reference for multiple attr.','description'=>'If enabled, the attribute name is added to the combination reference. (SOURCEREF_ATTRNAME)')
		,'CAT_IMPORT_CATEGCREA_ACTIVE' => array('id'=>'CAT_IMPORT_CATEGCREA_ACTIVE','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'default status of created categories','description'=>'Available values:<br/>0: created categories by the import process are disabled<br/>1: created categories by the import process are enabled')
		,'CAT_IMPORT_IGNORED_LINES' => array('id'=>'CAT_IMPORT_IGNORED_LINES','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'ignored lines','description'=>'When lines are ignored during import:<br/>0: keep the line in the working file .TODO.csv<br/>1: delete this line in .TODO.csv')
		,'CAT_IMPORT_KEEP_SORTING_ATTRIBUTE_FROM_FILE' => array('id'=>'CAT_IMPORT_KEEP_SORTING_ATTRIBUTE_FROM_FILE','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'keep file sorting attributes','description'=>'When adding attributes<br/>0: let SC sort the attributes<br/>1: sort attribute like in your csv file')
		,'CAT_EXPORT_ROOT_CATEGORY' => array('id'=>'CAT_EXPORT_ROOT_CATEGORY','section1'=>'Catalog','section2'=>'Export','default_value'=>'0','name'=>'Export root category (Prestashop  version < 1.5)','description'=>'Export root category for full paths: Home > ...')
		,'CAT_PROD_GRID_MARGIN_OPERATION' => array('id'=>'CAT_PROD_GRID_MARGIN_OPERATION','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>0,'name'=>'margin operation defintion','description'=>'Set the margin operation for the column [margin] in the price grid in [Prices] view. Available values:<br/>0: priceExcTax - wholesale_price<br/>1: (priceExcTax - wholesale_price)*100 / wholesale_price<br/>2: priceExcTax / wholesale_price<br/>3: priceIncTax / wholesale_price<br/>4: (priceIncTax - wholesale_price)*100 / wholesale_price<br/>5: (priceExcTax - wholesale_price)*100 / priceExcTax')
		,'CAT_PROD_COMBI_METHOD' => array('id'=>'CAT_PROD_COMBI_METHOD','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'0','name'=>'combinations grid format','description'=>'Possible values:<br/>0: 1 combination = 1 unique physical product (standard)<br/>1: product combinations are composed of several disparate attributes (used for special configurators)')
		,'CAT_PROD_WHOLESALEPRICE4DEC' => array('id'=>'CAT_PROD_WHOLESALEPRICE4DEC','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Price','default_value'=>0,'name'=>'use 4 decimals for wholesale price','description'=>'Possible values:<br/>0: wholesale price format with 2 decimals (standard)<br/>1: wholesale price format with 4 decimals')
		,'CAT_PROD_ECOTAXINCLUDED' => array('id'=>'CAT_PROD_ECOTAXINCLUDED','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Price','default_value'=>1,'name'=>'price including taxes contains ecotax','description'=>'Possible values:<br/>0: Ecotax is not included in the Incl. taxes price but purely in the Ecotax column<br/>1: Price including taxes contains ecotax')
		,'CAT_NOTICE_WHOLESALEPRICEHIGHER' => array('id'=>'CAT_NOTICE_WHOLESALEPRICEHIGHER','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'wholesale price > sell price','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert message after cell edition')
		,'CAT_SEO_NAME_TO_URL' => array('id'=>'CAT_SEO_NAME_TO_URL','needRefresh'=>1,'section1'=>'Catalog','section2'=>'SEO','default_value'=>'1','name'=>'always use product name as link rewrite','description'=>'Possible values:<br/>0: SC will NOT modifiy the link_rewrite of the product: you should set it yourself.<br/>1: SC always set the link_rewrite url to the name of the product.')
		,'CAT_SEO_META_TITLE_COLOR' => array('id'=>'CAT_SEO_META_TITLE_COLOR','needRefresh'=>0,'section1'=>'Catalog','section2'=>'SEO','default_value'=>'70','name'=>'color cell if meta_title exceeds the length defined by the user','description'=>'number of characters from which the cell will be colored')
		,'CMS_PAGE_LANGUAGE_ALL' => array('id'=>'CMS_PAGE_LANGUAGE_ALL','needRefresh'=>1,'section1'=>'CMS','section2'=>'Interface','default_value'=>'1','name'=>'display all languages','description'=>'Possible values:<br/>0: Only enabled languages are available in the interface.<br/>1: All languages are available in the interface.')
		,'CMS_PAGEPROP_GRID_DEFAULT' => array('id'=>'CMS_PAGEPROP_GRID_DEFAULT','section1'=>'CMS','section2'=>'Interface','default_value'=>'description','name'=>'default CMS properties panel','description'=>'Set CMS properties panel displayed when you launch SC. (seo, description)')
		,'CMS_PAGE_GRID_DEFAULT' => array('id'=>'CMS_PAGE_GRID_DEFAULT','section1'=>'CMS','section2'=>'Interface','default_value'=>'grid_light','name'=>'default product grid view','description'=>'Set CMS grid view displayed when you launch SC. (grid_light, grid_large, grid_seo)')
		,'CMS_LINK_REWRITE_SIZE' => array('id'=>'CMS_LINK_REWRITE_SIZE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Database','default_value'=>128,'name'=>'link rewrite field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CMS_PAGE_LANGUAGE_ALL' => array('id'=>'CMS_PAGE_LANGUAGE_ALL','needRefresh'=>1,'section1'=>'CMS','section2'=>'Interface','default_value'=>'1','name'=>'display all languages','description'=>'Possible values:<br/>0: Only enabled languages are available in the interface.<br/>1: All languages are available in the interface.')
		,'CMS_SEO_META_TITLE_COLOR' => array('id'=>'CMS_SEO_META_TITLE_COLOR','needRefresh'=>0,'section1'=>'CMS','section2'=>'SEO','default_value'=>'70','name'=>'color cell if meta_title exceeds the length defined by the user','description'=>'number of characters from which the cell will be colored')
		,'CMS_PAGE_OPEN_URL' => array('id'=>'CMS_PAGE_OPEN_URL','needRefresh'=>1,'section1'=>'CMS','section2'=>'Interface','default_value'=>'3','name'=>'max CMS to open in browser','description'=>'Set the maximum number of new browser tabs to open when you do a right click on CMS > See on shop')
		,'CMS_PAGE_CREA_ACTIVE' => array('id'=>'CMS_PAGE_CREA_ACTIVE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Page','default_value'=>'0','name'=>'new page active state default','description'=>'Active state used when the page is created in SC. The active column must be present in the grid.')
		,'CMS_META_DESC_SIZE' => array('id'=>'CMS_META_DESC_SIZE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Database','default_value'=>255,'name'=>'meta description field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CMS_META_KEYWORDS_SIZE' => array('id'=>'CMS_META_KEYWORDS_SIZE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Database','default_value'=>255,'name'=>'meta keywords field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'CMS_PAGE_CREA_ACTIVE' => array('id'=>'CMS_PAGE_CREA_ACTIVE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Page','default_value'=>'0','name'=>'new CMS active state default','description'=>'Active state used when the CMS is created in SC. The active column must be present in the grid.')
		,'CMS_PAGE_CREA_INDEX' => array('id'=>'CMS_PAGE_CREA_INDEX','needRefresh'=>1,'section1'=>'CMS','section2'=>'Page','default_value'=>'0','name'=>'new CMS indexation state default','description'=>'Indexation state used when the CMS is created in SC. The indexation column must be present in the grid.')
		,'CMS_PAGE_LANGUAGE_ALL' => array('id'=>'CMS_PAGE_LANGUAGE_ALL','needRefresh'=>1,'section1'=>'CMS','section2'=>'Interface','default_value'=>'1','name'=>'display all languages','description'=>'Possible values:<br/>0: Only enabled languages are available in the interface.<br/>1: All languages are available in the interface.')

		,'MAN_MANUF_PROP_GRID_DEFAULT' => array('id'=>'MAN_MANUF_PROP_GRID_DEFAULT','section1'=>'Manufacturers','section2'=>'Interface','default_value'=>'description','name'=>'default manufacturer properties panel','description'=>'Set manufacturer properties panel displayed when you launch SC. (seo, description)')
		,'MAN_MANUF_GRID_DEFAULT' => array('id'=>'MAN_MANUF_GRID_DEFAULT','section1'=>'Manufacturers','section2'=>'Interface','default_value'=>'grid_light','name'=>'default manufacturer grid view','description'=>'Set manufacturer grid view displayed when you launch SC. (grid_light, grid_large, grid_seo)')
		,'MAN_MANUF_CREA_ACTIVE' => array('id'=>'MAN_MANUF_CREA_ACTIVE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Manufacturer','default_value'=>'0','name'=>'new manufacturer active state default','description'=>'Active state used when the manufacturer is created in SC. The active column must be present in the grid.')
		,'MAN_SHORT_DESC_SIZE' => array('id'=>'MAN_SHORT_DESC_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>(version_compare(_PS_VERSION_, '1.4.5.1', '>=')?800:400),'name'=>'max charset in short description field','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_META_TITLE_SIZE' => array('id'=>'MAN_META_TITLE_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>128,'name'=>'meta title field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_META_DESC_SIZE' => array('id'=>'MAN_META_DESC_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>255,'name'=>'meta description field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_META_KEYWORDS_SIZE' => array('id'=>'MAN_META_KEYWORDS_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>255,'name'=>'meta keywords field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_PROPERTIES_DESCRIPTION_CSS' => array('id'=>'MAN_PROPERTIES_DESCRIPTION_CSS','section1'=>'Manufacturers','section2'=>'Interface','default_value'=>'1','name'=>'Use global.css in descriptions','description'=>'Use the global.css stylesheet of the shop in the editors. You can set it to 0 if the background of your shop is displayed in the text editors.')
		,'MAN_NAME_SIZE' => array('id'=>'MAN_NAME_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>64,'name'=>'name field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_MANUFACTURER_OPEN_URL' => array('id'=>'MAN_MANUFACTURER_OPEN_URL','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Interface','default_value'=>'3','name'=>'max manufacturer to open in browser','description'=>'Set the maximum number of new browser tabs to open when you do a right click on manufacturer > See on shop')
		,'MAN_PROD_LANGUAGE_ALL' => array('id'=>'MAN_PROD_LANGUAGE_ALL','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Interface','default_value'=>'1','name'=>'display all languages','description'=>'Possible values:<br/>0: Only enabled languages are available in the interface.<br/>1: All languages are available in the interface.')
		,'MAN_SEO_META_TITLE_COLOR' => array('id'=>'MAN_SEO_META_TITLE_COLOR','needRefresh'=>0,'section1'=>'Manufacturers','section2'=>'SEO','default_value'=>'70','name'=>'color cell if meta_title exceeds the length defined by the user','description'=>'number of characters from which the cell will be colored')
		,'MAN_META_DESC_SIZE' => array('id'=>'MAN_META_DESC_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>255,'name'=>'meta description field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')
		,'MAN_META_KEYWORDS_SIZE' => array('id'=>'MAN_META_KEYWORDS_SIZE','needRefresh'=>1,'section1'=>'Manufacturers','section2'=>'Database','default_value'=>255,'name'=>'meta keywords field size','description'=>'Set the maximum character set SC checks before saving it in the database. This does NOT modify the database.')

		,'APP_FORCE_UPDATE' => array('id'=>'APP_FORCE_UPDATE','needRefresh'=>1,'section1'=>'Application','section2'=>'Update','default_value'=>0,'name'=>'force update','description'=>'Allow to display the update tool and force the update of Store Commander. You need to reload SC.')
		,'APP_DISABLE_CHANGE_HISTORY' => array('id'=>'APP_DISABLE_CHANGE_HISTORY','needRefresh'=>1,'section1'=>'Application','section2'=>'Tools','default_value'=>0,'name'=>'disable change history','description'=>'Do not save modifications in database. This option hides the Tools > Change history menu.')
		,'APP_CHANGE_HISTORY_MAX' => array('id'=>'APP_CHANGE_HISTORY_MAX','section1'=>'Application','section2'=>'Tools','default_value'=>500,'name'=>'max elements in change history','description'=>'Set the maximum of elements to store in database')
//		,'APP_COMPAT_EBAY' => array('id'=>'APP_COMPAT_EBAY','needRefresh'=>1,'section1'=>'Application','section2'=>'Compatibility','default_value'=>0,'name'=>'eBay module','description'=>'Set this option to 1 if you use the eBay module developped by Prestashop. You need to reload SC. <a target="_blank" href="http://support.storecommander.com/categories/search?query=ebay&locale=1">Read more</a>')
		,'APP_COMPAT_HOOK' => array('id'=>'APP_COMPAT_HOOK','needRefresh'=>1,'section1'=>'Application','section2'=>'Compatibility','default_value'=>1,'name'=>'Prestashop hooks','description'=>'Set this option to 0 if you don\'t want SC to use the Prestashop hook system.')
		,'APP_COMPAT_MEMORY' => array('id'=>'APP_COMPAT_MEMORY','needRefresh'=>0,'section1'=>'Application','section2'=>'Compatibility','default_value'=>0,'name'=>'php memory limit','description'=>'Set this option to "128M" for example if you want to set the php memory limit (ini_set "memory_limit"). Set to "0" to use system default value.')
		,'APP_COMPAT_USERLOGIN' => array('id'=>'APP_COMPAT_USERLOGIN','needRefresh'=>0,'section1'=>'Application','section2'=>'Compatibility','default_value'=>0,'name'=>'login as selected customer','description'=>'Use this compatibility mode if "Login as selected customer" does not work on the front office')
		,'APP_FORCE_OPEN_BROWSER_TAB' => array('id'=>'APP_FORCE_OPEN_BROWSER_TAB','needRefresh'=>1,'section1'=>'Application','section2'=>'Interface','default_value'=>0,'name'=>'open browser tab','description'=>'Set to 1 if you wish to open PS windows in a new browser tab instead of SC window. (forced to 1 if SC is run on iPad)')
		,'CAT_PROD_DUPLICATE' => array('id'=>'CAT_PROD_DUPLICATE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'5','name'=>'max products to duplicate','description'=>'Set the maximum number of duplicate products to create in one click.')
		,'CAT_PROD_OPEN_URL' => array('id'=>'CAT_PROD_OPEN_URL','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'5','name'=>'max products to open in browser','description'=>'Set the maximum number of new browser tabs to open when you do a right click on products > See on shop')
		,'CAT_PROD_GRID_MARGIN_COLOR' => array('id'=>'CAT_PROD_GRID_MARGIN_COLOR','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Interface','default_value'=>'','name'=>'colors of margin cells','description'=>'Set the rules for the background color of the margin cells.<br/>Format: Value:Color;Value:Color;...<br/>Exemple: 20:#BA2329;50:#E3772B;1000:#34841F<br/>If the margin is < Value then the cell will be Color.<br/><a target="_blank" href="https://www.storecommander.com/support/en/product-amp-combination-management/648-how-to-manage-your-products-margins.html">Read more</a>')
		,'CAT_NOTICE_EXPORT_SEPARATOR' => array('id'=>'CAT_NOTICE_EXPORT_SEPARATOR','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'export: field separator = value separator','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert message after cell edition')
		,'ORD_ORDER_GRID_DEFAULT' => array('id'=>'ORD_ORDER_GRID_DEFAULT','section1'=>'Orders','section2'=>'Interface','default_value'=>'grid_light','name'=>'default order grid view','description'=>'Set order grid view displayed when you launch SC. (grid_light, grid_large, grid_picking, grid_delivery)')
		,'ORD_ORDPROP_GRID_DEFAULT' => array('id'=>'ORD_ORDPROP_GRID_DEFAULT','section1'=>'Orders','section2'=>'Interface','default_value'=>'orderproduct','name'=>'default order properties panel','description'=>'Set order properties panel displayed when you launch SC. (orderproduct,message,orderhistory,orderpsorderpage)')

		,'CUS_CUSTOMER_GRID_DEFAULT' => array('id'=>'CUS_CUSTOMER_GRID_DEFAULT','section1'=>'Customers','section2'=>'Interface','default_value'=>'grid_light','name'=>'default customer grid view','description'=>'Set customer grid view displayed when you launch SC. (grid_light, grid_large, grid_address, grid_convert)')
		,'CUS_CUSPROP_GRID_DEFAULT' => array('id'=>'CUS_CUSPROP_GRID_DEFAULT','section1'=>'Customers','section2'=>'Interface','default_value'=>'customerorder','name'=>'default customer properties panel','description'=>'Set customer properties panel displayed when you launch SC. (customerorder,message,customergroup,customeraddress)')
		,'CUS_USE_COMPANY_FIELDS' => array('id'=>'CUS_USE_COMPANY_FIELDS','section1'=>'Customers','section2'=>'Interface','default_value'=>'0','name'=>'Show Company, Reg. and SIC cols','description'=>'Dou you want to display these 3 cols in the customers grids?')
		,'CUS_MAX_CUSTOMERS' => array('id'=>'CUS_MAX_CUSTOMERS','section1'=>'Customers','section2'=>'Interface','default_value'=>'100','name'=>'maximum customers displayed','description'=>'Set the maximum number of customers displayed in the main grid. You can increase this value if your server\'s ressources are sufficient.')
		
		,'CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE' => array('id'=>'CAT_ADVANCEDSTOCKS_WAREHOUSESHARE_DEFAULT_TYPE','default_value'=>'0','name'=>'','description'=>'')

		,'CAT_PROD_LIMIT_SMARTRENDERING' => array('id'=>'CAT_PROD_LIMIT_SMARTRENDERING','section1'=>'Catalog','section2'=>'Interface','default_value'=>'500','name'=>'optimized grid loading','description'=>'Use an optimized grid display method for grids with more than 500 lines (by default). Set to 0 to disable the optimized display method.')

		,'CAT_PROD_IMAGE_GENERATION_METHOD' => array('id'=>'CAT_PROD_IMAGE_GENERATION_METHOD','section1'=>'Catalog','section2'=>'Image','default_value'=>'0','name'=>'images creation mode','description'=>'Possible values:<br/>0: the whole image is resized to fit the destination size with colored background (PS standard)<br/>1: the image is cropped to get a better resolution but you lose the image borders<br/>2: the whole image is resized to fit the destination size without colored background')

		,'CAT_PROD_AUTO_ACTIVATION_MB_SHARE' => array('id'=>'CAT_PROD_AUTO_ACTIVATION_MB_SHARE','section1'=>'Catalog','section2'=>'MultiStores','default_value'=>'1','name'=>'share and activate product','description'=>'Set to 0 if you don\'t want to activate the product you are sharing in a new shop')

//		,'CAT_PROD_MVT_STOCK_ADD_WHOLESELA_PRICE' => array('id'=>'CAT_PROD_MVT_STOCK_ADD_WHOLESELA_PRICE','section1'=>'Catalog','section2'=>'Stock mvt','default_value'=>'1','name'=>'Add stock mvt: automatically add the product\'s current wholesale price','description'=>'When you are in the window allowing you to add new stock movements, the product\'s current wholesale price will automatically be inserted in this field, when the option is enabled')
		
		,'CAT_EXPORT_IMAGE_FORMAT' => array('id'=>'CAT_EXPORT_IMAGE_FORMAT','section1'=>'Catalog','section2'=>'Export','default_value'=>'','name'=>'Image\'s format to export','description'=>'Put the image\'s format to export: default, large, small,.... Leave space to put the original format.')
		
		,'CAT_ADVANCEDSTOCK_DEFAULT' => array('id'=>'CAT_ADVANCEDSTOCK_DEFAULT','section1'=>'Catalog','section2'=>'Advanced stock','default_value'=>'1','name'=>'Default type for Advanced Stock Management','description'=>'When Advanced Stock Management activated, define default type for a new product.<br/>1: Disabled<br/>2: Enabled<br/>3: Enabled + Manual Mgmt')
		
		,'CAT_ROUND_PRICE' => array('id'=>'CAT_ROUND_PRICE','section1'=>'Catalog','section2'=>'Price','default_value'=>'0','name'=>'Rounding prices up','description'=>'Rounding prices, possible values:<br/>0: Rounding up or down to the nearest 5 cents<br/>1:  Rounding up<br/>2: Rounding down<br/><a href="https://www.storecommander.com/support/en/product-amp-combination-management/646-how-does-price-rounding-rules-option-work.html" target="_blank">Read more</a>')
		
		,'CAT_IMPORT_FORCE_PROD_EAN_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_EAN_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'Product ean','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product EAN is not altered and each combination has its own EAN.<br/>1: The product EAN becomes the first combination EAN.')
		,'CAT_IMPORT_FORCE_PROD_UPC_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_UPC_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'Product upc','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product UPC is not altered and each combination has its own UPC.<br/>1: The product UPC becomes the first combination UPC.')
        ,'CAT_IMPORT_FORCE_PROD_ISBN_TO_FIRST_COMBI' => array('id'=>'CAT_IMPORT_FORCE_PROD_ISBN_TO_FIRST_COMBI','section1'=>'Catalog','section2'=>'Import','default_value'=>'1','name'=>'Product isbn','description'=>'When importing a product with combinations:<br/>Possible values:<br/>0: The product ISBN is not altered and each combination has its own ISBN.<br/>1: The product ISBN becomes the first combination ISBN.')

        ,'CAT_PROPERTIES_CUSTOMERS_START_DATE' => array('id'=>'CAT_PROPERTIES_CUSTOMERS_START_DATE','section1'=>'Catalog','section2'=>'Interface','default_value'=>'','name'=>'Customers grid: minimum order date','description'=>'Allows to display the orders spent from this date.<br/>Required format : YYYY-MM-DD')
		,'CAT_PROPERTIES_DESCRIPTION_CSS' => array('id'=>'CAT_PROPERTIES_DESCRIPTION_CSS','section1'=>'Catalog','section2'=>'Interface','default_value'=>'1','name'=>'Use global.css in descriptions','description'=>'Use the global.css stylesheet of the shop in the editors. You can set it to 0 if the background of your shop is displayed in the text editors.')
		,'CAT_PROPERTIES_DESCRIPTION_AUTO_SIZING' => array('id'=>'CAT_PROPERTIES_DESCRIPTION_AUTO_SIZING','needRefresh'=>1, 'section1'=>'Catalog','section2'=>'Interface','default_value'=>'1','name'=>'Automatic resizing of the property','description'=>'Possible values:<br/>0: disable automatic resizing.<br/>1: always resizing the property.')

		,'CORE_USE_EXTENSIONS' => array('id'=>'CORE_USE_EXTENSIONS','section1'=>'Application','section2'=>'Tools','default_value'=>'1','name'=>'Use SC extensions','description'=>'Enable/Disable SC Extensions')
		
		,'CAT_SEO_CAT_NAME_TO_URL' => array('id'=>'CAT_SEO_CAT_NAME_TO_URL','needRefresh'=>1,'section1'=>'Catalog','section2'=>'SEO','default_value'=>'1','name'=>'Always use category name as link rewrite','description'=>'Possible values:<br/>0: SC will NOT modifiy the link_rewrite of the category: you should set it yourself.<br/>1: SC always set the link_rewrite url to the name of the category.')
		
		,'CAT_NOTICE_UPDATE_PRODUCT_URL_REWRITE' => array('id'=>'CAT_NOTICE_UPDATE_PRODUCT_URL_REWRITE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'Notice for the automatic modification of products link_rewrite','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert message after name edition')
		
		,'CAT_NOTICE_DEFAULT_CONFIG_ADVANCED_STOCK' => array('id'=>'CAT_NOTICE_DEFAULT_CONFIG_ADVANCED_STOCK','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'Notice when Advanced stock preference in PS is different to SC preference','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert message at SC load.')
		
		,'CAT_ACTIVE_HOOK_UPDATE_QUANTITY' => array('id'=>'CAT_ACTIVE_HOOK_UPDATE_QUANTITY','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'1','name'=>'Use the Prestashop hook when quantity updated','description'=>'Set this option to 0 if you don\'t want SC to use the Prestashop hook system.')
		
		,'CAT_COLOR_SAME_COMBI' => array('id'=>'CAT_COLOR_SAME_COMBI','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'1','name'=>'Color the combinations in red with same attributes','description'=>'Color the row in red when some combinations have the same attributes values.')
		
		,'CAT_NOTICE_CREATE_FIRST_COMBI' => array('id'=>'CAT_NOTICE_CREATE_FIRST_COMBI','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'Notice when create first combi','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert.')
		
		,'CUS_DISPLAY_DELETED' => array('id'=>'CUS_DISPLAY_DELETED','section1'=>'Customers','section2'=>'Interface','default_value'=>'0','name'=>'Display deleted accounts','description'=>'Set to 1 if you want to display deleted accounts in customer views')
		
		,'APP_UPDATEQUEUE_LIMIT' => array('id'=>'APP_UPDATEQUEUE_LIMIT','needRefresh'=>0,'section1'=>'Application','section2'=>'Modification','default_value'=>'20','name'=>'Number of tasks sent to the server','description'=>'Number of tasks sent similtaneously to be actioned by the server')
		
		,'CAT_PROD_IMAGE_AUTO_SHOP_SHARE' => array('id'=>'CAT_PROD_IMAGE_AUTO_SHOP_SHARE','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Image','default_value'=>'1','name'=>'Automatically share the products images','description'=>'Automatically share the products images when the product is shared with a new shop. Set the option to 0 if you do not wish to share images automatically.')

		,'APP_RICH_EDITOR' => array('id'=>'APP_RICH_EDITOR','needRefresh'=>1,'section1'=>'Application','section2'=>'Tools','default_value'=>'0','name'=>'Use TinyMCE as text editor','description'=>'Set this option to 1 if you want to use TinyMCE rather than CKeditor (default).')

		,'CAT_NOTICE_HOOKACTIONPRODUCTUPDATE' => array('id'=>'CAT_NOTICE_HOOKACTIONPRODUCTUPDATE','needRefresh'=>0,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'Bad usage for hook actionProductUpdate','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert message if a bad usage for hook actionProductUpdate is detected')

		,'CAT_NOTICE_PSVERSIONUPDATE' => array('id'=>'CAT_NOTICE_PSVERSIONUPDATE','needRefresh'=>0,'section1'=>'Application','section2'=>'Notice','default_value'=>'1','name'=>'Show important information about your Prestashop version','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert.')

		,'CAT_IMPORT_ACTIVE_DEFAULT' => array('id'=>'CAT_IMPORT_ACTIVE_DEFAULT','section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'New products default status','description'=>'Active status used when the product is created by CSV import')
		
		,'CAT_EXPORT_EAN13_COMBI' => array('id'=>'CAT_EXPORT_EAN13_COMBI','section1'=>'Catalog','section2'=>'Export','default_value'=>'0','name'=>'Export EAN13','description'=>'When exporting EAN13 for combinations:<br/>Possible values:<br/>0: the exported EAN13 is associated to the combinations.<br/>1: the exported EAN13 is associated to the product if EAN13 is empty for combinations.')
		
		,'CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX' => array('id'=>'CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Price','default_value'=>'1','name'=>'Apply specific prices by default','description'=>'Possible values:<br/>0: product excl. tax<br/>1: product incl. tax')

		,'CAT_PROD_PRICE_DEFAULT_COMBINATION' => array('id'=>'CAT_PROD_PRICE_DEFAULT_COMBINATION','needRefresh'=>0,'section1'=>'Catalog','section2'=>'Price','default_value'=>'0','name'=>'Modify parent product price','description'=>'Should change:<br/>0: All combinations (Prestashop behavior)<br/>1: Only the default combination')

		,'CAT_NOTICE_SAVE_DESCRIPTION' => array('id'=>'CAT_NOTICE_SAVE_DESCRIPTION','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Notice','default_value'=>'1','name'=>'Notice when descriptions are not saved','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert.')
		
		,'CAT_PROD_IMPORT_METHOD' => array('id'=>'CAT_PROD_IMPORT_METHOD','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Import','default_value'=>'0','name'=>'Method to import products','description'=>'Possible values:<br/>0: standard process<br/>1: use of views.')

		,'CMS_NOTICE_SAVE_DESCRIPTION' => array('id'=>'CMS_NOTICE_SAVE_DESCRIPTION','needRefresh'=>1,'section1'=>'CMS','section2'=>'Notice','default_value'=>'1','name'=>'Notice when content are not saved','description'=>'Possible values:<br/>0: don\'t trigger alert<br/>1: display alert.')

		,'CMS_PROPERTIES_DESCRIPTION_CSS' => array('id'=>'CMS_PROPERTIES_DESCRIPTION_CSS','section1'=>'CMS','section2'=>'Interface','default_value'=>'1','name'=>'Use global.css in content','description'=>'Use the global.css stylesheet of the shop in the editors. You can set it to 0 if the background of your shop is displayed in the text editors.')

		,'CMS_PAGE_DUPLICATE' => array('id'=>'CMS_PAGE_DUPLICATE','needRefresh'=>1,'section1'=>'CMS','section2'=>'Interface','default_value'=>'5','name'=>'max CMS to duplicate','description'=>'Set the maximum number of duplicate CMS to create in one click.')

		,'CMS_SEO_CAT_NAME_TO_URL' => array('id'=>'CMS_SEO_CAT_NAME_TO_URL','needRefresh'=>1,'section1'=>'CMS','section2'=>'SEO','default_value'=>'1','name'=>'Always use category name as link rewrite','description'=>'Possible values:<br/>0: SC will NOT modifiy the link_rewrite of the CMS category: you should set it yourself.<br/>1: SC always set the link_rewrite url to the name of the CMS category.')

		,'CMS_SEO_META_TITLE_TO_URL' => array('id'=>'CMS_SEO_META_TITLE_TO_URL','needRefresh'=>1,'section1'=>'CMS','section2'=>'SEO','default_value'=>'1','name'=>'Always use meta_title as link rewrite','description'=>'Possible values:<br/>0: SC will NOT modifiy the link_rewrite of the CMS page: you should set it yourself.<br/>1: SC always set the link_rewrite url to the meta_title of the CMS page.')

		,'CMS_PAGE_LIMIT_SMARTRENDERING' => array('id'=>'CMS_PAGE_LIMIT_SMARTRENDERING','section1'=>'CMS','section2'=>'Interface','default_value'=>'500','name'=>'optimized grid loading','description'=>'Use an optimized grid display method for grids with more than 500 lines (by default). Set to 0 to disable the optimized display method.')

		,'APP_DEBUG_CATALOG_IMPORT' => array('id'=>'APP_DEBUG_CATALOG_IMPORT','needRefresh'=>1,'section1'=>'Application','section2'=>'Debug','default_value'=>'0','name'=>'Catalog CSV Import','description'=>'Possible values:<br/>0: debug mode disabled<br/>1: debug mode enabled.')
		
		,'CAT_EXPORT_PRICE_DECIMAL' => array('id'=>'CAT_EXPORT_PRICE_DECIMAL','section1'=>'Catalog','section2'=>'Export','default_value'=>'2','name'=>'Number of decimal to export prices','description'=>'Possible values: 0 to 6')
		
		,'CAT_PROD_COMBI_CREA_QTY' => array('id'=>'CAT_PROD_COMBI_CREA_QTY','needRefresh'=>1,'section1'=>'Catalog','section2'=>'Product','default_value'=>'1','name'=>'new combination quantity default','description'=>'Combination quantity used when the combination is created in SC.')

		,'ORD_EXPORT_DELIVERY_SORT' => array('id'=>'ORD_EXPORT_DELIVERY_SORT','section1'=>'Orders','section2'=>'Export','default_value'=>'1','name'=>'Delivery slips order in PDF file','description'=>'Possible values:<br/>1: by delivery number<br/>2: by id order.')

		,'ORD_EXPORT_USE_M4PDF' => array('id'=>'ORD_EXPORT_USE_M4PDF','section1'=>'Orders','section2'=>'Export','default_value'=>'1','name'=>'Use M4PDF if available','description'=>'Possible values:<br/>1: use M4PDF<br/>0: do not use M4PDF.')

		,'CAT_EXPORT_REF_COMBI' => array('id'=>'CAT_EXPORT_REF_COMBI','section1'=>'Catalog','section2'=>'Export','default_value'=>'0','name'=>'Export Reference','description'=>'When exporting reference for combinations:<br/>Possible values:<br/>0: the exported reference is associated to the combinations.<br/>1: the exported reference is associated to the product if reference is empty for combinations.')

		,'APP_COMPAT_MODULE_PPE' => array('id'=>'APP_COMPAT_MODULE_PPE','needRefresh'=>0,'section1'=>'Application','section2'=>'Compatibility','default_value'=>0,'name'=>'Compatibility for module Product Properties Extension','description'=>'Possible values for min. qty:<br/>0: no decimal (int type)<br/>1: float type in import and interface')

		,'ORD_ORDER_GROUP_BY' => array('id'=>'ORD_ORDER_GROUP_BY','needRefresh'=>1,'section1'=>'Orders','section2'=>'Interface','default_value'=>'1','name'=>'Group orders by ?','description'=>'Possible values:<br/>1: by product name<br/>2: by product ID')

		,'CAT_PROD_ATTCH_DESC' => array('id'=>'CAT_PROD_ATTCH_DESC','section1'=>'Catalog','section2'=>'Attachment','default_value'=>'1','name'=>'Automatic description','description'=>'Possible values:<br/>0: Empty<br/>1: File name + iso code<br/>2: File name')

		,'CAT_EXPORT_UPC_COMBI' => array('id'=>'CAT_EXPORT_UPC_COMBI','section1'=>'Catalog','section2'=>'Export','default_value'=>'0','name'=>'Export UPC','description'=>'When exporting UPC for combinations:<br/>Possible values:<br/>0: the exported UPC is associated to the combinations.<br/>1: the exported UPC is associated to the product if UPC is empty for combinations.')

		,'APP_DISABLED_COLUMN_MOVE' => array('id'=>'APP_DISABLED_COLUMN_MOVE','needRefresh'=>1,'section1'=>'Application','section2'=>'Interface','default_value'=>0,'name'=>'Disable column move in main grids for Catalog, Order and Customer interfaces','description'=>'Possible values:<br/>0: Enabled colmun move.<br/>1: Disabled colmun move.')

		,'CAT_APPLY_ALL_CART_RULES' => array('id'=>'CAT_APPLY_ALL_CART_RULES','section1'=>'Catalog','section2'=>'Price','default_value'=>'1','name'=>'Apply all catalog price rules','description'=>'Possible values:<br/>1: Use SpecificPriceRule::applyAllRules() to use catalog price rules everywhere in SC when you move a product in a category or modify a product<br/>0: Disable catalog price rules modifications during products updates')

        ,'APP_FOULEFACTORY' => array('id'=>'APP_FOULEFACTORY','needRefresh'=>0,'section1'=>'Application','section2'=>'Tools','default_value'=>1,'name'=>'FouleFactory enabled','description'=>'Possible values:<br/>0: disabled<br/>1: enabled')

        ,'APP_TRENDS' => array('id'=>'APP_TRENDS','needRefresh'=>0,'section1'=>'Application','section2'=>'Tools','default_value'=>0,'name'=>'Participate in Trends project and agree to send statistics','description'=>'Possible values:<br/>0: No<br/>1: Yes')

        ,'CAT_EXPORT_ISBN_COMBI' => array('id'=>'CAT_EXPORT_ISBN_COMBI','section1'=>'Catalog','section2'=>'Export','default_value'=>'0','name'=>'Export ISBN','description'=>'When exporting ISBN for combinations:<br/>Possible values:<br/>0: the exported ISBN is associated to the combinations.<br/>1: the exported ISBN is associated to the product if ISBN is empty for combinations.')

        ,'APP_CKEDITOR_AUTOCORRECT_ACTIVE' => array('id'=>'APP_CKEDITOR_AUTOCORRECT_ACTIVE','needRefresh'=>1,'section1'=>'Application','section2'=>'Tools','default_value'=>'1','name'=>'Active autocorrect in CKEditor','description'=>'Possible values:<br/>0: No<br/>1: Yes')

        ,'APP_SKIN_INTERFACE' => array('id'=>'APP_SKIN_INTERFACE','needRefresh'=>1,'section1'=>'Application','section2'=>'Interface','default_value'=>'1','name'=>'Storecommander design','description'=>'Possible values:<br/>1: New design<br/>2: Old design')

);
/*
	0: coef = PV HT - PV HT
	1: coef = (PV HT - PA HT) / PA HT
	2: coef = PV HT / PA HT
	3: coef = PV TTC / PA HT
	4: coef = (PV TTC - PA HT) / PA HT
*/

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		unset($default_settings['CAT_SHORT_DESC_SIZE']);
	}
	if (version_compare(_PS_VERSION_, '1.6.0.0', '<'))
	{
		unset($default_settings['CAT_NOTICE_UPDATE_PRODUCT_URL_REWRITE']);
	}
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		unset($default_settings['CAT_ACTIVE_HOOK_UPDATE_QUANTITY']);
	}
	if (version_compare(_PS_VERSION_, '1.6.0.11', '<'))
	{
		//unset($default_settings['CAT_PROD_SPECIFIC_PRICES_DEFAULT_TAX']);
	}
	if (version_compare(_PS_VERSION_, '1.7.0.0', '<'))
	{
        unset($default_settings['CAT_IMPORT_FORCE_PROD_ISBN_TO_FIRST_COMBI']);
        unset($default_settings['CAT_EXPORT_ISBN_COMBI']);
	}
	if(!SCI::getConfigurationValue('M4PDF_PDF_INVOICES') && !SCI::getConfigurationValue('M4PDF_PDF_DELIVERYSLIPS'))
	{
		unset($default_settings['ORD_EXPORT_USE_M4PDF']);
	}

// ----------------------------------------------------------------------------
//
//  Function:   loadSettings
//  Purpose:		Load settings from Configuration table of default values if not found
//  Arguments:	
//
// ----------------------------------------------------------------------------
function loadSettings()
{
	global $default_settings,$local_settings,$custom_settings;
	$tmp=$default_settings;
	if(SC_TOOLS)
		if (file_exists(SC_TOOLS_DIR.'settings/settings.php'))
		{
			require_once(SC_TOOLS_DIR.'settings/settings.php');
			if (isset($custom_settings) && is_array($custom_settings))
				$default_settings = array_merge($default_settings,$custom_settings);
		}
	$tmp=$default_settings;

	$local_settings=json_decode(SCI::getConfigurationValue('SC_SETTINGS',0),true);
	foreach($tmp AS $k => $v)
	{
		unset($tmp[$k]['id']);
		unset($tmp[$k]['section1']);
		unset($tmp[$k]['section2']);
		unset($tmp[$k]['name']);
		unset($tmp[$k]['description']);
		unset($tmp[$k]['needRefresh']);
		if ($local_settings==null || !sc_array_key_exists($k,$local_settings))
		{
			$tmp[$k]['value']=$default_settings[$k]['default_value'];
		}else{
			if (is_array($local_settings[$k]) && sc_array_key_exists('value',$local_settings[$k]))
				$tmp[$k]['value']=$local_settings[$k]['value'];
		}
		unset($tmp[$k]['default_value']);
	}
	$local_settings=$tmp;
	if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
	{
		$local_settings['CAT_APPLY_ALL_CART_RULES']['value'] = 0;
	}
	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$local_settings['CAT_SHORT_DESC_SIZE']['value'] = ((int)SCI::getConfigurationValue('PS_PRODUCT_SHORT_DESC_LIMIT')>0?(int)SCI::getConfigurationValue('PS_PRODUCT_SHORT_DESC_LIMIT'):800);
	}
	if (version_compare(_PS_VERSION_, '1.6.0.0', '>='))
	{
		$local_settings['CAT_SEO_NAME_TO_URL']['value'] = (int)SCI::getConfigurationValue('PS_FORCE_FRIENDLY_PRODUCT');
		//$local_settings['APP_COMPAT_HOOK']['value'] = (int)SCI::getConfigurationValue('PS_DISABLE_NON_NATIVE_MODULE');
	}
}

// ----------------------------------------------------------------------------
//
//  Function:   saveSettings
//  Purpose:		Save settings in Configuration table
//  Arguments:	
//
// ----------------------------------------------------------------------------
function saveSettings()
{
	global $local_settings;
	
	SCI::updateConfigurationValue('SC_SETTINGS',json_encode($local_settings),true);
}

// ----------------------------------------------------------------------------
//
//  Function:   resetSettings
//  Purpose:		Reset settings in Configuration table
//  Arguments:	
//
// ----------------------------------------------------------------------------
function resetSettings()
{
	SCI::updateConfigurationValue('SC_SETTINGS','',true);
}

// ----------------------------------------------------------------------------
//
//  Function:   _s
//  Purpose:		Get setting value
//  Arguments:	string: ID of setting
//
// ----------------------------------------------------------------------------
function _s($id){
	global $local_settings;
	if (!is_array($local_settings) || !sc_array_key_exists($id,$local_settings)) return 0;
	
	if($id=="CAT_PROD_ECOTAXINCLUDED")
	{
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && (int)SCI::getConfigurationValue('PS_USE_ECOTAX', null, 0, SCI::getSelectedShop())==0)
			$local_settings[$id]['value'] = 0;
		elseif ((version_compare(_PS_VERSION_, '1.4.0.0', '>=') && version_compare(_PS_VERSION_, '1.5.0.0', '<')) && (int)SCI::getConfigurationValue('PS_USE_ECOTAX')==0)
			$local_settings[$id]['value'] = 0;
		elseif (version_compare(_PS_VERSION_, '1.2.0.0', '<='))
			$local_settings[$id]['value'] = 0;
	}
	
	return $local_settings[$id]['value'];
}

if (isset($_GET['resetsettings']) && $_GET['resetsettings']==1)
	resetSettings();
loadSettings();

