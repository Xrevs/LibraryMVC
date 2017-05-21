<?php
require_once "Model/Connection.php";

/**
 * TODO: Improve statement building
 */
class Sql extends Connection
{
    private $table;

    public function __construct($table)
    {
        $this->table = $table;
    }

    public function select($field, $condition) {
        $connection = new Connection();
        $connection = $connection->get();
        $query = "SELECT $field FROM $this->table WHERE $condition;";
        $data = mysqli_query($connection, $query);
        $connection->close();
        if (!$data) {
            var_dump($data);
            var_dump($query);
            die();
        }
        return $data;
    }

    public function delete($field, $condition) {
        $connection = new Connection();
        $connection = $connection->get();
        $query = "DELETE $field FROM $this->table WHERE $condition;";
        $data = mysqli_query($connection, $query);
        $connection->close();
        if (!$data) {
            var_dump($data);
            var_dump($query);
            die();
        }
        return $data;
    }

    public function insert($values) {
        $connection = new Connection();
        $connection = $connection->get();
        $query = "INSERT INTO $this->table VALUES ($values);";
        $data = mysqli_query($connection, $query);
        $connection->close();
        if (!$data) {
            var_dump($data);
            var_dump($query);
        }
        return $data;
    }

    public function update($set, $condition) {
        $connection = new Connection();
        $connection = $connection->get();
        $query = "UPDATE $this->table SET $set WHERE $condition;";
        $data = mysqli_query($connection, $query);
        $connection->close();
        if (!$data) {
            var_dump($data);
            var_dump($query);
            die();
        }
        return $data;
    }
}