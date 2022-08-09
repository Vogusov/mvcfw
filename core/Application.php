<?php

namespace app\core;

use app\controllers\SiteController;

/**
 * Class Application
 * @package app\core
 */
class Application
{
    public static string $ROOT_DIR;
    public static Application $app;
    public SiteController $siteController;
    public Router $router;
    public Request $request;
    public Response $response;
    public Database $db;
    public Controller $controller;

    /**
     * @param $rootPath
     * @param array $config Настройки
     */
    public function __construct(string $rootPath, array $config)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->siteController = new SiteController();
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);

        $this->db = new Database($config['db']);
    }


    public function run()
    {
        echo $this->router->resolve();
    }

    /**
     * @return Controller
     */
    public function getController(): Controller
    {
        return $this->controller;
    }

    /**
     * @param Controller $controller
     */
    public function setController(Controller $controller): void
    {
        $this->controller = $controller;
    }
}
