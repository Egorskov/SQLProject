<?php

namespace App;

interface PsqlInterface
{
    public function __construct();
    public function addUser($arr);
    public function deleteUser($ID);
    public function listUsers();
}