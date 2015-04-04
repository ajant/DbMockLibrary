<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveCollectionTest extends \Test\TestCase
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
        MockDataManipulation::initDataContainer(['collection' => ['id' => ['field' => 'value']]]);
        $reflection   = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockDataManipulation::getInstance()->saveCollection($data['value'], $data['collection']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 overwrite collection
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => 'fooBar']],
                    'collection' => 'collection',
                    'expected'   => ['collection' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #1 add new collection
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => 'fooBar']],
                    'collection' => 'fooBar',
                    'expected'   => ['collection' => ['id' => ['field' => 'value']], 'fooBar' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ]
        ];
    }
}