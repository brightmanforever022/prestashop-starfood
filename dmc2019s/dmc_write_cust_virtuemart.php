<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_write_cust_virtuemart.php											*
*  Kunden schreiben fuer virtuemart										*
*  Copyright (C) 2015 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
*/
 
	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_write_customers() {
	
		global $dateihandle, $action;
				
		// Laufzeit
		$beginn = microtime(true); 
				
		fwrite($dateihandle, " dmc_write_customers() @  dmc_write_cust_virtuemart (".date("l d of F Y h:i:s A").")"); 
			// Gepostete Werte ermitteln
		if (is_file('userfunctions/customers/dmc_get_posts.php')) include ('userfunctions/customers/dmc_get_posts.php');
		else include ('functions/customers/dmc_get_posts.php');
		
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		  
		if (DEBUGGER>=1) {
			$daten = "Kunde Kundennummer = ".$customers_id." schreiben:".$customers_lastname.",".$customers_firstname. "\n";
			fwrite($dateihandle, $daten);
		}			
		   
		$exists = 0;
	  
		// Kunde laden
		$customer_shop_id = dmc_get_customer_id($customers_id,$customers_email_address);
		if ($customer_shop_id=="") 
			$exits=false; 
		else
			$exits=true;
		
		// Kunde ANLEGEN
		if ($exits==false) {
			fwrite($dateihandle, "Kunde ANLEGEN - " );	
			fwrite($dateihandle, "Step 1 - " );	
			// Mapping
			
			 // Parameter: Standard nur Deutsch
			 $parameter = '{"language":"de-DE"}';
			// 1.Schritt joomla USER anlegen (jos_users)
			$table='users';
			// Hoechste ID ermitteln
			$user_id=dmc_get_highest_id("id", DB_PREFIX.$table)+1;
			
			$insert_sql_data = array(	'id' => $user_id,
										'name' => $customers_firstname." ".$customers_lastname,
										'username' => trim($customers_email_address),
										'email' => trim($customers_email_address),
										'password' => '',	// $customers_password,
										'usertype' => '2',
										'block' => 0,	
										'sendEmail' => 0,	
										'registerDate' => 'now()',		
										'lastvisitDate' => '0000-00-00 00:00:00',		
										'activation' => '',		
										'params' => $parameter,		
										'lastResetTime' => '0000-00-00 00:00:00',		
										'resetCount' => 0
			);
			$mode='INSERTED';
			dmc_sql_insert_array(DB_TABLE_PREFIX.$table, $insert_sql_data);
			
			// 2.Schritt Virtuemart USER anlegen (jos_virtuemart_vmusers)
			fwrite($dateihandle, "Step 2 - ");	
			$table='virtuemart_vmusers';
			$insert_sql_data = array(	'virtuemart_user_id' => $user_id,
										'virtuemart_vendor_id' => 0,	
										'user_is_vendor' => 0,	
										'customer_number' => $customers_id,	
										'perms' => 'shopper',	
										'virtuemart_paymentmethod_id' => 0,	
										'virtuemart_shipmentmethod_id' => 0,	
										'agreed' => 1,	
										'created_on' => 'now()',		
										'created_by' => 0,	
										'modified_on' => 'now()',		
										'modified_by' => 0,	
										'locked_on' => '0000-00-00 00:00:00',		
										'locked_by' => 0,										
			);
			$mode='INSERTED';
			dmc_sql_insert_array(DB_TABLE_PREFIX.$table, $insert_sql_data);
		
			// 3.Schritt Virtuemart USERINFOS anlegen (jos_virtuemart_vmusers)
			fwrite($dateihandle, "Step 3 - " );	
			// Country ID ermitteln
			$table = "virtuemart_countries";
			$where = "country_2_code ='".$customers_countries_iso_code."'";
			$virtuemart_country_id = dmc_sql_select_query('virtuemart_country_id',$table,$where);				
		
			// Hoechste ID ermitteln
			$table='virtuemart_userinfos';
			$virtuemart_userinfo_id=dmc_get_highest_id("virtuemart_userinfo_id", DB_PREFIX.$table)+1;
									
			$insert_sql_data = array(	
										'virtuemart_userinfo_id' => $virtuemart_userinfo_id,
										'virtuemart_user_id' => $user_id,
										'address_type' => 'BT',								// BT - Rechnungsadresse, ST Versandadresse	
										'address_type_name' => '',	
										'name' => $customers_firstname." ".$customers_lastname,
										'company' => $customers_company,
										'title' =>  $customers_gender,
										'last_name' => $customers_lastname,
										'first_name' => $customers_firstname,
										'middle_name' => '',
										'phone_1' => $customers_telephone,
										'phone_2' => '',
										'address_1' => $customers_street_address,
										'address_2' => '',
										'city' => $customers_city,
										'virtuemart_state_id' => '560',					// Standard NRW
										'virtuemart_country_id' => $virtuemart_country_id,
										'zip' => $customers_postcode,
										'agreed' => 1,	
										'created_on' => 'now()',		
										'created_by' => 0,	
										'modified_on' => 'now()',		
										'modified_by' => 0,	
										'locked_on' => '0000-00-00 00:00:00',		
										'locked_by' => 0,
										'tax_exemption_number' => $customers_ustidentnr,		
			);
			$mode='INSERTED';
			dmc_sql_insert_array(DB_TABLE_PREFIX.$table, $insert_sql_data);
			
			// 4.Schritt Virtuemart Kundengruppen anlegen (mleko_virtuemart_vmuser_shoppergroups)
			fwrite($dateihandle, "Step 4 - " );	
			$table='virtuemart_vmuser_shoppergroups';
			$insert_sql_data = array(	
										// 'id' => $NEWid,
										'virtuemart_user_id' => $user_id,
										'virtuemart_shoppergroup_id' => $customers_status,
										
			);
			$mode='INSERTED';
			dmc_sql_insert_array(DB_TABLE_PREFIX.$table, $insert_sql_data);
			
			
			//  dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'customer_shop_id', ".trim($customer_shop_id)."");			
			fwrite($dateihandle, "done \n " );	
				
		} else {
			// Kunden UPDATE
			fwrite($dateihandle, "Kunde Aktualisieren - " );	
			fwrite($dateihandle, "Step 1 - " );	
			$table='users';
			$update_sql_data = array(	
									//	'id' => $user_id,
										'name' => $customers_firstname." ".$customers_lastname,
									//	'username' => trim($customers_email_address),
									//	'email' => trim($customers_email_address),
									//	'password' => '',	// $customers_password,
									//	'usertype' => '2',
									//	'block' => 0,	
									//	'sendEmail' => 0,	
										'registerDate' => 'now()',		
									//	'lastvisitDate' => '0000-00-00 00:00:00',		
									//	'activation' => '',		
									//	'params' => $parameter,		
									//	'lastResetTime' => '0000-00-00 00:00:00',		
									//	'resetCount' => 0
			);
			$mode='INSERTED';
			$where='id = '.$customer_shop_id;
			dmc_sql_update_array(DB_TABLE_PREFIX.$table, $update_sql_data, $where);
			
			// 2.Schritt Virtuemart USER anlegen (jos_virtuemart_vmusers)
			fwrite($dateihandle, "Step 2 - " );	
			$table='virtuemart_vmusers';
			$update_sql_data = array(	
									//	'virtuemart_user_id' => $user_id,
									//	'virtuemart_vendor_id' => 0,	
									//	'user_is_vendor' => 0,	
										'customer_number' => $customers_id,	
									//	'perms' => 'shopper',	
									//	'virtuemart_paymentmethod_id' => 0,	
									//	'virtuemart_shipmentmethod_id' => 0,	
									//	'agreed' => 1,	
									//	'created_on' => 'now()',		
									//	'created_by' => 0,	
										'modified_on' => 'now()',		
										'modified_by' => 0,	
									//	'locked_on' => '0000-00-00 00:00:00',		
									//	'locked_by' => 0,										
			);
			$mode='INSERTED';
			$where='virtuemart_user_id = '.$customer_shop_id;
			dmc_sql_update_array(DB_TABLE_PREFIX.$table, $update_sql_data, $where);
			
			// 3.Schritt Virtuemart USERINFOS anlegen (jos_virtuemart_vmusers)
			fwrite($dateihandle, "Step 3 - " );	
			// Country ID ermitteln
			$table = "virtuemart_countries";
			$where = "country_2_code ='".$customers_countries_iso_code."'";
			$virtuemart_country_id = dmc_sql_select_query('virtuemart_country_id',$table,$where);				
			
			$table='virtuemart_userinfos';
			// Hoechste ID ermitteln
			// $virtuemart_userinfo_id=dmc_get_highest_id("virtuemart_userinfo_id", DB_PREFIX.$table)+1;
								
			$update_sql_data = array(	
									//	'virtuemart_userinfo_id' => $virtuemart_userinfo_id,
									//	'virtuemart_user_id' => $customer_shop_id,
									//	'address_type' => 'BT',								// BT - Rechnungsadresse, ST Versandadresse	
									//	'address_type_name' => '',	
										'name' => $customers_firstname." ".$customers_lastname,
										'company' => $customers_company,
										'title' =>  $customers_gender,
										'last_name' => $customers_lastname,
										'first_name' => $customers_firstname,
									//	'middle_name' => '',
										'phone_1' => $customers_telephone,
									//	'phone_2' => '',
										'address_1' => $customers_street_address,
									//	'address_2' => '',
										'city' => $customers_city,
									//	'virtuemart_state_id' => '560',					// Standard NRW
										'virtuemart_country_id' => $virtuemart_country_id,
										'zip' => $customers_postcode,
									//	'agreed' => 1,	
									//	'created_on' => 'now()',		
									//	'created_by' => 0,	
										'modified_on' => 'now()',		
										'modified_by' => 0,	
									//	'locked_on' => '0000-00-00 00:00:00',		
									//	'locked_by' => 0,
										'tax_exemption_number' => $customers_ustidentnr,		
			);
			$mode='INSERTED';
			$where='virtuemart_user_id = '.$customer_shop_id ." AND address_type='BT' AND address_type_name = '' AND created_by = 0 ";
			dmc_sql_update_array(DB_TABLE_PREFIX.$table, $update_sql_data, $where);
			
			// 4.Schritt Virtuemart Kundengruppen anlegen (mleko_virtuemart_vmuser_shoppergroups)
			fwrite($dateihandle, "Step 4 - " );	
			$table='virtuemart_vmuser_shoppergroups';
			$update_sql_data = array(	
										// 'id' => $NEWid,
										// 'virtuemart_user_id' => $customer_shop_id,
										'virtuemart_shoppergroup_id' => $customers_status,
										
			);
			$mode='INSERTED';
			$where='virtuemart_user_id = '.$customer_shop_id ." ";
			dmc_sql_update_array(DB_TABLE_PREFIX.$table, $update_sql_data, $where);
			
			//dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'first_name' AND user_id=".$customer_shop_id);	
			fwrite($dateihandle, "done \n " );	
						
		}
		
		if (DEBUGGER>=50) fwrite($dateihandle, "- Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// Rueckgabe
		$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$customer_shop_id</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		echo $rueckgabe;
		fwrite($dateihandle, "dmc_write_art - rueckgabe=".$rueckgabe."\n"); 
				
		return $customer_shop_id;	
	} // end function

	
?>
