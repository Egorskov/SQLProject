<?php

namespace App;

require_once __DIR__ . '/PsqlInterface.php';

class JsonDB implements PsqlInterface
{
    private $db;
    private $data;

    public function __construct($db = 'db.json') {
        $this->db = $db;
        if (!file_exists($this->db)) {
            file_put_contents($this->db, '{}');
        }
        $json = file_get_contents($this->db);
        $this->data = json_decode($json, true);
    }

    public function parse($db): void
    {
        file_put_contents('db.json', json_encode($db, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function addUser($arr): void
    {
        if(!empty($arr)) {
            $ID = 1;
            if (!empty($this->data)) {
                $lastUser = end($this->data);
                $ID = $lastUser['ID'] + 1;
            }
            $arr = ['ID' => $ID] + $arr;
            $this->data[] = $arr;
            $this->parse($this->data);
            echo "user added with ID = $ID\n";
        } else {
            echo "no data\n";
        }
    }

    public function deleteUser($ID): void
    {
        $found = false;
        $newDB = array_filter((array)$this->data, function ($item) use ($ID, &$found) {
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
        $this->parse($newDB);

    }

    public function listUsers(): void
    {
        $users = $this->data;
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
}