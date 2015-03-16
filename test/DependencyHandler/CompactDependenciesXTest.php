<?php
namespace Test\DependencyHandler;

use \DbMockLibrary\DependencyHandler;

class CompactDependenciesXTest extends \Test\TestCase
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
        $dependenciesMethod = $reflection->getMethod('compactDependencies');
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
            // #0 dimension 1, should be 2
            [
                [
                    'data' => []
                ]
            ],
            // #1 dimension 3, should be 2
            [
                [
                    'data' => [[[]]]
                ]
            ]
        ];
    }
}