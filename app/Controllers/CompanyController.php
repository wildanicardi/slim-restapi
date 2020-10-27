<?php

namespace App\Controllers;
use Slim\Http\Request;
use Slim\Http\Response;

class CompanyController{

  protected $container;
  protected $db;
  public function __construct($container) {
    $this->container = $container;
    $this->db = $container->get('db');
  }

  public function getListCompany(Request $request, Response $response)
  {
    $sql = "SELECT * FROM company";
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
  public function getCompany(Request $request, Response $response,array $args)
  {
    $id = $args["id"];
           $sql = "SELECT * FROM company WHERE id=:id";
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
  public function createCompany(Request $request, Response $response)
  {
    
    $req = $request->getParsedBody();
        
    $sql = "INSERT INTO company ( name, address) VALUE (:name,:address)";
    $stmt = $this->db->prepare($sql);

    $data = [
        ":name" => $req["name"],
        ":address" => $req["address"],
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Create Company success"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invalid Create Company"], 400);
  }
  public function updateCompany(Request $request, Response $response, $args)
  {
    $id = $args["id"];
    $req = $request->getParsedBody();
    $sql = "UPDATE company SET name=:name, address=:address WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
      ":id" => $id,
      ":name" => $req["name"],
      ":address" => $req["address"],
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Update Company Success"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invailed Update Company"], 400);
  }
  public function deleteCompany(Request $request, Response $response, $args)
  {
    $id = $args["id"];
    $sql = "DELETE FROM company WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Success Delete Company"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invalid Delete Company"], 400);
  }
}