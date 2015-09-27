<?php
namespace DbMockLibrary\Test\DependencyHandler;

use DbMockLibrary\DependencyHandler;
use DbMockLibrary\Test\TestCase;

class RepackDependenciesXTest extends TestCase
{
    /**
     * @param array $data
     *
     * @dataProvider getData
     *
     * @return void
     */
    public function test_function(array $data)
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Invalid input');
        DependencyHandler::initDependencyHandler([]);
        $reflection         = new \ReflectionClass('\DbMockLibrary\DependencyHandler');
        $dependenciesMethod = $reflection->getMethod('repackDependencies');
        $dependenciesMethod->setAccessible(true);

        // invoke logic & test
        $dependenciesMethod->invoke(DependencyHandler::getInstance(), $data['data']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 dimension 2, should be 3
            [
                [
                    'data' => [[]]
                ]
            ],
            // #1 dimension 4, should be 3
            [
                [
                    'data' => [[[[]]]]
                ]
            ]
        ];
    }
}
