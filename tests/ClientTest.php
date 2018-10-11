<?php

namespace Tests;

use Salesforce\Api;

class ClientTest extends \PHPUnit\Framework\TestCase
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

    public function test_connection() {
        $this->api = new Api(Config::PARAMS);
        $result = $this->api->getClient()->testConnection();

        $this->assertTrue(true);
    }

    public function test_get_versions()
    {
        $result = $this->api->getClient()->getVersions();

        $this->assertTrue(true);
    }

    public function test_get_resources()
    {
        $result = $this->api->getClient()->getAvailableResources();

        $this->assertTrue(true);
    }

    public function test_describe()
    {
        $result = $this->api->getClient()->describe('Account');

        $this->assertTrue(true);
    }

}