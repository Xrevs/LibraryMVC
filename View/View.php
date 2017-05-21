<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/25/2017
 * Time: 5:36 PM
 */
class View
{
    private $dictionary;
    private $template;

    public function __construct($dictionary, $template, $stylesheets = "", $scripts = ""){
        require_once "View/widgets/Navbar.php";
        require_once "View/Widget.php";

        $defaultStylesheets = [
            ['link' => 'bootstrap.min.css'],
            ['link' => 'bootstrap-theme.min.css'],
            ['link' => 'jquery-ui.min.css'],
            ['link' => 'jquery-ui.theme.min.css'],
            ['link' => 'my-css.css']
        ];
        $defaultScripts = [
            ['link' => 'jquery-3.1.1.min.js'],
            ['link' => 'bootstrap.min.js'],
            ['link' => 'material.min.js'],
            ['link' => 'jquery-ui.min.js']
        ];

        if ($stylesheets != "") {
            $stylesheets = array_merge($defaultStylesheets, $stylesheets);
        } else {
            $stylesheets = $defaultStylesheets;
        }

        if ($scripts != "") {
            $scripts = array_merge($defaultScripts, $scripts);
        } else {
            $scripts = $defaultScripts;
        }

        $this->template = file_get_contents("View/templates/".$template);
        $this->dictionary = $dictionary;

        $this->dictionary['stylesheetWidget'] = new Widget($stylesheets, "stylesheet.html");
        $this->dictionary['scriptWidget'] = new Widget($scripts, "script.html");
        $this->dictionary['navbarWidget'] = new Navbar();

        $this->render();
    }
    public function getTemplate() {
        return $this->template;
    }
    public function setTemplate($template) {
        $this->template = file_get_contents($template);
    }
    public function getDictionary() {
        return $this->dictionary;
    }
    public function setDictionary($dictionary) {
        $this->dictionary = $dictionary;
    }

    public function render()
    {
        $page = $this->template;
        if($this->dictionary != null) {
            foreach ($this->dictionary as $key => $value) {
                if ($value instanceof Widget) {
                    $value = $value->__toString();
                }
                $page = str_replace("{" . $key . "}", $value, $page);
            }
        }
        echo $page;
    }
}