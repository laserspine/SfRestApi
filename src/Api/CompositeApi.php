<?php

namespace Salesforce\Api;

class CompositeApi
{
  /**
   * @var Salesforce\Api\Client
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
    $this->client = new Api($params);
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