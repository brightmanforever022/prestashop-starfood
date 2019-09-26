<?php

if (!defined('_PS_VERSION_'))
	exit;


function upgrade_module_4_2_5($object)
{	
		return ($object->registerHook('displayRevSlider'));
}