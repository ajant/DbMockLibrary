<?php

namespace DbMockLibrary;

use SimpleArrayLibrary\SimpleArrayLibrary;

abstract class AbstractImplementation extends DependencyHandler
{
    /**
     * @var array $insertedIntoDb
     */
    protected $insertedIntoDb = [];

    /**
     * Fill some or all collections with dummy data
     *
     * @param array $records
     *
     * @return void
     */
    public function setUp(array $records = [])
    {
        if (empty($records)) {
            if (is_array($this->data)){
                foreach ($this->data as $collection => $rows) {
                    $records[$collection] = array_keys($rows);
                }
            }
        } elseif (SimpleArrayLibrary::countMaxDepth($records) == 1) {
            $this->validateCollections($records);

            $temp = [];
            foreach ($records as $collection) {
                $temp[$collection] = array_keys($this->data[$collection]);
            }
            $records = $temp;
        } else {
            foreach ($records as $collection => $ids) {
                $this->validateIds($collection, $ids);
            }
        }

        if (empty($this->dependencies)) {
            foreach ($records as $collection => $ids) {
                foreach ($ids as $id) {
                    $this->insert($collection, $id);
                }
            }
        } else {
            foreach ($this->prepareDependencies($records) as $recordToInsert) {
                foreach ($recordToInsert as $collection => $ids) {
                    foreach ($ids as $id) {
                        $this->insert($collection, $id);
                    }
                }
            }
        }
    }

    /**
     * TearDown remove dummy data from one or more collections
     *
     * @param array $records
     *
     * @throws \UnexpectedValueException
     * @return void
     */
    public function tearDown(array $records = [])
    {
        if (empty($records)) {
            if (is_array($this->data)) {
                foreach ($this->data as $collection => $rows) {
                    $records[$collection] = array_keys($rows);
                }
            }
        } elseif (SimpleArrayLibrary::countMaxDepth($records) == 1) {
            $this->validateCollections($records);

            $temp = [];
            foreach ($records as $collection) {
                $temp[$collection] = array_keys($this->data[$collection]);
            }
            $records = $temp;
        } else {
            foreach ($records as $collection => $ids) {
                $this->validateIds($collection, $ids);
            }
        }

        foreach ($records as $collection => $ids) {
            foreach ($ids as $id) {
                $this->delete($collection, $id);
            }
        }
    }

    /**
     * Insert into database
     *
     * @param string $collection
     * @param string $id
     *
     * @return mixed
     */
    abstract protected function insert($collection, $id);

    /**
     * Delete from database
     *
     * @param string $collection
     * @param string $id
     *
     * @return void
     */
    abstract protected function delete($collection, $id);

    /**
     * @param string $collection
     * @param string $id
     *
     * @return void
     */
    protected function recordInsert($collection, $id)
    {
        if (!in_array([$collection => $id], $this->insertedIntoDb)) {
            $this->insertedIntoDb[] = [$collection => $id];
        }
    }

    /**
     * @return void
     */
    public function cleanUp()
    {
        $reverseOrder = array_reverse($this->insertedIntoDb);
        for ($i = 0; $i < count($reverseOrder); $i++) {
            foreach ($reverseOrder[$i] as $collection => $id) {
                $this->delete($collection, $id);
            }
        }
        $this->insertedIntoDb = [];
    }
} 