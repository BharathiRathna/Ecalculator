<?php
 session_start();
 // Database Connection
 require_once ('config\Constant.php');

 class Database
    {
        public function getConnection()
        {
            $conn = mysqli_connect(HOST,USER,PASSWORD,DATABASE);
            if($conn->connect_error)
            {
                die("Error failed to connect to MySQL: " .$conn->connect_error);
            }else
            {
                return $conn;
            }
        }
    }
    
?>