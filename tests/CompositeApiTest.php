<?php

namespace Tests;

use Salesforce\Api\CompositeApi;

class CompositeApiTest extends \PHPUnit\Framework\TestCase
{
  private $api;

  public function setUp()
  {
    $this->api = new CompositeApi(Config::PARAMS);
  }

  public function tearDown()
  {
    $this->api = null;
  }

  public function test_query()
  {
    $query = 'SELECT Id FROM Lead WHERE LastName = \'Alessandro\'';
    $result = $this->api->query($query, '1');

    $this->assertNotNull($result);
  }

  public function test_insert()
  {
    $lead = array(
      'FirstName' => 'Test'
      ,'LastName' => 'McTesterson'
      ,'Company' => 'Testing McTesting'
      ,'Phone' => '7276544321'
      ,'email' => 'test@nate.com'
    );

    $result = $this->api->insert('Lead', $lead);

    $this->assertNotNull($result);
  }

  public function test_upate()
  {
    $lead = array(
      'Id' => '003lkasdng123exs2'
      ,'FirstName' => 'Test'
      ,'LastName' => 'McTesterson'
      ,'Company' => 'Testing McTesting'
      ,'Phone' => '7276544321'
      ,'email' => 'test@nate.com'
    );

    $result = $this->api->Update('Lead', $lead);

    $this->assertNotNull($result);
  }

  public function test_delete()
  {
    $lead = array(
      'Id' => '003lkasdng123exs2'
      ,'FirstName' => 'Test'
      ,'LastName' => 'McTesterson'
      ,'Company' => 'Testing McTesting'
      ,'Phone' => '7276544321'
      ,'email' => 'test@nate.com'
    );

    $result = $this->api->delete('Lead', $lead['Id']);

    $this->assertNotNull($result);
  }

  public function test_request()
  {
    $query = 'SELECT Id FROM Lead WHERE LastName = \'Alessandro\'';
    $result = $this->api->query($query, '1');
    $array = json_decode(json_encode($result), true);

    $result = $this->api->request([$array], '');
    $this->assertFalse($result->hasErrors);
  }
}