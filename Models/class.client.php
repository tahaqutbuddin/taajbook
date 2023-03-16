<?php
require_once 'class.database.php';
class Client
{

    public function insertClient($arr = array())
    {
        try
        {
            $conn = new Database;
            $conn = $conn->connect();
            $arr_keys =  array_keys($arr) ;
            if (count($arr) > 0) {
                $columns = '`' . implode('`,`', $arr_keys).'`';
                $values = ":" . implode(",:", $arr_keys);
            }
            $query = "INSERT INTO `clients`($columns) VALUES ($values)";
            $sql = $conn->prepare($query);
            foreach($arr as $key => $val)
            {
                $sql->bindValue(":". $key , $val);
            }
            $sql->execute();
            if($sql->rowCount() > 0) { return $conn->lastInsertId();}else{ return false;}
        }catch(PDOException $ex)
        {
            return false;
        }  
    }

    public function checkUserExists($col , $val)
    {
        $conn = new Database;
        $conn = $conn->connect();
        $lower_val = strtolower($val);
        $sql = "select * from `clients` where `$col` = :cn ";
        $query = $conn->prepare($sql);
        $query->bindParam(':cn',$lower_val,PDO::PARAM_STR);
        $query->execute();
        if($query->rowCount() > 0)
        {
            $conn = NULL;
            $result = $query->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                return $row["client_id"];
            }
        }else
        {
            return false;
        }
    }

    public function getAllClientsData($columns=array() , $search, $limit_start , $limit_end,$user_id)
    {
        $obj = new Database;
        $conn = $obj->connect();
        if($user_id != 0)
        {
            if($search != NULL){
                if ( ($limit_start==0) && ($limit_end==0) )
                    $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%'  ) and users.user_id=:user order by clients.created_at desc;";
                else
                    $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`" ."FROM `clients` inner join `users` on users.user_id = clients.user_id where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%' ) and users.user_id=:user order by clients.created_at desc;";
                
            }else{
                if ( ($limit_start==0) && ($limit_end==0) )
                    $query = "SELECT ".   "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id where users.user_id = :user order by clients.created_at desc;";
                else
                    $query = "SELECT ".   "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id where users.user_id = :user order by clients.created_at desc;";
                
            }
            $sql = $conn->prepare($query);
            $sql->bindParam(":user",$user_id,PDO::PARAM_INT);
            $sql->execute();
            if ($sql->rowCount() > 0) {
                $result = $sql;
                $obj->closeConnection();
                return $result;
            }
        }
    }

    public function getAllClientsDataForAdmin($columns=array() , $search, $limit_start , $limit_end)
    {
        $obj = new Database;
        $conn = $obj->connect();
        if($search != NULL){
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%'  ) order by clients.created_at desc;";
            else
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`" ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%' )  order by clients.created_at desc;";
            
        }else{
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".   "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  order by clients.created_at desc;";
            else
                $query = "SELECT ".   "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  order by clients.created_at desc;";
        }
        $sql = $conn->query($query);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql;
            $obj->closeConnection();
            return $result;
        }
    }

    public function getAllDefaultersData($columns=array() , $search, $limit_start , $limit_end,$user_id)
    {
        $obj = new Database;
        $conn = $obj->connect();
        if($search != NULL){
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%'  ) and users.user_id=:user and total!=amount_given order by clients.created_at desc;";
            else
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%' ) and users.user_id=:user and total!=amount_given order by clients.created_at desc;";
           
        }else{
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where users.user_id=:user and total!=amount_given order by clients.created_at desc;";
            else
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where users.user_id=:user and total!=amount_given order by clients.created_at desc;";
            
        }
        $sql = $conn->prepare($query);
        $sql->bindParam(":user",$user_id,PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql;
            $obj->closeConnection();
            return $result;
        }
    }

    public function getAllDefaultersDataForAdmin($columns=array() , $search, $limit_start , $limit_end)
    {
        $obj = new Database;
        $conn = $obj->connect();
        if($search != NULL){
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%'  )  and total!=amount_given order by clients.created_at desc;";
            else
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where ( name LIKE '%".str_replace(' ', '%', $search)."%'  or code LIKE '%".str_replace(' ', '%', $search)."%' )  and total!=amount_given order by clients.created_at desc;";
           
        }else{
            if ( ($limit_start==0) && ($limit_end==0) )
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where total!=amount_given order by clients.created_at desc;";
            else
                $query = "SELECT ".  "`clients`.`".implode('`,`clients`.`',$columns)."`"  ."FROM `clients` inner join `users` on users.user_id = clients.user_id  where total!=amount_given order by clients.created_at desc;";
            
        }
        $sql = $conn->query($query);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql;
            $obj->closeConnection();
            return $result;
        }
    }

    public function getLatestClientID()
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "SELECT code FROM `clients` order by client_id desc limit 1;";
        $sql = $conn->query($query);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                $obj->closeConnection();
                return $row["code"];
            }
        }
        return false;
    }

    public function getValueOfClient($col,$client_id)
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "SELECT $col FROM `clients` where client_id=:cli";
        $sql = $conn->prepare($query);
        $sql->bindParam(':cli',$client_id,PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                $obj->closeConnection();
                return $row[$col];
            }
        }
    }

    public function updateClient($client_id , $arr = array())
    {
        try{
            $obj = new Database;
            $conn  = $obj->connect();
            $query = "UPDATE `clients` SET ";
            foreach($arr as $key=>$val)
            {
                $query .= " `$key` = :$key ,";
            }
            $query = rtrim($query, ",");
            $query .= 'where client_id = :cli';
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
            $sql->bindParam(':cli',$client_id,PDO::PARAM_INT);
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

    public function deleteClient($client_id)
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "DELETE from `clients` where client_id=:cli";
        $sql = $conn->prepare($query);
        $sql->bindParam(':cli',$client_id,PDO::PARAM_INT);
        $result = $sql->execute();
        if($result)
        {
            return true;
        }
        return false;
    }

    public function getTotalBalanceAmountForUser($userid)
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "select sum(total  - amount_given) as total from clients where user_id=:user ; ";
        $sql = $conn->prepare($query);
        $sql->bindParam(":user",$userid,PDO::PARAM_INT);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                $obj->closeConnection();
                return round($row["total"],3);
            }
        }
    }

    public function getTotalBalanceAmountForAdmin()
    {
        $obj = new Database;
        $conn = $obj->connect();
        $query = "select sum(total  - amount_given) as total from clients; ";
        $sql = $conn->query($query);
        $sql->execute();
        if ($sql->rowCount() > 0) {
            $result = $sql->fetchAll(PDO::FETCH_ASSOC);
            foreach($result as $row)
            {
                $obj->closeConnection();
                return round($row["total"],3);
            }
        }
    }

}


?>