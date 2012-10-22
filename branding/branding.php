<?php
function readAlertFile($brandingpath) {
	$alertFile = $brandingpath."/about.html";
	$fh = fopen($alertFile, 'r');
	$alertText = fread($fh, filesize($alertFile));
	fclose($fh);
	return $alertText;
}
function readLoginFile($brandingpath) {
	$loginFile = $brandingpath."/login.html";
	$fh = fopen($loginFile, 'r');
	$loginText = fread($fh, filesize($loginFile));
	fclose($fh);
	return $loginText;
}

    switch ($_SERVER["HTTP_HOST"])
    {
    
     case "map.mytrackingcompany.com":
	    $websocketURL = "ws://myWebsocketServer.net:88/websocket";
	    $brandingpath = "branding/mytrackingcompany/";
	    $prefix = "MyTrackingCo";
	    $brandingLogoURL = "http://www.mytrackingcompany.com";
	    $brandingDefaultTheme = "";
	    break;
     default:
	    $websocketURL = "ws://myWebsocketServer.net:88/websocket";
	    $brandingpath = "branding/mytrackingcompany/";
	    $prefix = "MyTrackingCo";
	    $brandingLogoURL = "http://www.mytrackingcompany.com";
	    $brandingDefaultTheme = "";
	    break;
    }

	$brandingLogoImg = $brandingpath.$prefix."Logo35.png";
	$brandingDialogImg = $brandingpath.$prefix."LogoLarge.png";
	$brandingAboutDialog = readAlertFile($brandingpath);
	$brandingLoginDialog = readLoginFile($brandingpath);
?>                                                                                              