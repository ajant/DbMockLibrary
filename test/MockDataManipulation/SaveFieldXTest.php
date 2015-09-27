<?php
namespace DbMockLibrary\Test\MockDataManipulation;

use DbMockLibrary\MockDataManipulation;
use DbMockLibrary\Test\TestCase;

class SaveFieldXTest extends TestCase
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
        MockDataManipulation::initDataContainer(['collection' => ['id' => ['field' => 'value']]]);

        // invoke logic & test
        MockDataManipulation::getInstance()->saveField($data['value'], $data['collection'], $data['id'], $data['field']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 collection doesn't exist
            [
                [
                    'value'      => 'value',
                    'collection' => 'fooBar',
                    'id'         => 'id',
                    'field'      => 'field',
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Non existing collection'
                ]
            ],
            // #1 row doesn't exist
            [
                [
                    'value'      => 'value',
                    'collection' => 'collection',
                    'id'         => 'fooBar',
                    'field'      => 'field',
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Non existing row'
                ]
            ]
        ];
    }
}