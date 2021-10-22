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
    protected $spaceId;

    protected function __construct(string $handle, string $refreshKey, bool $debug = false)
    {
        $this->handle                       = kbwp::slugify($handle);
        $this->apiUri                       = 'https://publicapi.challengermode.com/mk1/v1';
        $this->refreshKey                   = $refreshKey;
        $this->debug                        = $debug;
        $this->accessToken                  = new ChallengerMode\AccessToken($this->handle, $this->refreshKey, $this->debug);
        delete_option($this->prefix('challengermode-space-id'));
        $this->spaceId                      = $this->setSpaceId();
        self::$instances[$this->handle]     = $this;
    }


    public function __toString()
    {
        $class = get_called_class();
        return $class;
    }


    public static function init(string $handle, string $refreshKey, bool $debug = false)
    {
        $class = get_called_class();

        if (!isset(self::$instances[$class]))
        {
            self::$instances[$class] = new $class($handle, $refreshKey, $debug);
        }
        return self::$instances[$class];
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


    protected function setSpaceId()
    {
        $id = get_option($this->prefix('challengermode-space-id'));

        if (!$id) {
            $id = $this->getSpaceIdFromAPI();
            
            if ($id)
            {
                add_option($this->prefix('challengermode-space-id'), $id);
            }
        }
        return $id;
    }


    protected function getSpaceId()
    {
        $id = get_option($this->prefix('challengermode-space-id'));
        return $id;
    }


    public function prefix(string $string = '', string $separator = '_')
    {
        if (!empty($string))
        {
            return $this->handle . $separator . $string;
        }
        return $this->handle;
    }


    public function getSpaceIdFromAPI() 
    {
        $response = $this->get('/spaces/search?slug=' . $this->handle);
        if (is_array($response->ids))
        {
            $id = $response->ids[0];
            return $id;
        }
        return null;
    }


    public function getTeamFromAPI(string $teamId)
    {
        $team = $this->get('/tournaments/lineups/' . $teamId);
        
        if ($team) 
        {
            return $result = [$teamId => $team];
        }
        return false;
    }


    public function getTeamsFromAPI(array $teamIds)
    {
        foreach($teamIds as $id)
        {
            $team = $this->getTeamFromAPI($id);
            if ($team)
            {
                $results[] = $team;
            }
        }
        return $results ? $results : false;
    }


    public function getTournamentIdsFromAPI() 
    {
        $space_id = $this->getSpaceId();

        if ($space_id) 
        {
            $tournaments = $this->get('/tournaments/search?space_id=' . $space_id);
            if ($tournaments) {
                return $tournaments->ids;
            }
        }
        return false;
    }


    public function getTournamentFromAPI(string $id)
    {
        $tournament = $this->get('/tournaments/' . $id);

        if ($tournament) {
            return $tournament;
        }
        return false;
    }


    public function getTournamentsFromAPI(array $ids)
    {   
        foreach($ids as $id) 
        {
            $tournament = $this->getTournamentFromAPI($id);
            
            if ($tournament) {
                $results[$id] = $tournament;
            }
        }
        return $results ? $results : false;
    }
}