<?php

/**
 * Created by PhpStorm.
 * User: cicles
 * Date: 14/03/2017
 * Time: 19:29
 *
 */
class ManagerController extends Controller
{
    private $dictionary;
    private $template;
    private $scripts;
    private $stylesheets;

    function __construct($action)
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if ($_SESSION['role'] == $this->getJsonParams('user', 'member')) throw new ErrorHandler("You don't have the permissions to view this page. If this was unintentional, please contact the administrator.", 550, 'Permission Denied.');

        require_once "View/View.php";
        require_once "View/Widget.php";

        $this->$action();
        $this->stylesheets[] = ['link' => 'catalogue.css'];
        new View($this->dictionary, $this->template, $this->stylesheets, $this->scripts);
    }

    /**
     * TODO: This could display an overview of everything (num of books, users, etc).
     */
    function display() {
        $this->catalogue();
    }

    function catalogue() {
        require_once "Model/Book.php";
        $model = new Book();
        $widgetData = $model->search();
        $this->dictionary['stateWidget'] = new Widget($this->getJsonParams("state"), "paramOption.html");
        $this->dictionary['availabilityWidget'] = new Widget($this->getJsonParams("availability"), "paramOption.html");
        $this->dictionary['bookManagerWidget'] = new Widget($widgetData, "bookManagerWidget.html");
        $this->template = "catalogue-manager.html";
        $this->scripts[] = ['link' => "googleBooksAJAX.js"];
    }

    function users() {
        require_once "Model/User.php";
        $model = new User();
        $widgetData = $model->search();

        $this->dictionary['userTypesWidget'] = new Widget($this->getJsonParams("user"), "userTypesWidget.html");
        $this->dictionary['userManagerWidget'] = new Widget($widgetData, "userManagerWidget.html");
        $this->template = "users-manager.html";
    }

    function config() {
        $config = json_decode(file_get_contents('site_configuration.json'), true);

        if (is_array($config)) {
            $widgetData = [];
            foreach ($config as $key => $value) {
                $nestedWidgetData = [];
                $paramKey = $key;
                foreach ($value as $key2 => $value2) {
                    $nestedWidgetData[] = [
                        "paramKey" => $paramKey,
                        "key" => $key2,
                        "value" => $value2,
                    ];
                }
                $widgetData[] = [
                    "title" => ucfirst($key)." parameters",
                    "paramElementWidget" => new Widget($nestedWidgetData, 'paramElementWidget.html')
                ];
            }
            $this->dictionary['paramsWidget'] = new Widget($widgetData, 'paramsWidget.html');
            $this->template = "config-manager.html";
            $this->scripts[] = ['link' => 'config.js'];
        } else throw new ErrorHandler('Something went wrong in the server, please contact administrator. (json was not converted properly)', 500, 'Internal Error.');
    }

    function bookings() {
        $foo = [
            "keywords" => "GET",
            "filter" => "GET"
        ];
        $data = $this->getArray($foo);
        require_once "Model/Booking.php";
        $model = new Booking();

        if (isset($data['keywords'])) $widgetData = $model->filter($data['filter'], $data['keywords']);
        else $widgetData = $model->getAll();

        $this->dictionary['bookingManagerWidget'] = new Widget($widgetData, "bookingManagerWidget.html");
        $this->template = "booking-manager.html";
        $this->scripts[] = ['link' => 'booking.js'];
    }

    function newUser() {
        $foo = [
            'name' => 'POST',
            'surname' => 'POST',
            'email' => 'POST',
            'telephone' => 'POST',
            'address' => 'POST',
            'username' => 'POST',
            'password' => 'POST',
            'password_confirmation' => 'POST',
            'type' => 'POST'
        ];
        $data = $this->getArray($foo);
        if (!$this->getJsonParams($data['type'])) throw new ErrorHandler("Seems you tried to change the value directly from the HTML, don't do that!", 503, "Resource not found.");

        require_once "Model/User.php";
        $model = new User();
        $model->register($data);

        header("Location: index.php?controller=manager&action=users");
    }

    function removeUser() {
        require_once "Model/User.php";
        $model = new User();
        $model->remove($this->getArray(["id" => "GET"])['id']);
        $this->users();

        header("Location: index.php?controller=manager&action=users");
    }

    function newBook() {
        require_once "Model/Book.php";
        $model = new Book();
        if (isset($_POST['manual'])) {
            $_POST['id'] = $_POST['isbn'];
            $model->add($_POST);
            header("Location: index.php?controller=manager&action=catalogue");
        }
        echo $model->add($_POST);
        exit();
    }

    function removeBook() {
        require_once "Model/Book.php";
        $model = new Book();
        $model->remove($this->getArray(["id" => "GET"])['id']);

        header("Location: index.php?controller=manager&action=catalogue");
    }

    function newBooking() {
        $foo = [
            "id" => "POST",
            "user" => "POST",
            "from" => "POST",
            "to" => "POST"
        ];

        $data = $this->getArray($foo);

        require_once "Model/Booking.php";
        $model = new Booking();
        $result = $model->book($data['book'],$data['user'],$data['from'], $data['to']);

        if ($result) echo 'OK';
        exit();
    }

    /**TODO: Port to AJAX. */
    function setReturned() {
        $data = $this->getArray(['id' => 'GET']);

        require_once "Model/Booking.php";
        $model = new Booking();
        $model->setReturned($data['id']);

        header("Location: index.php?controller=manager&action=bookings");
    }

    function flushReturned() {
        require_once "Model/Booking.php";
        $model = new Booking();
        $model->flushAll();

        header("Location: index.php?controller=manager&action=bookings");
    }

    function updateParams() {
        $data = $this->getArray([
            'paramKey' => 'POST',
            'key' => 'POST',
            'value' => 'POST',
            'oldKey' => 'POST',
            'new' => 'POST'
        ]);
        $config = json_decode(file_get_contents('site_configuration.json'), true);

        if (isset($data['oldKey'])) unset($config[$data['paramKey']][$data['oldKey']]);
        $config[$data['paramKey']][$data['key']] = is_numeric($data['value']) ? intval($data['value']) : $data['value'];

        file_put_contents('site_configuration.json', json_encode($config, JSON_PRETTY_PRINT));
        echo "OK";
        exit();
    }
}