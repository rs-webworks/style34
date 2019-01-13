<?php

namespace EryseClient\Service;

use Unirest\Request;
use Unirest\Response;

/**
 * Class ServerRestService
 * @package EryseClient\Service
 */
final class ServerRestService extends AbstractService
{
    /**
     * @var string
     */
    private $serverUrl;

    /**
     * RestService constructor.
     * @param string $serverUrl
     */
    public function __construct(string $serverUrl)
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     *
     */
    public function prepareHeaders()
    {
        return array('Accept' => 'application/json');
    }

    /**
     * @param $url
     * @param $query
     * @return \Unirest\Response
     */
    public function get($url, $query)
    {
        return Request::get($this->serverUrl . $url, $query, $this->prepareHeaders());
    }


    /**
     * @param $url
     * @param $query
     * @return \Unirest\Response
     */
    public function post($url, $query)
    {
        return Request::post($this->serverUrl . $url, $query, $this->prepareHeaders());
    }

    /**
     * @param $url
     * @param $query
     * @return Response
     */
    public function put($url, $query)
    {
        return Request::put($this->serverUrl . $url, $query, $this->prepareHeaders());
    }

    /**
     * @param $url
     * @param $query
     * @return Response
     */
    public function patch($url, $query)
    {
        return Request::patch($this->serverUrl . $url, $query, $this->prepareHeaders());
    }

    /**
     * @param $url
     * @param $query
     * @return Response
     */
    public function delete($url, $query)
    {
        return Request::delete($this->serverUrl . $url, $query, $this->prepareHeaders());
    }
}