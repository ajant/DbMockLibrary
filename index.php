<?php
require_once 'vendor/autoload.php';

$actual = new \DbMockLibrary\Experimentation\Actual();
$actual->actual();

\DbMockLibrary\DbImplementations\MySQL::init(
    [
        'symptom' => [0 => ['id' => '9', 'name' => 'fooBar', 'description' => 'fooBar']],
        'cause' => [],
        'treatment' => [],
        'treat' => [],
        'disease' => []
    ],
    'localhost',
    'klot',
    'root',
    ''
);

\DbMockLibrary\DbImplementations\MySQL::getInstance()->insert('symptom', '0');
\DbMockLibrary\DbImplementations\MySQL::getInstance()->delete('symptom', '0');