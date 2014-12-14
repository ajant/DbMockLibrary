<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class SaveDataXTest extends \Test\TestCase
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
        $this->setExpectedException($data['exception'], $data['message']);
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);

        // invoke logic & test
        MockLibrary::getInstance()->saveData($data['value'], $data['collection'], $data['id'], $data['field'], $data['strict']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 collection param invalid
            [
                [
                    'value'      => 'value',
                    'collection' => 'fooBar',
                    'id'         => 'id',
                    'field'      => 'field',
                    'strict'     => true,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Collection \'fooBar\' does not exist'
                ]
            ],
            // #1 id param invalid
            [
                [
                    'value'      => 'value',
                    'collection' => 'collection',
                    'id'         => 'fooBar',
                    'field'      => 'field',
                    'strict'     => true,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Element with id \'fooBar\' does not exist'
                ]
            ],
            // #2 strict param invalid
            [
                [
                    'value'      => 'value',
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => 'field',
                    'strict'     => 'fooBar',
                    'exception'  => '\InvalidArgumentException',
                    'message'    => 'Strict must be a boolean'
                ]
            ],
            // #3 field is required in a collection, but is missing
            [
                [
                    'value'      => 'value',
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => 'fooBar',
                    'strict'     => true,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Invalid field: "fooBar"'
                ]
            ],
            // #4 field is a row, so value should be array
            [
                [
                    'value'      => 'value',
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => '',
                    'strict'     => false,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Row should be an array of fields'
                ]
            ],
            // #5 field is a collection, so value should be at least 2-dimensional array (array of rows)
            [
                [
                    'value'      => ['value'],
                    'collection' => 'collection',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Collection has to be array of rows which are all arrays of fields'
                ]
            ],
            // #6 field a database, so value should be at least 3-dimensional array (array of collections)
            [
                [
                    'value'      => [['value']],
                    'collection' => '',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Data has to be an array of collections which are all arrays of rows which are all arrays of fields'
                ]
            ]
        ];
    }
}