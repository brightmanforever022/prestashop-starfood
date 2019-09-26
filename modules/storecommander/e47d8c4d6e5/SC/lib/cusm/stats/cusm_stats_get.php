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
 
$id_discussion = (int)Tools::getValue("id_discussion",0);
if(empty($id_discussion))
	die();

$discussion = new CustomerThread($id_discussion);
?><!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>SC - Affiliation</title>
<style type="text/css">

html, body, div, span, applet, object, iframe,
h1, h2, h3, h4, h5, h6, p, blockquote, pre,
a, abbr, acronym, address, big, cite, code,
del, dfn, em, img, ins, kbd, q, s, samp,
small, strike, strong, sub, sup, tt, var,
b, u, i, center,
dl, dt, dd, ol, ul, li,
fieldset, form, label, legend,
table, caption, tbody, tfoot, thead, tr, th, td,
article, aside, canvas, details, embed, 
figure, figcaption, footer, header, hgroup, 
menu, nav, output, ruby, section, summary,
time, mark, audio, video {
	margin: 0;
	padding: 0;
	border: 0;
	font-size: 100%;
	font: inherit;
	vertical-align: baseline;
}
/* HTML5 display-role reset for older browsers */
article, aside, details, figcaption, figure, 
footer, header, hgroup, menu, nav, section {
	display: block;
}
body {
	line-height: 1;
	color: #000000;
    font-family: Tahoma;
}
ol, ul {
	list-style: none;
}
blockquote, q {
	quotes: none;
}
blockquote:before, blockquote:after,
q:before, q:after {
	content: '';
	content: none;
}
table {
	border-collapse: collapse;
	border-spacing: 0;
}

.stats {
	width: 100%;
	border: 1px solid #A4BED4;
	font-size: 11px;
}
.stats td {
	border: 1px solid #A4BED4;
	padding: 4px 8px;
}
.stats .edd td {
	background: #E2EDF2;
}
</style>
</head>
<body>
<table class="stats">
	<tr>
		<td><?php echo _l('Total discussions'); ?></td>
		<td><strong><?php 
		echo (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_thread
				WHERE 1=1
					AND id_customer = "'.(int)$discussion->id_customer.'"
				'.(SCMS && SCI::getSelectedShop() ? 'AND id_shop='.(int)SCI::getSelectedShop():'').'
			');
		?></strong></td>
	</tr>
	<tr class="edd">
		<td><?php echo _l('Open discussions'); ?></td>
		<td><strong><?php 
		echo (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_thread
				WHERE
					status = "open"
					AND id_customer = "'.(int)$discussion->id_customer.'"
				'.(SCMS && SCI::getSelectedShop() ? 'AND id_shop='.(int)SCI::getSelectedShop():'').'
			');
		?></strong></td>
	</tr>
	<tr>
		<td><?php echo _l('Discussions pending'); ?></td>
		<td><strong><?php 
		echo (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_thread
				WHERE
					status LIKE "%pending%"
					AND id_customer = "'.(int)$discussion->id_customer.'"
				'.(SCMS && SCI::getSelectedShop() ? 'AND id_shop='.(int)SCI::getSelectedShop():'').'
			');
		?></strong></td>
	</tr>
	<tr class="edd">
		<td><?php echo _l('Closed discussions'); ?></td>
		<td><strong><?php 
		echo (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_thread
				WHERE
					status = "closed"
					AND id_customer = "'.(int)$discussion->id_customer.'"
				'.(SCMS && SCI::getSelectedShop() ? 'AND id_shop='.(int)SCI::getSelectedShop():'').'
			');
		?></strong></td>
	</tr>
	<tr>
		<td><?php echo _l('Total messages'); ?></td>
		<td><strong><?php 
		echo (int)Db::getInstance()->getValue('
				SELECT COUNT(*)
				FROM '._DB_PREFIX_.'customer_message cm
					INNER JOIN `'._DB_PREFIX_.'customer_thread` ct ON (cm.id_customer_thread=ct.id_customer_thread)
				WHERE
					ct.id_customer = "'.(int)$discussion->id_customer.'"
					'.(SCMS && SCI::getSelectedShop() ? ' AND ct.id_shop='.(int)SCI::getSelectedShop().'':'').'
					AND cm.id_employee = 0
				GROUP BY cm.id_customer_message
			');
		?></strong></td>
	</tr>
	<tr>
		<td colspan="2"></td>
	</tr>
	<?php 
	$orders_ok = 0;
	$total_ok = 0;
	$orders = Order::getCustomerOrders($discussion->id_customer);
	if ($orders && count($orders))
	{
		foreach ($orders as $key => $order)
		{
			if ($order['valid'])
			{
				$orders_ok++;
				$total_ok += $order['total_paid_real'];
			}
		}
	}
	?>
	<tr>
		<td><?php echo _l('Validated Orders'); ?></td>
		<td><strong><?php 
		echo (int)$orders_ok;
		?></strong></td>
	</tr>
	<tr class="edd">
		<td><?php echo _l('Customer turnover'); ?></td>
		<td><strong><?php 
		echo $total_ok;
		?></strong></td>
	</tr>
</table>

</body>
</html>