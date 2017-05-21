<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/28/2017
 * Time: 5:01 PM
 *
 * TODO: History.
 * TODO: See other user's profiles.
 */
class ProfileController extends Controller
{
    private $dictionary;
    private $template;
    private $scripts;
    private $stylesheets;

    function __construct($action)
    {
        require_once "View/View.php";
        require_once "View/Widget.php";

        if (session_status() == PHP_SESSION_NONE) session_start();
        $this->template = "profile.html";
        $this->$action();

        new View($this->dictionary, $this->template, $this->stylesheets, $this->scripts);
    }

    /**
     * TODO: Load profile per type of user
     */
    function display() {
        require_once "Model/User.php";
        $model = new User();
        $user = $model->loadSelfProfile($_SESSION['username']);

        $user['profile_pic'] = "./img/" . $user['profile_pic'];
        $user['joined'] = date('d \of F, Y', strtotime($user['joined']));
        $this->dictionary = $user;

        require_once "Model/Booking.php";
        $model = new Booking();
        $results = $model->userBookings($_SESSION['id']);

        if ($results != false) {
            $this->dictionary['bookTableWidget'] = new Widget($results, "bookTableWidget.html");
        } else {
            $foo = [
                [
                    "id" => "",
                    "title" => "<b>No</b>",
                    "out_date" => "<b>Bookings</b>",
                    "in_date" => ""
                ]
            ];
            $this->dictionary['bookTableWidget'] = new Widget($foo, "bookTableWidget.html");
        }
    }

    /**
     * TODO: Error modal.
     * TODO: Custom profile picture
     */
    function update() {
        $foo = [
            "name" => "POST",
            "surnames" => "POST",
            "username" => "POST",
            "email" => "POST",
            "password" => "POST",
            "password_verify" => "POST",
            "telephone" => "POST",
            "address" => "POST"
        ];
        $data = array_filter($this->getArray($foo));

        if (isset($data['password']) && $data['password'] != $data['password_verify']) {
            header("Location: index.php?controller=profile&action=display&errPw");
        }

        if (is_uploaded_file($_FILES['profile_pic']['tmp_name'])) {
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], "./img/" . basename($_FILES['profile_pic']['name']));
            $data['profile_pic'] = basename($_FILES['profile_pic']['name']);
        }

        require_once "Model/User.php";
        $model = new User();
        $result = $model->modify($data, $_SESSION['username']);

        if ($result == true) {
            if (isset($data['username'])) $_SESSION['username'] = $data['username'];
            $this->display();
            header("Location: index.php?controller=profile&action=display&ok");
        } else {
            error_log(print_r($result, TRUE));
            header("Location: index.php?controller=profile&action=display&err");
        }
    }
}