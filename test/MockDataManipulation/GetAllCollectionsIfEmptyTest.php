<?php
namespace DbMockLibrary\Test\MockDataManipulation;

use DbMockLibrary\MockDataManipulation;
use DbMockLibrary\Test\TestCase;

class GetAllCollectionsIfEmptyTest extends TestCase
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
        MockDataManipulation::initDataContainer(['collection1' => [], 'collection2' => []]);
        $reflection                     = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $getAllCollectionsIfEmptyMethod = $reflection->getMethod('getAllCollectionsIfEmpty');
        $getAllCollectionsIfEmptyMethod->setAccessible(true);

        // invoke logic
        $result = $getAllCollectionsIfEmptyMethod->invoke(MockDataManipulation::getInstance(), $data['collections']);

        // test
        $this->assertEquals($data['expected'], $result);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 not empty
            [
                [
                    'collections' => ['collection1'],
                    'expected'    => ['collection1']
                ]
            ],
            // #1 empty
            [
                [
                    'collections' => [],
                    'expected'    => ['collection1', 'collection2']
                ]
            ]
        ];
    }
}