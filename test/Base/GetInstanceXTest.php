<?php
namespace Test\Base;

use \DbMockLibrary\Base;

class GetInstanceXTest extends \Test\TestCase
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