<?php

require_once ('dmc_db_functions.php');    

class dmcCustomerInvoice
{
  private $numResults=0;
  private $resArray;

  public function search($query)
  {
    $this->resArray=array("eis","wasser","dampf");
    $this->numResults=count($this->resArray);


  }
  public function getResult()
  {
    $res="";
    for ($resIndex=0; $resIndex<$this->numResults; $resIndex++)
    {
      $res.=$this->resArray[$resIndex];
    }
    return $res;
  }

  public function getNumResults()
  {
    return $this->numResults;
  }
  
  public function setInvoiceHeader()
  {
	// Check for existing invoices
	$where="(order_no=$order_no)";
	if (dmc_entry_exits('order_no', 'dmc_invoice_header', $where)) {
		$query='UPDATE '.DB_TABLE_PREFIX.'dmc_invoice_header SET value='.$Artikel_Preis.' WHERE entity_id='.$entity_id.' AND qty='.$Artikel_Preis_Ab_Menge.'';
	} else {
		$query='INSERT INTO '.DB_TABLE_PREFIX.'dmc_invoice_header (entity_id, all_groups, customer_group_id, qty, value, website_id) VALUES ('.$entity_id.',1,0,'.$Artikel_Preis_Ab_Menge.','.$Artikel_Preis.',0);';
	}
	dmc_sql_query($query);
   return true;
  }
  
  public function setInvoicePositions()
  {
    return true;
  }
}

$dmcCustomerInvoice=new dmcCustomerInvoice();
$dmcCustomerInvoice->search("nach einer guten Idee");
echo $dmcCustomerInvoice->numResults;  //Fehler: Cannot access private property
$dmcCustomerInvoice->numResults=5;
// Hier auch. Ihn das Ã„ndern zu lassen wäre gefährlich, weil getResult
// Fehler machen wrde. Der Programmierer kann getNumResults() verwenden.
echo $dmcCustomerInvoice->getResult();

?>

<?

				// Magento entity_id des Artikels ermitteln
				$entity_id = dmc_get_id_by_artno($Artikel_Artikelnr);
				
				if ($entity_id<>'') {
					//  Staffelpreis hinzfuegen, wenn noch nicht existiert, sonst UPDATE
					$where="(entity_id=$entity_id AND qty=$Artikel_Preis_Ab_Menge)";
					if (dmc_entry_exits('entity_id', 'catalog_product_entity_tier_price', $where)) {
						$query='UPDATE '.DB_TABLE_PREFIX.'catalog_product_entity_tier_price SET value='.$Artikel_Preis.' WHERE entity_id='.$entity_id.' AND qty='.$Artikel_Preis_Ab_Menge.'';
					} else {
						$query='INSERT INTO '.DB_TABLE_PREFIX.'catalog_product_entity_tier_price (entity_id, all_groups, customer_group_id, qty, value, website_id) VALUES ('.$entity_id.',1,0,'.$Artikel_Preis_Ab_Menge.','.$Artikel_Preis.',0);';
					}
					dmc_sql_query($query);
					// Update Minimum Preis
					$query='UPDATE '.DB_TABLE_PREFIX.'catalog_product_index_tier_price SET min_price='.$Artikel_Preis.' WHERE entity_id='.$entity_id.' AND min_price>'.$Artikel_Preis.'';
					dmc_sql_query($query);
					$query='UPDATE '.DB_TABLE_PREFIX.'catalog_product_index_price SET tier_price='.$Artikel_Preis.' WHERE entity_id='.$entity_id.' AND tier_price>'.$Artikel_Preis.'';
					dmc_sql_query($query);
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - 426 - ID:$entity_id Amount:$Artikel_Preis_Ab_Menge Product price:$Artikel_Preis.\n");	
				} else {
					if (DEBUGGER>=1) fwrite($dateihandle, "dmc_set_details - 428 - Product tier prices update failed: Product not exists.\n");	
				}
	
				
			} // end if if ($USE_API) 
		} // end if Staffelpreis

		
	  } // end  - dmc_set_tier_price
	  
	  	// Exportmodus categorie languages
		if ($ExportModusSpecial=='ustorelocator_location') {
		
			if (DEBUGGER>=1) fwrite($dateihandle, "Details ustorelocator_location\n");
			
			$title = $Freifeld{2};				// Store_Name
			$customers_street_address = $Freifeld{3};
			$customers_postcode = $Freifeld{4};
			$customers_city = $Freifeld{5};
			$customers_countries_iso_code = $Freifeld{6};
			$website_url = $Freifeld{7};			// URL
			$store_phone = $Freifeld{8};
			$customers_email_address = $Freifeld{9};
			$product_types = $Freifeld{10};
			$grade = $Freifeld{11};
			$brands = $Freifeld{12};
			
			// Standard
			$latitude='0.00';
			$longitude='0.00';
			$map_address=$customers_street_address.', '.$customers_postcode.' '.$customers_city;
			$address_display=$customers_street_address.', '.$customers_postcode.', '.$customers_countries_iso_code." ".$customers_city;
			
			// get Magento customer ID 
			$CustomerId=dmc_get_id_by_email($customers_email_address);	
			$brands=substr($brands,0,-1);
			if (DEBUGGER>=1 && $brands <> "") fwrite($dateihandle, "Google Infos fuer Kundenid $CustomerId mit eMail $customers_email_address und Brands $brands setzen.\n");
			
			
			// Wenn Kunde existiert, und Brands eingetragen Details zuordnen 
			if ($CustomerId<> "" && $brands<>"") {
				$where="cid=".$CustomerId;
				if (dmc_entry_exits('cid', 'ustorelocator_location', $where)) {
					// Update
					$query="UPDATE ".DB_TABLE_PREFIX."ustorelocator_location SET title='$title', address_display='$address_display', website_url='$website_url', store_phone='$store_phone', product_types='$product_types', grade='$grade', brands='$brands' WHERE cid=$CustomerId";
					dmc_sql_query($query);
				} else {
					// Insert
					dmc_sql_insert("ustorelocator_location", 
									"(title, map_address, latitude, longitude, address_display, notes, website_url, store_phone, product_types, grade, brands, cid)", 
									"('$title', '$map_address', $latitude, $longitude, '$address_display', '', '$website_url', '$store_phone', '$product_types', $grade, '$brands', $CustomerId)");
				}
									
				
			} //  endif Wenn Kunde existieren
		} // end exportmodus ustorelocator_location
		
	  
	}// end function    SetDetails
	
	
	
?>
	
	