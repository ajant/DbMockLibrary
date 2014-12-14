<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\Exceptions\AlreadyInitializedException;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use DbMockLibrary\AbstractMockLibrary;
use PDO;
use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class MySQL extends AbstractMockLibrary
{
    /**
     * @var MySQL $instance
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
            self::$instance->connection  = new PDO('mysql:host=' . $serverName . ';dbname=' . $database, $username, $password);
            self::$instance->primaryKeys = [];
            foreach ($initialData as $table => $data) {
                $stmt = self::$instance->connection->prepare("SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'");
                $stmt->execute();
                self::$instance->primaryKeys[$table] = [];
                foreach ($stmt->fetchAll() as $row) {
                    self::$instance->primaryKeys[$table][] = $row['Column_name'];
                }
            }

            foreach (self::$instance->primaryKeys as $table => $keys) {
                foreach ($initialData[$table] as $row) {
                    if (!SimpleArrayLibrary::hasAllKeys($row, $keys)) {
                        throw new UnexpectedValueException('Missing keys in initial data for table: ' . $table);
                    }
                }
            }

            // edit $data array where needed
            self::$instance->update();
        } else {
            throw new AlreadyInitializedException('MySQL library already initialized');
        }
    }

    /**
     * @return MySQL
     */
    public static function getInstance()
    {
        return self::$instance;
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
    public function insert($collection, $id)
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
    public function delete($collection, $id)
    {
        $query      = 'DELETE FROM ' . $collection . ' WHERE ';
        $conditions = [];
        $values     = [];
        foreach ($this->primaryKeys[$collection] as $key) {
            $conditions[] = $key . ' = ' . ':' . $key;
            $values[$key] = $this->data[$collection][$id][$key];
        }
        $query .= implode(', ', $conditions);
        $stmt = $this->connection->prepare($query);

        if (!$stmt->execute($values)) {
            throw new DbOperationFailedException('Delete failed');
        }
    }
}