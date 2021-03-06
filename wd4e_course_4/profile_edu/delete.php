<?php
session_start();
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=misc',
'root', 'ubcfm123');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ( ! isset($_SESSION["user_id"]) ) {
  die("ACCESS DENIED");
}
// If the user requested logout go back to index.php
if ( isset($_POST['cancel']) ) {
  header('Location: index.php');
  return;
}


$success = false;
$failure = false;
if (isset($_POST['profile_id'])) {
  try {
    $sql = "DELETE FROM profile WHERE profile_id = :id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute(array(
      ':id' => $_POST['profile_id']
    ));

    $_SESSION['success'] = "Record deleted.";
    header("Location: index.php");
    return;
  } catch (Exception $ex) {
    echo ("Exception message: ".$ex->getMessage());
    return;
  }
}


$sql = "SELECT * FROM profile where profile_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  $_SESSION['error'] = 'Bad value for id';
  header("Location: index.php");
  return;
}
$fn = htmlentities($row['first_name']);
$ln = htmlentities($row['last_name']);
$em = htmlentities($row['email']);
$hd = htmlentities($row['headline']);
$su = htmlentities($row['summary']);
$profile_id = $row['profile_id'];

?>
<!DOCTYPE html>
<html>
<head>
  <title>Yifu Chen (Charles) 6d4e59d5</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>

  <div class="container">
    <h1>Deleting Profile</h1>
    <?php

    if ( isset($_SESSION['error']) ) {
      echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
      unset($_SESSION['error']);
    }
    if ($success !== false) {
      echo $success;
    }
    ?>
    <form method="POST">
      <p>First Name: <?= $fn?></p>
      <p>Last Name: <?= $ln?></p>
      <input type="hidden" name="profile_id" value="<?= $profile_id?>"><br/>
      <input type="submit" value="Delete">
      <input type="submit" name="cancel" value="Cancel">
    </form>
    <?php


    ?>

  </div>
</body>
</html>
