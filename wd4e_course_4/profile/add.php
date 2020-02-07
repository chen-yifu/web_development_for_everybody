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

if ( isset($_POST['first_name']) && isset($_POST['last_name'])) {
  if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
  || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
  || strlen($_POST['summary']) < 1 ) {

    $_SESSION['error'] = "All fields are required";
    header("Location: add.php");
    return;
  } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $_SESSION["error"] = "Email must have an at-sign (@)";
    error_log("Login fail ".$_POST['email']." $check");
    header("Location: add.php");
    return;
  }
  // if(!(is_numeric($_POST['year']))) {
  //   $_SESSION['error'] = "Year must be an integer";
  //   header("Location: add.php");
  //   return;
  // } else if (!is_numeric($_POST['mileage'])) {
  //   $_SESSION['error'] =  "Mileage must be an integer";
  //   header("Location: add.php");
  //   return;
  // }
  else {
    try {
      $stmt = $pdo->prepare("INSERT INTO profile
        (user_id, first_name, last_name, email, headline, summary)
        VALUES ( :uid, :fi, :la, :em, :he, :su)");

        $res = $stmt->execute(array(
          ':uid' => $_SESSION['user_id'],
          ':fi' => $_POST['first_name'],
          ':la' => $_POST['last_name'],
          ':em' => $_POST['email'],
          ':he' => $_POST['headline'],
          ':su' => $_POST['summary'])
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
        <label for="fn">First name   </label>
        <input type="text" name="first_name" size="60" id="fn"><br/>
        <label for="ln">Last name</label>
        <input type="text" name="last_name" size="60" id="ln"><br/>
        <label for="em">Email</label>
        <input type="text" name="email" size="40" id="em"><br/>
        <label for="hd">Headline</label>
        <input type="text" name="headline" size="100" id="hd"><br/>
        <label for="su">Summary</label>
        <input type="text" name="summary" rows="8" size="80" id="su"><br/>
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
