<?php

	/*require 'whatsapp/src/whatsprot.class.php';
	$username="5492616210405";
	$password="1B1f0G2pUIzadxQG+km8n1yhsUA=";
	$wa = new WhatsProt($username, "Casablanca Sistema", 0);
	$wa->connect();
	$wa->loginWithPassword($password);
	$msg="se desocupa habitacion ".$_GET['num'];
	//$numero="5492614686768";
	//$numero="5492614686763";
	$numero="5492613458390";
	$id=$wa->sendMessage($numero, $msg);
	*/


//http://www.digitalcreative.com.ar/casablanca/alarmawsp.php?num=11
// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
CURLOPT_RETURNTRANSFER => 1,
CURLOPT_URL => 'http://www.digitalcreative.com.ar/casablanca/alarmawsp.php?num='.$_GET['num'],
CURLOPT_USERAGENT => 'Codular Sample cURL Request'
));
// Send the request & save response to $resp
$resp = curl_exec($curl);
// Close request to clear up some resources
curl_close($curl);


?>