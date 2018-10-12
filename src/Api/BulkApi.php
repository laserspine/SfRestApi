<?php

namespace Salesforce\Api;

use Salesforce\Job\Job;
use Salesforce\Api;

/**
 * Utilize Salesforce Bulk API
 */
class BulkApi
{
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

  /**
   * Helper function to insert a batch of records through the bulk api
   *
   * @param string $sobject   The object to be updated
   * @param array $record     The data with which to update
   */
  public function insert(string $sobject, array $records)
  {
    return $this->sendNewBatch( $sobject, 'insert', json_encode( $records ) );
  }

  /**
   * Helper function to update a batch of records through the bulk api
   *
   * @param string $sobject   The object to be updated
   * @param array  $records   The data with which to update
   *
   * @return bool
   */
  public function update(string $sobject, array $records)
  {
    return $this->sendNewBatch( $sobject, 'update', json_encode( $records ) );
  }

  public function delete(string $sobject, array $records)
  {

  }

  
}