<?php

namespace Test;

use DbMockLibrary\MockLibrary;

class TestCase extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $staticProperties = $reflection->getStaticProperties();

        if (!is_null($staticProperties['instance'])) {
            // has to be used because f the bug/feature with getStaticProperty method
            MockLibrary::getInstance()->destroy();
        }
    }
} 