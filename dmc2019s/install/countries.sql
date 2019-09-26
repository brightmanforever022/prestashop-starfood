-- Status:1:250:MP_0:sievekingsql1:php:1.24.4::5.5.57-0+deb8u1:1:::utf8:EXTINFO
--
-- TABLE-INFO
-- TABLE|countries|250|45640|2017-09-29 15:55:51|MyISAM
-- EOF TABLE-INFO
--
-- Dump by MySQLDumper 1.24.4 (http://mysqldumper.net)
/*!40101 SET NAMES 'utf8' */;
SET FOREIGN_KEY_CHECKS=0;
-- Dump created: 2017-09-30 10:44

--
-- Create Table `countries`
--

DROP TABLE IF EXISTS `countries`;
CREATE TABLE `countries` (
  `code` char(2) CHARACTER SET utf8 NOT NULL,
  `en` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `de` varchar(100) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `es` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `fr` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `it` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `ru` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`code`),
  KEY `de` (`de`),
  KEY `en` (`en`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Data for Table `countries`
--

/*!40000 ALTER TABLE `countries` DISABLE KEYS */;
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AD','Andorra','Andorra','Andorra','ANDORRE','Andorra ','Андорра');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AE','United Arab Emirates','Vereinigte Arabische Emirate','Emiratos Árabes Unidos','ÉMIRATS ARABES UNIS','Emirati Arabi Uniti ','ОАЭ');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AF','Afghanistan','Afghanistan','Afganistán','AFGHANISTAN','Afghanistan','Афганистан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AG','Antigua and Barbuda','Antigua und Barbuda','Antigua y Barbuda','ANTIGUA-ET-BARBUDA','Antigua e Barbuda','Антигуа и Барбуда');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AI','Anguilla','Anguilla','Anguila','ANGUILLA','Anguilla','Ангилья');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AL','Albania','Albanien','Albania','ALBANIE','Albania ','Албания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AM','Armenia','Armenien','Armenia','ARMÉNIE','Armenia ','Армения');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AN','Netherlands Antilles','Niederländische Antillen','Antillas Neerlandesas','','','');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AO','Angola','Angola','Angola','ANGOLA','Angola ','Ангола');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AQ','Antarctica','Antarktis','Antártida','ANTARCTIQUE','Antartide ','Антарктида');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AR','Argentina','Argentinien','Argentina','ARGENTINE','Argentina ','Аргентина');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AS','American Samoa','Amerikanisch-Samoa','Samoa Americana','SAMOA AMÉRICAINES','Samoa Americane','Американское Самоа');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AT','Austria','Österreich','Austria','AUTRICHE','Austria','Австрия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AU','Australia','Australien','Australia','AUSTRALIE','Australia','Австралия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AW','Aruba','Aruba','Aruba','ARUBA','Aruba','Аруба');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AX','Aland Islands','Åland','Islas Áland','ÅLAND, ÎLES','Isole Åland','Аландские острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('AZ','Azerbaijan','Aserbaidschan','Azerbaiyán','AZERBAÏDJAN','Azerbaigian','Азербайджан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BA','Bosnia and Herzegovina','Bosnien und Herzegowina','Bosnia y Herzegovina','BOSNIE-HERZÉGOVINE','Bosnia ed Erzegovina','Босния и Герцеговина');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BB','Barbados','Barbados','Barbados','BARBADE','Barbados','Барбадос');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BD','Bangladesh','Bangladesch','Bangladesh','BANGLADESH','Bangladesh','Бангладеш');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BE','Belgium','Belgien','Bélgica','BELGIQUE','Belgio','Бельгия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BF','Burkina Faso','Burkina Faso','Burkina Faso','BURKINA FASO','Burkina Faso','Буркина-Фасо');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BG','Bulgaria','Bulgarien','Bulgaria','BULGARIE','Bulgaria','Болгария');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BH','Bahrain','Bahrain','Bahréin','BAHREÏN','Bahrein','Бахрейн');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BI','Burundi','Burundi','Burundi','BURUNDI','Burundi','Бурунди');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BJ','Benin','Benin','Benin','BÉNIN','Benin','Бенин');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BM','Bermuda','Bermuda','Bermudas','BERMUDES','Bermuda','Бермуды');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BN','Brunei','Brunei Darussalam','Brunéi','BRUNÉI DARUSSALAM','Brunei','Бруней');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BO','Bolivia','Bolivien','Bolivia','BOLIVIE, ÉTAT PLURINATIONAL DE','Bolivia','Боливия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BR','Brazil','Brasilien','Brasil','BRÉSIL','Brasile','Бразилия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BS','Bahamas','Bahamas','Bahamas','BAHAMAS','Bahamas','Багамы');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BT','Bhutan','Bhutan','Bhután','BHOUTAN','Bhutan','Бутан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BV','Bouvet Island','Bouvetinsel','Isla Bouvet','BOUVET, ÎLE','Isola Bouvet','Остров Буве');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BW','Botswana','Botswana','Botsuana','BOTSWANA','Botswana','Ботсвана');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BY','Belarus','Belarus (Weißrussland)','Belarús','BÉLARUS','Bielorussia','Белоруссия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BZ','Belize','Belize','Belice','BELIZE','Belize','Белиз');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CA','Canada','Kanada','Canadá','CANADA','Canada','Канада');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CC','Cocos (Keeling) Islands','Kokosinseln (Keelinginseln)','Islas Cocos','COCOS (KEELING), ÎLES','Isole Cocos (Keeling)','Кокосовые острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CD','Congo (Kinshasa)','Kongo','Congo','CONGO, LA RÉPUBLIQUE DÉMOCRATIQUE DU','RD del Congo','Демократическая Республика Конго');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CF','Central African Republic','Zentralafrikanische Republik','República Centro-Africana','CENTRAFRICAINE, RÉPUBLIQUE','Rep. Centrafricana','ЦАР');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CG','Congo (Brazzaville)','Republik Kongo','Congo','CONGO','Rep. del Congo','Республика Конго');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CH','Switzerland','Schweiz','Suiza','SUISSE','Svizzera','Швейцария');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CI','Ivory Coast','Elfenbeinküste','Costa de Marfil','CÔTE D’IVOIRE','Costa d\'Avorio','Кот-д’Ивуар');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CK','Cook Islands','Cookinseln','Islas Cook','COOK, ÎLES','Isole Cook','Острова Кука');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CL','Chile','Chile','Chile','CHILI','Cile','Чили');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CM','Cameroon','Kamerun','Camerún','CAMEROUN','Camerun','Камерун');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CN','China','China, Volksrepublik','China','CHINE','Cina','КНР (Китайская Народная Республика)');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CO','Colombia','Kolumbien','Colombia','COLOMBIE','Colombia','Колумбия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CR','Costa Rica','Costa Rica','Costa Rica','COSTA RICA','Costa Rica','Коста-Рика');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CU','Cuba','Kuba','Cuba','CUBA','Cuba','Куба');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CV','Cape Verde','Kap Verde','Cabo Verde','CABO VERDE','Capo Verde','Кабо-Верде');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CX','Christmas Island','Weihnachtsinsel','Islas Christmas','CHRISTMAS, ÎLE','Isola di Natale','Остров Рождества');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CY','Cyprus','Zypern','Chipre','CHYPRE','Cipro','Кипр');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CZ','Czech Republic','Tschechische Republik','República Checa','TCHÈQUE, RÉPUBLIQUE','Rep. Ceca','Чехия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DE','Germany','Deutschland','Alemania','ALLEMAGNE','Germania','Германия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DJ','Djibouti','Dschibuti','Yibuti','DJIBOUTI','Gibuti','Джибути');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DK','Denmark','Dänemark','Dinamarca','DANEMARK','Danimarca','Дания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DM','Dominica','Dominica','Domínica','DOMINIQUE','Dominica','Доминика');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DO','Dominican Republic','Dominikanische Republik','República Dominicana','DOMINICAINE, RÉPUBLIQUE','Rep. Dominicana','Доминиканская Республика');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('DZ','Algeria','Algerien','Argelia','ALGÉRIE','Algeria','Алжир');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('EC','Ecuador','Ecuador','Ecuador','ÉQUATEUR','Ecuador','Эквадор');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('EE','Estonia','Estland (Reval)','Estonia','ESTONIE','Estonia','Эстония');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('EG','Egypt','Ägypten','Egipto','ÉGYPTE','Egitto','Египет');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('EH','Western Sahara','Westsahara','Sahara Occidental','SAHARA OCCIDENTAL','Sahara Occidentale','САДР');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ER','Eritrea','Eritrea','Eritrea','ÉRYTHRÉE','Eritrea','Эритрея');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ES','Spain','Spanien','España','ESPAGNE','Spagna','Испания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ET','Ethiopia','Äthiopien','Etiopía','ÉTHIOPIE','Etiopia','Эфиопия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FI','Finland','Finnland','Finlandia','FINLANDE','Finlandia','Финляндия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FJ','Fiji','Fidschi','Fiji','FIDJI','Figi','Фиджи');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FK','Falkland Islands','Falklandinseln (Malwinen)','Islas Malvinas','FALKLAND, ÎLES (MALVINAS)','Isole Falkland','Фолклендские острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FM','Micronesia','Mikronesien','Micronesia','MICRONÉSIE, ÉTATS FÉDÉRÉS DE','Micronesia','Микронезия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FO','Faroe Islands','Färöer','Islas Faroe','FÉROÉ, ÎLES','Fær Øer','Фареры');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('FR','France','Frankreich','Francia','FRANCE','Francia','Франция');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GA','Gabon','Gabun','Gabón','GABON','Gabon','Габон');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GB','United Kingdom','Großbritannien und Nordirland','Reino Unido','ROYAUME-UNI','Regno Unito','Великобритания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GD','Grenada','Grenada','Granada','GRENADE','Grenada','Гренада');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GE','Georgia','Georgien','Georgia','GÉORGIE','Georgia','Грузия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GF','French Guiana','Französisch-Guayana','Guayana Francesa','GUYANE FRANÇAISE','Guyana francese','Гвиана');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GG','Guernsey','Guernsey (Kanalinsel)','Guernsey','GUERNESEY','Guernsey','Гернси');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GH','Ghana','Ghana','Ghana','GHANA','Ghana','Гана');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GI','Gibraltar','Gibraltar','Gibraltar','GIBRALTAR','Gibilterra','Гибралтар');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GL','Greenland','Grönland','Groenlandia','GROENLAND','Groenlandia','Гренландия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GM','Gambia','Gambia','Gambia','GAMBIE','Gambia','Гамбия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GN','Guinea','Guinea','Guinea','GUINÉE','Guinea','Гвинея');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GP','Guadeloupe','Guadeloupe','Guadalupe','GUADELOUPE','Guadalupa','Гваделупа');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GQ','Equatorial Guinea','Äquatorialguinea','Guinea Ecuatorial','GUINÉE ÉQUATORIALE','Guinea Equatoriale','Экваториальная Гвинея');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GR','Greece','Griechenland','Grecia','GRÈCE','Grecia ','Греция');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GS','South Georgia and the South Sandwich Islands','Südgeorgien und die Südl. Sandwichinseln','Georgia del Sur e Islas Sandwich del Sur','GÉORGIE DU SUD ET LES ÎLES SANDWICH DU SUD','Georgia del Sud e isole Sandwich meridionali','Южная Георгия и Южные Сандвичевы Острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GT','Guatemala','Guatemala','Guatemala','GUATEMALA','Guatemala','Гватемала');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GU','Guam','Guam','Guam','GUAM','Guam','Гуам');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GW','Guinea-Bissau','Guinea-Bissau','Guinea-Bissau','GUINÉE-BISSAU','Guinea-Bissau','Гвинея-Бисау');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('GY','Guyana','Guyana','Guayana','GUYANA','Guyana','Гайана');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HK','Hong Kong S.A.R., China','Hongkong','Hong Kong','HONG KONG','Hong Kong','Гонконг');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HM','Heard Island and McDonald Islands','Heard- und McDonald-Inseln','Islas Heard y McDonald','HEARD ET MACDONALD, ÎLES','Isole Heard e McDonald','Херд и Макдональд');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HN','Honduras','Honduras','Honduras','HONDURAS','Honduras','Гондурас');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HR','Croatia','Kroatien','Croacia','CROATIE','Croazia','Хорватия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HT','Haiti','Haiti','Haití','HAÏTI','Haiti ','Гаити');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('HU','Hungary','Ungarn','Hungría','HONGRIE','Ungheria','Венгрия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ID','Indonesia','Indonesien','Indonesia','INDONÉSIE','Indonesia','Индонезия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IE','Ireland','Irland','Irlanda','IRLANDE','Irlanda ','Флаг Ирландии Ирландия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IL','Israel','Israel','Israel','ISRAËL','Israele ','Израиль');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IM','Isle of Man','Insel Man','Isla de Man','ÎLE DE MAN','Isola di Man','Остров Мэн');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IN','India','Indien','India','INDE','India ','Индия Индия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IO','British Indian Ocean Territory','Britisches Territorium im Indischen Ozean','Territorio Británico del Océano Índico','OCÉAN INDIEN, TERRITOIRE BRITANNIQUE DE L\'','Territorio britannico dell\'oceano','Британская территория в Индийском океане');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IQ','Iraq','Irak','Irak','IRAQ','Iraq ','Ирак');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IR','Iran','Iran','Irán','IRAN, RÉPUBLIQUE ISLAMIQUE D\'','Iran ','Иран');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IS','Iceland','Island','Islandia','ISLANDE','Islanda ','Исландия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('IT','Italy','Italien','Italia','ITALIE','Italia ','Италия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('JE','Jersey','Jersey (Kanalinsel)','Jersey','JERSEY','Jersey ','Джерси');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('JM','Jamaica','Jamaika','Jamaica','JAMAÏQUE','Giamaica','Ямайка');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('JO','Jordan','Jordanien','Jordania','JORDANIE','Giordania ','Иордания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('JP','Japan','Japan','Japón','JAPON','Giappone ','Япония');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KE','Kenya','Kenia','Kenia','KENYA','Kenya ','Кения');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KG','Kyrgyzstan','Kirgisistan','Kirguistán','KIRGHIZISTAN','Kirghizistan','Киргизия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KH','Cambodia','Kambodscha','Camboya','CAMBODGE','Cambogia ','Камбоджа');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KI','Kiribati','Kiribati','Kiribati','KIRIBATI','Kiribati ','Кирибати');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KM','Comoros','Komoren','Comoros','COMORES','Comore ','Коморы');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KN','Saint Kitts and Nevis','St. Kitts und Nevis','San Cristóbal y Nieves','SAINT-KITTS-ET-NEVIS','Saint Kitts e Nevis','Сент-Китс и Невис');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KP','North Korea','Nordkorea','Corea del Norte','CORÉE, RÉPUBLIQUE POPULAIRE DÉMOCRATIQUE DE','Corea del Nord ','КНДР (Корейская Народно-Демократическая Республика)');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KR','South Korea','Südkorea','Corea del Sur','CORÉE, RÉPUBLIQUE DE','Corea del Sud ','Республика Корея');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KW','Kuwait','Kuwait','Kuwait','KOWEÏT','Kuwait ','Кувейт');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KY','Cayman Islands','Kaimaninseln','Islas Caimán','CAÏMANES, ÎLES','Isole Cayman','Острова Кайман');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('KZ','Kazakhstan','Kasachstan','Kazajstán','KAZAKHSTAN','Kazakistan ','Казахстан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LA','Laos','Laos','Laos','LAO, RÉPUBLIQUE DÉMOCRATIQUE POPULAIRE','Laos ','Лаос');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LB','Lebanon','Libanon','Líbano','LIBAN','Libano','Ливан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LC','Saint Lucia','St. Lucia','Santa Lucía','SAINTE-LUCIE','Santa Lucia','Сент-Люсия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LI','Liechtenstein','Liechtenstein','Liechtenstein','LIECHTENSTEIN','Liechtenstein','Лихтенштейн');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LK','Sri Lanka','Sri Lanka','Sri Lanka','SRI LANKA','Sri Lanka ','Шри-Ланка');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LR','Liberia','Liberia','Liberia','LIBÉRIA','Liberia ','Либерия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LS','Lesotho','Lesotho','Lesotho','LESOTHO','Lesotho ','Лесото');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LT','Lithuania','Litauen','Lituania','LITUANIE','Lituania','Литва');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LU','Luxembourg','Luxemburg','Luxemburgo','LUXEMBOURG','Lussemburgo','Люксембург');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LV','Latvia','Lettland','Letonia','LETTONIE','Lettonia ','Латвия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('LY','Libya','Libyen','Libia','LIBYE','Libia ','Ливия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MA','Morocco','Marokko','Marruecos','MAROC','Marocco','Марокко');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MC','Monaco','Monaco','Mónaco','MONACO','Monaco ','Монако');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MD','Moldova','Moldawien','Moldova','MOLDOVA','Moldavia','Молдавия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MG','Madagascar','Madagaskar','Madagascar','MADAGASCAR','Madagascar ','Мадагаскар');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MH','Marshall Islands','Marshallinseln','Islas Marshall','MARSHALL, ÎLES','Isole Marshall','Маршалловы Острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MK','Macedonia','Mazedonien','Macedonia','MACÉDOINE, L\'EX-RÉPUBLIQUE YOUGOSLAVE DE','Macedonia ','Македония');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ML','Mali','Mali','Mali','MALI','Mali ','Мали');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MM','Myanmar','Myanmar (Burma)','Myanmar','MYANMAR','Birmania','Мьянма');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MN','Mongolia','Mongolei','Mongolia','MONGOLIE','Mongolia','Монголия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MO','Macao S.A.R., China','Macau','Macao','MACAO','Macao ','Макао');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MP','Northern Mariana Islands','Nördliche Marianen','Islas Marianas del Norte','MARIANNES DU NORD, ÎLES','Isole Marianne Settentrionali','Северные Марианские острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MQ','Martinique','Martinique','Martinica','MARTINIQUE','Martinica','Мартиника');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MR','Mauritania','Mauretanien','Mauritania','MAURITANIE','Mauritania','Мавритания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MS','Montserrat','Montserrat','Montserrat','MONTSERRAT','Montserrat','Монтсеррат');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MT','Malta','Malta','Malta','MALTE','Malta ','Мальта');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MU','Mauritius','Mauritius','Mauricio','MAURICE','Mauritius','Маврикий');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MV','Maldives','Malediven','Maldivas','MALDIVES','Maldive ','Мальдивы');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MW','Malawi','Malawi','Malawi','MALAWI','Malawi ','Малави');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MX','Mexico','Mexiko','México','MEXIQUE','Messico ','Мексика');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MY','Malaysia','Malaysia','Malasia','MALAISIE','Malesia ','Малайзия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MZ','Mozambique','Mosambik','Mozambique','MOZAMBIQUE','Mozambico','Мозамбик');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NA','Namibia','Namibia','Namibia','NAMIBIE','Namibia ','Намибия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NC','New Caledonia','Neukaledonien','Nueva Caledonia','NOUVELLE-CALÉDONIE','Nuova Caledonia','Новая Каледония');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NE','Niger','Niger','Níger','NIGER','Niger ','Нигер');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NF','Norfolk Island','Norfolkinsel','Islas Norkfolk','NORFOLK, ÎLE','Isola Norfolk','Остров Норфолк');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NG','Nigeria','Nigeria','Nigeria','NIGÉRIA','Nigeria ','Нигерия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NI','Nicaragua','Nicaragua','Nicaragua','NICARAGUA','Nicaragua','Никарагуа');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NL','Netherlands','Niederlande','Países Bajos','PAYS-BAS','Paesi Bassi','Нидерланды');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NO','Norway','Norwegen','Noruega','NORVÈGE','Norvegia ','Норвегия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NP','Nepal','Nepal','Nepal','NÉPAL','Nepal ','Непал');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NR','Nauru','Nauru','Nauru','NAURU','Nauru ','Науру');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NU','Niue','Niue','Niue','NIUÉ','Niue ','Ниуэ');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('NZ','New Zealand','Neuseeland','Nueva Zelanda','NOUVELLE-ZÉLANDE','Nuova Zelanda','Новая Зеландия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('OM','Oman','Oman','Omán','OMAN','Oman ','Оман');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PA','Panama','Panama','Panamá','PANAMA','Panamá','Панама');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PE','Peru','Peru','Perú','PÉROU','Perù ','Перу');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PF','French Polynesia','Französisch-Polynesien','Polinesia Francesa','POLYNÉSIE FRANÇAISE','Polinesia Francese ','Французская Полинезия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PG','Papua New Guinea','Papua-Neuguinea','Papúa Nueva Guinea','PAPOUASIE-NOUVELLE-GUINÉE','Papua Nuova Guinea ','Папуа — Новая Гвинея');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PH','Philippines','Philippinen','Filipinas','PHILIPPINES','Filippine ','Филиппины');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PK','Pakistan','Pakistan','Pakistán','PAKISTAN','Pakistan ','Пакистан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PL','Poland','Polen','Polonia','POLOGNE','Polonia ','Польша');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PM','Saint Pierre and Miquelon','St. Pierre und Miquelon','San Pedro y Miquelón','SAINT-PIERRE-ET-MIQUELON','Saint-Pierre e Miquelon','Сен-Пьер и Микелон');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PN','Pitcairn','Pitcairninseln','Islas Pitcairn','PITCAIRN','Isole Pitcairn ','Острова Питкэрн');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PR','Puerto Rico','Puerto Rico','Puerto Rico','PORTO RICO','Porto Rico ','Пуэрто-Рико');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PS','Palestine','Palästina','Palestina','ÉTAT DE PALESTINE','Palestina ','Государство Палестина');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PT','Portugal','Portugal','Portugal','PORTUGAL','Portogallo ','Португалия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PW','Palau','Palau','Islas Palaos','PALAOS','Palau ','Палау');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('PY','Paraguay','Paraguay','Paraguay','PARAGUAY','Paraguay ','Парагвай');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('QA','Qatar','Katar','Qatar','QATAR','Qatar ','Катар');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('RE','Reunion','Réunion','Reunión','RÉUNION','Riunione ','Реюньон');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('RO','Romania','Rumänien','Rumanía','ROUMANIE','Romania ','Румыния');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('RU','Russia','Russische Föderation','Rusia','RUSSIE, FÉDÉRATION DE','Russia ','Россия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('RW','Rwanda','Ruanda','Ruanda','RWANDA','Ruanda ','Руанда');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SA','Saudi Arabia','Saudi-Arabien','Arabia Saudita','ARABIE SAOUDITE','Arabia Saudita','Саудовская Аравия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SB','Solomon Islands','Salomonen','Islas Solomón','SALOMON, ÎLES','Isole Salomone','Соломоновы Острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SC','Seychelles','Seychellen','Seychelles','SEYCHELLES','Seychelles','Сейшельские Острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SD','Sudan','Sudan','Sudán','SOUDAN','Sudan ','Судан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SE','Sweden','Schweden','Suecia','SUÈDE','Svezia','Швеция');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SG','Singapore','Singapur','Singapur','SINGAPOUR','Singapore','Сингапур');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SH','Saint Helena','St. Helena','Santa Elena','SAINTE-HÉLÈNE, ASCENSION ET TRISTAN DA CUNHA','Sant\'Elena, Ascensione e Tristan da Cunha','Острова Святой Елены, Вознесения и Тристан-да-Кунья');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SI','Slovenia','Slowenien','Eslovenia','SLOVÉNIE','Slovenia Slovenia','Словения');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SJ','Svalbard and Jan Mayen','Svalbard und Jan Mayen','Islas Svalbard y Jan Mayen','SVALBARD ET ÎLE JAN MAYEN','Svalbard e Jan Mayen','Флаг Шпицбергена и Ян-Майена Шпицберген и Ян-Майен');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SK','Slovakia','Slowakei','Eslovaquia','SLOVAQUIE','Slovacchia ','Словакия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SL','Sierra Leone','Sierra Leone','Sierra Leona','SIERRA LEONE','Sierra Leone','Сьерра-Леоне');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SM','San Marino','San Marino','San Marino','SAINT-MARIN','San Marino ','Сан-Марино');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SN','Senegal','Senegal','Senegal','SÉNÉGAL','Senegal ','Сенегал');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SO','Somalia','Somalia','Somalia','SOMALIE','Somalia ','Сомали');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SR','Suriname','Suriname','Surinam','SURINAME','Suriname','Суринам');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ST','Sao Tome and Principe','São Tomé und Príncipe','Santo Tomé y Príncipe','SAO TOMÉ-ET-PRINCIPE','São Tomé e Príncipe','Сан-Томе и Принсипи');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SV','El Salvador','El Salvador','El Salvador','EL SALVADOR','El Salvador ','Сальвадор');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SY','Syria','Syrien','Siria','SYRIENNE, RÉPUBLIQUE ARABE','Siria ','Сирия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SZ','Swaziland','Swasiland','Suazilandia','SWAZILAND','Swaziland','Свазиленд');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TC','Turks and Caicos Islands','Turks- und Caicosinseln','Islas Turcas y Caicos','TURKS ET CAÏQUES, ÎLES','Turks e Caicos ','Тёркс и Кайкос');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TD','Chad','Tschad','Chad','TCHAD','Ciad ','Чад');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TF','French Southern Territories','Französische Süd- und Antarktisgebiete','Territorios Australes Franceses','TERRES AUSTRALES FRANÇAISES','Terre australi e antartiche francesi','Французские Южные и Антарктические Территории');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TG','Togo','Togo','Togo','TOGO','Togo ','Того');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TH','Thailand','Thailand','Tailandia','THAÏLANDE','Thailandia','Таиланд');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TJ','Tajikistan','Tadschikistan','Tayikistán','TADJIKISTAN','Tagikistan','Таджикистан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TK','Tokelau','Tokelau','Tokelau','TOKELAU','Tokelau ','Токелау');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TL','East Timor','Timor-Leste','Timor-Leste','TIMOR-LESTE','Timor Est','Восточный Тимор');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TM','Turkmenistan','Turkmenistan','Turkmenistán','TURKMÉNISTAN','Turkmenistan','Туркмения');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TN','Tunisia','Tunesien','Túnez','TUNISIE','Tunisia ','Тунис');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TO','Tonga','Tonga','Tonga','TONGA','Tonga ','Тонга');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TR','Turkey','Türkei','Turquía','TURQUIE','Turchia','Турция');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TT','Trinidad and Tobago','Trinidad und Tobago','Trinidad y Tobago','TRINITÉ-ET-TOBAGO','Trinidad e Tobago','Тринидад и Тобаго');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TV','Tuvalu','Tuvalu','Tuvalu','TUVALU','Tuvalu ','Тувалу');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TW','Taiwan','Taiwan','Taiwán','TAÏWAN, PROVINCE DE CHINE','Taiwan ','Китайская Республика');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('TZ','Tanzania','Tansania','Tanzania','TANZANIE, RÉPUBLIQUE UNIE DE','Tanzania ','Танзания');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('UA','Ukraine','Ukraine','Ucrania','UKRAINE','Ucraina ','Украина');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('UG','Uganda','Uganda','Uganda','OUGANDA','Uganda ','Уганда');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('UM','United States Minor Outlying Islands','Amerikanisch-Ozeanien','Islas menores periféricas de los Estados Unidos','ÎLES MINEURES ÉLOIGNÉES DES ÉTATS-UNIS','Isole minori esterne degli Stati Uniti','Внешние малые острова (США)');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('US','United States','Vereinigte Staaten von Amerika','Estados Unidos de América','ÉTATS-UNIS','Stati Uniti','США');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('UY','Uruguay','Uruguay','Uruguay','URUGUAY','Uruguay ','Уругвай');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('UZ','Uzbekistan','Usbekistan','Uzbekistán','OUZBÉKISTAN','Uzbekistan','Узбекистан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VA','Vatican','Vatikanstadt','Ciudad del Vaticano','SAINT-SIÈGE (ÉTAT DE LA CITÉ DU VATICAN)','Città del Vaticano','Ватикан');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VC','Saint Vincent and the Grenadines','St. Vincent und die Grenadinen','San Vicente y las Granadinas','SAINT-VINCENT-ET-LES-GRENADINES','Saint Vincent e Grenadine','Сент-Винсент и Гренадины');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VE','Venezuela','Venezuela','Venezuela','VENEZUELA, RÉPUBLIQUE BOLIVARIENNE DU','Venezuela ','Венесуэла');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VG','British Virgin Islands','Britische Jungferninseln','Islas Vírgenes Británicas','ÎLES VIERGES BRITANNIQUES','Isole Vergini britanniche ','Британские Виргинские острова');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VI','U.S. Virgin Islands','Amerikanische Jungferninseln','Islas Vírgenes de los Estados Unidos de América','ÎLES VIERGES DES ÉTATS-UNIS','Isole Vergini americane ','Виргинские Острова (США)');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VN','Vietnam','Vietnam','Vietnam','VIET NAM','Vietnam','Вьетнам');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('VU','Vanuatu','Vanuatu','Vanuatu','VANUATU','Vanuatu','Вануату');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('WF','Wallis and Futuna','Wallis und Futuna','Wallis y Futuna','WALLIS-ET-FUTUNA','Wallis e Futuna','Уоллис и Футуна');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('WS','Samoa','Samoa','Samoa','SAMOA','Samoa ','Самоа');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('YE','Yemen','Jemen','Yemen','YÉMEN','Yemen ','Йемен');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('YT','Mayotte','Mayotte','Mayotte','MAYOTTE','Mayotte ','Майотта');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ZA','South Africa','Südafrika','Sudáfrica','AFRIQUE DU SUD','Sudafrica ','ЮАР');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ZM','Zambia','Sambia','Zambia','ZAMBIE','Zambia ','Замбия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ZW','Zimbabwe','Simbabwe','Zimbabue','ZIMBABWE','Zimbabwe','Зимбабве');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('RS','Serbia','Serbien','Serbia','SERBIE','Serbia ','Сербия');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('ME','Montenegro','Montenegro','Montenegro','MONTÉNÉGRO','Montenegro','Черногория');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BL','Saint Barthelemy !Saint Barthélemy','Saint-Barthélemy','Saint Barthélemy','SAINT-BARTHÉLEMY','Saint-Barthélemy','Сен-Бартелеми');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('BQ','Bonaire, Sint Eustatius and Saba','Bonaire, Sint Eustatius und Saba','Bonaire, San Eustaquio y Saba','BONAIRE, SAINT-EUSTACHE ET SABA','Isole BES','Синт-Эстатиус и Саба');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('CW','Curacao !Curaçao','Curaçao','Curaçao','CURAÇAO','Curaçao','Кюрасао');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('MF','Saint Martin (French part)','Saint-Martin (franz. Teil)','Saint Martin (parte francesa)','SAINT-MARTIN (PARTIE FRANÇAISE)','Saint-Martin','Сен-Мартен');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SX','Sint Maarten (Dutch part)','Sint Maarten (niederl. Teil)','Sint Maarten (parte neerlandesa)','SAINT-MARTIN (PARTIE NÉERLANDAISE)','Sint Maarten ','Синт-Мартен');
INSERT INTO `countries` (`code`,`en`,`de`,`es`,`fr`,`it`,`ru`) VALUES ('SS','South Sudan','Sudsudan!Südsudan','Sudán del Sur','SOUDAN DU SUD','Sudan del Sud','Южный Судан');
/*!40000 ALTER TABLE `countries` ENABLE KEYS */;

SET FOREIGN_KEY_CHECKS=1;
-- EOB

