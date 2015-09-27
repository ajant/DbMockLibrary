<?php

namespace DbMockLibrary;

use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class MockDataManipulation extends DataContainer
{
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
            if (isset(static::$initialData[$collection]) || array_key_exists($collection, static::$initialData)) {
                $this->data[$collection] = static::$initialData[$collection];
            } else {
                unset($this->data[$collection]);
            }
        }
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
}