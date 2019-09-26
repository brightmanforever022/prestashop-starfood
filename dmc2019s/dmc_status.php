<?php

	/**
	 *
	 * @write Verarbeite spezielle Status von der Schnittstelle
	 * @param string $s
	 * @return string $s
	 *
	 * Version vom 24.07.2013
	 *
	 * 22.05.2011 - Verarbeitung vom Status write_artikel_begin (Beginn Artikelabgleich)
	 */
	
	defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );
	ini_set("display_errors", 1);
	error_reporting(E_ERROR);
	error_reporting(E_ALL);

	function dmc_status() {
		global $action,$dateihandle; 		
		$status=$_POST['Status'];
		if (DEBUGGER>=1) fwrite($dateihandle, "\n******************dmc_status mit Status $status ******************\n");
		
		if (strpos($status,'categorie_end')!==false) {
				// VEYTON -> dmc_set_top_categories();		
		}
		// Verarbeitung vom Status write_artikel_begin (Beginn Artikelabgleich
		// if ($status=='write_artikel_begin' && (strpos(strtolower(SHOPSYSTEM), 'woocommerce') === false)) {
		if ($status=='write_artikel_begin' && (strpos(strtolower(SHOPSYSTEM), 'woocommerce') === false)) {
			// Basierend auf den Definitionsdateien koennen zu Beginn des Artikelabgleichs bestimmte Aktionenn durchgeführt werden
			// Alle Artikel loeschen, (nur) alle Varianten loeschen, alle Artikel deaktivieren
			
			//	if (STATUS_WRITE_ART_BEGIN_DETELE_ART)
			//		dmc_delete_first(); // löschen aller Artikel / dbc_db_functions.php
				
			//	if (STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART)
			//		dmc_deactivte_first(); // deaktivieren aller Artikel  / dbc_db_functions.php
					
			//	if (STATUS_WRITE_ART_BEGIN_DETELE_ART_VARIANTS)
			//		dmc_delete_variants_first(); // löschen aller Varianten Artikel / dbc_db_functions.php
				
			//	if (STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART_VARIANTS)
			//		dmc_deactivate_variants_first(); // unktion um alle Varianten Produkte zu deaktivieren / dbc_db_functions.php
				// Alle Staffelpreise löschen
			//	dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_1 WHERE quantity >1");
			//	dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_2 WHERE quantity >1");
			//	dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "personal_offers_by_customers_status_3 WHERE quantity >1");
					// Alle wooCommerce produkte deaktivieren
			//$query= "update " . DB_PREFIX . "posts SET post_status = 'draft' WHERE post_type='product'";
			//if (DEBUGGER>=1) fwrite($dateihandle, "$query\n");
			//dmc_sql_query($query);
			// Alle wooCommerce Produkte löschen
			/*	include_once "../wp-load.php";
			$cmd =  "SELECT id FROM " . DB_PREFIX . "posts WHERE post_type='product'";
			$query = dmc_db_query($cmd);
			while ($result = dmc_db_fetch_array($query))
			{
				wp_delete_post( $result['id'], true );
			}
			*/
			// Vorab alle wooCommerce Artikel deaktivieren - UPDATE wp_posts SET post_status = 'draft' WHERE post_type='product'
					// Vorab alle wooCommerce Artikel deaktivieren, wenn abgleich zwischen 20 und 24 Uhr
			// $uhrzeit = time() + 60*60;
			// Uhrzeit zur vollen Stunde: ".date("H", $uhrzeit)
			//if (date("H", $uhrzeit)>20 && date("H", $uhrzeit)<24) {		// hier zwischen 20 und 24 Uhr.
			//	dmc_sql_query("UPDATE " . DB_TABLE_PREFIX . "posts SET post_status = 'draft' WHERE post_type='product'");
			//}
			// Nicht geänderte Artikel deaktivieren
	//-		//update `wp_posts` set post_status='draft' WHERE `post_type` ='product' and  `post_modified` < now()-  INTERVAL 1 DAY
		}	//write_artikel_begin
		 
		
		if (strpos($status,'aktionen_begin')!==false) { 
			// Alle Aktionspreise der Schnittstelle löschen
			// dmc_sql_query("DELETE FROM " . DB_TABLE_PREFIX . "specials WHERE date_status_change='1973-02-28 05:00:00'");
		}
		
		if (strpos($status,'categorie_begin')!==false) { 
		//	dmc_delete_categories_first(); // löschen aller Kategorien / dbc_db_functions.php
		//	dmc_deactivte_first(); // deaktivieren aller Artikel  / dbc_db_functions.php
		//	dmc_delete_variants_first(); // löschen aller Varianten Artikel / dbc_db_functions.php
		}
		
		if (strpos($status,'write_customer_end')!==false) { 
			//	Shopware - alle Newsletterkunden auch in Newsletter Datenbanktabelle schreiben
			/*$query= "INSERT IGNORE INTO s_campaigns_mailaddresses (SELECT id AS id, '1' AS customer, '1' AS groupID, email AS email, 0 AS lastmailing, 0 AS lastread, firstlogin AS added FROM s_user WHERE newsletter=1)";
			//if (DEBUGGER>=1) fwrite($dateihandle, "$query\n");
			dmc_sql_query($query);*/
			
		}
		
		// Verarbeitung vom Status write_artikel_end (Ende Artikelabgleich)
		if ($status=='write_artikel_end' && (strpos(strtolower(SHOPSYSTEM), 'woocommerce') === false)) {
			// veyton dmc_deactivate_master_without_slave();
			//  Kategorien der höchsten Ordnung, welche keine Artikel-Zuordnung mehr besitzen, werden im Anschluß an den Artikelexport gelöscht.|492|79|22|3|
			// Shopware Ebene resultiert aus dem Feld path in path , zb |492|79|22|3| ist Ebene 4
			//dmc_del_cat_without_products($ebene=4);
		//	if (strpos(strtolower(SHOPSYSTEM), 'shopware') !== false)
			//	$output = shell_exec('php bin/console sw:media:cleanup --delete'); //  - verschiebt ungenutzte Bilder in den Papierkorb und löscht diese direkt (ab 5.1)
			
		}
		
	}// end function dmc_status	
	
?>