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

    public string $layout = 'main';
    public string $userClass;
    public static Application $app;
    public SiteController $siteController;
    public Router $router;
    public Request $request;
    public Response $response;
    public Session $session;
    public Database $db;
    public ?Controller $controller = null;
    public ?DbModel $user;

    /**
     * @param $rootPath
     * @param array $config Настройки
     */
    public function __construct(string $rootPath, array $config)
    {
        $this->userClass = $config['userClass'];
        self::$ROOT_DIR = $rootPath;
        self::$app = $this;
        $this->siteController = new SiteController();
        $this->response = new Response();
        $this->session = new Session();
        $this->request = new Request();
        $this->router = new Router($this->request, $this->response);

        $this->db = new Database($config['db']);

        $primaryValue = $this->session->get('user');
        if ($primaryValue) {
            $primaryKey = $this->userClass::primaryKey();
            $this->user = $this->userClass::findOne([$primaryKey => $primaryValue]);
        } else {
            $this->user = null;
        }
    }


    public function run()
    {
        try {
            echo $this->router->resolve();
        } catch (\Throwable $e) {
            $this->response->setStatusCode($e->getCode());
            echo $this->router->renderView('_error', [
                'exception' => $e
            ]);
        }
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

    public function login(DbModel $user)
    {
        $this->user = $user;
        $primaryKey = $user->primaryKey();
        $primaryValue = $user->{$primaryKey};
        $this->session->set('user', $primaryValue);
        return true;
    }

    public function logout()
    {
        $this->user = null;
        $this->session->remove('user');
    }

    public static function isGuest() {
        return !self::$app->user;
    }
}
