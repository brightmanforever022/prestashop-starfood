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

class NewsletterProOurModules
{
	public $iso_code;

	public $context;

	public $module;

	public $lang;

	public static function newInstance()
	{
		return new self();
	}

	public function __construct()
	{
		$this->context = Context::getContext();
		$this->module = NewsletterPro::getInstance();
		$this->iso_code = $this->context->language->iso_code;
	}

	public function get()
	{
		$this->lang = (empty($this->iso_code)) ? 'en' : $this->iso_code;

		$contents = $this->getDeveloperModules();

		if ($contents[0] == 'no_connection')
		{
			$links = 'Checkout the links below to see our high quality modules:<br>';

			foreach ($contents[1] as $value)
				$links .= '<a href="'.$value.'" target="_blank">'.$value.'</a><br>';

			return $links;
		}

		$xml = simplexml_load_string($contents);

		$items = $xml->channel->item;

		$count = 0;
		foreach ($items as $item)
			if ((int)$item->displayNew)
				$count++;

		$this->context->smarty->assign(array(
			'items' => $items,
			'display_version' => (int)$xml->channel->displayVersion,
			'display_rating' => (int)$xml->channel->displayRating,
			'display_details' => (int)$xml->channel->displayDetails,
			'display_video' => (int)$xml->channel->displayVideo,
			'display_demo' => (int)$xml->channel->displayDemo,
			'display_price' => (int)$xml->channel->displayPrice,
			'display_badge' => (int)$xml->channel->displayBadge,
			'display_new' => (int)$xml->channel->displayNew,
			'count_new' => $count
		));

		$output = $this->context->smarty->fetch(dirname(__FILE__).'/../views/templates/admin/our_modules_content.tpl' );

		return $output;

	}

	public function getDeveloperModules()
	{
		if ($this->lang != 'en' && $this->lang != 'fr' && $this->lang != 'es')
			$this->lang = 'en';

		$links = array(
			'http://addons.prestashop.com/'.$this->lang.'/93_proquality',
		);

		$modules = '';
		$count   = 0;
		foreach ($links as $value)
		{
			$contents = @Tools::file_get_contents($value);

			if (empty($contents))
			{
				return array(
					'no_connection',
					$links
				);
			}

			preg_match_all('/(<div class="module ">)(.*)(<div class="module-hover">)/sU', $contents, $patterns);

			foreach ($patterns[2] as $value2)
			{
				$details_link = explode('"', $value2);
				$details_link = $details_link[1];

				$contents = @Tools::file_get_contents($details_link);

				preg_match_all('/(<img itemprop="image").*data-original="([^"]+)/', $contents, $patterns);

				$logo_img = explode('"', $patterns[2][0]);
				$logo_img = $logo_img[0];

				preg_match_all('/<img class="badge" src="(.*?)".*?>/i', $contents, $patterns);
				$logo_badge = $patterns[1][1];

				preg_match_all('/<img class="badge" src="(.*?)".*?>/i', $contents, $patterns);

				$downloads = explode('title="', $patterns[0][1]);
				$downloads = str_replace('" />', '', $downloads[1]);

				preg_match_all('/(<meta itemprop="rating")(.*)(title=")/sU', $contents, $patterns);
				$logo_rating = explode('"', $patterns[2][0]);
				$logo_rating = $logo_rating[7];
				$rating      = explode('"', $patterns[2][0]);
				$rating      = $rating[1];

				preg_match_all('/(<h3 >Vers)(.*)(<\/tr>)/sU', $contents, $patterns);

				$version = strip_tags(trim($patterns[2][0]));
				$version = preg_replace('/[^0-9,.]/', '', $version);

				preg_match_all('/(<h1 class="title_product">)(.*)(<\/span>)/sU', $contents, $patterns);
				$product_name = strip_tags(trim($patterns[2][0]));	
				$product_name = trim(str_replace('Module', '', trim($product_name)));

				preg_match_all('/(<span id="pretaxe_price_display">)(.*)(\/span>)/sU', $contents, $patterns);
				$product_price = strip_tags(trim($patterns[2][0]));

				preg_match_all('/(id="video">)(.*)(<\/iframe>)/sU', $contents, $patterns);
				$video_link = explode('"', $patterns[2][0]);
				$video_link = $video_link[3];

				preg_match_all('/(<h3 >Des)(.*)(<h3 >)/sU', $contents, $patterns);
				$description = trim(str_replace('cription', '', $patterns[2][0]));	
				$description = trim(str_replace('cripci&oacute;n', '', $description));	
				$description = strip_tags(trim($description));	
				$description = mb_convert_encoding($description, 'UTF-8', 'auto');

				if ($this->lang == 'en') preg_match_all('/(<td ><h3 >Updated<\/h3><\/td>)(.*)(<\/td>)/sU', $contents, $patterns);
				elseif ($this->lang == 'fr')preg_match_all('/(<td ><h3 >Mise &agrave; jour<\/h3><\/td>)(.*)(<\/td>)/sU', $contents, $patterns);
				elseif ($this->lang == 'es')preg_match_all('/(<td ><h3 >Actualizado<\/h3><\/td>)(.*)(<\/td>)/sU', $contents, $patterns);
				$updated_date = strip_tags(trim($patterns[2][0]));
				if ($this->lang != 'en') $updated_date = str_replace('/', '-', $updated_date);

				preg_match_all('/(<div class="block_demo">)(.*)(<\/div>)/sU', $contents, $patterns);
				$demo_link = explode('"', $patterns[2][0]);
				if (strstr($demo_link[1], 'FO'))
				{
					$demo_link_front_office = $demo_link[1];
					if (strstr($demo_link[5], 'BO'))
						$demo_link_back_offile = $demo_link[5];
				}
				else
				{
					$demo_link_front_office = '';
					if (strstr($demo_link[1], 'BO'))
						$demo_link_back_offile = $demo_link[1];
				}

				$begin_timestamp   = strtotime('-1 month');
				$updated_timestamp = strtotime($updated_date);

				if ($updated_timestamp <= $begin_timestamp)
					$new = '0';
				else
					$new = '1';

				$modules[$count]['logo_img']               = $logo_img;
				$modules[$count]['logo_badge']             = $logo_badge;
				$modules[$count]['downloads']              = $downloads;
				$modules[$count]['logo_rating']            = $logo_rating;
				$modules[$count]['rating']                 = $rating;
				$modules[$count]['version']                = $version;
				$modules[$count]['product_name']           = $product_name;
				$modules[$count]['product_price']          = $product_price;
				$modules[$count]['video_link']             = $video_link;
				$modules[$count]['details_link']           = $details_link;
				$modules[$count]['description']            = $description;
				$modules[$count]['updated_date']           = $updated_date;
				$modules[$count]['new']                    = $new;
				$modules[$count]['demo_link_front_office'] = $demo_link_front_office;
				$modules[$count]['demo_link_back_offile']  = $demo_link_back_offile;

				$count++;
			}

		}

		if (ob_get_level()) ob_end_clean();

		$rss = new XMLWriter();
		$rss->openMemory();
		$rss->setIndent(true);
		$rss->startElement('rss');
		$rss->writeAttribute('version', '2.0');
		$rss->startElement('channel');

		foreach ($modules as $value)
		{
			$display_rating_visibility = empty($value['logo_rating']) ? '0' : '1';
			$display_badge_visibility = empty($value['logo_badge']) ? '0' : '1';
			$display_video_visibility = empty($value['video_link']) ? '0' : '1';

			$rss->writeElement('displayVersion', '1');
			$rss->writeElement('displayRating', '1');
			$rss->writeElement('displayDetails', '1');
			$rss->writeElement('displayVideo', '1');
			$rss->writeElement('displayDemo', '1');
			$rss->writeElement('displayPrice', '1');
			$rss->writeElement('displayNew', '1');
			$rss->writeElement('displayBadge', '1');
				$rss->startElement('item');
					$rss->writeElement('displayRating', $display_rating_visibility);
					$rss->writeElement('displayDetails', '1');
					$rss->writeElement('displayVideo', $display_video_visibility);
					$rss->writeElement('displayDemo', '1');
					$rss->writeElement('displayNew', $value['new']);
					$rss->writeElement('displayBadge', $display_badge_visibility);
					$rss->writeElement('badge', $value['logo_badge']);
					$rss->writeElement('downloads', $value['downloads']);
					$rss->writeElement('version', $value['version']);
					$rss->writeElement('name', $value['product_name']);
					$rss->writeElement('rating', $value['logo_rating']);
					$rss->writeElement('description', $value['description']);
					$rss->writeElement('icon', $value['logo_img']);
					$rss->writeElement('demo', $value['demo_link_back_offile']);
					$rss->writeElement('video', $value['video_link']);
					$rss->writeElement('details', $value['details_link']);
					$rss->writeElement('price', $value['product_price']);
					$rss->writeElement('detailsHTML', 'details');
					$rss->writeElement('videoHTML', 'video');
					$rss->writeElement('demoHTML', 'demo');
					$rss->writeElement('newHTML', 'new');
				$rss->endElement();
		}
		$rss->endElement();
		$rss->endElement();

		return $rss->flush();
	}
}