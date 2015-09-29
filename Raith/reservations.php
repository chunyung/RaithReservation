<?php
  $passed = $_COOKIE{"passed"};
  if ($passed != "TRUE")
  {
    header("location:index.htm");
    exit();
  }	
  else
  {
    require_once("dbtools.inc.php");
    $id = $_COOKIE{"id"};
    $link = create_connection();
    $sql = "SELECT * FROM User WHERE id = $id";
    $result = execute_sql("Raith", $sql, $link);
    $user = mysql_fetch_object($result);
    $sql = "SELECT * FROM Tasks WHERE user = '$user->name'";
    $result = execute_sql("Raith",$sql, $link);
?>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <title>Add Menu</title>
    <SCRIPT LANGUAGE=javascript>
function add_new_menu() {
 var num = document.getElementById("table1").rows.length;
 var Tr = document.getElementById("table1").insertRow(num);
 var user = '<?php echo $user->name ?>';
 Td = Tr.insertCell(Tr.cells.length);
 Td.innerHTML = '<input type="checkbox" name="Date[]" value = 0>Mon  <input type="checkbox" name="Date[]" value = 1>Tue  <input type="checkbox" name="Date[]" value = 2>Wed  <input type="checkbox" name="Date[]" value = 3>Thu  <input type="checkbox" name="Date[]" value = 4>Fri  <input type="checkbox" name="Date[]" value = 5>Sat  <input type="checkbox" name="Date[]" value = 6>Sun</td>';
 Td = Tr.insertCell(Tr.cells.length);
 Td.innerHTML = '<input type=text name="Title[]" value = "' + user + '">';
 Td = Tr.insertCell(Tr.cells.length);
 Td.innerHTML = '<input type=text name="StartH[]" value = "17" maxlength = 2 size = 2> : <input type=text name="StartM[]" value = "00" maxlength = 2 size = 2>';
 Td = Tr.insertCell(Tr.cells.length);
 Td.innerHTML='<input type=text name="EndH[]" value = "06" maxlength = 2 size = 2> : <input type=text name="EndM[]" value = "00" maxlength = 2 size = 2>';
}

</SCRIPT>
</head>
<body>
<center>
<a href="main.php">Back</a><P><H1>Add new reservations</H1>
<form id=addmenu action="updateTasks.php" enctype="multipart/form-data" method="post">
<div>
  <table id=table1 cellSpacing=1 cellPadding=1 width="50%" border=1>
    <tr><td>Date</td><td>Title</td><td>Start</td><td>End</td></tr>
    <tr>
    	<td><input type="checkbox" name="Date[]" value = 0>Mon  <input type="checkbox" name="Date[]" value = 1>Tue  <input type="checkbox" name="Date[]" value = 2>Wed  <input type="checkbox" name="Date[]" value = 3>Thu  
    		<input type="checkbox" name="Date[]" value = 4>Fri  <input type="checkbox" name="Date[]" value = 5>Sat  <input type="checkbox" name="Date[]" value = 6>Sun</td>
    	<td><input type=text name="Title"<?php echo "value = '$user->name'"?></td>
    	<td><input type=text name="StartH" value = "17" maxlength = 2 size = 2> : <input type=text name="StartM" value = "00" maxlength = 2 size = 2></td>
    	<td><input type=text name="EndH" value = "06" maxlength = 2 size = 2> : <input type=text name="EndM" value = "00" maxlength = 2 size = 2></td>
    </tr>
  </table>
 </div>
 </P>
<INPUT id="submit" name="submit" type="submit" value="Submit">
<!--<INPUT type="button" value="Add new menu" onclick="add_new_menu()">-->
</form>
<?php
	if (mysql_num_rows($result) != 0)
	{
		echo "<form id=currmenu action=\"deleteTasks.php\" method=\"post\">";
		echo "<p><H1>Current Reservations</H1></p>";
		echo "<div><table align='center' id=table2 cellSpacing=1 cellPadding=1 width=\"50%\" border=1><tr><td>Choose</td><td>Date</td><td>Title</td><td>Start</td><td>End</td></tr>";
		while ($task = mysql_fetch_object($result))
		{
			$date = $task->weekDate;
			$dateWord = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
			$weekDate = "";
			for ($i = 0; $i < 7; ++$i) {	
				if ((($date >> $i) & 1) == 1) {
					$weekDate = $weekDate . "/" . $dateWord[$i] . " ";
				}
			}
			$weekDate = substr($weekDate, 1, -1);
			echo "<tr>";
			echo "<td><input type=\"checkbox\" name=\"taskId[]\" value=\"$task->id\"></td>";
			echo "<td>$weekDate</td>";
			echo "<td>$task->title</td>";
			echo "<td>$task->start</td>";
			echo "<td>$task->end</td>";
			echo "</tr>";
		}
		echo "</table></div>";
		echo "<INPUT id=\"submit\" name=\"submit\" type=\"submit\" value=\"Delete selected menu\">";
	}
?>
</center>
</body>
</html>
<?php
    mysql_free_result($result);
    mysql_close($link);
  }
?>