<?php

require_once "Model/Sql.php";
class Book extends Sql
{
    const table = "books";

    public function __construct()
    {
        parent::__construct(self::table);
    }

    public function load($id) {
        $query = $this->select("*", "id = '$id'");
        $result = mysqli_fetch_assoc($query);
        return $result;
    }

    function search($filter = "", $keywords = "") {

        if ($keywords !== "") $keywords = " LIKE '%$keywords%'";
        if ($filter == "") $filter = "id = id";
        $query = $this->select("*", $filter . $keywords);

        $result = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }

        return $result;
    }

    public function add($data) {
        $id = $data['id'];
        $title = $data['title'];
        $author = $data['author'];
        $availability = $data['availability'];
        $category = $data['category'];
        $state = $data['state'];
        $cover = $data['cover'];
        return $this->insert("'$id', '$title', '$state', '$availability', '$author', '$cover', '$category'");
    }

    function remove($id) {
        $result = $this->delete("", "id = '$id'");
        return $result;
    }
}