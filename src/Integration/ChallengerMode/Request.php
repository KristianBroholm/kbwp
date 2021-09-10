<?php
namespace kbwp\Integration\ChallengerMode;
use kbwp\kbwp as kbwp;

class Request
{
    private $headers;
    private $accessToken;
    private $endpoint;
    private $jsonDecode;
    public $response;

    public function __construct(string $endpoint, AccessToken $accessToken, bool $jsonDecode = true, bool $debug = false)
    {
        $this->endpoint     = $endpoint;
        $this->accessToken  = $accessToken;
        $this->jsonDecode   = $jsonDecode;
        $this->debug        = $debug;

        $this->headers = [
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $accessToken->get()
        ];

        $this->response = $this->request();
    }

    private function request()
    {
        $response = wp_remote_get(
            $this->endpoint,
            [
                'headers'   => $this->headers
            ]
        );

        if (200 == $response['response']['code']) 
        {
            $return = $this->jsonDecode ? json_decode($response['body']) : $response['body'];
            return $return;
        }
        if ($this->debug) {
            kbwp::log($response);
        }
        return false;
    }
}