<?php
 $component = new OssnComponents;

 $app_name = input('app_name');
 $app_id = input('app_id');
 $app_api_key = input('app_api_key');
 $app_networks = input('app_networks');
 $app_permission_name = input ('app_permission_name');
 $app_endpoint_url = input ('app_endpoint_url');
 $app_signal_callback = input ('app_signal_callback')
 $app_auth_callback = input ('app_auth_callback')

 $args = array(
   'app_name' => trim($app_name),
   'app_id' => trim($app_id),
   'app_api_key' => trim($app_api_key),
   'app_networks' => trim($app_networks),
   'app_permission_name' => trim($app_permission_name),
   'app_endpoint_url' => trim($app_endpoint_url),
   'app_signal_callback' =>trim($app_signal_callback),
   'app_auth_callback' => triim($app_auth_callback),
  );

 if($component->setSettings('OAuthLogin', $args)){
		ossn_trigger_message(ossn_print('oauth:login:settings:saved'));
		redirect(REF);
 } else {
		ossn_trigger_message(ossn_print('oauth:login:settings:save:error'), 'error');
		redirect(REF);
 }
