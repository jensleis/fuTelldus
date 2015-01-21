<?php
error_reporting ( E_ALL );
ini_set ( 'display_errors', '1' );

  require("../lib/base.inc.php");

  // Auto login with remember-me cookie
  if (isset($_COOKIE["fuTelldus_user_loggedin"])) {
    $_SESSION['fuTelldus_user_loggedin'] = $_COOKIE["fuTelldus_user_loggedin"];
    header("Location: ../index.php");
    exit();
  }

?>


<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="utf-8">
    
    <title><?php echo $config['pagetitle']; ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">


  

    <!-- Jquery -->
    <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

    <!-- Bootstrap framework -->
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">
<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>


    <link href="css/pagestyle.css" rel="stylesheet">
    <!-- FontAwsome -->
<!-- <link rel="stylesheet" href="../lib/packages/font-awesome/css/font-awesome.min.css"> -->
<!-- <link rel="stylesheet" href="../lib/packages/bootstrap-social-buttons/social-buttons.css"> -->

</head>

<body>

<div class="container">


    <form class="form-signin" action="./login_exec.php" method="POST">
    
    <h2 class="form-signin-heading">
    	<img style='height: 64px; margin-bottom:15px' src="../images/earth64.png" alt='logo' />
    	<?php echo $config['pagetitle']; ?>
    </h2>

        <?php
          if (isset($_GET['msg'])) {
              if ($_GET['msg'] == 01) echo "<div class='alert alert-danger'>Wrong username and/or password</div>";
              if ($_GET['msg'] == 02) echo "<div class='alert alert-info'>You logged out</div>";
              if ($_GET['msg'] == 03) echo "<div class='alert alert-danger'>No public sensors active</div>";
          }
        ?>

        
        <input type="text" class="input-block-level form-control" name="mail" placeholder="Email address">
        <input type="password" class="input-block-level form-control" name="password" placeholder="Password">

        <label class="checkbox">
          <input type="checkbox" name="remember" value="1"><small>Remember me</small>
        </label>

        <div class="pull-right">

            <?php
//               $query = "SELECT * FROM ".$db_prefix."sensors WHERE monitoring='1' AND public='1'";
//               $result = $mysqli->query($query);
//               $numRows = $result->num_rows;

//               if ($numRows > 0) {
//                 echo "<a style='margin-right:10px;' href='../public/'>{$lang['View public sensors']}</a>";
//               }
            ?>

            <button class="btn btn-lg btn-primary" type="submit">Sign in</button>

<!--             <br /> <br /> -->
<!--    			  <a href="google_login.php" class="btn btn-google-plus"><i class="fa fa-google-plus"></i> | Connect with Google</a> -->
			      
        </div>


        <div style="clear:both;"></div>


        <?php
          // Create a random key to secure the login from this form!
          $_SESSION['secure_fuCRM_loginForm'] = "fuTelldus3sfFwer35tF36¤234%&".time()."254543";
          $hashSecureFormLogin = hash('sha256', $_SESSION['secure_fuTelldus_loginForm']);
          echo "<input type='hidden' name='uniq' value='$hashSecureFormLogin' />";
        ?>
    </form>

</div> <!-- /container -->

</body>
</html>
