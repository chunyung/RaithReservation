<?php
  function create_connection()
  {
    $link = mysql_connect("localhost", “XXXXX”, "XXXXXXXXXX")
      or die("Can't connect to server!<br><br>" . mysql_error());
	  
    mysql_query("SET NAMES utf8");
			   	
    return $link;
  }
	
  function execute_sql($database, $sql, $link)
  {
    $db_selected = mysql_select_db($database, $link)
      or die("Can't connect to database<br><br>" . mysql_error($link));
						 
    $result = mysql_query($sql, $link);
		
    return $result;
  }
?>