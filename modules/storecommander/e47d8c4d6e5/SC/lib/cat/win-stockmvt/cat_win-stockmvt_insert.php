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
// get id product and product attribute if possible
$id_product = (int)Tools::getValue('id_product', 0);
$id_product_attribute = (int)Tools::getValue('id_product_attribute', 0);
$warehouse_price_in_product = (int)Tools::getValue('warehouse_price_in_product', 0);

$return = "";

if(!empty($id_product))
{
	// Global checks when add / remove / transfer product
	if ((Tools::isSubmit('addstock') || Tools::isSubmit('removestock') || Tools::isSubmit('transferstock') ) && Tools::isSubmit('is_post'))
	{
		// get quantity and check that the post value is really an integer
		// If it's not, we have nothing to do
		$quantity = Tools::getValue('quantity', 0);
		if (!is_numeric($quantity) || (int)$quantity <= 0)
			$return .= _l('The quantity value is invalid.')."<br/>";
		$quantity = (int)$quantity;
	}

	// Global checks when add / remove product
	if ((Tools::isSubmit('addstock') || Tools::isSubmit('removestock') ) && Tools::isSubmit('is_post'))
	{
		// get warehouse id
		$id_warehouse = (int)Tools::getValue('id_warehouse', 0);
		if ($id_warehouse <= 0 || !Warehouse::exists($id_warehouse))
			$return .= _l('The selected warehouse is invalid.')."<br/>";

		// get stock movement reason id
		$id_stock_mvt_reason = (int)Tools::getValue('id_stock_mvt_reason', 0);
		if ($id_stock_mvt_reason <= 0 || !StockMvtReason::exists($id_stock_mvt_reason))
			$return .= _l('The reason is invalid.')."<br/>";

		// get usable flag
		$usable = Tools::getValue('usable', null);
		if (is_null($usable))
			$return .= _l('You have to specify if the product quantity is available for sale on the store.')."<br/>";
		$usable = (bool)$usable;
	}

	if (Tools::isSubmit('addstock') && Tools::isSubmit('is_post'))
	{
		// get product unit price
		$price = floatval(str_replace(',', '.', Tools::getValue('price', 0)));
		if (empty($price) || !is_numeric($price))
			$return .= _l('The wholesale price is invalid.')."<br/>";
		$price = round(floatval($price), 6);

		// get product unit price currency id
		$id_currency = (int)Tools::getValue('id_currency', 0);
		if ($id_currency <= 0 || ( !($result = Currency::getCurrency($id_currency)) || empty($result) ))
			$return .= _l('The selected currency is invalid.')."<br/>";

		// if all is ok, add stock
		if (empty($return))
		{
			$warehouse = new Warehouse($id_warehouse);

			// convert price to warehouse currency if needed
			if ($id_currency != $warehouse->id_currency)
			{
				// First convert price to the default currency
				$price_converted_to_default_currency = Tools::convertPrice($price, $id_currency, false);

				// Convert the new price from default currency to needed currency
				$price = Tools::convertPrice($price_converted_to_default_currency, $warehouse->id_currency, true);
			}

			// Update wholesale price product
			if($warehouse_price_in_product)
			{
				if(!empty($id_product_attribute))
				{
					$sql_update = "UPDATE "._DB_PREFIX_."product_attribute_shop SET wholesale_price = '".pSQL($price)."' WHERE id_product_attribute = '".(int)$id_product_attribute."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
					Db::getInstance()->execute($sql_update);
				}
				else
				{
					$sql_update = "UPDATE "._DB_PREFIX_."product_shop SET wholesale_price = '".pSQL($price)."' WHERE id_product = '".(int)$id_product."' AND id_shop IN (".pSQL(SCI::getSelectedShopActionList(true)).")";
					Db::getInstance()->execute($sql_update);
				}
			}
			
			// add stock
			$stock_manager = StockManagerFactory::getManager();

			if ($stock_manager->addProduct($id_product, $id_product_attribute, $warehouse, $quantity, $id_stock_mvt_reason, $price, $usable))
			{
				StockAvailable::synchronize($id_product);
				if (Tools::isSubmit('addstockAndStay'))
				{
					$redirect = self::$currentIndex.'&id_product='.(int)$id_product;
					if ($id_product_attribute)
						$redirect .= '&id_product_attribute='.(int)$id_product_attribute;
					$redirect .= '&addstock&token='.$token;
				}
			}
			else
				$return .= _l('An error occurred. No stock was added.')."<br/>";
		}
	}

	if (Tools::isSubmit('removestock') && Tools::isSubmit('is_post'))
	{
		// if all is ok, remove stock
		if (empty($return))
		{
			$warehouse = new Warehouse($id_warehouse);

			// remove stock
			$stock_manager = StockManagerFactory::getManager();
			$removed_products = $stock_manager->removeProduct($id_product, $id_product_attribute, $warehouse, $quantity, $id_stock_mvt_reason, $usable);

			if (count($removed_products) > 0)
			{
				StockAvailable::synchronize($id_product);
			}
			else
			{
				$physical_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), false);
				$usable_quantity_in_stock = (int)$stock_manager->getProductPhysicalQuantities($id_product, $id_product_attribute, array($warehouse->id), true);
				$not_usable_quantity = ($physical_quantity_in_stock - $usable_quantity_in_stock);
				if ($usable_quantity_in_stock < $quantity)
					$return .= _l('You do not have enough available quantity.')."<br/>";
				else if ($not_usable_quantity < $quantity)
					$return .= _l('You do not have enough quantity (not available).')."<br/>";
				else
					$return .= _l('It is not possible to remove the specified quantity or an error occurred. No stock was removed.')."<br/>";
			}
		}
	}

	if (Tools::isSubmit('transferstock') && Tools::isSubmit('is_post'))
	{
		// get source warehouse id
		$id_warehouse_from = (int)Tools::getValue('id_warehouse_from', 0);
		if ($id_warehouse_from <= 0 || !Warehouse::exists($id_warehouse_from))
			$return .= _l('The source warehouse is not valid.')."<br/>";

		// get destination warehouse id
		$id_warehouse_to = (int)Tools::getValue('id_warehouse_to', 0);
		if ($id_warehouse_to <= 0 || !Warehouse::exists($id_warehouse_to))
			$return .= _l('The destination warehouse is not valid.')."<br/>";

		// get usable flag for source warehouse
		$usable_from = Tools::getValue('usable_from', null);
		if (is_null($usable_from))
			$return .= _l('You have to specify if the product quantity is available for sale on the store in the source warehouse.')."<br/>";
		$usable_from = (bool)$usable_from;

		// get usable flag for destination warehouse
		$usable_to = Tools::getValue('usable_to', null);
		if (is_null($usable_to))
			$return .= _l('You have to specify if the product quantity is available for sale on the store in the destination warehouse.')."<br/>";
		$usable_to = (bool)$usable_to;

		// if we can process stock transfers
		if (empty($return))
		{
			// transfer stock
			$stock_manager = StockManagerFactory::getManager();
			
			$check_in_warehouse = WarehouseProductLocation::getIdByProductAndWarehouse((int)$id_product, $id_product_attribute, (int)$id_warehouse_to);
			if(empty($check_in_warehouse))
			{
				$new = new WarehouseProductLocation();
				$new->id_product = (int)$id_product;
				$new->id_product_attribute = $id_product_attribute;
				$new->id_warehouse = (int)$id_warehouse_to;
				$new->save();
			}

			$is_transfer = $stock_manager->transferBetweenWarehouses(
				$id_product,
				$id_product_attribute,
				$quantity,
				$id_warehouse_from,
				$id_warehouse_to,
				$usable_from,
				$usable_to
			);
			StockAvailable::synchronize($id_product);
			if (!$is_transfer)
				$return .= _l('It is not possible to transfer the specified quantity, or an error occurred. No stock was transferred.')."<br/>";
		}
	}

	// PM Cache
	if(!empty($id_product))
    {
        ExtensionPMCM::clearFromIdsProduct($id_product);
        if(_s("APP_COMPAT_HOOK"))
        {
            $p = new Product((int)$id_product, false, null);
            SCI::hookExec('updateProduct', array('product' => $p));
            if(!empty($id_product_attribute))
                SCI::hookExec('updateProductAttribute', array('product' => $p));
        }
    }
}
else
	$return .= "The specified product is not valid";

if(empty($return))
	echo "success";
else
	echo $return;