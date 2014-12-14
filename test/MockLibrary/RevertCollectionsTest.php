<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection1' => ['id1' => [1], 'id2' => [2]], 'collection2' => ['id3' => [1], 'id4' => [2]]]);
        $reflection = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);
        $dataProperty->setValue(MockLibrary::getInstance(), ['collection1' => [], 'collection2' => []]);

        // invoke logic
        MockLibrary::getInstance()->revertCollections($data['collections']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockLibrary::getInstance()));
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