<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class InitXTest extends \Test\TestCase
{
    /**
     * @return void
     */
    public function test_function()
    {
        // prepare
        $this->setExpectedException('\DbMockLibrary\Exceptions\AlreadyInitializedException', 'MockLibrary has already been initialized');

        // invoke logic
        MockLibrary::init([]);
        MockLibrary::init([]);
    }
}