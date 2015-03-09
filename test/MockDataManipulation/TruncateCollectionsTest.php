<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class TruncateCollectionsTest extends \Test\TestCase
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
        MockDataManipulation::init(['collection1' => ['id1' => [1], 'id2' => [2]], 'collection2' => ['id3' => [1], 'id4' => [2]]]);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockDataManipulation::getInstance()->truncateCollections($data['collections']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 truncate selected collections
            [
                [
                    'collections'  => ['collection1'],
                    'expected'     => ['collection1' => [], 'collection2' => ['id3' => [1], 'id4' => [2]]]
                ]
            ],
            // #1 truncate all collections
            [
                [
                    'collections'  => [],
                    'expected'     => ['collection1' => [], 'collection2' => []]
                ]
            ]
        ];
    }
}