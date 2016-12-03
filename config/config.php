<?php
    /* TENHLE SOUBOR NESMÍ BÝT VE SLOŽCE CO JE VIDĚT NA SERVRU, PROTO JE TADY*/
    define('DB_HOST', '127.0.0.1');
    define('DB_DATABASE', 'konference');
    define('DB_USER', 'root');
    define('DB_PASS', 'heslo');
    define('DB_CHARSET', 'utf8');

    ini_set("session.cookie_httponly", 1);

    require_once ROOT . "models" . DIRECTORY_SEPARATOR . "Model.php";