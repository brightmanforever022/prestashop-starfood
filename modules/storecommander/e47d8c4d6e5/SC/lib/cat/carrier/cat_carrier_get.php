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

$idlist=Tools::getValue('idlist',0);
$id_lang=intval(Tools::getValue('id_lang'));
$cntProducts=count(explode(',',$idlist));

	function getCarriers()
	{
		global $idlist,$id_lang, $cntProducts, $sc_agent;
		
		$multiple = false;
		if(strpos($idlist, ",") !== false)
			$multiple = true;
		
		$sql = 'SELECT c.*
				FROM `'._DB_PREFIX_.'carrier` c
				'.((SCMS && SCI::getSelectedShop())?'LEFT JOIN `'._DB_PREFIX_.'carrier_shop` cs ON (cs.`id_carrier` = c.`id_carrier` AND cs.id_shop = "'.(int)SCI::getSelectedShop().'")':'').'
				'.((SCMS && SCI::getSelectedShop()) && (!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = cs.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"").'
				WHERE c.`deleted` = "0"
				GROUP BY c.`id_carrier`
			ORDER BY c.`position` ASC';
		$carriers = Db::getInstance()->executeS($sql);

		if(!$multiple)
		{
			foreach ($carriers as $carrier)
			{
				$present = 0;
					
				$sql = 'SELECT pc.*
					FROM `'._DB_PREFIX_.'product_carrier` pc
						LEFT JOIN `'._DB_PREFIX_.'product` p ON (pc.`id_product`=p.`id_product`)
					WHERE pc.`id_carrier_reference` = "'.(int)$carrier["id_reference"].'"
						AND pc.`id_product` = '.(int)$idlist.'
						'.(SCMS?(SCI::getSelectedShop()>0?' AND pc.id_shop='.(int)SCI::getSelectedShop():' AND pc.id_shop=p.id_shop_default '):'').'';
				$tmp_present = Db::getInstance()->executeS($sql);
				if(!empty($tmp_present))
					$present = 1;
					
				if($carrier['name']=="0")
					$carrier['name'] = Configuration::get('PS_SHOP_NAME');
					
				echo "<row id=\"".$carrier['id_reference']."\">";
				echo 		"<cell>".$carrier['id_carrier']."</cell>";
				echo 		"<cell><![CDATA[".$carrier['name']."]]></cell>";
				echo 		"<cell>".$present."</cell>";
				echo "</row>";
			}
		}
		else
		{
			foreach ($carriers as $carrier)
			{
				$color_present = "DDDDDD";
				$present = 0;
				$nb_present = 0;
				
				$ids = explode(',',$idlist);
				foreach($ids as $id)
				{
					$sql2 = 'SELECT pc.id_carrier_reference
					FROM `'._DB_PREFIX_.'product_carrier` pc
						LEFT JOIN `'._DB_PREFIX_.'product` p ON (pc.`id_product`=p.`id_product`)
					WHERE pc.`id_carrier_reference` = "'.(int)$carrier["id_reference"].'"
						AND pc.`id_product` = '.(int)$id.'
						'.(SCMS?(SCI::getSelectedShop()>0?' AND pc.id_shop='.(int)SCI::getSelectedShop():' AND pc.id_shop=p.id_shop_default '):'').'';
					$res2 = Db::getInstance()->ExecuteS($sql2);
					if(!empty($res2[0]["id_carrier_reference"]))
						$nb_present+=1;
				}
				
				if($nb_present==$cntProducts)
				{
					$present = 1;
					$color_present = "7777AA";
				}
				elseif($nb_present<$cntProducts && $nb_present>0)
				{
					$color_present = "777777";
				}
					
				if($carrier['name']=="0")
					$carrier['name'] = Configuration::get('PS_SHOP_NAME');
					
				echo "<row id=\"".$carrier['id_reference']."\">";
				echo 		"<cell>".$carrier['id_reference']."</cell>";
				echo 		"<cell><![CDATA[".$carrier['name']."]]></cell>";
				echo 		"<cell style=\"background-color:".((!empty($color_present))?"#".$color_present:"")."\">".$present."</cell>";
				echo "</row>";
			}
		}
		
	}

	if(stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml")){
	 		header("Content-type: application/xhtml+xml");
	}else{
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
?>
<rows>
<head>
<beforeInit>
<call command="attachHeader"><param><![CDATA[#text_filter,#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="40" type="ro" align="right" sort="str"><?php echo _l('ID')?></column>
<column id="name" width="200" type="ro" align="left" sort="str"><?php echo _l('Carrier')?></column>
<column id="active" width="80" type="ch" align="center" sort="int"><?php echo _l('Active')?></column>
</head>
<?php
	echo '<userdata name="uisettings">'.uisettings::getSetting('cat_carrier').'</userdata>'."\n";
	getCarriers();
	//echo '</rows>';
?>
</rows>