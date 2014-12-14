<?php

namespace DbMockLibrary\Experimentation;

use DbMockLibrary\MockLibrary;

class Mock
{
    function mock()
    {
        MockLibrary::init([]);
        MockLibrary::getInstance()->recordTrace();
        MockLibrary::getInstance()->recordTrace();
        MockLibrary::getInstance()->recordArguments($this, __FUNCTION__, func_get_args());
    }
} 