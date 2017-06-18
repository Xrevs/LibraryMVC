<?php

/**
 * Created by PhpStorm.
 * User: cicles
 * Date: 16/03/2017
 * Time: 17:36
 */
require_once "Model/Sql.php";
class Booking extends Sql
{
    const table = "booking";
    public function __construct()
    {
        parent::__construct(self::table);
    }

    function getAll() {
        $query = $this->select("*", "id = id");
        $result = [];
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['returned'] == 0) $row['returned'] = "Pending";
            else $row['returned'] = "Returned";
            $result[] = $row;
        }
        return $result;
    }

    function book($book_id, $user_id, $from, $to) {
        $checkBooked = $this->select("*", "book_id = '$book_id' AND in_date > '$from'");
        $userHasAlreadyBooked = $this->select("*", "user_id = '$user_id' AND book_id = '$book_id'");

        if (mysqli_num_rows($userHasAlreadyBooked) != 0) return "user";
        if (mysqli_num_rows($checkBooked) != 0) return false;

        return $this->insert("null, '$book_id', '$from', '$to', $user_id, 0");
    }

    function setReturned($id) {
        $query = $this->update("returned = 1", "id = $id AND returned = 0");
        return $query;
    }

    function userBookings($user_id) {
        $query = $this->select("*", "user_id = $user_id");
        $result = [];
        if ($query) {
            require_once "Model/Book.php";
            $model = new Book();
            while ($row = mysqli_fetch_assoc($query)) {
                $row['title'] = $model->load($row['book_id'])['title'];
                $result[] = $row;
            }
            return $result;
        }
        return false;
    }

    function filter($filter = "", $keywords = "") {

        if ($keywords !== "") $keywords = " LIKE '%$keywords%'";
        if ($filter == "") $filter = "id = id";
        $query = $this->select("*", $filter . $keywords);

        $result = [];
        while ($row = mysqli_fetch_assoc($query)) {
            if ($row['returned'] == 0) $row['returned'] = "Pending";
            else $row['returned'] = "Returned";
            $result[] = $row;
        }

        return $result;
    }

    function flushAll() {
        return $this->delete("", "returned = true");
    }
}