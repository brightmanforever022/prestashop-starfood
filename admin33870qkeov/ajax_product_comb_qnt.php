<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/
if (!defined('_PS_ADMIN_DIR_')) {
    define('_PS_ADMIN_DIR_', getcwd());
}
include(_PS_ADMIN_DIR_.'/../config/config.inc.php');
/* Getting cookie or logout */
require_once(_PS_ADMIN_DIR_.'/init.php');

$query = Tools::getValue('q', false);
if (!$query or $query == '' or strlen($query) < 1) {
    die();
}


$productSql = '
	SELECT *
	FROM `'._DB_PREFIX_.'product_attribute` pa
	'.Shop::addSqlAssociation('product_attribute', 'pa').'
	WHERE pa.supplier_reference = "'. pSQL($query) .'"
';
$productData = Db::getInstance()->getRow($productSql);


if( $productData == false ){
	die;
}

$product = new Product($productData['id_product']);

$combinations = $product->getAttributeCombinations($context->language->id);
$sizeQnt = array();
$reqSize = null;
foreach($combinations as $comb){
    $sizeQnt[ $comb['attribute_name'] ] = $comb['quantity'];
	if($comb['supplier_reference'] == $query){
        $reqSize = $comb['attribute_name'];
	}
}
ksort($sizeQnt);
echo '<style type="text/css">.table-stocks{border-radius:0;}</style>';
echo '<table style="width:900px;" ><tr><td style="width:290px;height:300px;">';
echo '<table class="table table-stocks" style="width:100%;height:300px;">';
echo '<caption><b>Aktueller Bestand</b></caption>';
echo '<tr><td><b>Größe</b></td><td><b>Menge</b></td></tr>';
foreach($sizeQnt as $name => $qnt ){
	echo '<tr style="'. ($reqSize == $name ? 'font-weight:bold;' : '') .'"><td>'. $name .'</td><td>'. $qnt .'</td></tr>';
}
echo '<tr><td></td><td></td></tr>';
echo '</table></td><td style="width:600px;height:300px;">';


$expDelUrl = 'https://www.vipdress.de/admin123/index_service.php/supplier_orders/show_supplier_orders_by_sku_ext/'
	. rawurlencode($query);
echo $resp = Tools::file_get_contents($expDelUrl);

echo '</td></tr></table>';
die;

