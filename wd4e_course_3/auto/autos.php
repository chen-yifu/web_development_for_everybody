<?php

$pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc',
'fred', 'zap');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


// Demand a GET parameter
// if ( ! isset($_GET['name']) || strlen($_GET['name']) < 1  ) {
//   $failure = "User name and password are required";
// }

// If the user requested logout go back to index.php
if ( isset($_POST['logout']) ) {
  header('Location: index.php');
  return;
}
$success = false;
$failure = false;
if ( isset($_POST['mileage']) && isset($_POST['year'])) {
  if(!(is_numeric($_POST['mileage']) && is_numeric($_POST['year']))) {
    $failure = "Mileage and year must be numeric";
    // $success =
  } else if(strlen($_POST['make']) < 1) {
    $failure = "Make is required";

  } else {
    try {
      $stmt = $pdo->prepare("INSERT INTO autos
        (make, year, mileage) VALUES ( :mk, :yr, :mi)");

        $res = $stmt->execute(array(
          ':mk' => $_POST['make'],
          ':yr' => $_POST['year'],
          ':mi' => $_POST['mileage'])
        );
        $success =  "<div style ='color:green'> Record Inserted.</div>";
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
    <title>Yifu Chen (Charles) c12437a1</title>
    <?php require_once "bootstrap.php"; ?>
  </head>
  <body>
    <div class="container">
      <?php
      if ( isset($_REQUEST['name']) ) {
        echo "<h1>Tracking Autos For: ";
        echo htmlentities($_REQUEST['name']);
        echo "</h1>\n";
      }
      if ($success !== false) {
        echo $success;
      }
      ?>
      <form method="POST">
        <label for="nam">Make</label>
        <input type="text" name="make" id="make"><br/>
        <label for="year">Year</label>
        <input type="text" name="year" id="year"><br/>
        <label for="mileage">Mileage</label>
        <input type="text" name="mileage" id="mileage"><br/>
        <input type="submit" value="Add">
        <input type="submit" name="logout" value="Logout">
      </form>
      <?php

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

      // Note triple not equals and think how badly double
      // not equals would work here...
      if ( $failure !== false ) {
        // Look closely at the use of single and double quotes
        echo('<p style="color: red;">'.htmlentities($failure)."</p>\n");
      }


      ?>

    </div>
  </body>
  </html>
