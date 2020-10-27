<?php

use Slim\App;

return function (App $app) {
    $container = $app->getContainer();

    // view renderer
    $container['renderer'] = function ($c) {
        $settings = $c->get('settings')['renderer'];
        return new \Slim\Views\PhpRenderer($settings['template_path']);
    };

    // monolog
    $container['logger'] = function ($c) {
        $settings = $c->get('settings')['logger'];
        $logger = new \Monolog\Logger($settings['name']);
        $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
        $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], $settings['level']));
        return $logger;
    };
    // database
    $container['db'] = function ($c){
        $settings = $c->get('settings')['db'];
        $server = $settings['driver'].":host=".$settings['host'].";dbname=".$settings['dbname'];
        $conn = new PDO($server, $settings["user"], $settings["pass"]);  
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    };
    $container["errorHandler"] = function ($container)
    {
        return function ($request,$response,$exception) use ($container)
        {
            return $response->withStatus(500)
                ->withHeader('Content-Type','application/json')
                ->write(json_encode(
                    array(
                        "success"=>false,
                        "error"=>"INTERNAL_ERROR",
                        "message"=>"something went wrong internally",
                        "status_code"=>"500",
                        'trace'=>$exception->getTraceAsString()
                    ),
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                ));
        };
    };


    $container["notFoundHandler"] = function ($container)
    {
        return function ($request,$response,$exception) use ($container)
        {
            return $response->withStatus(404)
                ->withHeader('Content-Type','application/json')
                ->write(json_encode(
                    array(
                        "success"=>false,
                        "error"=>"NOT_FOUND",
                        "message"=>"EndPoint was not found",
                        "status_code"=>"404",
                    ),
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                ));
        };
    };



    $container["notAllowedHandler"] = function ($container)
    {
        return function ($request,$response,$exception) use ($container)
        {
            return $response->withStatus(405)
                ->withHeader('Content-Type','application/json')
                ->write(json_encode(
                    array(
                        "success"=>false,
                        "error"=>"NOT_ALLOWED",
                        "message"=>"this request is not allowed on this route",
                        "status_code"=>"405",
                    ),
                    JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT
                ));
        };
    };




    // $container['phpErrorHandler'] = function($container)
    // {
    // return $container["errorHandler"];
    // };
};
