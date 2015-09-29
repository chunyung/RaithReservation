<?php
require_once 'google-api-php-client/autoload.php';
require_once("dbtools.inc.php");
$client = new Google_Client();
$id = $_COOKIE["id"];
$link = create_connection();
$sql = "SELECT * FROM APP";
$result = execute_sql("Raith", $sql, $link);
$app = mysql_fetch_object($result);
$client_id = $app->clientID;
$client_secret = $app->clientSecret;
$redirect_uri = $app->redirect;
$calendarID = $app->calendarID;

$client = new Google_Client();
$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope('https://www.googleapis.com/auth/calendar');

$Tokens = $client->authenticate($_GET['code']);
$accessToken = $client->getAccessToken();
$google_token= json_decode($Tokens);
$service = new Google_Service_Calendar($client);
$calendars = $service->calendarList->listCalendarList()->getItems();
$found = FALSE;
for ($i = 0, $size = count($calendars); $i < $size; ++$i) {
	if ($calendars[$i]['id'] == $calendarID) {
		$found = TRUE;
		break;
	}
}
if ($found) {
	$sql = "UPDATE User SET token = '$google_token->refresh_token', accessToken = '$accessToken' WHERE id = '$id'";
	$result = execute_sql("Raith", $sql, $link);
	mysql_close($link);
	header("Location:main.php");
} else {
	$sql = "UPDATE User SET token = '', accessToken = '' WHERE id = '$id'";
	$result = execute_sql("Raith", $sql, $link);
	mysql_close($link);
	echo "<p>You don't have Raith calendar Access, please contact super-user to grant you calendar access!<br></p>";
	echo "<p>Or if you have multiple Google accounts, please grant access with the one which can access Raith calendar!<br></p>";
	echo "<p>Once you get the Raith calendar access, please log-in again!<br></p>";
	echo "<a href='index.htm'>Go back to log-in page</a>";
}
//header("location:main.php");
?>