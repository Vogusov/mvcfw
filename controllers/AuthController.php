<?php

namespace app\controllers;

use app\core\Application;
use app\core\Controller;
use app\core\Request;
use app\models\User;

class AuthController extends Controller
{
    /**
     * Управляет авторизацией
     * @return array|false|string|string[]
     */
    public function login()
    {
        $this->setLayout('auth');
        return $this->render('login');
    }

    /**
     * Управляет регистрацией
     * В зависимости от метода запроса обрабатывает данные
     * и рендерит их
     * @param Request $request
     * @return array|false|string|string[]|void
     */
    public function register(Request $request)
    {
        $user = new User();

        if ($request->isPost()) {
            $user->loadData($request->getBody());
            if ($user->validate() && $user->save()) {
                Application::$app->session->setFlash('success', 'Thanks for registering');
                Application::$app->response->redirect('/');
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
}
