<?php

class FFApi
{

    protected $ff_url;
    protected $ff_url_auth;
    protected $ff_version = "v1.1";

    protected $login;
    protected $pass;


    /**
     * Inform login and pass if exist
     * and configure access urls
     *
     * @param string $login,$pass
     * @return -
     */
    function __construct($login="", $pass="") {
        if (!extension_loaded('curl'))
            throw new FFApiException('Please activate the PHP extension \'curl\' to allow use of FouleFactory Web Services');
        if (!empty($login))
            $this->login = $login;
        if (!empty($pass))
        {
            $pass = urlencode($pass);
            $this->pass = $pass;
        }

        $this->ff_url = "https://sandbox-api.foulefactory.com/".$this->ff_version."/";
        if(!empty($login) && !empty($pass))
            $this->ff_url_auth = "https://".$login.":".$pass."@sandbox-api.foulefactory.com/".$this->ff_version."/";
        /*$this->ff_url = "https://api.foulefactory.com/".$this->ff_version."/";
        if(!empty($login) && !empty($pass))
            $this->ff_url_auth = "https://".$login.":".$pass."@api.foulefactory.com/".$this->ff_version."/";*/
        /*$this->ff_url = "https://cd-api.foulefactory.com/".$this->ff_version."/";
        if(!empty($login) && !empty($pass))
            $this->ff_url_auth = "https://".$login.":".$pass."@cd-api.foulefactory.com/".$this->ff_version."/";*/
    }

    /**
     * Inform login
     *
     * @param string $login
     * @return -
     */
    public function setLogin($login)
    {
        if (!empty($login))
            $this->login = $login;
    }
    /**
     * Inform pass
     *
     * @param string $pass
     * @return -
     */
    public function setPass($pass)
    {
        if (!empty($pass))
            $this->pass = $pass;
    }

    /**
     * Check status code and make
     * exception if code is error
     *
     * @param int $status_code
     * @return -
     */
    protected function checkStatusCode($return)
    {
        $status_code = $return['status_code'];
        $error_label = 'This call to FouleFactory Web Services failed and returned an HTTP status of %d. That means: %s.';
        switch($status_code)
        {
            case 200:	case 201: break;
            case 400:
                $obj = $return["response"];
                if(!empty($obj->Message))
                    $return['message'] = $obj->Message;
                break;
            case 204: $return['message'] = (sprintf($error_label, $status_code, 'No content'));break;
            //case 400: throw new FFApiException(sprintf($error_label, $status_code, 'Logical error'));break;
            case 401: $return['message'] = (sprintf($error_label, $status_code, 'Access is forbidden'));break;
            case 404: $return['message'] = (sprintf($error_label, $status_code, 'Object not found'));break;
            case 405: $return['message'] = (sprintf($error_label, $status_code, 'Method Not Allowed'));break;
            case 500: $return['message'] = (sprintf($error_label, $status_code, 'Internal Server Error'));break;
            default: $return['message'] = ('This call to FouleFactory Web Services returned an unexpected HTTP status of:' . $status_code);
        }
        if($status_code!=200 && $status_code!=201)
        {
            $return['message'] .= "<br/>".$return['response']->Message." (#".$status_code.")";
            if(!empty($return['response']->Errors) && is_array($return['response']->Errors))
                $return['message'] .=  "<br/>".implode("<br/>", $return['response']->Errors);
            elseif(!empty($return['response']->Errors))
                $return['message'] .=  "<br/>".$return['response']->Errors;
        }
        return $return;
    }

    /**
     * Make the request in GET or POST
     *
     * @param string $url (projects, account, ...)
     *        boolean $post (true if for update or add, false for get informations)
     *        array $post_data (params sending in post)
     * @return array returned by API + status code
     */
    protected function executeRequest($url, $post=false, $post_data=array())
    {
        $status_code = "200";

        if(!empty($this->ff_url_auth))
            $service_url =  $this->ff_url_auth.$url;
        else
            $service_url =  $this->ff_url.$url;
        $curl = curl_init($service_url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        if($post)
        {
            curl_setopt($curl, CURLOPT_POST, true);
            if(!empty($post_data) && is_array($post_data))
            {
                $data_string = json_encode($post_data);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
                curl_setopt($curl, CURLOPT_HTTPHEADER, array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($data_string))
                );
            }
        }

        $curl_response = curl_exec($curl);
        if ($curl_response === false) {
            $info = curl_getinfo($curl);
            curl_close($curl);
            throw new FFApiException('error occured during curl exec. Additional info: ' . var_export($info));
        }

        $info = curl_getinfo($curl);
        curl_close($curl);

        $decoded = json_decode($curl_response);
        if(!empty($info["http_code"]))
            $status_code = $info["http_code"];

        return array('status_code' => $status_code,'message'=>'', 'response' => $decoded);
    }

    /**
     * Make a GET request for get information
     *
     * @param string $url (projects, account, ...)
     * @return array returned by API + status code
     */
    public function queryGet($url)
    {
        if (empty($url))
            return false;
        $results = self::executeRequest($url);
        $results = self::checkStatusCode($results);// check the response validity
        return $results;
    }

    /**
     * Make a POST request for update or add information
     *
     * @param string $url (projects, account, ...)
     *        array $params (params sending in post)
     * @return array returned by API + status code
     */
    public function queryPost($url,$params)
    {
        if (empty($url))
            return false;
        $results = self::executeRequest($url, true, $params);
        $results = self::checkStatusCode($results);// check the response validity
        return $results;
    }

    static function getIdProjectReseller($quality, $type="feature")
    {
        $return = null;
        // PROD
        if(empty($type) || $type=="feature")
        {
            if($quality=="good")
                $return = 0;
            elseif($quality=="higher")
                $return = 3;
            elseif($quality=="excellent")
                $return = 5;
        }
        elseif($type=="desc_short")
        {
            if($quality=="10")
                $return = 6;
            elseif($quality=="20")
                $return = 7;
            elseif($quality=="30")
                $return = 8;
            elseif($quality=="50")
                $return = 9;
            elseif($quality=="100")
                $return = 10;
        }
        elseif($type=="desc_long")
        {
            if($quality=="50")
                $return = 9;
            elseif($quality=="100")
                $return = 10;
            elseif($quality=="150")
                $return = 11;
            elseif($quality=="250")
                $return = 12;
            elseif($quality=="350")
                $return = 13;
            elseif($quality=="500")
                $return = 14;
            elseif($quality=="750")
                $return = 15;
        }
        // SANDBOX
        if(empty($type) || $type=="feature")
        {
            if($quality=="good")
                $return = 0;
            elseif($quality=="higher")
                $return = 2;
            elseif($quality=="excellent")
                $return = 4;
        }
        elseif($type=="desc_short")
        {
            if($quality=="10")
                $return = 5;
            elseif($quality=="20")
                $return = 6;
            elseif($quality=="30")
                $return = 7;
            elseif($quality=="50")
                $return = 7;
            elseif($quality=="100")
                $return = 7;
        }
        elseif($type=="desc_long")
        {
            if($quality=="50")
                $return = 8;
            elseif($quality=="100")
                $return = 9;
            elseif($quality=="150")
                $return = 10;
            elseif($quality=="250")
                $return = 11;
            elseif($quality=="350")
                $return = 12;
            elseif($quality=="500")
                $return = 13;
            elseif($quality=="750")
                $return = 17;
        }
        return $return;
    }

}


class FFApiException extends Exception { }
