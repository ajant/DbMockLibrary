<?php
namespace DbMockLibrary\Test\MockMethodCalls;

use DbMockLibrary\MockMethodCalls;
use DbMockLibrary\Test\TestCase;

class WasCalledTest extends TestCase
{
    /**
     * @dataProvider getData
     *
     * @param array $data
     *
     * @return void
     */
    public function test_function(array $data)
    {
        // prepare
        MockMethodCalls::init();
        $traces = [
            [
                [
                    'function'  => 'getMessage',
                    'class'     => 'Exception',
                    'args'      => ['foo']
                ]
            ],
            [
                [
                    'function'  => 'getMessage',
                    'class'     => 'Exception',
                    'args'      => ['foo']
                ]
            ]
        ];
        $reflection    = new \ReflectionClass('\DbMockLibrary\MockMethodCalls');
        $traceProperty = $reflection->getProperty('traces');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue(MockMethodCalls::getInstance(), $traces);

        // invoke logic
        if (isset($data['arguments'])) {
            $result = MockMethodCalls::getInstance()->wasCalledCount($data['class'], $data['method'], $data['arguments']);
        } else {
            $result = MockMethodCalls::getInstance()->wasCalledCount($data['class'], $data['method']);
        }

        // test
        $this->assertEquals($data['expected'], $result);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 was called at least once
            [
                [
                    'class'     => 'Exception',
                    'method'    => 'getMessage',
                    'arguments' => ['bar'],
                    'expected'  => 0
                ]
            ],
            // #1 was not called required number of times
            [
                [
                    'class'     => 'Exception',
                    'method'    => 'getMessage',
                    'arguments' => ['foo'],
                    'expected'  => 2
                ]
            ],
            // #1 was called required number of times
            [
                [
                    'class'     => 'Exception',
                    'method'    => 'getMessage',
                    'expected'  => 2
                ]
            ]
        ];
    }
}