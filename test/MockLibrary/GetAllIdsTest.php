<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

class GetAllIdsTest extends \Test\TestCase
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
        MockLibrary::init([
                'collection1' => ['id1' => [1], 'id2' => [2]], 'collection2' => ['id3' => [1], 'id4' => [2]]
            ]);

        // invoke logic
        $result = MockLibrary::getInstance()->getAllIds($data['collections'], $data['byCollection']);

        // test
        $this->assertEquals($data['expected'], $result);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 get all ids of the collections
            [
                [
                    'byCollection' => false,
                    'collections'  => ['collection1'],
                    'expected'     => ['id1', 'id2']
                ]
            ],
            // #1 get all ids of all collections
            [
                [
                    'byCollection' => false,
                    'collections'  => [],
                    'expected'     => ['id1', 'id2', 'id3', 'id4']
                ]
            ],
            // #2 get all ids of the collections, sort by collection
            [
                [
                    'byCollection' => true,
                    'collections'  => ['collection1'],
                    'expected'     => ['collection1' => ['id1', 'id2']]
                ]
            ]
        ];
    }
}