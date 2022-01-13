<?php
namespace kbwp\Integration;

class FaceIt 
{
    private $_api_key;
    private $_id;
    private $_debug;

    public function __construct(string $id, string $api_key, bool $debug_mode_enabled = false)
    {
        $this->_api_key = $api_key;
        $this->_id = $id;
        $this->_debug = $debug_mode_enabled;
    }

    public function getFromAPI(string $request)
    {
        
    }    
}