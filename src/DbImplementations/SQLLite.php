<?php

namespace DbMockLibrary\DbImplementations;

use DbMockLibrary\AbstractImplementation;
use DbMockLibrary\Exceptions\DbOperationFailedException;

class SQLLite extends AbstractImplementation
{
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
        // TODO: Implement insert() method.
    }

    /**
     * Delete from database
     *
     * @param string $collection
     * @param string $id
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