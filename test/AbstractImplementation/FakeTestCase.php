<?php

namespace DbMockLibrary\Test\AbstractImplementation;

use DbMockLibrary\MockMethodCalls;
use Mockery;
use DbMockLibrary\Test\TestCase;
use ReflectionClass;

class FakeTestCase extends TestCase
{
    /**
     * @var FakeImplementation $fake
     */
    protected $fake;

    /**
     * @var MockMethodCalls $mmc
     */
    protected $mmc;

    public function setUp()
    {
        parent::setUp();

        $reflection = new ReflectionClass('DbMockLibrary\Test\AbstractImplementation\FakeImplementation');
        $this->fake = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($this->fake, 'instance', $this->fake);

        $reflection = new ReflectionClass('DbMockLibrary\MockMethodCalls');
        $this->mmc = $reflection->newInstanceWithoutConstructor();
        $this->setPropertyByReflection($this->mmc, 'instance', $this->mmc);
    }

    public function tearDown()
    {
        parent::tearDown();

        $this->mmc->reset();
    }
}