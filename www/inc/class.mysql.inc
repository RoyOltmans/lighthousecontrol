<?php
class MySQL {
	function mysqldb_query($query) {
		$mysql_host = "localhost";
		$mysql_user = "username";
		$mysql_password = "password";
		$my_database = "lighthousecontrol";
		// Connecting, selecting database
		$link = mysql_connect($mysql_host, $mysql_user, $mysql_password) or die('Could not connect: ' . mysql_error());
		mysql_select_db($my_database) or die('Could not select database');
		$result = mysql_query($query) or die('Query failed: ' . mysql_error());
		while($resultArray[] = mysql_fetch_array($result, MYSQL_ASSOC));
		return $resultArray;
		// Free resultset
		mysql_free_result($result);
		// Closing connection
		mysql_close($link);
	}
}
?>