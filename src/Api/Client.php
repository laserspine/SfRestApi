<?php

namespace Salesforce\Api;

use Salesforce\Api\Auth;

class Client extends Auth
{
  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  public function test_connection(): string
  {
    $this->getVersions();
  }
  
  public function getVersions() : string
  {
    $uri = '/services/data/';
    $response = $this->guzzle->request('GET', $uri);
    
    return json_decode($response->getBody(true), true);
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources(): string
  {
    $uri = sprintf('/services/data/%s', $this->apiVersion);
    $response = $this->guzzle->request('GET', $uri, [ 'headers' => $this->getHeaders()]);

    return json_decode($response->getBody(true), true);
  }

  public function makeRequest(String $method, String $endpoint, array $data = []) : string
  {
    try {
      $uri = str_replace(' ', '+', sprintf('%s', $this->baseUri.'/'.$this->apiVersion.'/'.$endpoint));
      $result = $this->guzzle->request($method, $uri, ['headers' => $this->getHeaders()]);
    } catch (\Exception $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }

    return json_decode($result->getBody()->getContents());
  }
}



