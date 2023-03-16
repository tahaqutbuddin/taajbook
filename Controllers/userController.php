<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once './Models/class.user.php';
require_once './Models/class.login.php';


// For Adding new users
if(isset($_POST["insertUser"]))
{
    $message = '';
    unset($_POST["terms"] , $_POST["insertUser"]);
    foreach ($_POST as $key => $val)
    {
        if(strlen($val)>0)
            if($key == "email")
                $_POST[$key] = htmlentities(filter_var($val, FILTER_SANITIZE_EMAIL));
            else
                $_POST[$key] = htmlentities($val);
    }
    $insertUserObj = new User;
    $result = $insertUserObj->insertUser($_POST);
    if(is_int($result))
    {
        unset($insertUserObj);
        $message = '<div class="alert alert-danger alert-dismissible>This user already exists.</div>';
    }else
    {
        unset($insertUserObj);
        unset($_POST);
        header("Location: ./login.php");
    }
}

if(isset($_POST["blockUser"]))
{
    $userObj = new User;
    $id = base64_decode($_POST["user_id"]);
    echo $id;
    if($userObj->deactivateAccount($id))
    { 
        unset($userObj);
        unset($_POST);
        header("Location: ./allUsers.php");
    }
}

if(isset($_POST["unblockUser"]))
{
    $userObj = new User;
    $id = base64_decode($_POST["user_id"]);
    echo $id;
    if($userObj->activateAccount($id))
    { 
        unset($userObj);
        unset($_POST);
        header("Location: ./allUsers.php");
    }
}

if(isset($_POST["deleteUser"]))
{
    $userObj = new User;
    if (isset($_POST["user_id"]))
        $id = base64_decode($_POST["user_id"]);
    else 
        $id = base64_decode($_GET["record"]);

    if($userObj->deleteAccount($id))
    { 
        unset($userObj);
        unset($_POST);
        header("Location: ./allUsers.php");
    }
}


//for saving edited info
if(isset($_POST["saveUser"]))
{
    $message = '';
    $id = base64_decode($_GET["record"]);
    unset($_POST["saveUser"]);
    foreach ($_POST as $key => $val)
    {
        if(strlen($val)>0)
            $_POST[$key] = htmlentities($val);
    }
    $updateObj = new User;
    if(!$updateObj->updateUser($id , $_POST))
    {
        $message = '<div class="alert alert-danger alert-dismissible" role="alert">Unable to update Data</div><br/>';
    }
    unset($updateObj);
    unset($_POST);
    header("Location:".$_SERVER["HTTP_REFERER"]);
}




$directoryURI = $_SERVER['REQUEST_URI'];
$path = parse_url($directoryURI, PHP_URL_PATH);
$components = explode('/', $path);
$first_part = $components[count($components)-1];



if($first_part == 'allUsers.php')
{
    $userObj = new User;
    $result = $userObj->getAllUsersData(array('user_id'), $search=NULL, $limit_start = 0, $limit_end = 0);
    $allUsersCount = $result->rowCount();
    unset($userObj);
}else if($first_part == 'index.php')
{
    $userObj = new User;
    if(isset($_SESSION["userid"]))
        $id = $_SESSION["userid"];
    else if (isset($_SESSION["adminid"]))
        $id = $_SESSION["adminid"];

    $result1 = $userObj->getSingleUserData($id,array('username',"fullname"));
    $_SESSION["username"] = $result1[0]["username"];
    $_SESSION["fullname"] = $result1[0]["fullname"];
    unset($userObj);
}else if($first_part == "editUser.php")
{
    $userObj = new User;
    if (isset($_GET["record"]))
        $id = base64_decode($_GET["record"]);

    $result1 = $userObj->getSingleUserData($id,array('username',"fullname","email"));
    $username = $result1[0]["username"];
    $fullname = $result1[0]["fullname"];
    $email = $result1[0]["email"];
    unset($userObj);
}

function getAllUsers( $search, $limit_start , $limit_end)
{
    $Obj = new User;
    $result = $Obj->getAllUsersData(array('user_id','email','fullname','username','is_active','created_at'), $search, $limit_start , $limit_end);
    unset($Obj);
    return $result;
}

?>