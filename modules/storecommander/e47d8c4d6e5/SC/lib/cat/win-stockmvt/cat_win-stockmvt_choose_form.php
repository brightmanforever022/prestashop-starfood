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

$id_lang=intval(Tools::getValue('id_lang'));
$id_product=intval(Tools::getValue('id_product'));
$id_warehouse= (int)SCI::getSelectedWarehouse();
?>
<a href="javascript: void(0);" onclick="window.parent.stockmvtChooseAdd()" style="font-family: Tahoma;font-size: 11px !important;color: black;text-decoration: none;float: left; margin-left: 10px;">
	<center>
		<img src="lib/img/add_big.png" alt="<?php echo _l('Add to stock')?>" title="<?php echo _l('Add to stock')?>" /><br/>
		<?php echo _l('Add to stock')?>
	</center>
</a>
<div  style="font-family: Tahoma;font-size: 11px !important;color: black;text-decoration: none;float: left;margin: 60px 0px 0 30px;">
	<?php echo _l('OR')?>
</div>
<a href="javascript: void(0);" onclick="window.parent.stockmvtChooseRemove()" style="font-family: Tahoma;font-size: 11px !important;color: black;text-decoration: none;float: right; margin-right: 10px;">
	<center>
		<img src="lib/img/delete_big.png" alt="<?php echo _l('Remove stock')?>" title="<?php echo _l('Remove stock')?>" /><br/>
		<?php echo _l('Remove stock')?>
	</center>
</a>