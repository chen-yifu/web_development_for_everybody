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


if ( isset($_POST['first_name']) && isset($_POST['last_name'])) {
  if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1
  || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1
  || strlen($_POST['summary']) < 1 ) {

    $_SESSION['error'] = "All fields are required";
    header("Location: edit.php?profile_id=".$profile_id);
    return;
  } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
    $_SESSION["error"] = "Email must have an at-sign (@)";
    error_log("Edit fail ".$_POST['email']." $check");
    header("Location: edit.php?profile_id=".$profile_id);
    return;
  } try {
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
    $msg = validatePos();
    if (is_string($msg)) {
      $_SESSION['error'] = $msg;
      header("Location: edit.php?profile_id=".$profile_id);
      return;
    }

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

      $stmt = $pdo->prepare("DELETE FROM position WHERE profile_id = :id");

      $res = $stmt->execute(array(
        ':id'=>$_POST['profile_id']
      ));

      $stmt2 = $pdo->prepare("DELETE FROM education WHERE profile_id = :id");
      $res = $stmt2->execute(array(
        ':id'=>$_POST['profile_id']
      ));

      $rank = 1;

      for ($i = 1; $i<=40; $i++) {
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


        $rank = 1;
        for ($i = 1; $i<=40; $i++) {
          error_log("111");
          if(!isset($_POST['edu_year'.$i])) continue;
          error_log("222");
          if(!isset($_POST['edu_school'.$i])) continue;
          error_log("333");
          $edu_year = $_POST['edu_year'.$i];
          $school_name = $_POST['edu_school'.$i];
          $stmt = $pdo->prepare('SELECT * FROM institution WHERE name = :sname');
          $stmt->execute(array(
            ':sname' => $school_name
          )
        );

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$row) {
          $stmt = $pdo->prepare('INSERT INTO institution (name) VALUES (:sname)');
          $stmt->execute(
            array(
              ':sname' => $school_name
            )
          );
          $school_id = $pdo->lastInsertId();
        } else {
          $school_id = $row['institution_id'];
        }
        $stmt = $pdo->prepare('INSERT INTO education
          (profile_id, `rank`, year, institution_id)
          VALUES (:pid, :rank, :year, :iid)');
          $stmt->execute(array(
            ':pid' => $profile_id,
            ':rank' => $rank,
            ':year' => $edu_year,
            ':iid' => $school_id
          )
        );
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
        <p>
          Position: <input type="submit" id='addPos' value="+">
          <div id='position_fields'>
          </div>
          Education: <input type="submit" id='addEdu' value="+">
          <div id='education_fields'>
          </div>

          <?php
          $sql = "SELECT * FROM position where profile_id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(":id" => $_GET['profile_id']));
          if($row === false) {
            // $_SESSION['error'] = 'Bad value for id';
            // header("Location: index.php");
            // return;
          }
          $countPos = 20;
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo '<div id="position'.$countPos.'" >
            <p> Year: <input type="text" name="year'.$countPos.'" value="'.$row['year'].'" />
            <input type="button" value="-"
            onclick="$(\'#position'.$countPos.'\').remove();return false;"></p>
            <textarea name="desc'.$countPos.'" rows="8" cols="80">'.$row['description'].'</textarea>
            </div>';
            $countPos++;
          }


          $sql = "SELECT * FROM education where profile_id = :id";
          $stmt = $pdo->prepare($sql);
          $stmt->execute(array(":id" => $_GET['profile_id']));

          if($row === false) {
            // $_SESSION['error'] = 'Bad value for id';
            // header("Location: index.php");
            // return;
          }


          $countPos = 20;
          while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $profile_id = $row['profile_id'];
            $school_id = $row['institution_id'];
            $edu_year = $row['year'];
            $stmt2 = $pdo->prepare("SELECT * FROM institution WHERE institution_id = :id");
            $stmt2->execute(array(
              ":id" => $school_id
            ));
            $edu_row = $stmt2->fetch(PDO::FETCH_ASSOC);
            $school_name = $edu_row['name'];
            echo '<div id="education'.$countPos.'" >
            <p> Year: <input type="text" name="edu_year'.$countPos.'" value="'.$edu_year.'" />
            <input type="button" value="-"
            onclick="$(\'#education'.$countPos.'\').remove();return false;"></p>
            School: <input type="text" size="80" name="edu_school'.$countPos.'" class="school" value="'.$school_name.'" />
            </div>';
            $countPos++;
          }
          ?>
        </p>
        <input type="hidden" name="profile_id" value="<?= $profile_id?>"><br/>

        <input type="submit" value="Save">
        <input type="submit" name="cancel" value="Cancel">
        <p></p>
      </form>

      <script>

      countPos = 0;
      countEdu = 0;

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
          }
        )

        $('#addEdu').click(function(event) {
          event.preventDefault();
          if(countEdu >= 9) {
            alert("Maximum of nine education entries exceeded");
            return;
          }
          countEdu++;
          window.console && console.log('Adding education' + countEdu);
          $('#education_fields').append(
            '<div id="education'+countEdu+'" > \
            <p> Year: <input type="text" name="edu_year'+countEdu+'" value="" /> \
            <input type="button" value="-" \
            onclick="$(\'#education'+countEdu+'\').remove();return false;"></p> \
            School: <input type="text" size="80" name="edu_school'+countEdu+'" class="school" value="" /> \
            </div>');

            $('.school').autocomplete({
              source: "school.php"
            });
          }
        )

      }
    );



    </script>

    <?php


    ?>

  </div>
</body>
</html>
