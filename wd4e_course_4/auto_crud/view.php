<?php
session_start();
$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc',
'fred', 'zap');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if ( ! isset($_SESSION["account"]) ) {
  die('Not logged in');
}

// If the user requested logout
if ( isset($_POST['logout']) ) {
  header('Location: logout.php');
  return;
}
if (isset($_POST['add'])) {
  if (isset($_SESSION['account'])) {
    header("Location: add.php");
    return;
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
    <?php
    if (isset($_SESSION["inserted"]) == "Record inserted") {
      echo "<div style ='color:green'> Record Inserted.</div>";
      unset($_SESSION["inserted"]);
    }
    $pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc',
    'fred', 'zap');
    // See the "errors" folder for details...
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "<h1>Automobiles</h1>";
    $stmt = $pdo->prepare("SELECT make, year, mileage FROM autos");
    $stmt->execute();
    echo '<table border="1">'."\n";
    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
      echo "<tr><td>";
      echo htmlentities($row['make']);
      echo " </td><td>";
      echo htmlentities($row['year']);
      echo " </td><td>";
      echo htmlentities($row['mileage']);
      echo " </td></tr>";
    }
    ?>

    <a href="add.php">Add New</a>
    <a href="logout.php">Logout</a>


  </div>
</body>
</html>
