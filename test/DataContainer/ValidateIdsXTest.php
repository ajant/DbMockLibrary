<?php
namespace Test\DataContainer;

use \DbMockLibrary\DataContainer;

class ValidateIdsXTest extends \Test\TestCase
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
        $this->setExpectedException($data['exception'], $data['errorMessage']);
        DataContainer::init(['collection' => ['id' => []]]);
        $reflection                = new \ReflectionClass(DataContainer::getInstance());
        $validateCollectionsMethod = $reflection->getMethod('validateIds');
        $validateCollectionsMethod->setAccessible(true);

        // invoke logic & test
        $validateCollectionsMethod->invoke(DataContainer::getInstance(), $data['collection'], $data['id']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 invalid collection
            [
                [
                    'id'           => ['id'],
                    'collection'   => 'fooBar',
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Collection \'fooBar\' does not exist'
                ]
            ],
            // #1 invalid id
            [
                [
                    'id'           => ['fooBar'],
                    'collection'   => 'collection',
                    'exception'    => '\UnexpectedValueException',
                    'errorMessage' => 'Element with id \'fooBar\' does not exist'
                ]
            ],
            // #2 invalid id type
            [
                [
                    'id'           => [new \stdClass()],
                    'collection'   => 'collection',
                    'exception'    => '\InvalidArgumentException',
                    'errorMessage' => 'Invalid id ' . var_export(new \stdClass(), true)
                ]
            ]
        ];
    }
}