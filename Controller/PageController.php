<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/25/2017
 * Time: 10:04 PM
 */
class PageController extends Controller
{
    private $dictionary;
    private $template;
    private $scripts;
    private $stylesheets;

    function __construct($action)
    {
        require_once "View/View.php";
        require_once "View/Widget.php";

        $this->stylesheets[] = ['link' => 'catalogue.css'];
        $this->template = "homepage.html";
        $this->$action();

        new View($this->dictionary, $this->template, $this->stylesheets, $this->scripts);
    }

    function render() {
        require_once "Model/Book.php";
        $model = new Book();
        $results = $model->search();

        $this->dictionary['bookListWidget'] = new Widget($results, "bookListWidget.html");
    }
}