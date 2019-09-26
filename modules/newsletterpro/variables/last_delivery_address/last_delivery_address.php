<?php
/**
* 2013-2015 Ovidiu Cimpean
*
* Ovidiu Cimpean - Newsletter Pro © All rights reserved.
*
* DISCLAIMER
*
* Do not edit, modify or copy this file.
* If you wish to customize it, contact us at addons4prestashop@gmail.com.
*
* @author    Ovidiu Cimpean <addons4prestashop@gmail.com>
* @copyright 2013-2015 Ovidiu Cimpean
* @version   Release: 4
* @license   Do not edit, modify or copy this file
*/

class NewsletterProTemplateVariableLastDeliveryAddress
{
	/**
	 * Setup the user variables
	 *
	 * The class name should be like the filename without slashes and with camelcase words
	 * Example:
	 * my_variable_name.php the class will be NewsletterProTemplateVariableMyVariableName
	 * 
	 * Example:
	 * $user->my_name = 'John Smith'; 
	 *
	 * The variable available in the template will be {my_name}
	 *
	 * The help files will are placed in the folder newsletterpro/views/templates/admin/variables_help/
	 * 
	 * @param object $user
	 */
	public function __construct(&$user)
	{
		$user->last_delivery_address = '';

		if ($user->user_type == 'customer')
		{
			$customer = new Customer($user->id);

			$orders = Order::getCustomerOrders($customer->id);
			if (Validate::isLoadedObject($customer) && $orders)
			{
				$last_order = $orders[0];
				$id_address = (int)$last_order['id_address_delivery'];

				$delivery_address = new Address($id_address);
				if (Validate::isLoadedObject($delivery_address));
				$formatted_delivery_address = AddressFormat::generateAddress($delivery_address, array(), '<br />', ' ');
				$user->last_delivery_address = $formatted_delivery_address;
			}
		}
	}
}

?>