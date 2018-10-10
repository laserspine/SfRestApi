<?php

namespace Salesforce\Api;

use Salesforce\Api\Auth;

class Client extends Auth
{
  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  public function connect() 
  {
    $this->getVersions();
  }
  
  public function getVersions()
  {
    $uri = '/services/data/';
    $response = $this->guzzle->request('GET', $uri);
    
    return json_decode($response->getBody(true), true);
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources()
  {
    $uri = sprintf('/services/data/%s', $this->apiVersion);
    $response = $this->guzzle->request('GET', $uri, [ 'headers' => $this->getHeaders()]);

    return json_decode($response->getBody(true), true);
  }
}

