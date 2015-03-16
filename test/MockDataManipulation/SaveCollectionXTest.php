<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveCollectionXTest extends \Test\TestCase
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
        MockDataManipulation::getInstance()->saveCollection($data['value'], $data['collection']);
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
                    'exception'  => '\UnexpectedValueException',
                    'message'    => 'Collection has to be array of rows which are all arrays of fields'
                ]
            ],
        ];
    }
}