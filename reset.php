<?php  include "includes/db.php"; ?>
<?php  include "includes/header.php"; ?>


<?php 

// Suggested code from other user
$tokenCheck = mysqli_prepare($connection, "SELECT token, user_email FROM users WHERE user_email = ?");
    mysqli_stmt_bind_param($tokenCheck, "s", $_GET['email']);
    mysqli_stmt_execute($tokenCheck);
    mysqli_stmt_bind_result($tokenCheck, $tokenV, $emailV);
    mysqli_stmt_fetch($tokenCheck);
    if($_GET['token'] != $tokenV){
        redirect('index');
    }
    mysqli_stmt_close($tokenCheck);//caused navigation to break if not included

    if(!isset($_GET['email']) && !isset($_GET['token'])){
        redirect('index');
    }

    if($stmt = mysqli_prepare($connection, 'SELECT username, user_email, token FROM users WHERE token=?')){
        mysqli_stmt_bind_param($stmt, "s", $_GET['token']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $username, $user_email, $token);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // if($_GET['token'] !== $token || $_GET['email'] !== $email){
        //     redirect('index');
        // }

        if(isset($_POST['password']) && isset($_POST['confirmPassword'])){
            if($_POST['password'] === $_POST['confirmPassword']){
                $password = $_POST['password'];
                $hashedPwd = password_hash($password, PASSWORD_BCRYPT, array('cost'=>12));
                if($stmt = mysqli_prepare($connection, "UPDATE users SET token='', user_password='{$hashedPwd}' WHERE user_email=?")){
                    mysqli_stmt_bind_param($stmt, "s", $_GET['email']);
                    mysqli_stmt_execute($stmt);
                    if(mysqli_stmt_affected_rows($stmt) >= 1){
                        redirect('/cms/login');
                    }
                    mysqli_stmt_close($stmt);
                    $verified = true;
                }
            }
        }
    }


?>

<!-- Navigation -->
<?php include "includes/navigation.php";?>

<!-- Page Content -->
<div class="container">
        <div class="row">
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="text-center">


                            <h3><i class="fa fa-lock fa-4x"></i></h3>
                            <h2 class="text-center">Reset Password</h2>
                            <p>You can reset your password here.</p>
                            <div class="panel-body">


                                <form id="register-form" role="form" autocomplete="off" class="form" method="post">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-user color-blue"></i></span>
                                            <input id="password" name="password" placeholder="Enter password" class="form-control"  type="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="input-group">
                                            <span class="input-group-addon"><i class="glyphicon glyphicon-ok color-blue"></i></span>
                                            <input id="confirmPassword" name="confirmPassword" placeholder="Confirm password" class="form-control"  type="password">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <input name="resetPassword" class="btn btn-lg btn-primary btn-block" value="Reset Password" type="submit">
                                    </div>

                                    <input type="hidden" class="hide" name="token" id="token" value="">
                                </form>

                            </div><!-- Body-->

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<hr>

    <?php include "includes/footer.php";?>

</div> <!-- /.container -->

