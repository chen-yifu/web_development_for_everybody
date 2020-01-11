<html>

<head>
  <title>Hello World</title>
</head>
<body>
  <p>Hi!</p>
  <form action="process.php" method="post">
    What is your name: <input name="name" type="text">
    <input type="submit">
  </form>
  <?php

  $loggedIn = true;
  if ($loggedIn == true) {
    echo "Yifu is logged in";
  } else {
    echo "Yifu is not logged in";
  }

  $people = array("Alice", "Bob", "Catherine");
  $numbers = array(5,3,7);
  $sum = 0;
  foreach($numbers as $num) {
    $sum += $num;

  }
  echo " ". $sum;
  ?>

</body>
</html>
