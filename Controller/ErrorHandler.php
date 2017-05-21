<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 4/24/2017
 * Time: 10:12 PM
 */
class ErrorHandler extends Exception
{
    private $type;
    public function __construct($message, $code = 0, $type = null, Exception $previous = null) {
        $this->type = $type;
        parent::__construct($message, $code, $previous);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function printErrorPage() {
        $dictinary = ['code' => $this->code, 'type' => $this->type, 'message' => $this->message];
        $template = "error-page.html";
        include_once "View/View.php";
        new View($dictinary, $template);
        die();
    }
}