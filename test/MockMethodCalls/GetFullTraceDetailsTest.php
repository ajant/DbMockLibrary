<?php
namespace Test\MockMethodCalls;

use \DbMockLibrary\MockMethodCalls;

class GetFullTraceDetailsTest extends \Test\TestCase
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
        MockMethodCalls::init([]);
        $traces = [
            [
                [
                    'function'  => 'getMessage',
                    'class'     => 'Exception',
                    'args'      => ['fooBar'],
                    'foo'       => 'bar'
                ]
            ]
        ];
        $reflection    = new \ReflectionClass('\DbMockLibrary\MockMethodCalls');
        $traceProperty = $reflection->getProperty('traces');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue(MockMethodCalls::getInstance(), $traces);
        $getFullTraceDetailsMethod = $reflection->getMethod('getFullTraceDetails');
        $getFullTraceDetailsMethod->setAccessible(true);

        // invoke logic
        $result = $getFullTraceDetailsMethod->invoke(MockMethodCalls::getInstance(), $data['class'], $data['method']);

        // test
        $this->assertEquals($data['expected'], $result);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 method was called
            [
                [
                    'class'     => 'Exception',
                    'method'    => 'getMessage',
                    'expected'  => [
                        [
                            [
                                'function'  => 'getMessage',
                                'class'     => 'Exception',
                                'args'      => ['fooBar']
                            ]
                        ]
                    ]
                ]
            ],
            // #1 method wasn't called
            [
                [
                    'class'     => 'Exception',
                    'method'    => 'getTrace',
                    'expected'  => []
                ]
            ]
        ];
    }
}