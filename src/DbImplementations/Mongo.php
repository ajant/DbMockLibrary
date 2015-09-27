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
     * @var static $instance
     */
    protected static $instance;

    /**
     * @var array $initialData
     */
    protected static $initialData;

    /**
     * @var \MongoDB $database
     */
    protected $database;

    /**
     * @param array  $initialData
     * @param string $database
     * @param array  $dependencies
     *
     * @throws AlreadyInitializedException
     */
    public static function initMongo(array $initialData, $database, array $dependencies)
    {
        if (!static::$instance) {
            if (empty($database) || !is_string($database)) {
                throw new UnexpectedValueException('Invalid database name');
            }
            if (!SimpleArrayLibrary::isAssociative($initialData) && !empty($initialData)) {
                throw new UnexpectedValueException('Invalid collection names');
            }

            static::$initialData = $initialData;
            static::initDependencyHandler($initialData, $dependencies);
            $client                     = new \MongoClient();
            static::$instance->database = $client->selectDB($database);
        } else {
            throw new AlreadyInitializedException('Mongo library already initialized');
        }
    }

    /**
     * @return static
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
     * @return void
     * @throws DbOperationFailedException
     */
    protected function insert($collectionName, $id)
    {
        $collection = static::$instance->database->selectCollection($collectionName);
        // throws MongoCursorException
        $status = $collection->insert($this->data[$collectionName][$id], ['w' => 1]);
        if (!empty($status['err'])) {
            throw new DbOperationFailedException('Insert failed');
        }
        $this->recordInsert($collectionName, $id);
    }

    /**
     * Delete from database
     *
     * @param string $collectionName
     * @param string $id
     *
     * @return void
     * @throws DbOperationFailedException
     */
    protected function delete($collectionName, $id)
    {
        $collection = static::$instance->database->selectCollection($collectionName);
        // throws MongoCursorException
        $status = $collection->remove(['_id' => $this->data[$collectionName][$id]['_id']], ['w' => 1]);
        if (!empty($status['err'])) {
            throw new DbOperationFailedException('Delete failed');
        }
    }
}