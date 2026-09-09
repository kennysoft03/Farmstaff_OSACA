<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class Googleplus {
	
	public function __construct() {
		
		$CI =& get_instance();
		$CI->config->load('googleplus');
		
		require APPPATH .'third_party/google-login-api/apiClient.php';
		require APPPATH .'third_party/google-login-api/contrib/apiOauth2Service.php';
		
		$this->client = new apiClient();
		$this->client->setApplicationName($CI->config->item('application_name', 'Giftrete'));
		$this->client->setClientId($CI->config->item('client_id', '276769163557-rfrri69d25kps5hpcodf1kgh4m3jsfih.apps.googleusercontent.com'));
		$this->client->setClientSecret($CI->config->item('client_secret', '2_HBHmPye0hi_EhIpgvMXNTy'));
		$this->client->setRedirectUri($CI->config->item('redirect_uri', 'http://ola.com'));
		$this->client->setDeveloperKey($CI->config->item('api_key', 'AIzaSyDxR2s8vjK4PhnFE_0cOGJHj1EFFN_ecoM'));
		$this->client->setScopes($CI->config->item('scopes', 'https://www.googleapis.com/auth/userinfo.email'));
		$this->client->setAccessType('online');
		$this->client->setApprovalPrompt('auto');
		$this->oauth2 = new apiOauth2Service($this->client);

	}
	
	public function loginURL() {
        return $this->client->createAuthUrl();
    }
	
	public function getAuthenticate() {
        return $this->client->authenticate();
    }
	
	public function getAccessToken() {
        return $this->client->getAccessToken();
    }
	
	public function setAccessToken() {
        return $this->client->setAccessToken();
    }
	
	public function revokeToken() {
        return $this->client->revokeToken();
    }
	
	public function getUserInfo() {
        return $this->oauth2->userinfo->get();
    }
	
}
?>