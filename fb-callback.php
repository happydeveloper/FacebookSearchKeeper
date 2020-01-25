<?php
if(!session_id()) {
    session_start();
}

// Include the required dependencies.
require_once( 'vendor/autoload.php' );

// 초기화 - 페이스북앱 FS - Test1
$fb = new Facebook\Facebook([
    'app_id'                => '187124949355240',
    'app_secret'            => 'b706108eac776ec5aa90e3ca0e34f09a',
    'default_graph_version' => 'v5.0',
]);
   
  $helper = $fb->getRedirectLoginHelper();

  $_SESSION['FBRLH_state']=$_GET['state'];
   
  try {
    $accessToken = $helper->getAccessToken();
  } catch(Facebook\Exceptions\FacebookResponseException $e) {
    // When Graph returns an error
    echo 'Graph returned an error: ' . $e->getMessage();
    exit;
  } catch(Facebook\Exceptions\FacebookSDKException $e) {
    // When validation fails or other local issues
    echo 'Facebook SDK returned an error: ' . $e->getMessage();
    exit;
  }

  if (! isset($accessToken)) {
    if ($helper->getError()) {
      header('HTTP/1.0 401 Unauthorized');
      echo "Error: " . $helper->getError() . "\n";
      echo "Error Code: " . $helper->getErrorCode() . "\n";
      echo "Error Reason: " . $helper->getErrorReason() . "\n";
      echo "Error Description: " . $helper->getErrorDescription() . "\n";
    } else {
      header('HTTP/1.0 400 Bad Request');
      echo 'Bad request';
    }
    exit;
  }
 
  // Logged in
echo '<h3>Access Token</h3>';
ob_start();
var_dump($accessToken->getValue());
$result = ob_get_clean();
echo $result;

// The OAuth 2.0 client handler helps us manage access tokens
$oAuth2Client = $fb->getOAuth2Client();
 
// Get the access token metadata from /debug_token
$tokenMetadata = $oAuth2Client->debugToken($accessToken);
echo '<h3>Metadata</h3>';
var_dump($tokenMetadata);

echo '<h3>appID</h3>';
// Validation (these will throw FacebookSDKException's when they fail)

// If you know the user ID this access token belongs to, you can validate it here
//$tokenMetadata->validateUserId('123');
$tokenMetadata->validateExpiration();
 
if (! $accessToken->isLongLived()) {
  // Exchanges a short-lived access token for a long-lived one
  try {
    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
  } catch (Facebook\Exceptions\FacebookSDKException $e) {
    echo "<p>Error getting long-lived access token: " . $e->getMessage() . "</p>\n\n";
    exit;
  }
 
  echo '<h3>Long-lived</h3>';
  var_dump($accessToken->getValue());
}

$_SESSION['fb_access_token'] = (string) $accessToken;
 
// User is logged in with a long-lived access token.
// You can redirect them to a members-only page.
//header('Location: https://example.com/members.php');

// Response example.
$res = $fb->get('/me', $accessToken);
 
var_dump($res->getDecodedBody());

$res = $fb->get('/me/feed', $accessToken);

var_dump($res->getDecodedBody());
 
