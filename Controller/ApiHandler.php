<?php

/**
 * Created by PhpStorm.
 * User: Xavier
 * Date: 5/14/2017
 * Time: 7:05 PM
 */
class ApiHandler
{
    private $key = "";

    public function __construct()
    {
        require_once "httpful.phar";
        $this->key = "AIzaSyCDCkCzqDEUjNc51o21iFEtbaWbecBZzqA";
    }

    public function searchByISBN($isbn) {
        $request = \Httpful\Request::get("https://www.googleapis.com/books/v1/volumes?q=isbn:$isbn&key=$this->key")->send();
        $response = json_decode($request, true);

        if ($response['totalItems'] == 0) return false;

        $result = [];
        foreach ($response['items'] as $key => $val) {
            $result[] = [
                "id" => $val['id'],
                "title" => $val['volumeInfo']['title']
            ];
        }
        return $result;
    }

    public function getBookDetails($bookId) {
        $request = \Httpful\Request::get("https://www.googleapis.com/books/v1/volumes/$bookId")->send();
        $response = json_decode($request, true);

        $volumeInfo = $response['volumeInfo'];

        $result = [
            "title" => $volumeInfo['title'],
            "publisher" => $volumeInfo['publisher'],
            "author/s" => implode(", ",$volumeInfo['authors']),
            "year" => $volumeInfo['publishedDate'],
            "cover" => $volumeInfo['imageLinks']['thumbnail'],
            "categories" => implode(", ",$volumeInfo['categories']),
            "pageCount" => $volumeInfo['pageCount'],
            "averageRating" => $volumeInfo['averageRating'],
            "ratingsCount" => $volumeInfo['ratingsCount']
        ];

        return $result;
    }
}