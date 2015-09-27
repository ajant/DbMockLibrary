<?php
namespace DbMockLibrary\Test\DbImplementations\Mongo;

use DbMockLibrary\DbImplementations\Mongo;
use DbMockLibrary\Test\TestCase;

class InitXTest extends TestCase
{
    public function tearDown()
    {
        if (Mongo::getInstance()) {
            Mongo::getInstance()->destroy();
        }
    }

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
        $this->setExpectedException($data['exception'], $data['errorMessage']);

        // invoke logic
        Mongo::initMongo($data['initialData'], $data['database'], []);
        if (isset($data['initTwice'])) {
            Mongo::initMongo($data['initialData'], $data['database'], []);
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 instance already initialized
            [
                [
                    'exception'    => '\DbMockLibrary\Exceptions\AlreadyInitializedException',
                    'errorMessage' => 'Mongo library already initialized',
                    'database'     => 'DbMockLibraryTest',
                    'initialData'  => [],
                    'initTwice'    => true
                ]
            ],
            // #1 invalid database parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid database name',
                    'database'     => '',
                    'initialData'  => []
                ]
            ],
            // #2 invalid database parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid database name',
                    'database'     => [],
                    'initialData'  => []
                ]
            ],
            // #3 invalid collection names (not a string) in initial data parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid collection names',
                    'database'     => 'DbMockLibraryTest',
                    'initialData'  => [1 => ['foo' => 'value', 'id' => 1]]
                ]
            ]
        ];
    }
}