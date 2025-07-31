<?php

namespace Database\PsqlDB;

use PDO;
use Dotenv\Dotenv;

class PsqlDB
{
    private $db;
    public function __construct() {
        $dotenv = Dotenv::createImmutable(__DIR__ . '/..');
        $dotenv->load();
        $this->db = new PDO(
            "pgsql:host=postgresql;dbname=".$_ENV['DB_NAME'], $_ENV['DB_USER'], $_ENV['DB_PASS']);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected successfully"."\n";
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






}
