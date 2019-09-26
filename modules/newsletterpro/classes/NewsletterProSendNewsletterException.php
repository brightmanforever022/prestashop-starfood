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

class NewsletterProSendNewsletterException extends Exception
{
	const CODE_NO_STEPS_AVAILABLE = 101;

	const CODE_SEND_IN_PROGRESS = 102;

	const CODE_NO_CONNECTIONS_AVAILABLE = 103;

	const CODE_SEND_IS_PAUSED = 104;

	const CODE_NO_ACTIVE_SEND = 105;

	const CODE_SEND_COMPLETE = 106;
}