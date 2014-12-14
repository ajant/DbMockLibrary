<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\Exceptions\AlreadyInitializedException;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use DbMockLibrary\AbstractMockLibrary;
use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class Mongo extends AbstractMockLibrary
{

    /**
     * @param array  $initialData
     * @param string $serverName
     * @param string $database
     * @param string $username
     * @param string $password
     *
     * @throws AlreadyInitializedException
     * @return void
     */
    public static function init(array $initialData, $serverName, $database, $username, $password)
    {
        if (!self::$instance) {
            if (empty($serverName) || !is_string($serverName)) {
                throw new UnexpectedValueException('Invalid server name');
            }
            if (empty($database) || !is_string($database)) {
                throw new UnexpectedValueException('Invalid database name');
            }
            if (empty($username) || !is_string($username)) {
                throw new UnexpectedValueException('Invalid username');
            }
            if (!is_string($password)) {
                throw new UnexpectedValueException('Invalid password');
            }
            if (!SimpleArrayLibrary::isAssociative($initialData) && !empty($initialData)) {
                throw new UnexpectedValueException('Invalid table names');
            }

            self::$instance              = new self();
            self::$instance->data        = self::$initialData = $initialData;

            // edit $data array where needed
            self::$instance->update();
        } else {
            throw new AlreadyInitializedException('MySQL library already initialized');
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
    protected function insert($collection, $id)
    {
        // TODO: Implement insert() method.
    }

    /**
     * Delete from database
     *
     * @param string $collection
     * @param array  $id
     *
     * @internal param array $data
     *
     * @return void
     */
    protected function delete($collection, $id)
    {
        // TODO: Implement delete() method.
    }
}