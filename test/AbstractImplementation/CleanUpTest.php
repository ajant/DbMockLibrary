<?php
namespace DbMockLibrary\Test\AbstractImplementation;

use DbMockLibrary\MockMethodCalls;

class CleanUpTest extends FakeTestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $insertedIntoDb = [['collection1' => 'id1'], ['collection2' => 'id2']];
        $this->setPropertyByReflection($this->fake, 'insertedIntoDb', $insertedIntoDb);

        // invoke logic
        $this->invokeMethodByReflection($this->fake, 'cleanUp', []);

        // test
        $this->assertEquals(1, MockMethodCalls::getInstance()->wasCalledCount(
            'DbMockLibrary\Test\AbstractImplementation\FakeImplementation',
            'delete',
            ['collection1', 'id1']
        ));
        $this->assertEquals(1, MockMethodCalls::getInstance()->wasCalledCount(
            'DbMockLibrary\Test\AbstractImplementation\FakeImplementation',
            'delete',
            ['collection2', 'id2']
        ));
    }
}