<?php

/* 
 * Class is purposed to record events
 */


class MSSSLog
{
    static function reportError($subject, $message)
    {
        $idLang = Context::getContext()->cookie->id_lang;
        $idShop = Context::getContext()->shop->id;
        
        if(empty(Configuration::get('MSSS_CLIENT_DONT_SEND_EMAIL_TO_SHOP_ADMIN')))
        {
            $to = [Configuration::get('PS_SHOP_EMAIL', null, null, $idShop)];
        }
        else
        {
            $to = [];
        }
        if (!empty(Configuration::get('MSSS_CLIENT_DEBUG_EMAIL')))
        {
            $to []= Configuration::get('MSSS_CLIENT_DEBUG_EMAIL');
        }
        
        if (count($to))
        {
            Mail::Send(
                $idLang, 'admin_error', 'msss client error '._PS_BASE_URL_.': '.$subject, ['{message}' => nl2br($message)], 
                $to, 
                null, (string) Configuration::get('PS_SHOP_EMAIL', null, null, $idShop), 
                (string) Configuration::get('PS_SHOP_NAME', null, null, $idShop), null, null, dirname(__FILE__) . '/../mails/', false, $idShop
            );
        }
    }

}
