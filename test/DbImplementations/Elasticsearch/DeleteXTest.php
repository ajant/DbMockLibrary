<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use Mockery;
use ReflectionClass;

class DeleteXTest extends ElasticsearchTestCase
{
    public function testDeleteFailed()
    {
        // prepare
        $this->setExpectedException('\DbMockLibrary\Exceptions\DbOperationFailedException', 'Delete failed');
        $reflection = new ReflectionClass('\DbMockLibrary\DbImplementations\Elasticsearch');
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'indexTypes', [$this->testIndex => self::REGULAR_TYPE]);
        $mockClient = Mockery::mock('\Elasticsearch\Client');
        $mockClient->shouldReceive('delete')->times(1)->with([
            'index' => $this->testIndex,
            'type' => self::REGULAR_TYPE,
            'id' => 0,
        ])->andThrow('\Exception', 'failed');
        $this->setPropertyByReflection($instance, 'client', $mockClient);

        // invoke logic &  test
        $this->invokeMethodByReflection($instance, 'delete', [$this->testIndex, 0]);
    }
}