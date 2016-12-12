<?php
    define('ROOT',dirname(__FILE__). DIRECTORY_SEPARATOR .'..' . DIRECTORY_SEPARATOR);

    // nacteni konfigu
    require_once ROOT . 'config/config.php';

    session_start();

    // nacte zavislosti z vendor
    require_once ROOT . 'vendor/autoload.php';

    // nactec tridu rodice */
    require ROOT . "controllers" . DIRECTORY_SEPARATOR . 'Controller.php';

    $page = @$_REQUEST['page'];
    $action = @$_REQUEST['action'];
    $param = @$_REQUEST['param'];

    if(empty($page) && empty($action)) {
        // nacte home
        require ROOT. "controllers" . DIRECTORY_SEPARATOR . 'HomeController.php';
        // vyuzviva namespace co jsem nastavil v te tride samotne
        $controller = new Sp\Controlers\HomeController();
        $controller->index();
    } elseif (file_exists(ROOT. "controllers" . DIRECTORY_SEPARATOR . $page . 'Controller.php')) {
        // kdyz existuje vytvor instanci, zde se trida vola uz pres namespace
        require ROOT. "controllers" . DIRECTORY_SEPARATOR . $page . 'Controller.php';
        $controller_name = "\\Sp\\Controlers\\" . $page . 'Controller';
        $controller = new $controller_name();
        // exituje metoda ?
        if (method_exists($controller, $action)) {
            if (empty($param)) {
                // action = stranka kterou chci
                $controller->{$action}();
            } else {
                call_user_func_array([$controller, $action], [$param]);
            }
        }
        else {
            require ROOT. "controllers" . DIRECTORY_SEPARATOR . 'ErrorController.php';
            $c = "\\Sp\\Controlers\\" . "ErrorController";
            $controller = new $c();
            $controller->error404();
        }
    } else {
        require ROOT. "controllers" . DIRECTORY_SEPARATOR . 'ErrorController.php';
        $c = "\\Sp\\Controlers\\" . "ErrorController";
        $controller = new $c();
        $controller->error404();
    }
