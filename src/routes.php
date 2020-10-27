<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;
use Illuminate\Support\Facades\Date;

return function (App $app) {
    $container = $app->getContainer();

    // $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
    //     // Sample log message
    //     $container->get('logger')->info("Slim-Skeleton '/' route");

    //     // Render index view
    //     return $container->get('renderer')->render($response, 'index.phtml', $args);
    // });
    $app->group('/api',function ()use($app,$container)
    {
        $app->get('/users', "UserController:getListUser");
        $app->get('/user/{id}',"UserController:getUser");
        $app->post("/user", "UserController:createUser");
        $app->put("/user/{id}", "UserController:updateUser");
        $app->delete("/user/{id}", "UserController:deleteUser");

        $app->get('/company', "CompanyController:getListCompany");
        $app->get('/company/{id}',"CompanyController:getCompany");
        $app->post("/company", "CompanyController:createCompany");     
        $app->put("/company/{id}", "CompanyController:updateCompany");  
        $app->delete("/company/{id}", "CompanyController:deleteCompany");

        $app->get('/budget', "BudgetController:getListBudget");
        $app->post('/budget', "BudgetController:createBudget");
        $app->get('/budget/{id}',"BudgetController:getBudget");
        
       
        $app->post("/close","TransactionController:close");
        $app->post('/reimburse',"TransactionController:reimburse");
        $app->post('/disburse',"TransactionController:disburse");
        $app->get('/log-transaction',"TransactionController:getLogTransaction");
            
    });
   
  
};