<?
// tiny but strong
include_once('tbs/tbs_class.php');
include_once('tbs/plugins/tbs_plugin_html.php'); // Plug-in to select HTML items.

// configuration - move to mongoDB later?
include_once('branding/branding.php');

// parameters
if (!isset($_GET)) $_POST=&$HTTP_GET_VARS ;

if (isset($_COOKIE["websocket"])) {
	$cookie = unserialize($_COOKIE["websocket"]);
	$txtUsename = $cookie['username'];
	$txtPassword = $cookie['password'];
	$txtSaveCookie = 'checked="checked"';
}
else {
	$txtUsename = '';
	$txtPassword = '';
	$txtSaveCookie = '';
}

if (isset($_COOKIE["css"])) {
	$themename = $_COOKIE["css"];
}
else {
	$themename = 'css/smoothness/jquery-ui-1.8.16.custom.css';
}

$themes = array();
$themes[] = array(option=>'Smoothness', value=>'css/smoothness/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Cupertino', value=>'css/cupertino/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Flick', value=>'css/flick/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Humanity', value=>'css/humanity/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Overcast', value=>'css/overcast/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Redmond', value=>'css/redmond/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Sunny', value=>'css/sunny/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Hot Sneaks', value=>'css/hot-sneaks/jquery-ui-1.8.16.custom.css');
//$themes[] = array(option=>'Vader', value=>'css/vader/jquery-ui-1.8.16.custom.css');
//$themes[] = array(option=>'Eggplant', value=>'css/eggplant/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Blitzer', value=>'css/blitzer/jquery-ui-1.8.16.custom.css');
$themes[] = array(option=>'Pepper Grinder', value=>'css/pepper-grinder/jquery-ui-1.8.16.custom.css');

if (isset($_COOKIE["map"])) {
	$mapapi = $_COOKIE["map"];
}
else {
	$mapapi = 'mapquest';
}

$maps = array();
$maps[] = array(option=>'Google', value=>'google');
$maps[] = array(option=>'Yahoo', value=>'yahoo');
$maps[] = array(option=>'Microsoft', value=>'microsoft');
$maps[] = array(option=>'Microsoft7', value=>'microsoft7');
$maps[] = array(option=>'OpenStreetMap', value=>'mapquest');

// cookie not needed as keyvals arrive before display - these are defaults
$markers = array();
$markers[] = array(option=>'Car', value=>'car');
$markers[] = array(option=>'Boat', value=>'boat');
$markers[] = array(option=>'Flatbed Truck', value=>'flatbed');
$markers[] = array(option=>'Truck', value=>'truck');
$markers[] = array(option=>'Large Van', value=>'largeVan');
$markers[] = array(option=>'Small Van', value=>'smallVan');

// cookie not needed as keyvals arrive before display
$icons = array();
$icons[] = array(option=>'Car Blue', value=>'0');
$icons[] = array(option=>'Car Black', value=>'1');
$icons[] = array(option=>'Car Green', value=>'2');
$icons[] = array(option=>'Car Orange', value=>'3');
$icons[] = array(option=>'Car Pink', value=>'4');
$icons[] = array(option=>'Car Red', value=>'5');
$icons[] = array(option=>'Car Yellow', value=>'6');
$icons[] = array(option=>'Truck Blue', value=>'7');
$icons[] = array(option=>'Truck Black', value=>'8');
$icons[] = array(option=>'Truck Green', value=>'9');
$icons[] = array(option=>'Truck Orange', value=>'10');
$icons[] = array(option=>'Truck Pink', value=>'11');
$icons[] = array(option=>'Truck Red', value=>'12');
$icons[] = array(option=>'Truck Yellow', value=>'13');
$icons[] = array(option=>'Pda Blue', value=>'14');
$icons[] = array(option=>'Pda Black', value=>'15');
$icons[] = array(option=>'Pda Green', value=>'16');
$icons[] = array(option=>'Pda Orange', value=>'17');
$icons[] = array(option=>'Pda Pink', value=>'18');
$icons[] = array(option=>'Pda Red', value=>'19');
$icons[] = array(option=>'Pda Yellow', value=>'20');
$icons[] = array(option=>'LargeVan Blue', value=>'21');
$icons[] = array(option=>'LargeVan Black', value=>'22');
$icons[] = array(option=>'LargeVan Green', value=>'23');
$icons[] = array(option=>'LargeVan Orange', value=>'24');
$icons[] = array(option=>'LargeVan Pink', value=>'25');
$icons[] = array(option=>'LargeVan Red', value=>'26');
$icons[] = array(option=>'LargeVan Yellow', value=>'27');
$icons[] = array(option=>'SmallVan Blue', value=>'28');
$icons[] = array(option=>'SmallVan Black', value=>'29');
$icons[] = array(option=>'SmallVan Green', value=>'30');
$icons[] = array(option=>'SmallVan Orange', value=>'31');
$icons[] = array(option=>'SmallVan Pink', value=>'32');
$icons[] = array(option=>'SmallVan Red', value=>'33');
$icons[] = array(option=>'SmallVan Yellow', value=>'34');
$icons[] = array(option=>'Boat Blue', value=>'35');
$icons[] = array(option=>'Boat Black', value=>'36');
$icons[] = array(option=>'Boat Green', value=>'37');
$icons[] = array(option=>'Boat Orange', value=>'38');
$icons[] = array(option=>'Boat Pink', value=>'39');
$icons[] = array(option=>'Boat Red', value=>'40');
$icons[] = array(option=>'Boat Yellow', value=>'41');
$icons[] = array(option=>'Pickup Blue', value=>'42');
$icons[] = array(option=>'Pickup Black', value=>'43');
$icons[] = array(option=>'Pickup Green', value=>'44');
$icons[] = array(option=>'Pickup Orange', value=>'45');
$icons[] = array(option=>'Pickup Pink', value=>'46');
$icons[] = array(option=>'Pickup Red', value=>'47');
$icons[] = array(option=>'Pickup Yellow', value=>'48');
$icons[] = array(option=>'Flatbed Blue', value=>'49');
$icons[] = array(option=>'Flatbed Black', value=>'50');
$icons[] = array(option=>'Flatbed Green', value=>'51');
$icons[] = array(option=>'Flatbed Orange', value=>'52');
$icons[] = array(option=>'Flatbed Pink', value=>'53');
$icons[] = array(option=>'Flatbed Red', value=>'54');
$icons[] = array(option=>'Flatbed Yellow', value=>'55');
$icons[] = array(option=>'FlatbedCrane Blue', value=>'56');
$icons[] = array(option=>'FlatbedCrane Black', value=>'57');
$icons[] = array(option=>'FlatbedCrane Green', value=>'58');
$icons[] = array(option=>'FlatbedCrane Orange', value=>'59');
$icons[] = array(option=>'FlatbedCrane Pink', value=>'60');
$icons[] = array(option=>'FlatbedCrane Red', value=>'61');
$icons[] = array(option=>'FlatbedCrane Yellow', value=>'62');
$icons[] = array(option=>'FlatbedCargo Blue', value=>'63');
$icons[] = array(option=>'FlatbedCargo Black', value=>'64');
$icons[] = array(option=>'FlatbedCargo Green', value=>'65');
$icons[] = array(option=>'FlatbedCargo Orange', value=>'66');
$icons[] = array(option=>'FlatbedCargo Pink', value=>'67');
$icons[] = array(option=>'FlatbedCargo Red', value=>'68');
$icons[] = array(option=>'FlatbedCargo Yellow', value=>'69');


// Template engine
$TBS = new clsTinyButStrong;

$TBS->LoadTemplate('index.ctp');

$TBS->MergeBlock('blk1',$themes);
$TBS->MergeBlock('blk2',$maps);
$TBS->MergeBlock('blk3',$markers);
$TBS->MergeBlock('blk4',$icons);

$TBS->Show() ;
?>