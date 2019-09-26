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

class NewsletterProRSS
{
	public $version;

	public $iso_lang;

	public $link;

	public $template;

	public $context;

	public $xml;

	/**
	* Don't forget to:
	* 
	* include the files [our_modules.css, our_modules.js]
	* include the files for PrestaShop 1.5 [font-awesome.css, fonts]
	*/
	public function __construct($link, $version, $iso_lang)
	{
		$this->context = Context::getContext();
		$this->version = $version;
		$this->iso_lang = $iso_lang;
		$this->link = $link;
	}

	public function setTemplate($filename)
	{
		if (!file_exists($filename))
			throw new Exception('The rss template not exists.');

		$this->template = $filename;
	}

	public function getXML()
	{
		$rss = $this->link.sprintf('?version=%s&lang=%s', $this->version, $this->iso_lang);
		return @simplexml_load_file($rss);
	}

	public function render()
	{
		$output = '';

		if (!isset($this->template))
			throw new Exception('You need to set the template path before to render the template.');

		$xml = $this->getXML();

		if (!$xml)
		{
			if (isset($this->context->controller))
				$output = $this->context->controller->module->l('Cannot connect to the RSS.');
			else
				$output = 'Cannot connect to the RSS.';
		}
		else
		{
			$items = $xml->channel->item;
			// if the links don't match the prestashop website will redirect them to my prestashop accout
			$regex = '/^(http:\/\/|https:\/\/)?(www\.)?addons\.prestashop\.com/';
			$my_modules = 'http://addons.prestashop.com/en/2_community?contributor=236068';

			$replace_description = array();
			foreach ($items as $item)
			{
				if (!preg_match($regex, (string)$item->details))
					$item->details = $my_modules;

				if (!preg_match($regex, (string)$item->demo))
					$item->demo = $my_modules;

				if (!preg_match('/^(http:\/\/|https:\/\/)?(www\.)?youtube\.com/', (string)$item->video))
					$item->video = $my_modules;

				if (preg_match('/(?P<link>(http:\/\/|https:\/\/)?(\w+\.){1,}\w+)/', $item->description, $match))
					$replace_description[] = $match['link'];
			}

			$this->context->smarty->assign(array(
				'items'           => $items,
				'display_version' => (int)$xml->channel->displayVersion,
				'display_rating'  => (int)$xml->channel->displayRating,

				'display_details' => (int)$xml->channel->displayDetails,
				'display_video'   => (int)$xml->channel->displayVideo,
				'display_demo'    => (int)$xml->channel->displayDemo,

				'display_price'   => (int)$xml->channel->displayPrice,
				'display_badge'   => (int)$xml->channel->displayBadge,
				'display_new'     => (int)$xml->channel->displayNew,
				'count_new'       => $this->countNew($xml->channel)
			));

			$output = $this->context->smarty->fetch($this->template);

			// filter the undesired urls
			if (!empty($replace_description))
				$output = str_replace($replace_description, '', $output);
		}

		return $output;
	}

	public function countNew($channel)
	{
		$count = 0;
		$items = $channel->item;
		$display_new = (int)$channel->displayNew;
		if ($display_new)
		{
			foreach ($items as $item)
				if ((int)$item->displayNew)
					$count++;
		}

		return $count;
	}
}