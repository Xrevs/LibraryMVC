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

    public function searchByISBN($isbn)
    {
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

    public function getBookDetails($bookId)
    {
        $request = \Httpful\Request::get("https://www.googleapis.com/books/v1/volumes/$bookId")->send();
        $response = json_decode($request, true);

        if (isset($response['error'])) return [
            "publisher" => "unspecified",
            "year" => "-",
            "pageCount" => "0",
            "averageRating" => "0",
            "ratingsCount" => "-"
        ];

        $volumeInfo = $response['volumeInfo'];
        return [
            "id" => $bookId,
            "title" => isset($volumeInfo['title']) ? $volumeInfo['title'] : "Untitled",
            "publisher" => isset($volumeInfo['publisher']) ? $volumeInfo['publisher'] : "-",
            "author" => isset($volumeInfo['authors']) ? implode(", ", $volumeInfo['authors']) : "-",
            "year" => isset($volumeInfo['publishedDate']) ? $volumeInfo['publishedDate'] : "-",
            "cover" => isset($volumeInfo['imageLinks']['thumbnail']) ? $volumeInfo['imageLinks']['thumbnail'] : "no-cover.jpg",
            "category" => isset($volumeInfo['categories']) ? implode("/", $volumeInfo['categories']) : "-",
            "pageCount" => isset($volumeInfo['pageCount']) ? $volumeInfo['pageCount'] : "-",
            "averageRating" => isset($volumeInfo['averageRating']) ? $volumeInfo['averageRating'] : "-",
            "ratingsCount" => isset($volumeInfo['ratingsCount']) ? $volumeInfo['ratingsCount'] : "-"
        ];
    }
}