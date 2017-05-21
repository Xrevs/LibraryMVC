<?php

require_once "Model/Sql.php";
class User extends Sql {
    const table = "user";

    public function __construct() {
        parent::__construct(self::table);
    }

    // Needs to check if SESSION is forgery or not
    public function loadSelfProfile($username) {
        $condition = "username = '$username'";
        $query = $this->select("*", $condition);
        return mysqli_fetch_assoc($query);
    }

    public function validate($username, $password) {
        $condition = "username = '$username' AND password = '$password'";
        $query = $this->select("*", $condition);
//        var_dump(mysqli_num_rows($query));
        $results = [];
        if (mysqli_num_rows($query) > 0) {
            $row = mysqli_fetch_assoc($query);
            $results['id'] = $row['id'];
            $results['type'] = $row['type'];
        } else {
            return false;
        }
        return $results;
    }

    public function register($data, $type = 1) {
        $name = $data['name'];
        $surname = $data['surname'];
        $email = $data['email'];
        $telephone = $data['telephone'];
        $address = $data['address'];
        $username = $data['username'];
        $password = $data['password'];
        if (isset($data['type'])) $type = $data['type'];
        $values = "null, '$name', '$surname', '$email', '$telephone', '$address', '$username', '$password', DEFAULT, $type, null";
        return $this->insert($values);
    }

    public function modify($data, $username) {
        $set = "";
        foreach ($data as $key => $value) {
            $set .= "$key = '$value',";
        }
        $set = rtrim($set, ',');
        $condition = "username = '$username'";
        return $this->update($set, $condition);
    }

//    TODO: Search users.
    function search($data = "") {
        $query = $this->select("*", "id = id");
        $results = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $results[] = $row;
        }
        return $results;
    }

    function remove($id) {
        $results = $this->delete("", "id = $id");
        return $results;
    }
}