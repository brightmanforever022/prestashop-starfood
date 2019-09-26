<?php
if (!defined('_PS_VERSION_'))
	exit;

function upgrade_module_4_6_3($object)
{
	$sql ="CREATE TABLE IF NOT EXISTS " ._DB_PREFIX_.GlobalsRevSlider::TABLE_STATIC_SLIDES_NAME." (
								  id int(9) NOT NULL AUTO_INCREMENT,
								  slider_id int(9) NOT NULL,
								  params text NOT NULL,
								  layers text NOT NULL,
								  PRIMARY KEY (id)
								)";
	Db::getInstance()->execute($sql);
	$object->registerHook('displayBackOfficeHeader');
	$object->uploadControllerRegistration();
	return true;
}
