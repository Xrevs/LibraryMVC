<?php
/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 5/14/2017
 * Time: 8:22 PM
 */
require_once "../Controller/ApiHandler.php";
$handler = new ApiHandler();

echo json_encode($handler->searchByISBN($_GET['isbn']));