<?php

namespace App;

/**
 * php index.php add first_name last_name email -добавить пользователя
 * php index.php add random - добавить рандомного пользователям
 * php index.php delete ID - удалить пользователя по ID
 * php index.php list - показать список пользователей
 */
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/JsonDB.php';
require_once __DIR__ . '/PsqlDB.php';
require_once __DIR__ . '/PsqlInterface.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__.'/..');
$dotenv->load();

$select = $_ENV['DB_SOURCE'];
if ( $select === 'psql' ){
    $db = new PsqlDB();
} elseif ($select === 'json' ){
    $db = new JsonDB();
}

if (php_sapi_name() == 'cli') {
    toCli($argv, $db);
} else {
    toHttp($db);
}

function toCli($argv, $db): void
{
    $argv = $_SERVER['argv'];
    $argc = count($argv);
    $command = $argv[1];

    if ($argc < 2) {
        helper();
    } elseif ($command == 'add') {
        if ($argc < 3) {
            echo "Error: Missing parameters for 'add' command\n";
            helper();
        }
        $arr = createUser($argv);
        if ($arr === null) {
            return;
        }
        $result = $db->addUser($arr);
        echo $result['message']."\n";
    } elseif ($command == 'delete') {
        $id = $argv[2];
        $result = $db->deleteUser($id);
        echo $result['message']."\n";
    } elseif ($command == 'list') {
        $users = $db->listUsers();
        foreach ($users as $user) {
            printf(
                "ID: %d, Имя: %s, Фамилия: %s, Email: %s\n",
                $user['id'],
                $user['first_name'],
                $user['last_name'],
                $user['email']
            );
        }
    } else {
        helper();
    }
}

function toHttp($db): void
{
    header('Access-Control-Allow-Origin: *');
    header('Content-Type: application/json');

    $method = $_SERVER['REQUEST_METHOD'];
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    if ($method === 'GET' && $path === '/list') {
        $users = $db->listUsers();
        echo json_encode($users, JSON_UNESCAPED_UNICODE);
    } elseif ($method === 'DELETE' && str_starts_with($path, '/delete/')) {
        $parts = explode('/', $path);
        $id = (int)$parts[2];
        $result = $db->deleteUser($id);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    } elseif ($method === 'POST'  && $path === '/add') {
        $rawData = file_get_contents('php://input');
        $data = json_decode($rawData, true);
        $result = $db->addUser($data);
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
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
