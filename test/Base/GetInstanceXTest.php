<?php
namespace DbMockLibrary\Test\Base;

use DbMockLibrary\Base;
use DbMockLibrary\Test\TestCase;

class GetInstanceXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\UnexpectedValueException', 'Not initialized');

        // invoke logic & test
        Base::getInstance();
    }
}