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

    public function addUser($arr): array
    {
        if(!empty($arr)) {
            $ID = 1;
            if (!empty($this->data)) {
                $lastUser = end($this->data);
                $ID = $lastUser['id'] + 1;
            }
            $arr = ['id' => $ID] + $arr;
            $this->data[] = $arr;
            $this->parse($this->data);
            return ['message'=> 'user added with ID = ' . $ID];
        } else {
            return ['message'=> 'no data'];
        }
    }

    public function deleteUser($ID): array
    {
        $found = false;
        $newDB = array_filter((array)$this->data, function ($item) use ($ID, &$found) {
            if ($item['id'] == $ID) {
                $found = true;
                return false;
            }
            return true;
        });
        if (!$found) {
            return ['message'=>'user ID = ' . $ID . ' not found'];
        }
        $this->parse($newDB);
        return ['message'=>'user ID = ' . $ID . ' found. user deleted with ID = ' . $ID];
    }

    public function listUsers() : array
    {
        return $this->data;
    }
}