<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_array_create_additional.php											*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Array mit shopspizifischen Werten ergaenzen						*
*  Copyright (C) 2012 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
12.03.2012
- neu
18.02.2014 - Erweitert um die Befuellung von Tabellenzusatzfeldern uber Merkmale products_
*/
			fwrite($dateihandle, "dmc_array_create_additional Einheit ID= $Artikel_VPE MIT LIEFERSTATUS $Artikel_Lieferstatus\n");			
						
			if (strpos(strtolower(SHOPSYSTEM), 'presta') === false && strpos(strtolower(SHOPSYSTEM), 'zencart') === false
				&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false
				&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
							// product essentials
				// VPEinheiten	gelten fuer alle 	 - bis auf standard inst presta
				//$sql_data_array['products_vpe'] = $Artikel_VPE;
				$sql_data_array['products_vpe'] = $Artikel_VPE_ID;
				$sql_data_array['products_vpe_status']=$Artikel_VPE_Status;
				$sql_data_array['products_vpe_value']=$Artikel_VPE_Value;
			}
			
			// Spezifisch fuer hhg
			if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) {
				if (HHG_OPTION_SELECT_TEMPLATE != "")
					$sql_data_array['option_select_template'] = HHG_OPTION_SELECT_TEMPLATE;
				else 
					$sql_data_array['option_select_template'] = 'default';
				if (HHG_OPTION_PRODUCT_TEMPLATE != "")
					$sql_data_array['option_product_template'] = HHG_OPTION_PRODUCT_TEMPLATE;
				else 
					$sql_data_array['option_product_template'] = 'default';
				if (PRODUCTS_OWNER != "")
					$sql_data_array['products_owner'] = PRODUCTS_OWNER;
				else 
					$sql_data_array['products_owner'] = '1';
				if (HHG_OPTION_SELECT_TEMPLATE != "")
					$sql_data_array['option_select_template'] = HHG_OPTION_SELECT_TEMPLATE;
				if (HHG_OPTION_PRODUCT_TEMPLATE != "")
					$sql_data_array['option_product_template'] = HHG_OPTION_PRODUCT_TEMPLATE;					
			} // end if hhg

			// Nur ab Gambio GX 
			if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) {
				if (GM_OPTIONS_TEMPLATE != "")
					$sql_data_array['GM_OPTIONS_TEMPLATE'] = GM_OPTIONS_TEMPLATE;					
				if (GM_SITEMAP_ENTRY != "" && GM_SITEMAP_ENTRY != "false")
					$sql_data_array['GM_SITEMAP_ENTRY'] = GM_SITEMAP_ENTRY;
				if (GM_SHOW_weight != "" && GM_SHOW_weight != "false")
					$sql_data_array['GM_SHOW_weight'] = GM_SHOW_weight;	
				if (GM_SHOW_QTY_INFO != "" && GM_SHOW_QTY_INFO != "false")
					$sql_data_array['GM_SHOW_QTY_INFO'] = GM_SHOW_QTY_INFO;	
				if ( $Artikel_Preis<0)
						// nicht käuflich
						$gm_price_status=2;
					 else if ($Artikel_Preis==0)
						// preis auf Anfrage
						$gm_price_status=1;
					 else
						// Normalpreis
						$gm_price_status=0;
				$sql_data_array['gm_price_status'] = $gm_price_status;				
			} // end if gambiogx
			
			if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'hhg') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'veyton') === false 
			&&	strpos(strtolower(SHOPSYSTEM), 'presta') === false
			&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
				if (OPTIONS_TEMPLATE != "")
					$sql_data_array['options_template'] = OPTIONS_TEMPLATE;
			} // end if nicht zen und hhg
			
			if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false && 
			strpos(strtolower(SHOPSYSTEM), 'presta') === false && 
			strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false
			&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
				$sql_data_array['products_ean'] = $Artikel_EAN;
				$sql_data_array['products_shippingtime'] = $Artikel_Lieferstatus;
				if (PRODUCT_TEMPLATE != "")
					$sql_data_array['product_template'] = PRODUCT_TEMPLATE;
				if (FSK18=="true")
					$sql_data_array['products_fsk18'] = '1';	
			}
			
			fwrite($dateihandle, "GROUP_PERMISSION_ \n");
			if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'presta') === false
			&& strpos(strtolower(SHOPSYSTEM), 'veyton') === false
			&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
				// $sql_data_array['products_startpage'] = $Artikel_Startseite;
				// weitere (Shopspezifische) Details
				for($gruppe = 0; $gruppe <= 15; $gruppe++) {     //  durchlaufen	     
				//	fwrite($dateihandle, "'GROUP_PERMISSION_' . $gruppe =\n");
				//	fwrite($dateihandle, constant('GROUP_PERMISSION_' . $gruppe)."\n");
					//if (defined(constant('GROUP_PERMISSION_' . $gruppe)))
						if (constant('GROUP_PERMISSION_' . $gruppe)!=''){  	
							 $sql_data_array['group_permission_' . $gruppe] = constant('GROUP_PERMISSION_' . $gruppe);	
							// fwrite($dateihandle, "GROUP_PERMISSION_ ".$gruppe."=".constant('GROUP_PERMISSION_' . $gruppe)."\n");
						} else 	{
							//fwrite($dateihandle, 'GROUP_PERMISSION_' . $gruppe." ohne Wert\n");
						} // end if
				} // END FOR
			} // end if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false)
			
			// Image wird bei HHG nicht in products zugeordnet
			if (!$SkipImages && SHOPSYSTEM!='hhg' && strpos(strtolower(SHOPSYSTEM), 'presta') === false  && 
			strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false
			)
			{
				fwrite($dateihandle, "Bild suchen in ".DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Artikel_Bilddatei." und ".DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei."\n");	
					// Überprüfen, ob dem Artikel zugehörige Bilddatei  zugewiesen werden soll und ob diese im thumbnail oder original images Ordner verfügbar  // ToDo strtolower($file) 
				if ($Artikel_Bilddatei!="" && (is_file(DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES.$Artikel_Bilddatei) || is_file(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.$Artikel_Bilddatei) || is_file(DIR_FS_CATALOG.PRODUCTS_EXTRA_PIC_PATH.$Artikel_Bilddatei)))			
					$sql_data_array['products_image'] = $Artikel_Bilddatei;
				else 			
					$sql_data_array['products_image'] = 'noimage.gif';			
			}
			
			// product description HHG
			if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false ) {
				if (PRODUCTS_DETAILS != "")
					$sql_product_details_array['products_details'] = PRODUCTS_DETAILS;
				if (PRODUCTS_SPECS != "")
					$sql_product_details_array['products_specs'] = PRODUCTS_SPECS;
	      	}
			
		// Datenbanktabellen Zusatzfelder	
		// GGfl Attribute ermitteln // Getrennt durch @
		if (strpos(strtolower(SHOPSYSTEM), 'zencart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'virtuemart') === false 
			&& strpos(strtolower(SHOPSYSTEM), 'presta') === false
			&& strpos(strtolower(SHOPSYSTEM), 'osc') === false) {
			
			$Artikel_Merkmale = explode("@",$Artikel_Merkmal);
			$Artikel_Auspraegungen = explode("@",$Artikel_Auspraegung);
			for ( $i = 0; $i < count ( $Artikel_Merkmale ); $i++ )
			{			
				// Einzelne Attribute verknüpfen, wenn Werte gesetzt
				if ($Artikel_Merkmale[$i] != "" && $Artikel_Auspraegungen[$i] != "")
				{
					// Merkmale ermitteln // Getrennt durch @
					// Solche, die mit products_ anfangen entsprechen den Tabellenspalten des 
					// Shop. z.b. products_alter fuer das Alter, welches in der Tabellenspalte 'alter' von "products" hinterlegt wird
					$pos = strpos($Artikel_Merkmale[$i], 'products_');
					if ($pos !== false) {
						$tabellen_spalte=substr($Artikel_Merkmale[$i],9,256);
						$sql_data_array[$tabellen_spalte] = $Artikel_Auspraegungen[$i];
						fwrite($dateihandle, "Tabelle products Spalte $tabellen_spalte mit Wert ".$Artikel_Auspraegungen[$i]." fuellen\n");					
					}
					// Pruefen auf modified Filter Attribute
					$pos = strpos($Artikel_Merkmale[$i], 'filter_');
					if ($pos !== false) {
						$filter_bezeichnung=substr($Artikel_Merkmale[$i],7,256);
						$filter=true;
						$sql_filter_array[$filter_bezeichnung] = $Artikel_Auspraegungen[$i];
						fwrite($dateihandle, "Tabelle products FILTER $filter_bezeichnung mit Wert ".$Artikel_Auspraegungen[$i]." fuellen\n");					
					}
					
					
				} // end if
			} // end for
		} // end if shops 
	
		if (SHOPSYSTEM_VERSION>=4.2) $sql_product_details_array['products_store_id'] = SHOP_ID;		// Ab veyton 4.2 
			
?>
	