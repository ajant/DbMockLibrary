<?php
namespace DbMockLibrary\Test\AbstractImplementation;

use DbMockLibrary\DependencyHandler;
use DbMockLibrary\MockMethodCalls;

class SetUpTest extends FakeTestCase
{
    /**
     * @dataProvider getData
     *
     * @return void
     */
    public function test_function(array $data)
    {
        // prepare
        $this->setPropertyByReflection($this->fake, 'data', $data['data']);
        $this->setPropertyByReflection($this->fake, 'dependencies', $data['dependencies']);

        // invoke logic
        $this->fake->setUp($data['records']);

        // test
        foreach ($data['arguments'] as $key => $arguments) {
            $this->assertEquals(1, MockMethodCalls::getInstance()->wasCalledCount(
                'DbMockLibrary\Test\AbstractImplementation\FakeImplementation',
                'insert',
                $arguments
            ));
        }
    }

    public function getData()
    {
        return [
            // #0 insert everything, no dependencies
            [
                [
                    'records' => [],
                    'data' => ['collection' => ['id' => []]],
                    'dependencies' => [],
                    'arguments' => [['collection', 'id']],
                ],
            ],
            // #1 insert entire collections, no dependencies, to be used
            [
                [
                    'records' => ['collection1'],
                    'data' => [
                        'collection1' => [
                            'id11' => ['column1' => 1],
                            'id12' => ['column1' => 2],
                        ],
                        'collection2' => ['id2' => ['column2' => 1]],
                        'collection3' => ['id3' => ['column3' => 1]]
                    ],
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['collection3' => 'column3'],
                            DependencyHandler::ON => ['collection2' => 'column2']
                        ]
                    ],
                    'arguments' => [
                        ['collection1', 'id11'],
                        ['collection1', 'id12']
                    ],
                ]
            ],
            // #2 insert rows, dependencies to be used
            [
                [
                    'records' => ['collection3' => ['id3']],
                    'data' => [
                        'collection1' => [
                            'id11' => ['column1' => 1],
                            'id12' => ['column1' => 2],
                        ],
                        'collection2' => ['id2' => ['column2' => 1]],
                        'collection3' => ['id3' => ['column3' => 1]]
                    ],
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['collection3' => 'column3'],
                            DependencyHandler::ON => ['collection2' => 'column2']
                        ]
                    ],
                    'arguments' => [
                        ['collection3', 'id3'],
                        ['collection2', 'id2']
                    ],
                ]
            ],
        ];
    }
}