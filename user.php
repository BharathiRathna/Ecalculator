<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();

$user = new User($db);
$expense = new Expense($db);

if(!$user->loggedIn())
{
    header("Location: index.php");
}
include('inc/header.php');

?>


<script src="js/jquery.dataTables.min.js"></script>
<script src="js/dataTables.bootstrap.min.js"></script>		
<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
<script src="js/general.js"></script>
<script src="js/user.js"></script>
<?php include('inc/container.php');?>
<div class="container">
    <h2> Expense Calculator System</h2><br>
    <?php include_once('top_menus.php');?>
    <div>
        <div class="panel-handling">
            <div class="row">
                <div class="col-md-10">
                    <h3 class="panel-title"></h3>
                </div>
                <div class="col-md-2" style = "align : right";>
                    <button id="adduser" class="btn btn-info" title ="Add User"><span class="glyphicon glyphicon-plus"></span></button>
                </div>
            </div>
        </div>
        <table class=" table table-bordered table-striped">
            <thead>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>

                </tr>
            </thead>
        </table>
    </div>

    <div id="userModal" class = "modal fade">
        <div class="modal-dialog">
            <form action="" method ="post" id="userForm">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type ="button" class="close" data-dismiss ="modal">$times;</button>
                        <h4 class ="modal-title"><i class="fa fa-plus"></i> Edit User</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="country" class="control-label">Role</label>
                            <select name="role" id="role" class="form-control">
                                <option value="">Select Role</option>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="Income" class="control-label">First Name</label>
                            <input type="text" naem= "first_name" id="first_name" autocomplete ="off" class="form-control" placeholder="First name">
                        </div>

                        <div class="form-group">
                            <label for="project" class="control-label">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Last name" >			
                        </div>	

                        <div class="form-group">
                            <label for="project" class="control-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email" >			
                        </div>
                        
                        <div class="form-group">
                            <label for="project" class="control-label">New Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="password" >			
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
 <?php include_once ('inc/footer.php'); ?>