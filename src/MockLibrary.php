<?php

namespace DbMockLibrary;

use SimpleArrayLibrary\SimpleArrayLibrary;
use DbMockLibrary\Exceptions\AlreadyInitializedException;
use InvalidArgumentException;
use UnexpectedValueException;

class MockLibrary
{
    /**
     * @var MockLibrary $instance
     */
    protected static $instance;

    /**
     * @var array $data
     */
    protected $data;

    /**
     * @var array $initialData
     */
    protected static $initialData;

    /**
     * @var array $config
     */
    protected $config;

    /**
     * @var array $callArguments
     */
    protected $callArguments = [];

    /**
     * @var array $traces
     */
    protected $traces = [];

    protected function __construct() {}

    /**
     * @param array $initialData
     *
     * @throws AlreadyInitializedException
     * @return MockLibrary
     */
    public static function init(array $initialData)
    {
        if (!static::$instance) {
            static::$instance       = new self();
            static::$instance->data = self::$initialData = $initialData;

            // fix/update $data array where needed
            static::$instance->update();
        } else {
            throw new AlreadyInitializedException('MockLibrary has already been initialized');
        }
    }

    /**
     * @return void
     */
    public function destroy()
    {
        static::$instance = null;
    }

    /**
     * Returns MockLibrary instance
     *
     * @throws UnexpectedValueException
     * @return MockLibrary
     */
    public static function getInstance()
    {
        if (empty(static::$instance)) {
            throw new UnexpectedValueException('MockLibrary object not initialized');
        }

        return static::$instance;
    }

    /**
     * Resets DbMockLibrary class instance to cancel changes made to the $data array by tests
     *
     * @return void
     */
    public function resetData()
    {
        // clear all changes to $data array
        $this->data = self::$initialData;

        // initialize $data array where needed
        $this->update();
    }

    /**
     * Initialize/change $data array where needed (maybe based on current time, or whatever)
     *
     * @param string $collection
     *
     * @return void
     */
    protected function update($collection = '')
    {
    }

    /**
     * Return collection data
     *
     * @param string      $collection
     * @param string|bool $id
     *
     * @throws UnexpectedValueException
     * @return mixed|array
     */
    public function getCollectionElements($collection, $id = false)
    {
        if ($id === false) {
            $this->validateCollections([$collection]);
            $return = $this->data[$collection];
        } else {
            $this->validateIds($collection, [$id]);
            $return = $this->data[$collection][$id];
        }

        return $return;
    }

    /**
     * Edits $data array
     *
     * @param array|string $value
     * @param string       $collection
     * @param string       $id
     * @param string       $field
     * @param bool         $strict
     *
     * @return void
     */
    public function saveData($value, $collection = '', $id = '', $field = '', $strict = false)
    {
        if ($strict && !empty($collection)) {
            $this->validateCollections([$collection]);
        }
        if ($strict && !empty($id)) {
            $this->validateIds($collection, [$id]);
        }
        if (!is_bool($strict)) {
            throw new InvalidArgumentException('Strict must be a boolean');
        }

        // if collection is specified we need to perform additional checks
        if (!empty($collection)) {
            // if element is defined
            if (!empty($id)) {
                // if field is defined, insert/replace
                if (!empty($field)) {
                    // field required, but missing
                    if ($strict && (!isset($this->data[$collection][$id][$field]) || !array_key_exists($field, $this->data[$collection][$id]))) {
                        throw new UnexpectedValueException("Invalid field: \"{$field}\"");
                    }
                    // exception not thrown, insert/replace field
                    $this->data[$collection][$id][$field] = $value;
                } else {
                    // insert/replace entire collection element
                    if (!is_array($value)) {
                        // row must be array of fields
                        throw new UnexpectedValueException('Row should be an array of fields');
                    }

                    // insert/replace new row of the collection
                    $this->data[$collection][$id] = $value;
                }
            } else {
                // insert/replace an entire collection
                if (SimpleArrayLibrary::countMaxDepth($value) <= 1) {
                    // collection has to be array of rows which are all arrays of fields
                    throw new UnexpectedValueException('Collection has to be array of rows which are all arrays of fields');
                }
                // /insert/replace collection
                $this->data[$collection] = $value;
            }
        } else {
            // insert/replace an entire collection
            if (SimpleArrayLibrary::countMaxDepth($value) <= 2) {
                // data has to be array of collections which are all arrays of rows which are all arrays of fields
                throw new UnexpectedValueException('Data has to be an array of collections which are all arrays of rows which are all arrays of fields');
            }

            // if collection is not specified, whole data array is overwritten
            $this->data = $value;
        }
    }

    /**
     * Deletes the record from the collection
     *
     * @param string $collection
     * @param array  $ids
     *
     * @return void
     * @throws UnexpectedValueException
     */
    public function deleteRow($collection, array $ids)
    {
        $this->validateIds($collection, $ids);

        foreach ($ids as $id) {
            unset($this->data[$collection][$id]);
        }
    }

    /**
     * Returns keys of a collection or all collections, or empty array for invalid collection
     *
     * @param array $collections
     * @param bool  $byCollection
     *
     * @return array
     */
    public function getAllIds(array $collections = [], $byCollection = false)
    {
        $this->validateCollections($collections);

        $collections = $this->getAllCollectionsIfEmpty($collections);

        $return = [];
        foreach ($collections as $collection) {
            if ($byCollection) {
                $return = array_merge($return, [$collection => array_keys($this->data[$collection])]);
            } else {
                $return = array_merge($return, array_keys($this->data[$collection]));
            }
        }

        return $return;
    }

    /**
     * Clears dummy collection(s)
     *
     * @param array $collections
     *
     * @throws UnexpectedValueException
     * @return void
     */
    public function truncateCollections(array $collections = [])
    {
        $this->validateCollections($collections);

        $collections = $this->getAllCollectionsIfEmpty($collections);

        foreach ($collections as $collection) {
            $this->data[$collection] = [];
        }
    }

    /**
     * Reverts dummy collections to initial state
     *
     * @param array $collections
     *
     * @throws UnexpectedValueException
     * @return void
     */
    public function revertCollections(array $collections = [])
    {
        $this->validateCollections($collections);

        $collections = $this->getAllCollectionsIfEmpty($collections);

        foreach ($collections as $collection) {
            $this->data[$collection] = self::$initialData[$collection];
            $this->update($collection);
        }
    }

    /**
     * Drop collection from database
     *
     * @param array $collections
     *
     * @return void
     */
    public function dropCollections(array $collections = [])
    {
        $this->validateCollections($collections);

        $collections = $this->getAllCollectionsIfEmpty($collections);

        foreach ($collections as $key) {
            unset($this->data[$key]);
        }
    }

    /**
     * @param string $object
     * @param string $method
     * @param array  $arguments
     *
     * @return array
     */
    public function recordArguments($object, $method, array $arguments)
    {
        if (!method_exists($object, $method)) {
            throw new UnexpectedValueException('Invalid method');
        }

        $this->callArguments[] = [get_class($object) . '::' . $method => $arguments];
    }

    /**
     * @return void
     */
    public function recordTrace()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $tmp = $e->getTrace();
            array_shift($tmp);
            $this->traces[] = $tmp;
        }
    }

    /**
     * @param array $collections
     *
     * @return array
     */
    protected function getAllCollectionsIfEmpty(array $collections)
    {
        if (empty($collections)) {
            $collections = array_keys($this->data);
        }

        return $collections;
    }

    /**
     * @param string $class
     * @param string $method
     * @param array  $arguments
     * @param bool   $times
     *
     * @return bool
     */
    public function wasCalled($class, $method, array $arguments = null)
    {
        $traces  = $this->getFullTraceDetails($class, $method);
        $counter = 0;

        foreach ($traces as $trace) {
            foreach ($trace as $calls) {
                if ($calls['function'] == $method && $calls['class'] == $class && (is_null($arguments) || $calls['args'] == $arguments)) {
                    $counter++;
                }
            }
        }

        return $counter;
    }

    /**
     * @param array $collections
     *
     * @throws UnexpectedValueException
     * @return void
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
     * @throws UnexpectedValueException
     * @return void
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

    /**
     * @param string $class
     * @param string $method
     *
     * @return array
     */
    protected function getFullTraceDetails($class, $method)
    {
        if (!method_exists($class, $method)) {
            throw new UnexpectedValueException('Invalid method');
        }

        $return = [];
        // iterate through recorded traces
        foreach ($this->traces as $trace) {
            $save       = false;
            $usefulData = [];
            // iterate through calls withing every trace
            foreach ($trace as $calls) {
                // if call is made inside the trace, save the trace and return it
                if ($calls['function'] == $method && $calls['class'] == $class) {
                    $save = true;
                }
                // prepare trace as if it was going to be returned...
                $usefulData[] = [
                    'function' => $calls['function'],
                    'class'    => $calls['class'],
                    'args'     => $calls['args'],
                ];
            }
            // ... but only return it if needed
            if ($save) {
                $return[] = $usefulData;
            }
        }

        return $return;
    }

    /**
     * @return array
     */
    public function getCallArguments()
    {
        return $this->callArguments;
    }
}