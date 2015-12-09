<?php

namespace DbMockLibrary\Test;

use InvalidArgumentException;
use Mockery;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class TestCase extends PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        parent::tearDown();

        Mockery::close();

        $reflections = [
            new ReflectionClass('\DbMockLibrary\Base'),
            new ReflectionClass('\DbMockLibrary\MockMethodCalls'),
            new ReflectionClass('\DbMockLibrary\DataContainer'),
            new ReflectionClass('\DbMockLibrary\DependencyHandler'),
            new ReflectionClass('\DbMockLibrary\DbImplementations\Mongo'),
            new ReflectionClass('\DbMockLibrary\DbImplementations\MySQL'),
            new ReflectionClass('\DbMockLibrary\DbImplementations\Postgres'),
        ];

        /* @var $reflection ReflectionClass */
        foreach ($reflections as $reflection) {
            $staticProperties = $reflection->getStaticProperties();
            if (!is_null($staticProperties['instance'])) {
                // destroy has to be used because of the bug/feature with getStaticProperty method
                $getInstanceMethod = $reflection->getMethod('getInstance');
                $destroy = 'destroy';
                $getInstanceMethod->invoke($reflection)->$destroy();
            }
        }
    }

    /**
     * @param mixed $class
     * @param string $property
     * @param mixed $value
     *
     * @return void
     * @throws InvalidArgumentException
     */
    public function setPropertyByReflection($class, $property, $value)
    {
        if (
            !is_object($class)
            && !(
                !is_string($class)
                || !class_exists($class)
            )
        ) {
            throw new InvalidArgumentException('Object argument is not an object: ' . var_export($class, true));
        }
        if (!is_string($property)) {
            throw new InvalidArgumentException('Property argument is not a string: ' . var_export($property, true));
        }

        $reflection = new ReflectionClass($class);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);
        $propertyReflection->setValue($class, $value);
    }

    /**
     * @param mixed $class
     * @param string $property
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function getPropertyByReflection($class, $property)
    {
        if (
            !is_object($class)
            && !(
                !is_string($class)
                || !class_exists($class)
            )
        ) {
            throw new InvalidArgumentException('Object argument is not an object: ' . var_export($class, true));
        }
        if (!is_string($property)) {
            throw new InvalidArgumentException('Property argument is not a string: ' . var_export($property, true));
        }

        $reflection = new ReflectionClass($class);
        $propertyReflection = $reflection->getProperty($property);
        $propertyReflection->setAccessible(true);

        return $propertyReflection->getValue($class);
    }

    /**
     * @param mixed $class
     * @param string $method
     * @param array $arguments
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    public function invokeMethodByReflection($class, $method, array $arguments)
    {
        if (
            !is_object($class)
            && !(
                !is_string($class)
                || !class_exists($class)
            )
        ) {
            throw new InvalidArgumentException('Object argument is not an object: ' . var_export($class, true));
        }
        if (!is_string($method)) {
            throw new InvalidArgumentException('Method argument is not a string: ' . var_export($method, true));
        }

        $reflection = new ReflectionClass($class);
        $methodReflection = $reflection->getMethod($method);
        $methodReflection->setAccessible(true);

        return $methodReflection->invokeArgs($class, $arguments);
    }
} 