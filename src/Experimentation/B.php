<?php

namespace DbMockLibrary\Experimentation;

class B
{
    function b()
    {
        $mock = new Mock();
        $mock->mock(1, [2], new \stdClass(), true, 'fooBar');
    }
} 