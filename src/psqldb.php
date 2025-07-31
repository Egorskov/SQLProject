<?php

namespace App;

use PDO;

require_once __DIR__ . '/PsqlInterface.php';

class PsqlDB implements PsqlInterface
{
    private $db;
    public function __construct() {
        $this->db = new PDO(
            "pgsql:host=postgresql;dbname=".$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully"."\n";
        $this->createTable();
    }

    private function createTable(): void
    {
        $this->db->exec("
    CREATE TABLE IF NOT EXISTS users (ID SERIAL PRIMARY KEY,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE)
    ");
    }

    function addUser($arr): void
    {
        if (!empty($arr)) {
            $added = $this->db->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)");
            $added->execute([$arr['first_name'], $arr['last_name'], $arr['email']]);
            $ID = $this->db->lastInsertId();
            echo "user added with ID = $ID\n";
        } else {
            echo "no data\n";
        }
    }
    function deleteUser($ID): void
    {
        $check = $this->db->prepare("SELECT ID FROM users WHERE ID = ?");
        $check->execute([$ID]);
        $deleted = $check->fetch();

        if($deleted) {
            echo "user ID = $ID found \n";
            $user = $this->db->prepare("DELETE FROM users WHERE ID = ?");
            $user->execute([$ID]);
            echo "user ID = $ID deleted\n";
        } else {
            echo "user ID = $ID not found\n";
        }
    }
    function listUsers(): void
    {
        $listed = $this->db->prepare("SELECT * FROM users LIMIT 10");
        $listed->execute();
        $users = $listed->fetchAll(PDO::FETCH_ASSOC);
        foreach ($users as $user) {
            printf(
                "ID: %d, Имя: %s, Фамилия: %s, Email: %s\n",
                $user['id'],
                $user['first_name'],
                $user['last_name'],
                $user['email']
            );
        }
    }
}
