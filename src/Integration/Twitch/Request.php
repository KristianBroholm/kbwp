<?php
namespace kbwp\Integration\Twitch;

class Request
{
    protected $apiUri;
    protected $headers;
    protected $clientID;
    protected $accessToken;
    protected $query;
    protected $response;
    protected $jsonDecode;

    public function __construct(string $query, string $clientID, AppAccessToken $accessToken, bool $jsonDecode = true) 
    {
        $this->apiUri       = 'https://api.twitch.tv/helix/';
        $this->query        = $query;
        $this->clientID     = $clientID;
        $this->accessToken  = $accessToken;

        $this->headers = [
            'Authorization: Bearer ' . $this->accessToken->get('token'),
            'Client-Id: ' . $this->clientID
        ];
        
        $this->jsonDecode = $jsonDecode;
    }

    public function respond()
    {
        $request = curl_init();
        curl_setopt($request, CURLOPT_URL, $this->apiUri . $this->query);
        curl_setopt($request, CURLOPT_HTTPHEADER, $this->headers);
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($request);
        curl_close($request);

        if (array_key_exists('status', json_decode($response, true)))
        {
            return false;
        }

        if ($this->jsonDecode) {
            $obj = json_decode($response);
            return $obj->{'data'};
        }
        return $response;
    }
}