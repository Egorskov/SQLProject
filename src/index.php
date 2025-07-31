<?php

namespace App;

/**
 * php index.php add first_name last_name email -добавить пользователя
 * php index.php add random - добавить рандомного пользователям
 * php index.php delete ID - удалить пользователя по ID
 * php index.php list - показать список пользователей
 */
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/JsonDB.php';
require_once __DIR__ . '/PsqlDB.php';
require_once __DIR__ . '/PsqlInterface.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

$select = $_ENV['DB_SOURCE'];
if ( $select === 'psql' ){
    $db = new PsqlDB();
} elseif ($select === 'json' ){
    $db = new JsonDB();
}


$argv = $_SERVER['argv'];
$argc = count($argv);
$command = $argv[1];

if ($argc < 2){
    helper();
} elseif ($command == 'add'){
    if ($argc < 3) {
        echo "Error: Missing parameters for 'add' command\n";
        helper();
    }
    $arr = createUser($argv);
    if ($arr === null) {
        return;
    }
    $db->addUser($arr);
} elseif ($command == 'delete') {
    $id = $argv[2];
    $db->deleteUser($id);
} elseif ($command == 'list') {
        $db->listUsers();
} else {
    helper();
}

function createUser($argv): ?array
{
    if (count($argv) < 5 && $argv[2] !== 'random') {
        helper();
        return null;
    }
      return $argv[2] == 'random' ?
            ['first_name' => randomizer(),
                "last_name" => randomizer(),
                "email" => randomizer() . "@gmail.com"] :
            ['first_name' => $argv[2],
                "last_name" => $argv[3],
                "email" => $argv[4]];

}

function randomizer($length = 6): string
{
    $characters = 'abcdefghijklmnopqrstuvwxyz';
    $word = '';
    for ($i = 0; $i < $length; $i++) {
        $word .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $word;
}

function helper (): void
{
    echo "invalid command\n";
    echo "php index.php add first_name last_name email - add user\n";
    echo "php index.php add random - add random user\n";
    echo "php index.php delete ID - delete user with ID\n";
    echo "php index.php list - show all users\n";
}