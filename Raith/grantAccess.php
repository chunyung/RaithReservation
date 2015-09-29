<?php
require_once 'google-api-php-client/autoload.php';
require_once("dbtools.inc.php");
$id = $_COOKIE["id"];
$link = create_connection();
$sql = "SELECT * FROM APP";
$result = execute_sql("Raith", $sql, $link);
$app = mysql_fetch_object($result);
$client_id = $app->clientID;
$client_secret = $app->clientSecret;
$redirect_uri = $app->redirect;
$accessType = $app->accessType;
mysql_free_result($result);	
mysql_close($link);
$client = new Google_Client();

$client->setClientId($client_id);
$client->setClientSecret($client_secret);
$client->setRedirectUri($redirect_uri);
$client->addScope('https://www.googleapis.com/auth/calendar');
$client->setAccessType($accessType);
$client->setApprovalPrompt('force');
$service = new Google_Service_Calendar($client);

$authUrl = $client->createAuthUrl();

//Request authorization

if (isset($authUrl)) {
  echo "<a class='login' href='" . $authUrl . "'>Connect Me!</a>";
} else {
  echo <<<END
    <form id="url" method="GET" action="{$_SERVER['PHP_SELF']}">
      <input name="url" class="url" type="text">
      <input type="submit" value="Shorten">
    </form>
    <a class='logout' href='?logout'>Logout</a>
END;
}