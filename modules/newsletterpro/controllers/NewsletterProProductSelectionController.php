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

class NewsletterProProductSelectionController extends NewsletterProController
{
	public function newInstance()
	{
		return new self();
	}

	public function initContent()
	{
		return parent::initContent();
	}

	public function postProcess()
	{
		parent::postProcess();

		$action = 'submit_product_selection';

		if (Tools::isSubmit($action))
		{
			@ini_set('max_execution_time', '2880');
			@ob_clean();
			@ob_end_clean();

			if (Tools::getValue('token') != $this->token)
				$this->display('Invalid Token!');

			switch (Tools::getValue($action))
			{
				case 'addProduct';
					$id_product = Tools::getValue('id_product');
					$this->display($this->addProduct($id_product), true);
				break;
			}
		}
	}

	private function addProduct($id_product)
	{
		try
		{
			$product = NewsletterProProduct::newInstance($id_product);
			$this->response->set('product', $product->toArray());
		}
		catch(Exception $e)
		{
			$this->response->addError($e->getMessage());
		}

		return $this->response->display();
	}
}