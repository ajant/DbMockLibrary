<?php
namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use Elasticsearch\Client;
use Exception;
use Mockery;
use ReflectionClass;

class InsertXTest extends ElasticsearchTestCase
{
    const REGULAR_TYPE = 'testingType';
    const PERCOLATOR_TYPE = '.percolator';

    /**
     * @param $indexType
     *
     * @dataProvider getData
     */
    public function testInsertFailed($indexType)
    {
        // prepare
        $this->setExpectedException(DbOperationFailedException::class, 'Insert failed');
        $reflection = new ReflectionClass(Elasticsearch::class);
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'data', [$this->testIndex => [[]]]);
        $mockClient = Mockery::mock(Client::class);
        if ($indexType === self::REGULAR_TYPE) {
            $mockClient->shouldReceive('index')->times(1)->with(
                [
                    'index' => $this->testIndex,
                    'type' => $indexType,
                    'id' => 0,
                    'body' => [],
                ]
            )->andThrow(Exception::class, 'exception');
        }
        $this->setPropertyByReflection($instance, 'client', $mockClient);
        $this->setPropertyByReflection($instance, 'indexTypes', [$this->testIndex => $indexType]);

        // invoke logic &  test
        $this->invokeMethodByReflection($instance, 'insert', [$this->testIndex, 0]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 regular type index
            [
                'indexType' => self::REGULAR_TYPE,
            ],
            // #1 percolator type index
            [
                'indexType' => self::PERCOLATOR_TYPE,
            ],
        ];
    }
}