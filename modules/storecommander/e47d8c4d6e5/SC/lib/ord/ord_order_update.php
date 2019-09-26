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
	$id_order=intval(Tools::getValue('gr_id',0));
	$isStatus = 0;

	if(isset($_POST["!nativeeditor_status"]) && trim($_POST["!nativeeditor_status"])=="updated"){

		$fields=array('payment','status','shipping_number','invoice_number','delivery_number',
			'date_add','id_carrier','reference','conversion_rate',
			'recyclable','gift','gift_message','total_discounts',
			'total_discounts_tax_incl','total_discounts_tax_excl',
			'total_paid_tax_incl','total_paid_tax_excl','total_paid_real',
			'total_products','total_products_wt','total_shipping',
			'total_shipping_tax_incl','total_shipping_tax_excl',
			'carrier_tax_rate','total_wrapping','total_wrapping_tax_incl',
			'total_wrapping_tax_excl','invoice_date','delivery_date',
			'customer_note');
		$fields_address_delivery=array('del_address1', 'del_address2','del_postcode','del_city',
			'del_id_country','del_id_state');
		$fields_address_invoice=array('inv_address1','inv_address2','inv_postcode','inv_city','inv_id_country',
			'inv_id_state');

		sc_ext::readCustomOrdersGridsConfigXML('updateSettings');
		sc_ext::readCustomOrdersGridsConfigXML('onBeforeUpdateSQL');
		$todo=array();
		$todo_payment=array();
		$todo_address_delivery = array();
		$todo_address_invoice = array();
		foreach($fields AS $field)
		{
			if (isset($_POST[$field]) && $field=='payment')
			{
				/*$module=Module::getInstanceByName(Tools::getValue($field));
				if ($module instanceof Module)
				{
					$todo[]=$field."='".psql(html_entity_decode( Tools::getValue($field)))."'";
					$todo[]="payment='".psql($module->displayName)."'";
					$todo_payment[]="payment_method='".psql($module->displayName)."'";
					addToHistory('order','modification',$field,intval($id_order),0,_DB_PREFIX_."orders",psql(Tools::getValue($field)));
				}*/
				$todo[]="payment='".psql(Tools::getValue($field))."'";
				$todo_payment[]="payment_method='".psql(Tools::getValue($field))."'";
				addToHistory('order','modification',$field,intval($id_order),0,_DB_PREFIX_."orders",psql(Tools::getValue($field)));
				continue;
			}
			if (isset($_POST[$field]) && $field=='customer_note')
			{
				$order = new Order($id_order);
				
				$customer = new Customer((int)$order->id_customer);
				$customer->note = $_POST[$field];
				$customer->save();
			}
			if (isset($_POST[$field]) && $field=='shipping_number')
			{
				if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
				{
					/*$id_order_invoice = _qgv('
						SELECT `id_order_invoice`
						FROM `'._DB_PREFIX_.'order_invoice`
						WHERE `id_order` = '.intval($id_order).'
						ORDER BY id_order_invoice ASC');
					$id_order_carrier = _qgv('
						SELECT `id_order_carrier`
						FROM `'._DB_PREFIX_.'order_carrier`
						WHERE `id_order` = '.intval($id_order).'
						  AND `id_order_invoice` = '.intval($id_order_invoice).'
						ORDER BY id_order_carrier ASC');*/
					$id_order_carrier = _qgv('
						SELECT `id_order_carrier`
						FROM `'._DB_PREFIX_.'order_carrier`
						WHERE `id_order` = '.intval($id_order).'
						ORDER BY id_order_carrier ASC');
					Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'order_carrier`
						SET tracking_number = \''.psql(html_entity_decode( Tools::getValue($field))).'\'
						WHERE id_order_carrier = '.(int)$id_order_carrier);
				}
				
				if(!empty($_POST[$field]))
				{
					$order = new Order($id_order);
					$customer = new Customer((int)$order->id_customer);
					$carrier = new Carrier((int)$order->id_carrier, $order->id_lang);
					if (Validate::isLoadedObject($customer) && Validate::isLoadedObject($carrier))
					{
						$templateVars = array(
								'{followup}' => str_replace('@', $_POST[$field], $carrier->url),
								'{firstname}' => $customer->firstname,
								'{lastname}' => $customer->lastname,
								'{id_order}' => $order->id
						);
						if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							$templateVars['{order_name}'] =  $order->getUniqReference();
							
							/*@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
									$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
									_PS_MAIL_DIR_, true, (int)$order->id_shop);*/
							if((version_compare(_PS_VERSION_, '1.6.0.0', '<') ) || (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_MAIL_METHOD")!=3))
							{
							    if(version_compare(_PS_VERSION_, '1.6.1.6', '>=') )
							        @Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
                                        $customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
                                        _PS_MAIL_DIR_, true, (int)$order->id_shop);
							    else
								    @SCI::SendMail((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
									$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
									_PS_MAIL_DIR_, true, (int)$order->id_shop);
							}
							
							//Logger::addLog("SC Order - envoie email statut : ord ".$order->id." / in_transit / ".Mail::l('Package in transit', (int)$order->id_lang)." / shop ".$order->id_shop." / ".$customer->email." ( ".$customer->firstname." ".$customer->lastname.")");
						}
						elseif(version_compare(_PS_VERSION_, '1.4.0.0', '>='))
						{
							if((version_compare(_PS_VERSION_, '1.6.0.0', '<') ) || (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_MAIL_METHOD")!=3))
							{
								@Mail::Send((int)$order->id_lang, 'in_transit', Mail::l('Package in transit', (int)$order->id_lang), $templateVars,
									$customer->email, $customer->firstname.' '.$customer->lastname, null, null, null, null,
									_PS_MAIL_DIR_, true);
							}
						}
						else
						{
							if((version_compare(_PS_VERSION_, '1.6.0.0', '<') ) || (version_compare(_PS_VERSION_, '1.6.0.0', '>=') && SCI::getConfigurationValue("PS_MAIL_METHOD")!=3))
							{
								$subject = 'Package in transit';
								@Mail::Send(intval($order->id_lang), 'in_transit', ((is_array((int)$order->id_lang) AND key_exists($subject, (int)$order->id_lang)) ? (int)$order->id_lang[$subject] : $subject), $templateVars, $customer->email, $customer->firstname.' '.$customer->lastname);
							}
						}
					}
				}
			}
			if (isset($_POST[$field]) && $field=='invoice_date' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$id_order_invoice = _qgv('
						SELECT `id_order_invoice`
						FROM `'._DB_PREFIX_.'order_invoice`
						WHERE `id_order` = '.intval($id_order).'
						ORDER BY id_order_invoice ASC');
				Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'order_invoice`
						SET date_add = \''.psql(html_entity_decode( Tools::getValue($field))).'\'
						WHERE id_order_invoice = '.(int)$id_order_invoice);
			}
			if (isset($_POST[$field]) && $field=='delivery_date' && version_compare(_PS_VERSION_, '1.5.0.0', '>='))
			{
				$id_order_invoice = _qgv('
						SELECT `id_order_invoice`
						FROM `'._DB_PREFIX_.'order_invoice`
						WHERE `id_order` = '.intval($id_order).'
						ORDER BY id_order_invoice ASC');
				Db::getInstance()->Execute('
						UPDATE `'._DB_PREFIX_.'order_invoice`
						SET delivery_date = \''.psql(html_entity_decode( Tools::getValue($field))).'\'
						WHERE id_order_invoice = '.(int)$id_order_invoice);
			}
			if (isset($_POST[$field]) && $field=='id_carrier')
			{
				$order = new Order($id_order);
				if (Validate::isLoadedObject($order))
				{
					if(!$order->hasBeenShipped()) {
						$id_order_carrier = Db::getInstance()->getValue('
								SELECT `id_order_carrier`
								FROM `'._DB_PREFIX_.'order_carrier`
								WHERE `id_order` = '.(int)$id_order);
						if ($id_order_carrier) {
							$order_carrier = new OrderCarrier($id_order_carrier);
							$order_carrier->id_carrier = (int)Tools::getValue($field);
							$order_carrier->save();
						}
						$order->id_carrier = (int)Tools::getValue($field);
						$order->save();
					}
				}
			}
			if (isset($_POST[$field]) && $field=='status')
			{
				$isStatus=1;
				$order_state = new OrderState(Tools::getValue($field));
				if (Validate::isLoadedObject($order_state))
				{
					$order = new Order($id_order);
					$result = Db::getInstance()->getRow('
						SELECT `id_order_state`
						FROM `'._DB_PREFIX_.'order_history`
						WHERE `id_order` = '.intval($id_order).'
						ORDER BY `date_add` DESC, `id_order_history` DESC');
					$current_order_state = new OrderState(intval($result['id_order_state']));
					if ($current_order_state->id != $order_state->id)
					{
						// Create new OrderHistory
						$history = new OrderHistory();
						$history->id_order = $order->id;
						$history->id_employee = (int)$sc_agent->id_employee;
						$use_existings_payment = false;
						if ((version_compare(_PS_VERSION_, '1.5.0.0', '>=') && !$order->hasInvoice()) || (version_compare(_PS_VERSION_, '1.5.0.0', '<') && $order->invoice_number))
							$use_existings_payment = true;
						$history->changeIdOrderState((int)$order_state->id, $order->id, $use_existings_payment);

						$carrier = new Carrier($order->id_carrier, $order->id_lang);
						$templateVars = array();
						if ($history->id_order_state == Configuration::get('PS_OS_SHIPPING') && $order->shipping_number)
							$templateVars = array('{followup}' => str_replace('@', $order->shipping_number, $carrier->url));

						if(version_compare(_PS_VERSION_, '1.5.0.0', '>='))
						{
							$context = Context::getContext();
							$context->shop->id = $order->id_shop;
							$addWithemail = $history->addWithemail(true, $templateVars, $context);
						}
						else
							$addWithemail = $history->addWithemail(true, $templateVars);

						// Save all changes
						if ($addWithemail)
						{
							// synchronizes quantities if needed..
							if (SCAS)
							{
								foreach ($order->getProducts() as $product)
								{
									if (StockAvailable::dependsOnStock($product['product_id']))
										StockAvailable::synchronize($product['product_id'], (int)$product['id_shop']);
								}
							}
						}

						addToHistory('order','modification',"current_state",intval($id_order),0,_DB_PREFIX_."orders",$order_state->name[$order->id_lang]." (id_order #".intval($id_order).")",$current_order_state->name[$order->id_lang]);

					}
				}
				continue;
			}
			if (isset($_POST[$field]))
			{
				$todo[]=$field."='".psql($_POST[$field])."'";
				switch($field){
					case 'total_discounts_tax_incl':
						$todo[]="total_discounts='".psql($_POST[$field])."'";
						break;
					case 'total_paid_tax_incl':
						$todo[]="total_paid='".psql($_POST[$field])."'";
						break;
					case 'total_shipping_tax_incl':
						$todo[]="total_shipping='".psql($_POST[$field])."'";
						break;
					case 'total_wrapping_tax_incl':
						$todo[]="total_wrapping='".psql($_POST[$field])."'";
						break;
				}
				addToHistory('order','modification',$field,intval($id_order),0,_DB_PREFIX_."orders",psql(Tools::getValue($field)));
			}
		}
		foreach($fields_address_delivery AS $field)
		{
			if (isset($_POST[$field])) {
				$order = new Order($id_order);
				$sql = "select ".substr($field,4)." from "._DB_PREFIX_."address   WHERE id_address=".intval($order->id_address_delivery);
				$oValue= Db::getInstance()->getValue($sql);
				$del_oValue= $oValue;
				$todo_address_delivery[] = substr($field,4) . "='" . psql($_POST[$field]) . "'";
				addToHistory('order','modification',$field,intval($id_order),0,_DB_PREFIX_."address",psql(Tools::getValue($field)),$del_oValue);
			}
		}
		foreach($fields_address_invoice AS $field)
		{
			if (isset($_POST[$field])) {
				$order = new Order($id_order);
				$sql = "select ".substr($field,4)." from "._DB_PREFIX_."address   WHERE id_address=".intval($order->id_address_invoice);
				$oValue= Db::getInstance()->getValue($sql);
				$inv_oValue= $oValue;
				$todo_address_invoice[] = substr($field,4) . "='" . psql($_POST[$field]) . "'";
				addToHistory('order','modification',$field,intval($id_order),0,_DB_PREFIX_."address",psql(Tools::getValue($field)), $inv_oValue);
			}
		}
		if (count($todo))
		{
			$todo[]="date_upd=NOW()";
			$sql = "UPDATE "._DB_PREFIX_."orders SET ".join(' , ',$todo)." WHERE id_order=".intval($id_order);
			Db::getInstance()->Execute($sql);
		}
		if (count($todo_payment))
		{
			$sql = "SELECT id_order_payment FROM "._DB_PREFIX_."order_invoice_payment  WHERE id_order=".intval($id_order)." LIMIT 1";
			$id_order_payment = Db::getInstance()->ExecuteS($sql);
			
			if(!empty($id_order_payment[0]["id_order_payment"]))
			{
				$sql = "UPDATE "._DB_PREFIX_."order_payment SET ".join(' , ',$todo_payment)." WHERE id_order_payment=".intval($id_order_payment[0]["id_order_payment"]);
				Db::getInstance()->Execute($sql);
			}
		}
		if (count($todo_address_delivery))
		{
			$order = new Order($id_order);
			$todo_address_delivery[]="date_upd=NOW()";
			$sql4 = "UPDATE "._DB_PREFIX_."address SET ".join(' , ',$todo_address_delivery)." WHERE id_address=".(int)$order->id_address_delivery;
			Db::getInstance()->Execute($sql4);
		}
		if(count($todo_address_invoice)){
			$order = new Order($id_order);
			$todo_address_invoice[]="date_upd=NOW()";
			$sql5 = "UPDATE "._DB_PREFIX_."address SET ".join(' , ',$todo_address_invoice)." WHERE id_address=".(int)$order->id_address_invoice;
			Db::getInstance()->Execute($sql5);
		}

		sc_ext::readCustomOrdersGridsConfigXML('onAfterUpdateSQL');
		$newId = $_POST["gr_id"];
		$action = "update";
	}

	sc_ext::readCustomGridsConfigXML('extraVars');
	
	if ( stristr($_SERVER["HTTP_ACCEPT"],"application/xhtml+xml") ) {
	 		header("Content-type: application/xhtml+xml"); } else {
	 		header("Content-type: text/xml");
	}
	echo("<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n"); 
	echo '<data>';
	echo "<action type='".$action."' sid='".$_POST["gr_id"]."' is_status='".$isStatus."' tid='".$newId."'/>";
	echo ($debug && isset($sql) ? '<sql><![CDATA['.$sql.']]></sql>':'');
	echo ($debug && isset($sql2) ? '<sql><![CDATA['.$sql2.']]></sql>':'');
	echo ($debug && isset($sql3) ? '<sql><![CDATA['.$sql3.']]></sql>':'');
	echo ($debug && isset($sql4) ? '<sql><![CDATA['.$sql4.']]></sql>':'');
	echo ($debug && isset($sql5) ? '<sql><![CDATA['.$sql5.']]></sql>':'');

echo '</data>';

