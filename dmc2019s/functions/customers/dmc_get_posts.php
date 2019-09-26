<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_get_posts.php														*
*  inkludiert von dmc_write_cust_woocommerce.php 										*								
*  Übergebene Variablen ermitteln										*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
15.03.2015
- neu
*/
	// Post ermitteln
	$exists = False;
	//
		$customers_id = ($_POST['customers_id']);					// Kundennummer
		if (preg_match('/@/', $customers_id)) {
			$werte = explode ( '@', $customers_id);
			$customers_id = $werte[0];
			$customers_ustidentnr = $werte[1];
			if (DEBUGGER>=1) fwrite($dateihandle, "customers_ustidentnr extrahiert ".$customers_ustidentnr);
		} else {
			$customers_ustidentnr = "";
		}
		  $customers_gender = ($_POST['customers_gender']);
			if ($customers_gender=="null" || $customers_gender == null ) $customers_gender ='';
		  $customers_firstname = sonderzeichen2html(true,$_POST['customers_firstname']);
			if ($customers_firstname=="null" || $customers_firstname == null || $customers_firstname == '' || $customers_firstname == '-') $customers_firstname ='';
		  $customers_lastname = sonderzeichen2html(true,$_POST['customers_lastname']);
			if ($customers_lastname=="null" || $customers_lastname == null || $customers_lastname == '') $customers_lastname = $_POST['customers_company'];
		   $customers_group = ($_POST['customers_dob']);
			if (preg_match('/@/', $customers_group)) {
				$werte = explode ( '@', $customers_group);
				$customers_group = $werte[0];
				$customers_ustidentnr = $werte[1];
				if (DEBUGGER>=1) fwrite($dateihandle, "customers_ustidentnr extrahiert ".$customers_ustidentnr);
			}
			// Gruppe
			$wp_user_level=0;
			// Modul "Private Groups" (private_group)
			$private_group="";
			// Berechtigungen
			$wp_capabilities = 'a:1:{s:13:"user";b:1;}'; 
			// Mapping Kundengruppe etc
			if (strpos($customers_group, 'A-') !== false) {
				$customers_group="a-partner";	// Kundengruppe Modul "Rolebased Prices"
				$private_group="*group1*";	// Hier *group1* = Vertriebspartner
				$wp_capabilities = 'a:2:{s:15:"bbp_participant";b:1;s:8:"customer";b:1;}';	// Teilnehmer und Kunde
			}
			else if (strpos($customers_group, 'B-') !== false) {
				$customers_group="b-partner"; // Kundengruppe Modul "Rolebased Prices"
				$private_group="*group1*";	// Hier *group1* = Vertriebspartner
				$wp_capabilities = 'a:2:{s:15:"bbp_participant";b:1;s:8:"customer";b:1;}';	// Teilnehmer und Kunde
			}
			else if (strpos($customers_group, 'Partner') !== false) {
				$customers_group="partner"; // Kundengruppe Modul "Rolebased Prices"
				$private_group="*group1*";	// Hier *group1* = Vertriebspartner
				$wp_capabilities = 'a:2:{s:15:"bbp_participant";b:1;s:8:"customer";b:1;}';	// Teilnehmer und Kunde
			}
			else if (strpos($customers_group, 'End') !== false) {
				$customers_group="endkunde"; // Kundengruppe Modul "Rolebased Prices"
				$private_group="*group2*";	// Hier *group2* = Kundenforum
				$wp_capabilities = 'a:2:{s:15:"bbp_participant";b:1;s:8:"customer";b:1;}';	// Teilnehmer und Kunde
			}
		  $customers_email_address = sonderzeichen2html(true,$_POST['customers_email_address']);
			if ($customers_email_address=="null" || $customers_email_address == null ) $customers_email_address ='';  
		  $customers_telephone = ($_POST['customers_telephone']);
			if ($customers_telephone=="null" || $customers_telephone == null ) $customers_telephone ='';  
		  $customers_fax = ($_POST['customers_fax']);
			if ($customers_fax=="null" || $customers_fax == null ) $customers_fax ='';  
		  $customers_status = ($_POST['customers_website_store_storeview']);							// Status ist Standard=0 bei wooCommerce
			if ($customers_status=="null" || $customers_status == null ) $customers_status ='0';  
		  $customers_company = sonderzeichen2html(true,$_POST['customers_company']);
			if ($customers_company=="null" || $customers_company == null ) $customers_company ='';  
		  $customers_street_address = sonderzeichen2html(true,$_POST['customers_street_address']);
			if ($customers_street_address=="null" || $customers_street_address == null ) $customers_street_address ='';	
		  $customers_postcode = ($_POST['customers_postcode']);
			if ($customers_postcode=="null" || $customers_postcode == null ) $customers_postcode ='';  
		  $customers_city = sonderzeichen2html(true,$_POST['customers_city']);
			if ($customers_city=="null" || $customers_city == null ) $customers_city ='';  
		  $customers_countries_iso_code = ($_POST['customers_countries_iso_code']);
			if ($customers_countries_iso_code=="null" || $customers_countries_iso_code == null ) $customers_countries_iso_code ='';  
		  $customers_password = sonderzeichen2html(true,$_POST['customers_password']);
			if ($customers_password=="null" || $customers_password == null ) $customers_password ='RCM30419';  
		
	if ($customers_password=="zufall") {
				// Zufallskennwort generieren
				//Mögliche Zeichen für den String
			   $laenge=10;
			   $zeichen = '0123456789';
			   $zeichen .= 'abcdefghijklmnopqrstuvwxyz';
			   $zeichen .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
			   $zeichen .= '()!.:=';
			 
			   //String wird generiert
			   $str = '';
			   $anz = strlen($zeichen);
			   for ($i=0; $i<$laenge; $i++) {
				  $str .= $zeichen[rand(0,$anz-1)];
			   }
			   $customers_password = $str;
			}
	
?>
	