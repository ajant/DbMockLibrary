<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class GetCollectionElementsTest extends \Test\TestCase
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
        MockDataManipulation::init(['collection' => ['id' => [1]]]);

        // invoke logic
        $result = MockDataManipulation::getInstance()->getCollectionElements($data['collection'], $data['id']);

        // test
        $this->assertEquals($data['expected'], $result);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 collection element found
            [
                [
                    'id'         => 'id',
                    'collection' => 'collection',
                    'expected'   => [1]
                ]
            ],
            // #1 collection found
            [
                [
                    'id'         => false,
                    'collection' => 'collection',
                    'expected'   => ['id' => [1]]
                ]
            ]
        ];
    }
}