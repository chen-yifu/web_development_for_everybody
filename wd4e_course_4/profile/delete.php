<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=misc',
'root', 'ubcfm123');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ( ! isset($_SESSION["user_id"]) ) {
  die('Not logged in');
  die("ACCESS DENIED");
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}


$success = false;
$failure = false;
if (isset($_POST['autos_id'])) {
  try {
    $sql = "DELETE FROM autos WHERE autos_id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
      ':id' => $_POST['autos_id']
    ));
    //
    // $row = $stmt->fetch(PDO::FETCH_ASSOC);
    // if($row === false) {
    //   $_SESSION['error'] = 'Bad value for id';
    //   header("Location: index.php");
    // }
    //
    // $ma = htmlentities($row['make']);
    // $mo = htmlentities($row['model']);
    // $ye = htmlentities($row['year']);
    // $mi = htmlentities($row['mileage']);
    // $autos_id = $row['autos_id'];
    $_SESSION['success'] = "Record deleted.";
    header("Location: index.php");
    return;
  } catch (Exception $ex) {
    echo ("Exception message: ".$ex->getMessage());
    return;
  }
}


$sql = "SELECT * FROM autos where autos_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['autos_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  $_SESSION['error'] = 'Bad value for id';
  header("Location: index.php");
  return;
}
$ma = htmlentities($row['make']);
$mo = htmlentities($row['model']);
$ye = htmlentities($row['year']);
$mi = htmlentities($row['mileage']);
$autos_id = $row['autos_id'];

?>
<!DOCTYPE html>
<html>
<head>
  <title>Yifu Chen (Charles) 6d4e59d5</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>

  <div class="container">
    <h1>Editing Automobiles</h1>
    <?php
    if ( isset($_REQUEST['name']) ) {
      echo "<h1>Tracking Autos For: ";
      echo htmlentities($_REQUEST['name']);
      echo "</h1>\n";
    }
    if ( isset($_SESSION['error']) ) {
      echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
    }
    if ($success !== false) {
      echo $success;
    }
    ?>
    <form method="POST">

      <input type="hidden" name="autos_id" value="<?= $autos_id?>"><br/>
      <input type="submit" value="Delete">
      <input type="submit" name="cancel" value="Cancel">
    </form>
    <?php

    // echo "<h1>Automobiles</h1>";
    // $stmt = $pdo->prepare("SELECT make, year, mileage FROM autos");
    // $stmt->execute();
    // echo '<table border="1">'."\n";
    // while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    //   echo "<tr><td>";
    //   echo htmlentities($row['make']);
    //   echo " </td><td>";
    //   echo htmlentities($row['year']);
    //   echo " </td><td>";
    //   echo htmlentities($row['mileage']);
    //   echo " </td></tr>";
    // }
    //
    // // Note triple not equals and think how badly double
    // // not equals would work here...
    // if ( $failure !== false ) {
    //   // Look closely at the use of single and double quotes
    //   echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
    // }
    //

    ?>

  </div>
</body>
</html>
