<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class SaveDataTest extends \Test\TestCase
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
        MockDataManipulation::getInstance()->saveData($data['value'], $data['collection'], $data['id'], $data['field'], $data['strict']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockDataManipulation::getInstance()));
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 overwrite database
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => ['fooBar' => 'fooBar']]],
                    'collection' => '',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['fooBar' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #1 overwrite collection
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => 'fooBar']],
                    'collection' => 'collection',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #2 add new collection
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => 'fooBar']],
                    'collection' => 'fooBar',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'value']], 'fooBar' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #3 overwrite row
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
            // #4 add new row
            [
                [
                    'value'      => ['fooBar' => 'fooBar'],
                    'collection' => 'collection',
                    'id'         => 'fooBar',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'value'], 'fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #5 overwrite field
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
            // #6 add new field
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