<?php
namespace DbMockLibrary\Test\DependencyHandler;

use DbMockLibrary\DependencyHandler;
use DbMockLibrary\Test\TestCase;

class InitDependencyHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // invoke logic
        $dataArray    = [
            'foo1' => [
                'bar1' => [
                    'baz1' => 1
                ]
            ],
            'foo2' => [
                'bar2' => [
                    'baz2' => 2
                ]
            ]
        ];
        $dependencies = [
            [
                DependencyHandler::DEPENDENT => ['foo1' => 'baz1'],
                DependencyHandler::ON        => ['foo2' => 'baz2']
            ]
        ];
        DependencyHandler::initDependencyHandler($dataArray, $dependencies);

        // prepare
        $reflection       = new \ReflectionClass('\DbMockLibrary\DependencyHandler');
        $staticProperties = $reflection->getStaticProperties();
        $dependenciesProperty = $reflection->getProperty('dependencies');
        $dependenciesProperty->setAccessible(true);

        // test
        $this->assertInstanceOf('\DbMockLibrary\DependencyHandler', $staticProperties['instance']);
        $this->assertEquals($dependencies, $dependenciesProperty->getValue(DependencyHandler::getInstance()));
    }
}