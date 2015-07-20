<?php

namespace DbMockLibrary;

use DbMockLibrary\Exceptions\InvalidDependencyException;
use DbMockLibrary\Exceptions\AlreadyInitializedException;
use UnexpectedValueException;

class Base
{
    /**
     * @var static $instance
     */
    protected static $instance;

    protected function __construct()
    {
    }

    /**
     * @return void
     * @throws AlreadyInitializedException
     * @throws InvalidDependencyException
     */
    public static function init()
    {
        if (!static::$instance) {
            static::$instance = new static();
        } else {
            throw new AlreadyInitializedException(get_class(static::$instance) . ' has already been initialized');
        }
    }

    /**
     * @return void
     */
    public static function destroy()
    {
        static::$instance = null;
    }

    /**
     * @throws UnexpectedValueException
     * @return static
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            throw new UnexpectedValueException('Not initialized');
        }

        return static::$instance;
    }
}