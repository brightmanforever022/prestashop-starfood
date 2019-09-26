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
if(version_compare(_PS_VERSION_, '1.5.6.1', '<')) {
	$grids=array(
		'grid_light' => 	  'id_cms,meta_title,link_rewrite,active',
		'grid_large' => 	  'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,position,active',
		'grid_seo' => 		  'id_cms,meta_title,meta_description,meta_keywords,link_rewrite',
	);
} else {
	$grids=array(
		'grid_light' => 	  'id_cms,meta_title,link_rewrite,active',
		'grid_large' => 	  'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,position,active,indexation',
		'grid_seo' => 		  'id_cms,meta_title,meta_description,meta_keywords,link_rewrite,indexation',
	);
}
