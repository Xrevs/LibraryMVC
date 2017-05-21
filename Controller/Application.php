<?php

/**
 * Created by PhpStorm.
 * User: cicles
 * Date: 24/02/2017
 * Time: 20:40
 */
include_once "ErrorHandler.php";
class Application extends Controller
{
    private $controller;
    private $action;

    function __construct()
    {
        try {
            if (isset($_GET['controller']) && isset($_GET['action'])) {
                $data = $this->getArray(["controller" => "GET", "action" => "GET"]);
                $this->controller = ucfirst($data["controller"]) . "Controller";;
                $this->action = $data['action'];
            } else {
                $this->controller="PageController";
                $this->action="render";
            }
            if (!file_exists('Controller/'.$this->controller . ".php")) throw new ErrorHandler('The requested page was not found.', 404, 'Page Not Found.');
            require_once $this->controller . ".php";
            if (!method_exists($this->controller, $this->action)) throw new ErrorHandler('The requested action was not found.', 404, 'Page Not Found.');
            new $this->controller($this->action);
        } catch (ErrorHandler $e) {
            $e->printErrorPage();
            die();
        }
    }
}