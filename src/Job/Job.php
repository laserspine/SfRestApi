<?php

namespace Salesforce\Job;

use Salesforce\Interfaces\JobInterface;

class Job implements JobInterface
{
  /**
   * Retrieve the Salesforce Job Id
   *
   * @param string $sobject   The Object in Salesforce on which to create the job
   * @param string $type      The type of job you are performing {update|insert}
   * @return bool|mixed       Return string $this->jobId or false
   */
  protected function get(string $sobject, string $type)
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
  protected function create(string $obj, string $jobType)
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
      throw new \Exception($e->getResponse()->getBody()->getContents() );
    }

    $job = json_decode($result->getBody()->getContents());
    $this->jobId = $job->id;

    return true;
  }

  protected function abort(string $obj, string $jobType)
  {
  }
  
  /**
   * Close Salesforce Job created for batch processing
   * 
   * @return boolean
   */
  protected function close()
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

  protected function addBatch()
  {}

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
}

