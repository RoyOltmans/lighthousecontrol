KAKU API 0.1<br/>
Setters:
<?php
	if(isset($_POST['kanaal'])) $kanaal = $_POST['kanaal'];
	if(isset($_POST['actie'])) $actie = $_POST['actie'];
	if(isset($_GET['kanaal'])) $kanaal = $_GET['kanaal'];
	if(isset($_GET['actie'])) $actie = $_GET['actie'];
	print $kanaal;
	print $actie;
	sleep(2);
    #Stuur commando naar kaku service
    if (isset($kanaal) && isset($actie)){
      $fp = fsockopen("localhost", 50007, $errno, $errstr, 10);
      if (!$fp){
        echo $errstr." (".$errno.") <br>\n";
      } else {
        $out = "$kanaal, $actie";
        fwrite($fp, $out);
        fclose($fp);
      }
    }
?>
