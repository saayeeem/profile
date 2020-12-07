<?php
require_once "pdo.php";
session_start();

if (!isset($_SESSION['name'])) {
    die('NOT Logged In');
}

// line 1 added to trigger color syntax highlight
function validatePos() {
  for($i = 1; $i <= 9; $i++) {
    if (!isset($_POST['year'.$i])) continue;
    if (!isset($_POST['desc'.$i])) continue;
    $year = $_POST['year'.$i];
    $desc = $_POST['desc'.$i];
    if (strlen($year) == 0 || strlen($desc) == 0) {
      return "All fields are required";
    }
    if (!is_numeric($year)) {
      return "Position year must be numeric";
    }
  }
  return true;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {
    if (strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            $_SESSION['error'] = 'All fields are required';
            header("Location: edit.php");
            return;
        } elseif (strpos($_POST['email'], '@') === false) {
            $_SESSION['error'] = 'Email address must contain @';
            header("Location: edit.php");
            return;
        } elseif (validatePos() != true) {
            $_SESSION['error'] = validatePos();
            header("Location: edit.php");
        } else {
          $sql = "UPDATE Profile SET first_name = :fn, last_name = :ln, email = :em, headline = :he, summary = :su WHERE profile_id = :profile_id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(':fn' => $_POST['first_name'], ':ln' => $_POST['last_name'],
                              ':em' => $_POST['email'], ':he' => $_POST['headline'],
                              ':su' => $_POST['summary'], ':profile_id' => $_GET['profile_id']));
          $_SESSION['success'] = 'Record edited';
          $stmt = $pdo->prepare('DELETE FROM position WHERE profile_id=:pid');
          $stmt->execute(array(':pid' => $_REQUEST['profile_id']));
          $rank = 1;
          for ($i = 1; $i <= 9; $i++) {
              if (!isset($_POST['year' . $i])) continue;
              if (!isset($_POST['desc' . $i])) continue;
              $year = $_POST['year' . $i];
              $desc = $_POST['desc' . $i];
              $stmt = $pdo->prepare('INSERT INTO position (profile_id, rank, year, description) VALUES (:pid, :rank, :year, :desc)');
              $stmt->execute(array(':pid' => $_REQUEST['profile_id'], ':rank' => $rank, ':year' => $year, ':desc' => $desc));
              $rank++;
          }
          $_SESSION['success'] = 'Profile Edited';
          header('Location: index.php');
          return;
        }
}

if (!isset($_GET['profile_id'])) {
    $_SESSION['error'] = "Missing profile_id";
    header('Location: index.php');
    return;
}

$stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :xyz");
$stmt->execute(array(":xyz" => $_GET['profile_id']));
$items = $stmt->fetch(PDO::FETCH_ASSOC);

$stmtPos = $pdo->prepare("SELECT * FROM position where profile_id = :xyz");
$stmtPos->execute(array(":xyz" => $_GET['profile_id']));
$itemsPos = $stmtPos->fetchAll();

if ($items === false) {
    $_SESSION['error'] = 'Bad value for user_id';
    header('Location: index.php');
    return;
}

?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once "bootstrap.php"; ?>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" integrity="sha384-1q8mTJOASx8j1Au+a5WDVnPi2lkFfwwEAa8hDDdjZlpLegxhjVME1fgjWPGmkzs7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap-theme.min.css" integrity="sha384-fLW2N01lMqjakBkx3l/M9EahuwpSfeNvV63J5ezn3uZzapT0u7EYsXMjQV+0En5r" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.js" integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE=" crossorigin="anonymous"></script>
    <title>Mohammad Sayem Chowdhury</title>
</head>
<body>
<div class="container">
    <h1>Editing Profile for UMSI</h1>
    <?php
    if (isset($_SESSION['error'])) {
        echo('<p style="color: red;">' . htmlentities($_SESSION['error']) . "</p>\n");
        unset($_SESSION['error']);
    }
    ?>
    <form method="post">
        <p>First Name: <input type="text" name="first_name" size="60" value="<?php echo $items['first_name'] ?>"/></p>
        <p>Last Name: <input type="text" name="last_name" size="60" value="<?php echo $items['last_name'] ?>"/></p>
        <p>Email: <input type="text" name="email" value="<?php echo $items['email'] ?>"/></p>
        <p>Headline: <input type="text" name="headline" size="100" value="<?php echo $items['headline'] ?>"/></p>
        <p>Summary:<br/><textarea name="summary" rows="10" cols="100"><?php echo $items['summary'] ?></textarea>
        <p>Position: <input type="submit" id="addPos" value="+">
        <div id="posField">
            <?php
            $rank = 1;
            foreach ($itemsPos as $item) {
                echo "<div id=\"position" . $rank . "\">
                <p>Year: <input type=\"text\" name=\"year1\" value=\"".$item['year']."\">
                <input type=\"button\" value=\"-\" onclick=\"$('#position". $rank ."').remove(); return false;\"></p>
                <textarea name=\"desc". $rank ."\"').\" rows=\"8\" cols=\"80\">".$item['description']."</textarea></div>";
                $rank++;
            } ?>
        </div>
        <input type="submit" value="Add" class="btn btn-primary" name = "add">
        <input type="submit" value="Cancel" class="btn btn-dark" name="cancel">
    </form>
    <script>
        cntPos = 0;
        $(document).ready(function () {
            window.console && console.log('Document ready called');
            $('#addPos').click(function (event) {
                event.preventDefault();
                if (cntPos >= 9) {
                    alert("Maximum of 9 position entries exceeded");
                    return;
                }
                cntPos++;
                window.console && console.log("Adding position " + cntPos);
                $('#posField').append('<div id = "position' + cntPos + '"> \
                    <p>Year: <input type = "text" name = "year' + cntPos + '" value = "" /> \
                    <input type = "button" value = "-" onclick="$(\'#position' + cntPos + '\').remove(); return false;"></p> \
                    <textarea name = "desc' + cntPos + '" rows = "8" cols = "80"></textarea></div>');
                });
          });
      </script>
</div>
</body>
</html>
