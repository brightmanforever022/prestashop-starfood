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

class NewsletterProMyAccountModuleFrontController extends ModuleFrontController
{
	public $module;

	public function __construct()
	{
		if ((bool)Configuration::get('PS_SSL_ENABLED'))
			$this->ssl = true;

		parent::__construct();
	}

	public function initContent()
	{
		$this->display_column_left = false;
		parent::initContent();

		$this->module = Module::getInstanceByName('newsletterpro');

		if (!Validate::isLoadedObject($this->module))
			Tools::redirect('index.php');

		if (!$this->context->customer->isLogged(true))
			Tools::redirect('index.php?controller=authentication&redirect=module&module=newsletterpro&back='.urlencode($this->module->my_account_url) );

		if (!$this->isFeatureActivated())
			Tools::redirect('index.php');

		$this->context->smarty->assign(array(
			'tpl_location' => $this->module->dir_location.'views/',
			'my_account_url' => $this->module->my_account_url,
			'is_subscribed' => (int)$this->context->customer->newsletter,
			'list_of_interest' => NewsletterProListOfInterest::getListActiveCustomer($this->context->customer->id),
			'category_tree' => $this->getCategoryTree(),
			'subscribe_by_category_active' => (bool)pqnp_config('SUBSCRIBE_BY_CATEGORY'),
			'customer_subscribe_by_loi_active' => (bool)pqnp_config('CUSTOMER_SUBSCRIBE_BY_LOI'),
		));

		if ($this->module->isPS16())
			$this->setTemplate('1.6/my_account.tpl');
		else
			$this->setTemplate('1.5/my_account.tpl');
	}

	public function setMedia()
	{
		parent::setMedia();

		$this->context->controller->addCSS(array(
			$this->module->uri_location.'views/css/my_account.css',
		));
	}

	public function isFeatureActivated()
	{
		return (bool)pqnp_config('DISPLYA_MY_ACCOUNT_NP_SETTINGS');
	}

	public function getCategoryTree()
	{
		$root = Category::getRootCategory();
		$tab_root = array('id_category' => $root->id, 'name' => $root->name);

		$sql = 'SELECT * FROM `'._DB_PREFIX_.'newsletter_pro_customer_category` WHERE `id_customer` = '.(int)$this->context->customer->id;

		$selected_cat = array();
		$result = Db::getInstance()->getRow($sql);
		if ($result)
			$selected_cat = explode(',', trim($result['categories'], ','));

		
		$category_tree = $this->module->renderCategoryTree(array(
			'root'                => $tab_root,
			'selected_cat'        => $selected_cat,
			'input_name'          => 'categoryBox',
			'use_radio'           => false,
			'disabled_categories' => array(),
			'use_search'          => true,
			'use_in_popup'        => false,
			'use_shop_context'    => true,
			'ajax_request_url'    => $this->module->uri_location.'ajax/ajax_newsletterpro_front.php',
		));

		return $category_tree;
	}

	public function postProcess()
	{


		$filename = pathinfo(__FILE__, PATHINFO_FILENAME);

		if (Tools::isSubmit('submitNewsletterProSettings'))
		{
			$newsletter = (Tools::isSubmit('newsletter') ? (int)Tools::getValue('newsletter') : 0);

			if ((bool)pqnp_config('CUSTOMER_SUBSCRIBE_BY_LOI'))
			{
				$list_of_interest = Tools::getValue('list_of_interest');

				$entry_exists = Db::getInstance()->getValue('
					SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_customer_list_of_interests` 
					WHERE `id_customer`= '.(int)$this->context->customer->id
				);

				if (!empty($list_of_interest)) {


					$list_of_interest_str = trim(implode(',', $list_of_interest), ',');

					if (!$entry_exists) {

						if (!Db::getInstance()->insert('newsletter_pro_customer_list_of_interests', array(
							'categories' => pSQL($list_of_interest_str),
							'id_customer' => (int)$this->context->customer->id,
							)))
							$this->errors[] = $this->module->l('Error on updating the list of interests.', $filename);
					} else {

						if (!Db::getInstance()->update('newsletter_pro_customer_list_of_interests', array(
								'categories' => pSQL($list_of_interest_str),
							), '`id_customer`='.(int)$this->context->customer->id))
							$this->errors[] = $this->module->l('Error on updating the list of interests.', $filename);
					}
				} else {
					if ($entry_exists)
						Db::getInstance()->delete('newsletter_pro_customer_list_of_interests', 'id_customer = '.(int)$this->context->customer->id , 1);
				}
			}

			if (empty($this->errors)) {

				if (Db::getInstance()->update('customer', array(
					'newsletter' => (int)$newsletter,
				), '`id_customer`='.(int)$this->context->customer->id))
					$this->context->customer->newsletter = $newsletter;
				else
					$this->errors[] = $this->module->l('Error on updating the newsletter subscription!', $filename);
			}

			if ((bool)pqnp_config('SUBSCRIBE_BY_CATEGORY'))
			{
				$category_box = Tools::isSubmit('categoryBox') && is_array(Tools::getValue('categoryBox')) ? Tools::getValue('categoryBox') : array();

				if (empty($this->errors))
				{

					$category_box_str = trim(implode(',', $category_box), ',');

					$entry_exists = Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'newsletter_pro_customer_category` WHERE `id_customer`= '.(int)$this->context->customer->id);

					if (!$entry_exists)
					{
						if (Db::getInstance()->insert('newsletter_pro_customer_category', array(
							'id_customer' => (int)$this->context->customer->id,
							'categories' => pSQL($category_box_str),
						)))
							$this->context->customer->newsletter = $newsletter;
						else
							$this->errors[] = $this->module->l('Error on updating the categories!', $filename);
					}
					else
					{
						if (Db::getInstance()->update('newsletter_pro_customer_category', array(
							'categories' => pSQL($category_box_str),
						), '`id_customer`='.(int)$this->context->customer->id))
							$this->context->customer->newsletter = $newsletter;
						else
							$this->errors[] = $this->module->l('Error on updating the categories!', $filename);
					}
				}
			}



		}
	}
}
?>