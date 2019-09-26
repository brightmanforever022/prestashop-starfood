<?php 
class Link extends LinkCore
{
	/**
	 * @see LinkCore::goPage()
	 * @param $url
	 * @param $p
	 * @param $seo if true then page will be added in seo manner 
	 */
	public function goPage($url, $p, $seo=false)
	{
		if ($seo)
		{
			if ($p==1)
			{
				return $url;
			}
			if (strpos($url, '#')===false)
			{
				$url .= '#';
			}
			return $url.'/page-'.$p;
		}
		else
		{
			$url = rtrim(str_replace('?&', '?', $url), '?');
			return $url.($p == 1 ? '' : (!strstr($url, '?') ? '?' : '&').'p='.(int)$p);
		}
	}
}