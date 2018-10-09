<?php

namespace Salseforce\Request;

class Client extends Auth
{
  public function __construct(array $params)
  {
    parent::__construct($params);
  }
  
  public function getVersions()
  {
    $uri = '/services/data/';

    return $this->client->request('GET', $uri);
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources()
  {
    $uri = sprintf('/services/data/%s', $this->apiVersion);
    $response = $this->client->request('GET', $uri, [ 'headers' => $this->getHeaders()]);

    return json_decode($response->getBody(true), true);
  }
}

