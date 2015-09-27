<?php
namespace DbMockLibrary\Test\Base;

use DbMockLibrary\Base;
use DbMockLibrary\Test\TestCase;

class InitXTest extends TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\DbMockLibrary\Exceptions\AlreadyInitializedException', 'DbMockLibrary\Base has already been initialized');

        // invoke logic
        Base::init();
        Base::init();
    }
}