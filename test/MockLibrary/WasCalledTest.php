<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class WasCalledTest extends \Test\TestCase
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
        MockLibrary::init([]);
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
        $reflection    = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $traceProperty = $reflection->getProperty('traces');
        $traceProperty->setAccessible(true);
        $traceProperty->setValue(MockLibrary::getInstance(), $traces);

        // invoke logic
        if (isset($data['arguments'])) {
            $result = MockLibrary::getInstance()->wasCalled($data['class'], $data['method'], $data['arguments']);
        } else {
            $result = MockLibrary::getInstance()->wasCalled($data['class'], $data['method']);
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