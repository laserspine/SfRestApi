<?php

namespace Salesforce;

use Salesforce\Request\Client;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

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

    /**
     * Perform a SOQL query on Salesforce
     *
     *
     * @param string $query     The Query string you would like executed on Salesforce.
     * @return bool|mixed       Return False on exception otherwise returns array of records
     */
    public function query(string $query)
    {
      $uri = str_replace(' ', '+', sprintf('%s%s', $this->baseUri.'/'.$this->apiVersion.'/query/?q=', $query));

      try {
        $result = $this->client->request('GET', $uri, [ 'headers' => $this->getHeaders()]);
      } catch (GuzzleException $e) {
        throw new \Exception( $e->getResponse()->getBody()->getContents() );
      }

      return json_decode($result->getBody()->getContents());
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
        try
        {
            $result = $this->client->request('GET', $uri, ['headers' => $this->getHeaders() ]);
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        return json_decode($result->getBody()->getContents());
    }

    /**
     * TODO: Build Out?
     * Function to update a single record of an object
     *
     * @param string $sobject   The object to be updated
     * @param array $record     The record data with which to update
     */
    public function update(string $sobject, array $record)
    {

    }

    /**
     * TODO: Build out?
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
     * Helper function to insert a batch of records through the bulk api
     *
     * @param string $sobject   The object to be updated
     * @param array $record     The data with which to update
     */
    public function bulkInsert( string $sobject, array $records )
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
    public function bulkUpdate (string $sobject, array $records)
    {
        return $this->sendNewBatch( $sobject, 'update', json_encode( $records ) );
    }

    /**
     * --------------------------------------------------
     * Composite Request
     * --------------------------------------------------
     * Forwards composite request body onto Salesforce through
     * the makeRequst method. See composite request documentation
     * on Salesforce to properly build a request body.
     * @link https://developer.salesforce.com/docs/atlas.en-us.api_rest.meta/api_rest/requests_composite.htm
     *
     * @param array $records
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    public function compositeRequest (array $requests, string $type = 'batch')
    {
        $uri = $this->baseUri . '/' . $this->apiVersion . '/composite/' . $type;

        try
        {
            $response = $this->client->request('POST',
                $uri,
                [
                    'headers' => $this->getHeaders(),
                    'body' => json_encode( $requests )
                ]
            );
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        return $response;
    }

    /**
     * Send new batch of data to Salesforce Bulk API for processing
     *
     * @param string $sobject   Object on which the bulk job will run
     * @param string $type      Type of process to be run {update|insert|etc.}
     * @param string $records   JSON string of records to process
     *
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \Exception
     */
    protected function sendNewBatch( string $sobject, string $type, string $records )
    {
        $this->getJob( $sobject, $type );

        $uri = '/services/async/' . str_replace('v', '', $this->apiVersion) . '/job/' . $this->jobId . '/batch';

        try
        {
            $result = $this->client->request('POST',
                $uri,
                [
                    'headers' => $this->getHeaders(),
                    'body' => $records
                ]
            );
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        $this->closeJob();
        $this->jobId = '';

        return $result;
    }

    /**
     * Retrieve the Salesforce Job Id
     *
     * @param string $sobject   The Object in Salesforce on which to create the job
     * @param string $type      The type of job you are performing {update|insert}
     * @return bool|mixed       Return string $this->jobId or false
     */
    protected function getJob(string $sobject, string $type)
    {
        if(!$this->jobId)
        {
            return $this->createJob( strtolower($sobject), $type );
        }

        return true;
    }

    /**
     * Create new job instance on Salesforce.
     *
     * @param string $obj       The Object the job will run on
     * @param string $jobType   The process that will occur {insert|update|etc.}
     * @return bool
     */
    protected function createJob(string $obj, string $jobType)
    {
        $uri = '/services/async/' . str_replace('v', '', $this->apiVersion) . '/job';

        $headers = $this->getHeaders();
        $headers['operation'] = $jobType;
        $headers['object'] = $obj;

        $body = array(
            'operation' => $jobType,
            'object' => $obj,
            'contentType' => 'JSON'
        );

        try {
            $result = $this->client->request('POST',
                $uri,
                [
                    'headers' => $headers,
                    'body' => json_encode($body)
                ]
            );
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        $job = json_decode($result->getBody()->getContents());
        $this->jobId = $job->id;

        return true;
    }
    
    /**
     * Close Salesforce Job created for batch processing
     * 
     * @return boolean
     */
    protected function closeJob()
    {
        $uri = $uri = '/services/async/' . str_replace('v', '', $this->apiVersion) . '/job/' . $this->jobId;

        // TODO: CONVERT TO makeRequest();
        try
        {
            $response = $this->client->request('POST',
                $uri,
                [
                    'headers' => $this->getHeaders(),
                    'body' => json_encode(array('state' => 'Closed'))
                ]
            );
        }
        catch (GuzzleException $e)
        {
            throw new \Exception( $e->getResponse()->getBody()->getContents() );
        }

        return true;
    }

    /**
    * @return mixed
    */
    protected function getAccessToken()
    {
        if (null === $this->accessToken) {
            $query = [
                'grant_type'    => 'password',
                'client_id'     => $this->consumerKey,
                'client_secret' => $this->consumerSecret,
                'username'      => $this->username,
                'password'      => $this->password.$this->securityToken,
            ];

            $uri = sprintf('%s?%s', '/services/oauth2/token', http_build_query($query));
            $response = $this->client->request('POST', $uri);

            if (200 == $response->getStatusCode()) {
                $body = json_decode($response->getBody(true), true);
                $this->accessToken = $body['access_token'];
                $this->isAuthorized = true;
            }
        }

        return $this->accessToken;
    }

     /**
     * @return array
     */
    protected function getHeaders()
    {
        $headers = array(
            'content-type' => 'application/json',
            'accept' => 'application/json',
            'authorization' => sprintf('Bearer %s', $this->getAccessToken()),
            'x-prettyprint' => 1,
            'x-sfdc-session' => substr($this->getAccessToken(), strpos($this->getAccessToken(), '!'))
        );
        
        return $headers;
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