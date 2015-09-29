<?php
function validHour($hour) {
	return (intval($hour) >= 0 && intval($hour) <= 23);
}
function validMin($min) {
	return (intval($min) >= 0 && intval() <= 59);
}
function checkValid($startH, $startM, $endH, $endM) {
	if (!is_numeric($startH) || !is_numeric($startM) || !is_numeric($endH) || !is_numeric($endM)) {
		return FALSE;
	}
	if (!validHour($startH) || !validHour($endH) || !validMin($endM) || !validMin($startM)) {
		return FALSE;
	}
	return TRUE;
}
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
	$Date = $_POST["Date"];
	$title = $_POST["Title"];
	if (checkValid($_POST["StartH"], $_POST["StartM"], $_POST["EndH"], $_POST["EndM"])) {
		$start = $_POST["StartH"]. ':' . $_POST["StartM"];
		$end = $_POST["EndH"]. ':' . $_POST["EndM"];
		$size = count($Date);
		if ($size > 0) {
			for ($i = 0; $i < $size; ++$i) {
				$weekDate += 1 << $Date[$i];
			}
			$link = create_connection();
			$sql = "SELECT name FROM User WHERE id = '$id'";
			$result = execute_sql("Raith", $sql, $link);
			$user = mysql_fetch_object($result)->name;
			$sql = "INSERT INTO Tasks (user, weekDate, title, start, end) VALUES ('$user', '$weekDate', '$title', '$start', '$end')";
			$result = execute_sql("Raith", $sql, $link);
			mysql_close($link);
		}
	}
	header("location:main.php");
}
?>