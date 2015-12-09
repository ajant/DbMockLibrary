<?php
namespace DbMockLibrary\Test\DbImplementations\Postgres;

use Mockery;
use DbMockLibrary\Test\TestCase;

class InsertXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('DbMockLibrary\Exceptions\DbOperationFailedException', 'Insert failed');
        $reflection = new \ReflectionClass('DbMockLibrary\DbImplementations\Postgres');
        $instance = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($instance, 'instance', $instance);
        $this->setPropertyByReflection($instance, 'data', ['collection' => [[]]]);
        $mockPDOStatement = Mockery::mock('\PDOStatement');
        $mockPDOStatement->shouldReceive('execute')->times(1)->with([])->andReturn(false);
        $mockConnection = Mockery::mock('\PDO');
        $mockConnection->shouldReceive('prepare')->times(1)->with('INSERT INTO collection () VALUES ();')->andReturn($mockPDOStatement);
        $this->setPropertyByReflection($instance, 'connection', $mockConnection);

        // invoke logic &  test
        $this->invokeMethodByReflection($instance, 'insert', ['collection', 0]);
    }
}