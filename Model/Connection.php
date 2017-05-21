<?php

class Connection
{
    const database = "library";
    const mysql_server = "localhost";
    const mysql_login = "root";
    const mysql_pass = "";

    public function __construct(){}

    public function get()
    {
        $conexion = new mysqli(self::mysql_server, self::mysql_login, self::mysql_pass, self::database);
        if ($conexion->connect_errno) {
            echo "<h1>Failed to connect to MySQL</h1>";
            die();
        }
        mysqli_set_charset($conexion,"utf8");
        return $conexion;
    }
}