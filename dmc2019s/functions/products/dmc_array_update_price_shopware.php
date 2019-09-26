<?php
/****************************************************************************
*                                                                        	*
*  dmConnector for Shopware shop											*
*  dmc_array_update_price_shopware.php										*
*  inkludiert von dmc_art_update_shopware.php								*	
*  Shopware Artikel Array mit Werten fuellen								*
*  Copyright (C) 2018 DoubleM-GmbH.de										*
*                                                                       	*
*****************************************************************************/

				// Mapping Preise B2B und B2C auf Kundengruppen //						
				// Endkunden
				$sql_update_price_array['mainDetail']['prices'][0]['customerGroupKey'] = 'EK';	
				$sql_update_price_array['mainDetail']['prices'][1]['customerGroupKey'] = '9100';	
				$sql_update_price_array['mainDetail']['prices'][2]['customerGroupKey'] = '9500';	
				$sql_update_price_array['mainDetail']['prices'][3]['customerGroupKey'] = '9155';	
				$sql_update_price_array['mainDetail']['prices'][4]['customerGroupKey'] = '9220';	
				$sql_update_price_array['mainDetail']['prices'][5]['customerGroupKey'] = '9222';	
				$sql_update_price_array['mainDetail']['prices'][6]['customerGroupKey'] = '9223';	
				$sql_update_price_array['mainDetail']['prices'][7]['customerGroupKey'] = '9224';	
				$sql_update_price_array['mainDetail']['prices'][8]['customerGroupKey'] = '9800';	
				$sql_update_price_array['mainDetail']['prices'][9]['customerGroupKey'] = '9210';	
				$sql_update_price_array['mainDetail']['prices'][10]['customerGroupKey'] = '9212';	
				for ($j=0;$j<=10;$j++)
					$sql_update_price_array['mainDetail']['prices'][$j]['price'] = $Artikel_Preis;
				// B2B Deutschland
				$sql_update_price_array['mainDetail']['prices'][11]['customerGroupKey'] = 'x';	 
				$sql_update_price_array['mainDetail']['prices'][12]['customerGroupKey'] = 'xx';	 
				$sql_update_price_array['mainDetail']['prices'][13]['customerGroupKey'] = 'xxx';	 
				$sql_update_price_array['mainDetail']['prices'][14]['customerGroupKey'] = 'xxxx';	 
				$sql_update_price_array['mainDetail']['prices'][15]['customerGroupKey'] = 'xxx5';	 
				$sql_update_price_array['mainDetail']['prices'][16]['customerGroupKey'] = 'pi';	 
				$sql_update_price_array['mainDetail']['prices'][17]['customerGroupKey'] = 'po';	 
				for ($j=11;$j<=17;$j++)
					$sql_update_price_array['mainDetail']['prices'][$j]['price'] = $Artikel_Preis1;
				// B2B USA
				$sql_update_price_array['mainDetail']['prices'][18]['customerGroupKey'] = '1100';	 
				$sql_update_price_array['mainDetail']['prices'][19]['customerGroupKey'] = '1190';	 
				for ($j=18;$j<=19;$j++)
					$sql_update_price_array['mainDetail']['prices'][$j]['price'] = $Artikel_Preis2;
				// Schweiz - International
				$sql_update_price_array['mainDetail']['prices'][20]['customerGroupKey'] = '1200';	 
				$sql_update_price_array['mainDetail']['prices'][21]['customerGroupKey'] = '1250';	 
				for ($j=20;$j<=21;$j++)
					$sql_update_price_array['mainDetail']['prices'][$j]['price'] = $Artikel_Preis3;
				// USA Privatkunden
				$sql_update_price_array['mainDetail']['prices'][22]['customerGroupKey'] = '9130';	 
				$sql_update_price_array['mainDetail']['prices'][23]['customerGroupKey'] = '9300';	 
				for ($j=22;$j<=23;$j++)
					$sql_update_price_array['mainDetail']['prices'][$j]['price'] = $Artikel_Preis3;
				 
				
?>