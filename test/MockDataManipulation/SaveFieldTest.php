<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveFieldTest extends \Test\TestCase
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
        MockDataManipulation::getInstance()->saveField($data['value'], $data['collection'], $data['id'], $data['field']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 overwrite field
            [
                [
                    'value'      => 'fooBar',
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => 'field',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'fooBar']]]
                ]
            ],
            // #1 add new field
            [
                [
                    'value'      => 'fooBar',
                    'collection' => 'collection',
                    'id'         => 'id',
                    'field'      => 'fooBar',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'value', 'fooBar' => 'fooBar']]]
                ]
            ]
        ];
    }
}