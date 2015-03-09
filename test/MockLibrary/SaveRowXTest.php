<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);

        // invoke logic & test
        MockLibrary::getInstance()->saveRow($data['value'], $data['collection'], $data['id']);
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