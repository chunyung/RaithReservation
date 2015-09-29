<?php
  require_once("dbtools.inc.php");
  
  $account = $_POST["account"];
  $password = $_POST["password"]; 
  $name = $_POST["name"]; 
  $csl = $_POST["csl"]; 
  $link = create_connection();
			
  $sql = "SELECT * FROM User WHERE account = '$account'";
  $result = execute_sql("Raith", $sql, $link);

  if (mysql_num_rows($result) != 0 || $csl != “XXXXXX”)
  {
    mysql_free_result($result);
		
    echo "<script type='text/javascript'>";
	if ($csl != "XXXXXX") {
		echo "alert('CSL verification code error');";
	} else {
		echo "alert('This account has been used, please choose another one');";
	}
    echo "history.back();";
    echo "</script>";
  }

  else
  {
    mysql_free_result($result);
	$escapeName = mysql_real_escape_string($name);
	$escapePass =mysql_real_escape_string($password);
   	$sql = "INSERT INTO User (account, name, pass) VALUES ('$account', '$escapeName', '$escapePass')";
    $result = execute_sql("Raith", $sql, $link);
  }	
  mysql_close($link);
?>
<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Account added successfully</title>
  </head>
  <body bgcolor="#FFFFFF">
    <p align="center"><img src="successful.jpg">       
    <p align="center">Congratulations, your account have been created, following is your information:<br>
    account:<font color="#FF0000"><?php echo $account ?></font><br>
    password:<font color="#FF0000"><?php echo $password ?></font><br>       
    please memorize your account and password and<a href="index.htm"> log into website</a>
    </p>
  </body>
</html>