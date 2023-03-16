<?php
require_once 'class.database.php';
class User
{

    public function insertUser($arr = array())
    {
        $c_id = $this->checkUserExists('username',$arr["username"]); 
        $e_id = $this->checkUserExists('email',$arr["email"]);
        if(!$c_id && !$e_id)
        {
            $hash_password = hash('sha512' , $arr["username"].'#$@cdh#$'.$arr["password"]);
            $arr["password"] = $hash_password;
            $conn = new Database;
            $conn = $conn->connect();
            $arr_keys =  array_keys($arr) ;
            if (count($arr) > 0) {
                $columns = '`' . implode('`,`', $arr_keys).'`';
                $values = ":" . implode(",:", $arr_keys);
            }
            $query = "INSERT INTO `users`($columns) VALUES ($values)";
            $sql = $conn->prepare($query);
            foreach($arr as $key => $val)
            {
                $sql->bindValue(":". $key , $val);
            }
            $sql->execute();
            if($sql->rowCount() > 0) { return $conn->lastInsertId();}else{ return false;}
        }else
        {
            return $c_id;
        }
    }

    public function checkUserExists($col , $val)
    {
        $conn = new Database;
        $conn = $conn->connect();
        $lower_val = strtolower($val);
        $sql = "select * from `users` where `$col` = :cn and `is_active`=1 ;";
        $query = $conn->prepare($sql);
        $query->bindParam(':cn',$lower_val,PDO::PARAM_STR);
        $query->execute();
        if($query->rowCount() > 0)
        {
            $conn = NULL;
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                return $row["user_id"];
            }
        }else
        {
            return false;
        }
    }

    public function getSingleUserData($userid,$columns=array())
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "SELECT ".  "`".implode('`,`',$columns)."`"  ." FROM `users` where users.user_id = :u and `users`.`is_active` = 1  limit 1";
        $sql = $conn->prepare($query);
        $sql->bindParam(':u',$userid,PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            $obj->closeConnection();
            return $result;
        }
    }

    public function deactivateAccount($userid)
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "UPDATE `users` SET `is_active`=0 where users.user_id = :u and `users`.`is_active` = 1 ";
        $sql = $conn->prepare($query);
        $sql->bindParam(':u',$userid,PDO::PARAM_INT);
        $result = $sql->execute();
        if($result)
        {
            return true;
        }else
        {
            return false;
        }
    }  

    public function activateAccount($userid)
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "UPDATE `users` SET `is_active`=1 where users.user_id = :u and `users`.`is_active` = 0 ";
        $sql = $conn->prepare($query);
        $sql->bindParam(':u',$userid,PDO::PARAM_INT);
        $result = $sql->execute();
        if($result)
        {
            return true;
        }else
        {
            return false;
        }
    } 
    
    public function deleteAccount($userid)
    {
        $obj = new Database;
        $conn = $obj->connect();

        $query = "DELETE from `clients` where clients.user_id = :u ;";
        $sql = $conn->prepare($query);
        $sql->bindParam(':u',$userid,PDO::PARAM_INT);
        $result = $sql->execute();
        if($result)
        {
            $query1 = "DELETE from `users` where users.user_id = :u ;";
            $sql1 = $conn->prepare($query1);
            $sql1->bindParam(':u',$userid,PDO::PARAM_INT);
            $result1 = $sql1->execute();
            if($result1)
            {
                return true;
            }else
            {
                return false;
            }
        }else
        {
            return false;
        }
    } 

    public function getAllUsersData($columns=array() , $search, $limit_start , $limit_end)
    {
        $obj = new Database;
        $conn = $obj->connect();
        if($search != NULL){
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`".implode('`,`',$columns)."`"  ."FROM `users` where ( username LIKE '%".str_replace(' ', '%', $search)."%'  or fullname LIKE '%".str_replace(' ', '%', $search)."%' or email LIKE '%".str_replace(' ', '%', $search)."%' ) and role!='admin' order by user_id ASC;";
            else
                $query = "SELECT ".  "`".implode('`,`',$columns)."`"  ."FROM `users` where ( username LIKE '%".str_replace(' ', '%', $search)."%'  or fullname LIKE '%".str_replace(' ', '%', $search)."%' or email LIKE '%".str_replace(' ', '%', $search)."%' ) and role!='admin' order by user_id ASC limit $limit_start,$limit_end; ";
        }else{
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`".implode('`,`',$columns)."`"  ."FROM `users` where role!='admin' order by user_id ASC ";
            else
                $query = "SELECT ".  "`".implode('`,`',$columns)."`"  ."FROM `users` where role!='admin' order by user_id ASC limit $limit_start,$limit_end;";
        }
        $sql = $conn->query($query);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql;
            $obj->closeConnection();
            return $result;
        }
    }

    public function updateUser($id , $arr = array())
    {
        try{
            $obj = new Database;
            $conn  = $obj->connect();
            $query = "UPDATE `users` SET ";
            foreach($arr as $key=>$val)
            {
                $query .= " `$key` = :$key ,";
            }
            $query = rtrim($query, ",");
            $query .= 'where user_id = :u';
            $sql = $conn->prepare($query);
            foreach($arr as $key=>$val)
            {
                if(is_int($val))
                {
                    $sql->bindParam(':'.$key,$arr[$key],PDO::PARAM_INT);
                }else if(is_string($val))
                {
                    $sql->bindParam(':'.$key,$arr[$key],PDO::PARAM_STR);
                }
            }
            $sql->bindParam(':u',$id,PDO::PARAM_INT);
            $res = $sql->execute();
            if ($res) {
                return true;
            }else
            {
                return false;
            }   
        }catch(PDOException $ex)
        {
            echo "Error: ".$ex->getMessage();
        }
    }

}


?>