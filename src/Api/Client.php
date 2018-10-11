<?php

namespace Salesforce\Api;

use Salesforce\Api\Auth;

class Client extends Auth
{
  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  public function testConnection()
  {
    $this->getVersions();
  }
  
  public function getVersions(): array
  {
    $uri = '/services/data/';
    $response = $this->guzzle->request('GET', $uri);
    
    return json_decode($response->getBody(true));
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources(): \stdClass
  {
    $uri = sprintf('/services/data/%s', $this->apiVersion);
    $response = $this->guzzle->request('GET'
                                  ,$uri
                                  ,[ 'headers' => $this->getHeaders()]
                                );

    return json_decode($response->getBody(true));
  }

  public function request(String $method, String $endpoint, array $data = []) : \stdClass
  {
    try {
      $uri = str_replace(' ', '+', sprintf('%s', $this->baseUri.'/'.$this->apiVersion.'/'.$endpoint));
      $result = $this->guzzle->request($method
                                      ,$uri
                                      ,['headers' => $this->getHeaders()
                                          ,'body' => json_encode($data)]
                                        );
    } catch (\Exception $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }

    return json_decode($result->getBody()->getContents());
  }
}



