<?php

use watrlabs\router\routing;
use watrlabs\authentication;
use watrlabs\users\getuserinfo;
use watrlabs\logging\discord;
use watrlabs\sitefunctions;

require_once '../init.php';

try {

    try {

        global $db;
        global $router;

        if(isset($_ENV["APP_DEBUG"])){
            if($_ENV["APP_DEBUG"] !== "true"){
                error_reporting(0);
            }
        }

        ob_start();

        $auth = new authentication();
        $router = new routing();

        $routers = [
            "web",
            "auth",
            "authapi"
        ];

        foreach ($routers as $r) {
            $router->addrouter($r);
        }

        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $uri = strtolower($uri);
        $method = $_SERVER['REQUEST_METHOD'];
        $response = $router->dispatch($uri, $method);

        ob_end_flush();

    } catch(PDOException $e){
        handle_error($e);
    }

    
} catch(ErrorException $e){
    handle_error($e);
}

function handle_error($e){
    $log = new discord();
    try {
        $log->internal_log($e, "Site Error!");
        ob_clean();
        file_put_contents("../storage/errorlog.log", $e . "\n\n", FILE_APPEND);
        http_response_code(500);
        global $twig;
        echo $twig->render('status_codes/500.twig');
    } catch(ErrorException $e){
        $log->internal_log($e, "Site Error!");
        ob_clean();
        http_response_code(500);
        echo $e;
        file_put_contents("../storage/errorlog.log", $e . "\n\n", FILE_APPEND);
        die("Couldn't process your request. Please try again later.");
    }
}

// aaaaaaaaaaaaaaaa my brain hurts 