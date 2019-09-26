<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_write_custustomer.php												*
*  Kunden schreiben fuer Shops												*
*  Copyright (C) 2017 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
*/
 
defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
function dmc_write_customers() {
	
	global $action, $dateihandle,$client;
    // TRIM (fuer e3000)
	foreach ($_POST as $Key => $Value)
	{
		$_POST[$Key]=trim($Value);
	}
	$daten = "WriteCustomers";
	fwrite($dateihandle, "\n");
	
	$no_of_languages=dmc_count_languages();
	
	$ExportModus = ($_POST['ExportModus']);
	//$customers_id = substr($_POST['customers_id'],0,5);					// Kundennummer
	$customers_id = $_POST['customers_id'];					// Kundennummer
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
		// Anrede und Titel aus customers_gender extrahieren
		if (preg_match('/@/', $customers_gender)) {
			$werte = explode ( '@', $customers_gender);
			$customers_gender = $werte[0];
			$customers_titel = $werte[1];
		} else {
			$customers_titel = "";
		}
	  $customers_firstname = sonderzeichen2html(true,$_POST['customers_firstname']);
		if ($customers_firstname=="null" || $customers_firstname == null || $customers_firstname == '') $customers_firstname ='-';
	  $customers_lastname = sonderzeichen2html(true,$_POST['customers_lastname']);
		if ($customers_lastname=="null" || $customers_lastname == null || $customers_lastname == '') $customers_lastname ='-';
	// Vorname und Nachname aus Nachname extrahieren
		if ($customers_firstname=='-' && strpos($customers_lastname,' ')!==false) {
			$customers_firstname=substr($customers_lastname,0,strpos($customers_lastname,' '));
			$customers_lastname=substr($customers_lastname,strpos($customers_lastname,' ')+1,256);		
		}
		if ($customers_firstname=='Frau') {
			$customers_gender=="Frau";
			$customers_firstname='';
		}
		
	  $customers_dob = ($_POST['customers_dob']);
		if ($customers_dob=="null" || $customers_dob == null ) $customers_dob ='';  
	  $customers_email_address = sonderzeichen2html(true,$_POST['customers_email_address']);
		if ($customers_email_address=="null" || $customers_email_address == null ) $customers_email_address ='';  
		 $customers_email_address=str_replace(" ",'',$customers_email_address);
		 $customers_email_address=str_replace("KN-Email",'',$customers_email_address);
		 $customers_email_address=str_replace("KN-",'',$customers_email_address);
		 $customers_email_address=str_replace("kN-Email",'',$customers_email_address);
		 $customers_email_address=str_replace("kN-",'',$customers_email_address);
		 $customers_email_address=str_replace("kn-Email",'',$customers_email_address);
		 $customers_email_address=str_replace("kn-",'',$customers_email_address);
		 $customers_email_address=str_replace("keinNL-Email",'',$customers_email_address);
		 $customers_email_address=str_replace("keinNL-",'',$customers_email_address);
	 	 $customers_email_address=str_replace("keinNL",'',$customers_email_address);
	  $customers_telephone = ($_POST['customers_telephone']);
		if ($customers_telephone=="null" || $customers_telephone == null ) $customers_telephone ='';  
	  $customers_fax = ($_POST['customers_fax']);
		if ($customers_fax=="null" || $customers_fax == null ) $customers_fax ='';  
	  // Kundengruppe
	  $customers_status = sonderzeichen2html(true,($_POST['customers_website_store_storeview']));			// Kundengruppe 2 bei gambio = neuer Kunde
	  $paymentId=4;	// Shopware
	  $newsletter=0;	// Shopware
	if ($customers_status=="null" || $customers_status == null ) $customers_status ='2';  
	   //  Ue bergabe von Gruppe und Rabatt und Zahlungskonditionen
		if (preg_match('/@/', $customers_status)) {
			$werte = explode ( '@', $customers_status);
			$customers_group = $werte[0];
			if ($customers_group == 'x Privat') $customers_group = 'EK';
			else if ($customers_group == 'GH') $customers_group = 'GH';
			//else $customers_group = 'H';
				
			// Shopware Rabattsatz
			$customers_rabatt=$werte[1];
			$customers_rabatt=str_replace(".00",'',$customers_rabatt); 
			$customers_rabatt=str_replace(".50",'.5',$customers_rabatt); 
			$customers_rabatt=str_replace(".",',',$customers_rabatt);
			// Shopware Zahlungskondition
			$Zahlungskondition_kuerzel=$werte[2];
			// Mapping $paymentId
			if ($Zahlungskondition_kuerzel=='50')
				$paymentId=4;	// Rechnung
			else
				$paymentId=5;	// Vorkasse
			// Shopware Zahlungskondition
			$auswertungskennzeichen=$Zahlungskondition=$werte[3];
			if (strpos($auswertungskennzeichen,'NEWS')!==false)
				$newsletter=1;
			// Matchcode
			$Matchcode=$werte[4];
			// Matchcode
			$website=$werte[5];
			if (DEBUGGER>=1) fwrite($dateihandle, "  Kundenrabattsatz ".$customers_rabatt." - zahungskond:".$Zahlungskondition." - Matchcode= $Matchcode \n");
			
		} else {
			// Mapping Kundengruppen Shopware
			/*if ($customers_status=='4')
				$customers_group = 'B2C U';
			else if ($customers_status=='3')
				$customers_group = 'USA';
			else if ($customers_status=='2')
				$customers_group = 'H';
			else 
				$customers_group = 'EK';*/
			$customers_group = $customers_status;
			// Shopware Rabattsatz
			$customers_rabatt=0; 
			$Zahlungskondition='';
		}
		
		
		// Sonderzeichen aus Kundengruppen entfernen
		$customers_group=str_replace("*", "x",$customers_group);
		$customers_group=str_replace(".", "",$customers_group);
		
	  
	
	  $customers_company = sonderzeichen2html(true,$_POST['customers_company']);
		if ($customers_company=="null" || $customers_company == null ) $customers_company ='';  
	  $customers_street_address = sonderzeichen2html(true,$_POST['customers_street_address']);
		if ($customers_street_address=="null" || $customers_street_address == null ) $customers_street_address ='';	
		
		if (preg_match('/@/', $customers_street_address)) {
			$werte = explode ( '@', $customers_street_address);
			$customers_street_address = $werte[0];
			$additionalAddressLine1 = $werte[1];
			if (sizeof($werte)>1) {
				$additionalAddressLine2 = $werte[2];
			} else {
				$additionalAddressLine2 = "";
			}
		} else {
			$additionalAddressLine1 = "";
			$additionalAddressLine2 = "";
		}
	  $customers_postcode = ($_POST['customers_postcode']);
		if ($customers_postcode=="null" || $customers_postcode == null ) $customers_postcode ='';  
	  $customers_city = sonderzeichen2html(true,$_POST['customers_city']);
		if ($customers_city=="null" || $customers_city == null ) $customers_city ='';  
	  $customers_countries_iso_code = ($_POST['customers_countries_iso_code']);
		if ($customers_countries_iso_code=="null" || $customers_countries_iso_code == null ) $customers_countries_iso_code ='';  
	  $customers_password = sonderzeichen2html(true,$_POST['customers_password']);
	  $customers_password = substr($customers_password,0,6);
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
	 // $customers_password=md5($customers_password);
	  
		// Weitere Vorgabewerte (Veyton)
		$shop_id = STORE_ID;
		$customers_default_currency = 'EUR';
		$customers_default_language = 'de';
		$address_class='default';		// oder shipping oder payment
		
			// Weitere Vorgabewerte Presta
		$id_shop=STORE_ID;
		$id_shop_group=1;
		$id_gender	 = 0;
		$id_lang = 1;
		$active = 1;
				
				
		// Ist nicht md5 verschlüsselt, wenn mit %% und muss nachgeholt werden
	//	if (substr($customers_password,0,2)=='%%')
	//	{
		  //  $customers_password=md5($customers_password);
	//	}
	  
	  // Standardland: Deutsch $country_id=81; $zone_id=79;, fuer veyton nicht (mehr) erforderlich
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
		  $country_id=dmc_get_country_id($customers_countries_iso_code);
		  	  if (strpos(strtolower(SHOPSYSTEM), 'shopware') === false)  $zone_id=dmc_get_region_id($country_id);
		}
	
		if (DEBUGGER>=1) {
			$daten = "Kunde Kundennummer =".$customers_id." schreiben:".$customers_lastname.",".$customers_firstname;
			fwrite($dateihandle, "\n**************-". $customers_countries_iso_code."=$country_id mit Zone=$zone_id-\n");
		}			
		   
		$exists = 0;
	  
		// Anrede zuordnen
		if (strpos(SHOPSYSTEM, 'xtc') !== false || strpos(SHOPSYSTEM, 'gambiogx') !== false )  {
			if ($customers_gender=="Herr" || $customers_gender=="Herrn") $customers_gender ='m';
			if ($customers_gender=="Frau") $customers_gender ='f';
			if ($customers_gender=="Firma") $customers_gender ='m';
		}

		// Kunde laden
		$customers_shop_id =  dmc_get_customer_id($customers_id,$customers_email_address);
		
			// Wenn customers_dob = "loeschen", dann kunde loeschen
		if ($customers_dob == "loeschen" && strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			require_once('../wp-admin/includes/user.php' );
			if ($customers_shop_id!='') wp_delete_user( $customers_shop_id );
			return;
		}
		
		
		if ($customers_shop_id!='') {
			// Kunde existiert
			$exists = 1;
			if (strpos(SHOPSYSTEM, 'virtuemart') === false && strpos(SHOPSYSTEM, 'shopware') === false)  {
				// Adressen ID ermitteln (Id der ersten zugeordneten Adresse )
				$address_class='default'; // veyton kann z.B. haben $address_class, payment und shipping
				$customers_adress_shop_id = dmc_get_address_id($customers_shop_id,$address_class);
				if ($customers_adress_shop_id=='') {
					$address_class=''; // veyton kann z.B. haben $address_class, payment und shipping
					$customers_adress_shop_id = dmc_get_address_id($customers_shop_id,$address_class);
				}
				fwrite($dateihandle, "# 1915 Adresse=".$customers_adress_shop_id."\n" );
			}
		} else {
			// Kunde existiert nicht
			$exists = 0;
		}
		
		//   Ermittlung der ID einer Kundengruppe zugewiesen ist
		// Rueckgabe "", wenn keine ID vorhanden
		if (strpos(strtolower(SHOPSYSTEM), 'shopware') === false) {
						$kundengruppen_id=dmc_get_customer_group_id($customers_status);
			if ($kundengruppen_id=="") {
				// bei xtc, gambio etc ggfls Kundengruppe erst anlegen
				if (strpos(SHOPSYSTEM, 'joom') === false && strpos(SHOPSYSTEM, 'virtuemart') === false && strpos(strtolower(SHOPSYSTEM), 'shopware') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false)  {
					// Kundengruppe anlegen mit Rückgabe der ID
					fwrite($dateihandle, "Kundengruppe ".$customers_status." anlegen ... " );
					$customers_status = dmc_create_customer_group($customers_status,$no_of_languages);
					fwrite($dateihandle, "erfolgt mit ID=".$customers_status."\n" );
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') === false)  {
					$kundengruppen_id=1;
				}
			} else {
				$customers_status=$kundengruppen_id;
			}
		}
		
		if (strpos(strtolower(SHOPSYSTEM), 'shopware') === true)  {
				// Kunde nochmal laden
				$col="id";
				$table="s_user";
				$where="email='$customers_email_address'";
				$userID =  dmc_sql_select_query($col,$table,$where);
				// AdressID ermitteln
				$col="id";
				$table="s_user_billingaddress";
				$where="userID='$userID'";
				$billingID =  dmc_sql_select_query($col,$table,$where);
				// mandantenspezifische Felder ergänzen
				//if ($customers_rabatt>0) {
					//  s_user_billingaddress_attributes“ die Spalte „text4 und $Zahlungskondition in text5
					if (dmc_entry_exits($column, $table, $where)) { 
					//	$doquery = "update s_user_billingaddress_attributes SET text4='$customers_rabatt', text5='$Zahlungskondition' WHERE billingID=".$billingID;
					} else { 
					//	$doquery = "insert into s_user_billingaddress_attributes (billingID,text4,text5) VALUES ($billingID,'$customers_rabatt','$Zahlungskondition')";
					}
					dmc_db_query($doquery);
					
				//} 
			}
	
	  
  
		if ($exists==0)
		{
			// Hoechste IDs ermitteln
			if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)  {
				// Nicht bei Shopware erforderlich
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)  {
				$customers_shop_id = dmc_get_highest_id("id_customer",TABLE_CUSTOMERS);
				$customers_shop_id++;	
				$address_book_id = dmc_get_highest_id("id_address",TABLE_CUSTOMERS_ADDRESSES);
				$address_book_id++;	
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				$customers_shop_id = dmc_get_highest_id("customers_id",TABLE_CUSTOMERS);
				$customers_shop_id++;	
				$address_book_id = dmc_get_highest_id("address_book_id",TABLE_CUSTOMERS_ADDRESSES);
				$address_book_id++;	
			} else {
				$customers_shop_id = dmc_get_highest_id("customers_id",TABLE_CUSTOMERS);
				$customers_shop_id++;	
				$address_book_id = dmc_get_highest_id("address_book_id",TABLE_ADDRESS_BOOK);
				$address_book_id++;	
			}
			
			if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)  {
				// NEU ab 5.2 -> Pflichtangabe
				if ( $customers_status == "")  $customers_status = 'EK';
				else if ($customers_gender=="Herr" || $customers_gender=="Herrn") 
					$customers_gender ='mr';
				else if ($customers_gender=="Frau") 
					$customers_gender ='ms';
				else if ($customers_gender=="Firma") 
					$customers_gender ='mr';
				else
					$customers_gender ='mr';			
				
				//  Berücksichtigung ShopID und languageIso
				if ($customers_countries_iso_code=="US") {
					$shopId = 4;
					$languageIso_code = "EN";
					// ACHTUNG: ZIPCODE ist Pflichtfeld, d.h. nicht in City zu übergeben wie Chicago, IL 60611
					if ($customers_postcode=="") {
						$customers_postcode = substr($customers_city,-5);
						$customers_city = substr($customers_city,0,-5);
					}
				} else {
					$shopId = 1;
					$languageIso_code = "DE";
				}
				
				$sql_data_array = array
								(	//'id' => 1,
									'paymentId' => $paymentId,	 
									'groupKey' => $customers_group,							// Kundengruppe
									'shopId' => $shopId, 
									//'priceGroupId' => 4,						
									'password' => $customers_password,
									//'rawPassword' => $customers_password,
									//'hashPassword' => $customers_password,
									'active' => 1,
									'email' => $customers_email_address,
									'salutation' => $customers_gender,
									'firstName' => $customers_firstname,
									'lastName' =>  $customers_lastname,	
									'number' => $customers_id,									
									//'accountMode' => 0,
									'newsletter' => $newsletter,
									//'validation' => 
									//'affiliate' => 0,
									//'paymentPreset' => 0,
									'languageIso' => $languageIso_code,
									//'attribute' => 
									'billing' => Array
										(
										//	'id' => 1
										//	'customerId' => 1,
											'country' => $country_id,					// bis 5.1 countryId
											'company' => $customers_company,
											// 'department' => 
											'title' => $customers_titel,
											'salutation' => $customers_gender,
											'number' => $customers_id,
											'firstName' => $customers_firstname,
											'lastName' =>  $customers_lastname,		
											'street' => $customers_street_address,
											'additionalAddressLine1' => $additionalAddressLine1,
											'additionalAddressLine2' => $additionalAddressLine2,
											// 'streetNumber' => 10
											'zipCode' => $customers_postcode,
											'city' => $customers_city,
											'phone' => $customers_telephone,
											'fax' => $customers_fax,
											'vatId' => $customers_ustidentnr,
											//'birthday' => $customers_dob, //-0001-11-30T00:00:00+0100
											'attribute' => Array
												(
													// 'id' => 1
													// 'text1' => 
													//'text2' => $Matchcode,
													//'text3' => $website,
													//'text4' => $customers_rabatt,
												//	'rabattsatz' => $customers_rabatt,
													//'text5' => $Zahlungskondition,
													//'text6' => $Zahlungskondition_kuerzel 
													//	'customerBillingId' => 1
												) 

										),
										 'attribute' => Array
												(
													//'id' => 1
													//'text1' => 
												//	'text2' => $Matchcode,
												//	'text3' => $website,
												//	'text4' => $customers_rabatt,
													'rabattsatz' => $customers_rabatt,
													'auswertungskennzeichen'  => $auswertungskennzeichen,
												//	'text5' => $Zahlungskondition,
												//	'text6' => $Zahlungskondition_kuerzel
													//'customerBillingId' => 1
												) 
												/* 'paymentData' => array(
															array(
																"paymentMeanId"   => 2,
															//	"accountNumber" => "Account",
																"bankCode"      => "55555555",
																"bankName"      => "Bank",
																"accountHolder" => "Max Mustermann",
															),
														),
														*/
								//	'shipping' => 
								//	'debit' => 
								);
					$loggen = print_r($sql_data_array, true);
					fwrite($dateihandle, "Kundenarray: $loggen \n" );
					
					$result=$client->call('customers', ApiClient::METHODE_POST, $sql_data_array);
					$loggen = print_r($result, true);
					fwrite($dateihandle, "Kunde in Shopware eingefuegt: $loggen \n" );
					
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)  { 
				// Array füllen
				$customers_password = md5($customers_password);
				$sql_data_array_customers = array(
					'id_customer' => $customers_shop_id,
					'id_shop_group' => $id_shop_group,	 
					'id_shop' => $id_shop,	 
					'id_gender' => $id_gender,	 
					'id_default_group' => $kundengruppen_id,	 
					'id_lang' => $id_lang,	 
					'id_risk' => 0,	 
					'company' => $customers_company, 
					//'siret' => ,	 
					//'ape' => ,	 
					'firstname' => $customers_firstname,
					'lastname' => $customers_lastname ,
					'email' => $customers_email_address , 
					'passwd' => $customers_password,
					'last_passwd_gen'	=> 'now()', 
					'birthday'	 => "0000-00-00",
					'newsletter'	=> 0,
					// ip_registration_newsletter	 
					'newsletter_date_add'	 => "0000-00-00",	 
					'optin'	=> 0,	 
					// 'website'	=> 0,	 
					'outstanding_allow_amount'	=> 0,	 
					'show_public_prices'	=> 0,	 
					'max_payment_days'	=> 0,	
					// 'secure_key' => md5($customers_password),
					// note   [BB]	 
					'active' => $active,	 
					'is_guest' => 0,	 
					'deleted' => 0,	 
					'date_add'	=> 'now()',	 
					'date_upd'	=> 'now()',
					 );
			  
				$sql_data_array_customers_info = array(
					'customers_info_id' => $customers_shop_id,
					'customers_info_date_account_created' => 'now()');
																								
				$sql_data_array_adress_table = array(
					'id_address' => $address_book_id,
					'id_customer' => $customers_shop_id,
					'id_country' => $country_id,
					'id_state' => 0,
					'id_manufacturer' => 0,
					'id_supplier' => 0,
					'alias' => '',
					'company' => $customers_company,	
					'firstname' => $customers_firstname,
					'lastname' => $customers_lastname,		
					'address1' => $customers_street_address,
					'address2' => '',
					'postcode' => $customers_postcode,
					'city' => $customers_city,
					// 'other' => '';
					'phone' => $customers_telephone,
					// 'phone_mobile' => '',
					// 'fax' => $customers_fax,
					'vat_number' => $customers_ustidentnr,
					//'dni' => '',
					'date_add' => 'now()',
					'date_upd' => 'now()',
					'active' => $active,
					'deleted' => 0
					);
				
				$sql_data_array_customer_group = array(
					'id_group' => $kundengruppen_id,
					'id_customer' => $customers_shop_id
				);
				
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				// Array füllen
				$sql_data_array_customers = array(
					'customers_id' => $customers_shop_id,
					'customers_cid' => $customers_id,
					'customers_vat_id' => $customers_ustidentnr,	
					'customers_status' => $customers_status,		// Kundengruppe
					'customers_email_address' => $customers_email_address,
					'customers_password' => $customers_password,
					//	'customers_default_address_id' => $address_book_id, -> gibt es bei veton nicht mehr
					'shop_id'	=>	$shop_id,
					'customers_default_currency' => $customers_default_currency,
					'customers_default_language' => $customers_default_language
					);
				 
				$sql_data_array_adress_table = array(
					'address_book_id' => $address_book_id,
					// 'external_id' = $wawKundenNur,
					'customers_id' => $customers_shop_id,
					'customers_gender' => $customers_gender,
					'customers_dob' => $customers_dob,
					'customers_phone' => $customers_telephone,
					'customers_fax' => $customers_fax,
					'customers_company' => $customers_company,	
					// 'customers_company_2' => $customers_company,	
					// 'customers_company_3' => $customers_company,	
					'customers_firstname' => $customers_firstname,
					'customers_lastname' => $customers_lastname,		
					'customers_street_address' => $customers_street_address,
					// 'customers_suburb' => '',
					'customers_postcode' => $customers_postcode,
					'customers_city' => $customers_city,
					'customers_country_code' => $customers_countries_iso_code,
					'address_class' => $address_class,
					'date_added' => 'now()',
					'last_modified' => 'now()');
			} else {
				// Array füllen
				$sql_data_array_customers = array(
					'customers_id' => $customers_shop_id,
					'customers_cid' => $customers_id,
					'customers_vat_id' => $customers_ustidentnr,
					'customers_status' => $customers_status,
					'customers_gender' => $customers_gender,
					'customers_firstname' => $customers_firstname,
					'customers_lastname' => $customers_lastname,		
					'customers_dob' => $customers_dob,
					'customers_email_address' => $customers_email_address,
					'customers_default_address_id' => $address_book_id,
					'customers_telephone' => $customers_telephone,
					'customers_fax' => $customers_fax,
					'customers_password' => $customers_password,
					'customers_date_added' => 'now()',
					'customers_last_modified' => 'now()');
			  
				$sql_data_array_customers_info = array(
					'customers_info_id' => $customers_shop_id,
					'customers_info_date_account_created' => 'now()',
					'customers_info_date_account_last_modified' => 'now()');
			  
				$sql_data_array_adress_table = array(
					'address_book_id' => $address_book_id,
					'customers_id' => $customers_shop_id,
					'entry_company' => $customers_company,	
					'entry_firstname' => $customers_firstname,
					'entry_lastname' => $customers_lastname,		
					'entry_street_address' => $customers_street_address,
					'entry_postcode' => $customers_postcode,
					'entry_city' => $customers_city,
					'entry_country_id' => $country_id,
					'entry_zone_id' => $zone_id,       
					'address_date_added' => 'now()',
					'address_last_modified' => 'now()');
			}
		}
		else // Existiert -> Update
		{
			fwrite($dateihandle, "Kunden Update \n" );

			if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)  {
				
				// Shopware Array mit direkter Ausführung
				// Update auf ID !!!
				if ( $customers_status == "")  $customers_status = 'EK';
				else if ($customers_gender=="Herr" || $customers_gender=="Herrn") 
					$customers_gender ='mr';
				else if ($customers_gender=="Frau") 
					$customers_gender ='ms';
				else if ($customers_gender=="Firma") 
					$customers_gender ='mr';
				else
					$customers_gender ='mr';
			//	fwrite($dateihandle, "Shopware Kunden ID ".$customers_shop_id." ($customers_id) wird mit customers_password ".$customers_password." aktualisiert \n" );
				fwrite($dateihandle, "Shopware Kunden ID ".$customers_shop_id." ($customers_id) wird mit number ".$customers_id." aktualisiert \n" );
					//  Berücksichtigung ShopID und languageIso
				if ($customers_countries_iso_code=="US") {
					$shopId = 4;
					$languageIso_code = "EN";
					// ACHTUNG: ZIPCODE ist Pflichtfeld, d.h. nicht in City zu übergeben wie Chicago, IL 60611
					if ($customers_postcode=="") {
						$customers_postcode = substr($customers_city,-5);
						$customers_city = substr($customers_city,0,-5);
					}
				} else {
					$shopId = 1;
					$languageIso_code = "DE";
				}
				$sql_update_data_array = array
								(	//'id' => 1,
									'paymentId' => $paymentId,
									'groupKey' => $customers_group,							// Kundengruppe
									'shopId' => $shopId ,
									//'priceGroupId' => 
									//'hashPassword' => md5($customers_password),
									'number' => $customers_id,
									'salutation' => $customers_gender,
									'title' => $customers_titel,
									'firstName' => $customers_firstname,
									'lastName' =>  $customers_lastname,		
									'password' => $customers_password,
									//'rawPassword' => $customers_password,
									'active' => 1,
								//	'email' => $customers_email_address,
									//'accountMode' => 0,
									'newsletter' => $newsletter,
									//'validation' => 
									//'affiliate' => 0,
									//'paymentPreset' => 0,
									'languageIso' => $languageIso_code,
									//'attribute' => 
									'billing' => Array
										(
										//	'id' => 1
										//	'customerId' => 1,
											'countryId' => $country_id,
											'company' => $customers_company,
											// 'department' => 
											'salutation' => $customers_gender,
											'number' => $customers_id,
											'firstName' => $customers_firstname,
											'lastName' =>  $customers_lastname,		
											'street' => $customers_street_address,
											'additionalAddressLine1' => $additionalAddressLine1,
											'additionalAddressLine2' => $additionalAddressLine2,
											// 'streetNumber' => 10
											'zipCode' => $customers_postcode,
											'city' => $customers_city,
											'phone' => $customers_telephone,
											'fax' => $customers_fax,
											'vatId' => $customers_ustidentnr,
											//'birthday' => $customers_dob, //-0001-11-30T00:00:00+0100
											'attribute' => Array
												(
													//'id' => 1
													//'text1' => 
												//	'text2' => $Matchcode,
												//	'text3' => $website,
												//	'text4' => $customers_rabatt,
												//	'text5' => $Zahlungskondition,
												//	'text6' => $Zahlungskondition_kuerzel
													//'customerBillingId' => 1
												) 

										 ),
										 'attribute' => Array
												(
													//'id' => 1
													//'text1' => 
												//	'text2' => $Matchcode,
												//	'text3' => $website,
												//	'text4' => $customers_rabatt,
													'rabattsatz' => $customers_rabatt,
													'auswertungskennzeichen'  => $auswertungskennzeichen,
												//	'text5' => $Zahlungskondition,
												//	'text6' => $Zahlungskondition_kuerzel
													//'customerBillingId' => 1
												) 
								
								//	'shipping' => 
								//	'debit' => 
								);
					$result=$client->call('customers/'.$customers_shop_id, ApiClient::METHODE_PUT, $sql_update_data_array);
					$loggen = print_r($result, true);
					fwrite($dateihandle, "Kunde aktualisiert: $loggen \n" );
					
						/*		text2: Kunden Kurzbezeichnung
text4: Kundenrabattsatz
text3: Webseite
text5: KHKZahlungskonditionen.Bezeichnung
text6: Zahlungskondition
								$customers_rabatt=$werte[1];
			$customers_rabatt=str_replace(".00",'',$customers_rabatt); // 
			$customers_rabatt=str_replace(".50",'.5',$customers_rabatt); // 
			$customers_rabatt=str_replace(".",',',$customers_rabatt); // 
			// Shopware Zahlungskondition
			$Zahlungskondition_kuerzel=$werte[2];
			// Shopware Zahlungskondition
			$Zahlungskondition=$werte[3];
			// Matchcode
			$Matchcode=$werte[4];
			// Matchcode
			$website=$werte[5];
			*/
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)  {
				// Array füllen
				$sql_data_array_customers = array(
					//'id_customer' => $customers_shop_id,
					'id_shop_group' => $id_shop_group,	 
					//'id_shop' => $id_shop,	 
					//'id_gender' => $id_gender,	 
					//'id_default_group' => $kundengruppen_id,	 
					//'id_lang' => $id_lang,	 
					//'id_risk' => 0,	 
					'company' => $customers_company, 
					//'siret' => ,	 
					//'ape' => ,	 
					'firstname' => $customers_firstname,
					'lastname' => $customers_lastname ,
					'email' => $customers_email_address , 
					//'passwd' => $customers_password,
					//'last_passwd_gen'	=> 'now()', 
					//'birthday'	 => "0000-00-00",
					//'newsletter'	=> 0,
					// ip_registration_newsletter	 
					//'newsletter_date_add'	 => "0000-00-00",	 
					//'optin'	=> 0,	 
					// 'website'	=> 0,	 
					//'outstanding_allow_amount'	=> 0,	 
					//'show_public_prices'	=> 0,	 
					//'max_payment_days'	=> 0,	
					// 'secure_key' => md5($customers_password),
					// note   [BB]	 
					'active' => $active,	 
					//'is_guest' => 0,	 
					//'deleted' => 0,	 
					//'date_add'	=> 'now()',	 
					'date_upd'	=> 'now()',
				);
			  
				$sql_data_array_adress_table = array(
					//'id_address' => $address_book_id,
					//'id_customer' => $customers_shop_id,
					'id_country' => $country_id,
					//'id_state' => 0,
					//'id_manufacturer' => 0,
					//'id_supplier' => 0,
					//'alias' => '',
					'company' => $customers_company,	
					'firstname' => $customers_firstname,
					'lastname' => $customers_lastname,		
					'address1' => $customers_street_address,
					'address2' => '',
					'postcode' => $customers_postcode,
					'city' => $customers_city,
					// 'other' => '';
					'phone' => $customers_telephone,
					// 'phone_mobile' => '',
					// 'fax' => $customers_fax,
					'vat_number' => $customers_ustidentnr,
					//'dni' => '',
					//'date_add' => 'now()',
					'date_upd' => 'now()',
					'active' => $active,
					//'deleted' => 0
					);
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
				// Array füllen
				$sql_data_array_customers = array(
					'customers_cid' => $customers_id,
					'customers_vat_id' => $customers_ustidentnr,	
					'customers_status' => $customers_status,		// Kundengruppe
					//'customers_email_address' => $customers_email_address,
					// 'customers_password' => $customers_password,
					'shop_id'	=>	$shop_id,
					'customers_default_currency' => $customers_default_currency,
					'customers_default_language' => $customers_default_language
					);
				 
				$sql_data_array_adress_table = array(
					'customers_id' => $customers_shop_id,
					'customers_gender' => $customers_gender,
					'customers_dob' => $customers_dob,
					'customers_phone' => $customers_telephone,
					'customers_fax' => $customers_fax,
					'customers_company' => $customers_company,	
					// 'customers_company_2' => $customers_company,	
					// 'customers_company_3' => $customers_company,	
					'customers_firstname' => $customers_firstname,
					'customers_lastname' => $customers_lastname,		
					'customers_street_address' => $customers_street_address,
					// 'customers_suburb' => '',
					'customers_postcode' => $customers_postcode,
					'customers_city' => $customers_city,
					'customers_country_code' => $customers_countries_iso_code,
					'address_class' => $address_class,
					'last_modified' => 'now()');
			} else {
				// Array füllen
				$sql_data_array_customers = array(
					//'customers_cid' => $customers_id,
					//'customers_vat_id' => $customers_ustidentnr,
					'customers_status' => $customers_status,
				//	'customers_gender' => $customers_gender,
				//	'customers_firstname' => $customers_firstname,
					'customers_lastname' => $customers_lastname,		
				//	'customers_dob' => $customers_dob,
			//		'customers_telephone' => $customers_telephone,
				//	'customers_fax' => $customers_fax,
				//	'customers_last_modified' => 'now()'
				);
			  
				$sql_data_array_adress_table = array(
					'customers_id' => $customers_shop_id,
					'entry_company' => $customers_company,	
					'entry_firstname' => $customers_firstname,
					'entry_lastname' => $customers_lastname,		
					'entry_street_address' => $customers_street_address,
					'entry_postcode' => $customers_postcode,
					'entry_city' => $customers_city,
					'entry_country_id' => $country_id,
					'entry_zone_id' => $zone_id,    
					'address_last_modified' => 'now()');
			} // end if shopsystem
		} // end if existiert
     
		if (strpos(strtolower(SHOPSYSTEM), 'shopware') === false)
			if ($exists==0) // Neuanlage (ID wird zurueckgegeben !!!)
			{
				$mode='INSERTED';
				// customers tabelle
				dmc_sql_insert_array(TABLE_CUSTOMERS, $sql_data_array_customers);
				$customers_new_id = dmc_db_get_new_id();
				// customers_info tabelle (nicht bei veyton existent)
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					// ADDRESS_BOOK Tabelle
					dmc_sql_insert_array(TABLE_CUSTOMERS_ADDRESSES, $sql_data_array_adress_table);
					$customers_new_address_id = dmc_db_get_new_id();
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					// ADDRESS_BOOK Tabelle
					dmc_sql_insert_array(TABLE_CUSTOMERS_ADDRESSES, $sql_data_array_adress_table);
					$customers_new_address_id = dmc_db_get_new_id();
					// ADDRESS_BOOK Tabelle
					dmc_sql_insert_array(TABLE_CUSTOMERS_GROUP, $sql_data_array_customer_group);
				} else {
					dmc_sql_insert_array(TABLE_CUSTOMERS_INFO, $sql_data_array_customers_info);
					$customers_new_id = dmc_db_get_new_id();
					// ADDRESS_BOOK Tabelle
					dmc_sql_insert_array(TABLE_ADDRESS_BOOK, $sql_data_array_adress_table);
					$customers_new_address_id = dmc_db_get_new_id();
				}
				
				if (DEBUGGER>=1) 
					fwrite($dateihandle, "Kunde mit neuer ShopID (".$customers_new_id.") erstellt.\n");
			  
			}
			elseif ($exists==1) //Update
			{
				// fwrite($dateihandle, "3640 Update ".$sql_data_array_customers['customers_lastname']." - "."customers_id = '$customers_shop_id' 0"."\n" );
				// print_array($sql_data_array_customers);
				$mode='UPDATED';
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					// customers tabelle (NICHT AUF Admin mit Status 0
					dmc_sql_update_array(TABLE_CUSTOMERS, $sql_data_array_customers, "customers_id = '$customers_shop_id'");
					// ADDRESS_BOOK Tabelle
					dmc_sql_update_array(TABLE_CUSTOMERS_ADDRESSES, $sql_data_array_adress_table, "address_book_id = '$customers_adress_shop_id' "); 
				} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
					fwrite($dateihandle, "2570 Update Kundentabelle \n");
					dmc_sql_update_array(TABLE_CUSTOMERS, $sql_data_array_customers, "id_customer = '$customers_shop_id' ");
					fwrite($dateihandle, "2572 Update Addresstabelle \n");
					// ADDRESS_BOOK Tabelle
					dmc_sql_update_array(TABLE_ADDRESS_BOOK, $sql_data_array_adress_table, "id_address = '$address_book_id' ");
				} else {
					// customers tabelle (NICHT AUF Admin mit Status 0 AND status<>0
					//fwrite($dateihandle, "3651");
					dmc_sql_update_array(TABLE_CUSTOMERS, $sql_data_array_customers, "customers_id = '$customers_shop_id' ");
					//fwrite($dateihandle, "3653");
					// ADDRESS_BOOK Tabelle
					dmc_sql_update_array(TABLE_ADDRESS_BOOK, $sql_data_array_adress_table, "address_book_id = '$customers_adress_shop_id' ");
					//fwrite($dateihandle, "3656");			
				}
				if (DEBUGGER>=1) 
					fwrite($dateihandle, "Kunde ".$customers_shop_id." Kundennummer =".$customers_id." Update für Adress-ID".$customers_adress_shop_id." durchgefuehrt.\n");		
			}

		/*	if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)  {
				// Kunde nochmal laden
				$col="id";
				$table="s_user";
				$where="email='$customers_email_address'";
				$userID =  dmc_sql_select_query($col,$table,$where);
				// AdressID ermitteln
				$col="id";
				$table="s_user_billingaddress";
				$where="userID='$userID'";
				$billingID =  dmc_sql_select_query($col,$table,$where);
				// mandantenspezifische Felder ergänzen
				//if ($customers_rabatt>0) {
					//  s_user_billingaddress_attributes“ die Spalte „text4 und $Zahlungskondition in text5
					if (dmc_entry_exits($column, $table, $where)) { 
					//	$doquery = "update s_user_billingaddress_attributes SET text4='$customers_rabatt', text5='$Zahlungskondition' WHERE billingID=".$billingID;
					} else { 
					//	$doquery = "insert into s_user_billingaddress_attributes (billingID,text4,text5) VALUES ($billingID,'$customers_rabatt','$Zahlungskondition')";
					}
					dmc_db_query($doquery);
					
				//} 
			}
			*/
			
	echo 	'<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>write_customer</ACTION>\n" .
		   "    <CODE>0</CODE>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <CUSTOMER_ID>$customers_id</CUSTOMER_ID>\n" .
		   "    <STATUS>$Status</STATUS>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		   
	} // end function

	
?>
