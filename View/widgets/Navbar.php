<?php
/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/25/2017
 * Time: 5:55 PM
 */
class Navbar
{

    private $template;
    private $dictionary;

    private $visitor_links = [
        [
            "link" => "index.php?controller=user&action=login",
            "label" => "Login"
        ],
        [
            "link" => "index.php?controller=user&action=register",
            "label" => "Register"
        ]
    ];
    private $user_links = [
            "profile" => "Profile",
            "history" => "History",
            "separator",
            "log_out" => "Log out"
        ];
    private $librarian_links = [
            "manage_books" => "Manage books",
            "manage_users" => "Manage users",
            "manage_reserves" => "Manage reserves",
            "pick_book" => "Pick book",
            "separator",
            "return_book" => "Return book",
            "reserve_book" => "Make reservation",
            "separator"
        ];

    function __construct()
    {
        if (session_status() == PHP_SESSION_NONE) session_start();
        if (isset($_SESSION['logged']) && $_SESSION['logged'] == true){
            $this->dictionary = ['username' => $_SESSION['username']];
            if ($_SESSION['role'] == 3) {
                $this->template = file_get_contents("View/templates/navbarAdmin.html");
            } else if ($_SESSION['role'] == 2) {
                $this->template = file_get_contents("View/templates/navbarLibrarian.html");
            } else {
                $this->template = file_get_contents("View/templates/navbarLogged.html");
            }
        }else{
            $this->template = file_get_contents("View/templates/navbarDefault.html");
            $this->dictionary = [];
        }
    }

    public function __toString()
    {
        foreach ($this->dictionary as $clave => $valor) {
            $this->template = str_replace('{'. $clave .'}', $valor, $this->template);
        }
        return $this->template;
    }
}