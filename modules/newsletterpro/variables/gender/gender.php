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

class NewsletterProTemplateVariableGender
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
		$user->gender = '';
		if ($user->user_type == 'customer')
		{
			$customer = new Customer($user->id);

			if (Validate::isLoadedObject($customer))
			{
				if (isset($customer->id_gender) && $customer->id_gender > 0)
				{
					$gender = new Gender($customer->id_gender);
					if (Validate::isLoadedObject($gender))
					{
						if (isset($gender->name[$user->id_lang]))
							$user->gender = $gender->name[$user->id_lang];
					}
				}
			}
		}
	}
}

?>