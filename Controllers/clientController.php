<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './Models/class.client.php';
require_once './Models/class.login.php';


// For adding new client
if(isset($_POST["addClient"]))
{
    $message = '';
    unset($_POST["addClient"]);
    foreach ($_POST as $key => $val)
    {
        if(strlen($val)>0)
            $_POST[$key] = htmlentities($val);
    }
    $insertObj = new Client;
    if(isset($_SESSION["userid"]))
        $_POST["user_id"] = $_SESSION["userid"];
    else
        $_POST["user_id"] = $_SESSION["adminid"];
    if(!$insertObj->insertClient($_POST))
    {
        $message = '<div class="alert alert-danger alert-dismissible" role="alert">Unable to execute operation.</div><br/>';
    }
    unset($insertObj);
    unset($_POST);
    header("Location:allClients.php");
}

//for saving edited info of client
if(isset($_POST["saveClient"]))
{
    $message = '';
    $client_id = base64_decode($_GET["record"]);
    unset($_POST["saveClient"]);
    foreach ($_POST as $key => $val)
    {
        if(strlen($val)>0)
            $_POST[$key] = htmlentities($val);
    }
    $updateObj = new Client;
    if(!$updateObj->updateClient($client_id , $_POST))
    {
        $message = '<div class="alert alert-danger alert-dismissible" role="alert">Unable to update Data</div><br/>';
    }
    unset($updateObj);
    unset($_POST);
    header("Location:defaulters.php");
}

//delete client details
if(isset($_POST["deleteClient"]))
{
    if( (isset($_GET["record"])))
    {
        $clientObj = new Client;
        $client_id = base64_decode($_GET["record"]);
        if($clientObj->deleteClient($client_id))
        { 
            unset($clientObj);
            header("Location: ./defaulters.php"); 
        }
    }else if( (isset($_POST["client_id"],$_POST["deleteClient"])))
    {
        $clientObj = new Client;
        $client_id = base64_decode($_POST["client_id"]);
        if($clientObj->deleteClient($client_id))
        { 
            unset($clientObj);
            header("Location: ./allClients.php"); 
        }
    }
}



$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$first_part = $components[count($components)-1];

if($first_part == 'index.php')
{
    $Obj = new Client; 
    if(isset($_SESSION["userid"]))
    {
        $cur_user_id = $_SESSION["userid"];
        $result = $Obj->getAllClientsData(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0,$cur_user_id);
        if($result == NULL) 
            $totalClients = 0;
        else
            $totalClients = $result->rowCount();
            
        $result1 = $Obj->getAllDefaultersData(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0,$cur_user_id);
        if($result1 == NULL) 
            $totalDefaulters = 0;
        else
            $totalDefaulters = $result1->rowCount();
        unset($Obj);
    }else if(isset($_SESSION["adminid"]))
    {
        $result = $Obj->getAllClientsDataForAdmin(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0);
        if($result == NULL) 
            $totalClients = 0;
        else
            $totalClients = $result->rowCount();
            
        $result1 = $Obj->getAllDefaultersDataForAdmin(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0);
        if($result1 == NULL) 
            $totalDefaulters = 0;
        else
            $totalDefaulters = $result1->rowCount();
        unset($Obj);
    }

}else if($first_part == 'allClients.php')
{
    $Obj = new Client;    
    if(isset($_SESSION["userid"]))
    {
        $cur_user_id = $_SESSION["userid"];
        $result = $Obj->getAllClientsData(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0,$cur_user_id);
        if($result == NULL) 
            $allClientsCount = 0;
        else
            $allClientsCount = $result->rowCount();
    }else if(isset($_SESSION["adminid"]))
    {
        $result = $Obj->getAllClientsDataForAdmin(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0);
        if($result == NULL) 
            $allClientsCount = 0;
        else
            $allClientsCount = $result->rowCount();
    }
    unset($Obj);
}
else if($first_part == 'defaulters.php')
{
    $Obj = new Client;
    if(isset($_SESSION["userid"]))
    {
        $cur_user_id = $_SESSION["userid"];
        $balance = $Obj->getTotalBalanceAmountForUser($cur_user_id);
        $result = $Obj->getAllDefaultersData(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0,$cur_user_id);
        if($result == NULL) 
            $allDefaulters = 0;
        else
            $allDefaulters = $result->rowCount();
    
    }else if(isset($_SESSION["adminid"]))
    {
        $result = $Obj->getAllDefaultersDataForAdmin(array('client_id'), $search=NULL, $limit_start = 0, $limit_end = 0);
        $balance = $Obj->getTotalBalanceAmountForAdmin();
        if($result == NULL) 
            $allDefaulters = 0;
        else
            $allDefaulters = $result->rowCount();
    }
    unset($Obj);
}else if($first_part == 'addClient.php')
{
    $Obj = new Client;
    $result = $Obj->getLatestClientID();
    if( !$result ) 
        $clientCode = "CLI_10001";
    else{
        $exp_code = explode("_",$result);
        $exp_code[1] =  (int) ++$exp_code[1];
        $clientCode = implode("_",$exp_code);
    }
    unset($Obj);
}
else if($first_part == 'editClient.php')
{
    $Obj = new Client;
    $client_id = base64_decode($_GET["record"]);
    $clientName = $Obj->getValueOfClient("name",$client_id);
    $clientCode = $Obj->getValueOfClient("code",$client_id);
    $total = $Obj->getValueOfClient("total",$client_id);
    $amount = $Obj->getValueOfClient("amount_given",$client_id);
    unset($Obj);
}

function getAllClients( $search, $limit_start , $limit_end)
{
    $Obj = new Client;
    if(isset($_SESSION["userid"]))
    {
        $cur_user_id = $_SESSION["userid"];
        $result = $Obj->getAllClientsData(array('client_id','code','name','phone','total','amount_given','created_at'), $search, $limit_start , $limit_end,$cur_user_id);
    }else if(isset($_SESSION["adminid"]))
        $result = $Obj->getAllClientsDataForAdmin(array('client_id','code','name','phone','total','amount_given','created_at'), $search, $limit_start , $limit_end);
    unset($Obj);
    return $result;
}

function getAllDefaulters( $search, $limit_start , $limit_end)
{
    $Obj = new Client;
    if(isset($_SESSION["userid"]))
    {
        $cur_user_id = $_SESSION["userid"];
        $result = $Obj->getAllDefaultersData(array('client_id','code','name','phone','total','amount_given','created_at'), $search, $limit_start , $limit_end,$cur_user_id);
    }else if(isset($_SESSION["adminid"]))
        $result = $Obj->getAllDefaultersDataForAdmin(array('client_id','code','name','phone','total','amount_given','created_at'), $search, $limit_start , $limit_end);
    unset($Obj);
    return $result;
}



?>