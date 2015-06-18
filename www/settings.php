<?php
//DEBUG AREA-----------------------------------------
//
//error_reporting(E_ALL);
//ini_set("display_errors", 1); 
//print "<pre>";
//print_r($_POST);
//print "</pre>";
//
//DEBUG AREA-----------------------------------------
include "inc/class.mysql.inc";
include "inc/class.postget.inc";
$objMySQL = new MySQL;
$objPostGet = new PostGet;

//Set variables
$registermessage = $pchangedevice = $pregisterdevice = $pchannel = $pminchannel = $pscene = $plevel = $pdesc = $plocked = $pkksid = $ptype = $gchannel = $glevel = $gtest = $gdelete = '';

//Get Post variables
if(isset($_POST['changedevice'])) $pchangedevice = $_POST['changedevice'];
if(isset($_POST['registerdevice'])) $pregisterdevice = $_POST['registerdevice'];
if(isset($_POST['channel'])) $pchannel = $_POST['channel'];
if(isset($_POST['minchannel'])) $pminchannel = $_POST['minchannel'];
if(isset($_POST['scene'])) $pscene = $_POST['scene'];
if(isset($_POST['level'])) $plevel = $_POST['level'];
if(isset($_POST['desc'])) $pdesc = $_POST['desc'];
if(isset($_POST['locked'])) $plocked = $_POST['locked'];
if(isset($_POST['kksid'])) $pkksid = $_POST['kksid'];
if(isset($_POST['type'])) $ptype = $_POST['type'];

//Get Get variables
if(isset($_GET['channel'])) $gchannel = $_GET['channel'];
if(isset($_GET['level'])) $glevel = $_GET['level'];
if(isset($_GET['test'])) $gtest = $_GET['test'];
if(isset($_GET['delete'])) $gdelete = $_GET['delete'];

//Form property for channels
$maxChannel = 0;										 

//set base url api
$urlBase = "http://".getenv(HTTP_HOST);
$urlBaseApi = $urlBase."/api/";

//If device type is switch and level is not 17 or 0 then set 17 (because it has two states 17 on or 0 off
if ($ptype == 0 and $plevel != 0 and $plevel != 17) {
	$plevel = 17;
} elseif ($ptype > 0) {
	$plevel = 0;
}

//DEVICE C.U.D.
if ($pchangedevice == 1) {
	//Update Device in DB
	$objMySQL->mysqldb_query('UPDATE kaku.kaku_status
                              SET level='.$plevel.', scene='.$pscene.' WHERE channel='.$pchannel.'');
	$objMySQL->mysqldb_query('UPDATE kaku.kaku_channel_desc
                              SET `desc`=\''.$pdesc.'\', locked='.$plocked.', type='.$ptype.' 
							  WHERE channel='.$pchannel);
			//---Set new value
			$data = array('kanaal' => $pchannel, 'actie' => $plevel);
			$result = $objPostGet->PostData($urlBaseApi,$data); 
} elseif ($pregisterdevice == 1) {
	if ($pminchannel <= $pchannel and $pchannel <= 249) {
		if ($pdesc != '') {
			if ($ptype == 1) {
				$plevel = '11';
			} else {
				$plevel = '17';
			}
			//Create device in DB
			$objMySQL->mysqldb_query('INSERT INTO kaku.kaku_status (channel,scene,level) VALUES ('.$pchannel.','.$pscene.',0)');
			$objMySQL->mysqldb_query('INSERT INTO kaku.kaku_channel_desc (`desc`,channel,locked,type) VALUES (\''.$pdesc.'\','.$pchannel.','.$plocked.','.$ptype.')');
			//---register device physically  Via Post to the API
			$data = array('kanaal' => $pchannel, 'actie' => $plevel);
			$result = $objPostGet->PostData($urlBaseApi,$data); 
			$data = array('kanaal' => $pchannel, 'actie' => '0');
			$objPostGet->PostData($urlBaseApi,$data);
		} else {
		//Error for missing fields
			$registermessage = 'Description shoudl always have a <br/>description.';
		}
	} else {
		$registermessage = 'Channel cannot be lower than <br/>'.$pminchannel.' and higher than 249.';
	}
} elseif ($gdelete == 1) {
	//Delete device in DB
	$objMySQL->mysqldb_query('DELETE FROM kaku.kaku_status 
                              WHERE channel='.$gchannel);
	$objMySQL->mysqldb_query('DELETE FROM kaku.kaku_channel_desc 
                              WHERE channel='.$gchannel);
}

//Send API request to test switch
if ($gtest == 1) {
	if ($level == 0) {
		$data = array('kanaal' => $gchannel, 'actie' => '17');
		$result = $objPostGet->PostData($urlBaseApi,$data); 
		$data = array('kanaal' => $gchannel, 'actie' => '0');
		$objPostGet->PostData($urlBaseApi,$data);
	} elseif ($level == 17) {
		$data = array('kanaal' => $gchannel, 'actie' => '0');
		$objPostGet->PostData($urlBaseApi,$data);
		$data = array('kanaal' => $gchannel, 'actie' => '17');
		$objPostGet->PostData($urlBaseApi,$data); 
	}
}
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta http-equiv="Cache-Control" content="no-store" />
	<title>Settings</title>
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
	<script type='text/javascript' src="js/validate.js"></script>
</head>
<body>
<div data-role="page" id="page-swipe">
    <div data-role="header" data-theme="b" data-position="fixed" data-tap-toggle="false">
        <h1>Settings</h1>
        <a href="#left-panel" data-icon="grid" data-iconpos="notext" data-shadow="false" data-iconshadow="false" class="ui-nodisc-icon">Open left panel</a>
    </div><!-- /header -->
	<div data-role="main" class="ui-content">
		<div class="content-primary" >
				<ul data-role="listview" data-filter="true" data-input="#filterBasic-input" data-icon="false" class="table">
<?php
//get exciting devices on the network
$queryResult = $objMySQL->mysqldb_query('SELECT kks.id, kkcd.desc, kks.channel, kks.scene, kks.level, kkcd.locked, kkcd.type
                                         FROM kaku.kaku_channel_desc AS kkcd 
                                         RIGHT JOIN kaku.kaku_status AS kks ON kkcd.channel = kks.channel');

foreach ($queryResult as $row){
//need to check if this query object can be used twice because of the pointer placing...
$queryResultDevices = $objMySQL->mysqldb_query('SELECT description, type 
                                                FROM kaku.kaku_device_types 
												WHERE type IS NOT NULL
												ORDER BY type ASC');
	//Exit loop if the ID is null
	if ($row['id'] == '') {
		break;
	}
	//Set minimum channel number for form
	if ($row['channel'] > $maxChannel) {$maxChannel = $row['channel'];}
	print "			<li>". PHP_EOL;
	print "				<form id=\"formchng".$row['id']."\" method=\"post\" action=\"/settings\">". PHP_EOL;
	print "				<div class=\"ui-field-contain\">". PHP_EOL;
	print "					<label for=\"text-basic\">Description:</label>". PHP_EOL;
	print "					<input name=\"desc\" data-mini=\"true\" id=\"description\" value=\"".$row['desc']."\" type=\"text\" required=\"required\" />". PHP_EOL;
	print "					<label for=\"number-pattern\">scene:</label>". PHP_EOL;
	print "					<input name=\"scene\" data-mini=\"true\" pattern=\"[0-9]*\" id=\"number-pattern\" value=\"".$row['scene']."\" type=\"number\" required=\"required\" />". PHP_EOL;
	print "					<label for=\"number-pattern\">channel:</label>". PHP_EOL;
	print "					<input name=\"channel\" id=\"channel\" data-mini=\"true\" min=\"".$row['channel']."\" max=\"250\" value=\"".$row['channel']."\" type=\"range\" />". PHP_EOL;
	print "				</div>". PHP_EOL;
	print "				<div class=\"ui-field-contain\">". PHP_EOL;
	print "					<fieldset class=\"type_".$row['id']."\" id=\"type_".$row['id']."\"  data-role=\"controlgroup\" data-type=\"horizontal\" data-mini=\"true\">". PHP_EOL;
	print "						<legend>Type:</legend>". PHP_EOL;
	//--Make device list with types
	foreach ($queryResultDevices as $row_){
		if ($row_['type'] != '' and strlen($row_['type']) != 0) {
			//select the type of device
			if ($row['type'] == $row_['type']) {
				print	"						<input type=\"radio\" name=\"type\" id=\"type_".$row['id'].$row_['type']."\" value=\"".$row_['type']."\" checked=\"checked\"/>". PHP_EOL;
				print	"						<label class=\"type_".$row['id'].$row_['type']."\" for=\"type_".$row['id'].$row_['type']."\" data-mini=\"true\">".$row_['description']."</label>". PHP_EOL;
			} else {
				print	"						<input type=\"radio\" name=\"type\" id=\"type_".$row['id'].$row_['type']."\" value=\"".$row_['type']."\" />". PHP_EOL;
				print	"						<label class=\"type_".$row['id'].$row_['type']."\" for=\"type_".$row['id'].$row_['type']."\" data-mini=\"true\">".$row_['description']."</label>". PHP_EOL;
			}
		}
	}
	print "					</fieldset>". PHP_EOL;
	print "				</div>". PHP_EOL;
	print "				<div class=\"ui-field-contain\">". PHP_EOL;
	//Set device status to ON/OFF or level number
	if ($row['type'] == 0) {
		print "				<label for=\"level\">Switch: </label>". PHP_EOL;
		print "				<select name=\"level\" class=\"flip\" id=\"flip-".$row['id']."\" data-role=\"flipswitch\" data-mini=\"true\">". PHP_EOL;
		if ($row['level'] == 0) {
			print "					<option value=\"0\" selected=\"\">Off</option>". PHP_EOL;
			print "					<option value=\"17\">On</option>". PHP_EOL;
		} elseif ($row['level'] == 17) {
			print "					<option value=\"0\">Off</option>". PHP_EOL;
			print "					<option value=\"17\" selected=\"\">On</option>". PHP_EOL;
		}
		print "				</select>". PHP_EOL;
	} elseif ($row['type'] == 1) {
		print "				<label for=\"number-pattern\">level:</label>". PHP_EOL;
		print "				<input name=\"level\" id=\"level\" data-mini=\"true\" min=\"0\" max=\"64\" value=\"".$row['level']."\" type=\"range\" />". PHP_EOL;
	}
	print "				</div>". PHP_EOL;
	print "				<div class=\"ui-field-contain\">". PHP_EOL;
	print "				<label for=\"slider-flip-m\">Locked:</label>". PHP_EOL;
	print "				<select name=\"locked\" data-role=\"flipswitch\" data-mini=\"true\">". PHP_EOL;
	//Set device on on or off feature in main locked Y or N
	if ($row['locked'] == 0) {
		print "					<option value=\"0\" selected=\"\">No</option>". PHP_EOL;
		print "					<option value=\"1\">Yes</option>". PHP_EOL;
	} else {
		print "					<option value=\"0\">No</option>". PHP_EOL;
		print "					<option value=\"1\" selected=\"\">Yes</option>". PHP_EOL;
	}
	print "				</select>". PHP_EOL;
	print "				</div>". PHP_EOL;
	print "				<div class=\"ui-field-contain\">". PHP_EOL;
	print "				<label for=\"status-".$row['id']."\">Status: </label>". PHP_EOL;
	print "				<select name=\"status-".$row['id']."\" data-role=\"flipswitch\" data-mini=\"true\" disabled=\"disabled\">". PHP_EOL;
	//Status of On or Off depricated
	if ($row['level'] == 0) {
		print "					<option value=\"".$row['channel'].", off\" selected=\"\">Off</option>". PHP_EOL;
		print "					<option value=\"".$row['channel'].", on\">On</option>". PHP_EOL;
	} elseif ($row['level'] == 17) {
		print "					<option value=\"".$row['channel'].", off\">Off</option>". PHP_EOL;
		print "					<option value=\"".$row['channel'].", on\" selected=\"\">On</option>". PHP_EOL;
	}
	print "				</select>". PHP_EOL;
	print "				</div>". PHP_EOL;
	print "					<input type=\"hidden\" name=\"kksid\" value=\"".$row['id']."\">". PHP_EOL;
	print "					<input type=\"hidden\" name=\"changedevice\" value=\"1\">". PHP_EOL;
	print "					<input type=\"submit\" value=\"Change values\" data-inline=\"true\" data-mini=\"true\">". PHP_EOL;
	//Delete or test device
	if ($row['locked'] == 0) {
		print "					<a href=\"/settings?delete=1&channel=".$row['channel']."\" data-role=\"button\" data-inline=\"true\" data-mini=\"true\">Delete</a>". PHP_EOL;
		print "					<a href=\"/settings?test=1&channel=".$row['channel']."&level=".$row['level']."\" data-role=\"button\" data-inline=\"true\" data-mini=\"true\">Test</a>". PHP_EOL;
	}
	print "				</form>". PHP_EOL;
	print "			</li>". PHP_EOL;
}?>			<li>
				<h1><font color="red"><?php print $registermessage; ?></font></h1>
				<form id="formreg" method="post" action="/settings">
				<div class="ui-field-contain">
					<label for="text-basic">Description:</label>
					<input name="desc" id="description" value="" type="text" required="required"/>
					<label for="number-pattern">scene:</label>
					<input name="scene" pattern="[0-9]*" id="number-pattern" value="1" type="number" required="required"/>
					<label for="number-pattern">channel:</label>
					<input name="channel" id="channel" data-mini="true" min="<?php print ($maxChannel+1); ?>" max="250" value="<?php print ($maxChannel+1); ?>" type="range" />
				</div>
				<div class="ui-field-contain">
					<fieldset data-role="controlgroup" data-type="horizontal" data-mini="true">
						<legend>Type:</legend>
<?php 
$queryResultDevices = $objMySQL->mysqldb_query('SELECT description, type 
                                                FROM kaku.kaku_device_types 
												WHERE type IS NOT NULL
												ORDER BY type ASC');
foreach ($queryResultDevices as $row){
	if ($row['type'] == '') {
		break;
	}
	print	"						<input type=\"radio\" name=\"type\" id=\"type_".$row['type']."\" value=\"".$row['type']."\"  />". PHP_EOL;
	print	"						<label for=\"type_".$row['type']."\" data-mini=\"true\">".$row['description']."</label>". PHP_EOL;
}?>
					</fieldset>
				</div>
				<div class="ui-field-contain">
					<label for="slider-flip-m">Locked:</label>
					<select name="locked" id="slider-flip-m" data-role="slider" data-mini="true">
						<option value="0" selected="">No</option>
						<option value="1">Yes</option>
					</select>
				</div>
					<input type="hidden" name="minchannel" value="<?php print ($maxChannel+1); ?>"/>
					<input type="hidden" name="registerdevice" value="1"/>
					<input type="submit" value="Register New Device" data-inline="true" data-mini="true"/>
				</form>
			</li>
			</ul>
		</div>
	</div><!-- /main -->
	<div data-role="footer">
		<br/><center><font size=1>Copyright Roy Oltmans<br/>Thanks to KaKu and <a href="https://launchpad.net/pykaku">Pykaku</a><br/></font></center>
		 <h1 id="notification"></h1>
	</div><!-- /footer -->
<?php
	//Add menu options left side
	include_once("inc/menu.inc");
?>
</div><!-- /page -->
</body>
</html>