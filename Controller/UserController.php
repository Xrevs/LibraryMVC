<?php

/**
 * Created by PhpStorm.
 * User: cicles
 * Date: 24/02/2017
 * Time: 20:55
 *
 * TODO:
 * - Add librarian action for adding/removing a user
 * - Add administrator action for adding/removing any user
 */
class UserController extends Controller
{
    private $dictionary;
    private $template;
    private $scripts;
    private $stylesheets;

    function __construct($action)
    {
        require_once "View/View.php";
        require_once "View/Widget.php";

        $this->$action();

        new View($this->dictionary, $this->template, $this->stylesheets, $this->scripts);
    }

    function login() {
        if (isset($_GET['err'])) $this->template = "login-error.html";
        else $this->template = "login.html";
        $this->stylesheets[] = ['link' => 'registration.css'];
    }

    function registration() {
        if (isset($_GET['err'])) $this->template = "registration-error.html";
        else $this->template = "registration.html";
        $this->stylesheets[] = ['link' => 'registration.css'];
    }

    function validate() {
        $data = $this->getArray(["username" => "POST", "password" => "POST"]);
        $username = $data['username'];
        $password = $data['password'];

        require_once "Model/User.php";
        $model = new User();
        $data = $model->validate($username, $password);

        if ($data != false) {
            session_cache_limiter('nocache,private');
            session_start();
            $_SESSION['id'] = $data['id'];
            $_SESSION['logged'] = true;
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $data['type'];
            header('Location: index.php');
        } else {
            header('Location: index.php?controller=user&action=login&err');
        }
    }

    function logout() {
        session_start();
        setcookie(session_name(), '', 100);
        session_unset();
        session_destroy();
        $_SESSION = array();
        header('Location: index.php');
    }

    function register() {
        $foo = [
            'name' => 'POST',
            'surname' => 'POST',
            'email' => 'POST',
            'telephone' => 'POST',
            'address' => 'POST',
            'username' => 'POST',
            'password' => 'POST',
            'password_confirmation' => 'POST',
        ];
        $data = $this->getArray($foo);
        if ($data['password'] != $data['password_confirmation']) header('Location: index.php?controller=user&action=registration&err');

        require_once "Model/User.php";
        $model = new User();
        if ($model->register($data)) {
            header('Location: index.php');
        }
        else header('Location: index.php?controller=user&action=registration&err');
    }

    function unregister() {
        if (session_status() == PHP_SESSION_NONE) session_start();

        require_once "Model/User.php";
        $model = new User();
        $model->remove($_SESSION['id']);
        $this->logout();
    }
}