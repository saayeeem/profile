<?php
session_start();
require_once "pdo.php";
require_once "util.php";
if (!isset($_SESSION['name']))
{
    die('Not logged in');
}
   require_once "pdo.php";
   if ( isset($_POST['cancel']) ) {
       header('Location: index.php');
       return;
   }
   $status = false;  // If we have no POST data
   $status_color = 'red';

   if ( isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email'])
        && isset($_POST['headline']) && isset($_POST['summary'])) {
          unset($_SESSION['first_name']);
          unset($_SESSION['last_name']);
          unset($_SESSION['email']);
          unset($_SESSION['headline']);
          unset($_SESSION['summary']);

          $countpos = 0;
          for($i=1; $i<=9; $i++) {
              if ( ! isset($_POST['year'.$i]) ) continue;
              if ( ! isset($_POST['desc'.$i]) ) continue;

              $year = $_POST['year'.$i];
              $desc = $_POST['desc'.$i];

              if ( strlen($year) == 0 || strlen($desc) == 0 ) {
                $_SESSION["err"] = "All fields are required";
                error_log("Position All fields are required");
                header( 'Location: add.php' ) ;
                return;
              }
              if ( ! is_numeric($year) ) {
                $_SESSION["err"] = "Position year must be numeric";
                error_log("Position year must be numeric");
                header( 'Location: add.php' ) ;
                return;
              }
              $countpos++;
            }

          if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1  ||
              strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 ||
              strlen($_POST['summary']) < 1){
            $_SESSION["err"] = "All fields are required";
            error_log("Fields Required  ");
            header( 'Location: add.php' ) ;
            return;
          }else if ((strpos(htmlentities($_POST['email']), '@') === false)){
                $_SESSION["err"] = "Email must have an at-sign (@)";
                error_log("Email must contain @  ".$_POST['email']);
                header( 'Location: add.php' ) ;
                return;
              }else{
                  $stmt = $pdo->prepare('INSERT INTO Profile
                          (user_id, first_name, last_name, email, headline, summary)
                          VALUES ( :uid, :fn, :ln, :em, :he, :su)');
                  $stmt->execute(array(
                    ':uid' => $_SESSION['user_id'],
                    ':fn' => $_POST['first_name'],
                    ':ln' => $_POST['last_name'],
                    ':em' => $_POST['email'],
                    ':he' => $_POST['headline'],
                    ':su' => $_POST['summary'])
                  );
                  $profile_id = $pdo->lastInsertId();
                  for($rank=1; $rank<=$countpos; $rank++) {
                    $year = $_POST['year'.$rank];
                    $desc = $_POST['desc'.$rank];
                    $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)');
                    $stmt->execute(array(
                      ':pid' => $profile_id,
                      ':rank' => $rank,
                      ':year' => $year,
                      ':desc' => $desc)
                    );


       }

     }
     if ( isset($_POST['add'])) {
         $_SESSION['success'] = 'Profile Added';
         header( 'Location: index.php' ) ;
         return;
     }

   }
?>
<html>
<head>
    <title>Mohammad Sayem Chowdhury Adding Page</title>
    <link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css"
    integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7"
    crossorigin="anonymous">

    <link rel="stylesheet"
    href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css"
    integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r"
    crossorigin="anonymous">

    <script
      src="https://code.jquery.com/jquery-3.2.1.js"
      integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
      crossorigin="anonymous"></script>

    <?php require_once "bootstrap.php"; ?>

</head>
<body>
  <div class="container">
    <h1>Adding Profile for <?= htmlentities($_SESSION['name']); ?></h1>

    <?php flashMessage(); ?>
  
    <form method="POST">

      <div class="form-group row">
        <label for="first_name" class="col-form-label col-sm-2">First Name:</label>
        <div class="col-sm-5">
          <input type="text" name="first_name" id="first_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="last_name" class="col-form-label col-sm-2">Last Name:</label>
        <div class="col-sm-5">
          <input type="text" name="last_name" id="last_name" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="email" class="col-form-label col-sm-2">Email:</label>
        <div class="col-sm-5">
          <input type="text" name="email" id="email" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="headline" class="col-form-label col-sm-2">Headline:</label>
        <div class="col-sm-5">
          <input type="text" name="headline" id="headline" class="form-control">
        </div>
      </div>

      <div class="form-group row">
        <label for="summary" class="col-form-label col-sm-2">Summary:</label>
        <div class="col-sm-5">
          <textarea name="summary" id="summary" cols="10" rows="5" class="form-control"></textarea>
        </div>
      </div>

      <div class="form-group row">
        <label class="col-form-label col-sm-2">Position: </label>
        <div class="col-sm-5">
          <button id="addPos" class="btn btn-secondary">+</button>
        </div>
      </div>

      <div id="position_fields">

      </div>

      <input type="submit" value="Add" class="btn btn-primary" name = "add">
      <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">

    </form>
  </div>


<script>
countPos = 0;


// http://stackoverflow.com/questions/17650776/add-remove-html-inside-div-using-javascript
$(document).ready(function(){
    window.console && console.log('Document ready called');
    $('#addPos').click(function(event){
        // http://api.jquery.com/event.preventdefault/
        event.preventDefault();
        if ( countPos >= 9 ) {
            alert("Maximum of nine position entries exceeded");
            return;
        }
        countPos++;
        window.console && console.log("Adding position "+countPos);
        $('#position_fields').append(
            '<div id="position'+countPos+'"> \
            <p>Year: <input type="text" name="year'+countPos+'" value="" /> \
            <input class ="btn btn-danger" type="button" value="-" \
                onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
            <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea>\
            </div>');
    });
});
</script>
</body>
</html>
