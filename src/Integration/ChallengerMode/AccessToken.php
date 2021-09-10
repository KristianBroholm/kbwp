<?php
namespace kbwp\Integration\ChallengerMode;
use kbwp\kbwp as kbwp;

class AccessToken
{
    protected $handle;
    protected $refreshKey;
    protected $token;
    protected $authUri;
    protected $debug;

    public function __construct(string $handle, string $refreshKey, bool $debug = false)
    {
        $this->handle           = kbwp::slugify($handle);
        $this->authUri          = 'https://publicapi.challengermode.com/mk1/v1/auth/access_keys/';
        $this->refreshKey       = $refreshKey;
        $this->debug            = $debug;
        $this->token            = $this->setAccessToken();
    }

    private function setAccessToken()
    {
        if ($this->isValid()) {
            return $this->getFromDatabase();
        } else {
            return $this->requestAccessTokenFromServer();
        }
    }

    private function isValid() : bool
    {
        $token = $this->getFromDatabase();
        if (false !== $token) 
        {
            if ($token['expires'] > time())
            {
                return true;
            }
        }
        return false;
    }

    private function getFromDatabase() 
    {
        $token = get_option($this->handle);
        
        if ($token) 
        {
            return json_decode($token, true);
        }
        return false;
    }

    private function requestAccessTokenFromServer()
    {
        $response = wp_remote_post(
            $this->authUri,
            [
                'headers' => [
                    'Content-Type'  => 'application/json',
                ],
                'body'  => wp_json_encode(['refreshKey' => $this->refreshKey])

            ]
        );

        if (200 == $response['response']['code']) 
        {
            $json = json_decode($response['body']);

            $accessToken = [
                'token'     => $json->{'value'},
                'expires'   => strtotime($json->{'expiresAt'}),
            ];

            $option = json_encode($accessToken);

            if (get_option($this->handle)) 
            {
                update_option($this->handle, $option);
            } else {
                add_option($this->handle,$option);
            }
            return $this->getFromDatabase();
        }
        delete_option($this->handle);

        if ($this->debug) {
            kbwp::log($response);
        }
        return null;
    }

    public function get() {
        return $this->token['token'];
    }
}