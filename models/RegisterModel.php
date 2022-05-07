<?php

namespace app\models;

use app\core\Model;

class RegisterModel extends Model
{
    /**
     * Названия валидируемых атрибутов формы регистрации
     *
     */
    public string $firstname = '';
    public string $lastname = '';
    public string $email = '';
    public string $password = '';
    public string $passwordRepeat = '';

    /**
     * Создает нового пользователя
     * @return void
     */
    public function register() {
        echo "Creating new user";
    }


    /**
     * Возвращает массив полей формы регистрации с соответствующими правилами валидации
     * @return array[]
     */
    public function rules(): array
    {
        return [
          'firstname' => [self::RULE_REQUIRED],
          'lastname' => [self::RULE_REQUIRED],
          'email' => [self::RULE_REQUIRED, self::RULE_EMAIL],
          'password' => [self::RULE_REQUIRED, [self::RULE_MIN, 'min' => 8], [self::RULE_MAX, 'max' => 24]],
          'passwordRepeat' => [self::RULE_REQUIRED, [self::RULE_MATCH, 'match' => 'password']],
        ];
    }
}