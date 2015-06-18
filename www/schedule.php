<?php
include "inc/class.mysql.inc";
$objMySQL = new MySQL;
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="no-store" />
	<title>Schedule</title>
	<link rel="shortcut icon" href="favicon.ico">
    <link rel="stylesheet" href="http://fonts.googleapis.com/css?family=Open+Sans:300,400,700">
	<link rel="stylesheet" href="css/themes/default/jquery.mobile-1.4.4.min.css">
	<link rel="stylesheet" href="_assets/css/jqm-demos.css">
	<link rel="stylesheet" href="css/swipe.css">
	<link rel="stylesheet" href="css/style.css">
	<link id="custom-label-flipswitch" rel="stylesheet" href="css/custom-label-flipswitch.css">
	<script src="js/jquery.js"></script>
	<script src="_assets/js/index.js"></script>
	<script src="js/jquery.mobile-1.4.4.min.js"></script>
	<script src="js/mobileinit.js"></script> 
	<script src="js/panel-swipe.js"></script>
</head>
<body>
<div data-role="page" data-role="page" id="page-swipe">
    <div data-role="header" data-theme="b" data-position="fixed">
        <h1>Schedule</h1>
        <a href="#left-panel" data-icon="grid" data-iconpos="notext" data-shadow="false" data-iconshadow="false" class="ui-nodisc-icon">Open left panel</a>
    </div><!-- /header -->
	<div data-role="main" class="ui-content">
		<div class="content-primary" >
			<table data-role="table" class="ui-responsive">
			<thead>
				<tr>
					<th>Description</th>
					<th>Date</th>
					<th>Time</th>
					<th>State</th>
				</tr>
			</thead>
			<tbody>
<?php
$queryResult = $objMySQL->mysqldb_query('SELECT taskdesc, date, time, state FROM kaku.kaku_schedule ORDER BY time, taskdesc, date, time ASC');
foreach ($queryResult as $row){
	if ($row['taskdesc'] == '') {
		break;
	}
	print "			<tr>". PHP_EOL;
	print "				<td>".$row['taskdesc']."</td>". PHP_EOL;
	print "				<td>".$row['date']."</td>". PHP_EOL;
	print "				<td>".$row['time']."</div>". PHP_EOL;
	print "				<td>".$row['state']."</td>". PHP_EOL;
	print "			</tr>". PHP_EOL;
					
}?>			</tbody>
			</table>
		</div>
	</div><!-- /main -->
	<div data-role="footer">
		<br/><center><font size=1>Copyright Roy Oltmans<br/>Thanks to KaKu and <a href="https://launchpad.net/pykaku">Pykaku</a><br/></font></center>
		 <h1 id="notification"></h1>
	</div><!-- /footer -->
<?php
	include_once("inc/menu.inc");
?></div><!-- /page -->
</body>
</html>
