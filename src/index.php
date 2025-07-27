<?php

/**
 * php index.php add first_name last_name email -добавить пользователя
 * php index.php add random - добавить рандомного пользователям
 * php index.php delete ID - удалить пользователя по ID
 * php index.php list - показать список пользователей
 */

function initJsonDB(): void
{
    if(!file_exists('db.json')) {
        file_put_contents('db.json', '{}');
    }
}

function getJsonDB()
{
    initJsonDB();
    $json = file_get_contents('db.json');
    return json_decode($json, true);
}

function toParse($DB): void
{
    file_put_contents('db.json', json_encode($DB, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
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

function addUser($arr): void
{
    $DB = getJsonDB();
    $ID = 1;
    if (!empty($DB)){
        $lastUser = end($DB);
        $ID = $lastUser['ID'] + 1;
    }
    $arr = ['ID' => $ID] + $arr;
    $DB[] = $arr;
    toParse($DB);
    echo "user added with ID = $ID\n";
}

function deleteUser($ID): void
{
    $DB = getJsonDB();
    $found = false;
    $newDB = array_filter($DB, function ($item) use ($ID, &$found) {
        if ($item['ID'] == $ID) {
            $found = true;
            echo "user ID = $ID found \n";
            echo "user ID = $ID deleted\n";
            return false;
        }
        return true;
    });
    if (!$found) {
        echo "user ID = $ID not found\n";
    }
    toParse($newDB);

}

function listUsers(): void
{
    $users = getJsonDB();
    foreach ($users as $user) {
        printf(
            "ID: %d, Имя: %s, Фамилия: %s, Email: %s\n",
            $user['ID'],
            $user['first_name'],
            $user['last_name'],
            $user['email']
        );
    }
}

$argc = $_SERVER['argv'];
$command = $argv[1];

if ($command == 'add' && $argv[2] == 'random') {
    $arr = [];
    $arr['first_name'] = randomizer();
    $arr['last_name'] = randomizer();
    $arr['email'] = randomizer()."@gmail.com";
    addUser($arr);
} elseif ($command == 'add') {
    $name = $argv[2];
    $lastname = $argv[3];
    $email = $argv[4];
    $arr = [$argv[2], $argv[3], $argv[4]];
    addUser($arr);
} elseif ($command == 'delete') {
    $id = $argv[2];
    deleteUser($id);
} elseif ($command == 'list') {
        listUsers();
} else {
    echo "invalid command\n";
    echo "php index.php add first_name last_name email -добавить пользователя\n";
    echo "php index.php add random - добавить рандомного пользователям\n";
    echo "php index.php delete ID - удалить пользователя по ID\n";
    echo "php index.php list - показать список пользователей";
}

