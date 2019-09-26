<?php

// verbose errors
ini_set('display_errors', 1);
error_reporting(E_ALL);
exit;

define('_DMC_ACCESSIBLE',true);
define('_VALID_XTC',true);
define('VALID_DMC',true);

$user="dmConnectorAPIUser";
$password="1EDhNy97RrMbmbaBlWucXRg9wUV8FwGnnxIZVTQz";

	require('conf/configure_shop_shopware.php');
	include_once ('functions/shopware_api_client.php');			// Shopware API Definitions	
	include('./functions/dmc_db_functions.php');
	include('./functions/dmc_functions.php');

	echo "Willkommen auf - API_URL= ".API_URL."\n";
		$main_art_id=2;
	
	$client=dmc_get_shopware_api_client(API_URL,$user,$password); // API Verbindung zu Shopware herstellen	
echo "Verbindung hergestellt zu Client:<br>\n";
//	print_r($client);



$minimalTestArticle = array(
    'name' => 'Turnschuh',
    'active' => true,
    'tax' => 8,
    'supplier' => 'Turnschuh Inc.',
    'categories' => array(
        array('id' => 20),
    ),
    'mainDetail' => array(
        'number' => 'turn',
        'prices' => array(
            array(
                'customerGroupKey' => 'EK',
                'price' => 999,
            ),
        )
    ),
);
print_r ($client->post('articles', $minimalTestArticle));

exit;
echo "Get Artikel ID $main_art_id  <br>\n";
	$ergebnis=$client->call('articles/'.$main_art_id, ApiClient::METHODE_GET);
	echo "Ergebnis:\n";
	$ausgabe=print_r($ergebnis,true);
	var_dump($ausgabe);
	

	exit;
	
	// Variante hinzufuegen
	$varianten_artikel = Array (
		'configuratorSet' => Array
        (
            'groups' => Array
                (
                 //   '0' => 
				 Array
                        (
                            'name' => "Groesse",
                            'options' => Array
                                (
                                    '0' => Array
                                        (
                                            'name' => "43"
                                        )

                                )

                        )

                )

        ),

		/*'variants' => array(
			array(
				'isMain' => false,
				'number' => 'turn54',
				'inStock' => 15,
				'additionaltext' => 'L / Black',
				'configuratorOptions' => array(
					array('group' => 'Groesse', 'option' => '45'),
				),
				'prices' => array(
					array(
						'customerGroupKey' => 'EK',
						'price' => 1999,
					),
				) 
			)
		)
		*/
		'variants' => Array
        (
           // '0' => 
		   Array
                (
                   'isMain' => false,
                    'number' => '15786496_45',
                    'inStock' => 1,
                   // 'weight' => 0,
                    'additionaltext' => "45",
                    'configuratorOptions' => Array
                        (
                            // '0' => 
							Array
                                (
                                    'group' => "Groesse",
                                    'option' => "45"
                                )

                        ),
						'prices' => array(
							array(
								'customerGroupKey' => 'EK',
								'price' => 1999,
							),
						) 

                )

        ) 
	);
	
	echo "Variante anlegen\n";
	$result=$client->call('articles/' . $main_art_id , ApiClient::METHODE_PUT, $varianten_artikel);
						$results = print_r($result, true);
						echo "# 240 Ergebnis: $results \n";
					

	exit;

	/*
	for ($i=0;$i<count($art_id);$i++){
		//dmc_sql_select_query('id','s_categories',"metadescription like '%".$neuemetadescription."'");
		// Zusaetzliche KategorieIDs ermitteln
		$uquery="SELECT * FROM 
			s_categories WHERE ".
			" metadescription LIKE '%U".$neuemetadescription'$i'."'". 
			" OR metadescription LIKE '%O".$neuemetadescription'$i'."'"; 
			echo "uquery=".$uquery." \n";
			$j=0;
			$usql_query=dmc_db_query($uquery);
			while ($uresult = dmc_db_fetch_array($usql_query))
			{
				echo "ID=".$uresult''id''." - ";
				$sql_update_data_array''categories'''$uresult''id'''''id'' = $uresult''id'';
			}
			
			
			// Artikel update
	//		echo "\nArtikel update ".$art_id'$i'." \n";
			//var_dump($sql_update_data_array);
			$result=$client->call('articles/' . $art_id'$i' , ApiClient::METHODE_PUT, $sql_update_data_array);
//echo "ERGEBNIS:";
//var_dump($result);	
    
		$ergebnis=$client->call('articles/'.$art_id'$i', ApiClient::METHODE_GET);
	 //   var_dump($ergebnis);
		$i++;
		//if ($i==3) break;
	};
	*/
	

$minimalTestArticle = array(
    'name' => 'Turnschuh',
    'active' => true,
    'tax' => 8,
    'supplier' => 'Turnschuh Inc.',
    'categories' => array(
        array('id' => 20),
    ),
    'mainDetail' => array(
        'number' => 'turn',
        'prices' => array(
            array(
                'customerGroupKey' => 'EK',
                'price' => 999,
            ),
        )
    ),
);
print_r ($client->post('articles', $minimalTestArticle));

//print_r ($client->call('articles/2', ApiClient::METHODE_GET))

?>