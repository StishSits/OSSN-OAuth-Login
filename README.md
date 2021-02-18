# OSSN-OAuth-Login
A component for OSSN to configure OAuth login from any third party client such as ORE ID. Code In Progress.
Will Update Readme with details on how to create ORE ID account and get needed info for the component to work. 

## How to obtain OAuth API Details in WordPress

1) Go to OAuth Server plugin settings in WordPress
2) Add a client
3) Set redirect uri to {your-ossn-site.com}/oauth_login/wordpress
4) Find your Client ID and Client secret and save them in {your-ossn-site.com}.com/administrator/component/OauthLogin
5) You will need to look at documentation for the WordPress OAuth plugin's authorize/token/resource urls and save those under OauthLogin settings as well
