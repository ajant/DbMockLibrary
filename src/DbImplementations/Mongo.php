<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\Exceptions\AlreadyInitializedException;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use DbMockLibrary\AbstractImplementation;
use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class Mongo extends AbstractImplementation
{
    /**
     * @var Mongo $instance
     */
    protected static $instance;

    /**
     * @var \MongoDB $database
     */
    protected $database;

    /**
     * @param array $initialData
     *
     * @throws AlreadyInitializedException
     * @throws UnexpectedValueException
     * @return void
     */
    public static function initMongo(array $initialData, $database)
    {
        if (!static::$instance) {
            if (empty($database) || !is_string($database)) {
                throw new UnexpectedValueException('Invalid database name');
            }
            if (!SimpleArrayLibrary::isAssociative($initialData) && !empty($initialData)) {
                throw new UnexpectedValueException('Invalid collection names');
            }

            static::$instance             = new static();
            static::$instance->data       = static::$initialData = $initialData;
            $client = new \MongoClient();
            static::$instance->database = $client->selectDB($database);

            // edit $data array where needed
            static::$instance->update();
        } else {
            throw new AlreadyInitializedException('Mongo library already initialized');
        }
    }

    /**
     * @return Mongo
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * Insert into database
     *
     * @param string $collectionName
     * @param string $id
     *
     * @throws DbOperationFailedException
     * @return mixed
     */
    protected function insert($collectionName, $id)
    {
        $collection = static::$instance->database->selectCollection($collectionName);
        if (!$collection->insert($this->data[$collectionName][$id], ['w' => 1])) {
            throw new DbOperationFailedException('Insert failed');
        }
    }

    /**
     * Delete from database
     *
     * @param string $collectionName
     * @param string $id
     *
     * @throws DbOperationFailedException
     * @return void
     */
    protected function delete($collectionName, $id)
    {
        $collection = static::$instance->database->selectCollection($collectionName);
        if (!$collection->remove(['_id' => $this->data[$collectionName][$id]['_id']], ['w' => 1])) {
            throw new DbOperationFailedException('Delete failed');
        }
    }
}