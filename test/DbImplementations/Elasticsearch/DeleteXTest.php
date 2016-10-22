<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use Elasticsearch\Client;
use Exception;
use Mockery;
use ReflectionClass;

class DeleteXTest extends ElasticsearchTestCase
{
    public function testDeleteFailed()
    {
        // prepare
        $this->setExpectedException(DbOperationFailedException::class, 'Delete failed');
        $reflection = new ReflectionClass(Elasticsearch::class);
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'indexTypes', [$this->testIndex => self::REGULAR_TYPE]);
        $mockClient = Mockery::mock(Client::class);
        $mockClient->shouldReceive('delete')->times(1)->with([
            'index' => $this->testIndex,
            'type' => self::REGULAR_TYPE,
            'id' => 0,
        ])->andThrow(Exception::class, 'failed');
        $this->setPropertyByReflection($instance, 'client', $mockClient);

        // invoke logic &  test
        $this->invokeMethodByReflection($instance, 'delete', [$this->testIndex, 0]);
    }
}