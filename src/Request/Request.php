<?php

namespace Salesforce\Request;

use Client;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception;
use Salesforce\Interfaces\RequestInterface;

class Request implements RequestInterface
{
  private $client;
  
  public function __construct(array $params) 
  {
    $thisClient = new Client($params);
  }
  
  /**
   * Make Request
   * 
   * @param Client $client
   */
  public static function makeRequest (GuzzleClient $client) {
    
  }
}

