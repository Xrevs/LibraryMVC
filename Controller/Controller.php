<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 2/25/2017
 * Time: 7:15 PM
 */
class Controller
{
    public function getArray($array)
    {
        $results = array();
        foreach ($array as $item => $where) {
            if (isset($_GET[$item])) $results[$item] = $this->sanitaze($_GET[$item]);
            if (isset($_POST[$item])) $results[$item] = $this->sanitaze($_POST[$item]);
        }
        return $results;
    }

    public function escapeToHTML($string)
    {
        return htmlspecialchars($_GET['username'], ENT_QUOTES);
    }

    public function getJsonParams($paramKey, $key = "")
    {
        $json = json_decode(file_get_contents('site_configuration.json'), true);
        $result = [];

        if (isset($json[$paramKey][$key])) return $json[$paramKey][$key];

        foreach ($json[$paramKey] as $jsonKey => $value) {
            $result[] = [
                "key" => $jsonKey,
                "value" => $value
            ];
        }
        return $result;
    }

    /*TODO: Stop using this, sanitizing should be on the way out, not in*/

    private function sanitaze($key)
    {
        trim($key);
        $valor = str_ireplace("SELECT", "", $key);
        $valor = str_ireplace("COPY", "", $valor);
        $valor = str_ireplace("DELETE", "", $valor);
        $valor = str_ireplace("DROP", "", $valor);
        $valor = str_ireplace("DUMP", "", $valor);
        $valor = str_ireplace(" OR ", "", $valor);
        $valor = str_ireplace("%", "", $valor);
        $valor = str_ireplace("--", "", $valor);
        $valor = str_ireplace("^", "", $valor);
        $valor = str_ireplace("[", "", $valor);
        $valor = str_ireplace("]", "", $valor);
        $valor = str_ireplace("!", "", $valor);
        $valor = str_ireplace("¡", "", $valor);
        $valor = str_ireplace("?", "", $valor);
        $valor = str_ireplace("=", "", $valor);
        $valor = str_ireplace("$", "", $valor);
        $valor = str_ireplace("&", "", $valor);
        return $valor;
    }
}