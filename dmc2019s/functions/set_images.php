<?php
/************************************************
*                                            	*
*  dmConnecto fuer diverse shops				*
*  set_images.php								*
*  Bildzuordnungen							 	*
*  Copyright (C) 2011 DoubleM-GmbH.de			*
*                                               *
*************************************************/

 // 07122011 erweitert um attach_images_to_category 
 // 11012012 erweitert um Veyton Bilder in Media Tabelle zu schreiben veyton_fill_media_table($products_image_name, $class)
 // 20052015 erweitert um allgemeine Erkennung von Zusatzbildern, d.h. alles was mit Artikelnummer beginnt....
 
	// Allgemeine Bildzuordnung	 
	function attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $bilddatei, $bildbezeichnung, $dateihandle)
	{	
			fwrite($dateihandle, "set_images.php - attach_images_to_product - Bild ".$bilddatei."\n");
			 
			// Abfangen, dass in bildbezeichnung kein / sein arf, da Verzeichnis
			$bildbezeichnung =  str_replace('/', '_', $bildbezeichnung);
			$bildbezeichnung = $Artikel_Artikelnr;
			
			$bilder_pfad=DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.'/';
			if (!is_dir($bilder_pfad))
				if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false || strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) 
					$bilder_pfad=PRODUCTS_EXTRA_PIC_PATH;
					
					
			// $bilddatei muss grösser ALS 4 sein, denn 4 wäre nur .jpg oder .gif
			if (isset($bilddatei) && strlen($bilddatei)>4)
	        {
				// Dateiname OHNE Endung			
				$bilddatei_name = substr($bilddatei,0,-4);
				// Dateiname  Endung
				$bilddatei_extension = substr($bilddatei,-4);
				// $bilddatei_extension = '.jpg';

				$files=array();
				// Auf 1+4 Bilder überprufen
				for ($i=0;$i<10;$i++) {
					// Standard ist 0 - $bilddatei
					if (PRODUCTS_EXTRA_PIC_NAME == "ARTIKELNUMMER") $bilddatei_name = $Artikel_Artikelnr;		
					if ($i>=1) $bilddatei = $bilddatei_name.PRODUCTS_EXTRA_PIC_EXTENSION.$i.$bilddatei_extension;
					
					// Überprüfen, ob dem Artikel zugehörige Bilddatei existent. // ToDo strtolower($file) 
					if (is_file($bilder_pfad.$bilddatei)){
						//if ($i==0) $Bildname_Neu=$bildbezeichnung.'_'.$Artikel_ID.$bilddatei_extension;
						//if ($i>=1) $Bildname_Neu=$bildbezeichnung.'_'.$Artikel_ID."_".$i.$bilddatei_extension;
						//	copy ($bilder_pfad.$bilddatei,$bilder_pfad.$Bildname_Neu);
						if (DEBUGGER>=1 && $i<=1) fwrite($dateihandle, "52 Bilddatei ".$bilder_pfad.$bilddatei." (neu:$Bildname_Neu) vorhanden"."\n");
						$Bildname_Neu=$bilddatei;
						$files[]=array(
	                                'id' => $Bildname_Neu,
	                                'text' =>$Bildname_Neu);
					} else {
						// GROSS/KLEINSCHREIBUNG
						$bilddatei =  str_replace('.jpg', '.jpg', $bilddatei);
						if (is_file($bilder_pfad.$bilddatei)){
							if (DEBUGGER>=1 && $i<=1) fwrite($dateihandle, "73 Bilddatei ".$bilder_pfad.$bilddatei." vorhanden"."\n");
							if ($i==0) { 
								$Bildname_Neu=$bildbezeichnung.$bilddatei_extension;
								// Bilddatei umbenennen fuer seo
								copy ($bilder_pfad.$bilddatei,$bilder_pfad.$Bildname_Neu);
							}
							$files[]=array(
										'id' => $Bildname_Neu,
										'text' =>$Bildname_Neu);
						} else {
							if (DEBUGGER>=1 && $i<=1) fwrite($dateihandle, "84 - Bilddatei ".$bilder_pfad.$bilddatei." nicht vorhanden"."\n");
						}
					} // endif 
				} // end for
		
				// Pruefe auf Dateien beginnend mit Artikelnummer, wie "1234 toller artikel.jpg" - seit 200514
				// fwrite($dateihandle, "Pruefe auf Zusatzbilder\n");
			/*	if ($handle = opendir($bilder_pfad)) {
					while (false !== ($file = readdir($handle))) {
						if ($file != "." && $file != "..") {
							// fwrite($dateihandle, "Zusatzbild: $file\n");
							if  (strpos($file,$Artikel_Artikelnr)===0) {		// entspricht startswith, d.h. Dateibezeichnung faengt mit Artikelnummer an.
								 $files[]=array(
											'id' => $file,
											'text' =>$file);
							}						
						} 
					}
					closedir($handle);
				}
				*/
            
			// fwrite($dateihandle, "set_images 91 - Anzahl Bilder: ".sizeof($files)."\n");
					
				// Alle Bilddateien bearbeiten"
		        for ($i=0;$n=sizeof($files),$i<$n;$i++) {
		             $products_image_name = $files[$i]['text'];          
					// Extra Bilderpfad für Zusatzbilder
					//if ($i==0) $bilder_pfad=DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES;
					//	else $bilder_pfad=PRODUCTS_EXTRA_PIC_PATH.'/';
					if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false) $bilder_pfad=PRODUCTS_EXTRA_PIC_PATH;
				   
				  // $bilder_pfad = $bilder_pfad.'/';
				  // $bilder_pfad=PRODUCTS_EXTRA_PIC_PATH;
					// if (DEBUGGER>=1) 
					// fwrite($dateihandle, "set_images 100 - Bearbeite Datei: ".$bilder_pfad . $products_image_name."\n");
					   // rewrite values to use resample classes
			         if ($i==0) {
						define('DIR_FS_CATALOG_ORIGINAL_IMAGES',$bilder_pfad);
						define('DIR_FS_CATALOG_POPUP_IMAGES',DIR_FS_CATALOG.DIR_WS_POPUP_IMAGES);
						define('DIR_FS_CATALOG_INFO_IMAGES',DIR_FS_CATALOG.DIR_WS_INFO_IMAGES);
						define('DIR_FS_CATALOG_ISLIDER_IMAGES',DIR_FS_CATALOG.DIR_WS_ISLIDER_IMAGES);
						define('DIR_FS_CATALOG_THUMBNAIL_IMAGES',DIR_FS_CATALOG.DIR_WS_THUMBNAIL_IMAGES);
						define('DIR_FS_CATALOG_SIDEBAR_IMAGES',DIR_FS_CATALOG.DIR_WS_SIDEBAR_IMAGES);
						define('DIR_FS_CATALOG_SMALLPRODUCT_IMAGES',DIR_FS_CATALOG.DIR_WS_SMALLPRODUCT_IMAGES);
						define('DIR_FS_CATALOG_MOBILETHUMB_IMAGES',DIR_FS_CATALOG.DIR_WS_MOBILETHUMB_IMAGES);
						define('DIR_FS_CATALOG_MOBILEPOPUP_IMAGES',DIR_FS_CATALOG.DIR_WS_MOBILEPOPUP_IMAGES);
						define('DIR_FS_CATALOG_IMAGES',DIR_FS_CATALOG.DIR_WS_IMAGES); 
					}
					   // resample files
					   if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false || strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
							fwrite($dateihandle, "\nset_images veyton icon \n"); 
							require(DIR_FS_INC.'product_icon_images.php');
							fwrite($dateihandle, "\nset_images veyton info \n"); 
							require(DIR_FS_INC.'product_info_images.php');
							fwrite($dateihandle, "\n set_images veyton thumbnail \n");  
							require(DIR_FS_INC.'product_thumbnail_images.php');
							fwrite($dateihandle, "\nset_images veyton popup \n"); 
							require(DIR_FS_INC.'product_popup_images.php');	
							/* fwrite($dateihandle, "\nset_images veyton sidebar \n"); 
							require(DIR_FS_INC.'product_sidebar_images.php');
							fwrite($dateihandle, "\nset_images veyton mobilethumb \n"); 
							require(DIR_FS_INC.'product_mobilethumb_images.php');
							fwrite($dateihandle, "\nset_images veyton smallproduct \n"); 
							require(DIR_FS_INC.'product_smallproduct_images.php');
							fwrite($dateihandle, "\nset_images veyton islider \n"); 
							require(DIR_FS_INC.'product_islider_images.php');
							fwrite($dateihandle, "\nset_images veyton mobilepopup \n"); 
							require(DIR_FS_INC.'product_mobilepopup_images.php');	 */
						} else {		
							// fwrite($dateihandle, "132 set_images shop functions generiere für ".DIR_FS_CATALOG_THUMBNAIL_IMAGES." \n"); 
							require(DIR_FS_DOCUMENT_ROOT.'/admin/includes/product_thumbnail_images.php');
							require(DIR_FS_DOCUMENT_ROOT.'/admin/includes/product_info_images.php');
							require(DIR_FS_DOCUMENT_ROOT.'/admin/includes/product_popup_images.php');	
					   }
						
						// ab gambio gx, bzw das gallery images function existiert
						if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false || file_exists(DIR_FS_DOCUMENT_ROOT.'/admin/includes/product_gallery_images.php') ) {
							require(DIR_FS_DOCUMENT_ROOT.'/admin/includes/product_gallery_images.php');
						}
						 
						// Bildname umbennen in 
						if ($i==0) {
							$update_array = array( 'products_image' => $products_image_name	);
							//fwrite($dateihandle, "146 Bildname neu =$products_image_name\n");
							dmc_sql_update_array(TABLE_PRODUCTS, $update_array, "products_id='$Artikel_ID'");
						}
 
						// Formes - Bilder in die Order aus Thumbnail copieren
						/* if ($i==0) {
							$bilder_pfad_thumbnail_images =  str_replace('original_images', 'thumbnail_images', $bilder_pfad);
							$bilder_pfad_properties_combis_images =  str_replace('original_images', 'properties_combis_images', $bilder_pfad);
							$bilder_pfad_gm_gmotion_images =  str_replace('original_images', 'gm_gmotion_images', $bilder_pfad);
							$bilder_pfad_gallery_images =  str_replace('original_images', 'gallery_images', $bilder_pfad);
							$bilder_pfad_attribute_images =  str_replace('original_images', 'attribute_images', $bilder_pfad);
						}
						//  thumbnail_images zu properties_combis_images gm_gmotion_images gallery_images attribute_images
						fwrite($dateihandle, "FORMES set_images 136 copiere etc: ".$bilder_pfad_thumbnail_images. $products_image_name." zu ".$bilder_pfad_properties_combis_images. $products_image_name."\n");
						copy ($bilder_pfad_thumbnail_images.$products_image_name,$bilder_pfad_properties_combis_images.$products_image_name);
						copy ($bilder_pfad_thumbnail_images.$products_image_name,$bilder_pfad_gm_gmotion_images.$products_image_name);
						copy ($bilder_pfad_thumbnail_images.$products_image_name,$bilder_pfad_gallery_images.$products_image_name);
						copy ($bilder_pfad_thumbnail_images.$products_image_name,$bilder_pfad_attribute_images.$products_image_name);
						*/
						
				   // Zuordnung der (Extra-)Bilder
					// Bei dem ersten Bild sollen zunachest alle alten Verknuepfungen geloescht werden
					if ($i==0 && 
						( strpos(strtolower(SHOPSYSTEM), 'gambiogx2') !== false 
						|| strpos(strtolower(SHOPSYSTEM), 'xtcmodified') !== false 
						|| strpos(strtolower(SHOPSYSTEM), 'veyton') !== false)) {
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false)
							$cmd = "DELETE FROM ".TABLE_PRODUCTS_IMAGES." WHERE " .
							"products_id=$Artikel_ID";
						else 
							$cmd = "DELETE FROM " . TABLE_MEDIA_LINK . " WHERE " .
							"link_id=$Artikel_ID AND type='images' AND class='product'";
						dmc_db_query($cmd);
					}
						
					if (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) {
					
						if ($i>0 || strpos(strtolower(SHOPSYSTEM), 'xtcmodified') !== false){
						   $cmd = "SELECT products_id,image_nr FROM " . TABLE_PRODUCTS_IMAGES . " WHERE " .
									"products_id='$Artikel_ID' AND image_nr='$i'";
							$sql_query = dmc_db_query($cmd);
							// Erfolgt nur, wenn Zuordnung noch nicht existent
						    if (!($desc = dmc_db_fetch_array($sql_query)))
						    {
								$insert_sql_data= array(
									'products_id' => $Artikel_ID,
									'image_nr' => $i, 
									'image_name' => $products_image_name);
								if (strpos(strtolower(SHOPSYSTEM), 'gambiogx') !== false) $insert_sql_data['gm_show_image']=1;
								dmc_sql_insert_array(TABLE_PRODUCTS_IMAGES, $insert_sql_data);
								fwrite($dateihandle, "180 insert into: ".TABLE_PRODUCTS_IMAGES." $products_image_name\n");
							} else { // end if  
								// update
								$update_array= array(
									'image_name' => $products_image_name);
								dmc_sql_update_array(TABLE_PRODUCTS_IMAGES, $update_array, "products_id='$Artikel_ID' AND image_nr = $i");
							 fwrite($dateihandle, "186 update into: ".TABLE_PRODUCTS_IMAGES." $products_image_name\n");
							}
						} 
					} else if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false && $i>0) { // veyton
						$Bild_ID = veyton_fill_media_table($products_image_name, 'product', $dateihandle);
						$cmd = "SELECT ml_id FROM " . TABLE_MEDIA_LINK . " WHERE " .
							"m_id=$Bild_ID AND link_id=$Artikel_ID AND type='images' AND class='product'";
						$sql_query = dmc_db_query($cmd);
						// Erfolgt nur, wenn Zuordnung noch nicht existent
						if (!($desc = dmc_db_fetch_array($sql_query)))
						{
								$insert_sql_data= array(
								 // 'image_id'=> $,	// autoincrement
								 //'image_nr'=> $Bild_ID,	// image ID
								 //'products_id'=> $Artikel_ID,	// product id
								 'm_id'=> $Bild_ID,	// image ID
								 'link_id'=> $Artikel_ID,	// product id
								 'type'=>  'images',
								 'class'=> 'product',
								 'sort_order'=> $i);
								dmc_sql_insert_array(TABLE_MEDIA_LINK, $insert_sql_data);
						} // end if 
					} // end if 
					
				} // end for
				
				// in dmconnector include_once '../../xtCore/main.php';
				// in dmconnector include_once _SRV_WEBROOT.'/xtFramework/classes/class.ImageProcessing.php';
				if (strpos(strtolower(SHOPSYSTEM), 'veyton')) {
					fwrite($dateihandle, "Veyton Image processing \n");
					$data['mgID']=$Bild_ID;
					$processing = new ImageProcessing();
					$processing->run_processing($data);
				}
		    } // end if isset
			
			fwrite($dateihandle, "END set_images.php - attach_images_to_product - Bild ".$products_image_name."\n");
	} // end if function attach_images_to_product($Artikel_Artikelnr, $Artikel_ID, $bilddatei, $dateihandle);
	
	// Allgemeine Bildzuordnung	 
	function attach_images_to_category($Kategorie_ID, $bilddatei, $dateihandle)
	{	
		 IF (DEBUGGER>=1)	fwrite($dateihandle, "set_images.php - attach_images_to_category - Bild ".$bilddatei." to id Kategorie_ID\n");
			
			
			// $bilddatei muss grösser ALS 4 sein, denn 4 wäre nur .jpg oder .gif
			if ($Kategorie_ID<>"" && strlen($bilddatei)>4)
	        {
				// Extra Bilderpfad für Zusatzbilder
				$IMAGE_FOLDER=DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES.'/';
				if (strpos(strtolower(SHOPSYSTEM), 'hhg') !== false || strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) $IMAGE_FOLDER=PRODUCTS_EXTRA_PIC_PATH;
				//$IMAGE_FOLDER='/var/www/vhosts/.de/httpdocs/shop/media/images/org/';
				// Überprüfen, ob der Kategorie zugehörige Bilddatei existent. // ToDo strtolower($file) 
					if (is_file($IMAGE_FOLDER.$bilddatei)){
						if (DEBUGGER>=1) fwrite($dateihandle, "Bilddatei ".$IMAGE_FOLDER.$bilddatei." ist vorhanden"."\n");
						// Datei in Ordner kopieren
						if (strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) {
						//	copy($IMAGE_FOLDER . $bilddatei , DIR_FS_CATALOG."/media/images/category/icons/".$bilddatei);
						//	copy($IMAGE_FOLDER . $bilddatei , DIR_FS_CATALOG."/media/images/category/info/".$bilddatei);
						//	copy($IMAGE_FOLDER . $bilddatei , DIR_FS_CATALOG."/media/images/category/popup/".$bilddatei);
						//	copy($IMAGE_FOLDER . $bilddatei , DIR_FS_CATALOG."/media/images/category/thumb/".$bilddatei);
							$category_image_quality=80;
							// Icons
							$category_image_folder=DIR_FS_CATALOG."/media/images/category/icon/";
							$category_image_width=20;	// Steht in db table xt_image_type
							$category_image_height=20;
							fwrite($dateihandle, "set_images 215 - Bearbeite Datei: ".$category_image_folder . $bilddatei."\n");
							image_resize(DIR_FS_CATALOG.DIR_WS_ORIGINAL_IMAGES . $bilddatei, $category_image_folder .$bilddatei, 
							$width, $category_image_height, $crop=0);
							require(DIR_FS_INC.'dmc_category_images.php');
							// thumb
							$category_image_folder=DIR_FS_CATALOG."/media/images/category/thumb/";
							$category_image_width=150;	// Steht in db table xt_image_type
							$category_image_height=100;
							fwrite($dateihandle, "set_images 221 - Bearbeite Datei: ".$category_image_folder . $bilddatei."\n");
							require(DIR_FS_INC.'dmc_category_images.php');
							// info
							$category_image_folder=DIR_FS_CATALOG."/media/images/category/info/";
							$category_image_width=200;	// Steht in db table xt_image_type
							$category_image_height=180;
							require(DIR_FS_INC.'dmc_category_images.php');
							// popup
							$category_image_folder=DIR_FS_CATALOG."/media/images/category/popup/";
							$category_image_width=450;	// Steht in db table xt_image_type
							$category_image_height=500;
							require(DIR_FS_INC.'dmc_category_images.php');
						} else {
							copy($IMAGE_FOLDER . $bilddatei , DIR_FS_CATALOG."/images/categories/".$bilddatei);
						}
						// Update auf DBcategories_image
						$update_array = array( 'categories_image' => $bilddatei	);
						if (DEBUGGER>=1) fwrite($dateihandle, "249 Bilddatei ".$bilddatei." in DB ".TABLE_CATEGORIES." für ID =$Kategorie_ID."."\n");
						dmc_sql_update_array(TABLE_CATEGORIES, $update_array, "categories_id='$Kategorie_ID'");
						$exists=true;
					} else {
						if (DEBUGGER>=1) fwrite($dateihandle, "Bilddatei ".$IMAGE_FOLDER.$bilddatei." nicht vorhanden"."\n");
						$exists=false;
					} // endif 
		    }
			if ($exists && strpos(strtolower(SHOPSYSTEM), 'veyton') !== false) $Bild_ID = veyton_fill_media_table($bilddatei, 'category', $dateihandle);
			fwrite($dateihandle, "END set_images.php - attach_images_to_category - Bild ".$bilddatei."\n");

	} // end if function attach_images_to_category
				
	// Veyton-Bilder in Media Tabelle schreiben
	function veyton_fill_media_table($products_image_name, $class, $dateihandle)
	{	
		if ($class == 'category') $class2 = 3;
		else  $class2 = 2;
		
		fwrite($dateihandle, "veyton_fill_media_table - Bild ".$products_image_name."\n");
		
		$cmd = "SELECT id FROM " . TABLE_MEDIA . " WHERE " .
				"file='$products_image_name' AND type='images' AND class='$class'";
				
		fwrite($dateihandle, "statement ".$cmd."\n");
		$sql_query = dmc_db_query($cmd);
		
		if ($desc = dmc_db_fetch_array($sql_query))
		{
			$Bild_ID=$desc['id'];
			$update_array= array(
				'file' => $products_image_name
			);
			
			fwrite($dateihandle, "file -> in tabelle (".TABLE_MEDIA.")".$products_image_name."\n");
			dmc_sql_update_array(TABLE_MEDIA, $update_array, "id='$Bild_ID' ");
		
			fwrite($dateihandle, "versuche in db zu aktualisieren\n");
			$update_array2= array(
				'm_id'=> $Bild_ID,
				'mg_id'=> $class2
			);
			dmc_sql_update_array('xt_media_to_media_gallery', $update_array2, "m_id='$Bild_ID' ");					
		
			fwrite($dateihandle, "erfolgreicher UPDATE  in xt_media_to_media_gallery \n");
		} else {
			
			// Erfolgt nur, wenn Zuordnung noch nicht existent
			$insert_sql_data= array(
				 // 'id'=> $,	// autoincrement
				 'file'=> $products_image_name,
				 'type'=>  'images',
				 'class'=> $class,
				 'download_status'=> 'free',
				 'status'=>  'true',
				 'owner'=> 1,
				 'date_added'=> 'now()',
				 'last_modified'=> 'now()',
				 'max_dl_count'=> 0,
				 'max_dl_days' => 0
			); 
			
			fwrite($dateihandle, "Class: ".$class."\n");
			dmc_sql_insert_array(TABLE_MEDIA, $insert_sql_data);
			
			fwrite($dateihandle, "vor xtc... BildID = $Bild_ID, Class = $class2\n");			
			$Bild_ID = dmc_db_get_new_id();  // ID wird auf Basis der letzten per autoincrement eingefügten id (+1) ermittelt
			fwrite($dateihandle, "nach xtc... BildID = $Bild_ID, Class = $class2\n");
			
			fwrite($dateihandle, "BildID = $Bild_ID, Class = $class2\n");
			// Erfolgt nur, wenn Zuordnung noch nicht existent
			fwrite($dateihandle, "verusche in xt_media_to_media_gallery zu schreiben \n");
			$insert_sql_data= array(
				'm_id'=> $Bild_ID,
				'mg_id'=> $class2
			); 
			dmc_sql_insert_array('xt_media_to_media_gallery', $insert_sql_data);	
			fwrite($dateihandle, "update xt_media_to_media_gallery set m_id='$Bild_ID',mg_id='$class2' \n");
			fwrite($dateihandle, "update erfolgreich\n");
		}
		fwrite($dateihandle, "vor dem return der Funktion\n");
		return $Bild_ID;
	} // end if function veyton_fill_media_table($products_image_name, $class)
	
	function image_resize($src, $dst, $width, $height, $crop=0){

  if(!list($w, $h) = getimagesize($src)) return "Unsupported picture type!";

  $type = strtolower(substr(strrchr($src,"."),1));
  if($type == 'jpeg') $type = 'jpg';
  switch($type){
    case 'bmp': $img = imagecreatefromwbmp($src); break;
    case 'gif': $img = imagecreatefromgif($src); break;
    case 'jpg': $img = imagecreatefromjpeg($src); break;
    case 'png': $img = imagecreatefrompng($src); break;
    default : return "Unsupported picture type!";
  }

  // resize
  if($crop){
    if($w < $width or $h < $height) return "Picture is too small!";
    $ratio = max($width/$w, $height/$h);
    $h = $height / $ratio;
    $x = ($w - $width / $ratio) / 2;
    $w = $width / $ratio;
  }
  else{
    if($w < $width and $h < $height) return "Picture is too small!";
    $ratio = min($width/$w, $height/$h);
    $width = $w * $ratio;
    $height = $h * $ratio;
    $x = 0;
  }

  $new = imagecreatetruecolor($width, $height);

  // preserve transparency
  if($type == "gif" or $type == "png"){
    imagecolortransparent($new, imagecolorallocatealpha($new, 0, 0, 0, 127));
    imagealphablending($new, false);
    imagesavealpha($new, true);
  }

  imagecopyresampled($new, $img, 0, 0, $x, 0, $width, $height, $w, $h);

  switch($type){
    case 'bmp': imagewbmp($new, $dst); break;
    case 'gif': imagegif($new, $dst); break;
    case 'jpg': imagejpeg($new, $dst); break;
    case 'png': imagepng($new, $dst); break;
  }
  return true;
}
?>
