<?php
namespace Test\DbImplementations\Mongo;

use \DbMockLibrary\DbImplementations\Mongo;

class RevertCollectionsTest extends \Test\TestCase
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
        Mongo::initMongo(['collection1' => ['id1' => [1], 'id2' => [2]], 'collection2' => ['id3' => [1], 'id4' => [2]]], 'foo', []);
        $reflection = new \ReflectionClass('\DbMockLibrary\DbImplementations\Mongo');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue(Mongo::getInstance(), ['collection1' => [], 'collection2' => []]);

        // invoke logic
        Mongo::getInstance()->revertCollections($data['collections']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(Mongo::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 revert selected collections
            [
                [
                    'collections'  => ['collection2'],
                    'expected'     => ['collection1' => [], 'collection2' => ['id3' => [1], 'id4' => [2]]]
                ]
            ],
            // #1 revert all collections
            [
                [
                    'collections'  => [],
                    'expected'     => ['collection1' => ['id1' => [1], 'id2' => [2]], 'collection2' => ['id3' => [1], 'id4' => [2]]]
                ]
            ]
        ];
    }
}