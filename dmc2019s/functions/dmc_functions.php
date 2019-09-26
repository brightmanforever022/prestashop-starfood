<?php
/********************************************************************************
*                                                                               *
*  dmConnector  for magento shop												*
*  dmc_functions.php															*
*  ehemals dmc_functions.php													*
*  Allgemeine Funktionen														*
*  Copyright (C) 2008-16 DoubleM-GmbH.de										*
*                                                                               *
********************************************************************************/
//  21.05.2010 - Neue Funktion print_post
// 21.07.2010 - Neue funktion log_array
// 06.01.2011 - nicht UTF8 Buchstaben zu # - function prove_utf8($str)
// 27.02.2011 - rtf Unterstuetzung convert_rtf_2_html
// 27.02.2011 - neue umlaute_order_export mit unbekannte / nicht UTF8 Zeichen zu #
// 16.03.2012 - ansi2ascii aus ehem. dm_ansi_functions uebernommen
// 06.01.2014 - delFiles($verzeichnis,$endung,$sekundenalt) um Dateien aus einem Ordner zu loeschen, zB session oder cache
// 17.08.2014 - dmc_get_shopware_api_client($url,$user,$apikey) // API Verbindung zu Shopware herstellen
// 07.01.2016 - dmc_php_mail_4_xtc basierend auf xtc_php_mail fuer XTC Syste // Status eMail senden
// 07.03.2016 - dmc_send_email($an_email,$von_email,$von_name,$betreff,$inhalt) //  eMail senden
// 06.09.2017 - dmc_generate_seo($wert)	// suchmaschinen URL oder Kategorie_id
// 06.09.2017 - dmc_convert_umlaute($wert)	// Umlaute konvertieren
// dmc_php_mail_4_xtc basierend auf xtc_php_mail fuer XTC Systeme
// 06.09.2017 dmc_convert_umlaute - Umlaute konvertieren
// 06.09.2017 dmc_generate_seo - @Suchmaschinen URL oder Kategorie_id erstellen
// 23.10.2017 dmc_compare_files - Pruefung, ob Dateien identisch sind, ruckegabe true oder false
	
	
defined( '_DMC_ACCESSIBLE' ) or die( 'Direct Access to this location is not allowed.' );

	/**
	 *
	 * @Update order status
	 * Ergänzt am 20.07.2010 rcm
	 */
	function dmc_set_OrderStatus(){
		  // Update Order Status rcm
		  $Order_ID = (integer)($_POST['Order_ID']);	
			$status_id = $_POST['Status_ID'];
		
		if (UPDATE_ORDER_STATUS_ERP==true) {
			// Post ermitteln		  
			
			// map status from ERP
			if ($status_id=="written") $Status = NEW_ORDER_STATUS_ERP;
			if ($status_id=="error") $Status = NEW_ORDER_STATUS_FAILED;
				
			$LangID = 2;	// 2= deutsch

			  $orders_status_array = array();
			  $cmd = "select orders_status_id, orders_status_name FROM " .
			    TABLE_ORDERS_STATUS . " where language_id = '" . (int)$LangID . "'";
			  $orders_status_query = dmc_db_query($cmd);
			  while ($orders_status = dmc_db_fetch_array($orders_status_query))
			  {
			    $orders_status_array[$orders_status['orders_status_id']] = $orders_status['orders_status_name'];
			  }

			  if ($Order_ID != 0 && isset($orders_status_array[$Status]))
			  {
			    $cmd = "select customers_name, customers_email_address, orders_status, date_purchased, language from " .
			      TABLE_ORDERS . " where orders_id = '" . $Order_ID . "'";
			    $Order_Query = dmc_db_query($cmd);
			    if ($Order = dmc_db_fetch_array($Order_Query))
			    {
			      if ($Order['orders_status'] != $Status)
			      {
			        $update_sql_data = array(
			          'orders_status' => $Status,
			          'last_modified' => 'now()');
			       dmc_sql_update_array(TABLE_ORDERS, $update_sql_data, "orders_id='$Order_ID'");

					// Kundeninformation per eMail senden
					if (NOTIFY_CUSTOMER_ERP=='true') {
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
				        $smarty->assign('ORDER_NR',$Order_ID);
				        $smarty->assign('ORDER_LINK',xtc_href_link(FILENAME_CATALOG_ACCOUNT_HISTORY_INFO, 'order_id=' . $Order_ID, 'SSL'));
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
					} // endif Kunden Info Mail senden

			        $insert_sql_data = array(
			          'orders_id' => $Order_ID,
			          'orders_status_id' => $Status,
			          'date_added' => 'now()',
			          'customer_notified' => '1',
			          'comments' => '');
			      dmc_sql_update_array(TABLE_ORDERS_STATUS_HISTORY, $insert_sql_data);

			      }
			    }
			  }
			}
		
		// return the string
		//  return $s;
	}// end function    dmc_set_OrderStatus
	

	/**
	 *
	 * @convert 
	 * @param string $s
	 * @return string $s
	 * Ergänzte am 20.03.09 rcm
	 */
	 
	function sonderzeichen2html($do_decode,$s){
		global $dateihandle;
//		fwrite($dateihandle, "*** Sonderzeichen2HTML Begin mit $s ***\n");		
		// decode any entities 
		 //	 $s = strtr($s,array_flip(get_html_translation_table(HTML_ENTITIES)));
		 
		 // convert & 
		 $s = preg_replace('@&@i','&amp;',$s);
		 		 
		 //  Umlaute (für GS Auftrag)
		// $d1 = array("Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","é","•");
		// $d2 = array("&#196;","&#214;","&#220;","&#228;","&#246;","&#252;","&#223;","e","&bull;");
		 //$s = str_replace($d1, $d2, $s);
		 $s = str_replace("é", "e", $s);		 
		 $s = preg_replace('@Ã„@i','&#196;',$s);
		 $s = preg_replace('@Ã–@i','&#214;',$s);
		 $s = preg_replace('@Ãœ@i','&#220;',$s);
		 $s = preg_replace('@Ã¤@i','&#228;',$s);
		 $s = preg_replace('@Ã¶@i','&#246;',$s);
		 $s = preg_replace('@Ã¼@i','&#252;',$s);
		 $s = preg_replace('@ÃŸ@i','&#223;',$s);
		 $s = preg_replace('@Ã˜@i','&Oslash;',$s);	// durchmesser
		 
		 $s = preg_replace('@Ã˜@i','&Oslash;',$s);	// durchmesser
		 $s = preg_replace('@Âº@i','&deg;',$s);	// grad 
		 $s = preg_replace('@Â°@i','&deg;',$s);	// grad 
		 $s = preg_replace('@Ã©@i','&eacute;',$s);	// e akzent degue
		 $s = preg_replace('@Ã©@i','&eacute;',$s);	// e akzent degue
		 $s = preg_replace('@Ãš@i','&egrave;',$s);	// e akzent grave
		 $s = preg_replace('@Ã¨@i','&egrave;',$s);	// e akzent grave 
		 $s = preg_replace('@é@i','&egrave;',$s);	// e akzent degue 
		 $s = preg_replace('@â€@i','&quot;',$s);	// anführungszeichen 
		// $s = preg_replace('@â@i','&quot;',$s);	// anführungszeichen 
		$s = preg_replace('@â@i','&quot;',$s);	// anführungszeichen 
		
		
		//Sonderzeichen CSV/ ODBC Anfang
		//$s = str_replace("ä¼", "ü", $s);		
		$s = str_replace("ä§","ç", $s);
		$s = str_replace("Ã©","é", $s);
		$s = str_replace("ä©","é", $s);
		$s = str_replace("Ã¨","è", $s);
		$s = str_replace("ä¨","è", $s);
		$s = str_replace("Ãª","ê", $s);
		$s = str_replace("äª","ê", $s);
		$s = str_replace("Ã«","ë", $s);	
		$s = str_replace("ä«","ë", $s);	
		$s = str_replace("Ã?","Ê", $s);
		$s = str_replace("ä?","Ê", $s);
		$s = str_replace("Ã?","Ë", $s);
		$s = str_replace("ä?","Ë", $s);
		$s = str_replace("Ã®","î", $s);
		$s = str_replace("ä®","î", $s);
		$s = str_replace("Ã¯","ï", $s);
		$s = str_replace("ä¯","ï", $s);
		$s = str_replace("Ã¬","ì", $s);
		$s = str_replace("Ã?","Î", $s);
		$s = str_replace("ä?","Î", $s);
		$s = str_replace("Ã²","ò", $s);	
		$s = str_replace("ä²","ò", $s);	
		$s = str_replace("Ã´","ô", $s);
		$s = str_replace("ä´","ô", $s);
		$s = str_replace("Ã¶","ö", $s);	
		$s = str_replace("ä¶","ö", $s);	
		$s = str_replace("Ãµ","õ", $s);
		$s = str_replace("Ã³","ó", $s);
		$s = str_replace("Ã¸","ø", $s);
		$s = str_replace("äµ","õ", $s);
		$s = str_replace("ä³","ó", $s);
		$s = str_replace("ä¸","ø", $s);
		$s = str_replace("Ã?","Ô", $s);
		$s = str_replace("ä?","Ô", $s);
		$s = str_replace("Ã?","Ö", $s);	
		$s = str_replace("ä?","Ö", $s);	
		$s = str_replace("Ã ","à", $s);
		$s = str_replace("ä ","à", $s);
		$s = str_replace("Ã¢","â", $s);
		$s = str_replace("ä¢","â", $s);
		$s = str_replace("Ã¤","ä", $s);	
		$s = str_replace("ä¤","ä", $s);
		$s = str_replace("Ã¥","å", $s);
		$s = str_replace("ä¥","å", $s);
		$s = str_replace("Ã?","Â", $s);
		$s = str_replace("ä?","Â", $s);
		$s = str_replace("Ã?","Ä", $s);	
		$s = str_replace("ä?","Ä", $s);	
		$s = str_replace("Ã¹","u", $s);	
		$s = str_replace("Ã»","û", $s);
		$s = str_replace("Ã¼","ü", $s);
		$s = str_replace("ä¼","ü", $s);
		$s = str_replace("Ã?","Û", $s);
		$s = str_replace("Ã?","Ü", $s);
		$s = str_replace("ä¹","u", $s);	
		$s = str_replace("ä»","û", $s);
		$s = str_replace("ä¼","ü", $s);
		$s = str_replace("ä¼","ü", $s);
		$s = str_replace("ä?","Û", $s);
		$s = str_replace("ä?","Ü", $s);
		$s = str_replace("Ã²","ñ", $s);
		$s = str_replace("Ã±","ñ", $s);	
		$s = str_replace("\x0d","", $s);	
		$s = str_replace("\\x0d","", $s);	
		$s = str_replace("\x0a","", $s);	
		$s = str_replace("\\x0a","", $s);	
		$s = str_replace("Â","´", $s);	
		$s = str_replace("äÜ","ö", $s);	
		$s = str_replace("ä&Uuml;","ö", $s);	
		$s = str_replace("äŸ","ß", $s);	
		$s = str_replace("\\x0","", $s);	
		$s = str_replace("\\x0","", $s);	
		$s = str_replace("â€","", $s);	
		$s = str_replace("Ã„","Ä", $s);	
		$s = str_replace("â€¦","", $s);	
		$s = str_replace("ÃŸ","ß", $s);	
		$s = str_replace("™","", $s);	
		$s = str_replace("â€","", $s);	
		$s = str_replace("ä„","Ä", $s);	
		$s = str_replace("&agrave;´","´", $s);	
		$s = str_replace("¦","", $s);	
		$s = str_replace("´´","´", $s);	
		$s = str_replace("Ã–","Ö", $s);	
		$s = str_replace("Ãœ","Ü", $s);	
	
		
		//Sonderzeichen CSV/ ODBC ende
		// Zoll
		$s = str_replace("\'\'", "Zoll", $s);		 
		 
		 	// Zeilenumbruch
		 $s = str_replace("\n",'<br>',$s);	// Zeilenumbruch
		 $s = str_replace("\n\r",'<br>',$s);	// Zeilenumbruch
			$s = str_replace("\r\n",'<br>',$s);	// Zeilenumbruch
			$s = str_replace("\r",'<br>',$s);	// Zeilenumbruch
		 $s = str_replace("\\n",'<br>',$s);	// Zeilenumbruch
		
		
		// ggfls RTF TEXT umwandeln
		// DECREPATED, da nun von JAVA aus
		 if (substr($s,0,1) == "{") {
			convert_rtf_2_html ($s);
			// Fehlerabfangroutine
			// RTF entfernen
			$s = str_replace("\\\\", '||', $s);
			$s = str_replace('\\\\', '||', $s);
			$s = str_replace("||||", '||', $s); 
								
			$s = str_replace("{||f1||fnil||fcharset0 Verdana;||viewkind4||uc1d||f0||fs20", '', $s);
			$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||froman||fcharset0 Times New Roman;", '', $s);
			$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;}}", '', $s);
			$s = str_replace('{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;}}', '', $s);
			$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;", '', $s);
			$s = str_replace("||viewkind4||uc1||pard||f0||fs17||\\'b0", '', $s);  
			$s = str_replace("||\\'b0", '<br>', $s);
			$s = str_replace("||\\'e4", '&auml;', $s);
			$s = str_replace("||\'f6", '&ouml;', $s);
				
			$s = str_replace("||\\'fc", '&uuml;', $s);
			$s = str_replace("||par", '', $s);
			$s = str_replace("}", '', $s);
		} 
		
		 $s = str_replace("é", "e", $s);		 
 	//	 $d1 = array("&#196;","&#214;","&#220;","&#228;","&#246;","&#252;","&#223;");
	//	 $d2 = array("Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß","é");
	//	$s = str_replace($d1, $d2, $s);
		// $s = str_replace("# ", "<li>", $s);		 
		
		// GS-Auftrag RTF spezifisch
		// Wenn im Text <style> enthalten, Ersten Absatz suchen und darf den Teil entfernen
		if (strpos($s, '<style>') !== false) {
			$pos = strpos($s,'<p ');
			if ($pos !== false) {
				$s=substr($s,$pos);	
			}
		} else {
			// Zeilenumbruch in HTML
			$s = nl2br($s); // \n -> br	Zeilenumbruch 
		}

			// Datenbank Steuerzeichen
			$s = str_replace('\'', '', $s);	
		
		$s = str_replace('\"', '"', $s);	
		
		// decodierung oder encodierung
		$SONDERZEICHEN = SONDERZEICHEN;
	//	fwrite($dateihandle, " Sonderzeichen2HTML  SONDERZEICHEN = ".SONDERZEICHEN." \n");#}
		// $SONDERZEICHEN =='utf8encode';
		if ($do_decode){
			// fwrite($dateihandle, " Sonderzeichen2HTML SONDERZEICHEN=".SONDERZEICHEN." \n");
			if (strpos ($SONDERZEICHEN, 'decode' )!== false) {
				// ALT2: WaWi UTF8 und Shop ANSI
				$s=utf8_decode($s);
	//			fwrite($dateihandle, " Sonderzeichen2HTML  utf8decode \n");#}
			} else if (strpos ($SONDERZEICHEN, 'encode' )!== false) {
				// ALT3: WaWi ANSI und Shop UTF8
				$s=utf8_encode($s);
		//		fwrite($dateihandle, " Sonderzeichen2HTML  utf8_encode \n");#}
			} else {
				// ALT1: Entweder Zeichensaetze stimmen
				$s=$s;
			//	fwrite($dateihandle, " Sonderzeichen2HTML weder utf8_encode noch utf8_decode\n");#}
			}
				
		} else {
			// ALT1: Entweder Zeichensaetze stimmen
			$s=$s;
			fwrite($dateihandle, " Sonderzeichen2HTML KEIN do_decode \n");#}
		}
		
			// ALT 1 - Zeichensaetze ERP - Shop entsprechen sich
			$s=$s;
			// ALT2: WaWi UTF8 und Shop ANSI
	//		$s=utf8_decode($s);
			// ALT3: WaWi ANSI und Shop UTF8
	//		$s=utf8_encode($s);
	
		// Zeilenumbruch in HTML
		$s = nl2br($s); // \n -> br	Zeilenumbruch 
	
	// Zeilenumbruch
		/* $s = str_replace("\n",'<br>',$s);	// Zeilenumbruch
		 $s = str_replace("\n\r",'<br>',$s);	// Zeilenumbruch
			$s = str_replace("\r\n",'<br>',$s);	// Zeilenumbruch
			$s = str_replace("\r",'<br>',$s);	// Zeilenumbruch */
		 $s = str_replace("\\n",'<br>',$s);	// Zeilenumbruch
		
		
		 // return the string
		 return $s;
	}// end function    sonderzeichen2html

	/**
	 *
	 * @convert html to ascii chars
	 * @param string $s
	 * @return string $s
	 *
	 */
	function html2ascii($s){
		 // convert links
		 $s = preg_replace('/<a\s+.*?href="?([^\" >]*)"?[^>]*>(.*?)<\/a>/i','$2 ($1)',$s);
		 
		 // convert p, br and hr tags
		 $s = preg_replace('@<(b|h)r[^>]*>@i',"\n",$s);
		 $s = preg_replace('@<p[^>]*>@i',"\n\n",$s);
		 $s = preg_replace('@<div[^>]*>(.*)</div>@i',"\n".'$1'."\n",$s);
		 
		 // convert bold and italic tags
		 $s = preg_replace('@<b[^>]*>(.*?)</b>@i','*$1*',$s);
		 $s = preg_replace('@<strong[^>]*>(.*?)</strong>@i','*$1*',$s);
		 $s = preg_replace('@<i[^>]*>(.*?)</i>@i','_$1_',$s);
		 $s = preg_replace('@<em[^>]*>(.*?)</em>@i','_$1_',$s);
		 
		 // decode any entities
		 $s = strtr($s,array_flip(get_html_translation_table(HTML_ENTITIES)));
		 
		 // convert & 
		// $s = preg_replace('@&@i','&amp;',$s);
		 		 
		/* //  Umlaute für GS Auftrag
		 $d1 = array("Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß");
		 $d2 = array("&#196;","&#214;","&#220;","&#228;","&#246;","&#252;","&#223;");
		// $s = str_replace($d1, $d2, $s);		 
		 $s = preg_replace('@Ã„@i','&#196;',$s);
		 $s = preg_replace('@Ã–@i','&#214;',$s);
		 $s = preg_replace('@Ãœ@i','&#220;',$s);
		 $s = preg_replace('@Ã¤@i','&#228;',$s);
		 $s = preg_replace('@Ã¶@i','&#246;',$s);
		 $s = preg_replace('@Ã¼@i','&#252;',$s);
		 $s = preg_replace('@ÃŸ@i','&#223;',$s); 
		 $s = str_replace('²', '2', $s);
		 $s = str_replace('é', 'e', $s);
		 $s = preg_replace('@Ã©@i','e',$s); 
		
		  
		$s = str_replace('Ã?', 'ß', $s);
		$s = str_replace('ÃŒ', 'ü', $s);
		$s = str_replace('´', ' ', $s);
		
		 // decode numbered entities
		 // $s = preg_replace('//e','chr(\\1)',$s);
		 */
		 // strip any remaining HTML tags
		 $s = strip_tags($s);
		 
		 // return the string
		 return $s;
	}// end function    
	
	/**
	 *
	 * @check if string is utf8
	 * @param string $s
	 * @return string $s
	 *
	 */
	function is_utf8_codierung($str){
	  $strlen = strlen($str);
	  for($i=0; $i<$strlen; $i++){
		$ord = ord($str[$i]);
		if($ord < 0x80) continue; // 0bbbbbbb
		elseif(($ord&0xE0)===0xC0 && $ord>0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
		elseif(($ord&0xF0)===0xE0) $n = 2; // 1110bbbb
		elseif(($ord&0xF8)===0xF0 && $ord<0xF5) $n = 3; // 11110bbb (exkl F5-FF)
		else return false; // ungültiges UTF-8-Zeichen
		for($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
		  if(++$i===$strlen || (ord($str[$i])&0xC0)!==0x80)
			return false; // ungültiges UTF-8-Zeichen
	  }
	  return true; // kein ungültiges UTF-8-Zeichen gefunden
	}
	
	/**
	 *
	 * @filter string to utf8
	 * @param string $str
	 * @return string rueckgabe
	 *
	 */
	function umlaute_order_export($str){
				global $dateihandle;
			
		// & ist nicht XML Konform
//		$str = str_replace("&", '+', $str);
		$str = str_replace("'", "", $str);
		/*
	  $strlen = strlen($str);
	  for($i=0; $i<$strlen; $i++){
		$ord = ord($str[$i]);
		if($ord < 0x80) {
			$rueckgabe .= $str[$i];
			continue; // 0bbbbbbb
		}
		elseif(($ord&0xE0)===0xC0 && $ord>0xC1) $n = 1; // 110bbbbb (exkl C0-C1)
		elseif(($ord&0xF0)===0xE0) $n = 2; // 1110bbbb
		elseif(($ord&0xF8)===0xF0 && $ord<0xF5) $n = 3; // 11110bbb (exkl F5-FF)
		else {
			// ungültiges UTF-8-Zeichen
			// Versuch Gültigkeit durch en/dekodierung zu bekommen
			if (is_utf8_codierung("A".utf8_decode($str[$i])) && utf8_decode($str[$i])!='?') $rueckgabe .= utf8_decode($str[$i]);
			else if (is_utf8_codierung("B".utf8_encode($str[$i]))) $rueckgabe .= utf8_encode($str[$i]);
			else $rueckgabe .= "#";
		}
		for($c=0; $c<$n; $c++) // $n Folgebytes? // 10bbbbbb
			if(++$i===$strlen || (ord($str[$i])&0xC0)!==0x80) {
				// ungültiges UTF-8-Zeichen
				// Versuch Gültigkeit durch en/dekodierung zu bekommen
				if (is_utf8_codierung("A".utf8_decode($str[$i])) && utf8_decode($str[$i])!='?') $rueckgabe .= utf8_decode($str[$i]);
				else if (is_utf8_codierung("B".utf8_encode($str[$i]))) $rueckgabe .= utf8_encode($str[$i]);
				else $rueckgabe .= "#"; 
			} else {
				$rueckgabe .= $str[$i];
			}
	  }*/
	  
		$SONDERZEICHEN = trim(SONDERZEICHEN);
		//fwrite($dateihandle, " umlaute_order_export mit  SONDERZEICHEN = ".$SONDERZEICHEN."  -> ".$str."\n");#}
		// $SONDERZEICHEN =='utf8encode';
		// fwrite($dateihandle, " Sonderzeichen2HTML SONDERZEICHEN=".SONDERZEICHEN." \n");
			if (strpos ($SONDERZEICHEN, 'decode' )!== false) {
				// ALT2: WaWi UTF8 und Shop ANSI
				$str=utf8_decode($str);
			} else if (strpos ($SONDERZEICHEN, 'encode' )!== false) {
				// ALT3: WaWi ANSI und Shop UTF8
				$str=utf8_encode($str);
				// Ggfls ungueltige UTF8 Zeichen ausfiltern
				/* $strlen = strlen($str);
				  for($i=0; $i<$strlen; $i++){
					$ord = ord($str[$i]);
					if(mb_detect_encoding($str[$i], 'UTF-8, ISO-8859-1') === 'UTF-8'){
					  # der String ist in UTF-8 kodiert
					} else {
						# das Zeichen ist nicht valides UTF8
						$str[$i] = '';
					}
				  } */
			} else {
				// ALT1: Entweder Zeichensaetze stimmen
				$str=$str;
				// Ggfls ungueltige UTF8 Zeichen ausfiltern AUSKOMMENTIEREN, WENN NICHT UTF-8 exportiert werden soll
				/* $strlen = strlen($str);
				  for($i=0; $i<$strlen; $i++){
					$ord = ord($str[$i]);
					if(mb_detect_encoding($str[$i], 'UTF-8, ISO-8859-1') === 'UTF-8'){
					  # der String ist in UTF-8 kodiert
					} else {
						# das Zeichen ist nicht valides UTF8
						$str[$i] = '';
					}
				  } */
			} 
			
		//fwrite($dateihandle, " umlaute_order_export mit  SONDERZEICHEN = ".SONDERZEICHEN."  -> ".$str."\n");#}
		
		$rueckgabe = $str;
		return "<![CDATA[".trim($rueckgabe)."]]>"; 
	}
	
	/**
	 *
	 * @convert RTF text to HTNL
	 * @param string $s
	 * @return $s
	 *
	 */
	// rtf in html 
	function convert_rtf_2_html ($s) {
	
			global $dateihandle;
			
			// include rtf functions
			fwrite($dateihandle, "rtf alt= $s \n");
			require_once('functions/dmc_rtfclass.php');
			$rtf=$s;
			$r = new rtf( stripslashes( $rtf));
			$r->output( "html");
			$r->parse();
			if( count( $r->err) == 0) { // no errors detected
				$s=$r->out;
				$s.='.'.$s;
			} else { // Fehlerabfangroutine
				// RTF entfernen
				$s = str_replace("\\\\", '||', $s);
				$s = str_replace('\\\\', '||', $s);
				$s = str_replace("||||", '||', $s); 
								
				$s = str_replace("{||f1||fnil||fcharset0 Verdana;||viewkind4||uc1d||f0||fs20", '', $s);
				$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||froman||fcharset0 Times New Roman;", '', $s);
				$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;}}", '', $s);
				$s = str_replace('{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;}}', '', $s);
				$s = str_replace("{||rtf1||ansi||ansicpg1252||deff0||deflang2055{||fonttbl{||f0||fnil||fcharset0 Microsoft Sans Serif;", '', $s);
					$s = str_replace("||viewkind4||uc1||pard||f0||fs17||\\'b0", '', $s);  
				$s = str_replace("||\\'b0", '<br>', $s);
				$s = str_replace("||\\'e4", '&auml;', $s);
				$s = str_replace("||\'f6", '&ouml;', $s);
				
				$s = str_replace("||\\'fc", '&uuml;', $s);
				$s = str_replace("||par", '', $s);
				$s = str_replace("}", '', $s);
				if (strpos ( $s, 'Abbildung kann vom Original' )!==false) {
					$s='Abbildung kann vom Original abweichen';
				} 
				$s = "RTF Fehler"; 
			} // end 
			fwrite($dateihandle, "rtf neu= $s \n");
			
			return $s;
		} // end function convert_rtf_2_html
	

	// die uebergabenen Daten loggen
	function print_post($dateihandle) {
				
				global $debugger, $dateihandle, $action, $version_year, $version_month;
				
				  $ergebnis = "  <GetDaten>\n";
				  foreach ($_GET as $Key => $Value)
				  {
					if ($Key<>'password' && $Key<>'user')
				 $ergebnis .= "    <$Key>".substr($Value,0,300)."</$Key>\n";
				  }
				  $ergebnis .= "  </GetDaten>\n";

				  $ergebnis .= "  <PostDaten>\n";
				  foreach ($_POST AS $Key2 => $Value)
				  {
					if ($Key2<>'password' && $Key2<>'user')
				    $ergebnis .= "    <$Key2>".substr($Value,0,300)."</$Key2>\n";
				  }
				  fwrite($dateihandle, "\nUebergebene Daten:\n".$ergebnis."\n ********************************** \n");
	} // end print post
	
	function ansi2ascii($s){
		
		//  Umlaute für GS Auftrag
		 $d1 = array("Ä", "Ö", "Ü", "ä" , "ö", "ü", "ß");
		 $d2 = array("&#196;","&#214;","&#220;","&#228;","&#246;","&#252;","&#223;");
		 $s = str_replace($d1, $d2, $s);		 

		 // strip any remaining HTML tags
		 $s = strip_tags($s);
		 
		 // return the string
		 return $s;
	}// end function 

	// Löschen von Dateien aus einem Verzeichnis ggfls mit spezieller Endung, ggfls nur mit einer Zeit in Sekunden, zB 3600, aelter als 3600 Sekunden
	function delFiles($verzeichnis,$endung,$sekundenalt)
	{
		$time = gettimeofday();
        
		if (substr($verzeichnis,-1)!="/") $verzeichnis = $verzeichnis."/";
		// Nur wenn Variable deklarieren
		if (is_dir($verzeichnis)) {
			// Variable deklarieren und Verzeichnis öffnen
			$verz = opendir($verzeichnis);
			// Verzeichnisinhalt auslesen
			while ($file = readdir ($verz)) 
			{
			  // "." und ".." bei der Ausgabe unterdrücken
			  if($file != "." && $file != "..") 
			  {
				if ($sekundenalt!="") {
					if ( $time[sec] - date(filemtime($verzeichnis.$file)) >= $sekundenalt )  
					{ 
						//echo " Datei $verzeichnis".$file;
						if (substr($file, -strlen($endung)) == $endung || $endung=="") {
						//	echo " loeschen";
							unlink($verzeichnis.$file); 
						} else {
							//echo " nicht loeschen, da endung ".substr($file, -strlen($endung))." <> ".$endung;						
						}
					} else {
						// echo " Datei ".$file." nicht älter als ".$sekundenalt."s gefunden , da aktuelle Zeit (".$time[sec].") - Dateizeit (". date(filemtime($verzeichnis.$file)).") = ".($time[sec] - date(filemtime($verzeichnis.$file)));
					}
					
				} else {
					// File löschen, wenn Endung vohanden
					if (substr($filename, strlen($endung)) == $endung || $endung=="") unlink($verzeichnis.$file);
				}
			  }
			}
			// Verzeichnis schließen
			closedir($verz); 
		}
	}  // end function  delFiles($verzeichnis,$endung,$sekundenalt) 

	// Gruppenberechtigungen setzten - Whitelist 
	function dmc_set_veyton_group_permissions($ID,$gruppenberechtigung,$Art,$permission) {
	
		global  $dateihandle;
		fwrite($dateihandle, "dmc_set_veyton_group_permissions \n");
		if ($Art=='category' ) $table='xt_categories_permission';
		else $table = 'xt_products_permission';
		
		$sql_data= array(
	              'pid' => $ID,						// ab 1
				  'permission' => $permission, 						// ab 1 
				  'pgroup' => $gruppenberechtigung);
		//  Überprüfen, ob Eintragung existent
		
		$query="SELECT pid as total from ".$table." WHERE pid='".$ID."' AND pgroup='".$gruppenberechtigung."'";
			fwrite($dateihandle, "query=$query \n");
		$temp_id_query = dmc_db_query($query);
		$TEMP_ID = dmc_db_fetch_array($temp_id_query);				 
		// Wenn noch kein Eintrag
		if ($TEMP_ID['total']=='' || $TEMP_ID['total']==null) {
				 // $sql_data['id'] = $ID;				
				fwrite($dateihandle, "insert \n");				 
				 xtc_db_perform($table, $sql_data);
		} else {
				fwrite($dateihandle, "update \n");
				xtc_db_perform($table, $sql_data, 'update', "pid ='$ID' AND pgroup='$gruppenberechtigung'");
		}
		
		return true;	
	} // end function dmc_set_veyton_group_permissions
	
	// API Verbindung zu Shopware herstellen
	function dmc_get_shopware_api_client($url,$user,$apikey) {
		global $dateihandle;
		if (substr($password,0,2)=='%%')
			$password = md5(base64_decode($password,2,40));
			
		$client = new ApiClient(
			//URL des Shopware Rest Servers
			$url,
			//Benutzername
			$user, 
			//API-Key des Benutzers
			$apikey
		);
		fwrite ($dateihandle , ".\n");	
		//	echo "...c client=".$client;
		return $client;
	}
	
	// dmc_php_mail_4_xtc basierend auf xtc_php_mail fuer XTC Systeme
	function dmc_php_mail_4_xtc($from_email_address, $from_email_name, $to_email_address, $to_name, $forwarding_to, $reply_address, $reply_address_name, $path_to_attachement, $path_to_more_attachements, $email_subject, $message_body_html, $message_body_plain) {
		global $dateihandle;

		$mail = new PHPMailer();
		$mail->PluginDir = DIR_FS_DOCUMENT_ROOT.'includes/classes/';

		if (isset ($_SESSION['language_charset'])) {
			$mail->CharSet = $_SESSION['language_charset'];
		} else {
			$lang_query = "SELECT * FROM ".TABLE_LANGUAGES." WHERE code = '".DEFAULT_LANGUAGE."'";
			$lang_query = dmc_db_query($lang_query);
			$lang_data = dmc_db_fetch_array($lang_query);
			$mail->CharSet = $lang_data['language_charset'];
		}
		if ($_SESSION['language'] == 'german') {
			$mail->SetLanguage("de", DIR_WS_CLASSES);
		} else {
			$mail->SetLanguage("en", DIR_WS_CLASSES);
		}
		if (EMAIL_TRANSPORT == 'smtp') {
			$mail->IsSMTP();
			$mail->SMTPKeepAlive = true; // set mailer to use SMTP
			$mail->SMTPAuth = SMTP_AUTH; // turn on SMTP authentication true/false
			$mail->Username = SMTP_USERNAME; // SMTP username
			$mail->Password = SMTP_PASSWORD; // SMTP password
			$mail->Host = SMTP_MAIN_SERVER.';'.SMTP_Backup_Server; // specify main and backup server "smtp1.example.com;smtp2.example.com"
		}

		if (EMAIL_TRANSPORT == 'sendmail') { // set mailer to use SMTP
			$mail->IsSendmail();
			$mail->Sendmail = SENDMAIL_PATH;
		}
		if (EMAIL_TRANSPORT == 'mail') {
			$mail->IsMail();
		}

		if (EMAIL_USE_HTML == 'true') // set email format to HTML
			{
			$mail->IsHTML(true);
			$mail->Body = $message_body_html;
			// remove html tags
			$message_body_plain = str_replace('<br />', " \n", $message_body_plain);
			$message_body_plain = strip_tags($message_body_plain);
			$mail->AltBody = $message_body_plain;
		} else {
			$mail->IsHTML(false);
			//remove html tags
			$message_body_plain = str_replace('<br />', " \n", $message_body_plain);
			$message_body_plain = strip_tags($message_body_plain);
			$mail->Body = $message_body_plain;
		}

		$mail->From = $from_email_address;
		$mail->Sender = $from_email_address;
		$mail->FromName = $from_email_name;
		$mail->AddAddress($to_email_address, $to_name);
		if ($forwarding_to != '')
			$mail->AddBCC($forwarding_to);
		$mail->AddReplyTo($reply_address, $reply_address_name);

		$mail->WordWrap = 50; // set word wrap to 50 characters
		//$mail->AddAttachment($path_to_attachement);                     // add attachments
		//$mail->AddAttachment($path_to_more_attachements);               // optional name                                          

		$mail->Subject = $email_subject;

		if (!$mail->Send()) {
			if (DEBUGGER>=1) fwrite($dateihandle, "error in dmc_finctions.php / dmc_php_mail Message was not sent ".$mail->ErrorInfo." \n");
		
		}
	}	// dmc_php_mail
	
	/**
	 *
	 * @eMail versenden
	 * Ergänzt am 07.01.2016 rcm
	 */
	function dmc_send_email($empfaenger,$von_email,$von_name,$betreff,$inhalt) {
		global $dateihandle;
		//  Status eMail senden
		if (DEBUGGER>=1) fwrite($dateihandle, "dmc_send_email($an,$betreff,$inhalt)\n");
		$from = "From: ".$von_name." <".$von_name.">\n";
		$from .= "Reply-To: ".$von_email."\n";
		$from .= "Content-Type: text/html\n";
		
		mail($empfaenger, $betreff, $inhalt, $from);
		
	} // end dmc_send_email
	
	/**
	 *
	 * @Umlaute konvertieren
	 * Ergänzt am 06.09.2017 rcm
	 */
	function  dmc_convert_umlaute($wert) {
		global $dateihandle;
		//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_generate_seo($wert)\n");
		$wert=str_replace('ö','oe',$wert);
		$wert=str_replace('ä','ae',$wert);
		$wert=str_replace('ü','ue',$wert);
		$wert=str_replace('Ö','Oe',$wert);
		$wert=str_replace('Ä','Ae',$wert);
		$wert=str_replace('Ü','Ue',$wert);
		$wert=str_replace('ß','ss',$wert);
		return $wert;
	} // end dmc_convert_umlaute
	
	/**
	 *
	 * @Suchmaschinen URL oder Kategorie_id erstellen
	 * Ergänzt am 06.09.2017 rcm
	 */
	function  dmc_generate_seo($wert) {
		global $dateihandle;
		//if (DEBUGGER>=1) fwrite($dateihandle, "dmc_generate_seo($wert)\n");
		$wert=str_replace('ö','oe',$wert);
		$wert=str_replace('ä','ae',$wert);
		$wert=str_replace('ü','ue',$wert);
		$wert=str_replace('Ö','Oe',$wert);
		$wert=str_replace('Ä','Ae',$wert);
		$wert=str_replace('Ü','Ue',$wert);
		$wert=str_replace('ß','ss',$wert);
		$wert = str_replace(' ', '-', $wert);
		$wert = preg_replace('/[^0-9a-zA-Z-_]/', '', $wert);
		$wert = strtolower($wert);
		return $wert;
	} // end dmc_generate_seo
	
	// dmc_compare_files - Pruefung, ob Dateien identisch sind, ruckegabe true oder false
	function dmc_compare_files($datei1,$datei2) {
		// echo "Vergleiche ".$datei1." mit ".$datei2."\n<br>";
		global $dateihandle;
	    if (DEBUGGER>=50) fwrite($dateihandle, "dmc_compare_files($datei1,$datei2) \n"); 
		if (file_exists($datei1) && file_exists($datei2) ){ 
			//if (DEBUGGER>=50) fwrite($dateihandle, "Groesse ".$datei1." = ".hash_file("sha512", $datei1)." und ".$datei2." = ".hash_file("sha512", $datei2)."\n"); 
		//	echo "Groesse ".$datei1." = ".hash_file("sha512", $datei1)." und ".$datei2." = ".hash_file("sha512", $datei2)."\n<br>";
			if (hash_file("sha512", $datei1) == hash_file("sha512", $datei2)) {
				return true;
			} else {
				return false;
			}
		} else {
		/*	if (!file_exists($datei1))
				echo "Datei 1 existiert nicht "."\n<br>";
			if (!file_exists($datei2))
				echo "Datei 2 existiert nicht "."\n<br>"; */
			return false;
		}
			
	}
	
?>