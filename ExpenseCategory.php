<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
if(!$user->loggedIn())
{
  header("Location :index.php");
}
include_once ('inc/header.php');
?>
<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/general.js"></script>
<script src="js/expense_category.js"></script>
<div class="container">
    <h2>Expense Management System</h2><br>
    <?php include_once('TopMenus.php');?>
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-10">
                <h3 class="panel-title"></h3>
            </div>
            <div class="col-md-2" style = "align:right";>
				<button type="button" id="addCategory" class="btn btn-info" title="Add Category"><span class="glyphicon glyphicon-plus"></span>
                </button>
			</div>
        </div>
    </div>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>S.no</th>
                <th>Category</th>
                <th>Status</th>
            </tr>
        </thead>
    </table>


    <div id="categorymodal class= modal fade">
        <div class="modal-dialog">
            <form action="" method="POST" id="categoryForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title"><i class="fa fa-plus"></i>Edit Category</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <div class="row">
                                <label for="" class="col-md-4">Category Name <span class="text-danger">*</span></label>
                                <div class="col-md-8">
                                        <input type="text" name="categoryName" id="categoryName" autocomplete="off" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4 text-right">Status <span class="text-danger">*</span></label>
                                    <div class="col-md-8">
                                        <select name="status" id="status" class="form-control">
                                            <option value="enable">Enable</option>
                                            <option value="disable">Disable</option>
                                        </select>
                                    </div>
                                </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                            <input type="hidden" name="id" id="id" />						
                            <input type="hidden" name="action" id="action" value="" />
                            <input type="submit" name="save" id="save" class="btn btn-info" value="Save" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php include_once ('inc/header.php');?>