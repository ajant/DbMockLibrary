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
        if (!self::$instance) {
            if (empty($database) || !is_string($database)) {
                throw new UnexpectedValueException('Invalid database name');
            }
            if (!SimpleArrayLibrary::isAssociative($initialData) && !empty($initialData)) {
                throw new UnexpectedValueException('Invalid collection names');
            }

            self::$instance             = new self();
            self::$instance->data       = self::$initialData = $initialData;
            $client = new \MongoClient();
            self::$instance->database = $client->selectDB($database);

            // edit $data array where needed
            self::$instance->update();
        } else {
            throw new AlreadyInitializedException('Mongo library already initialized');
        }
    }

    /**
     * @return Mongo
     */
    public static function getInstance()
    {
        return self::$instance;
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
        $collection = self::$instance->database->selectCollection($collectionName);
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
        $collection = self::$instance->database->selectCollection($collectionName);
        if (!$collection->remove(['_id' => $this->data[$collectionName][$id]['_id']], ['w' => 1])) {
            throw new DbOperationFailedException('Delete failed');
        }
    }
}