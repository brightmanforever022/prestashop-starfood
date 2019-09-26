<?php

class Tools extends ToolsCore
{
    /**
    * Return price with currency sign for a given product
    *
    * @param float $price Product price
    * @param object|array $currency Current currency (object, id_currency, NULL => context currency)
    * @return string Price correctly formated (sign, decimal separator...)
    */
    public static function displayPrice($price, $currency = null, $no_utf8 = false, Context $context = null)
    {
        if (!is_numeric($price)) {
            return $price;
        }
        if (!$context) {
            $context = Context::getContext();
        }
        if ($currency === null) {
            $currency = $context->currency;
        }
        // if you modified this function, don't forget to modify the Javascript function formatCurrency (in tools.js)
        elseif (is_int($currency)) {
            $currency = Currency::getCurrencyInstance((int)$currency);
        }

        if (is_array($currency)) {
            $c_char = $currency['sign'];
            $c_format = $currency['format'];
            $c_decimals = (int)$currency['decimals'] * _PS_PRICE_DISPLAY_PRECISION_;
            $c_blank = $currency['blank'];
        } elseif (is_object($currency)) {
            $c_char = $currency->sign;
            $c_format = $currency->format;
            $c_decimals = (int)$currency->decimals * _PS_PRICE_DISPLAY_PRECISION_;
            $c_blank = $currency->blank;
        } else {
            return false;
        }

        $blank = ($c_blank ? ' ' : '');
        $ret = 0;
        if (($is_negative = ($price < 0))) {
            $price *= -1;
        }
        $price = Tools::ps_round($price, $c_decimals);

        /*
        * If the language is RTL and the selected currency format contains spaces as thousands separator
        * then the number will be printed in reverse since the space is interpreted as separating words.
        * To avoid this we replace the currency format containing a space with the one containing a comma (,) as thousand
        * separator when the language is RTL.
        *
        * TODO: This is not ideal, a currency format should probably be tied to a language, not to a currency.
        */
        if (($c_format == 2) && ($context->language->is_rtl == 1)) {
            $c_format = 4;
        }

        switch ($c_format) {
            /* X 0,000.00 */
            case 1:
                $ret = $c_char.$blank.number_format($price, $c_decimals, '.', ',');
                break;
            /* 0 000,00 X*/
            case 2:
                $ret = number_format($price, $c_decimals, ',', ' ').$blank.$c_char;
                break;
            /* X 0.000,00 */
            case 3:
                $ret = $c_char.$blank.number_format($price, $c_decimals, ',', '.');
                break;
            /* 0,000.00 X */
            case 4:
                $ret = number_format($price, $c_decimals, '.', ',').$blank.$c_char;
                break;
            /* X 0'000.00  Added for the switzerland currency */
            case 5:
                $ret = number_format($price, $c_decimals, '.', "'").$blank.$c_char;
                break;
            /* 0.000,00 X*/
            case 6:
                $ret = number_format($price, $c_decimals, ',', '.').$blank.$c_char;
                break;
        }
        if ($is_negative) {
            $ret = '-'.$ret;
        }
        if ($no_utf8) {
            return str_replace('€', chr(128), $ret);
        }
        return $ret;
    }
    
    
    /**
    * Display date regarding to language preferences
    *
    * @param string $date Date to display format UNIX
    * @param int $id_lang Language id DEPRECATED
    * @param bool $full With time or not (optional)
    * @param string $separator DEPRECATED
    * @return string Date
    */
    public static function displayDateLang($date, $id_lang, $full = false)
    {
        if (!$date || !($time = strtotime($date))) {
            return $date;
        }

        if ($date == '0000-00-00 00:00:00' || $date == '0000-00-00') {
            return '';
        }

        if (!Validate::isDate($date) || !Validate::isBool($full)) {
            throw new PrestaShopException('Invalid date');
        }

        $language = Language::getLanguage($id_lang);
        $date_format = ($full ? $language['date_format_full'] : $language['date_format_lite']);
        return date($date_format, $time);
    }
    
    
    /**
    * Change language in cookie while clicking on a flag
    *
    * @return string iso code
    */
//    public static function setCookieLanguage($cookie = null)
//    {
//        if (!$cookie) {
//            $cookie = Context::getContext()->cookie;
//        }
//        /* If language does not exist or is disabled, erase it */
//        if ($cookie->id_lang) {
//            $lang = new Language((int)$cookie->id_lang);
//            if (!Validate::isLoadedObject($lang) || !$lang->active || !$lang->isAssociatedToShop()) {
//                $cookie->id_lang = null;
//            }
//        }
//
//        if (!Configuration::get('PS_DETECT_LANG')) {
//            unset($cookie->detect_language);
//        }
//
//        /* Automatically detect language if not already defined, detect_language is set in Cookie::update */
//        if (!Tools::getValue('isolang') && !Tools::getValue('id_lang') && (!$cookie->id_lang || isset($cookie->detect_language))
//            && isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
//            $array  = explode(',', Tools::strtolower($_SERVER['HTTP_ACCEPT_LANGUAGE']));
//            $string = $array[0];
//
//            if (Validate::isLanguageCode($string)) {
//                $lang = Language::getLanguageByIETFCode($string);
//                if (Validate::isLoadedObject($lang) && $lang->active && $lang->isAssociatedToShop()) {
//                    Context::getContext()->language = $lang;
//                    $cookie->id_lang = (int)$lang->id;
//                }
//                else
//                {
//                    $lang = new Language(Language::getIdByIso('en'));
//                    Context::getContext()->language = $lang;
//                    $cookie->id_lang = (int)$lang->id;
//                }
//            }
//        }
//
//        if (isset($cookie->detect_language)) {
//            unset($cookie->detect_language);
//        }
//
//        /* If language file not present, you must use default language file */
//        if (!$cookie->id_lang || !Validate::isUnsignedId($cookie->id_lang)) {
//            $cookie->id_lang = (int) Language::getIdByIso('en');
//        }
//
//        $iso = Language::getIsoById((int)$cookie->id_lang);
//        @include_once(_PS_THEME_DIR_.'lang/'.$iso.'.php');
//
//        return $iso;
//    }
}