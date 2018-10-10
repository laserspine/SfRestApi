<?php

namespace Salesforce\Bulk;

use Salesforce\Job\Job;

/**
 * Utilize Salesforce Bulk API
 */
class Bulk
{
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
}