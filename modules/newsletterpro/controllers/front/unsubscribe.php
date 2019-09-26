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

class NewsletterProUnsubscribeModuleFrontController extends ModuleFrontController
{
	public $id_newsletter;

	public function __construct()
	{
		if ((bool)Configuration::get('PS_SSL_ENABLED'))
			$this->ssl = true;

		parent::__construct();
	}

	public function initContent()
	{
		parent::initContent();
		$this->setTemplate('unsubscribe.tpl');
	}

	public function postProcess()
	{
		try
		{
			if (Tools::isSubmit('email'))
			{
				$email = trim(Tools::getValue('email'));

				if (Validate::isEmail($email))
				{
					$token_ok = false;
					
					if (Tools::isSubmit('mc_token'))
						$token_ok = NewsletterProMailChimpToken::validateToken('mc_token');

					if (!$token_ok)
						$token_ok = Tools::isSubmit('u_token') && Tools::getValue('u_token') === Tools::encrypt($email);

					if (!$token_ok)
					{
						$this->context->smarty->assign(array(
							'token_not_valid' => true
						));
					}
					else
					{
						$result = array();

						$sql = 'UPDATE `'._DB_PREFIX_.'customer` SET `newsletter`=0 WHERE `email`= "'.pSQL($email).'" LIMIT 5';
						Db::getInstance()->execute($sql);
						$result['customer'] = Db::getInstance()->Affected_Rows();

						// Verify if the newsletter table exists
						$sql = "SELECT COUNT(*) AS `count`
								FROM INFORMATION_SCHEMA.TABLES
								WHERE  TABLE_SCHEMA = '"._DB_NAME_."' 
								AND TABLE_NAME = '"._DB_PREFIX_."newsletter';";

						$count = Db::getInstance()->executeS($sql);
						if (!empty($count) && isset($count[0]['count']) && $count[0]['count'] >= 1)
						{
							$sql = 'UPDATE `'._DB_PREFIX_.'newsletter` SET `active`=0 WHERE `email` = "'.pSQL($email).'" LIMIT 5;';
							Db::getInstance()->execute($sql);
							$result['newsletter'] = Db::getInstance()->Affected_Rows();
						}

						$sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_email` SET `active`=0 WHERE `email` = "'.pSQL($email).'" LIMIT 5;';
						Db::getInstance()->execute($sql);
						$result['newsletter_pro_email'] = Db::getInstance()->Affected_Rows();

						$sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_subscribers` SET `active`= 0 WHERE `email` = "'.pSQL($email).'" LIMIT 5;';
						Db::getInstance()->execute($sql);
						$result['newsletter_pro_subscribers'] = Db::getInstance()->Affected_Rows();

						Db::getInstance()->delete('newsletter_pro_forward', '`to` = "'.pSQL($email).'"');
						$result['newsletter_pro_forward'] = Db::getInstance()->Affected_Rows();

						if (in_array(true, $result))
						{
							$this->id_newsletter = $this->getHistoryIdByToken(Tools::getValue('token'));

							if ($this->id_newsletter)
							{
								if (self::isForwarder($result))
								{
									// deleted the forwarders is he unsubscribe
									Db::getInstance()->delete('newsletter_pro_forward', '`from` = "'.pSQL($email).'"');
									if ($this->updateFwdUnsubscribed())
									{
										Db::getInstance()->insert('newsletter_pro_fwd_unsibscribed', array(
											'id_newsletter_pro_tpl_history' => (int)$this->id_newsletter,
											'email' => $email,
										));
									}
								}
								else
								{
									if ($this->updateUnsubscribed())
									{
										Db::getInstance()->insert('newsletter_pro_unsibscribed', array(
											'id_newsletter_pro_tpl_history' => (int)$this->id_newsletter,
											'email' => $email,
										));
									}
								}
							}

							$this->context->smarty->assign(array(
								'unsubscribe' => true
							));
						}
						else
						{
							$this->context->smarty->assign(array(
								'email_not_found' => true
							));
						}
					}
				}
				else
				{
					$this->context->smarty->assign(array(
							'email_not_valid' => true
					));
				}
			}
			else
				Tools::redirect('index.php');

			if (Tools::getValue('msg') == 'false' || Tools::getValue('msg') == '0')
				Tools::redirect('index.php');
		}
		catch(Exception $e)
		{
			NewsletterProLog::writeStrip($e->getMessage(), NewsletterProLog::ERROR_FILE);
		}
	}

	public static function isForwarder($result)
	{
		if (isset($result['newsletter_pro_forward']) && $result['newsletter_pro_forward'] == true)
			return true;
		return false;
	}

	public function getHistoryIdByToken($token)
	{
		return (int)Db::getInstance()->getValue('SELECT `id_newsletter_pro_tpl_history` FROM `'._DB_PREFIX_.'newsletter_pro_tpl_history` WHERE `token` = "'.pSQL($token).'"');
	}

	public function updateUnsubscribed()
	{
		if (!isset($this->id_newsletter))
			return false;

		$sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_tpl_history`
				SET `unsubscribed` = unsubscribed + 1 
				WHERE `id_newsletter_pro_tpl_history` = '.(int)$this->id_newsletter.';';

		return Db::getInstance()->execute($sql);
	}

	public function updateFwdUnsubscribed()
	{
		if (!isset($this->id_newsletter))
			return false;

		$sql = 'UPDATE `'._DB_PREFIX_.'newsletter_pro_tpl_history`
				SET `fwd_unsubscribed` = fwd_unsubscribed + 1 
				WHERE `id_newsletter_pro_tpl_history` = '.(int)$this->id_newsletter.';';

		return Db::getInstance()->execute($sql);
	}

}
?>