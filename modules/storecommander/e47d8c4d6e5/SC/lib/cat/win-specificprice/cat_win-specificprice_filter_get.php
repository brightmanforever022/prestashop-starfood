<?php
/**
 * Store Commander
 *
 * @category administration
 * @author Store Commander - support@storecommander.com
 * @version 2015-09-15
 * @uses Prestashop modules
 * @since 2009
 * @copyright Copyright &copy; 2009-2015, Store Commander
 * @license commercial
 * All rights reserved! Copying, duplication strictly prohibited
 *
 * *****************************************
 * *           STORE COMMANDER             *
 * *   http://www.StoreCommander.com       *
 * *            V 2015-09-15               *
 * *****************************************
 *
 * Compatibility: PS version: 1.1 to 1.6.1
 *
 **/
$id_lang=(int)Tools::getValue('id_lang');
$id_shop=(int)Tools::getValue('id_shop',SCI::getSelectedShop());
$dateT=Tools::getValue('dateT',date("d/m/Y"));
$selection=Tools::getValue('selection',"");
$selection = explode(",", $selection);

/*if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	header("Content-type: application/xhtml+xml");
} else {
	header("Content-type: text/xml");
}*/
echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
echo '<tree id="0">';

$icon='catalog.png';
	/*
	 * Date
	 */
	echo '<item id="Gdat" text="'._l('Date').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" open="1">'."\n";
		echo '<item id="dat_past" text="'._l('Before').' '.$dateT.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array("dat_past",$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'></item>'."\n";
		echo '<item id="dat_present" text="'._l('On').' '.$dateT.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array("dat_present",$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'></item>'."\n";
		echo '<item id="dat_futur" text="'._l('After').' '.$dateT.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array("dat_futur",$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'></item>'."\n";
		echo '<item id="dat_unlimited" text="'._l('Unlimited').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array("dat_unlimited",$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'></item>'."\n";
	echo '</item>'."\n";
	
	/*
	 * Fournisseurs
	 */
	echo '<item id="Gsup" text="'._l('Suppliers').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'">'."\n";
		
		$query = 'SELECT s.* FROM '._DB_PREFIX_.'supplier s ';
		if(SCMS)
			$query .= ' INNER JOIN '._DB_PREFIX_.'supplier_shop ss ON (s.`id_supplier` = ss.`id_supplier` AND ss.`id_shop` = '.(int)$id_shop.') ';
		$query .= ' GROUP BY s.id_supplier ';
		$query .= ' ORDER BY s.`name` ASC ';

		$suppliers = Db::getInstance()->executeS($query);
		foreach($suppliers as $supplier)
		{
			$name = $supplier['name'];
			$name = str_replace("&", _l("and"), $name);
			$name = str_replace("<", "1", $name);
			$name = str_replace(">", "2", $name);
			$name = str_replace('"', "''", $name);
			echo '<item id="sup_'.$supplier['id_supplier'].'" text="'.$name.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array('sup_'.$supplier['id_supplier'],$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'></item>'."\n";
		}
	echo '</item>'."\n";

	/*
	 * Marques
	*/
	echo '<item id="Gman" text="'._l('Manufacturers').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'">'."\n";
	
	$query = 'SELECT s.* FROM '._DB_PREFIX_.'manufacturer s ';
	if(SCMS)
		$query .= ' INNER JOIN '._DB_PREFIX_.'manufacturer_shop ss ON (s.`id_manufacturer` = ss.`id_manufacturer` AND ss.`id_shop` = '.(int)$id_shop.') ';
	$query .= ' GROUP BY s.id_manufacturer ';
	$query .= ' ORDER BY s.`name` ASC ';
	
	$suppliers = Db::getInstance()->executeS($query);
	
	$manufacturers = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
	foreach($manufacturers as $manufacturer)
	{
		$name = $manufacturer['name'];
		$name = str_replace("&", _l("and"), $name);
		$name = str_replace("<", "1", $name);
		$name = str_replace(">", "2", $name);
		$name = str_replace('"', "''", $name);
		echo '<item id="man_'.$manufacturer['id_manufacturer'].'" text="'.$name.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array('man_'.$supplier['id_manufacturer'],$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'>'."\n";
		echo '</item>'."\n";
	}
	echo '</item>'."\n";

	/*
	 * Pays
	*/
	$countries = Db::getInstance()->ExecuteS('
	SELECT c.id_country, cl.name
	FROM `'._DB_PREFIX_.'country` c
	'.(SCMS?'LEFT JOIN `'._DB_PREFIX_.'country_shop` cs ON (cs.`id_country`= c.`id_country` AND cs.`id_shop` = '.(int)$id_shop.')':'').'
	LEFT JOIN `'._DB_PREFIX_.'country_lang` cl ON (c.`id_country` = cl.`id_country` AND cl.`id_lang` = '.(int)$id_lang.')
	INNER JOIN `'._DB_PREFIX_.'specific_price` sp ON (c.`id_country` = sp.`id_country`)
	GROUP BY c.id_country
	ORDER BY cl.name ASC');
	if(!empty($countries) && count($countries))
	{
		echo '<item id="Gcou" text="'._l('Countries').'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'">'."\n";
		foreach($countries as $country)
		{
			$name = $country['name'];
			$name = str_replace("&", _l("and"), $name);
			$name = str_replace("<", "1", $name);
			$name = str_replace(">", "2", $name);
			$name = str_replace('"', "''", $name);
			echo '<item id="cou_'.$country['id_country'].'" text="'.$name.'" im0="'.$icon.'" im1="'.$icon.'" im2="'.$icon.'" '.(sc_in_array('cou_'.$country['id_country'],$selection,"catWinSpecPriceFilterGet_selection")?'checked="1"':'').'>'."\n";
			echo '</item>'."\n";
		}
		echo '</item>'."\n";
	}
echo '</tree>';