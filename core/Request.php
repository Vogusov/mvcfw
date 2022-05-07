<?php

namespace app\core;

/**
 * Class Request
 * Определяет методы запроса,
 * получает тело запроса,
 * определяет метод по URL
 */
class Request
{
    /**
     * Отделяет GET-параметры и возвращает основной путь
     * @return mixed|string
     */
    public function getPath()
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        if ($position === false) {
            return $path;
        }
        return substr($path, 0, $position);

    }


    /**
     * Определяет метод запроса
     * @return string
     */
    public function method()
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Проверяет метод "на GET"
     * @return bool
     */
    public function isGet()
    {
        return $this->method() === 'get';
    }


    /**
     * Проверяет метод "на POST"
     * @return bool
     */
    public function isPost()
    {
        return $this->method() === 'post';
    }


    /**
     * Фильтрует параметры запросов.
     * Возвращает проверенное тело запроса
     * @return array
     */
    public function getBody()
    {
        $body = [];
        if ($this->method() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->method() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

}