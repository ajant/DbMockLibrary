<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\AbstractImplementation;
use DbMockLibrary\Exceptions\AlreadyInitializedException;
use DbMockLibrary\Exceptions\DbOperationFailedException;
use DbMockLibrary\Exceptions\InvalidDependencyException;
use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Exception;
use InvalidArgumentException;
use SimpleArrayLibrary\SimpleArrayLibrary;
use UnexpectedValueException;

class Elasticsearch extends AbstractImplementation
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
     * @var  array $indexTypes
     */
    protected static $indexTypes;

    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @param array $hosts
     * @param array $initialData
     * @param array $dependencies
     * @param array $indexTypes
     *
     * @throws AlreadyInitializedException
     * @throws UnexpectedValueException
     * @throws InvalidDependencyException
     */
    public static function initElasticsearch(array $hosts, array $initialData, array $dependencies, array $indexTypes)
    {
        if (!static::$instance) {
            if ($initialData !== [] && !SimpleArrayLibrary::isAssociative($initialData)) {
                throw new UnexpectedValueException('Invalid indices names');
            }

            static::$initialData = $initialData;
            static::initDependencyHandler($initialData, $dependencies);
            static::$instance->client = ClientBuilder::create()->setHosts($hosts)->build();
            static::$indexTypes = $indexTypes;

            // make changes reflect immediately
            static::$instance->client->indices()->refresh();
        } else {
            throw new AlreadyInitializedException('Elasticsearch library already initialized');
        }
    }

    /**
     * Insert into database
     *
     * @param string $indexName
     * @param string $id
     *
     * @return void
     * @throws InvalidArgumentException
     * @throws DbOperationFailedException
     */
    protected function insert($indexName, $id)
    {
        $client = static::$instance->client;
        try {
            $indexType = $this->getType($indexName);
            if ($indexType === '.percolator') {
                if (!array_key_exists('body', $this->data[$indexName][$id])) {
                    throw new InvalidArgumentException('Percolator element data must contain "body" section');
                }

                $indexData = [
                    'index' => $indexName,
                    'type' => $this->getType($indexName),
                    'id' => $id,
                    'body' => $this->data[$indexName][$id]['body'],
                ];
                if (array_key_exists('routing', $this->data[$indexName][$id])) {
                    $indexData = array_merge($indexData, ['routing' => $this->data[$indexName][$id]['routing']]);
                }

                $client->index($indexData);
            } else {
                $client->index(
                    [
                        'index' => $indexName,
                        'type' => $this->getType($indexName),
                        'id' => $id,
                        'body' => $this->data[$indexName][$id],
                    ]
                );
            }
        } catch (Exception $e) {
            throw new DbOperationFailedException('Insert failed: ' . $e->getMessage());
        }

        $this->recordInsert($indexName, $id);

        // make changes reflect immediately
        $this->client->indices()->refresh();
    }

    /**
     * Delete from database
     *
     * @param string $indexName
     * @param string $id
     *
     * @return void
     * @throws DbOperationFailedException
     */
    protected function delete($indexName, $id)
    {
        $client = static::$instance->client;
        try {
            $indexType = $this->getType($indexName);
            if ($indexType === '.percolator') {
                $indexData = [
                    'index' => $indexName,
                    'type' => $this->getType($indexName),
                    'id' => $id,
                ];
                if (array_key_exists('routing', $this->data[$indexName][$id])) {
                    $indexData = array_merge($indexData, ['routing' => $this->data[$indexName][$id]['routing']]);
                }
                $client->delete($indexData);
            } else {
                $client->delete([
                    'index' => $indexName,
                    'type' => $this->getType($indexName),
                    'id' => $id,
                ]);
            }
        } catch (Exception $e) {
            throw new DbOperationFailedException('Delete failed: ' . $e->getMessage());
        }

        // make changes reflect immediately
        $this->client->indices()->refresh();
    }

    /**
     * @param $indexName
     * @return mixed
     * @throws InvalidArgumentException
     */
    private function getType($indexName)
    {
        if (!array_key_exists($indexName, static::$indexTypes)) {
            throw new InvalidArgumentException("Undefined type for index $indexName");
        }
        return static::$indexTypes[$indexName];
    }
}
