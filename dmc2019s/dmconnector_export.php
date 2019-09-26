<?php

/******************************************************************************************
*                                                                                          									*
*  dmConnector export for xtc tec shop											*
*  Copyright (C) 2009 DoubleM-GmbH.de											*
*                                                                                          									*
*******************************************************************************************/

// Erweitert am 220109 um PRODUCTS_POS
// Erweitert am 120209 um COUNTRY_ISO_CODE
// Erweitert am 200209 um Rabatt Modul ot_discount und Bonus Madul ot_bonus_fee und Vokasse modul ot_payment
// Erweiterung um Update Order Status
// Erweiterung um Abfrage Aulsandskunden /EG Kunden / Netto Kunden
// 071210 -> if (SHOPSYSTEM != 'veyton') {
// 270411 - kompatibilitaet presta 1.3.7
// 10.08.2012 - Unterstuetzung mehrere Order Status mit ORDER_STATUS_GET durch @ getrennter Order Status
// 10.08.2012 - // Nur eine bestimmte Anzahl an Bestellungen abrufen - Parameter noOfOrder
// 08.12.2012 - Erweitert zur Unterstuetzung von Virtuemart 2
// 31.03.2015 - Zusaetzliche Informationen zum Bestellabruf vom JAVA Programm verarbeiten

	ini_set("display_errors", 1);
	error_reporting(E_ERROR);
	define('_DMC_ACCESSIBLE',true);
	// dmc configure
	include('conf/definitions.inc.php');
	include('conf/configure_export.php');
//	include('conf/configure_export.php');

	// GGLS subshopID fuer SHOPWARE angeben. wenn nicht von allen Shops abgerufen werden soll
	// mehrere durch , getrennt. D.h. zB '' oder '1' oder '1,4'
	$subshopID='';
	
	if (DEBUGGER>=1)
	{ 
		date_default_timezone_set('Europe/Berlin');
		$datum = date("d.m.Y");
		$uhrzeit = date("H:i");
		$daten = "\n***********************************************************************\n";
		$daten .= "************************* dmconnector Shop *****************************\n";
		$daten .= "***********************************************************************\n";
		$daten .= $datum." - ".$uhrzeit." Uhr\n";
		if (LOG_ROTATION=='size' && is_numeric(LOG_ROTATION_VALUE)) {
			if (!file_exists(LOG_DATEI)) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else if ((filesize(LOG_DATEI)/1048576)>LOG_ROTATION_VALUE) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File neu erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else {
				$dateihandle = fopen(LOG_DATEI,"a");
			} 
		} else {
			if (!file_exists(LOG_DATEI)) {
				$dateihandle = fopen(LOG_DATEI,"w"); // LOG File erstellen
				fwrite($dateihandle, "<?php \n exit;");
			} else {
				$dateihandle = fopen(LOG_DATEI,"a");
			} 	
		}
		fwrite($dateihandle, $daten);	
		
	}
	
	// Laufzeit
	$beginn = microtime(true); 
		
	// 080916 - Bereich ...	require('conf/configure_shop_woocommerce.php');
	// integriert in conf/definitions.inc.php

	
		// fwrite($dateihandle, "76 - user $user ".$_POST['user']." - ".$_GET['user']." password $password \n");	
	include('./functions/dmc_functions.php');
	include('./functions/dmc_db_functions.php');
	// Uebergebene Daten loggen
	if (DEBUGGER>=1 && PRINT_POST) print_post($dateihandle);
	  // connect to database
		// Uebergebene Daten
	$action = (isset($_POST['action']) ?
	  $_POST['action'] : $_GET['action']);

	$user = (isset($_POST['user']) ?
	  $_POST['user'] : $_GET['user']);

	$password = (isset($_POST['password']) ?
	  $_POST['password'] : $_GET['password']);
	  
	// fwrite($dateihandle, "94 - user $user ".$_POST['user']." - ".$_GET['user']." password $password \n");	
	  
	// Abruch wenn kein korrektes login
	if (!CheckLogin($user,$password))
		exit;

	if ($action) {
		switch ($action) {
		case 'categories_export':
				//if (SET_TIME_LIMIT==1) xtc_set_time_limit(0);
				$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
				'<CATEGORIES>' . "\n";

				echo $schema;
				//$schema .=  '</CATEGORIES></xml>' . "\n";
				//echo $schema;
				//break;
				 $SPRACHE_ID=2;
				 
				if (strtolower(SHOPSYSTEM) == 'woocommerce') {
					// SELECT * FROM tss_terms AS tt INNER JOIN `tss_term_taxonomy` AS ttt ON  tt.term_id=tt.term_id WHERE ttt.taxonomy='product_cat' ORDER BY `parent` DESC
					
					$query = "SELECT tt.term_id AS categories_id, '' AS categories_image, ttt.parent AS parent_id, '' AS sort_order, '' AS date_added,".
						" '' AS last_modified, ttt.count AS no_of_subs,".
						" tt.name AS categories_name, ttt.description AS categories_description,".
						" IFNULL((SELECT term_id FROM " . DB_PREFIX."term_taxonomy WHERE term_id=ttt.parent LIMIT 1),0) AS parent_parent_id ".
						" FROM " . DB_PREFIX."terms AS tt INNER JOIN " . DB_PREFIX."term_taxonomy AS ttt ON tt.term_id=ttt.term_id". 
						" WHERE ttt.taxonomy='product_cat' ORDER BY ttt.parent, tt.term_id ASC";
				} else {
					$query = "SELECT c.categories_id, c.categories_image, c.parent_id, c.sort_order, c.date_added,".
						" c.last_modified, (SELECT count(categories_id) FROM categories WHERE parent_id=c.categories_id) AS no_of_subs,".
						" IFNULL((SELECT categories_id FROM categories WHERE categories_id=c.parent_id LIMIT 1),0) AS parent_parent_id ".
						" FROM " . TABLE_CATEGORIES ." AS c ". 
						" WHERE c.categories_status=1 ORDER BY c.parent_id, c.categories_id";
				}
				
				if (DEBUGGER>=1) fwrite($dateihandle, "cat_query $query\n");
				
				// $schema  = '<CATEGORIES_DATA>' . "\n";
				
				$cat_query=dmc_db_query($query);
				while ($cat = dmc_db_fetch_array($cat_query))
				{
					// SOL
					if ($cat['parent_id']=="0") {
						$cat['main_cat_id'] = $cat['categories_id'];
						$cat['parent_id'] = "EMPTY";
						$cat['level'] = 1;
					} else if ($cat['parent_id']==$cat['parent_parent_id']){
						$cat['main_cat_id'] = $cat['parent_id'];
						$cat['level'] = 2;
					} else {
						$cat['main_cat_id'] = $cat['parent_parent_id'];
						$cat['level'] = 3;
					}	
					if ($cat['no_of_subs']>0)
						$cat['has_sub'] = -1;
					else 
						$cat['has_sub'] = 0;
					
					$schema  = '<CATEGORIES_DATA>' . "\n" .
							 '<ID>' . $cat['categories_id'] . '</ID>' . "\n" .
							 '<PARENT_ID>' . $cat['parent_id'] . '</PARENT_ID>' . "\n" .
							 '<MAIN_CATEGORY_ID>' . $cat['main_cat_id'] . '</MAIN_CATEGORY_ID>' . "\n" .
							 '<IMAGE_URL>' . umlaute_order_export($cat['categories_image']) . '</IMAGE_URL>' . "\n" .
							 '<SORT_ORDER>' . $cat['sort_order'] . '</SORT_ORDER>' . "\n" .
							 '<LEVEL>' . $cat['level'] . '</LEVEL>' . "\n" .
							 '<HAS_SUBCATEGORIES>' . $cat['has_sub'] . '</HAS_SUBCATEGORIES>' . "\n" .
							 '<NO_OF_SUBCATEGORIES>' . $cat['no_of_subs'] . '</NO_OF_SUBCATEGORIES>' . "\n" .
							
							'<DATE_ADDED>' . $cat['date_added'] . '</DATE_ADDED>' . "\n" .
							 '<LAST_MODIFIED>' . $cat['last_modified'] . '</LAST_MODIFIED>' . "\n";
	
					if (strtolower(SHOPSYSTEM) == 'woocommerce') {
							$schema .= "<CATEGORIES_DESCRIPTION ID='DE' CODE='DE' CATNAME='" . ($cat['categories_name']) . "'>\n";
							$schema .= "	<NAME>" . umlaute_order_export(substr($cat['categories_name'],0,45)) . "</NAME>" . "\n";
							$schema .= "	<HEADING_TITLE></HEADING_TITLE>" . "\n";
							$schema .= "	<DESCRIPTION>" . umlaute_order_export($cat['categories_description']) . "</DESCRIPTION>" . "\n";
							$schema .= "	<META_TITLE></META_TITLE>" . "\n";
							$schema .= "	<META_DESCRIPTION></META_DESCRIPTION>" . "\n";
							$schema .= "	<META_KEYWORDS></META_KEYWORDS>" . "\n";
							$schema .= "</CATEGORIES_DESCRIPTION>\n";
					} else {
						$query="select categories_id, language_id,
													   categories_name,
													   categories_heading_title,
													   categories_description,
													   categories_meta_title,
													   categories_meta_description,
													   categories_meta_keywords, " . TABLE_LANGUAGES . ".code as lang_code, " . TABLE_LANGUAGES . ".name as lang_name from " . TABLE_CATEGORIES_DESCRIPTION . "," . TABLE_LANGUAGES .
														   " WHERE " . TABLE_CATEGORIES_DESCRIPTION . ".categories_id=" . $cat['categories_id'] . " AND " . TABLE_LANGUAGES . ".languages_id=" . TABLE_CATEGORIES_DESCRIPTION . ".language_id AND 
														   categories_description.language_id = ".$SPRACHE_ID;
						// if (DEBUGGER>=1) fwrite($dateihandle, "detail_query $query\n");
						$detail_query = dmc_db_query($query);
						while ($details = dmc_db_fetch_array($detail_query))
						{
							$schema .= "<CATEGORIES_DESCRIPTION ID='" . $details["language_id"] ."' CODE='" . $details["lang_code"] . "' NAME='" . $details["lang_name"] . "'>\n";
							$schema .= "<NAME>" . umlaute_order_export($details['categories_name']) . "</NAME>" . "\n";
							$schema .= "<HEADING_TITLE>" . umlaute_order_export($details["categories_heading_title"]) . "</HEADING_TITLE>" . "\n";
							$schema .= "<DESCRIPTION>" . umlaute_order_export($details["categories_description"]) . "</DESCRIPTION>" . "\n";
							$schema .= "<META_TITLE>" . umlaute_order_export($details["categories_meta_title"]) . "</META_TITLE>" . "\n";
							$schema .= "<META_DESCRIPTION>" . umlaute_order_export($details["categories_meta_description"]) . "</META_DESCRIPTION>" . "\n";
							$schema .= "<META_KEYWORDS>" . umlaute_order_export($details["categories_meta_keywords"]) . "</META_KEYWORDS>" . "\n";
							$schema .= "</CATEGORIES_DESCRIPTION>\n";
						}
				  
						// Produkte in dieser Categorie auflisten
					  
					  
						$prod2cat_query = dmc_db_query("select categories_id, products_id from " . TABLE_PRODUCTS_TO_CATEGORIES .
													 " where categories_id='" . $cat['categories_id'] . "'");
												   
						while ($prod2cat = dmc_db_fetch_array($prod2cat_query))
						{
							$schema .="<PRODUCTS ID='" . $prod2cat["products_id"] ."'></PRODUCTS>" . "\n";
						}
					}
						
				  
					$schema .= '</CATEGORIES_DATA>' . "\n";
					
					echo $schema;
				}
				$schema = '</CATEGORIES>' . "\n";

				echo $schema;
				break;

		case 'manufacturers_export':
			

			$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
					  '<MANUFACTURERS>' . "\n";
			echo $schema;
		  
			$cat_query = dmc_db_query("select manufacturers_id, manufacturers_name, manufacturers_image, date_added, last_modified ".
							 " from " . TABLE_MANUFACTURERS . " order by manufacturers_id");

			while ($cat = dmc_db_fetch_array($cat_query)) {
				$schema  = '<MANUFACTURERS_DATA>' . "\n" .
					 '<ID>' . $cat['manufacturers_id'] . '</ID>' . "\n" .
					 '<NAME>' . htmlspecialchars($cat['manufacturers_name']) . '</NAME>' . "\n" .
					 '<IMAGE>' . htmlspecialchars($cat['manufacturers_image']) . '</IMAGE>' . "\n" .
					 '<DATE_ADDED>' . $cat['date_added'] . '</DATE_ADDED>' . "\n" .
					 '<LAST_MODIFIED>' . $cat['last_modified'] . '</LAST_MODIFIED>' . "\n";
			 
  
				$detail_query = dmc_db_query("select manufacturers_id, mi.languages_id, manufacturers_url, url_clicked, date_last_click, l.code AS lang_code, l.name AS lang_name from " . TABLE_MANUFACTURERS_INFO . " mi, " . TABLE_LANGUAGES .
									   " l where mi.manufacturers_id='" . $cat['manufacturers_id'] . "' and l.languages_id = mi.languages_id");

				while ($details = dmc_db_fetch_array($detail_query)) {
				   $schema .= "<MANUFACTURERS_DESCRIPTION ID='" . $details["languages_id"] ."' CODE='" . $details["lang_code"] . "' NAME='" . $details["lang_name"] . "'>\n";
					$schema .= "<URL>" . htmlspecialchars($details["manufacturers_url"]) . "</URL>" . "\n" ;
					$schema .= "<URL_CLICK>" . $details["url_clicked"] . "</URL_CLICK>" . "\n" ;
					$schema .= "<DATE_LAST_CLICK>" . $details["date_last_click"] . "</DATE_LAST_CLICK>" . "\n" ;
					$schema .= "</MANUFACTURERS_DESCRIPTION>\n";
				}
				$schema .= '</MANUFACTURERS_DATA>' . "\n";
				echo $schema;
			} //end while_aussen
			$schema = '</MANUFACTURERS>' . "\n";
			echo $schema;
			break;
		
		// BESTELLIUNGEN exportieren
		case 'orders_export':
				echo "<!-- dmc --> \n";
				/*
				echo '<?xml version="1.0" encoding="UTF-8"?>' ."\n";
				echo '<!DOCTYPE ORDER SYSTEM "openTRANS_ORDER_1_0.dtd">'."\n";
				*/
				$schema ="";
				
		//		$order_from = (isset($_GET['order_from']) ? $_GET['order_from'] : '');
		//		$order_to = (isset($_GET['order_to']) ? $_GET['order_to'] : '');
			//	$order_status = (isset($_GET['order_status']) ? $_GET['order_status'] : '');
				$no_of_orders = (isset($_GET['no_of_orders']) ? $_GET['no_of_orders'] : ''); // Nur eine bestimmte Anzahl an Bestellungen abrufen - Parameter noOfOrder
	
				// Neue Infos
				/* zB    <order_status>pending</order_status>
						<change_status>0</change_status>
						<order_date>05.03.2015-31.12.2015</order_date>
						<order_number>1</order_number>
						&OrderDatum=01.04.2015-31.12.2015&OrderNummer=1&InformCustomer=0&ChangeStatus=0&OrderStatus=pending&user=r@paradies.de&password=********
				*/
				/* order_status */
				if (isset($_POST['OrderStatus']))
					$order_status = $_POST['OrderStatus'];
				else if (isset($_GET['OrderStatus']))
					$order_status = $_GET['OrderStatus'];
				else
					$order_status = "";
				/* change_status */
				if (isset($_POST['ChangeStatus']))
					$change_status = $_POST['ChangeStatus'];
				else if (isset($_GET['ChangeStatus']))
					$change_status = $_GET['ChangeStatus'];
				else
					$change_status = false;
				/* InformCustomer */
				if (isset($_POST['InformCustomer']))
					$notify_customer = $_POST['InformCustomer'];
				else if (isset($_GET['InformCustomer']))
					$notify_customer = $_GET['InformCustomer'];
				else
					$notify_customer = "";
				/* order_number */
				if (isset($_POST['OrderNummer']))
					$order_number = $_POST['OrderNummer'];
				else if (isset($_GET['OrderNummer']))
					$order_number = $_GET['OrderNummer'];
				else
					$order_number = 1;
				/* order_number */
				if (isset($_POST['OrderDatum']))
					$order_date = $_POST['OrderDatum'];
				else if (isset($_GET['OrderDatum']))
					$order_date = $_GET['OrderDatum'];
				else
					$order_date = "";
				
				// Preufe auf Datum und von - bis 
				if ($order_date!="") {
					if (strpos($order_date,'@') !== false) {
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
			
				if (DEBUGGER>=1) fwrite($dateihandle, "orders_export $order_from - $order_to - $order_status start with order $order_number and change Statsu =$change_status and inform = $notify_customer \n");
				
				//	'<ORDER_LIST>'. "\n" ;
				//echo $schema;
				// Bestellungen ermitteln  
				if (strtolower(SHOPSYSTEM) == 'veyton') {
						// Veyton
					$sql =	"select * from " . TABLE_ORDERS . " AS o, ".TABLE_CUSTOMERS_ADDRESSES." AS c where o.customers_id=c.customers_id AND c.address_class='default' AND o.orders_id >= '" . ($order_number) . "'";
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
						$sql ="select * from " . TABLE_ORDERS . " AS o, ".TABLE_CUSTOMERS_ADDRESSES." AS c where o.customers_id=c.customers_id AND c.address_class='default' AND o.orders_id >= '" . FIRST_ORDER_ID . "'";
					// Multishop veyton
					if (defined('ORDER_SHOP_IDS') && (ORDER_SHOP_IDS <> '') ) {
						$shop_id = explode ( '@', ORDER_SHOP_IDS);
						$add_sql = " AND (";
						for($Anzahl = 0; $Anzahl < count($shop_id); $Anzahl++) {     // ShopIDs durchlaufen	 
							if ($Anzahl ==0) $add_sql .= "shop_id=".$shop_id[$Anzahl];
							else $add_sql .= " OR shop_id=".$shop_id[$Anzahl];
						}
						$sql = $sql . $add_sql .")";
					}
					if (DEBUGGER>=1) fwrite($dateihandle, "orders_export veyton sql= $sql");
				} else if (strtolower(SHOPSYSTEM) == 'presta') {
					/*HEUTE*/
					/*id_order 	id_carrier 	id_lang 	id_customer 	id_cart 	id_currency 	
					id_address_delivery 	id_address_invoice 	secure_key 	payment 	conversion_rate 	
					recyclable 	gift 	gift_message 	shipping_number 	total_discounts 	total_paid 	
					total_paid_real 	total_products 	total_products_wt 	total_shipping 	carrier_tax_rate 	
					total_wrapping 	invoice_number 	delivery_number 	invoice_date 	delivery_date 	valid 	
					date_add 	date_upd */
					//$sql =	"SELECT * from " . TABLE_ORDERS . " AS o, ".TABLE_ORDERS_STATUS_HISTORY." AS oh ".
					//		"WHERE o.id_order=oh.id_order AND o.id_order  >= '" . ($order_from) . "'";
					// Unterstuetzung mehrerer Order Status mit ORDER_STATUS_GET durch @ getrennter Order Status
					// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
					// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
					if (DEBUGGER>=1) fwrite($dateihandle, "Presta ORDER_STATUS_GET= ".ORDER_STATUS_GET." \n");
					if ($order_status!="1" && $order_status!="")
						$Bestellstatus = explode ( '@', $order_status);
					else
						$Bestellstatus = explode ( '@', ORDER_STATUS_GET);
						
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
						$FIRST_ORDER_ID=FIRST_ORDER_ID;
				//	else if ($order_from<>"")
				//		$FIRST_ORDER_ID=$order_from;
					else 
						$FIRST_ORDER_ID=1;
					$sql = 	"SELECT * FROM " . TABLE_ORDERS ." o".
							" WHERE (o.current_state  = '".$Bestellstatus[0]."'";
							// zusaetzliche status?
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR o.current_state  = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							} // end if
							$sql .= " ) ";
							
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
						$sql .= " AND o.id_order >= '" . FIRST_ORDER_ID . "'";
			
					// Mehrere durch , getrennt
					if (defined('STORE_ID_EXPORT') && (STORE_ID_EXPORT <> '') && is_numeric(STORE_ID_EXPORT) )
						$sql .= " AND o.id_shop IN (" . STORE_ID_EXPORT . ")";
				
					if ($order_from!="") $sql .= " AND o.date_add>='".$order_from."'";
					if ($order_to!="") $sql .= " AND o.date_add<='".$order_to."'";	
	
							
				/*	$sql = 	"SELECT * FROM " . TABLE_ORDERS ." o".
							" INNER JOIN ".TABLE_ORDERS_STATUS_HISTORY." oh ON (o.id_order = oh.id_order)" .
							" WHERE (oh.id_order_state  = '".$Bestellstatus[0]."'";
							// zusaetzliche status?
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR oh.id_order_state  = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							} // end if
							
							$sql .= " ) AND (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY ." WHERE id_order_state = oh.id_order_state)=o.id_order";
							" AND (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY ." WHERE id_order_state = oh.id_order_state AND id_order=oh.id_order) =".
							" (SELECT MAX(id_order_history) FROM " . TABLE_ORDERS_STATUS_HISTORY .
							"WHERE o.id_order=oh.id_order AND o.id_order  >= '" . $FIRST_ORDER_ID . "'";
					*/ 
					
				} else if (strtolower(SHOPSYSTEM) == 'virtuemart') { 
					$sql =	"SELECT *, o.virtuemart_order_id AS order_id, "." (SELECT payment_name FROM ".DB_PREFIX."virtuemart_paymentmethods_de_de WHERE virtuemart_paymentmethod_id=o.virtuemart_paymentmethod_id) AS payment_method, o.virtuemart_paymentmethod_id AS payment_class, (SELECT shipment_name FROM ".DB_PREFIX."virtuemart_shipmentmethods_de_de WHERE virtuemart_shipmentmethod_id=o.virtuemart_shipmentmethod_id) AS shipping_method, o.virtuemart_shipmentmethod_id AS shipping_class, ".
					"o.coupon_discount AS order_discount_amount ".
					" FROM "  . TABLE_ORDERS . " AS o WHERE o.virtuemart_order_id >= '" . ($order_number) . "'";
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
						$sql .=	" AND o.virtuemart_order_id >= '" . FIRST_ORDER_ID . "'";
							
				
						//$sql =	"SELECT * ".", (SELECT payment_name FROM ".DB_PREFIX."virtuemart_paymentmethods_de_de WHERE virtuemart_paymentmethod_id=o.virtuemart_paymentmethod_id) AS payment_method, (SELECT shipment_name FROM ".DB_PREFIX."virtuemart_shipmentmethods_de_de WHERE virtuemart_shipmentmethod_id=o.virtuemart_shipmentmethod_id) AS shipping_method, ".
						//" o.virtuemart_shipmentmethod_id AS shipping_class, '0' AS order_discount_amount".
						//" FROM "  . TABLE_ORDERS . " AS o WHERE o.virtuemart_order_id >= '" . FIRST_ORDER_ID . "'";
								
					if ($order_from!="") $sql .= " AND o.created_on>='".$order_from."'";
					if ($order_to!="") $sql .= " AND o.created_on<='".$order_to."'";	
	
			
				} else if (strtolower(SHOPSYSTEM) == 'joomshopping') { 
						$sql = "SELECT ".
							"o.user_id AS customer_id, o.email AS customer_email, o.client_type_name AS customer_customergroup, '0' AS customer_newsletter, ".
							"o.order_date AS created_on, ".
							"o.lang AS customer_language, '1' AS customer_shop_ID, '1' AS customer_price_group, ".
							"o.order_id AS order_id, o.order_number AS order_number, o.user_id AS customer_user_id, o.order_total AS order_total, ".
							"o.order_discount AS order_discount_amount, (o.order_total-o.order_tax) AS order_amount_net, 
							o.order_tax AS order_billTaxAmount,".  // Steuer Summe
							" o.order_payment AS order_payment, o.payment_tax AS order_payment_tax, ". 	// Kosten Zahlungsmethode
							" o.order_shipping AS order_shipment, o.order_shipping*(1/(1+shipping_tax/100)) AS order_shipment_net, ". 
							" (o.order_shipping-o.order_shipping*(1/(1+shipping_tax/100))) AS order_shipment_tax, ".
							"o.order_status AS order_status, o.payment_method_id AS payment_class, ".
							"(SELECT payment_code FROM ".DB_PREFIX."jshopping_payment_method WHERE payment_id=o.payment_method_id) AS payment_method, ".
							"(SELECT payment_class FROM ".DB_PREFIX."jshopping_payment_method WHERE payment_id=o.payment_method_id) AS payment_class, ".
							"(SELECT `name_de-DE` FROM ".DB_PREFIX."jshopping_shipping_method WHERE shipping_id=o.shipping_method_id) AS shipping_method, (SELECT alias FROM ".DB_PREFIX."jshopping_shipping_method WHERE shipping_id=o.shipping_method_id) AS shipping_class, ".
							" o.ip_address AS ip_address,".
							"'' AS order_transaction_id, o.order_add_info AS order_comment, '' AS order_customer_comment,  ".
							"'' AS order_trackingcode, o.currency_code_iso AS user_currency_id, o.currency_exchange AS user_currency_rate, '1' AS order_shop_id, ".
							"'' AS order_attributes,  ".
							"'' AS billing_address_id, o.f_name AS billing_company, '' AS billing_company_2,". "o.title AS billing_gender, o.user_id AS billing_customer_number, ".
							"CONCAT(o.f_name,' ',o.m_name) AS billing_firstname, o.l_name AS billing_lastname, CONCAT(o.street,' ',o.street_nr) AS billing_street, ". 
							"o.street_nr AS billing_streetnumber, o.zip AS billing_zip, o.city AS billing_city, ".
							"o.phone AS billing_phone, o.fax AS billing_fax, o.country AS billing_country_id, ".
							"(SELECT `name_de-DE` FROM ".DB_PREFIX."jshopping_countries WHERE country_id=o.country) AS billing_country, ".
							"(SELECT country_code_2 FROM ".DB_PREFIX."jshopping_countries WHERE country_id=o.country) AS billing_country_iso_code, ".
							// "ba.ustid AS billing_vat_id, CONCAT(ba.text1,ba.text2,ba.text3,ba.text4,ba.text5,ba.text6) AS billing_texts,".
							"o.tax_number AS billing_vat_id, '' AS billing_texts,".
						"'' AS delivery_address_id, o.d_f_name AS delivery_company, '' AS delivery_company_2,". "o.d_title AS delivery_gender, '' AS delivery_customer_number, ".
							"CONCAT(o.d_f_name,' ',o.d_m_name) AS delivery_firstname, o.d_l_name AS delivery_lastname, CONCAT(o.d_street,' ',o.d_street_nr) AS delivery_street_address, ". 
							"o.d_street_nr AS delivery_streetnumber, o.d_zip AS delivery_postcode, o.d_city AS delivery_city, ".
							"o.d_phone AS delivery_phone, o.d_fax AS delivery_fax, o.d_country AS delivery_country_id, ".
							"(SELECT `name_de-DE`  FROM ".DB_PREFIX."jshopping_countries WHERE country_id=o.d_country) AS delivery_country, ".
							"(SELECT country_code_2 FROM ".DB_PREFIX."jshopping_countries WHERE country_id=o.d_country) AS delivery_country_iso_code, ".
							"(SELECT country_code_2 FROM ".DB_PREFIX."jshopping_countries WHERE country_id=o.d_country) AS delivery_country_iso_code_2, ".
							//"sa.ustid AS shipping_vat_id, ". 
							"'' AS sdelivery_vat_id, ".
							"'' AS delivery_texts ".
							"FROM ".DB_TABLE_PREFIX ."jshopping_orders AS o ".
							"INNER JOIN ".DB_TABLE_PREFIX ."jshopping_order_status AS status ON status.status_id=o.order_status ";
							
							// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
							if ($order_status!="")
								$Bestellstatus = explode ( '@', $order_status);
							else
								$Bestellstatus = explode ( '@', ORDER_STATUS_GET);
									
							// Order Status 
							$sql .= " WHERE (status.status_code = '".$Bestellstatus[0]."' OR status.`name_de-DE` = '".$Bestellstatus[0]."' OR status.status_id = '".$Bestellstatus[0]."'";
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR status.status_code = '".$Bestellstatus[$Anzahl]."' OR status.`name_de-DE` = '".$Bestellstatus[$Anzahl]."' OR status.status_id = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							}
							$sql .= ") ";
							if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID != '') && is_numeric(FIRST_ORDER_ID) )
								$sql .=	" AND o.order_number >=".FIRST_ORDER_ID;
							
							$sql .=	" AND o.order_number >=".$order_number;
							
							if ($order_from!="") $sql .= " AND o.order_date>='".$order_from."'";
							if ($order_to!="") $sql .= " AND o.order_date<='".$order_to."'";	
				
				} else if (strtolower(SHOPSYSTEM) == 'magento') {
					$sql =	"SELECT * FROM " . DB_TABLE_PREFIX . "sales_order AS o WHERE o.is_active=1 AND o.increment_id >= '" . ($order_number) . "'"; 
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
						$sql =	"SELECT * FROM " . DB_TABLE_PREFIX . "sales_order AS o WHERE o.is_active=1 AND o.increment_id >= '" . FIRST_ORDER_ID . "'";
				} else if (strtolower(SHOPSYSTEM) == 'shopware') {
					// Standard Abfrage für Bestellungen
					$sql = "SELECT ".
							"CASE WHEN ba.customernumber='' THEN o.userID ELSE ba.customernumber END AS customer_id, c.email AS customer_email, c.customergroup AS customer_customergroup, c.newsletter AS customer_newsletter, ".
							// "o.userID AS customer_id, c.email AS customer_email, c.customergroup AS customer_customergroup, c.newsletter AS customer_newsletter, ".
							"LEFT(o.ordertime,10) AS created_on, ".
							"c.language AS customer_language, c.subshopID AS customer_shop_ID, c.pricegroupID AS customer_price_group, ".
							"o.id AS order_id, o.ordernumber AS order_number, o.userID AS customer_user_id, o.invoice_amount AS order_total, ".
							"'0' AS order_discount_amount, o.invoice_amount_net AS order_amount_net, (o.invoice_amount-o.invoice_amount_net) AS order_billTaxAmount,".  // Steuer Summe
							"	'0' AS order_payment, '0' AS order_payment_tax, ". 	// Kosten Zahlungsmethode
							" o.invoice_shipping_net AS order_shipment, o.invoice_shipping AS order_shipment_gros, o.invoice_shipping_net AS order_shipment_net, ". 
							"(o.invoice_shipping -o.invoice_shipping_net) AS order_shipment_tax, ".
							"status.description AS order_status, o.paymentID AS payment_class, ".
							"(SELECT description FROM ".DB_TABLE_PREFIX ."s_core_paymentmeans WHERE id=o.paymentID LIMIT 1) AS payment_method, ".
							"(SELECT name FROM ".DB_TABLE_PREFIX ."s_premium_dispatch where id=o.dispatchID limit 1) AS shipping_method, (SELECT name FROM ".DB_TABLE_PREFIX ."s_premium_dispatch where id=o.dispatchID limit 1) AS shipping_class, '' AS ip_address,".
							"o.transactionID AS order_transaction_id, CASE WHEN o.comment='' THEN o.customercomment ELSE o.comment END AS order_comment, o.customercomment AS order_customer_comment,  ".
							"o.trackingcode AS order_trackingcode, o.currency AS user_currency_id, '1' AS user_currency_rate, o.subshopID AS order_shop_id, ".
							//"CONCAT(o.o_attr1,o.o_attr2,o.o_attr3,o.o_attr3,o.o_attr4,o.o_attr5,o.o_attr6) AS order_attributes,  ".
							"'' AS order_attributes,  ".
							"ba.userID AS billing_address_id, ba.company AS billing_company, ba.department AS billing_company_2,". "ba.salutation AS billing_gender, ba.customernumber AS billing_customer_number, ".
							"ba.firstname AS billing_firstname, ba.lastname AS billing_lastname, ba.street AS billing_street, ". 
							"ba.additional_address_line1 AS billing_zusatz_1, ba.additional_address_line2 AS billing_zusatz_2, ".
							// Mit hausnummer - streetnumber wohl bis Version 5
							// "ba.streetnumber AS billing_streetnumber, ba.zipcode AS billing_zip, ba.city AS billing_city, ".
							"'' AS billing_streetnumber, ba.zipcode AS billing_zip, ba.city AS billing_city, ".
							
							//	mit FAX 
							// "ba.phone AS billing_phone, ba.fax AS billing_fax, ba.countryID AS billing_country_id, ".
							// OHNE FAX (Standard?)
							"ba.phone AS billing_phone, '' AS billing_fax, ba.countryID AS billing_country_id, ".
							"(SELECT countryname FROM s_core_countries WHERE id=ba.countryID) AS billing_country, ".
							"(SELECT countryiso FROM s_core_countries WHERE id=ba.countryID) AS billing_country_iso_code, ".
							// "ba.ustid AS billing_vat_id, CONCAT(ba.text1,ba.text2,ba.text3,ba.text4,ba.text5,ba.text6) AS billing_texts,".
							"ba.ustid AS billing_vat_id, '' AS billing_texts,".
							"sa.userID AS delivery_address_id, sa.company AS delivery_company, sa.department AS delivery_company_2,". "sa.salutation AS delivery_gender, '' AS delivery_customer_number, ".
							// Mit hausnummer - streetnumber wohl bis Version 5 
						//	"sa.firstname AS delivery_firstname, sa.lastname AS delivery_lastname, concat(sa.street,' ',sa.streetnumber) AS delivery_street_address, ". "sa.streetnumber AS delivery_streetnumber, sa.zipcode AS delivery_postcode, sa.city AS delivery_city, ".
							"sa.firstname AS delivery_firstname, sa.lastname AS delivery_lastname, sa.street AS delivery_street_address, ". "'' AS delivery_streetnumber, sa.zipcode AS delivery_postcode, sa.city AS delivery_city, ".
							// "sa.phone AS delivery_phone, sa.fax AS delivery_fax, ".
							"'' AS delivery_phone, '' AS delivery_fax, ".
							"sa.additional_address_line1 AS delivery_zusatz_1, sa.additional_address_line2 AS delivery_zusatz_2, ".
							"sa.countryID AS delivery_country_id, ".
							"(SELECT countryname FROM s_core_countries WHERE id=sa.countryID) AS delivery_country, ".
							"(SELECT countryiso FROM s_core_countries WHERE id=sa.countryID) AS delivery_country_iso_code_2, ".
							//"sa.ustid AS shipping_vat_id, ". 
							"'' AS sdelivery_vat_id, ".
						//	"CONCAT(sa.text1,sa.text2,sa.text3,sa.text4,sa.text5,sa.text6) AS shipping_texts ".
							"'' AS delivery_texts ".
							"FROM ".DB_TABLE_PREFIX ."s_order AS o ".
							"INNER JOIN ".DB_TABLE_PREFIX ."s_order_billingaddress AS ba ON (o.userID=ba.userID  AND ba.orderID=o.ID) ".
							"INNER JOIN ".DB_TABLE_PREFIX ."s_order_shippingaddress AS sa ON (ba.userID=sa.userID AND sa.orderID=o.ID) ".
							"LEFT OUTER JOIN ".DB_TABLE_PREFIX ."s_user AS c ON c.id=sa.userid ".
							"INNER JOIN ".DB_TABLE_PREFIX ."s_core_states AS status ON status.id=o.status ";
							// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
							if ($order_status!="")
								$Bestellstatus = explode ( '@', $order_status);
							else
								$Bestellstatus = explode ( '@', ORDER_STATUS_GET);
									
							// Order Status 
							$sql .= " WHERE (status.description = '".$Bestellstatus[0]."'";
							if ( count($Bestellstatus) > 1) {
								for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR status.description  = '".$Bestellstatus[$Anzahl]."'";
								} // end for
							}
							$sql .= ") ";
							if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID != '') && is_numeric(FIRST_ORDER_ID) )
								$sql .=	" AND o.ordernumber >=".FIRST_ORDER_ID;
							
							if ($order_from!="") $sql .= " AND o.ordertime>='".$order_from."'";
							if ($order_to!="") $sql .= " AND o.ordertime<='".$order_to."'";		
	
							if ($subshopID!="") $sql .= " AND o.subshopID IN (".$subshopID.")";	
		
							// NUR in Rechnung gestellte Bestellungen
							if ($export_type=="rechnungen")
								$sql .=  " AND o.id IN (SELECT orderID FROM s_order_documents where type=1)";
			
				} else if (strtolower(SHOPSYSTEM) == 'woocommerce') {
						
					if (SHOP_VERSION=='2') 	{	// abweichende Logik bei bestimmten Systemen
						$sql = "SELECT ".
								"o.id AS order_id, ".
								"o.post_excerpt AS order_comment, ".		// wie ebay und amazon informationen
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID LIMIT 1) AS customer_id, 
								'0' AS customer_shop_ID, 
								'0' AS customer_price_group, ".
								"o.post_date AS created_on, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_ip_address' AND post_id=o.ID LIMIT 1) AS ip_address, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_number_formatted ' AND post_id=o.ID LIMIT 1) AS order_number, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_currency' AND post_id=o.ID LIMIT 1) AS user_currency_id, ".
								"'1' AS user_currency_rate, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_total' AND post_id=o.ID LIMIT 1) AS order_total, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_tax' AND post_id=o.ID LIMIT 1) AS order_billTaxAmount, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_total' AND post_id=o.ID LIMIT 1)+(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_tax' AND post_id=o.ID LIMIT 1) AS order_amount_net, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping' AND post_id=o.ID LIMIT 1) AS order_shipment, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping_tax' AND post_id=o.ID LIMIT 1) AS order_shipment_net, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping_tax' AND post_id=o.ID LIMIT 1) AS order_shipment_tax, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_discount' AND post_id=o.ID LIMIT 1) AS order_discount_amount, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method_title' AND post_id=o.ID LIMIT 1) AS shipping_method, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method' AND post_id=o.ID LIMIT 1) AS shipping_class, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_payment_method_title' AND post_id=o.ID LIMIT 1) AS payment_method, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_payment_method' AND post_id=o.ID LIMIT 1) AS payment_class, ".
								// spezialanpassung
							/*	"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='donation_beneficiary' AND post_id=o.ID LIMIT 1)  AS donation_beneficiary, ". // Spendenempfänger
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='donation_amount' AND post_id=o.ID LIMIT 1)  AS donation_amount,".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='donation_tax_status' AND post_id=o.ID LIMIT 1)  AS donation_tax_status, ".	// 1 = spender, 2 = sponsering
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_completed_date' AND post_id=o.ID LIMIT 1)  AS completed_date, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_paid_date' AND post_id=o.ID LIMIT 1)  AS _paid_date, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='tss_bt_transfers' AND post_id=o.ID LIMIT 1)  AS tss_bt_transfers, ".
								*/
								// ENDE spezialanpassung
								// WP Lister
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_order_id' AND post_id=o.ID LIMIT 1) AS ebay_order_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_user_id' AND post_id=o.ID LIMIT 1) AS ebay_user_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_account_id' AND post_id=o.ID LIMIT 1) AS ebay_account_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_site_id' AND post_id=o.ID LIMIT 1) AS ebay_site_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_ebay_account_name' AND post_id=o.ID LIMIT 1) AS ebay_account_name, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_wpla_amazon_order_id' AND post_id=o.ID LIMIT 1) AS amazon_order_id, ".
								// WP LISTER ENDE
								"(SELECT meta_value FROM ".DB_PREFIX."woocommerce_order_items Inner JOIN ".DB_PREFIX."woocommerce_order_itemmeta on ".DB_PREFIX."woocommerce_order_items.order_item_id = ".DB_PREFIX."woocommerce_order_itemmeta.order_item_id where order_id= o.ID && order_item_type = 'coupon' AND meta_key = 'discount_amount') AS couponbetrag, " .
								"o.post_status AS order_status, '0' AS order_payment, '0' AS order_payment_tax ". 	// Kosten Zahlungsmethode				
							"FROM `" . DB_PREFIX . "posts` AS o ".
							"WHERE  o.post_type = 'shop_order'  AND o.id >= " . ($order_number) . " ";
							if ($order_from!="") $sql .= " AND o.post_date>='".$order_from."'";
							if ($order_to!="") $sql .= " AND o.post_date<='".$order_to."'";	
											
					} else {
						// Standard
						$sql = "SELECT ".
								"o.id AS order_id, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS customer_id, 
								'0' AS customer_shop_ID, 
								'0' AS customer_price_group, ".
								"o.post_date AS created_on, ".
								"(SELECT meta_value FROM `wp_rserdqzhui_postmeta` WHERE meta_key='_customer_ip_address' AND post_id=o.ID LIMIT 1) AS ip_address,".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_number_formatted ' AND post_id=o.ID LIMIT 1) AS order_number, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_currency' AND post_id=o.ID) AS user_currency_id, ".
								"'1' AS user_currency_rate, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_total' AND post_id=o.ID) AS order_total, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_tax' AND post_id=o.ID) AS order_billTaxAmount, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_total' AND post_id=o.ID)-(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_tax' AND post_id=o.ID) AS order_amount_net, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping' AND post_id=o.ID) AS order_shipment, ".
								"(SELECT meta_value*0.840336 FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping' AND post_id=o.ID) AS order_shipment_net, ".
								"(SELECT (meta_value-(meta_value*0.840336)) FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping' AND post_id=o.ID) AS order_shipment_tax, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_discount' AND post_id=o.ID) AS order_discount_amount, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method_title' AND post_id=o.ID) AS shipping_method, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method' AND post_id=o.ID) AS shipping_class, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_payment_method_title' AND post_id=o.ID) AS payment_method, ".
								"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_payment_method' AND post_id=o.ID) AS payment_class, ".
								"o.post_status AS order_status, '0' AS order_payment, '0' AS order_payment_tax ". 	// Kosten Zahlungsmethode	
								
							"FROM `" . DB_PREFIX . "posts` AS o ".
							"INNER JOIN " . DB_PREFIX . "term_relationships AS tr ON o.id=tr.object_id ".
							"INNER JOIN " . DB_PREFIX . "term_taxonomy AS tt ON tr.term_taxonomy_id=tt.term_taxonomy_id ".
							"INNER JOIN " . DB_PREFIX . "terms AS t ON tt.term_id=t.term_id ".
							"WHERE o.post_status='publish' AND o.post_type = 'shop_order'  AND o.id >= " . ($order_number) . " ";
							if ($order_from!="") $sql .= " AND o.post_date>='".$order_from."'";
							if ($order_to!="") $sql .= " AND o.post_date<='".$order_to."'";	
													
					}
						
						// Ab einer bestimmten ID
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID != '') && is_numeric(FIRST_ORDER_ID) )
						$sql .=	" AND o.id >=".FIRST_ORDER_ID;// AND 

					// Order Status woocommerce select tt.term_taxonomy_id, t.name FROM wp_term_taxonomy AS tt INNER JOIN wp_terms AS t ON  tt.term_id=t.term_id AND tt.taxonomy ='shop_order_status'
				/*	if (SHOP_VERSION=='2') 	{	// abweichende Logik bei bestimmten Systemen
						$sql .= " AND (o.post_status = '".$Bestellstatus[0]."'";
						if ( count($Bestellstatus) > 1) {
							for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
								$sql .= " OR o.post_status  = '".$Bestellstatus[$Anzahl]."'";
							} // end for
						}
						$sql .= ") ";
					} else { // Standard	
						// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
						if ($order_status!="")
							$Bestellstatus = explode ( '@', $order_status);
						else
							$Bestellstatus = explode ( '@', ORDER_STATUS_GET);
							// Bestellstatus
						$sql .=	 " AND (t.name = '".$Bestellstatus[0]."'";
						if ( count($Bestellstatus) > 1) {
							for($Anzahl = 1; $Anzahl < count($Bestellstatus); $Anzahl++) {     // Bestellstatus durchlaufen	     
								$sql .= " OR t.name = '".$Bestellstatus[$Anzahl]."'";
							} // end for
						}
						$sql .= ") ";
					}*/
				} else {
					$sql =	"SELECT * from " . TABLE_ORDERS . " AS o WHERE o.orders_id >= '" . ($order_number) . "'";
					if (defined('FIRST_ORDER_ID') && (FIRST_ORDER_ID <> '') && is_numeric(FIRST_ORDER_ID) )
					$sql = "select * from " . TABLE_ORDERS . " AS o WHERE o.orders_id >= '" . FIRST_ORDER_ID . "' AND o.orders_id >= '" . $order_number . "'";
					if ($order_from!="") $sql .= " AND o.date_purchased >= '".$order_from."'";
					if ($order_to!="") $sql .= " AND o.date_purchased <= '".$order_to."'";					
				}//ende if-else
				
				$order_paypal_factoring_info="";
				$order_paypal_factoring_info_betrag="";
				$order_paypal_factoring_info_iban="";
				$order_paypal_factoring_info_bic="";
				$order_paypal_factoring_info_kreditinstitut="";
				$order_paypal_factoring_info_verwendungszweck="";
				$order_paypal_factoring_info_zahlbar="";
				$order_paypal_finanzierungs_info="";
				
				$robin=true;
				if ($robin==true) {
				// if (!isset($order_status) && !isset($order_from)) {
					// Order Status aus Definitionsdatei 
					// Unterstuetzung mehrere Order Status mit ORDER_STATUS_GET durch @ getrennter Order Status
					// Wenn von JAVA order_status ubergeben, dann ueberschreibt dieses ORDER_STATUS_GET
					if ($order_status!="")
						$order_status = explode ( '@', $order_status);
					else
						$order_status = explode ( '@', ORDER_STATUS_GET);
					
					if (strtolower(SHOPSYSTEM) == 'presta' || SHOPSYSTEM == 'shopware' || SHOPSYSTEM == 'joomshopping') {
						// Status hierfuer siehe oben
					} else if (strtolower(SHOPSYSTEM) == 'virtuemart') {
						$sql .= " AND o.order_status = '" . $order_status[0]."'"; 	// Standard = 'U'
					} else if (strtolower(SHOPSYSTEM) == 'magento') {			// bei Magento o.status
						$sql .= " AND (o.status = '" . $order_status[0]."'"; 	// Standard = 'pending'
						// zusaetzliche status?
						if ( count($order_status) > 1) {
							for($Anzahl = 1; $Anzahl < count($order_status); $Anzahl++) {     // Bestellstatus durchlaufen	     
								$sql .= " OR o.status  = '".$order_status[$Anzahl]."'";
							} // end for
						} // end if
						$sql .= ")";	// Ende or orders_status ...
					} else if (strtolower(SHOPSYSTEM) == 'woocommerce') {		
						// Order Status woocommerce select tt.term_taxonomy_id, t.name FROM wp_term_taxonomy AS tt INNER JOIN wp_terms AS t ON  tt.term_id=t.term_id AND tt.taxonomy ='shop_order_status'
						if (SHOP_VERSION=='2') 	{	// abweichende Logik bei bestimmten Systemen
							$sql .= " AND (o.post_status = '".$order_status[0]."'";
							if ( count($order_status) > 1) {
								for($Anzahl = 1; $Anzahl < count($order_status); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR o.post_status  = '".$order_status[$Anzahl]."'";
								} // end for
							}
							$sql .= ") ";
						} else { // Standard	
							$sql .= " AND (t.name = '".$order_status[0]."'";
							if ( count($order_status) > 1) {
								for($Anzahl = 1; $Anzahl < count($order_status); $Anzahl++) {     // Bestellstatus durchlaufen	     
									$sql .= " OR t.name  = '".$order_status[$Anzahl]."'";
								} // end for
							}
							$sql .= ") ";
						}
						 $sql .= " LIMIT 1000";	
					} else {
						$sql .= " AND (o.orders_status = " . $order_status[0];
						// zusaetzliche status?
						if ( count($order_status) > 1) {
							for($Anzahl = 1; $Anzahl < count($order_status); $Anzahl++) {     // Bestellstatus durchlaufen	     
								$sql .= " OR o.orders_status   = '".$order_status[$Anzahl]."'";
							} // end for
						} // end if
						$sql .= ")";	// Ende or orders_status ...
						
					} 
				} else if ($order_status!='') {
					// order_status durch Schnittstelle übergeben
					if (strtolower(SHOPSYSTEM) == 'presta') {
						//$sql .= " and oh.id_order_state = " . $order_status; 	// Standard = 1
					} else if (strtolower(SHOPSYSTEM) == 'virtuemart'  || SHOPSYSTEM=='joomshopping') {
						$sql .= " and o.order_status = '" . $order_status."'"; 	// Standard = 'P'
					} else if (strtolower(SHOPSYSTEM) == 'magento' || SHOPSYSTEM=='shopware' ) {			// bei Magento und Shopware o.status
						$sql .= " and o.status = '" . $order_status."'"; 	// Standard = 'U'
					} else {
						$sql .= " and (o.orders_status = " . $order_status . ")";	
					} 
				} // end if 
				
				// Anzahl der abzurufenden Bestellungen eingrenzen
				if ($no_of_orders<>"" && is_numeric($no_of_orders)) {
					//$sql .= " LIMIT ".$no_of_orders;
				}
			//		$sql .= " LIMIT 3";
			
				//  Mindenmengenzuschlag
				$mindermengenzuschlagstitel='';
				$rcm_mindermengenzuschlag_net=0;
				$rcm_mindermengenzuschlag_tax_id =0;
				$rcm_mindermengenzuschlag_tax_rate =0;
				$rcm_mindermengenzuschlag = $rcm_mindermengenzuschlag_gros =0;
				$rcm_mindermengenzuschlag_tax=0;	
				$shipping_tax_rate=0;
				$currency_value=1;		
				$customer_shop_ID=0;
				//if (DEBUGGER>=1) fwrite($dateihandle, "440 - orders_export sql= $sql\n");
				$exists = false;		// Bestellungen vorhanden
				$orders_query = dmc_db_query($sql);
				while ($orders = dmc_db_fetch_array($orders_query)) {
					$exists = true;
					// get customer, invoice and delivery information
					if (strtolower(SHOPSYSTEM) == 'presta') {
						// presta
						// customer and billing address
						$cust_sql = "SELECT ".
						"ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ".
						"ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, ".
						"c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , ".
						"c.lastname AS customers_lastname, c.firstname AS customers_firstname, ".
						"cgd.name AS customers_group, ca.vat_number AS vat_number ". // vat_number bei presta ggfls nicht vorhanden
						"FROM " . TABLE_CUSTOMERS_ADDRESSES . " AS ca, " . TABLE_CUSTOMERS." AS c, ".
						TABLE_CUSTOMERS_GROUP. " AS cg, " . TABLE_GROUP_DESC ." AS cgd ".
						"WHERE c.id_customer=ca.id_customer AND c.id_customer=cg.id_customer AND cg.id_group=cgd.id_group".
						" AND cgd.id_lang=".STD_LANGUAGE_ID." ".
						" AND c.id_customer=" . $orders['id_customer']." ".
						" AND ca.id_address=".$orders['id_address_invoice'];
						//if (DEBUGGER>=1) fwrite($dateihandle, "Presta Kundenabfrage".$cust_sql);
						$cust_query = dmc_db_query ($cust_sql);
						if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query))) {
							// Presta customer is only defined by name, email and birthday
							// Customer and invoice address
							$customers_cid="";
							$customers_group=$orders['customers_group'];
							$customers_dob=$billing_dob=$cust_data['customers_dob'];
							$customers_ustid=$billing_ustid=$cust_data['vat_number'];	// bei presta ggfls nicht vorhanden
							$customers_title=$billing_title=$cust_data['customers_gender'];
							$customers_id=$billing_id=$cust_data['id_customer'];
							$customers_address_id=$billing_address_id=$cust_data['id_address'];
							$customers_country_id=$billing_country_id=$cust_data['id_country'];
							$customers_company=$billing_company=$cust_data['company'];
							$customers_company=$billing_company2="";		
							$customers_lastname=$cust_data['customers_lastname'];
							$billing_lastname=$cust_data['lastname'];						// billing <> customer ?
							$customers_firstname=$cust_data['customers_firstname'];
							$billing_firstname=$cust_data['firstname'];						// billing <> customer ?
							$customers_address1=$billing_address1=$cust_data['address1'];
							$customers_address2=$billing_address2=$cust_data['address2'];
							$customers_zip=$billing_zip=$cust_data['postcode'];
							$customers_city=$billing_city=$cust_data['city'];
							$customers_suburb=$billing_suburb="";
							$customers_country=$billing_country=presta_get_country_by_id($cust_data['id_country'],STD_LANGUAGE_ID,$dateihandle);
							$customers_country_iso_code=$billing_country_iso_code=presta_get_isocode_by_id($cust_data['id_country'],$dateihandle);	
							$customers_state=$billing_state="";						// todo presta
							$customers_phone=$billing_phone=$cust_data['phone'];
							$customers_phone_mobile=$billing_phone_mobile=$cust_data['phone_mobile'];
							$customers_email_address=$billing_email_address=$cust_data['customers_email_address'];
						} // end if 
						// delivery address
						$cust_sql = "SELECT ".
							"ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ".
							"ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, ".
							"c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , ".
							"c.lastname AS customers_lastname, c.firstname AS customers_firstname ".
							"FROM " . TABLE_CUSTOMERS_ADDRESSES . " AS ca, " . TABLE_CUSTOMERS." AS c ".
							"WHERE c.id_customer=ca.id_customer AND c.id_customer=" . $orders['id_customer']." ".
							" AND ca.id_address=".$orders['id_address_delivery'];
			
						$cust_query = dmc_db_query ($cust_sql);
						if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query))) {
							// Presta customer is only defined by name, email and birthday
							$delivery_dob =  $cust_data['customers_dob'];
							$delivery_ustid = "";	// bei presta nicht vorhanden
							$delivery_title = $cust_data['customers_gender'];
							$delivery_id=$cust_data['id_customer'];
							$delivery_address_id=$cust_data['id_address'];
							$delivery_country_id=$cust_data['id_country'];
							$delivery_company=$cust_data['company'];
							$delivery_company2="";		
							$delivery_lastname=$cust_data['lastname'];
							$delivery_firstname=$cust_data['firstname'];
							$delivery_address1=$cust_data['address1'];
							$delivery_address2=$cust_data['address2'];
							$delivery_zip=$cust_data['postcode'];
							$delivery_city=$cust_data['city'];
							$delivery_suburb="";
							$delivery_country=presta_get_country_by_id($cust_data['id_country'],STD_LANGUAGE_ID,$dateihandle);
							$delivery_country_iso_code=presta_get_isocode_by_id($cust_data['id_country'],$dateihandle);	
							$delivery_state="";						// todo presta
							$delivery_phone=$cust_data['phone']; 
							$delivery_phone_mobile=$cust_data['phone_mobile'];
							$delivery_email_address=$cust_data['customers_email_address'];
						}//end delivery query
					} else if (strtolower(SHOPSYSTEM) == 'virtuemart') {
						// virtuemart
						$orders['order_id']=$orders['virtuemart_order_id'];
						// customer billing address
						$cust_sql = "SELECT ".
						"ca.virtuemart_user_id AS customers_cid, 'std' AS customers_group, '' AS customers_dob, '' AS customers_ustid,
						ca.title AS customers_title,  ca.virtuemart_user_id AS  customers_id, ca.virtuemart_user_id AS customers_address_id, 
						ca.company AS customers_company, ca.last_name AS customers_lastname, concat(`first_name`,' ',IFNULL(`middle_name`,'')) AS customers_firstname, 
						ca.address_'1' AS customers_address1, ca.address_2 AS customers_address2, ca.zip AS customers_zip, ca.city AS customers_city, 
						'' AS customers_suburb,country.country_name AS customers_country, country.country_2_code AS customers_country_iso_code,
						virtuemart_state_id AS customers_state, ca.phone_'1' AS customers_phone, ca.phone_2 AS customers_phone_mobile, 
						ca.fax AS customers_telefax, ca.email AS customers_email_address ".
						"FROM " . TABLE_CUSTOMERS . " AS ca, " . TABLE_COUNTRIES ." AS country ".
						"WHERE ca.virtuemart_country_id = country.virtuemart_country_id".
						" AND virtuemart_order_id=". $orders['order_id'].
						" AND address_type='BT'"; // Billing Address
						if (DEBUGGER>=1) fwrite($dateihandle, "Virtuemart Kundenabfrage".$cust_sql);						
						$cust_query = dmc_db_query ($cust_sql);
						if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query))) {
							// Customer invoice address
							$customers_cid=$cust_data['customers_cid'];
							$customers_group=$cust_data['customers_group'];
							$customers_dob=$billing_dob=$cust_data['customers_dob'];
							$customers_ustid=$billing_ustid=$cust_data['customers_ustid'];	// bei virtuemart nicht vorhanden
							$customers_title=$billing_title=$cust_data['customers_title'];
							$customers_id=$billing_id=$cust_data['customers_id'];
							$customers_address_id=$billing_address_id=$cust_data['customers_address_id'];
							$customers_country=$billing_country=$cust_data['customers_country'];
							$customers_country_id=$billing_country_id=$cust_data['customers_country'];
							$customers_company=$billing_company=$cust_data['customers_company'];
							$customers_lastname=$billing_lastname=$cust_data['customers_lastname'];
							$customers_firstname=$billing_firstname=$cust_data['customers_firstname'];
							$customers_address1=$billing_address1=$cust_data['customers_address1'];
							$customers_address2=$billing_address2=$cust_data['customers_address2'];
							$customers_zip=$billing_zip=$cust_data['customers_zip'];
							$customers_city=$billing_city=$cust_data['customers_city'];
							$customers_suburb=$billing_suburb=$cust_data['customers_suburb'];
							$customers_country=$billing_country=$cust_data['customers_country'];
							$customers_country_iso_code=$billing_country_iso_code=$cust_data['customers_country_iso_code'];	
							$customers_state=$billing_state=$cust_data['customers_state'];						
							$customers_phone=$billing_phone=$cust_data['customers_phone'];
							$customers_phone_mobile=$billing_phone_mobile=$cust_data['customers_phone_mobile'];
							$customers_telefax=$billing_phone_mobile=$cust_data['customers_telefax'];
							$customers_email_address=$billing_email_address=$cust_data['customers_email_address'];
						} // end if 
						
						// customer billing address
						$cust_sql = "SELECT ".
						"ca.virtuemart_user_id AS customers_cid, 'std' AS customers_group, '' AS customers_dob, '' AS customers_ustid,
						ca.title AS customers_title,  ca.virtuemart_user_id AS  customers_id, ca.virtuemart_user_id AS customers_address_id, 
						ca.company AS customers_company, ca.last_name AS customers_lastname, CONCAT(ca.first_name,' ',IFNULL(ca.middle_name,'')) AS customers_firstname, 
						ca.address_'1' AS customers_address1, ca.address_2 AS customers_address2, ca.zip AS customers_zip, ca.city AS customers_city, 
						'' AS customers_suburb,country.country_name AS customers_country, country.country_2_code AS customers_country_iso_code,
						virtuemart_state_id AS customers_state, ca.phone_'1' AS customers_phone, ca.phone_2 AS customers_phone_mobile, 
						ca.fax AS customers_telefax, ca.email AS customers_email_address ".
						"FROM " . TABLE_CUSTOMERS . " AS ca, " . TABLE_COUNTRIES ." AS country ".
						"WHERE ca.virtuemart_country_id = country.virtuemart_country_id".
						" AND virtuemart_order_id=". $orders['virtuemart_order_id'].
						" AND address_type='ST'"; // Shipping Address
						$cust_query = dmc_db_query ($cust_sql);
						if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query))) {
							// Customer shipping address
							$delivery_cid=$cust_data['customers_cid'];
							$delivery_group=$cust_data['customers_group'];
							$delivery_dob=$billing_dob=$cust_data['customers_dob'];
							$delivery_ustid=$billing_ustid=$cust_data['customers_ustid'];	// bei virtuemart nicht vorhanden
							$delivery_title=$billing_title=$cust_data['customers_title'];
							$delivery_id=$billing_id=$cust_data['customers_id'];
							$delivery_address_id=$billing_address_id=$cust_data['customers_address_id'];
							$delivery_country=$billing_country=$cust_data['customers_country'];
							$delivery_country_id=$billing_country_id=$cust_data['customers_country'];
							$delivery_company=$billing_company=$cust_data['customers_company'];
							$delivery_lastname=$billing_lastname=$cust_data['customers_lastname'];
							$delivery_firstname=$billing_firstname=$cust_data['customers_firstname'];
							$delivery_address1=$billing_address1=$cust_data['customers_address1'];
							$delivery_address2=$billing_address2=$cust_data['customers_address2'];
							$delivery_zip=$billing_zip=$cust_data['customers_zip'];
							$delivery_city=$billing_city=$cust_data['customers_city'];
							$delivery_suburb=$billing_suburb=$cust_data['customers_suburb'];
							$delivery_country=$billing_country=$cust_data['customers_country'];
							$delivery_country_iso_code=$billing_country_iso_code=$cust_data['customers_country_iso_code'];	
							$delivery_state=$billing_state=$cust_data['customers_state'];						
							$delivery_phone=$billing_phone=$cust_data['customers_phone'];
							$delivery_phone_mobile=$billing_phone_mobile=$cust_data['customers_phone_mobile'];
							$delivery_telefax=$billing_phone_mobile=$cust_data['customers_telefax'];
							$delivery_email_address=$billing_email_address=$cust_data['customers_email_address'];
						} // end if 
					} else if (strtolower(SHOPSYSTEM) == 'woocommerce') {
						// woocommerce
						// SELECT o.ID, o.post_date FROM `wp_posts` AS o WHERE o.post_type = 'shop_order' ORDER BY `ID` ASC
						$cust_sql = "SELECT ".
						"(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS customers_cid, 'std' AS customers_group, '' AS customers_dob, (SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_title' AND post_id=o.ID) AS customers_title,  
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS customers_id, (SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS customers_address_id, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_company' AND post_id=o.ID) AS customers_company, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_vat_number' AND post_id=o.ID) AS customers_ustid, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_last_name' AND post_id=o.ID) AS customers_lastname,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_first_name' AND post_id=o.ID) AS customers_firstname, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_address_1' AND post_id=o.ID) AS customers_address1, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_address_2' AND post_id=o.ID) AS customers_address2, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_postcode' AND post_id=o.ID) AS customers_zip, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_city' AND post_id=o.ID) AS customers_city, '' AS customers_suburb,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_country' AND post_id=o.ID) AS customers_country, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_country' AND post_id=o.ID) AS customers_country_iso_code,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_state' AND post_id=o.ID) AS customers_state, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_phone' AND post_id=o.ID) AS customers_phone, '' AS customers_phone_mobile, '' AS customers_telefax, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_billing_email' AND post_id=o.ID) AS customers_email_address, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS delivery_id, (SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS delivery_address_id, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_title' AND post_id=o.ID) AS delivery_title, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_company' AND post_id=o.ID) AS delivery_company, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_last_name' AND post_id=o.ID) AS delivery_lastname,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_first_name' AND post_id=o.ID) AS delivery_firstname, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_address_1' AND post_id=o.ID) AS delivery_address1, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_address_2' AND post_id=o.ID) AS delivery_address2, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_postcode' AND post_id=o.ID) AS delivery_zip, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_city' AND post_id=o.ID) AS delivery_city, '' AS delivery_suburb,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_country' AND post_id=o.ID) AS delivery_country, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_country' AND post_id=o.ID) AS delivery_country_iso_code,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_state' AND post_id=o.ID) AS delivery_state, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_phone' AND post_id=o.ID) AS delivery_phone, '' AS delivery_phone_mobile, '' AS delivery_telefax, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_email' AND post_id=o.ID) AS delivery_email_address, 
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_shipping' AND post_id=o.ID) AS order_shipping_costs,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_cart_discount' AND post_id=o.ID) AS order_cart_discount,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_discount' AND post_id=o.ID) AS order_discount,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_order_total' AND post_id=o.ID) AS order_order_total,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) AS order_customer_user,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method' AND post_id=o.ID) AS order_shipping_method,
						(SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_shipping_method_title' AND post_id=o.ID)  AS order_shipping_method_title, ".
						// SOL Debitoreninformationen
						"(SELECT meta_value FROM `".DB_PREFIX."usermeta` WHERE meta_key='SOL_DEBITORENNUMMER' AND user_id= (SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) LIMIT 1) AS SOL_DEBITORENNUMMER, ".
						"(SELECT meta_value FROM `".DB_PREFIX."usermeta` WHERE meta_key='SOL_ADRESSNUMMER' AND user_id= (SELECT meta_value FROM `".DB_PREFIX."postmeta` WHERE meta_key='_customer_user' AND post_id=o.ID) LIMIT 1) AS SOL_ADRESSNUMMER ".
						"FROM `" . DB_PREFIX . "posts` AS o ".
						"WHERE o.post_type = 'shop_order' AND o.id=". $orders['order_id'];
						
				   	    // if (DEBUGGER>=1) fwrite($dateihandle, "\nwooCommerce Kundenabfrage\n".$cust_sql);						
						
						$cust_query = dmc_db_query ($cust_sql); 
						if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query))) {
							// SOL
							$SOL_ADRESSNUMMER=$cust_data['SOL_ADRESSNUMMER'];
							$SOL_DEBITORENNUMMER=$cust_data['SOL_DEBITORENNUMMER'];
							// Customer and invoice address
							$customers_cid=$cust_data['customers_cid'];
							$customers_group=$cust_data['customers_group'];
							$customers_dob=$billing_dob=$cust_data['customers_dob'];
							$customers_ustid=$billing_ustid=$cust_data['customers_ustid'];	
							$customers_title=$billing_title=$cust_data['customers_title'];
							$customers_id=$billing_id=$cust_data['customers_id'];
							$customers_address_id=$billing_address_id=$cust_data['customers_address_id'];
							$customers_country=$billing_country=$cust_data['customers_country'];
							$customers_country_id=$billing_country_id=$cust_data['customers_country'];
							$customers_company=$billing_company=$cust_data['customers_company'];
							$customers_lastname=$billing_lastname=$cust_data['customers_lastname'];
							$customers_firstname=$billing_firstname=$cust_data['customers_firstname'];
							$customers_address1=$billing_address1=$cust_data['customers_address1'];
							$customers_address2=$billing_address2=$cust_data['customers_address2'];
							$customers_zip=$billing_zip=$cust_data['customers_zip'];
							$customers_city=$billing_city=$cust_data['customers_city'];
							$customers_suburb=$billing_suburb=$cust_data['customers_suburb'];
							$customers_country=$billing_country=$cust_data['customers_country'];
							$customers_country_iso_code=$billing_country_iso_code=$cust_data['customers_country_iso_code'];	
							$customers_state=$billing_state=$cust_data['customers_state'];						
							$customers_phone=$billing_phone=$cust_data['customers_phone'];
							$customers_phone_mobile=$billing_phone_mobile=$cust_data['customers_phone_mobile'];
							$customers_telefax=$billing_phone_mobile=$cust_data['customers_telefax'];
							$customers_email_address=$billing_email_address=$cust_data['customers_email_address'];
								// Customer shipping address
							$delivery_cid=$cust_data['delivery_cid'];
							$delivery_group=$cust_data['delivery_group'];
							$delivery_dob=$billing_dob=$cust_data['delivery_dob'];
							$delivery_ustid=$cust_data['delivery_ustid'];	// bei woocommerce nicht vorhanden
							$delivery_title=$cust_data['delivery_title'];
							$delivery_id=$cust_data['delivery_id'];
							$delivery_address_id=$cust_data['delivery_address_id'];
							$delivery_country=$cust_data['delivery_country'];
							$delivery_country_id=$cust_data['delivery_country'];
							$delivery_company=$cust_data['delivery_company'];
							$delivery_lastname=$cust_data['delivery_lastname'];
							$delivery_firstname=$cust_data['delivery_firstname'];
							$delivery_address1=$cust_data['delivery_address1'];
							$delivery_address2=$cust_data['delivery_address2'];
							$delivery_zip=$cust_data['delivery_zip'];
							$delivery_city=$cust_data['delivery_city'];
							$delivery_suburb=$cust_data['delivery_suburb'];
							$delivery_country=$cust_data['delivery_country'];
							$delivery_country_iso_code=$cust_data['delivery_country_iso_code'];	
							$delivery_state=$cust_data['delivery_state'];						
							$delivery_phone=$cust_data['delivery_phone'];
							$delivery_phone_mobile=$cust_data['delivery_phone_mobile'];
							$delivery_email_address=$cust_data['delivery_email_address'];
							$delivery_email_address=$cust_data['delivery_email_address'];						
						} // end if 
						$shipping_amount_7=0;
						$shipping_tax_amount_7=0;
						$shipping_amount_19=0;
						$shipping_tax_amount_19=0;
						// spezialanpassung Versandkosten Steuern ermitteln
						$sum_wert_produkte_mwst_voll=dmc_get_order_product_value_tax_and_order_id($orders['order_id'],'');
						$sum_wert_produkte_mwst_reduziert=dmc_get_order_product_value_tax_and_order_id($orders['order_id'],'reduced-rate');
						if ($sum_wert_produkte_mwst_voll>0 && $sum_wert_produkte_mwst_reduziert==0) {
							// nur 19%
							$shipping_amount=$gesamtversandkosten;
							$shipping_amount_19=0;
							$shipping_tax_amount_19=0;
						} else if ($sum_wert_produkte_mwst_voll==0 && $sum_wert_produkte_mwst_reduziert>0) {
							// nur 7%
							$shipping_amount=$gesamtversandkosten;
							$shipping_amount_7=0;
							$shipping_tax_amount_7=0;
						} else {
							// anteilig
							$shipping_amount=$gesamtversandkosten;
							$shipping_amount_7=0;
							$shipping_tax_amount_7=0;
							$shipping_amount_19=0;
							$shipping_tax_amount_19=0;
						}
						// spezialanpassung Versandkosten Steuern ermitteln ALT
						/* $versand_19_sql = "SELECT meta_value FROM " . DB_TABLE_PREFIX . "woocommerce_order_itemmeta WHERE meta_key='shipping_tax_amount' AND order_item_id=(SELECT order_item_id FROM " . DB_TABLE_PREFIX . "woocommerce_order_items WHERE order_item_type='tax' AND order_id=". $orders['order_id']." AND order_item_name='DE-MWST-1')";
						$result = dmc_db_query ($versand_19_sql);
						if (($result) && ($versand_data = dmc_db_fetch_array($result))) {
							// Customer and invoice address
							$shipping_tax_amount_19=$versand_data['meta_value'];
							if (DEBUGGER>=1) fwrite($dateihandle, "\n #933 mwst 19%=".$shipping_tax_amount_19);
						}
						$versand_7_sql = "SELECT meta_value FROM " . DB_TABLE_PREFIX . "woocommerce_order_itemmeta WHERE meta_key='shipping_tax_amount' AND order_item_id=(SELECT order_item_id FROM " . DB_TABLE_PREFIX . "woocommerce_order_items WHERE order_item_type='tax' AND order_id=". $orders['order_id']." AND order_item_name='DE-MWST.-1')";
						$result = dmc_db_query ($versand_7_sql);
						if (($result) && ($versand_data = dmc_db_fetch_array($result))) {
							// Customer and invoice address
							$shipping_tax_amount_7=$versand_data['meta_value'];
						}
						// Versandkosten basierend auf Steuern berechnen.
						$shipping_amount_7 =  $shipping_tax_amount_7 / 0.07;
						$shipping_amount_7 = round($shipping_amount_7*100)*0.01;
						$shipping_tax_amount_7 = round($shipping_tax_amount_7*100)*0.01;
						$shipping_amount_19 =  $shipping_tax_amount_19 / 0.19;
						$shipping_amount_19 = round($shipping_amount_19*100)*0.01;
						$shipping_tax_amount_19 = round($shipping_tax_amount_19*100)*0.01;
						// Abfangroutine fuer Rundungsfehler woocommerce. -> Summe muss 4,90 Euro sein (nicht 4,89)
						$gesamtversandkosten = $shipping_amount_19+$shipping_tax_amount_19+$shipping_amount_7+$shipping_tax_amount_7;
						fwrite($dateihandle, "#950 gesamtversandkosten: $gesamtversandkosten = $shipping_amount_19+$shipping_tax_amount_19+$shipping_amount_7+$shipping_tax_amount_7 \n");
						if (round($gesamtversandkosten*100) == 489) {
							$shipping_amount_7 = $shipping_amount_7 + 0.01;
							fwrite($dateihandle, "#952 shipping_amount_7 neu = $shipping_amount_7+$shipping_tax_amount_7 \n");
						}
						if ($gesamtversandkosten > 4.75 && $gesamtversandkosten < 4.95) {
							// $shipping_amount_7 = $shipping_amount_7 + 0.01;
					       fwrite($dateihandle, "#952a shipping_amount_7 neu = $shipping_amount_7+$shipping_tax_amount_7 \n");
						}
						if (round($gesamtversandkosten*100) == 488) {
							$shipping_amount_7 = $shipping_amount_7 + 0.02;
					//		fwrite($dateihandle, "#958  shipping_amount_7 neu =  $shipping_amount_7+$shipping_tax_amount_7 \n");
						}
						if ($gesamtversandkosten > 4.905 && $gesamtversandkosten < 4.925 || round($gesamtversandkosten*100) == 491) {
							$shipping_amount_19 = $shipping_amount_19 - 0.01;
							fwrite($dateihandle, "#961 shipping_amount_19 neu = $shipping_amount_19  \n");
						}
						$gesamtversandkosten = $shipping_amount_19+$shipping_tax_amount_19+$shipping_amount_7+$shipping_tax_amount_7;
						fwrite($dateihandle, "#969 gesamtversandkosten: $gesamtversandkosten = $shipping_amount_19+$shipping_tax_amount_19+$shipping_amount_7+$shipping_tax_amount_7 \n");
						// Wibben Versandkosten Steuern ermitteln ende
						*/
						
				} else {	// all other shops     -    end woo
						// customer address
						// Veyton
						if (strtolower(SHOPSYSTEM) == 'veyton') {
							$cust_sql = "select * from " . TABLE_CUSTOMERS_ADDRESSES . " AS c ".
										"WHERE c.address_class='default'  AND c.customers_id=" . $orders['customers_id'];
							$cust_query = dmc_db_query ($cust_sql);
							if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query)))
							{
								$customers_ustid = $orders['customers_vat_id'];	// Veyton in orders
								$customers_title = $cust_data['customers_gender'];
								$customers_cid=$orders['customers_cid'];
								$customers_email_address=$orders['customers_email_address'];
							  
								$customers_dob = $cust_data['customers_dob'];
								$customers_group="";
								$customers_id=$cust_data['customers_id'];
								$customers_address_id=$cust_data['address_book_id'];
								$customers_country_id="";
								$customers_company=$cust_data['customers_company']." ".$cust_data['customers_company_2']." ".$cust_data['customers_company_3'];		// todo $cust_data['company'];
								$customers_lastname=$cust_data['customers_lastname'];
								$customers_firstname=$cust_data['customers_firstname'];
								$customers_address1=$cust_data['customers_street_address'];
								$customers_address2='';
								$customers_zip=$cust_data['customers_postcode'];
								$customers_city=$cust_data['customers_city'];
								$customers_suburb=$cust_data['customers_suburb'];
								$customers_country=$cust_data['customers_country_code'];
								$customers_state="";
								$customers_country_iso_code=$cust_data['customers_country_code'];
								$customers_phone=$cust_data['customers_phone'];
								$customers_fax=$cust_data['customers_fax'];
								$customers_phone_mobile="";	// $cust_data['phone_mobile'];
								
							}
						} else { // all other shops - special informations from customes table
							if (strtolower(SHOPSYSTEM) == 'shopware' || SHOPSYSTEM == 'joomshopping') {
								$customers_dob = "";
								$customers_ustid = $cust_data['customers_vat_id'];
								$customers_title = $cust_data['customers_gender'];
								$customers_fax=$cust_data['customers_fax'];
								// customer information from order table
								$customers_cid=$orders['customer_id'];
								$customers_group=$orders['customer_customergroup'];
								$customers_id=$orders['customer_id'];
								$customers_address_id="";
								$customers_country_id="";
								$customers_company=$orders['billing_company'];
								$customers_lastname=$orders['billing_lastname'];
								$customers_firstname=$orders['billing_firstname'];
								$customers_address1=$orders['billing_street'].' '.$orders['billing_streetnumber'];
								$customers_address2='';
								$customers_zip=$orders['billing_zip'];
								$customers_city=$orders['billing_city'];
								$customers_suburb=$orders['billing_suburb'];
								$customers_country=$orders['billing_country'];
								$customers_state=$orders['billing_state'];
								$customers_country_iso_code=$orders['billing_country_iso_code'];
								$customers_phone=$orders['billing_telephone'];
								$customers_phone_mobile="";	// $orders['phone_mobile'];
								$customers_email_address=$orders['billing_email_address'];
								$customer_shop_ID=$orders['customer_shop_ID'];
							} else {
								$cust_sql = "select * from " . TABLE_CUSTOMERS . 
											" WHERE customers_id=" . $orders['customers_id'];
								$cust_query = dmc_db_query ($cust_sql);
								if (($cust_query) && ($cust_data = dmc_db_fetch_array($cust_query)))
								{
									$customers_dob = $cust_data['customers_dob'];
									if ($customers_ustid=="") {
										$customers_ustid = $cust_data['customers_vat_id'];
									} else {
										$customers_ustid = $orders['customers_vat_id'];
									}
									$customers_title = $cust_data['customers_gender'];
									$customers_fax=$cust_data['customers_fax'];
								}
								// customer information from order table
								$customers_cid=$orders['customers_cid'];
								$customers_group=$orders['customers_status'];
								$customers_id=$orders['customers_id'];
								$customers_address_id="";
								$customers_country_id="";
								if (strtolower(SHOPSYSTEM) == 'zencart') {
									$customers_company=$orders['customers_company'];
									$customers_lastname=$orders['customers_name'];
									$customers_firstname='';								
								} else {
									$customers_company=$orders['company'];
									$customers_lastname=$orders['customers_lastname'];
									$customers_firstname=$orders['customers_firstname'];								
								}
								$customers_address1=$orders['customers_street_address'];
								$customers_address2='';
								$customers_zip=$orders['customers_postcode'];
								$customers_city=$orders['customers_city'];
								$customers_suburb=$orders['customers_suburb'];
								$customers_country=$orders['customers_country'];
								$customers_state=$orders['customers_state'];
								$customers_country_iso_code=$orders['customers_country_iso_code_2'];
								// ZB bei Modified nur billing ISO Code
								if ($customers_country_iso_code=="")
									$customers_country_iso_code=$orders['billing_country_iso_code_2'];
								$customers_phone=$orders['customers_telephone'];
								$customers_phone_mobile="";	// $orders['phone_mobile'];
								$customers_email_address=$orders['customers_email_address'];
							}
						} // end if
					
						// billing information from order table
						$billing_id=$orders['customers_id'];
						$billing_address_id=$orders['billing_address_book_id'];
						if ($orders['billing_gender']=='m' || $orders['billing_gender']=='mr') {
							$billing_title="Herr";
							$billing_gender="m";
						} else if ($orders['billing_gender']=='f' || $orders['billing_gender']=='ms') {
							$billing_title="Frau";
							$billing_gender="f";
						} else {
							$billing_title="An";
							$billing_gender="n";
						}
						$billing_country_id="";
						$billing_address1=$orders['billing_street_address'];
						$billing_address2='';
						$billing_zip=$orders['billing_postcode'];
						$billing_city=$orders['billing_city'];
						$billing_suburb=$orders['billing_suburb'];
						$billing_country=$orders['billing_country'];
						$billing_state=$orders['billing_state'];
						
						if (strtolower(SHOPSYSTEM) == 'veyton') { // modified
							$billing_company=$orders['billing_company']." ".$orders['billing_company_2']." ".$orders['billing_company_3'];		
							$billing_company2=$orders['billing_company_2'];		
							$billing_lastname=$orders['billing_lastname'];
							$billing_firstname=$orders['billing_firstname'];	
							$billing_country_iso_code=$orders['billing_country_code'];
							$billing_phone=$orders['billing_phone'];
							if ($billing_phone=="")
								$billing_phone=$customers_phone;
							$billing_phone_2=$orders['billing_mobile_phone'];
							$billing_phone_mobile=$orders['billing_mobile_phone'];
							$billing_fax=$orders['billing_fax'];
							$billing_email_address=$orders['customers_email_address'];
						} else {
							if (strtolower(SHOPSYSTEM) == 'zencart') {
								$billing_company=$orders['billing_company'];		
								$billing_company2='';		
								$billing_lastname=$orders['billing_name'];
								$billing_firstname='-';	
							} else {
								// $billing_company=$orders['billing_company']." ".$orders['billing_company_2']." ".$orders['billing_company_3'];		
								$billing_company=$orders['billing_company'];		
								$billing_company2=$orders['billing_company_2'];		
								$billing_lastname=$orders['billing_lastname'];
								$billing_firstname=$orders['billing_firstname'];									
							}
							$billing_country_iso_code=$orders['billing_country_iso_code_2'];
							$billing_phone=$orders['billing_phone'];
							if ($billing_phone=="")
								$billing_phone=$customers_phone;
							$billing_phone_mobile="";
							$billing_fax=$orders['billing_fax'];
							$billing_email_address=$orders['customers_email_address'];
						}
					/*	} else {
							$billing_company=$orders['billing_company'];		
							$billing_company2="";		
							$billing_lastname=$orders['billing_name'];
							$billing_firstname="";  //	$orders['billing_firstname'];	
							$billing_country_iso_code=$orders['customers_country_iso_code_2'];
							$billing_phone="";
							$billing_phone_mobile="";
							$billing_fax=$customers_fax;
							$billing_email_address=$orders['customers_email_address'];
						}*/
						
						// delivery address
						// delivery informations from order table
						$delivery_id=$orders['customers_id'];
						$delivery_address_id=$orders['delivery_address_book_id'];
						$delivery_country_id="";
						if ($orders['delivery_gender']=='m' || $orders['delivery_gender']=='mr') {
							$delivery_title="Herr";
							$delivery_gender="m";
						} else if ($orders['delivery_gender']=='f' || $orders['delivery_gender']=='ms') {
							$delivery_title="Frau";
							$delivery_gender="f";
						} else {
							$delivery_title="An";
							$delivery_gender="n";
						}
						
						
						$delivery_address1=$orders['delivery_street_address'];
						$delivery_address2='';
						$delivery_zip=$orders['delivery_postcode'];
						$delivery_city=$orders['delivery_city'];
						$delivery_suburb=$orders['delivery_suburb'];
						$delivery_country=$orders['delivery_country'];
						$delivery_state=$orders['delivery_state'];
						if (strtolower(SHOPSYSTEM) == 'veyton')  {		// modified
							$delivery_company=$orders['delivery_company']." ".$orders['delivery_company_2']." ".$orders['delivery_company_3'];		
							$delivery_company2=$orders['delivery_company_2'];		
							$delivery_lastname=$orders['delivery_lastname'];
							$delivery_firstname=$orders['delivery_firstname'];	
							if (strtolower(SHOPSYSTEM) == 'zencart') {
								$delivery_company=$orders['delivery_company'];		
								$delivery_company2='';		
								$delivery_lastname=$orders['delivery_name'];
								$delivery_firstname='';	
							} else {
								$delivery_company=$orders['delivery_company']." ".$orders['delivery_company_2']." ".$orders['delivery_company_3'];		
								$delivery_company2=$orders['delivery_company_2'];		
								$delivery_lastname=$orders['delivery_lastname'];
								$delivery_firstname=$orders['delivery_firstname'];	
							}
							$delivery_country_iso_code=$orders['delivery_country_code'];
							$delivery_phone=$orders['delivery_phone'];
							$delivery_phone_mobile="";
							$delivery_fax=$orders['delivery_fax'];
							$delivery_email_address=$orders['customers_email_address'];
							if ($delivery_phone=="")
								$delivery_phone=$customers_phone;
						} else {
							$delivery_company=$orders['delivery_company']." ".$orders['delivery_company_2']." ".$orders['delivery_company_3'];		
							$delivery_company2=$orders['delivery_company_2'];		
							$delivery_lastname=$orders['delivery_lastname'];
							$delivery_firstname=$orders['delivery_firstname'];	
							$delivery_country_iso_code=$orders['delivery_country_iso_code_2'];
							$delivery_phone=$orders['delivery_phone'];
							$delivery_phone_mobile="";
							$delivery_fax=$orders['delivery_fax'];
							$delivery_email_address=$orders['customers_email_address'];
							if ($delivery_phone=="")
								$delivery_phone=$customers_phone;
						}	
					/*	} else {
							$delivery_company=$orders['delivery_company'];		
							$delivery_company2="";		
							$delivery_lastname=$orders['delivery_name'];
							$delivery_firstname="";    //$orders['delivery_firstname'];	
							$delivery_country_iso_code=$orders['customers_country_iso_code_2'];
							$delivery_phone="";
							$delivery_phone_mobile="";
							$delivery_fax="";
							$delivery_email_address=$orders['customers_email_address'];
						} */
							
					
						// Länder ISO Code ermitteln veyton
						// if (strtolower(SHOPSYSTEM) == 'veyton') {
						// $customers_country_iso_code=$orders['delivery_country_code'];
					
					} // end if else Anschriften Shops
					
					// Wenn keine GESONDERTE Rechnungsanschrift, Rechnungsanschrift = Lieferanschrift
					// if ($orders['billing_company']=='') $orders['billing_company']=$orders['delivery_company'];
					// if ($orders['billing_name']=='')  $orders['billing_name']=$orders['delivery_name'];
					// if ($orders['billing_street_address']=='') $orders['billing_street_address']=$orders['delivery_street_address'];
					// if ($orders['billing_postcode']=='')  $orders['billing_postcode']=$orders['delivery_postcode'];
					// if ($orders['billing_city']=='')  $orders['billing_city']=$orders['delivery_city'];
					// if ($orders['billing_suburb']=='') $orders['billing_suburb']=$orders['delivery_suburb'];
					// if ($orders['billing_state']=='')  $orders['billing_state']=$orders['delivery_state'];
					// if ($orders['billing_country']=='')  $orders['billing_country']=$orders['delivery_country'];
					 
					// Wenn keine GESONDERTE Lieferanschrift, dann auch nicht ausgeben, sondern nur Kundenanschrift	
					if (COMPARE_SHIPPING_ADRESSE_WITH=='billing') {
						if ($billing_company==$delivery_company &&							
							$billing_address1==$delivery_address1 &&
							$billing_zip==$delivery_zip) 
						{
							$delivery_adress_type="same";
						} else {
							$delivery_adress_type="different";
						}
					} else {
						if ($customers_company==$delivery_company && $customers_address1=$delivery_address1 && $customers_zip==$delivery_zip) {
							$delivery_adress_type="same";
						} else {
						$delivery_adress_type="different";
						}
					} // end if 

			//	fwrite($dateihandle, "1345\n");
					$payment_transactionID = '';
					if (strtolower(SHOPSYSTEM) == 'presta') {
							$currency_code=presta_get_currency_by_id($orders['id_currency'], $dateihandle);
							// $currency_value=presta_get_currency_rate_by_id($orders['id_currency'], $dateihandle);
							$currency_value=$orders['conversion_rate'];
							$delivery_country_code = $delivery_country_code;
							$billing_country_code=$billing_country_code;
							$payment_method = $orders['payment'];
							$payment_class=$orders['module'];
							$orders_id=$orders['id_order'];
							$orders['orders_id'] = $orders_id;
							$shipping_amount_gros=$orders['total_shipping_tax_incl'];							
							$rcm_versandkosten=$shipping_amount=$orders['total_shipping_tax_excl'];							
							$rcm_versandkosten_tax=$shipping_tax_amount=$shipping_amount_gros-$shipping_amount;
							$shipping_tax_rate=$orders['carrier_tax_rate'];
							if ($shipping_amount>0)
								$shipping_method="Versand";
							else
								$shipping_method="Kostenfrei";
							$rcm_versandart=$shipping_method;
							$orders['shipping_class']=$shipping_method;
							
							$shipping_weight=$orders['weight']=0;
							$discount_amount=0.00;
							$shop_id="0";
							$language_code="de";
							$orders_date=$orders['date_add'];
							$order_total_gros_discount=$orders['total_paid_tax_incl'];
							$order_total_gros=$orders['total_paid_tax_incl']+ $orders['total_discounts_tax_incl'];
							$order_total_net=$orders['total_paid_tax_excl']+ $orders['total_discounts_tax_excl'];
							$order_tax_amount_discount=$orders['total_paid_tax_incl']-$orders['total_paid_tax_excl'];
							$order_tax_amount=$order_total_gros-$order_total_net;
							$order_total_net=$orders['total_paid_tax_excl'];						
					} else if (strtolower(SHOPSYSTEM) == 'virtuemart' || SHOPSYSTEM == 'shopware' 
								|| SHOPSYSTEM == 'woocommerce' || SHOPSYSTEM == 'joomshopping') {
							// Felder mappen Virtuemart
							$orders_date=$orders['created_on'];
							$orders_status=$orders['order_status'];
							$orders_ip=$orders['ip_address'];
							$orders_id=$orders['order_id'];
							$orders_no=$orders['order_number'];
							$orders['orders_id'] = $orders_id;
							$currency_code=$orders['user_currency_id'];	 	 
							$currency_value=$orders['user_currency_rate'];	
							$shop_id="0";
							$language_code="de";  	 
							// summen
							$zwischensumme=$orders['order_total'];	// subtotal = without shipping 
							$order_total_gros=$orders['order_total'];
							$order_tax_amount=$orders['order_billTaxAmount'];
							$order_total_net=$orders['order_total']-$orders['order_billTaxAmount'];
						//	$currency_code=$orders['order_currency'];
							// Zur Zeit noch nicht aktiviert, d.h Zuweisung Standard
							$order_total_discount_amount=$orders['order_discount_amount'];
							$order_total_discount_gros=$orders['order_discount_amount'];
							$order_total_discount_net=$order_total_discount_gros-$orders['order_billTaxAmount'];
							$order_discount_tax_amount=$orders['order_billTaxAmount'];
							// Zahlung 
							$payment_method = $orders['payment_method'];
							$payment_class=$orders['payment_class'];
							$payment_costs=$orders['order_payment'];
							$payment_costs_tax_amount=$orders['order_payment_tax'];
							if (strtolower(SHOPSYSTEM) == 'shopware') {
								$payment_transactionID = $orders['order_transaction_id'];
							}
							if ($payment_costs==0)
								$payment_costs_tax=0;	// Standard bei keinen Versandkosten nehmen
							else
								$payment_costs_tax=$payment_costs_tax_amount/$payment_costs*100;							 	
							// Versand
							$shipping_method=$orders['shipping_method'];
							$shipping_class=$orders['shipping_class'];
							//	$shipping_method=$orders['virtuemart_shipmentmethod_id'];
							$shipping_amount=$orders['order_shipment'];	 
							$shipping_tax_amount=$orders['order_shipment_tax'];
							
							$shipping_tax_rate=0;
							if ($shipping_amount_net>0) {
								// 7 oder 19% ?
								$versandkosten_steuersatz_prozent=$shipping_tax_rate=floor((($shipping_amount_gros/$shipping_amount_net)-1)*100);
							} else {
								$versandkosten_steuersatz_prozent=$shipping_tax_rate=0;
							}
							
							if ($shipping_amount==0)
								$shipping_tax=0;	// Standard bei keinen Versandkosten nehmen
							else
								$shipping_tax=$shipping_tax_amount/$shipping_amount;
							
							if ($shipping_amount==0)
								$shipping_tax=0;	// Standard bei keinen Versandkosten nehmen
							else
								$shipping_tax=$shipping_tax_amount/$shipping_amount;
							
							// Shipping Steuersatz ermitteln
							if ($orders['order_shipment_gros']<>'' && $orders['order_shipment_gros']>0 && $orders['order_shipment_net']<>'' ) {
								if ($orders['order_shipment_gros'] == $orders['order_shipment_net']) {
									$shipping_tax_rate = 0;
								} else {
									$shipping_tax_rate = (($orders['order_shipment_gros']/$orders['order_shipment_net'])-1)*100;			// Zb ((2.9 / 2.44)-1)*100 = 18,85
									$shipping_tax_rate = round($shipping_tax_rate, 0);
								}
							}
						
							// bei woocommerce Versandkosten auf 19%
						/*	if(strtolower(SHOPSYSTEM) == 'woocommerce') {
								$shipping_amount=$orders['order_shipment'];	 
								$shipping_tax_amount=$orders['order_shipment']*1.19-$orders['order_shipment'];
								if ($shipping_amount==0)
									$shipping_tax=0;	// Standard bei keinen Versandkosten nehmen
								else
									$shipping_tax=19;
								
							} */
							
							// woocommerce wibben
							$donation_amount=0+$orders['donation_amount'];
							$donation_tax_status=0+$orders['donation_tax_status']; 			// 1 = spender, 2 = sponsering
							$donation_beneficiary=0+$orders['donation_beneficiary']; 		// Spendenempfänger
							$completed_date=$orders['completed_date'];
							$paid_date=$orders['_paid_date'];
							$tss_bt_transfers=$orders['tss_bt_transfers'];					// zB 'a:1:{i:0;a:2:{s:4:"date";s:10:"25.05.2015";s:6:"amount";d:33.18;}}';
							$tss_bt_transfers_array=unserialize($tss_bt_transfers);
							$zahlungsdatum = $tss_bt_transfers_array[0]['date'];
							$zahlungssumme = $tss_bt_transfers_array[0]['amount'];
							for($rcm=1; $i < count($tss_bt_transfers_array); $rcm++)
							{	
								// Bei mehreren Buchungen die Betraege summieren
							   $zahlungssumme += $tss_bt_transfers_array[$rcm]['amount'];
							}

							$shipping_weight=0; 
							$discount_amount=0+$orders['order_billDiscountAmount'];
							$rcm_versandkosten = $shipping_amount+$shipping_tax_amount;	 
							$rcm_versandkosten_net = $shipping_amount;	
							$rcm_versandkosten_gros = $rcm_versandkosten;	
							$rcm_versandkosten_tax = $shipping_tax_amount;		
							$payment_amount= $orders['order_payment'];	
							$payment_tax_amount= $orders['order_payment_tax'];	
							
							// Kundendaten - Nur Shopware und joomshopping
							if (strtolower(SHOPSYSTEM) == 'shopware' || SHOPSYSTEM == 'joomshopping') {
								$customers_id=$billing_id=$orders['customer_id'];
								$customers_email_address = $billing_email_address = $orders['customer_email'];
								$billing_address1=$orders['billing_street'].' '.$orders['billing_streetnumber'];
								$billing_zip=$orders['billing_zip'];
								$billing_country_iso_code=$orders['billing_country_iso_code'];
								$customers_ustid = $orders['billing_vat_id'];
							}
						} else {
							// veyton and others
							$orders_date=$orders['date_purchased'];
							$orders_status=$orders['orders_status'];
							$orders_ip=$orders['customers_ip'];
							$currency_value=$orders['currency_value'];
							$orders_id=$orders['orders_id'];
							// Gambio GX 2 - Kundengruppenrabatt
							$customers_status_discount=$orders['customers_status_discount'];
							#
							// Versandkosten ermitteln
							   if (strtolower(SHOPSYSTEM) == 'veyton') {
									$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where orders_total_key=\"shipping\" and orders_id = " . $orders_id;
									$versand_query = dmc_db_query($versand_sql);
									if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
										$shipping_method=umlaute_order_export(html2ascii($versanddata['orders_total_name'])); 
										$shipping_class=html2ascii($versanddata['orders_total_model']);
										$rcm_versandkosten_net = $versanddata['orders_total_price'];
										$rcm_versandkosten_tax_rate = $versanddata['orders_total_tax'];
										$rcm_versandkosten = $rcm_versandkosten_gros = $rcm_versandkosten_net*(1+$versanddata['orders_total_tax']/100);
										$rcm_versandkosten_tax = $rcm_versandkosten_gros-$rcm_versandkosten_net;										
									}
								} else {
									$versand_sql = "select * from ".TABLE_ORDERS_TOTAL." where class=\"ot_shipping\" and orders_id = " . $orders_id;
									
									$versand_query = dmc_db_query($versand_sql);
									if (($versand_query) && ($versanddata = dmc_db_fetch_array($versand_query))) {
										$shipping_method=umlaute_order_export(html2ascii($versanddata['title']));
										$shipping_class=html2ascii($versanddata['class']);
										// dynamische Ermittlung Versandkostensteuersatz basierend auf dem Steursatz der Bestellung
										$steuersatz_sql = "SELECT * FROM ".TABLE_ORDERS_TOTAL." WHERE class=\"ot_tax\" AND orders_id = " . $orders_id . " LIMIT 1 ";
										
									//	fwrite($dateihandle, "1499 - in sr\n");
										
										$steuersatz_query = dmc_db_query($steuersatz_sql);
										if (($steuersatz_query) && ($steuersatz_query_data = dmc_db_fetch_array($steuersatz_query))) {
											$steuersatz_title=$steuersatz_query_data['title']; 
											$versandkosten_steuersatz=TAX_SHIPPING;		// Als Standand angenommen
											$versandkosten_steuersatz_prozent=(TAX_SHIPPING-1)*100; 	// Als Standand angenommen
											// Im Title müsste stehen inkl. 19% MwSt.: oder inkl. 20% MwSt.: etc
											if (strrpos($steuersatz_title, " ".$j) === false) {
												// Kommt nicht vor
												if (DEBUGGER>=99) fwrite($dateihandle, "1424 - in steuersatz_title = $steuersatz_title kommt $j NICHT vor\n");
											} else {
												if (DEBUGGER>=99) fwrite($dateihandle, "1426 - in steuersatz_title = $steuersatz_title kommt $j vor\n");
												$versandkosten_steuersatz_prozent=$j;
												$versandkosten_steuersatz=1+($j/100);
												// break;
											}
											
										}
										
										if (BRUTTO_SHOP) {
											$rcm_versandkosten_gros = $versanddata['value'];	 
											$rcm_versandkosten = round($rcm_versandkosten*(1/$versandkosten_steuersatz),2);
											$rcm_versandkosten_tax =  $rcm_versandkosten_gros-$rcm_versandkosten;
											// $rcm_versandkosten_net = $rcm_versandkosten/TAX_SHIPPING;
										} else {
											$rcm_versandkosten = $versanddata['value'];	 
											$rcm_versandkosten_gros = round($rcm_versandkosten*$versandkosten_steuersatz,2);
											$rcm_versandkosten_tax =  $rcm_versandkosten_gros-$rcm_versandkosten;
										} 
										//	$rcm_versandkosten_tax =  $rcm_versandkosten-$rcm_versandkosten_net;
									}
								}
									
							// only veyton
							if (strtolower(SHOPSYSTEM) == 'veyton') {
								$currency_code=$orders['currency_code'];
								$delivery_country_code =($orders['delivery_country_code']);
								$billing_country_code=($orders['billing_country_code']);
								$payment_method = html2ascii($orders['payment_code']);
								$payment_class=html2ascii($orders['payment_code']);
								$shipping_amount = $rcm_versandkosten_net;// dmc_get_shipping_costs($shipping_method,$orders_id);
								$shipping_tax_amount = $rcm_versandkosten_tax;
								$shipping_weight = 0+$orders['weight'];
								$shipping_method = $orders['shipping_code']; 
								$discount_amount = 0+$orders['discount_amount'];
								$payment_amount = dmc_get_payment_costs($payment_class,$orders_id);
								$payment_tax_amount = $payment_amount*(1+dmc_get_payment_vat($payment_class,$orders_id)/100)-$payment_amount;
								$shop_id=$orders['shop_id'];
								$language_code=$orders['language_code'];
								// Transaktionsnummern abfragen von verschiedenen Modulen
								$transaktionsnummern=$orders['amazon_ordernumber'].' '.$orders['orders_data'].' '.$orders['authorization_id'].' '.$orders['XT_PAYMENTS_AUTHORIZATION_ID'];
							} else { // only others
								$currency_code=$orders['currency'];
								$delivery_country_code =($orders['delivery_country_iso_code_2']);
								$billing_country_code = ($customers_country_iso_code);
								$payment_method =html2ascii($orders['payment_method']);
								$payment_class=html2ascii($orders['payment_class']);
								$shipping_method=$shipping_method;
								$shipping_amount=$rcm_versandkosten;
								$shipping_tax_amount=$rcm_versandkosten_tax;
								$shipping_weight=0+$orders['weight'];
								$payment_amount=0; 
								$payment_tax_amount=0;
								$discount_amount=0+$orders['discount_amount'];
								$shop_id="0";
								$language_code="de";
							}//end veyton
							// Veyton Modul Mindenmengenzuschlag
							if (strtolower(SHOPSYSTEM) == 'veyton') {
									$dosql = "select * from ".TABLE_ORDERS_TOTAL." where orders_total_key=\"low_quantity_surcharge\" and orders_id = " . $orders_id;
									$sqlquery = dmc_db_query($dosql);
									if (($sqlquery) && ($versanddata = dmc_db_fetch_array($sqlquery))) {
										$mindermengenzuschlagstitel=umlaute_order_export(html2ascii($versanddata['orders_total_name'])); 
										$rcm_mindermengenzuschlag_net = $versanddata['orders_total_price'];
										$rcm_mindermengenzuschlag_tax_id = $versanddata['orders_total_tax_class'];
										$rcm_mindermengenzuschlag_tax_rate = $versanddata['orders_total_tax'];
										$rcm_mindermengenzuschlag = $rcm_mindermengenzuschlag_gros = $rcm_mindermengenzuschlag_net*(1+$versanddata['orders_total_tax']/100);
										$rcm_mindermengenzuschlag_tax = $rcm_mindermengenzuschlag_gros-$rcm_mindermengenzuschlag_net;										
									}
							} 
							// Shopspezifische Zusatzfelder
							if (strpos(strtolower(SHOPSYSTEM), "modified") !== false) {		
								// xtcModified / modified
	   							$orders_delivery_date=$orders['delivery_date'];
								if ($orders['anonym']<>0)
									$orders_delivery_anonym ="Anonymer Versand";
								$orders_greetings_text=($orders['greetings_text']);
								$shipping_method=$orders['shipping_method'];
								$shipping_class=$orders['shipping_class'];							
							}
							
							
							// 23.10.2017 - Paypal Informationen au Gambio hinsichtich Finanzierung und Ratenzahlung ermitteln
							if (strpos(SHOPSYSTEM, "Gambio") !== false) {		
								$order_paypal_factoring_info=dmc_get_gm_order_factoring_information($orders_id,'');
								$order_paypal_factoring_info_betrag=dmc_get_gm_order_factoring_information($orders_id,'betrag');
								$order_paypal_factoring_info_iban=dmc_get_gm_order_factoring_information($orders_id,'iban');
								$order_paypal_factoring_info_bic=dmc_get_gm_order_factoring_information($orders_id,'bic');
								$order_paypal_factoring_info_kreditinstitut=dmc_get_gm_order_factoring_information($orders_id,'kreditinstitut');
								$order_paypal_factoring_info_verwendungszweck=dmc_get_gm_order_factoring_information($orders_id,'verwendungszweck');
								$order_paypal_factoring_info_zahlbar=dmc_get_gm_order_factoring_information($orders_id,'zahlbar');
								$order_paypal_finanzierungs_info_zahlbar=dmc_get_gm_order_finanzierung_information($orders_id);
							}
						}//end else
						// Ausland/Netto initialisieren
				
						$eg_ausland=0;
						$ausland=0;
						$nettokunde=0;
						$customers_status_discount=0;
						if ( $shipping_amount==0)
							$shipping_amount=$gesamtversandkosten;
					/*	$shipping_amount_gros=0;			 				
						$shipping_amount=0;							
						$rcm_versandkosten_net = 0;
						$rcm_versandkosten_tax = 0;
						$rcm_versandkosten = 0;
						*/
						// Steuerlich relevanter ISO Code  
						$iso_code=$billing_country_iso_code;
						
						// Prüfen, ob  Ausland
						if ($iso_code!='DE' && $iso_code!='D' && $iso_code!='Deutschland' && $iso_code!='') { //Deutschland
							$ausland=1;
							$nettokunde=1;
						}							
						// Prüfen, ob EG Ausland
						// Kundengruppenrabatt							
						if (($iso_code=='AT')  //Österreich
						|| ($iso_code=='B') //  Belgien
						|| ($iso_code=='BE') //  Belgien
					 	|| ($iso_code=='BG') //Bulgarien
					 	|| ($iso_code=='CZ') //Tschechische Republik
					 	|| ($iso_code=='CY') //Cypern
					 	|| ($iso_code=='DK') //Dänemark
					 	|| ($iso_code=='E') //  Spanien
					 	|| ($iso_code=='EE') //  Spanien
					 	|| ($iso_code=='ES') //  Spanien
					 	|| ($iso_code=='EL') //Griechenland
					 	|| ($iso_code=='GR') //Griechenland
					 	|| ($iso_code=='F') //  Frankreich
					 	|| ($iso_code=='FR') //  Frankreich
					 	|| ($iso_code=='FI') // Finnland
						|| ($iso_code=='HU') //Ungarn
					 	|| ($iso_code=='UK') //Vereinigtes Königreich
					 	|| ($iso_code=='IE') //  Irland
					 	|| ($iso_code=='IRL') //  Irland
					 	|| ($iso_code=='I') // //Italien
					 	|| ($iso_code=='IT') // //Italien
						|| ($iso_code=='HR') //Kroatien
					 	|| ($iso_code=='L') //  Luxemburg
					 	|| ($iso_code=='LU') //  Luxemburg
					 	|| ($iso_code=='LT') //Litauen
					 	|| ($iso_code=='LV') //Lettland
					 	|| ($iso_code=='MT') //  Malta
					 	|| ($iso_code=='NL') //Niederlande
					 	|| ($iso_code=='P') // Portugal
					 	|| ($iso_code=='PT') // Portugal
					 	|| ($iso_code=='PL') //Polen
					 	|| ($iso_code=='RO') //  Rumänien
					 	|| ($iso_code=='RS') //  Serbien
					 	|| ($iso_code=='SE') //  Schweden
					 	|| ($iso_code=='SI') //Slowenien
					 	|| ($iso_code=='SK'))  // Slowakische Republik
						{
							$eg_ausland=1;
							// Prüfen, um UmStIdentNr gesetzt
							if ($customers_ustid!='') $nettokunde=1;
							else $nettokunde=0;
						} // end if EG Ausland
						$transaktionsnummern == ''; // Bisher nur veyton
						
						// Besonderheit: Bei Ausland nicht EU muss auch die Lieferung ins Ausland nicht EU erfolgen, sonst nicht steuerfrei
						if ($eg_ausland==0 && $ausland==1) {
							// Prüfung auf Lieferadresse Ausland und nicht EU
							if (($delivery_country_iso_code=='AT')  //Österreich
							|| ($delivery_country_iso_code=='B') //  Belgien
							|| ($delivery_country_iso_code=='BE') //  Belgien
							|| ($delivery_country_iso_code=='BG') //Bulgarien
							|| ($delivery_country_iso_code=='CZ') //Tschechische Republik
							|| ($delivery_country_iso_code=='CY') //Cypern
							|| ($delivery_country_iso_code=='DK') //Dänemark
							|| ($delivery_country_iso_code=='E') //  Spanien
							|| ($delivery_country_iso_code=='EE') //  Spanien
							|| ($delivery_country_iso_code=='ES') //  Spanien
							|| ($delivery_country_iso_code=='EL') //Griechenland
							|| ($delivery_country_iso_code=='GR') //Griechenland
							|| ($delivery_country_iso_code=='F') //  Frankreich
							|| ($delivery_country_iso_code=='FR') //  Frankreich
							|| ($delivery_country_iso_code=='FI') // Finnland
							|| ($delivery_country_iso_code=='HU') //Ungarn
							|| ($delivery_country_iso_code=='UK') //Vereinigtes Königreich
							|| ($delivery_country_iso_code=='IE') //  Irland
							|| ($delivery_country_iso_code=='IRL') //  Irland
							|| ($delivery_country_iso_code=='I') // //Italien
							|| ($delivery_country_iso_code=='IT') // //Italien
							|| ($delivery_country_iso_code=='HR') //Kroatien
							|| ($delivery_country_iso_code=='L') //  Luxemburg
							|| ($delivery_country_iso_code=='LU') //  Luxemburg
							|| ($delivery_country_iso_code=='LT') //Litauen
							|| ($delivery_country_iso_code=='LV') //Lettland
							|| ($delivery_country_iso_code=='MT') //  Malta
							|| ($delivery_country_iso_code=='NL') //Niederlande
							|| ($delivery_country_iso_code=='P') // Portugal
							|| ($delivery_country_iso_code=='PT') // Portugal
							|| ($delivery_country_iso_code=='PL') //Polen
							|| ($delivery_country_iso_code=='RO') //  Rumänien
							|| ($delivery_country_iso_code=='RS') //  Serbien
							|| ($delivery_country_iso_code=='SE') //  Schweden
							|| ($delivery_country_iso_code=='SI') //Slowenien
							|| ($delivery_country_iso_code=='SK'))  // Slowakische Republik
							{
								$nettokunde=0;
							} // end if EG Ausland
						
						}
						
						switch ($orders['payment_class']) {
							case 'banktransfer':
							// Bankverbindung laden, wenn aktiv
							$bank_name = '';
							$bank_blz  = '';
							$bank_kto  = '';
							$bank_inh  = '';
							$bank_stat = -1;
							$bank_sql = "select * from banktransfer where orders_id = " . $orders_id;
							// 		$bank_sql = "select * from sepabanktransfer where customers_id = " . $orders['customers_id']." ORDER BY sepabanktransfer_id LIMIT 1";
        					$bank_query = dmc_db_query($bank_sql);
							
							if (($bank_query) && ($bankdata = dmc_db_fetch_array($bank_query))) {
								$bank_name = $bankdata['banktransfer_bankname'];
								$bank_blz  = $bankdata['banktransfer_blz'];
								$bank_kto  = $bankdata['banktransfer_number'];
								$bank_bic  = $bankdata['banktransfer_bic'];
								$bank_iban  = $bankdata['banktransfer_iban'];
								$bank_inh  = $bankdata['banktransfer_owner'];
								$bank_stat = $bankdata['banktransfer_status'];
								/*
								         $bank_name = $bankdata['sepabanktransfer_name'];
											  $bank_blz  = $bankdata['sepabanktransfer_bic'];
											  $bank_kto  = $bankdata['sepabanktransfer_iban'];
											  $bank_inh  = $bankdata['sepabanktransfer_owner'];
									 */
							}
						} // end switch
					
					// xml header 
				//	$schema .="<POSI>981</POSI>\n";
					include('./dmc_includes/dmc_xml_order_opentrans_header2.inc.php');// xml products 	
		//		fwrite($dateihandle, "840\n");
				//	$schema .="<POSI2>984</POSI2>\n";
					include('dmc_includes/dmc_xml_order_opentrans_products.inc.php'); // includes itself dmc_xml_order_single_product.inc.php					
		//			fwrite($dateihandle, "1361\n");
					include('dmc_includes/dmc_xml_order_opentrans_footer.inc.php');// xml footer 						
		//			fwrite($dateihandle, "1363\n");
					$schema .= '</ORDER>' . "\n"; 
		//			fwrite($dateihandle, "1365\n");
					// $orders++;
					// Update wooCommerce Order Status 
					if ($change_status !== false) {
						if ((UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1')  && SHOPSYSTEM == 'woocommerce') {
							if (SHOP_VERSION=='2') 	{	// abweichende Logik bei bestimmten Systemen
								$sql=	"UPDATE " . DB_PREFIX . "posts ".
										"SET post_status='".ORDER_STATUS_SET."'".
										" WHERE post_type = 'shop_order' AND id=".$orders_id;
								fwrite($dateihandle, "UPDATE_ORDER_STATUS woo $sql\n");	
								dmc_db_query($sql);	
							} else { // Standard	
								$sql=	"UPDATE " . DB_PREFIX . "term_relationships ".
										"SET term_taxonomy_id=".
										"(SELECT tt.term_taxonomy_id FROM " . DB_PREFIX . "term_taxonomy AS tt ".
										"INNER JOIN " . DB_PREFIX . "terms AS t ON tt.term_id=t.term_id WHERE t.name ='".ORDER_STATUS_SET."') ".
										" WHERE object_id=".$orders_id;
								fwrite($dateihandle, "UPDATE_ORDER_STATUS woo $sql\n");	
								dmc_db_query($sql);	

							}
						} // end if update order status für woocommerce 
						
						// Update Shopware Order Status 
						if ((UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1')  && SHOPSYSTEM == 'shopware') {
							$sql=	"UPDATE " . DB_TABLE_PREFIX . "s_order ".
									"SET status= ".
									 "(SELECT id FROM ".DB_TABLE_PREFIX ."s_core_states WHERE description='".ORDER_STATUS_SET."') ".
									" WHERE id=".$orders_id;
							fwrite($dateihandle, "UPDATE_ORDER_STATUS shopware $sql\n");	
							dmc_db_query($sql);	
						} // end if update order status für Shopware 
						
						// Update Shopware Order Status joomshopping
						if ((UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1')  && SHOPSYSTEM == 'joomshopping') {
							$sql=	"UPDATE " . DB_TABLE_PREFIX . "jshopping_orders ".
									"SET order_status= ".
									 "(SELECT status_id FROM ".DB_TABLE_PREFIX ."jshopping_order_status WHERE ".
									 "status_id='".ORDER_STATUS_SET."' OR `name_de-DE` ='".ORDER_STATUS_SET."' OR status_code='".ORDER_STATUS_SET."') ".
									" WHERE order_id=".$orders_id." LIMIT 1";
							fwrite($dateihandle, "UPDATE_ORDER_STATUS joomshopping $sql\n");	
							dmc_db_query($sql);	
						} // end if update order status für Shopware 
						
						// Update Order Status
						if ((UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1') 
						&& SHOPSYSTEM != 'presta' 
						&& SHOPSYSTEM != 'virtuemart' 
						&& SHOPSYSTEM != 'woocommerce'
						&& SHOPSYSTEM != 'shopware'
						&& SHOPSYSTEM != 'joomshopping'
						) {				
							$Status = ORDER_STATUS_SET; 	// 3="In Bearbeitung" (2=Versendet) als neuer Status
							$LangID = 2;	// 2= deutsch
		 
							if (SHOPSYSTEM != 'veyton') {
								$orders_status_array = array();
								$cmd = "select orders_status_id, orders_status_name from " .
								TABLE_ORDERS_STATUS . " where language_id = '" . (int)$LangID . "'";
								$orders_status_query = dmc_db_query($cmd);
						
								while ($orders_status = dmc_db_fetch_array($orders_status_query)) {
									$orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
								}
							} // end if veyton
				  
							if (($orders_id != 0 && isset($orders_status_array[$Status])) || SHOPSYSTEM == 'veyton') {
								if (strtolower(SHOPSYSTEM) == 'veyton')
									 $cmd = "select billing_lastname AS customers_name, customers_email_address, orders_status, date_purchased, language_code AS language from " .
									TABLE_ORDERS . " where orders_id = '" . $orders_id . "'";
								else 	if (strtolower(SHOPSYSTEM) == 'osc')
									 $cmd = "select customers_name, customers_email_address, orders_status, date_purchased, 'de' AS language from " .
									TABLE_ORDERS . " where orders_id = '" . $orders_id . "'";
								else 
									 $cmd = "select customers_name, customers_email_address, orders_status, date_purchased, language from " .
									TABLE_ORDERS . " where orders_id = '" . $orders_id . "'";
			  
								$Order_Query = dmc_db_query($cmd);
								
								if ($Order = dmc_db_fetch_array($Order_Query)) {
									if ($Order['orders_status'] != $Status) {
										$update_sql_data = array(
										'orders_status' => $Status,
										'last_modified' => 'now()');
										// dmc_sql_update_array(TABLE_ORDERS, $update_sql_data,  "orders_id='" . $orders_id . "'");
										$query="update ".TABLE_ORDERS." SET orders_status='".$Status."', last_modified	= now() WHERE orders_id=" . $orders_id; 
										dmc_sql_query($query);
										// Kundeninformation per eMail senden
										if(ORDER_STATUS_NOTIFY_CUSTOMER=='true' &&
											strpos(strtolower(SHOPSYSTEM), 'veyton') === false && 
											strpos(strtolower(SHOPSYSTEM), 'gambiogx') === false && 
											strpos(strtolower(SHOPSYSTEM), 'osc') === false  ) { 
											// require functionblock for mails 
											require_once(DIR_WS_CLASSES.'class.phpmailer.php');
											require_once(DIR_FS_INC . 'xtc_php_mail.inc.php');
											require_once(DIR_FS_INC . 'xtc_add_tax.inc.php');
											require_once(DIR_FS_INC . 'xtc_not_null.inc.php');
											require_once(DIR_FS_INC . 'changedataout.inc.php');
											require_once(DIR_FS_INC . 'xtc_href_link.inc.php');
											require_once(DIR_FS_INC . 'xtc_date_long.inc.php');
											require_once(DIR_FS_INC . 'xtc_check_agent.inc.php');
											$smarty = new Smarty;

											$smarty->assign('language', $Order['language']);
											$smarty->caching = false;
											$smarty->template_dir=DIR_FS_CATALOG.'templates';
											$smarty->compile_dir=DIR_FS_CATALOG.'templates_c';
											$smarty->config_dir=DIR_FS_CATALOG.'lang';
											$smarty->assign('tpl_path','templates/'.CURRENT_TEMPLATE.'/');
											$smarty->assign('logo_path',HTTP_SERVER  . DIR_WS_CATALOG.'templates/'.CURRENT_TEMPLATE.'/img/');
											$smarty->assign('NAME',$Order['customers_name']);
											$smarty->assign('ORDER_NR',$orders_id);
											$smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'orders_id=' . $orders_id, 'SSL'));
											$smarty->assign('ORDER_DATE',xtc_date_long($Order['date_purchased']));
											$smarty->assign('NOTIFY_COMMENTS', '');
											$smarty->assign('ORDER_STATUS', $orders_status_array[$Status]);

											$html_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.html');
											$txt_mail=$smarty->fetch(CURRENT_TEMPLATE . '/admin/mail/'.$Order['language'].'/change_order_mail.txt');

											// send mail with html/txt template
											dmc_php_mail_4_xtc(EMAIL_BILLING_ADDRESS,
														 EMAIL_BILLING_NAME ,
														 $Order['customers_email_address'],
														 $Order['customers_name'],
														 '',
														 EMAIL_BILLING_REPLY_ADDRESS,
														 EMAIL_BILLING_REPLY_ADDRESS_NAME,
														 '',
														 '',
														 EMAIL_BILLING_SUBJECT,
														 $html_mail ,
														 $txt_mail);
										} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
											// Veyton Status setzen und Kunde informieren
											/*include('../../xtFramework/classes/class.order.php');
											// benoetigte data setzen
											$status=ORDER_STATUS_SET;
											$data["orders_id"]= (integer)($orders_id);
											$comments=''; 
											$send_email=ORDER_STATUS_NOTIFY_CUSTOMER;
											$send_comments=ORDER_STATUS_NOTIFY_CUSTOMER;
											$trigger = 'user';
											$callback_id = 0;
											// Veyton Funktion aus class aufrufen
											_updateOrderStatus($status, $comments, $send_email, $send_comments,$trigger,$callback_id); */
										} // endif Kunden Info Mail senden

										$insert_sql_data = array(
											  'orders_id' => $orders_id,
											  'orders_status_id' => $Status,
											  'date_added' => 'now()',
											  'customer_notified' => '1',
											  'comments' => ''
										);
										dmc_sql_insert_array(TABLE_ORDERS_STATUS_HISTORY, $insert_sql_data);
										fwrite($dateihandle, "#1486 UPDATE_ORDER_STATUS $sql\n");						
									}//end order_status
								}//end order_query
							}//end order
				
						//$schema .= '</ORDER>' . "\n\n"; ''      
						} else if (strtolower(SHOPSYSTEM) == 'presta' && (UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1')) {			
							fwrite($dateihandle, "#1500 UPDATE_ORDER_STATUS presta ... ");
							$Status = ORDER_STATUS_SET; 	
							$update_sql_data = array(
								'current_state' => $Status,
								'date_upd' => 'now()');
							dmc_sql_update_array(TABLE_ORDERS, $update_sql_data,  "id_order='" . $orders_id . "'");
							fwrite($dateihandle, "done \n");
						} else if (strtolower(SHOPSYSTEM) == 'virtuemart' && (UPDATE_ORDER_STATUS=='true' || UPDATE_ORDER_STATUS=='1')) {			
							$Status = ORDER_STATUS_SET; 	
							$update_sql_data = array(
								'order_status' => $Status,
								'modified_on' => 'now()');
							dmc_sql_update_array(TABLE_ORDERS, $update_sql_data,  "orders_id='" . $orders_id . "'");
							fwrite($dateihandle, "#1500 UPDATE_ORDER_STATUS virtuemart \n");	
						} // end if Update Order Status  
					} // end if Update Order Status  mit $change_status !== false
					
					//echo $schema;    // print order xml	
				}//ende while($orders = dmc_db_fetch_array($orders_query))
	
				
				if ($exists == false)		// Bestellungen vorhanden
					echo "Keine Bestellungen vorhanden";
				else 
					// Aktuelle Order ID als FIRST_ORDER_ID übergeben
					if (defined(UPDATE_FIRST_ORDER_ID))
						if (UPDATE_FIRST_ORDER_ID == true) {
							$dateihandleOrderID = fopen("./conf/definitions/FIRST_ORDER_ID.dmc","w");
							fwrite($dateihandleOrderID, $orders_id+1);
							fclose($dateihandleOrderID);
						}
						
				//echo $schema;    // print order xml		

				//fwrite($dateihandle, "#1520 schema= $schema\n");
				
				 //$schema .= '</ORDER>' . "\n\n";       
				 echo trim($schema);
			break;

		case 'products_export':
			// Artikelexport
			fwrite($dateihandle, "products_export\n");	

			$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
				'<PRODUCTS_LIST>' . "\n".
				'<PRODUCTS>' . "\n";
			//echo $schema;
			if (strtolower(SHOPSYSTEM) == 'woocommerce') {
					
				$sql =  "SELECT p.ID AS products_id, '0' AS products_fsk18, ".
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_stock' AND post_id=p.ID) AS products_quantity, ".
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_sku' AND post_id=p.ID) AS products_model, ".
						"'' AS products_ean, '' AS products_image, " .
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_price' AND post_id=p.ID) AS products_price, ".
						"p.post_date AS products_date_added, p.post_modified AS products_last_modified, '28.02.1973'  AS products_date_available, " .
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_weight' AND post_id=p.ID) AS products_weight, ".
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_visibility' AND post_id=p.ID) AS products_status, ".
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='_tax_class' AND post_id=p.ID) AS products_tax_class_id, ".
						"'' AS manufacturers_id, ".
						"'' AS manufacturers_name, ".
						"(SELECT meta_value FROM " . DB_PREFIX."postmeta WHERE meta_key ='total_sales' AND post_id=p.ID) AS products_ordered, ".
						"p.guid AS product_link, ".
						"p.post_title AS products_name, p.post_excerpt AS products_short_description, p.post_content AS products_description ".
						"FROM " . DB_PREFIX."posts AS p WHERE p.post_type='product' AND p.post_status = 'publish' ";
 			} else {
				$sql =  "select products_id,products_fsk18, products_quantity, products_model, products_ean, products_image, products_price, " .
					"products_date_added, products_last_modified, products_date_available, products_weight, " .
					"products_status, products_tax_class_id, manufacturers_id, manufacturers_id AS manufacturers_name, products_ordered, ".
					"(SELECT CASE WHEN products_vpe_name='Liter' THEN 'L' ELSE 'Stk' END FROM `products_vpe` WHERE products_vpe_id=products_vpe LIMIT 1) AS  products_einheit ".
					"FROM " . TABLE_PRODUCTS." WHERE products_price>0 "; 
					// ." 	limit 549,1";
			
				$from = ($_GET['products_from']);
				$anz  = ($_GET['products_count']);
				
				if (isset($from)) {
				  if (!isset($anz)) $anz=10; 
				  $sql .= " limit " . $from . "," . $anz;
				}
			}
			// $sql .= " limit 10 "; 
			fwrite($dateihandle, "sql 976 =$sql\n");	
			$nummer=0;
			$orders_query = dmc_db_query($sql);
			
			while ($products = dmc_db_fetch_array($orders_query)) {
				if (strtolower(SHOPSYSTEM) == 'woocommerce') {
					$artikellink=$products['product_link'];
					
				} else {
					$artikellink=HTTP_SERVER.DIR_WS_CATALOG.$xtc_filename['product_info'].'?products_id='.$products['products_id'];
					// Herstellername ermitteln
					$query=dmc_db_query("SELECT manufacturers_name FROM manufacturers WHERE manufacturers_id='".$products['manufacturers_id']."'");
					$hersteller=array();
					while ($hersteller_data=dmc_db_fetch_array($query)) {
						$hersteller[]=$hersteller_data['manufacturers_name'];
					}
					$products['manufacturers_name']=$hersteller[0];
				}
				
				if ($products['products_model']=='') $products['products_model']='leer1234';
				
				$nummer++;
				$schema  .= '<PRODUCT_INFO>' . "\n" .
					 '<PRODUCT_DATA>' . "\n" .
					 '<PRODUCT_EXPORT_NO>'.$nummer.'</PRODUCT_EXPORT_NO>' . "\n" .
					 '<PRODUCT_ID>'.$products['products_id'].'</PRODUCT_ID>' . "\n" .
					 '<PRODUCT_DEEPLINK>'. $artikellink.'</PRODUCT_DEEPLINK>' . "\n" .
					 '<PRODUCT_QUANTITY>' . $products['products_quantity'] . '</PRODUCT_QUANTITY>' . "\n" .
					 '<PRODUCT_MODEL>' . htmlspecialchars($products['products_model']) . '</PRODUCT_MODEL>' . "\n" .
						'<PRODUCT_EAN>' . htmlspecialchars($products['products_ean']) . '</PRODUCT_EAN>' . "\n" .
						'<PRODUCT_EINHEIT>' . htmlspecialchars($products['products_einheit']) . '</PRODUCT_EINHEIT>' . "\n" .
					
					 '<PRODUCT_FSK18>' . htmlspecialchars($products['products_fsk18']) . '</PRODUCT_FSK18>' . "\n" .
					 '<PRODUCT_IMAGE>' . htmlspecialchars($products['products_image']) . '</PRODUCT_IMAGE>' . "\n";
	
				$schema .= '<PRODUCT_PRICE>' . $products['products_price'] . '</PRODUCT_PRICE>' . "\n";
				
				if ($products['products_image']!='') {
					if (SHOPSYSTEM != 'woocommerce') {
						$schema .= '<PRODUCT_IMAGE_POPUP>'.HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_POPUP_IMAGES.$products['products_image'].'</PRODUCT_IMAGE_POPUP>'. "\n" .
						'<PRODUCT_IMAGE_SMALL>'.HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_INFO_IMAGES.$products['products_image'].'</PRODUCT_IMAGE_SMALL>'. "\n" .
						'<PRODUCT_IMAGE_THUMBNAIL>'.HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$products['products_image'].'</PRODUCT_IMAGE_THUMBNAIL>'. "\n" .
						'<PRODUCT_IMAGE_ORIGINAL>'.HTTP_SERVER.DIR_WS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$products['products_image'].'</PRODUCT_IMAGE_ORIGINAL>'. "\n";
					}

				}//ende if image_small/thumb/org
			
				if (SHOPSYSTEM != 'woocommerce') {
					// require_once(DIR_FS_INC .'xtc_get_customers_statuses.inc.php');
					// $customers_status=xtc_get_customers_statuses();
		   
					for ($i=1,$n=sizeof($customers_status);$i<$n; $i++) { 
						if ($customers_status[$i]['id']!=0) {
							$schema .= "<PRODUCT_GROUP_PRICES ID='".$customers_status[$i]['id']."' NAME='".$customers_status[$i]['text']. "'>". "\n";

							$group_price_query=dmc_db_query("SELECT * FROM personal_offers_by_customers_status_".$customers_status[$i]['id']." where products_id = '".$products['products_id']."'");

							while ($group_price_data=dmc_db_fetch_array($group_price_query)) {
								$schema .='<PRICE_ID>'.$group_price_data['price_id'].'</PRICE_ID>';
								$schema .='<PRODUCT_ID>'.$group_price_data['products_id'].'</PRODUCT_ID>';
								$schema .='<QTY>'.$group_price_data['quantity'].'</QTY>';
								$schema .='<PRICE>'.$group_price_data['personal_offer'].'</PRICE>';
							}//end while innen
						
						$schema .= "</PRODUCT_GROUP_PRICES>\n";
						}//end if
					}//end for
			
					// products Options
					$products_attributes = '';
					$products_options_data = array();
					$products_options_array = array();
					$products_attributes_query = dmc_db_query("select count(*) AS total
													 from " . TABLE_PRODUCTS_OPTIONS . "
													 popt, " . TABLE_PRODUCTS_ATTRIBUTES . "
													 patrib where patrib.products_id='" . $products['products_id'] . "'
													 and patrib.options_id = popt.products_options_id
													 and popt.language_id = '" . LANG_ID . "'");

					$products_attributes = dmc_db_fetch_array($products_attributes_query);

					if ($products_attributes['total'] > 0) {
						$products_options_name_query = dmc_db_query("select distinct
											   popt.products_options_id,
											   popt.products_options_name
											   from " . TABLE_PRODUCTS_OPTIONS . "
											   popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib
											   where patrib.products_id='" . $products['products_id'] . "'
											   and patrib.options_id = popt.products_options_id
											   and popt.language_id = '" . LANG_ID . "' order by popt.products_options_name");
						$row = 0;
						$col = 0;
						$products_options_data=array();

						while ($products_options_name = dmc_db_fetch_array($products_options_name_query)) {
							$selected = 0;
							$products_options_array = array();
							
							$products_options_data[$row]=array(
										   'NAME' => $products_options_name['products_options_name'],
										   'ID' => $products_options_name['products_options_id'],
										   'DATA' => '');
							
							$products_options_query = dmc_db_query("select
												pov.products_options_values_id,	pov.products_options_values_name,
												pa.attributes_model, pa.options_values_price, pa.options_values_weight,
												pa.price_prefix, pa.weight_prefix, pa.attributes_stock, pa.attributes_model
												from " . TABLE_PRODUCTS_ATTRIBUTES . "
												pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . "
												pov where
												pa.products_id = '" . $products['products_id'] . "'
												and pa.options_id = '" . $products_options_name['products_options_id'] . "' and
												pa.options_values_id = pov.products_options_values_id and
												pov.language_id = '" . LANG_ID . "' order by pov.products_options_values_name");
							$col = 0;
							
							while ($products_options = dmc_db_fetch_array($products_options_query)) {
								$products_options_array[] = array('id' => $products_options['products_options_values_id'], 'text' => $products_options['products_options_values_name']);
								
								if ($products_options['options_values_price'] != '0') {
									$products_options_array[sizeof($products_options_array)-1]['text'] .=  ' '.$products_options['price_prefix'].' '.$products_options['options_values_price'].' '.$_SESSION['currency'] ;
								}
								$price='';
								$products_options_data[$row]['DATA'][$col] = array(
									'ID' => $products_options['products_options_values_id'],
									'TEXT' =>$products_options['products_options_values_name'],
									'MODEL' =>$products_options['attributes_model'],
									'WEIGHT' =>$products_options['options_values_weight'],
									'PRICE' =>$products_options['options_values_price'],
									'WEIGHT_PREFIX' =>$products_options['weight_prefix'],
									'PREFIX' =>$products_options['price_prefix']);
							$col++;
							}
						$row++;
						}
					}//end if

					if (sizeof($products_options_data)!=0) {
						for ($i=0,$n=sizeof($products_options_data);$i<$n;$i++) {
							$schema .= "<PRODUCT_ATTRIBUTES NAME='".html2ascii($products_options_data[$i]['NAME'])."'>";

							for ($ii=0,$nn=sizeof($products_options_data[$i]['DATA']);$ii<$nn;$ii++) {
								$schema .= '<OPTION>';
								$schema .= '<ID>'.$products_options_data[$i]['DATA'][$ii]['ID'].'</ID>';
								$schema .= '<MODEL>'.$products_options_data[$i]['DATA'][$ii]['MODEL'].'</MODEL>';
								$schema .= '<TEXT>'.$products_options_data[$i]['DATA'][$ii]['TEXT'].'</TEXT>';
								$schema .= '<WEIGHT>'.$products_options_data[$i]['DATA'][$ii]['WEIGHT'].'</WEIGHT>';
								$schema .= '<PRICE>'.$products_options_data[$i]['DATA'][$ii]['PRICE'].'</PRICE>';
								$schema .= '<WEIGHT_PREFIX>'.$products_options_data[$i]['DATA'][$ii]['WEIGHT_PREFIX'].'</WEIGHT_PREFIX>';
								$schema .= '<PREFIX>'.$products_options_data[$i]['DATA'][$ii]['PREFIX'].'</PREFIX>';
								$schema .= '</OPTION>';
							}
							$schema .= '</PRODUCT_ATTRIBUTES>';
						}//end for innen
					}//end for aussen
				
					// group prices
					//require_once(DIR_FS_INC .'xtc_get_tax_rate.inc.php');

					if (SWITCH_MWST=='true') {
					   // switch IDs
					   if ($products['products_tax_class_id']==1) $products['products_tax_class_id']=2;
					   if ($products['products_tax_class_id']==2) $products['products_tax_class_id']=1;
					}
				} // end if nicht woocommerce
				
				// Steuersatz ermitteln
			
					$steuerprozent = $products['products_tax_class_id'];
					if ($products['products_tax_class_id'] == '') {	
						$products['products_tax_class_id'] = '1';
						$steuerprozent = 19;
					} else {	
						$products['products_tax_class_id'] = '2';
						$steuerprozent = 7;
					}
				
				
				$schema .=
					'<PRODUCT_WEIGHT>' . $products['products_weight'] . '</PRODUCT_WEIGHT>' . "\n" .
					'<PRODUCT_STATUS>' . $products['products_status'] . '</PRODUCT_STATUS>' . "\n" .
					'<PRODUCT_TAX_CLASS_ID>' . $products['products_tax_class_id'] . '</PRODUCT_TAX_CLASS_ID>' . "\n"  .
					'<PRODUCT_TAX_RATE>' . $steuerprozent . '</PRODUCT_TAX_RATE>' . "\n"  .
					'<MANUFACTURERS_ID>' . $products['manufacturers_id'] . '</MANUFACTURERS_ID>' . "\n" .
					'<MANUFACTURERS_NAME>' . $products['manufacturers_name'] . '</MANUFACTURERS_NAME>' . "\n" .
					'<PRODUCT_DATE_ADDED>' . $products['products_date_added'] . '</PRODUCT_DATE_ADDED>' . "\n" .
					'<PRODUCT_LAST_MODIFIED>' . $products['products_last_modified'] . '</PRODUCT_LAST_MODIFIED>' . "\n" .
					'<PRODUCT_DATE_AVAILABLE>' . $products['products_date_available'] . '</PRODUCT_DATE_AVAILABLE>' . "\n" .
					'<PRODUCTS_ORDERED>' . $products['products_ordered'] . '</PRODUCTS_ORDERED>' . "\n" ;

					$categories_query=dmc_db_query("SELECT
								 categories_id
								 FROM ".TABLE_PRODUCTS_TO_CATEGORIES."
								 where products_id='".$products['products_id']."'");
 
					$categories=array();
					
					while ($categories_data=dmc_db_fetch_array($categories_query)) {
						$categories[]=$categories_data['categories_id'];
					}
		
					$categories=implode(',',$categories);
					$schema .= '<PRODUCTS_CATEGORIES>' . $categories . '</PRODUCTS_CATEGORIES>' . "\n" ;

					// Beschreibungen
					if (strtolower(SHOPSYSTEM) == 'woocommerce') {
						$schema .= "<PRODUCT_DESCRIPTION ID='1' CODE='DE' NAME='-'>\n";
						$schema .= "<NAME>" . umlaute_order_export(substr($products["products_name"],0,45)) . "</NAME>" . "\n" ;
						$schema .=  "<URL>" . htmlspecialchars($products["products_link"]) . "</URL>" . "\n" ;
						$schema .=  "<DESCRIPTION>" . umlaute_order_export($products["products_description"]) . "</DESCRIPTION>" . "\n";
						$schema .=  "<SHORT_DESCRIPTION>" . umlaute_order_export($products["products_short_description"]) . "</SHORT_DESCRIPTION>" . "\n";
						$schema .=  "<META_TITLE></META_TITLE>" . "\n";
						$schema .=  "<META_DESCRIPTION></META_DESCRIPTION>" . "\n";
						$schema .=  "<META_KEYWORDS></META_KEYWORDS>" . "\n";
						$schema .= "</PRODUCT_DESCRIPTION>\n";					
					} else {
						$sql= "select products_id,
								language_id, products_name, " . TABLE_PRODUCTS_DESCRIPTION .
								".products_description, products_short_description,
								products_meta_title, products_meta_description, products_meta_keywords,
								products_url, name AS language_name, code AS language_code " .
								"from " . TABLE_PRODUCTS_DESCRIPTION . ", " . TABLE_LANGUAGES .
								" where " . TABLE_PRODUCTS_DESCRIPTION . ".language_id=" . TABLE_LANGUAGES . ".languages_id " .
								"and " . TABLE_PRODUCTS_DESCRIPTION . ".products_id=" . $products['products_id'];
							
						$detail_query = dmc_db_query($sql);
						
						while ($details = dmc_db_fetch_array($detail_query)) {
							$schema .= "<PRODUCT_DESCRIPTION ID='" . $details["language_id"] ."' CODE='" . $details["language_code"] . "' NAME='" . $details["language_name"] . "'>\n";

							if ($details["products_name"] !='Array') {
								$schema .= "<NAME>" . umlaute_order_export($details["products_name"]) . "</NAME>" . "\n" ;
							}
							$schema .=  "<URL>" . htmlspecialchars($details["products_url"]) . "</URL>" . "\n" ;

							$prod_details = $details["products_description"];
							if ($prod_details != 'Array') {
								$schema .=  "<DESCRIPTION>" . umlaute_order_export($details["products_description"]) . "</DESCRIPTION>" . "\n";
								$schema .=  "<SHORT_DESCRIPTION>" . umlaute_order_export($details["products_short_description"]) . "</SHORT_DESCRIPTION>" . "\n";
								$schema .=  "<META_TITLE>" . umlaute_order_export($details["products_meta_title"]) . "</META_TITLE>" . "\n";
								$schema .=  "<META_DESCRIPTION>" . umlaute_order_export($details["products_meta_description"]) . "</META_DESCRIPTION>" . "\n";
								$schema .=  "<META_KEYWORDS>" . umlaute_order_export($details["products_meta_keywords"]) . "</META_KEYWORDS>" . "\n";
							}
							$schema .= "</PRODUCT_DESCRIPTION>\n";
						}//end while details
					}
					
					$schema .= '</PRODUCT_DATA>' . "\n" .
					'</PRODUCT_INFO>' . "\n";
				}
				$schema .= '</PRODUCTS>' . "\n";
				
				
				$schema .= '</PRODUCTS_LIST>' . "\n";
				echo $schema;
			break;

			case 'customers_export':
				

				$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
						'<CUSTOMERS>' . "\n";
	
				echo $schema;

				$from = ($_GET['customers_from']);
				$anz  = ($_GET['customers_count']);

				$address_query = "select c.customers_gender, c.customers_id,c.customers_cid, c.customers_firstname, c.customers_lastname,c.customers_dob, c.customers_email_address, c.customers_telephone, c.customers_fax,
					ci.customers_info_date_account_created  AS customers_date_account_created, 
					a.entry_firstname AS firstname, a.entry_lastname AS lastname, a.entry_company AS company, a.entry_street_address AS street_address, a.entry_city AS city, a.entry_postcode AS postcode, 
					co.countries_iso_code_2 AS country 
					from " . TABLE_CUSTOMERS. " c, ". TABLE_CUSTOMERS_INFO. " ci, ". TABLE_ADDRESS_BOOK . " a , ".TABLE_COUNTRIES." co
					where c.customers_id = ci.customers_info_id AND c.customers_id = a.customers_id 
					AND c.customers_default_address_id = a.address_book_id AND a.entry_country_id  = co.countries_id";

				if (isset($from)) {
					if (!isset($anz)) $anz = 1000;
					$address_query.= " limit " . $from . "," . $anz;
				}

				$address_result = dmc_db_query($address_query);

				while ($address = dmc_db_fetch_array($address_result)) {
					$schema = '<CUSTOMERS_DATA>' . "\n";		
						foreach($address AS $key => $value) 
						{
							$schema.= '<'.strtoupper($key).'>'.htmlspecialchars($value).'</'.strtoupper($key).'>'."\n";
						}
					$schema .= '</CUSTOMERS_DATA>' . "\n";		
					echo $schema;
				}

				$schema = '</CUSTOMERS>' . "\n\n";
				echo $schema;
			break;

			//-- end action for customers
			// Newsletter export
			case 'customers_newsletter_export':
				
				
				$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
					'<CUSTOMERS>' . "\n".

				$from = ($_GET['customers_from']);
				$anz  = ($_GET['customers_count']);

				$address_query = "select * from " . TABLE_CUSTOMERS. " where customers_newsletter = 1";

				if (isset($from)) {
					if (!isset($anz)) $anz = 1000;
					$address_query.= " limit " . $from . "," . $anz;
				}
				$address_result = dmc_db_query($address_query);

				while ($address = dmc_db_fetch_array($address_result)) {
					$schema .= '<CUSTOMERS_DATA>' . "\n";
					$schema .= '<CUSTOMERS_ID>' . $address['customers_id'] . '</CUSTOMERS_ID>' . "\n";
					$schema .= '<CUSTOMERS_CID>' . $address['customers_cid'] . '</CUSTOMERS_CID>' . "\n";
					$schema .= '<CUSTOMERS_GENDER>' . $address['customers_gender'] . '</CUSTOMERS_GENDER>' . "\n";
					$schema .= '<CUSTOMERS_FIRSTNAME>' . $address['customers_firstname'] . '</CUSTOMERS_FIRSTNAME>' . "\n";
					$schema .= '<CUSTOMERS_LASTNAME>' . $address['customers_lastname'] . '</CUSTOMERS_LASTNAME>' . "\n";
					$schema .= '<CUSTOMERS_EMAIL_ADDRESS>' . $address['customers_email_address'] . '</CUSTOMERS_EMAIL_ADDRESS>' . "\n";
					$schema .= '</CUSTOMERS_DATA>' . "\n";		
				}

				$schema .= '</CUSTOMERS>' . "\n\n";
				echo $schema;
			break;
			//-- end action for customers
			
			case 'showdebug':
				// Debuginfos
				echo "<DEBUG>\n";
				echo "<GetAction>$_GET[action]</GetAction>\n";
				echo "<PostAction>$_POST[action]</PostAction>\n";

				echo "<GetDaten>\n";
				
				foreach ($_GET AS $Key => $Value) {
					echo "<$Key>$Value</$Key>\n";
				}	
				echo "</GetDaten>\n";
				echo "<PostDaten>\n";
				
				foreach ($_POST AS $Key => $Value) {
					echo "<$Key>$Value</$Key>\n";
				}
				echo "</PostDaten>\n";
				echo "</DEBUG>\n";
			break; // ShowDebug

			case 'getversion':
			// Versionsausgabe
				echo '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
					"<STATUS>\n" .
					"  <STATUS_INFO>\n" .
					"    <ACTION>$action</ACTION>\n" .
					"    <CODE>111</CODE>\n" .
					"    <SCRIPT_version_year>$version_year</SCRIPT_version_year>\n" .
					"    <SCRIPT_version_month>$version_month</SCRIPT_version_month>\n" .
					"    <SCRIPT_DATE>$version_datum</SCRIPT_DATE>\n" .
					"    <SCRIPT_DEFAULTCHARSET>" . htmlspecialchars(ini_get('default_charset')) . "</SCRIPT_DEFAULTCHARSET>\n" .
					"  </STATUS_INFO>\n" .
					"</STATUS>\n\n";
			break;  // end GetVersion


			case 'checkstatus':
				// Statusausgabe
				// Übergabe z.B. action=check_status&user=admin&passwort=admin			

				$status= '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
						"<STATUS>\n" .
						"  <STATUS_INFO>\n" .
						"    <ACTION>$action</ACTION>\n" .
						"    <CODE>STATUS OK</CODE>\n" .
						"    <SCRIPT_version_year>$version_year</SCRIPT_version_year>\n" .
						"    <SCRIPT_version_month>$version_month</SCRIPT_version_month>\n" .
						"    <SCRIPT_DATE>$version_datum</SCRIPT_DATE>\n" .
						"    <SCRIPT_DEFAULTCHARSET>" . htmlspecialchars(ini_get('default_charset')) . "</SCRIPT_DEFAULTCHARSET>\n" .
						"  </STATUS_INFO>\n" .
						"</STATUS>\n\n";
				echo $status;  
			break; // CheckStatus	 
		}
	} else {
		//  header ("Last-Modified: ". gmdate ("D, d M Y H:i:s"). " GMT");  // immer geändert
		// header ("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		// header ("Pragma: no-cache"); // HTTP/1.0
		// header ("Content-type: text/xml");

		if ($_GET['error'] == '') $_GET['error'] = 'NO PASSWORD OR USERNAME';
		if ($_GET['code'] == '') $_GET['code'] = '100';

		$schema = '<?xml version="1.0" encoding="' . CHARSET . '"?>' . "\n" .
			   '<STATUS><STATUS_DATA><CODE>' . $_GET['code'] . '</CODE><MESSAGE>' . $_GET['error'] . '</MESSAGE></STATUS_DATA></STATUS>';

		echo $schema;
	}//end else action

	if (DEBUGGER>=50) fwrite($dateihandle, " Ende Laufzeit = ".(microtime(true) - $beginn)."\n");
		
?>