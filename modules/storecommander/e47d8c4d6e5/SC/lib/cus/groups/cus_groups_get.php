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

	$idlist=Tools::getValue('id_customer',0);
	$id_lang=intval(Tools::getValue('id_lang'));
	$used=array();
		
	$multiple = false;
	if(strpos($idlist, ",") !== false)
		$multiple = true;
	
	$cntCustomers=count(explode(',',$idlist));
	
	function getGroups()
	{
		global $idlist,$multiple,$id_lang,$used, $cntCustomers;
		
		if(empty($idlist))
			return false;
		
		$groups = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
				SELECT DISTINCT g.`id_group`, gl.`name`
				FROM `'._DB_PREFIX_.'group` g
				LEFT JOIN `'._DB_PREFIX_.'group_lang` AS gl ON (g.`id_group` = gl.`id_group` AND gl.`id_lang` = '.(int)$id_lang.')
				'.(SCMS && SCI::getSelectedShop() ? 'INNER JOIN `'._DB_PREFIX_.'group_shop` gs ON (g.id_group=gs.id_group AND gs.id_shop='.(int)SCI::getSelectedShop().')':'').'
				ORDER BY gl.`name` ASC');
		
		//$used[$id_group] = array("défaut","couleur_défaut","présent","couleur_present");
		
		if(!$multiple)
		{
			$customer = new Customer((int)$idlist);
			foreach($groups as $group)
			{					
				$used[$group['id_group']] = array(0,"", 0, "");
				
				if($customer->id_default_group==$group["id_group"])
					$used[$group['id_group']][0] = 1;
				
				$in_group = Db::getInstance()->getRow('
				SELECT `id_group`
				FROM `'._DB_PREFIX_.'customer_group`
				WHERE id_group = "'.(int)$group["id_group"].'"
					AND id_customer = "'.(int)$idlist.'"');
				if(!empty($in_group["id_group"]))
					$used[$group['id_group']][2] = 1;
			}
		}
		else
		{
			$list = explode(",",$idlist);
			foreach($groups as $group)
			{
				$used[$group['id_group']] = array(0,"DDDDDD", 0, "DDDDDD");
				$nb_present = 0;
				$nb_default = 0;
			
				foreach($list as $customer_id)
				{
					if(!empty($customer_id))
					{
						$in_group = Db::getInstance()->getRow('
						SELECT `id_group`
						FROM `'._DB_PREFIX_.'customer_group`
						WHERE id_group = "'.(int)$group["id_group"].'"
							AND id_customer = "'.(int)$customer_id.'"');
						if(!empty($in_group["id_group"]))
							$nb_present++;
						
						$customer = new Customer((int)$customer_id);
						if($customer->id_default_group==$group["id_group"])
							$nb_default++;
					}
				}

				if($nb_default==$cntCustomers)
				{
					$used[$group['id_group']][0] = 1;
					$used[$group['id_group']][1] = "7777AA";
				}
				elseif($nb_default<$cntCustomers && $nb_default>0)
				{
					$used[$group['id_group']][1] = "777777";
				}

				if($nb_present==$cntCustomers)
				{
					$used[$group['id_group']][2] = 1;
					$used[$group['id_group']][3] = "7777AA";
				}
				elseif($nb_present<$cntCustomers && $nb_present>0)
				{
					$used[$group['id_group']][3] = "777777";
				}
			}
		}
		
		foreach($groups as $row){
			echo "<row id=\"".$row['id_group']."\">";
			echo 		"<cell><![CDATA[".$row['name']."]]></cell>";
			if (version_compare(_PS_VERSION_, '1.3.0.0', '>='))
				echo 		"<cell style=\"background-color:".((!empty($used[$row['id_group']][1]))?"#".$used[$row['id_group']][1]:"")."\">".$used[$row['id_group']][0]."</cell>";
			echo 		"<cell style=\"background-color:".((!empty($used[$row['id_group']][3]))?"#".$used[$row['id_group']][3]:"")."\">".$used[$row['id_group']][2]."</cell>";
			echo "</row>";
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
<call command="attachHeader"><param><![CDATA[#select_filter,#select_filter,#select_filter]]></param></call>
</beforeInit>
<column id="id" width="200" type="ro" align="left" sort="str"><?php echo _l('Group')?></column>
<?php if (version_compare(_PS_VERSION_, '1.3.0.0', '>=')) {  ?>
<column id="is_default" width="80" type="ra" align="center" sort="str"><?php echo _l('Default')?></column>
<?php } ?>
<column id="present" width="80" type="ch" align="center" sort="int"><?php echo _l('Present')?></column>
</head>
<?php 
	echo '<userdata name="uisettings">'.uisettings::getSetting('cus_groups').'</userdata>'."\n";
	getGroups();
?>
</rows>