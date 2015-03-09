<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);
        $reflection   = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockLibrary::getInstance()->saveRow($data['value'], $data['collection'], $data['id']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockLibrary::getInstance()));
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