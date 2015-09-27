<?php
namespace DbMockLibrary\Test\DbImplementations\Mongo;

use DbMockLibrary\DbImplementations\Mongo;
use DbMockLibrary\Test\TestCase;

class DeleteTest extends TestCase
{
    /**
     * @var \MongoDB $database
     */
    protected $database;

    public function setUp()
    {
        if (is_null($this->database)) {
            $client = new \MongoClient();
            $this->database = $client->selectDB('DbMockLibraryTest');
        }

        $this->database->dropCollection('testCollection');
        $this->database->createCollection('testCollection');
        $testCollection = $this->database->selectCollection('testCollection');
        $testCollection->insert(['foo' => 0, '_id' => 0]);

        Mongo::initMongo(['testCollection' => [1 => ['foo' => 0, '_id' => 0]]], 'DbMockLibraryTest', []);
    }

    public function tearDown()
    {
        $this->database->dropCollection('testCollection');
        $this->database->drop();

        if (Mongo::getInstance()) {
            Mongo::getInstance()->destroy();
        }
    }

    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $testCollection = $this->database->selectCollection('testCollection');
        $result = iterator_to_array($testCollection->find(['_id' => 0]));
        $reflection = new \ReflectionClass(Mongo::getInstance());
        $deleteMethod = $reflection->getMethod('delete');
        $deleteMethod->setAccessible(true);

        // test
        $this->assertCount(1, $result);

        // invoke logic
        $deleteMethod->invoke(Mongo::getInstance(), 'testCollection', 1);

        // prepare
        $result = iterator_to_array($testCollection->find(['_id' => 0]));

        // test
        $this->assertCount(0, $result);
    }
}