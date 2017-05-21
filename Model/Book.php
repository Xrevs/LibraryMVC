<?php

require_once "Model/Sql.php";
class Book extends Sql
{
    const table = "book";

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

        if ($keywords == "") {
            $query = $this->select("*", "id = id");
        } else if ($filter == "") {
            $query = $this->select("*", "title LIKE '%$keywords%' OR author LIKE '%$keywords%' OR category LIKE '%$keywords%'");
        } else {
            $query = $this->select("*", "$filter LIKE '%$keywords%'");
        }

        $result = [];
        while ($row = mysqli_fetch_assoc($query)) {
            $result[] = $row;
        }

        return $result;
    }

    public function add($data) {
        $title = $data['title'];
        $author = $data['author'];
        $categ = $data['category'];
        $year = $data['year'];
        $isbn = $data['isbn'];
        $availability = $data['availability'];
        $state = $data['state'];
        $cover = $data['cover'];
        $result = $this->insert("null, '$title', '$author', '$categ', '$year', '$isbn', '$state', '$availability', '$cover'");
        return $result;
    }

    function remove($id) {
        $result = $this->delete("", "id = $id");
        return $result;
    }
}