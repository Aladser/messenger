<?php

namespace Aladser\Core;

class Route
{
    public static function start()
    {
        session_start();

        // контроллер и действие по умолчанию
        $routes = mb_substr($_SERVER['REDIRECT_URL'], 1);
        $controller_name = !empty($routes) ? ucfirst($routes) : 'main';
        $action_name = 'Index';

        // преобразовать url в название класса
        $controller_name = str_replace('-', ' ', $controller_name);
        $controller_name = ucwords($controller_name);
        $controller_name = str_replace(' ', '', $controller_name);

        // авторизация сохраняется в куки и сессии. Если авторизация есть, то messenger.local -> /chats
        if ($controller_name === 'Main'
            && (isset($_SESSION['auth']) || isset($_COOKIE['auth']))
            && !isset($_GET['logout'])
        ) {
            $controller_name = 'Chats';
        }

        // редирект /chats или /profile без авторизации -> messenger.local
        if (($controller_name === 'Chats'|| $controller_name === 'Profile')
            && !(isset($_SESSION['auth']) || isset($_COOKIE['auth']))
        ) {
            $controller_name = 'Main';
        }

        //echo $controller_name;

        // добавляем префиксы
        $model_name = $controller_name.'Model';
        $controller_name = $controller_name.'Controller';
        $action_name = 'action'.$action_name;

        // подцепляем файл с классом модели (файла модели может и не быть)
        $model_path = "application/Models/$model_name.php";
        if (file_exists($model_path)) {
            include $model_path;
        }

        // подцепляем файл с классом контроллера
        $controller_path = "application/Controllers/$controller_name.php";
        if (file_exists($controller_path)) {
            include $controller_path;
        } else {
            Route::errorPage404();
        }

        // создаем модель, если существует
        $model = null;
        if (file_exists($model_path)) {
            $model_name = "\\Aladser\\Models\\$model_name";
            $model = new $model_name(new ConfigClass());
        }

        // создаем контроллер
        $controller_name = "\\Aladser\\Controllers\\$controller_name";
        $controller = file_exists($model_path) ? new $controller_name($model) : new $controller_name();

        $action = $action_name;
        if (method_exists($controller, $action)) {
            // вызываем действие контроллера
            $controller->$action();
        } else {
            Route::errorPage404();
        }
    }
    
    public static function errorPage404()
    {
        $host = "https://'{$_SERVER['HTTP_HOST']}/";
        header('HTTP/1.1 404 Not Found');
        header("Status: 404 Not Found");
        header('Location:'.$host.'404');
    }
}