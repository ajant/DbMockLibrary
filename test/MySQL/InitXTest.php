<?php
namespace Test\MySQL;

use \DbMockLibrary\DbImplementations\MySQL;

class InitXTest extends \Test\TestCase
{
    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    public function setUp()
    {
        if (is_null($this->pdo)) {
            $this->pdo = new \PDO('mysql:host=localhost;', 'root', '');
        }

        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS `DbMockLibraryTest`');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE DATABASE `DbMockLibraryTest`');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE TABLE IF NOT EXISTS DbMockLibraryTest.testTable (`id` INT, `foo` INT, PRIMARY KEY (`id`))');
        $stmt->execute();
    }

    public function tearDown()
    {
        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS `DbMockLibraryTest`');
        $stmt->execute();

        if (MySQL::getInstance()) {
            MySQL::getInstance()->destroy();
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
        MySQL::init($data['initialData'], $data['serverName'], $data['database'], $data['username'], $data['password']);
        if (isset($data['initTwice'])) {
            MySQL::init($data['initialData'], $data['serverName'], $data['database'], $data['username'], $data['password']);
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
                    'errorMessage' => 'MySQL library already initialized',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => [],
                    'initTwice'    => true
                ]
            ],
            // #1 invalid server name parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid server name',
                    'serverName'   => '',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => []
                ]
            ],
            // #2 invalid server name parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid server name',
                    'serverName'   => [],
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => []
                ]
            ],
            // #3 invalid database parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid database name',
                    'serverName'   => 'localhost',
                    'database'     => '',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => []
                ]
            ],
            // #4 invalid database parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid database name',
                    'serverName'   => 'localhost',
                    'database'     => [],
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => []
                ]
            ],
            // #5 invalid username parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid username',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => '',
                    'password'     => '',
                    'initialData'  => []

                ]
            ],
            // #6 invalid username parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid username',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => [],
                    'password'     => '',
                    'initialData'  => []

                ]
            ],
            // #7 invalid password parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid password',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => [],
                    'initialData'  => []
                ]
            ],
            // #8 invalid table names (not a string) in initial data parameter
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Invalid table names',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => [1 => ['foo' => 'value', 'id' => 1]]
                ]
            ],
            // #9 missing (part or whole) primary key in initial data
            [
                [
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Missing keys in initial data for table: testTable',
                    'serverName'   => 'localhost',
                    'database'     => 'DbMockLibraryTest',
                    'username'     => 'root',
                    'password'     => '',
                    'initialData'  => ['testTable' => [1 => ['foo' => 1]]]
                ]
            ]
        ];
    }
}