<?php
include_once 'config/Database.php';
include_once 'class/User.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

if($user->loggedIn())
{
    header("Location :expense.php");
}
$loginMessage ='';
if(!empty($_POST["login"]) && !empty($_POST["email"]) && !empty($_POST["password"]))
{
    $user->email = $_POST["email"];
    $user->password = $_POST["password"];
    if($user->login())
    {
        header("Location: expense.php");
    }else{
        $loginMessage = 'Invalid login!! Please try again.';
    }
}else{
    $loginMessage ='Fill all fields.';
}

include_once('inc/header.php');
?>
<!--Login Form  -->
<div class="content">
    <div class="container-fluid">
        <div class="col-md-6">
            <div class="panel panel-info login-form">
                <div class="panel-heading ">
                    <div class= "panel-title">
                        Log In
                    </div>
                </div>
                <div style= "padding-top:30px" class="panel-body">     
                    <?php if($loginMessage != '') { ?>
                        <div id="login-alert" class="alert alert-dander col-sm-12"><?php echo $loginMessage; ?>
                        </div>
                    <?php }?>
                    <form id ="loginform" class ="form-horizontal" role="form" method="POST"> 
                        <div style= "margin-bottom :25px" class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-addon"></i>
                            </span>
                            <input type="text" class="form-control" id="email" name="email" value="<?php if(!empty($_POST["email"])) { echo $_POST["email"]; } ?>" placeholder="email" style="background:white;" required>
                        </div>
                        <div style="margin-bottom:25px" class="input-group">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-lock"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" value="<?php if(!empty($_POST["password"])) { echo $_POST["password"]; } ?>" placeholder="password" required>
                        </div>
                        <div style="margin-top:10px" class="form-group">
                            <div class="col-sm-12 controls">
                                <input type="submit" name="login" value="Login" class="btn btn-info">
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--End Login Form-->
<?php include_once('inc/footer.php'); ?>