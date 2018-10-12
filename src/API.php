<?php

namespace Salesforce;

use Salesforce\Api\Api as Salesforce;

class API extends Salesforce
{
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
}