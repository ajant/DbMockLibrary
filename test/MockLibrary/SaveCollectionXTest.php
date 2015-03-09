<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);

        // invoke logic & test
        MockLibrary::getInstance()->saveCollection($data['value'], $data['collection']);
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