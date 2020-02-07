<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=misc',
'root', 'ubcfm123');
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
  if(strlen($_POST['make']) < 1 || strlen($_POST['mileage']) < 1 || strlen($_POST['year']) < 1 || strlen($_POST['model']) < 1 ) {
    $_SESSION['error'] = "All fields are required";
    header("Location: add.php");
    return;
  }
  if(!(is_numeric($_POST['year']))) {
    $_SESSION['error'] = "Year must be an integer";
    header("Location: add.php");
    return;
  } else if (!is_numeric($_POST['mileage'])) {
    $_SESSION['error'] =  "Mileage must be an integer";
    header("Location: add.php");
    return;
  } else {
    try {
      $stmt = $pdo->prepare("INSERT INTO autos
        (make, model, year, mileage) VALUES ( :mk, :mo, :yr, :mi)");

        $res = $stmt->execute(array(
          ':mk' => $_POST['make'],
          ':mo' => $_POST['model'],
          ':yr' => $_POST['year'],
          ':mi' => $_POST['mileage'])
        );
        $_SESSION['success'] = "Record inserted, record added";
        header("Location: index.php");
        return;
      } catch (Exception $ex) {
        echo ("Exception message: ".$ex->getMessage());
        return;
      }
    }
  }

  ?>
  <!DOCTYPE html>
  <html>
  <head>
    <title>Yifu Chen (Charles) 6d4e59d5</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
  <body>

    <div class="container">
      <h1>Adding Automobiles</h1>
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
        <input type="text" name="make" id="make"><br/>
        <label for="model">Model</label>
        <input type="text" name="model" id="model"><br/>
        <label for="year">Year</label>
        <input type="text" name="year" id="year"><br/>
        <label for="mileage">Mileage</label>
        <input type="text" name="mileage" id="mileage"><br/>

        <input type="submit" value="Add">
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
