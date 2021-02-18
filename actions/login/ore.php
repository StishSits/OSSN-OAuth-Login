<?php
/**
 * @package 	Open Source Social Network OAuth (ore) Login
 * @author    Haden Hiles https://github.com/HadenHiles
 * @license   General Public Licence
 */
$oauth = oauth_login_cred();
if(empty($oauth->ore->app_name) || empty($oauth->ore->app_id) || empty($oauth->ore->app_api_key) || empty($oauth->ore->redirect_uri)){
		ossn_trigger_message("Error 100!", 'error');
		redirect();
}

$content = "response_type=code&client_id=" . $oauth->ore->consumer_key . "&redirect_uri=" . $oauth->ore->redirect_uri;
$authorization = base64_encode($oauth->ore->consumer_key . ":" . $oauth->ore->consumer_secret);
$header = array("Authorization: Basic {$authorization}","Content-Type: application/x-www-form-urlencoded");

$curl = curl_init();
curl_setopt_array($curl, array(
	CURLOPT_URL => $oauth->ore->consumer_authorization_url,
	CURLOPT_HTTPHEADER => $header,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_SSL_VERIFYHOST => false,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_POST => true,
	CURLOPT_POSTFIELDS => $content
));
$response = curl_exec($curl);
curl_close($curl);
var_dump($response);
