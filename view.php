<?php
session_start();
// Demand a GET parameter
require_once "pdo.php";

if ( ! isset($_SESSION['name']) || strlen($_SESSION['name']) < 1  ) {
    die('Not logged in');
}

$status = false;

if ( isset($_SESSION['status']) ) {
	$status = $_SESSION['status'];
	$status_color = $_SESSION['color'];

	unset($_SESSION['status']);
	unset($_SESSION['color']);
}
// If the user requested logout go back to index.php

$name = htmlentities($_SESSION['name']);

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);

$stmt1 = $pdo->prepare("SELECT * FROM position where profile_id = :xyz");
$stmt1->execute(array(":xyz" => $_GET['profile_id']));
$rowOfPosition = $stmt1->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Mohammad Sayem Chowdhury</title>
    <?php require_once "bootstrap.php"; ?>
</head>
<body>
<div class="container">
  <h1>Profile information</h1>

  <p>First Name: <?php echo($row['first_name']); ?></p>
  <p>Last Name: <?php echo($row['last_name']); ?></p>
  <p>Email: <?php echo($row['email']); ?></p>
  <p>Headline:<br/> <?php echo($row['headline']); ?></p>
  <p>Summary: <br/><?php echo($row['summary']); ?></p>
  <p>Position: <br/>
  <ul>
      <?php
        foreach ($rowOfPosition as $row) {
          echo('<li>'.$row['year'].':'.$row['description'].'</li>');
      } ?>
      </ul></p>
   <a href="index.php" class="btn btn-primary" type="submit">Done</a>
</div>
</body>
</html>
