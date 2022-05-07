<?php

namespace app\controllers;

use app\core\Controller;
use app\core\Request;
use app\models\RegisterModel;

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
        $registerModel = new RegisterModel();

        if ($request->isPost()) {
            $registerModel->loadData($request->getBody());
            if ($registerModel->validate() && $registerModel->register()) {
                return 'Success';
            }
            return $this->render('register', [
                'model' => $registerModel,
            ]);
        }

        if ($request->isGet()) {
            $this->setLayout('auth');
            return $this->render('register', [
                'model' => $registerModel,
            ]);;
        }
    }


}