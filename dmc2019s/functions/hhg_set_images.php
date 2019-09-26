<?php
/************************************************
*                                            	*
*  dmConnector for shops						*
*  hhg_set_images.php							*
*  HHG Bildzuordnung						 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

//  HHG Bildzuordnung	
	function hhg_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $bilddatei, $dateihandle)
	{	
			if (DEBUGGER>=1) fwrite($dateihandle, " hhg_set_images.php fuer $bilddatei \n");	
	
			// $bilddatei muss grösser ALS 4 sein, denn 4 wäre nur .jpg oder .gif
			if (isset($bilddatei) && strlen($bilddatei)>4)
	        {
				 if (DEBUGGER>=1) fwrite($dateihandle, " bilddatei=$bilddatei zuornen \n");	
	
				// Dateiname OHNE Endung			
				$bilddatei_name = substr($bilddatei,0,-4);
				// Dateiname  Endung
				$bilddatei_extension = substr($bilddatei,-4,4);
				
				$files=array();
				// Auf 1+4 Bilder überprufen
				for ($i=0;$i<5;$i++) {
					// Standard ist 0 - $bilddatei
					// HHG
					$bilder_pfad=PRODUCTS_EXTRA_PIC_PATH;
					
					if (PRODUCTS_EXTRA_PIC_NAME == "ARTIKELNUMMER") $bilddatei_name = $Artikel_Artikelnr;		
					if ($i==1) $bilddatei = $bilddatei_name.PRODUCTS_EXTRA_PIC_EXTENSION."1".$bilddatei_extension;
					if ($i==2) $bilddatei = $bilddatei_name.PRODUCTS_EXTRA_PIC_EXTENSION."2".$bilddatei_extension;
					if ($i==3) $bilddatei = $bilddatei_name.PRODUCTS_EXTRA_PIC_EXTENSION."3".$bilddatei_extension;
					if ($i==4) $bilddatei = $bilddatei_name.PRODUCTS_EXTRA_PIC_EXTENSION."4".$bilddatei_extension;
					
					// Überprüfen, ob dem Artikel zugehörige Bilddatei existent. // ToDo strtolower($file) 
					if (is_file($bilder_pfad.$bilddatei)){
						if ($debugger==1) fwrite($dateihandle, "HHG Bilddatei ".$bilder_pfad.$bilddatei." vorhanden"."\n");
	                    $files[]=array(
	                                'id' => $bilddatei,
	                                'text' =>$bilddatei);
					} else {
						if ($debugger==1) fwrite($dateihandle, "HHG Bilddatei ".$bilder_pfad.$bilddatei." nicht vorhanden"."\n");
					} // endif 
				} // end for
		
            
				// Alle Bilddateien bearbeiten"
		        for ($i=0;$n=sizeof($files),$i<$n;$i++) {
		             $products_image_name = $files[$i]['text'];          
					
					  if ($debugger==1) fwrite($dateihandle, "Bearbeite Bilddatei: ".$bilder_pfad . $files[$i]['text']."\n");
					   // rewrite values to use resample classes
			          define('DIR_FS_CATALOG_ORIGINAL_IMAGES',$bilder_pfad);
			          define('DIR_FS_CATALOG_INFO_IMAGES',DIR_RCM_IMAGES."info_images/");
			          define('DIR_FS_CATALOG_POPUP_IMAGES',DIR_RCM_IMAGES."popup_images/");
			          define('DIR_FS_CATALOG_THUMBNAIL_IMAGES',DIR_RCM_IMAGES."thumbnail_images/");
			          define('DIR_FS_CATALOG_IMAGES',DIR_RCM_IMAGES."");
					   
					   // resample files
					    require('inc/product_thumbnail_images.php');
						require('inc/product_info_images.php');
						require('inc/product_popup_images.php');	
					   // Zuordnung der Bilder
					 	   $cmd = "select products_id,image_nr from " . TABLE_PRODUCTS_IMAGES . " where " .
									"products_id='$Artikel_ID' and image_nr='$i'";
						if (DEBUGGER>=1) fwrite($dateihandle, "in function hhg_attach_images_to_product -> $cmd \n");	
	
							$sql_query = dmc_db_query($cmd);
							// Erfolgt, wenn Zuordnung noch nicht existent
						    if (!($desc = dmc_db_fetch_array($sql_query)))
						    {
								$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'store_id' => 1,
									'image_nr' => $i, 
									'image_name' => $products_image_name);
								xtc_db_perform(TABLE_PRODUCTS_IMAGES, $insert_sql_data);
							} else {// end if 
								$sql_data_array= array(
									'store_id' => 1,
									'image_nr' => $i, 
									'image_name' => $products_image_name);
								xtc_db_perform(TABLE_PRODUCTS_IMAGES, $sql_data_array, 'update', "products_id = '$Artikel_ID'");
							}	
		         } 
	        }
	} // end if function hhg_attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $bilddatei, $dateihandle);

?>