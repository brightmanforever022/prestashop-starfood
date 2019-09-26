<?php
exit;

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:00 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>check_orders</action>
    <order_status></order_status>
    <change_status>0</change_status>
    <order_date>2019-02-28@2024-02-28</order_date>
    <order_number>1</order_number>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
CheckOrders check_orders 2019-02-28 - 2024-02-28 23:59:59 -  start with order 1 
 CheckOrders sql= SELECT count(*) AS total FROM prs_orders o WHERE (o.current_state  = '9' OR o.current_state  = '2' )  AND o.id_order >= '20' AND o.id_order >= '1' AND o.id_shop IN (1) AND o.date_add>='2019-02-28' AND o.date_add<='2024-02-28 23:59:59'  -> 2

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:00 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>orders_export</action>
    <OrderDatum>2019-02-28@2024-02-28</OrderDatum>
    <OrderNummer>1</OrderNummer>
    <InformCustomer>0</InformCustomer>
    <ChangeStatus>0</ChangeStatus>
    <OrderStatus></OrderStatus>
    <durchlaeufe>1</durchlaeufe>
    <durchlauf>1</durchlauf>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
orders_export 2019-02-28 - 2024-02-28 23:59:59 -  start with order 1 and change Statsu =0 and inform = 0 
Presta ORDER_STATUS_GET= 9@2 

dmc_db_query-SQL= SELECT * FROM prs_orders o WHERE (o.current_state  = '9' OR o.current_state  = '2' )  AND o.id_order >= '20' AND o.id_shop IN (1) AND o.date_add>='2019-02-28' AND o.date_add<='2024-02-28 23:59:59' 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname, cgd.name AS customers_group, ca.vat_number AS vat_number FROM prs_address AS ca, prs_customer AS c, prs_customer_group AS cg, prs_group_lang AS cgd WHERE c.id_customer=ca.id_customer AND c.id_customer=cg.id_customer AND cg.id_group=cgd.id_group AND cgd.id_lang=1  AND c.id_customer=924  AND ca.id_address=1707  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname FROM prs_address AS ca, prs_customer AS c WHERE c.id_customer=ca.id_customer AND c.id_customer=924  AND ca.id_address=1707  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1  presta_get_currency_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.name FROM prs_currency AS ca WHERE ca.id_currency=1 
dmc_xml_order_opentrans_prod... products_query=  select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '21'

dmc_db_query-SQL= select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '21' \851 Versandkosten als Produkt : (Versand) SHOPSYSTEM= presta Versandkosten:20.000000

dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname, cgd.name AS customers_group, ca.vat_number AS vat_number FROM prs_address AS ca, prs_customer AS c, prs_customer_group AS cg, prs_group_lang AS cgd WHERE c.id_customer=ca.id_customer AND c.id_customer=cg.id_customer AND cg.id_group=cgd.id_group AND cgd.id_lang=1  AND c.id_customer=925  AND ca.id_address=1708  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname FROM prs_address AS ca, prs_customer AS c WHERE c.id_customer=ca.id_customer AND c.id_customer=925  AND ca.id_address=1708  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1  presta_get_currency_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.name FROM prs_currency AS ca WHERE ca.id_currency=1 
dmc_xml_order_opentrans_prod... products_query=  select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '25'

dmc_db_query-SQL= select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '25' \851 Versandkosten als Produkt : (Versand) SHOPSYSTEM= presta Versandkosten:20.000000
 Ende Laufzeit = 0.025679111480713

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:04 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:04 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Art_Update - Thursday 11 2019f April 2019 01:04:20 PM - Artikel (als Hauptartikel) exisitert nicht...

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:04 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Art_Update - Thursday 11 2019f April 2019 01:04:20 PM - Artikel (als Hauptartikel) exisitert nicht...

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:04 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:04 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_3</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>01001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Preis1>0.7500</Artikel_Preis1>
    <Artikel_Preis2>0.7500</Artikel_Preis2>
    <Artikel_Preis3>0.7500</Artikel_Preis3>
    <Artikel_Preis4>0.7500</Artikel_Preis4>
    <Artikel_Gewicht>0.200000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.0700</Artikel_Steuersatz>
    <Artikel_Bilddatei>01001.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Malted Milk Biscuit 200g (Royalty)</Artikel_Bezeichnung1>
    <Artikel_Text1>20 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 01001 (Thursday 11 2019f April 2019 01:10:55 PM) merkmal   - Bez: =Malted Milk Biscuit 200g (Royalty) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Artikel_ID  mit Bild= 01001.jpg
 85 Laufzeit = 0.0057511329650879
dmc_generate_cat_id - KategorieId alt = _3 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_3' OR  meta_description like '_3 %' OR meta_description = '_3' or meta_description like '_3,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _3 
 91 (LZ = 0.0073831081390381)  
dmc_get_highest_id-SQL= SELECT MAX(id_product) AS rueckgabe FROM prs_product =  ERGEBNIS= 3786 .
Neuer Artikel wird angelegt mit ID = 3787 (LZ = 0.0083799362182617)
*********** ArtID 3787 mit Preis 0.7500 fuer Kategorie _3 schreiben:
Artikel_Bezeichnung = Malted Milk Biscuit 200g (Royalty) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0084159374237061)
dmc_array_create_additional Einheit ID= 0 MIT LIEFERSTATUS Auf Lager
GROUP_PERMISSION_ 
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product (id_product, id_supplier, id_manufacturer, id_tax_rules_group, on_sale, ean13, ecotax, quantity, price, wholesale_price, unity, unit_price_ratio, reference, supplier_reference, redirect_type, upc, cache_default_attribute, location, weight, out_of_stock, quantity_discount, customizable, uploadable_files, text_fields, active, indexed, date_add, date_upd) values ('3787', '', '', '0.0700', '0', '', '0', '0', '0.7500', '0.7500', '0', '1', '01001', '', '404', '', '0', '', '0.200000', '1', '0', '0', '0', '0', '1', '0', now(), now()); .
eingetragen
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Artikel angelegt mit Artikel_ID 3787 
dmc_set_art_desc_presta - Presta Product-Details ArtID 3787 fuer Shop 1
dmc_set_art_desc_presta Beschreibung Sprache 1
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3787', '1', 'Malted Milk Biscuit 200g (Royalty)', '20 StÃ¼ck per Karton', '&nbsp;', '3787_maltedmilkbiscuit200groyalty', '', '', '', 'Auf Lager', '', '1'); .
eingetragen
dmc_set_art_desc_presta Beschreibung Sprache 2
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3787', '1', 'Malted Milk Biscuit 200g (Royalty)', '20 StÃ¼ck per Karton', '&nbsp;', '3787_maltedmilkbiscuit200groyalty', '', '', '', 'Auf Lager', '', '2'); .
eingetragen
dmc_set_art_seo 
516 Laufzeit = 0.022411108016968
 520 PROD_TO_CAT, da UPDATE_PROD_TO_CAT=0 bzw exists=0 
dmc_set_art_cat_presta - Presta PRODUCTS_TO_CATEGORIES 

dmc_db_query-SQL= select id_product from prs_category_product where id_product='3787' 
dmc_db_query-SQL= SELECT count(*)+0 as Anzahl_Position FROM prs_category_product where id_category='' dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_category_product (id_category, id_product, position) values ('', '3787', '0'); .
eingetragen
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3787'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3787', '1', '', '1', '0', '0', '0', '1', '0.7500', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3787' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3787' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3787'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3787', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.032673120498657
dmc_set_images ... presta ... presta_attach_images_to_product(01001, 3787, , 2, 01001.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3787' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=01001.jpg 

Suchen nach Bild Nr. 0 =01001.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/01001.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/01001.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.86135196685791
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>INSERTED</MODE>
    <ID>3787</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_12</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Preis1>32.0100</Artikel_Preis1>
    <Artikel_Preis2>32.0100</Artikel_Preis2>
    <Artikel_Preis3>32.0100</Artikel_Preis3>
    <Artikel_Preis4>32.0100</Artikel_Preis4>
    <Artikel_Gewicht>12.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.1900</Artikel_Steuersatz>
    <Artikel_Bilddatei>06001.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Frische Ados Taro 12KG</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 06001 (Thursday 11 2019f April 2019 01:10:56 PM) merkmal   - Bez: =Frische Ados Taro 12KG 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Artikel_ID  mit Bild= 06001.jpg
 85 Laufzeit = 0.0057969093322754
dmc_generate_cat_id - KategorieId alt = _12 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_12' OR  meta_description like '_12 %' OR meta_description = '_12' or meta_description like '_12,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _12 
 91 (LZ = 0.0069789886474609)  
dmc_get_highest_id-SQL= SELECT MAX(id_product) AS rueckgabe FROM prs_product =  ERGEBNIS= 3787 .
Neuer Artikel wird angelegt mit ID = 3788 (LZ = 0.0083088874816895)
*********** ArtID 3788 mit Preis 32.0100 fuer Kategorie _12 schreiben:
Artikel_Bezeichnung = Frische Ados Taro 12KG mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0083549022674561)
dmc_array_create_additional Einheit ID= 0 MIT LIEFERSTATUS Auf Lager
GROUP_PERMISSION_ 
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product (id_product, id_supplier, id_manufacturer, id_tax_rules_group, on_sale, ean13, ecotax, quantity, price, wholesale_price, unity, unit_price_ratio, reference, supplier_reference, redirect_type, upc, cache_default_attribute, location, weight, out_of_stock, quantity_discount, customizable, uploadable_files, text_fields, active, indexed, date_add, date_upd) values ('3788', '', '', '0.1900', '0', '', '0', '0', '32.0100', '32.0100', '0', '1', '06001', '', '404', '', '0', '', '12.000000', '1', '0', '0', '0', '0', '1', '0', now(), now()); .
eingetragen
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Artikel angelegt mit Artikel_ID 3788 
dmc_set_art_desc_presta - Presta Product-Details ArtID 3788 fuer Shop 1
dmc_set_art_desc_presta Beschreibung Sprache 1
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3788', '1', 'Frische Ados Taro 12KG', '&nbsp;', '&nbsp;', '3788_frischeadostaro12kg', '', '', '', 'Auf Lager', '', '1'); .
eingetragen
dmc_set_art_desc_presta Beschreibung Sprache 2
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3788', '1', 'Frische Ados Taro 12KG', '&nbsp;', '&nbsp;', '3788_frischeadostaro12kg', '', '', '', 'Auf Lager', '', '2'); .
eingetragen
dmc_set_art_seo 
516 Laufzeit = 0.017088890075684
 520 PROD_TO_CAT, da UPDATE_PROD_TO_CAT=0 bzw exists=0 
dmc_set_art_cat_presta - Presta PRODUCTS_TO_CATEGORIES 

dmc_db_query-SQL= select id_product from prs_category_product where id_product='3788' 
dmc_db_query-SQL= SELECT count(*)+0 as Anzahl_Position FROM prs_category_product where id_category='' dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_category_product (id_category, id_product, position) values ('', '3788', '1'); .
eingetragen
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3788'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3788', '1', '', '1', '0', '0', '0', '1', '32.0100', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3788' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3788' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3788'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3788', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.024222850799561
dmc_set_images ... presta ... presta_attach_images_to_product(06001, 3788, , 2, 06001.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3788' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=06001.jpg 

Suchen nach Bild Nr. 0 =06001.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06001.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06001.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.89374184608459
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>INSERTED</MODE>
    <ID>3788</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_12</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Preis1>3.8100</Artikel_Preis1>
    <Artikel_Preis2>3.8100</Artikel_Preis2>
    <Artikel_Preis3>3.8100</Artikel_Preis3>
    <Artikel_Preis4>3.8100</Artikel_Preis4>
    <Artikel_Gewicht>1.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.1900</Artikel_Steuersatz>
    <Artikel_Bilddatei>06002.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Frische Cocoyam 1kg</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 06002 (Thursday 11 2019f April 2019 01:10:57 PM) merkmal   - Bez: =Frische Cocoyam 1kg 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Artikel_ID  mit Bild= 06002.jpg
 85 Laufzeit = 0.0058979988098145
dmc_generate_cat_id - KategorieId alt = _12 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_12' OR  meta_description like '_12 %' OR meta_description = '_12' or meta_description like '_12,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _12 
 91 (LZ = 0.007777214050293)  
dmc_get_highest_id-SQL= SELECT MAX(id_product) AS rueckgabe FROM prs_product =  ERGEBNIS= 3788 .
Neuer Artikel wird angelegt mit ID = 3789 (LZ = 0.0087912082672119)
*********** ArtID 3789 mit Preis 3.8100 fuer Kategorie _12 schreiben:
Artikel_Bezeichnung = Frische Cocoyam 1kg mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0088331699371338)
dmc_array_create_additional Einheit ID= 0 MIT LIEFERSTATUS Auf Lager
GROUP_PERMISSION_ 
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product (id_product, id_supplier, id_manufacturer, id_tax_rules_group, on_sale, ean13, ecotax, quantity, price, wholesale_price, unity, unit_price_ratio, reference, supplier_reference, redirect_type, upc, cache_default_attribute, location, weight, out_of_stock, quantity_discount, customizable, uploadable_files, text_fields, active, indexed, date_add, date_upd) values ('3789', '', '', '0.1900', '0', '', '0', '0', '3.8100', '3.8100', '0', '1', '06002', '', '404', '', '0', '', '1.000000', '1', '0', '0', '0', '0', '1', '0', now(), now()); .
eingetragen
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Artikel angelegt mit Artikel_ID 3789 
dmc_set_art_desc_presta - Presta Product-Details ArtID 3789 fuer Shop 1
dmc_set_art_desc_presta Beschreibung Sprache 1
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3789', '1', 'Frische Cocoyam 1kg', '&nbsp;', '&nbsp;', '3789_frischecocoyam1kg', '', '', '', 'Auf Lager', '', '1'); .
eingetragen
dmc_set_art_desc_presta Beschreibung Sprache 2
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_lang (id_product, id_shop, name, description, description_short, link_rewrite, meta_description, meta_keywords, meta_title, available_now, available_later, id_lang) values ('3789', '1', 'Frische Cocoyam 1kg', '&nbsp;', '&nbsp;', '3789_frischecocoyam1kg', '', '', '', 'Auf Lager', '', '2'); .
eingetragen
dmc_set_art_seo 
516 Laufzeit = 0.017807006835938
 520 PROD_TO_CAT, da UPDATE_PROD_TO_CAT=0 bzw exists=0 
dmc_set_art_cat_presta - Presta PRODUCTS_TO_CATEGORIES 

dmc_db_query-SQL= select id_product from prs_category_product where id_product='3789' 
dmc_db_query-SQL= SELECT count(*)+0 as Anzahl_Position FROM prs_category_product where id_category='' dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_category_product (id_category, id_product, position) values ('', '3789', '2'); .
eingetragen
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3789'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3789', '1', '', '1', '0', '0', '0', '1', '3.8100', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3789' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3789' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3789'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3789', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.025358200073242
dmc_set_images ... presta ... presta_attach_images_to_product(06002, 3789, , 2, 06002.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3789' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=06002.jpg 

Suchen nach Bild Nr. 0 =06002.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06002.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06002.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.86703014373779
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>INSERTED</MODE>
    <ID>3789</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_45</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>36002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>9.9000</Artikel_Preis>
    <Artikel_Preis1>9.9000</Artikel_Preis1>
    <Artikel_Preis2>9.9000</Artikel_Preis2>
    <Artikel_Preis3>9.9000</Artikel_Preis3>
    <Artikel_Preis4>9.9000</Artikel_Preis4>
    <Artikel_Gewicht>10.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.0700</Artikel_Steuersatz>
    <Artikel_Bilddatei>36002.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Elephant Atta GB 10KG</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 36002 (Thursday 11 2019f April 2019 01:10:58 PM) merkmal   - Bez: =Elephant Atta GB 10KG 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='36002' .
Artikel_ID 1664 mit Bild= 36002.jpg
 85 Laufzeit = 0.0054810047149658
dmc_generate_cat_id - KategorieId alt = _45 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_45' OR  meta_description like '_45 %' OR meta_description = '_45' or meta_description like '_45,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _45 
 91 (LZ = 0.0066380500793457)  
Artikel existent mit Artikel_ID=1664 (LZ = 0.0066471099853516)
*********** ArtID 1664 mit Preis 9.9000 fuer Kategorie _45 schreiben:
Artikel_Bezeichnung = Elephant Atta GB 10KG mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.00667405128479)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', id_tax_rules_group = '0.0700', ean13 ='', quantity = '0', price = '9.9000', wholesale_price = '9.9000', unity = '0', unit_price_ratio = '1', weight = '10.000000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '1664' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='1664' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 1664 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_1664_Elephant_Atta_GB_10KG_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_1664_elephant_atta_gb_10kg_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Elephant Atta GB 10KG', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_1664_elephant_atta_gb_10kg_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='1664' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.014655113220215
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='1664'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('1664', '1', '_45', '1', '0', '0', '0', '1', '9.9000', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='1664' ORDER BY id_category ASC LIMIT 1) WHERE id_product='1664' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='1664'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('1664', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.022746086120605
dmc_set_images ... presta ... presta_attach_images_to_product(36002, 1664, , 2, 36002.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='1664' LIMIT 1  -> rueckgabe= 1663 
Bild fuer Artikel 1664 existiert bereits mit ID 1663 

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=36002.jpg 

Suchen nach Bild Nr. 0 =36002.jpg 
dmc_get_highest_id-SQL= SELECT MAX(id_image) AS rueckgabe FROM prs_image =  ERGEBNIS= 2419 .
162 TempFile /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/36002.jpg zu neuem Path =/home/starfood/public_html/img/p/2/4/2/0/ 
 array imagesTypes = 
Array
(
    [0] => Array
        (
            [id_image_type] => 81
            [name] => brands
            [width] => 198
            [height] => 80
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [1] => Array
        (
            [id_image_type] => 111
            [name] => cart_default
            [width] => 80
            [height] => 77
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [2] => Array
        (
            [id_image_type] => 117
            [name] => category_default
            [width] => 873
            [height] => 260
            [products] => 0
            [categories] => 1
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [3] => Array
        (
            [id_image_type] => 114
            [name] => home_default
            [width] => 279
            [height] => 268
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [4] => Array
        (
            [id_image_type] => 120
            [name] => img-manu
            [width] => 172
            [height] => 67
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [5] => Array
        (
            [id_image_type] => 115
            [name] => large_default
            [width] => 440
            [height] => 422
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [6] => Array
        (
            [id_image_type] => 91
            [name] => logo-manu
            [width] => 170
            [height] => 70
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [7] => Array
        (
            [id_image_type] => 113
            [name] => medium_default
            [width] => 125
            [height] => 120
            [products] => 1
            [categories] => 1
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [8] => Array
        (
            [id_image_type] => 119
            [name] => m_scene_default
            [width] => 161
            [height] => 58
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [9] => Array
        (
            [id_image_type] => 121
            [name] => Newsletter 200px
            [width] => 200
            [height] => 200
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [10] => Array
        (
            [id_image_type] => 122
            [name] => Newsletter Logos
            [width] => 20
            [height] => 20
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [11] => Array
        (
            [id_image_type] => 32
            [name] => pr_details_thumb
            [width] => 37
            [height] => 53
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [12] => Array
        (
            [id_image_type] => 118
            [name] => scene_default
            [width] => 870
            [height] => 270
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [13] => Array
        (
            [id_image_type] => 112
            [name] => small_default
            [width] => 82
            [height] => 79
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [14] => Array
        (
            [id_image_type] => 116
            [name] => thickbox_default
            [width] => 800
            [height] => 800
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

)
 
dmc_sql_delete-SQL= DELETE FROM prs_image_lang WHERE  id_lang=1 AND id_image in (select id_image from prs_image where id_product=1664)   gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image WHERE id_product='1664'  gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image_shop WHERE id_shop=1 AND id_product =1664   gelÃ¶scht
 imageResize to=/home/starfood/public_html/img/p/2/4/2/0/2420-brands.jpg 
 width=198 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:10 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Preis1>0.7500</Artikel_Preis1>
    <Artikel_Preis2>0.7500</Artikel_Preis2>
    <Artikel_Preis3>0.7500</Artikel_Preis3>
    <Artikel_Preis4>0.7500</Artikel_Preis4>
    <Artikel_Gewicht>0.100000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.0700</Artikel_Steuersatz>
    <Artikel_Bilddatei>77084.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Citric Acid 100g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1>20 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77084 (Thursday 11 2019f April 2019 01:10:59 PM) merkmal   - Bez: =Citric Acid 100g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Artikel_ID 3240 mit Bild= 77084.jpg
 85 Laufzeit = 0.0054240226745605
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.006511926651001)  
Artikel existent mit Artikel_ID=3240 (LZ = 0.0065181255340576)
*********** ArtID 3240 mit Preis 0.7500 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Citric Acid 100g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0065360069274902)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', id_tax_rules_group = '0.0700', ean13 ='', quantity = '0', price = '0.7500', wholesale_price = '0.7500', unity = '0', unit_price_ratio = '1', weight = '0.100000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3240' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3240' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3240 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3240_Citric_Acid_100g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3240_citric_acid_100g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Citric Acid 100g (TRS)', description ='20 StÃ¼ck per Karton', description_short ='&nbsp;', link_rewrite ='_3240_citric_acid_100g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3240' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.011976003646851
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3240'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3240', '1', '_81', '1', '0', '0', '0', '1', '0.7500', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3240' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3240' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3240'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3240', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.018868923187256
dmc_set_images ... presta ... presta_attach_images_to_product(77084, 3240, , 2, 77084.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3240' LIMIT 1  -> rueckgabe= 647 
Bild fuer Artikel 3240 existiert bereits mit ID 647 

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77084.jpg 

Suchen nach Bild Nr. 0 =77084.jpg 
dmc_get_highest_id-SQL= SELECT MAX(id_image) AS rueckgabe FROM prs_image =  ERGEBNIS= 2419 .
162 TempFile /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77084.jpg zu neuem Path =/home/starfood/public_html/img/p/2/4/2/0/ 
 array imagesTypes = 
Array
(
    [0] => Array
        (
            [id_image_type] => 81
            [name] => brands
            [width] => 198
            [height] => 80
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [1] => Array
        (
            [id_image_type] => 111
            [name] => cart_default
            [width] => 80
            [height] => 77
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [2] => Array
        (
            [id_image_type] => 117
            [name] => category_default
            [width] => 873
            [height] => 260
            [products] => 0
            [categories] => 1
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [3] => Array
        (
            [id_image_type] => 114
            [name] => home_default
            [width] => 279
            [height] => 268
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [4] => Array
        (
            [id_image_type] => 120
            [name] => img-manu
            [width] => 172
            [height] => 67
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [5] => Array
        (
            [id_image_type] => 115
            [name] => large_default
            [width] => 440
            [height] => 422
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [6] => Array
        (
            [id_image_type] => 91
            [name] => logo-manu
            [width] => 170
            [height] => 70
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [7] => Array
        (
            [id_image_type] => 113
            [name] => medium_default
            [width] => 125
            [height] => 120
            [products] => 1
            [categories] => 1
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [8] => Array
        (
            [id_image_type] => 119
            [name] => m_scene_default
            [width] => 161
            [height] => 58
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [9] => Array
        (
            [id_image_type] => 121
            [name] => Newsletter 200px
            [width] => 200
            [height] => 200
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [10] => Array
        (
            [id_image_type] => 122
            [name] => Newsletter Logos
            [width] => 20
            [height] => 20
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [11] => Array
        (
            [id_image_type] => 32
            [name] => pr_details_thumb
            [width] => 37
            [height] => 53
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [12] => Array
        (
            [id_image_type] => 118
            [name] => scene_default
            [width] => 870
            [height] => 270
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [13] => Array
        (
            [id_image_type] => 112
            [name] => small_default
            [width] => 82
            [height] => 79
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [14] => Array
        (
            [id_image_type] => 116
            [name] => thickbox_default
            [width] => 800
            [height] => 800
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

)
 
dmc_sql_delete-SQL= DELETE FROM prs_image_lang WHERE  id_lang=1 AND id_image in (select id_image from prs_image where id_product=3240)   gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image WHERE id_product='3240'  gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image_shop WHERE id_shop=1 AND id_product =3240   gelÃ¶scht
 imageResize to=/home/starfood/public_html/img/p/2/4/2/0/2420-brands.jpg 
 width=198 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:11 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Preis1>10.5900</Artikel_Preis1>
    <Artikel_Preis2>10.5900</Artikel_Preis2>
    <Artikel_Preis3>10.5900</Artikel_Preis3>
    <Artikel_Preis4>10.5900</Artikel_Preis4>
    <Artikel_Gewicht>0.400000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.0700</Artikel_Steuersatz>
    <Artikel_Bilddatei>77087.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Cloves 400g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1>10 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77087 (Thursday 11 2019f April 2019 01:11:00 PM) merkmal   - Bez: =Cloves 400g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Artikel_ID 3243 mit Bild= 77087.jpg
 85 Laufzeit = 0.0053830146789551
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.0066001415252686)  
Artikel existent mit Artikel_ID=3243 (LZ = 0.0066111087799072)
*********** ArtID 3243 mit Preis 10.5900 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Cloves 400g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.006641149520874)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', id_tax_rules_group = '0.0700', ean13 ='', quantity = '0', price = '10.5900', wholesale_price = '10.5900', unity = '0', unit_price_ratio = '1', weight = '0.400000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3243' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3243' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3243 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3243_Cloves_400g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3243_cloves_400g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Cloves 400g (TRS)', description ='10 StÃ¼ck per Karton', description_short ='&nbsp;', link_rewrite ='_3243_cloves_400g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3243' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.011258125305176
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3243'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3243', '1', '_81', '1', '0', '0', '0', '1', '10.5900', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3243' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3243' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3243'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3243', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.01744818687439
dmc_set_images ... presta ... presta_attach_images_to_product(77087, 3243, , 2, 77087.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3243' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77087.jpg 

Suchen nach Bild Nr. 0 =77087.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77087.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77087.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.86709713935852
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3243</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:11 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Preis1>0.8000</Artikel_Preis1>
    <Artikel_Preis2>0.8000</Artikel_Preis2>
    <Artikel_Preis3>0.8000</Artikel_Preis3>
    <Artikel_Preis4>0.8000</Artikel_Preis4>
    <Artikel_Gewicht>0.500000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>0.0700</Artikel_Steuersatz>
    <Artikel_Bilddatei>77435.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Peas Green Whole 500g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77435 (Thursday 11 2019f April 2019 01:11:01 PM) merkmal   - Bez: =Peas Green Whole 500g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Artikel_ID 3554 mit Bild= 77435.jpg
 85 Laufzeit = 0.0052559375762939
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.0064010620117188)  
Artikel existent mit Artikel_ID=3554 (LZ = 0.0064120292663574)
*********** ArtID 3554 mit Preis 0.8000 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Peas Green Whole 500g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0064399242401123)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', id_tax_rules_group = '0.0700', ean13 ='', quantity = '0', price = '0.8000', wholesale_price = '0.8000', unity = '0', unit_price_ratio = '1', weight = '0.500000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3554' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3554' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3554 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3554_Peas_Green_Whole_500g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3554_peas_green_whole_500g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Peas Green Whole 500g (TRS)', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_3554_peas_green_whole_500g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3554' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.01166296005249
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3554'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, id_type_redirected, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3554', '1', '_81', '1', '0', '0', '0', '1', '0.8000', '1', '0', '1', '0', '0', '0', '0', '1', '404', '0', '1', '1', 'both', '0', '0', now(), now()); .
Fehler: NICHT eingetragen: Unknown column 'id_type_redirected' in 'field list'
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3554' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3554' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3554'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3554', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.018456935882568
dmc_set_images ... presta ... presta_attach_images_to_product(77435, 3554, , 2, 77435.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3554' LIMIT 1  -> rueckgabe= 717 
Bild fuer Artikel 3554 existiert bereits mit ID 717 

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77435.jpg 

Suchen nach Bild Nr. 0 =77435.jpg 
dmc_get_highest_id-SQL= SELECT MAX(id_image) AS rueckgabe FROM prs_image =  ERGEBNIS= 2419 .
162 TempFile /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77435.jpg zu neuem Path =/home/starfood/public_html/img/p/2/4/2/0/ 
 array imagesTypes = 
Array
(
    [0] => Array
        (
            [id_image_type] => 81
            [name] => brands
            [width] => 198
            [height] => 80
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [1] => Array
        (
            [id_image_type] => 111
            [name] => cart_default
            [width] => 80
            [height] => 77
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [2] => Array
        (
            [id_image_type] => 117
            [name] => category_default
            [width] => 873
            [height] => 260
            [products] => 0
            [categories] => 1
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [3] => Array
        (
            [id_image_type] => 114
            [name] => home_default
            [width] => 279
            [height] => 268
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [4] => Array
        (
            [id_image_type] => 120
            [name] => img-manu
            [width] => 172
            [height] => 67
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [5] => Array
        (
            [id_image_type] => 115
            [name] => large_default
            [width] => 440
            [height] => 422
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [6] => Array
        (
            [id_image_type] => 91
            [name] => logo-manu
            [width] => 170
            [height] => 70
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

    [7] => Array
        (
            [id_image_type] => 113
            [name] => medium_default
            [width] => 125
            [height] => 120
            [products] => 1
            [categories] => 1
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [8] => Array
        (
            [id_image_type] => 119
            [name] => m_scene_default
            [width] => 161
            [height] => 58
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [9] => Array
        (
            [id_image_type] => 121
            [name] => Newsletter 200px
            [width] => 200
            [height] => 200
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [10] => Array
        (
            [id_image_type] => 122
            [name] => Newsletter Logos
            [width] => 20
            [height] => 20
            [products] => 0
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [11] => Array
        (
            [id_image_type] => 32
            [name] => pr_details_thumb
            [width] => 37
            [height] => 53
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 0
        )

    [12] => Array
        (
            [id_image_type] => 118
            [name] => scene_default
            [width] => 870
            [height] => 270
            [products] => 0
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 1
            [stores] => 1
        )

    [13] => Array
        (
            [id_image_type] => 112
            [name] => small_default
            [width] => 82
            [height] => 79
            [products] => 1
            [categories] => 0
            [manufacturers] => 1
            [suppliers] => 1
            [scenes] => 0
            [stores] => 1
        )

    [14] => Array
        (
            [id_image_type] => 116
            [name] => thickbox_default
            [width] => 800
            [height] => 800
            [products] => 1
            [categories] => 0
            [manufacturers] => 0
            [suppliers] => 0
            [scenes] => 0
            [stores] => 1
        )

)
 
dmc_sql_delete-SQL= DELETE FROM prs_image_lang WHERE  id_lang=1 AND id_image in (select id_image from prs_image where id_product=3554)   gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image WHERE id_product='3554'  gelÃ¶scht
dmc_sql_delete-SQL= DELETE FROM prs_image_shop WHERE id_shop=1 AND id_product =3554   gelÃ¶scht
 imageResize to=/home/starfood/public_html/img/p/2/4/2/0/2420-brands.jpg 
 width=198 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:11 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:11 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_3</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>01001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Preis1>0.7500</Artikel_Preis1>
    <Artikel_Preis2>0.7500</Artikel_Preis2>
    <Artikel_Preis3>0.7500</Artikel_Preis3>
    <Artikel_Preis4>0.7500</Artikel_Preis4>
    <Artikel_Gewicht>0.200000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>01001.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Malted Milk Biscuit 200g (Royalty)</Artikel_Bezeichnung1>
    <Artikel_Text1>20 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 01001 (Thursday 11 2019f April 2019 01:21:22 PM) merkmal   - Bez: =Malted Milk Biscuit 200g (Royalty) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Artikel_ID 3787 mit Bild= 01001.jpg
 85 Laufzeit = 0.0070829391479492
dmc_generate_cat_id - KategorieId alt = _3 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_3' OR  meta_description like '_3 %' OR meta_description = '_3' or meta_description like '_3,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _3 
 91 (LZ = 0.0090000629425049)  
Artikel existent mit Artikel_ID=3787 (LZ = 0.0090138912200928)
*********** ArtID 3787 mit Preis 0.7500 fuer Kategorie _3 schreiben:
Artikel_Bezeichnung = Malted Milk Biscuit 200g (Royalty) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0090570449829102)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '0.7500', wholesale_price = '0.7500', unity = '0', unit_price_ratio = '1', weight = '0.200000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3787' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3787' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3787 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3787_Malted_Milk_Biscuit_200g_Royalty_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3787_malted_milk_biscuit_200g_royalty_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Malted Milk Biscuit 200g (Royalty)', description ='20 StÃ¼ck per Karton', description_short ='&nbsp;', link_rewrite ='_3787_malted_milk_biscuit_200g_royalty_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3787' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.015650987625122
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3787'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3787', '1', '_3', '1', '0', '0', '0', '1', '0.7500', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3787' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3787' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3787'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3787', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.025552988052368
dmc_set_images ... presta ... presta_attach_images_to_product(01001, 3787, , 2, 01001.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3787' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=01001.jpg 

Suchen nach Bild Nr. 0 =01001.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/01001.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/01001.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.87370991706848
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3787</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_12</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Preis1>32.0100</Artikel_Preis1>
    <Artikel_Preis2>32.0100</Artikel_Preis2>
    <Artikel_Preis3>32.0100</Artikel_Preis3>
    <Artikel_Preis4>32.0100</Artikel_Preis4>
    <Artikel_Gewicht>12.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>06001.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Frische Ados Taro 12KG</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 06001 (Thursday 11 2019f April 2019 01:21:23 PM) merkmal   - Bez: =Frische Ados Taro 12KG 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Artikel_ID 3788 mit Bild= 06001.jpg
 85 Laufzeit = 0.0054199695587158
dmc_generate_cat_id - KategorieId alt = _12 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_12' OR  meta_description like '_12 %' OR meta_description = '_12' or meta_description like '_12,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _12 
 91 (LZ = 0.0066981315612793)  
Artikel existent mit Artikel_ID=3788 (LZ = 0.0067100524902344)
*********** ArtID 3788 mit Preis 32.0100 fuer Kategorie _12 schreiben:
Artikel_Bezeichnung = Frische Ados Taro 12KG mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0067479610443115)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '32.0100', wholesale_price = '32.0100', unity = '0', unit_price_ratio = '1', weight = '12.000000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3788' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3788' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3788 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3788_Frische_Ados_Taro_12KG_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3788_frische_ados_taro_12kg_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Frische Ados Taro 12KG', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_3788_frische_ados_taro_12kg_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3788' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.011841058731079
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3788'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3788', '1', '_12', '1', '0', '0', '0', '1', '32.0100', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3788' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3788' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3788'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3788', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.019257068634033
dmc_set_images ... presta ... presta_attach_images_to_product(06001, 3788, , 2, 06001.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3788' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=06001.jpg 

Suchen nach Bild Nr. 0 =06001.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06001.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06001.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.84287095069885
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3788</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_12</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Preis1>3.8100</Artikel_Preis1>
    <Artikel_Preis2>3.8100</Artikel_Preis2>
    <Artikel_Preis3>3.8100</Artikel_Preis3>
    <Artikel_Preis4>3.8100</Artikel_Preis4>
    <Artikel_Gewicht>1.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>06002.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Frische Cocoyam 1kg</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 06002 (Thursday 11 2019f April 2019 01:21:24 PM) merkmal   - Bez: =Frische Cocoyam 1kg 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Artikel_ID 3789 mit Bild= 06002.jpg
 85 Laufzeit = 0.0042650699615479
dmc_generate_cat_id - KategorieId alt = _12 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_12' OR  meta_description like '_12 %' OR meta_description = '_12' or meta_description like '_12,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _12 
 91 (LZ = 0.0054669380187988)  
Artikel existent mit Artikel_ID=3789 (LZ = 0.0054769515991211)
*********** ArtID 3789 mit Preis 3.8100 fuer Kategorie _12 schreiben:
Artikel_Bezeichnung = Frische Cocoyam 1kg mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0055029392242432)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '3.8100', wholesale_price = '3.8100', unity = '0', unit_price_ratio = '1', weight = '1.000000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3789' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3789' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3789 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3789_Frische_Cocoyam_1kg_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3789_frische_cocoyam_1kg_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Frische Cocoyam 1kg', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_3789_frische_cocoyam_1kg_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3789' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.011088132858276
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3789'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3789', '1', '_12', '1', '0', '0', '0', '1', '3.8100', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3789' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3789' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3789'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3789', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.017451047897339
dmc_set_images ... presta ... presta_attach_images_to_product(06002, 3789, , 2, 06002.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3789' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=06002.jpg 

Suchen nach Bild Nr. 0 =06002.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06002.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/06002.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.83222699165344
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3789</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_45</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>36002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>9.9000</Artikel_Preis>
    <Artikel_Preis1>9.9000</Artikel_Preis1>
    <Artikel_Preis2>9.9000</Artikel_Preis2>
    <Artikel_Preis3>9.9000</Artikel_Preis3>
    <Artikel_Preis4>9.9000</Artikel_Preis4>
    <Artikel_Gewicht>10.000000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>36002.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Elephant Atta GB 10KG</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 36002 (Thursday 11 2019f April 2019 01:21:25 PM) merkmal   - Bez: =Elephant Atta GB 10KG 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='36002' .
Artikel_ID 1664 mit Bild= 36002.jpg
 85 Laufzeit = 0.0046789646148682
dmc_generate_cat_id - KategorieId alt = _45 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_45' OR  meta_description like '_45 %' OR meta_description = '_45' or meta_description like '_45,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _45 
 91 (LZ = 0.0058379173278809)  
Artikel existent mit Artikel_ID=1664 (LZ = 0.0058488845825195)
*********** ArtID 1664 mit Preis 9.9000 fuer Kategorie _45 schreiben:
Artikel_Bezeichnung = Elephant Atta GB 10KG mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0058848857879639)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '9.9000', wholesale_price = '9.9000', unity = '0', unit_price_ratio = '1', weight = '10.000000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '1664' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='1664' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 1664 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_1664_Elephant_Atta_GB_10KG_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_1664_elephant_atta_gb_10kg_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Elephant Atta GB 10KG', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_1664_elephant_atta_gb_10kg_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='1664' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.0099368095397949
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='1664'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('1664', '1', '_45', '1', '0', '0', '0', '1', '9.9000', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='1664' ORDER BY id_category ASC LIMIT 1) WHERE id_product='1664' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='1664'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('1664', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.020982027053833
dmc_set_images ... presta ... presta_attach_images_to_product(36002, 1664, , 2, 36002.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='1664' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=36002.jpg 

Suchen nach Bild Nr. 0 =36002.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/36002.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/36002.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.85341286659241
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>1664</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Preis1>0.7500</Artikel_Preis1>
    <Artikel_Preis2>0.7500</Artikel_Preis2>
    <Artikel_Preis3>0.7500</Artikel_Preis3>
    <Artikel_Preis4>0.7500</Artikel_Preis4>
    <Artikel_Gewicht>0.100000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>77084.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Citric Acid 100g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1>20 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77084 (Thursday 11 2019f April 2019 01:21:26 PM) merkmal   - Bez: =Citric Acid 100g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Artikel_ID 3240 mit Bild= 77084.jpg
 85 Laufzeit = 0.0056750774383545
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.0067880153656006)  
Artikel existent mit Artikel_ID=3240 (LZ = 0.0068001747131348)
*********** ArtID 3240 mit Preis 0.7500 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Citric Acid 100g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0068361759185791)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '0.7500', wholesale_price = '0.7500', unity = '0', unit_price_ratio = '1', weight = '0.100000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3240' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3240' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3240 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3240_Citric_Acid_100g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3240_citric_acid_100g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Citric Acid 100g (TRS)', description ='20 StÃ¼ck per Karton', description_short ='&nbsp;', link_rewrite ='_3240_citric_acid_100g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3240' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.011707067489624
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3240'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3240', '1', '_81', '1', '0', '0', '0', '1', '0.7500', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3240' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3240' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3240'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3240', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.018810987472534
dmc_set_images ... presta ... presta_attach_images_to_product(77084, 3240, , 2, 77084.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3240' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77084.jpg 

Suchen nach Bild Nr. 0 =77084.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77084.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77084.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.86130499839783
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3240</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Preis1>10.5900</Artikel_Preis1>
    <Artikel_Preis2>10.5900</Artikel_Preis2>
    <Artikel_Preis3>10.5900</Artikel_Preis3>
    <Artikel_Preis4>10.5900</Artikel_Preis4>
    <Artikel_Gewicht>0.400000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>77087.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Cloves 400g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1>10 StÃ¼ck per Karton</Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77087 (Thursday 11 2019f April 2019 01:21:27 PM) merkmal   - Bez: =Cloves 400g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Artikel_ID 3243 mit Bild= 77087.jpg
 85 Laufzeit = 0.004490852355957
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.0056159496307373)  
Artikel existent mit Artikel_ID=3243 (LZ = 0.0056219100952148)
*********** ArtID 3243 mit Preis 10.5900 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Cloves 400g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0056488513946533)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '10.5900', wholesale_price = '10.5900', unity = '0', unit_price_ratio = '1', weight = '0.400000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3243' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3243' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3243 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3243_Cloves_400g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3243_cloves_400g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Cloves 400g (TRS)', description ='10 StÃ¼ck per Karton', description_short ='&nbsp;', link_rewrite ='_3243_cloves_400g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3243' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.010125875473022
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3243'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3243', '1', '_81', '1', '0', '0', '0', '1', '10.5900', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3243' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3243' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3243'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3243', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.019087791442871
dmc_set_images ... presta ... presta_attach_images_to_product(77087, 3243, , 2, 77087.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3243' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77087.jpg 

Suchen nach Bild Nr. 0 =77087.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77087.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77087.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.85772085189819
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3243</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>write_artikel</action>
    <ExportModus>NoOverwrite</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Kategorie_ID>_81</Artikel_Kategorie_ID>
    <Hersteller_ID></Hersteller_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Preis1>0.8000</Artikel_Preis1>
    <Artikel_Preis2>0.8000</Artikel_Preis2>
    <Artikel_Preis3>0.8000</Artikel_Preis3>
    <Artikel_Preis4>0.8000</Artikel_Preis4>
    <Artikel_Gewicht>0.500000</Artikel_Gewicht>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>3</Artikel_Steuersatz>
    <Artikel_Bilddatei>77435.jpg</Artikel_Bilddatei>
    <Artikel_VPE></Artikel_VPE>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>
    <Artikel_Startseite>0</Artikel_Startseite>
    <SkipImages>0</SkipImages>
    <Artikel_Bezeichnung1>Peas Green Whole 500g (TRS)</Artikel_Bezeichnung1>
    <Artikel_Text1></Artikel_Text1>
    <Artikel_Kurztext1></Artikel_Kurztext1>
    <Artikel_TextLanguage1>2</Artikel_TextLanguage1>
    <Artikel_MetaTitle1></Artikel_MetaTitle1>
    <Artikel_MetaDescription1></Artikel_MetaDescription1>
    <Artikel_MetaKeywords1></Artikel_MetaKeywords1>
    <Artikel_URL1></Artikel_URL1>
    <Aktiv>1</Aktiv>
    <Aenderungsdatum></Aenderungsdatum>
    <Artikel_Variante_Von></Artikel_Variante_Von>
    <Artikel_Merkmal></Artikel_Merkmal>
    <Artikel_Auspraegung></Artikel_Auspraegung>

 ********************************** 
write_artikelfunction CheckLogin SHOPSYSTEM=presta 
 - 240 - dmc_write_art - ArtNr: 77435 (Thursday 11 2019f April 2019 01:21:28 PM) merkmal   - Bez: =Peas Green Whole 500g (TRS) 
dmc_count_languages-SQL= SELECT id_lang,name,active,iso_code FROM prs_lang = 2.
dmc_art_functions - products_vpe=  .
771 products_vpe= 0 .
772 Artikel_VPE_ID= 0 .
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Artikel_ID 3554 mit Bild= 77435.jpg
 85 Laufzeit = 0.0035910606384277
dmc_generate_cat_id - KategorieId alt = _81 
dmc_get_category_id  (db_functions) ->  ( select id_category as categories_id from prs_category_lang WHERE name='_81' OR  meta_description like '_81 %' OR meta_description = '_81' or meta_description like '_81,%' LIMIT 1 ) ->dmc_generate_cat_id - KategorieId neu = _81 
 91 (LZ = 0.0044620037078857)  
Artikel existent mit Artikel_ID=3554 (LZ = 0.004472017288208)
*********** ArtID 3554 mit Preis 0.8000 fuer Kategorie _81 schreiben:
Artikel_Bezeichnung = Peas Green Whole 500g (TRS) mit Desc(Anfang)=
**************
112 - KEIN VARIANTENARTIKEL (LZ = 0.0045039653778076)
GROUP_PERMISSION_ 
dmc_sql_update_array-SQL= UPDATE prs_product SET supplier_reference ='', ean13 ='', quantity = '0', price = '0.8000', wholesale_price = '0.8000', unity = '0', unit_price_ratio = '1', weight = '0.500000', out_of_stock = '1', active = '1', date_upd = now() where id_product = '3554' ... aktualisiert
dmc_update_art_desc_presta - Presta Product-Details fuer ShopID 1 Sprache 1
# dmc_sql_select_query-SQL= SELECT id_product AS rueckgabe FROM prs_product_lang WHERE id_product='3554' AND id_lang =1 AND id_shop=1 LIMIT 1  -> rueckgabe= 3554 
*** dmc_art_functions dmc_prepare_seo
/// 631= einzutragen=_3554_Peas_Green_Whole_500g_TRS_1
Beschreibung UPDATE presta Sprache=1 und URL Key (seo)=_3554_peas_green_whole_500g_trs_1
dmc_sql_update_array-SQL= UPDATE prs_product_lang SET name ='Peas Green Whole 500g (TRS)', description ='&nbsp;', description_short ='&nbsp;', link_rewrite ='_3554_peas_green_whole_500g_trs_1', meta_description ='', meta_keywords ='', meta_title ='', available_now ='Auf Lager', available_later ='' where id_product ='3554' and id_lang = '1' AND id_shop=1 ... aktualisiert
460  516 Laufzeit = 0.0084390640258789
dmc_set_art_shop_presta - Presta Shop-Details fuer SHOP_ID 1 
dmc_sql_delete-SQL= DELETE FROM prs_product_shop WHERE id_shop = 1 AND id_product='3554'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_product_shop (id_product, id_shop, id_category_default, id_tax_rules_group, on_sale, online_only, ecotax, minimal_quantity, price, wholesale_price, unity, unit_price_ratio, additional_shipping_cost, customizable, uploadable_files, text_fields, active, redirect_type, available_for_order, show_price, visibility, cache_default_attribute, advanced_stock_management, date_add, date_upd) values ('3554', '1', '_81', '1', '0', '0', '0', '1', '0.8000', '1', '0', '1', '0', '0', '0', '0', '1', '404', '1', '1', 'both', '0', '0', now(), now()); .
eingetragen
dmc_sql_query-SQL= UPDATE prs_product_shop SET id_category_default = (SELECT id_category FROM prs_category_product WHERE id_product='3554' ORDER BY id_category ASC LIMIT 1) WHERE id_product='3554' AND id_shop=1  ausgefuehrt.
dmc_sql_delete-SQL= DELETE FROM prs_stock_available WHERE id_shop = 1 AND id_product='3554'  gelÃ¶scht
dmc_sql_insert_array 
dmc_sql_insert_array-SQL= INSERT INTO prs_stock_available (id_product, id_product_attribute, id_shop, id_shop_group, quantity, depends_on_stock, out_of_stock) values ('3554', '0', '1', '0', '0', '0', '2'); .
eingetragen

 619 Laufzeit = 0.015718936920166
dmc_set_images ... presta ... presta_attach_images_to_product(77435, 3554, , 2, 77435.jpg, Resource id #103)  
 PS_ADMIN_DIR=/home/starfood/public_html/dmc2019s/../admin054dllptf/ 
 _PS_PROD_IMG_DIR_=/home/starfood/public_html/img/p/ 
lokale Bilder
# dmc_sql_select_query-SQL= SELECT id_image AS rueckgabe FROM prs_image WHERE id_product='3554' LIMIT 1  -> rueckgabe=  

 Anzahl uebergebener Bilder aus WaWi: 1, erstes Bilder=77435.jpg 

Suchen nach Bild Nr. 0 =77435.jpg 
Lokale Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77435.jpg existiert nicht
 Bilddatei /home/starfood/public_html/dmc2019s/../dmc2018f/upload_images/77435.gif (auch nicht mit .png .jpg) exisitiert nicht 
done 
dmc_userfunctions_h - User Attribute 
dmc_run_special_functions 
dmc_run_special_functions - ende
 Ende Laufzeit = 0.83934092521667
dmc_write_art - rueckgabe=<?xml version="1.0" encoding="UTF-8"?>
<STATUS>
  <STATUS_INFO>
    <ACTION>write_artikel</ACTION>
    <MESSAGE>OK</MESSAGE>
    <MODE>UPDATED</MODE>
    <ID>3554</ID>
  </STATUS_INFO>
</STATUS>



***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:21 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>write_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status write_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>01001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Art_Update - Thursday 11 2019f April 2019 01:26:47 PM - Artikel 01001 mit ID 3787 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3787 soll neuen PREIS 0.7500 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '0.7500', date_upd = now() where id_product = '3787' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=0.7500 WHERE id_product = '3787' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3787' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3787'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Art_Update - Thursday 11 2019f April 2019 01:26:47 PM - Artikel 06001 mit ID 3788 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3788 soll neuen PREIS 32.0100 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '32.0100', date_upd = now() where id_product = '3788' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=32.0100 WHERE id_product = '3788' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3788' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3788'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Art_Update - Thursday 11 2019f April 2019 01:26:47 PM - Artikel 06002 mit ID 3789 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3789 soll neuen PREIS 3.8100 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '3.8100', date_upd = now() where id_product = '3789' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=3.8100 WHERE id_product = '3789' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3789' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3789'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>36002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>9.9000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='36002' .
Art_Update - Thursday 11 2019f April 2019 01:26:48 PM - Artikel 36002 mit ID 1664 fuer Preisupdate auf Shop  simple  existiert
Artikel id 1664 soll neuen PREIS 9.9000 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '9.9000', date_upd = now() where id_product = '1664' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=9.9000 WHERE id_product = '1664' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '1664' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '1664'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Art_Update - Thursday 11 2019f April 2019 01:26:48 PM - Artikel 77084 mit ID 3240 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3240 soll neuen PREIS 0.7500 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '0.7500', date_upd = now() where id_product = '3240' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=0.7500 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3240'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Art_Update - Thursday 11 2019f April 2019 01:26:48 PM - Artikel 77087 mit ID 3243 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3243 soll neuen PREIS 10.5900 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '10.5900', date_upd = now() where id_product = '3243' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=10.5900 WHERE id_product = '3243' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3243' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3243'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>PreisQuantity</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Art_Update - Thursday 11 2019f April 2019 01:26:48 PM - Artikel 77435 mit ID 3554 fuer Preisupdate auf Shop  simple  existiert
Artikel id 3554 soll neuen PREIS 0.8000 und Menge 0 und Status 1 bekommen ...dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', price = '0.8000', date_upd = now() where id_product = '3554' ... aktualisiert

dmc_db_query-SQL= UPDATE prs_product_shop SET price=0.8000 WHERE id_product = '3554' 
dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3554' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3554'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:26 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>01001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 01001 mit ID 3787 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3787 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3787' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3787' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3787'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 06001 mit ID 3788 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3788 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3788' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3788' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3788'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 06002 mit ID 3789 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3789 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3789' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3789' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3789'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>36002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>9.9000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='36002' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 36002 mit ID 1664 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 1664 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '1664' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '1664' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '1664'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 77084 mit ID 3240 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3240 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3240' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3240'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 77087 mit ID 3243 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3243 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3243' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3243' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3243'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Art_Update - Thursday 11 2019f April 2019 01:28:03 PM - Artikel 77435 mit ID 3554 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3554 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3554' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3554' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3554'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:28 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:29 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>check_orders</action>
    <order_status></order_status>
    <change_status>0</change_status>
    <order_date>2019-02-28@2024-02-28</order_date>
    <order_number>1</order_number>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
CheckOrders check_orders 2019-02-28 - 2024-02-28 23:59:59 -  start with order 1 
 CheckOrders sql= SELECT count(*) AS total FROM prs_orders o WHERE (o.current_state  = '9' OR o.current_state  = '2' )  AND o.id_order >= '20' AND o.id_order >= '1' AND o.id_shop IN (1) AND o.date_add>='2019-02-28' AND o.date_add<='2024-02-28 23:59:59'  -> 2

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:29 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>orders_export</action>
    <OrderDatum>2019-02-28@2024-02-28</OrderDatum>
    <OrderNummer>1</OrderNummer>
    <InformCustomer>0</InformCustomer>
    <ChangeStatus>0</ChangeStatus>
    <OrderStatus></OrderStatus>
    <durchlaeufe>1</durchlaeufe>
    <durchlauf>1</durchlauf>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
orders_export 2019-02-28 - 2024-02-28 23:59:59 -  start with order 1 and change Statsu =0 and inform = 0 
Presta ORDER_STATUS_GET= 9@2 

dmc_db_query-SQL= SELECT * FROM prs_orders o WHERE (o.current_state  = '9' OR o.current_state  = '2' )  AND o.id_order >= '20' AND o.id_shop IN (1) AND o.date_add>='2019-02-28' AND o.date_add<='2024-02-28 23:59:59' 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname, cgd.name AS customers_group, ca.vat_number AS vat_number FROM prs_address AS ca, prs_customer AS c, prs_customer_group AS cg, prs_group_lang AS cgd WHERE c.id_customer=ca.id_customer AND c.id_customer=cg.id_customer AND cg.id_group=cgd.id_group AND cgd.id_lang=1  AND c.id_customer=924  AND ca.id_address=1707  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname FROM prs_address AS ca, prs_customer AS c WHERE c.id_customer=ca.id_customer AND c.id_customer=924  AND ca.id_address=1707  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1  presta_get_currency_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.name FROM prs_currency AS ca WHERE ca.id_currency=1 
dmc_xml_order_opentrans_prod... products_query=  select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '21'

dmc_db_query-SQL= select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '21' \851 Versandkosten als Produkt : (Versand) SHOPSYSTEM= presta Versandkosten:20.000000

dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname, cgd.name AS customers_group, ca.vat_number AS vat_number FROM prs_address AS ca, prs_customer AS c, prs_customer_group AS cg, prs_group_lang AS cgd WHERE c.id_customer=ca.id_customer AND c.id_customer=cg.id_customer AND cg.id_group=cgd.id_group AND cgd.id_lang=1  AND c.id_customer=925  AND ca.id_address=1708  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1 
dmc_db_query-SQL= SELECT ca.id_address, ca.id_country, ca.id_state, ca.id_customer, ca.id_manufacturer, ca.id_supplier, ca.alias, ca.company, ca.lastname, ca.firstname, ca.address1, ca.address2, ca.postcode, ca.city, ca.other, ca.phone, ca.phone_mobile active, c.id_gender AS customers_gender, c.birthday AS customers_dob, c.email AS customers_email_address , c.lastname AS customers_lastname, c.firstname AS customers_firstname FROM prs_address AS ca, prs_customer AS c WHERE c.id_customer=ca.id_customer AND c.id_customer=925  AND ca.id_address=1708  presta_get_country_by_id fuer country_id 1 = 
dmc_db_query-SQL= SELECT ca.name FROM prs_country_lang AS ca WHERE ca.id_country=1 AND id_lang=1  Germany 
 presta_get_isocode_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.iso_code FROM prs_country AS ca WHERE ca.id_country=1  presta_get_currency_by_id fuer country_id 1 

dmc_db_query-SQL= SELECT ca.name FROM prs_currency AS ca WHERE ca.id_currency=1 
dmc_xml_order_opentrans_prod... products_query=  select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '25'

dmc_db_query-SQL= select o.id_order_detail AS orders_products_id, '1' AS allow_tax, o.product_id AS products_id, o.product_reference AS products_model, product_name AS products_name, o.unit_price_tax_incl AS products_price, o.total_price_tax_incl AS final_price, o.product_quantity AS products_quantity, (o.unit_price_tax_incl-o.unit_price_tax_excl) AS products_tax_amount, (o.id_tax_rules_group) AS products_tax_id, o.tax_rate AS tax_rate FROM prs_order_detail AS o where o.id_order = '25' \851 Versandkosten als Produkt : (Versand) SHOPSYSTEM= presta Versandkosten:20.000000
 Ende Laufzeit = 0.018388032913208

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>01001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='01001' .
Art_Update - Thursday 11 2019f April 2019 01:32:10 PM - Artikel 01001 mit ID 3787 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3787 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3787' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3787' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3787'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06001</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>32.0100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06001' .
Art_Update - Thursday 11 2019f April 2019 01:32:10 PM - Artikel 06001 mit ID 3788 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3788 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3788' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3788' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3788'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>06002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>3.8100</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='06002' .
Art_Update - Thursday 11 2019f April 2019 01:32:10 PM - Artikel 06002 mit ID 3789 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3789 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3789' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3789' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3789'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>36002</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>9.9000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='36002' .
Art_Update - Thursday 11 2019f April 2019 01:32:10 PM - Artikel 36002 mit ID 1664 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 1664 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '1664' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '1664' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '1664'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Art_Update - Thursday 11 2019f April 2019 01:32:11 PM - Artikel 77084 mit ID 3240 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3240 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3240' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3240'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Art_Update - Thursday 11 2019f April 2019 01:32:11 PM - Artikel 77087 mit ID 3243 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3243 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3243' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3243' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3243'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Art_Update - Thursday 11 2019f April 2019 01:32:11 PM - Artikel 77435 mit ID 3554 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3554 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3554' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3554' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3554'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:32 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Art_Update - Thursday 11 2019f April 2019 01:34:41 PM - Artikel 77084 mit ID 3240 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3240 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3240' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3240'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77087</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>10.5900</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77087' .
Art_Update - Thursday 11 2019f April 2019 01:34:41 PM - Artikel 77087 mit ID 3243 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3243 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3243' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3243' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3243'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77435</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.8000</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77435' .
Art_Update - Thursday 11 2019f April 2019 01:34:41 PM - Artikel 77435 mit ID 3554 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3554 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3554' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3554' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3554'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:34 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:35 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_begin</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_begin ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:35 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Art_Update</action>
    <ExportModus>QuantityOnly</ExportModus>
    <Artikel_ID>simple</Artikel_ID>
    <Artikel_Artikelnr>77084</Artikel_Artikelnr>
    <Artikel_Menge>0.00</Artikel_Menge>
    <Artikel_Preis>0.7500</Artikel_Preis>
    <Artikel_Status>1</Artikel_Status>
    <Artikel_Steuersatz>1</Artikel_Steuersatz>
    <Artikel_Lieferstatus>1</Artikel_Lieferstatus>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
dmc_get_id_by_artno-SQL= SELECT id_product as id from prs_product WHERE reference ='77084' .
Art_Update - Thursday 11 2019f April 2019 01:35:05 PM - Artikel 77084 mit ID 3240 fuer Preisupdate auf Shop  simple  existiert
Artikel ID 3240 hat neuen Bestand 0 bekommen.
dmc_sql_update_array-SQL= UPDATE prs_product SET quantity = '0', status = '1', date_upd = now() where id_product = '3240' ...Fehler: NICHT eingetragen: Unknown column 'status' in 'field list'

dmc_db_query-SQL= UPDATE prs_stock_available SET quantity=0 WHERE id_product = '3240' 
dmc_db_query-SQL= UPDATE prs_product SET location='L' WHERE id_product = '3240'  Update erfolgt 

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:35 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 13:35 Uhr

Uebergebene Daten:
  <GetDaten>
  </GetDaten>
  <PostDaten>
    <action>Status</action>
    <Status>update_artikel_details_end</Status>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 

******************dmc_status mit Status update_artikel_details_end ******************

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
11.04.2019 - 16:33 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>products_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!

***********************************************************************
************************* dmconnector Shop *****************************
***********************************************************************
22.05.2019 - 14:08 Uhr

Uebergebene Daten:
  <GetDaten>
    <action>categories_export</action>
    <step>1</step>
    <maxsteps>1</maxsteps>
  </GetDaten>
  <PostDaten>

 ********************************** 
function CheckLogin SHOPSYSTEM=presta 
FEHLER 489: *dmConnect0r*!=*dmConnect0r* dmC_01042019/ *v`´×N6Ó_*!=*dmC_01042019#* !
FEHLER 562: Anmeldung: Name/Passwort dmConnect0r, 3d0f07a3f86e9f89c85e80d15018d4b8 / dmC_01042019 / base64_decode(dmC_01042019)= v`´×N6Ó_  nicht korrekt!  
FEHLER 569: Bei presta wird auf LOGIN Daten in der configure_shop_presta.php geprueft!
