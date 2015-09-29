<?php
	$passed = $_COOKIE["passed"];
	if ($passed != "TRUE")
	{
		header("location:index.htm");
    	exit();
  	}
  	require_once("dbtools.inc.php");
  	$id = $_COOKIE["id"];
  	$link = create_connection();
  	$sql = "SELECT * FROM User WHERE id = '$id'";
  	$result = execute_sql("Raith", $sql, $link);
  	$user = mysql_fetch_object($result);
	require_once 'google-api-php-client/autoload.php';
  	$sql = "SELECT * FROM APP";
  	$result = execute_sql("Raith", $sql, $link);
  	$app = mysql_fetch_object($result);
  	$client_id = $app->clientID;
  	$client_secret = $app->clientSecret;
	$redirect_uri = $app->redirect;
	$accessType = $app->accessType;
	$client = new Google_Client();
	$client->setClientId($client_id);
	$client->setClientSecret($client_secret);
	$client->setRedirectUri($redirect_uri);
	$client->addScope('https://www.googleapis.com/auth/calendar');
	$client->setAccessType($accessType);
	$client->setApprovalPrompt('force');
	$authUrl = $client->createAuthUrl();
  	if ($user->token == "") {
  		header("location:$authUrl");	
    	mysql_close($link);	
  	}
  	$sql = "SELECT * FROM Tasks WHERE user = '$user->name'";
  	$result = execute_sql("Raith",$sql, $link);
  	mysql_close($link);	
?>
<html>
  <head>
    <title>Account Management</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  </head>
  <body>
    <p align="center"><img src="reservation.gif"></p>
    <p align="center">
      <!--<a href="<?php echo $authUrl ?>">Grant Google access</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
      <a href="logout.php">Log out</a>
    </p>
    <center>
<P><H1>Add new reservations</H1>
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
		echo "<p><H1>Current Reservations</H1>";
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
		echo "</table></div></p>";
		echo "<INPUT id=\"submit\" name=\"submit\" type=\"submit\" value=\"Delete selected menu\">";
	}
?>
</center>
  </body>
</html>