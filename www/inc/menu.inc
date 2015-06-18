<?php
include "inc/class.tools.inc";
$objTools = new tools;
?>
 <div data-role="panel" id="left-panel" data-theme="b">
    	<p>Menu:</p>
		<hr/>
		<a href="/?rand=<?php print $objTools->generateRandomString(); ?>" data-role="button">Main</a>
		<a href="/schedule?rand=<?php print $objTools->generateRandomString(); ?>" data-role="button">Schedule</a>
		<a href="/settings?rand=<?php print $objTools->generateRandomString(); ?>" data-role="button">Settings</a>
		<a href="#" data-rel="close" class="ui-btn ui-corner-all ui-shadow ui-mini ui-btn-inline ui-icon-delete ui-btn-icon-left ui-btn-right">Close</a>
	</div><!-- /left panel -->