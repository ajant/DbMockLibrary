<?php
namespace Test\DependencyHandler;

use \DbMockLibrary\DependencyHandler;

class ValidateIdsXTest extends \Test\TestCase
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
        $this->setExpectedException('DbMockLibrary\Exceptions\InvalidDependencyException', $data['errorMessage']);
        DependencyHandler::initDependencyHandler($data['data']);
        $reflection                = new \ReflectionClass(DependencyHandler::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('validate');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(DependencyHandler::getInstance(), $data['dependencies']);
    }

    /**
     * @return array
     */
    public function getData()
    {
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

        return [
            // #0 dependent collection doesn't exist
            [
                [
                    'errorMessage' => 'Collection "foo11" does not exist',
                    'data'         => $dataArray,
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['foo11' => 'baz1'],
                            DependencyHandler::ON        => ['foo2' => 'baz2']
                        ]
                    ]
                ]
            ],
            // #1 dependent column doesn't exist in one of the rows
            [
                [
                    'errorMessage' => 'Column "baz11" does not exist in one of the rows in a collection "foo1"',
                    'data'         => $dataArray,
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['foo1' => 'baz11'],
                            DependencyHandler::ON        => ['foo2' => 'baz2']
                        ]
                    ]
                ]
            ],
            // #2 depended on collection doesn't exist
            [
                [
                    'errorMessage' => 'Collection "foo22" does not exist',
                    'data'         => $dataArray,
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['foo1' => 'baz1'],
                            DependencyHandler::ON        => ['foo22' => 'baz2']
                        ]
                    ]
                ]
            ],
            // #3 depended on column doesn't exist in one of he rows
            [
                [
                    'errorMessage' => 'Column "baz22" does not exist in one of the rows in a collection "foo2"',
                    'data'         => $dataArray,
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['foo1' => 'baz1'],
                            DependencyHandler::ON        => ['foo2' => 'baz22']
                        ]
                    ]
                ]
            ],
            // #4 invalid id type
            [
                [
                    'errorMessage' => 'Collection: "foo1" depends on itself via "foo2" collection',
                    'data'         => $dataArray,
                    'dependencies' => [
                        [
                            DependencyHandler::DEPENDENT => ['foo1' => 'baz1'],
                            DependencyHandler::ON        => ['foo2' => 'baz2']
                        ],
                        [
                            DependencyHandler::DEPENDENT => ['foo2' => 'baz2'],
                            DependencyHandler::ON        => ['foo1' => 'baz1']
                        ]
                    ]
                ]
            ]
        ];
    }
}