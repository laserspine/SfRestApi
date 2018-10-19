<?php

namespace Salesforce\Api;

use Salesforce\Interfaces\ApiInterface;

class CompositeApi implements CompositeInterface
{
  /**
   * @var Salesforce\Api\Client
   */
  protected $api;

  /**
   * @var Boolean
   */
  protected $allOrNone ;

  /**
   * RESTApi constructor.
   *
   * @param string $key
   * @param string $secret
   * @param string $user
   * @param string $pass
   * @param string $token
   * @param string $getBaseUrl()
   */
  public function __construct(array $params)
  {
    $this->api = new Api($params);
    $this->unsetAllOrNone();
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
  public function request(array $requests, string $type = 'batch')
  {
    if ($type == 'batch')
      $body = array('batchRequests' => $requests);
    else
      $body = array('compositeRequest' => $requests);

    $response = $this->api->getClient()->request('POST'
      ,'/composite/' . $type
      ,array('batchRequests' => $requests)
    );

    return $response;
  }

  /**
   * Build Query Subrequest
   *
   * @param string $query
   * @return \stdClass|null
   */
  public function query(string $query, string $count): ?\stdClass
  {
    $req = new \stdClass();
    $req->method = 'GET';
    $req->url = str_replace(' ', '+', $this->api->getClient()->getApiVersion() . '/query?q=' . $query);

    return $req;
  }

  /**
   * Build Insert Subrequest
   *
   * @param string $sobject
   * @param array $record
   * @return \stdClass|null
   */
  public function insert(string $sobject, array $record, string $count): ?\stdClass
  {
    $req = new \stdClass();
    $req->method = 'POST';
    $req->url = $this->api->getClient()->getApiVersion() . '/sobjects/'.$sobject.'/';
    $req->body = json_encode($record);
    $req->referenceId = $sobject;

    return $req;
  }
  
  /**
   * Build Update Subrequest
   *
   * @param string $sobject
   * @param array $record
   * @return \stdClass|null
   */
  public function update(string $sobject, array $record, string $count): ?\stdClass
  {
    $req = new \stdClass();
    $req->method = 'PATCH';
    $req->url = $this->api->getClient()->getApiVersion() . '/sobjects/'.$sobject.'/' . $record['Id'];
    unset($record['Id']);
    $req->body = json_encode($record);
    $req->referenceId = $sobject;

    return $req;
  }

  /**
   * Build Delte Subrequest
   *
   * @param string $sobject
   * @param string $id
   * @return string|null
   */
  public function delete(string $sobject, string $id, string $count): ?string
  {
    $req = new \stdClass();
    $req->method = 'DELETE';
    $req->url = $this->api->getClient()->getApiVersion() . '/sobjects/'.$sobject.'/' . $id;
    $req->referenceId = $sobject;

    return json_encode($req);
  }

  public function setAllOrNone()
  {
    $this->allOrNone = true;
  }

  public function unsetAllOrNone()
  {
    $this->allOrNone = false;
  }
} 