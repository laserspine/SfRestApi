<?php

namespace Tests;

use Salesforce\Api;

class ApiTest extends \PHPUnit\Framework\TestCase
{
  protected $api;

  public function setup()
  {
    $this->api = new Api(Config::PARAMS);
  }

  public function tearDown()
  {
    $this->api = null;
  }

  public function test_query() {
    $query = 'SELECT Id FROM Lead WHERE LastName = \'Alessandro\'';
    $result = $this->api->query($query);

    $this->assertTrue($result->records[0]->Id !== null);
  }

  public function test_insert() {
    $lead = array(
      'FirstName' => 'Test'
      ,'LastName' => 'McTesterson'
      ,'Company' => 'Testing McTesting'
      ,'Phone' => '7276544321'
      ,'email' => 'test@nate.com'
    );

    $response = $this->api->insert('Lead', $lead);

    $this->assertTrue(!is_null($response->id));
  }

  public function test_update() {
    $query = "SELECT Id FROM Lead WHERE email = 'test@nate.com' LIMIT 1";
    $result = $this->api->query($query);
    if(count($result->records)) {
      $lead = array(
        'Id' => $result->records[0]->Id
        ,'LastName' => 'McTesting'
      );
      
      $result = $this->api->update('Lead', $lead);
      $this->assertTrue(is_null($result));
    } else {
      $this->assertTrue(false);
    }
  }

  public function test_upsert_not_exist() {

  }

  public function test_upsert_exist() {

  }

  public function test_delete() {

  }
}