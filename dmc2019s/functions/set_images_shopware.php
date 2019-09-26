<?php
/************************************************
*                                            	*
*  dmConnector for shops						*
*  set_images_shopware.php						*
*  Shopware Bildzuordnung						 *
*  Copyright (C) 2015 DoubleM-GmbH.de			*
*                                               *
*************************************************/

				// Bilder - mehrere durch @ getrennt
				// Unterscheidung auf Lokal und URLs
				$bildexists=false;
				$Artikel_Bilddatei=str_replace('@@','',$Artikel_Bilddatei);
				if (substr($Artikel_Bilddatei,-1) == '@') {
					$Artikel_Bilddatei=substr($Artikel_Bilddatei,0,strlen($Artikel_Bilddatei)-1);
				}

				fwrite($dateihandle, "\n(set_images_shopware) Artikel_Bilddatei $Artikel_Artikelnr = ".$Artikel_Bilddatei.". Anzahl Bilder im sql_data_array: ".count($sql_data_array['images'])."\n");
				// Wenn keine Bilddatei übergeben, prüfe auf Artikelnummer.jpg
				if ($Artikel_Bilddatei=='') {
					$Artikel_Bilddatei = strtolower($Artikel_Artikelnr).'.jpg';
				}
				if ($Artikel_Bilddatei != "") {
					/*	if (strpos($bilder[0], "http")===false) 
						fwrite($dateihandle, "\n http kommt in ".$bilder[0]." NICHT vor \n");
					else
						fwrite($dateihandle, "\n http kommt in ".$bilder[0]." vor \n");
					*/
					$bilder=explode ('@', $Artikel_Bilddatei);
					fwrite($dateihandle, "\n Anzahl uebergebener Bilder aus WaWi: ".(sizeof($bilder)).", erstes Bilder=".$bilder[0]." \n");
					if (sizeof($bilder)>1)
						fwrite($dateihandle, "Zweites Bild=".$bilder[1]." \n");
					
					if (strpos($bilder[0], "http")!==false) {
						// URLs
						fwrite($dateihandle, "set_images_shopware: ".count ( $bilder )." URL Bilder anlegen \n");
						for ( $i = 0; $i < sizeof ( $bilder ); $i++ ) {
							if (strpos($bilder[$i], "http")!==false) {
								if ($i==1)
											$sql_data_array['__options_images']=true;
										
								//if (strpos($bilder[$i], "." )!==false)
										$sql_data_array['images'][$i]['link']=$bilder[$i];
								fwrite($dateihandle, "URL Bild[$i] = ".$sql_data_array['images'][$i]['link']." \n");
							} 
						} 
					} else {
						// Vorpruefung, ob EIN neues Bild vorhanden (muss nicht das hauptbild sein)
						$neuebilder=0;
						for ( $i = 0; $i < sizeof ($bilder); $i++ ) {
							if (strpos($bilder[$i], "\\") !== false) {
								$bilder[$i]=substr($bilder[$i],(strrpos($bilder[$i],"\\")+1),254); 
							} else if (strpos($bilder[$i], "/") !== false && strpos($bilder[$i], "http") === false ) {
								$bilder[$i]=substr($bilder[$i],(strrpos($bilder[$i],"/")+1),254); 
							} 
							// Zuordnen, wenn Bild vorhanden, zunaechst neue Bilder in /zugeordnet kopieren und dann mit den bereits zugeordneten erneut zuordnen.
							$bilddateiupload = './upload_images/'.$bilder[$i];
						//	$bilddateiupload =str_replace('dmc2015b/../dmc2015b/','dmc2015b/',$bilddateiupload );
							$bilddatei = './upload_images/zugeordnet/'.$bilder[$i];
						//	$bilddatei =str_replace('dmc2015b/../dmc2015b/','dmc2015b/',$bilddatei );
							for ( $j = 0; $j <=10; $j++ ) {
								if ($j==1) { 
									$bilddateiupload = str_replace(".jpg","_1.jpg",$bilddateiupload);
									$bilddatei = str_replace(".jpg","_1.jpg",$bilddatei);
									$bilddateiupload = str_replace("_0_1.jpg","_1.jpg",$bilddateiupload);
									$bilddatei = str_replace("_0_1.jpg","_1.jpg",$bilddatei);
									$bilddateiupload = str_replace("_1_1.jpg","_2.jpg",$bilddateiupload);
									$bilddatei = str_replace("_1_1.jpg","_2.jpg",$bilddatei);
								}
								if ($j>0) {
									$bilddateiupload = str_replace("_".($j-1).".jpg","_".$j.".jpg",$bilddateiupload);
									$bilddatei = str_replace("_".($j-1).".jpg","_".$j.".jpg",$bilddatei);
								}
						 
								fwrite($dateihandle, "\n74 Bild suchen und dann kopieren copy($bilddateiupload, $bilddatei) ... ");
									
								if (file_exists($bilddateiupload) || file_exists($bilddatei)) {
									fwrite($dateihandle, "\n77 Bild in /zugeordnet kopieren copy($bilddateiupload, $bilddatei) ... ");
									if (!copy($bilddateiupload, $bilddatei)) {
										fwrite($dateihandle, "failed to copy $bilddateiupload \n");
									} else {
										// $neuebilder++;
										fwrite($dateihandle, "ok -> unlink($bilddateiupload) \n");
										unlink($bilddateiupload);
										// Pruefen anhand der Bildgroesse, ob Bild dem Artikel bereits zugeordnet war
										// "SELECT * FROM `s_articles_img` where acticleID = "$art_id
										// SELECT m.file_size FROM `s_articles_img` a INNER JOIN `s_media` m ON a.media_id=m.id WHERE a.articleID = 35307 ORDER BY m.`id` LIMIT 1
										$groesse_bilddatei_alt = dmc_sql_select_query('m.file_size', "`s_articles_img` a INNER JOIN `s_media` m ON a.media_id=m.id",  "a.articleID = '".$art_id."'"); 
										fwrite($dateihandle, "Bildgroesse alt= ".$groesse_bilddatei_alt." - neu =".filesize($bilddatei)." \n");
										if ($groesse_bilddatei_alt != '') {
											if (filesize($bilddatei)==$groesse_bilddatei_alt) {
												if ($i<3) fwrite($dateihandle, "Bild $i war bereits zugeordnet \n");
												$bildexists=false;
											} else {
												$neuebilder++;
											}
										} else {
											$neuebilder++;
										}
									}
								} else {
											fwrite($dateihandle, "\n82 Bild $bilddateiupload nicht vorhanden ... ");
							
								} 
							}
						}
						// lokale vorhandene Bilder
						fwrite($dateihandle, "\n set_images_shopware lokale Bilder hinzufuegen, $neuebilder neue Bilder im upload_images Verzeichnis \n");
						// Verzeichnis fuer zugeordnete Bilder
						$shop_media_url=SHOP_URL.DIR_ORIGINAL_IMAGES.'zugeordnet/';
						// Nur, wenn zumindest 1 neues Bild vorhanden ist, alle zuordnen (die in zugeordnet/ sind)
						if ($neuebilder>0)
							for ( $i = 0; $i < count ( $bilder ); $i++ ) {
								//fwrite($dateihandle, "Bild ".$shop_media_url.$bilder[$i]." von Bildern ".count ( $bilder )."\n");
								if (file_exists('./upload_images/zugeordnet/'.$bilder[$i])) {
									$bildexists=true;
									// Zuordnen wenn exisitiert und noch nicht zugeordnet
									if ($bildexists)  {
										// Anweisung AB SW5, Bilder zu replacen setzen
										if ($i==1)
											$sql_data_array['__options_images']=true;
										if (DEBUGGER>=1 && $i<=2) 
											fwrite($dateihandle, "set_images_shopware -> Bild $bilddatei (".$shop_media_url.$bilder[$i].") sql_data_array['images'][$i]['link'] hinzufuegen\n");
										$sql_data_array['images'][$i]['link']=$shop_media_url.$bilder[$i];
										// Auf Zusatzbilder 1 bis 10 pruefen
										if ($i==0) {
											for ( $j = 1; $j <=10; $j++ ) {
												$zusatzbilddatei = str_replace(".","_".$j.".",$bilder[$i]);
												$zusatzbilddatei = str_replace("_0_","_",$zusatzbilddatei);
												if ($j==1) { 
													$zusatzbilddatei = str_replace("_0_1.jpg","_1.jpg",$zusatzbilddatei);
												}
												if ($i<3) fwrite($dateihandle, "set_images_shopware -> zusatzbilddatei = ".DIR_ORIGINAL_IMAGES.$zusatzbilddatei." ... ");
												if (file_exists('./upload_images/zugeordnet/'.$zusatzbilddatei)) {
													$sql_data_array['images'][count($bilder)+$j]['link']=$shop_media_url.$zusatzbilddatei;
													if (DEBUGGER>=1 && $i<=2) fwrite($dateihandle, "Zusatzbild existiert ".$sql_data_array['images'][count($bilder)+$j]['link']." \n");
													//	unlink($zusatzbilddatei);
												} else  
													fwrite($dateihandle, "Zusatzbild existiert nicht: $zusatzbilddatei \n");
											}
										} // end if Zusatzbilder fuer Bild1 _1 _2 ...
										fwrite($dateihandle, "Bild Link $i = ".$sql_data_array['images'][$i]['link']."\n");
									}
								} else {
									if (DEBUGGER>=1 && $i==0) fwrite($dateihandle, "set_images_shopware Bild $bilddatei (".$shop_media_url.$bilder[$i].") nicht vorhanden \n");
								} // end if else						
							} // end for anzahl der Bilder
						// Zusaetzlich pruefen auf Artikelbilddatei = Artikelnummer.png 
						// Zuordnen, wenn Bild vorhanden
						/*$bild=strtolower($Artikel_Artikelnr).'.png';
						$bilddatei = SHOP_ROOT.DIR_ORIGINAL_IMAGES.$bild;
						if ($Artikel_Bilddatei != $bild && file_exists($bilddatei)) {
							$groesse_bilddatei_alt = dmc_sql_select_query('m.file_size', "`s_articles_img` a INNER JOIN `s_media` m ON a.media_id=m.id",  "a.articleID = '".$art_id."'"); 
							fwrite($dateihandle, "Bildgroesse alt= ".$groesse_bilddatei_alt." - neu =".filesize($bilddatei)." \n");
								if ($groesse_bilddatei_alt != '') {
									if (filesize($bilddatei)==$groesse_bilddatei_alt) {
										fwrite($dateihandle, "Bild ist bereits zugeordnet \n");
										$bildexists=false;
									}
								}
							if ($bildexists)  {
								if (DEBUGGER>=1 && $i==0) fwrite($dateihandle, "set_images_shopware -> Bild $bilddatei (".$shop_media_url.$bild.") hinzufuegen \n");
								if (strpos($shop_media_url.$bild, "." )!==false)
											$sql_data_array['images'][$i]['link']=$shop_media_url.$bild;
								$bildexists=true;
								// Auf Zusatzbilder 1 bis 10 pruefen
								if ($i==0) {
									for ( $j = 1; $j <=10; $j++ ) {
										$zusatzbilddatei = str_replace(".","_".$j.".",$bild);
										fwrite($dateihandle, "set_images_shopware -> zusatzbilddatei = ".SHOP_ROOT.DIR_ORIGINAL_IMAGES.$zusatzbilddatei." ... ");
										if (file_exists(SHOP_ROOT.DIR_ORIGINAL_IMAGES.$zusatzbilddatei)) {
											if (strpos($shop_media_url.$zusatzbilddatei, "." )!==false)
												$sql_data_array['images'][count($bilder)+$j]['link']=$shop_media_url.$zusatzbilddatei;
											if (DEBUGGER>=1 && $i<=2) fwrite($dateihandle, "Zusatzbild existiert ".$sql_data_array['images'][count($bilder)+$j]['link']." \n");
										//	unlink($zusatzbilddatei);
										} else  fwrite($dateihandle, "Zusatzbild existiert nicht: $zusatzbilddatei \n");
										
									}
								}
								fwrite($dateihandle, "Bild 0 = "."\n"+$sql_data_array['images'][0]['link']);
							}
						//	unlink($bilddatei);
						} else {
							if (DEBUGGER>=1 && $i==0) fwrite($dateihandle, "set_images_shopware Artikelnummer Bild $bilddatei (".$shop_media_url.$bilder[$i].") nicht vorhanden \n");
						} // end if else
						*/
						
					}
				}
					


?>