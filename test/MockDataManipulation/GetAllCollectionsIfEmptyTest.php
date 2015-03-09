<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class GetAllCollectionsIfEmptyTest extends \Test\TestCase
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
        MockDataManipulation::init(['collection1' => [], 'collection2' => []]);
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