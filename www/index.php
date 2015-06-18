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
	<title>Main</title>
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
        <h1>Main</h1>
        <a href="#left-panel" data-icon="grid" data-iconpos="notext" data-shadow="false" data-iconshadow="false" class="ui-nodisc-icon">Open left panel</a>
    </div><!-- /header -->
	<div data-role="main" class="ui-content">
		<div class="content-primary" >
			<form id="kakuform" method="post" action="/">
			<ul data-role="listview" data-filter="true" data-input="#filterBasic-input" data-icon="false" class="table">
<?php
$queryResult = $objMySQL->mysqldb_query('SELECT kks.id, kkcd.desc, kks.channel, kks.scene, kks.level, kkcd.locked 
                                         FROM kaku.kaku_channel_desc AS kkcd 
										 RIGHT JOIN kaku.kaku_status AS kks ON kkcd.channel = kks.channel');
foreach ($queryResult as $row){
	if ($row['id'] == '') {
		break;
	}
	print "			<li><a href=\"#\"><div class=\"ui-field-contain\">". PHP_EOL;
	print "				<label for=\"flip-".$row['id']."\">".$row['desc'].": </label>". PHP_EOL;
	if ($row['locked'] == 1) {
		print "				<select name=\"flip-".$row['id']."\" class=\"flip\" id=\"flip-".$row['id']."\" data-role=\"flipswitch\" data-mini=\"true\" disabled=\"disabled\">". PHP_EOL;
	} else {
		print "				<select name=\"flip-".$row['id']."\" class=\"flip\" id=\"flip-".$row['id']."\" data-role=\"flipswitch\" data-mini=\"true\">". PHP_EOL;
	}
	if ($row['level'] == 0) {
		print "					<option value=\"".$row['channel'].", off\" selected=\"\">Off</option>". PHP_EOL;
		print "					<option value=\"".$row['channel'].", on\">On</option>". PHP_EOL;
	} elseif ($row['level'] == 17) {
		print "					<option value=\"".$row['channel'].", off\">Off</option>". PHP_EOL;
		print "					<option value=\"".$row['channel'].", on\" selected=\"\">On</option>". PHP_EOL;
	}
	print "				</select>". PHP_EOL;
	print "			</div></a></li>". PHP_EOL;
}?>			</ul>
			</form>
		</div>
	</div><!-- /main -->
	<div data-role="footer">
		<br/><center><font size=1>Copyright Roy Oltmans<br/>Thanks to KaKu and <a href="https://launchpad.net/pykaku">Pykaku</a><br/></font></center>
		 <h1 id="notification"></h1>
	</div><!-- /footer -->
<?php
	include_once("inc/menu.inc");
?></div><!-- /page -->
<script>
	//<![CDATA[
	$('.flip').bind('change', function() {
		var paramater = jQuery(this).val().split(","); 
		$("#notification").append('Chanf channel: ' + paramater[0] + ' Action: ' + paramater[1] + '<BR>');
		$.post("api/", "kanaal="+paramater[0]+"&actie="+paramater[1],
		function(data){}); 
	});
	//]]>
</script>
</body>
</html>
