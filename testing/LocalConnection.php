<?php

include '../src/UrabeAPI.php';

class LocalConnection extends Urabe\Config\KanojoX
{ 
    public function __construct()
    {
        $this->db_driver = Urabe\Config\DBDriver::MYSQL;
        $this->host = "127.0.0.1";
        $this->port = 3306;
        $this->db_name = 'urabe_db';
        $this->user_name='root';
        $this->password = "";
    }
}




$conn = new LocalConnection();
var_dump($conn);