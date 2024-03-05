<?php
class Income 
{
    private $incomeTable = 'income';
    private $incomeCategoryTable =' income_categories';
    private $conn;

    public function __construct($db){
        $this->conn = $db;
    }

    public function listIncome()
    {
        if($_SESSION["userid"])
        {
            $sqlQuery = "SELECT income.id, income.amount, income.date, category.name
            FROM ".$this->incomeTable." AS income 
            LEFT JOIN ".$this->incomeCategoryTable." AS category ON income.category_id = category.id 
            WHERE income.user_id = '".$_SESSION["userid"]."' ";

            if(!empty($_POST["search"]["value"]))
            {
                $$sqlQuery .= ' AND (income.id LIKE "%'.$_POST["search"]["value"].'%" ';
				$sqlQuery .= ' OR income.amount LIKE "%'.$_POST["search"]["value"].'%" ';
				$sqlQuery .= ' OR income.date LIKE "%'.$_POST["search"]["value"].'%" ';
				$sqlQuery .= ' OR category.name LIKE "%'.$_POST["search"]["value"].'%" ';
            }

            if(!empty($_POST["order"]))
            {
                $sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
            } else{
                $sqlQuery .= 'ORDER BY income.id ';
            }

            if($_POST["length"] != -1){
				$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
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
			$count = 1;
			while ($income = $result->fetch_assoc()) { 				
				$rows = array();			
				$rows[] = $count;
				$rows[] = ucfirst($this->IND_money_format($income['amount']));
				$rows[] = $income['name'];	
				$rows[] = date("d-m-Y", strtotime($income['date']));			
				$rows[] = '<button type="button" name="update" id="'.$income["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span></button>';
				$rows[] = '<button type="button" name="delete" id="'.$income["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete"></span></button>';
				$records[] = $rows;
				$count++;
			}

            $output = array(
				"draw"	=>	intval($_POST["draw"]),			
				"iTotalRecords"	=> 	$displayRecords,
				"iTotalDisplayRecords"	=>  $allRecords,
				"data"	=> 	$records
			);

            echo json_encode($output);
        }
    }

	public function IND_money_format($number){
        $decimal = (string)($number - floor($number));
        $money = floor($number);
        $length = strlen($money);
        $delimiter = '';
        $money = strrev($money);

        for($i=0;$i<$length;$i++){
            if(( $i==3 || ($i>3 && ($i-1)%2==0) )&& $i!=$length){
                $delimiter .=',';
            }
            $delimiter .=$money[$i];
        }

        $result = strrev($delimiter);
        $decimal = preg_replace("/0\./i", ".", $decimal);
        $decimal = substr($decimal, 0, 3);

        if( $decimal != '0'){
            $result = $result.$decimal;
        }

        return $result;
    }

    public function insert()
    {
        if($this->income_category && $this->amount && $_SESSION["userid"]) 
        {
			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->incomeTable."(`amount`, `date`, `category_id`, `user_id`)
				VALUES(?, ?, ?, ?)");
		
			$this->amount = htmlspecialchars(strip_tags($this->amount));
			$this->income_date = htmlspecialchars(strip_tags($this->income_date));
			$this->income_category = htmlspecialchars(strip_tags($this->income_category));
			
			$stmt->bind_param("isii", $this->amount, $this->income_date, $this->income_category, $_SESSION["userid"]);
			
			if($stmt->execute())
            {
				return true;
			}		
		}
    }
    
    public function update()
    {
		if($this->id && $this->income_category && $this->amount && $_SESSION["userid"]) 
        {			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->incomeTable." 
			SET amount = ?, date = ?, category_id = ?
			WHERE id = ?");
	 
			$this->amount = htmlspecialchars(strip_tags($this->amount));
			$this->income_date = htmlspecialchars(strip_tags($this->income_date));
			$this->income_category = htmlspecialchars(strip_tags($this->income_category));
								
			$stmt->bind_param("isii", $this->amount, $this->income_date, $this->income_category, $this->id);
			
			if($stmt->execute())
            {				
				return true;
			}			
		}	
	}

    public function delete()
    {
		if($this->id && $_SESSION["userid"]) 
        {
			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->incomeTable." 
				WHERE id = ?");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bind_param("i", $this->id);

			if($stmt->execute())
            {				
				return true;
			}
		}
	}

    public function getIncomeDetails()
    {
		if($this->income_id && $_SESSION["userid"]) {		
			$sqlQuery = "
			SELECT income.id, income.amount, income.date, income.category_id
			FROM ".$this->incomeTable." AS income
			LEFT JOIN ".$this->incomeCategoryTable." AS category ON income.category_id = category.id
			WHERE income.id = ? ";	
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->income_id);	
			$stmt->execute();
			$result = $stmt->get_result();				
			$records = array();		
			while ($income = $result->fetch_assoc()) { 				
				$rows = array();	
				$rows['id'] = $income['id'];				
				$rows['amount'] = $income['amount'];				
				$rows['date'] = $income['date'];
				$rows['category_id'] = $income['category_id'];				
				$records[] = $rows;
			}		
			$output = array(			
				"data"	=> 	$records
			);
			echo json_encode($output);
		}
	}

    function getCategoryList()
    {		
		$stmt = $this->conn->prepare("
		SELECT id, name, status FROM ".$this->incomeCategoryTable);				
		$stmt->execute();			
		$result = $stmt->get_result();		
		return $result;	
	}

    public function listCateogry()
    {		
		$sqlQuery = "SELECT id, name, status
			FROM ".$this->incomeCategoryTable." ";			
			
		if(!empty($_POST["search"]["value"]))
        {
			$sqlQuery .= ' AND (id LIKE "%'.$_POST["search"]["value"].'%" ';
			$sqlQuery .= ' OR name LIKE "%'.$_POST["search"]["value"].'%" ';
							
		}
		
		if(!empty($_POST["order"]))
        {
			$sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
		} else {
			$sqlQuery .= 'ORDER BY id DESC ';
		}
		
		if($_POST["length"] != -1)
        {
			$sqlQuery .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
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
		$count = 1;
		while ($category = $result->fetch_assoc()) { 				
			$rows = array();			
			$rows[] = $count;
			$rows[] = ucfirst($category['name']);
			$rows[] = $category['status'] == "1" ? 'Active' : 'Inactive';				
			$rows[] = '<button type="button" name="update" id="'.$category["id"].'" class="btn btn-warning btn-xs update"><span class="glyphicon glyphicon-edit" title="Edit"></span></button>';
			$rows[] = '<button type="button" name="delete" id="'.$category["id"].'" class="btn btn-danger btn-xs delete" ><span class="glyphicon glyphicon-remove" title="Delete"></span></button>';
			$records[] = $rows;
			$count++;
		}
		
		$output = array(
			"draw"	=>	intval($_POST["draw"]),			
			"iTotalRecords"	=> 	$displayRecords,
			"iTotalDisplayRecords"	=>  $allRecords,
			"data"	=> 	$records
		);
		
		echo json_encode($output);
	}

    public function insertCategory(){
		
		if($this->categoryName && $_SESSION["userid"]) {
			
			$sqlQuery = "SELECT * FROM ".$this->incomeCategoryTable." WHERE name = ? ";	
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("s", $this->categoryName);	
			$stmt->execute();
			$result = $stmt->get_result();
		if($result->num_rows > 0) {
			$output = array(	
				"status"  => false,			
				"message" => 'Income Category Already Exists'
			);
			
			echo json_encode($output);
		}
		else{
			$stmt = $this->conn->prepare("
				INSERT INTO ".$this->incomeCategoryTable."(`name`, `status`)
				VALUES(?, ?)");
		
			$this->categoryName = htmlspecialchars(strip_tags($this->categoryName));
			$this->status = htmlspecialchars(strip_tags($this->status));
			
			$stmt->bind_param("ss", $this->categoryName, $this->status);
			
			if($stmt->execute()){
				return true;
			}
		  }		
		}
	}

    public function updateCategory(){
		
		if($this->id && $this->categoryName && $_SESSION["userid"]) {
			
			$stmt = $this->conn->prepare("
			UPDATE ".$this->incomeCategoryTable." 
			SET name = ?, status = ?
			WHERE id = ?");
	 
			$this->categoryName = htmlspecialchars(strip_tags($this->categoryName));
			$this->status = htmlspecialchars(strip_tags($this->status));
								
			$stmt->bind_param("ssi", $this->categoryName, $this->status, $this->id);
			
			$output;
			if($stmt->execute()){				
				$output = array(			
					"status"	=> 	true,
					"message" => ""
				);
			}	else{
				$output = array(			
					"status"	=> 	false,
					"message"	=> $stmt->errorInfo()
				);				
			}	
			echo json_encode($output);		
		}	
	}

    public function getIncomeCategoryDetails(){
		if($this->id && $_SESSION["userid"]) {			
					
			$sqlQuery = "
			SELECT id, name, status
			FROM ".$this->incomeCategoryTable." WHERE id = ? ";	
					
			$stmt = $this->conn->prepare($sqlQuery);
			$stmt->bind_param("i", $this->id);	
			$stmt->execute();
			$result = $stmt->get_result();				
			$records = array();		
			while ($category = $result->fetch_assoc()) { 				
				$rows = array();	
				$rows['id'] = $category['id'];				
				$rows['name'] = $category['name'];				
				$rows['status'] = $category['status'];					
				$records[] = $rows;
			}		
			$output = array(			
				"data"	=> 	$records
			);
			echo json_encode($output);
		}
	}

    public function deleteCategory(){
		if($this->id && $_SESSION["userid"]) {			

			$stmt = $this->conn->prepare("
				DELETE FROM ".$this->incomeCategoryTable." 
				WHERE id = ?");

			$this->id = htmlspecialchars(strip_tags($this->id));

			$stmt->bind_param("i", $this->id);

			if($stmt->execute()){				
				return true;
			}
		}
	}



}
?>