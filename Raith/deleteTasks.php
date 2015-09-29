<?php
$passed = $_COOKIE["passed"];
if ($passed != "TRUE")
{
	header("location:index.php");
	exit();
}
else
{
	require_once("dbtools.inc.php");
	$id = $_COOKIE["id"];
	$taskId = $_POST["taskId"];
	$link = create_connection();
	$num = count($taskId);
	for ($i = 0; $i < $num; ++$i) {
		$sql = "DELETE FROM Tasks WHERE id = '$taskId[$i]'";
		$result = execute_sql("Raith", $sql, $link);
	}
	mysql_close($link);
	header("location:main.php");
}
?>