<!DOCTYPE html>
<html>
<head>
  <title>Welcome to View Automobiles Yifu Chen (Charles) 6d4e59d5</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <title>Welcome to View Automobiles Yifu Chen (Charles) 6d4e59d5</title>

    <p>
      <?php
      echo "<h1>Automobiles</h1>";
      session_start();
      if (isset($_SESSION["success"])) {
        echo "<div style ='color:green'>".$_SESSION["success"]." </div>";
        unset($_SESSION["success"]);
      }
      if (isset($_SESSION["error"])) {
        echo "<div style ='color:red'>".$_SESSION["error"]." </div>";
        unset($_SESSION["error"]);
      }
      if (isset($_SESSION["account"])) {
        $pdo = new PDO('mysql:host=localhost;port=8889;dbname=misc',
        'fred', 'zap');
        // See the "errors" folder for details...
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare("SELECT autos_id, make, model, year, mileage FROM autos");
        $stmt->execute();
        echo '<table border="1">'."\n";

        $first = true;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if ($first && !$row) {
            echo "<p>No rows found</p>";
            $first = false;
          } else {
            if($first) {
              echo "<tr><td>Make";
              echo " </td><td>Model";
              echo " </td><td>Year";
              echo " </td><td>Mileage";
              echo " </td><td>Action";
              echo "</td></tr>\n";
              $first = false;
            }

            echo "<tr><td>";
            echo htmlentities($row['make']);
            echo " </td><td>";
            echo htmlentities($row['model']);
            echo " </td><td>";
            echo htmlentities($row['year']);
            echo " </td><td>";
            echo htmlentities($row['mileage']);
            echo " </td><td>";
            echo "<a href='edit.php?autos_id=" . $row['autos_id'] ."''" ."> Edit </a>";
            echo "<a href='delete.php?autos_id=" . $row['autos_id'] ."''". "> Delete </a>";
            echo "</td></tr>\n";
          }
        }
        echo "<a href='add.php'>Add New Entry</a><p></p>  ";
        echo "<a href='logout.php'>Logout</a>";
      } else {
        echo "<a href='login.php'>Please log in</a>";
      }
      ?>
    </p>
  </div>
</body>
