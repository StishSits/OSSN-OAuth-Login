<?php
/**
 * @package 	OAuth Login Component
 * @author    Haden Hiles https://github.com/HadenHiles
 * @license   General Public Licence
 */
define('OAUTH_LOGIN', ossn_route()->com . 'OAuthLogin/');

/**
 * OAuth Login Init
 *
 * @return void
 */
function oauth_login_init() {
		ossn_register_page('oauth_login', 'oauth_login_handler');
		ossn_register_com_panel('OAuthLogin', 'settings');

		ossn_extend_view('css/ossn.default', 'css/oauth/login');
		ossn_extend_view('css/ossn.admin.default', 'css/oauth/adminform');
		ossn_extend_view('forms/login2/before/submit', 'oauth/login');

		if(ossn_isAdminLoggedin()) {
				ossn_register_action('oauth_login/settings', OAUTH_LOGIN . 'actions/settings.php');
		}
		// if(!ossn_isLoggedin()) {
		// 		ossn_register_action('oauth/login/wordpress', OAUTH_LOGIN . 'actions/login/wordpress.php');
		// }
		ossn_register_action('oauth/login/wordpress', OAUTH_LOGIN . 'actions/login/wordpress.php');
}

/**
 * OAuth Login Details
 *
 * @return object
 */
function oauth_login_cred() {
		$component = new OssnComponents;
		$settings  = $component->getSettings('OAuthLogin');

		$oauth           = new stdClass;
		$oauth->ore = new stdClass;

		$oauth->ore->app_name    = $settings->app_name;
		$oauth->ore->app_id    = $settings->app_id;
		$oauth->ore->app_api_key   = $settings->api_key;
		$oauth->ore->app_networks = $settings->app_networks;
		$oauth->ore->app_permission_name = $settings->app_permission_name;
		$oauth->ore->redirect_uri = ossn_site_url('oauth_login/ore');
		$oauth->ore->app_endpoint_url = $settings->app_endpoint_url;
		$oauth->ore->app_signal_callback = $settings->app_signal_callback;
		$oauth->ore->app_auth_callback + $settings->app_auth_callback;

		return $oauth;
}
/**
 * OAuth login pages
 *
 * @param array $pages A list of handlers
 *
 * @return void
 */
function oauth_login_handler($pages) {
		$page = $pages[0];
		$auth_code = $_REQUEST['code'];
		$oauth = oauth_login_cred();

		switch($page) {
				case 'ore':
				$app_name = $oauth->ore->app_name;
			  $app_id = $oauth->ore->app_id;
			  $app_api_key = $oauth->app_api_key;
			  $app_networks = $oauth->app_networks;
			  $app_permission_name = $oauth->app_permission_name;
			  $app_endpoint_url = $oauth->app_endpoint_url;
			  $app_signal_callback = $oauth->_callback;
			  $app_auth_callback = $oauth->app_auth_callback
				

					$access_token = getAccessToken($token_url, $auth_code, $client_id, $client_secret, $redirect_uri);
					$user = getResource($access_token, $client_endpoint_url);

					$ossnuser    = ossn_user_by_username($user['user_login']);
					if(!$ossnuser) {
							$username = $user['user_login'];

							//Check if username already exists
							if(ossn_user_by_username($username)) {
								ossn_trigger_message(ossn_print('oauth:login:account:create:error'), 'error');
								redirect(REF);
							}

							// Set a default password for the user (won't be used)
							$password_minimum = ossn_call_hook('user', 'password:minimum:length', false, 6);
							$password = substr(md5(time()), 0, $password_minimum);

							// Separate the WordPress user's first name and last name
							$display_name = $user['display_name'];
							$display_name_first_last = explode(' ', $display_name);
							$firstname = $display_name_first_last[0];
							$lastname = $display_name_first_last[1];

							$add             = new OssnUser;
							$add->username   = $username;
							$add->first_name = $firstname;
							$add->last_name  = $lastname;
							$add->email      = $user['user_email'];
							$add->password   = $password;
							$add->validated  = true;
							if($add->addUser()) {
									if($add->Login()) {
											redirect("home");
									}
							} else {
									ossn_trigger_message(ossn_print('oauth:login:account:create:error'), 'error');
									redirect(REF);
							}
					} else {
							OssnSession::assign('OSSN_USER', $ossnuser);
							redirect("home");
					}
					break;
		}
}

//	OAuth step A, B - single call with client credentials as the basic auth header
//		will return access_token
function getAccessToken($token_url, $auth_code, $client_id, $client_secret, $redirect_uri) {
	$authorization = base64_encode("$client_id:$client_secret");
	$content = array(
		'grant_type' => 'authorization_code',
		'client_id' => $client_id,
		'client_secret' => $client_id,
		'code' => $auth_code,
		'redirect_uri' => $redirect_uri
	);
	$content_string = http_build_query($content);
	$header = array(
		"Authorization: Basic {$authorization}",
		'Content-Type: application/x-www-form-urlencoded'
	);
	$curl = curl_init();

	curl_setopt_array($curl, array(
	  CURLOPT_URL => $token_url,
	  CURLOPT_RETURNTRANSFER => true,
		CURLINFO_HEADER_OUT => true,
	  CURLOPT_CUSTOMREQUEST => "POST",
	  CURLOPT_POSTFIELDS => $content_string,
	  CURLOPT_HTTPHEADER => $header
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		ossn_trigger_message("OAuth Error: {$err}", 'error');
		redirect(REF);
	}

	return json_decode($response)->access_token;
}

// OAuth step B - with the returned access_token we can make as many calls as we want
function getResource($access_token, $api_url) {
	$header = array("Authorization: Bearer {$access_token}");

	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_URL => $api_url,
		CURLOPT_HTTPHEADER => $header,
		CURLOPT_SSL_VERIFYPEER => false,
		CURLOPT_RETURNTRANSFER => true
	));

	$response = curl_exec($curl);
	$err = curl_error($curl);

	curl_close($curl);

	if ($err) {
		ossn_trigger_message("OAuth Error: {$err}", 'error');
		redirect(REF);
	}

	return json_decode($response, true);
}

ossn_register_callback('ossn', 'init', 'oauth_login_init');
