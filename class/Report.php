<?php 
class Report 
{
    private $categoryTable ='expense_categories';
    private $expenseTable = 'expenses';
    private $incomeTable = 'income';
    private $incomeCategoryTable = ' income_categories';

    private $conn;
    
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getReports()
    {
        $output = [];
        if($this->fromDate && $this->toDate && $_SESSION["userid"])
        {
            // IncomeReport
            $sqlQuery = "SELECT expense.id, expense.amount, expense.date, category.name AS category
				FROM ".$this->incomeTable." AS expense 
				LEFT JOIN ".$this->incomeCategoryTable." AS category ON expense.category_id = category.id 
				WHERE expense.user_id = '".$_SESSION["userid"]."' AND expense.date BETWEEN  '".$this->fromDate."' AND '".$this->toDate."'";

					
			$stmt = $this->conn->prepare($sqlQuery);			
			$stmt->execute();
			$result = $stmt->get_result();				
			$incomeRecords = array();	
			$totalIncome = 0;
			while ($income = $result->fetch_assoc()) {			
				$totalIncome+=$income['amount'];			
			}
			if($totalIncome) {
				$row = array();
				$row['total'] = $totalIncome;
				$incomeRecords[] = $row;
			}

            // ExpenseReport
            $sqlQuery = "SELECT expense.id,expense.amount,expense.date,category.name AS category FROM ".$this->expenseTable." AS expense LEFT JOIN ".$this->categoryTable." AS category ON expense.category_id = category.id WHERE expense.date BETWEEN '". $this->fromDate."' AND '".$this->toDate."'";

            $stmt = $this->conn->prepare($sqlQuery);
            $stmt->execute();
            $result = $stmt->get_result();
            $records = array();
            while ($expense = $result->fetch_assoc())
            {
                $rows = array();
                $rows['id'] = $expense['id'];
                $rows['amount'] = $expense['amount'];
                $rows['date'] = $expense['date'];
                $rows['category'] = $expense['category'];
                $records[] = $rows;
            }
            $output = array(
                "data" => $records,
                "income" => $incomeRecords
            );
            
        }
        else{
            $output = array(
                "data" => [],
            );
        }
        echo json_encode($output);
    }
}
?>