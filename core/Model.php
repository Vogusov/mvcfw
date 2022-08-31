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
    public const RULE_UNIQUE = 'unique';

    /**
     * @var array - Массив, наполняемый ошибками при валидации
     */
    public array $errors = [];


    /**
     * Присваивает значение атрибутов формы соответствующим свойствам модели, если такие в ней существуют
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

    public function labels(): array
    {
        return [];
    }

    public function getLabel($attribute)
    {
        return $this->labels()[$attribute] ?? $attribute;
    }


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
                    $this->addErrorForRule($attribute, self::RULE_REQUIRED);
                }
                /* Валидация RULE_EMAIL */
                if ($ruleName === self::RULE_EMAIL && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $this->addErrorForRule($attribute, self::RULE_EMAIL);
                }
                /* Валидация RULE_MIN */
                if ($ruleName === self::RULE_MIN && strlen($value) < $rule['min']) {
                    $rule['field'] = $this->getLabel($attribute);
                    $this->addErrorForRule($attribute, self::RULE_MIN, $rule);
                }
                /* Валидация RULE_MAX */
                if ($ruleName === self::RULE_MAX && strlen($value) > $rule['max']) {
                    $rule['field'] = $this->getLabel($attribute);
                    $this->addErrorForRule($attribute, self::RULE_MAX, $rule);
                }
                /* Валидация RULE_MATCH */
                if ($ruleName === self::RULE_MATCH && $value !== $this->{$rule['match']}) {
                    $rule['match'] = $this->getLabel($rule['match']);
                    $this->addErrorForRule($attribute, self::RULE_MATCH, $rule);
                }
                /* Валидация RULE_UNIQUE */
                if ($ruleName === self::RULE_UNIQUE) {
                    $className = $rule['class'];
                    $uniqueAttr = $rule['attribute'] ?? $attribute;
                    $tableName = $className::tableName();
                    $statement = Application::$app->db->prepare("SELECT * FROM $tableName WHERE $uniqueAttr = :attr");
                    $statement->bindValue(":attr", $value);
                    $statement->execute();
                    $record = $statement->fetchObject();
                    if ($record) {
                        $this->addErrorForRule($attribute, self::RULE_UNIQUE, ['field' => $this->getLabel($attribute)]);
                    }
                }
            }
        }
        return empty($this->errors);
    }

    /**
     * Добавляет ошибки для правил.
     * Добавляет ошибки в массив,
     * заменяя поля значениями
     * @param string $attribute
     * @param string $rule
     * @param $params
     * @return void
     */
    private function addErrorForRule(string $attribute, string $rule, $params = [])
    {
        $message = $this->errorMessages()[$rule] ?? '';
        /* Подстановка значений параметров в сообщения */
        foreach ($params as $key => $value) {
            $message = str_replace("{{$key}}", $value, $message);
        }
        $this->errors[$attribute][] = $message;
    }

    /**
     * Добавляет ошибки
     * @param string $attribute
     * @param string $message
     */
    public function addError(string $attribute, string $message)
    {
        $this->errors[$attribute][] = $message;
    }

    /**
     * Список сообщений об ошибках валидации
     * @return string[]
     */
    public function errorMessages()
    {
        return [
            self::RULE_REQUIRED => 'This field required',
            self::RULE_EMAIL => 'This field must be valid email address',
            self::RULE_MIN => 'Min length of {field} field must be {min}',
            self::RULE_MAX => 'Max length of {field} field must be {max}',
            self::RULE_MATCH => 'This field must be the same as {match}',
            self::RULE_UNIQUE => 'Record with this {field} already exists',
        ];
    }

    /**
     * Проверяет, есть ли ошибки после валидации формы
     * @param string $attribute
     * @return false|mixed
     */
    public function hasError(string $attribute)
    {
        return $this->errors[$attribute] ?? false;
    }

    /**
     * Получить первое сообщение об ошибке из имеющихся после валидации формы
     * @param string $attribute
     * @return false|mixed
     */
    public function getFirstError(string $attribute)
    {
        return $this->errors[$attribute][0] ?? false;
    }
}
