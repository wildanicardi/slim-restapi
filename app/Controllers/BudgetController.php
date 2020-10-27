<?php

namespace App\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;

class BudgetController{

  protected $container;
  protected $db;
  public function __construct($container) {
    $this->container = $container;
    $this->db = $container->get('db');
  }

  public function getListBudget(Request $request, Response $response)
  {
    $sql = "SELECT * FROM company_budget";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result) {
        $data = [
            'code' => 200,
            'message' => 'success',
            'data' => $result
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
  public function getBudget(Request $request, Response $response,array $args)
  {
    $id = $args["id"];
    $sql = "SELECT * FROM company_budget WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    $stmt->execute([":id" => $id]);
    $result = $stmt->fetch();
    if ($result) {
        if ($stmt->rowCount()) {
            $data = array(
                'code' => 200,
                'message' => 'success',
                'data' => $result);
        }else{
            $data = array(
                'code' => 404,
                'message' => 'data not found',
                'data' => null);
        }
    }else{
        $data = array(
            'code' => 500,
            'message' => 'Error',
            'data' => null);
    }
    return $response->withJson($data, $data['code']);
  }
  public function createBudget(Request $request, Response $response)
  {
    $req = $request->getParsedBody();
        
    $sql = "INSERT INTO company_budget ( company_id, amount) VALUE (:company_id,:amount)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":company_id" => $req["company_id"],
        ":amount" => $req["amount"],
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Create Budget success"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invalid Create Budget"], 400);
  }
}