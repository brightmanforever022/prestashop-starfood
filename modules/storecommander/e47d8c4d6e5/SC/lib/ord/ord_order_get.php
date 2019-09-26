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

	if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
	{
		$shop_id = SCI::getSelectedShop();
		$shop = new Shop($shop_id);
		$shop_group = $shop->getGroup();
	}
	
	$id_lang=intval(Tools::getValue('id_lang'));
	$view=Tools::getValue('view','grid_light');
	$period=Tools::getValue('period','3months');
	$filter_params=Tools::getValue('filter_params','');
	$status=Tools::getValue('status','');
	$statusFilter=explode(',',$status);

	$periods=array(
                                '1days'=>' AND o.date_add >= "'.pSQL(date("Y-m-d")).' 00:00:00" ',
                                '2days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 2',
                                '3days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 3',
								'5days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 5',
								'10days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 10',
								'15days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 15',
								'30days'=>' AND TO_DAYS(NOW()) - TO_DAYS(o.date_add) < 30',
								'3months'=>' AND DATE_SUB(NOW(), INTERVAL 3 MONTH) < o.date_add',
								'6months'=>' AND DATE_SUB(NOW(), INTERVAL 6 MONTH) < o.date_add',
								'1year'=>' AND DATE_SUB(NOW(), INTERVAL 1 YEAR) < o.date_add',
								'all'=>''
								);
	foreach($statusFilter AS $k => $s)
	{
		if (sc_array_key_exists($s,$periods))
			unset($statusFilter[$k]);
		if ($s == 'status')
			unset($statusFilter[$k]);
	}
	if (isset($statusFilter[0]) && $statusFilter[0]=='')
		unset($statusFilter[0]);

	$id_segment = 0;
	$id_segment_get=Tools::getValue('id_segment', 0);
	if(!empty($id_segment_get))
	{
		if(substr($id_segment_get, 0, 4)=="seg_" && SCSG)
		{
			$id_segment = intval(str_replace("seg_", "", $id_segment_get));
		}
	}

	$grids=SCI::getGridViews("order");
	sc_ext::readCustomOrdersGridsConfigXML('gridConfig');

	$cdata=(isset($_COOKIE['cg_ord_treegrid_col_'.$view])?$_COOKIE['cg_ord_treegrid_col_'.$view]:'');
	//check validity
	$check=explode(',',$cdata);
	foreach($check as $c)
		if ($c=='undefined')
		{
			$cdata='';
			break;
		}
	if ($cdata!='') $grids[$view]=$cdata;


	// get order status
	$orderStatusPS = OrderState::getOrderStates($sc_agent->id_lang);
	$orderStatus=array();
	$arrStatus=array();
	foreach($orderStatusPS AS $status)
	{
		$orderStatus[$status['id_order_state']]=$status;
		$arrStatus[$status['id_order_state']]=$status['name'];
	}

	// get order carrier
	//$orderCarrierPS = Carrier::getCarriers($sc_agent->id_lang);
	$sql = 'SELECT c.*, cl.delay
				FROM `'._DB_PREFIX_.'carrier` c
				LEFT JOIN `'._DB_PREFIX_.'carrier_lang` cl ON (c.`id_carrier` = cl.`id_carrier` AND cl.`id_lang` = '.(int)$sc_agent->id_lang.(SCMS?' AND cl.id_shop="'.(int)SCI::getSelectedShop().'"':'').')
				'.(SCMS && SCI::getSelectedShop()>0?' INNER JOIN `'._DB_PREFIX_.'carrier_shop` cs ON (cs.id_carrier=c.id_carrier AND cs.id_shop="'.(int)SCI::getSelectedShop().'")':'').'
				GROUP BY c.`id_carrier`';
	$orderCarrierPS = Db::getInstance()->executeS($sql);
	$orderCarrier=array();
	$arrCarrier = array();
	foreach($orderCarrierPS AS $carrier)
	{
		if($carrier['deleted']=="1")
			$carrier['name'] = _l("(deleted)")." ".$carrier['name'];
		$orderCarrier[$carrier['id_carrier']]=$carrier;
		$arrCarrier[$carrier['id_carrier']] = $carrier['name'];
	}

	// get order country
	$orderCountryPS = Country::getCountries($sc_agent->id_lang);
	$orderCountry=array();
	foreach($orderCountryPS AS $country)
	{
	    if(!empty($country['name']))
		    $orderCountry[$country['id_country']]=$country['name'];
	}

	// get order country state
	$orderStatePS = State::getStates($sc_agent->id_lang);
	$orderState=array(0=>array('name'=>''));
	foreach($orderStatePS AS $state)
	{
        if(!empty($state['name']))
		    $orderState[$state['id_state']]=$state;
	}

	// get order language
	$orderLanguagePS = Language::getLanguages($sc_agent->id_lang);
	$orderLanguage=array(0=>array('name'=>''));
	foreach($orderLanguagePS AS $lang)
	{
		$orderLanguage[$lang['id_lang']]=$lang;
	}

	// get order currency
	$orderCurrencyPS = Currency::getCurrencies();
	$orderCurrency=array(0=>array('name'=>''));
	foreach($orderCurrencyPS AS $cur)
	{
		$orderCurrency[$cur['id_currency']]=$cur;
	}

	// get payemnts
	$pnamelist=Db::getInstance()->executeS('
			SELECT DISTINCT o.`payment`
			FROM `'._DB_PREFIX_.'orders` o
		');
	foreach($pnamelist AS $n)
	{
		$n['payment'] = str_replace('&','-',strip_tags($n['payment']));
		$arrPayments[$n['payment']]=$n['payment'];
	}

	$cols=explode(',',$grids[$view]);

	$colSettings=array();
	$colSettings=SCI::getGridFields("order");
	sc_ext::readCustomOrdersGridsConfigXML('colSettings');

	function getColSettingsAsXML()
	{
		global $cols,$colSettings,$view;

		$uiset = uisettings::getSetting('ord_grid_'.$view);
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[2]);
		$sizes = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$sizes[$s[0]] = $s[1];
		}
		$tmp = explode('|',$uiset);
		$tmp = explode('-',$tmp[0]);
		$hidden = array();
		foreach($tmp AS $v)
		{
			$s = explode(':',$v);
			$hidden[$s[0]] = $s[1];
		}

		$xml='';
		if ($view=='grid_picking' || sc_in_array("id_order_detail", $cols,"ordGet_cols"))
			$colSettings['payment']['type']='ro';
		if ($view=='grid_picking' || sc_in_array("id_order_detail", $cols,"ordGet_cols"))
			$colSettings['status']['type']='ro';
		foreach($cols AS $id => $col)
		{
			if (!sc_array_key_exists($col,$colSettings)) continue;
			$xml.='<column id="'.$col.'"'.(sc_array_key_exists('format',$colSettings[$col])?
					' format="'.$colSettings[$col]['format'].'"':'').
					' width="'.( sc_array_key_exists($col,$sizes) ? $sizes[$col] : ($view=='grid_combination_price'&&$col=='id' ? $colSettings[$col]['width']+50:$colSettings[$col]['width'])).'"'.
					' hidden="'.( sc_array_key_exists($col,$hidden) ? $hidden[$col] : 0 ).'"'.
					' align="'.$colSettings[$col]['align'].'" 
					type="'.$colSettings[$col]['type'].'" 
					sort="'.$colSettings[$col]['sort'].'" 
					color="'.$colSettings[$col]['color'].'">'.$colSettings[$col]['text']."\n";
			if (sc_array_key_exists('options',$colSettings[$col]) && is_array($colSettings[$col]['options']) /*&& $col!="payment"*/)
			{
				foreach($colSettings[$col]['options'] AS $k => $v)
				{
					$xml.='<option value="'.str_replace('"','\'',$k).'"><![CDATA['.$v.']]></option>'."\n";
				}
			}

			$xml.='</column>'."\n";
		}
		return $xml;
	}

	function getFooterColSettings()
	{
		global $cols,$colSettings;

		$footer='';
		foreach($cols AS $id => $col)
		{
			if (sc_array_key_exists($col,$colSettings) && sc_array_key_exists('footer',$colSettings[$col]))
				$footer.=$colSettings[$col]['footer'].',';
			else
				$footer.=',';
		}
		return $footer;
	}

	function getFilterColSettings()
	{
		global $cols,$colSettings;
		
		$filters='';
		foreach($cols AS $id => $col)
		{
			if($colSettings[$col]['filter']=="na")
				$colSettings[$col]['filter'] = "";
			$filters.=$colSettings[$col]['filter'].',';
		}
		$filters=trim($filters,',');
		return $filters;
	}

	function getOrders()
	{
		global $sql,$id_segment,$periods,$period,$statusFilter,$filter_params,$orderStatus,$orderCarrier,$orderCountry,$orderState,$orderLanguage,$orderCurrency,$sc_agent,$arrManufacturers,$id_lang,$cols,$view,$colSettings,$user_lang_iso,$fields_order,$fields_customer,$fields_lang,$col,$orderrow;
		$yesno=array(0=>_l('No'),1=>_l('Yes'));
		$fields_order=array('id_customer','total_paid','payment','invoice_number','delivery_number','date_add','id_carrier','reference','id_lang','id_cart','id_currency','conversion_rate','recyclable','gift','gift_message','shipping_number','total_discounts','total_discounts_tax_incl','total_discounts_tax_excl','total_paid_tax_incl','total_paid_tax_excl','total_paid_real','total_products','total_products_wt','total_shipping','total_shipping_tax_incl','total_shipping_tax_excl','carrier_tax_rate','total_wrapping','total_wrapping_tax_incl','total_wrapping_tax_excl','invoice_date','delivery_date','valid','date_upd','id_shop');
		$fields_customer=array('firstname','lastname','email');
		if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			$fields_customer[] = 'company';
		$fields_lang=array('name');
		sc_ext::readCustomOrdersGridsConfigXML('updateSettings');
		
		if(!empty($id_segment))
			$segment = new ScSegment($id_segment);
		
		$blacklistfields=array('status','instock','pdf','msg');
		$sqlOrder='';
		$sqlProductLang='';
		
		foreach($cols as $col)
		{
			if (sc_in_array($col,$blacklistfields,"ordGet_blacklistfields")) // calculated fields
				continue;
			if (sc_in_array($col,$fields_order,"ordGet_fields_order"))
				$sqlOrder.=',o.`'.$col.'`';
			if (sc_in_array($col,$fields_customer,"ordGet_fields_customer"))
				$sqlOrder.=',c.`'.$col.'`';
			if (sc_in_array($col,$fields_lang,"ordGet_fields_lang"))
				$sqlProductLang.=',pl.`'.$col.'`';
		}
		$sqlOrder=trim($sqlOrder,',');
		if (sc_in_array('pdf',$cols,"ordGet_cols") && strpos($sqlOrder,'invoice_number')===false)
			$sqlOrder.=',o.invoice_number';
		if (sc_in_array('pdf',$cols,"ordGet_cols") && strpos($sqlOrder,'delivery_number')===false)
			$sqlOrder.=',o.delivery_number';
		$sqlProductLang=trim($sqlProductLang,',');

/*			$sql="SELECT ".$sqlProduct.$sqlProductLang.",'-' AS position".
						(_s('CAT_PROD_GRID_DISABLE_IMAGE')==0?",i.id_image":'').
						(($view=='grid_discount'||$view=='grid_large')&& version_compare(_PS_VERSION_, '1.4.0.0', '>=')&&version_compare(_PS_VERSION_, '1.5.0.0', '<')?',sp.from,sp.to,sp.reduction,sp.reduction_type,sp.id_group':'').
						(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?",ps.product_supplier_reference AS supplier_reference":'').
						(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?",sa.quantity":'');
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataSelect');
			$sql.=		" FROM "._DB_PREFIX_."product p
						LEFT JOIN "._DB_PREFIX_."product_lang pl ON (pl.id_product= p.id_product AND pl.id_lang=".intval($id_lang).(SCI::getSelectedShop()?' AND pl.id_shop='.(int)SCI::getSelectedShop():'').")".
						(_s('CAT_PROD_GRID_DISABLE_IMAGE')==0?" LEFT JOIN "._DB_PREFIX_."image i ON (i.id_product= p.id_product AND i.cover=1)":'').
						(($view=='grid_discount'||$view=='grid_large') && version_compare(_PS_VERSION_, '1.4.0.0', '>=')&&version_compare(_PS_VERSION_, '1.5.0.0', '<')?"LEFT JOIN "._DB_PREFIX_."specific_price sp ON (sp.id_product= p.id_product AND sp.id_currency=0 AND sp.id_country=0 AND sp.id_group=0)":'').
						(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?"LEFT JOIN "._DB_PREFIX_."product_supplier ps ON (ps.id_product=p.id_product AND ps.id_product_attribute=0 AND ps.id_supplier=p.id_supplier)
																														 LEFT JOIN "._DB_PREFIX_."stock_available sa ON (sa.id_product=p.id_product AND sa.id_product_attribute=0 ".($shop_group->share_stock ? "AND sa.id_shop_group=".(int)$shop_group->id." AND sa.id_shop=0":"AND sa.id_shop=".(int)$shop_id).")
								".(SCMS && SCI::getSelectedShop() > 0 ?"	LEFT JOIN "._DB_PREFIX_."supplier_shop ss ON (ss.id_supplier = ps.id_supplier AND ss.id_shop=".(int)SCI::getSelectedShop().")":"")."":'');
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataLeftJoin');
			$sql.=
						(SCMS && SCI::getSelectedShop() > 0 ? "INNER JOIN "._DB_PREFIX_."product_shop prs ON (prs.id_product=p.id_product AND prs.id_shop = (".(int)SCI::getSelectedShop()."))":'').
						" ORDER BY p.id_product DESC";*/
		if ($view=='grid_picking' || sc_in_array("id_order_detail", $cols,"ordGet_cols"))
		{
			$sql = "SELECT od.id_order_detail,o.id_order,".( SCAS ?'od.id_warehouse,w.name AS warehousename,':'')."od.product_quantity,od.product_id,od.product_reference,pl.name as product_name,
											od.product_supplier_reference,od.product_ean13,".(version_compare(_PS_VERSION_, '1.4.0.0', '>=')?"od.product_upc,":"").(version_compare(_PS_VERSION_, '1.7.0.0', '>=')?"od.product_isbn,":"").psql($sqlOrder).", IF( od.product_attribute_id > 0, CONCAT(product_id,'-',od.product_attribute_id), product_id) AS product_id, od.product_attribute_id,
											".(SCAS?" (SELECT wpl2.location FROM "._DB_PREFIX_."warehouse_product_location wpl2 WHERE wpl2.id_product=od.product_id AND wpl2.id_warehouse=od.id_warehouse LIMIT 1) AS location "
														:" p.location AS location ");
			if (sc_in_array('location_old',$cols,"ordGet_cols"))
			{
				$sql.= " , p.location AS location_old ";
			}
			if (sc_in_array('total_remaining_paid',$cols,"ordGet_cols"))
			{
				$sql.= " , (o.total_paid - o.total_paid_real) AS total_remaining_paid ";
			}
			if (version_compare(_PS_VERSION_, '1.6.1.1', '>='))
			{
				$sql.= ",od.original_wholesale_price";
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$sql.= ",o.current_state";
			}else{
				$sql.= ",oh1.id_order_state AS current_state ";
//SLOW NEW		$sql.= ",(SELECT oh.id_order_state FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1) as current_state ";
			}
			if (sc_in_array('status_date',$cols,"ordGet_cols"))
				$sql.= ",oh1.date_add AS status_date ";
//SLOW			$sql.= ",(SELECT oh.date_add FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1) as status_date ";
			if (sc_in_array('date_add',$cols,"ordGet_cols"))
				$sql.= ",oh2.date_add ";
//SLOW			$sql.= ",(SELECT oh2.date_add FROM "._DB_PREFIX_."order_history oh2 WHERE oh2.id_order=o.id_order ORDER BY oh2.id_order_history ASC LIMIT 1) as date_add ";
			
			if (sc_in_array('del_address1',$cols,"ordGet_cols") || sc_in_array('del_postcode',$cols,"ordGet_cols") || sc_in_array('del_id_country',$cols,"ordGet_cols"))
			{
				$sql.= ",ad.company AS del_company,ad.firstname AS del_firstname,ad.lastname AS del_lastname,ad.address1 AS del_address1,ad.address2 AS del_address2,ad.postcode AS del_postcode,ad.city AS del_city,ad.id_country AS del_id_country,ad.id_state AS del_id_state,ad.other AS del_other,ad.phone AS del_phone,ad.phone_mobile AS del_phone_mobile ";
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
				$sql.= ",ad.company AS company ";
			if (sc_in_array('inv_address1',$cols,"ordGet_cols") || sc_in_array('inv_company',$cols,"ordGet_cols") || sc_in_array('inv_id_country',$cols,"ordGet_cols"))
			{
				$sql.= ",adi.company AS inv_company,adi.firstname AS inv_firstname,adi.lastname AS inv_lastname,adi.address1 AS inv_address1,adi.address2 AS inv_address2,adi.postcode AS inv_postcode,adi.city AS inv_city,adi.id_country AS inv_id_country,adi.id_state AS inv_id_state,adi.other AS inv_other,adi.phone AS inv_phone,adi.phone_mobile AS inv_phone_mobile ";
			}
			if (sc_in_array('inv_vat_number',$cols,"ordGet_cols"))
			{
				$sql.= ",adi.vat_number AS inv_vat_number ";
			}
			if (sc_in_array('msg',$cols,"ordGet_cols") && version_compare(_PS_VERSION_, '1.2.0.0', '>='))
			{
				$sql.= ",(SELECT COUNT(*) FROM "._DB_PREFIX_."message m WHERE m.id_order=o.id_order AND m.private!=1) as msg_count ";
				$sql.= ",(SELECT COUNT(*) FROM "._DB_PREFIX_."message m LEFT JOIN "._DB_PREFIX_."message_readed mr ON (m.id_message = mr.id_message) WHERE m.id_order=o.id_order AND m.private!=1 AND mr.id_employee=".(int)$sc_agent->id_employee.") as msg_read ";
			}
			if (sc_in_array('instock',$cols,"ordGet_cols") || sc_in_array('product_quantity_in_stock',$cols,"ordGet_cols"))
			{
//				$sql.= ",LEAST(1,GREATEST(0 , od.product_quantity_in_stock - od.product_quantity)) AS instock,od.product_quantity_in_stock ";
//				$sql.= ",NOT(SELECT COUNT(od2.id_order_detail) FROM "._DB_PREFIX_."order_detail od2 WHERE od2.id_order_detail = od.id_order_detail AND product_quantity_in_stock < product_quantity) as instock, od.product_quantity_in_stock ";
				$sql.= ",od.product_quantity_in_stock ";
			}
			if (sc_in_array('supplier_name',$cols,"ordGet_cols"))
				$sql.= ",spl.name AS supplier_name ";
			
			if (sc_in_array('delivery_info',$cols,"ordGet_cols") || sc_in_array('delivery_date_standard',$cols,"ordGet_cols") || sc_in_array('delivery_date_limit',$cols,"ordGet_cols"))
			{
				$sql.= " ,sdi.delivery_info,sdi.date_standard AS delivery_date_standard,sdi.date_limit AS delivery_date_limit ";
			}

			$sql.= ", ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?"ps.id_category_default":"p.id_category_default")." AS id_category_default, cl.name as category_name, ".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?"ps.wholesale_price":"p.wholesale_price")." AS wholesale_price ";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$sql.= ", od.unit_price_tax_excl AS product_price ";
			else
				$sql.= ", od.product_price ";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$sql.= ", ps.id_category_default ";
			
			if (sc_in_array('quantity_physical',$cols,"ordGet_cols") || sc_in_array('quantity_usable',$cols,"ordGet_cols") || sc_in_array('quantity_real',$cols,"ordGet_cols"))
			{
				$sql.= ", ps.advanced_stock_management, od.id_warehouse ";
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array('shipping_number',$cols,"ordGet_cols"))
			{
				$sql.= ",oc.tracking_number AS shipping_number ";
			}

			if (sc_in_array('default_group',$cols,"ordGet_cols"))
			{
				$sql.= ",c.id_default_group AS default_group ";
			}
			if (sc_in_array('customer_note',$cols,"ordGet_cols"))
			{
				$sql.= ",c.note AS customer_note ";
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$sql.= " , od.id_shop ";
			}
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $sql.= ",oin.date_add AS invoice_date ";
			$sql.= ", od.sc_attr_infos_v1 AS sc_attr_infos ";
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataSelect');
			$sql.= "	FROM "._DB_PREFIX_."order_detail od
								LEFT JOIN "._DB_PREFIX_."orders o ON (o.id_order = od.id_order) 
								LEFT JOIN "._DB_PREFIX_."product p ON (od.product_id = p.id_product) ";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				$sql.= "	LEFT JOIN "._DB_PREFIX_."product_shop ps ON (ps.id_product = p.id_product AND ps.id_shop = ".(int)SCI::getSelectedShop().") ";
			if (SCAS)
				$sql.= "	LEFT JOIN "._DB_PREFIX_."warehouse w ON (od.id_warehouse = w.id_warehouse) ";
			if (sc_in_array('supplier_name',$cols,"ordGet_cols"))
				$sql.= "	LEFT JOIN "._DB_PREFIX_."supplier spl ON (p.id_supplier = spl.id_supplier) ";
			
			$sql.= "	LEFT JOIN "._DB_PREFIX_."product_lang pl ON (".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?"ps.id_product":"p.id_product")." = pl.id_product AND pl.id_lang=".intval($id_lang).(SCMS && SCI::getSelectedShop()?" AND pl.id_shop=".(int)SCI::getSelectedShop():"").") ";
			$sql.= "	LEFT JOIN "._DB_PREFIX_."category_lang cl ON (".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?"ps.id_category_default":"p.id_category_default")." = cl.id_category AND cl.id_lang=".intval($id_lang).(SCMS && SCI::getSelectedShop()?" AND cl.id_shop=".(int)SCI::getSelectedShop():"").") ";

			$sql.= "	LEFT JOIN "._DB_PREFIX_."customer c ON (o.id_customer = c.id_customer) ";
			if ((sc_in_array('del_address1',$cols,"ordGet_cols") || sc_in_array('del_postcode',$cols,"ordGet_cols") || sc_in_array('del_id_country',$cols,"ordGet_cols")) || version_compare(_PS_VERSION_, '1.5.0.0', '<'))// NEW
				$sql.= " 	LEFT JOIN "._DB_PREFIX_."address ad ON (ad.id_address = o.id_address_delivery) ";
			if (/*in_array('del_address1',$cols) ||*/sc_in_array('inv_address1',$cols,"ordGet_cols") || sc_in_array('inv_vat_number',$cols,"ordGet_cols") || sc_in_array('inv_company',$cols,"ordGet_cols") || sc_in_array('inv_id_country',$cols,"ordGet_cols"))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."address adi ON (adi.id_address = o.id_address_invoice) ";
			}
			if (sc_in_array('delivery_info',$cols,"ordGet_cols") || sc_in_array('delivery_date_standard',$cols,"ordGet_cols") || sc_in_array('delivery_date_limit',$cols,"ordGet_cols"))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."storecom_delivery_info sdi ON (sdi.id_order = o.id_order) ";
			}
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array('shipping_number',$cols,"ordGet_cols"))
            {
                $sql.= " LEFT JOIN "._DB_PREFIX_."order_carrier oc ON (oc.id_order = o.id_order) ";
            }
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql.= " LEFT JOIN "._DB_PREFIX_."order_invoice oin ON (oin.id_order = o.id_order) ";
            }
			if (sc_in_array('status_date',$cols,"ordGet_cols") || version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."order_history oh1 ON (oh1.id_order=o.id_order AND oh1.id_order_history=(SELECT MAX(oh11.id_order_history) FROM "._DB_PREFIX_."order_history oh11 WHERE oh11.id_order=oh1.id_order )) ";
			}
			if (sc_in_array('date_add',$cols,"ordGet_cols") || (empty($withSearch) && !(!empty($id_segment) && SCSG) && (!empty($period) && strpos($period, "from_to_")!==false)))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."order_history oh2 ON (oh2.id_order=o.id_order AND oh2.id_order_history=(SELECT MIN(oh21.id_order_history) FROM "._DB_PREFIX_."order_history oh21 WHERE oh21.id_order=oh2.id_order )) ";
			}
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataLeftJoin');
			$sql.= "	WHERE 1 ";
			
			$withSearch = false;
			if(!empty($filter_params))
			{
				$filters = explode(",", $filter_params);
				foreach ($filters as $filter)
				{
					list($field,$search) = explode("|||",$filter);
					if(!empty($field) && !empty($search) && sc_in_array($field, $cols,"ordGet_cols"))
					{
						if (sc_in_array($field,array('id_order'),"ordGet_id_order"))
						{
							$searched = " LIKE '".pSQL($search)."%' ";
							if(strpos($search, ">=")!==false)
								$searched = " >= '".intval($search)."' ";
							elseif(strpos($search, "<=")!==false)
								$searched = " <= '".intval($search)."' ";
							elseif(strpos($search, "<")!==false)
								$searched = " < '".intval($search)."' ";
							elseif(strpos($search, ">")!==false)
								$searched = " > '".intval($search)."' ";
							$sql .= " AND ( o.`".$field."` ".$searched.") ";
						}else{
							$sql .= " AND ( o.`".$field."` LIKE '%".pSQL($search)."%' ) ";
						}
						$withSearch = true;
					}
				}
			}
			if(empty($withSearch))
			{
				if(!empty($id_segment) && SCSG)
				{
					if($segment->type=="manual")
						$sql .= " AND od.id_order IN (SELECT id_element FROM "._DB_PREFIX_."sc_segment_element WHERE type_element='order' AND id_segment='".intval($id_segment)."')";
					elseif($segment->type=="auto")
					{
						$params = array("id_lang"=>$id_lang, "id_segment"=>$id_segment, "access"=>"orders");
						for($i=1;$i<=15;$i++)
						{
						$param=Tools::getValue('segment_params_'.$i);
								if(!empty($param))
							$params['segment_params_'.$i]=$param;
						}
						if(SCMS)
							$params['id_shop']=(int)SCI::getSelectedShop();
						$params['is_order']="0";
						$sql .= SegmentHook::hookByIdSegment("segmentAutoSqlQuery", $segment, $params);
					}
				}
				else
				{
					if(!empty($period) && strpos($period, "inv_from_to_")!==false)
					{
						$dates = str_replace("inv_from_to_", "", $period);
						$exp = explode("_", $dates);
						$from = $exp[0];
						$to = '';
						if(!empty($exp[1]))
							$to = $exp[1];
					
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							$where = "";
							if(!empty($from))
								$where .= " AND ( oi.date_add >= '".pSQL($from)." 00:00:00' ) ";
							if(!empty($to))
								$where .= " AND ( oi.date_add <= '".pSQL($to)." 23:59:59' ) ";
							$sql .= " AND o.id_order IN (SELECT oi.id_order FROM "._DB_PREFIX_."order_invoice oi WHERE 1 ".$where." ) ";
						}
						else
						{
							if(!empty($from))
								$sql .= " AND ( o.invoice_date >= '".pSQL($from)." 00:00:00' ) ";
							if(!empty($to))
								$sql .= " AND ( o.invoice_date <= '".pSQL($to)." 23:59:59' ) ";
						}
					}
					elseif(!empty($period) && strpos($period, "from_to_")!==false)
					{
						$dates = str_replace("from_to_", "", $period);
						$exp = explode("_", $dates);
						$from = $exp[0];
						$to = '';
						if(!empty($exp[1]))
							$to = $exp[1];
					
						/*if(!empty($from))
							$sql .= " AND ( o.date_add >= '".pSQL($from)." 00:00:00' ) ";
						if(!empty($to))
							$sql .= " AND ( o.date_add <= '".pSQL($to)." 23:59:59' ) ";*/
						/*if(!empty($from))
							$sql .= " AND ( (SELECT oh_f.date_add FROM "._DB_PREFIX_."order_history oh_f WHERE oh_f.id_order=o.id_order ORDER BY oh_f.id_order_history ASC LIMIT 1 ) >= '".pSQL($from)." 00:00:00' ) ";
						if(!empty($to))
							$sql .= " AND ( (SELECT oh_t.date_add FROM "._DB_PREFIX_."order_history oh_t WHERE oh_t.id_order=o.id_order ORDER BY oh_t.id_order_history ASC LIMIT 1 ) <= '".pSQL($to)." 23:59:59' ) ";*/
						if(!empty($from))
							$sql .= " AND ( oh2.date_add >= '".pSQL($from)." 00:00:00' ) ";
						if(!empty($to))
							$sql .= " AND ( oh2.date_add <= '".pSQL($to)." 23:59:59' ) ";
					}
					else
						$sql.= $periods[$period];
				}
			}
			$sql.= (SCMS && SCI::getSelectedShop() > 0 ? " AND o.id_shop = ".(int)SCI::getSelectedShop():'');

			$sql.= " GROUP BY od.id_order_detail";
			$sql.= " ORDER BY od.id_order_detail DESC";
		}else{
			$sql = "SELECT o.id_order,".psql($sqlOrder)."";
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
                $sql.= ",o.current_state, o.id_shop ";
			}else{
				$sql.= ",oh1.id_order_state AS current_state ";
//SLOW	NEW			$sql.= ",(SELECT oh.id_order_state FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1) as current_state ";
			}
			if (sc_in_array('status_date',$cols,"ordGet_cols"))
				$sql.= ",oh1.date_add AS status_date ";
//SLOW				$sql.= ",(SELECT oh.date_add FROM "._DB_PREFIX_."order_history oh WHERE oh.id_order=o.id_order ORDER BY oh.id_order_history DESC LIMIT 1) as status_date ";
			if (sc_in_array('date_add',$cols,"ordGet_cols"))
				$sql.= ",oh2.date_add ";
//SLOW				$sql.= ",(SELECT oh2.date_add FROM "._DB_PREFIX_."order_history oh2 WHERE oh2.id_order=o.id_order ORDER BY oh2.id_order_history ASC LIMIT 1) as date_add ";

			if (sc_in_array('total_remaining_paid',$cols,"ordGet_cols"))
			{
				$sql.= " , (o.total_paid - o.total_paid_real) AS total_remaining_paid ";
			}
			if (sc_in_array('msg',$cols,"ordGet_cols") && version_compare(_PS_VERSION_, '1.2.0.0', '>='))
			{
				$sql.= ",(SELECT COUNT(*) FROM "._DB_PREFIX_."message m WHERE m.id_order=o.id_order AND m.private!=1) as msg_count ";
				$sql.= ",(SELECT COUNT(*) FROM "._DB_PREFIX_."message m LEFT JOIN "._DB_PREFIX_."message_readed mr ON (m.id_message = mr.id_message) WHERE m.id_order=o.id_order AND m.private!=1 AND mr.id_employee=".(int)$sc_agent->id_employee.") as msg_read ";
			}
			if (sc_in_array('instock',$cols,"ordGet_cols"))
			{
				$sql.= ",NOT(SELECT COUNT(id_order) FROM "._DB_PREFIX_."order_detail od WHERE od.id_order = o.id_order AND product_quantity_in_stock < product_quantity) as instock ";
			}
			if (sc_in_array('del_address1',$cols,"ordGet_cols") || sc_in_array('del_postcode',$cols,"ordGet_cols") || sc_in_array('del_id_country',$cols,"ordGet_cols"))
			{
				$sql.= ",ad.company AS del_company,ad.firstname AS del_firstname,ad.lastname AS del_lastname,ad.address1 AS del_address1,ad.address2 AS del_address2,ad.postcode AS del_postcode,ad.city AS del_city,ad.id_country AS del_id_country,ad.id_state AS del_id_state,ad.other AS del_other,ad.phone AS del_phone,ad.phone_mobile AS del_phone_mobile ";
			}
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                $sql.= ",oin.date_add AS invoice_date ";
            if (version_compare(_PS_VERSION_, '1.5.0.0', '<'))
                $sql.= ",ad.company AS company ";
			/*else
			{
				$sql.= ",ad.company AS company ";
			}*/
			if (sc_in_array('inv_address1',$cols,"ordGet_cols") || sc_in_array('inv_company',$cols,"ordGet_cols") || sc_in_array('inv_id_country',$cols,"ordGet_cols"))
			{
				$sql.= ",adi.company AS inv_company,adi.firstname AS inv_firstname,adi.lastname AS inv_lastname,adi.address1 AS inv_address1,adi.address2 AS inv_address2,adi.postcode AS inv_postcode,adi.city AS inv_city,adi.id_country AS inv_id_country,adi.id_state AS inv_id_state,adi.other AS inv_other,adi.phone AS inv_phone,adi.phone_mobile AS inv_phone_mobile ";
			}
			if (sc_in_array('inv_vat_number',$cols,"ordGet_cols"))
			{
				$sql.= ",adi.vat_number AS inv_vat_number ";
			}
			
			if (sc_in_array('default_group',$cols,"ordGet_cols"))
			{
				$sql.= ",c.id_default_group AS default_group ";
			}
			
			if (sc_in_array('delivery_info',$cols,"ordGet_cols") || sc_in_array('delivery_date_standard',$cols,"ordGet_cols") || sc_in_array('delivery_date_limit',$cols,"ordGet_cols"))
			{
				$sql.= " ,sdi.delivery_info,sdi.date_standard AS delivery_date_standard,sdi.date_limit AS delivery_date_limit ";
			}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array('shipping_number',$cols,"ordGet_cols"))
			{
				$sql.= ",oc.tracking_number AS shipping_number ";
			}
			if (sc_in_array('customer_note',$cols,"ordGet_cols"))
			{
				$sql.= ",c.note AS customer_note ";
			}
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataSelect');
			$sql.= " FROM "._DB_PREFIX_."orders o
							LEFT JOIN "._DB_PREFIX_."customer c ON (o.id_customer = c.id_customer) ";
			//if (in_array('del_address1',$cols))
			if ((sc_in_array('del_address1',$cols,"ordGet_cols") || sc_in_array('del_id_country',$cols,"ordGet_cols") || sc_in_array('del_postcode',$cols,"ordGet_cols")) || version_compare(_PS_VERSION_, '1.5.0.0', '<'))// NEW
				$sql.= " LEFT JOIN "._DB_PREFIX_."address ad ON (ad.id_address = o.id_address_delivery) ";
			if (sc_in_array('inv_address1',$cols,"ordGet_cols") || sc_in_array('inv_vat_number',$cols,"ordGet_cols") || sc_in_array('inv_company',$cols,"ordGet_cols") || sc_in_array('inv_id_country',$cols,"ordGet_cols"))// NEW
				$sql.= " LEFT JOIN "._DB_PREFIX_."address adi ON (adi.id_address = o.id_address_invoice) ";

				if (sc_in_array('delivery_info',$cols,"ordGet_cols") || sc_in_array('delivery_date_standard',$cols,"ordGet_cols") || sc_in_array('delivery_date_limit',$cols,"ordGet_cols"))
				{
					$sql.= " LEFT JOIN "._DB_PREFIX_."storecom_delivery_info sdi ON (sdi.id_order = o.id_order) ";
				}
			if (version_compare(_PS_VERSION_, '1.5.0.0', '>=') && sc_in_array('shipping_number',$cols,"ordGet_cols"))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."order_carrier oc ON (oc.id_order = o.id_order) ";
			}
            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
            {
                $sql.= " LEFT JOIN "._DB_PREFIX_."order_invoice oin ON (oin.id_order = o.id_order) ";
            }
			if (sc_in_array('status_date',$cols,"ordGet_cols") || version_compare(_PS_VERSION_, '1.5.0.0', '<'))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."order_history oh1 ON (oh1.id_order=o.id_order AND oh1.id_order_history=(SELECT MAX(oh11.id_order_history) FROM "._DB_PREFIX_."order_history oh11 WHERE oh11.id_order=oh1.id_order )) ";
			}
			if (sc_in_array('date_add',$cols,"ordGet_cols") || (empty($withSearch) && !(!empty($id_segment) && SCSG) && (!empty($period) && strpos($period, "from_to_")!==false)))
			{
				$sql.= " LEFT JOIN "._DB_PREFIX_."order_history oh2 ON (oh2.id_order=o.id_order AND oh2.id_order_history=(SELECT MIN(oh21.id_order_history) FROM "._DB_PREFIX_."order_history oh21 WHERE oh21.id_order=oh2.id_order )) ";
			}
			sc_ext::readCustomOrdersGridsConfigXML('SQLSelectDataLeftJoin');
			$sql.= " WHERE 1 ";

			$withSearch = false;
			if(!empty($filter_params))
			{
				$filters = explode(",", $filter_params);
				foreach ($filters as $filter)
				{
					list($field,$search) = explode("|||",$filter);
					if(!empty($field) && !empty($search) && sc_in_array($field, $cols,"ordGet_cols"))
					{
						if (sc_in_array($field,array('id_order'),"ordGet_id_order"))
						{
							$searched = " LIKE '".pSQL($search)."%' ";
							if(strpos($search, ">=")!==false)
								$searched = " >= '".intval($search)."' ";
							elseif(strpos($search, "<=")!==false)
								$searched = " <= '".intval($search)."' ";
							elseif(strpos($search, "<")!==false)
								$searched = " < '".intval($search)."' ";
							elseif(strpos($search, ">")!==false)
								$searched = " > '".intval($search)."' ";
							$sql .= " AND ( o.`".$field."` ".$searched.") ";
						}else{
							$sql .= " AND ( o.`".$field."` LIKE '%".pSQL($search)."%' ) ";
						}
						$withSearch = true;
					}
				}
			}
			if(empty($withSearch))
			{
				if(!empty($id_segment) && SCSG)
				{
					if($segment->type=="manual")
						$sql .= " AND o.id_order IN (SELECT id_element FROM "._DB_PREFIX_."sc_segment_element WHERE type_element='order' AND id_segment='".intval($id_segment)."')";
					elseif($segment->type=="auto")
					{
						$seg_params = unserialize($segment->auto_params);
						$params = array("id_lang"=>$id_lang, "id_segment"=>$id_segment, "access"=>"orders");
						for($i=1;$i<=15;$i++)
						{
							$param=Tools::getValue('segment_params_'.$i);
								if(!empty($param))
							$params['segment_params_'.$i]=$param;
						}
						if(SCMS)
							$params['id_shop']=(int)SCI::getSelectedShop();
							$params['is_order']="1";
						$sql .= SegmentHook::hookByIdSegment("segmentAutoSqlQuery", $segment, $params);
						if($seg_params['use_filters'] == 1) {
							if(!empty($period) && strpos($period, "inv_from_to_")!==false)
							{
								$dates = str_replace("inv_from_to_", "", $period);
								$exp = explode("_", $dates);
								$from = $exp[0];
								$to = '';
								if(!empty($exp[1]))
									$to = $exp[1];

								if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
								{
									$where = "";
									if(!empty($from))
										$where .= " AND ( oi.date_add >= '".pSQL($from)." 00:00:00' ) ";
									if(!empty($to))
										$where .= " AND ( oi.date_add <= '".pSQL($to)." 23:59:59' ) ";
									$sql .= " AND o.id_order IN (SELECT oi.id_order FROM "._DB_PREFIX_."order_invoice oi WHERE 1 ".$where." ) ";
								}
								else
								{
									if(!empty($from))
										$sql .= " AND ( o.invoice_date >= '".pSQL($from)." 00:00:00' ) ";
									if(!empty($to))
										$sql .= " AND ( o.invoice_date <= '".pSQL($to)." 23:59:59' ) ";
								}
							}
							elseif(!empty($period) && strpos($period, "from_to_")!==false)
							{
								$dates = str_replace("from_to_", "", $period);
								$exp = explode("_", $dates);
								$from = $exp[0];
								$to = '';
								if(!empty($exp[1]))
									$to = $exp[1];

								/*if(!empty($from))
                                    $sql .= " AND ( (SELECT oh_f.date_add FROM "._DB_PREFIX_."order_history oh_f WHERE oh_f.id_order=o.id_order ORDER BY oh_f.id_order_history ASC LIMIT 1 ) >= '".pSQL($from)." 00:00:00' ) ";
                                if(!empty($to))
                                    $sql .= " AND ( (SELECT oh_t.date_add FROM "._DB_PREFIX_."order_history oh_t WHERE oh_t.id_order=o.id_order ORDER BY oh_t.id_order_history ASC LIMIT 1 ) <= '".pSQL($to)." 23:59:59' ) ";*/
								if(!empty($from))
									$sql .= " AND ( oh2.date_add >= '".pSQL($from)." 00:00:00' ) ";
								if(!empty($to))
									$sql .= " AND ( oh2.date_add <= '".pSQL($to)." 23:59:59' ) ";

							}
							else
								$sql.= $periods[$period];
						}
					}
				}
				else
				{
					if(!empty($period) && strpos($period, "inv_from_to_")!==false)
					{
						$dates = str_replace("inv_from_to_", "", $period);
						$exp = explode("_", $dates);
						$from = $exp[0];
						$to = '';
						if(!empty($exp[1]))
							$to = $exp[1];
					
						if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							$where = "";
							if(!empty($from))
								$where .= " AND ( oi.date_add >= '".pSQL($from)." 00:00:00' ) ";
							if(!empty($to))
								$where .= " AND ( oi.date_add <= '".pSQL($to)." 23:59:59' ) ";
							$sql .= " AND o.id_order IN (SELECT oi.id_order FROM "._DB_PREFIX_."order_invoice oi WHERE 1 ".$where." ) ";
						}
						else
						{
							if(!empty($from))
								$sql .= " AND ( o.invoice_date >= '".pSQL($from)." 00:00:00' ) ";
							if(!empty($to))
								$sql .= " AND ( o.invoice_date <= '".pSQL($to)." 23:59:59' ) ";
						}
					}
					elseif(!empty($period) && strpos($period, "from_to_")!==false)
					{
						$dates = str_replace("from_to_", "", $period);
						$exp = explode("_", $dates);
						$from = $exp[0];
						$to = '';
						if(!empty($exp[1]))
							$to = $exp[1];
					
						/*if(!empty($from))
							$sql .= " AND ( (SELECT oh_f.date_add FROM "._DB_PREFIX_."order_history oh_f WHERE oh_f.id_order=o.id_order ORDER BY oh_f.id_order_history ASC LIMIT 1 ) >= '".pSQL($from)." 00:00:00' ) ";
						if(!empty($to))
							$sql .= " AND ( (SELECT oh_t.date_add FROM "._DB_PREFIX_."order_history oh_t WHERE oh_t.id_order=o.id_order ORDER BY oh_t.id_order_history ASC LIMIT 1 ) <= '".pSQL($to)." 23:59:59' ) ";*/
						if(!empty($from))
						 	$sql .= " AND ( oh2.date_add >= '".pSQL($from)." 00:00:00' ) ";
						if(!empty($to))
							$sql .= " AND ( oh2.date_add <= '".pSQL($to)." 23:59:59' ) ";
						
					}
					else
						$sql.= $periods[$period];
				}
			}
			
			$sql.= (SCMS && SCI::getSelectedShop() > 0 ? " AND o.id_shop = ".(int)SCI::getSelectedShop():'');
			$sql.= " GROUP BY o.id_order ";
			$sql.= " ORDER BY o.id_order DESC";
		}

		global $dd;
		$dd=$sql;
		//echo "\n\n\n".$sql."\n\n\n";
		$res=Db::getInstance()->ExecuteS($sql);

		//echo "\n\n\n".count($res)."\n\n\n";die();

		$cache_attr=array();

		foreach($res as $orderrow){
			if (count($statusFilter) && !sc_in_array($orderrow['current_state'],$statusFilter,"ordGet_statusFilter"))
				continue;

			if(!empty($orderrow["product_attribute_id"]))
			{
				$res_pa = array();
				$id_cache = $orderrow["product_attribute_id"]."_".intval($orderrow["id_warehouse"])."_".(!empty($orderrow["id_shop"])?(int)$orderrow["id_shop"]:"0");
				if(empty($orderrow["sc_attr_infos"]))
				{
					$sc_attr_infos = "";
					if(empty($cache_attr[$id_cache]))
					{
						$sql_pa= "SELECT pa.id_product_attribute,
							".(SCAS?" (SELECT wpl.location FROM "._DB_PREFIX_."warehouse_product_location wpl WHERE wpl.id_product_attribute=pa.id_product_attribute AND wpl.id_warehouse=".(int)$orderrow["id_warehouse"]." LIMIT 1) AS location, ":" pa.location AS location, ")."
							".(sc_in_array('location_old',$cols,"ordGet_cols")?" pa.location AS location_old, ":"")."
							".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" pas.wholesale_price ":" pa.wholesale_price ")."
						FROM "._DB_PREFIX_."product_attribute pa
							".(version_compare(_PS_VERSION_, '1.5.0.0', '>=')?" LEFT JOIN "._DB_PREFIX_."product_attribute_shop pas ON (pa.id_product_attribute=pas.id_product_attribute AND pas.id_shop='".(int)$orderrow["id_shop"]."') ":"")."
						WHERE pa.id_product_attribute = '".(int)$orderrow["product_attribute_id"]."'
						LIMIT 1";
						$res_temp=Db::getInstance()->ExecuteS($sql_pa);
						if(!empty($res_temp[0]))
						{
							$res_pa = $res_temp[0];
							$cache_attr[$id_cache] = $res_pa;
							$sc_attr_infos = json_encode($cache_attr[$id_cache]);
						}
					}
					else
					{
						$sc_attr_infos = json_encode($cache_attr[$id_cache]);
						$res_pa = $cache_attr[$id_cache];
					}
					if(!empty($sc_attr_infos))
					{
						$sql_attr_infos = "UPDATE "._DB_PREFIX_."order_detail SET sc_attr_infos_v1='".pSQL($sc_attr_infos)."' WHERE id_order_detail = '".(int)$orderrow["id_order_detail"]."'";
						Db::getInstance()->Execute($sql_attr_infos);
						$nb_sql++;
					}
				}
				else
				{
					if(empty($cache_attr[$id_cache]))
					{
						$cache_attr[$id_cache] = json_decode($orderrow["sc_attr_infos"]);
					}
					$res_pa = $cache_attr[$id_cache];
				}
				if(!empty($res_pa))
					foreach($res_pa as $key=>$pa_field)
						$orderrow[$key]=$pa_field;
			}

			$avanced_quantities = array("physical_quantity"=>0,"usable_quantity"=>0);
			$actual_prices = array("price_wt"=>0,"price_it"=>0,"price_reduction_wt"=>0,"price_reduction_it"=>0);
			$tax=array();
			$type_advanced_stock_management = 1;// Not Advanced Stock Management
			$is_advanced_stock_management = false;
			$has_combination = false;
			$not_in_warehouse = true;
			$without_warehouse = true;
			if ($view=='grid_picking' || sc_in_array("id_order_detail", $cols,"ordGet_cols"))
			{
				$id_prd = $orderrow['product_id'];
				$id_prd_attr = 0;
				if(strpos($orderrow['product_id'], "-")!==false)
				{
					$exp = explode("-", $orderrow['product_id']);
					$id_prd = $exp[0];
					$id_prd_attr = $exp[1];
				}

				// ACTUAL PRODUCT PRICE
				if((sc_in_array('actual_product_price_wt',$cols,"ordGet_cols") || sc_in_array('actual_product_price_it',$cols,"ordGet_cols") || sc_in_array('actual_product_price_reduction_wt',$cols,"ordGet_cols") || sc_in_array('actual_product_price_reduction_it',$cols,"ordGet_cols")))
				{
					$shop_row = 1;
					if(!empty($orderrow['id_shop']))
						$shop_row = $orderrow['id_shop'];

					if(sc_in_array('actual_product_price_reduction_wt',$cols,"ordGet_cols") || sc_in_array('actual_product_price_reduction_it',$cols,"ordGet_cols"))
						$actual_prices = SCI::getPrice($id_prd,$id_prd_attr,$shop_row,true);
					else
						$actual_prices = SCI::getPrice($id_prd,$id_prd_attr,$shop_row);

				}
				// IN STOCK
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				{
					/*$actual_quantity_in_stock = SCI::getProductQty($orderrow['product_id'], $orderrow['product_attribute_id'], (!empty($orderrow['id_warehouse'])?$orderrow['id_warehouse']:null) );
					$orderrow['instock']=($actual_quantity_in_stock >= $orderrow['product_quantity'] ? 1 : 0);*/

					$orderrow['instock']=0;
					$color_instock = "";
					$order_in_stock=($orderrow['product_quantity_in_stock'] >= $orderrow['product_quantity'] ? 1 : 0);
					if($order_in_stock==1)
						$orderrow['instock']=1;
					else
					{
						$total_qty_wanted = 0;
						if(!empty($id_prd))
						{
							$actual_quantity_in_stock = SCI::getProductQty((int)$id_prd, (int)$id_prd_attr, (!empty($orderrow['id_warehouse'])?$orderrow['id_warehouse']:null) );
							/*if($id_prd==7)
								$actual_quantity_in_stock = 1;*/
							// Dans le cas où le stock au moment de la commande
							// est négatif, il faut utiliser la différence
							// de stock pour savoir combien de produits il y a
							// actuellement par rapport au passage de la commande
							// Exemple : -15 à la commande et -10 actuellement => 5 en stock
							if($orderrow['product_quantity_in_stock']<0 && $actual_quantity_in_stock>=$orderrow['product_quantity_in_stock'])
								$actual_quantity_in_stock -= $orderrow['product_quantity_in_stock'];
							
							$sql_details = "SELECT product_quantity FROM "._DB_PREFIX_."order_detail WHERE product_id='".intval($id_prd)."' AND product_attribute_id='".intval($id_prd_attr)."'";
							$res_details=Db::getInstance()->ExecuteS($sql_details);
							foreach($res_details as $res_detail)
							{
								$total_qty_wanted += $res_detail["product_quantity"];
							}
							
							if($actual_quantity_in_stock >= $orderrow['product_quantity'])
								$orderrow['instock']=1;
							if($actual_quantity_in_stock<$total_qty_wanted && $actual_quantity_in_stock>0)
							{
								$orderrow['instock']=3;
								$color_instock = "#FF9900";
							}
						}
					}
					if($orderrow['instock']==0 && empty($color_instock))
						$color_instock = "#FF0000";
				}
				/*
				 * SCAS
				 */
				if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					if (sc_in_array('quantity_physical',$cols,"ordGet_cols") || sc_in_array('quantity_usable',$cols,"ordGet_cols") || sc_in_array('quantity_real',$cols,"ordGet_cols"))
					{
						if(SCAS)
						{
							// Produit utilise la gestion avancée
							if($orderrow['advanced_stock_management']==1)
							{
								$is_advanced_stock_management = true;
								$type_advanced_stock_management = 2;// With Advanced Stock Management
								
								// Produit est lié à l'entrepôt
								$temp_check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$orderrow['product_id'], (int)$orderrow['product_attribute_id'], (int)$orderrow['id_warehouse']);
								if(!empty($temp_check_in_warehouse))
								{
									$not_in_warehouse = false;
									$without_warehouse = false;
								}
								
								// Produit lié à au moins un entrepôt
								if($not_in_warehouse)
								{
									$query = new DbQuery();
									$query->select('wpl.id_warehouse_product_location');
									$query->from('warehouse_product_location', 'wpl');
									$query->where('wpl.id_product = '.(int)$orderrow['product_id'].'
										AND wpl.id_product_attribute = '.(int)$orderrow['product_attribute_id'].'
										AND wpl.id_warehouse != 0'
									);
									$rslt = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($query);
									if(count($rslt)>0)
										$without_warehouse = false;
								}
								
								if(!StockAvailable::dependsOnStock((int)$orderrow['product_id'], $orderrow['id_shop']))
									$type_advanced_stock_management = 3;// With Advanced Stock Management + Manual management
								
							}
							/*$combis=Db::getInstance()->ExecuteS("SELECT id_product_attribute FROM "._DB_PREFIX_."product_attribute WHERE id_product = '".(int)$orderrow['product_id']."'");
							if(!empty($combis) && count($combis)>0)
								$has_combination = true;*/
							if(!empty($orderrow['product_attribute_id']) && !$not_in_warehouse)
							{
								$query = new DbQuery();
								$query->select('st.physical_quantity');
								$query->select('st.usable_quantity');
								//$query->select('SUM(price_te * physical_quantity) as valuation');
								$query->from('stock', "st");
								$query->innerJoin("warehouse_product_location", "wpl", "(wpl.id_product = st.id_product AND wpl.id_product_attribute = st.id_product_attribute AND wpl.id_warehouse = ".(int)$orderrow['id_warehouse'].")");
								$query->where('st.id_product = '.(int)$orderrow['product_id'].'');
								$query->where('st.id_warehouse = '.(int)$orderrow['id_warehouse'].'');
								$query->where('st.id_product_attribute = '.(int)$orderrow['product_attribute_id']);
								$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
							}
							elseif(!$not_in_warehouse)
							{
								$query = new DbQuery();
								$query->select('st.physical_quantity');
								$query->select('st.usable_quantity');
								//$query->select('SUM(price_te * physical_quantity) as valuation');
								$query->from('stock', "st");
								$query->where('id_product = '.(int)$orderrow['product_id'].'');
								$query->where('id_warehouse = '.(int)$orderrow['id_warehouse'].'');
								$avanced_quantities = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($query);
							}
						}
					}
				}
				
				echo '<row id="'.$orderrow['id_order_detail'].'">';
				echo '  <userdata name="id_order">'.intval($gridrow['id_order']).'</userdata>';
			}else{
				echo '<row id="'.$orderrow['id_order'].'">';
				
				// IN STOCK
				if (version_compare(_PS_VERSION_, '1.4.0.0', '>='))
				{
					$orderrow['instock'] = 1;
					$color_instock = "";
					$sql_details = "SELECT * FROM "._DB_PREFIX_."order_detail WHERE id_order='".intval($orderrow['id_order'])."'";
					$res_details=Db::getInstance()->ExecuteS($sql_details);
					foreach($res_details as $res_detail)
					{
						$order_in_stock=($res_detail['product_quantity_in_stock'] >= $res_detail['product_quantity'] ? 1 : 0);
						if($order_in_stock==0)
						{
							$total_qty_wanted = 0;
							$id_prd = $res_detail['product_id'];
							$id_prd_attr = $res_detail['product_attribute_id'];
							if(!empty($id_prd))
							{
								$actual_quantity_in_stock = SCI::getProductQty((int)$id_prd, (int)$id_prd_attr, (!empty($orderrow['id_warehouse'])?$orderrow['id_warehouse']:null) );
								/*if($id_prd==7)
									$actual_quantity_in_stock = 1;*/
								// Dans le cas où le stock au moment de la commande
								// est négatif, il faut utiliser la différence
								// de stock pour savoir combien de produits il y a
								// actuellement par rapport au passage de la commande
								// Exemple : -15 à la commande et -10 actuellement => 5 en stock
								if($res_detail['product_quantity_in_stock']<0 && $actual_quantity_in_stock>=$res_detail['product_quantity_in_stock'])
									$actual_quantity_in_stock -= $res_detail['product_quantity_in_stock'];
							
								$sql_details = "SELECT product_quantity FROM "._DB_PREFIX_."order_detail WHERE product_id='".intval($id_prd)."' AND product_attribute_id='".intval($id_prd_attr)."'";
								$res_details=Db::getInstance()->ExecuteS($sql_details);
								foreach($res_details as $res_detail)
								{
									$total_qty_wanted += $res_detail["product_quantity"];
								}
								
								if($orderrow['instock']!=0 && $actual_quantity_in_stock<$total_qty_wanted && $actual_quantity_in_stock>0)
								{
									$orderrow['instock']=2;
									$color_instock = "#FF9900";
								}
								if($actual_quantity_in_stock<=0 || $actual_quantity_in_stock < $orderrow['product_quantity'])
									$orderrow['instock']=0;
								
							}
						}
					}
					if($orderrow['instock']==0)
						$color_instock = "#FF0000";
				}
			}
//			echo 		"<userdata name=\"id_specific_price\">".intval($user_data["id_specific_price"])."</userdata>";
			
			echo 		"<userdata name=\"open_cat_grid\">".intval($orderrow["id_category_default"])."-".$orderrow['product_id']."</userdata>";
			
			
			sc_ext::readCustomOrdersGridsConfigXML('rowUserData',$orderrow);
			foreach($cols AS $key => $col)
			{
				switch($col){
                    case'id_order':
                        echo 	"<cell>".$orderrow['id_order']."</cell>";
                        break;
					case'default_group':
						if(SCMS)
							$group = new Group((int)$orderrow['default_group'], (int)$id_lang, (int)SCI::getSelectedShop());
						else
							$group = new Group((int)$orderrow['default_group'], (int)$id_lang);
						echo 	"<cell><![CDATA[".$group->name."]]></cell>";
						break;
					case 'actual_product_price_wt':
						echo 	"<cell>".number_format($actual_prices["price_wt"], 2, '.', '')."</cell>";
						break;
					case 'actual_product_price_it':
						echo 	"<cell>".number_format($actual_prices["price_it"], 2, '.', '')."</cell>";
						break;
					case 'actual_product_price_reduction_wt':
						echo 	"<cell>".number_format($actual_prices["price_reduction_wt"], 2, '.', '')."</cell>";
						break;
					case 'actual_product_price_reduction_it':
					echo 	"<cell>".number_format($actual_prices["price_reduction_it"], 2, '.', '')."</cell>";
						break;
					case'quantity_usable':						
						$editable='';
						
						$value = $avanced_quantities['usable_quantity'];
						if($type_advanced_stock_management!=2)
							$value = "";
						
						echo 		"<cell".$editable.">".$value."</cell>";
						break;
					case'quantity_physical':
						$editable='';
						$value = $avanced_quantities['physical_quantity'];
						if($type_advanced_stock_management!=2)
							$value = "";
						
						echo 		"<cell>".$value."</cell>";
						break;
					case'quantity_real':
						$editable='';
						
						$value = SCI::getProductRealQuantities($orderrow['product_id'],
								(int)$orderrow['product_attribute_id'],
								(int)$orderrow['id_warehouse'],
								true,
								$has_combination);
						if($type_advanced_stock_management!=2)
							$value = "";
						
						echo 		"<cell>".$value."</cell>";
						break;
					case'product_quantity_in_stock':						
						echo 		"<cell>".($orderrow['product_quantity']+$orderrow['product_quantity_in_stock'])."</cell>";
						break;
					case'product_name':
					    list($id_product,$id_product_attribute) = explode('-',$orderrow['product_id']);
                        $combination_detail = null;
                        $key = null;
					    $prod = new Product($id_product);
					    $resume = $prod->getAttributesResume($id_lang);
                        if(!empty($resume)) {
                            $key = array_search((int)$id_product_attribute, array_column($resume, 'id_product_attribute'));
                            if($key >= 0) {
                                $combination_detail = $resume[$key]['attribute_designation'];
                            }
                        }
					    $prod_name = $orderrow['product_name'].(!empty($combination_detail) ? ' : '.$combination_detail : '');
						echo 		"<cell><![CDATA[".$prod_name."]]></cell>";
						break;
					case'wholesale_price':
						echo 	"<cell>".number_format($orderrow['wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '')."</cell>";
						break;
					case'original_wholesale_price':
						echo 	"<cell>".number_format($orderrow['original_wholesale_price'], (_s('CAT_PROD_WHOLESALEPRICE4DEC')?4:2), '.', '')."</cell>";
						break;
					case'product_price':
						echo 	"<cell>".number_format($orderrow['product_price'], 2, '.', '')."</cell>";
						break;
					case'msg':
						echo 	"<cell".($orderrow['msg_count']-$orderrow['msg_read'] > 0 ? ' bgColor="#FF0000"  style="color:#FFFFFF"':'')."><![CDATA[".($orderrow['msg_count']-$orderrow['msg_read'])."/".$orderrow['msg_count']."]]></cell>";
						break;
					case'pdf':
						echo 	"<cell><![CDATA[";
						if ($orderrow['invoice_number'])
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							{
								echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'index.php?controller=AdminPdf&submitAction=generateInvoicePDF&id_order='.$orderrow['id_order'].'&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/calculator.png" alt="invoice" /></a> ';
							}else{
								echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'pdf.php?id_order='.$orderrow['id_order'].'&pdf&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/calculator.png" alt="invoice" /></a> ';
							}
						if ($orderrow['delivery_number'])
							if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
							{
								echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'index.php?controller=AdminPdf&submitAction=generateDeliverySlipPDF&id_order='.$orderrow['id_order'].'&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/lorry.png" alt="Shipping" /></a> ';
							}else{
								echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'pdf.php?id_delivery='.$orderrow['delivery_number'].'&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/lorry.png" alt="Shipping" /></a> ';
							}

                        $sql_slips = "SELECT oslip.*";
                        $sql_slips.= " FROM "._DB_PREFIX_."order_slip oslip";
                        $sql_slips.= " WHERE oslip.id_order = '".(int)$orderrow['id_order']."'";
                        $sql_slips.= " ORDER BY oslip.date_add DESC";
                        $slips=Db::getInstance()->ExecuteS($sql_slips);
                        if(!empty($slips[0]['id_order_slip']))
                            if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                            {
                                echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'index.php?controller=AdminPdf&submitAction=generateOrderSlipPDF&id_order_slip='.$slips[0]['id_order_slip'].'&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/money_delete.png" alt="Slip" /></a> ';
                            }else{
                                echo '<a target="_blank" href="'.SC_PS_PATH_ADMIN_REL.'pdf.php?id_order_slip='.$slips[0]['id_order_slip'].'&token='.$sc_agent->getPSToken('AdminPdf').'"><img src="lib/img/money_delete.png" alt="Slip" /></a> ';
                            }

						echo "]]></cell>";
						break;
					case'status':
						echo 	'<cell bgColor="'.$orderStatus[$orderrow['current_state']]['color'].'" '.(SCI::getBrightness($orderStatus[$orderrow['current_state']]['color'])<128? ' style="color:#FFFFFF"':' style="color:#383838"').'><![CDATA['.($view=='grid_picking' || sc_in_array("id_order_detail", $cols,"ordGet_cols") || _r("ACT_ORD_UPDATE_STATUS")!="1" ? $orderStatus[$orderrow['current_state']]['name'] : $orderrow['current_state'])."]]></cell>";
						break;
					case'instock':
						if($orderrow['instock']==2)
							$instock = _l("Insufficient current total stock");
						elseif($orderrow['instock']==3)
							$instock = _l("Partial");
						else
							$instock = $yesno[$orderrow['instock']];
						echo 	"<cell".(!empty($color_instock)?' bgColor="'.$color_instock.'"  style="color:#FFFFFF"':'').">".$instock."</cell>";
						break;
					case'id_carrier':
						echo 	"<cell><![CDATA[".$orderrow['id_carrier']."]]></cell>";
						break;
					case'del_id_state':
						echo 	"<cell><![CDATA[".$orderState[$orderrow['del_id_state']]['name']."]]></cell>";
						break;
					case'order_weight':
						$sql_weight= " SELECT (product_quantity * product_weight) AS detail_weight";
						$sql_weight.= " FROM "._DB_PREFIX_."order_detail";
						$sql_weight.= " WHERE id_order = '".(int)$orderrow['id_order']."' ";
						$weight = 0;
						$res_weight=Db::getInstance()->ExecuteS($sql_weight);
						foreach($res_weight as $detail_weight)
						{
							$weight += floatval($detail_weight["detail_weight"]);
						}
						
						echo 	"<cell><![CDATA[".$weight."]]></cell>";
						break;
					case'total_assets':
						$total_assets = 0;
						$sql_assets= " SELECT SUM(amount) AS total_assets";
						$sql_assets.= " FROM "._DB_PREFIX_."order_slip";
						$sql_assets.= " WHERE id_order = '".(int)$orderrow['id_order']."' ";
						$res_assets=Db::getInstance()->ExecuteS($sql_assets);
						if(!empty($res_assets[0]["total_assets"]))
							$total_assets = $res_assets[0]["total_assets"];

						echo 	"<cell>".$total_assets."</cell>";
						break;
					case'total_product_quantity':
						$sql_qty= " SELECT SUM(product_quantity) AS total_product_quantity";
						$sql_qty.= " FROM "._DB_PREFIX_."order_detail";
						$sql_qty.= " WHERE id_order = '".(int)$orderrow['id_order']."' ";
						$total_product_quantity = 0;
						$res_qty=Db::getInstance()->ExecuteS($sql_qty);
						if(!empty($res_qty[0]["total_product_quantity"]))
							$total_product_quantity = ceil($res_qty[0]["total_product_quantity"]);

						echo 	"<cell>".$total_product_quantity."</cell>";
						break;
					case'actual_quantity_in_stock':
						echo 	"<cell>".SCI::getProductQty($orderrow['product_id'], $orderrow['product_attribute_id'], (!empty($orderrow['id_warehouse'])?$orderrow['id_warehouse']:null))."</cell>";
						break;
					case'id_lang':
						echo 	"<cell><![CDATA[".$orderLanguage[$orderrow['id_lang']]['name']."]]></cell>";
						break;
					case'id_currency':
						echo 	"<cell><![CDATA[".$orderCurrency[$orderrow['id_currency']]['name']."]]></cell>";
						break;
					/*case'del_id_country':
						echo 	"<cell><![CDATA[".$orderCountry[$orderrow['del_id_country']]['name']."]]></cell>";
						break;
					case'inv_id_country':
						echo 	"<cell><![CDATA[".$orderCountry[$orderrow['inv_id_country']]['name']."]]></cell>";
						break;*/
					case'inv_postcode':
						echo 	"<cell><![CDATA[".$orderrow['inv_postcode']."]]></cell>";
						break;
					case'del_postcode':
						echo 	"<cell><![CDATA[".$orderrow['del_postcode']."]]></cell>";
						break;
					case'id_warehouse':
						echo 	"<cell><![CDATA[".$orderrow['warehousename']."]]></cell>";
						break;
                    case'payment':
                        echo 	"<cell><![CDATA[".str_replace('&','-',$orderrow['payment'])."]]></cell>";
                        break;
                    case'default_category':
                        echo 	"<cell><![CDATA[".$orderrow['category_name']."]]></cell>";
                        break;
                    case'total_wholesale_price':
                        $orderrow['total_wholesale_price'] = "0";
                        if (version_compare(_PS_VERSION_, '1.5.0.0', '>='))
                        {
                            if (!empty($orderrow['product_attribute_id']))
                            {
                                $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product_attribute_shop ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_attribute_id = ps.id_product_attribute AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                $sql_wholesale .= " WHERE ps.id_shop = '" . (int)$orderrow['id_shop'] . "'";
                                $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                if (!empty($result[0]['total_wholesale_price']))
                                    $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                                else
                                {
                                    $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                    $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product_shop ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_id = ps.id_product AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                    $sql_wholesale .= " WHERE ps.id_shop = '" . (int)$orderrow['id_shop'] . "'";
                                    $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                    if (!empty($result[0]['total_wholesale_price']))
                                        $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                                }
                            }
                            else
                            {
                                $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product_shop ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_id = ps.id_product AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                $sql_wholesale .= " WHERE ps.id_shop = '" . (int)$orderrow['id_shop'] . "'";
                                $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                if (!empty($result[0]['total_wholesale_price']))
                                    $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                            }
                        }
                        else
                        {
                            if (!empty($orderrow['product_attribute_id']))
                            {
                                $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product_attribute ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_attribute_id = ps.id_product_attribute AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                if (!empty($result[0]['total_wholesale_price']))
                                    $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                                else
                                {
                                    $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                    $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_id = ps.id_product AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                    $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                    if (!empty($result[0]['total_wholesale_price']))
                                        $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                                }
                            }
                            else
                            {
                                $sql_wholesale = " SELECT SUM(ps.wholesale_price * od.product_quantity) AS total_wholesale_price";
                                $sql_wholesale .= " FROM " . _DB_PREFIX_ . "product ps
                                        INNER JOIN " . _DB_PREFIX_ . "order_detail od ON (od.product_id = ps.id_product AND od.id_order = '" . (int)$orderrow['id_order'] . "' )";
                                $result = Db::getInstance()->ExecuteS($sql_wholesale);
                                if (!empty($result[0]['total_wholesale_price']))
                                    $orderrow['total_wholesale_price'] = $result[0]['total_wholesale_price'];
                            }
                        }
                        echo 	"<cell><![CDATA[".number_format($orderrow['total_wholesale_price'], 2, '.', '')."]]></cell>";
                        break;
					case"del_other":
						echo 	"<cell><![CDATA[".str_replace(array("\n","\r"), '', $orderrow[$col])."]]></cell>";
						break;
					default:
						sc_ext::readCustomOrdersGridsConfigXML('rowData');
						if (sc_array_key_exists('buildDefaultValue',$colSettings[$col]) && $colSettings[$col]['buildDefaultValue']!='')
						{
							if ($colSettings[$col]['buildDefaultValue']=='ID')
								echo "<cell>ID".$orderrow['product_id']."</cell>";
						}else{
							if ($orderrow[$col]=='' || $orderrow[$col]===0 || $orderrow[$col]===1) // opti perf is_numeric($orderrow[$col]) ||
							{
								echo "<cell><![CDATA[".$orderrow[$col]."]]></cell>";
							}else{
								echo "<cell><![CDATA[".$orderrow[$col]."]]></cell>";
							}
						}
				}
			}
			echo "</row>\n";
		}
	}

	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml");
	} else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n");
	echo '<rows><head>';
	echo getColSettingsAsXML();
	echo '<afterInit>
					<call command="attachHeader"><param>'.getFilterColSettings().'</param></call>
					<call command="attachFooter"><param><![CDATA['.getFooterColSettings().']]></param></call>
				</afterInit>';
	echo '</head>';
	
	$uiset = uisettings::getSetting('ord_grid_'.$view);
	$tmp = explode('|',$uiset);
	$uiset = "|".$tmp[1]."||".$tmp[3];
	echo '<userdata name="uisettings">'.$uiset.'</userdata>'."\n";
	//echo '<userdata name="uisettings">'.uisettings::getSetting('ord_grid_'.$view).'</userdata>'."\n";
	
	echo '<userdata name="LIMIT_SMARTRENDERING">'.intval(_s("CAT_PROD_LIMIT_SMARTRENDERING")).'</userdata>';
	sc_ext::readCustomOrdersGridsConfigXML('gridUserData');
	echo "\n";
	getOrders();
	if (isset($_GET['DEBUG'])) echo '<az><![CDATA['.$dd.']]></az>';
	echo '</rows>';
