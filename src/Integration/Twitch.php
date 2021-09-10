<?php
namespace kbwp\Integration;

class Twitch 
{
    protected static $instances = [];
    protected $clientID;
    protected $clientSecret;
    protected $accessToken;

    private function __construct(string $clientID, string $clientSecret)
    {
        $this->clientID                = $clientID;
        $this->clientSecret            = $clientSecret;  
        $this->accessToken             = new Twitch\AppAccessToken($this->clientID, $this->clientSecret);
        self::$instances[$clientID]    = $this;
    }

    public static function init(string $clientID, string $clientSecret)
    {
        if (in_array($clientID, self::$instances))
        {
            return self::$instances[$clientID];
        }
        return new self($clientID, $clientSecret);
    }

    public function get(string $query, bool $jsonDecode = true)
    {
        $request = new Twitch\Request($query, $this->clientID, $this->accessToken, $jsonDecode);
        return $request->response();
    }
}