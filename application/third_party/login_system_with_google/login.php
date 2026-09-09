<?php
session_start();
// although 2nd and 3rd line is not needed session_destroy() is needed,
// but just to be extra sure that no session remains in the cache.

//this make sure it is not saving the login everytime.....
//this reset the previous login session....you can figure this out later dear
$_SESSION = array();
unset($_SESSION);
session_destroy();


/*
 * @author Puneet Mehta
 * @website: http://www.PHPHive.info
 * @facebook: https://www.facebook.com/pages/PHPHive/1548210492057258
 */
 
require('http.php');
require('oauth_client.php');
require('config.php');


$client = new oauth_client_class;

// set the offline access only if you need to call an API
// when the user is not present and the token may expire
$client->offline = FALSE;

$client->debug = false;
$client->debug_http = true;
$client->redirect_uri = REDIRECT_URL;

$client->client_id = CLIENT_ID;
$application_line = __LINE__;
$client->client_secret = CLIENT_SECRET;

if (strlen($client->client_id) == 0 || strlen($client->client_secret) == 0)
  die('Please go to Google APIs console page ' .
          'http://code.google.com/apis/console in the API access tab, ' .
          'create a new client ID, and in the line ' . $application_line .
          ' set the client_id to Client ID and client_secret with Client Secret. ' .
          'The callback URL must be ' . $client->redirect_uri . ' but make sure ' .
          'the domain is valid and can be resolved by a public DNS.');

/* API permissions
 */
$client->scope = SCOPE;
if (($success = $client->Initialize())) {
  if (($success = $client->Process())) {
    if (strlen($client->authorization_error)) {
      $client->error = $client->authorization_error;
      $success = false;
    } elseif (strlen($client->access_token)) {
      $success = $client->CallAPI(
              'https://www.googleapis.com/oauth2/v1/userinfo', 'GET', array(), array('FailOnAccessError' => true), $user);
    }
  }
  $success = $client->Finalize($success);
}
if ($client->exit)
  exit;
 
?>