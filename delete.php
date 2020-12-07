<?php
require_once "pdo.php";
session_start();
if ( ! isset($_SESSION['user_id'])) {
    die('Not logged in');
}
if ( isset($_POST['cancel'] ) ) {
    // Redirect the browser to game.php
    header("Location: index.php");
    return;
}
if ( isset($_POST['delete']) && isset($_POST['profile_id']) ) {
    $sql = "DELETE FROM profile WHERE profile_id = :zip";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(':zip' => $_POST['profile_id']));
    $_SESSION['success'] = 'profile deleted';
    header( 'Location: index.php' ) ;
    return;
}

// Guardian: Make sure that profile_id is present
if ( ! isset($_GET['profile_id']) ) {
  $_SESSION['error'] = "Missing profile_id";
  header('Location: index.php');
  return;
}

$stmt = $pdo->prepare("SELECT first_name,last_name FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ( $row === false ) {
    $_SESSION['error'] = 'Bad value for profile_id';
    header('Location: index.php');
    return;
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Welcome to profile Database</title>
    <script src="jquery-3.5.1.min.js"></script>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
    <p>Confirm: Deleting <?php echo $row['first_name'].$row['last_name'] ?></p>
    <form method="post"><input type="hidden" name="profile_id" value="<?php echo $_GET['profile_id'] ?>">
       <input  class="btn btn-primary" type="submit" value="Delete" name="delete">
      <input  class="btn btn-primary" type="submit" name="cancel" value="Cancel">
    </form>
</div>
</body>
