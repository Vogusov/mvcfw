<?php

namespace app\core;

use app\core\exception\NotFoundException;

/**
 * Class Router
 * @package app\core;
 */
class Router
{
    public Response $response;
    public Request $request;
    protected array $routes = [];

    /**
     * @param Request $request
     * @param Response $response
     */
    public function __construct(Request $request, Response $response) //
    {
        //        $this->siteController = $siteController;
        $this->request = $request;
        $this->response = $response;
    }


    public function get($path, $callback)
    {
        $this->routes['get'][$path] = $callback;
    }

    public function post($path, $callback)
    {
        $this->routes['post'][$path] = $callback;
    }

    public function resolve()
    {
        $path = $this->request->getPath();
        $method = $this->request->method();
        $callback = $this->routes[$method][$path] ?? false;
        if ($callback === false) {
            
            throw new NotFoundException();
        }
        if (is_string($callback)) {
            return $this->renderView($callback);
        }
        if (is_array($callback)) {
            /**
             * @var \app\core\Controller $controller
             */
            $controller = new $callback[0]();
            Application::$app->controller = $controller;
            $controller->action = $callback[1];
            $callback[0] =  $controller;

            foreach($controller->getMiddlewares() as $middleware) {
                $middleware->execute();
            }
        }
        return call_user_func($callback, $this->request, $this->response);
    }

    /**
     * Рендер шаблона с данными из view
     * @param string $view
     * @param array $params
     * @return array|false|string|string[]
     */
    public function renderView(string $view, array $params = [])
    {
        $layoutContent = $this->layoutContent();
        $viewContent = $this->renderOnlyView($view, $params);
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }

    /**
     * Получение данных из шаблона для интеграции в view
     * @param string $view Название view-файла с данными
     * @return false|string
     */
    protected function renderOnlyView(string $view, ?array $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    /**
     * Получение основного шаблона для вставки туда данных для рендера страницы
     * @return false|string
     */
    private function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    // /**
    //  * !! Не используется
    //  * @param string $viewContent
    //  * @return array|false|string|string[]
    //  */
    // public function renderContent(string $viewContent)
    // {
    //     $layoutContent = $this->layoutContent();
    //     return str_replace('{{content}}', $viewContent, $layoutContent);
    // }
}
