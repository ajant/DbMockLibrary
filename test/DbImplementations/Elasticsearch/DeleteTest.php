<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use ReflectionClass;

class DeleteTest extends ElasticsearchTestCase
{
    /**
     * @dataProvider getData
     */
    public function testDeletion($indexType, array $mappings, array $data)
    {
        // prepare
        if ($mappings !== []) {
            $this->client->indices()->putMapping($mappings);
        }
        $this->client->index($data);
        if ($indexType === self::PERCOLATOR_TYPE) {
            unset($data['index']);
            $data = [$this->testIndex => [1 => $data]];
        }
        Elasticsearch::initElasticsearch($this->hosts, $data, [], [$this->testIndex => $indexType]);
        $beforeCount = $this->client->count([
            'index' => $this->testIndex,
            'type' => $indexType,
            'body' => ['query' => ['match_all' => []]],
        ]);

        $reflection = new ReflectionClass(Elasticsearch::getInstance());
        $deleteMethod = $reflection->getMethod('delete');
        $deleteMethod->setAccessible(true);

        // test
        $this->assertArrayHasKey('count', $beforeCount);
        $this->assertEquals(1, $beforeCount['count']);

        // invoke logic
        $deleteMethod->invoke(Elasticsearch::getInstance(), $this->testIndex, 1);

        // prepare
        $afterCount = $this->client->count([
            'index' => $this->testIndex,
            'type' => $indexType,
            'body' => ['query' => ['match_all' => []]],
        ]);

        // test
        $this->assertArrayHasKey('count', $afterCount);
        $this->assertEquals(0, $afterCount['count']);
    }

    public function getData()
    {
        return [
            // #0 regular index type
            [
                'indexType' => self::REGULAR_TYPE,
                'mappings' => [],
                'data' => [
                    'index' => $this->testIndex,
                    'type' => self::REGULAR_TYPE,
                    'id' => 1,
                    'body' => [
                        'field1' => 'value1',
                        'field2' => 'value2',
                    ],
                ],
            ],
            // #1 percolator index type without routing
            [
                'indexType' => self::PERCOLATOR_TYPE,
                'mappings' => [
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
                    'index' => $this->testIndex,
                    'type' => self::PERCOLATOR_TYPE,
                    'id' => 1,
                    'body' => [
                        'query' => [
                            'match' => [
                                'field1' => 'value1',
                            ],
                        ],
                    ],
                ],
            ],
            // #2 percolator index type with routing
            [
                'indexType' => self::PERCOLATOR_TYPE,
                'mappings' => [
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
                    'index' => $this->testIndex,
                    'type' => self::PERCOLATOR_TYPE,
                    'id' => 1,
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
        ];
    }
}