<?php

use Slim\App;
use Slim\Http\Request;
use Slim\Http\Response;

return function (App $app) {
    $container = $app->getContainer();

    $app->get('/[{name}]', function (Request $request, Response $response, array $args) use ($container) {
        // Sample log message
        $container->get('logger')->info("Slim-Skeleton '/' route");

        // Render index view
        return $container->get('renderer')->render($response, 'index.phtml', $args);
    });
    $app->group('/api',function ()use($app,$container)
    {
        $app->get('/users', function (Request $request, Response $response){
            $sql = "SELECT * FROM user";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if ($result) {
                $data = array(
                    'code' => 200,
                    'message' => 'success',
                    'data' => $stmt->fetchAll());
            }else{
                $data = array(
                    'code' => 404,
                    'message' => 'data not found',
                    'data' => null);
            }
            return $response->withJson($data, $data['code']);
        });
        $app->get('/user/{id}',function (Request $request, Response $response, array $args)use ($container)
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
                        'data' => $stmt->fetchAll());
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
            $container->get('logger')->info("get user id");
            return $response->withJson($data, $data['code']);
        });
        $app->get('/company', function (Request $request, Response $response){
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
        });
        $app->get('/company/{id}',function (Request $request, Response $response, array $args)use ($container)
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
                        'data' => $stmt->fetchAll());
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
            $container->get('logger')->info("get user id");
            return $response->withJson($data, $data['code']);
        });
        $app->get('/budget', function (Request $request, Response $response){
            $sql = "SELECT * FROM company_budget";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll();
            if ($result) {
                $data = [
                    'code' => 200,
                    'message' => 'success',
                    'data' => $stmt->fetchAll()
                ];
            }else{
                $data = [
                    'code' => 404,
                    'message' => 'data not found',
                    'data' => null
                ];
            }
            return $response->withJson($data, $data['code']);
        });
        $app->get('/budget/{id}',function (Request $request, Response $response, array $args)use ($container)
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
                        'data' => $stmt->fetchAll());
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
            $container->get('logger')->info("get user id");
            return $response->withJson($data, $data['code']);
        });
        $app->post("/user", function (Request $request, Response $response){

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
        });
        $app->post("/company", function (Request $request, Response $response){

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
        });
        $app->put("/user/{id}", function (Request $request, Response $response, $args){
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
        });
        $app->put("/company/{id}", function (Request $request, Response $response, $args){
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
        });
        $app->delete("/user/{id}", function (Request $request, Response $response, $args){
            $id = $args["id"];
            $sql = "DELETE FROM user WHERE id=:id";
            $stmt = $this->db->prepare($sql);
            
            $data = [
                ":id" => $id
            ];
        
            if($stmt->execute($data))
               return $response->withJson(["status" => "success", "message" => "Success Delete User"], 200);
            
            return $response->withJson(["status" => "failed", "message" => "Invalid Delete User"], 400);
        });
        $app->delete("/company/{id}", function (Request $request, Response $response, $args){
            $id = $args["id"];
            $sql = "DELETE FROM company WHERE id=:id";
            $stmt = $this->db->prepare($sql);
            
            $data = [
                ":id" => $id
            ];
        
            if($stmt->execute($data))
               return $response->withJson(["status" => "success", "message" => "Success Delete Company"], 200);
            
            return $response->withJson(["status" => "failed", "message" => "Invalid Delete Company"], 400);
        });
    });
   
  
};