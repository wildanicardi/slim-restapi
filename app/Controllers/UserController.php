<?php
namespace App\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;

class UserController{

  protected $container;
  protected $db;
  public function __construct($container) {
    $this->container = $container;
    $this->db = $container->get('db');

  }
  public function getListUser(Request $request, Response $response)
  {
    $sql = "SELECT * FROM user";
    $stmt = $this->db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll();
    if ($result) {
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
        return $response->withJson($data, $data['code']);
  }
  public function getUser(Request $request, Response $response,array $args)
  {
    $id = $args["id"];
    $sql = "SELECT * FROM user WHERE id=:id";
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
  public function createUser(Request $request, Response $response)
  {
    $req = $request->getParsedBody();
        
    $company = "SELECT * FROM company WHERE id=:id";
    $company_stmt = $this->db->prepare($company);
    $company_stmt->execute([":id" =>$req["company_id"]]);
    $company_id = $company_stmt->fetch();
    if (!$company_id) {
        return $response->withJson(["status" => "failed", "message" => "Invalid Company Id"], 404);
    }

    $sql = "INSERT INTO user (first_name, last_name, email,account,company_id) VALUE (:first_name, :last_name, :email,:account,:company_id)";
    $stmt = $this->db->prepare($sql);

    
    $data = [
        ":first_name" => $req["first_name"],
        ":last_name" => $req["last_name"],
        ":email" => $req["email"],
        ":account" => $req["account"],
        ":company_id" => $req["company_id"]
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Create User success"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invalid Create User"], 400);
  }
  public function updateUser(Request $request, Response $response,array $args)
  {
    $id = $args["id"];
    $req = $request->getParsedBody();
    $company = "SELECT * FROM company WHERE id=:id";
    $company_stmt = $this->db->prepare($company);
    $company_stmt->execute([":id" =>$req["company_id"]]);
    $company_id = $company_stmt->fetch();
    if (!$company_id) {
        return $response->withJson(["status" => "failed", "message" => "Invalid Company Id"], 404);
    }
    $sql = "UPDATE user SET first_name=:first_name, last_name=:last_name, email=:email,account=:account,company_id=:company_id WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id,
        ":first_name" => $req["first_name"],
        ":last_name" => $req["last_name"],
        ":email" => $req["email"],
        ":account" => $req["account"],
        ":company_id" => $req["company_id"],
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Update User Success"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invailed Update User"], 400);
  }
  public function deleteUser(Request $request, Response $response,array $args)
  {
    $id = $args["id"];
    $sql = "DELETE FROM user WHERE id=:id";
    $stmt = $this->db->prepare($sql);
    
    $data = [
        ":id" => $id
    ];

    if($stmt->execute($data))
       return $response->withJson(["status" => "success", "message" => "Success Delete User"], 200);
    
    return $response->withJson(["status" => "failed", "message" => "Invalid Delete User"], 400);
  }
}