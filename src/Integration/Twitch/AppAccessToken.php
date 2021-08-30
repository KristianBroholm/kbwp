<?php
namespace kbwp\Integration\Twitch;

class AppAccessToken 
{
    private $_clientID;
    private $_clientSecret;
    private $_optionKey;
    private $_accessToken;
    

    public function __construct(string $clientID, string $clientSecret)
    {
        $this->_clientID        = $clientID;
        $this->_clientSecret    = $clientSecret;
        $this->_optionKey       = get_called_class() . '_' . $clientID;
        delete_option($this->_optionKey);

        if ($this->exists() && $this->isValid()) {
            $this->getTokenFromDatabase();
        } else {
            $this->requestAccessToken();
        }
    }


    private function exists() : bool
    {
        $token = get_option($this->_optionKey);

        if ($token) {
            return true;
        }
        return false;
    }


    private function getTokenFromDatabase()
    {   
        $token = get_option($this->_optionKey);
        $return = json_decode($token);
        $this->_accessToken = $return;
    }


    private function isValid()
    {
        if ($this->exists()) 
        {
            $timestamp  = $this->_accessToken['timestamp'];
            $expires    = $this->_accessToken['expires_in'];

            if ($timestamp + $expires < time()) {
                return true;
            }
        }
        return false;
    }


    private function requestAccessToken() 
    {
        $url = 'https://id.twitch.tv/oauth2/token';
        $post = [
            'client_id'     => $this->_clientID,
            'client_secret' => $this->_clientSecret,
            'grant_type'    => 'client_credentials'
        ];
        
        $request = curl_init($url);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($request, CURLOPT_POSTFIELDS, $post);
        $response = curl_exec($request);
        curl_close($request);
        return $this->storeTokenToDatabase($response);
    }


    private function storeTokenToDatabase($data) 
    {
        $object = json_decode($data);
        $object->{'timestamp'} = time();
        
        $json = json_encode($object);

        if ($this->exists()) {
            update_option($this->_optionKey, $json);
        } else {
            add_option($this->_optionKey, $json);
        }
        return $this->getTokenFromDatabase();
    }


    public function get(string $property = 'token'): string 
    {
        switch($property) {
            case 'token':
                return $this->_accessToken->{'access_token'};
                break;
            case 'type':
                return $this->_accessToken->{'token_type'};
                break;
        }
    }
}