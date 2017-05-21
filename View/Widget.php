<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/27/2017
 * Time: 12:03 AM
 *
 * @ChangeLog:
 *      Instead of having an array of N widgets, only one instance
 */
class Widget
{
    private $template;
    private $dictionary;
    function __construct($dictionary, $fileName)
    {
        $this->template = file_get_contents("View/widgets/".$fileName);
        $this->dictionary = $dictionary;
    }

    public function __toString()
    {
//        var_dump($this->dictionary);
        $parsedWidget = "";
        foreach ($this->dictionary as $element) {
            $elemTemp = $this->template;
            foreach ($element as $key => $content) {
                if ($content instanceof Widget) {
                    $content = $content->__toString();
                }
                $elemTemp = str_replace('{'. $key .'}', $content, $elemTemp);
            }
            $parsedWidget .= $elemTemp;
        }
        return $parsedWidget;
    }
}