<?php
namespace kbwp\Integration;
use kbwp\kbwp as kbwp;

class FaceIt 
{
    private $_instances = [];
    private $_api_key;
    private $_id;
    private $_debug;
    private $_api_url;

    private function __construct(string $id, string $api_key, bool $debug_mode_enabled = false)
    {
        $this->_api_key         = $api_key;
        $this->_id              = $id;
        $this->_debug           = $debug_mode_enabled;
        $this->_api_url         = 'https://open.faceit.com/data/v4/';
        self::$_instances[$id]  = $this;
    }

    public static function init(string $id, string $api_key, bool $debug_mode_enabled = false)
    {
        if (in_array($id, self::$_instances))
        {
            return self::$instances[$id];
        }
        return new self($id, $api_key, $debug_mode_enabled);
    }

    public function get_from_API(string $endpoint, bool $json_decode = true)
    {
        $url = $this->_api_url . $endpoint;

        $headers = [
            'accept'        => 'application/json',
            'Authorization' => 'Bearer ' . $this->_api_key  
        ];

        $response = wp_remote_get(
            $url,
            [
                'headers'   => $headers
            ]
        );

        if ($this->_debug)
        {
            kbwp::log($response);
        }

        if (200 === $response['response']['code'])
        {
            return $json_decode ? json_decode($response['body'], true) : $response['body'];
        }
        return false;
    }    
}