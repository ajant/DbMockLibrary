<?php
namespace Test\MockDataManipulation;

use \DbMockLibrary\MockDataManipulation;

class GetCollectionElementsXTest extends \Test\TestCase
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
        $this->setExpectedException('\UnexpectedValueException', $data['errorMessage']);
        MockDataManipulation::init(['collection' => ['id' => []]]);

        // invoke logic & test
        MockDataManipulation::getInstance()->getCollectionElements($data['collection'], $data['id']);
    }

    /**
     * @return array
     */
    public function getData()
    {
        return [
            // #0 invalid collection, id present, validateCollections, inside validateIds, throws the exception
            [
                [
                    'id'           => ['id'],
                    'collection'   => 'fooBar',
                    'errorMessage' => 'Collection \'fooBar\' does not exist'
                ]
            ],
            // #1 invalid id, id present, validateIds throws the exception
            [
                [
                    'id'           => 'fooBar',
                    'collection'   => 'collection',
                    'errorMessage' => 'Element with id \'fooBar\' does not exist'
                ]
            ],
            // #2 invalid collection, id not present, validateCollections throws the exception
            [
                [
                    'id'           => false,
                    'collection'   => 'fooBar',
                    'errorMessage' => 'Collection \'fooBar\' does not exist'
                ]
            ]
        ];
    }
}