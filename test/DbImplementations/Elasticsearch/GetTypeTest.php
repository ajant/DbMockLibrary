<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use ReflectionClass;

class GetTypeTest extends ElasticsearchTestCase
{
    public function testSuccessful()
    {
        // prepare
        $indexTypes = [
            $this->testIndex => self::REGULAR_TYPE,
        ];
        $expectedType = self::REGULAR_TYPE;
        $reflection = new ReflectionClass(Elasticsearch::class);
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'indexTypes', $indexTypes);

        // invoke logic
        $type = $this->invokeMethodByReflection($instance, 'getType', [$this->testIndex]);

        // test
        $this->assertEquals($expectedType, $type);
    }
}