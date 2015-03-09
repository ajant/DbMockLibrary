<?php
namespace Test\Base;

use \DbMockLibrary\Base;

class InitXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\DbMockLibrary\Exceptions\AlreadyInitializedException', 'DbMockLibrary\Base has already been initialized');

        // invoke logic
        Base::init([]);
        Base::init([]);
    }
}