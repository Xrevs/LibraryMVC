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
        if (!isset($data['filter'])) $data['filter'] = "id = id";

        require_once "Model/Book.php";
        $model = new Book();
        $results = $model->search($data['filter'], $data['keywords']);
        $this->dictionary['bookListWidget'] = new Widget($results, "bookListWidget.html");
        $this->stylesheets[] = ['link' => 'catalogue.css'];
    }

    function details() {
        $id = $_GET['id'];
        $hasPermission = $_SESSION['role'] !== $this->getJsonParams("user", "Member");

        require_once "Model/Book.php";
        $model = new Book();
        $DbDetails = $model->load($id);

        require_once "Controller/ApiHandler.php";
        $handler = new ApiHandler();

        if ($DbDetails) {
            $this->dictionary['modalComponents'] = file_get_contents("View/modals/booking.html");
            $this->dictionary['buttonComponent'] = file_get_contents("View/components/bookItem.html");
            $this->scripts[] = ['link' => 'booking.js'];
        } else if ($hasPermission) {
            $this->dictionary['state'] = "";
            $this->dictionary['availability'] = "";

            $this->dictionary['buttonComponent'] = file_get_contents("View/components/addBookButton.html");
            $this->dictionary['modalComponents'] = file_get_contents("View/modals/addBook.html");

            $this->dictionary['stateWidget'] = new Widget($this->getJsonParams("state"), "paramOption.html");
            $this->dictionary['availabilityWidget'] = new Widget($this->getJsonParams("availability"), "paramOption.html");

            $this->scripts[] = ['link' => 'addBook.js'];
        }

        $this->dictionary = $this->dictionary + $handler->getBookDetails($id);

        if ($DbDetails) $this->dictionary = array_merge($this->dictionary, $DbDetails);

        $this->stylesheets[] = ['link' => 'details.css'];
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
        echo $result;
        exit();
    }
}