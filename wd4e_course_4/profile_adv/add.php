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
    $msg = validatePos();
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: add.php");
      return;
    }

    // Data is valid - time to insert!!!

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
        $profile_id = $pdo->lastInsertId();


        $rank = 1;
        for ($i = 1; $i<=9; $i++) {
          if(!isset($_POST['year'.$i])) continue;
          if(!isset($_POST['desc'.$i])) continue;
          $year = $_POST['year'.$i];
          $desc = $_POST['desc'.$i];

          $stmt = $pdo->prepare('INSERT INTO position
            (profile_id, `rank`, year, description)
            VALUES (:pid, :rank, :year, :desc)');

            $stmt->execute(array(
              ':pid' => $profile_id,
              ':rank' => $rank,
              ':year' => $year,
              ':desc' => $desc
            ));
            $rank++;
          }

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
        <h1>Adding Profile</h1>

        <?php
        function validatePos() {
          for($i=1; $i<=9; $i++) {
            if ( ! isset($_POST['year'.$i]) ) continue;
            if ( ! isset($_POST['desc'.$i]) ) continue;
            $year = $_POST['year'.$i];
            $desc = $_POST['desc'.$i];
            if ( strlen($year) == 0 || strlen($desc) == 0 ) {
              return "All fields are required";
            }
            if ( ! is_numeric($year) ) {
              return "Position year must be numeric";
            }
          }
          return true;
        }
        if ( isset($_SESSION['error']) ) {
          echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
          unset($_SESSION['error']);
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
          <p>
            Position: <input type="submit" id='addPos' value="+">
            <div id='position_fields'>
            </div>
          </p>
          <input type="submit" value="Add">
          <input type="submit" name="cancel" value="Cancel">
        </form>
        <script>
        countPos = 0;

        $(document).ready(function() {
          window.console && console.log('Document ready called');
          $('#addPos').click(function(event) {
            event.preventDefault();
            if(countPos >= 9) {
              alert("Maximum of nine position entries exceeded");
              return;
            }
            countPos++;
            window.console && console.log('Adding position' + countPos);
            $('#position_fields').append(
              '<div id="position'+countPos+'" > \
              <p> Year: <input type="text" name="year'+countPos+'" value="" /> \
              <input type="button" value="-" \
              onclick="$(\'#position'+countPos+'\').remove();return false;"></p> \
              <textarea name="desc'+countPos+'" rows="8" cols="80"></textarea> \
              </div>');
            })
          })

          </script>
          <?php

          ?>

          </div>
          </body>
          </html>
