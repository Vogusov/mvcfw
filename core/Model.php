<?php

namespace app\core;

abstract class Model
{
    /**
     * Правила валидации
     */
    public const RULE_REQUIRED = 'required';
    public const RULE_EMAIL = 'email';
    public const RULE_MIN = 'min';
    public const RULE_MAX = 'max';
    public const RULE_MATCH = 'matches';

    /**
     * @var array - Массив, наполняемый ошибками при валидации
     */
    public array $errors = [];


    /**
     *
     * @param $data
     * @return void
     */
    public function loadData($data)
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * Абстрактный метод.
     * Возвращает ассоциативный массив с правилами валидации для каждого поля формы.
     * Правила валидации назначаются в зависимости от модели.
     * Array[ 'fieldName1' => [self::RULE_REQUIRED, ..], ..]
     * @return array
     */
    abstract public function rules(): array;



    public function validate()
    {
        foreach ($this->rules() as $attribute => $rules) {
            $value = $this->{$attribute}; // объявляются в наследуемых классах
            foreach ($rules as $rule) {
                $ruleName = $rule;
                if (is_array($ruleName)) {
                    $ruleName = $rule[0];
                }
                /* Валидация RULE_REQUIRED */
                if ($ruleName === self::RULE_REQUIRED && !$value) { // если значение не существует (не введено пользователем)
                    // todo STOP HERE at 19:54
                    $this->addError($attribute, self::RULE_REQUIRED);
                }
                /* Валидация RULE_EMAIL */
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addError($attribute, self::RULE_EMAIL);
                }
                /* Валидация RULE_MIN */
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $this->addError($attribute, self::RULE_MIN, $rule);
                }
                /* Валидация RULE_MAX */
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $this->addError($attribute, self::RULE_MAX, $rule);
                }
                /* Валидация RULE_MATCH */
                if ($ruleName === self::RULE_MATCH && $value !== $rule['match']) {
                    $this->addError($attribute, self::RULE_MATCH, $rule);
                }
            }
        }
        return empty($this->errors);
    }

    public function addError(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        /* Подстановка значений параметров в сообщения */
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }


    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field required',
            self::RULE_EMAIL => 'This field must valid email address',
            self::RULE_MIN => 'Min length of this field must be {min}',
            self::RULE_MAX => 'Max length of this field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}'
        ];
    }
}