<?php

namespace Salesforce\Api;

use Salesforce\Job\Job;

class BulkApi implements BulkInterface
{
  /**
   * @var Saleforce\Api\Client
   */
  protected $client;

  /**
   * RESTApi constructor.
   *
   * @param string $key
   * @param string $secret
   * @param string $user
   * @param string $pass
   * @param string $token
   * @param string $baseUrl
   */
  public function __construct(array $params)
  {
    Auth::checkParams($params);
    $this->client = new Client($params);
  }

  public function insert(string $sobject, array $records)
  {

  }

  public function update(string $sobject, array $records)
  {
    
  }

  public function delete(string $sobject, array $records)
  {

  }
}