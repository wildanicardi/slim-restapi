<?php

namespace App\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;

class TransactionController{

  protected $container;
  protected $db;
  public function __construct($container) {
    $this->container = $container;
    $this->db = $container->get('db');
  }

  public function close(Request $request, Response $response, $args)
  {
    $req = $request->getParsedBody();
            
    //get Data Company_budget
    $sql_companyAmount  =  "SELECT amount FROM company_budget WHERE company_id=:company_id";
    $stmt_amount = $this->db->prepare($sql_companyAmount);
    $stmt_amount->execute([":company_id" => $req['company_id']]);
    $result = $stmt_amount->fetch();

    // create transaction
    $sql_transaction = "INSERT INTO transaction(type,user_id,amount) VALUE(:type,:user_id,:amount)";
    $stmt_transaction = $this->db->prepare($sql_transaction);
    $data_transaction = [
        ":type" => "S",
        ":user_id" => $req["user_id"], 
        ":amount" => $req["amount"]
    ];
    if($stmt_transaction->execute($data_transaction)){
        //update company budget
        $sql_companyBudget = "UPDATE  company_budget SET amount = :amount WHERE company_id=:company_id";
        $stmt_updateBudget = $this->db->prepare($sql_companyBudget);
        $total_amount = $result['amount'] + $req["amount"];
        $data_budget = [
            ":company_id" => $req['company_id'],
            ":amount" => $total_amount,
        ];
        $stmt_updateBudget->execute($data_budget);
        return $response->withJson(["status" => "success", "message" => "Transaction Success"], 200);
    }else {
        return $response->withJson(["status" => "failed", "message" => "Invalid Create Transaction Close"], 400);
    }
  }
  public function reimburse(Request $request, Response $response, $args)
  {
    $req = $request->getParsedBody();
            
    //get Data Company_budget
    $sql_companyAmount  =  "SELECT amount FROM company_budget WHERE company_id=:company_id";
    $stmt_amount = $this->db->prepare($sql_companyAmount);
    $stmt_amount->execute([":company_id" => $req['company_id']]);
    $result = $stmt_amount->fetch();

    
    // create transaction
    $sql_transaction = "INSERT INTO transaction(type,user_id,amount) VALUE(:type,:user_id,:amount)";
    $stmt_transaction = $this->db->prepare($sql_transaction);
    $data_transaction = [
        ":type" => "R",
        ":user_id" => $req["user_id"], 
        ":amount" => $req["amount"]
    ];
    if ($result['amount'] <= $req["amount"]) {
        return $response->withJson(["status" => "failed", "message" => "the amount of your budget is less"], 400);
    }
    if($stmt_transaction->execute($data_transaction)){
        //update company budget
        $sql_companyBudget = "UPDATE  company_budget SET amount = :amount WHERE company_id=:company_id";
        $stmt_updateBudget = $this->db->prepare($sql_companyBudget);
        $total_amount = $result['amount'] - $req["amount"];
        $data_budget = [
            ":company_id" => $req['company_id'],
            ":amount" => $total_amount,
        ];
        $stmt_updateBudget->execute($data_budget);
        return $response->withJson(["status" => "success", "message" => "Transaction Success"], 200);
    }else {
        return $response->withJson(["status" => "failed", "message" => "Invalid Create Transaction Close"], 400);
    }
  }
  public function disburse(Request $request, Response $response, $args)
  {
    $req = $request->getParsedBody();
            
    //get Data Company_budget
    $sql_companyAmount  =  "SELECT amount FROM company_budget WHERE company_id=:company_id";
    $stmt_amount = $this->db->prepare($sql_companyAmount);
    $stmt_amount->execute([":company_id" => $req['company_id']]);
    $result = $stmt_amount->fetch();

    
    // create transaction
    $sql_transaction = "INSERT INTO transaction(type,user_id,amount) VALUE(:type,:user_id,:amount)";
    $stmt_transaction = $this->db->prepare($sql_transaction);
    $data_transaction = [
        ":type" => "C",
        ":user_id" => $req["user_id"], 
        ":amount" => $req["amount"]
    ];
    if ($result['amount'] <= $req["amount"]) {
        return $response->withJson(["status" => "failed", "message" => "the amount of your budget is less"], 400);
    }
    if($stmt_transaction->execute($data_transaction)){
        //update company budget
        $sql_companyBudget = "UPDATE  company_budget SET amount = :amount WHERE company_id=:company_id";
        $stmt_updateBudget = $this->db->prepare($sql_companyBudget);
        $total_amount = $result['amount'] - $req["amount"];
        $data_budget = [
            ":company_id" => $req['company_id'],
            ":amount" => $total_amount,
        ];
        $stmt_updateBudget->execute($data_budget);
        return $response->withJson(["status" => "success", "message" => "Transaction Success"], 200);
    }else {
        return $response->withJson(["status" => "failed", "message" => "Invalid Create Transaction Close"], 400);
    }
  }
  public function getLogTransaction(Request $request, Response $response, $args)
  {
    $sql = "SELECT  user.id ,CONCAT(user.first_name, ' ', user.last_name) AS name, transaction.type, transaction.amount,transaction.date, company.name AS company_name,user.account,company_budget.amount AS remaining_amount
    FROM (((user
    INNER JOIN transaction ON user.id = transaction.user_id)
    INNER JOIN company ON user.company_id = company.id)
    INNER JOIN company_budget ON user.company_id = company_budget.company_id)
    ";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();

    if ($result) {
        $result_response = array_map(function ($item){
            switch ($item["type"]) {
                case 'S':
                    $type = "Close";
                    break;
                case 'R':
                    $type = "Reimburse";
                    break;
                case 'C':
                    $type = "Disburse";
                    break;
                default:
                    $type = "";
                    break;
            }
            $data = [
                "user_id" => $item["id"],
                "Username" => $item["name"],
                "User Account" => $item["account"],
                "Company Name" => $item["company_name"],
                "Transaction Type" => $type,
                "Transaction Amount" => $item["amount"],
                "Remaining Amount" => $item["remaining_amount"],
                "Transaction Date" => $item["date"],
            ];
            return $data;
        },$result);
        $data = [
            'code' => 200,
            'message' => 'success',
            'data' => $result_response
        ];
    }else{
        $data = [
            'code' => 404,
            'message' => 'data not found',
            'data' => null
        ];
    }
    return $response->withJson($data, $data['code']);

  }
}