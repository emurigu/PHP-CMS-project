<?php  include "includes/db.php"; ?>
<?php  include "includes/header.php"; ?>

<?php 
require __DIR__ . '/vendor/autoload.php';

// setting language variables
if(isset($_GET['lang']) && !empty($_GET['lang'])){
    $_SESSION['lang'] = $_GET['lang'];

    if(isset($_SESSION['lang']) && $_SESSION['lang'] != $_GET['lang']){
        echo "<script type='text/javascript'>location.reload();</script>";
    }
}

if(isset($_SESSION['lang'])){
    include "includes/languages/".$_SESSION['lang'].".php";
} else {
    include "includes/languages/en.php";
}


// section 311 code - Pusher Notification
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$options = array(
    'cluster' => 'us2',
    'useTLS' => true
  );
$pusher = new Pusher\Pusher(getenv('APP_KEY'),getenv('APP_SECRET'),getenv('APP_ID'),$options);

// Authentication 
if($_SERVER['REQUEST_METHOD'] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    $error = [
        'username' => '',
        'email' => '',
        'password' => ''
    ];
// username length validation
    if(strlen($username) < 4){
        $error['username'] = 'Username too short';
    }
// username validation
    if($username ==''){
        $error['username'] = 'Username cannot be empty';
    }
// username confirmation
    if(username_exists($username)){
        $error['username'] = 'Username exists, think harder';
    }
// email validation
    if($email ==''){
        $error['email'] = 'Email cannot be empty';
    }
// email exist validation
    if(useremail_exists($email)){
        $error['email'] = 'Email already exists, <a href="./">Login Here</a>';
    }
// password validation
    if($password == ''){
        $error['password'] = 'Password cannot be empty';
    }
//ensures key is empty before procceeding 
    foreach ($error as $key => $value){
        if(empty($value)){
            unset($error[$key]);
        }
    }

    if(empty($error)){
        register_user($username, $email, $password);
        // Pusher Intergration notification
        //$data['message']= $username;
        $pusher->trigger('notifications','new_user',$username);

        login_user($username, $password);
    }

}
?>
    <!-- Navigation -->
    
    <?php  include "includes/navigation.php"; ?>
    
    <!-- Page Content -->
    <div class="container">

    <form method="get" class="navbar-form navbar-right" id="language_form" action="">
        <div class="form-group">
            <select name="lang" class="form-contol" onchange="changeLanguage()">
                <option value="en" <?php if(isset($_SESSION['lang']) && $_SESSION['lang'] == 'en'){ echo "selected";} ?>>English</option>
                <option value="es" <?php if(isset($_SESSION['lang']) && $_SESSION['lang'] == 'es'){ echo "selected";} ?>>Spanish</option>
            </select>
        </div>
    </form>
    
<section id="login">
    <div class="container">
        <div class="row">
            <div class="col-xs-6 col-xs-offset-3">
                <div class="form-wrap">
                <h1><?php echo _REGISTER;?></h1>
                    <form role="form" action="registration.php" method="post" id="login-form" autocomplete="off">

                        <div class="form-group">
                            <label for="username" class="sr-only">username</label>
                            <input type="text" name="username" id="username" class="form-control" placeholder="<?php echo _USERNAME; ?>" autocomplete="on"
                            value="<?php echo isset($username) ? $username : '' ?>"><!--retains user input data incase of fail -->
                            <p><?php echo isset($error['username']) ? $error['username'] : '' ?></p> <!--shorthand if statement-->
                        </div>
                         <div class="form-group">
                            <label for="email" class="sr-only">Email</label>
                            <input type="email" name="email" id="email" class="form-control" placeholder="<?php echo _EMAIL; ?>"
                            autocomplete="on"
                            value="<?php echo isset($email) ? $email : '' ?>"><!--retains user input data incase of fail -->
                            <p><?php echo isset($error['email']) ? $error['email'] : '' ?></p>
                        </div>
                         <div class="form-group">
                            <label for="password" class="sr-only">Password</label>
                            <input type="password" name="password" id="key" class="form-control" placeholder="<?php echo _PASSWORD; ?>">
                            <p><?php echo isset($error['password']) ? $error['password'] : '' ?></p>
                        </div>
                
                        <input type="submit" name="register" id="btn-login" class="btn btn-primary btn-lg btn-block" value="<?php echo _REGISTER;?>">
                    </form>
                 
                </div>
            </div> <!-- /.col-xs-12 -->
        </div> <!-- /.row -->
    </div> <!-- /.container -->
</section>

        <hr>

        <script>
            function changeLanguage(){
                document.getElementById('language_form').submit();
            }
        </script>

<?php include "includes/footer.php";?>
