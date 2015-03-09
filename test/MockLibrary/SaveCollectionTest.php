<?php
namespace Test\MockLibrary;

use \DbMockLibrary\MockLibrary;

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
        MockLibrary::init(['collection' => ['id' => ['field' => 'value']]]);
        $reflection   = new \ReflectionClass('\DbMockLibrary\MockLibrary');
        $dataProperty = $reflection->getProperty('data');
        $dataProperty->setAccessible(true);

        // invoke logic
        MockLibrary::getInstance()->saveCollection($data['value'], $data['collection']);

        // test
        $this->assertEquals($data['expected'], $dataProperty->getValue(MockLibrary::getInstance()));
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
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ],
            // #1 add new collection
            [
                [
                    'value'      => ['fooBar' => ['fooBar' => 'fooBar']],
                    'collection' => 'fooBar',
                    'id'         => '',
                    'field'      => '',
                    'strict'     => false,
                    'expected'   => ['collection' => ['id' => ['field' => 'value']], 'fooBar' => ['fooBar' => ['fooBar' => 'fooBar']]]
                ]
            ]
        ];
    }
}