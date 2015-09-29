<?php
require_once 'google-api-php-client/autoload.php';
require_once("dbtools.inc.php");
$cmd = "ntpdate -u pool.ntp.org";
shell_exec($cmd);
$services = array();
$link = create_connection();
$sql = "SELECT * FROM APP";
$result = execute_sql("Raith", $sql, $link);
$app = mysql_fetch_object($result);
$clientID = $app->clientID;
$clientSecret = $app->clientSecret;
$redirect = $app->redirect;
$calendarID = $app->calendarID;
$scope = 'https://www.googleapis.com/auth/calendar';
$weekDate = date("N") % 7;
$sql = "SELECT DISTINCT user FROM Tasks WHERE ((weekDate >> $weekDate) & 1) = 1";
$result = execute_sql("Raith", $sql, $link);
if (mysql_num_rows($result) != 0) {
	while ($name = mysql_fetch_object($result)) {
		$sql = "SELECT * FROM User WHERE name = '$name->user'";
		$tokens = mysql_fetch_object(execute_sql("Raith", $sql, $link));
		$client = new Google_Client();
		$client->setClientId($clientID);
		$client->setClientSecret($clientSecret);
		$client->setRedirectUri($redirect);
		$client->addScope($scope);
		$client->setAccessToken($tokens->accessToken);
		$authorized = TRUE;
		if ($client->isAccessTokenExpired()) {
			try {
				$client->refreshToken($tokens->token);	
				$accessToken = $client->getAccessToken();
				$sql = "UPDATE User SET accessToken = '$accessToken' WHERE id = '$tokens->id'";
			} catch (Exception $e){
				$authorized = FALSE;
				$sql = "UPDATE User SET accessToken = '', token = '' WHERE id = '$tokens->id'"; 
			}
			execute_sql("Raith", $sql, $link);
		}
		if ($authorized) {
			$services["$name->user"] = new Google_Service_Calendar($client);
		}
	}
}

$events = array();
while ($username = current($services)) {
	$key = key($services);
	$sql = "SELECT * FROM Tasks WHERE user = '$key' AND ((weekDate >> $weekDate) & 1) = 1";
	$result = execute_sql("Raith", $sql, $link);
	$eventList = array();
	while ($task = mysql_fetch_object($result)) {
		$event = new Google_Service_Calendar_Event();
		$event->setSummary($task->title);
		$advanced_day = 7;
		$t = time() + ($advanced_day * 24 * 60 * 60 + 5 * 60 * 60);
		$startTime = $task->start;
		$endTime = $task->end;
		$startDate = date('Y-m-d',$t);
		if (intval(substr($startTime,0,2)) <= intval(substr($endTime,0,2))) {
			$endDate = date('Y-m-d',$t);
		} else {
			$endDate = date('Y-m-d',$t + 24 * 60 * 60);	
		}
		$startTime = $task->start;
		$endTime = $task->end;
		if(date("I",mktime(0, 0, 0, date("m"), date("d") + $advanced_day + 1, date("Y"))) == '1') {
			$tzOffset = "-07";
		} else {
			$tzOffset = "-08";
		}
		$start = new Google_Service_Calendar_EventDateTime();
		$start->setDateTime("{$startDate}T{$startTime}:00.000{$tzOffset}:00");
		$event->setStart($start);
		$end = new Google_Service_Calendar_EventDateTime();
		$end->setDateTime("{$endDate}T{$endTime}:00.000{$tzOffset}:00");
		$event->setEnd($end);
		$eventList[] = $event;
	}
	$events["$key"] = $eventList;
	next($services);
}
if (count($services) > 0) {
	sleep(25);
	$second = date("s");
	while (TRUE)
	{
		if ($second >= "58")
		{
			//usleep(1400000);
			$keys = array_keys($services);
			for ($i = 0, $sizeKey = count($keys); $i < $sizeKey; ++$i) {
				for ($j = 0, $sizeEvent = count($events["$keys[$i]"]); $j < $sizeEvent; ++$j) {
					try {
						$services["$keys[$i]"]->events->insert($calendarID, $events["$keys[$i]"][$j]);	
					} catch (Exception $e) {
						$sql = "UPDATE User SET accessToken = '', token = '' WHERE name = '$keys[$i]'";
						execute_sql("Raith", $sql, $link);
					}
				}
			}
			break;
		}
		$second = date("s");
	}
}
mysql_close($link);
?>