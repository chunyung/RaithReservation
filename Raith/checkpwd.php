<?php
  require_once("dbtools.inc.php");
  header("Content-type: text/html; charset=utf-8");
  $account = $_POST["account"]; 	
  $password = $_POST["password"];
  $link = create_connection();
  $sql = "SELECT * FROM User Where account = '$account' AND pass = '$password'";
  $result = execute_sql("Raith", $sql, $link);
  if (mysql_num_rows($result) == 0)
  {
    mysql_free_result($result);	
    mysql_close($link);		
    echo "<html>";
    echo "<script type='text/javascript'>";
    echo "alert('Password and account aren\'t matched, please login again!');";
    echo "history.back();";
    echo "</script></html>";
  }
  else
  {
    $id = mysql_result($result, 0, "id");
    mysql_free_result($result);	
    mysql_close($link);				
    setcookie("id", $id);
    setcookie("passed", "TRUE");		
    header("location:main.php");		
  }
?>