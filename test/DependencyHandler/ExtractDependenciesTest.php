<?php
namespace Test\DependencyHandler;

use \DbMockLibrary\DependencyHandler;

class ExtractDependenciesTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $wanted       = ['a' => ['a1']];
        $data         = [
            'a' => [
                'a1' => [
                    'aa1' => 1,
                    'aa2' => 2
                ],
                'a2' => [
                    'aa1' => 3,
                    'aa2' => 4
                ]
            ],
            'b' => [
                'b1' => [
                    'bb1' => 1,
                    'bb2' => 2
                ],
                'b2' => [
                    'bb1' => 3,
                    'bb2' => 4
                ]
            ],
            'c' => [
                'c1' => [
                    'cc1' => 1,
                    'cc2' => 2
                ],
                'c2' => [
                    'cc1' => 3,
                    'cc2' => 4
                ]
            ],
            'd' => [
                'd1' => [
                    'dd1' => 1,
                    'dd2' => 2
                ],
                'd2' => [
                    'dd1' => 3,
                    'dd2' => 2
                ]
            ]
        ];
        $dependencies = [
            [
                DependencyHandler::DEPENDENT => ['b' => 'bb1'],
                DependencyHandler::ON        => ['d' => 'dd1']
            ],
            [
                DependencyHandler::DEPENDENT => ['a' => 'aa1'],
                DependencyHandler::ON        => ['c' => 'cc1']
            ],
            [
                DependencyHandler::DEPENDENT => ['c' => 'cc2'],
                DependencyHandler::ON        => ['d' => 'dd2']
            ],
            [
                DependencyHandler::DEPENDENT => ['a' => 'aa1'],
                DependencyHandler::ON        => ['b' => 'bb1']
            ],
        ];
        $expected     = [
            [
                'a' => [
                    'a1'
                ]
            ],
            [
                'c' =>
                    [
                        'c1'
                    ]
            ],
            [
                'd' =>
                    [
                        'd1',
                        'd2'
                    ]
            ],
            [
                'b' =>
                    ['b1']
            ],
            [
                'd' =>
                    ['d1']
            ]
        ];
        DependencyHandler::initDependencyHandler($data, $dependencies);
        $reflection         = new \ReflectionClass('\DbMockLibrary\DependencyHandler');
        $dependenciesMethod = $reflection->getMethod('extractDependencies');
        $dependenciesMethod->setAccessible(true);

        // test
        $this->assertEquals($expected, $dependenciesMethod->invoke(DependencyHandler::getInstance(), $wanted));
    }
}