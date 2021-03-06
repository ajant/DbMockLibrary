<?php

namespace DbMockLibrary;

use DbMockLibrary\Exceptions\InvalidDependencyException;
use DbMockLibrary\Exceptions\AlreadyInitializedException;
use UnexpectedValueException;
use InvalidArgumentException;

class DataContainer extends Base
{
    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var array $initialData
     */
    protected static $initialData;

    /**
     * @param array $initialData
     *
     * @throws AlreadyInitializedException
     * @throws InvalidDependencyException
     */
    public static function initDataContainer(array $initialData)
    {
        static::init();
        static::$instance->data = static::$initialData = $initialData;
    }

    /**
     * Resets DbMockLibrary class instance to cancel changes made to the $data array by tests
     *
     * @return void
     */
    public function resetData()
    {
        // clear all changes to $data array
        $this->data = static::$initialData;
    }

    /**
     * @param array $collections
     *
     * @return void
     * @throws UnexpectedValueException
     */
    protected function validateCollections(array $collections)
    {
        foreach ($collections as $collection) {
            if (!isset($this->data[$collection]) || !array_key_exists($collection, $this->data)) {
                throw new UnexpectedValueException('Collection ' . var_export($collection, true) . ' does not exist');
            }
        }
    }

    /**
     * @param string $collection
     * @param array  $ids
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws UnexpectedValueException
     */
    protected function validateIds($collection, array $ids)
    {
        $this->validateCollections([$collection]);

        foreach ($ids as $id) {
            if (!is_string($id) && !is_int($id)) {
                throw new InvalidArgumentException('Invalid id ' . var_export($id, true));
            }
            if (!isset($this->data[$collection][$id]) && !array_key_exists($id, $this->data[$collection])) {
                throw new UnexpectedValueException('Element with id ' . var_export($id, true) . ' does not exist');
            }
        }
    }
}