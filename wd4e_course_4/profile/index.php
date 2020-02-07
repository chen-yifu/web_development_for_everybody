<!DOCTYPE html>
<html>
<head>
  <title>Welcome to View Profiles Yifu Chen (Charles) 6d4e59d5</title>
  <?php require_once "bootstrap.php"; ?>
</head>
<body>
  <div class="container">
    <title>Welcome to View Profiles Yifu Chen (Charles) 6d4e59d5</title>

    <p>
      <?php
      echo "<h1>Profiles</h1>";
      session_start();
      if (isset($_SESSION["success"])) {
        echo "<div style ='color:green'>".$_SESSION["success"]." </div>";
        unset($_SESSION["success"]);
      }
      if (isset($_SESSION["error"])) {
        echo "<div style ='color:red'>".$_SESSION["error"]." </div>";
        unset($_SESSION["error"]);
      }

      if (isset($_SESSION["user_id"])) {
        $pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=misc',
        'root', 'ubcfm123');
        // See the "errors" folder for details...
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


        $stmt = $pdo->prepare("SELECT profile_id, user_id, first_name, last_name, headline FROM profile");
        $stmt->execute();
        echo '<table border="1">'."\n";

        $first = true;
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
          if ($first && !$row) {
            echo "<p>No rows found</p>";
            $first = false;
          } else {
            if($first) {
              echo "<tr><td>Name";
              echo " </td><td>Headline";
              echo " </td><td>Action";
              echo "</td></tr>\n";
              $first = false;
            }

            echo "<tr><td>";
            echo htmlentities($row['first_name']);
            echo " ";
            echo htmlentities($row['last_name']);
            echo " </td><td>";
            echo htmlentities($row['headline']);
            // echo " </td><td>";
            // echo htmlentities($row['year']);
            // echo " </td><td>";
            // echo htmlentities($row['mileage']);
            echo " </td><td>";
            echo "<a href='edit.php?profile_id=" . $row['profile_id'] ."''" ."> Edit </a>";
            echo "<a href='delete.php?profile_id=" . $row['profile_id'] ."''". "> Delete </a>";
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
