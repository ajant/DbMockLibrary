<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);
        $reflection   = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockLibrary::getInstance()->saveField($data['value'], $data['collection'], $data['id'], $data['field']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockLibrary::getInstance()));
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