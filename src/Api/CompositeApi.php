<?php

namespace Salesforce\Api;

use Salesforce\Interfaces\CompositeInterface;

class CompositeApi implements CompositeInterface
{
  /**
   * @var Salesforce\Api\Client
   */
  protected $api;

  /**
   * @var Boolean
   */
  protected $allOrNone;

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
    if ($type === 'batch')
        $body = array('batchRequests' => $requests);
    else
        $body = array('compositeRequest' => $requests);
      

    $response = $this->api->getClient()->request('POST'
      ,'/composite/' . $type
      ,$body
    );

    return $response;
  }

  /**
   * Build Query Subrequest
   *
   * @param string $query
   * @return \stdClass|null
   */
  public function query(string $query, string $count)
  {
    $req = new \stdClass();
    $req->method = 'GET';
    $req->referenceId = 'Query'.$count;
    $req->url = str_replace(' ', '+', $this->api->getClient()->getBaseUri() . '/'. $this->api->getClient()->getApiVersion() . '/query/?q=' . $query);

    return $req;
  }

  /**
   * Build Insert Subrequest
   *
   * @param string $sobject
   * @param array $record
   * @return \stdClass|null
   */
  public function insert(string $sobject, array $record, string $count)
  {
    $req = new \stdClass();
    $req->method = 'POST';
    $req->url = $this->api->getClient()->getBaseUri() . '/'. $this->api->getClient()->getApiVersion() . '/sobjects/'.$sobject.'/';
    $req->body = $record;
    $req->referenceId = $sobject.$count;

    return $req;
  }
  
  /**
   * Build Update Subrequest
   *
   * @param string $sobject
   * @param array $record
   * @return \stdClass|null
   */
  public function update(string $sobject, array $record, string $count)
  {
    $req = new \stdClass();
    $req->method = 'PATCH';
    $req->url = $this->api->getClient()->getBaseUri() . '/'
                  . $this->api->getClient()->getApiVersion() 
                  . '/sobjects/'.$sobject.'/' . $record['Id'];
    unset($record['Id']);
    $req->body = $record;
    $req->referenceId = $sobject.$count;

    return $req;
  }

  /**
   * Build Delte Subrequest
   *
   * @param string $sobject
   * @param string $id
   * @return string|null
   */
  public function delete(string $sobject, string $id, string $count)
  {
    $req = new \stdClass();
    $req->method = 'DELETE';
    $req->url = $this->api->getClient()->getBaseUri() . '/'. $this->api->getClient()->getApiVersion() . '/sobjects/'.$sobject.'/' . $id;
    $req->referenceId = $sobject.$count;

    return json_encode($req);
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
    $req = new \stdClass();
    $request->method = $method;
    $request->url = $uri;
    $request->body = json_encode($data);

    return $req;
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