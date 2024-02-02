<?php
class Expense {

    private $categoryTable ='expense_categories';
    private $expenseTable ='expenses';
    private $conn;

    public function __construct($db)
    {
        $this->conn - $db;
    }

    public function listExpense()
    {
        if($_SESSION["userid"])
        {
            $sqlQuery = "SELECT expense.id,expense.amount,expense.date,category.name FROM ".$this->expenseTable."AS expense LEFT JOIN ".$this->categoryTable." AS category ON expense.category_id = category.id WHERE expense.user_id = '".$_SESSION["userid"]."' ";

            if(!empty($_POST["search"]["value"]))
            {
                $sqlQuery .= 'AND (expense.id LIKE "%'.$_POST["search"]["value"].'%" ';
                $sqlQuery .= 'OR expense.amount LIKE "%'.$_POST["search"]["value"].'%" ';
                $sqlQuery .= 'OR expense.date LIKE "%'.$_POST["search"]["value"].'%" ';
                $sqlQuery .= 'OR category.name LIKE "%'.$_POST["search"]["value"].'%")';             
            }

            if(!empty($_post["order"]))
            {
                $sqlQuery .='ORDER BY '.$_POST['order']['0']['column'].''.$_POST['order']['0']['dir'].' ';
            }else{
                $sqlQuery .= 'ORDER BY expense.date DES ';
            }

            if($_POST["length"] != -1)
            {
                $sqlQuery .= 'LIMIT '. $_POST['start'] .',' . $_POST['length'];
            }

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $result = $stmt->get_result();

            $stmtTotal = $this->conn->prepare($sqlQuery);
            $stmtTotal->execute();
            $allResult = $stmtTotal->get_result();
            $allRecords = $allResult->num_rows;

            $displayRecords = $result->num_rows;
            $records = array();
            $count =1;
            while ($expense = $result->fetch_assoc())
            {
                $rows = array();
                $rows[] = $count;
                $rows[] = ucfirst($expense['amount']);
                $rows[] = $expense['name'];
                $rows[] = $expense['date'];
                $rows[] = '<button type="button" name ="update" id="'.$expense["id"].'"class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span
                > </button>';
                $rows[] = '<button type="button" name ="delete" id="'.$expense["id"].'"class="btn btn-danger btn-xs delete"><span class="glyphicon glyphicon-remove" title="Delete"></span
                > </button>';
                $records[] = $rows;
                $count++;                
            }
            
            $output = array(
                "draw" => intval($_POST["draw"]),
                "iTotalRecords" => $displayRecords,
                "iTotalDisplayRecords" => $allRecords,
                "data" => $records
            );
            echo json_encode($output);
        }
    }

    public function insert()
    {		
		if($this->expense_category && $this->amount && $_SESSION["userid"]) 
        {
			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->expenseTable."(`amount`, `date`, `category_id`, `user_id`)
				VALUES(?, ?, ?, ?)");		
			$this->amount = htmlspecialchars(strip_tags($this->amount));
			$this->expense_date = htmlspecialchars(strip_tags($this->expense_date));
			$this->expense_category = htmlspecialchars(strip_tags($this->expense_category));
			
			$stmt->bind_param("isii", $this->amount, $this->expense_date, $this->expense_category, $_SESSION["userid"]);
			
			if($stmt->execute()){
				return true;
			}		
		}
	}

    public function update()
    {		
		if($this->id && $this->expense_category && $this->amount && $_SESSION["userid"]) 
        {
			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->expenseTable." 
			SET amount = ?, date = ?, category_id = ?
			WHERE id = ?");
	 
			$this->amount = htmlspecialchars(strip_tags($this->amount));
			$this->expense_date = htmlspecialchars(strip_tags($this->expense_date));
			$this->expense_category = htmlspecialchars(strip_tags($this->expense_category));
								
			$stmt->bind_param("isii", $this->amount, $this->expense_date, $this->expense_category, $this->id);
			
			if($stmt->execute())
            {				
				return true;
			}			
		}	
	}	
}
?>