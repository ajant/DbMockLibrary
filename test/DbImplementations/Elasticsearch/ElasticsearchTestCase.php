<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use DbMockLibrary\Test\TestCase;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;

class ElasticsearchTestCase extends TestCase
{
    const REGULAR_TYPE = 'testingType';
    const PERCOLATOR_TYPE = '.percolator';

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var string $hosts
     */
    protected $hosts = ['http://localhost:9200'];

    /**
     * @var string $testIndex
     */
    protected $testIndex = 'db_mock_library_test_index';

    /**
     * @var string $testType
     */
    protected $testType = 'testType';

    public function setUp()
    {
        parent::setUp();

        if (is_null($this->client)) {
            $this->client = ClientBuilder::create()->setHosts($this->hosts)->build();
        }

        $this->client->indices()->create(['index' => $this->testIndex]);
    }

    public function tearDown()
    {
        $this->client->indices()->delete(['index' => $this->testIndex]);

        Elasticsearch::getInstance()->destroy();

        parent::tearDown();
    }
}