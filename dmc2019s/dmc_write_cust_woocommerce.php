<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shops												*
*  dmc_write_cust_woocommerce.php											*
*  Kunden schreiben fuer woocommerce										*
*  Copyright (C) 2015 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
*/
 
 // SPEZIALANPASSUNGEN BIGREP - Debitorennummer und Adressnummer setzen
 
	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	function dmc_write_customers() {
	
		global $dateihandle, $action;
		
		// Wenn kundenmetadaten basierend auf Vorlagekunden angelegt werden sollen, hier die ID eingeben
		$vorlage_user_id=22;
		
		// Laufzeit 
		$beginn = microtime(true); 
				
		fwrite($dateihandle, " dmc_write_customers() @  dmc_write_customers() (".date("l d of F Y h:i:s A").")"); 
			// Gepostete Werte ermitteln
		if (is_file('userfunctions/customers/dmc_get_posts.php')) include ('userfunctions/customers/dmc_get_posts.php');
		else include ('functions/customers/dmc_get_posts.php');
		
		if (is_file('userfunctions/products/dmc_art_functions.php')) include ('userfunctions/products/dmc_art_functions.php');
		else include ('functions/products/dmc_art_functions.php');
		  
		$daten = "Kunde Kundennummer =".$customers_id." schreiben:".$customers_lastname.",".$customers_firstname;
		fwrite($dateihandle, $daten."\n");
		   
		$exists = 0;
	  
		// Kunde laden
		$post_ID= dmc_get_customer_id($customers_id,$customers_email_address);	// ACHTUNG: Spezialanpassung für BIGREP in function dmc_get_customer_id($customers_id,$user)
		fwrite($dateihandle, "# customers_dob =".$customers_dob."\n" );
		// Wenn customers_dob = "loeschen", dann kunde loeschen
		if ($customers_dob == "loeschen" && strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			//require_once(ABSPATH.'wp-admin/includes/user.php' );
			require_once('../wp-admin/includes/user.php' );
			fwrite($dateihandle, "# Delete User =".$post_ID."\n" );
			if ($post_ID!='') wp_delete_user( $post_ID );
			return;
		}
		
		if ($post_ID=="") {
			$exits=false;
			// Open DB
			/* $link=dmc_db_connect();
			// ACHTUNG: Spezialanpassung, wenn nicht mit 1. email erkannt, auch Prüfung auf 
			// 2. name+strasse
			$query="SELECT um1.user_id AS customers_id" .
						   " FROM ".DB_TABLE_PREFIX. "usermeta AS um1 ".
						   " INNER JOIN ".DB_TABLE_PREFIX. "usermeta AS um2 ON um1.user_id=um2.user_id ".
						   " INNER JOIN ".DB_TABLE_PREFIX. "usermeta AS um3 ON um1.user_id=um3.user_id ".
						   " WHERE um1.meta_key='billing_first_name' AND um1.meta_value = '$customers_firstname' ".
						   " AND um2.meta_key='billing_last_name' AND um2.meta_value = '$customers_lastname'  ".
						   " AND um3.meta_key='billing_address_1' AND um3.meta_value = '$customers_street_address'  ".
						   " ORDER BY um1.user_id DESC LIMIT 1";
			fwrite($dateihandle, " email Kunde nicht existent, Pruefung auf name+strasse -> ".$query." \n" );	
			$customers_query=mysqli_query($link,$query);
			if (!($customers = mysqli_fetch_assoc($customers_query)))
				$post_ID='';
			else
				$post_ID = $customers['customers_id'];
			if ($post_ID=="") {
				// 3. strasse+plz
				$query="SELECT um1.user_id AS customers_id" .
						   " FROM ".DB_TABLE_PREFIX. "usermeta AS um1 ".
						   " INNER JOIN ".DB_TABLE_PREFIX. "usermeta AS um2 ON um1.user_id=um2.user_id ".
						   " WHERE um1.meta_key='billing_postcode' AND um1.meta_value = '$customers_postcode' ".
						   " AND um2.meta_key='billing_address_1' AND um2.meta_value = '$customers_street_address'  ".
						   " ORDER BY um1.user_id DESC LIMIT 1";
				fwrite($dateihandle, " email Kunde nicht existent, Pruefung auf strasse+plz -> ".$query." \n" );	
				$customers_query=mysqli_query($link,$query);
				if (!($customers = mysqli_fetch_assoc($customers_query)))
					$post_ID='';
				else
					$post_ID = $customers['customers_id'];
				if ($post_ID=="") {
					// 4. name+plz
					$query="SELECT um1.user_id AS customers_id" .
							   " FROM ".DB_TABLE_PREFIX. "usermeta AS um1 ".
							   " INNER JOIN ".DB_TABLE_PREFIX. "usermeta AS um2 ON um1.user_id=um2.user_id ".
							   " INNER JOIN ".DB_TABLE_PREFIX. "usermeta AS um3 ON um1.user_id=um3.user_id ".
							   " WHERE um1.meta_key='billing_first_name' AND um1.meta_value = '$customers_firstname' ".
							   " AND um2.meta_key='billing_last_name' AND um2.meta_value = '$customers_lastname'  ".
							   " AND um3.meta_key='billing_postcode' AND um3.meta_value = '$customers_postcode'  ".
							   " ORDER BY um1.user_id DESC LIMIT 1";
					fwrite($dateihandle, " email Kunde nicht existent, Pruefung auf name+plz -> ".$query." \n" );	
					$customers_query=mysqli_query($link,$query);
					if (!($customers = mysqli_fetch_assoc($customers_query)))
						$post_ID='';
					else
						$post_ID = $customers['customers_id'];
				}
			}
			
			dmc_db_disconnect($link);	*/
		} else
			$exits=true;
			
		fwrite($dateihandle, " -> Customerid/post_ID= ".$post_ID." existiert= ".$exits." \n" );	
		
		// Kunde ANLEGEN
		if ($exits==false) {
			fwrite($dateihandle, "Kunde ANLEGEN \n" );	
			
			
			// Mapping
			// Status
			if ( $Aktiv == '1' || $Aktiv == 'publish') { $post_status= 'publish'; $comment_status= 'open'; $ping_status = 'closed';} 	
			else { $post_status= 'draft'; $comment_status= 'closed'; $ping_status = 'closed';	}	// nicht online 
			// 1.Schritt Post anlegen
			// Hoechste ID ermitteln
			$user_id=dmc_get_highest_id("ID", DB_PREFIX."users")+1;
			
			// user_nicename generieren
			$user_nicename=dmc_prepare_seo_name($customers_email_address,'DE');
			$user_nicename = str_replace("@","",$user_nicename);
			$user_login=trim($customers_email_address);
			
		//	$user_nicename=$customers_id;
		//	$user_login=$customers_id;
			
			if ($customers_company!='')
				$display_name=$customers_company;
			else 
				$display_name=$customers_firstname." ".$customers_lastname;
				
			$insert_sql_data = array(	
										'ID' => $user_id,
										'user_login' => $user_login,
										'user_pass' => $customers_password,
										'user_nicename' => $user_nicename,
										'user_email' => trim($customers_email_address),			
										'user_url' => '',			
										'user_registered' => 'now()',		
										'user_activation_key' => $post_status,
										'user_status' => $customers_status,
										'display_name' => $customers_company
			);
				
			// Insert in posts durchfuehren
			$mode='INSERTED';
			dmc_sql_insert_array(DB_PREFIX."users", $insert_sql_data);
			
			// NEU 10012018 POSTMETA basierend auf einem VorlagekundenId generieren und die wichtigen Werte Updaten
			if ($vorlage_user_id != "") {
					$table = "usermeta";
					fwrite($dateihandle, "usermeta basierend auf Vorlagekunde UserID ".$vorlage_user_id." generieren\n");
					$cmd = "SELECT meta_key, meta_value FROM " . DB_PREFIX . $table." WHERE user_id = " . (int)$vorlage_user_id . "";
				 	// fwrite($dateihandle, "Query =$cmd\n");
					$metas_query = dmc_db_query($cmd);
					while ($metas_query_result = dmc_db_fetch_array($metas_query))
					{
						// Neuem Kunden metas von VorlageKunden  zuweisen
						if (strpos($metas_query_result['meta_value'], "{")===false) {
							// MIT meta_value , bei Bedarf anpassen  
							dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", '".$metas_query_result['meta_key']."', '".$metas_query_result['meta_value']."'");	
						} else {
							// MIT meta_value , bei Bedarf anpassen  
							dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", '".$metas_query_result['meta_key']."', '".$metas_query_result['meta_value']."'");	
						}
					}
					// Wesentliche Kundenwerte aktualisieren, da <> Vorlagekunde
					dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'first_name' AND user_id=".$user_id);		
					dmc_sql_update($table,  "meta_value='".trim($customers_id)."'" , "meta_key= 'nickname' AND user_id=".$user_id);		
 
					
				if ($customers_lastname!='')
						dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'last_name' AND user_id=".$user_id);
					else
						dmc_sql_update($table,  "meta_value='".trim($customers_company)."'" , "meta_key= 'last_name' AND user_id=".$user_id);
					// Rechnungsanschrift
					dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'billing_first_name' AND user_id=".$user_id);
					dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'billing_last_name' AND user_id=".$user_id);
					dmc_sql_update($table,  "meta_value='".trim($customers_company)."'" , "meta_key= 'billing_company' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_street_address)."'" , "meta_key= 'billing_address_1' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_city)."'" , "meta_key= 'billing_city' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_postcode)."'" , "meta_key= 'billing_postcode' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_countries_iso_code)."'" , "meta_key= 'billing_country' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_telephone)."'" , "meta_key= 'billing_phone' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_email_address)."'" , "meta_key= 'billing_email' AND user_id=".$user_id);	
					// Lieferanschrift
					dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'shipping_first_name' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'shipping_last_name' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_company)."'" , "meta_key= 'shipping_company' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_street_address)."'" , "meta_key= 'shipping_address_1' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_city)."'" , "meta_key= 'shipping_city' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_postcode)."'" , "meta_key= 'shipping_postcode' AND user_id=".$user_id);	
					dmc_sql_update($table,  "meta_value='".trim($customers_countries_iso_code)."'" , "meta_key= 'shipping_country' AND user_id=".$user_id);	
					
					fwrite($dateihandle, "ende add_post_meta basierend auf VorlageKunde \n");									
			} else {
				fwrite($dateihandle, "* Kunde ANLEGEN * \n" );	
				
				// 2ter Schritt - usermeta hinzufuegen select * from wp_usermeta where meta_key ='_sku' and meta_value='102808'
				$table = "usermeta";
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'first_name', '".trim($customers_firstname)."'");	
				if ($customers_lastname!='')
					dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'last_name' AND user_id=".$user_id);
				else
					dmc_sql_update($table,  "meta_value='".trim($customers_company)."'" , "meta_key= 'last_name' AND user_id=".$user_id);
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'nickname', '".trim($customers_firstname).' '.trim($customers_lastname)."'");
				// Rechnungsanschrift
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_first_name', '".trim($customers_firstname)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_last_name', '".trim($customers_lastname)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_company', '".trim($customers_company)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_address_1', '".trim($customers_street_address)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_city', '".trim($customers_city)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_postcode', '".trim($customers_postcode)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_country', '".trim($customers_countries_iso_code)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_phone', '".trim($customers_telephone)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_email', '".trim($customers_email_address)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'billing_state', 'Bayern'");	
				
				// Lieferanschrift
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_first_name', '".trim($customers_firstname)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_last_name', '".trim($customers_lastname)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_company', '".trim($customers_company)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_address_1', '".trim($customers_street_address)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_city', '".trim($customers_city)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_postcode', '".trim($customers_postcode)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_country', '".trim($customers_countries_iso_code)."'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'shipping_state', 'Bayern'");	
			
				// Details
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'description', ''");
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'rich_editing', 1");		
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'comment_shortcuts', 0");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'admin_color', 'fresh'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'use_ssl', 0");			
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'show_admin_bar_front', 1");						
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'dismissed_wp_pointers', 'wp350_media,wp360_revisions,wp360_locks,wp390_widgets'");		
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'show_welcome_panel', 0");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_dashboard_quick_press_last_post_id', 8128");		
			//	dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_user-settings', 'libraryContent=browse&wooframeworkhidebannerwooseosbmremoved=1&woosidebarsshowadvanced=1&cats=pop&hidetb=1&editor=tinymce&imgsize=thumbnail&wplink=1&advImgDetails=show&urlbutton=custom&posts_list_mode=list'");							
			//	dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_user-settings-time', '1423036884'");							
			//	dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_user-settings', 'a:2:{i:0;s:24:\"woocommerce-product-data\";i:1;s:10:\"postcustom\";}'");
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'metaboxhidden_product', 'a:1:{i:0;s:7:\"slugdiv\";}'");	
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'customer_id', ".trim($customers_id)."");			
				// Berechtigungen
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_capabilities', ".$wp_capabilities ."");		
				// Kundengruppe			
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'wp_user_level',  ".$wp_user_level ."");								
				// Kundengruppe Modul "Rolebased Prices"
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'customer_group', ".trim($customers_group)."");			
				// Modul "Private Groups" (private_group)
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'private_group', ".trim($private_group)."");			
			

				// SPEZIALANPASSUNGEN  - Debitorennummer und Adressnummer setzen
				//dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'SOL_DEBITORENNUMMER', '$debitorennummer'");			
				//dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $user_id.", 'SOL_ADRESSNUMMER', '$addressnummer'");						

			}
			
			
		} else {
			// Kunden UPDATE
			fwrite($dateihandle, "Kunde Aktualisieren \n" );	
			
			// Insert in posts durchfuehren
			$table = "users";
			// dmc_sql_update($table,  "post_title='".trim($Artikel_Bezeichnung)."', post_modified = 'now()', post_modified_gmt = 'now()'" , "post_type= 'product' AND ID=".$post_ID);	
			// Adressupdate
			$table = "usermeta";
			dmc_sql_update($table,  "meta_value='".trim($customers_id)."'" , "meta_key= 'nickname' AND user_id=".$post_ID);		
			dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'first_name' AND user_id=".$post_ID);		
			dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'last_name' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_firstname)."'" , "meta_key= 'billing_first_name' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_lastname)."'" , "meta_key= 'billing_last_name' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_company)."'" , "meta_key= 'billing_company' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_street_address)."'" , "meta_key= 'billing_address_1' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_city)."'" , "meta_key= 'billing_city' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_postcode)."'" , "meta_key= 'billing_postcode' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_countries_iso_code)."'" , "meta_key= 'billing_country' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_telephone)."'" , "meta_key= 'billing_phone' AND user_id=".$post_ID);	
			dmc_sql_update($table,  "meta_value='".trim($customers_email_address)."'" , "meta_key= 'billing_email' AND user_id=".$post_ID);	
			// SPEZIALANPASSUNGEN  - Debitorennummer und Adressnummer setzen, wenn noch nicht existent, sonst update
			/* $column='meta_key';
			$value='SOL_DEBITORENNUMMER';
			if (dmc_entry_exists(DB_TABLE_PREFIX.$table,$column,$value)) {
				dmc_sql_update($table,  "meta_value='".trim($customers_id)."'" , "meta_key= 'customer_id' AND user_id=".$post_ID);	
				dmc_sql_update($table,  "meta_value='".trim($debitorennummer)."'" , "meta_key= 'SOL_DEBITORENNUMMER' AND user_id=".$post_ID);	
				dmc_sql_update($table,  "meta_value='".trim($addressnummer)."'" , "meta_key= 'SOL_ADRESSNUMMER' AND user_id=".$post_ID);	
			} else {
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $post_ID.", 'customer_id', ".trim($customers_id)."");		
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $post_ID.", 'SOL_DEBITORENNUMMER', '$debitorennummer'");			
				dmc_sql_insert($table,  "user_id, meta_key, meta_value" , $post_ID.", 'SOL_ADRESSNUMMER', '$addressnummer'");						
			} */
		}
		
		if (DEBUGGER>=50) fwrite($dateihandle, "- Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
		// Rueckgabe
		$rueckgabe = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
		   "<STATUS>\n" .
		   "  <STATUS_INFO>\n" .
		   "    <ACTION>$action</ACTION>\n" .
		   "    <MESSAGE>OK</MESSAGE>\n" .
		   "    <MODE>$mode</MODE>\n" .
		   "    <ID>$post_ID</ID>\n" .
		   "  </STATUS_INFO>\n" .
		   "</STATUS>\n\n";
		
		echo $rueckgabe;
		fwrite($dateihandle, "dmc_write_art - rueckgabe=".$rueckgabe."\n"); 
				
		return $post_ID;	
	} // end function

	
?>
