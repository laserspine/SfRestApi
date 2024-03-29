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
  
  public function getVersions()
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
    $dt = json_encode($data);
    try {
      $uri = str_replace(' ', '+', sprintf('%s', $this->baseUri.'/'.$this->apiVersion.$endpoint));
      $result = $this->guzzle->request($method
                                      ,$uri
                                      ,['headers' => $this->getHeaders()
                                          ,'body' => json_encode($data, JSON_UNESCAPED_SLASHES)]
                                        );
    } catch (\Exception $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }

    return json_decode($result->getBody()->getContents());
  }

  public function requestCstm(String $method, String $endpoint, array $data = [])
  {
    $dt = json_encode($data, JSON_UNESCAPED_SLASHES);
    try {
      $result = $this->guzzle->request($method
                                      ,$endpoint
                                      ,['headers' => $this->getHeaders()
                                          ,'body' => $dt]
                                        );
    } catch (\Exception $e) {
      throw new \Exception($e->getResponse()->getBody()->getContents());
    }

    return json_decode($result->getBody()->getContents());
  }
}



