<?php
class User
{
    private $userTable ='expense_users';
    private $conn;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function listUsers()
    {
        // Query for resgister form
        $sqlQuery = "SELECT id,first_name, last_name,email,password, role FROM ".$this->userTable. " ";

        if(!empty($_POST["search"]["value"]))
        {
            $sqlQuery .= 'WHERE id LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= 'first_name LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= 'WHERE (id LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= 'WHERE (id LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= 'WHERE (id LIKE "%'.$_POST["search"]["value"].'%" ';
        }
    }
}

?>