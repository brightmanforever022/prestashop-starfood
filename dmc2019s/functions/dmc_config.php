<?php
/************************************************
*                                            	*
*  dmConnector fuer diverse shops				*
*  dmc_config.php								*
*  onfigurationseinstellungen				 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

	// Konfigurationseinstellungen an JAVA	 
	function dmc_get_conf()
	{	
	 	global $dateihandle;

		$licence_key = xtc_db_prepare_input(isset($_POST['licence_key']) ?  $_POST['licence_key'] : $_GET['licence_key']);
		$mode = xtc_db_prepare_input(isset($_POST['mode']) ?  $_POST['mode'] : $_GET['mode']);
		
		fwrite($dateihandle, "dmc_get_conf Modus=$mode\n");
		// Rueckgabe XML Datei mit Conf-Einstellungen
		if ($mode == 'licence') {
			// Rueckgabe
			echo dmc_get_config_value('dmclicencecompany').'|'.dmc_get_config_value('dmclicencekey').'|'.dmc_get_config_value('dmclicencevalid');
		} // end if ($mode == 'licence') {
			
	} // end if function dmc_get_conf
	
	
?>