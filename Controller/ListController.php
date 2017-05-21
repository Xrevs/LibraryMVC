<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/25/2017
 * Time: 4:35 PM
 */
class ListController
{
    function __construct($action)
    {
        $this->$action();
    }

    function users() {
        require_once "Model/User.php";
        $model = new User();
        return $model->listAll();
    }

    function books() {
        require_once "Model/Book.php";
        $model = new BookList();
    }

    function search() {
        require_once "Model/User.php";
    }
}