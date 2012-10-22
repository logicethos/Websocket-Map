<?php
//Get Post Variables. The name is the same as what was in the object that was sent in the jQuery
if (isset($_POST['thisUsername'])){
    $cookie['username'] = $_POST['thisUsername'];
}else{
    $cookie['username'] = '';
}

if (isset($_POST['thisPassword'])){
	$cookie['password'] = $_POST['thisPassword'];
}else{
    $cookie['password'] = '';
}

$serialized = serialize($cookie);

if ($cookie['username'] == '') {
	setcookie("websocket", "", time()-3600, '/');
}
else {
	setcookie("websocket", $serialized, time()+3600*24*1000, '/');
}

//Because we want to use json, we have to place things in an array and encode it for json.
//This will give us a nice javascript object on the front side.
echo json_encode(array("cookie"=>$serialized));
?>
