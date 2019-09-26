<?php
#ini_set("display_errors", 1);
#error_reporting(E_ERROR & ~E_NOTICE & ~E_DEPRECATED);

defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );
/********************************************************************************
*                                                                               *
*  dmConnector  for magento shop												*
*  dmc_db_functions.php															*
*  ehemals dmc_functions.php													*
*  Allgemeine Funktionen														*
*  Copyright (C) 2008-16 DoubleM-GmbH.de										*
*                                                                               *
********************************************************************************/

// Version 2011 - ersetzt dm_db_functions.php
// 30.04.2011 - dmc_get_order_comment -> Bestellommentar ermitteln
// 28.05.2011 - CheckOrders()
// 03.06.2011 - dmc_main_price_not_0 -> Variantenhauptprodukt bekomment Variantenpreis, wenn Preis = 0
// 12.07.2011 - dmc_get_category_id($Kategorie_ID) -> Numerisch oder alphanumerische ids
// 21.07.2011 - dmc_get_weight -> gewicht eines (hauot)artikels ermitteln
// 28.07.2011 - dmc_get_customer_id
// 28.07.2011 - dmc_get_address_id
// 21.09.2011 - dmc_get_shipping_costs
// 21.09.2011 - dmc_get_shipping_vat
// 21.09.2011 - dmc_get_payment_costs
// 21.09.2011 - dmc_get_payment_vat
// 11.10.2011 - dmc_entry_exists
// 14.10.2011 - dmc_get_config_value
// 15.03.2012 - dmc_cat_desc_exists
// 19.03.2012 - dmc_get_shop_config_value -> Wert aus der Configuration Tabelle des shops ermitteln
// 20.03.2012 - dmc_count_languages
// 21.06.2012 - dmc_sql_...
// 21.06.2012 - dmc_sql_select_query($col,$table,$where) - Abfrage eines Wertes einer Spalte
// 10.08.2012 - CheckOrders() Unterstuetzung mehrere Order Status mit ORDER_STATUS_GET durch @ getrennter Order Status
// 19.07.2013 - dmc_sql_insert_array - Array in Datenbank einfuegen
// 24.07.2013 - dmc_delete_first() - löschen aller Artikel
// 24.07.2013 - dmc_delete_variants_first() - löschen aller Varianten Artikel
// 24.07.2013 - dmc_deactivte_first() - deaktivieren aller Artikel
// 24.07.2013 - dmc_deactivate_variants_first() - Funktion um alle Varianten Produkte zu deaktivieren
// 02.08.2013 - dmc_delete_categories_first() - Funktion um alle Kategorien zu löschen
// 16.10.2013 - dmc_deactivate_master_without_slave - Veyton Funktion um Variant-Haupt-Produkte ohne Varianten zu deaktivieren
// 24.03.2014 - dmc_set_top_categories - Alle Hauptkategorien als Veyton TOP Kategorien setzen 
// 05.02.2015 - dmc_sql_update_array - Werte aus Array in Datenbank aktualisieren
// 24.02.2015 - dmc_entry_exits -  Ueberpruefe ob Datensatz vorhanden - Check if entry exits
// 16.03.2016 - dmc_del_cat_without_products - Kategorien ohne Artikel löschen
// 24.08.2016 - dmc_get_customer_group_id - Ermittlung der ID einer Kundengruppe zugewiesen ist
// 24.08.2016 - dmc_create_customer_group - Kundengruppe anlegen mit Rückgabe der ID
// 23.10.2017 - dmc_woo_get_assigned_image_ids - Ids aller zugeordneten Bilder wooCommerce ermitteln
// 23.10.2017 - dmc_woo_get_image_id- wooCommerce ID Hauptbild ermitteln
// 23.10.2017 - dmc_woo_get_image_name_by_id - wooCommerce BildName nach ID  ermitteln
// 23.10.2017 - dmc_woo_get_image_ids_by_name - wooCommerce BilderIDs nach Bildname
// 23.10.2017 - dmc_woo_get_gallery_image_ids wooCommerce IDs Zusatzbilder ermitteln
// 23.10.2017 - dmc_get_magnalister__info - Information Magnalister für OrderID 
// 23.10.2017 - dmc_get_gm_order_factoring_information - Information Gambio PayPal Factoring für OrderID 
// 23.10.2017 - dmc_get_gm_order_finanzierung_information - Information Gambio PayPal Finanzierung für OrderID 
		

// Anzahl Fremdsprachen
	function dmc_count_languages() {
		global  $dateihandle,$link;

		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$query = "SELECT id_lang,name,active,iso_code FROM " . DB_TABLE_PREFIX. "lang";
		} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
			// NUR BEI MEHRSPRACHENMODUL $query = "SELECT * FROM s_core_multilanguage";
			return 1;
		} else if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
			// NUR BEI MEHRSPRACHENMODUL $query = "SELECT * FROM s_core_multilanguage";
			return 1;
		} else {
			$query = "SELECT languages_id, name, code FROM " . TABLE_LANGUAGES;
		}
	
		$link=dmc_db_connect();
		//	$dateiname=LOG_FILE;	
		//$dateihandle = fopen($dateiname,"a");
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_count_languages-SQL= ".$query." = ");
			$no_of_languages=0;
		$sql_query = mysqli_query($link,$query);				
		while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
			// Anzahl der Sprachen ermitteln
			$no_of_languages++;	
		}		
	
		if (DEBUGGER==99)  fwrite($dateihandle, "$no_of_languages.\n");
		
		// dmc_db_disconnect($link);	
		
		return $no_of_languages;	
	} // end function dmc_count_languages

	// Wert eines Shop Konfigurationseintrages zurueckgeben (XTC etc)
	function dmc_get_shop_config_value($attribute)
	{
		global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_shop_config_value for $attribute\n"); 
	
		$query=mysqli_query($link,"SELECT configuration_value" .
							" FROM configuration " .
							" WHERE configuration_key='$attribute' LIMIT 1");				   
		
		if (!($ergebnis = mysqli_fetch_assoc($query)))
				$value='';
		else if ($ergebnis['value']!=null && $ergebnis['value']!='')
			$value = $ergebnis['value'];
		else
			$value='';
			
		return $value;
	} // end 	function dmc_get_shop_config_value
	
// Wert eines Konfigurationseintrages zurueckgeben
	function dmc_get_config_value($attribute)
	{
		// Get Veyton payment-VAT
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_config_value for $attribute\n"); 
	
		$query=mysqli_query($link,"SELECT value" .
							" FROM dmc_config " .
							" WHERE attribute='$attribute' LIMIT 1");				   
		
		if (!($ergebnis = mysqli_fetch_assoc($query)))
				$value='';
		else if ($ergebnis['value']!=null && $ergebnis['value']!='')
			$value = $ergebnis['value'];
		else
			$value='';
			
		return $value;
	} // end 	function dmc_get_config_value
	
	function dmc_get_payment_costs($payment_method,$orders_id)
	{
		// Get Veyton payment-costs
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_payment_costs .. "); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$sqlquery = "SELECT IFNULL(sum(orders_total_price),0) AS ergebnis" .
						   " FROM " . TABLE_ORDERS_TOTAL .
						   " WHERE orders_total_key = 'payment'".
						   " AND orders_id='$orders_id' ";
		
			if (DEBUGGER>=1) fwrite($dateihandle, $sqlquery ); 				   
			$query=mysqli_query($link, $sqlquery );				   
			
			if (!($ergebnis = mysqli_fetch_assoc($query)))
				$costs=0;
			else if ($ergebnis['ergebnis']>null && $ergebnis['ergebnis']!='')
				$costs = $ergebnis['ergebnis'];
			else
				$costs=0;
		}	
		if (DEBUGGER>=1) fwrite($dateihandle, " -> ".$costs."\n" ); 		
		return $costs;
	} // end 	function dmc_get_payment_costs
	
	function dmc_get_orders_total($total_type,$is_net,$orders_id)
	{
		// Get Veyton  orders_total
		// $total_type -> subtotal, total
		// $is_net=true net else gros 
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_orders_total: $total_type\n"); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			// Get products subtotal 
			if ($is_net) {
				// subtotal net
				$query=mysqli_query($link,"SELECT sum(products_price*(1+(CASE WHEN products_discount IS null THEN 0 ELSE products_discount END)/100)) AS total" .
							   " FROM " . TABLE_ORDERS_PRODUCTS .
							   " WHERE orders_id='$orders_id'");				   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$products_subtotal=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$products_subtotal = $ergebnis['total'];
				else
					$products_subtotal=0;
			} else {  // if (!$is_net) {
				// subtotal gros
				$query=mysqli_query($link,"SELECT sum(products_price*(1+(CASE WHEN products_discount IS null THEN 0 ELSE products_discount END)/100)*(1+(CASE WHEN products_tax IS null THEN 0 ELSE products_tax END)/100)) AS total" .
							   " FROM " . TABLE_ORDERS_PRODUCTS .
							   " WHERE orders_id='$orders_id'");	
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$products_subtotal=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$products_subtotal = $ergebnis['total'];
				else
					$products_subtotal=0;
			}	// end if 
			// Get surcharge 
			if ($is_net) {
				// surcharge net
				$query=mysqli_query($link,"SELECT sum(orders_total_price) AS total" .
							   " FROM " . TABLE_ORDERS_TOTAL .
							   " WHERE orders_id='$orders_id' AND orders_total_price>0");				   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$surcharge=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$surcharge = $ergebnis['total'];
				else
					$surcharge=0;
			} else {  // if (!$is_net) {
				// surcharge gros
				$query=mysqli_query($link,"SELECT sum(orders_total_price*(1+(CASE WHEN orders_total_tax IS null THEN 0 ELSE orders_total_tax END)/100)) AS total" .
							   " FROM " . TABLE_ORDERS_TOTAL .
							   " WHERE orders_id='$orders_id' AND orders_total_price>0");			   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$surcharge=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$surcharge = $ergebnis['total'];
				else
					$surcharge=0;
			}	// end if 
			// Get discount 
			if ($is_net) {
				// discount net
				$query=mysqli_query($link,"SELECT sum(orders_total_price) AS total" .
							   " FROM " . TABLE_ORDERS_TOTAL .
							   " WHERE orders_id='$orders_id' AND orders_total_price<0");				   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$discount=0; 
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$discount = $ergebnis['total'];
				else
					$discount=0;
			} else {  // if (!$is_net) {
				// discount gros
				$query=mysqli_query($link,"SELECT sum(orders_total_price*(1+orders_total_tax/100)) AS total" .
							   " FROM " . TABLE_ORDERS_TOTAL .
							   " WHERE orders_id='$orders_id' AND orders_total_price<0");			   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$discount=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$discount = $ergebnis['total'];
				else
					$discount=0;
			}	// end if 
			// products discount
			if ($is_net) {
				// products discount net
				$query=mysqli_query($link,"SELECT sum(products_price*(1+(CASE WHEN products_discount IS null THEN 0 ELSE products_discount END)/100))-sum(products_price) AS total" .
							   " FROM " . TABLE_ORDERS_PRODUCTS .
							   " WHERE orders_id='$orders_id'");				   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$products_discount=0; 
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$products_discount = $ergebnis['total'];
				else
					$products_discount=0;
			} else {  // if (!$is_net) {
				// products discount gros
				$query=mysqli_query($link,"SELECT sum(products_price*(1+(CASE WHEN products_discount IS null THEN 0 ELSE products_discount END)/100)*(1+(CASE WHEN products_tax IS null THEN 0 ELSE products_tax END)/100))-sum(products_price) AS total" .
							   " FROM " . TABLE_ORDERS_PRODUCTS .
							   " WHERE orders_id='$orders_id'");				   
				if (!($ergebnis = mysqli_fetch_assoc($query)))
					$products_discount=0;
				else if ($ergebnis['total']>null && $ergebnis['total']!='')
					$products_discount = $ergebnis['total'];
				else
					$products_discount=0;
			}	// end if 
			
		}	// end if  (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false
		
		if ($total_type == "subtotal")
			$gesamt=$gesamt=$products_subtotal;
		else if ($total_type == "surcharge")
			$gesamt=$surcharge;
		else if ($total_type == "discount")
			$gesamt=$discount+$products_discount;
		else if ($total_type == "discount_total")
			$gesamt=$products_subtotal-$products_discount+$surcharge-$discount;
		else if ($total_type == "total")
			$gesamt=$products_subtotal+$surcharge; //+$discount+$products_discount;
		else
			$gesamt=0;
				 fwrite($dateihandle, "156 for total_type ==$total_type  $gesamt => $products_subtotal-$products_discount+$surcharge-$discount \n"); 
		
		return $gesamt;
	} // end 	function dmc_get_orders_total
	
	function dmc_get_payment_vat($payment_method,$orders_id)
	{
		// Get Veyton payment-VAT
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_payment_vat\n"); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query=mysqli_query($link,"SELECT  orders_total_tax AS vat" .
						   " FROM " . TABLE_ORDERS_TOTAL .
						   " WHERE orders_total_key = 'payment'".
						   " AND orders_id='$orders_id' LIMIT 1");				   
			
			if (!($ergebnis = mysqli_fetch_assoc($query)))
				$vat=0;
			else if ($ergebnis['vat']>null && $ergebnis['vat']!='')
				$vat = $ergebnis['vat'];
			else
				$vat=0;
		}	
		return $vat;
	} // end 	function dmc_get_payment_vat
	
		
	function dmc_get_shipping_costs($shipping_method,$orders_id)
	{
		// Get Veyton Shipping-costs
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_shipping_costs\n"); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query=mysqli_query($link,"SELECT sum(orders_total_price) AS costs" .
						   " FROM " . TABLE_ORDERS_TOTAL .
						   " WHERE orders_total_key = 'shipping'".
						   " AND orders_id='$orders_id'");				   
			
			if (!($ergebnis = mysqli_fetch_assoc($query)))
				$costs=0;
			else if ($ergebnis['costs']>null && $ergebnis['costs']!='')
				$costs = $ergebnis['costs'];
			else
				$costs=0;
		}	
		return $costs;
	} // end 	function dmc_get_shipping_costs
	
	function dmc_get_shipping_vat($shipping_method,$orders_id)
	{
		// Get Veyton Shipping-VAT
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_shipping_vat\n"); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query=mysqli_query($link,"SELECT  orders_total_tax AS vat" .
						   " FROM " . TABLE_ORDERS_TOTAL .
						   " WHERE orders_total_key = 'shipping'".
						   " AND orders_id='$orders_id' LIMIT 1");				   
			
			if (!($ergebnis = mysqli_fetch_assoc($query)))
				$vat=0;
			else if ($ergebnis['vat']>null && $ergebnis['vat']!='')
				$vat = $ergebnis['vat'];
			else
				$vat=0;
		}	
		return $vat;
	} // end 	function dmc_get_shipping_vat
	
	function dmc_get_customer_id($customers_id,$user)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function ... dmc_get_customer_id ... "); 
		$link=dmc_db_connect();
		
		if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false)
			$customers_query=mysqli_query($link,"SELECT customers_id" .
						   " FROM " . TABLE_CUSTOMERS .
						   " WHERE customers_email_address = '$user'".
						   " OR customers_cid='$customers_id'");
		else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$customers_query=mysqli_query($link,"SELECT id_customer AS customers_id" .
						   " FROM " . TABLE_CUSTOMERS .
						   " WHERE email = '$user'");
		else if (strpos(strtolower(SHOPSYSTEM), 'virtu') !== false) {
			$query="SELECT id AS customers_id" . 
						   " FROM " . DB_TABLE_PREFIX. "users" .
						   " WHERE email = '$user'";
			$customers_query=mysqli_query($link,$query);
		} else if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			$query="SELECT ID AS customers_id" .
						   " FROM " . DB_TABLE_PREFIX. "users" .
						   " WHERE user_email = '$user'";
			$customers_query=mysqli_query($link,$query);
		} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$customers_query=mysqli_query($link,"SELECT customers_id" .
						   " FROM " . TABLE_CUSTOMERS .
						   " WHERE customers_email_address = '$user'".
						   " OR customers_cid='$customers_id'");				   
		} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
			$query="SELECT id AS customers_id" .
						   " FROM s_user" .
						   " WHERE email = '$user'";
			//ALTERNATIV - Erkennung bestehender Kunden basierend auf Debitorennummer, statt auf eMail Adresse
			/* $query="SELECT userID AS customers_id" .
						   " FROM s_user_billingaddress" .
						   " WHERE customernumber = '$customers_id'"; */
			$customers_query=mysqli_query($link,$query);
		} else {
			$query="SELECT customers_id" .
						   " FROM " . TABLE_CUSTOMERS .
						   " WHERE customers_email_address = '$user'".
						   " OR customers_cid='$customers_id'";
			$customers_query=mysqli_query($link,$query);
		}
		if (!($customers = mysqli_fetch_assoc($customers_query)))
			$customers_id='';
		else
			$customers_id = $customers['customers_id'];
	
		if (DEBUGGER>=1) fwrite($dateihandle, "query=$query mit Ergebnis=$customers_id\n"); 

		return $customers_id;
	} // end 	function dmc_get_customer_id
	
	// Id der ersten zugeordneten Adresse ermitteln
	function dmc_get_address_id($customers_id, $address_class)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function dmc_get_address_id "); 
		$link=dmc_db_connect();
	
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$customers_query=mysqli_query($link,"SELECT min(id_address) AS ergebnis " .
						   " FROM " . TABLE_CUSTOMERS_ADDRESSES .
						   " WHERE id_customer='$customers_id' AND active = 1 AND	deleted = 0");
		else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) 
			$customers_query=mysqli_query($link,"SELECT min(address_book_id) AS ergebnis FROM " . TABLE_CUSTOMERS_ADDRESSES .
										" WHERE customers_id='$customers_id'");				   
		else 
			if ($address_class <> '')
				$customers_query=mysqli_query($link,"SELECT min(address_book_id) AS ergebnis  FROM " . TABLE_ADDRESS_BOOK .
										" WHERE customers_id='$customers_id' AND address_class='$address_class'");
			else
				$customers_query=mysqli_query($link,"SELECT max(address_book_id) AS ergebnis  FROM " . TABLE_ADDRESS_BOOK .
										" WHERE customers_id='$customers_id' ");
		
		if (DEBUGGER>=1) fwrite($dateihandle, "-> "."SELECT max(address_book_id) AS ergebnis  FROM " . TABLE_ADDRESS_BOOK .
										" WHERE customers_id='$customers_id' AND address_class='$address_class'");
		
		if (!($customers = mysqli_fetch_assoc($customers_query)))
			$customers_adress_id='';
		else
			$customers_adress_id = $customers['ergebnis'];

		if (DEBUGGER>=1) fwrite($dateihandle, "-> ".$customers_adress_id."\n");
		
		return $customers_adress_id;
	} // end 	function dmc_get_address_id
	
	// Variantenhauptprodukt bekomment Variantenpreis, wenn Preis = 0
	function dmc_main_price_not_0($artid, $price) {
		global $dateihandle, $link; 
		fwrite($dateihandle, "dmc_main_price_not_null of $artid \n");
	
		// Preis für Artikel ID ermitteln
		if (dmc_get_price($artid) == 0) {
			// Preis Hauptprodukt auf Preis der Variante aendern
			$update_sql_data = array(
		          'products_price' => $price);
			
			// Bei Gambio GX msste ferner noch der Preisstatus auf normal geaendert werden
			if (SHOPSYSTEM == 'gambiogx')$update_sql_data['gm_price_status'] = 0;
			
		    xtc_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', "products_id='" . $artid . "'");
			fwrite($dateihandle, "neuer preis Hauptprodukt =".$price."\n");
		}
		
		return;	
	} // end function


	function CheckLogin($user,$password)
	{
		global $dateihandle, $action, $version_major, $version_minor;
		
		if (DEBUGGER>=1) fwrite($dateihandle, "function CheckLogin SHOPSYSTEM=".SHOPSYSTEM." \n"); 
				
			//	$password='rcm2802';
		$ok=True;
		$sql_query="";
		$link=dmc_db_connect();
		$password_pur=$password;
		
		// Bei Shopware erfolgt die Überprüfung vorab durch API Zugangskontrolle.
		if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)
			return $ok;			
		
		// Ist nicht md5 verschlüsselt, wenn mit %% und muss nachgeholt werden
		if (substr($password,0,2)=='%%')
		{
				$password_pur = substr($password,2,40);
				$password = md5(base64_decode(substr($password,2,40)));
		} else {
				$password=md5($password);
		}
		// Wenn kein Username dann Abbruch 
		if ($user=='')
		{
				$ok=False;
		}

	
		if (defined('DMCONNECTOR_LOGIN_NAME')) {
			// Pruefung auf Daten auf defintions.inc.php
			if ($user==DMCONNECTOR_LOGIN_NAME && ($password_pur==DMCONNECTOR_PASSWORT || base64_decode($password_pur)==DMCONNECTOR_PASSWORT )) 
				$ok=true;
			else {
				$ok=false;
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER 489: *$user*!=*".DMCONNECTOR_LOGIN_NAME."* $password_pur/ *".base64_decode($password_pur)."*!=*".DMCONNECTOR_PASSWORT."* !\n");
			}
		} else if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false 
			|| strpos(strtolower(SHOPSYSTEM), 'osc') !== false
			|| strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			// Bei woocommerce wird auf Daten in der configure_shop_woocommerce.php geprueft
			if ($user==LOGIN_USER && ($$password_pur==LOGIN_PASSWORD || base64_decode($password_pur)==LOGIN_PASSWORD )) 
				$ok=true;
			else 
				$ok=false;
			
		} else {
			// Pruefung auf Daten auf Datenbank etc
			if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false) {
					$sql_query="select admin_id" .
								   " FROM " . DB_TABLE_PREFIX . "admin " .
								   " WHERE (admin_name = '$user' OR admin_email = '$user') AND (admin_pass = '$password' OR admin_pass = '$password_pur'  )";
			/*} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				if ($password_pur=='dmc_0312')
					$sql_query="select id_employee,passwd" .
								   " from " . TABLE_USERS . 
								   " where email = '$user' and active=1 ";
				else
					$sql_query="select id_employee,passwd" .
								   " from " . TABLE_USERS . 
								   " where email = '$user' and active=1 AND (passwd = '$password')";
			*/
			} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
					$sql_query="select email AS id ,user_password" .
								   " from " . TABLE_ADMIN_ACL_AREA_USER .
								   " where (handle = '$user' OR email='$user') and status=1 AND (user_password = '$password' OR user_password = '$password_pur' )";					   
			} else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false || strpos(strtolower(SHOPSYSTEM), 'joomshopping') !== false) {
					$sql_query="SELECT id " .
								   "FROM " . DB_TABLE_PREFIX . "users ".
								   "WHERE (username = '$user' OR email='$user') ".
								  // "AND (usertype LIKE '%admin%' OR usertype = '') ".		-> Nur bestimmte Versionen, nicht mehr ab 3.x
								   "AND (password = '$password' OR password = '$password_pur') AND block=0";
					//if ($password_pur=='dmc_0312') $sql_query="SELECT id FROM " . DB_TABLE_PREFIX . "users LIMIT 1";
			} else { 
				$sql_query="select customers_id,customers_status,customers_password" .
								   " from " . TABLE_CUSTOMERS .
								   " where customers_email_address = '$user' AND customers_status='0' AND (customers_password = '$password' 
								   OR customers_password = '$password_pur') ";
			}
		
			// if (DEBUGGER>=1) fwrite($dateihandle, "491 = $sql_query \n");
			$result=mysqli_query($link,$sql_query);
			//if (DEBUGGER>=1) fwrite($dateihandle, "470 \n");
			if (!($customers = mysqli_fetch_assoc($result)))
			{			
				// fwrite($dateihandle, "473 \n");
				$ok=False;
			}
			else
			{
				// fwrite($dateihandle, "478 \n");
				if (DEBUGGER>=1) fwrite($dateihandle, "LOGIN erfolgreich: ID=".$customers['id']." \n");
		
				// check if customer password is okay
				// if ($customers['customers_status']!='0' && strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'zencart') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false)
				//{
				//	$ok=False;
				//}
			}
				//echo "function CheckLogin $sql_query\n"; 
			//	if (DEBUGGER>=1) fwrite($dateihandle, "486 \n");
			// Abfangroutine fuer Presta bei Problemen mit Verschluesselung fuer Standardpasswort
			
			// dmc_db_disconnect($link);
		}
		// if ( $password_pur=='rcmrcm2802' || base64_encode($password)=='rcm2800='  || base64_encode($password)=='rcm2802') $ok=true;
			
		if (!$ok) 
		{
			// Nicht als XML ausgeben, da Textausgabe direkt als Fehler gesehen wird, während ein <Status> auch für die
			// Versionsnummer ausgewertet wird
			if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER 562: Anmeldung: Name/Passwort $user, $password / $password_pur / base64_decode($password_pur)= ".base64_decode($password_pur)."  nicht korrekt! $sql_query \n");
			if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
				// Bei woocommerce wird auf Daten in der configure_shop_woocommerce.php geprueft
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER 565: Bei woocommerce wird auf LOGIN Daten in der configure_shop_woocommerce.php geprueft!\n");
				echo "Login fehlgeschlagen -> pruefe die Datei configure_shop_woocommerce.php und die LOGs.";
			} else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
				// Bei woocommerce wird auf Daten in der configure_shop_woocommerce.php geprueft
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!\n");
				echo "Login fehlgeschlagen -> pruefe die Datei configure_shop_presta.php und die LOGs.";
			} else { 
				if (DEBUGGER>=1) fwrite($dateihandle, "FEHLER 572: geben Sie ggfls die Benutzerdaten aus der lokalen Benutzeroberflaeche in der conf/definitions.inc.php an !\n");
				echo "Login fehlgeschlagen";
			}
		} 

		return $ok;
	} // end 	function CheckLogin($user,$password)

	function CheckOrders() {
			global $dateihandle, $action, $version_major, $version_minor;
			if (DEBUGGER>=1) fwrite($dateihandle, "CheckOrders "); 
			// Neue Infos
				/* zB    <order_status>pending</order_status>
						<change_status>0</change_status>
						<order_date>05.03.2015-31.12.2015</order_date>
						<order_number>1</order_number>
						&OrderDatum=01.04.2015-31.12.2015&OrderNummer=1&InformCustomer=0&ChangeStatus=0&OrderStatus=pending&user=r@paradies.de&password=********
				*/
				/* order_status */
				if (isset($_POST['order_status']))
					$order_status = $_POST['order_status'];
				else if (isset($_GET['order_status']))
					$order_status = $_GET['order_status'];
				else
					$order_status = "";
				/* order_number */
				if (isset($_POST['order_number']))
					$order_number = $_POST['order_number'];
				else if (isset($_GET['order_number']))
					$order_number = $_GET['order_number'];
				else
					$order_number = 1;
				/* order_number */
				if (isset($_POST['order_date']))
					$order_date = $_POST['order_date']; 
				else if (isset($_GET['order_date']))
					$order_date = $_GET['order_date'];
				else
					$order_date = "";
				
				// Preufe auf Datum und von - bis 
				if ($order_date!="") {
					if (strpos($order_date,'@')!==false) {
						// Von bis durch - getrennt
						$vonbis = explode ( '@', $order_date);
						$order_from = $vonbis[0];
						$order_to = $vonbis[1].' 23:59:59';
					}
				}
				
				if (strpos(strtolower($order_status),"rechnung")!==false || strpos(strtolower($order_status),"datev")!==false) {
					if (DEBUGGER>=1) fwrite($dateihandle, "orders_export Rechnungen zu Datev");
					$order_status=""; // siehe unten ORDER_STATUS_GET
					$export_type="rechnungen";
					$notify_customer = "";
					$change_status = false;
				}
			
			// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
			if ($order_status!="1" && $order_status!="")
				$Bestellstatus = explode ( '@', $order_status);
			else
				$Bestellstatus = explode ( '@', ORDER_STATUS_GET);
			
			if (DEBUGGER>=1) fwrite($dateihandle, "check_orders $order_from - $order_to - $order_status start with order $order_number \n");
			
			// Open DB
			$link=dmc_db_connect();
		
			// Rückgabe der Zahl der Bestellungen
			if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
							
				// NUR aktuellster Eintrag aus history
				$query = 	"SELECT count(*) AS total FROM " . TABLE_ORDERS ." o".
							" WHERE (o.current_state  = '".$Bestellstatus[0]."'";
							// zusaetzliche status?
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$query .= " OR o.current_state  = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							} // end if
				$query .= " ) ";
							
				if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
					$query .= " AND o.id_order >= '" . FIRST_ORDER_ID . "'";
					$query .= " AND o.id_order >= '" . $order_number . "'";
					
				// Mehrere durch , getrennt
				if (defined('STORE_ID_EXPORT') && (STORE_ID_EXPORT <> '') && is_numeric(STORE_ID_EXPORT) )
					$query .= " AND o.id_shop IN (" . STORE_ID_EXPORT . ")";
				
				if ($order_from!="") $query .= " AND o.date_add>='".$order_from."'";
				if ($order_to!="") $query .= " AND o.date_add<='".$order_to."'";	
			
				/*$query = 	"SELECT count(*) AS total FROM " . TABLE_ORDERS ." o".
							" INNER JOIN ".TABLE_ORDERS_STATUS_HISTORY." oh ON (o.id_order = oh.id_order)" .
							" WHERE (oh.id_order_state  = '".$Bestellstatus[0]."'";
							// zusaetzliche status?
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$query .= " OR oh.id_order_state  = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							} // end if
							// " AND (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY ." WHERE id_order_state = oh.id_order_state)=o.id_order";
							$query .= ") AND (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY ." WHERE id_order_state = oh.id_order_state AND id_order=oh.id_order) =".
							" (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY ." WHERE id_order=oh.id_order)";
							
						//	" ORDER BY oh.id_order_history DESC LIMIT 1"; 
					*/
			}  else if (strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
				$query = "SELECT count(*) AS total FROM " . TABLE_ORDERS .
							" AS o WHERE o.order_status  = '".$Bestellstatus[0]."'";
				// zusaetzliche status?
				if ( count($Bestellstatus) > 1) {
					for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
						$query .= " OR o.order_status  = '".$Bestellstatus[$Anzahl]."'";
					} // end for
				} // end if
				if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID != '') && is_numeric(FIRST_ORDER_ID) )
								$query .=	" AND o.virtuemart_order_id >=".FIRST_ORDER_ID;
							
				if ($order_from!="") $query .= " AND o.created_on>='".$order_from."'";
				if ($order_to!="") $query .= " AND o.created_on<='".$order_to."'";	
				
			}  else if (strpos(strtolower(SHOPSYSTEM), 'joomshopping') !== false) {
				$query = "SELECT count(*) AS total FROM ".DB_TABLE_PREFIX ."jshopping_orders AS o ".
							"INNER JOIN ".DB_TABLE_PREFIX ."jshopping_order_status AS status ON status.status_id=o.order_status ";
				$query .= " WHERE (status.status_code = '".$Bestellstatus[0]."' OR status.`name_de-DE` = '".$Bestellstatus[0]."' OR status.status_id = '".$Bestellstatus[0]."'";
				if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$query .= " OR status.status_code = '".$Bestellstatus[$Anzahl]."' OR status.`name_de-DE` = '".$Bestellstatus[$Anzahl]."' OR status.status_id = '".$Bestellstatus[$Anzahl]."'";
								} // end for
				}
				$query .= ") ";
				if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID != '') && is_numeric(FIRST_ORDER_ID) )
								$query .=	" AND o.order_number >=".FIRST_ORDER_ID;
							
				if ($order_from!="") $query .= " AND o.order_date>='".$order_from."'";
				if ($order_to!="") $query .= " AND o.order_date<='".$order_to."'";	
				
			}  else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
				$query = "SELECT count(*) AS total FROM " .DB_TABLE_PREFIX ."s_order AS o INNER JOIN ".DB_TABLE_PREFIX ."s_core_states AS status ON status.id=o.status ".
							" WHERE (status.description = '".$Bestellstatus[0]."'";
				// zusaetzliche status?
				if ( count($Bestellstatus) > 1) {
					for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
						$query .= " OR status.description  = '".$Bestellstatus[$Anzahl]."'";
					} // end for
				} // end if				
				
				$query .= ") ";
				
				if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) && FIRST_ORDER_ID>=1 )
						$query .=	" AND o.ordernumber >=".FIRST_ORDER_ID;// AND 
				if ($order_number != '')
						$query .=	" AND o.ordernumber >=".$order_number;// AND 
				if ($order_from!="") $query .= " AND o.ordertime>='".$order_from."'";
				if ($order_to!="") $query .= " AND o.ordertime<='".$order_to."'";	
				// NUR in Rechnung gestellte Bestellungen
				if ($export_type=="rechnungen")	$query .=  " AND o.id IN (SELECT orderID FROM s_order_documents where type=1)";
							
				
			}  else if (strpos(strtolower(SHOPSYSTEM), 'woocommerce') !== false) {
				if (SHOP_VERSION=='2') 	{	// abweichende Logik bei bestimmten Systemen
					// Order Status woocommerce select tt.term_taxonomy_id, t.name FROM wp_term_taxonomy AS tt INNER JOIN wp_terms AS t ON  tt.term_id=t.term_id AND tt.taxonomy ='shop_order_status'
					$query = "SELECT count(*) AS total FROM `" . DB_TABLE_PREFIX . "posts` AS o ".
							"WHERE  o.post_type = 'shop_order' ".
							"AND (o.post_status = '".$Bestellstatus[0]."' ";						
				
					if ( count($Bestellstatus) > 1) {
						for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
							$query .= " OR o.post_status = '".$Bestellstatus[$Anzahl]."'";
						} // end for
					}
					$query .= ") ";
									
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) && FIRST_ORDER_ID>=1 )
						$query .=	" AND o.id >=".FIRST_ORDER_ID;// AND 
						$query .=	" AND o.id >=".$order_number;// AND 
						if ($order_from!="") $query .= " AND o.post_date>='".$order_from."'";
						if ($order_to!="") $query .= " AND o.post_date<='".$order_to."'";													
					/*	$query = "SELECT count(*) AS total FROM " . TABLE_ORDERS .
								" WHERE order_status  = '".$Bestellstatus[0]."'";
					// zusaetzliche status?
					if ( count($Bestellstatus) > 1) {
						for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
							$query .= " OR order_status  = '".$Bestellstatus[$Anzahl]."'";
						} // end for
					} // end if */
				} else {
					// Standard
					// Order Status woocommerce select tt.term_taxonomy_id, t.name FROM wp_term_taxonomy AS tt INNER JOIN wp_terms AS t ON  tt.term_id=t.term_id AND tt.taxonomy ='shop_order_status'
					$query = "SELECT count(*) AS total FROM `" . DB_TABLE_PREFIX . "posts` AS o ".
							"INNER JOIN " . DB_TABLE_PREFIX . "term_relationships AS tr ON o.id=tr.object_id ".
							"INNER JOIN " . DB_TABLE_PREFIX . "term_taxonomy AS tt ON tr.term_taxonomy_id=tt.term_taxonomy_id ".
							"INNER JOIN " . DB_TABLE_PREFIX . "terms AS t ON tt.term_id=t.term_id ".
							"WHERE o.post_status='publish' AND o.post_type = 'shop_order' ".
							"AND (t.name = '".$Bestellstatus[0]."' ";				

				
					if ( count($Bestellstatus) > 1) {
						for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
							$query .= " OR t.name = '".$Bestellstatus[$Anzahl]."'";
						} // end for
					}
					$query .= ") ";
									
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) && FIRST_ORDER_ID>=1 )
						$query .=	" AND o.id >=".FIRST_ORDER_ID;// AND 
						$query .=	" AND o.id >=".$order_number;// AND 
						if ($order_from!="") $query .= " AND o.post_date>='".$order_from."'";
						if ($order_to!="") $query .= " AND o.post_date<='".$order_to."'";													
					/*	$query = "SELECT count(*) AS total FROM " . TABLE_ORDERS .
								" WHERE order_status  = '".$Bestellstatus[0]."'";
					// zusaetzliche status?
					if ( count($Bestellstatus) > 1) {
						for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
							$query .= " OR order_status  = '".$Bestellstatus[$Anzahl]."'";
						} // end for
					} // end if */
				}
			} else {
				// Standard fuer alle anderen
				$query = "SELECT count(*) AS total FROM " . TABLE_ORDERS .
							" WHERE (orders_status  = '".$Bestellstatus[0]."'";
				// zusaetzliche status?
				if ( count($Bestellstatus) > 1) {
					for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
						$query .= " OR orders_status  = '".$Bestellstatus[$Anzahl]."'";
					} // end for
				} // end if
				
				$query .= ")";
				
				if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
					$query .= " AND orders_id >= '" . FIRST_ORDER_ID . "'";
					$query .= " AND orders_id >= '" . $order_number . "'";
				
				// Multishop veyton
				if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && defined('ORDER_SHOP_IDS') && (ORDER_SHOP_IDS <> '') ) {
					$shop_id = explode ( '@', ORDER_SHOP_IDS);
					$add_sql = " AND (";
					for($Anzahl = 0; $Anzahl < count($shop_id); $Anzahl++) {     // ShopIDs durchlaufen	 
						if ($Anzahl ==0) $add_sql .= "shop_id=".$shop_id[$Anzahl];
						else $add_sql .= " OR shop_id=".$shop_id[$Anzahl];
					}
					$query = $query . $add_sql .")";
				}
				
				if ($order_from!="") $query .= " AND date_purchased >= '".$order_from."'";
				if ($order_to!="") $query .= " AND date_purchased <= '".$order_to."'";					
			}
	 
			if (DEBUGGER>=1) fwrite($dateihandle, " CheckOrders sql= $query "); 
			$sql_query = mysqli_query($link, $query);					
			$TEMP_ID = mysqli_fetch_assoc($sql_query);				
			if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
				$no_of_orders = 0;
			else
				$no_of_orders = $TEMP_ID['total'];
			
			if (DEBUGGER>=1) fwrite($dateihandle, " -> $no_of_orders\n"); 
			// dmc_db_disconnect($link);	
				
			return $no_of_orders;	
	} // end function
	
	function dmc_sql_update($table, $what, $where) {
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
		
		$query	= "UPDATE ".DB_TABLE_PREFIX."".$table;	
		$query	.= " SET ".$what;	
		$query	.= " WHERE ".$where;			
	
		$doquery = $query; // if no array
		// foreach($query AS $doquery)
		//{
		// mysqli_query($link,"SET NAMES 'utf8'", $link);
			if (DEBUGGER==99) fwrite($dateihandle, "dmc_sql_update-SQL= ".$doquery." .\n");
			$sql_query = mysqli_query($link,$doquery);
		//} // end foreach
		
		// close db
		// dmc_db_disconnect($link);		
	} // end function
	
	function dmc_sql_insert($table, $columns, $values) {
	
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
	
		$query	= "INSERT INTO ".DB_TABLE_PREFIX."".$table;	
		$query	.= " (".$columns.") ";	
		$query	.= " values (".$values.")";			
	
		$doquery = $query; // if no array
		if (DEBUGGER==99) fwrite($dateihandle, "dmc_sql_insert-SQL= ".$doquery." .\n");
			// mysqli_query($link,"SET NAMES 'utf8'", $link);
			// mysqli_query($link,"SET CHARACTER SET 'utf8'", $link);
			//mysql_real_escape_string($doquery, $link);
			if (mysqli_query($link,$doquery) && DEBUGGER==99) fwrite($dateihandle, "eingetragen\n");
			else fwrite($dateihandle, "Fehler: NICHT eingetragen: ". mysqli_error($link) . "\n");
		
		// close db
		// dmc_db_disconnect($link);		
	} // end function dmc_sql_insert
	
	function dmc_sql_insert_array($table, $array) { 
	
		global  $dateihandle,$link;
		fwrite($dateihandle, "dmc_sql_insert_array \n");
		// Open DB
		$link=dmc_db_connect();
	
		//$query	= "INSERT INTO ".DB_TABLE_PREFIX."".$table;	
		$query	= "INSERT INTO ".$table;	
	
	    // Spalten ermitteln
	    $query	.= " (";	
		while (list($columns, ) = each($array)) {
          $query .= $columns . ', ';
        }
        $query = substr($query, 0, -2) . ") values (";
        reset($array);
        // Werte ermitteln
		while (list(, $value) = each($array)) {
			if (is_numeric($value))
				$query .= "'". $value . "', ";
			else if ($value=="now()") 
				$query .= "now(), ";
			else if ($value=="null") 
				$query .= "null, ";
			else
			  $query .= "'". $value . "', ";
	//		fwrite($dateihandle, "´-> ".$query." .\n");
        } // end while
		
		$query = substr($query, 0, -2) . ");";
        if (DEBUGGER==99)	fwrite($dateihandle, "dmc_sql_insert_array-SQL= ".$query." .\n");
		// mysqli_query($link,"SET NAMES 'utf8'", $link);
			// mysqli_query($link,"SET CHARACTER SET 'utf8'", $link);
			//mysql_real_escape_string($doquery, $link);
		if (mysqli_query($link,$query) && DEBUGGER==99) fwrite($dateihandle, "eingetragen\n");
		else fwrite($dateihandle, "Fehler: NICHT eingetragen: ". mysqli_error($link) . "\n");
		
		// mysqli_query($link, $query);
		// fwrite($dateihandle, "New Record has id %d.\n", mysqli_insert_id($link));
		// close db
		// dmc_db_disconnect($link);	
		return mysqli_insert_id($link);
	} // end function dmc_sql_insert_array
	
	// - Werte aus Array in Datenbank aktualisieren
	function dmc_sql_update_array($table, $array, $where) { 
	
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
	
		//$query	= "UPDATE ".DB_TABLE_PREFIX."".$table;	
		 $query	= "UPDATE ".$table;	
		 // Spalten und Werte ermitteln
	    $query	.= " SET ";	
		reset($array);
        // Werte ermitteln
		while (list($column, $value) = each($array)) {
			if (is_numeric($value))
				$query .= $column." = '". $value . "', ";
			else if ($value=="now()") 
				$query .= $column.' = now(), ';
			else if ($value=="null") 
				$query .= $column.' = null, ';
			else
			  $query .= $column." ='". $value . "', ";
	//		fwrite($dateihandle, "´-> ".$query." .\n");
        } // end while
		
		$query = substr($query, 0, -2) . ' where ' . $where;
		
        if (DEBUGGER==99)	fwrite($dateihandle, "dmc_sql_update_array-SQL= ".$query." ...");
		// mysqli_query($link,"SET NAMES 'utf8'", $link);
		// mysqli_query($link,"SET CHARACTER SET 'utf8'", $link);
		//mysql_real_escape_string($doquery, $link);
		if (mysqli_query($link,$query) && DEBUGGER==99) fwrite($dateihandle, " aktualisiert\n");
		else fwrite($dateihandle, "Fehler: NICHT eingetragen: ". mysqli_error($link) . "\n");
		
		// close db
		// dmc_db_disconnect($link);		
	} // end function dmc_sql_update_array
	
	function dmc_sql_delete($table, $where) {
	
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
	
		$query	= "DELETE FROM ".$table;	
		$query	.= " WHERE ".$where." ";	
		
		$doquery = $query; // if no array
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_sql_delete-SQL= ".$doquery." ");
			if (mysqli_query($link,$doquery) && DEBUGGER==99) fwrite($dateihandle, "gelöscht\n");
			else fwrite($dateihandle, "Fehler: " . mysqli_error($link) . "\n");
		
		// close db
		// dmc_db_disconnect($link);		
	} // end function dmc_sql_delete
	
	// Abfrage eines SQL Querys mit Rueckgabe result Array
	function dmc_sql_query($query) {
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
	
		$doquery = $query; // if no array
		// if (DEBUGGER==99) 
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_sql_query-SQL= ".$doquery." ");
			if (@$result = mysqli_query($link,$doquery) && DEBUGGER==99) fwrite($dateihandle, " ausgefuehrt.\n");
			else fwrite($dateihandle, "Fehler: ". mysqli_error($link) . "\n");
		
		// close db
		// dmc_db_disconnect($link);
		return $result;
	} // end function dmc_sql_query
	
	// Verwarbeitung result array
	function dmc_db_fetch_array(&$db_query) {
		global  $dateihandle,$link;
		return mysqli_fetch_assoc($db_query);
	}
	
	// Absetzen eines SQL Querys
	function dmc_db_query($doquery ) {
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
	//	if (DEBUGGER==99)  
			fwrite($dateihandle, "\ndmc_db_query-SQL= ".$doquery." ");
		$result = mysqli_query($link,$doquery);					
		return $result;	
	} // end function dmc_db_query
	
	// Abfrage eines Wertes einer Spalte
	function dmc_sql_select_query($col,$table,$where) {
	
		global  $dateihandle,$link;
		// Open DB
		$link=dmc_db_connect();
		$table	= DB_TABLE_PREFIX."".$table;	
	
		$doquery = "SELECT $col AS rueckgabe FROM $table WHERE $where LIMIT 1"; 				// no array > limit 1
		if (DEBUGGER==99)  fwrite($dateihandle, "# dmc_sql_select_query-SQL= ".$doquery." ");
		
		$sql_query = mysqli_query($link,$doquery);					
		$result = mysqli_fetch_assoc($sql_query);				
			if ($result['rueckgabe']=='' || $result['rueckgabe']=='null')
				$rueckgabe = '';
			else
				$rueckgabe = $result['rueckgabe'];
		
		if (DEBUGGER==99)  fwrite($dateihandle, " -> rueckgabe= ".$rueckgabe." \n");
		
		// close db
		// dmc_db_disconnect($link);	
		
		return $rueckgabe;	
		
	} // end function dmc_sql_select_query
	
	
	/* ENDE HEUTE */
	
	function dmc_db_disconnect($link) {
		mysqli_close($link);		
	} // end function
	
	function dmc_db_connect() {
		
		global  $dateihandle, $link;
	//	if ($link) {
	//		fwrite($dateihandle, "dmc_db_connect - Verbindung zu Datenbank ".DB_DATABASE." WAR BEREITS hergestellt.\n");
	//	} else {
			// Fehlerabfangroutine can't connect to local MySQL server through socket '/tmp' (111)
			if (DB_SERVER=='localhost')
				$DB_SERVER='localhost';	// $DB_SERVER='127.0.0.1';
			else
				$DB_SERVER=DB_SERVER;
		
			// mysqli_connect(host,username,password,dbname,port,socket);
			if (defined('DB_PORT') && DB_PORT!="")
				$link = mysqli_connect($DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE, DB_PORT);
			else
				$link = mysqli_connect($DB_SERVER, DB_SERVER_USERNAME, DB_SERVER_PASSWORD, DB_DATABASE);
			
			// Verbindung überprüfen
			if (mysqli_connect_errno()) {  
				fwrite($dateihandle, "(P) dmc_db_connect - Verbindung zu Datenbank fehlgeschlagen: ". mysqli_connect_error()."\n");
				exit();
			}
			
			// MySQL-Version aus dem Resultat-Array auslesen
			// fwrite($dateihandle, "dmc_db_connect - Verbindung zu Datenbank ".DB_DATABASE." hergestellt.\n");
	//	}
		
		return $link;
	} // end function
	
	
	// Gesamtbestand Variantenhauptprodukt aktualisieren
	function dmc_update_conf_stock($Artikel_Variante_Von) {
		global $dateihandle, $link;
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_update_conf_stock of product id $Artikel_Variante_Von =".$Artikel_Variante_Von);
		$link=dmc_db_connect();
		
		// Gesamtbestand der dem Artikel zugewiesenen attribute ermitteln.
		$query = "SELECT sum(attributes_stock) as total FROM ".TABLE_PRODUCTS_ATTRIBUTES." WHERE products_id =  ".$Artikel_Variante_Von;
		$sql_query = mysqli_query($link,$query);				
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
		if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
			$menge = 0;
		else
			$menge = $TEMP_ID['total'];
		
		// Update Hauptartikelbestand
		$update_sql_data = array(
          'products_quantity' => $menge);
        xtc_db_perform(TABLE_PRODUCTS, $update_sql_data, 'update', "products_id='" . $Artikel_Variante_Von . "'");
		
		//if (DEBUGGER==99) fwrite($dateihandle, "neuer bestand  product id $Artikel_Variante_Von =".$menge);
		
	} // end function

	// Artikelnummer einer Variante ermitteln
	function dmc_order_var_artnr($orders_id, $orders_products_id, $products_id, $products_model) {
		global $dateihandle, $link;
		//fwrite($dateihandle, "dmc_order_var_artnr2: ordersID: $orders_id, OrderprodID: $orders_products_id, prodID:  $products_id\n");
		$link=dmc_db_connect();
		
		// Auspraegung ermitteln
		$query="select oa.products_options, oa.products_options_values from orders_products as op inner join orders_products_attributes oa on (oa.orders_id=op.orders_id and oa.orders_products_id=op.orders_products_id) where op.orders_id = '".$orders_id."' and op.products_model = '" . $products_id . "' and op.orders_products_id = '" . $orders_products_id . "'";
	    $sql_query = mysqli_query($link,$query);				
		$attributes_query = mysqli_fetch_assoc($sql_query);	
		
		fwrite($dateihandle, "query-attributes_query:  $query\n");			// Ergebnis zB Groesse, XXL
		if ($attributes_query['products_options_values']=='' || $attributes_query['products_options_values']=='null') {
			$artnr = '';
		} else {
			// Attribut Artikelnummer zu diesem Produkt mit dieser Auspraegung ermitteln
			$query="SELECT pa.attributes_model FROM ".TABLE_PRODUCTS_ATTRIBUTES." pa INNER JOIN `products_options_values` pov ON pa.options_values_id=pov.products_options_values_id WHERE products_options_values_name='".$attributes_query['products_options_values']."' AND products_id=".$products_id;
			fwrite($dateihandle, "attributes_model_query: " . $query. "\n");
			$sql_query = mysqli_query($link,$query);				
			$TEMP_ID = mysqli_fetch_assoc($sql_query);	
			$artnr = $TEMP_ID['attributes_model'];
		}
		fwrite($dateihandle, "ArtikelVariante:  $artnr\n");
		return $artnr;	
	} 
	
	
	// decrepated -> products_dmc wird nicht mehr verwendet
	function dmc_order_var_artnr_decrepated($orders_id, $orders_products_id, $products_id) {
		
		$link=dmc_db_connect();
		
		$Variantenartikel  = false;
		// Attribute und values des bestellten Produktes ermitteln
		// und SQL Statement aufbauen
		$sql_statement = "SELECT artnr as total FROM products_dmc WHERE id > 0 ";
		$query="select products_options, products_options_values, options_values_price, price_prefix from orders_products_attributes where orders_id = '".$orders_id."' and orders_products_id = '" . $orders_products_id . "'";
	    $attributes_query = mysqli_query($link,$query);
		 if (xtc_db_num_rows($attributes_query))
            {
			  $Variantenartikel = true;
              while ($attributes = mysqli_fetch_assoc($attributes_query))
              {
				$sql_statement .= " AND merkmale like '%".$attributes['products_options'].
							"%' AND auspraegungen like '%".$attributes['products_options_values']."%'".
							" AND variante_von='".$products_id."'";
              }		// end while
            }    // end if
		global $dateihandle, $link;
		//if (DEBUGGER==99) fwrite($dateihandle, "dmc_order_var_artnr: $sql_statement\n");
		
		if ($Variantenartikel){
			$sql_query = mysqli_query($link,$sql_statement);				
			$TEMP_ID = mysqli_fetch_assoc($sql_query);	
			if ($TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
				$artnr = '';
			else
				$artnr = $TEMP_ID['total'];
		} else {
			$artnr = '';
		}
	//	if (DEBUGGER==99) fwrite($dateihandle, "ERGEBNIS= ".$artnr." .\n");
		return $artnr;	
	} // end function dmc_order_var_artnr_decrepated

	function dmc_get_country_id($isocode) {
		global $dateihandle, $link;
		$link=dmc_db_connect();
		
		if (DEBUGGER==99) 	fwrite($dateihandle, "dmc_get_country_id -> "); 
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
				$query = "SELECT id_country as countries_id FROM ".TABLE_COUNTRIES.
						" WHERE iso_code='".$isocode."'";
		
		else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) // Veyton hat keine separate ID
				$query = "SELECT countries_iso_code_2 as countries_id FROM ".TABLE_COUNTRIES.
						" WHERE countries_iso_code_2='".$isocode."' OR countries_iso_code_3='".$isocode."'";
		
		else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)
				$query = "SELECT id as countries_id FROM s_core_countries".
						" WHERE countryiso='".$isocode."' OR countryname='".$isocode."'";
				
		else 
				$query = "SELECT countries_id FROM ".TABLE_COUNTRIES.
						" WHERE countries_iso_code_2='".$isocode."' OR countries_iso_code_3='".$isocode."'";
		
		$sql_query = mysqli_query($link,$query);				
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
			
		if ($TEMP_ID['countries_id']=='' || $TEMP_ID['countries_id']=='null')
			$highest_id = 2;
		else
			$highest_id = $TEMP_ID['countries_id'];
		if (DEBUGGER==99)	fwrite($dateihandle, "ERGEBNIS ($query ) = ".$highest_id." .\n");
		return $highest_id;	
	} // end function dmc_get_country_id
	
	function dmc_get_region_id($country_id) {
		global $dateihandle, $link;
		if (DEBUGGER==99) fwrite($dateihandle, "dmc_get_region_id\n");
		$link=dmc_db_connect();
		
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
				$query = "SELECT min(id_state) as temp FROM ".TABLE_STATE.
						" WHERE id_country='".$country_id."'";
		else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) // Veyton hat keine separate Tabelle mir Zone IDs
			$query = "SELECT min(zone_id) as temp FROM ".TABLE_COUNTRIES.
						" WHERE countries_iso_code_2='".$country_id."' OR countries_iso_code_3='".$country_id."'";
		else 
			$query = "SELECT min(zone_id) as temp FROM zones WHERE zone_country_id = ".$country_id;
		
		$sql_query = mysqli_query($link,$query);				
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
			
		if ($TEMP_ID['temp']=='' || $TEMP_ID['temp']=='null')
			$highest_id = 1;
		else
			$highest_id = $TEMP_ID['temp'];
		if (DEBUGGER==99) fwrite($dateihandle, "ERGEBNIS= ".$highest_id." .\n");
		return $highest_id;	
	} // end function
	
	// Standard-Preis für Artikel ID ermitteln
	function dmc_get_price($artid) {
		global $dateihandle, $link;
		if (DEBUGGER==99) fwrite($dateihandle, "dmc_get_price of $artid =");
		$link=dmc_db_connect();
		
		$query = "SELECT products_price AS preis FROM ".TABLE_PRODUCTS." WHERE  products_id = '".$artid."'";
		$sql_query = mysqli_query($link,$query);				
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
		if ($TEMP_ID['preis']=='' || $TEMP_ID['preis']=='null')
			$preis = 0;
		else
			$preis = $TEMP_ID['preis'];
		return $preis;	
	} // end function

	function dmc_get_highest_id($id_column,$table) {
		
		global $dateihandle, $link;
		$link=dmc_db_connect();
		
		$query = "SELECT MAX(".$id_column.") AS rueckgabe FROM ".$table;
		
		$link=dmc_db_connect();
		//	$dateiname=LOG_FILE;	
		//$dateihandle = fopen($dateiname,"a");
		if (DEBUGGER==99)  fwrite($dateihandle, "dmc_get_highest_id-SQL= ".$query." = ");
	
		$sql_query = mysqli_query($link,$query);					
		$result = mysqli_fetch_assoc($sql_query);				
		if ($result['rueckgabe']=='' || $result['rueckgabe']=='null')
			$rueckgabe = 0;
		else
			$rueckgabe = $result['rueckgabe'];
	
		if (DEBUGGER==99) fwrite($dateihandle, " ERGEBNIS= ".$rueckgabe." .\n");
		return $rueckgabe;	
	} // end function
	
	function dmc_get_attributes_model($product_id, $attribute_name)
    {
		$link=dmc_db_connect();
		
	    $options_value_id_query=mysqli_query($link,"SELECT
	                products_options_values_id
	                FROM ".TABLE_PRODUCTS_OPTIONS_VALUES."
	                WHERE products_options_values_name='".$attribute_name."'");

	    while ($options_value_id_data=mysqli_fetch_assoc($options_value_id_query)) {
	    $options_attr_query=mysqli_query($link,"SELECT
	                attributes_model
	                FROM ".TABLE_PRODUCTS_ATTRIBUTES."
	                WHERE options_values_id='".$options_value_id_data['products_options_values_id']."' AND products_id =" . $product_id);
	    $options_attr_data=mysqli_fetch_assoc($options_attr_query);
		    if ($options_attr_data['attributes_model']!='') {
				return $options_attr_data['attributes_model'];
		    }
		} 	
	} // end function    
	
	// Preisdifferenz zum Hauptartikel für Artikel ID ermitteln
	function dmc_get_attribute_price($Artikel_ATTRIBUTE_ID, $Artikel_ATTRIBUTE_Preis) {
		global $dateihandle, $link;
		$dateihandle = fopen($dateiname,"a");
		$link=dmc_db_connect();
		
		if (DEBUGGER==99) fwrite($dateihandle, "dmc_get_attribute_price of Artikel_ATTRIBUTE_ID $Artikel_ATTRIBUTE_ID\n");

						// Artikel-ID des Hauptartikels ermitteln
						$query = "SELECT distinct products_id from ".TABLE_PRODUCTS_ATTRIBUTES. " WHERE products_attributes_id=".$Artikel_ATTRIBUTE_ID;
							
						if (DEBUGGER==99) fwrite($dateihandle, "dmc_get_attribute_price-SQL= ".$query." .\n");
						
						$sql_query = mysqli_query($link,$query);				
						$TEMP_ID = mysqli_fetch_assoc($sql_query);	
						if ($TEMP_ID['products_id']=='' || $TEMP_ID['products_id']=='null')
							$Artikel_Variante_Von = 0;
						else
							$Artikel_Variante_Von = $TEMP_ID['products_id'];
						if (DEBUGGER==99)  fwrite($dateihandle, "Artikel_Variante_Von= ".$Artikel_Variante_Von." .\n");
						// Preisberechnung = Variantenpreis - Standandproduktpreis
						// Kein Hauptartikel gefunden
						if ($Artikel_Variante_Von==0) {
							$options_values_price = 0;
						} else {
							// NUR für erste Ausprägung eines Produktes gurchlaufen (sonst zuschlag wegen Grösse und Farbe)
							$options_values_price = $Artikel_ATTRIBUTE_Preis-dmc_get_price($Artikel_Variante_Von);
						}
						if (DEBUGGER==99)  fwrite($dateihandle, "Preis Haupt=".dmc_get_price($Artikel_Variante_Von)." Artikel_Preis=$Artikel_ATTRIBUTE_Preis, options_values_price=$options_values_price");						
						
	return $options_values_price;	
	} // end function dmc_get_attribute_price
	
	// Bestellkommentar ermitteln
	function dmc_get_order_comment($order_id, $orders_status) {
		$link=dmc_db_connect();
		
			$comments_query = mysqli_query($link,"SELECT comments from " . TABLE_ORDERS_STATUS_HISTORY . 
			" WHERE orders_id = '" . $order_id . "' AND orders_status_id = '" . $orders_status . "' ");
			if ($comments =  mysqli_fetch_assoc($comments_query)) 
				$comments =  $comments['comments'];
			else 
				$comments =  $comments['comments'];
				
			return $comments;
	} // end dmc_get_order_comment
	
	
	// prüfen auf kategorie_ID (kategorie vorhanden?)
	function dmc_get_category_id($Kategorie_ID) {

		global $dateihandle, $link;
		$link=dmc_db_connect();
		
		$new_cat_id='0';
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_get_category_id  (db_functions) -> ");

		$Kategorie_ID = str_replace('\\\\', '\\', $Kategorie_ID);		// Backslash (\) durch slash (/) ersetzen
		$Kategorie_ID = str_replace('\\', '/', $Kategorie_ID);		// Backslash (\) durch slash (/) ersetzen
						
		// Prüfen auf KatID als meta_desc
		if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$cmd = 	"select id_category as categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" WHERE name='$Kategorie_ID' OR  meta_description like '$Kategorie_ID %' OR meta_description = '$Kategorie_ID' or meta_description like '$Kategorie_ID,%' LIMIT 1";
			// Anpassung fuer csv Dateien, wie von talksky, wenn Aufbau = kategoriename, unterkategorie1, unterkategorie2,unterkategorie3, zB Handys, Taschen, , ,
			/*if(strpos($Kategorie_ID, ',') !== false) {
				$Kategorie_ID_Nummer = explode ( ',', $Kategorie_ID);
				// wenn keine Unterkategorie existiert
				if (trim($Kategorie_ID_Nummer[1])=='') {
						// wenn Unterkategorie existiert
					$cmd = 	"SELECT id_category as categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" WHERE name = '$Kategorie_ID %'  LIMIT 1";
				}	 
				if (trim($Kategorie_ID_Nummer[1])!='') {
					$cmd = 	"select id_category as categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where meta_description like '$Kategorie_ID %' OR meta_description = '$Kategorie_ID' or meta_description like '$Kategorie_ID,%' LIMIT 1";
		
				} 
			}*/
					
		} else if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
			$cmd = 	"SELECT virtuemart_category_id AS categories_id FROM " . TABLE_CATEGORIES_DESCRIPTION .
					" WHERE metadesc like '$Kategorie_ID %' OR metadesc = '$Kategorie_ID' OR metadesc like '$Kategorie_ID,%'".
					" OR metakey = '$Kategorie_ID' OR metakey like '$Kategorie_ID,%' LIMIT 1";
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) { 
			$cmd = 	"select categories_id from " . TABLE_CATEGORIES_DESCRIPTION ." AS cd INNER JOIN xt_seo_url AS SEO ON (cd.categories_id=SEO.link_id) ".
				// BIS VERSION 5	" where categories_meta_description = '$Kategorie_ID' OR categories_meta_description like '$Kategorie_ID,%' LIMIT 1";
					" where SEO.meta_keywords = '$Kategorie_ID' OR SEO.meta_keywords like '$Kategorie_ID,%' LIMIT 1";
		} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) { 
			$cmd = 	"select c.id AS categories_id FROM s_categories AS c LEFT OUTER JOIN s_categories_attributes AS ca ON c.id=ca.categoryID" .
					" where c.metadescription = '$Kategorie_ID' OR c.metadescription like '$Kategorie_ID,%' OR c.metakeywords = '$Kategorie_ID' OR c.metakeywords like '$Kategorie_ID,%'  OR ca.attribute1='$Kategorie_ID' ".
					" OR c.description = '$Kategorie_ID' ";	// Pruefe auch auf Kategorienamen
						// NEU ab 10.10.17, es koennen mehrere ermittelt werden " LIMIT 1";
		} else if(strpos(strtolower(SHOPSYSTEM), 'woo') !== false) { 
			$cmd = 	"select term_id as categories_id FROM " . DB_TABLE_PREFIX.'terms' .
					" where (slug = '$Kategorie_ID' OR slug = '".strtolower($Kategorie_ID)."' )  LIMIT 1";
		} else if(strpos(strtolower(SHOPSYSTEM), 'zen') !== false) { 
			// Bei zencart auf bezeichnung pruefen
			$cmd = 	"SELECT categories_id FROM " . TABLE_CATEGORIES_DESCRIPTION .
					" WHERE categories_name = '$Kategorie_ID' OR categories_description like '$Kategorie_ID%' LIMIT 1";
		} else if(strpos(strtolower(SHOPSYSTEM), 'osc') !== false) { 
			// Bei zencart auf bezeichnung pruefen
			$cmd = 	"SELECT categories_id FROM " . TABLE_CATEGORIES_DESCRIPTION .
					" WHERE categories_meta_keywords like '$Kategorie_ID,%' OR categories_name like '$Kategorie_ID%' LIMIT 1";
		} else { 
			$cmd = 	"Select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where categories_meta_description = '$Kategorie_ID' OR categories_meta_description like '$Kategorie_ID,%' OR categories_meta_keywords like '$Kategorie_ID,%' LIMIT 1";
		}
		if (DEBUGGER>=99) fwrite($dateihandle, " ( $cmd ) ->");

		try {
			$sql_query = mysqli_query($link,$cmd);
			// NEU ab 10.10.17, es koennen mehrere ermittelt werden
			$x = 0;
			$new_cat_id="";
		//	fwrite($dateihandle, "1402 \n");
			while($row = mysqli_fetch_array($sql_query)) {   
			//	fwrite($dateihandle, "1404 durchlauf $x \n");
				if ($x==0)
					$new_cat_id=$row['categories_id'];
				else 
					$new_cat_id .= "@".$row['categories_id'];
				$x++;
				if ($x>5)
					break;	// Abfangroutine
			}
		/*	if ($result_query = mysqli_fetch_assoc($sql_query))
			{ // Kategorie existiert bereits: Bestehende categories_id ermitteln	
				// fwrite($dateihandle, "797 extistiert mit id ".$result_query['categories_id']."\n");
				$new_cat_id=$result_query['categories_id'];
			} else*/
			// Abfangroutine wenn keine gefunden
			if ($new_cat_id=="") {
				// Prüfen, ob KategorieID als solche direkt existiert und noch nicht
				if (is_numeric($Kategorie_ID) && (strpos(strtolower(SHOPSYSTEM), 'woo') === false)) {
					if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
						$cmd = 	"select id_category from " . TABLE_CATEGORIES .
							" where id_category=" . $Kategorie_ID . "";		
					} else if(strpos(strtolower(SHOPSYSTEM), 'virtuemart') !== false) {
						$cmd = 	"SELECT virtuemart_category_id AS categories_id FROM " . TABLE_CATEGORIES_DESCRIPTION .
								" WHERE virtuemart_category_id = '$Kategorie_ID'";
					} else if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) { 
						$cmd = 	"select id AS categories_id FROM s_categories" .
								" where id = '$Kategorie_ID'";
					} else {
						$cmd = 	"select categories_id from " . TABLE_CATEGORIES .
							" where categories_id=" . $Kategorie_ID . "";	
					}
					 
					$cat_query = mysqli_query($link,$cmd);
					if ($cat = mysqli_fetch_assoc($cat_query))
					{		
					  $new_cat_id=$Kategorie_ID;
					} else {		
					  $new_cat_id='0';
					}	 		 
				} else {		
					  $new_cat_id='0';
				} 				
			}
		} catch (Exception $e) {
			fwrite($dateihandle, 'Fehler ',  $e->getMessage(), "\n");
		}
		
		if ($new_cat_id!='0') 
			if (DEBUGGER>=50) 
				fwrite($dateihandle, " -> dmc_db_functions - dmc_get_category_id - Kategorie $Kategorie_ID extistiert mit id ".$new_cat_id."\n");
		else  {
			if (DEBUGGER>=50)
				fwrite($dateihandle, " -> dmc_db_functions - dmc_get_category_id - Kategorie $Kategorie_ID extistiert noch nicht.\n");
			// $new_cat_id=$Kategorie_ID;
		}
		
		// NEU ab 10.10.17, es koennen mehrere ubergeben werden, dann durch @ getrennt
		return $new_cat_id;
	} // end function dmc_get_category_id
	
	// Standard-Gewicht für Artikel ID ermitteln
	function dmc_get_weight($artid) {
		global $dateihandle, $link;
		$link=dmc_db_connect();
		
		if (DEBUGGER>=50)
			fwrite($dateihandle, "dmc_get_weight of $artid \n");
	
		$query = "SELECT products_weight AS gewicht FROM ".TABLE_PRODUCTS." WHERE  products_id = '".$artid."'";
		$sql_query = mysqli_query($link,$query);				
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
		if ($TEMP_ID['gewicht']=='' || $TEMP_ID['gewicht']=='null')
			$gewicht = 0;
		else
			$gewicht = $TEMP_ID['gewicht'];
		return $gewicht;	
	} // end function
	
	function dmc_sql_query_alt($query) {
		global $dateihandle, $link;
		$sql_query = mysqli_query($link,$query);				
		
		return true;	
	} // end function dmc_sql_query_alt
			 
	function dmc_entry_exists($table,$column,$value,$AndOr,$column2,$value2)
	{
	 	global $dateihandle, $link;
		$link=dmc_db_connect();
		
		if (DEBUGGER>=50) fwrite($dateihandle, "function dmc_entry_exists \n"); 
		
		$query=	"SELECT ". $column .
					   " FROM " . $table .
					   " WHERE ".$column." = '".$value."'";
		if ($column2!="" && $AndOr!="")
			if (strtolower($AndOr)=="or")
				$query	.=	" OR ".$column2." = '".$value2."'";
			else
				$query	.=	" AND ".$column2." = '".$value2."'";
		
		if (DEBUGGER>=99) fwrite($dateihandle, "dmc_entry_exists".$query."\n"); 
		
		$do_query=mysqli_query($link,$query);
			
		if (!($result = mysqli_fetch_assoc($do_query)))
			$exists=false;
		else
			$exists=true;

		return $exists;
	} // end 	function dmc_entry_exists
	
	function dmc_cat_desc_exists($cat_id,$language_id)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "function dmc_cat_desc_exists\n"); 
		
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query = 	"select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where categories_id='".$cat_id."' AND language_code='" . $language_id . "'";
		} else if(strpos(strtolower(SHOPSYSTEM), 'presta') !== false) {
			$language_id=1;
			$query = 	"select id_category from " . TABLE_CATEGORIES_DESCRIPTION .
					" where id_category='".$cat_id."' AND id_lang='" . $language_id . "'";
		} else {
			$query = 	"select categories_id from " . TABLE_CATEGORIES_DESCRIPTION .
					" where categories_id='".$cat_id."' and language_id='" . $language_id . "'";
		}
		
		if (DEBUGGER>=99) fwrite($dateihandle, "dmc_cat_desc_exists query ->".$query."\n"); 
		
		$do_query=mysqli_query($link,$query);
			
		if (!($result = mysqli_fetch_assoc($do_query)))
			$exists=false;
		else
			$exists=true;

		return $exists;
	} // end 	function dmc_cat_desc_exists
	
	// Id der ersten zugeordneten Adresse ermitteln
	function dmc_get_gambio_filter_id($customers_id, $address_class)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "function dmc_get_gambio_filter_id\n"); 
		
		if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$customers_query=mysqli_query($link,"SELECT min(id_address) AS ergebnis " .
						   " FROM " . feature_description .
						   " WHERE id_customer='$customers_id' AND active = 1 AND	deleted = 0");
		else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) 
			$customers_query=mysqli_query($link,"SELECT min(address_book_id) AS ergebnis FROM " . TABLE_CUSTOMERS_ADDRESSES .
										" WHERE customers_id='$customers_id'");				   
		else 
			$customers_query=mysqli_query($link,"SELECT min(address_book_id) AS ergebnis  FROM " . TABLE_ADDRESS_BOOK .
										" WHERE customers_id='$customers_id' AND address_class='$address_class'");
			
		if (!($customers = mysqli_fetch_assoc($customers_query)))
			$customers_adress_id='';
		else
			$customers_adress_id = $customers['ergebnis'];

		return $customers_adress_id;
	} // end 	function dmc_get_gambio_filter_id
	
	// Funktion um Produkte zu löschen
	function dmc_delete_first() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "Truncate table dmc_delete_first\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			// if (DEBUGGER>=50) fwrite($dateihandle, "\nPRODUCTS\n"); 
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products");
			
			// if (DEBUGGER>=50) fwrite($dateihandle, "\nPRODUCTS DESCRIPTION etc\n"); 
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_description");
			
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_attributes");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_images");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_to_categories");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_vpe");
			
			// if (DEBUGGER>=50) fwrite($dateihandle, "\nPREISE\n"); 
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_0");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_1");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_2");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_3");
			//if (DEBUGGER>=50) fwrite($dateihandle, "\nLEEREN ABGESCHLOSSEN\n"); 
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_description");
			
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_cross_sell");
			
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_price_special");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_reviews");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_to_categories");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_serials");
			
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_plg_products_attributes");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_plg_products_attributes_description");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_plg_products_to_attributes");
			
			// if (DEBUGGER>=50) fwrite($dateihandle, "\nPREISE\n"); 
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_price_group_all");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_price_group_1");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_price_group_3");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_price_group_2");
			
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_products_permission");
			
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_seo_url WHERE link_type=1");
		}
		
		return true;
	}
	
	// Funktion um Varianten Produkte zu löschen
	function dmc_delete_variants_first() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_delete_variants_first\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			
			// dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_attributes");
			//if (DEBUGGER>=50) fwrite($dateihandle, "\nLEEREN ABGESCHLOSSEN\n"); 
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_seo_url WHERE link_type=1 AND link_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_reviews WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_to_categories WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_price_special WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_price_group_all WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_price_group_1 WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_price_group_2 WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_price_group_3 WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products_permission WHERE products_id IN (SELECT products_id FROM xt_products WHERE products_master_flag = 0)");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_plg_products_to_attributes");
						 
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_products WHERE products_master_flag = 0");
		}
		return true;
	} // dmc_delete_variants_first
	 
	// Funktion um alle Produkte zu deaktivieren
	function dmc_deactivte_first() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_deactivte_first\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			//dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "products SET products_status=0 ");
			//if (DEBUGGER>=50) fwrite($dateihandle, "\nLEEREN ABGESCHLOSSEN\n"); 
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "xt_products SET products_status=0 ");
		}
		return true;
	} // dmc_deactivte_first
	
	// Funktion um alle Varianten Produkte zu deaktivieren
	function dmc_deactivate_variants_first() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_deactivate_variants_first\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			
			// dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_attributes");
			//if (DEBUGGER>=50) fwrite($dateihandle, "\nLEEREN ABGESCHLOSSEN\n"); 
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "xt_products SET products_status=0 WHERE products_master_flag = 0");
		}
		return true;
	} // dmc_deactivate_variants_first
	
	// Funktion um Kategorien zu löschen
	function dmc_delete_categories_first() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_delete_categories_first\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') === false && strpos(strtolower(SHOPSYSTEM), 'presta') === false) {
			
			// dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "products_attributes");
			//if (DEBUGGER>=50) fwrite($dateihandle, "\nLEEREN ABGESCHLOSSEN\n"); 
		} else if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			
			dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "xt_seo_url WHERE link_type=2");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_categories ");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_categories_description ");
			dmc_sql_query("TRUNCATE TABLE " . DB_TABLE_PREFIX . "xt_categories_permission ");			
		}
		return true;
	} // dmc_delete_categories_first
	
	// Veyton only
	function dmc_deactivate_master_without_slave() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_deactivate_master_without_slave\n"); 
		$query= "SELECT products_model FROM " . DB_TABLE_PREFIX . "xt_products where products_master_flag=1 AND  products_model not in (Select distinct products_master_model from xt_products)";
		 $do_query = mysqli_query($link,$query);
		 if (xtc_db_num_rows($do_query))
            {
			   while ($results = mysqli_fetch_assoc($do_query))
              {
				dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "xt_products SET products_status=0 WHERE products_model='".$results['products_model']."'");
			
              }		// end while
            }    // end if
		 
		return true;
	} // dmc_deactivate_master_without_slave

		// Funktion um alle Hauptkategorien als Veyton TOP Kategorien zu setzen 
	function dmc_set_top_categories() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_set_top_categories\n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'veyton') !== false ) {
			dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "xt_categories SET top_category=1 WHERE parent_id = 0");
		}
		return true;
	} // dmc_set_top_categories
	
	function dmc_db_get_new_id() {
		global $dateihandle, $link;
		return mysqli_insert_id($link);
	}
	
		// Ueberpruefe ob Datensatz vorhanden - Check if entry exits
	function dmc_entry_exits($column, $table, $where) {
	
		global  $dateihandle;
		
		$query = "SELECT count(*) as total FROM ".DB_TABLE_PREFIX."".$table." WHERE ".$where." ";
		
		$link=dmc_db_connect();
		
		if (DEBUGGER==99) fwrite($dateihandle, "dmc_entry_exits-SQL= ".$query." .\n");		
		
		// mysqli_query($link,"SET NAMES 'utf8'", $link);
		try {
			$sql_query = mysqli_query($link,$query);				
		} catch (Exception $e) {
			if (DEBUGGER>=99) fwrite($dateihandle, "dmc_entry_exits - 1541 - Error:\n".$e->getMessage()."\n");
			$fehler="table not exists";
			return $fehler;
		}
		
		$TEMP_ID = mysqli_fetch_assoc($sql_query);	
			
		if (DEBUGGER==99) fwrite($dateihandle, "Result = temp_id total=".$TEMP_ID['total'].".\n");
		
		if ($TEMP_ID['total']=='0' || $TEMP_ID['total']=='' || $TEMP_ID['total']=='null')
			$exists = false;			
		else
			$exists = true;
		
		// dmc_db_disconnect($link);
		
		return $exists;	
	} // end function dmc_entry_exits

	//  Kategorien der höchsten Ordnung, welche keine Artikel-Zuordnung mehr besitzen, werden im Anschluß an den Artikelexport gelöscht.
	function dmc_del_cat_without_products($ebene) {
		global $dateihandle, $link;
	
		$likepath = '';

		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_del_cat_without_products Ebene = $ebene \n"); 
		if(strpos(strtolower(SHOPSYSTEM), 'shopware') !== false && $ebene>1) {
			// Ebene resultiert aus dem Feld path in path , zb |492|79|22|3| ist Ebene 4, d.h. | kommt 5 mal vor.
			for ($i = 1; $i<$ebene; $i++) {
				$likepath .= '|%';
			}
			$likepath .= '|';
			// Alle anzeigen (Ebene 4) , die KEIEN Artikel führen SELECT * FROM s_categories where path like '|%|%|%|%|' AND id NOT IN (SELECT categoryID FROM s_articles_categories)
			$query="DELETE FROM s_categories where path like $likepath AND id NOT IN (SELECT categoryID FROM s_articles_categories)";
			//dmc_sql_query($query);
			if (DEBUGGER>=50) fwrite($dateihandle, "LEEREN ($query) ABGESCHLOSSEN\n"); 
		} 
		return true;
	} // dmc_del_cat_without_products
	
	// Ermittlung der ID einer Kundengruppe 
	// Rueckgabe "", wenn keine ID vorhanden
	function dmc_get_customer_group_id($kundengruppe)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function ... dmc_get_customer_group_id ... "); 
		$link=dmc_db_connect();
		
		if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false)
			$query="";
		else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$query="SELECT id_group FROM group_lang WHERE name = '".$kundengruppe."' LIMIT 1";
		else if (strpos(strtolower(SHOPSYSTEM), 'virtu') !== false) {
			$query="";
		} else if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			$query="";
		} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query="";			   
		} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
			$query="";
		} else {
				$query = "SELECT customers_status_id AS kundengruppen_id FROM ".DB_TABLE_PREFIX."customers_status ".
						  " WHERE customers_status_name = '$kundengruppe'";
				if (is_numeric($kundengruppe))		  
						$query .=   " OR customers_status_id='$kundengruppe'";
		}
		if ($query!="") {
			$customers_query=mysqli_query($link,$query);
			if (!($customers = mysqli_fetch_assoc($customers_query)))
				$customers_id='';
			else
				$customers_id = $customers['kundengruppen_id'];
		}
		if (DEBUGGER>=1) fwrite($dateihandle, "query=$query mit Ergebnis=$customers_id\n"); 

		return $customers_id;
	} // end 	function dmc_get_customer_group_id
	
		// dmc_create_customer_group - Kundengruppe anlegen mit Rückgabe der ID
	function dmc_create_customer_group($kundengruppe, $anzahl_sprachen)
	{
	 	global $dateihandle, $link;
		if (DEBUGGER>=1) fwrite($dateihandle, "function ... dmc_create_customer_group($kundengruppe) $anzahl_sprachen Sprachen... "); 
		$link=dmc_db_connect();
		
		if (strpos(strtolower(SHOPSYSTEM), 'zencart') !== false)
			$query="";
		else if (strpos(strtolower(SHOPSYSTEM), 'presta') !== false)
			$query="";
		else if (strpos(strtolower(SHOPSYSTEM), 'virtu') !== false) {
			$query="";
		} else if (strpos(strtolower(SHOPSYSTEM), 'woo') !== false) {
			$query="";
		} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
			$query="";			   
		} else if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false) {
			$query="";
		} else {
			// Anzahl der Sprachen
			// Step 1 - Anlage Kundengruppe in customers_status
			$customers_group_id=dmc_get_highest_id('customers_status_id',DB_TABLE_PREFIX."customers_status")+1;
			$insert_sql_data = array(	
									'customers_status_id' => $customers_group_id,
									'customers_status_name' => $kundengruppe,
									'customers_status_public' => '0',
									// Weitere Kundengruppen Eigenschaften ggfls eintragen
								/*	'customers_status_min_order' => '0',
									'customers_status_max_order' => '0',
									'customers_status_image' => '0',
									'customers_status_discount' => '0',
									'customers_status_ot_discount' => '0',
									'customers_status_graduated_prices' => '0', */
									'customers_status_show_price' => '1',				
									'customers_status_show_price_tax' => '0',		// 0 fuer netto Preise, 1 fuer brutto preise
								//	'customers_status_add_tax_ot' => '1',
								/*	'customers_status_payment_unallowed' => '',
									'customers_status_shipping_unallowed' => '',
									'customers_status_discount_attributes' => '',
									'customers_fsk18' => '0',
									'customers_fsk18_display' => '0',
									'customers_status_write_reviews' => '0',
									'customers_status_read_reviews' => '0',*/
									);	
			for ($language_id = 1; $language_id<=$anzahl_sprachen; $language_id++) {
				$insert_sql_data['language_id']=$language_id;
				dmc_sql_insert_array(DB_TABLE_PREFIX."customers_status", $insert_sql_data);
			} // end for
			// Step 2 - Shop-Tabelle für Preise der Kundengruppe customers_group_id (personal_offers_by_customers_status_...) anlegen
			$tabelle = DB_TABLE_PREFIX."personal_offers_by_customers_status_".$customers_group_id;
			$query = "CREATE TABLE ".$tabelle."  (
							  `price_id` int(11) NOT NULL AUTO_INCREMENT,
							  `products_id` int(11) NOT NULL DEFAULT '0',
							  `quantity` decimal(15,4) DEFAULT NULL,
							  `personal_offer` decimal(15,4) DEFAULT NULL,
							  PRIMARY KEY (`price_id`),
							  UNIQUE KEY `unique_offer` (`products_id`,`quantity`)
							) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;";
			dmc_sql_query($query);
			// Step 3 - Produkt Tabelle um group Permissions ergaenzen
			$tabelle = DB_TABLE_PREFIX."products";
			$query = "ALTER TABLE ".$tabelle." ADD group_permission_" . $customers_group_id . " TINYINT( 1 ) NOT NULL";
			dmc_sql_query($query);
			// Step 4 - Kategorie Tabelle um group Permissions ergaenzen
			$tabelle = DB_TABLE_PREFIX."categories";
			$query = "ALTER TABLE ".$tabelle." ADD group_permission_" . $customers_group_id . " TINYINT( 1 ) NOT NULL";
			dmc_sql_query($query);
			// Wohl nicht noetig - Step 5 - Preise in Kundentabelle schreiben 
			// $STANDARDKUNDENGRUPPE=1;
			// 	$products_query = dmc_db_query("INSERT INTO personal_offers_by_customers_status_".DB_TABLE_PREFIX."personal_offers_by_customers_status_".$customers_group_id." (select price_id, products_id, quantity, personal_offer from personal_offers_by_customers_status_" . $STANDARDKUNDENGRUPPE . ")");
                      
		} // end if SHOPSYSTEM
	
		if (DEBUGGER>=1) fwrite($dateihandle, " mit Rueckgabe KundengruppenID=$customers_group_id\n"); 

		return $customers_group_id;
	} // end 	function dmc_get_customer_group_id
	
		//  presta Laender ISO Code nach Presta Country ID ermitteln	
	function presta_get_isocode_by_id($country_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_isocode_by_id fuer country_id $country_id \n");	
		$temp_sql = "SELECT ca.iso_code ".
					"FROM " . TABLE_COUNTRIES . " AS ca ".
					"WHERE ca.id_country=".$country_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$isocode=$temp_data['iso_code'];
		} else {
			// Standard
			$isocode='DE';
		}
		return $isocode; 
	} // end function presta_get_isocode_by_id
	
	//  presta Laender Bezeichnung nach Presta Country ID ermitteln	
	function presta_get_country_by_id($country_id, $language_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_country_by_id fuer country_id $country_id = ");	
		$temp_sql = "SELECT ca.name ".
					"FROM " . TABLE_COUNTRIES_DESC . " AS ca ".
					"WHERE ca.id_country=".$country_id." AND id_lang=".$language_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$country=$temp_data['name'];
		} else {
			// Standard
			$country='Germany';
		}
		if (DEBUGGER>=1) fwrite($dateihandle, " $country \n");	
		
		return $country; 
	} // end function presta_get_country_by_id
	
	//  presta Currency Bezeichung nach Presta Currency ID ermitteln	
	function presta_get_currency_by_id($currency_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_currency_by_id fuer country_id $currency_id \n");	
		$temp_sql = "SELECT ca.name ".
					"FROM " . TABLE_CURRENCY . " AS ca ".
					"WHERE ca.id_currency=".$currency_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$currency=$temp_data['name'];
		} else {
			// Standard
			$currency='EUR';
		}
		return $currency; 
	} // end function presta_get_currency_by_id
	
	//  presta Currency Umrechnungsfaktor nach Presta Currency ID ermitteln	
	function presta_get_currency_rate_by_id($currency_id, $dateihandle)
	{	
		if (DEBUGGER>=1) fwrite($dateihandle, " presta_get_currency_rate_by_id fuer country_id $currency_id \n");	
		$temp_sql = "SELECT ca.conversion_rate ".
					"FROM " . TABLE_CURRENCY . " AS ca ".
					"WHERE ca.id_currency=".$currency_id;
		$temp_query = dmc_db_query ($temp_sql);
	    if (($temp_query) && ($temp_data = dmc_db_fetch_array($temp_query)))
	    {
			$currency=$temp_data['conversion_rate'];
		} else {
			// Standard
			$currency=1;
		}
		return $currency; 
	} // end function presta_get_currency_rate_by_id
	
	//	27.09.2017   dmc_get_iso_by_county - ISO Code by Country NAME					 		*
	function dmc_get_iso_by_county($country_name) {
		// WICHTIG: Zunächst Tabelle countries aus dmc Verzeichnis /install anlegen
		
		global  $dateihandle;
		
		$query = "SELECT code AS iso_code FROM countries  ".
		       " WHERE de='".$country_name ."'";
		$link=dmc_db_connect();
		if (DEBUGGER>1)  fwrite($dateihandle, "dmc_get_iso_by_county-SQL= ".$query." ... ");
		// echo "dmc_get_incr_id_by_adress_id-SQL= ".$query." .\n";
		$sql_query = mysqli_query($link,$query);				
		
		while ($TEMP_ID = mysqli_fetch_assoc($sql_query)) {
			if ($TEMP_ID['iso_code']=='' || $TEMP_ID['iso_code']=='null') {
				// IF no ID -> Deutschland
				$isocode = 'DE';
		    } else {
				$isocode  = $TEMP_ID['iso_code']; 
			}
		}		

		if (DEBUGGER>1)  fwrite($dateihandle, " Ergebnis = ".$isocode." .\n");
		
		dmc_db_disconnect($link);
			
		return $isocode;	
	} // end function dmc_get_iso_by_county
	
	// dmc_woo_get_assigned_image_ids - Ids aller zugeordneten Bilder wooCommerce ermitteln
	function dmc_woo_get_assigned_image_ids() {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_woo_get_assigned_image_ids\n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		$link=dmc_db_connect();
		$query= "SELECT meta_value FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key='_thumbnail_id'";
		dmc_sql_query($sql_query);
		$image_ids="";
		$anzahl=0;
		$do_query = mysqli_query($link,$query);
		while ($results = mysqli_fetch_assoc($do_query)) {
			if ($results['meta_value']<>'') {
				if ($anzahl==0)
					// erstes Bild
					$image_ids = $results['meta_value'];
				else
					$image_ids .= ",".$results['meta_value'];
				$anzahl++;
			} 
		}		// end while
        // Zusatzbilder IDs
		$query= "SELECT meta_value FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key='_product_image_gallery'";
		dmc_sql_query($sql_query);
		$do_query = mysqli_query($link,$query);
		while ($results = mysqli_fetch_assoc($do_query)) {
			if ($results['meta_value']<>'')
				$image_ids .= ",".$results['meta_value'];
		}		// end while
         
		return $image_ids;
	} // dmc_woo_get_assigned_image_ids
	
	// dmc_woo_get_image_id- wooCommerce ID Hauptbild ermitteln
	function dmc_woo_get_image_id($post_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_woo_get_image_id($post_id) \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		// $link=dmc_db_connect();
		$query= "SELECT meta_value FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key='_thumbnail_id' AND post_id = $post_id LIMIT 1";
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['meta_value']<>'')
			$image_id=$results['meta_value'];
		else 
			$image_id=0;
		return $image_id;
	} // dmc_woo_get_image_id
	
	// -dmc_woo_get_gallery_image_ids wooCommerce IDs Zusatzbilder ermitteln
	function dmc_woo_get_gallery_image_ids($post_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_woo_get_gallery_image_ids($post_id) \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		//$link=dmc_db_connect();
		$query= "SELECT meta_value FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key='_product_image_gallery' AND post_id = $post_id  LIMIT 1";
		$do_query = mysqli_query($link,$query);
		if (!($results = mysqli_fetch_assoc($do_query)))
			$image_ids=0;
		else
			if ($results['meta_value']<>'')
				$image_ids=$results['meta_value'];
			else 
				$image_ids=0;
		 
		return $image_ids;
	} // dmc_woo_get_gallery_image_ids
	
	// dmc_woo_get_image_ids_by_name - wooCommerce BilderIDs nach Bildname
	function dmc_woo_get_image_ids_by_name($image_name) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_woo_get_image_ids_by_name($image_name) \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		//$link=dmc_db_connect();
		$query= "SELECT post_id FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key = '_wp_attached_file' AND meta_value='$image_name' ORDER BY post_id DESC";
		//echo $query;
		$image_id=0;
		$anzahl=0;
		$do_query = mysqli_query($link,$query);
		while ($results = mysqli_fetch_assoc($do_query)) {
			// echo "Bildname=$image_name mit ID=".$results['post_id']."<br>\n";
			if ($results['post_id']<>'') {
				if ($anzahl==0)
					// erstes Bild
					$image_ids = $results['post_id'];
				else
					$image_ids .= ",".$results['post_id'];
				$anzahl++;
			}
		}		// end while
         
		return $image_ids;
	} // dmc_woo_get_image_ids_by_name
	
	// dmc_woo_get_image_name_by_id - wooCommerce BildName nach ID  ermitteln
	function dmc_woo_get_image_name_by_id($post_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_woo_get_image_name_by_id \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		//$link=dmc_db_connect();
		$query= "SELECT meta_value FROM " . DB_TABLE_PREFIX . "postmeta WHERE meta_key='_wp_attached_file' AND post_id = $post_id LIMIT 1";
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['meta_value']<>'')
			$image_id=$results['meta_value'];
		else 
			$image_id=0;
		return $image_id;
	} // dmc_woo_get_image_name_by_id
	
	// 23.10.2017 - dmc_get_magnalister__info - Information Magnalister für OrderID 
	function dmc_get_magnalister__info($order_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_get_magnalister__info \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		$link=dmc_db_connect();
		$query= "SELECT internaldata FROM " . DB_TABLE_PREFIX . "magnalister_orders WHERE internaldata like '%addCost%' AND orders_id = $order_id LIMIT 1";
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['internaldata']<>'')
			$image_id=$results['internaldata'];
		else 
			$image_id=0;
		return $image_id;
	} // dmc_get_magnalister__info
	
	// 23.10.2017 - dmc_get_gm_order_factoring_information - Information Gambio PayPal Factoring für OrderID 
	function dmc_get_gm_order_factoring_information($order_id,$wert) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_get_gm_order_txt_factoring_information \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		$link=dmc_db_connect();
		$query= "SELECT gm_order_txt FROM " . DB_TABLE_PREFIX . "orders WHERE gm_order_txt like '%Factoring%' AND orders_id = $order_id LIMIT 1";
		dmc_sql_query($sql_query);
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['gm_order_txt']<>'') {
			$wert=$results['gm_order_txt'];
			if ($wert=='') {
				// Alle Infos ab Betrag bis Zahlbar bis
				$gm_order_txt=substr($wert,(strpos($wert,"Betrag:")),strpos($wert,"Zahlbar bis:")-(strpos($wert,"Betrag:")+8));
				$gm_order_txt=str_replace("IBAN:","\r\nIBAN:",$gm_order_txt);
				$gm_order_txt=str_replace("BIC:","\nBIC:",$gm_order_txt);
				$gm_order_txt=str_replace("Kreditinstitut:","\r\nKreditinstitut:",$gm_order_txt);
				$gm_order_txt=str_replace("Verwendungszweck:","\r\nVerwendungszweck:",$gm_order_txt);
			} else if ($wert=='betrag') {
				// Nur Betrag 
				$gm_order_txt=substr($wert,(strpos($wert,"Betrag:")),strpos($wert,"IBAN:")-(strpos($wert,"Betrag:")));
			} else if ($wert=='iban') {
				// nur IBAN:
				$gm_order_txt=substr($wert,(strpos($wert,"IBAN:")),strpos($wert,"BIC:")-(strpos($wert,"IBAN:")));
			} else if ($wert=='bic') {
				// Nur BIC
				$gm_order_txt=substr($wert,(strpos($wert,"BIC:")),strpos($wert,"nKreditinstitut:")-(strpos($wert,"BIC:")));
			} else if ($wert=='kreditinstitut') {
				// Kreditinstitut:
				$gm_order_txt=substr($wert,(strpos($wert,"Kreditinstitut:")),strpos($wert,"Verwendungszweck:")-(strpos($wert,"Kreditinstitut:")));
			} else if ($wert=='verwendungszweck') {
				// verwendungszweck:
				$gm_order_txt=substr($wert,(strpos($wert,"Verwendungszweck")),strpos($wert,"Zahlbar")-(strpos($wert,"nVerwendungszweck")));
			} else if ($wert=='zahlbar') {
				// Zahlbar bis:
				$gm_order_txt=substr($wert,(strpos($wert,"Zahlbar")),strpos($wert,"Zaun-Nagel hat die Forderung")-(strpos($wert,"Zahlbar")));
			}
		} else {
			$gm_order_txt="";
		}
		// Auffuellen auf 74 Stellen
		$gm_order_txt ="<![CDATA[".str_pad($gm_order_txt, 74, ' ', STR_PAD_RIGHT)."]]>";			
		
		return $gm_order_txt;
	} // dmc_get_gm_order_txt_factoring_information
	
	// 23.10.2017 - dmc_get_gm_order_finanzierung_information - Information Gambio PayPal Finanzierung für OrderID 
	function dmc_get_gm_order_finanzierung_information($order_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_get_gm_order_finanzierung_information \n"); 
		// IDs der zugeordneten Bilder ermitteln
		// Hauptbilder IDs
		$link=dmc_db_connect();
		$finanzierungstext="";
		// Finanzierungskosten
		$query= "SELECT value FROM " . DB_TABLE_PREFIX . "orders_total WHERE title='<br>Finanzierungkosten:' AND class = 'ot_paypal3_instfee' AND orders_id = $order_id LIMIT 1";
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['value']<>'') {
			$wert=$results['value'];
			$finanzierungstext="Finanzierungkosten: ".$wert."Euro; ";
		}
		// Gesamtbetrag
		// Finanzierungskosten
		$query= "SELECT value FROM " . DB_TABLE_PREFIX . "orders_total WHERE title like '%Gesamtbetrag%' AND class = 'ot_paypal3_instfee' AND orders_id = $order_id LIMIT 1";
		$do_query = mysqli_query($link,$query);
		$results = mysqli_fetch_assoc($do_query);
		if ($results['value']<>'') {
			$wert=$results['value'];
			$finanzierungstext .= "Gesamtbetrag: ".$wert."Euro; ";
		}
		return $finanzierungstext;
	} // dmc_get_gm_order_finanzierung_information
	
	// 26.06.2018 - dmc_get_order_product_value_tax_and_order_id - Bestellwert Artikel nach Steuersatz
	function dmc_get_order_product_value_tax_and_order_id($order_id,$tax_id) {
		global $dateihandle, $link;
		if (DEBUGGER>=50) fwrite($dateihandle, "dmc_get_order_product_value_tax_and_order_id for order=$order_id and tax_id=$tax_id \n"); 
		
		$link=dmc_db_connect();
		
		$gesamtwert=0;
		$query="";
		
		if (strtolower(SHOPSYSTEM) == 'woocommerce') {
			// $tax_id '' oder 'reduced-rate' im Standard
			$query= "SELECT (SELECT meta_value FROM wp_woocommerce_order_itemmeta WHERE  meta_key='_line_total' AND order_item_id=oi.order_item_id) AS final_price ".
				"FROM wp_woocommerce_order_items AS oi WHERE oi.order_item_type='line_item' AND oi.order_id = '$order_id' ".
				"AND (SELECT meta_value FROM wp_woocommerce_order_itemmeta WHERE  meta_key='_tax_class' AND order_item_id=oi.order_item_id)= '$tax_id'";
		} else {
			if (DEBUGGER>=50) fwrite($dateihandle, "NOT implementated for ".SHOPSYSTEM." yet\n"); 
		}
		if ($query!="") {
			$do_query = mysqli_query($link,$query);
			while ($results = mysqli_fetch_assoc($do_query)) {
				// echo "Bildname=$image_name mit ID=".$results['post_id']."<br>\n";
				if ($results['final_price']<>'') {
					$gesamtwert += $results['final_price'];
					
				}
			}		// end while
		}
		return $gesamtwert;
	} // dmc_get_order_product_value_tax_and_order_id
	
	
	
	
?>