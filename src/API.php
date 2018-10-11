<?php

namespace Salesforce;

use Salesforce\Api\Auth;
use Salesforce\Api\Client;

class API
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
    public function query(string $query)
    {
        return $this->client->makeRequest('GET', 'query?q=' . $query);
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
        try {
            $result = $this->client->request('GET', $uri, ['headers' => $this->getHeaders() ]);
        } catch (GuzzleException $e) {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        return json_decode($result->getBody()->getContents());
    }

    /**
     * Function to insert a single record of an object
     *
     * @param string $sobject   The object to be updated
     * @param array $record     The record data with which to insert
     */
    public function insert(string $sobject, array $record)
    {
        $uri = $this->baseUri . '/' . $this->apiVersion . '/sobjects/'.$sobject.'/';
        try {
            $result = $this->client->request('POST',
                $uri,
                [
                    'headers' => $this->getHeaders(),
                    'body' => json_encode($record)
                ]
            );
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getMessage() );
        }
        
        return $result;
    }

    /**
     * Function to update a single record of an object
     *
     * @param string $sobject   The object to be updated
     * @param array $record     The record data with which to update
     */
    public function update(string $sobject, array $record)
    {

    }

    public function upsert (string $object, array $record) {

    }

    public function delete (string $sobject, array $record) {

    }

    protected function makeRequest (string $method, string $url, string $body) 
    {
        try
        {
          $respone = $this->client($method
                              ,$url
                              ,[
                                  'headers' => $this->getHeaders()
                                  ,'body' => $body
                              ]);
        } catch( GuzzleException $e ) {
            // TODO HANDLE EXCEPTION
        } 
    }
}