<?php
namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use Elasticsearch\Common\Exceptions\ServerErrorResponseException;
use ReflectionClass;

class InsertTest extends ElasticsearchTestCase
{
    /**
     * @var array
     */
    private $indexTypes;

    /**
     * @dataProvider getData
     */
    public function testInsertion($indexType, array $mapping, array $data)
    {
        // prepare
        $this->indexTypes = [
            $this->testIndex => $indexType,
        ];

        Elasticsearch::initElasticsearch($this->hosts, [
            $this->testIndex => $data,
        ], [], $this->indexTypes);

        if ($mapping !== []) {
            $this->client->indices()->putMapping($mapping);
        }

        // sometimes ES service isn't ready, because of constant setUps and tearDowns,
        // this will query ES until it is ready, or $expireWithin seconds expires
        $expireWithin = 10;
        $currentTime = time();
        do {
            $beforeCount = null;
            try {
                $beforeCount = $this->client->count([
                    'index' => $this->testIndex,
                    'type' => $indexType,
                    'body' => ['query' => ['match_all' => []]],
                ]);
            } catch (ServerErrorResponseException $e) {
                if ($e->getMessage() !== '{"count":0,"_shards":{"total":5,"successful":0,"failed":0}}') {
                    $this->assertTrue(false, 'ServerError: ' . $e->getMessage());
                }
            }
        } while ($beforeCount === null && time() - $currentTime < $expireWithin);
        $this->assertNotNull($beforeCount);

        $reflection = new ReflectionClass(Elasticsearch::getInstance());
        $insertMethod = $reflection->getMethod('insert');
        $insertMethod->setAccessible(true);

        // test
        $this->assertArrayHasKey('count', $beforeCount);
        $this->assertEquals(0, $beforeCount['count']);

        // invoke logic
        $insertMethod->invoke(Elasticsearch::getInstance(), $this->testIndex, 1);

        // prepare
        $afterCount = $this->client->count([
            'index' => $this->testIndex,
            'type' => $indexType,
            'body' => ['query' => ['match_all' => []]],
        ]);


        // test
        $this->assertArrayHasKey('count', $afterCount);
        $this->assertEquals(1, $afterCount['count']);
    }

    public function getData()
    {
        return [
            // #0 regular index type
            [
                'type' => self::REGULAR_TYPE,
                'mapping' => [],
                'data' => [
                    1 => [
                        'field1' => 'value1',
                    ],
                ],
            ],
            // #1 percolator index type without routing
            [
                'type' => self::PERCOLATOR_TYPE,
                'mapping' => [
                    'type' => self::PERCOLATOR_TYPE,
                    'body' => [
                        "properties" => [
                            "field1" => [
                                "type" => "string",
                                "index" => "not_analyzed",
                            ],
                        ]
                    ],
                ],
                'data' => [
                    1 => [
                        'body' => [
                            'query' => [
                                'match' => [
                                    'field1' => 'value1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // #2 percolator index type with routing
            [
                'type' => self::PERCOLATOR_TYPE,
                'mapping' => [
                    'type' => self::PERCOLATOR_TYPE,
                    'body' => [
                        "properties" => [
                            "field1" => [
                                "type" => "string",
                                "index" => "not_analyzed",
                            ],
                        ]
                    ],
                ],
                'data' => [
                    1 => [
                        'routing' => 2,
                        'body' => [
                            'query' => [
                                'match' => [
                                    'field1' => 'value1',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}