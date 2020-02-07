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
    header("Location: edit.php");
    return;
  } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $_SESSION["error"] = "Email must have an at-sign (@)";
    error_log("Edit fail ".$_POST['email']." $check");
    header("Location: edit.php");
    return;
  } try {
    $stmt = $pdo->prepare("UPDATE profile Set
      first_name = :fi, last_name = :la,
      email = :em, headline = :he,
      summary = :su, user_id = :uid where profile_id = :id");

      $res = $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fi' => $_POST['first_name'],
        ':la' => $_POST['last_name'],
        ':em' => $_POST['email'],
        ':he' => $_POST['headline'],
        ':su' => $_POST['summary'],
        ':id' => $_POST['profile_id'])
      );

      $_SESSION['success'] = 'Record edited';
      header("Location: index.php");
      return;
    } catch (Exception $ex) {
      echo ("Exception message: ".$ex->getMessage());
      return;
    }
  }
//
//   $row = $stmt->fetch(PDO::FETCH_ASSOC);
//   if($row === false) {
//     $_SESSION['error'] = 'Bad value for id';
//     header("Location: index.php");
//   }
//
//   $fn = htmlentities($row['first_name']);
//   $ln = htmlentities($row['last_name']);
//   $em = htmlentities($row['email']);
//   $hd = htmlentities($row['headline']);
//   $su = htmlentities($row['summary']);
//   $profile_id = $row['profile_id'];
//
// }

$sql = "SELECT * FROM profile where profile_id = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(array(":id" => $_GET['profile_id']));
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if($row === false) {
  // $_SESSION['error'] = 'Bad value for id';
  // header("Location: index.php");
  // return;
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
    <h1>Editing Profile</h1>
    <?php
    // if ( isset($_REQUEST['name']) ) {
    //   echo "<h1>Tracking Autos For: ";
    //   echo htmlentities($_REQUEST['name']);
    //   echo "</h1>\n";
    // }
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
      <input type="text" name="first_name" size="60" id="fn" value="<?= $fn?>"><br/>
      <label for="ln">Last name</label>
      <input type="text" name="last_name" size="60" id="ln" value="<?= $ln?>"><br/>
      <label for="em">Email</label>
      <input type="text" name="email" size="40" id="em" value="<?= $em?>"><br/>
      <label for="hd">Headline</label>
      <input type="text" name="headline" size="100" id="hd" value="<?= $hd?>"><br/>
      <label for="su">Summary</label>
      <input type="text" name="summary" rows="8" size="80" id="su" value="<?= $su?>"><br/>
      <input type="hidden" name="profile_id" value="<?= $profile_id?>"><br/>
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
