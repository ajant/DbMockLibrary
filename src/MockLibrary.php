<?php

namespace DbMockLibrary;

use DbMockLibrary\Exceptions\InvalidDependencyException;
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
     * @var array $dependencies
     */
    protected $dependencies;

    /**
     * @var array $initialData
     */
    protected static $initialData;

    /**
     * @var array $callArguments
     */
    protected $callArguments = [];

    /**
     * @var array $traces
     */
    protected $traces = [];

    /**
     * @var array $insertedIntoDb
     */
    protected $insertedIntoDb;

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
     *
     * @return void
     */
    public function saveData($value, $collection = '', $id = '', $field = '')
    {
        if (!empty($collection)) {
            if (!empty($id)) {
                if (!empty($field)) {
                    if (!isset($this->data[$collection])) {
                        throw new UnexpectedValueException('Non existing collection');
                    }
                    if (!isset($this->data[$collection][$id])) {
                        throw new UnexpectedValueException('Non existing row');
                    }

                    $this->data[$collection][$id][$field] = $value;
                } else {
                    if (!is_array($value)) {
                        throw new UnexpectedValueException('Row should be an array of fields');
                    }

                    $data[$collection] = isset($data[$collection]) ? $data[$collection] : [];
                    $this->data[$collection][$id] = $value;
                }
            } else {
                if (SimpleArrayLibrary::countMaxDepth($value) <= 1) {
                    throw new UnexpectedValueException('Collection has to be array of rows which are all arrays of fields');
                }

                $this->data[$collection] = $value;
            }
        } else {
            if (SimpleArrayLibrary::countMaxDepth($value) <= 2) {
                throw new UnexpectedValueException('Data has to be an array of collections which are all arrays of rows which are all arrays of fields');
            }

            $this->data = $value;
        }
    }

    /**
     * Wrapper for saveData, for insertion of a field
     *
     * @param $value
     * @param $collection
     * @param $id
     * @param $field
     *
     * @return void
     */
    public function saveField($value, $collection, $id, $field)
    {
        $this->saveData($value, $collection, $id, $field);
    }

    /**
     * Wrapper for saveData, for insertion of a row
     *
     * @param $value
     * @param $collection
     * @param $id
     *
     * @return void
     */
    public function saveRow($value, $collection, $id)
    {
        $this->saveData($value, $collection, $id);
    }

    /**
     * Wrapper for saveData, for insertion of a collection
     *
     * @param $value
     * @param $collection
     *
     * @return void
     */
    public function saveCollection($value, $collection)
    {
        $this->saveData($value, $collection);
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
     * @param array  $arguments
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



    public static function prepareDependencies(array $data, array $dependencies, array $wanted, $prepared = [])
    {
        $prepared = empty($prepared) ? [$wanted] : $prepared;
        foreach ($wanted as $dependentCollection => $dependentIds) {
            foreach ($dependencies as $dependency) {
                $toAdd = [];
                $onCollection = reset(array_keys($dependency[ON]));
                $onField = reset($dependency[ON]);
                $dependentField = reset($dependency[DEPENDENT]);
                // if dependency exists for the wanted collection
                if (isset($dependency[DEPENDENT][$dependentCollection])) {
                    foreach ($data[$onCollection] as $onId => $onRow) {
                        foreach ($dependentIds as $dependentId) {
                            if ($onRow[$onField] == $data[$dependentCollection][$dependentId][$dependentField]) {
                                $toAdd[$onId] = true;
                            }
                        }
                    }
                    $newWanted = [$onCollection => array_keys($toAdd)];
                    $prepared[] = $newWanted;
                    $prepared = self::prepareDependencies($data, $dependencies, $newWanted, $prepared);
                }
            }
        }

        return $prepared;
    }

    public static function validateDependencies(array $dependencies, array $data)
    {
        foreach ($dependencies as $dependency) {
            $dependentCollection = reset(array_keys($dependency[DEPENDENT]));
            $dependentColumn = $dependency[DEPENDENT][$dependentCollection];
            if (!(isset($data[$dependentCollection]) || array_key_exists($dependentCollection, $data))) {
                throw new InvalidDependencyException('Collection "' . $dependentCollection . ' does not exist');
            }
            foreach ($data[$dependentCollection] as $row) {
                if (!(isset($row[$dependentColumn]) || array_key_exists($dependentColumn, $row))) {
                    throw new InvalidDependencyException('Column "' . $dependentColumn . ' does not exist in a row in a collection "' . $dependentCollection . '"');
                }
            }

            $onCollection = reset(array_keys($dependency[ON]));
            $onColumn = $dependency[ON][$onCollection];
            if (!(isset($data[$onCollection]) || array_key_exists($onCollection, $data))) {
                throw new InvalidDependencyException('Collection "' . $onCollection . ' does not exist');
            }
            foreach ($data[$onCollection] as $row) {
                if (!(isset($row[$onColumn]) || array_key_exists($onColumn, $row))) {
                    throw new InvalidDependencyException('Column "' . $onColumn . ' does not exist in a row in a collection "' . $onCollection . '"');
                }
            }
        }
        foreach ($data as $collection => $whatever) {
            $levels = [[$collection]];
            for ($i = 0; $i < count($levels); $i++) {
                $newDependencies = [];
                foreach ($levels[$i] as $collectionToCheck) {
                    foreach ($dependencies as $dependency) {
                        if ($collectionToCheck == ($dependentCollection = reset(array_keys($dependency[DEPENDENT])))) {
                            if ($i != 0 && ($onCollection = reset(array_keys($dependency[ON]))) == $collection) {
                                throw new InvalidDependencyException('Collection: ' . $collection . ' depends on itself via ' . $dependentCollection);
                            } else {
                                $newDependencies[] = $onCollection = reset(array_keys($dependency[ON]));
                            }
                        }
                    }
                }
                if (!empty($newDependencies)) {
                    $levels[$i + 1] = $newDependencies;
                }
            }
        }
    }



    /**
     * @param array $table
     * @param string $column
     * @param bool  $ascending
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function orderTableByColumn(array $table, $column, $ascending = true)
    {
        if (empty($table)) {
            return $table;
        }

        if (SimpleArrayLibrary::isAssociative($table)) {
            throw new InvalidArgumentException('No point in sorting non-numeric array');
        }

        for ($i = 0; $i < count($table) - 1; $i++) {
            for ($j = $i + 1; $j < count($table); $j++) {
                if (!isset($table[$i][$column])) {
                    throw new InvalidArgumentException("Sort column missing from row: '{$i}'");
                }
                if (!isset($table[$j][$column])) {
                    throw new InvalidArgumentException("Sort column missing from row: '{$j}'");
                }

                // sort
                if ($ascending) {
                    if ($table[$i][$column] > $table[$j][$column]) {
                        $tmp       = $table[$i];
                        $table[$i] = $table[$j];
                        $table[$j] = $tmp;
                    }
                } else {
                    if ($table[$i][$column] < $table[$j][$column]) {
                        $tmp       = $table[$i];
                        $table[$i] = $table[$j];
                        $table[$j] = $tmp;
                    }
                }
            }
        }

        return $table;
    }

    /**
     * @param array $table1
     * @param array $table2
     * @param array $joinColumns
     * @param array $selectColumns1
     * @param array $selectColumns2
     * @param bool  $distinct
     *
     * @throws InvalidArgumentException
     * @return array
     */
    public static function joinTables(array $table1, array $table2, array $joinColumns, array $selectColumns1, array $selectColumns2, $distinct = false)
    {
        // input validation
        if (empty($joinColumns)) {
            throw new InvalidArgumentException('No join condition provided');
        }
        if (empty($selectColumns1) && empty($selectColumns1)) {
            throw new InvalidArgumentException('No columns selected from the joining result');
        }
        $table1Invalid = count(SimpleArrayLibrary::getRectangularDimensions($table1)) != 2 && !empty($table1);
        $table2Invalid = count(SimpleArrayLibrary::getRectangularDimensions($table2)) != 2 && !empty($table2);
        if ($table1Invalid || $table2Invalid) {
            if ($table1Invalid && $table2Invalid) {
                throw new InvalidArgumentException('Neither of the tables is a rectangular 2 dimensional array');
            } elseif ($table1Invalid) {
                throw new InvalidArgumentException('Table one is not a rectangular 2 dimensional array');
            } else {
                throw new InvalidArgumentException('Table two is not a rectangular 2 dimensional array');
            }
        }
        if (!empty(array_intersect($selectColumns1, $selectColumns2))) {
            throw new InvalidArgumentException('Select columns must have no equal values, or columns in the result will be overwritten');
        }
        if (!is_bool($distinct)) {
            throw new InvalidArgumentException('Invalid distinct parameter');
        }

        if (empty($table1) || empty($table2)) {
            return [];
        }

        $return = [];
        foreach ($table1 as $key1 => $row1) {
            // input validation
            if (!SimpleArrayLibrary::isSubarray(array_keys($row1), array_keys($joinColumns))) {
                throw new InvalidArgumentException("Join column(s) missing from row {$key1} of first table");
            }
            if (!SimpleArrayLibrary::isSubarray(array_keys($row1), array_keys($selectColumns1))) {
                throw new InvalidArgumentException("Selected column(s) missing from row {$key1} of first table");
            }

            foreach ($table2 as $key2 => $row2) {
                // input validation
                if (!SimpleArrayLibrary::isSubarray(array_keys($row2), $joinColumns)) {
                    throw new InvalidArgumentException("Join column(s) missing from row {$key2} of second table");
                }
                if (!SimpleArrayLibrary::isSubarray(array_keys($row2), array_keys($selectColumns2))) {
                    throw new InvalidArgumentException("Selected column(s) missing from row {$key2} of second table");
                }

                // check join conditions
                $match = true;
                foreach ($joinColumns as $key => $value) {
                    if ($row1[$key] != $row2[$value]) {
                        $match = false;
                        break;
                    }
                }

                // join rows if all required columns are matched
                if ($match) {
                    $row = [];
                    // include columns from table 1
                    foreach ($selectColumns1 as $oldKey => $newKey) {
                        $row[$newKey] = $row1[$oldKey];
                    }
                    // include columns from table 2
                    foreach ($selectColumns2 as $oldKey => $newKey) {
                        $row[$newKey] = $row2[$oldKey];
                    }
                    $return[] = $row;
                }
            }
        }

        // remove duplicated rows
        if ($distinct) {
            $return = array_unique($return, SORT_REGULAR);
        }

        return $return;
    }

    /**
     * @param array        $table
     * @param string       $column
     * @param string       $condition
     * @param array|string $value
     *
     * @return array
     * @throws InvalidArgumentException
     */
    public static function whereCondition(array $table, $column, $condition, $value)
    {
        // input validation
        if (!is_numeric($column)
            && (empty($column)
                || !is_string($column))
        ) {
            throw new InvalidArgumentException('Invalid column parameter');
        }

        if (count(SimpleArrayLibrary::getRectangularDimensions($table)) != 2) {
            if (empty($table)) {
                return [];
            } else {
                throw new InvalidArgumentException('Table is not a rectangular 2 dimensional array');
            }
        }

        $validConditions = [
            '>',
            '<',
            '=',
            '==',
            '>=',
            '<=',
            '<>',
            'in',
            'notin'
        ];
        $condition       = strtolower($condition);
        if (!in_array($condition, $validConditions)) {
            throw new InvalidArgumentException('Invalid condition parameter');
        }
        if (in_array($condition, ['in', 'notin'])
            && !is_array($value)
        ) {
            throw new InvalidArgumentException('In case of "in" & "notin" conditions, value must be an array');
        } elseif (!in_array($condition, ['in', 'notin'])
            && !is_numeric($value)
            && !is_string($value)
            && !is_null($value)
        ) {
            throw new InvalidArgumentException('Invalid value');
        }

        $return       = [];
        $tableNumeric = !SimpleArrayLibrary::isAssociative($table);
        foreach ($table as $index => $row) {
            if (!isset($row[$column])) {
                throw new InvalidArgumentException('Column missing from row: ' . $index);
            }

            $keep = true;
            switch ($condition) {
                case '>':
                    $keep = $row[$column] > $value;
                    break;
                case '<':
                    $keep = $row[$column] < $value;
                    break;
                case '>=':
                    $keep = $row[$column] >= $value;
                    break;
                case '<=':
                    $keep = $row[$column] <= $value;
                    break;
                case '=':
                case '==':
                    $keep = $row[$column] == $value;
                    break;
                case '<>':
                    $keep = $row[$column] <> $value;
                    break;
                case 'in':
                    $keep = in_array($row[$column], $value);
                    break;
                case 'notin':
                    $keep = !in_array($row[$column], $value);
                    break;
            }

            if ($keep) {
                if ($tableNumeric) {
                    $return[] = $row;
                } else {
                    $return[$index] = $row;
                }
            }
        }

        return $return;
    }
}