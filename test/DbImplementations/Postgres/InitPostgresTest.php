<?php
namespace DbMockLibrary\Test\DbImplementations\Postgres;

use DbMockLibrary\DbImplementations\Postgres;
use DbMockLibrary\Test\TestCase;

class InitPostgresTest extends TestCase
{
    /**
     * @var \PDO $pdo
     */
    protected $pdo;

    public function setUp()
    {
        if (is_null($this->pdo)) {
            $this->pdo = new \PDO('Postgres:host=localhost;', 'root', '');
        }

        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS DbMockLibraryTest');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE DATABASE DbMockLibraryTest');
        $stmt->execute();

        $stmt = $this->pdo->prepare('CREATE TABLE IF NOT EXISTS DbMockLibraryTest.testTable (id INT, foo INT, PRIMARY KEY (id))');
        $stmt->execute();
    }

    public function tearDown()
    {
        $stmt = $this->pdo->prepare('DROP DATABASE IF EXISTS DbMockLibraryTest');
        $stmt->execute();

        Postgres::getInstance()->destroy();
    }

    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $dataArray = ['testTable' => [1 => ['foo' => 1, 'id' => 1]]];

        // invoke logic
        Postgres::initPostgres($dataArray, 'localhost', 'DbMockLibraryTest', 'root', '', []);

        // prepare
        $reflection = new \ReflectionClass('\DbMockLibrary\DbImplementations\Postgres');
        $staticProperties = $reflection->getStaticProperties();

        // test
        $this->assertInstanceOf('\DbMockLibrary\DbImplementations\Postgres', $staticProperties['instance']);
        $this->assertEquals($dataArray, $staticProperties['initialData']);

        // prepare
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);
        $primaryKeysProperty = $reflection->getProperty('primaryKeys');
        $primaryKeysProperty->setAccessible(true);

        // test
        $this->assertEquals($dataArray, $dataProperty->getValue($staticProperties['instance']));
        $this->assertEquals(['testTable' => ['id']], $primaryKeysProperty->getValue($staticProperties['instance']));
    }
}