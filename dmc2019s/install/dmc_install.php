<head>
		<title>Installation dmConnector</title>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-15" />
		<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">
		<link type="text/css" rel="stylesheet" href="install/css/stylesheet.css" />
		<!--[if gte IE 9]>
		  <style type="text/css">
			#main.gradient, #main .gradient.button, #main .gradient.button.red, #main .gradient.button.green {
			   filter: none;
			}
		  </style>
		<![endif]-->
		<script type="text/javascript" src="../gm/javascript/jquery/jquery.js"></script>
	</head>
<?php

	defined( 'VALID_DMC' ) or die( 'Direct Access to this location is not allowed.' );
	
	include('./conf/definitions/de.inc.php');
	/*include('./functions/dmc_functions.php');
	include('./functions/dmc_db_functions.php');
*/

	// Zur Zeit nicht verwendet
	/*
	if( (strpos(strtolower(SHOPSYSTEM), 'veyton') === false) && (strpos(strtolower(SHOPSYSTEM), 'presta') === false) )
		$current_template=dmc_get_shop_config_value('CURRENT_TEMPLATE');
	else 
		$current_template="";
*/
	if (is_file(getcwd().'/../wp-config.php')) {
		define('SHOPSYSTEM' , 'woocommerce');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
					'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
				 'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
		// Kundengruppenpreise		
		$files_prices = array ( 'TABLE_PRICE1.dmc');
	} else if (is_file('../config.php')) { 
		define('SHOPSYSTEM' , 'shopware');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				 'SONDERZEICHEN.dmc','GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
			   'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc');
		// Kundengruppenpreise		
		$files_prices = array ( 'TABLE_PRICE1.dmc');
	} else if (is_file('../xtAdmin/login.php')) {
		define('SHOPSYSTEM' , 'veyton');
		define('SHOPSYSTEM_VERSION' , '4.2');
		define('SHOP_ID' , '1');
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
				  'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
		// Kundengruppenpreise		
			$files_prices = array ( 'TABLE_PRICE1.dmc', 'GROUP_PRICE1.dmc','TABLE_PRICE2.dmc', 'GROUP_PRICE2.dmc', 'TABLE_PRICE3.dmc', 'GROUP_PRICE3.dmc',
						'TABLE_PRICE4.dmc', 'GROUP_PRICE4.dmc','TABLE_PRICE5.dmc', 'GROUP_PRICE5.dmc', 'TABLE_PRICE6.dmc', 'GROUP_PRICE6.dmc',
						'TABLE_PRICE7.dmc', 'GROUP_PRICE7.dmc','TABLE_PRICE8.dmc', 'GROUP_PRICE8.dmc', 'TABLE_PRICE9.dmc', 'GROUP_PRICE9.dmc',
						'TABLE_PRICE10.dmc', 'GROUP_PRICE10.dmc','TABLE_PRICE11.dmc', 'GROUP_PRICE11.dmc','TABLE_PRICE12.dmc', 'GROUP_PRICE12.dmc',
						'TABLE_PRICE13.dmc', 'GROUP_PRICE13.dmc','TABLE_PRICE14.dmc', 'GROUP_PRICE14.dmc','TABLE_PRICE15.dmc', 'GROUP_PRICE15.dmc');
	} else if (is_file('../config/settings.inc.php')) {
		define('SHOPSYSTEM' , 'presta');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
			  'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
		// Kundengruppenpreise		
		$files_prices = array ( 'TABLE_PRICE1.dmc');
	} else if (is_file('../core/config/paths.php')) {
		define('SHOPSYSTEM' , 'hhg');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
			   'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
		// Kundengruppenpreise		
			$files_prices = array ( 'TABLE_PRICE1.dmc', 'GROUP_PRICE1.dmc','TABLE_PRICE2.dmc', 'GROUP_PRICE2.dmc', 'TABLE_PRICE3.dmc', 'GROUP_PRICE3.dmc',
						'TABLE_PRICE4.dmc', 'GROUP_PRICE4.dmc','TABLE_PRICE5.dmc', 'GROUP_PRICE5.dmc', 'TABLE_PRICE6.dmc', 'GROUP_PRICE6.dmc',
						'TABLE_PRICE7.dmc', 'GROUP_PRICE7.dmc','TABLE_PRICE8.dmc', 'GROUP_PRICE8.dmc', 'TABLE_PRICE9.dmc', 'GROUP_PRICE9.dmc',
						'TABLE_PRICE10.dmc', 'GROUP_PRICE10.dmc','TABLE_PRICE11.dmc', 'GROUP_PRICE11.dmc','TABLE_PRICE12.dmc', 'GROUP_PRICE12.dmc',
						'TABLE_PRICE13.dmc', 'GROUP_PRICE13.dmc','TABLE_PRICE14.dmc', 'GROUP_PRICE14.dmc','TABLE_PRICE15.dmc', 'GROUP_PRICE15.dmc');
	} else if (is_file('../configuration.php')) {
		define('SHOPSYSTEM' , 'virtuemart');
		define('SHOPSYSTEM_VERSION' , '');
		define('SHOP_ID' , '1');
		
		$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
			    'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
		// Kundengruppenpreise		
		$files_prices = array ( 'TABLE_PRICE1.dmc');
	} else {
		define('SHOPSYSTEM' , 'gambiogx');			// osc, hhg, myoos, gambio, zencart etc 
		define('SHOPSYSTEM_VERSION' , 'gx2');
		define('SHOP_ID' , '1');
			$files_std = array (//'SHOPSYSTEM.dmc', 'WAWI.dmc', 'DMC_FOLDER.dmc', 'CHARSET.dmc',
				'PRODUCT_TEMPLATE.dmc', 'OPTIONS_TEMPLATE.dmc', 'GENERATE_CAT_ID.dmc','CAT_DEVIDER.dmc', 'KATEGORIE_TRENNER.dmc', 'STANDARD_CAT_ID.dmc',
				'UPDATE_ORDER_STATUS.dmc', 'ORDER_STATUS_GET.dmc', 'ORDER_STATUS_SET.dmc',				// Bestellstatus
				'UPDATE_ORDER_STATUS_ERP.dmc', 'NEW_ORDER_STATUS_ERP.dmc','NEW_ORDER_STATUS_FAILED.dmc','NOTIFY_CUSTOMER_ERP.dmc',
				'GM_OPTIONS_TEMPLATE.dmc', 'LISTING_TEMPLATE.dmc','PRODUCTS_SORTING.dmc','PRODUCTS_SORTING2.dmc',
				'CATEGORIES_TEMPLATE.dmc', 'GM_SITEMAP_ENTRY.dmc','GM_SHOW_weight.dmc','GM_SHOW_QTY_INFO.dmc',
				'PRODUCTS_EXTRA_PIC_EXTENSION.dmc', 'GROUP_PERMISSION_0.dmc','GROUP_PERMISSION_1.dmc','GROUP_PERMISSION_2.dmc',
				'GROUP_PERMISSION_3.dmc', 'GROUP_PERMISSION_4.dmc','GROUP_PERMISSION_5.dmc','GROUP_PERMISSION_6.dmc',
				'GROUP_PERMISSION_7.dmc', 'GROUP_PERMISSION_8.dmc','GROUP_PERMISSION_9.dmc','GROUP_PERMISSION_10.dmc',
				'GROUP_PERMISSION_11.dmc','GROUP_PERMISSION_12.dmc','GROUP_PERMISSION_13.dmc','GROUP_PERMISSION_14.dmc','GROUP_PERMISSION_15.dmc',
				'FSK18.dmc', 'UPDATE_DESC.dmc','UPDATE_PROD_TO_CAT.dmc','UPDATE_CATEGORY.dmc','UPDATE_CATEGORY_DESC.dmc',
				'DELETE_INACTIVE_PRODUCT.dmc','DB_TABLE_PREFIX.dmc','SONDERZEICHEN.dmc');
			// Kundengruppenpreise		
			$files_prices = array ( 'TABLE_PRICE1.dmc', 'GROUP_PRICE1.dmc','TABLE_PRICE2.dmc', 'GROUP_PRICE2.dmc', 'TABLE_PRICE3.dmc', 'GROUP_PRICE3.dmc',
						'TABLE_PRICE4.dmc', 'GROUP_PRICE4.dmc','TABLE_PRICE5.dmc', 'GROUP_PRICE5.dmc', 'TABLE_PRICE6.dmc', 'GROUP_PRICE6.dmc',
						'TABLE_PRICE7.dmc', 'GROUP_PRICE7.dmc','TABLE_PRICE8.dmc', 'GROUP_PRICE8.dmc', 'TABLE_PRICE9.dmc', 'GROUP_PRICE9.dmc',
						'TABLE_PRICE10.dmc', 'GROUP_PRICE10.dmc','TABLE_PRICE11.dmc', 'GROUP_PRICE11.dmc','TABLE_PRICE12.dmc', 'GROUP_PRICE12.dmc',
						'TABLE_PRICE13.dmc', 'GROUP_PRICE13.dmc','TABLE_PRICE14.dmc', 'GROUP_PRICE14.dmc','TABLE_PRICE15.dmc', 'GROUP_PRICE15.dmc');
			
	}
	
	// Log und Debug		
	$files_debug = array ( 'DEBUGGER.dmc', 'LOG_DATEI.dmc','IMAGE_LOG_FILE.dmc', 'PRINT_POST.dmc', 'LOG_ROTATION.dmc', 'LOG_ROTATION_VALUE.dmc');
	

	// Spezielle Statusoperationen
	$files_status = array ( 'STATUS_WRITE_ART_BEGIN_DETELE_ART.dmc', 'STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART.dmc', 'STATUS_WRITE_ART_BEGIN_DETELE_ART_VARIANTS.dmc', 'STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART_VARIANTS.dmc'); //, 'STATUS_WRITE_ART_DETAILS_BEGIN.dmc','STATUS_WRITE_ART_END.dmc', 'STATUS_WRITE_ART_DETAILS_END.dmc');
	if ((isset($_POST['user']))and(isset($_POST['password']))) {
	//	if (checkLogin()) 
		$ok=true;
			// showDefinitions ();
	//	else 
	//		getLogin('failed');
	} else {
		// User oder Password nicht angegeben
	//	getLogin('new');
	}
	
function checkLoginWWW ()
{
	$ok=false; 
	// checkIfRegistered
	// Definition einlesen
		$dateihandle = fopen("./conf/definitions/DMC_U.dmc","r");
		$dUser = fread($dateihandle, 100);
		fclose($dateihandle);
		$dateihandle = fopen("./conf/definitions/DMC_P.dmc","r");
		$dPw = fread($dateihandle, 100);
		fclose($dateihandle);
		// Compare 
		if ($_POST['user']==$dUser && md5($_POST['password'])==$dPw)
			$ok=true;
	return $ok;
} // end function check 

function getLogin ($rcm)
{
	global $DMC_TEXT;
	$printHtml = '<html><head><link rel=stylesheet type="text/css" href="style.css"></head><body><h3>dmConnector Login</h3><br>';
	
	if ($rcm=='failed') echo'<font color=red>'.$DMC_TEXT['LOGIN_FAILED'].'</font><br>';
	$printHtml .='<table>';
		$printHtml .= 					'<form name="login" action="'.$_SERVER['PHP_SELF'].'" method="post">'.
										//'<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="'.$Key.'">'.
										//'<input type="hidden" name="action" value="dmc_install">'.
										'<tr>'.
											'<td>User:</td>'.
											'<td><input name="user" type="text" id="user" size="20" value="" /></td>'.
										'</tr>'.	
										'<tr>'.
											'<td>Password:</td>'.
											'<td><input name="password" type="password" id="password" size="20" value="" /></td>'.
										'</tr>'.	
										'<tr>'.
											'<td><input name="action" type="hidden" value="dmc_install" />&nbsp;</td>'.
											'<td><input type="submit" name="submit" value="login"></td>'.
										'</tr>';
	$printHtml .='</table></body></html>';
	echo $printHtml; 
} // end function getLogin 

function writeDefinitions ()
{
	// checkIfDefinition
	 foreach ($_POST as $Key => $Value)
	  {
		// Wenn Definition - nicht user etc, dann datei vorhanden und wert in datei aendern
		$filename = './conf/definitions/'.$Key.".dmc";
		if ($Key=='DMC_P') $Value=md5($Value);
		if (file_exists($filename)) {
			$dateihandle = fopen($filename,"w");
			fwrite($dateihandle,trim($Value));
			fclose($dateihandle);
		} 
	 }
}

//--------- Werte aus Definitionsdatei ausgeben ------------------------------

function showDefinitions ($user,$password)
{
	global $DMC_TEXT, $files_std, $files_debug, $files_prices, $files_status, $user, $password, $PHP_SELF;
	$Url = $PHP_SELF . "?action=dmc_install&user=" . $user . "&password=" . base64_encode($password);
	$seite=$_GET['site'];
	// Ueberpruefe uebergebene Werte und trage in Definitionsdateien ein.
	writeDefinitions();
		
	// Definitionen einlesen
	if ($seite=='5') {
		// Diverse Werte vom Shop ausgeben
		// echo "todo: Diverse Werte vom Shop ausgeben";
	} else if ($seite=='4') {
		for ($i = 0; $i < count($files_status); $i++) 
			$files[$i]=$files_status[$i];
	} else if ($seite=='3') {
		for ($i = 0; $i < count($files_debug); $i++) 
			$files[$i]=$files_debug[$i];
	} else if ($seite=='2') {
		for ($i = 0; $i < count($files_prices); $i++) 
			$files[$i]=$files_prices[$i];
	} else {
		for ($i = 0; $i < count($files_std); $i++) 
			$files[$i]=$files_std[$i];
    
	}
	
	for ( $i = 0; $i < count ( $files  ); $i++ ) {
		$defName = substr($files[$i],0,-4);
		$dateihandle = fopen("./conf/definitions/".$files[$i],"r");
		$defValue = fread($dateihandle, 100);
		// echo"$defName=$defValue<br>";
		$definition[$defName] = trim($defValue);
		fclose($dateihandle);
	} // end for

	
	$printHtml = '<html><head><style type="text/css">
form { background-image:url(background.gif); padding:20px; border:6px solid #ddd; font-size:8px; font-family:arial;}
td, input, select, textarea { font-size:13px; font-family:Verdana,sans-serif; font-weight:bold; }
input, select, textarea { color:#00c; }
.fehler { background-color:red; }

.Bereich, .Feld { background-color:#ffa; width:300px; border:6px solid #ddd; }
.Auswahl { background-color:#dff; width:300px; border:6px solid #ddd; }
.Check, .Radio { background-color:#ddff; border:1px solid #ddd; }
.Button { background-color:#aaa; color:#fff; width:200px; border:6px solid #ddd; }
</style>
</head><body><h3>'.$DMC_TEXT['CONFIGURE_HEADER'].'</h3><br>';
	$printHtml .='<div id="choose" style="width:25%;float:left"><a href="'.$Url.'&site=1">Grundeinstellungen</a>  </div>
	<div id="choose" style="width:25%;float:left"><a href="'.$Url.'&site=2">Preise</a> 	</div>
	<div id="choose" style="width:25%;float:left"><a href="'.$Url.'&site=3">Debug</a> </div>
	<div id="choose" style="width:25%;float:left"><a href="'.$Url.'&site=4">Sondereinstellungen</a> </div>
	<div id="choose" style="width:25%;float:left"><a href="'.$Url.'&site=5">Shopinformationen</a> </div>';
	$printHtml .='<br />';

	$printHtml .='<table>';
	$printHtml .= '<tr><th>'.$DMC_TEXT['TYPE'].'</th><th>'.$DMC_TEXT['NEW_VALUE'].'</th><th>'.$DMC_TEXT['CHANGE'].'</th><th>&nbsp;</th></tr>';
	if ($seite=='5') {
		// Diverse Werte vom Shop ausgeben
		$printHtml .= "<hr>".
						"<h2>Diverse Informationen vom Shop</h2>".
						"<ul>".
						"<li>Erkanntes SHOPSYSTEM:".SHOPSYSTEM."</li>".
						"<li>Erkannte Version:".SHOPSYSTEM_VERSION."</li>";
		
		// Kundengruppen
		if (SHOPSYSTEM=='shopware') {
			$sql = "SELECT id	, groupkey, description FROM `s_core_customergroups` ";
			$query_result = dmc_db_query($sql);
			$printHtml .= 	"<li>Vorhandene Kundengruppen</li>".
							"<ul>".
							"<li>id;groupkey;description</li>";
			
			while ($datensatz = dmc_db_fetch_array($query_result))
			{
				 $printHtml .= "<li>".$datensatz['id'].";".$datensatz['groupkey'].";".$datensatz['description']."</li>";
			}
			$printHtml .= "</ul>";
		}
		
		// Bestellstatus
		if (SHOPSYSTEM=='shopware') {
			$sql = "SELECT id	, name, description FROM `s_core_states` ";
			$query_result = dmc_db_query($sql);
			$printHtml .= 	"<li>Vorhandene Bestellstatus</li>".
							"<ul>".
							"<li>id;groupkey;description</li>";
			
			while ($datensatz = dmc_db_fetch_array($query_result))
			{
				 $printHtml .= "<li>".$datensatz['id'].";".$datensatz['name'].";".$datensatz['description']."</li>";
			}
			$printHtml .= "</ul>";
		}
		
		// Letzen 5 Bestellungen
		if (SHOPSYSTEM=='shopware') {
			$sql = "SELECT o.ordernumber AS nummer, o.ordertime AS datum, o.status AS status,  oa.lastname AS name,  oa.firstname AS vorname, oa.street AS strasse, oa.city AS ort ".
					"FROM `s_order` o INNER JOIN s_order_billingaddress oa ON o.id=oa.orderID  ".
					"ORDER BY o.ordernumber desc ".
					"LIMIT 5";
					 $printHtml .= $sql;
			$query_result = dmc_db_query($sql);
			$printHtml .= 	"<li>Letzte 5 Bestellungen (mit Zeichensatzkonvertierung <b>".SONDERZEICHEN."</b>)</li>".
							"<ul>".
							"<li>Nummer;Datum;Status;Name;Vorname;Strasse;Ort</li>";
			
			while ($datensatz = dmc_db_fetch_array($query_result))
			{
				 $printHtml .= "<li>(PLAIN) ".$datensatz['nummer'].";".($datensatz['datum']).";".($datensatz['status']).";".($datensatz['name']).";".($datensatz['vorname']).";".($datensatz['strasse']).";".($datensatz['ort'])."</li>";
				 $printHtml .= utf8_decode("<li>(utf8_decode) ".$datensatz['nummer'].";".($datensatz['datum']).";".($datensatz['status']).";".($datensatz['name']).";".($datensatz['vorname']).";".($datensatz['strasse']).";".($datensatz['ort'])."</li>");
				 $printHtml .= utf8_encode("<li>(utf8_encode) ".$datensatz['nummer'].";".($datensatz['datum']).";".($datensatz['status']).";".($datensatz['name']).";".($datensatz['vorname']).";".($datensatz['strasse']).";".($datensatz['ort'])."</li>");
			}
			$printHtml .= "</ul>";
		}
		 $printHtml .= "ENDE";
		
		$printHtml .= "</ul>".
						"<hr>";
	
	} // END SEITE 5 
	else 
	foreach ($definition as $Key => $Value)
	  {
	
						$printHtml .= 	'<tr>'.
										'<form name="'.$Key.'" action="'.$_SERVER['PHP_SELF'].'" method="post">'.
										//'<form action="'.$_SERVER['PHP_SELF'].'" method="post" id="'.$Key.'">'.
										'<input type="hidden" name="action" value="dmc_install">'.
										'<input type="hidden" name="user" value="'.$user.'">'.
										'<input type="hidden" name="password" value="rcm'. base64_encode($password).'">'.
										'<input type="hidden" name="site" value="'.$seite.'">'.
										'<td>'.$DMC_TEXT[$Key].'</td>';
									//		'<td>'.$DMC_TEXT[$Key].'(Key='.$Key.') </td>';
										
										
						if ($Value=="true")
							$printHtml .= 	'<td><select name="'.$Key.'" ><option value="true" selected>Ja</option><option value="false" >Nein</option></select></td>';
						else if ($Value=="false")
							$printHtml .= 	'<td><select name="'.$Key.'" ><option value="true">Ja</option><option value="false" selected>Nein</option></select></td>';
						else if (substr($Value,0,4)=="http")
							$printHtml .= 	'<td><input name="'.$Key.'" type="text" id="'.$Key.'" size="'.(strlen($Value)+5).'" value="'.$Value.'" /><a href="'.$Value.'" alt="'.$DMC_TEXT['CHECK'].'" target="_blank"> '.$DMC_TEXT['CHECK'].' </a></td>';
						else if ($Key == "SONDERZEICHEN") {
							$printHtml .= 	'<td><select name="'.$Key.'" >';
							if ($Value=='utf8_encode') $printHtml .='<option value="utf8_encode" selected>utf8_encode</option>'; else $printHtml .='<option value="utf8_encode" >utf8_encode</option>';
							if ($Value=='utf8_decode') $printHtml .='<option value="utf8_decode" selected>utf8_decode</option>'; else $printHtml .='<option value="utf8_decode" >utf8_decode</option>';
							if ($Value=='') $printHtml .='<option value="" selected>Keine</option>'; else $printHtml .='<option value="" >Keine</option>';
							$printHtml .= 	'</select></td>';
						} else if ($Key == "WAWI") {
							$printHtml .= 	'<td><select name="'.$Key.'" >';
							if ($Value=='easywinart') $printHtml .='<option value="easywinart" selected>EasyWinArt</option>'; else $printHtml .='<option value="easywinart" >EasyWinArt</option>';
							if ($Value=='pck') $printHtml .='<option value="pck" selected>PC-Kaufmann</option>'; else $printHtml .='<option value="pck" >PC-Kaufmann</option>';
							if ($Value=='selectline') $printHtml .='<option value="selectline" selected>SelectLine</option>'; else $printHtml .='<option value="selectline" >SelectLine</option>';
							if ($Value=='') $printHtml .='<option value="" selected>anderes</option>'; else $printHtml .='<option value="" >anderes</option>';
							$printHtml .= 	'</select></td>';
							$wawi=$Value;	// fuer weitere Verwendung
						} else if ($Key == "SHOPSYSTEM") {
							$printHtml .= 	'<td><select name="'.$Key.'" >';
							if ($Value=='gambio') $printHtml .='<option value="gambio" selected>Gambio 2007</option>'; else $printHtml .='<option value="gambio" >Gambio</option>';
							if ($Value=='gambiogx') $printHtml .='<option value="gambiogx2" selected>Gambio GX</option>'; else $printHtml .='<option value="gambiogx" >Gambio GX</option>';
							if ($Value=='gambiogx2') $printHtml .='<option value="gambiogx2" selected>Gambio GX 2</option>'; else $printHtml .='<option value="gambiogx2" >Gambio GX 2</option>';
							if ($Value=='hhg') $printHtml .='<option value="hhg" selected>HHG Multistore</option>'; else $printHtml .='<option value="hhg" >HHG Multistore</option>';
							if ($Value=='xtcmodified') $printHtml .='<option value="xtcmodified" selected>modified</option>'; else $printHtml .='<option value="xtcmodified" >modified</option>';
							if ($Value=='myoos') $printHtml .='<option value="presta" selected>myOOS</option>'; else $printHtml .='<option value="myoos" >myOOS</option>';
							if ($Value=='presta') $printHtml .='<option value="presta" selected>PrestaShop</option>'; else $printHtml .='<option value="presta" >PrestaShop</option>';
							if ($Value=='veyton') $printHtml .='<option value="veyton" selected>Veyton</option>'; else $printHtml .='<option value="veyton" >Veyton</option>';
							if ($Value=='virtuemart') $printHtml .='<option value="virtuemart" selected>Virtuemart</option>'; else $printHtml .='<option value="virtuemart" >Virtuemart</option>';
							if ($Value=='xtc') $printHtml .='<option value="xtc" selected>xt:Commerce</option>'; else $printHtml .='<option value="xtc" >xt:Commerce</option>';
							if ($Value=='xtcmodified') $printHtml .='<option value="xtcmodified" selected>xtc:modified</option>'; else $printHtml .='<option value="xtcmodified" >xtc:modified</option>';
							if ($Value=='zencart') $printHtml .='<option value="zencart" selected>Zen Cart</option>'; else $printHtml .='<option value="zencart" >Zen Cart</option>';
							$printHtml .= 	'</select></td>';
							$shop=$Value;	// fuer weitere Verwendung
						} else if ($Key =="DMC_FOLDER") {
							// pruefen ob verzeichnis existent
							if (!is_dir($Value)) 
								$printHtml .= 	'<td class="fehler"><input name="'.$Key.'" type="text" id="'.$Key.'" size="'.(strlen($Value)+5).'" value="'.$Value.'" /></td>';
							else
								$printHtml .= 	'<td><input name="'.$Key.'" type="text" id="'.$Key.'" size="'.(strlen($Value)+5).'" value="'.$Value.'" /></td>';
						
						}	
						else if ($Key =="DMC_P")
							$printHtml .= 	'<td><input name="'.$Key.'" type="password" id="'.$Key.'" size="20" value="'.$Value.'" /></td>';
						else if (strlen($Value)<=20)
							$printHtml .= 	'<td><input name="'.$Key.'" type="text" id="'.$Key.'" size="20" value="'.$Value.'" /></td>';
						else 				
							$printHtml .= 	'<td><input name="'.$Key.'" type="text" id="'.$Key.'" size="'.(strlen($Value)+5).'" value="'.$Value.'" /></td>';
						
						// Button
						$printHtml .= 	'<td><input type="submit" name="submit" value="'.$DMC_TEXT['CHANGE'].'"></td>'.
										'</form>'.
										'<td>&nbsp;&nbsp;&nbsp;<a href="javascript: void(0)" onclick="window.open(\'install/desc.php?option='.$Key.'\', \'desc\', \'width=500, height=350\'); return false;"><b>?</b></a> </td>'.
										'</tr>';
										
				
										
	} // end foreach
		// Passwortgeneratur
	/*$printHtml .= '<tr><td>&nbsp;</td><td><a href="javascript: void(0)" onclick="window.open(\'getpwd.php\', \'desc\', \'width=400, height=300\'); return false;"><b>'.$DMC_TEXT['GETPWD'].'</b></a></td><td>&nbsp;</td><td>&nbsp;</td></tr>';
	$printHtml .='</table></body></html>'; */
	echo $printHtml; 
	/* select  configuration_value from configuration where configuration_key='CURRENT_TEMPLATE'
<br><b>M&ouml;gliche Parameter :</b><br><br>
<a href="<? echo $Url; ?>&action=version">Ausgabe XML Scriptversion</a><br>
<br>
<a href="<? echo $Url; ?>&action=manufacturers_export">Ausgabe XML Manufacturers</a><br>
<a href="<? echo $Url; ?>&action=categories_export">Ausgabe XML Categories</a><br>
<a href="<? echo $Url; ?>&action=products_export">Ausgabe XML Products</a><br>
<a href="<? echo $Url; ?>&action=customers_export">Ausgabe XML Customers</a><br>
<a href="<? echo $Url; ?>&action=customers_newsletter_export">Ausgabe XML Customers-Newsletter</a><br>
<br>
<a href="<? echo $Url; ?>&action=orders_export">Ausgabe XML Orders</a><br>
<br>
<a href="<? echo $Url; ?>&action=config_export">Ausgabe XML Shop-Config</a><br>
<br>
<a href="<? echo $Url; ?>&action=update_tables"> -Tabellen aktualisieren</a><br>
</body>
</html>'
<?*/

}	// end function showDefinitions

	
?>