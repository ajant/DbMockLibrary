<?php
namespace DbMockLibrary\Test\DbImplementations\Mongo;

use Mockery;
use DbMockLibrary\Test\TestCase;

class DeleteXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('DbMockLibrary\Exceptions\DbOperationFailedException', 'Delete failed');
        $reflection = new \ReflectionClass('DbMockLibrary\DbImplementations\Mongo');
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'data', ['collection' => ['id' => ['_id' => 1]]]);
        $mockMongoCollection = Mockery::mock('\MongoCollection');
        $mockMongoCollection->shouldReceive('remove')->times(1)->with(['_id' => 1], ['w' => 1])->andReturn(['err' => 'foo']);
        $mockMongoDatabase = Mockery::mock('\MongoDB');
        $mockMongoDatabase->shouldReceive('selectCollection')->times(1)->with('collection')->andReturn($mockMongoCollection);
        $this->setPropertyByReflection($instance, 'database', $mockMongoDatabase);

        // invoke logic &  test
        $this->invokeMethodByReflection($instance, 'delete', ['collection', 'id']);
    }
}