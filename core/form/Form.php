<?php

namespace app\core\form;

use app\core\Model;

class Form
{
    public static function begin(string $action, string $method)
    {
        echo sprintf('<form action="%s" method="%s">', $action, $method);
        return new Form();
    }

    public static function end()
    {
        echo '</form>';
    }

    public static function field(Model $model, string $attribute)
    {
        return new Field($model, $attribute);
    }
}