<?php
namespace DbMockLibrary\Test\DbImplementations\Postgres;

use DbMockLibrary\DbImplementations\Postgres;
use DbMockLibrary\Test\TestCase;
use ReflectionClass;

class DeleteTest extends TestCase
{
    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    public function setUp()
    {
        if (is_null($this->pdo)) {
            $this->pdo = new \PDO('pgsql:host=localhost;dbname=homestead', 'postgres', null);
        }

        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS DbMockLibraryTest');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE DATABASE DbMockLibraryTest');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE TABLE IF NOT EXISTS DbMockLibraryTest.testTable (id INT, foo INT, PRIMARY KEY (id, foo))');
        $stmt->execute();

        $stmt = $this->pdo->prepare('INSERT INTO DbMockLibraryTest.testTable (id, foo) VALUES (0, 0)');
        $stmt->execute();

        Postgres::initPostgres(['testTable' => [1 => ['foo' => 0, 'id' => 0]]], 'localhost', 'DbMockLibraryTest', 'postgres', '', []);
    }

    public function tearDown()
    {
        $stmt = $this->pdo->prepare('DELETE FROM DbMockLibraryTest.testTable WHERE id = 0');
        $stmt->execute();

        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS DbMockLibraryTest');
        $stmt->execute();

        if (Postgres::getInstance()) {
            Postgres::getInstance()->destroy();
        }
    }

    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $stmt = $this->pdo->prepare('SELECT * FROM DbMockLibraryTest.testTable WHERE id = 0');
        $stmt->execute();
        $result = $stmt->fetchAll();
        $reflection = new ReflectionClass(Postgres::getInstance());
        $deleteMethod = $reflection->getMethod('delete');
        $deleteMethod->setAccessible(true);

        // test
        $this->assertCount(1, $result);

        // invoke logic
        $deleteMethod->invoke(Postgres::getInstance(), 'testTable', 1);

        // prepare
        $stmt->execute();
        $result = $stmt->fetchAll();

        // test
        $this->assertCount(0, $result);
    }
}