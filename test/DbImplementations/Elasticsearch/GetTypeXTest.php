<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use DbMockLibrary\DbImplementations\Elasticsearch;
use InvalidArgumentException;
use ReflectionClass;

class GetTypeXTest extends ElasticsearchTestCase
{
    public function testMissingIndex()
    {
        // prepare
        $undefinedIndex = 'undefinedIndex';
        $this->setExpectedException(InvalidArgumentException::class, "Undefined type for index $undefinedIndex");
        $indexTypes = [
            $this->testIndex => self::REGULAR_TYPE,
        ];
        $reflection = new ReflectionClass(Elasticsearch::class);
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'indexTypes', $indexTypes);

        // invoke logic
        $this->invokeMethodByReflection($instance, 'getType', [$undefinedIndex]);
    }
}