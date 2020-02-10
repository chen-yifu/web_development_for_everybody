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


?>
<!DOCTYPE html>
<html>
<head>
  <title>Yifu Chen (Charles) 6d4e59d5</title>
  <?php require_once "bootstrap.php"; ?>

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
</head>
<body>
  <div class="container">
    <?php

    $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=misc',
    'root', 'ubcfm123');
    // See the "errors" folder for details...
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Profiles</h1>";
    $stmt = $pdo->prepare("SELECT * FROM profile where profile_id = :id");
    $stmt->execute(
      array(
        ":id" => $_GET['profile_id']
      )
    );
    echo '<table border="1">'."\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

      echo "<p>First name: ".htmlentities($row['first_name']);
      echo "<p>Last name: ".htmlentities($row['last_name']);
      echo "<p>Email: ".htmlentities($row['email']);
      echo "<p>Headline: ".htmlentities($row['headline']);
      echo "<p>Summary: ".htmlentities($row['summary']);
      echo "<p>";
    }
    $stmt = $pdo->prepare("SELECT * FROM position where profile_id = :id");
    $stmt->execute(
      array(
        ":id" => $_GET['profile_id']
      )
    );

    echo "Position:<ul>";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<li>".$row['year']." : ".$row['description']."</li>";
    }


    echo "</ul>";

    $stmt = $pdo->prepare("SELECT * FROM education where profile_id = :id");
    $stmt->execute(
      array(
        ":id" => $_GET['profile_id']
      )
    );

    echo "Education:<ul>";

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $edu_year = $row['year'];
      $stmt2 = $pdo->prepare("SELECT * FROM institution where institution_id = :iid");
      $stmt2->execute(
        array(
          ":iid" => $row['institution_id']
        )
      );
      $edu_row = $stmt2->fetch(PDO::FETCH_ASSOC);
      echo "<li>".$edu_year." : ".$edu_row['name']."</li>";
    }


    echo "</ul>";
    ?>

    <a href="index.php">Done</a>



  </div>
</body>
</html>
