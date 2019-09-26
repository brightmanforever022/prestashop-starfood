<?php

require_once "configuration/config.php";

$disp = (isset($_GET['disp']) ? $_GET['disp'] : '');

switch($disp){
	case "fronttip": 
		require_once (PATH_CONTROLLER."FrontTipController.php");
		break;
	case "preference":
		require_once (PATH_CONTROLLER."PreferenceController.php");
		break;
	default:
		require_once (PATH_CONTROLLER."FrontTipController.php");
}

