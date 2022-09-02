<?php 

namespace app\core;

class View {

    public string $title = '';

    /**
     * Рендер шаблона с данными из view
     * @param string $view
     * @param array $params
     * @return array|false|string|string[]
     */
    public function renderView(string $view, array $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
        // return Application::$app->view->renderView($view, $params);
    }

    /**
     * Получение данных из шаблона для интеграции в view
     * @param string $view Название view-файла с данными
     * @return false|string
     */
    protected function renderOnlyView(string $view, ?array $params)
    {
        foreach ($params as $key => $value) {
            $$key = $value;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/$view.php";
        return ob_get_clean();
    }

    /**
     * Получение основного шаблона для вставки туда данных для рендера страницы
     * @return false|string
     */
    private function layoutContent()
    {
        $layout = Application::$app->layout;
        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }
        ob_start();
        include_once Application::$ROOT_DIR . "/views/layouts/$layout.php";
        return ob_get_clean();
    }

    /**
     * !! Не используется
     * @param string $viewContent
     * @return array|false|string|string[]
     */
    public function renderContent(string $viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{content}}', $viewContent, $layoutContent);
    }
}