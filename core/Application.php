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
    public function __construct($rootPath)
    {
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->siteController = new SiteController();
        $this->response = new Response();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);
    }


    public function run()
    {
       echo $this->router->resolve();
    }

}