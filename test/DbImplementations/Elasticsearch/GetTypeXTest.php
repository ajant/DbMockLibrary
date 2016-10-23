<?php

namespace DbMockLibrary\Test\DbImplementations\Elasticsearch;

use ReflectionClass;

class GetTypeXTest extends ElasticsearchTestCase
{
    public function testMissingIndex()
    {
        // prepare
        $undefinedIndex = 'undefinedIndex';
        $this->setExpectedException('\InvalidArgumentException', "Undefined type for index $undefinedIndex");
        $indexTypes = [
            $this->testIndex => self::REGULAR_TYPE,
        ];
        $reflection = new ReflectionClass('\DbMockLibrary\DbImplementations\Elasticsearch');
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'indexTypes', $indexTypes);

        // invoke logic
        $this->invokeMethodByReflection($instance, 'getType', [$undefinedIndex]);
    }
}