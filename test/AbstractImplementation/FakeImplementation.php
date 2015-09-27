<?php
namespace DbMockLibrary\Test\AbstractImplementation;

use DbMockLibrary\AbstractImplementation;
use DbMockLibrary\MockMethodCalls;

class FakeImplementation extends AbstractImplementation
{
    /**
     * Insert into database
     *
     * @param string $collection
     * @param string $id
     *
     * @return mixed
     */
    protected function insert($collection, $id)
    {
        MockMethodCalls::getInstance()->recordTrace();
    }

    /**
     * Delete from database
     *
     * @param string $collection
     * @param string $id
     *
     * @return void
     */
    protected function delete($collection, $id)
    {
        MockMethodCalls::getInstance()->recordTrace();
    }
}