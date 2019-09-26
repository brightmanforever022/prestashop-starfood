<?php

/**
 * Creates messages between master and slave and signs them,
 * checks signature of existing messages
 * 
 * Authentication is done by ip address and by crypting with known secret key.
 */
class MSSSMessageCreator
{
    /**
     * Generates text that will be sent from master to slave in request
     * @param type $messages array of messages that need to be sent
     * @param $secret secret password for encryption of message
     */
    static function createMessage(array $messages, $secret)
    {
        $m2sMsg = json_encode($messages);
        $iv = self::generateIV();
        $cipher = new Blowfish($secret, $iv);
        return $iv.'$'.$cipher->encrypt($m2sMsg);
    }
    
    
    static function parseMessage($msg, $secret)
    {
        list($iv, $message) = explode('$', $msg);

        $cipher = new Blowfish($secret, $iv);
        $message = json_decode($cipher->decrypt($message), true);
        
        if (!$message)
        {
            throw new Exception('Error while decrypting message');
        }
        
        return $message;
    }
 
    
    static function generateIV()
    {
        if (!function_exists('mcrypt_encrypt'))
        {
            echo ('Mcrypt is not activated on this server.');
            exit;
        }
	$iv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
	return base64_encode(mcrypt_create_iv($iv_size, MCRYPT_RAND));
    }
    
    static function generateKey()
    {
        if (!function_exists('mcrypt_encrypt'))
        {
            echo ('Mcrypt is not activated on this server.');
            exit;
        }
        $key_size = mcrypt_get_key_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
        return Tools::passwdGen($key_size);
    }
}