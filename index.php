<?php
    require 'vendor/autoload.php';
    use DbMockLibrary\DependencyHandler;
    $data = [
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
            DependencyHandler::ON => ['d' => 'dd1']
        ],
        [
            DependencyHandler::DEPENDENT => ['a' => 'aa1'],
            DependencyHandler::ON => ['c' => 'cc1']
        ],
        [
            DependencyHandler::DEPENDENT => ['c' => 'cc2'],
            DependencyHandler::ON => ['d' => 'dd2']
        ],
        [
            DependencyHandler::DEPENDENT => ['a' => 'aa1'],
            DependencyHandler::ON => ['b' => 'bb1']
        ],
    ];

    $wanted = ['a' => ['a1']];

    DependencyHandler::initDependencyHandler($data, $dependencies);
    var_dump(DependencyHandler::getInstance()->prepareDependencies($wanted));
