<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveRowXTest extends \Test\TestCase
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
        MockDataManipulation::getInstance()->saveRow($data['value'], $data['collection'], $data['id']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 field is a row, so value should be array
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
            ]
        ];
    }
}