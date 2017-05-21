<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/26/2017
 * Time: 11:02 PM
 *
 * TODO:
 * - search books
 * - advanced search
 * - add librarian/admin actions for catalogue management
 */
class CatalogueController extends Controller
{
    private $dictionary;
    private $template;
    private $scripts;
    private $stylesheets;

    function __construct($action)
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        require_once "View/View.php";
        require_once "View/Widget.php";

        $this->template = "catalogue.html";
        $this->$action();
        new View($this->dictionary, $this->template, $this->stylesheets, $this->scripts);
    }

    function display() {
        require_once "Model/Book.php";
        $model = new Book();
        $results = $model->search();

        $this->dictionary['bookListWidget'] = new Widget($results, "bookListWidget.html");
        $this->stylesheets[] = ['link' => 'catalogue.css'];
    }

    /**
     * TODO: Add filter searching
     */
    function search() {
        $foo = [
            "keywords" => "GET",
            "filter" => "GET"
        ];
        $data = $this->getArray($foo);
        if (!isset($data['filter'])) $data['filter'] = "";

        require_once "Model/Book.php";
        $model = new Book();
        $results = $model->search($data['filter'], $data['keywords']);

        $this->dictionary['bookListWidget'] = new Widget($results, "bookListWidget.html");
        $this->stylesheets[] = ['link' => 'catalogue.css'];
    }

    /**
     * Depending on the user, will print diferent details.
     * (client will see booking, librarian will see full details without the booking).
     */
    function details() {
        $id = $_POST['id'];
        $result = [];

        require_once "Model/Book.php";
        $model = new Book();
        $DbDetails = $model->load($id);

        require_once "Controller/ApiHandler.php";
        $handler = new ApiHandler();

        $this->dictionary = $handler->getBookDetails($id);
        if ($DbDetails) {
            $this->dictionary['protection'] = $this->getJsonParams("protection", $DbDetails['protection']);
            $this->dictionary['conservation'] = $this->getJsonParams("conservation", $DbDetails['conservation']);
        } else {
            $this->dictionary['protection'] = "";
            $this->dictionary['conservation'] = "";
        }
        $this->stylesheets[] = ['link' => 'details.css'];
        $this->scripts[] = ['link' => 'booking.js'];
        $this->template = "book-details.html";
    }

    function booking() {
        $foo = [
            "id" => "POST",
            "from" => "POST",
            "to" => "POST"
        ];
        $data = $this->getArray($foo);

        require_once "Model/Booking.php";
        $model = new Booking();
        $result = $model->book($data['id'], $_SESSION['id'], $data['from'], $data['to']);
        if ($result) echo 'OK';
        else echo 'NOPE';
        exit();
    }
}