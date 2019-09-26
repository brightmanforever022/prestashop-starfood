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

$has_restrictions = false;

	function getShopTree()
	{
		global $sc_agent,$has_restrictions;
		
		$tree = array();
		$sql = 'SELECT g.id_shop_group, g.name as group_name, s.id_shop, s.name as shop_name, u.id_shop_url, u.domain, u.physical_uri, u.virtual_uri
				FROM '._DB_PREFIX_.'shop_group g
				LEFT JOIN  '._DB_PREFIX_.'shop s ON g.id_shop_group = s.id_shop_group
				LEFT JOIN  '._DB_PREFIX_.'shop_url u ON u.id_shop = s.id_shop
				'.((!empty($sc_agent->id_employee))?" INNER JOIN "._DB_PREFIX_."employee_shop es ON (es.id_shop = s.id_shop AND es.id_employee = '".(int)$sc_agent->id_employee."') ":"").'
				GROUP BY  s.id_shop
				ORDER BY g.name, s.name, u.domain';
		$results_temps = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		$results = array();
		foreach($results_temps as $results_temp)
		{
			$id = trim($results_temp["id_shop"]);
			if(!empty($results_temp["id_shop"]))
				$results[] = $results_temp;
		}
		
		if(!empty($sc_agent->id_employee))
		{
			$sql = 'SELECT s.id_shop
				FROM '._DB_PREFIX_.'shop_group g
				LEFT JOIN  '._DB_PREFIX_.'shop s ON g.id_shop_group = s.id_shop_group
				LEFT JOIN  '._DB_PREFIX_.'shop_url u ON u.id_shop = s.id_shop
				GROUP BY  s.id_shop
				ORDER BY s.id_shop';
			$results_all_temps = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
			$results_all = array();
			foreach($results_all_temps as $results_all_temp)
			{
				$id = trim($results_all_temp["id_shop"]);
				if(!empty($results_all_temp["id_shop"]))
					$results_all[] = $results_all_temp;
			}
			if(count($results_all)!=count($results))
				$has_restrictions = true;
		}
		
		foreach ($results as $row)
		{
			$id_shop_group = $row['id_shop_group'];
			$id_shop = $row['id_shop'];
			$id_shop_url = $row['id_shop_url'];

			// Group list
			if (!isset($tree[$id_shop_group]))
				$tree[$id_shop_group] = array(
					'id' => $id_shop_group,
					'data' => array(
						'title' => ''._l('Group')._l(':').' '.$row['group_name'],
						'attr' => array(
							'href' => Context::getContext()->link->getAdminLink('AdminShop').'&id_shop_group='.$id_shop_group,
						)
					),
					'children' => array(),
				);

			// Shop list
			if (!$id_shop)
				continue;

			if (!isset($tree[$id_shop_group]['children'][$id_shop]))
				$tree[$id_shop_group]['children'][$id_shop] = array(
					'id' => $id_shop,
					'data' => array(
						'title' => $row['shop_name'],
						'attr' => array(
							'href' => Context::getContext()->link->getAdminLink('AdminShopUrl').'&id_shop='.$id_shop,
						)
					),
					'children' => array(),
				);
		}
	
		$icon='catalog.png';
		if(!$has_restrictions)
		{
			echo "<item select=\"1\"".
								" id=\"all\"".
								" text=\""._l('All shops')."\"".
								" im0=\"".$icon."\"".
								" im1=\"".$icon."\"".
								" im2=\"".$icon."\">\n";
		}
		$i=0;
		foreach ($tree as $groups)
		{
			
			echo " <item ".
									" id=\"G".$groups['id']."\"".
									" im0=\"".$icon."\"".
									" im1=\"".$icon."\"".
									" im2=\"".$icon."\">
									<itemtext><![CDATA[".formatText($groups['data']['title'])."]]></itemtext>\n";
			foreach ($groups['children'] as $shops)
			{
				echo "  <item ".
										" id=\"".$shops['id']."\"".
										" im0=\"".$icon."\"".
										" im1=\"".$icon."\"".
										" im2=\"".$icon."\">
										<itemtext><![CDATA[".formatText($shops['data']['title'])."]]></itemtext>\n";
				echo '</item>'."\n";
				$i++;
			}
			echo '</item>'."\n";
		}
		if(!$has_restrictions)
			echo '</item>'."\n";
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); 
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<tree id="0">';
	getShopTree();
	echo '<userdata name="has_shop_restrictions">'.intval($has_restrictions).'</userdata>'."\n";
	echo '</tree>';
