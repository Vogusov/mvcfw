<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\middlewares\AuthMiddleware;
use app\core\Request;
use app\core\Response;
use app\models\User;
use app\models\LoginForm;

class AuthController extends Controller
{
    public function __construct()
    {
        /**
         * Класс. Работает между Requerst и Controller.
         * Напримр, может запрещать доступ к страницам или ко всему контроллеру.
         */
        $this->registerMiddleware(new AuthMiddleware(['profile']));
    }


    /**
     * Управляет авторизацией
     * @return array|false|string|string[]
     */
    public function login(Request $request, Response $response)
    {
        $loginForm = new LoginForm();
        if ($request->isPost()) {
            $loginForm->loadData($request->getBody());
            if ($loginForm->validate() && $loginForm->login()) {
                $response->redirect('/');
                return;
            }
        }

        $this->setLayout('auth');
        return $this->render('login', [
            'model' => $loginForm
        ]);
    }

    /**
     * Управляет регистрацией
     * В зависимости от метода запроса обрабатывает данные
     * и рендерит их
     * @param Request $request
     * @return array|false|string|string[]|void
     */
    public function register(Request $request, Response $response)
    {
        $user = new User();

        if ($request->isPost()) {
            $user->loadData($request->getBody());
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash('success', 'Thanks for registering');
                $response->redirect('/');
                exit;
            }
            return $this->render('register', [
                'model' => $user,
            ]);
        }

        if ($request->isGet()) {
            $this->setLayout('auth');
            return $this->render('register', [
                'model' => $user,
            ]);;
        }
    }

    public function logout(Request $request, Response $response)
    {
        Application::$app->logout();
        $response->redirect('/');
    }

    public function profile() {
        return $this->render('profile');
    }
}
