<?php // Do not put any HTML above this line

session_start();
require_once "pdo.php";
unset($_SESSION['name']);
unset($_SESSION['user_id']);

if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}

$salt = 'XyZzy12*_';

$failure = false;
// Check to see if we have some POST data, if we do process 0interface
if ( isset($_POST['email']) && isset($_POST['pass']) ) {
    if ( strlen($_POST['email']) < 1 || strlen($_POST['pass']) < 1 ) {
        $_SESSION['failure'] = "User name and password are required";
        header("Location: login.php");
        return;
    }
    $check = hash('md5', $salt.$_POST['pass']);

    $stmt = $pdo->prepare('SELECT user_id, name FROM users WHERE email = :em AND password = :pass');
    $stmt->execute(array(':em' => $_POST['email'], ':pass' => $check));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

     if ($row !== false) {

         $_SESSION['name'] = $row['name'];

         $_SESSION['user_id'] = $row['user_id'];

 // Redirect the browser to index.php

         header("Location: index.php");

         return;
     }

     elseif($row == false) {
       error_log("Login fail ".$pass." $check");
       $_SESSION['failure'] = "Incorrect password.";
         header( "Location: login.php" ) ;

         return;
 }
}



// Fall through into the View
?>
<!DOCTYPE html>
<html>
<head>
  <title>Mohammad Sayem Chowdhry Add Page</title>
  <?php require_once "bootstrap.php"; ?>
  <script type="text/javascript" src="jquery.min.js">
  </script>
</head>
<body>
        <div class="container">
            <h1>Please Log In</h1>
                <?php
                    // Note triple not equals and think how badly double
                    // not equals would work here...
                    if ( $failure !== false )
                    {
                        // Look closely at the use of single and double quotes
                        echo(
                            '<p style="color: red;" class="col-sm-10 col-sm-offset-2">'.
                                htmlentities($failure).
                            "</p>\n"
                        );
                    }
                ?>
            <form method="post" class="form-horizontal">
                <div class="form-group">
                    <label class="control-label col-sm-2" for="email">User Name</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="email" id="email">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-2" for="pass">Password:</label>
                    <div class="col-sm-3">
                        <input class="form-control" type="text" name="pass" id="id_1723">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-2 col-sm-offset-2">
                        <input class="btn btn-primary" type="submit" onclick="return doValidate();"
                         value="Log In">
                </div>
            </form>
            <p>
                For a password hint, view source and find a password hint
                in the HTML comments.
                <!-- Hint: The password is the four character sound a cat
                makes (all lower case) followed by 123. -->
            </p>
            <script>
                function doValidate() {
                console.log('Validating...');

                try {
                addr = document.getElementById('email').value;
                pw = document.getElementById('id_1723').value;

                console.log("Validating pw="+pw);

                if (addr == null || addr =="" || pw == null || pw == "") {

                  alert("Both fields must be filled out");

                  return false;

                }
                if (addr.indexOf('@') == -1) {

                  alert("Invalid Email Address");
                    return false;

                }
                 return true;
               }catch(e) {

                return false;

                }

                return false;

                }
            </script>
        </div>
    </body>
</html>
