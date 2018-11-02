<?php

namespace Salesforce\Api;

use Salesforce\Api\Auth;

class Client extends Auth
{
  public function __construct(array $params)
  {
    parent::__construct($params);
  }

  public function testConnection(): void
  {
    $this->getVersions();
  }
  
  public function getVersions(): array
  {
    $uri = '/services/data/';
    $response = $this->guzzle->request('GET', $uri);
    
    return json_decode($response->getBody(true));
  }

  public function describe(String $object): ?\stdClass
  {
    return $this->request('GET', '/sobjects/'.$object . '/describe');
  }

  /**
  * @return array of Resources
  */
  public function getAvailableResources(): ?\stdClass
  {
    return $this->request('GET', '');
  }

  public function request(String $method, String $endpoint, array $data = [], bool $isCstm = false): ?\stdClass
  {
    $dt = json_encode($data);
    
    $uri = str_replace(' ', '+', sprintf('%s', $this->baseUri.'/'.$this->apiVersion.$endpoint));
    if($isCstm)
      $uri = $endpoint;

    try {
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
}



