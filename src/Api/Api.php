<?php

namespace Salesforce\Api;

use Salesforce\Interfaces\ApiInterface;

class Api implements ApiInterface
{
  protected $client;

  /**
   * RESTApi constructor.
   * 
   * @param array
   */
  public function __construct(array $params)
  {
    Auth::checkParams($params);
    $this->client = new Client($params);
  }

  public function getClient(): Client
  {
    return $this->client;
  }

  /**
   * Perform a SOQL query on Salesforce
   *
   * @param string $query     The Query string you would like executed on Salesforce.
   * @return bool|mixed       Return False on exception otherwise returns array of records
   */
  public function query(string $query): ?\stdClass
  {
    return $this->client->request('GET', '/query?q=' . $query);
  }

  /**
   * Query additional data over and above the maximum 2000 records returned
   * in a Salesforce REST query.
   *
   * @param string $uri
   *
   * @return mixed
   * @throws \Exception
   */
  public function queryMore(string $uri)
  {
    return $this->client->request('GET', $uri, ['headers' => $this->getHeaders() ]);
  }

  /**
   * Function to insert a single record of an object
   *
   * @param string $sobject   The object to be updated
   * @param array $record     The record data with which to insert
   */
  public function insert(string $sobject, array $record): ?\stdClass
  {
    $uri = '/sobjects/'.$sobject.'/';
   
    return $this->client->request('POST', $uri, $record);
  }

  /**
   * Update Single Record
   *
   * @param string $sobject   The object to be updated
   * @param array $record     The record data with which to update
   */
  public function update(String $sobject, array $record): ?\stdClass
  {
    $id = array_key_exists('Id',$record) ? $record['Id'] : $record['id'];
    $uri = '/sobjects/'.$sobject.'/'.$id;
    unset($record['Id']);
    unset($record['id']);

    return $this->client->request('PATCH', $uri, $record);
  }

  public function upsert (string $object, array $record, String $externalId = null): ?string
  {
    $id = array_key_exists('Id',$record) ? $record['Id'] : $record['id'];
    $uri = '/sobjects/'.$sobject;
    if($externalId)
      $uri .= '/'.$externalId;
    $uri .= '/'.$id;

    return $this->client->request('POST', $uri, $record);
  }
  
  /**
   * Delete Single Record
   *
   * @param string $sobject
   * @param string $id
   * @return null
   */
  public function delete (string $sobject, string $id): ?string
  {
    $uri = '/sobjects/'.$sobject.'/'.$id;
   
    return $this->client->request('DELETE', $uri);
  }

  /**
   * Custom Endpoint
   * currently only set to hit /services/3pcs on LaserSpine Instance
   * @todo build out for full custom REST
   *
   * @param string $method
   * @param string $uri
   * @param array $data
   * @return string
   */
  public function custom(string $method, string $uri, array $data): \stdClass
  {
    return $this->client->request($method, $uri, $data, true);
  }
}