<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use DbMockLibrary\Test\TestCase;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class InitElasticsearchXTest extends TestCase
{
    /**
     * @var Client $client
     */
    private $client;

    public function setUp()
    {
        parent::setUp();

        $this->client = ClientBuilder::create()->build();
    }

    public function tearDown()
    {
        Elasticsearch::destroy();

        parent::tearDown();
    }

    public function testAlreadyInitializedException()
    {
        // prepare
        Elasticsearch::init();
        $this->setExpectedException('\DbMockLibrary\Exceptions\AlreadyInitializedException', 'Elasticsearch library already initialized');

        // invoke logic
        Elasticsearch::initElasticsearch($this->client, [], [], []);
    }

    public function testInvalidIndicesException()
    {
        // prepare
        $data = [0 => 'index1'];
        $this->setExpectedException('\UnexpectedValueException', 'Invalid indices names');

        // invoke logic
        Elasticsearch::initElasticsearch($this->client, $data, [], []);
    }
}