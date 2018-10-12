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

  public function describe(String $object)
  {
    return $this->request('GET', '/sobjects/'.$object . '/describe');
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources()
  {
    return $this->request('GET', '');
  }

  public function request(String $method, String $endpoint, array $data = [])
  {
    try {
      $uri = str_replace(' ', '+', sprintf('%s', $this->baseUri.'/'.$this->apiVersion.$endpoint));
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



