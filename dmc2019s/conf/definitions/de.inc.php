<?php
// dmConnector Sprachdatei Deutsch
$DMC_TEXT['CONFIGURE_HEADER']='dmConnector - Konfiguration';
$DMC_TEXT['TYPE']='Art';
$DMC_TEXT['VALUE']='Wert';
$DMC_TEXT['NEW_VALUE']='Neuer Wert';
$DMC_TEXT['CHANGE']='&Auml;ndern';

// Definitionen 
$DMC_TEXT['SHOPSYSTEM']='Shopsystem';
$DMC_TEXT['SHOPSYSTEM_DESC']='xtCommerce, Gambio, GambioGX, ZenCart, HHG, Veyton, Presta, commerceSEO';
$DMC_TEXT['SHOPSYSTEM_VALUES']='xtCommerce;gambio;gambiogx;zencart;hhg;veyton;presta;commerceSEO';
$DMC_TEXT['SHOPSYSTEM_VERSION']='Shopsystem Version';
$DMC_TEXT['SHOPSYSTEM_VERSION_DESC']='z.B. gx2';
$DMC_TEXT['SHOPSYSTEM_VERSION_VALUES']=';Veyton;commerceSEO;gx;gx2';

$DMC_TEXT['WAWI']='Warenwirtschaftssystem'; // valid: PCK, GSA, NAV, SELECTLINE, SOL
$DMC_TEXT['WAWI_DESC']='z.B. PCK, GSA, NAV, SELECTLINE, SOL'; // valid: PCK, GSA, NAV, SELECTLINE, SOL
$DMC_TEXT['WAWI_VALUES']='sonstige;PCK;GSA;NAV;SELECTLINE;SOL'; // valid: PCK, GSA, NAV, SELECTLINE, SOL
$DMC_TEXT['DMC_FOLDER']='dmconnector Verzeichnis';
$DMC_TEXT['DMC_FOLDER_DESC']=getenv("DOCUMENT_ROOT")."/gambiogx2/dmc2012/";
$DMC_TEXT['CHARSET']='Zeichensatz';
$DMC_TEXT['CHARSET_DESC']='Standard: UTF-8/iso-8859-1';
$DMC_TEXT['PRODUCT_TEMPLATE']='PRODUCT_TEMPLATE';
$DMC_TEXT['PRODUCT_TEMPLATE_DESC']='standard=standard.html';
$DMC_TEXT['OPTIONS_TEMPLATE']='OPTIONS_TEMPLATE';
$DMC_TEXT['OPTIONS_TEMPLATE_DESC']='standard=product_options_selection';
$DMC_TEXT['GENERATE_CAT_ID']='KategorieID generieren';
$DMC_TEXT['GENERATE_CAT_ID_DESC']='Kategorie_ID basierend auf WaWi ID ermitteln true/false';
$DMC_TEXT['CAT_DEVIDER']='Kategorietrenner Multi';
$DMC_TEXT['CAT_DEVIDER_DESC']='Trenner für Multi - Kategorie_ID Zuordnung';
$DMC_TEXT['KATEGORIE_TRENNER']='Kategorietrenner Sonderkat';
$DMC_TEXT['KATEGORIE_TRENNER_DESC']='Trenner für Sonderkategorie: z.B. Angebot\Notebooks\Acer';

$DMC_TEXT['UPDATE_ORDER_STATUS_ERP']='Bestellstatus &auml;ndern durch ERP (true/false)';
$DMC_TEXT['UPDATE_ORDER_STATUS_ERP_DESC']='W&auml;hlen Sie hier aus, ob der Bestellstatus nach Abruf der Bestellungen AUS DER ERP HERAUS ge&auml;ndert werden soll, damit Bestellungen nicht doppelt abgerufen werden k&ouml;nnen.';
$DMC_TEXT['NEW_ORDER_STATUS_ERP']='Bestellstatus erfolgreich ERP';
$DMC_TEXT['NEW_ORDER_STATUS_ERP_DESC']='Hier geben Sie den Bestellstatus f&uuml;r KORREKT durch die Schnittstelle abgerufene Bestellungen IN DIE ERP ein.';
$DMC_TEXT['NEW_ORDER_STATUS_FAILED']='Bestellstatus FEHLER ERP';
$DMC_TEXT['NEW_ORDER_STATUS_FAILED_DESC']='Hier geben Sie den Bestellstatus f&uuml;r NICHT oder FEHLERHAFT durch die Schnittstelle abgerufene Bestellungen IN DIE ERP ein.';
$DMC_TEXT['NOTIFY_CUSTOMER_ERP']='Statusänderung durch ERP an Kunden?';
$DMC_TEXT['NOTIFY_CUSTOMER_ERP_DESC']='Kunde über geänderten Order Status nach Bestellabruf IN ERP informieren ? true/false';
$DMC_TEXT['GM_OPTIONS_TEMPLATE']='GambioGX GM_OPTIONS_TEMPLATE';
$DMC_TEXT['GM_OPTIONS_TEMPLATE_DESC']='Standard=product_options_selection.html';
$DMC_TEXT['GM_SITEMAP_ENTRY']='GambioGX Sitemap Änderungsfrequenz';
$DMC_TEXT['GM_SITEMAP_ENTRY_DESC']='Änderungsfrequenz in der Sitemap als Standard auf immer = 0';
$DMC_TEXT['GM_SHOW_weight']='GambioGX Gewicht anzeigen';
$DMC_TEXT['GM_SHOW_weight_DESC']='Gewicht anzeigen als Standard auf ja = 1';
$DMC_TEXT['GM_SHOW_QTY_INFO']='GambioGX Lagerbestand anzeigen';
$DMC_TEXT['GM_SHOW_QTY_INFO_DESC']='Lagerbestand anzeigen als Standard auf ja = 1';
$DMC_TEXT['CATEGORIES_TEMPLATE']='Kat. CATEGORIES_TEMPLATE';
$DMC_TEXT['CATEGORIES_TEMPLATE_DESC']='Std: categorie_listing.html';
$DMC_TEXT['LISTING_TEMPLATE']='Kat. LISTING_TEMPLATE';
$DMC_TEXT['LISTING_TEMPLATE_DESC']='Std: product_listing_v1.html';
$DMC_TEXT['PRODUCTS_SORTING']='Kat. PRODUCTS_SORTING';
$DMC_TEXT['PRODUCTS_SORTING_DESC']='Std: categorie_listing.html';
$DMC_TEXT['PRODUCTS_SORTING2']='Kat. SORTING Reihenfolge';
$DMC_TEXT['PRODUCTS_SORTING2_DESC']='Std: asc/desc';
$DMC_TEXT['PRODUCTS_EXTRA_PIC_EXTENSION']='Trenner für Bildextension';
$DMC_TEXT['PRODUCTS_EXTRA_PIC_EXTENSION_DESC']='Std: _ , oder z.B: -ALT für artikelnummer-alt2, artikelnummer-alt3 etc';
$DMC_TEXT['GROUP_PERMISSION_0']='GROUP_PERMISSION_0 0/1';
$DMC_TEXT['GROUP_PERMISSION_0_DESC']='Standard-Berechtigung Artikel für Kundengruppe 0 - 0/1';
for ($i=1;$i<=10;$i++) {
	$DMC_TEXT['GROUP_PERMISSION_'.$i]='GROUP_PERMISSION_'.$i.' 0/1';
	$DMC_TEXT['GROUP_PERMISSION_'.$i.'_DESC']='Standard-Berechtigung Artikel für Kundengruppe '.$i.'.<br>Achtung: Kundengruppe muss vorhanden sein.<br>Werte 0 oder 1.';
	$DMC_TEXT['GROUP_PERMISSION_'.$i.'_VALUES']='0;1';
}
$DMC_TEXT['FSK18']='FSK18';
$DMC_TEXT['FSK18_DESC']='Sichtbar nur für FSK18. Std: false';
$DMC_TEXT['FSK18_VALUES']='true;false';

$DMC_TEXT['UPDATE_DESC']='Artikel Beschreibungen aktualisieren?';
$DMC_TEXT['UPDATE_DESC_DESC']='Bei bestehenden Artikeln den Langtext etc überschreiben. true/false';
$DMC_TEXT['UPDATE_DESC_VALUES']='true;false';
$DMC_TEXT['UPDATE_PROD_TO_CAT']='Artikel-Kategoriezuordung aktualisieren?';
$DMC_TEXT['UPDATE_PROD_TO_CAT_DESC']='Bei bestehenden Artikeln die Kategoriezuordung überschreiben. true/false';
$DMC_TEXT['UPDATE_PROD_TO_CAT_VALUES']='true;false';
$DMC_TEXT['UPDATE_CATEGORY']='Kategorien aktualisieren?';
$DMC_TEXT['UPDATE_CATEGORY_DESC']='Bestehenden Kategorien aktualisieren, wie die Struktur, Sortierung etc. true/false';
$DMC_TEXT['UPDATE_CATEGORY_VALUES']='true;false';
$DMC_TEXT['UPDATE_CATEGORY_DESC']='Artikel Langtext aktualisieren?';
$DMC_TEXT['UPDATE_CATEGORY_DESC_DESC']='Bei bestehenden Kategorien die Beschreibung überschreiben. true/false';
$DMC_TEXT['UPDATE_CATEGORY_DESC_VALUES']='true;false';
$DMC_TEXT['DELETE_INACTIVE_PRODUCT']='Inaktive Artikel löschen?';
$DMC_TEXT['DELETE_INACTIVE_PRODUCT_DESC']='Nicht mehr aktive Artikel aus dem Shop löschen statt nur deaktiv zu setzen. true/false';
$DMC_TEXT['DELETE_INACTIVE_PRODUCT_VALUES']='true;false';

$DMC_TEXT['SONDERZEICHEN']='Export Zeichensatz ändern?';
$DMC_TEXT['SONDERZEICHEN_DESC']='UTF8 und Shop ANSI = utf8decode; ANSI und Shop UTF8 = utf8encode; nein = keine';
$DMC_TEXT['SONDERZEICHEN_VALUES']='utf8decode;utf8encode;nein';

$DMC_TEXT['STANDARD_CAT_ID']='Standard Kategorie ID';
$DMC_TEXT['STANDARD_CAT_ID_DESC']='Ggfls als Abfang ID definierbar.';



for ($i=1;$i<=10;$i++) {
	$DMC_TEXT['TABLE_PRICE'.$i]='Preis-Tabelle Kd_Grp '.$i.'';
	$DMC_TEXT['TABLE_PRICE'.$i.'_DESC']='Bezeichnung der Shop DB-Preis-Tabelle für Kundengruppe '.$i.'.<br>Std xtC etc: personal_offers_by_customers_status_'.$i.', Veyton: xt_products_price_group_'.$i;
	$DMC_TEXT['TABLE_PRICE'.$i.'_VALUES']='';
	$DMC_TEXT['GROUP_PRICE1'.$i]='Preis Nummer für Tabelle Kd_Grp '.$i.'';
	$DMC_TEXT['GROUP_PRICE1'.$i.'_DESC']='Übergebener Preis aus WaWi (Artikel_Preis_...) für Shop DB-Preis-Tabelle für Kundengruppe '.$i;
	$DMC_TEXT['GROUP_PRICE1'.$i.'_VALUES']='0;1;2;3;4;';
}
$DMC_TEXT['DEBUGGER']='Debug Modus 0;1;50;99';
$DMC_TEXT['DEBUGGER_DESC']='Debug Modus: 0-aus, 1-standard, 50-detailliert, 99-incl Datenbank';
$DMC_TEXT['DEBUGGER_VALUES']='0;1;50;99';
$DMC_TEXT['LOG_DATEI']='Log-Dateiname';
$DMC_TEXT['LOG_DATEI_DESC']='Dateiname der Log-Datei.';
$DMC_TEXT['LOG_DATEI_VALUES']='';
$DMC_TEXT['IMAGE_LOG_FILE']='Image-Log-Dateiname';
$DMC_TEXT['IMAGE_LOG_FILE_DESC']='Dateiname der Log-Datei für fehlende Bilder.';
$DMC_TEXT['IMAGE_LOG_FILE_VALUES']='';
$DMC_TEXT['PRINT_POST']='PRINT_POST';
$DMC_TEXT['PRINT_POST_DESC']='Übergebene Daten loggen.';
$DMC_TEXT['_VALUES']='';
$DMC_TEXT['LOG_ROTATION']='Art der Rotation der Logdatei';
$DMC_TEXT['LOG_ROTATION_DESC']='LOG nach ... löschen - Werte - -> aus / time -> nach Zeit in Tagen / size -> nach Grösse in Megabyte';
$DMC_TEXT['LOG_ROTATION_VALUES']='time;size';
$DMC_TEXT['LOG_ROTATION_VALUE']='Wert der Rotationslog.';
$DMC_TEXT['LOG_ROTATION_VALUE_DESC']='ZAHLEN-Wert nach Zeit in Tagen / nach Grösse in Megabyte';
$DMC_TEXT['LOG_ROTATION_VALUE_VALUES']='';
/* $DMC_TEXT['']='';
$DMC_TEXT['_DESC']='';
$DMC_TEXT['_VALUES']='';
*/
$DMC_TEXT['CAT_ROOT']='Magento Root Kategorie ID';
$DMC_TEXT['CAT_ROOT_DESC']='Die Magento Root Kategorie ID ermitteln Sie &uuml;ber den Magento Admin Bereich.<br/><br/>- Katalog<br/>- Kategorien verwalten<br/>
<br />Die "Root Categorie"/"Default Categorie" anklicken.<br />Neben der &Uuml;berschrift (z.B. Default Category (ID: 2)) sehen Sie die ID (hier:2)';
$DMC_TEXT['ATTRIBUTE_SET']='ID des zu verwendenden Attribut Sets';
$DMC_TEXT['ATTRIBUTE_SET_DESC']='Die Ermittlung erfolgt &uuml;ber den Magento Admin Bereich.<br/><br/>- Katalog<br/>- Attribute<br/>- Attributses verwalten<br/>
<br />Das zu verwendende Attribut Set anklicken.<br />In der Adressleiste des Browsers (Z.B. http://www.meinshop.de/index.php/admin/catalog_product_set/edit/id/4/key/ steht die ID rechts neben "id/" (hier: 4)';
$DMC_TEXT['SOAP_CLIENT']='Adresse Ihrer API-SOAP-Schnittstelle';
$DMC_TEXT['SOAP_CLIENT_DESC']='Die Adresse Ihrer API-SOAP-Schnittstelle entspricht zumeist Ihrer Magento-Shop Internetadresse (Z.B. http://www.meinshop.de/)
zuz&uuml;glich "api/soap/?wsdl" (hier: http://www.meinshop.de/api/soap/?wsdl).<br/></br>Geben Sie diese Adresse in den Browser ein (oder bet&auml;tigen den Link "testen"), muss eine XML-Datei erscheinen (und nicht "Not Found"). <br><br>Sollte dieses nicht der Fall sein, versuchen Sie folgende Aufrufe:<br>- Mit "index.php/api/soap/?wsdl" -> "http://www.meinshop.de/index.php/api/soap/?wsdl"<br>- Mit "/api/?wsdl" -> "http://www.meinshop.de/api/?wsdl"
<br>- Mit "index.php/api/?wsdl" -> "http://www.meinshop.de/index.php/api/?wsdl"';
$DMC_TEXT['MAX_CAT']='H&ouml;chste Kategorie-ID (z.B. 50)';
$DMC_TEXT['MAX_CAT_DESC']='Hier ist die h&ouml;chste Kategorie-ID aus Magento einzugeben. <br>Im Magento Admin Bereich:<br/><br/>- Katalog<br/>- Kategorien verwalten<br/><br />Die zuletzt &uuml;bermittelte Kategorie anklicken.<br />Neben der &Uuml;berschrift (z.B. Default Category (ID: 2)) sehen Sie die ID (hier:2). Addieren Sie am Besten noch eine 5 (hier dann: 7) und geben Sie den Wert ein.';
$DMC_TEXT['STORE_ID']='ID des Magento Stores';
$DMC_TEXT['STORE_ID_DESC']='Geben Sie hier die ID des Magento Stores an, an welchen die Produkte etc &uuml;bergeben werden sollen. (Oft 0 oder 1).';
$DMC_TEXT['STORE_ID_EXPORT']='Export Magento Store ID';
$DMC_TEXT['STORE_ID_EXPORT_DESC']='Geben Sie hier die ID des Magento Stores an, aus welchen die Daten &uuml;bergeben werden sollen. (Oft 0 oder 1, bei Problemen versuchen Sie die 1 oder 0). <b>Achtung:</b> Es kann sein, dass sich die Store ID von &Uuml;bernahme und &Uuml;bergabe unterscheiden.';
$DMC_TEXT['WEBSITE_ID']='ID der Magento Website';
$DMC_TEXT['WEBSITE_ID_DESC']='Geben Sie hier die ID der Magento Website an, an welchen die Produkte etc &uuml;bergeben werden sollen. (Oft 0 oder 1).';
$DMC_TEXT['ORDER_STATUS_GET']='Zu importierende Bestellstatus (@)';
$DMC_TEXT['ORDER_STATUS_GET_DESC']='Hier geben Sie den Bestellstatus f&uuml;r durch die Schnittstelle abzurufende Bestellungen ein.<br>Mehrere werden durch @ getrennt. zB 1@2';
$DMC_TEXT['ORDER_STATUS_SET']='Zu setzender Bestellstatus';
$DMC_TEXT['ORDER_STATUS_SET_DESC']='Neuer Bestellstatus nach Bestell-Abruf';
$DMC_TEXT['ORDER_STATUS2']='Zu importierender Bestellstatus 2';
$DMC_TEXT['ORDER_STATUS2_DESC']='Hier geben Sie bei Bedarf einen weiteren Bestellstatus f&uuml;r durch die Schnittstelle abzurufende Bestellungen ein.';
	$DMC_TEXT['UPDATE_ORDER_STATUS']='Bestellstatus &auml;ndern nach Abruf (true/false)';
	$DMC_TEXT['UPDATE_ORDER_STATUS_DESC']='W&auml;hlen Sie hier aus, ob der Bestellstatus nach Abruf der Bestellungen ge&auml;ndert werden soll, damit Bestellungen nicht doppelt abgerufen werden k&ouml;nnen.';
$DMC_TEXT['STANDARD_QUANTITY']='Standard Bestand (keine Angabe, wenn aus WaWi)';
$DMC_TEXT['STANDARD_QUANTITY_DESC']='Hier k&ouml;nnen Sie einen in Magento zu hinterlegenden Standard Bestand f&uuml;r Produkte angeben.';
$DMC_TEXT['CHECK']='testen';
$DMC_TEXT['LOGIN_FAILED']='Benutzername oder Passwort falsch. Bitte erneut versuchen.';
$DMC_TEXT['DMC_U']='Benutzername Installationsbereich';
$DMC_TEXT['DMC_U_DESC']='Geben Sie hier den Benutzernamen f&uuml;r den Installationsbereich (NICHT Magento Web User) ein.';
$DMC_TEXT['DMC_P']='Password Installationsbereich';
$DMC_TEXT['DMC_P_DESC']='Geben Sie hier das Password f&uuml;r den Installationsbereich (NICHT Magento Web User) ein.';
$DMC_TEXT['GETPWD']='Passwortgenerator';
$DMC_TEXT['GETPWD_DESC']='Geben Sie hier da Shop-Passwort ein. Klicken Sie auf "generieren" und entnehmen das ermittelte Paswort ein.';
$DMC_TEXT['GETPWD_BTN']='generieren';
$DMC_TEXT['STATUS_WRITE_ART_BEGIN_DETELE_ART']='Alle Artikel zu Abgleichbeginn löschen';
$DMC_TEXT['STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART']='Alle Artikel zu Abgleichbeginn deaktivieren';
$DMC_TEXT['STATUS_WRITE_ART_BEGIN_DETELE_ART_VARIANTS']='Alle Varianten zu Abgleichbeginn löschen';
$DMC_TEXT['STATUS_WRITE_ART_BEGIN_DEAKTIVATE_ART_VARIANTS']='Alle Varianten zu Abgleichbeginn deaktivieren';


?>