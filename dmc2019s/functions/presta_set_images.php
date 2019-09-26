<?php
/************************************************
*                                            	*
*  dmConnector for shops						*
*  presta_set_images.php						*
*  Prest Bildzuordnung						 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/
	ini_set("display_errors", 1);
	error_reporting(E_ERROR);
	//  presta Bildzuordnung	
	function presta_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Bild_Name, $Anzahl_Sprachen, $Bild_Dateiname, $dateihandle)
	{	
			$Bild_Dateiname=str_replace('@@','',$Bild_Dateiname);
			$Bild_Dateiname=str_replace('@ ','',$Bild_Dateiname);
			if (substr($Bild_Dateiname,-1) == '@') {
				$Bild_Dateiname=substr($Bild_Dateiname,0,strlen($Bild_Dateiname)-1);
			}
			
			if (DEBUGGER>=1) fwrite($dateihandle, " presta_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Bild_Name, $Anzahl_Sprachen, $Bild_Dateiname, $dateihandle)  \n");	
			
			define('PS_ADMIN_DIR', _PS_ADMIN_DIR_); // Retro-compatibility
			
			include(PS_ADMIN_DIR.'/../config/config.inc.php');
			include(PS_ADMIN_DIR.'/functions.php');
			if (DEBUGGER>=1) fwrite($dateihandle, " PS_ADMIN_DIR=".PS_ADMIN_DIR." \n");
			if (DEBUGGER>=1) fwrite($dateihandle, " _PS_PROD_IMG_DIR_="._PS_PROD_IMG_DIR_." \n");
					
					
			// Wenn HTTP Bild, zunächst herunterladen in UPLOAD_IMAGES
			if (substr($Bild_Dateiname,0,4)=="http") {
				  if (DEBUGGER>=1) fwrite($dateihandle, "WEB Bilder\n");
				// Trenner fuer mehrere Bilder
				$trenner=",";
				$Bild_Dateiname_URL = explode($trenner,$Bild_Dateiname);
				for ( $j = 0; $j < sizeof($Bild_Dateiname_URL); $j++ ) {
					if ($j>12) break;
				  $extern_url = $Bild_Dateiname_URL[$j]; 
				  $local_file = DIR_ORIGINAL_IMAGES.substr($Bild_Dateiname_URL[$j],strrpos($Bild_Dateiname_URL[$j],"/")+1,strlen($Bild_Dateiname_URL[$j])) ;
				  if (DEBUGGER>=1) fwrite($dateihandle, "Entferntes Bild $extern_url zu lokalem Bild: ".$local_file." \n");
				  $fpread = @fopen($extern_url, 'r');  
				  $exists=false;
				 if (file_put_contents($local_file, file_get_contents($extern_url)) !== false){
					 $exists=true;
					if (DEBUGGER>=1) fwrite($dateihandle, "erledigt \n");
					 
				} else {
					fwrite($dateihandle, "Entferntes Bild $extern_url nicht abrufbar bzw gefunden \n");
				}
				  if ($exists)
					  if ($j==0)
						$Bild_Dateiname = substr($Bild_Dateiname_URL[$j],strrpos($Bild_Dateiname_URL[$j],"/")+1,strlen($Bild_Dateiname_URL[$j]));
					  // wenn nicht letztes und nicht erstes
					//  if ($j>0)
					//	$Bild_Dateiname .= "@".substr($Bild_Dateiname_URL[$j],strrpos($Bild_Dateiname_URL[$j],"/")+1,strlen($Bild_Dateiname_URL[$j]));
				} // end for
				 if (DEBUGGER>=1) fwrite($dateihandle, "NEU Bild_Dateinamen: ".$Bild_Dateiname." \n");
			} else 
				 if (DEBUGGER>=1) fwrite($dateihandle, "lokale Bilder\n");

			//if ($Bild_Name=="") $Bild_Name="-";
			
				
			// NUR, wenn Bild noch nicht zugeordnet ist.
			$image_id_old=dmc_sql_select_query('id_image', 'image', "id_product='".$Artikel_ID."'");
			if (DEBUGGER>=1 && $image_id_old<>"") 
				fwrite($dateihandle, "Bild fuer Artikel $Artikel_ID existiert bereits mit ID ".$image_id_old." \n");	
			
			// Lukas: Funktion zum Bilder zunächst von FTP auf lokal zu kopieren
			$use_ftp_images=false;
			if (substr($Artikel_Artikelnr,0,1)=="k")
				$use_ftp_images=true;
			
			$bilder=explode ('@', $Bild_Dateiname);
			fwrite($dateihandle, "\n Anzahl uebergebener Bilder aus WaWi: ".(sizeof($bilder)).", erstes Bilder=".$bilder[0]." \n");
			if (sizeof($bilder)>1)
				fwrite($dateihandle, "Zweites Bild=".$bilder[1]." \n");
				
			for ( $bild_nummer_durchlauf = 0; $bild_nummer_durchlauf < sizeof ($bilder); $bild_nummer_durchlauf++ ) {
				
				$Bild_Dateiname = $bilder[$bild_nummer_durchlauf];
				$Bild_Name=$Bild_Dateiname;
				fwrite($dateihandle, "\nSuchen nach Bild Nr. $bild_nummer_durchlauf =".$Bild_Dateiname." \n");
				
				if ($use_ftp_images) {
					// define some variables
					$ftp_server = "ftp.komsa.de";
					$ftp_user_name="xxx";
					$ftp_user_pass="xxx";
					
					$local_file = DIR_ORIGINAL_IMAGES.$Bild_Dateiname;
					$server_file = "/bilder_hd/".$Bild_Dateiname;

					// set up basic connection
					$conn_id = ftp_connect($ftp_server);
					
					// login with username and password
					$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
					
					// check connection
					if ((!$conn_id) || (!$login_result)) {
						 fwrite($dateihandle, "PROBLEM in presta_set_images.php - FTP Verbindung zum Bildserver $ftp_server nicht möglich \n");
					} else {
						 // try to download $server_file and save to $local_file
						fwrite($dateihandle, "Kopiere  von $ftp_server - $server_file zu $local_file \n");
						if (ftp_get($conn_id, $local_file, $server_file, FTP_BINARY)) {
							fwrite($dateihandle, " Bilddatei per ftp nach $local_file kopiert \n");	
						} else {
							fwrite($dateihandle, " Bilddatei $server_file per ftp NICHT kopiert \n");
						}

						// close the connection
						ftp_close($conn_id);

					}
				}
				
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
				
				if  ($image_exists) {
					// Use Presta functions like imageResize
					$id_entity=$Artikel_ID;
					
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
					
					if (DEBUGGER>=1) fwrite($dateihandle, "162 TempFile $tmpfile zu neuem Path =".$path." \n");

					// Wenn Verzeichnis nicht vorhanden, dann anlegen.
					if (is_dir($path) == false) 
					{
					   if (mkdir("".$path."", 0777, true) )// Hier wird die Datei angelegt 
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
					$Bildnummer=1;		// 1 ist erstes Bild
					
					$ausgabe = print_r ($imagesTypes,true);
					
					if (DEBUGGER>=1) fwrite($dateihandle, " array imagesTypes = \n".$ausgabe." \n");
					
					$shop_id=STORE_ID; // Standard
				
					//$SHOP_ID=STORE_ID;	// Standard
					//Multishop mit Shop_IDs aus ARTIKEL_STARTSEITE = $Artikel_Presta_Multishop_iD
					if (preg_match('/@/', $Artikel_Presta_Multishop_iD)) {
						$shop_ids = explode ( "@", $Artikel_Presta_Multishop_iD);
					} else {
						$shop_ids[0] = $shop_id;
					}
						
					//dmc_sql_delete(TABLE_IMAGES, "id_image = ".$id_image." AND id_product='".$Artikel_ID."'");
					// Alle Bilder des Produktes aus Datenbank loeschen, WENN ERSTES BILD GESUCHT
					if ($bild_nummer_durchlauf==0) {
						dmc_sql_delete(DB_TABLE_PREFIX.'image_lang', " id_lang=".STD_LANGUAGE_ID." AND id_image in (select id_image from ".DB_TABLE_PREFIX."image where id_product=".$Artikel_ID.") ");
						dmc_sql_delete(DB_TABLE_PREFIX.'image', "id_product='".$Artikel_ID."'");
						for ($anzahl=0;$anzahl< sizeof($shop_ids);$anzahl++) {
							// dmc_sql_delete(DB_TABLE_PREFIX.'image_shop', "id_shop=".$shop_ids[$anzahl]." AND id_image in (select id_image from ".DB_TABLE_PREFIX."image where id_product=".$Artikel_ID.") ");
							dmc_sql_delete(DB_TABLE_PREFIX.'image_shop', "id_shop=".$shop_ids[$anzahl]." AND id_product =".$Artikel_ID." ");
						} // end for storeids
					}
					
								
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
							// Wenn Bild small-default, dann auch als Standardbild kopieren
							if ($imageType['name']=='small_default') {
								$newImage=$path.$id_image.'.jpg';
								if (!imageResize($tmpfile, $newImage, $imageType['width'], $imageType['height'])) {
									fwrite($dateihandle, "223-An error occurred while copying original picture image to your image folder.'");
									$fehler=true;
								} 
							}
							
							
							if (in_array($imageType['id_image_type'], $watermark_types))
								Module::hookExec('watermark', array('id_image' => $id_image, 'id_product' => $id_entity));
						
							// Wenn Bild_Dateiname angegeben,d.h > .jpg
							if (isset($Bild_Dateiname) && strlen($Bild_Dateiname)>4) // && !$fehler)
							{
								if (DEBUGGER>=1) fwrite($dateihandle, " Bild_Dateiname=$Bild_Dateiname zuordnen \n");	
								//$Bildnummer++;		// 1 ist erstes Bild
						
								// Bei exsitierenden Produkt zunaechst Bilder loeschen
								// todo
							
								if ($bild_nummer_durchlauf==0) {
									$cover=1;
								} else
									$cover=NULL;
								
								$Bildnummer++;		// 1 ist erstes Bild
								
								// Bildinformation anlegen in Image Tabellen
								$sql_data= array(
								  'id_image' => $id_image,						
								  'id_product' => $Artikel_ID, 			
								  'position' => $Bildnummer, 			
								  'cover' => $cover								// Hauptbild=1
								  );
								// Zugewiesene Autoincrement ID ermitteln
								//$image_id = dmc_db_get_new_id();
								
								//  Überprüfen, ob Eintragung existent
								$temp_id_query = dmc_db_query("SELECT id_image AS total FROM ".DB_TABLE_PREFIX.'image'.
															" WHERE id_image='".$id_image."' AND id_product='".$Artikel_ID."' AND position='".$Bildnummer."'");
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
												'legend' => $Bild_Name		
												);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_lang', $sql_data);
											
										} // end for
										// wenn standard sprache nicht in for schleife
										if (STD_LANGUAGE_ID>$Anzahl_Sprachen) {
											$sql_data = array(
												'id_image' => $id_image, 			
												'id_lang' => STD_LANGUAGE_ID, 			
												'legend' => $Bild_Name		
											);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_lang', $sql_data);
										}
										// Bild in der image_shop Tabelle ebenfalls hinterlegen
										for ($anzahl=0;$anzahl< sizeof($shop_ids);$anzahl++) {
											$sql_data = array(
													'id_image' => $id_image, 			
													'id_shop' => $shop_ids[$anzahl], 			
													'cover' => $cover,
													'id_product' => $Artikel_ID
											);
											dmc_sql_insert_array(DB_TABLE_PREFIX.'image_shop', $sql_data);
										} // end for storeids
								} else {
										// dmc_sql_update_array("products_dmc", $sql_data, "artnr ='$Artikel_Artikelnr'");
								}
							}
						} // end foreach ($imagesTypes AS $k => $imageType) 
					
				} // end if Bild existiert
			} // END FOR BILDER
	} // end if function presta_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $Bild_Dateiname, $dateihandle);
?>