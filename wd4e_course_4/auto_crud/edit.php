<?php
session_start();
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc',
'fred', 'zap');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ( ! isset($_SESSION["account"]) ) {
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
if ( isset($_POST['mileage']) && isset($_POST['year'])) {
  if(!(is_numeric($_POST['year']))) {
    $_SESSION['error'] = "Year must be an integer";
    header("Location: edit.php");
    return;
  } else if (!is_numeric($_POST['mileage'])) {
    $_SESSION['error'] =  "Mileage must be an integer";
    header("Location: edit.php");
    return;
  } else if(strlen($_POST['make']) < 1 || strlen($_POST['mileage']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['model']) < 1 ) {
    $_SESSION['error'] = "All fields are required";
    header("Location: edit.php");
    return;
  } else {
    try {
      $sql = "UPDATE autos SET make = :ma, year = :ye, model = :mo, mileage = :mi WHERE autos_id = :id";

      $stmt = $pdo->prepare($sql);

      $stmt->execute(array(
        ':ma' => $_POST['make'],
        ':mo' => $_POST['model'],
        ':ye' => $_POST['year'],
        ':mi' => $_POST['mileage'],
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
      $_SESSION['success'] = "Record edited";
      header("Location: index.php");
      return;
    } catch (Exception $ex) {
      echo ("Exception message: ".$ex->getMessage());
      return;
    }
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
      <label for="make">Make   </label>
      <input type="text" name="make" id="make" value="<?= $ma?>"><br/>
      <label for="model">Model</label>
      <input type="text" name="model" id="model" value="<?= $mo?>"><br/>
      <label for="year">Year</label>
      <input type="text" name="year" id="year" value="<?= $ye?>"><br/>
      <label for="mileage">Mileage</label>
      <input type="text" name="mileage" id="mileage" value="<?= $mi?>"><br/>
      <input type="hidden" name="autos_id" value="<?= $autos_id?>"><br/>
      <input type="submit" value="Save">
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
