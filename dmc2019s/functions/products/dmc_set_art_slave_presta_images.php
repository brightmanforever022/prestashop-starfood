<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for all shop													*
*  dmc_set_art_slave_presta_images.php										*
*  inkludiert von dmc_write_art.php 										*	
*  Artikel Variantenbilder für Shop anlegen -> Presta						*
*  Copyright (C) 2018 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/
/*
27.07.2018
- neu, Erkennung, ob Variantenbild dem Hauptartikel bereits zugeordnet (Wenn nicht zuordnen, da Pflicht
und Ergänzunt der Datenbankverknüpfung auf image_id
*/	
		
	    if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_art_slave_presta_images - produkt varianten bilder zuordnen (bzw auch anlegen) \n");
        
		// Prüfen, ob Bild bereits Hauptartikel zugeordnet.
		$query=	"select pil.id_image AS result from `ps_image_lang` pil INNER JOIN ps_image pi".
					" ON pil.id_image = pi.id_image ".
					" WHERE  pi.id_product=".$main_product_id." ".
					" AND pil.id_lang=1 AND pil.legend='".$Artikel_Bilddatei."' LIMIT 1";
		$sql_query = dmc_db_query($query);
		if ($sql_result = dmc_db_fetch_array($sql_query)) {
			$id_image=$sql_result['result'];
		} else {
			$id_image=0;
		}
		if (DEBUGGER>=1)
				fwrite($dateihandle, "id id_image=$id_image\n"); 
		
		if ($id_image==0) {
			// Noch nicht Hauptartikel zugeordnet - zuordnen
			// fwrite($dateihandle, "Aufruf presta_attach_images_to_product($Artikel_Variante_Von, $main_product_id, $Artikel_Bilddatei, 2, $Artikel_Bilddatei,...) \n"); 
			$Bild_Dateiname=$Artikel_Bilddatei;
		/*	if (is_file('functions/presta_set_images.php')) include_once('functions/presta_set_images.php');	// muesste eigentlich korrekt sein
			else if (is_file('../functions/presta_set_images.php')) include_once('../functions/presta_set_images.php');
			else fwrite($dateihandle, "Fehler in dmc_set_images: Finde functions/presta_set_images.php nicht \n");
			presta_attach_images_to_product($Artikel_Variante_Von, $main_product_id, $Artikel_Bilddatei, 2, $Artikel_Bilddatei, $dateihandle);
	*/
			if (!file_exists(DIR_ORIGINAL_IMAGES.$Bild_Dateiname) && $image_id_old=="")
				{	
					fwrite($dateihandle, "Lokale Bilddatei ".DIR_ORIGINAL_IMAGES.$Bild_Dateiname." existiert nicht\n");
					$Bild_Dateiname = str_replace(".jpg",".png", $Bild_Dateiname);
					if (!file_exists(DIR_ORIGINAL_IMAGES.$Bild_Dateiname) && $image_id_old=="")
					{	
						$Bild_Dateiname = str_replace(".png",".gif", $Bild_Dateiname);
						if (!file_exists(DIR_ORIGINAL_IMAGES.$Bild_Dateiname) && $image_id_old=="")
						{	
							if (DEBUGGER>=1 && $image_id_old=="") fwrite($dateihandle, " Bilddatei ".DIR_ORIGINAL_IMAGES.$Bild_Dateiname." (auch nicht mit .png .jpg) exisitiert nicht \n");	
							if (DEBUGGER>=1 && $image_id_old!="") fwrite($dateihandle, "  Bild ist schon zugeordnet \n");	
							$image_exists=false;
						}
					} 
			} else {
					$image_exists=true;
			}
			if ($image_exists) {
					define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility
			
					include_once(PS_ADMIN_DIR.'/../config/config.inc.php');
					include_once(PS_ADMIN_DIR.'/functions.php');
					if (DEBUGGER>=1) fwrite($dateihandle, " PS_ADMIN_DIR=".PS_ADMIN_DIR." \n");
					if (DEBUGGER>=1) fwrite($dateihandle, " _PS_PROD_IMG_DIR_="._PS_PROD_IMG_DIR_." \n");
					
					// Use Presta functions like imageResize
					$id_entity=$main_product_id;
					
					// ID des Bildes ist die naechst hoechste
					$table=TABLE_IMAGES;
					$id_image=dmc_get_highest_id('id_image',$table)+1;
					//fwrite($dateihandle, "34\n");
					//$tmpfile=DIR_ORIGINAL_IMAGES.intval($id_entity).'-'.intval($id_image).'.jpg';
					$tmpfile=DIR_ORIGINAL_IMAGES.$Bild_Dateiname;
					//fwrite($dateihandle, "37\n");
					// PRESTA ALTE VERSION: BilderPfad zB fuer ArtID 300 und Bild ID 23 /img/p/300-23...jpg 
					// $path = _PS_PROD_IMG_DIR_.intval($id_entity).'-'.intval($id_image);
					// PRESTA NEUE VERSION: BilderPfad: zB fuer Bild ID 23: img/p/2/3/23....pjg
					if (intval($id_image)<10)
						$path = _PS_PROD_IMG_DIR_.$id_image.'/';
					else if (intval($id_image)<100)
						$path = _PS_PROD_IMG_DIR_.substr($id_image,0,1).'/'.substr($id_image,1,1).'/';
					else if (intval($id_image)<1000)
						$path = _PS_PROD_IMG_DIR_.substr($id_image,0,1).'/'.substr($id_image,1,1).'/'.substr($id_image,2,1).'/';
					else if (intval($id_image)<10000)
						$path = _PS_PROD_IMG_DIR_.substr($id_image,0,1).'/'.substr($id_image,1,1).'/'.substr($id_image,2,1).'/'.substr($id_image,3,1).'/';
					else if (intval($id_image)<100000)
						$path = _PS_PROD_IMG_DIR_.substr($id_image,0,1).'/'.substr($id_image,1,1).'/'.substr($id_image,2,1).'/'.substr($id_image,3,1).'/'.substr($id_image,4,1).'/';
					else 
						$path = _PS_PROD_IMG_DIR_.substr($id_image,0,1).'/'.substr($id_image,1,1).'/'.substr($id_image,2,1).'/'.substr($id_image,3,1).'/'.substr($id_image,4,1).'/'.substr($id_image,5,1).'/';
					
					if (DEBUGGER>=1) fwrite($dateihandle, "86 TempFile $tmpfile zu neuem Path =".$path." \n");

					// Wenn Verzeichnis nicht vorhanden, dann anlegen.
					if (is_dir($path) == false) 
					{
					   if (mkdir("".$path."", 0777) )// Hier wird die Datei angelegt 
						{
							// angelegt
						}
						else
						{
							fwrite($dateihandle, "Das Verzeichnis <b>$path</b> konnte nicht angelegt werden!");
						}
					} 
					
					//imageResize($tmpfile, $path.'.jpg');
					$imagesTypes = ImageType::getImagesTypes($entity);
					$shop_id=STORE_ID; // Standard
				
					//$SHOP_ID=STORE_ID;	// Standard
					//Multishop mit Shop_IDs aus ARTIKEL_STARTSEITE = $Artikel_Presta_Multishop_iD
					if (preg_match('/@/', $Artikel_Presta_Multishop_iD)) {
						$shop_ids = explode ( "@", $Artikel_Presta_Multishop_iD);
					} else {
						$shop_ids[0] = $shop_id;
					}
						$cover=0;
						foreach ($imagesTypes AS $k => $imageType) {
							$fehler=false;
							// PRESTA ALTE VERSION: $newImage=$path.'-'.stripslashes($imageType['name']).'.jpg';
							// PRESTA NEUE VERSION:
							$newImage=$path.$id_image."-".stripslashes($imageType['name']).'.jpg';
							if (DEBUGGER>=1) fwrite($dateihandle, " imageResize to=".$newImage." \n");
							if (DEBUGGER>=1) fwrite($dateihandle, " width=".$imageType['width']." \n");
							if (!imageResize($tmpfile, $newImage, $imageType['width'], $imageType['height'])) {
								fwrite($dateihandle, "210-An error occurred while copying original picture image to your image folder.'");
								$fehler=true;
							} 
						
							if (in_array($imageType['id_image_type'], $watermark_types))
								Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
						} // end foreach ($imagesTypes AS $k => $imageType) 
						
							// Wenn Bild_Dateiname angegeben,d.h > .jpg
							if (isset($Bild_Dateiname) && strlen($Bild_Dateiname)>4) // && !$fehler)
							{
								if (DEBUGGER>=1) fwrite($dateihandle, " Bild_Dateiname=$Bild_Dateiname zuordnen \n");	
								$Bildnummer=$id_image;		// einfach hohe nummer
								
								// Bildinformation anlegen in Image Tabellen
								$sql_data= array(
								  'id_image' => $id_image,						
								  'id_product' => $main_product_id, 			
								  'position' => $Bildnummer, 			
								 // 'cover' => $cover								// Hauptbild=1
								  );
								// Zugewiesene Autoincrement ID ermitteln
								//$image_id = dmc_db_get_new_id();
								
								//  Überprüfen, ob Eintragung existent
								$temp_id_query = dmc_db_query("SELECT id_image AS total FROM ".DB_TABLE_PREFIX.'image'.
															" WHERE id_image='".$id_image."' AND id_product='".$main_product_id."' AND position='".$Bildnummer."'");
								$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
								// Wenn noch kein Eintrag
								if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null) {
										// $sql_data['id'] = $Artikel_ID;					
										dmc_sql_insert_array(DB_TABLE_PREFIX.'image', $sql_data);
										// Bildbezeichnungen in den Sprachen
										for ( $Sprach_ID = 1; $Sprach_ID <= $Anzahl_Sprachen; $Sprach_ID++ ) {
											$sql_data = array(
												'id_image' => $id_image, 			
												'id_lang' => $Sprach_ID, 			
												'legend' => $Bild_Dateiname		
												);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_lang', $sql_data);
											
										} // end for
										// wenn standard sprache nicht in for schleife
										if (STD_LANGUAGE_ID>$Anzahl_Sprachen) {
											$sql_data = array(
												'id_image' => $id_image, 			
												'id_lang' => STD_LANGUAGE_ID, 			
												'legend' => $Bild_Dateiname		
											);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_lang', $sql_data);
										}
										// Bild in der image_shop Tabelle ebenfalls hinterlegen
										for ($anzahl=0;$anzahl< sizeof($shop_ids);$anzahl++) {
											$sql_data = array(
													'id_image' => $id_image, 			
													'id_shop' => $shop_ids[$anzahl], 			
													// 'cover' => $cover,
													'id_product' => $main_product_id
											);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_shop', $sql_data);
										} // end for storeids
								} else {
										// dmc_sql_update_array("products_dmc", $sql_data, "artnr ='$Artikel_Artikelnr'");
								}
							}
						
					
			} // end if Bild existiert
				
		/*	$sql_query = dmc_db_query($query);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
				$id_image=$sql_result['result'];
			} else {
				$id_image=0;
			}
			*/
			if (DEBUGGER>=1)
					fwrite($dateihandle, "NEU id id_image neu dem Hauptartikel zugeordnet=$id_image\n"); 
		}
		
		if ($id_image<>0) {
			//  (Hauptartikel) Bild der Varianten zuordnen, wenn noch nicht zugeordnet
			$query = "SELECT id_product_attribute AS result FROM ps_product_attribute_image WHERE id_product_attribute=".$id_product_attribute;
			$sql_query = dmc_db_query($query);
			if ($sql_result = dmc_db_fetch_array($sql_query)) {
				$id_product_attribute=$sql_result['result'];
				$query = "UPDATE ps_product_attribute_image SET id_image=".$id_image." WHERE id_product_attribute=".$id_product_attribute;
			} else {
				$query = "INSERT INTO ps_product_attribute_image (id_product_attribute, id_image) VALUES ($id_product_attribute,$id_image)";
			}
			 dmc_db_query($query);
				fwrite($dateihandle, "Bild mit ID $id_image der Variante $id_product_attribute zugeordnet\n"); 
		} else {
			if (DEBUGGER>=1)
				fwrite($dateihandle, "Problem, keine Bilder ID -> id_image=$id_image\n"); 
		}
	
		
		
?>
	