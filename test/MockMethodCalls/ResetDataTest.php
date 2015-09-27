<?php

namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class ResetDataTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $traces = ['foo' => 1];
        $callArguments = ['bar' => 1];
        MockMethodCalls::init();
        $reflection = new \ReflectionClass('\DbMockLibrary\MockMethodCalls');
        $staticProperties = $reflection->getStaticProperties();
        $tracesProperty = $reflection->getProperty('traces');
        $tracesProperty->setAccessible(true);
        $tracesProperty->setValue($staticProperties['instance'], $traces);
        $callArgumentsProperty = $reflection->getProperty('callArguments');
        $callArgumentsProperty->setAccessible(true);
        $callArgumentsProperty->setValue($staticProperties['instance'], $callArguments);

        // invoke logic
        MockMethodCalls::getInstance()->reset();

        // test
        $this->assertEquals([], $tracesProperty->getValue($staticProperties['instance']));
        $this->assertEquals([], $callArgumentsProperty->getValue($staticProperties['instance']));
    }
}