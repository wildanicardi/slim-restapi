<?php
return function($container)
{
    $container["UserController"] = function($c)
    {
        return new \App\Controllers\UserController($c);
    };
    $container["CompanyController"] = function($c)
    {
        return new \App\Controllers\CompanyController($c);
    };
    $container["BudgetController"] = function($c)
    {
        return new \App\Controllers\BudgetController($c);
    };
    $container["TransactionController"] = function($c)
    {
        return new \App\Controllers\TransactionController($c);
    };

};