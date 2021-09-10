<?php
namespace kbwp\Integration;
use kbwp\kbwp as kbwp;

class ChallengerMode 
{
    protected static $instances = [];
    protected $handle;
    protected $apiUri;
    protected $refreshKey;
    protected $debug;
    protected $accessToken;

    protected function __construct(string $handle, string $refreshKey, bool $debug = false)
    {
        $this->handle                   = kbwp::slugify($handle);
        $this->apiUri                   = 'https://publicapi.challengermode.com/mk1/v1';
        $this->refreshKey               = $refreshKey;
        $this->debug                    = $debug;
        $this->accessToken              = new ChallengerMode\AccessToken($this->handle, $this->refreshKey, $this->debug);
        self::$instances[$this->handle]    = $this;
    }

    public static function init(string $handle, string $refreshKey, bool $debug = false)
    {
        if (array_key_exists($handle, self::$instances)) 
        {
            return self::$instances[$handle];
        }
        return new self($handle, $refreshKey, $debug);
    }

    public function get(string $query, bool $jsonDecode = true, int $cacheForSeconds = 0)
    {
        $transientKey = $this->handle . $query;
        $endpoint = $this->apiUri . $query;
        
        if (0 < $cacheForSeconds) 
        {
            $transient = get_transient($transientKey);

            if (!$transient) 
            {
                $request = new ChallengerMode\Request($endpoint, $this->accessToken, false, $this->debug);
                set_transient($transientKey, $request->response, $cacheForSeconds);
                $transient = get_transient($transientKey);
            }
            return $jsonDecode ? json_decode($transient) : $transient;
        }
        delete_transient($transientKey);
        $request = new ChallengerMode\Request($endpoint, $this->accessToken, $jsonDecode, $this->debug);
        return $request->response;
    }

    public function prefix(string $string = '', string $separator = '_')
    {
        if (!empty($string))
        {
            return $this->handle . $separator . $string;
        }
        return $this->handle;
    }
}