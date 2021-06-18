<?php
include "../dbhelper.php";
include "../credentialhelper.php";
$key = $_POST['key'];
$dbhelper = new dbhelper("root","moj","","localhost");
session_start();

if(isset($key)){
    switch ($key){
        case "login":
            $loginData = array();
            $email = $_POST['email'];
            $password = $_POST['password'];
            $credentials = new credentialhelper($dbhelper->DBConn());
            array_push($loginData,$credentials->getCredential("admin","username",$email),
                $credentials->getCredential("admin","password",$password));
            $userid = $dbhelper->getCurrentData("admin",$email,"username","id");
            if($credentials->checkLogin("admin","username",$email) && $credentials->checkLogin("admin","password",$password)) {
                $response[] = array(
                    "error"=>false,
                    "message"=>"User logged in!",
                    "userid"=>$userid[0]['id'],
                    "user"=>$loginData[0]['username']
                );
                $credentials->setUserid($userid[0]['id']);
                $credentials->createSession();
            }else{
                $response[] = array(
                    "error"=>true,
                    "message"=>"Something went Wrong",
                    "user"=>null,
                );
            }
            $data = array(
                "data"=>$response
            );
            echo json_encode($data);
            break;

    }

}
