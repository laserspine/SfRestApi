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

    }

    public function test_update() {

    }

    public function test_upsert_not_exist() {

    }

    public function test_upsert_exist() {

    }

    public function test_delete() {

    }

}