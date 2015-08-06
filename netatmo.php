<?php

// Script développé par Cédric Locqueneux. http://maison-et-domotique.com
// Synthaxe: http://your-web-server/scripts/netatmo.php?login=VOTRE_EMAIL&password=MOTDEPASSE
// Modifé par François LOZANO 

$password=$_GET['password'];
$username=$_GET['login'];

$app_id = 'your app id';
$app_secret = 'your app secret';

$token_url = "https://api.netatmo.net/oauth2/token";
$postdata = http_build_query(
        array(
            'grant_type' => "password",
            'client_id' => $app_id,
            'client_secret' => $app_secret,
            'username' => $username,
            'password' => $password
    )
);

$opts = array('http' =>
	array(
		'method'  => 'POST',
		'header'  => 'Content-type: application/x-www-form-urlencoded',
		'content' => $postdata
	)
);

$context  = stream_context_create($opts);
$response = file_get_contents($token_url, false, $context);

$params = null;
$params = json_decode($response, true);
$api_url = "https://api.netatmo.net/api/getuser?access_token=" . $params['access_token'];
$requete = @file_get_contents($api_url);

$url_devices = "https://api.netatmo.net/api/devicelist?access_token=" .  $params['access_token'];
$resulat_device = @file_get_contents($url_devices);	

$json_devices = json_decode($resulat_device,true);

$module_interne = $json_devices["body"]["devices"][0]["_id"];
$module_externe = $json_devices["body"]["modules"][0]["_id"];
$module_additionnel1 = $json_devices["body"]["modules"][1]["_id"];
$module_additionnel2 = $json_devices["body"]["modules"][2]["_id"];
$module_additionnel3 = $json_devices["body"]["modules"][3]["_id"];
$module_additionnel4 = $json_devices["body"]["modules"][4]["_id"];

$url_mesures_internes = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&scale=max&type=Temperature,CO2,Humidity,Pressure,Noise&date_end=last";
$mesures_internes = @file_get_contents($url_mesures_internes);

$url_mesures_externes = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&module_id=" . $module_externe . "&scale=max&type=Temperature,Humidity&date_end=last";
$mesures_externes = @file_get_contents($url_mesures_externes);

if (!empty($module_additionnel1)) {
	$url_mesures_additionnel1 = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&module_id=" . $module_additionnel1 . "&scale=max&type=Temperature,CO2,Humidity&date_end=last";
	$mesures_additionnel1 = file_get_contents($url_mesures_additionnel1);
	$json_mesures_additionnel1 = json_decode($mesures_additionnel1, true);
	$temperature_additionnel1 = $json_mesures_additionnel1["body"][0]["value"][0][0];
	$co2_additionnel1 = $json_mesures_additionnel1["body"][0]["value"][0][1];
	$humidite_additionnel1 = $json_mesures_additionnel1["body"][0]["value"][0][2];
}
if (!empty($module_additionnel2)) {
	$url_mesures_additionnel2 = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&module_id=" . $module_additionnel2 .  "&scale=max&type=Temperature,CO2,Humidity&date_end=last";
	$mesures_additionnel2 = file_get_contents($url_mesures_additionnel2);
	$json_mesures_additionnel2 = json_decode($mesures_additionnel2, true);
	$temperature_additionnel2 = $json_mesures_additionnel2["body"][0]["value"][0][0];
	$co2_additionnel2 = $json_mesures_additionnel2["body"][0]["value"][0][1];
	$humidite_additionnel2 = $json_mesures_additionnel2["body"][0]["value"][0][2];
}
if (!empty($module_additionnel3)) {
	$url_mesures_additionnel3 = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&module_id=" . $module_additionnel3 .  "&scale=max&type=Temperature,CO2,Humidity&date_end=last";
	$mesures_additionnel3 = file_get_contents($url_mesures_additionnel3);	
	$json_mesures_additionnel3 = json_decode($mesures_additionnel3, true);
	$temperature_additionnel3 = $json_mesures_additionnel3["body"][0]["value"][0][0];
	$co2_additionnel3 = $json_mesures_additionnel3["body"][0]["value"][0][1];
	$humidite_additionnel3 = $json_mesures_additionnel3["body"][0]["value"][0][2];
}	
if (!empty($module_additionnel4)) {
	$url_mesures_additionnel4 = "https://api.netatmo.net/api/getmeasure?access_token=" .  $params['access_token'] . "&device_id=" . $module_interne . "&module_id=" . $module_additionnel4 .  "&scale=1day&type=sum_rain&date_end=last";
	$mesures_additionnel4 = file_get_contents($url_mesures_additionnel4);	
	$json_mesures_additionnel4 = json_decode($mesures_additionnel4, true);
	$pluie_additionnel4 = $json_mesures_additionnel4["body"][0]["value"][0][0];
}	


$json_mesures_internes = json_decode($mesures_internes, true);
$json_mesures_externes = json_decode($mesures_externes, true);

$temperature_interne = $json_mesures_internes["body"][0]["value"][0][0];
$co2 = $json_mesures_internes["body"][0]["value"][0][1];
$humidite_interne = $json_mesures_internes["body"][0]["value"][0][2];
$pression = $json_mesures_internes["body"][0]["value"][0][3];
$bruit = $json_mesures_internes["body"][0]["value"][0][4];
$temperature_externe = $json_mesures_externes["body"][0]["value"][0][0];
$humidite_externe = $json_mesures_externes["body"][0]["value"][0][1];


	echo '<?xml version="1.0" encoding="utf8" ?>';
	echo "<netatmo>";
	echo "<temperature_interieure>" . $temperature_interne  . "</temperature_interieure>";
	echo "<co2>" . $co2  . "</co2>";
	echo "<humidite_interieure>" . $humidite_interne  . "</humidite_interieure>";
	echo "<pression>" . $pression  . "</pression>";
	echo "<niveau_sonore>" . $bruit  . "</niveau_sonore>";
	echo "<temperature_exterieure>" . $temperature_externe  . "</temperature_exterieure>";	
 	echo "<humidite_exterieure>" . $humidite_externe  . "</humidite_exterieure>";
	if (!empty($module_additionnel1)) {
		echo "<temperature_additionnel1>" . $temperature_additionnel1  . "</temperature_additionnel1>";
		echo "<co2_additionnel1>" . $co2_additionnel1  . "</co2_additionnel1>";
		echo "<humidite_additionnel1>" . $humidite_additionnel1  . "</humidite_additionnel1>";
	}
	if (!empty($module_additionnel2)) {
		echo "<temperature_additionnel2>" . $temperature_additionnel2  . "</temperature_additionnel2>";
		echo "<co2_additionnel2>" . $co2_additionnel2  . "</co2_additionnel2>";
		echo "<humidite_additionnel2>" . $humidite_additionnel2  . "</humidite_additionnel2>";
	}
	if (!empty($module_additionnel3)) {
		echo "<temperature_additionnel3>" . $temperature_additionnel3  . "</temperature_additionnel3>";
		echo "<co2_additionnel3>" . $co2_additionnel3  . "</co2_additionnel3>";
		echo "<humidite_additionnel3>" . $humidite_additionnel3  . "</humidite_additionnel3>";	
	}
		if (!empty($module_additionnel4)) {
		echo "<cumul_pluie>" . $pluie_additionnel4  . "</cumul_pluie>";
	}
	echo "</netatmo>";
	
?>
