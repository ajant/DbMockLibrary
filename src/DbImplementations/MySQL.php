<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\Exceptions\AlreadyInitializedException;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use DbMockLibrary\AbstractImplementation;
use PDO;
use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class MySQL extends AbstractImplementation
{
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @var PDO $connection
     */
    protected $connection;

    /**
     * @var array $primaryKeys
     */
    protected $primaryKeys;

    /**
     * @param array  $initialData
     * @param string $serverName
     * @param string $database
     * @param string $username
     * @param string $password
     * @param array  $dependencies
     *
     * @throws AlreadyInitializedException
     */
    public static function initMySQL(array $initialData, $serverName, $database, $username, $password, array $dependencies)
    {
        if (!static::$instance) {
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

            static::$initialData = $initialData;
            static::initDependencyHandler($initialData, $dependencies);
            static::$instance->connection   = new PDO('mysql:host=' . $serverName . ';dbname=' . $database, $username, $password);
            static::$instance->primaryKeys  = [];
            foreach ($initialData as $table => $data) {
                $stmt = static::$instance->connection->prepare("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
                $stmt->execute();
                static::$instance->primaryKeys[$table] = [];
                foreach ($stmt->fetchAll() as $row) {
                    static::$instance->primaryKeys[$table][] = $row['Column_name'];
                }
            }

            foreach (static::$instance->primaryKeys as $table => $keys) {
                foreach ($initialData[$table] as $row) {
                    if (!SimpleArrayLibrary::hasAllKeys($row, $keys)) {
                        throw new UnexpectedValueException('Missing keys in initial data for table: ' . $table);
                    }
                }
            }
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
     * @throws DbOperationFailedException
     * @return void
     */
    protected function insert($collection, $id)
    {
        $data    = $this->data[$collection][$id];
        $columns = array_map(function ($value) {
            return ':' . $value;
        }, array_keys($data));
        $stmt    = $this->connection->prepare(
            'INSERT INTO ' . $collection . ' (' . implode(', ', array_keys($data)) . ') VALUES (' . implode(', ', $columns) . ');'
        );
        if (!$stmt->execute($data)) {
            throw new DbOperationFailedException('Insert failed');
        }
        $this->recordInsert($collection, $id);
    }

    /**
     * Delete from database
     *
     * @param string $collection
     * @param string $id
     *
     * @throws DbOperationFailedException
     * @return void
     */
    protected function delete($collection, $id)
    {
        $query      = 'DELETE FROM ' . $collection . ' WHERE ';
        $conditions = [];
        $values     = [];
        foreach ($this->primaryKeys[$collection] as $key) {
            $conditions[] = '`' . $key . '` = ' . ':' . $key;
            $values[$key] = $this->data[$collection][$id][$key];
        }
        $query .= implode(' AND ', $conditions);
        $stmt = $this->connection->prepare($query);

        if (!$stmt->execute($values)) {
            throw new DbOperationFailedException('Delete failed');
        }
    }
}