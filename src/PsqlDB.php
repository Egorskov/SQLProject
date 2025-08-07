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
 //     echo "Connected successfully"."\n";
        $this->createTable();
    }

    private function createTable(): void
    {
        $this->db->exec("
    CREATE TABLE IF NOT EXISTS users (id SERIAL PRIMARY KEY,
    first_name TEXT NOT NULL,
    last_name TEXT NOT NULL,
    email TEXT NOT NULL UNIQUE)
    ");
    }

    function addUser($arr): array
    {
        if (!empty($arr)) {
            $added = $this->db->prepare("INSERT INTO users (first_name, last_name, email) VALUES (?, ?, ?)");
            $added->execute([$arr['first_name'], $arr['last_name'], $arr['email']]);
            $ID = $this->db->lastInsertId();
            return ['message'=> 'user added with ID = ' . $ID];
        } else {
            return ['message'=> 'no data'];
        }
    }
    function deleteUser($ID): array
    {
        $check = $this->db->prepare("SELECT ID FROM users WHERE id = ?");
        $check->execute([$ID]);
        $deleted = $check->fetch();
        if($deleted) {
            $user = $this->db->prepare("DELETE FROM users WHERE id = ?");
            $user->execute([$ID]);
            return ['message'=>'user ID = ' . $ID . ' found. user deleted with ID = ' . $ID];
        } else {
            return ['message'=>'user ID = ' . $ID . ' not found'];
        }
    }
    function listUsers(): array
    {
        $listed = $this->db->prepare("SELECT * FROM users LIMIT 10");
        $listed->execute();
        $users = $listed->fetchAll(PDO::FETCH_ASSOC);
        return $users;
    }
}
