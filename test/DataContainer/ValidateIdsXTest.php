<?php
namespace DbMockLibrary\Test\DataContainer;

use DbMockLibrary\DataContainer;
use DbMockLibrary\Test\TestCase;

class ValidateIdsXTest extends TestCase
{
    /**
     * @dataProvider getData
     *
     * @param array $data
     *
     * @return void
     */
    public function test_function(array $data)
    {
        // prepare
        $this->setExpectedException($data['exception'], $data['errorMessage']);
        DataContainer::initDataContainer(['collection' => ['id' => []]]);

        // invoke logic & test
        $this->invokeMethodByReflection(DataContainer::getInstance(), 'validateIds', [$data['collection'], $data['id']]);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 invalid collection
            [
                [
                    'id'           => ['id'],
                    'collection'   => 'fooBar',
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Collection \'fooBar\' does not exist'
                ]
            ],
            // #1 invalid id
            [
                [
                    'id'           => ['fooBar'],
                    'collection'   => 'collection',
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Element with id \'fooBar\' does not exist'
                ]
            ],
            // #2 invalid id type
            [
                [
                    'id'           => [new \stdClass()],
                    'collection'   => 'collection',
                    'exception'    => '\InvalidArgumentException',
                    'errorMessage' => 'Invalid id ' . var_export(new \stdClass(), true)
                ]
            ]
        ];
    }
}