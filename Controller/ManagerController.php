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

        //todo Quick fix to fix details first.
        if ($_SESSION['role'] == "Member") throw new ErrorHandler("You don't have the permissions to view this page. If this was unintentional, please contact the administrator.", 550, 'Permission Denied.');

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

        $this->dictionary['categoryWidget'] = new Widget($this->getJsonParams("category"), "userTypesWidget.html");
        //$this->dictionary['bookingTypeWidget'] = new Widget($this->getJsonParams("protection"), "bookingTypeWidget.html");
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
                foreach ($value as $key2 => $value2) {
                    $nestedWidgetData[] = [
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

        if (isset($data['keywords'])) {
            require_once "Model/Booking.php";
            $model = new Booking();
            $widgetData = $model->filter($data['filter'], $data['keywords']);
        } else {
            require_once "Model/Booking.php";
            $model = new Booking();
            $widgetData = $model->getAll();
        }

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
        $res = $model->add($_POST);
        echo $res;
        exit();
        /*$foo = [
            "title" => "POST",
            "author" => "POST",
            "category" => "POST",
            "year" => "POST",
            "isbn" => "POST",
            "availability" => "POST",
            "state" => "POST"
        ];
        $data = $this->getArray($foo);

        if (is_uploaded_file($_FILES['cover']['tmp_name'])) {
            move_uploaded_file($_FILES['cover']['tmp_name'], "./img/" . basename($_FILES['cover']['name']));
            $data['cover'] = "./img/" . basename($_FILES['cover']['name']);
        } else {
            $data['cover'] = "./img/no-cover.jpg";
        }

        require_once "Model/Book.php";
        $model = new Book();
        $model->add($data);

        header("Location: index.php?controller=manager&action=catalogue");*/
    }

    function removeBook() {
        require_once "Model/Book.php";
        $model = new Book();
        $model->remove($this->getArray(["id" => "GET"])['id']);

        header("Location: index.php?controller=manager&action=catalogue");
    }

    function newBooking() {
        $foo = [
            "book" => "POST",
            "user" => "POST",
            "from" => "POST",
            "to" => "POST"
        ];

        $data = $this->getArray($foo);

        require_once "Model/Booking.php";
        $model = new Booking();
        $result = $model->book($data['book'],$data['user'],$data['from'], $data['to']);

        if ($result) echo 'OK';
        else echo 'NOPE';
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

    /**TODO: Doesn't work for unknown reasons, I'll keep trying to fix it. */
    function modConf() {
        $foo = [
            'key' => 'POST',
            'value' => 'POST',
            'oldKey' => 'POST',
            'oldValue' => 'POST'
        ];
        $data = $this->getArray($foo);
        $config = json_decode(file_get_contents('site_configuration.json'), true);

            foreach ($config as $key => $value) {
            foreach ($value as $key2 => $value2) {
                if ($key2 == $data['oldKey']) {
                    if ($data['key'] != $data['oldKey']) {
                        $config[$key][$data['key']] = $config[$key][$key2];
                        $config[$key][$data['key']] = intval($data['value']);
                        unset($config[$key][$key2]);
                    } else {
                        $config[$key][$key2] = intval($data['value']);
                    }
                }
            }
        }
        file_put_contents('site_configuration.json', json_encode($config, JSON_PRETTY_PRINT));
        echo "OK";
        exit();
    }
}