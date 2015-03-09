<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveRowTest extends \Test\TestCase
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
        MockDataManipulation::init(['collection' => ['id' => ['field' => 'value']]]);
        $reflection   = new \ReflectionClass('\DbMockLibrary\MockDataManipulation');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockDataManipulation::getInstance()->saveRow($data['value'], $data['collection'], $data['id']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 overwrite row
            [
                [
                    'value'      => ['fooBar' => 'fooBar'],
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #1 add new row
            [
                [
                    'value'      => ['fooBar' => 'fooBar'],
                    'collection' => 'collection',
                    'id'         => 'fooBar',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'value'], 'fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ]
        ];
    }
}