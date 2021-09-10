<?php
namespace kbwp\Integration\Twitch;
use kbwp\kbwp as kbwp;

class AppAccessToken 
{
    private $clientID;
    private $clientSecret;
    private $optionKey;
    private $accessToken;
    

    public function __construct(string $clientID, string $clientSecret, bool $debug = false)
    {
        $this->clientID        = $clientID;
        $this->clientSecret    = $clientSecret;
        $this->optionKey       = get_called_class() . '_' . $clientID;
        $this->debug           = $debug;
        $this->authUri         = 'https://id.twitch.tv/oauth2/token';
        $this->accessToken     = $this->setAccessToken();
    }   

    private function exists() : bool
    {
        $token = get_option($this->optionKey);
        if ($token) {
            return true;
        }
        return false;
    }

    private function setAccessToken()
    {   
        if ($this->exists()) {
            $token = $this->getTokenFromDatabase();
            if ($token['timestamp'] + $token['expires_in'] > time()) {
                return $token;
            }
        }
        return $this->requestAccessToken();
    }

    private function getTokenFromDatabase()
    {   
        $token = get_option($this->optionKey);
        $return = json_decode($token, true);
        return $return;
    }

    private function requestAccessToken() 
    {
        $response = wp_remote_post(
            $this->authUri,
            [
                'body' => [
                    'client_id'     => $this->clientID,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'client_credentials'

                ]
            ]
        );

        if (200 === $response['response']['code']) {
            $this->storeTokenToDatabase($response['body']);
        }
        
    }

    private function storeTokenToDatabase($data) 
    {
        $object = json_decode($data);
        $object->{'timestamp'} = time();
        $json = json_encode($object);
  
        if ($this->exists()) {
            update_option($this->optionKey, $json);
        } else {
            add_option($this->optionKey, $json);
        }
        return $this->getTokenFromDatabase();
    }

    public function get(string $property = 'token'): string 
    {
        switch($property) {
            case 'token':
                return $this->accessToken['access_token'];
                break;
            case 'type':
                return $this->accessToken['token_type'];
                break;
        }
    }
}