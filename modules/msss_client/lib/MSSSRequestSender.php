<?php


class MSSSRequestSender
{
    const UserAgent = '';
    
    /**
     * Sends post request to specified url and returns answer
     * @param type $url
     * @param array $parameters
     * @return array(status, responseBody)
     */
    static function sendPostRequest($url, array $parameters)
    {
        $query = self::_getParametersAsString($parameters);
        $url = parse_url($url);
        $uri = array_key_exists('path', $url) ? $url['path'] : null;
        if (!isset ($uri)) {
                $uri = "/";
        }
        $scheme = '';

        switch ($url['scheme']) {
            case 'https':
                $scheme = 'https://';
                $port = empty($url['port']) ? 443 : $url['port'];
                break;
            default:
                $scheme = 'http://';
                $port = empty($url['port']) ? 80 : $url['port'];
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $scheme . $url['host'] . $uri);
        curl_setopt($ch, CURLOPT_PORT, $port);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, self::UserAgent);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query);
        curl_setopt($ch, CURLOPT_HEADER, true); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        //echo $response."\n";
        
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $errorMsg = '';
        if($httpCode != 200) {
            /* Handle 400 here. */
            $errorMsg = curl_error($ch);
        }

        $headerSize = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);
        //$header = substr($result , 0, $header_size);
        $body = substr($response , $headerSize);

        return array ('status' => (int)$httpCode, 'error'=>$errorMsg, 'responseBody' => $body);
    }
    
    
    /**
     * Convert paremeters to Url encoded query string
     */
    protected static function _getParametersAsString(array $parameters)
    {
        $queryParameters = array();
        foreach ($parameters as $key => $value) {
            $queryParameters[] = $key . '=' . rawurlencode($value);
        }
        return implode('&', $queryParameters);
    }
}