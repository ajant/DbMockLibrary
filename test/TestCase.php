<?php

namespace Test;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        $reflections =  [
            new \ReflectionClass('\DbMockLibrary\Base'),
            new \ReflectionClass('\DbMockLibrary\MockMethodCalls'),
            new \ReflectionClass('\DbMockLibrary\DataContainer'),
            new \ReflectionClass('\DbMockLibrary\DependencyHandler'),
            new \ReflectionClass('\DbMockLibrary\DbImplementations\Mongo'),
            new \ReflectionClass('\DbMockLibrary\DbImplementations\MySQL')
        ];

        /* @var $reflection \ReflectionClass */
        foreach ($reflections as $reflection) {
            $staticProperties = $reflection->getStaticProperties();
            if (!is_null($staticProperties['instance'])) {
                // destroy has to be used because of the bug/feature with getStaticProperty method
                $getInstanceMethod = $reflection->getMethod('getInstance');
                $destroy = 'destroy';
                $getInstanceMethod->invoke($reflection)->$destroy();
            }
        }
    }
} 