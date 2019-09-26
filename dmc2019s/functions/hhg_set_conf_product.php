<?
/* DECRIPATED -- new is dmc_set_art_slave_hhg.php */

	$no_of_languages=2;
	
		$dateihandle = fopen(LOG_DATEI,"a");
		fwrite($dateihandle, "***set_configurable_product ".DEBUGGER."\n");
				
		$Merkmale = explode ( '@', $Artikel_Merkmal);
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Merkmale ); $Anz_Merkmale++ )
				{
				   if (DEBUGGER>=1) fwrite($dateihandle, "Merkmal ".$Anz_Merkmale." = ".$Merkmale[$Anz_Merkmale]."<br \>");
				}
		// Auspreageung ermitteln - werden als Auspreageung1@Auspreageung2@... übergeben
		$Auspraegungen = explode ( '@', $Artikel_Auspraegung);
				for ( $Anz_Merkmale = 0; $Anz_Merkmale < count ( $Auspraegungen ); $Anz_Merkmale++ )
			{
					 if (DEBUGGER>=1) fwrite($dateihandle, "Auspraegung ".$Anz_Merkmale." = ".$Auspraegungen[$Anz_Merkmale]."\n");						  
			} 
			
		// Variantenprodukt anlegen
		// Voraussetzung: Hauptartikel existiert
		
			// Preis vom Hauptartikel ermitteln
			$sqlquery = "select products_price from " . TABLE_PRODUCTS . " where products_id='$Artikel_Variante_Von'";

		    $artikel_query = dmc_db_query($sqlquery);
		    if ($artikel = dmc_db_fetch_array($artikel_query))
		    {
				$hauptpreis=$artikel['products_price'];
				// Preisdifferenz
				$preis= $Artikel_Preis - $hauptpreis;
		    }
		    else
		    {
				// Kein Preis
				$preis= 0.00;
			}
		
		  // Für Artikel
           if (DEBUGGER>=1) fwrite($dateihandle, "Variantenprodukt anlegen\n");	
	      $sql_data_array = array(
	        //'products_id' => $Artikel_ID,
	        'products_quantity' => $Artikel_Menge,
	        'products_model' => $Artikel_Artikelnr,
			'products_price' => $preis,
	        'products_weight' => $Artikel_Gewicht,
	        'products_tax_class_id' => $Artikel_Steuersatz,
	        'products_status' => $Artikel_Status,
	        'manufacturers_id' => $Hersteller_ID);
			
			$sql_data_array['products_master_model'] = $Artikel_Variante_Von;
			
				$sql_data_array['products_shippingtime'] = $Artikel_Lieferstatus;
				$sql_data_array['products_startpage'] = $Artikel_Startseite;
				/// $sql_data_array['products_startpage'] = $Artikel_Startseite;
				// weitere (Shopspezifische) Details
				if (PRODUCT_TEMPLATE != "")
					$sql_data_array['PRODUCT_TEMPLATE'] = PRODUCT_TEMPLATE;
				if (GROUP_PERMISSION_0 != "")
					$sql_data_array['group_permission_0'] = GROUP_PERMISSION_0;		
				if (GROUP_PERMISSION_1 != "")
					$sql_data_array['group_permission_1'] = GROUP_PERMISSION_1;		
				if (GROUP_PERMISSION_2 != "")
					$sql_data_array['group_permission_2'] = GROUP_PERMISSION_2;		
				if (GROUP_PERMISSION_3 != "")
					$sql_data_array['group_permission_3'] = GROUP_PERMISSION_3;		
				if (GROUP_PERMISSION_4 != "")
					$sql_data_array['group_permission_4'] = GROUP_PERMISSION_4;
				if (GROUP_PERMISSION_5 != "")
					$sql_data_array['group_permission_5'] = GROUP_PERMISSION_5;	
				if (FSK18=="true")
					$sql_data_array['products_fsk18'] = '1';	
					
			// Szezifisch fuer hhg
			 if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
				if (OPTION_SELECT_TEMPLATE != "")
					$sql_data_array['option_select_template'] = OPTION_SELECT_TEMPLATE;
				else 
					$sql_data_array['option_select_template'] = 'default';
				if (OPTION_PRODUCT_TEMPLATE != "")
					$sql_data_array['option_product_template'] = OPTION_SELECT_TEMPLATE;
				else 
					$sql_data_array['option_product_template'] = 'default';
				if (PRODUCTS_OWNER != "")
					$sql_data_array['products_owner'] = PRODUCTS_OWNER;
				else 
					$sql_data_array['products_owner'] = '1';	
			} // end if hhg
			
			// Update oder Insert, wenn Produkt bereits existent
			if ($exists==0) {
				 $mode='INSERTED';
				 if (DEBUGGER>=1) fwrite($dateihandle, "Insert Variantenartikel = ".$Artikel_Artikelnr."\n");
			      $insert_sql_data = array('products_date_added' => 'now()');
			      $sql_data_array = array_merge($sql_data_array, $insert_sql_data);
			      xtc_db_perform(TABLE_PRODUCTS, $sql_data_array);
			      $Artikel_ID = dmc_db_get_new_id();
			} else {
				 $mode='UPDATED';
				 if (DEBUGGER>=1) fwrite($dateihandle, "Update Variantenartikel = ".$Artikel_Artikelnr." mit ID=".$Artikel_ID."\n");
				  xtc_db_perform(TABLE_PRODUCTS, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
			}
			
			  if (DEBUGGER>=1) fwrite($dateihandle, " PRODUCTS DESCRIPTION\n");	
	     
			// PRODUCTS DESCRIPTION
			    $sql_data_array = array(
	            'products_name' =>  html_entity_decode (sonderzeichen2html(true,$_POST["Artikel_Bezeichnung1"]), ENT_NOQUOTES)
				);			
			
	          // Bestehende Daten laden
	          $cmd = "select products_id from " . TABLE_PRODUCTS_DESCRIPTION .
	            " where products_id='$Artikel_ID' and language_id='2'";

	          $desc_query = dmc_db_query($cmd);
	          if ($desc = dmc_db_fetch_array($desc_query))
	          { //  Beschreibung update
				if (UPDATE_DESC == true) {
					if ($debugger==1) fwrite($dateihandle, "Beschreibung update ".$_POST["Artikel_Bezeichnung1"]."\n");
					if ($no_of_languages>1) {
						for ($i=1; $i<=$no_of_languages; $i++) // mehrere Sprachen
							xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id ='$Artikel_ID' and language_id = '" . $i . "'");
					} else // nur Standardprache
						xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array, 'update', "products_id ='$Artikel_ID' and language_id = '2'");
				}
	          }  
	          else
	          {
				if ($debugger==1) fwrite($dateihandle, " Bescheibung mit ".$_POST["Artikel_Bezeichnung1"]."\n");
				
				if ($no_of_languages>1) {
					for ($i=1; $i<=$no_of_languages; $i++) { 
						// Bescheibung insert - mehrere Sprachen
			            $sql_data_array['products_id'] = $Artikel_ID;
			            $sql_data_array['language_id'] = $i;
			            xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array);
					} // end for
				} else { // nur Standardsprache
						// Bescheibung insert
						 $sql_data_array['products_id'] = $Artikel_ID;
			            $sql_data_array['language_id'] = 2;
			            xtc_db_perform(TABLE_PRODUCTS_DESCRIPTION, $sql_data_array); 
				}
	          } 
	        
			 if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Artikel  dem Shop zuordnen \n");	
	     
			 	if (STORE_ALL)
						 $sql_data_array = array(
				          'products_id' => $Artikel_ID,
				          'store_all' => '1',
				          'store_1' => 0);
				else
						 $sql_data_array = array(
				          'products_id' => $Artikel_ID,
				          'store_all' => 0,
				          'store_1' => 1);
				
				if (!$exists) 
				    xtc_db_perform(TABLE_MS_PRODUCTS_TO_STORE, $sql_data_array);
				else 
					xtc_db_perform(TABLE_MS_PRODUCTS_TO_STORE, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
		  
	 if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Calkulationstabelle fuellen \n");	
	     
			// Standard products_id  store_id  multiplier  multiplier_0  multiplier_1  multiplier_2  multiplier_3 
			    $sql_data_array = array(
				          'products_id' => $Artikel_ID,
				          'multiplier' => '0.00',
						  'multiplier_1' => '0.00',
						  'multiplier_2' => '0.00',
						  'multiplier_3' => '0.00');
				
				if (STORE_ID != "")
						$sql_data_array['store_id'] = STORE_ID;
				else 
					$sql_data_array['store_id'] = 1;
						  
				if (!$exists) 
				    xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array);
				else 
					xtc_db_perform(TABLE_PRODUCTS_CALCULATION, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
		   
		   if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Optionskategorie zuordnen \n");	
	
		  // Produkt der Optionskategorie zuweisen
		 	        $cmd = "select products_id,categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where " .
			          "products_id='$Artikel_ID' and categories_id='".HHG_OPTIONS_CATEGORIE1."'";
				   $sql_query = dmc_db_query($cmd);
					// Nur eintragen, wenn diese Kategorie noch nicht zugeordnet ist
			        if (!($desc = dmc_db_fetch_array($sql_query)))
			        {
				     $insert_sql_data= array(
			            'products_id' => $Artikel_ID,
			            'categories_id' => HHG_OPTIONS_CATEGORIE1,
						'mall' => 0);

			          xtc_db_perform(TABLE_PRODUCTS_TO_CATEGORIES, $insert_sql_data);
			        } // endif Kategorie zuordnen 
			$bilddatei=$Artikel_Bilddatei;
		
			if (DEBUGGER>=1) fwrite($dateihandle, " HHG -> Bild $bilddatei zuordnen \n");	
			
			include('functions/hhg_set_images.php');
?>